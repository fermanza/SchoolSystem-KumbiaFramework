<?php

/**
 * Kumbia PHP Framework
 *
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbia.org/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package Auth
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * AuthException
 *
 */
require_once "library/kumbia/auth/exception.php";

/**
 * AuthInterface
 *
 */
require_once "library/kumbia/auth/interface.php";


class Auth extends Object {

	/**
	 * Nombre del adaptador usado para autenticar
	 *
	 * @var string
	 */
	private $adapter;

	/**
	 * Objeto Adaptador actual
	 *
	 * @var mixed
	 */
	private $adapter_object = null;

	/**
	 * Indica si un usuario debe loguearse solo una vez en el sistema desde
	 * cualquier parte
	 *
	 * @var boolean
	 */
	private $active_session = false;

	/**
	 * Tiempo en que expirara la sesion en caso de que no se termine con destroy_active_session
	 *
	 * @var integer
	 */
	private $expire_time = 3600;

	/**
	 * Argumentos extra enviados al Adaptador
	 *
	 * @var array
	 */
	private $extra_args = array();

	/**
	 * Tiempo que duerme la aplicacion cuando falla la autenticacion
	 */
	private $sleep_time = 0;

	/**
	 * Indica si el ultimo llamado a authenticate tuvo exito o no (persistente en sesion)
	 *
	 * @var boolean
	 */
	static private $is_valid = null;

	/**
	 * Ultima identidad obtenida por Authenticate (persistente en sesion)
	 *
	 * @var array
	 */
	static private $active_identity = array();

	/**
	 * Constructor del Autenticador
	 *
	 * @param string $adapter
	 */
	public function __construct(){
		$extra_args = get_params(func_get_args());
		if(isset($extra_args[0])){
			$adapter = $extra_args[0];
			unset($extra_args[0]);
		} else {
			$adapter = 'model';
		}
		$this->set_adapter($adapter, $this, $extra_args);

	}

	public function set_adapter($adapter, $auth = null, $extra_args=array()){
		if(!in_array($adapter, array('digest', 'http', 'model', 'kerberos5', 'radius'))){
			throw new AuthException("Adaptador de autenticaci&oacute;n '$adapter' no soportado");
		}
		$this->adapter = $adapter;
		require  "library/kumbia/auth/adapters/$adapter.php";
		$adapter_class = $adapter."Auth";
		$this->extra_args = $extra_args;
		$this->adapter_object = new $adapter_class($auth, $extra_args);

	}

	/**
	 * Obtiene el nombre del adaptador actual
	 * @return boolean
	 */
	public function get_adapter_name($adapter){
		return $this->adapter;
	}

	/**
	 * Realiza el proceso de autenticaci&oacute;n
	 *
	 * @return array
	 */
	public function authenticate(){
		$result = $this->adapter_object->authenticate();
		/**
		 * Si es una sesion activa maneja un archivo persistente para control
		 */
		if($result&&$this->active_session){
			$active_app = Kumbia::$active_app;
			$user_hash = md5(serialize($this->extra_args));
			$filename = "cache/".base64_encode($active_app);
			if(file_exists($filename)){
				$fp = fopen($filename, "r");
				while(!feof($fp)){
					$line = fgets($fp);
					$user = explode(":", $line);
					if($user_hash==$user[0]){
						if($user[1]+$user[2]>time()){
							if($this->sleep_time){
								sleep($this->sleep_time);
							}
							Auth::$identity = array();
							Auth::$is_valid = false;
							return false;
						} else {
							fclose($fp);
							$this->destroy_active_session();
							file_put_contents($filename, $user_hash.":".time().":".$this->expire_time."\n");
						}
					}
				}
				fclose($fp);
				$fp = fopen($filename, "a");
				fputs($fp, $user_hash.":".time().":".$this->expire_time."\n");
				fclose($fp);
			} else {
				file_put_contents($filename, $user_hash.":".time().":".$this->expire_time."\n");
			}
		}
		if(!$result){
			if($this->sleep_time){
				sleep($this->sleep_time);
			}
		}
		$_SESSION['KUMBIA_AUTH_IDENTITY'] = $this->adapter_object->get_identity();
		Auth::$active_identity = $this->adapter_object->get_identity();
		$_SESSION['KUMBIA_AUTH_VALID'] = $result;
		Auth::$is_valid = $result;
		return $result;

	}

	/**
	 * Realiza el proceso de autenticaci&oacute;n usando HTTP
	 *
	 * @return array
	 */
	public function authenticate_with_http(){
		if(!$_SERVER['PHP_AUTH_USER']){
    		header('WWW-Authenticate: Basic realm="basic"');
    		header('HTTP/1.0 401 Unauthorized');
    		return false;
		} else {
			$options = array("username" => $_SERVER['PHP_AUTH_USER'], "password" => $_SERVER['PHP_AUTH_PW']);
			$this->adapter_object->set_params($options);
			return $this->authenticate();
		}
	}

	/**
	 * Devuelve la identidad encontrada en caso de exito
	 *
	 * @return array
	 */
	public function get_identity(){
		return $this->adapter_object->get_identity();
	}

	/**
	 * Permite controlar que usuario no se loguee mas de una vez en el sistema desde cualquier parte
	 *
	 * @param string $value
	 */
	public function set_active_session($value, $time=3600){
		$this->active_session = $value;
		$this->expire_time = $time;
	}

	/**
	 * Destruir sesion activa del usuario autenticado
	 *
	 */
	public function destroy_active_session(){
		$active_app = Kumbia::$active_app;
		$user_hash = md5(serialize($this->extra_args));
		$filename = "cache/".base64_encode($active_app);
		$lines = file($filename);
		$lines_out = array();
		foreach($lines as $line){
			if(substr($line, 0, 32)!=$user_hash){
				$lines_out[] = $line;
			}
		}
		file_put_contents($filename, join("\n", $lines_out));
	}

	/**
	 * Devuelve la instancia del adaptador
	 *
	 * @return string
	 */
	public function get_adapter_instance(){
		return $this->adapter_object;
	}

	/**
	 * Determinar si debe dormir la aplicacion cuando falle la autenticacion y cuanto tiempo en segundos
	 *
	 * @param boolean $value
	 * @param integer $time
	 */
	public function sleep_on_fail($value, $time=2){
		$time = (int) $time;
		if($time<0){
			$time = 0;
		}
		if($value){
			$this->sleep_time = $time;
		} else {
			$this->sleep_time = 0;
		}
	}

	/**
	 * Devuelve el resultado del ultimo llamado a authenticate desde el ultimo objeto Auth instanciado
	 *
	 * @return boolean
	 */
	static public function is_valid(){
		if(!is_null(self::$is_valid)){
			return self::$is_valid;
		} else {
			self::$is_valid = isset($_SESSION['KUMBIA_AUTH_VALID']) ? $_SESSION['KUMBIA_AUTH_VALID'] : null;
			return self::$is_valid;
		}
	}

	/**
	 * Devuelve el resultado de la ultima identidad obtenida en authenticate desde el ultimo objeto Auth instanciado
	 *
	 * @return array
	 */
	static public function get_active_identity(){
		if(count(self::$active_identity)){
			return self::$active_identity;
		} else {
			self::$active_identity = $_SESSION['KUMBIA_AUTH_IDENTITY'];
			return self::$active_identity;
		}
	}

	/**
	 * Anula la identidad actual
	 *
	 */
	static public function destroy_identity(){
		self::$is_valid = null;
		unset($_SESSION['KUMBIA_AUTH_VALID']);
		self::$active_identity = null;
		unset($_SESSION['KUMBIA_AUTH_IDENTITY']);
	}

}

?>