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
 * @package Db
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @copyright Copyright (C) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see DbBaseInterface
 */
 include_once "library/kumbia/db/interface.php";

/**
 * @see DbException
 */
 include_once "library/kumbia/db/exception.php";

/**
 * @see DbLoader
 */
 include_once "library/kumbia/db/loader/loader.php";

/**
 * Clase principal que deben heredar todas las clases driver de KumbiaForms
 * contiene metodos utiles y variables generales
 *
 * $debug : Indica si se muestran por pantalla todas las operaciones sql que se
 * realizen con el driver
 * $logger : Indica si se va a logear a un archivo todas las transacciones que
 * se realizen en en driver. $logger = true crea un archivo con la fecha actual
 * en logs/ y $logger="nombre", crea un log con el nombre indicado
 * $display_errors  : Indica si se muestran los errores sql en Pantalla
 *
 * @category Kumbia
 * @package Db
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2006-2007 Giancarlo Corzo Vigil (www.antartec.com)
 * @copyright Copyright (C) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @access public
 */
class DbBase extends Object {

	/**
	 * Indica si esta en modo debug o no
	 *
	 * @var boolean
	 */
	public $debug = false;

	/**
	 * Indica si debe loggear o no (tambien permite establecer el nombre del log)
	 *
	 * @var mixed
	 */
	public $logger;

	/**
	 * Singleton de la conexi&oacute;n a la base de datos
	 *
	 * @var resource
	 */
	static private $raw_connection = null;

	private $dbLogger;
	
	/**
	 * Singleton de conexiones abiertas
	 * 
	 * @var array
	 **/
	protected static $raw_connections = array();

