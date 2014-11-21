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
 * @package Logger
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see LoggerException
 */
require_once "library/kumbia/logger/exception.php";

/**
 * Permite realizar logs en archivos de texto en la carpeta Logs
 *
 * $fileLogger = Es el File Handle para escribir los logs
 * $transaction = Indica si hay o no transaccion
 * $quenue = array con lista de logs pendientes
 *
 * Ej:
 * <code>
 * //Empieza un log en logs/logDDMMY.txt
 * $myLog = new Logger();
 *
 * $myLog->log("Loggear esto como un debug", Logger::DEBUG);
 *
 * //Esto se guarda al log inmediatamente
 * $myLog->log("Loggear esto como un error", Logger::ERROR);
 *
 * //Inicia una transacción
 * $myLog->begin();
 *
 * //Esto queda pendiente hasta que se llame a commit para guardar
 * //ó rollback para cancelar
 * $myLog->log("Esto es un log en la fila", Logger::WARNING)
 * $myLog->log("Esto es un otro log en la fila", Logger::WARNING)*
 *
 * //Se guarda al log
 * $myLog->commit();
 *
 * //Cierra el Log
 * $myLog->close();
 * </code>
 *
 * @category Kumbia
 * @package Logger
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */
class Logger extends Object {

	/**
	 * Resource hacia el Archivo del Log
	 *
	 * @var resource
	 */
	private $fileLogger;

	/**
	 * Indica si hay transaccion o no
	 *
	 * @var boolean
	 */
	private $transaction = false;

	/**
	 * Array con mensajes de log en cola en una transsaccion
	 *
	 * @var array
	 */
	private $quenue = array();

	/**
	 * Path donde se guardaran los logs
	 *
	 * @var string
	 */
	private $log_path = "logs/";

	const DEBUG = 1;
	const ERROR = 2;
	const WARNING = 3;
	const CUSTOM = 4;
	const CRITICAL = 5;
	const INFO = 6;
	const ALERT = 7;
	const EMERGENCE = 8;
	const NOTICE = 9;

	/**
 	 * Constructor del Logger
 	 */
	function __construct($name=''){
		if($name===''||$name===true){
			$name = 'log'.date('dmY').".txt";
		}
		$this->fileLogger = @fopen($this->log_path.$name, "a");
		if(!$this->fileLogger){
			throw new LoggerException("No se puede abrir el log llamado: ".$name);
			return false;
		}
	}

	/**
	 * Especifica el PATH donde se guardan los logs
	 *
	 * @param string $path
	 */
	public function set_path($path){
		$this->log_path = $path;
	}

	/**
	 * Obtener el path actual
	 *
	 * @return $path
	 */
	public function get_path(){
		return $this->log_path;
	}


	/**
 	 * Almacena un mensaje en el log
 	 *
 	 * @param string $msg
 	 */
	function log($msg, $type=self::DEBUG){
		if(!$this->fileLogger){
			throw new LoggerException("No se puede enviar mensaje al log porque es invalido");
		}
		if(is_array($msg)){
			$msg = print_r($msg, true);
		}
		if(PHP_VERSION>=5.1) {
			$date = date(DATE_RFC1036);
		} else {
			$date = date("r");
		}
		switch($type){
			case self::DEBUG:
				$type = 'DEBUG';
				break;
			case self::ERROR:
				$type = 'ERROR';
				break;
			case self::WARNING:
				$type = 'WARNING';
				break;
			case self::CUSTOM :
				$type = 'CUSTOM';
				break;
			case self::CRITICAL:
				$type = 'CRITICAL';
				break;
			case self::ALERT:
				$type = 'ALERT';
				break;
			case self::NOTICE:
				$type = 'NOTICE';
				break;
			case self::EMERGENCE:
				$type = 'EMERGENCE';
				break;
			case self::INFO:
				$type = 'INFO';
				break;
			default:
				$type = 'CUSTOM';
		}
		if($this->transaction){
			$this->quenue[] = "[$date][$type] ".$msg."\n";
		} else {
			fputs($this->fileLogger, "[$date][$type] ".$msg."\n");
		}
	}

	/**
 	 * Inicia una transacción
 	 *
 	 */
	function begin(){
		$this->transaction = true;
	}

	/**
 	 * Deshace una transacción
 	 *
 	 */
	function rollback(){
		$this->transaction = false;
		$this->quenue = array();
	}

	/**
 	 * Commit a una transacción
 	 */
	function commit(){
		$this->transaction = false;
		foreach($this->quenue as $msg){
			$this->log($msg);
		}
	}

	/**
 	 * Cierra el Logger
 	 *
 	 */
	function close(){
		if(!$this->fileLogger){
			throw new LoggerException("No se puede cerrar el log porque es invalido");
		}
		return fclose($this->fileLogger);
	}

	/**
	 * Hace dinamicamente el llamado a log usando nombres definidos
	 *
	 * @param string $type
	 * @param array $args
	 */
	function __call($type, $args=array()){
		switch($type){
			case 'debug':
				$args[1] = Logger::DEBUG;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'error':
				$args[1] = Logger::ERROR;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'warning':
				$args[1] = Logger::WARNING;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'alert':
				$args[1] = Logger::ALERT;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'notice':
				$args[1] = Logger::NOTICE;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'emergence':
				$args[1] = Logger::EMERGENCE;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'custom':
				$args[1] = Logger::CUSTOM;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'info':
				$args[1] = Logger::INFO;
				call_user_func_array(array($this, "log"), $args);
				break;
			case 'critical':
				$args[1] = Logger::CRITICAL;
				call_user_func_array(array($this, "log"), $args);
				break;
			default:
				throw new LoggerException("Tipo indefinido de excepci&oacute;n [".$args[0]."]", 0);
		}
	}

}

?>