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
 * @package Plugin
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see PluginException
 *
 */
require_once "library/kumbia/plugin/exception.php";

/**
 * @see Plugin
 *
 */
require_once "library/kumbia/plugin/abstract/plugin.php";

/**
 * @see ControllerPlugin
 *
 */
require_once "library/kumbia/plugin/abstract/controller_plugin.php";

/**
 * Permite administrar los plugins cargados
 *
 */
abstract class PluginManager extends Object {

	/**
	 * Array de todos los plugins
	 *
	 * @var array
	 */
	static private $plugins = array();

	/**
	 * Array de los plugins controlador
	 *
	 * @var array
	 */
	static private $plugins_controller = array();

	/**
	 * Array de los plugins de modelos
	 *
	 * @var array
	 */
	static private $plugins_model = array();

	/**
	 * Array de los plugins de vistas
	 *
	 * @var array
	 */
	static private $plugins_view = array();

	static public function initialize_plugins(){

		$active_app = Router::get_application();
		$plugin_classes = $_SESSION['KUMBIA_PLUGINS_CLASSES'][$_SESSION['KUMBIA_PATH']][$active_app];

		$plugins = array();
		$plugins_controller = array();
		$plugins_model = array();
		$plugins_view = array();

		foreach($plugin_classes as $plugin_class){
			if(class_exists($plugin_class)){
				$plug_in = new $plugin_class();
			} else {
				throw new PluginException("No existe la clase plug-in '$plugin_class' en el archivo $plugin_class.php");
			}
			if(is_subclass_of($plug_in, "Plugin")){
				$plugins[] = $plug_in;
			}
			if(is_subclass_of($plug_in, "ControllerPlugin")){
				$plugins_controller[] = $plug_in;
			}
			if(is_subclass_of($plug_in, "ModelPlugin")){
				$plugins_model[] = $plug_in;
			}
			if(is_subclass_of($plug_in, "ViewPlugin")){
				$plugins_view[] = $plug_in;
			}
		}

		self::$plugins = $plugins;
		self::$plugins_controller = $plugins_controller;
		self::$plugins_model = $plugins_model;
		self::$plugins_view = $plugins_view;

	}

	/**
	 * Obtiene instancias de los plugins
	 *
	 */
	static public function get_plugins(){
		return self::$plugins;
	}

	/**
	 * Obtiene instancias de los plugins tipo Controller
	 *
	 */
	static public function get_controller_plugins(){
		return self::$plugins_controller;
	}

	/**
	 * Obtiene instancias de los plugins tipo Model
	 *
	 */
	static public function get_model_plugins(){
		return self::$plugins_model;
	}

	/**
	 * Obtiene instancias de los plugins tipo View
	 *
	 */
	static public function get_view_plugins(){
		return self::$plugins_model;
	}

}

?>