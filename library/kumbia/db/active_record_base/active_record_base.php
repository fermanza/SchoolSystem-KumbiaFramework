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
 * @category   Kumbia
 * @package    Db
 * @subpackage ActiveRecord
 * @author     Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @copyright  2005-2008 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  2007-2007 Roger Jose Padilla Camacho (rogerjose81 at gmail.com)
 * @copyright  2007-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @copyright  2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 * @version    SVN:$id
 */
/**
 * ActiveRecordException
 */
require_once "library/kumbia/db/active_record_base/exception.php";
/**
 * ActiveRecordBase Clase para el Mapeo Objeto Relacional
 *
 * Active Record es un enfoque al problema de acceder a los datos de una
 * base de datos en forma orientada a objetos. Una fila en la
 * tabla de la base de datos (o vista) se envuelve en una clase,
 * de manera que se asocian filas &uacute;nicas de la base de datos
 * con objetos del lenguaje de programaci&oacute;n usado.
 * Cuando se crea uno de estos objetos, se a&ntilde;de una fila a
 * la tabla de la base de datos. Cuando se modifican los atributos del
 * objeto, se actualiza la fila de la base de datos.
 *
 * Propiedades Soportadas:
 * $db = Conexion al Motor de Base de datos
 * $mode = Modo de acceso a la bd (las conexiones en environment.ini)
 * $source = Tabla que contiene la tabla que esta siendo mapeada
 * $fields = Listado de Campos de la tabla que han sido mapeados
 * $count = Conteo del ultimo Resultado de un Select
 * $primary_key = Listado de columnas que conforman la llave primaria
 * $non_primary = Listado de columnas que no son llave primaria
 * $not_null = Listado de campos que son not_null
 * $attributes_names = nombres de todos los campos que han sido mapeados
 * $debug = Indica si se deben mostrar los SQL enviados al RDBM en pantalla
 * $logger = Si es diferente de false crea un log utilizando la clase Logger
 * en library/kumbia/logger/logger.php, esta crea un archivo .txt en logs/ con todas las
 * operaciones realizadas en ActiveRecord, si $logger = "nombre", crea un
 * archivo con ese nombre
 *
 * Propiedades sin Soportar:
 * $dynamic_update : La idea es que en un futuro ActiveRecord solo
 * actualize los campos que han cambiado.  (En Desarrollo)
 * $dynamic_insert : Indica si los valores del insert son solo aquellos
 * que sean no nulos. (En Desarrollo)
 * $select_before_update: Exige realizar una sentencia SELECT anterior
 * a la actualizacion UPDATE para comprobar que los datos no hayan sido
 * cambiados (En Desarrollo)
 * $subselect : Permitira crear una entidad ActiveRecord de solo lectura que
 * mapearia los resultados de un select directamente a un Objeto (En Desarrollo)
 *
 * @category   Kumbia
 * @package    Db
 * @subpackage ActiveRecord
 * @author     Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @copyright  2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  2007-2007 Roger Jose Padilla Camacho (rogerjose81 at gmail.com)
 * @copyright  2007-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @copyright  2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 *
 */
class ActiveRecordBase extends Object
{
    //Soportados

    /**
     * Resource de conexion a la base de datos
     *
     * @var DbBase
     */
    public $db;
    /**
     * Modo a utilizar para que el modelo acceda a la bd, utiliza
     * los modos que se encuentran en environment.ini, por defecto
     * se considera el definido en config.ini para la aplicacion
     *
     * @var string
     *
     */
    protected $mode;
    /**
     * Schema donde esta la tabla
     *
     * @var string
     */
    protected $schema;
    /**
     * Tabla utilizada para realizar el mapeo
     *
     * @var string
     */
    protected $source;
    /**
     * Numero de resultados generados en la ultima consulta
     *
     * @var integer
     */
    public $count;
    /**
     * Nombres de los atributos de la entidad
     *
     * @var array
     */
    public $fields = array();
    /**
     * LLaves primarias de la entidad
     *
     * @var array
     */
    public $primary_key = array();
    /**
     * Campos que no son llave primaria
     *
     * @var array
     */
    public $non_primary = array();
    /**
     * Campos que no permiten nulos
     *
     * @var array
     */
    public $not_null = array();
    /**
     * Campos que tienen valor por defecto
     *
     * @var array
     */
    protected $_with_default = array();
    /**
     * Nombres de atributos, es lo mismo que fields
     *
     * @var array
     */
    public $attributes_names = array();
    /**
     * Indica si la clase corresponde a un mapeo de una vista
     * en la base de datos
     *
     * @var boolean
     */
    public $is_view = false;
    /**
     * Indica si el modelo esta en modo debug
     *
     * @var boolean
     */
    public $debug = false;
    /**
     * Indica si se logearan los mensajes generados por la clase
     *
     * @var mixed
     */
    public $logger = false;
    /**
     * Indica si los datos del modelo deben ser persistidos
     *
     * @var boolean
     */
    public $persistent = false;
    //:Privados

