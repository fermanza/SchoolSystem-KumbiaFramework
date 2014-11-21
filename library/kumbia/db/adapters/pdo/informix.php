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
 * @subpackage PDOAdapters
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
 * @subpackage PDOAdapters
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @link http://www.php.net/manual/es/ref.ifx.php
 * @access Public
 *
 */
class DbPdoInformix extends DbPDO  {

	/**
	 * Nombre de RBDM
	 */
	protected $db_rbdm = "informix";

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
	 * Ejecuta acciones de incializacion del driver
	 *
	 */
	public function initialize(){

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
				'YES' AS NULL, c.collength as Length
				 FROM systables t, syscolumns c WHERE
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
				$field['type'] = "integer";
			}
			if($field['type']==13){
				$field['type'] = "varchar(".$field['length'].")";
			}
			if($field['type']==2){
				$field['type'] = "int(".$field['length'].")";
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

}

?>