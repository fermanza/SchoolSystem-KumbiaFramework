<?php
/**
 * Kumbia PHP Framework
 * PHP version 5
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbiaphp.com/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category  Kumbia
 * @package   Kumbia
 *
 * @author    Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @copyright 2008-2008 Emilio Rafael Silveira Tovar <emilio.rst at gmail.com>
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito <deivinsontejeda at gmail.com>
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU/GPL
 * @version   SVN:$id
 * @see       Object
 */
require "library/kumbia/object.php";
/**
 * Esta es la clase principal del framework, contiene metodos importantes
 * para cargar los controladores y ejecutar las acciones en estos ademas
 * de otras funciones importantes
 *
 * @category  Kumbia
 * @package   Kumbia
 * @author    Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @copyright 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @copyright 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU/GPL
 */
abstract class Kumbia extends Object
{

    /**
     * Almacena la configuracion global del framework
     *
     * @var Config
     */
    public $config;
    /**
     * Almacena la version actual del Framework
     * 
     */
    const KUMBIA_VERSION = '0.5 Stable';
    /**
     * Almacena datos compartidos en la aplicacion
     *
     * @var array
     */
    public $data = array();
    /**
     * Almacena los modelos que son persistentes
     *
     * @var array
     */
    static private $_persistence = array();
    /**
     * Cachea la salida al navegador
     *
     * @var string
     */
    static public $content = "";
    /**
     * Almacena el objeto Smarty
     *
     * @var string
     */
    private static $_smarty_object = "";
    /**
     * Lista de todos los modelos disponibles en la aplicacion
     *
     * @var array
     */
    static public $models = array();
    /**
     * Nombre del controlador actual
     *
     * @var string
     */
    static public $controller;
    /**
     * Nombre de la aplicacion activa
     *
     * @var string
     */
    static public $active_app;
    /**
     * Directorio de controladores activo
     *
     * @var string
     */
    public static $active_controllers_dir;
    /**
     * Directorio de modelos activo
     *
     * @var string
     */
    public static $active_models_dir;
    /**
     * Directorio de vistas activo
     *
     * @var string
     */
    public static $active_views_dir;
    /**
     * Directorio de plugins activo
     *
     * @var string
     */
    public static $active_plugins_dir;
    /**
     * Directorio de helpers activo
     *
     * @var string
     */
    public static $active_helpers_dir;
    /**
     * Enlace a la base de datos actual
     *
     * @var DbBase
     */
    static public $db;

