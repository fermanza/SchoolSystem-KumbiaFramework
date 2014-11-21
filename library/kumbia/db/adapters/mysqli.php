eo que<?php
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
 * @subpackage Adapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * MySQL Improved Database Support
 *
 * Estas funciones le permiten acceder a servidores de bases de datos MySQL usando
 * la version mejorada de acceso a MySQL para librerias clientes > 4.1x.
 * Puede encontrar m�s informaci�n sobre MySQL en http://www.mysql.com/.
 * La documentaci�n de MySQL puede encontrarse en http://dev.mysql.com/doc/.
 *
 * @category Kumbia
 * @package Db
 * @subpackage Adapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @link http://www.php.net/manual/es/ref.mysql.php
 * @access Public
 *
 */
class DbMySQLi extends DbBase implements DbBaseInterface  {

	/**
	 * Resource de la Conexion a MySQL
	 *
	 * @var resource
	 */
	public $id_connection;

	/**
	 * Ultimo Resultado de una Query
	 *
	 * @var resource
	 */
	public $last_result_query;

	/**
	 * Ultima sentencia SQL enviada a MySQL
	 *
	 * @var string
	 */
	private $last_query;
	/**
	 * Ultimo error generado por MySQL
	 *
	 * @var string
	 */
	public $last_error;

	/**
	 * Resultado de Array Asociativo
	 *
	 */
	const DB_ASSOC = MYSQLI_ASSOC;

	/**
	 * Resultado de Array Asociativo y Numerico
	 *
	 */
	const DB_BOTH = MYSQLI_BOTH;

	/**
	 * Resultado de Array Numerico
	 *
	 */
	const DB_NUM = MYSQLI_NUM;


	/**
	 * Tipo de Dato Integer
	 *
	 */
	const TYPE_INTEGER = "INTEGER";

	/**
	 * Tipo de Dato Date
	 *
	 */
	const TYPE_DATE = "DATE";

	/**
	 * Tipo de Dato Varchar
	 *
	 */
	const TYPE_VARCHAR = "VARCHAR";

	/**
	 * Tipo de Dato Decimal
	 *
	 */
	const TYPE_DECIMAL = "DECIMAL";

	/**
	 * Tipo de Dato Datetime
	 *
	 */
	const TYPE_DATETIME = "DATETIME";

	/**
	 * Tipo de Dato Char
	 *
	 */
	const TYPE_CHAR = "CHAR";

