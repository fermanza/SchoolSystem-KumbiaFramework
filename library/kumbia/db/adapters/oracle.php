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
 * Oracle Database Support
 *
 * Estas funciones le permiten acceder a servidores de bases de datos Oracle.
 * Puede encontrar m�s informaci�n sobre Oracle en http://www.oracle.com/.
 * La documentaci�n de Oracle puede encontrarse en http://www.oracle.com/technology/documentation/index.html.
 *
 * @category Kumbia
 * @package Db
 * @subpackage Adapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @link http://www.php.net/manual/es/ref.oci8.php
 *
 */
class DbOracle extends DbBase implements DbBaseInterface  {

	/**
	 * Resource de la Conexion a Oracle
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
	 * Ultima sentencia SQL enviada a Oracle
	 *
	 * @var string
	 */
	private $last_query;

	/**
	 * Nombre del usuario en Oracle
	 *
	 * @var string
	 */
	private $db_user;

	/**
	 * Host de Oracle
	 *
	 * @var string
	 */
	private $db_host;

	/**
	 * Password del Usuario en Oracle
	 *
	 * @var string
	 */
	private $db_pass;

	/**
	 * Nombre de la base de datos en Oracle
	 *
	 * @var string
	 */
	private $db_name;

	/**
	 * Puerto de Conexi&oacute;n a Oracle
	 *
	 * @var integer
	 */
	private $db_port;

	/**
	 * DSN de conexi&oacute;n a Oracle
	 *
	 * @var string
	 */
	private $db_dsn;

	/**
	 * Ultimo error generado por Oracle
	 *
	 * @var string
	 */
	public $lastError;

	/**
	 * Indica si los modelos usan autocommit
	 *
	 * @var boolean
	 */
	private $autocommit = false;

	/**
	 * NUmero de filas devueltas
	 *
	 * @var boolean
	 */
	private $num_rows = false;

	/**
	 * Resultado de Array Asociativo
	 *
	 */
	const DB_ASSOC = OCI_ASSOC;


	/**
	 * Resultado de Array Asociativo y Numerico
	 *
	 */
	const DB_BOTH = OCI_BOTH;

	/**
	 * Resultado de Array Numerico
	 *
	 */
	const DB_NUM = OCI_NUM;

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
	const TYPE_VARCHAR = "VARCHAR2";

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
	 * Hace una conexi�n a la base de datos de Oracle
	 *
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @return resource_connection
	 */
	function connect($dbhost='', $dbuser='', $dbpass='', $dbname='', $dbport='', $dbdsn=''){

		if(!extension_loaded('oci8')){
			throw new DbException('Debe cargar la extensi�n de PHP llamada php_oci8');
			return false;
		}

		if(!$dbhost){
			$dbhost = $this->db_host;
		} else {
			$this->db_host = $dbhost;
		}
		if(!$dbuser){
			$dbuser = $this->db_user;
		} else {
			$this->db_user = $dbuser;
		}
		if(!$dbpass){
			$dbpass = $this->db_pass;
		} else {
			$this->db_pass = $dbpass;
		}
		if(!$dbname){
			$dbname = $this->db_name;
		} else {
			$this->db_name = $dbname;
		}
		if(!$dbport){
			$dbport = $this->db_port;
		} else {
			$this->db_port = $dbport;
		}
		if(!$dbdsn){
			$dbdsn = $this->db_dsn;
		} else {
			$this->db_dsn = $dbdsn;
		}
		if($this->id_connection = @oci_pconnect($this->db_user, $this->db_pass, "//{$this->db_host}/$this->db_name")){
			/**
			 * Cambio el formato de fecha al estandar YYYY-MM-DD
			 */
			$this->query("alter session set nls_date_format = 'YYYY-MM-DD'");
			return true;
		} else {
			throw new DbException($this->error($php_errormsg), false);
			return false;
		}

	}

	/**
	 * Efectua operaciones SQL sobre la base de datos
	 *
	 * @param string $sqlQuery
	 * @return resource or false
	 */
	function query($sqlQuery){
		$this->debug($sqlQuery);
		$this->log($sqlQuery, Logger::DEBUG);
		if(!$this->id_connection){
			$this->connect();
			if(!$this->id_connection){
				return false;
			}
		}
		$this->num_rows = false;
		$this->lastQuery = $sqlQuery;
		$resultQuery = @oci_parse($this->id_connection, $sqlQuery);
		if($resultQuery){
			$this->last_result_query = $resultQuery;
		} else {
			$this->last_result_query = false;
			throw new DbException($this->error($php_errormsg), $this->no_error());
			return false;
		}
		if($this->autocommit){
			$commit = OCI_COMMIT_ON_SUCCESS;
		} else {
			$commit = OCI_DEFAULT;
		}

		if(!@oci_execute($resultQuery, $commit)){
			$this->last_result_query = false;
			throw new DbException($this->error($php_errormsg), $this->no_error());
			return false;
		}
		return $resultQuery;
	}

