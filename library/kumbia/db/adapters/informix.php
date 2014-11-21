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
 * @subpackage Adapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Informix Database Support
 *
 * Estas funciones le permiten acceder a servidores de bases de datos Informix.
 * Puede encontrar m�s informaci�n sobre Informix IDS en http://www.ibm.com/informix.
 *
 * @category Kumbia
 * @package Db
 * @subpackage Adapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @link http://www.php.net/manual/es/ref.ifx.php
 * @access Public
 *
 */
class DbInformix extends DbBase implements DbBaseInterface  {

	/**
	 * Resource de la Conexion a Informix
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
	 * Ultima sentencia SQL enviada a Informix
	 *
	 * @var string
	 */
	private $last_query;

	/**
	 * Nombre del usuario en Informix
	 *
	 * @var string
	 */
	private $db_user;

	/**
	 * Host de Informix
	 *
	 * @var string
	 */
	private $db_host;

	/**
	 * Password del Usuario en Informix
	 *
	 * @var string
	 */
	private $db_pass;

	/**
	 * Nombre de la base de datos en Informix
	 *
	 * @var string
	 */
	private $db_name;

	/**
	 * Puerto de Conexi&oacute;n a Informix
	 *
	 * @var integer
	 */
	private $db_port = 3306;

	/**
	 * DSN de conexi&oacute;n a Informix
	 *
	 * @var string
	 */
	private $db_dsn;

	/**
	 * Ultimo error generado por Informix
	 *
	 * @var string
	 */
	public $last_error;

	/**
	 * Indica si query devuelve registros o no;
	 *
	 * @var boolean
	 */
	private $return_rows = true;

	/**
	 * Emula un limit a nivel de Adaptador para Informix
	 */
	private $limit = -1;

	/**
	 * Numero de limit actual para fetch_array
	 */
	private $actual_limit = 0;

	/**
	 * Resultado de Array Asociativo
	 *
	 */
	const DB_ASSOC = 1;

	/**
	 * Resultado de Array Asociativo y Numerico
	 *
	 */
	const DB_BOTH = 2;