	/**
	 * Hace una conexi&oacute;n a la base de datos de MySQL
	 *
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @return resource_connection
	 */
	public function connect($dbhost='', $dbuser='', $dbpass='', $dbname='', $dbport='', $dbdsn=''){

		if(!extension_loaded('mysqli')){
			throw new DbException('Debe cargar la extensi�n de PHP llamada php_mysqli');
			return false;
		}

		if($this->id_connection = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport)){
			return true;
		} else {
			throw new DbException($this->error(), $this->no_error(), false);
			return false;
		}

	}

	/**
	 * Efectua operaciones SQL sobre la base de datos
	 *
	 * @param string $sqlQuery
	 * @return resource or false
	 */
	public function query($sql_query){
		$this->debug($sql_query);
		$this->log($sql_query, Logger::DEBUG);
		if(!$this->id_connection){
			$this->connect();
			if(!$this->id_connection){
				return false;
			}
		}
		$this->last_query = $sql_query;
		if($result_query = mysqli_query($this->id_connection, $sql_query)){
			$this->last_result_query = $result_query;
			return $result_query;
		} else {
			$this->last_result_query = false;
			throw new DbException($this->error(" al ejecutar <i>\"$sql_query\"</i>"), $this->no_error());
			return false;
		}
	}

	/**
	 * Cierra la Conexi�n al Motor de Base de datos
	 *
	 * @return boolean
	 */
	public function close(){
		if($this->id_connection) {
			return mysqli_close($this->id_connection);
		}
	}

	/**
	 * Devuelve fila por fila el contenido de un select
	 *
	 * @param resource $result_query
	 * @param integer $opt
	 * @return array
	 */
	public function fetch_array($result_query='', $opt=''){
		if($opt==='') $opt = db::DB_BOTH;
		if(!$this->id_connection){
			return false;
		}
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		return mysqli_fetch_array($result_query, $opt);
	}

	/**
	 * Constructor de la Clase
	 */
	public function __construct($dbhost=null, $dbuser=null, $dbpass=null, $dbname='', $dbport='', $dbdsn=''){
		$this->connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbdsn);
	}

	/**
	 * Devuelve el numero de filas de un select
	 */
	public function num_rows($result_query=''){
		if(!$this->id_connection){
			return false;
		}
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		if(($number_rows = mysqli_num_rows($result_query))!==false){
			return $number_rows;
		} else {
			throw new DbException($this->error(), $this->no_error());
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el nombre de un campo en el resultado de un select
	 *
	 * @param integer $number
	 * @param resource $result_query
	 * @return string
	 */
	public function field_name($number, $result_query=''){
		if(!$this->id_connection){
			return false;
		}
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		if(($fieldName = mysqli_field_seek($result_query, $number))!==false){
			$field = mysqli_fetch_field($result_query);
			return $field->name;
		} else {
			throw new DbException($this->error(), $this->no_error());
			return false;
		}
		return false;
	}


	/**
	 * Se Mueve al resultado indicado por $number en un select
	 *
	 * @param integer $number
	 * @param resource $result_query
	 * @return boolean
	 */
	public function data_seek($number, $result_query=''){
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		if(($success = mysqli_data_seek($result_query, $number))!==false){
			return $success;
		} else {
			throw new DbException($this->error(), $this->no_error());
			return false;
		}
		return false;
	}

	/**
	 * Numero de Filas afectadas en un insert, update o delete
	 *
	 * @param resource $result_query
	 * @return integer
	 */
	public function affected_rows($result_query=''){
		if(($numberRows = mysqli_affected_rows($this->id_connection))!==false){
			return $numberRows;
		} else {
			throw new DbException($this->error(), $this->no_error());
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el error de MySQL
	 *
	 * @return string
	 */
	public function error($err=''){
		$this->last_error = mysqli_error($this->id_connection) ? mysqli_error($this->id_connection) : "[Error Desconocido en MySQL: $err]";
		$this->last_error.= $err;
		$this->log($this->last_error, Logger::ERROR);
		return $this->last_error;
	}

	/**
	 * Devuelve el no error de MySQL
	 *
	 * @return integer
	 */
	public function no_error(){
		return mysqli_errno($this->id_connection);
	}

	/**
	 * Devuelve el ultimo id autonumerico generado en la BD
	 *
	 * @return integer
	 */
	public function last_insert_id($table='', $primary_key=''){
		if(!$this->id_connection){
			return false;
		}
		return mysqli_insert_id($this->id_connection);
	}

	/**
	 * Verifica si una tabla existe o no
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function table_exists($table, $schema=''){
		$table = addslashes("$table");
		if($schema==''){
			$num = $this->fetch_one("select count(*) from information_schema.tables where table_name = '$table'");
		} else {
			$schema = addslashes("$schema");
			$num = $this->fetch_one("select count(*) from information_schema.tables where table_name = '$table' and table_schema = '$schema'");
		}
		return $num[0];
	}

	/**
	 * Devuelve un LIMIT valido para un SELECT del RBDM
	 *
	 * @param string $sql consulta sql
	 * @return string
	 */
	public function limit($sql){
		$params = get_params(func_get_args());
		$sql_new = $sql;
	
		if(isset($params['limit']) && is_numeric($params['limit'])){
			$sql_new.=" LIMIT $params[limit]";
		}
		
		if(isset($params['offset']) && is_numeric($params['offset'])){
			$sql_new.=" OFFSET $params[offset]";
		}
		
		return $sql_new;
	}

	/**
	 * Borra una tabla de la base de datos
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function drop_table($table, $if_exists=true){
		if($if_exists){
			return $this->query("DROP TABLE IF EXISTS $table");
		} else {
			return $this->query("DROP TABLE $table");
		}
	}

	/**
	 * Crea una tabla utilizando SQL nativo del RDBM
	 *
	 * TODO:
	 * - Falta que el parametro index funcione. Este debe listar indices compuestos multipes y unicos
	 * - Agregar el tipo de tabla que debe usarse (MySQL)
	 * - Soporte para campos autonumericos
	 * - Soporte para llaves foraneas
	 *
	 * @param string $table
	 * @param array $definition
	 * @return boolean
	 */
	public function create_table($table, $definition, $index=array()){
		$create_sql = "CREATE TABLE $table (";
		if(!is_array($definition)){
			new DbException("Definici&oacute;n invalida para crear la tabla '$table'");
			return false;
		}
		$create_lines = array();
		$index = array();
		$unique_index = array();
		$primary = array();
		$not_null = "";
		$size = "";
		foreach($definition as $field => $field_def){
			if(isset($field_def['not_null'])){
				$not_null = $field_def['not_null'] ? 'NOT NULL' : '';
			} else {
				$not_null = "";
			}
			if(isset($field_def['size'])){
				$size = $field_def['size'] ? '('.$field_def['size'].')' : '';
			} else {
				$size = "";
			}
			if(isset($field_def['index'])){
				if($field_def['index']){
					$index[] = "INDEX(`$field`)";
				}
			}
			if(isset($field_def['unique_index'])){
				if($field_def['unique_index']){
					$index[] = "UNIQUE(`$field`)";
				}
			}
			if(isset($field_def['primary'])){
				if($field_def['primary']){
					$primary[] = "`$field`";
				}
			}
			if(isset($field_def['auto'])){
				if($field_def['auto']){
					$field_def['extra'] = isset($field_def['extra']) ? $field_def['extra']." AUTO_INCREMENT" :  "AUTO_INCREMENT";
				}
			}
			if(isset($field_def['extra'])){
				$extra = $field_def['extra'];
			} else {
				$extra = "";
			}
			$create_lines[] = "`$field` ".$field_def['type'].$size.' '.$not_null.' '.$extra;
		}
		$create_sql.= join(',', $create_lines);
		$last_lines = array();
		if(count($primary)){
			$last_lines[] = 'PRIMARY KEY('.join(",", $primary).')';
		}
		if(count($index)){
			$last_lines[] = join(',', $index);
		}
		if(count($unique_index)){
			$last_lines[] = join(',', $unique_index);
		}
		if(count($last_lines)){
			$create_sql.= ','.join(',', $last_lines).')';
		}
		return $this->query($create_sql);

	}

	/**
	 * Listar las tablas en la base de datos
	 *
	 * @return array
	 */
	public function list_tables(){
		return $this->fetch_all("SHOW TABLES");
	}

	/**
	 * Listar los campos de una tabla
	 *
	 * @param string $table
	 * @return array
	 */
	public function describe_table($table, $schema=''){
		if($schema==''){
			return $this->fetch_all("DESCRIBE `$table`");
		} else {
			return $this->fetch_all("DESCRIBE `$schema`.`$table`");
		}
	}


}

?>