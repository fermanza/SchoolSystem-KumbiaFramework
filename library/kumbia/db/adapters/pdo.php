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
 * @see DbPDOInterface
 */
require_once "library/kumbia/db/adapters/pdo/interface.php";

/**
 * PHP Data Objects
 *
 * The PHP Data Objects (PDO) extension defines a lightweight, consistent interface
 * for accessing databases in PHP. Each database driver that implements the PDO interface
 * can expose database-specific features as regular extension functions. Note that you cannot
 * perform any database functions using the PDO extension by itself; you must use
 * a database-specific PDO driver to access a database server.
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
abstract class DbPDO extends DbBase implements DbPDOInterface  {

	/**
	 * Instancia PDO
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * Ultimo Resultado de una Query
	 *
	 * @var PDOStament
	 */
	public $pdo_statement;

	/**
	 * Ultima sentencia SQL enviada
	 *
	 * @var string
	 */
	protected $last_query;

	/**
	 * DSN de conexi&oacute;n
	 *
	 * @var string
	 */
	protected $db_dsn;

	/**
	 * Nombre del usuario
	 *
	 * @var string
	 */
	private $db_user;

	/**
	 * Password del Usuario
	 *
	 * @var string
	 */
	private $db_pass;

	/**
	 * Ultimo error generado
	 *
	 * @var string
	 */
	protected $last_error;

	/**
	 * Numero de filas afectadas
	 */
	protected $affected_rows;

	/**
	 * Resultado de Array Asociativo
	 *
	 */
	const DB_ASSOC = PDO::FETCH_ASSOC;

	/**
	 * Resultado de Array Asociativo y Numerico
	 *
	 */
	const DB_BOTH = PDO::FETCH_BOTH;

	/**
	 * Resultado de Array Numerico
	 *
	 */
	const DB_NUM = PDO::FETCH_NUM;


	/**
	 * Hace una conexi&oacute;n a la base de datos de MySQL
	 *
	 * @param string $dbdsn
	 * @return resource_connection
	 */
	public function connect($dbdsn, $dbuser='', $dbpass=''){

		if(!extension_loaded('pdo')){
			throw new DbException('Debe cargar la extensi�n de PHP llamada php_pdo');
			return false;
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
		if(!$dbdsn) {
			$dbdsn = $this->db_dsn;
		} else {
			$this->db_dsn = $this->db_rbdm.":".$dbdsn;
		}

		try {
			$this->pdo = new PDO($this->db_dsn, $this->db_user, $this->db_pass);
			if(!$this->pdo){
				throw new dbException("No se pudo realizar la conexion con ".$this->db_rbdm, 0, false);
			}
			if($this->db_rbdm!='odbc'){
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
				$this->pdo->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY);
			}
			$this->initialize();
			return true;
		}
		catch(PDOException $e){
			throw new DbException($this->error($e->getMessage()), $this->no_error($e->getCode()), false);
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
		if(!$this->pdo){
			throw new DbException("No hay conexi&oacute;n para realizar esta acci&oacute;n:", 0);
		}
		$this->last_query = $sql_query;
		$this->pdo_statement = null;
		try {
			if($pdo_statement = $this->pdo->query($sql_query)){
				$this->pdo_statement = $pdo_statement;
				return $pdo_statement;
			} else {
				return false;
			}
		}
		catch(PDOException $e) {
			throw new DbException($this->error($e->getMessage()." al ejecutar <i>\"$sql_query\"</i>"), $this->no_error($e->getCode()));
		}
	}

	/**
	 * Efectua operaciones SQL sobre la base de datos y devuelve el numero de filas afectadas
	 *
	 * @param string $sqlQuery
	 * @return resource or false
	 */
	public function exec($sql_query){
		$this->debug(">".$sql_query);
		$this->log($sql_query, Logger::DEBUG);
		if(!$this->pdo){
			throw new DbException("No hay conexi&oacute;n para realizar esta acci&oacute;n:", 0);
		}
		$this->last_query = $sql_query;
		$this->pdo_statement = null;
		try {
			$result = $this->pdo->exec($sql_query);
			$this->affected_rows = $result;
			if($result===false){
				throw new DbException($this->error(" al ejecutar <i>\"$sql_query\"</i>"), $this->no_error());
			}
			return $result;
		}
		catch(PDOException $e) {
			throw new DbException($this->error(" al ejecutar <i>\"$sql_query\"</i>"), $this->no_error());
		}
	}

	/**
	 * Cierra la Conexi�n al Motor de Base de datos
	 */
	public function close(){
		if($this->pdo) {
			unset($this->pdo);
			return true;
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
	public function fetch_array($pdo_statement='', $opt=''){
		if($opt==='') {
			$opt = db::DB_BOTH;
		}
		if(!$this->pdo){
			throw new DbException("No hay conexi&oacute;n para realizar esta acci&oacute;n:", 0);
			return false;
		}
		if(!$pdo_statement){
			$pdo_statement = $this->pdo_statement;
			if(!$pdo_statement){
				return false;
			}
		}
		try {
			$pdo_statement->setFetchMode($opt);
			return $pdo_statement->fetch();
		}
		catch(PDOException $e) {
			throw new DbException($this->error($e->getMessage()), $this->no_error($e->getCode()));
		}
	}

	/**
	 * Constructor de la Clase
	 */
	public function __construct($dbhost=null, $dbuser=null, $dbpass=null, $dbname='', $dbport='', $dbdsn=''){
		$this->connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbdsn);
	}

	/**
	 * Devuelve el numero de filas de un select (No soportado en PDO)
	 *
	 * @param PDOStatement $pdo_statement
	 * @deprecated
	 * @return integer
	 */
	public function num_rows($pdo_statement=''){
		if($pdo_statement){
			$pdo = clone $pdo_statement;
			return count($pdo->fetchAll(PDO::FETCH_NUM));
		} else {
			return 0;
		}
	}

	/**
	 * Devuelve el nombre de un campo en el resultado de un select
	 *
	 * @param integer $number
	 * @param resource $result_query
	 * @return string
	 */
	public function field_name($number, $pdo_statement=''){
		if(!$this->pdo){
			throw new DbException("No hay conexi&oacute;n para realizar esta acci&oacute;n:", 0);
			return false;
		}
		if(!$pdo_statement){
			$pdo_statement = $this->pdo_statement;
			if(!$pdo_statement){
				return false;
			}
		}
		try {
			$meta = $pdo_statement->getColumnMeta($number);
			return $meta['name'];
		}
		catch(PDOException $e) {
			throw new DbException($this->error($e->getMessage()), $this->no_error($e->getCode()));
		}
		return false;
	}


	/**
	 * Se Mueve al resultado indicado por $number en un select (No soportado por PDO)
	 *
	 * @param integer $number
	 * @param PDOStatement $result_query
	 * @return boolean
	 */
	public function data_seek($number, $pdo_statement=''){
		return false;
	}

	/**
	 * Numero de Filas afectadas en un insert, update o delete
	 *
	 * @param resource $result_query
	 * @deprecated
	 * @return integer
	 */
	public function affected_rows($pdo_statement=''){
		if(!$this->pdo){
			throw new DbException("No hay conexi&oacute;n para realizar esta acci&oacute;n:", 0);
			return false;
		}
		if($pdo_statement){
			try {
				$row_count = $pdo_statement->rowCount();
				if($row_count===false){
					throw new DbException($this->error(" al ejecutar <i>\"$sql_query\"</i>"), $this->no_error());
				}
				return $row_count;
			}
			catch(PDOException $e) {
				throw new DbException($this->error($e->getMessage()), $this->no_error($e->getCode()));
			}
		} else {
			return $this->affected_rows;
		}
		return false;
	}

	/**
	 * Devuelve el error de MySQL
	 *
	 * @return string
	 */
	public function error($err=''){
		if($this->pdo){
			$error = $this->pdo->errorInfo();
			$error = $error[2];
		} else {
			$error = "";
		}
		$this->last_error.= $error." [".$err."]";
		$this->log($this->last_error, Logger::ERROR);
		return $this->last_error;
	}

	/**
	 * Devuelve el no error de MySQL
	 *
	 * @return integer
	 */
	public function no_error($number=0){
		if($this->pdo){
			$error = $this->pdo->errorInfo();
			$number = $error[1];
		}
		return $number;
	}

	/**
	 * Devuelve el ultimo id autonumerico generado en la BD
	 *
	 * @return integer
	 */
	public function last_insert_id($table='', $primary_key=''){
		if(!$this->pdo){
			return false;
		}
		return $this->pdo->lastInsertId();
	}

	/**
	 * Inicia una transacci&oacute;n si es posible
	 *
	 */
	public function begin(){
		return $this->pdo->beginTransaction();
	}


	/**
	 * Cancela una transacci&oacute;n si es posible
	 *
	 */
	public function rollback(){
		return $this->pdo->rollBack();
	}

	/**
	 * Hace commit sobre una transacci&oacute;n si es posible
	 *
	 */
	public function commit(){
		return $this->pdo->commit();
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
			return $this->exec($insert_sql);
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
		return $this->exec($update_sql);
	}

	/**
	 * Borra registros de una tabla!
	 *
	 * @param string $table
	 * @param string $where_condition
	 */
	public function delete($table, $where_condition){
		if($where_condition){
			return $this->exec("DELETE FROM $table WHERE $where_condition");
		} else {
			return $this->exec("DELETE FROM $table");
		}
	}

}

?>