	/**
	 * Hace un select de una forma mas corta, listo para usar en un foreach
	 *
	 * @param string $table
	 * @param string $where
	 * @param string $fields
	 * @param string $orderBy
	 * @return array
	 */
	public function find($table, $where="1=1", $fields="*", $orderBy="1"){
		ActiveRecord::sql_item_sanizite($table);
		ActiveRecord::sql_sanizite($fields);
		ActiveRecord::sql_sanizite($orderBy);
		$q = $this->query("SELECT $fields FROM $table WHERE $where ORDER BY $orderBy");
		$results = array();
		while($row=$this->fetch_array($q)){
			$results[] = $row;
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada por numeros y asociativamente
	 *
	 * @param string $sql
	 * @param integer $type
	 * @return array
	 */
	public function in_query($sql, $type=db::DB_BOTH){
		$q = $this->query($sql);
		$results = array();
		if($q){
			while($row=$this->fetch_array($q, $type)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada por numeros y asociativamente (Alias para in_query)
	 *
	 * @param string $sql
	 * @param integer $type
	 * @return array
	 */
	public function fetch_all($sql, $type=db::DB_BOTH){
		return $this->in_query($sql, $type);
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * indexada asociativamente
	 *
	 * @param string $sql
	 * @param integer $type
	 * @return array
	 */
	public function in_query_assoc($sql){
		$q = $this->query($sql);
		$results = array();
		if($q){
			while($row=$this->fetch_array($q, db::DB_ASSOC)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Realiza un query SQL y devuelve un array con los array resultados en forma
	 * numerica
	 *
	 * @param string $sql
	 * @param integer $type
	 * @return array
	 */
	public function in_query_num($sql){
		$q = $this->query($sql);
		$results = array();
		if($q){
			while($row=$this->fetch_array($q, db::DB_NUM)){
				$results[] = $row;
			}
		}
		return $results;
	}

	/**
	 * Devuelve un array del resultado de un select de un solo registro
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetch_one($sql){
		$q = $this->query($sql);
		if($q){
			if($this->num_rows($q)>1){
				Flash::warning("Una sentencia SQL: \"$sql\" retorno mas de una fila cuando se esperaba una sola");
			}
			return $this->fetch_array($q);
		} else {
			return array();
		}
	}

	/**
	 * Realiza una inserci&oacute;n
	 *
	 * @param string $table
	 * @param array $values
	 * @param array $fields
	 * @return boolean
	 */
	public function insert($table, $values, $fields=null){
		$insert_sql = "";
		if(is_array($values)){
			if(!count($values)){
				new DbException("Imposible realizar inserci&oacute;n en $table sin datos");
			}
			if(is_array($fields)){
				$insert_sql = "INSERT INTO $table (".join(",", $fields).") VALUES (".join(",", $values).")";
			} else {
				$insert_sql = "INSERT INTO $table VALUES (".join(",", $values).")";
			}
			return $this->query($insert_sql);
		} else{
			throw new DbException("El segundo parametro para insert no es un Array");
		}
	}

	/**
	 * Actualiza registros en una tabla
	 *
	 * @param string $table
	 * @param array $fields
	 * @param array $values
	 * @param string $where_condition
	 * @return boolean
	 */
	public function update($table, $fields, $values, $where_condition=null){
		$update_sql = "UPDATE $table SET ";
		if(count($fields)!=count($values)){
			throw new DbException('Los n&uacute;mero de valores a actualizar no es el mismo de los campos');
		}
		$i = 0;
		$update_values = array();
		foreach($fields as $field){
			$update_values[] = $field.' = '.$values[$i];
			$i++;
		}
		$update_sql.= join(',', $update_values);
		if($where_condition!=null){
			$update_sql.= " WHERE $where_condition";
		}
		return $this->query($update_sql);
	}

	/**
	 * Borra registros de una tabla!
	 *
	 * @param string $table
	 * @param string $where_condition
	 */
	public function delete($table, $where_condition){
		if(trim($where_condition)){
			return $this->query("DELETE FROM $table WHERE $where_condition");
		} else {
			return $this->query("DELETE FROM $table");
		}
	}

	/**
	 * Inicia una transacci&oacute;n si es posible
	 *
	 */
	public function begin(){
		return $this->query("BEGIN");
	}


	/**
	 * Cancela una transacci&oacute;n si es posible
	 *
	 */
	public function rollback(){
		return $this->query("ROLLBACK");
	}

	/**
	 * Hace commit sobre una transacci&oacute;n si es posible
	 *
	 */
	public function commit(){
		return $this->query("COMMIT");
	}

	/**
	 * Agrega comillas o simples segun soporte el RBDM
	 *
	 * @return string
	 */
	static public function add_quotes($value){
		return "'".addslashes($value)."'";
	}

	/**
	 * Loggea las operaciones sobre la base de datos si estan habilitadas
	 *
	 * @param string $msg
	 * @param string $type
	 */
	protected function log($msg, $type){
		if($this->logger){
			if(!$this->dbLogger){
				$this->dbLogger = new Logger($this->logger);
			}
			$this->dbLogger->log($msg, $type);
		}
	}

	/**
	 * Muestra Mensajes de Debug en Pantalla si esta habilitado
	 *
	 * @param string $sql
	 */
	protected function debug($sql){
		if($this->debug){
			Flash::notice($sql);
		}
	}

	/**
	 * Realiza una conexión directa al motor de base de datos
	 * usando el driver de Kumbia
	 *
	 * $new_connection = Si es verdadero devuelve un objeto
	 * db nuevo y no el del singleton
	 *
	 * mode : modo a utilizar de los que se encuentran en environment.ini
	 *
	 * @return db
	 */
	public static function raw_connect($new_connection=false){
		$params = get_params(func_get_args());
		$new_connection = isset($params[0]) ? $params[0] : false;
		
		$active_app = Kumbia::$active_app;

		/**
		 * Cargo el mode para mi aplicacion
		 **/
		if(!isset($params['mode'])) {
			$core = Config::read('config.ini');
			$mode = isset($core->$active_app->mode) ? $core->$active_app->mode : 'production';
		} else {
			$mode = $params['mode'];
		}
		
		$environment = Config::read('environment.ini');
		$config = $environment->$mode;
		
		/**
		 * Cargo valores por defecto para la conexion
		 * en caso de que no existan
		 **/
		if(!isset($config->database->port)){
			$config->database->port = 0;
		}
		if(!isset($config->database->dsn)){
			$config->database->dsn = "";
		}
		if(!isset($config->database->host)){
			$config->database->host = "";
		}
		if(!isset($config->database->username)){
			$config->database->username = "";
		}
		if(!isset($config->database->password)){
			$config->database->password = "";
		}

		/**
		 * Si no es una conexion nueva y existe la conexion singleton
		 **/
		if(!$new_connection && isset(self::$raw_connections[$mode])) {
			return self::$raw_connections[$mode];
		}

		/**
		 * Cargo la clase adaptadora necesaria
		 **/
		if(isset($config->database->pdo)){
			$dbclass = "DbPDO{$config->database->type}";
			if(!class_exists($dbclass)) {
				/**
				 * @see DbPDO
				 */
				require_once 'library/kumbia/db/adapters/pdo.php';
				require_once 'library/kumbia/db/adapters/pdo/'.$config->database->type.'.php';
			}
		} else {
			$dbclass = "Db{$config->database->type}";
			if(!class_exists($dbclass)) {
				require_once 'library/kumbia/db/adapters/'.$config->database->type.'.php';
			}
		}
		if(!class_exists($dbclass)){
			throw new DbLoaderException("No existe la clase {$dbclass}, necesaria para iniciar el adaptador", 0);
		}
		
		/**
		 * Creo la conexion
		 **/
		if(!isset($config->database->pdo)){
			$connection = new $dbclass($config->database->host,
					  				   $config->database->username,
					  				   $config->database->password,
				  					   $config->database->name,
				  					   $config->database->port,
				  	  				   $config->database->dsn);
		} else {
			$connection = new $dbclass($config->database->dsn,
					  				   $config->database->username,
					  				   $config->database->password
				  					   );
		}
		
		/**
		 * Si no es para conexion nueva, la cargo en el singleton
		 **/
		if(!$new_connection) {
			self::$raw_connections[$mode] = $connection;
		} 
		
		return $connection;
	}

}

?>