	/**
	 * Cierra la Conexi�n al Motor de Base de datos
	 */
	function close(){
		if($this->id_connection) {
			return oci_close($this->id_connection);
		}
	}

	/**
	 * Devuelve fila por fila el contenido de un select
	 *
	 * @param resource $resultQuery
	 * @param integer $opt
	 * @return array
	 */
	function fetch_array($resultQuery='', $opt=''){
		if($opt==='') $opt = db::DB_BOTH;
		if(!$this->id_connection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->last_result_query;
			if(!$resultQuery){
				return false;
			}
		}
		$result = oci_fetch_array($resultQuery, $opt);
		if(is_array($result)){
			$result_to_lower = array();
			foreach($result as $key => $value){
				$result_to_lower[strtolower($key)] = $value;
			}
			return $result_to_lower;
		} else {
			return false;
		}
		return false;
	}

	/**
	 * Constructor de la Clase
	 */
	function __construct($dbhost=null, $dbuser=null, $dbpass=null, $dbname='', $dbport='', $dbdsn=''){
		$this->connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbdsn);
	}

	/**
	 * Devuelve el numero de filas de un select
	 */
	function num_rows($resultQuery=''){
		if(!$this->id_connection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->last_result_query;
			if(!$resultQuery){
				throw new DbException($this->error('Resource invalido para db::num_rows'), $this->no_error());
				return false;
			}
		}
		/**
		 * El Adaptador cachea la ultima llamada a num_rows por razones de performance
		 */
		/*if($resultQuery==$this->last_result_query){
			if($this->num_rows!==false){
				return $this->num_rows;
			}
		}*/
		if($this->autocommit){
			$commit = OCI_COMMIT_ON_SUCCESS;
		} else {
			$commit = OCI_DEFAULT;
		}
		if(!@oci_execute($resultQuery, $commit)){
			$this->last_result_query = false;
			throw new DbException($this->error($php_errormsg." al ejecutar <i>'{$this->lastQuery}'</i>"), $this->no_error());
			return false;
		}
		$tmp = array();
		$this->num_rows = oci_fetch_all($resultQuery, $tmp);
		unset($tmp);
		@oci_execute($resultQuery, $commit);
		return $this->num_rows;
	}

	/**
	 * Devuelve el nombre de un campo en el resultado de un select
	 *
	 * @param integer $number
	 * @param resource $resultQuery
	 * @return string
	 */
	function field_name($number, $resultQuery=''){
		if(!$this->id_connection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->last_result_query;
			if(!$resultQuery){
				throw new DbException($this->error('Resource invalido para db::field_name'), $this->no_error());
				return false;
			}
		}

		if(($fieldName = oci_field_name($resultQuery, $number+1))!==false){
			return strtolower($fieldName);
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
	 * @param resource $resultQuery
	 * @return boolean
	 */
	function data_seek($number, $resultQuery=''){
		if(!$resultQuery){
			$resultQuery = $this->last_result_query;
			if(!$resultQuery){
				throw new DbException($this->error('Resource invalido para db::data_seek'), $this->no_error());
				return false;
			}
		}
		if($this->autocommit){
			$commit = OCI_COMMIT_ON_SUCCESS;
		} else {
			$commit = OCI_DEFAULT;
		}
		if(!@oci_execute($resultQuery, $commit)){
			throw new DbException($this->error($php_errormsg." al ejecutar <i>'{$this->lastQuery}'</i>"), $this->no_error());
			return false;
		}
		if($number){
			for($i=0;$i<=$number-1;$i++){
				if(!oci_fetch_row($resultQuery)){
					return false;
				}
			}
		} else {
			return true;
		}
		return true;
	}

	/**
	 * N�mero de Filas afectadas en un insert, update � delete
	 *
	 * @param resource $resultQuery
	 * @return integer
	 */
	function affected_rows($resultQuery=''){
		if(!$this->id_connection){
			return false;
		}
		if(!$resultQuery){
			$resultQuery = $this->last_result_query;
			if(!$resultQuery){
				return false;
			}
		}
		if(($numberRows = oci_num_rows($resultQuery))!==false){
			return $numberRows;
		} else {
			throw new DbException($this->error('Resource invalido para db::affected_rows'), $this->no_error());
			return false;
		}
		return false;
	}

	/**
	 * Devuelve el error de Oracle
	 *
	 * @return string
	 */
	function error($err=''){
		if(!$this->id_connection){
			$error = oci_error() ? oci_error() : "[Error Desconocido en Oracle]";
			if(is_array($error)){
				$error['message'].=" > $err ";
				return $error['message'];
			} else {
				$error.=" $php_errormsg ";
				return $error;
			}
		}
		$error = oci_error($this->id_connection);
		if($error){
			$error['message'].=" > $err ";
		} else {
			$error['message'] = $err;
		}
		return $error['message'];
	}

	/**
	 * Devuelve el no error de Oracle
	 *
	 * @return integer
	 */
	function no_error(){
		if(!$this->id_connection){
			$error = oci_error() ? oci_error() : "0";
			if(is_array($error)){
				return $error['code'];
			} else {
				return $error;
			}
		}
		$error = oci_error($this->id_connection);
		return $error['code'];
	}

	/**
	 * Devuelve un LIMIT valido para un SELECT del RBDM
	 *
	 * @param integer $number
	 * @return string
	 */
	public function limit($sql, $number){
		if(!is_numeric($number)||$number<0){
			return $sql;
		}
		if(eregi("ORDER[\t\n\r ]+BY", $sql)){
			if(stripos($sql, "WHERE")){
				return eregi_replace("ORDER[\t\n\r ]+BY", "AND ROWNUM <= $number ORDER BY", $sql);
			} else {
				return eregi_replace("ORDER[\t\n\r ]+BY", "WHERE ROWNUM <= $number ORDER BY", $sql);
			}
		} else {
			if(stripos($sql, "WHERE")){
				return "$sql AND ROWNUM <= $number";
			} else {
				return "$sql WHERE ROWNUM <= $number";
			}
		}
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
				return $this->query("DROP TABLE $table");
			} else {
				return true;
			}
		} else {
			return $this->query("DROP TABLE $table");
		}
	}

	/**
	 * Crea una tabla utilizando SQL nativo del RDBM
	 *
	 * TODO:
	 * - Falta que el parametro index funcione. Este debe listar indices compuestos multipes y unicos
	 * - Agregar el tipo de tabla que debe usarse (Oracle)
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
					$this->query("CREATE SEQUENCE {$table}_{$field}_seq START WITH 1");
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
		return $this->query($create_sql);

	}

	/**
	 * Listado de Tablas
	 *
	 * @param string $table
	 * @return boolean
	 */
	function list_tables(){
		return $this->fetch_all("SELECT table_name FROM all_tables");
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
		/**
		 * Oracle No soporta columnas autonum&eacute;ricas
		 */
		if($table&&$primary_key){
			$sequence = $table."_".$primary_key."_seq";
			$value = $this->fetch_one("SELECT $sequence.CURRVAL FROM dual");
			return $value[0];
		}
		return false;
	}

	/**
	 * Verifica si una tabla existe o no
	 *
	 * @param string $table
	 * @return boolean
	 */
	function table_exists($table, $schema=''){
		$num = $this->fetch_one("SELECT COUNT(*) FROM ALL_TABLES WHERE TABLE_NAME = '".strtoupper($table)."'");
		return $num[0];
	}

	/**
	 * Listar los campos de una tabla
	 *
	 * @param string $table
	 * @return array
	 */
	public function describe_table($table, $schema=''){
		/**
		 * Soporta schemas?
		 */
		$describe = $this->fetch_all("SELECT LOWER(ALL_TAB_COLUMNS.COLUMN_NAME) AS FIELD, LOWER(ALL_TAB_COLUMNS.DATA_TYPE) AS TYPE, ALL_TAB_COLUMNS.DATA_LENGTH AS LENGTH, (SELECT COUNT(*) FROM ALL_CONS_COLUMNS WHERE TABLE_NAME = '".strtoupper($table)."' AND ALL_CONS_COLUMNS.COLUMN_NAME = ALL_TAB_COLUMNS.COLUMN_NAME AND ALL_CONS_COLUMNS.POSITION IS NOT NULL) AS KEY, ALL_TAB_COLUMNS.NULLABLE AS ISNULL FROM ALL_TAB_COLUMNS WHERE ALL_TAB_COLUMNS.TABLE_NAME = '".strtoupper($table)."'");
		$final_describe = array();
		foreach($describe as $key => $value){
			$final_describe[] = array(
				"Field" => $value["field"],
				"Type" => $value["type"],
				"Null" => $value["isnull"] == "Y" ? "YES" : "NO",
				"Key" => $value["key"] == 1 ? "PRI" : ""
			);
		}
		return $final_describe;
	}

	/**
	 * Inicia una transacci&oacute;n si es posible
	 *
	 */
	public function begin(){
		//Simpre hay una transaccion
		//return $this->query("BEGIN WORK");
		return true;
	}

}

?>