	/**
	 * Resultado de Array Numerico
	 *
	 */
	const DB_NUM = 3;

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
	 * Hace una conexi&oacute;n a la base de datos de Informix
	 *
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @param string $dbport
	 * @param string $dbdsn
	 * @return resource_connection
	 */
	public function connect($dbhost='', $dbuser='', $dbpass='', $dbname='', $dbport='', $dbdsn=''){

		if(!extension_loaded('informix')){
			throw new DbException('Debe cargar la extensi�n de PHP llamada php_ifx');
			return false;
		}

		if(!$dbhost) {
			$dbhost = $this->db_host;
		} else {
			$this->db_host = $dbhost;
		}
		if(!$dbuser) {
			$dbuser = $this->db_user;
		} else {
			$this->db_user = $dbuser;
		}
		if(!$dbpass) {
			$dbpass = $this->db_pass;
		} else {
			$this->db_pass = $dbpass;
		}
		if(!$dbport) {
			$dbport = $this->db_port;
		} else {
			$this->db_port = $dbport;
		}
		if(!$dbdsn) {
			$dbdsn = $this->db_dsn;
		} else {
			$this->db_dsn = $dbdsn;
		}
		if(!$dbname) {
			$dbname = $this->db_name;
		} else {
			$this->db_name = $dbname;
		}
		if($this->id_connection = ifx_connect("{$this->db_name}@{$this->db_host}", $this->db_user, $this->db_pass)){
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
		/**
		 * Los resultados que devuelven filas usan cursores tipo SCROLL
		 */
		if($this->return_rows){
			$result_query = ifx_query($sql_query, $this->id_connection, IFX_HOLD);
		} else {
			$result_query = ifx_query($sql_query, $this->id_connection);
		}
		$this->set_return_rows(true);
		if($result_query===false){
			$this->last_result_query = false;
			throw new DbException($this->error(" al ejecutar <i>\"$sql_query\"</i>"), $this->no_error());
			return false;
		} else {
			$this->last_result_query = $result_query;
			return $result_query;
		}
	}

	/**
	 * Cierra la Conexi�n al Motor de Base de datos
	 */
	public function close(){
		if($this->id_connection) {
			return ifx_close($this->id_connection);
		}
		return false;
	}

	/**
	 * Devuelve fila por fila el contenido de un select
	 *
	 * @param resource $result_query
	 * @param integer $opt
	 * @return array
	 */
	public function fetch_array($result_query='', $opt=''){
		if($opt==='') {
			$opt = db::DB_BOTH;
		}
		if(!$this->id_connection){
			return false;
		}
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		$fetch = ifx_fetch_row($result_query, $opt);
		/**
		 * Informix no soporta limit por eso hay que emularlo
		 */
		if($this->limit!=-1){
			if($this->actual_limit>=$this->limit){
				$this->limit = -1;
				$this->actual_limit = 0;
				return false;
			} else {
				$this->actual_limit++;
				if($this->actual_limit==$this->limit){
					$this->limit = -1;
					$this->actual_limit = 0;
				}
			}
		}
		/**
		 * Informix no soporta fecth numerico, solo asociativo
		 */
		if(!is_array($fetch)||($opt==db::DB_ASSOC)){
			return $fetch;
		}
		if($opt==db::DB_BOTH){
			$result = array();
			$i = 0;
			foreach($fetch as $key => $value){
				$result[$key] = $value;
				$result[$i++] = $value;
			}
			return $result;
		}
		if($opt==db::DB_NUM){
			return array_values($fetch);
		}
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
		if(($number_rows = ifx_num_rows($result_query))!==false){
			/**
			 * Emula un limit a nivel de adaptador
			 */
			if($this->limit==-1){
				return $number_rows;
			} else {
				return $this->limit < $number_rows ? $this->limit : $number_rows;
			}
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
		$fields = ifx_fieldproperties($result_query);
		if(!is_array($fields)){
			return false;
		}

		$fields = array_keys($fields);
		return $fields[$number];
	}


	/**
	 * Se Mueve al resultado indicado por $number en un select
	 * Hay problemas con este metodo hay problemas con curesores IFX_SCROLL
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
		if(($success = ifx_fetch_row($result_query, $number))!==false){
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
		if(!$result_query){
			$result_query = $this->last_result_query;
			if(!$result_query){
				return false;
			}
		}
		if(($numberRows = ifx_affected_rows($result_query))!==false){
			return $numberRows;
		} else {
			$this->lastError = $this->error();
			throw new DbException($this->error(), $this->no_error());
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el error de Informix
	 *
	 * @return string
	 */
	public function error($err=''){
		if(!$this->id_connection){
			$this->last_error = ifx_errormsg() ? ifx_errormsg() : "[Error Desconocido en Informix: $err]";
			$this->log($this->last_error, Logger::ERROR);
			return $this->last_error;
		}
		$this->last_error = ifx_errormsg($this->id_connection) ? ifx_errormsg($this->id_connection) : "[Error Desconocido en Informix: $err]";
		$this->last_error.= $err;
		$this->log($this->last_error, Logger::ERROR);
		return $this->last_error;
	}

	/**
	 * Devuelve el no error de Informix
	 *
	 * @return integer
	 */
	public function no_error(){
		if(!$this->id_connection){
			return ifx_error();
		}
		return ifx_error();
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
		$sqlca = ifx_getsqlca($this->last_result_query);
		return $sqlca["sqlerrd1"];
	}

	/**
	 * Verifica si una tabla existe o no
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function table_exists($table, $schema=''){
		/**
		 * Informix no soporta schemas
		 */
		$table = addslashes("$table");
		$num = $this->fetch_one("SELECT COUNT(*) FROM systables WHERE tabname = '$table'");
		return (int) $num[0];
	}

	/**
	 * Devuelve un LIMIT valido para un SELECT del RBDM
	 *
	 * @param integer $number
	 * @return string
	 */
	public function limit($sql, $number){
		/**
		 * No esta soportado por Informix
		 */
		$number = (int) $number;
		$this->limit = $number;
		return "$sql -- LIMIT $number\n";
	}

	/**
	 * Borra una tabla de la base de datos
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function drop_table($table, $if_exists=true){
		if($if_exists){
			if($this->table_exists($table)){
				$this->set_return_rows(false);
				return $this->query("DROP TABLE $table");
			} else {
				return true;
			}
		} else {
			$this->set_return_rows(false);
			return $this->query("DROP TABLE $table");
		}
	}

	/**
	 * Crea una tabla utilizando SQL nativo del RDBM
	 *
	 * TODO:
	 * - Falta que el parametro index funcione. Este debe listar indices compuestos multipes y unicos
	 * - Agregar el tipo de tabla que debe usarse (Informix)
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
					$index[] = "INDEX($field)";
				}
			}
			if(isset($field_def['unique_index'])){
				if($field_def['unique_index']){
					$index[] = "UNIQUE($field)";
				}
			}
			if(isset($field_def['primary'])){
				if($field_def['primary']){
					$primary[] = "$field";
				}
			}
			if(isset($field_def['auto'])){
				if($field_def['auto']){
					$field_def['type'] = "SERIAL";
				}
			}
			if(isset($field_def['extra'])){
				$extra = $field_def['extra'];
			} else {
				$extra = "";
			}
			$create_lines[] = "$field ".$field_def['type'].$size.' '.$not_null.' '.$extra;
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
		$this->set_return_rows(false);
		return $this->query($create_sql);

	}

	/**
	 * Listar las tablas en la base de datos
	 *
	 * @return array
	 */
	public function list_tables(){
		return $this->fetch_all("SELECT tabname FROM systables WHERE tabtype = 'T' AND version <> 65537");
	}

	/**
	 * Listar los campos de una tabla
	 *
	 * @param string $table
	 * @return array
	 */
	public function describe_table($table, $schema=''){
		/**
		 * Informix no soporta schemas
		 * TODO: No hay un metodo identificable para obtener llaves primarias
		 * no nulos y tama�os reales de campos
		 * Primary Key, Null?
		 */
		$describe = $this->fetch_all("SELECT c.colname AS Field, c.coltype AS Type,
				'YES' AS NULL FROM systables t, syscolumns c WHERE
		 		c.tabid = t.tabid AND t.tabname = '$table' ORDER BY c.colno");
		$final_describe = array();
		foreach($describe as $field){
			//Serial
			if($field['field']=='id'){
				$field["key"] = 'PRI';
				$field["null"] = 'NO';
			} else {
				$field["key"] = '';
			}
			if(substr($field['field'], -3)=='_id'){
				$field["null"] = 'NO';
			}
			if($field['type']==262){
				$field['type'] = "serial";
			}
			if($field['type']==13){
				$field['type'] = "varchar";
			}
			if($field['type']==7){
				$field['type'] = "date";
			}
			$final_describe[] = array(
				"Field" => $field["field"],
				"Type" => $field["type"],
				"Null" => $field["null"],
				"Key" => $field["key"]
			);
		}
		return $final_describe;

	}

	/**
	 * Realiza una inserci&oacute;n (Sobreescrito para indicar que no devuelve registros)
	 *
	 * @param string $table
	 * @param array $values
	 * @param array $fields
	 * @return boolean
	 */
	public function insert($table, $values, $fields=null){
		$this->set_return_rows(false);
		return parent::insert($table, $values, $fields);
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
		$this->set_return_rows(false);
		return parent::update($table, $fields, $values, $where_condition);
	}

	/**
	 * Borra registros de una tabla!
	 *
	 * @param string $table
	 * @param string $where_condition
	 */
	public function delete($table, $where_condition){
		$this->set_return_rows(false);
		return parent::delete($table, $where_condition);
	}

	/**
	 * Indica internamente si el resultado obtenido es devuelve registros o no
	 */
	public function set_return_rows($value=true){
		$this->return_rows = $value;
	}

	/**
	 * Inicia una transacci&oacute;n si es posible
	 *
	 */
	public function begin(){
		$this->set_return_rows(false);
		return $this->query("BEGIN WORK");
	}


	/**
	 * Cancela una transacci&oacute;n si es posible
	 *
	 */
	public function rollback(){
		$this->set_return_rows(false);
		return $this->query("ROLLBACK");
	}

	/**
	 * Hace commit sobre una transacci&oacute;n si es posible
	 *
	 */
	public function commit(){
		$this->set_return_rows(false);
		return $this->query("COMMIT");
	}

}

?>