    static public function init_application() {
        /**
         * Inicializar el ExceptionHandler
         */
        set_exception_handler(array("Kumbia", "manage_exceptions"));
        /**
         * Crear el KUMBIA_PATH
         */
        $delete_session_cache = false;
        $path = join(array_slice(explode("/", dirname($_SERVER['PHP_SELF'])), 1, -1), "/");
        if (isset($_SESSION['KUMBIA_PATH'])) {
            if ($path != $_SESSION['KUMBIA_PATH']) {
                $delete_session_cache = true;
            }
        }
        $_SESSION['KUMBIA_PATH'] = $path;
        if ($_SESSION['KUMBIA_PATH']) {
            define('KUMBIA_PATH', "/" . $_SESSION['KUMBIA_PATH'] . "/");
        } else {
            define('KUMBIA_PATH', "/");
        }
        /**
         * Aplicacion Activa
         */
        $active_app = Router::get_application();
        /**
         * La lista de modulos en boot.ini son cacheados en la variable de sesion
         * $_SESSION['KUMBIA_MODULES'] para no leer este archivo muchas veces
         *
         * La variable extensiones en el apartado modules en config/boot.ini
         * tiene valores estilo kumbia.helpers,... esto hace que Kumbia cargue
         * automaticamente en el directorio library/kumbia/helpers el archivo helpers.php.
         *
         * Esta variable tambien puede ser utilizada para cargar modulos de
         * usuario y clases personalizadas
         *
         * Chequee la funci�n import() en este mismo archivo para encontrar una forma
         * alternativa para cargar modulos y clases de usuario en Kumbia
         *
         */
        if (!isset($_SESSION['KUMBIA_MODULES'])) {
            $_SESSION['KUMBIA_MODULES'] = array();
        }
        if (!isset($_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']])) {
            $_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']] = array();
        }
        if (!isset($_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app])) {
            $_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app] = array();
            if ($kumbia_config = Config::read('boot.ini')) {
                $kumbia_config->modules->extensions = str_replace(" ", "", $kumbia_config->modules->extensions);
                $extensions = explode(",", $kumbia_config->modules->extensions);
                foreach($extensions as $extension) {
                    $ex = explode(".", $extension);
                    if ($ex[0] == "kumbia") {
                        $_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app][] = "library/{$ex[0]}/{$ex[1]}/{$ex[1]}.php";
                    } else {
                        $_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app][] = "library/{$ex[0]}/{$ex[1]}.php";
                    }
                }
            }
        }
        foreach($_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app] as $kbmodule) {
            require_once $kbmodule;
        }
        /**
         * Cargar plug-ins de la aplicaci&oacute;n
         */
        if (!isset($_SESSION['KUMBIA_PLUGINS'])) {
            $_SESSION['KUMBIA_PLUGINS'] = array();
        }
        if (!isset($_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']])) {
            $_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']] = array();
            $_SESSION['KUMBIA_PLUGINS_CLASSES'][$_SESSION['KUMBIA_PATH']] = array();
        }
        if (!isset($_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']][$active_app])) {
            $_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']][$active_app] = array();
            $_SESSION['KUMBIA_PLUGINS_CLASSES'][$_SESSION['KUMBIA_PATH']][$active_app] = array();
            /**
             * Rutas a Plugins
             */
            $config = Config::read("config.ini");
            $plugins_dir = "apps/{$config->$active_app->plugins_dir}";
            foreach(scandir($plugins_dir) as $plugin) {
                if (strpos($plugin, ".php")) {
                    $_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']][$active_app][] = "{$plugins_dir}/$plugin";;
                    $plugin = str_replace(".php", "", $plugin);
                    $_SESSION['KUMBIA_PLUGINS_CLASSES'][$_SESSION['KUMBIA_PATH']][$active_app][] = $plugin;
                }
            }
        }
        foreach($_SESSION['KUMBIA_PLUGINS'][$_SESSION['KUMBIA_PATH']][$active_app] as $plugin) {
            include_once $plugin;
        }
        /**
         * Establecer el timezone para las fechas y horas
         */
        $config = Config::read("config.ini");
        if (isset($config->kumbia->timezone)) {
            date_default_timezone_set($config->kumbia->timezone);
        } else {
            date_default_timezone_set("America/New_York");
        }
    }
    /**
     * Funci&oacute;n Principal donde se ejecutan los controladores
     *
     * @return boolean
     */
    static function main() {
        /**
         * Rutas Base
         */
        $config = Config::read("config.ini");
        //Aplicacion Activa
        self::$active_app = $active_app = Router::get_application();
        //Directorio de controladores Activo
        $controllers_dir = "apps/" . $config->$active_app->controllers_dir;
        self::$active_controllers_dir = "apps/" . $config->$active_app->controllers_dir;
        //Directorio de modelos activo
        $models_dir = "apps/" . $config->$active_app->models_dir;
        self::$active_models_dir = "apps/" . $config->$active_app->models_dir;
        //Directorio de Vistas Activo
        $views_dir = "apps/" . $config->$active_app->views_dir;
        self::$active_views_dir = "apps/" . $config->$active_app->views_dir;
        //Directorio de Plugins Activo
        $plugins_dir = "apps/" . $config->$active_app->plugins_dir;
        self::$active_plugins_dir = $plugins_dir;
        //Directorio de Helpers Activo
        $helpers_dir = "apps/" . $config->$active_app->helpers_dir;
        self::$active_helpers_dir = $helpers_dir;
        /**
         * Leer la configuraci&oacute;n. La primera vez que se lee
         * enviroment.ini debe ser leido despues de definir los directorios
         * y aplicacion activa
         */
        $environ = Config::read();
        /**
         * @see ControllerBase
         */
        include_once "$controllers_dir/application.php";

		/**
    	 * Incluyendo las librerias del Framework
    	 */
    	self::_load_library_kumbia();

        /**
         * Carga los helpers de usuario del modulo principal
         *
         */
        use_helper('main');
        try {
            /**
             * Iniciar el buffer de salida
             */
            ob_start();
            /**
             * El driver de Kumbia es cargado segun lo que diga en config.ini
             */
            if (!DbLoader::load_driver()) {
                return false;
            }
            /**
             * Inicializa los Modelos. model_base es el modelo base
             */
            include_once "$models_dir/base/model_base.php";
            /**
             * Los demas modelos estan en el directorio de modelos
             */
            self::_init_models($models_dir);
            /**
             * Inicializa los plug-ins
             */
            PluginManager::initialize_plugins();
            $controller = null;
            if ($_SESSION['session_data']) {
                if (!is_array($_SESSION['session_data'])) {
                    $_SESSION['session_data'] = unserialize($_SESSION['session_data']);
                }
            }
            Router::set_routed(true);
            Router::if_routed();
            $controller_name = Router::get_controller();
            /**
             * Ejectutar Plugin::before_dispatch_loop()
             */
            foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                if (method_exists($controller_plugin, "before_dispatch_loop")) {
                    $controller_plugin->before_dispatch_loop($controller);
                    Router::if_routed();
                    $controller_name = Router::get_controller();
                }
            }
            /**
             * Ciclo del enrutador
             */
            while (Router::get_routed()) {
                Router::set_routed(false);
                /**
                 * Ejectutar Plugin::before_dispatch()
                 */
                foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                    if (method_exists($controller_plugin, "before_dispatch")) {
                        $controller_plugin->before_dispatch();
                        $controller_name = Router::get_controller();
                    }
                }
                /**
                 * Si no hay controlador ejecuta ControllerBase::init()
                 */
                if (empty($controller_name)) {
                    Dispatcher::init_base();
                } else {
                    /**
                     * El controlador builder es llamado automaticamente
                     * desde el core del framework
                     */
                    if (Router::get_controller() == "builder" && $config->$active_app->interactive) {
                        Dispatcher::set_controller_dir("library/kumbia/controller");
                    } else {
                        Dispatcher::set_controller_dir($controllers_dir);
                    }
                    /**
                     * Ejectutar Plugin::before_execute_route()
                     */
                    foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                        if (method_exists($controller_plugin, "before_execute_route")) {
                            $controller_plugin->before_execute_route(Router::get_controller(), Router::get_controller(), Router::get_action(), Router::get_parameters(), Router::get_all_parameters());
                        }
                    }
                    $controller = Dispatcher::execute_route(Router::get_module(), Router::get_controller(), Router::get_action(), Router::get_parameters(), Router::get_all_parameters());
                    /**
                     * Ejectutar Plugin::after_execute_route()
                     */
                    foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                        if (method_exists($controller_plugin, "after_execute_route")) {
                            $controller_plugin->after_execute_route(Router::get_controller(), Router::get_action(), Router::get_parameters(), Router::get_all_parameters());
                        }
                    }
                }
                Router::if_routed();
                $module_name = Router::get_module();
                $controller_name = Router::get_controller();
                $action_name = Router::get_action();
                /**
                 * Ejectutar Plugin::after_dispatch()
                 */
                foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                    if (method_exists($controller_plugin, "after_dispatch")) {
                        $controller_plugin->after_dispatch($controller);
                        Router::if_routed();
                        $module_name = Router::get_module();
                        $controller_name = Router::get_controller();
                        $action_name = Router::get_action();
                    }
                }
            }
            /**
             * Ejectutar Plugin::after_dispatch_loop()
             */
            foreach(PluginManager::get_controller_plugins() as $controller_plugin) {
                if (method_exists($controller_plugin, "after_dispatch_loop")) {
                    $controller_plugin->after_dispatch_loop($controller);
                    Router::if_routed();
                    $module_name = Router::get_module();
                    $controller_name = Router::get_controller();
                    $action_name = Router::get_action();
                }
            }
            if (!empty($controller_name)) {
            	extract(self::$models, EXTR_OVERWRITE);
                if (is_subclass_of($controller, "ApplicationController")) {
                    foreach($controller as $var => $value) {
                        $$var = $value;
                    }
                }
                foreach(self::$_persistence as $p) {
                    Session::set($p, self::$models[$p]);
                }
                /**
                 * Kumbia busca un los templates correspondientes al nombre de la accion y el layout
                 * del controlador. Si el controlador tiene un atributo $template tambien va a
                 * cargar la vista ubicada en layouts con el valor de esta
                 *
                 * en views/$controller/$action
                 * en views/layouts/$controller
                 * en views/layouts/$template
                 *
                 * Los archivos con extension .phtml son archivos template de kumbia que
                 * tienen codigo html y php y son el estandar, tambien pueden tener
                 * extensi&oacute;n .tpl, en este caso, Kumbia hace la integraci&oacute;n con Smarty si
                 * este esta disponible.
                 *
                 */
                Kumbia::$content = ob_get_contents();
                /**
                 * Verifica si existe cache para el layout, vista &oacute; template
                 * sino, crea un directorio en cache
                 */
                $view_cache_dir = null;
                if ($controller_name != "") {
                    /*
                     * Creando el directorio Cache
                     */
                	if ($controller->get_view_cache() || $controller->get_layout_cache()) {
                		self::_create_cache_dir($module_name, $controller_name);
                	}

                    $view_file = "$views_dir/";
                    if ($module_name) {
                        $view_file.= "$module_name/";
                    }
                    $view_file.= "$controller_name/$action_name";
                    /**
                     * Insertar la vista si es necesario
                     */
                    if ($controller->response != 'xml') {
                        $view_file.= '.phtml';
                        if (file_exists($view_file)) {
                            ob_clean();
                            /**
                             * Aqui verifica si existe un valor en minutos para el cache
                             */
                            if ($controller->get_view_cache()) {
                                /**
                                 * Busca el archivo en el directorio de cache que se crea
                                 * a partir del valor $_SESSION['SID'] para que sea &uacute;nico
                                 * para cada sesi&oacute;n
                                 */
                                if (!file_exists($view_cache_dir . "/$action_name")) {
                                    include $view_file;
                                    file_put_contents($view_cache_dir . "/$action_name", ob_get_contents());
                                } else {
                                    $time_cache = $controller->get_view_cache();
                                    if ((time() - $time_cache * 60) < filemtime("$view_cache_dir/$action_name")) {
                                        include "$view_cache_dir/$action_name";
                                    } else {
                                        include $view_file;
                                        file_put_contents($view_cache_dir . "/$action_name", ob_get_contents());
                                    }
                                }
                            } else {
                                include $view_file;
                            }
                            Kumbia::$content = ob_get_contents();
                        } else {
                            $view_file.= '.tpl';
                            if (defined('USE_SMARTY')) {
                                if (file_exists($view_file)) {
                                    if (!Kumbia::$_smarty_object) {
                                        self::_kumbia_smarty($controller);
                                    }
                                    foreach($controller as $_key => $_value) {
                                        Kumbia::$_smarty_object->assign($_key, $_value);
                                    }
                                    foreach(self::$models as $_model_name => $_model) {
                                        Kumbia::$_smarty_object->assign($_model_name, $_model);
                                    }
                                    Kumbia::$_smarty_object->display($action_name . ".tpl");
                                    Kumbia::$content = ob_get_contents();
                                }
                            }
                        }
                    }
                    /**
                     * Incluir Template (layout)
                     */
                    if ($controller->response != 'xml' && $controller->response != 'view') {
                        if ($controller->template) {
                            /**
                             * Aqui verifica si existe un valor en minutos para el cache
                             */
                            if (file_exists("$views_dir/layouts/" . $controller->template . ".phtml")) {
                                ob_clean();
                                if ($controller->get_layout_cache()) {
                                    /**
                                     * Busca el archivo en el directorio de cache que se crea
                                     * a partir del valor session_id() para que sea &uacute;nico
                                     * para cada sesion
                                     */
                                    if (!file_exists($view_cache_dir . "/layout")) {
                                        include "$views_dir/layouts/" . $controller->template . ".phtml";
                                        file_put_contents($view_cache_dir . "/layout", ob_get_contents());
                                    } else {
                                        $time_cache = $controller->get_layout_cache();
                                        if ((time() - $time_cache * 60) < filemtime($view_cache_dir . "/layout")) {
                                            include $view_cache_dir . "/layout";
                                        } else {
                                            include "$views_dir/layouts/" . $controller->template . ".phtml";
                                            file_put_contents($view_cache_dir . "/layout", ob_get_contents());
                                        }
                                    }
                                } else {
                                    include "$views_dir/layouts/" . $controller->template . ".phtml";
                                }
                                Kumbia::$content = ob_get_contents();
                            }
                        } elseif (file_exists("$views_dir/layouts/$controller_name.phtml")) {
                            ob_clean();
                            if ($controller->get_layout_cache()) {
                                /**
                                 * Busca el archivo en el directorio de cache que se crea
                                 * a partir del valor session_id() para que sea &uacute;nico
                                 * para cada sesion
                                 */
                                if (!file_exists($view_cache_dir . "/layout")) {
                                    include "$views_dir/layouts/$controller_name.phtml";
                                    file_put_contents($view_cache_dir . "/layout", ob_get_contents());
                                } else {
                                    $time_cache = $controller->get_layout_cache();
                                    if ((time() - $time_cache * 60) < filemtime($view_cache_dir . "/layout")) {
                                        include $view_cache_dir . "/layout";
                                    } else {
                                        include "$views_dir/layouts/" . $controller_name . ".phtml";
                                        file_put_contents($view_cache_dir . "/layout", ob_get_contents());
                                    }
                                }
                            } else {
                                include "$views_dir/layouts/$controller_name.phtml";
                            }
                            Kumbia::$content = ob_get_contents();
                        }
                    }
                }
                /**
                 * Incluir Vista Principal
                 */
                if (($controller->response != 'view') && ($controller->response != 'xml')) {
                    if (file_exists("$views_dir/index.phtml")) {
                        ob_clean();
                        include "$views_dir/index.phtml";
                        Kumbia::$content = ob_get_contents();
                    }
                    if ($_SESSION['session_data']) {
                        $_SESSION['session_data'] = serialize($_SESSION['session_data']);
                    }
                    foreach(self::$_persistence as $p) {
                        if (Session::get_data($p)) {
                            Session::get_data($p, self::$models[$p]);
                        }
                    }
                    ob_end_flush();
                    $controller = null;
                }
            }
        }
		catch(DispatcherException $e) {
			if (!isset($config->$active_app->interactive) || !$config->$active_app->interactive) {
				ob_end_clean();
				ob_start();
				header('HTTP/1.1 404 Not Found');
				render_view('not_found');
			} else {
				 $e->show_message();
			}
			Kumbia::$content = ob_get_contents();
			ob_end_clean();
			xhtml_template('white');
		}
        catch(KumbiaException $e) {
            $controller = Dispatcher::get_controller();
            if ($_SESSION['session_data']) {
                $_SESSION['session_data'] = serialize($_SESSION['session_data']);
            }
            foreach(self::$_persistence as $p) {
                if (Session::get_data($p)) {
                    Session::get_data($p, self::$models[$p]);
                }
            }
            /**
             * Si no es una Accion AJAX incluye index.phtml y muestra
             * el contenido de las excepciones dentro de este.
             */
            if (!$controller) {
                $controller = new Controller();
            }
            if (($controller->response != 'view') && ($controller->response != 'xml')) {
                if (!isset($config->$active_app->interactive) || !$config->$active_app->interactive) {
                    ob_end_clean();
                    Kumbia::$content = "";
                    ob_start();
                }
                $e->show_message();
                Kumbia::$content = ob_get_contents();
                ob_end_clean();
                xhtml_template('white');
            } else {
                ob_end_clean();
                $e->show_message();
            }
            return;
        }
    }
    /**
     * Inicializa los modelos en el directorio models
     *
     */
    private static function _init_models($models_dir) {
        foreach(scandir($models_dir) as $model) {
            if (!in_array($model, array('.', '..', 'base'))) {
                if (is_dir($models_dir . "/" . $model)) {
                    self::_init_models($models_dir . "/" . $model);
                }
            }
            if (ereg("\.php$", $model)) {
                require_once "$models_dir/$model";
                $model = str_replace(".php", "", $model);
                $object_model = str_replace("_", " ", $model);
                $object_model = ucwords($object_model);
                $object_model = str_replace(" ", "", $object_model);
                if (!class_exists($object_model)) {
                    throw new KumbiaException("No se encontr&oacute; la Clase \"$object_model\"", "Es necesario definir una clase en el modelo
							'$model' llamado '$object_model' para que esto funcione correctamente.");
                } else {
                    self::$models[$object_model] = new $object_model();
                    if (!is_subclass_of(self::$models[$object_model], "ActiveRecord")) {
                        throw new KumbiaException("Error inicializando modelo \"$object_model\"", "El modelo '$model' debe ser una clase o sub-clase de ActiveRecord");
                    }
                    if (!self::$models[$object_model]->get_source()) {
                        self::$models[$object_model]->set_source($model);
                    }
                    if (isset(self::$models[$object_model]->persistent)) {
                        if (self::$models[$object_model]->persistent) {
                            self::$_persistence[] = $object_model;
                        }
                    }
                }
            }
        }
        foreach(self::$_persistence as $p) {
            if (Session::get_data($p)) {
                self::$models[$p] = Session::get_data($p);
            }
        }
    }
    /**
     * Esta funci&oacute;n realiza la integraci&oacute;n con Smarty, creando los
     * directorios necesarios para que un controlador pueda
     * utlizar templates Smarty
     *
     * @param $controllerObj
     */
    private static function _kumbia_smarty($controller_object) {
        $controller_name = $controller_object->controller_name;
        if ($controller_object->module_name) {
            $controller_name = "{$controller_object->module_name}/$controller_name";
        }
        if (!Kumbia::$_smarty_object) {
            foreach($controller_object as $property => $value) {
                if (@get_class($value) == "Smarty") {
                    Kumbia::$smarty_object = $value;
                }
            }
        }
        if (!Kumbia::$_smarty_object) {
            Kumbia::$_smarty_object = new Smarty();
        }
        if (!file_exists("cache/$controller_name")) {
            mkpath("cache/$controller_name/compile");
            mkpath("cache/$controller_name/cache");
            mkpath("cache/$controller_name/config");
        }
        Kumbia::$_smarty_object->template_dir = "views/$controller_name/";
        Kumbia::$_smarty_object->compile_dir = "cache/$controller_name/compile";
        Kumbia::$_smarty_object->cache_dir = "cache/$controller_name/cache";
        Kumbia::$_smarty_object->config_dir = "cache/$controller_name/config";
    }
    /**
     * Verifica si $model es un modelo del Proyecto
     *
     * @param string $model
     * @return boolean
     */
    public static function is_model($model) {
        if ($model == '') {
            return false;
        }
        return isset(self::$models[self::get_model_name($model) ]);
    }
    /**
     * Devuelve el nombre de modelo de la entidad $model
     *
     * @param string $model
     * @return string
     */
    public static function get_model_name($model) {
        if ($model == '') {
            return false;
        }
        $objModel = str_replace("_", " ", $model);
        $objModel = ucwords($objModel);
        $objModel = str_replace(" ", "", $objModel);
        return $objModel;
    }
    /**
     * Crear los directorios de cache si es necesario asociados al controlador
     *
     * @return boolean
     */
    private static function _create_cache_dir($module_name, $controller_name){
    	$view_cache_dir = "cache/" . session_id() . "/";
		if (!file_exists("cache/" . session_id() . "/")) {
			mkdir($view_cache_dir);
		}
		$view_cache_dir.= self::$active_app;
		if ($module_name) {
			$view_cache_dir.= "/$module_name";
		}
		$view_cache_dir.= "/$controller_name";
		if (!file_exists($view_cache_dir)) {
        	mkpath($view_cache_dir);
		}
    }
    /**
     * Carga Librerias JavaScript Importantes en el Framework
     *
     */
    public static function javascript_base() {
        $application = Router::get_active_app();
        $controller_name = Router::get_controller();
        $action_name = Router::get_action();
        $module = Router::get_module();
        $id = Router::get_id();
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/scriptaculous/prototype.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/scriptaculous/effects.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/scriptaculous/dragdrop.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/kumbia/base.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/kumbia/validations.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/kumbia/main.php?app=$application&amp;module=$module&amp;path=" . urlencode(KUMBIA_PATH) . "&amp;controller=$controller_name&amp;action=$action_name&amp;id=$id\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/kumbia/kumbia.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/scriptaculous/unittest.js\"></script>\r\n";
        print "<script type=\"text/javascript\" src=\"" . KUMBIA_PATH . "javascript/scriptaculous/scriptaculous.js\"></script>\r\n";
        /**
         * Cargo constantes para el objeto javascript
         *
         */
        print "<script type=\"text/javascript\">\r\n";
        print "	<!-- <![CDATA[\r\n";
        print '$Kumbia.Constant.KUMBIA_PATH = "' . KUMBIA_PATH . "\";\r\n";
        print '$Kumbia.Constant.application = "' . $application . "\";\r\n";
        print '$Kumbia.Constant.module = "' . $module . "\";\r\n";
        print '$Kumbia.Constant.controller_name = "' . $controller_name . "\";\r\n";
        print '$Kumbia.Constant.action_name = "' . $action_name . "\";\r\n";
        print "	// ]]> -->\r\n";
        print "</script>\r\n";
    }
    /**
     * Carga Librerias JavaScript Windows
     *
     */
    private static function _javascript_windows() {
        print "<script type='text/javascript' src='" . KUMBIA_PATH . "javascript/scriptaculous/window.js'></script>\r\n";
    }
    /**
     * Imprime los CSS cargados mediante stylesheet_link_tag
     *
     */
    public static function stylesheet_link_tags() {
        $imports = Registry::get("KUMBIA_CSS_IMPORTS");
        if ($imports && is_array($imports)) {
            foreach ($imports as $css) {
                echo $css;
            }
        } else {
            echo $imports;
        }
    }
    /**
     * Enruta el controlador actual a otro controlador,
     * o otra accion
     * Ej:
     * <code>
     * kumbia::route_to("controller: nombre", ["action: accion"], ["id: id"])
     * </code>
     *
     * @return null
     */
    public static function route_to() {
        $args = func_get_args();
        return call_user_func_array(array("Router", "route_to"), $args);
    }
    /**
     * Metodo que muestra informaci&oacute;n del Framework y la licencia
     *
     */
    public static function info() {

        ob_start();
        print self::javascript_base();
        print self::_javascript_windows();
        print stylesheet_link_tag("../themes/default");
        print stylesheet_link_tag("../themes/mac_os_x");
        print "<style type=\"text/css\"><!--
	body {
		background: #6699cc;
	}

	#info {
		padding: 3em;
		font-family: \"Lucida Grande\",Verdana;
	}

	#info h1 { font-size: 3em; color: #2c2c2c; margin: 0; }
	#info p,li { font-size: 1.2em; text-align: justify; }
	-->
</style>" . "\n";
        print "<script type='text/javascript'>
		// <![CDATA[
		new Event.observe(window, \"load\", function(){
			var welcomeWindow = new Window(
				{
					className: \"mac_os_x\",
					width: 700,
					height: 500,
					zIndex: 100,
					resizable: true,
					title: \"Bienvenido a Kumbia\",
					showEffect: Effect.Appear,
					hideEffect: Effect.SwitchOff,
					draggable:true
				}
			)
			welcomeWindow.setHTMLContent($('content').innerHTML)
			welcomeWindow.showCenter()
		})
		// ]]>
		</script>";
        print "
		<div style=\"display:none\" id=\"content\">
	    <div id=\"info\">
	    <h1>Bienvenido a Kumbia</h1>
	    <p>Ya puedes empezar a usar el mejor framework para desarrollar aplicaciones web con php.</p>
	    <p>Kumbia es un web framework libre escrito en PHP5. Basado en las mejores pr&aacute;cticas
	    de desarrollo web, usado en software comercial y educativo, Kumbia fomenta la velocidad
	    y eficiencia en la creaci&oacute;n y mantenimiento de aplicaciones web, reemplazando tareas de
	    codificaci&oacute;n repetitivas por poder, control y placer. </p>
	    <p>Si ha visto a Ruby-Rails/Python-Django encontrara a Kumbia una alternativa para proyectos en PHP con caracter&iacute;sticas como: </p>