    /**
     * Campos que les ser� validado el tama�o
     *
     * @var array
     */
    protected $_validates_length = array();
    /**
     * Campos que ser�n validados si son numericos
     *
     * @var array
     */
    protected $_validates_numericality = array();
    /**
     * Campos que seran validados si son email
     *
     * @var array
     */
    protected $_validates_email = array();
    /**
     * Campos que ser�n validados si son Fecha
     *
     * @var array
     */
    protected $_validates_date = array();
    /**
     * Campos que seran validados si son unicos
     *
     * @var array
     */
    protected $_validates_uniqueness = array();
    /**
     * Campos que deberan tener valores dentro de una lista
     * establecida
     *
     * @var array
     */
    protected $_validates_inclusion = array();
    /**
     * Campos que deberan tener valores por fuera de una lista
     * establecida
     *
     * @var array
     */
    protected $_validates_exclusion = array();
    /**
     * Campos que seran validados contra un formato establecido
     *
     * @var array
     */
    protected $_validates_format = array();
    /**
     * Campos que son obligatorios, no pueden ser nulos
     *
     * @var array
     */
    protected $_validates_presence = array();
    /**
     * Campos que terminan en _in
     *
     * @var array
     */
    protected $_in = array();
    /**
     * Campos que terminan en _at
     *
     * @var array
     */
    protected $_at = array();
    /**
     * Variable para crear una condicion basada en los
     * valores del where
     *
     * @var string
     */
    protected $_where_pk;
    /**
     * Indica si ya se han obtenido los metadatos del Modelo
     *
     * @var boolean
     */
    protected $_dumped = false;
    /**
     * Indica si hay bloqueo sobre los warnings cuando una propiedad
     * del modelo no esta definida-
     *
     * @var boolean
     */
    protected $_dump_lock = false;
    /**
     * Tipos de datos de los campos del modelo
     *
     * @var array
     */
    protected $_data_type = array();
    /**
     * Relaciones a las cuales tiene una cardinalidad 1-1
     *
     * @var array
     */
    protected $_has_one = array();
    /**
     * Relaciones a las cuales tiene una cardinalidad 1-n
     *
     * @var array
     */
    protected $_has_many = array();
    /**
     * Relaciones a las cuales tiene una cardinalidad 1-1
     *
     * @var array
     */
    protected $_belongs_to = array();
    /**
     * Relaciones a las cuales tiene una cardinalidad n-n (muchos a muchos) o 1-n inversa
     *
     * @var array
     */
    protected $_has_and_belongs_to_many = array();
    /**
     * Clases de las cuales es padre la clase actual
     *
     * @var array
     */
    public $parent_of = array();
    /**
     * Persistance Models Meta-data
     */
    static public $models;
    /**
     * Constructor del Modelo
     */
    function __construct()
    {
        if (!$this->source) {
            $this->_model_name();
        }
        /**
         * Inicializa el modelo en caso de que exista initialize
         */
        if (method_exists($this, "initialize")) {
            $this->initialize();
        }
        if (func_num_args()) {
            $params = func_get_args();
            if (is_array($params[0])) {
                $params = $params[0];
            } else {
                $params = get_params($params);
            }
            $this->dump_result_self($params);
        }
    }
    /**
     * Obtiene el nombre de la relacion en el RDBM a partir del nombre de la clase
     *
     */
    protected function _model_name()
    {
        if (!$this->source) {
            $this->source = uncamelize(lcfirst(get_class($this)));
        }
    }
    /**
     * Establece publicamente el $source de la tabla
     *
     * @param string $source
     */
    public function set_source($source)
    {
        $this->source = $source;
    }
    /**
     * Devuelve el source actual
     *
     * @return string
     */
    public function get_source()
    {
        return $this->source;
    }
    /**
     * Establece publicamente el $mode del modelo
     *
     * @param string $mode
     */
    public function set_mode($mode)
    {
        $this->mode = $mode;
    }
    /**
     * Devuelve el mode actual
     *
     * @return string
     */
    public function get_mode()
    {
        if ($this->mode) {
            return $this->mode;
        } else {
            return Kumbia::get_active_app_mode();
        }
    }
    /**
     * Pregunta si el ActiveRecord ya ha consultado la informacion de metadatos
     * de la base de datos o del registro persistente
     *
     * @return boolean
     */
    public function is_dumped()
    {
        return $this->_dumped;
    }
    /**
     * Valida que los valores que sean leidos del objeto ActiveRecord esten definidos
     * previamente o sean atributos de la entidad
     *
     * @param string $property
     */
    function __get($property)
    {
        $this->_connect();
        if (!$this->_dump_lock) {
            if (!isset($this->$property)) {
                if (array_key_exists($property, $this->_belongs_to)) {
                    $relation = $this->_belongs_to[$property];
                    if (isset(kumbia::$models[$relation->model])) {
                        $assoc_obj = kumbia::$models[$relation->model]->find_first($this->{$relation->fk});
                        return $assoc_obj;
                    } else {
                        ActiveRecordException::display_warning("Propiedad no definida", "Propiedad indefinida '$property' leida de el modelo '$this->source'", $this->source);
                        return null;
                    }
                } elseif (array_key_exists($property, $this->_has_one)) {
                    $relation = $this->_has_one[$property];
                    if (isset(kumbia::$models[$relation->model])) {
                        if ($this->{$this->primary_key[0]}) {
                            $assoc_obj = kumbia::$models[$relation->model]->find_first("{$relation->fk}={$this->db->add_quotes($this->{$this->primary_key[0]}) }");
                            return $assoc_obj;
                        } else {
                            return null;
                        }
                    } else {
                        ActiveRecordException::display_warning("Propiedad no definida", "Propiedad indefinida '$property' leida de el modelo '$this->source'", $this->source);
                        return null;
                    }
                } elseif (array_key_exists($property, $this->_has_many)) {
                    $relation = $this->_has_many[$property];
                    if (isset(kumbia::$models[$relation->model])) {
                        if ($this->{$this->primary_key[0]}) {
                            $assoc_objs = kumbia::$models[$relation->model]->find("{$relation->fk}={$this->db->add_quotes($this->{$this->primary_key[0]}) }");
                            return $assoc_objs;
                        } else {
                            return array();
                        }
                    } else {
                        ActiveRecordException::display_warning("Propiedad no definida", "Propiedad indefinida '$property' leida de el modelo '$this->source'", $this->source);
                        return null;
                    }
                } elseif (array_key_exists($property, $this->_has_and_belongs_to_many)) {
                    $relation = $this->_has_and_belongs_to_many[$property];
                    if (isset(kumbia::$models[$relation->model])) {
                        $relation_model = kumbia::$models[$relation->model];
                        $relation_model->dump_model();
                        $source = $this->source;
                        $relation_source = $relation_model->source;
                        /**
                         * Cargo atraves de que tabla se efectuara la relacion
                         *
                         */
                        if (!isset($relation->through)) {
                            if ($source > $relation_source) {
                                $relation->through = "{$this->source}_{$relation_source}";
                            } else {
                                $relation->through = "{$relation_source}_{$this->source}";
                            }
                        }
                        if ($this->{$this->primary_key[0]}) {
                            $assoc_objs = kumbia::$models[$relation->model]->find_all_by_sql("SELECT $relation_source.* FROM $relation_source, {$relation->through}, $source
									WHERE {$relation->through}.{$relation->key} = {$this->db->add_quotes($this->{$this->primary_key[0]}) }
									AND {$relation->through}.{$relation->fk} = $relation_source.{$relation_model->primary_key[0]}
									AND {$relation->through}.{$relation->key} = $source.{$this->primary_key[0]}
									ORDER BY $relation_source.{$relation_model->primary_key[0]}");
                            return $assoc_objs;
                        } else {
                            return array();
                        }
                    } else {
                        ActiveRecordException::display_warning("Propiedad no definida", "Propiedad indefinida '$property' leida de el modelo '$this->source'", $this->source);
                        return null;
                    }
                } else {
                    return null;
                }
            }
        }
        return $this->$property;
    }
    /**
     * Valida que los valores que sean asignados al objeto ActiveRecord esten definidos
     * o sean atributos de la entidad
     *
     * @param string $property
     * @param mixed $value
     */
    function __set($property, $value)
    {
        $this->_connect();
        if (!$this->_dump_lock) {
            if (!isset($this->$property) && is_object($value) && is_subclass_of($value, 'ActiveRecordBase')) {
                if (array_key_exists($property, $this->_belongs_to)) {
                    $relation = $this->_belongs_to[$property];
                    $value->dump_model();
                    $this->{$relation->fk} = $value->{$value->primary_key[0]};
                } elseif (array_key_exists($property, $this->_has_one)) {
                    $relation = $this->_has_one[$property];
                    $value->{$relation->fk} = $this->{$this->primary_key[0]};
                }
            } elseif ($property == "source") {
                $value = ActiveRecord::sql_item_sanizite($value);
            }
        }
        $this->$property = $value;
    }
    /**
     * Devuelve un valor o un listado dependiendo del tipo de Relaci&oacute;n
     *
     */
    public function __call($method, $args = array())
    {
        $this->_connect();
        $has_relation = false;
        if (substr($method, 0, 8) == "find_by_") {
            $field = substr($method, 8);
            ActiveRecord::sql_item_sanizite($field);
            if (isset($args[0])) {
                $arg = array("conditions: $field = {$this->db->add_quotes($args[0]) }");
                unset($args[0]);
            } else {
                $arg = array();
            }
            return call_user_func_array(array($this, "find_first"), array_merge($arg, $args));
        }
        if (substr($method, 0, 9) == "count_by_") {
            $field = substr($method, 9);
            ActiveRecord::sql_item_sanizite($field);
            if (isset($args[0])) {
                $arg = array("conditions: $field = {$this->db->add_quotes($args[0]) }");
                unset($args[0]);
            } else {
                $arg = array();
            }
            return call_user_func_array(array($this, "count"), array_merge($arg, $args));
        }
        if (substr($method, 0, 12) == "find_all_by_") {
            $field = substr($method, 12);
            ActiveRecord::sql_item_sanizite($field);
            if (isset($args[0])) {
                $arg = array("conditions: $field = {$this->db->add_quotes($args[0]) }");
                unset($args[0]);
            } else {
                $arg = array();
            }
            return call_user_func_array(array($this, "find"), array_merge($arg, $args));
        }
        $model = ereg_replace("^get", "", $method);
        $mmodel = uncamelize(lcfirst($model));
        if (array_key_exists($mmodel, $this->_belongs_to)) {
            $has_relation = true;
            $relation = $this->_belongs_to[$mmodel];
            if (kumbia::$models[$relation->model]) {
                return kumbia::$models[$relation->model]->find_first($this->{$relation->fk});
            }
        }
        if (array_key_exists($mmodel, $this->_has_many)) {
            $this->_connect();
            $has_relation = true;
            $relation = $this->_has_many[$mmodel];
            if (kumbia::$models[$relation->model]) {
                if ($this->{$this->primary_key[0]}) {
                    return kumbia::$models[$relation->model]->find("{$relation->fk}={$this->db->add_quotes($this->{$this->primary_key[0]}) }");
                } else {
                    return array();
                }
            }
        }
        if (array_key_exists($mmodel, $this->_has_one)) {
            $this->_connect();
            $has_relation = true;
            $relation = $this->_has_one[$mmodel];
            if (kumbia::$models[$relation->model]) {
                if ($this->{$this->primary_key[0]}) {
                    return kumbia::$models[$relation->model]->find_first("{$relation->fk}={$this->db->add_quotes($this->{$this->primary_key[0]}) }");
                } else {
                    return null;
                }
            }
        }
        if (array_key_exists($mmodel, $this->_has_and_belongs_to_many)) {
            $has_relation = true;
            $relation = $this->_has_and_belongs_to_many[$mmodel];
            if (kumbia::$models[$relation->model]) {
                if ($this->{$this->primary_key[0]}) {
                    $source = $this->source;
                    $relation_model = kumbia::$models[$relation->model];
                    $relation_model->dump_model();
                    $relation_source = $relation_model->source;
                    /**
                     * Cargo atraves de que tabla se efectuara la relacion
                     *
                     */
                    if (!isset($relation->through)) {
                        if ($source > $relation_source) {
                            $relation->through = "{$this->source}_{$relation_source}";
                        } else {
                            $relation->through = "{$relation_source}_{$this->source}";
                        }
                    }
                    return kumbia::$models[$relation->model]->find_all_by_sql("SELECT $relation_source.* FROM $relation_source, {$relation->through}, $source
						WHERE {$relation->through}.{$relation->key} = {$this->db->add_quotes($this->{$this->primary_key[0]}) }
						AND {$relation->through}.{$relation->fk} = $relation_source.{$relation_model->primary_key[0]}
						AND {$relation->through}.{$relation->key} = $source.{$this->primary_key[0]}
						ORDER BY $relation_source.{$relation_model->primary_key[0]}");
                } else {
                    return array();
                }
            }
        }
        try {
            if (method_exists($this, $method)) {
                call_user_func_array(array($this, $method), $args);
            } else {
                if ($has_relation) {
                    throw new ActiveRecordException("No existe el modelo '$model' para relacionar con ActiveRecord::{$this->source}");
                } else {
                    throw new ActiveRecordException("No existe el m&eacute;todo '$method' en ActiveRecord::" . get_class($this));
                }
            }
        }
        catch(Exception $e) {
            $this->exceptions($e);
        }
        return $this->$method($args);
    }
    /**
     * Se conecta a la base de datos y descarga los meta-datos si es necesario
     *
     * @param boolean $new_connection
     */
    protected function _connect($new_connection = false)
    {
        if (!is_object($this->db) || $new_connection) {
            if ($this->mode) {
                $this->db = DbBase::raw_connect($new_connection, "mode: {$this->mode}");
            } else {
                $this->db = DbBase::raw_connect($new_connection);
            }
        }
        $this->db->debug = $this->debug;
        $this->db->logger = $this->logger;
        $this->dump();
    }
    /**
     * Cargar los metadatos de la tabla
     *
     */
    public function dump_model()
    {
        $this->_connect();
    }
    /**
     * Verifica si la tabla definida en $this->source existe
     * en la base de datos y la vuelca en dump_info
     *
     * @return boolean
     */
    protected function dump()
    {
        if ($this->_dumped) {
            return false;
        }
        if ($this->source) {
            $this->source = str_replace(";", "", strtolower($this->source));
        } else {
            $this->_model_name();
            if (!$this->source) {
                return false;
            }
        }
        $table = $this->source;
        $schema = $this->schema;
        if (!count(ActiveRecord::get_meta_data($this->source))) {
            $this->_dumped = true;
            if ($this->db->table_exists($table, $schema)) {
                $this->_dump_info($table, $schema);
            } else {
                throw new ActiveRecordException("No existe la tabla '$table' en la base de datos");
                return false;
            }
            if (!count($this->primary_key)) {
                if (!$this->is_view) {
                    throw new ActiveRecordException("No se ha definido una llave primaria para la tabla '$table' esto imposibilita crear el ActiveRecord para esta entidad");
                    return false;
                }
            }
        } else {
            if (!$this->is_dumped()) {
                $this->_dumped = true;
                $this->_dump_info($table, $schema);
            }
        }
        return true;
    }
    /**
     * Vuelca la informaci&oacute;n de la tabla $table en la base de datos
     * para armar los atributos y meta-data del ActiveRecord
     *
     * @param string $table
     * @return boolean
     */
    protected function _dump_info($table, $schema = '')
    {
        $this->_dump_lock = true;
        if (!count(ActiveRecord::get_meta_data($table))) {
            $meta_data = $this->db->describe_table($table, $schema);
            if ($meta_data) {
                ActiveRecord::set_meta_data($table, $meta_data);
            }
        }
        foreach(ActiveRecord::get_meta_data($table) as $field) {
            $this->fields[] = $field['Field'];
            if ($field['Key'] == 'PRI') {
                $this->primary_key[] = $field['Field'];
            } else $this->non_primary[] = $field['Field'];
            /**
             * Si se indica que no puede ser nulo, pero se indica un
             * valor por defecto, entonces no se incluye en la lista, ya que
             * al colocar un valor por defecto, el campo nunca sera nulo
             *
             */
            if ($field['Null'] == 'NO' && !(isset($field['Default']) && $field['Default'])) {
                $this->not_null[] = $field['Field'];
            }
            if (isset($field['Default']) && $field['Default']) {
                $this->_with_default[] = $field['Field'];
            }
            if ($field['Type']) {
                $this->_data_type[$field['Field']] = strtolower($field['Type']);
            }
            if (substr($field['Field'], strlen($field['Field']) - 3, 3) == '_at') {
                $this->_at[] = $field['Field'];
            }
            if (substr($field['Field'], strlen($field['Field']) - 3, 3) == '_in') {
                $this->_in[] = $field['Field'];
            }
        }
        $this->attributes_names = $this->fields;
        $this->_dump_lock = false;
        return true;
    }
    /**
     * Commit a Transaction
     *
     * @return success
     */
    public function commit()
    {
        $this->_connect();
        return $this->db->commit();
    }
    /**
     * Rollback a Transaction
     *
     * @return success
     */
    public function rollback()
    {
        $this->_connect();
        return $this->db->rollback();
    }
    /**
     * Start a transaction in RDBM
     *
     * @return success
     */
    public function begin()
    {
        $this->_connect(true);
        return $this->db->begin();
    }
    /**
     * Find all records in this table using a SQL Statement
     *
     * @param string $sqlQuery
     * @return ActiveRecord Cursor
     */
    public function find_all_by_sql($sqlQuery)
    {
        $this->_connect();
        $results = array();
        foreach($this->db->fetch_all($sqlQuery) as $result) {
            $results[] = $this->dump_result($result);
        }
        return $results;
    }
    /**
     * Find a record in this table using a SQL Statement
     *
     * @param string $sqlQuery
     * @return ActiveRecord Cursor
     */
    public function find_by_sql($sqlQuery)
    {
        $this->_connect();
        $row = $this->db->fetch_one($sqlQuery);
        if ($row !== false) {
            $this->dump_result_self($row);
            return $this->dump_result($row);
        } else {
            return false;
        }
    }
    /**
     * Execute a SQL Statement directly
     *
     * @param string $sqlQuery
     * @return int affected
     */
    public function sql($sqlQuery)
   	{
        $this->_connect();
        return $this->db->query($sqlQuery);
    }
    /**
     * Return Fist Record
     *
     * @param mixed $what
     * @param boolean $debug
     *
     * Recibe los mismos parametros que find
     *
     * @return ActiveRecord Cursor
     */
    public function find_first($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        $select = "SELECT ";
        if (isset($what['columns'])) {
            $select.= ActiveRecord::sql_sanizite($what['columns']);
        } elseif (isset($what['distinct'])) {
            $select.= 'DISTINCT ';
            $select.= $what['distinct'] ? ActiveRecord::sql_sanizite($what['distinct']) : join(",", $this->fields);
        } else {
            $select.= join(",", $this->fields);
        }
        if ($this->schema) {
            $select.= " FROM {$this->schema}.{$this->source}";
        } else {
            $select.= " FROM {$this->source}";
        }
        $what['limit'] = 1;
        $select.= $this->convert_params_to_sql($what);
        $resp = false;
        try {
            $result = $this->db->fetch_one($select);
            if ($result) {
                $this->dump_result_self($result);
                $resp = $this->dump_result($result);
            }
        }
        catch(Exception $e) {
            $this->exceptions($e);
        }
        return $resp;
    }
    /**
     * Find data on Relational Map table
     *
     * @param string $what
     * @return ActiveRecord Cursor
     *
     * columns: columnas a utilizar
     * conditions : condiciones de busqueda en WHERE
     * join: inclusion inner join o outer join
     * group : campo para grupo en GROUP BY
     * having : condicion para el grupo
     * order : campo para criterio de ordenamiento ORDER BY
     * distinct: campos para hacer select distinct
     */
    public function find($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        $select = "SELECT ";
        if (isset($what['columns'])) {
            $select.= $what['columns'] ? ActiveRecord::sql_sanizite($what['columns']) : join(",", $this->fields);
        } elseif (isset($what['distinct'])) {
            $select.= 'DISTINCT ';
            $select.= $what['distinct'] ? ActiveRecord::sql_sanizite($what['distinct']) : join(",", $this->fields);
        } else {
            $select.= join(",", $this->fields);
        }
        if ($this->schema) {
            $select.= " FROM {$this->schema}.{$this->source}";
        } else {
            $select.= " FROM {$this->source}";
        }
        $select.= $this->convert_params_to_sql($what);
        $results = array();
        $all_results = $this->db->in_query($select);
        foreach($all_results AS $result) {
            $results[] = $this->dump_result($result);
        }
        $this->count = count($results);
        if (isset($what[0]) && is_numeric($what[0])) {
            if (!isset($results[0])) {
                $this->count = 0;
                return false;
            } else {
                $this->dump_result_self($all_results[0]);
                $this->count = 1;
                return $results[0];
            }
        } else {
            $this->count = count($results);
            return $results;
        }
    }
    /*
    * Arma una consulta SQL con el parametro $what, as�:
    * 	$what = get_params(func_get_args());
    * 	$select = "SELECT * FROM Clientes";
    *	$select.= $this->convert_params_to_sql($what);
    *
    * @param string $what
    * @return string
    */
    public function convert_params_to_sql($what = '')
    {
        $select = "";
        if (is_array($what)) {
            if (!isset($what['conditions'])) {
                if (!isset($this->primary_key[0]) && (isset($this->id) || $this->is_view)) {
                    $this->primary_key[0] = "id";
                }
                ActiveRecord::sql_item_sanizite($this->primary_key[0]);
                if (isset($what[0])) {
                    if (is_numeric($what[0])) {
                        $what['conditions'] = "{$this->primary_key[0]} = {$this->db->add_quotes($what[0]) }";
                    } else {
                        if ($what[0] == '') {
                            $what['conditions'] = "{$this->primary_key[0]} = ''";
                        } else {
                            $what['conditions'] = $what[0];
                        }
                    }
                }
            }
            if (isset($what['join']) && $what['join']) {
                $select.= " {$what['join']}";
            }
            if (isset($what['conditions']) && $what['conditions']) {
                $select.= " WHERE {$what['conditions']}";
            }
            if (isset($what['group']) && $what['group']) {
                $select.= " GROUP BY {$what['group']}";
            }
            if (isset($what['having']) && $what['having']) {
                $select.= " HAVING {$what['having']}";
            }
            if (isset($what['order']) && $what['order']) {
                ActiveRecord::sql_sanizite($what['order']);
                $select.= " ORDER BY {$what['order']}";
            }
            $limit_args = array($select);
            if (isset($what['limit'])) {
                array_push($limit_args, "limit: $what[limit]");
            }
            if (isset($what['offset'])) {
                array_push($limit_args, "offset: $what[offset]");
            }
            if (count($limit_args) > 1) {
                $select = call_user_func_array(array($this, 'limit'), $limit_args);
            }
        } else {
            if (strlen($what)) {
                if (is_numeric($what)) {
                    $select.= "WHERE {$this->primary_key[0]} = '$what'";
                } else {
                    $select.= "WHERE $what";
                }
            }
        }
        return $select;
    }
    /*
    * Devuelve una clausula LIMIT adecuada al RDBMS empleado
    *
    * limit: maxima cantidad de elementos a mostrar
    * offset: desde que elemento se comienza a mostrar
    *
    * @param string $sql consulta select
    * @return String clausula LIMIT adecuada al RDBMS empleado
    */
    public function limit($sql)
    {
        $args = func_get_args();
        return call_user_func_array(array($this->db, 'limit'), $args);
    }
    /**
     * Ejecuta un SELECT DISTINCT
     * @param string $what
     * @return array
     *
     * Soporta parametros iguales a find
     *
     */
    public function distinct($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if ($this->schema) {
            $table = $this->schema . "." . $this->source;
        } else {
            $table = $this->source;
        }
        if (!isset($what['columns'])) {
            $what['columns'] = $what['0'];
        } else {
            if (!$what['columns']) {
                $what['columns'] = $what['0'];
            }
        }
        $what['columns'] = ActiveRecord::sql_sanizite($what['columns']);
        $select = "SELECT DISTINCT {$what['columns']} FROM $table ";
        /**
         * Se elimina el de indice cero ya que por defecto convert_params_to_sql lo considera como una condicion en WHERE
         */
        unset($what[0]);
        $select.= $this->convert_params_to_sql($what);
        $results = array();
        foreach($this->db->fetch_all($select) as $result) {
            $results[] = $result[0];
        }
        return $results;
    }
    /**
     * Ejecuta una consulta en el RDBM directamente
     *
     * @param string $sql
     * @return resource
     */
    public function select_one($sql)
    {
        $this->_connect();
        if (substr(ltrim($sql), 0, 7) != "SELECT") {
            $sql = "SELECT " . $sql;
        }
        $num = $this->db->fetch_one($sql);
        return $num[0];
    }
    static public function static_select_one($sql)
    {
        $db = db::raw_connect();
        if (substr(ltrim($sql), 0, 7) != "SELECT") {
            $sql = "SELECT " . $sql;
        }
        $num = $db->fetch_one($sql);
        return $num[0];
    }
    /**
     * Realiza un conteo de filas
     *
     * @param string $what
     * @return integer
     */
    public function count($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        if (isset($what['distinct']) && $what['distinct']) {
            if (isset($what['group']) || isset($what['order'])) {
                $select = "SELECT COUNT(*) FROM (SELECT DISTINCT {$what['distinct']} FROM $table ";
                $select.= $this->convert_params_to_sql($what);
                $select.= ') AS t ';
            } else {
                $select = "SELECT COUNT(DISTINCT {$what['distinct']}) FROM $table ";
                $select.= $this->convert_params_to_sql($what);
            }
        } else {
            $select = "SELECT COUNT(*) FROM $table ";
            $select.= $this->convert_params_to_sql($what);
        }
        $num = $this->db->fetch_one($select);
        return $num[0];
    }
    /**
     * Realiza un promedio sobre el campo $what
     *
     * @param string $what
     * @return array
     */
    public function average($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if (isset($what['column'])) {
            if (!$what['column']) {
                $what['column'] = $what[0];
            }
        } else {
            $what['column'] = $what[0];
        }
        unset($what[0]);
        ActiveRecord::sql_item_sanizite($what['column']);
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        $select = "SELECT AVG({$what['column']}) FROM $table ";
        $select.= $this->convert_params_to_sql($what);
        $num = $this->db->fetch_one($select);
        return $num[0];
    }
    public function sum($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if (isset($what['column'])) {
            if (!$what['column']) {
                $what['column'] = $what[0];
            }
        } else {
            $what['column'] = $what[0];
        }
        unset($what[0]);
        ActiveRecord::sql_item_sanizite($what['column']);
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        $select = "SELECT SUM({$what['column']}) FROM $table ";
        $select.= $this->convert_params_to_sql($what);
        $num = $this->db->fetch_one($select);
        return $num[0];
    }
    /**
     * Busca el valor maximo para el campo $what
     *
     * @param string $what
     * @return mixed
     */
    public function maximum($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if (isset($what['column'])) {
            if (!$what['column']) {
                $what['column'] = $what[0];
            }
        } else {
            $what['column'] = $what[0];
        }
        unset($what[0]);
        ActiveRecord::sql_item_sanizite($what['column']);
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        $select = "SELECT MAX({$what['column']}) FROM $table ";
        $select.= $this->convert_params_to_sql($what);
        $num = $this->db->fetch_one($select);
        return $num[0];
    }
    /**
     * Busca el valor minimo para el campo $what
     *
     * @param string $what
     * @return mixed
     */
    public function minimum($what = '')
    {
        $this->_connect();
        $what = get_params(func_get_args());
        if (isset($what['column'])) {
            if (!$what['column']) {
                $what['column'] = $what[0];
            }
        } else {
            $what['column'] = $what[0];
        }
        unset($what[0]);
        ActiveRecord::sql_item_sanizite($what['column']);
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        $select = "SELECT MIN({$what['column']}) FROM $table ";
        $select.= $this->convert_params_to_sql($what);
        $num = $this->db->fetch_one($select);
        return $num[0];
    }
    /**
     * Realiza un conteo directo mediante $sql
     *
     * @param string $sqlQuery
     * @return mixed
     */
    public function count_by_sql($sqlQuery)
    {
        $this->_connect();
        $num = $this->db->fetch_one($sqlQuery);
        return $num[0];
    }
    /**
     * Iguala los valores de un resultado de la base de datos
     * en un nuevo objeto con sus correspondientes
     * atributos de la clase
     *
     * @param array $result
     * @return ActiveRecord
     */
    public function dump_result($result){
        $this->_connect();
        $obj = clone $this;
        /**
         * Consulta si la clase es padre de otra y crea el tipo de dato correcto
         */
        if (isset($result['type'])) {
            if (in_array($result['type'], $this->parent_of)) {
                if (class_exists($result['type'])) {
                    $obj = new $result['type'];
                    unset($result['type']);
                }
            }
        }
        $this->_dump_lock = true;
        if (is_array($result)) {
            foreach($result as $k => $r) {
                if (!is_numeric($k)) {
                    $obj->$k = stripslashes($r);
                }
            }
        }
        $this->_dump_lock = false;
        return $obj;
    }
    /**
     * Iguala los valores de un resultado de la base de datos
     * con sus correspondientes atributos de la clase
     *
     * @param array $result
     * @return ActiveRecord
     */
    public function dump_result_self($result)
    {
        $this->_connect();
        $this->_dump_lock = true;
        if (is_array($result)) {
            foreach($result as $k => $r) {
                if (!is_numeric($k)) {
                    $this->$k = stripslashes($r);
                }
            }
        }
        $this->_dump_lock = false;
    }
    /**
     * Create a new Row using values from $_REQUEST
     *
     * @param string $form form name for request, equivalent to $_REQUEST[$form]
     * @return boolean success
     */
    public function create_from_request($form = '')
    {
        if ($form) {
            return $this->create($_REQUEST[$form]);
        } else {
            return $this->create($_REQUEST);
        }
    }
    /**
     * Saves a new Row using values from $_REQUEST
     *
     * @param string $form form name for request, equivalent to $_REQUEST[$form]
     * @return boolean success
     */
    public function save_from_request($form = '')
    {
        if ($form) {
            return $this->save($_REQUEST[$form]);
        } else {
            return $this->save($_REQUEST);
        }
    }
    /**
     * Updates a Row using values from $_REQUEST
     *
     * @param string $form form name for request, equivalent to $_REQUEST[$form]
     * @return boolean success
     */
    public function update_from_request($form = '')
    {
        if ($form) {
            return $this->update($_REQUEST[$form]);
        } else {
            return $this->update($_REQUEST);
        }
    }
    /**
     * Creates a new Row in map table
     *
     * @param mixed $values
     * @return success boolean
     */
    public function create()
    {
        $this->_connect();
        if (func_num_args() > 0) {
            $params = get_params(func_get_args());
            $values = (isset($params[0]) && is_array($params[0])) ? $params[0] : $params;
            foreach($this->fields as $field) {
                if (isset($values[$field])) {
                    $this->$field = $values[$field];
                }
            }
        }
        if ($this->primary_key[0] == 'id') {
            $this->id = null;
        }
        return $this->save();
    }
    /**
     * Consulta si un determinado registro existe o no
     * en la entidad de la base de datos
     *
     * @return boolean
     */
    function exists($where_pk = '')
    {
        $this->_connect();
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        if (!$where_pk) {
            $where_pk = array();
            foreach($this->primary_key as $key) {
                if ($this->$key) {
                    $where_pk[] = " $key = '{$this->$key}'";
                }
            }
            if (count($where_pk)) {
                $this->_where_pk = join(" AND ", $where_pk);
            } else {
                return 0;
            }
            $query = "SELECT COUNT(*) FROM $table WHERE {$this->_where_pk}";
        } else {
            if (is_numeric($where_pk)) {
                $query = "SELECT(*) FROM $table WHERE id = '$where_pk'";
            } else {
                $query = "SELECT COUNT(*) FROM $table WHERE $where_pk";
            }
        }
        $num = $this->db->fetch_one($query);
        return $num[0];
    }
    /**
     * Saves Information on the ActiveRecord Properties
     * @param array $values array de valores a cargar
     * @return boolean success
     */
    public function save()
    {
        $this->_connect();
        if (func_num_args() > 0) {
            $params = get_params(func_get_args());
            $values = (isset($params[0]) && is_array($params[0])) ? $params[0] : $params;
            foreach($this->fields as $field) {
                if (isset($values[$field])) {
                    $this->$field = $values[$field];
                }
            }
        }
        $ex = $this->exists();
        if ($this->schema) {
            $table = $this->schema . "." . $this->source;
        } else {
            $table = $this->source;
        }
        #Run Validation Callbacks Before
        if (method_exists($this, 'before_validation')) {
            if ($this->before_validation() == 'cancel') {
                return false;
            }
        } else {
            if (isset($this->before_validation)) {
                $method = $this->before_validation;
                if ($this->$method() == 'cancel') {
                    return false;
                }
            }
        }
        if(!$ex){
        	if (method_exists($this, "before_validation_on_create")) {
        		if ($this->before_validation_on_create() == 'cancel') {
        			return false;
        		}
        	} else {
        		if (isset($this->before_validation_on_create)) {
        			$method = $this->before_validation_on_create;
        			if ($this->$method() == 'cancel') {
        				return false;
        			}
        		}
        	}
        }
		if($ex){
			if (method_exists($this, "before_validation_on_update")) {
				if ($this->before_validation_on_update() == 'cancel') {
					return false;
				}
			} else {
				if (isset($this->before_validation_on_update)) {
					$method = $this->before_validation_on_update;
					if($this->$method() == 'cancel') {
						return false;
					}
				}
			}
		}
        /**
         * Recordamos que aqui no aparecen los que tienen valores por defecto,
         * pero sin embargo se debe estar pendiente de validar en las otras verificaciones
         * los campos nulos, ya que en estas si el campo es nulo, realmente se refiere a un campo que
         * debe tomar el valor por defecto
         *
         */
        $e = false;
        for ($i = 0;$i <= count($this->not_null) - 1;$i++) {
            $f = $this->not_null[$i];
            if (isset($this->$f) && (is_null($this->$f) || $this->$f == '')) {
                if (!$ex && $f == 'id') {
                    continue;
                }
                if (!$ex && in_array($f, $this->_at)) {
                    continue;
                }
                if ($ex && in_array($f, $this->_in)) {
                    continue;
                }
                Flash::error("Error: El campo $f no puede ser nulo");
                $e = true;
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_presence
         *
         */
        $e = false;
        foreach($this->_validates_presence as $f => $opt) {
            if (isset($this->$f) && (is_null($this->$f) || $this->$f == '')) {
                if (!$ex && $f == 'id') {
                    continue;
                }
                if (!$ex && in_array($f, $this->_at)) {
                    continue;
                }
                if ($ex && in_array($f, $this->_in)) {
                    continue;
                }
                if (isset($opt['message'])) {
                    Flash::error($opt['message']);
                } else {
                    $field = isset($opt['field']) ? $opt['field'] : $f;
                    Flash::error("Error: El campo $field no puede ser nulo");
                }
                $e = true;
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_length
         *
         */
        $e = false;
        foreach($this->_validates_length as $f => $opt) {
            if (isset($this->$f) && !is_null($this->$f) && $this->$f != '') {
                $field = isset($opt['field']) ? $opt['field'] : $f;
                if (isset($opt['in'])) {
                    $in = explode(":", $opt['in']);
                    if (is_numeric($in[0]) && is_numeric($in[1])) {
                        $opt['minimum'] = $in[0];
                        $opt['maximum'] = $in[1];
                    }
                }
                if (isset($opt['minimum']) && is_numeric($opt['minimum'])) {
                    $n = $opt['minimum'];
                    if (strlen($this->$f) < $n) {
                        if (!isset($opt['too_short'])) {
                            Flash::error("Error: El campo $field debe tener como m&iacute;nimo $n caracteres");
                            $e = true;
                        } else {
                            Flash::error($opt['too_short']);
                            $e = true;
                        }
                    }
                }
                if (isset($opt['maximum']) && is_numeric($opt['maximum'])) {
                    $n = $opt['maximum'];
                    if (strlen($this->$f) > $n) {
                        if (!isset($opt['too_long'])) {
                            Flash::error("Error: El campo $field debe tener como m&aacute;ximo $n caracteres");
                            $e = true;
                        } else {
                            Flash::error($opt['too_long']);
                            $e = true;
                        }
                    }
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_inclusion
         *
         */
        $e = false;
        foreach($this->_validates_inclusion as $finc => $list) {
            if (isset($this->$finc) && !is_null($this->$finc) && $this->$finc != '') {
                if (!is_array($list[1]) && $this->$finc != $list[1]) {
                    if (isset($list['message'])) {
                        Flash::error($list['message']);
                    } else {
                        $field = isset($list['field']) ? $list['field'] : ucwords($finc);
                        Flash::error("$field debe tener un valor entre ({$list[1]})");
                    }
                    $e = true;
                } else {
                    if (!in_array($this->$finc, $list[1])) {
                        if (isset($list['message'])) {
                            Flash::error($list['message']);
                        } else {
                            $field = isset($list['field']) ? $list['field'] : ucwords($finc);
                            Flash::error("$field debe tener un valor entre (" . join(",", $list[1]) . ")");
                        }
                        $e = true;
                    }
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_exclusion
         *
         */
        $e = false;
        foreach($this->_validates_exclusion as $finc => $list) {
            if (isset($this->$finc) && !is_null($this->$finc) && $this->$finc != '') {
                if (!is_array($list[1]) && $this->$finc == $list[1]) {
                    if (isset($list['message'])) {
                        Flash::error($list['message']);
                    } else {
                        $field = isset($list['field']) ? $list['field'] : ucwords($finc);
                        Flash::error("$field no debe tener un valor entre ({$list[1]})");
                    }
                    $e = true;
                } else {
                    if (in_array($this->$finc, $list[1])) {
                        if (isset($list['message'])) {
                            Flash::error($list['message']);
                        } else {
                            $field = isset($list['field']) ? $list['field'] : ucwords($finc);
                            Flash::error("$field no debe tener un valor entre (" . join(",", $list[1]) . ")");
                        }
                        $e = true;
                    }
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_numericality
         *
         */
        $e = false;
        foreach($this->_validates_numericality as $fnum => $opt) {
            if (isset($this->$fnum) && !is_null($this->$fnum) && $this->$fnum != '') {
                if (!is_numeric($this->$fnum)) {
                    if (isset($opt['message'])) {
                        Flash::error($opt['message']);
                    } else {
                        $field = isset($opt['field']) ? $opt['field'] : ucwords($fnum);
                        Flash::error("$field debe tener un valor num&eacute;rico");
                    }
                    $e = true;
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_format
         *
         */
        $e = false;
        foreach($this->_validates_format as $fkey => $opt) {
            if (isset($this->$fkey) && !is_null($this->$fkey) && $this->$fkey != '') {
                if (!ereg($opt[1], $this->$fkey)) {
                    if (isset($opt['message'])) {
                        Flash::error($opt['message']);
                    } else {
                        $field = isset($opt['field']) ? $opt['field'] : $fkey;
                        Flash::error("Formato erroneo para $field");
                    }
                    $e = true;
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_date
         *
         */
        $e = false;
        foreach($this->_validates_date as $fkey => $opt) {
            if (isset($this->$fkey) && !is_null($this->$fkey) && $this->$fkey != '') {
                if (!ereg("^[0-9]{4}[-/](0[1-9]|1[12])[-/](0[1-9]|[12][0-9]|3[01])$", $this->$fkey, $regs)) {
                    if (isset($opt['message'])) {
                        Flash::error($opt['message']);
                    } else {
                        $field = isset($opt['field']) ? $opt['field'] : $fkey;
                        Flash::error("Formato de fecha ({$this->$fkey}) erroneo para $field");
                    }
                    $e = true;
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_email
         *
         */
        $e = false;
        foreach($this->_validates_email as $fkey) {
            if (isset($this->$fkey) && !is_null($this->$fkey) && $this->$fkey != '') {
                if (!ereg("^[a-zA-Z0-9_\.\+]+@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$", $this->$fkey, $regs)) {
                    if (isset($opt['message'])) {
                        Flash::error($opt['message']);
                    } else {
                        $field = isset($opt['field']) ? $opt['field'] : $fkey;
                        Flash::error("Formato de e-mail erroneo en el campo $field");
                    }
                    $e = true;
                }
            }
        }
        if ($e) {
            return false;
        }
        /**
         * Validacion validates_uniqueness
         *
         */
        $e = false;
        foreach($this->_validates_uniqueness as $fkey => $opt) {
            ActiveRecord::sql_item_sanizite($fkey);
            if (isset($this->$fkey) && !is_null($this->$fkey) && $this->$fkey != '') {
                if ($ex) {
                    $copy = clone $this;
                    $copy->find_first("$fkey = {$this->db->add_quotes($this->$fkey) }");
                    $number = $this->db->fetch_one("SELECT COUNT(*) FROM $table WHERE $fkey = {$this->db->add_quotes($this->$fkey) } and $fkey <> {$this->db->add_quotes($copy->$fkey) }");
                } else {
                    $number = $this->db->fetch_one("SELECT COUNT(*) FROM $table WHERE $fkey = {$this->db->add_quotes($this->$fkey) }");
                }
                if ((int)$number[0]) {
                    if (isset($opt['message'])) {
                        Flash::error($opt['message']);
                    } else {
                        $field = isset($opt['field']) ? $opt['field'] : $fkey;
                        Flash::error("El valor '{$this->$fkey}' ya existe para el campo $field");
                    }
                    $e = true;
                }
            }
        }
        if ($e) {
            return false;
        }
        #Run Validation Callbacks After
        if(!$ex){
        	if (method_exists($this, "after_validation_on_create")) {
            	if ($this->after_validation_on_create() == 'cancel') {
                	return false;
            	}
        	} else {
            	if (isset($this->after_validation_on_create)) {
                	$method = $this->after_validation_on_create;
                	if ($this->$method() == 'cancel') {
                    	return false;
                	}
            	}
        	}
        }
        if($ex){
        	if (method_exists($this, "after_validation_on_update")) {
            	if ($this->after_validation_on_update() == 'cancel') {
                	return false;
            	}
        	} else {
        		if (isset($this->after_validation_on_update)) {
        			$method = $this->after_validation_on_update;
        			if ($this->$method() == 'cancel') return false;
        		}
        	}
        }

        if (method_exists($this, 'after_validation')) {
            if ($this->after_validation() == 'cancel') {
                return false;
            }
        } else {
            if (isset($this->after_validation)) {
                $method = $this->after_validation;
                if ($this->$method() == 'cancel') {
                    return false;
                }
            }
        }
        # Run Before Callbacks
        if (method_exists($this, "before_save")) {
            if ($this->before_save() == 'cancel') {
                return false;
            }
        } else {
            if (isset($this->before_save)) {
                $method = $this->before_save;
                if ($this->$method() == 'cancel') {
                    return false;
                }
            }
        }
        if($ex){
        	if (method_exists($this, "before_update")) {
        		if ($this->before_update() == 'cancel') {
        			return false;
        		}
        	} else {
        		if (isset($this->before_update)) {
        			$method = $this->before_update;
        			if ($this->$method() == 'cancel') {
        				return false;
        			}
        		}
        	}
        }
        if(!$ex){
        	if (method_exists($this, "before_create")) {
        		if ($this->before_create() == 'cancel') {
        			return false;
        		}
        	} else {
        		if (isset($this->before_create)) {
        			$method = $this->before_create;
        			if ($this->$method() == 'cancel') {
        				return false;
        			}
        		}
        	}
        }
        $environment = Config::read("environment.ini");
        $config = $environment->{$this->get_mode() };
        if ($ex) {
            $fields = array();
            $values = array();
            foreach($this->non_primary as $np) {
                $np = ActiveRecord::sql_item_sanizite($np);
                if (in_array($np, $this->_in)) {
                    if ($config->database->type == 'oracle') {
                        $this->$np = date("Y-m-d");
                    } else {
                        $this->$np = date("Y-m-d G:i:s");
                    }
                }
                if (isset($this->$np)) {
                    $fields[] = $np;
                    if (is_null($this->$np) || $this->$np == '') {
                        $values[] = "NULL";
                    } elseif (substr($this->$np, 0, 1) == "%") {
                        $values[] = str_replace("%", "", $this->$np);
                    } else {
                        /**
                         * Se debe especificar el formato de fecha en Oracle
                         */
                        if ($this->_data_type[$np] == 'date' && $config->database->type == 'oracle') {
                            $values[] = "TO_DATE(" . $this->db->add_quotes($this->$np) . ", 'YYYY-MM-DD')";
                        } else {
                            $values[] = $this->db->add_quotes($this->$np);
                        }
                    }
                }
            }
            $val = $this->db->update($table, $fields, $values, $this->_where_pk);
        } else {
            $fields = array();
            $values = array();
            foreach($this->fields as $field) {
                if ($field != 'id' && !$this->id) {
                    if (in_array($field, $this->_at)) {
                        if ($config->database->type == 'oracle') {
                            $this->$field = date("Y-m-d");
                        } else {
                            $this->$field = date("Y-m-d G:i:s");
                        }
                    }
                    if (in_array($field, $this->_in)) {
                        unset($this->$field);
                    }
                    $use_default = in_array($field, $this->_with_default) && isset($this->$field) && (is_null($this->$field) || $this->$field == '');
                    if($this->_data_type[$field] == 'datetime' && $config->database->type == 'mysql'){
                    	$this->$field = date("Y-m-d G:i:s",strtotime($this->$field));
						}
					if (isset($this->$field) && !$use_default) {
                        $fields[] = ActiveRecord::sql_sanizite($field);
                        if (substr($this->$field, 0, 1) == "%") {
                            $values[] = str_replace("%", "", $this->$field);
                        } else {
                            if ($this->is_a_numeric_type($field) || $this->$field == null) {
                                $values[] = addslashes($this->$field !== '' && $this->$field !== null ? $this->$field : "NULL");
                            } else {
                                if ($this->_data_type[$field] == 'date' && $config->database->type == 'oracle') {
                                    /**
                                     * Se debe especificar el formato de fecha en Oracle
                                     */
                                    $values[] = "TO_DATE(" . $this->db->add_quotes($this->$field) . ", 'YYYY-MM-DD')";
                                } else {
                                    if (!is_null($this->$field) && $this->$field != '') {
                                        $values[] = $this->db->add_quotes($this->$field);
                                    } else {
                                        $values[] = "NULL";
                                    }
                                }
                            }
                        }
                    }
                } else {
                    /**
                     * Campos autonumericos en Oracle deben utilizar una sequencia auxiliar
                     */
                    if ($config->database->type == 'oracle') {
                        if (!$this->id) {
                            $fields[] = "id";
                            $values[] = $this->source . "_id_seq.NEXTVAL";
                        }
                    }
                    if ($config->database->type == 'informix') {
                        if (!$this->id) {
                            $fields[] = "id";
                            $values[] = 0;
                        }
                    }
                }
            }
            $val = $this->db->insert($table, $values, $fields);
        }
        if (!isset($config->database->pdo) && $config->database->type == 'oracle') {
            $this->commit();
        }
        if (!$ex) {
            $this->db->logger = true;
            $m = $this->db->last_insert_id($table, $this->primary_key[0]);
            $this->find_first($m);
        }
        if ($val) {
        	if($ex){
        		if (method_exists($this, "after_update")) {
        			if ($this->after_update() == 'cancel') {
        				return false;
        			}
        		} else {
        			if (isset($this->after_update)) {
        				$method = $this->after_update;
        				if ($this->$method() == 'cancel') {
        					return false;
        				}
        			}
        		}
        	}
        	if(!$ex){
        		if (method_exists($this, "after_create")) {
        			if ($this->after_create() == 'cancel') {
        				return false;
        			}
        		} else {
        			if (isset($this->after_create)) {
        				$method = $this->after_create;
        				if ($this->$method() == 'cancel') {
        					return false;
        				}
        			}
        		}
        	}
            if (method_exists($this, "after_save")) {
                if ($this->after_save() == 'cancel') {
                    return false;
                }
            } else {
                if (isset($this->after_save)) {
                    $method = $this->after_save;
                    if ($this->$method() == 'cancel') {
                        return false;
                    }
                }
            }
            return $val;
        } else {
            return false;
        }
    }
    /**
     * Find All data in the Relational Table
     *
     * @param string $field
     * @param string $value
     * @return ActiveRecod Cursor
     */
    function find_all_by($field, $value)
    {
        ActiveRecord::sql_item_sanizite($field);
        return $this->find("conditions: $field = {$this->db->add_quotes($value) }");
    }
    /**
     * Updates Data in the Relational Table
     *
     * @param mixed $values
     * @return boolean sucess
     */
    function update()
    {
        $this->_connect();
        if (func_num_args() > 0) {
            $params = get_params(func_get_args());
            $values = (isset($params[0]) && is_array($params[0])) ? $params[0] : $params;
            foreach($this->fields as $field) {
                if (isset($values[$field])) {
                    $this->$field = $values[$field];
                }
            }
        }
        if ($this->exists()) {
            return $this->save();
        } else {
            Flash::error('No se puede actualizar porque el registro no existe');
            return false;
        }
    }
    /**
     * Deletes data from Relational Map Table
     *
     * @param mixed $what
     */
    public function delete($what = '')
    {
        $this->_connect();
        if (func_num_args() > 1) {
            $what = get_params(func_get_args());
        }
        if ($this->schema) {
            $table = $this->schema . "." . $this->source;
        } else {
            $table = $this->source;
        }
        $conditions = "";
        if (is_array($what)) {
            if ($what["conditions"]) {
                $conditions = $what["conditions"];
            }
        } else {
            if (is_numeric($what)) {
                ActiveRecord::sql_sanizite($this->primary_key[0]);
                $conditions = "{$this->primary_key[0]} = '$what'";
            } else {
                if ($what) {
                    $conditions = $what;
                } else {
                    ActiveRecord::sql_sanizite($this->primary_key[0]);
                    $conditions = "{$this->primary_key[0]} = '{$this->{$this->primary_key[0]}}'";
                }
            }
        }
        if (method_exists($this, "before_delete")) {
            if ($this->id) {
                $this->find($this->id);
            }
            if ($this->before_delete() == 'cancel') {
                return false;
            }
        } else {
            if (isset($this->before_delete)) {
                if ($this->id) {
                    $this->find($this->id);
                }
                $method = $this->before_delete;
                if ($this->$method() == 'cancel') {
                    return false;
                }
            }
        }
        $val = $this->db->delete($table, $conditions);
        if ($val) {
            if (method_exists($this, "after_delete")) {
                if ($this->after_delete() == 'cancel') {
                    return false;
                }
            } else {
                if (isset($this->after_delete)) {
                    $method = $this->after_delete;
                    if ($this->$method() == 'cancel') {
                        return false;
                    }
                }
            }
        }
        return $val;
    }
    /**
     * Actualiza todos los atributos de la entidad
     * $Clientes->update_all("estado='A', fecha='2005-02-02'", "id>100");
     * $Clientes->update_all("estado='A', fecha='2005-02-02'", "id>100", "limit: 10");
     *
     * @param string $values
     */
    public function update_all($values) {
        $this->_connect();
        $params = array();
        if ($this->schema) {
            $table = $this->schema . "." . $this->source;
        } else {
            $table = $this->source;
        }
        if (func_num_args() > 1) {
            $params = get_params(func_get_args());
        }
        if (!isset($params['conditions']) || !$params['conditions']) {
            if (isset($params[1])) {
                $params['conditions'] = $params[1];
            } else {
                $params['conditions'] = "";
            }
        }
        if ($params['conditions']) {
            $params['conditions'] = " WHERE " . $params['conditions'];
        }
        $sql = "UPDATE $table SET $values {$params['conditions']}";
        $limit_args = array($sql);
        if (isset($params['limit'])) {
            array_push($limit_args, "limit: $params[limit]");
        }
        if (isset($params['offset'])) {
            array_push($limit_args, "offset: $params[offset]");
        }
        if (count($limit_args) > 1) {
            $sql = call_user_func_array(array($this, 'limit'), $limit_args);
        }
        $environment = Config::read("environment.ini");
        $config = $environment->{$this->get_mode() };
        if (!isset($config->database->pdo) || !$config->database->pdo) {
            if ($config->database->type == "informix") {
                $this->db->set_return_rows(false);
            }
        }
        return $this->db->query($sql);
    }
    /**
     * Delete All data from Relational Map Table
     *
     * @param string $conditions
     * @return boolean
     */
    public function delete_all($conditions = '') {
        $this->_connect();
        $limit = "";
        if ($this->schema) {
            $table = $this->schema . "." . $this->source;
        } else {
            $table = $this->source;
        }
        if (func_num_args() > 1) {
            $params = get_params(func_get_args());
            $limit_args = array($select);
            if (isset($params['limit'])) {
                array_push($limit_args, "limit: $params[limit]");
            }
            if (isset($params['offset'])) {
                array_push($limit_args, "offset: $params[offset]");
            }
            if (count($limit_args) > 1) {
                $select = call_user_func_array(array($this, 'limit'), $limit_args);
            }
        }
        return $this->db->delete($table, $conditions);
    }
    /**
     * *********************************************************************************
     * Metodos de Debug
     * *********************************************************************************
     */
    /**
     * Imprime una version humana de los valores de los campos
     * del modelo en una sola linea
     *
     */
    public function inspect() {
        $this->_connect();
        $inspect = array();
        foreach($this->fields as $field) {
            if (!is_array($field)) {
                $inspect[] = "$field: {$this->$field}";
            }
        }
        return join(", ", $inspect);
    }
    /**
     * *********************************************************************************
     * Metodos de Validacion
     * *********************************************************************************
     */
    /**
     * Validate that Attributes cannot have a NULL value
     */
    protected function validates_presence_of()
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $fields = array();
        for ($i = 0;isset($params[$i]);$i++) {
            $fields[] = $params[$i];
            unset($params[$i]);
            $i++;
        }
        foreach($fields as $p) {
            if (!in_array($p, $this->fields) && !isset($this->$p)) {
                throw new ActiveRecordException('No se puede validar presencia de "' . $p . '"
					en el modelo ' . $this->source . ' porque no existe el atributo</u><br>
					Verifique que el atributo este bien escrito y/o exista en la relaci&oacute;n ');
                return false;
            }
            if (!isset($this->_validates_presence[$p])) {
                $this->_validates_presence[$p] = $params;
            }
        }
        return true;
    }
    /**
     * Valida el tama&ntilde;o de ciertos campos antes de insertar
     * o actualizar
     *
     * @params string $field campo a validar
     *
     * $this->validates_length_of("nombre", "minumum: 15")
     * $this->validates_length_of("nombre", "minumum: 15", "too_short: El Nombre es muy corto")
     * $this->validates_length_of("nombre", "maximum: 40", "too_long: El nombre es muy largo")
     * $this->validates_length_of("nombre", "in: 15:40",
     *      "too_short: El Nombre es muy corto",
     *      "too_long: El nombre es muy largo (40 min)"
     * )
     *
     * @return boolean
     */
    protected function validates_length_of($field)
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (!isset($this->_validates_length[$field])) {
            $this->_validates_length[$field] = $params;
        }
        return true;
    }
    /**
     * Valida que el campo se encuentre entre los valores de una lista
     * antes de insertar o actualizar
     *
     * $this->_validates_inclusion_in("estado", array("A", "I"))
     *
     * @param string $campo
     * @param array $list
     * @return boolean
     */
    protected function validates_inclusion_in($campo, $list)
    {
        $this->_connect();
        if (!isset($this->_validates_inclusion[$campo])) {
            $this->_validates_inclusion[$campo] = get_params(func_get_args());
        }
        return true;
    }
    /**
     * Valida que el campo no se encuentre entre los valores de una lista
     * antes de insertar o actualizar
     *
     * <code>
     * $this->_validates_exclusion_of("edad", range(1, 13))
     * </code>
     *
     * @param string $campo
     * @param array $list
     * @return boolean
     */
    protected function validates_exclusion_of($campo, $list)
    {
        $this->_connect();
        if (!isset($this->_validates_exclusion[$campo])) {
            $this->_validates_exclusion[$campo] = get_params(func_get_args());
        }
        return true;
    }
    /**
     * Valida que el campo tenga determinado formato segun una expresion regular
     * antes de insertar o actualizar
     *
     * $this->_validates_format_of("email", "^(+)@((?:[?a?z0?9]+\.)+[a?z]{2,})$")
     *
     * @param string
     * @param array $list
     * @return boolean
     */
    protected function validates_format_of($campo, $pattern)
    {
        $this->_connect();
        if (!isset($this->_validates_format[$campo])) {
            $this->_validates_format[$campo] = get_params(func_get_args());
        }
        return true;
    }
    /**
     * Valida que ciertos atributos tengan un valor numerico
     * antes de insertar o actualizar
     *
     * $this->validates_numericality_of("precio")
     */
    protected function validates_numericality_of()
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $fields = array();
        for ($i = 0;isset($params[$i]);$i++) {
            $fields[] = $params[$i];
            unset($params[$i]);
            $i++;
        }
        foreach($fields as $p) {
            if (!in_array($p, $this->fields) && !isset($this->$p)) {
                throw new ActiveRecordException('No se puede validar presencia de "' . $p . '"
					en el modelo ' . $this->source . ' porque no existe el atributo</u><br>
					Verifique que el atributo este bien escrito y/o exista en la relaci&oacute;n ');
                return false;
            }
            if (!isset($this->_validates_numericality[$p])) {
                $this->_validates_numericality[$p] = $params;
            }
        }
        return true;
    }
    /**
     * Valida que ciertos atributos tengan un formato de e-mail correcto
     * antes de insertar o actualizar
     *
     * $this->_validates_email_in("correo")
     */
    protected function validates_email_in()
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $fields = array();
        for ($i = 0;isset($params[$i]);$i++) {
            $fields[] = $params[$i];
            unset($params[$i]);
            $i++;
        }
        foreach($fields as $p) {
            if (!in_array($p, $this->fields) && !isset($this->$p)) {
                throw new ActiveRecordException('No se puede validar presencia de "' . $p . '"
					en el modelo ' . $this->source . ' porque no existe el atributo</u><br>
					Verifique que el atributo este bien escrito y/o exista en la relaci&oacute;n ');
                return false;
            }
            if (!isset($this->_validates_email[$p])) {
                $this->_validates_email[$p] = $params;
            }
        }
        return true;
    }
    /**
     * Valida que ciertos atributos tengan un valor unico antes
     * de insertar o actualizar
     *
     * message: mensaje a mostrar
     * field: nombre de campo que mostrara
     *
     * $this->_validates_uniqueness_of("cedula")
     */
    protected function validates_uniqueness_of()
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $fields = array();
        for ($i = 0;isset($params[$i]);$i++) {
            $fields[] = $params[$i];
            unset($params[$i]);
            $i++;
        }
        foreach($fields as $p) {
            if (!in_array($p, $this->fields) && !isset($this->$p)) {
                throw new ActiveRecordException('No se puede validar presencia de "' . $p . '"
					en el modelo ' . $this->source . ' porque no existe el atributo</u><br>
					Verifique que el atributo este bien escrito y/o exista en la relaci&oacute;n ');
                return false;
            }
            if (!isset($this->_validates_uniqueness[$p])) {
                $this->_validates_uniqueness[$p] = $params;
            }
        }
        return true;
    }
    /**
     * Valida que ciertos atributos tengan un formato de fecha acorde al indicado en
     * config/config.ini antes de insertar o actualizar
     *
     * $this->_validates_date_in("fecha_registro")
     */
    protected function validates_date_in()
    {
        $this->_connect();
        $params = get_params(func_get_args());
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $fields = array();
        for ($i = 0;isset($params[$i]);$i++) {
            $fields[] = $params[$i];
            unset($params[$i]);
            $i++;
        }
        foreach($fields as $p) {
            if (!in_array($p, $this->fields) && !isset($this->$p)) {
                throw new ActiveRecordException('No se puede validar presencia de "' . $p . '"
					en el modelo ' . $this->source . ' porque no existe el atributo</u><br>
					Verifique que el atributo este bien escrito y/o exista en la relaci&oacute;n ');
                return false;
            }
            if (!isset($this->_validates_date[$p])) {
                $this->_validates_date[$p] = $params;
            }
        }
        return true;
    }
    /**
     * Verifica si un campo es de tipo de dato numerico o no
     *
     * @param string $field
     * @return boolean
     */
    public function is_a_numeric_type($field)
    {
        if (strpos(" " . $this->_data_type[$field], "int") || strpos(" " . $this->_data_type[$field], "decimal") || strpos(" " . $this->_data_type[$field], "number")) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Obtiene los datos de los metadatos generados por Primera vez en la Sesi&oacute;n
     *
     * @param string $table
     * @return array
     */
    static function get_meta_data($table)
    {
        if (isset(self::$models[$table])) {
            return self::$models[$table];
        } else {
            $active_app = Router::get_active_app();
            if (isset($_SESSION['KUMBIA_META_DATA'][$_SESSION['KUMBIA_PATH']][$active_app][$table])) {
                self::set_meta_data($table, $_SESSION['KUMBIA_META_DATA'][$_SESSION['KUMBIA_PATH']][$active_app][$table]);
                return self::$models[$table];
            }
            return array();
        }
    }
    /**
     * Crea un registro de meta datos para la tabla especificada
     *
     * @param string $table
     * @param array $meta_data
     */
    static function set_meta_data($table, $meta_data)
    {
        $active_app = Router::get_active_app();
        if (!isset($_SESSION['KUMBIA_META_DATA'][$_SESSION['KUMBIA_PATH']][$active_app][$table])) {
            $_SESSION['KUMBIA_META_DATA'][$_SESSION['KUMBIA_PATH']][$active_app][$table] = $meta_data;
        }
        self::$models[$table] = $meta_data;
        return true;
    }
    /**
     * Elimina la informaci&oacute;n de cache del objeto y hace que sea cargada en la proxima operaci&oacute;n
     *
     */
    public function reset_cache_information()
    {
        $active_app = Router::get_active_app();
        unset($_SESSION['KUMBIA_META_DATA'][$_SESSION['KUMBIA_PATH']][$active_app][$this->source]);
        $this->_dumped = false;
        if (!$this->is_dumped()) {
            $this->dump();
        }
    }
    /*******************************************************************************************
    * Metodos para generacion de relaciones
    *******************************************************************************************/
    /**
     * Crea una relacion 1-1 entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foranea)
     */
    protected function has_one($relation)
    {
        $params = get_params(func_get_args());
        for ($i = 0;isset($params[$i]);$i++) {
            $relation = uncamelize(lcfirst($params[$i]));
            if (!array_key_exists($relation, $this->_has_one)) {
                $this->_has_one[$relation] = new stdClass();
                $this->_has_one[$relation]->model = ucfirst(camelize(isset($params['model']) ? $params['model'] : $relation));
                $this->_has_one[$relation]->fk = isset($params['fk']) ? $params['fk'] : uncamelize(lcfirst(get_class($this))) . '_id';
            }
        }
    }
    /**
     * Crea una relacion 1-1 inversa entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foranea)
     */
    protected function belongs_to($relation)
    {
        $params = get_params(func_get_args());
        for ($i = 0;isset($params[$i]);$i++) {
            $relation = uncamelize(lcfirst($params[$i]));
            if (!array_key_exists($relation, $this->_belongs_to)) {
                $this->_belongs_to[$relation] = new stdClass();
                $this->_belongs_to[$relation]->model = ucfirst(camelize(isset($params['model']) ? $params['model'] : $relation));
                $this->_belongs_to[$relation]->fk = isset($params['fk']) ? $params['fk'] : "{$relation}_id";
            }
        }
    }
    /**
     * Crea una relacion 1-n entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foranea)
     */
    protected function has_many($relation)
   	{
        $params = get_params(func_get_args());
        for ($i = 0;isset($params[$i]);$i++) {
            $relation = uncamelize(lcfirst($params[$i]));
            if (!array_key_exists($relation, $this->_has_many)) {
                $this->_has_many[$relation] = new stdClass();
                $this->_has_many[$relation]->model = ucfirst(camelize(isset($params['model']) ? $params['model'] : $relation));
                $this->_has_many[$relation]->fk = isset($params['fk']) ? $params['fk'] : uncamelize(lcfirst(get_class($this))) . '_id';
            }
        }
    }
    /**
     * Crea una relacion n-n o 1-n inversa entre dos modelos
     *
     * @param string $relation
     *
     * model : nombre del modelo al que se refiere
     * fk : campo por el cual se relaciona (llave foranea)
     * key: campo llave que identifica al propio modelo
     * through : atrav�s de que tabla
     */
    protected function has_and_belongs_to_many($relation)
    {
        $params = get_params(func_get_args());
        for ($i = 0;isset($params[$i]);$i++) {
            $relation = uncamelize(lcfirst($params[$i]));
            if (!array_key_exists($relation, $this->_has_and_belongs_to_many)) {
                $this->_has_and_belongs_to_many[$relation] = new stdClass();
                $this->_has_and_belongs_to_many[$relation]->model = ucfirst(camelize(isset($params['model']) ? $params['model'] : $relation));
                $this->_has_and_belongs_to_many[$relation]->fk = isset($params['fk']) ? $params['fk'] : "{$relation}_id";
                $this->_has_and_belongs_to_many[$relation]->key = isset($params['key']) ? $params['key'] : uncamelize(lcfirst(get_class($this))) . '_id';
                if (isset($params['through'])) {
                    $this->_has_and_belongs_to_many[$relation]->through = $params['through'];
                }
            }
        }
    }
    /**
     * Herencia Simple
     */
    /**
     * Especifica que la clase es padre de otra
     *
     * @param string $parent
     */
    public function parent_of($parent)
    {
        $parents = func_get_args();
        foreach($parents as $parent) {
            if (!in_array($parent, $this->parent_of)) {
                $this->parent_of[] = $parent;
            }
        }
    }
    /**
     * Elimina caracteres que podrian ayudar a ejecutar
     * un ataque de Inyeccion SQL
     *
     * @param string $sql_item
     */
    public static function sql_item_sanizite($sql_item)
    {
        $sql_item = trim($sql_item);
        if ($sql_item !== '' && $sql_item !== null) {
            $sql_item = ereg_replace("[ ]+", "", $sql_item);
            if (!ereg("^[a-zA-Z0-9_\.]+$", $sql_item)) {
                throw new ActiveRecordException("Se esta tratando de ejecutar una operacion maliciosa!");
            }
        }
        return $sql_item;
    }
    /**
     * Elimina caracteres que podrian ayudar a ejecutar
     * un ataque de Inyeccion SQL
     *
     * @param string $sql_item
     */
    public static function sql_sanizite($sql_item)
   	{
        $sql_item = trim($sql_item);
        if ($sql_item !== '' && $sql_item !== null) {
            $sql_item = ereg_replace("[ ]+", "", $sql_item);
            if (!ereg("^[a-zA-Z_0-9\,\(\)\.\*]+$", $sql_item)) {
                throw new ActiveRecordException("Se esta tratando de ejecutar una operacion maliciosa!");
            }
        }
        return $sql_item;
    }
    /**
     * Al sobreescribir este metodo se puede controlar las excepciones de un modelo
     *
     * @param unknown_type $e
     */
    protected function exceptions($e)
    {
        throw $e;
    }
    /**
     * Implementacion de __toString Standard
     *
     */
    public function __toString()
    {
        return "<" . get_class() . " Object>";
    }
    /**
     * Paginador para el modelo
     *
     * conditions: condiciones para paginacion
     * page: numero de pagina a mostrar (por defecto la pagina 1)
     * per_page: cantidad de elementos por pagina (por defecto 10 items por pagina)
     *
     * @return un objeto Page identico al que se regresa con el util paginate
     *
     */
    public function paginate()
    {
        $args = func_get_args();
        array_unshift($args, $this);
        return call_user_func_array('paginate', $args);
    }
    /**
     * Paginador para el modelo atraves de consulta sql
     *
     * @param string $sql consulta sql
     *
     * page: numero de pagina a mostrar (por defecto la pagina 1)
     * per_page: cantidad de elementos por pagina (por defecto 10 items por pagina)
     *
     * @return un objeto Page identico al que se regresa con el util paginate_by_sql
     *
     */
    public function paginate_by_sql($sql)
    {
        $args = func_get_args();
        array_unshift($args, $this);
        return call_user_func_array('paginate_by_sql', $args);
    }
    /**
     * Operaciones al serializar
     *
     **/
    public function __sleep()
    {
        /**
         * Anulando conexion a bd en el modelo
         **/
        $this->db = null;
        
        return array_keys(get_object_vars($this));
    }
    /**
     * Operaciones al deserializar
     *
     **/
    public function __wakeup()
    {
        /**
         * Restableciendo conexion a la bd
         **/
        $this->_connect();
    }
}
?>