<ul>
<li>Sistema de Plantillas sencillo</li>
<li>Administraci&oacute;n de Cache</li>
<li>Scaffolding Avanzado</li>
<li>Modelo de Objetos y Separaci&oacute;n MVC</li>
<li>Soporte para AJAX</li>
<li>Generaci&oacute;n de Formularios</li>
<li>Componentes Gr&aacute;ficos</li>
<li>Seguridad</li>
</ul>
<p>y muchas cosas m&aacute;s. Kumbia puede ser la soluci&oacute;n que esta buscando. </p>

<p>El n&uacute;mero de prerrequisitos para instalar y configurar es muy peque&ntilde;o, apenas Unix o Windows con un servidor web y PHP5 instalado. Kumbia es compatible con motores de base de datos como MySQL, PostgreSQL, SQLite, Informix, Firebird, Informix y Oracle.</p>
<p>Usar Kumbia es f&aacute;cil para personas que han usado PHP y han trabajado patrones de dise&ntilde;o para aplicaciones de Internet cuya curva de aprendizaje est reducida a un d&iacute;a. El dise&ntilde;o limpio y la f&aacute;cil lectura del c&oacute;digo se facilitan con Kumbia. Desarrolladores pueden aplicar principios de desarrollo como DRY, KISS &oacute; XP, enfoc&aacute;ndose en la l&oacute;gica de aplicaci&oacute;n y dejando atr&aacute;s otros detalles que quitan tiempo.</p>
<p>Kumbia intenta proporcionar facilidades para construir aplicaciones robustas para entornos comerciales. Esto significa que el framework es muy flexible y configurable. Al escoger Kumbia esta apoyando un proyecto libre publicado bajo licencia GNU/GPL.</p>
<p>Para iniciar edite el archivo <strong>config/config.ini</strong></p>
		</div>
	</div>";
        Kumbia::$content = ob_get_contents();
        ob_end_clean();
        xhtml_template();
    }
    /**
     * Realiza un escaneo recursivo en un directorio
     *
     * @param string $package_dir
     * @param array $files
     * @return array
     */
    private static function _scandir_recursive($package_dir, $files = array()) {
        foreach(scandir($package_dir) as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($package_dir . "/" . $file)) {
                    $files = self::_scandir_recursive($package_dir . "/" . $file, $files);
                } else {
                    if (ereg("(.)+\.php$", $file)) {
                        $files[] = $package_dir . "/" . $file;
                    }
                }
            }
        }
        return $files;
    }
    /**
     * Importa un paquete recursivamente
     *
     * @param string $package
     */
    public static function import($package) {
        $package_array = explode(".", $package);
        $package_dir = "";
        $class = "";
        if ($package_array[count($package_array) - 1] == '*') {
            unset($package_array[count($package_array) - 1]);
            $package_dir = join(".", $package_array);
            $class = '*';
        } else {
            $package_dir = $package;
        }
        if ($class == '*') {
            $package_dir = str_replace('.', '/', $package_dir);
            if (!file_exists($package_dir)) {
                throw new kumbiaException("No existe el directorio '$package_dir'\n");
            }
            $files = self::scandir_recursive($package_dir);
            foreach($files as $file) {
                include_once $file;
            }
        } else {
            $package_dir = str_replace('.', '/', $package_dir);
            if (file_exists($package_dir . '.php')) {
                $package_dir = escapeshellcmd($package_dir);
                include_once $package_dir . '.php';
            } else {
                throw new kumbiaException("No existe el directorio '$package_dir'\n");
            }
        }
    }
    /**
     * Permite lanzar excepciones de PHP o externas a Kumbia como propias
     *
     * @param Exception $exception
     */
    public static function manage_exceptions($exception) {
        throw new KumbiaException($exception->getMessage(), $exception->getCode());
    }
    /**
     * Permite obtener el mode de la aplicacion activa
     * @return string
     *
     */
    public static function get_active_app_mode() {
        $core = Config::read('config.ini');
        return $core->{self::$active_app}->mode;
    }
    /**
     * Permite obtener el tipo de codificacion de caracteres de la aplicacion activa
     * @return string
     *
     */
    public static function get_active_app_charset() {
        $core = Config::read('config.ini');
        return $core->{self::$active_app}->charset;
    }

    /**
     * Metodo para incluir las librerias de Framework
     *
     */
    private static function _load_library_kumbia(){
    	/**
         * @see Dispatcher
         */
        include_once "library/kumbia/dispatcher/dispatcher.php";
        /**
         * @see Session
         */
        include_once "library/kumbia/session/session.php";
        /**
         * @see Controller
         */
        include_once "library/kumbia/controller/controller.php";
        /**
         * @see ApplicationController
         */
        include_once "library/kumbia/controller/application/application.php";
        /**
         * @see StandardForm
         */
        include_once "library/kumbia/controller/standard_form/standard_form.php";
        /**
         * @see BuilderController
         */
        include_once "library/kumbia/controller/builder/builder.php";
        /**
         * @see Filter
         */
        include_once "library/kumbia/filter/filter.php";
        /**
         * @see Db
         */
        include_once "library/kumbia/db/db.php";
        /**
         * @see ActiveRecordBase
         */
        include_once "library/kumbia/db/active_record_base/active_record_base.php";
        /**
         * @see Flash
         */
        include_once "library/kumbia/messages/flash.php";
        /**
         * @see Helpers
         */
        include_once "library/kumbia/helpers/helpers.php";
        /**
         * @see Benchmark
         */
        include_once 'library/kumbia/benchmark/benchmark.php';
    }
}
?>
