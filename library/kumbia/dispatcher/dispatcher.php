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
 * @package    Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  Copyright (c) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @copyright  Copyright (c) 2007-2008 Deivinson Jose Tejeda Brito (deivinsontejeda at gmail.com)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see DispatcherException
 */
require_once "library/kumbia/dispatcher/exception.php";

/**
 * Clase para manejar excepciones ocurridas en la clase Logger
 *
 * @category Kumbia
 * @package Dispatcher
 * @abstract
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @access public
 */
abstract class Dispatcher extends Object {

	/**
	 * Objeto del controlador en ejecuci�n
	 *
	 * @var mixed
	 */
	static private $controller;

	/**
	 * Directorio de controladores
	 *
	 * @var string
	 */
	static private $controllers_dir;

	/**
	 * Codigo de error cuando no encuentra la accion
	 */
	const NOT_FOUND_ACTION = 100;
	const NOT_FOUND_CONTROLLER = 101;
	const NOT_FOUND_FILE_CONTROLLER = 102;
	const NOT_FOUND_INIT_ACTION = 103;

	/**
	 * Ejecuta la accion init en ApplicationController
	 *
	 * @return boolean
	 */
	static public function init_base(){

		/**
		 * Inicializa los componentes del Framework
		 */
		self::init_components();

		$app = new ApplicationController();
		if(method_exists($app, 'init')){
			$app->init();
		} else {
			if(method_exists($app, 'not_found')){
				$app->not_found($_REQUEST['controller'], $_REQUEST['action'], $_REQUEST['id']);
			} else {
				throw new DispatcherException("No se encontr&oacute; la Acci&oacute;n por defecto \"init\"
						Es necesario definir un m&eacute;todo en la clase controladora
						'ApplicationController' llamado 'init' para que esto funcione correctamente.",
						self::NOT_FOUND_INIT_ACTION);
			}
		}

	}

	/**
	 * Establece el directorio de los controladores
	 *
	 * @param string $directory
	 */
	static public function set_controller_dir($directory){
		self::$controllers_dir = $directory;
	}

	/**
	 * Ejecuta el filtro before presente en el controlador
	 *
	 * @param mixed $app_controller
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	static private function run_before_filters($app_controller, $controller, $action, $params){
		/**
   	     * El metodo before_filter es llamado antes de ejecutar una accion en un
		 * controlador, puede servir para realizar ciertas validaciones
		 */
		$params = array_merge(array($controller,$action), $params);
		if(method_exists($app_controller, "before_filter")){
			if(call_user_func_array(array(self::$controller, "before_filter"), $params)===false){
				return false;
			}
		} else {
			if(isset(self::$controller->before_filter)){
				if(call_user_func_array(array(self::$controller, self::$controller->before_filter), $params)===false){
					return false;
				}
			}
		}
	}

	/**
	 * Corre los filtros after en el controlador actual
	 *
	 * @param string $app_controller
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	static private function run_after_filters($app_controller, $controller, $action, $params){
		/**
		 * El metodo after_filter es llamado despues de ejecutar una accion en un
		 * controlador, puede servir para realizar ciertas validaciones
		 */
		$params = array_merge(array($controller,$action), $params);
		if(method_exists($app_controller, "after_filter")){
			call_user_func_array(array(self::$controller, "after_filter"), $params);
		} else {
			if(isset(self::$controller->after_filter)){
				call_user_func_array(array(self::$controller, self::$controller->after_filter), $params);
			}
		}
	}

	/**
	 * Incluye los componentes para ejecutar la petici&oacute;n
	 *
	 */
	static public function init_components(){

		/**
 		 * @see Security
 		 */
		require_once "library/kumbia/security/security.php";

		/**
 		 * @see InteractiveBuilder
 		 */
		require_once "library/kumbia/generator/builder.php";

		/**
		 * @see Generator
		 */
		require_once "library/kumbia/generator/generator.php";

		/**
		 * @see Helpers
		 */
		require_once "library/kumbia/helpers/helpers.php";

		/**
		 * @see SimpeXMLResponse
		 */
		require_once "library/kumbia/xml/xml.php";

	}

	/**
	 * Realiza el dispatch de una ruta
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array  $params
	 * @return boolean
	 */
	static public function execute_route($module, $controller, $action, $parameters, $all_parameters){

		/**
		 * Aplicacion activa
		 */
		$active_app = Router::get_application();

		if($module){
			$controllers_dir = self::$controllers_dir."/".$module;
		} else {
			$controllers_dir = self::$controllers_dir;
		}

		$app_controller = ucfirst(camelize($controller))."Controller";

		if(!class_exists($app_controller)){
			if(file_exists("$controllers_dir/{$controller}_controller.php")){
				require_once "$controllers_dir/{$controller}_controller.php";
			} else {

				/**
		 	     * @see InteractiveBuilder
		 	     */
				require_once "library/kumbia/generator/builder.php";
				$active_app = Kumbia::$active_app;
				$config = Config::read("config.ini");
				$interactive = $config->$active_app->interactive;

				if($interactive){
					InteractiveBuilder::create_controller($controller, $action);
				}
				throw new DispatcherException("No se encontr&oacute; el Controlador \"$controller\". Hubo un problema al cargar el controlador, probablemente
					el archivo no exista en el directorio de m&oacute;dulos &oacute; exista algun error de sintaxis.", self::NOT_FOUND_FILE_CONTROLLER);
			}
		}

		if(class_exists($app_controller)) {

			/**
			 * Inicializa los componentes del Framework
			 */
			self::init_components();

			if(!isset($_SESSION['KUMBIA_CONTROLLERS'][$_SESSION['KUMBIA_PATH']][$active_app][$module][$app_controller])||eval("return $app_controller::\$force;")){
				self::$controller = new $app_controller();
			} else {
				self::$controller = unserialize($_SESSION['KUMBIA_CONTROLLERS'][$_SESSION['KUMBIA_PATH']][$active_app][$module][$app_controller]);
			}
			$_SESSION['KUMBIA_CONTROLLERS'][$_SESSION['KUMBIA_PATH']][$active_app][$module][$app_controller] = serialize(self::$controller);
			self::$controller->response = "";
			self::$controller->module_name = $module;
			self::$controller->controller_name = $controller;
			self::$controller->action_name = $action;

			if(isset($parameters[0])){
				self::$controller->id = $parameters[0];
			} else {
				self::$controller->id = 0;
			}
			self::$controller->all_parameters = $all_parameters;
			self::$controller->parameters = $parameters;

			try {

				/**
			 	 * Se ejecutan los filtros before
			 	 */
				if(self::run_before_filters($app_controller, $controller, $action, $parameters)===false){
					return false;
				}

				/**
			 	 * Se agrega una referencia a los modelos como propiedades del controlador
			 	 */
				foreach(Kumbia::$models as $model_name => $model){
					self::$controller->{$model_name} = $model;
				}

				/**
			 	 * Se ejecuta el metodo con el nombre de la accion
			 	 * en la clase
			 	 */
				if(!method_exists(self::$controller, $action)){
					if(method_exists(self::$controller, 'not_found')){
						self::$controller->route_to('action: not_found');
						return true;
					} else {
					    throw new DispatcherException(
					    "No se encontr&oacute; la Acci&oacute;n \"{$action}\". Es necesario definir un m&eacute;todo en la clase
					     controladora '{$controller}' llamado '$action' para que
				         esto funcione correctamente.", Dispatcher::NOT_FOUND_ACTION);
						return false;
					}
				}
				/**
			 	 * Cuando una acci&oacute;n retorna un valor diferente de Nulo
			 	 * Kumbia autom&aacute;ticamente crea una salida XML
			 	 * para la acci&oacute;n utilizando un CDATA, es muy
			 	 * &uacute;til en conjunto a la funci&oacute;n JavaScript
			 	 * AJAX.query
			 	 */
				$value_returned = call_user_func_array(array(self::$controller, $action), $parameters);
				if(!is_null($value_returned)){
					$xml = new SimpleXMLResponse();
					$xml->add_data($value_returned);
					$xml->out_response();
					self::$controller->set_response('xml');
				}

				/**
			 	 * Corre los filtros after
			 	 */
				self::run_after_filters($app_controller, $controller, $action, $parameters);

			}
			catch(Exception $e){

				$cancel_throw_exception = false;

				/**
				 * Ejectutar Plugin::on_controller_exception()
				 */
				foreach(PluginManager::get_controller_plugins() as $controller_plugin){
					if(method_exists($controller_plugin, "on_controller_exception")){
						if($controller_plugin->on_controller_exception($e, self::$controller)===false){
							$cancel_throw_exception = true;
						}
					}
				}

				if(method_exists(self::$controller, "on_exception")){
					call_user_func(array(self::$controller, "on_exception"), $e);
				} else {
					if(!$cancel_throw_exception){
						throw $e;
					}
				}
			}

			foreach(Kumbia::$models as $model_name => $model){
				unset(self::$controller->{$model_name});
			}

			/**
			 * Se clona el controlador y se serializan las propiedades que no
			 * sean instancias de modelos
			 */
			$controller = clone self::$controller;
			try{
				foreach($controller as $property => $value){
					if(is_object($value)&&is_subclass_of($value, "ActiveRecordBase")){
						unset($controller->{$property});
					}
				}
				if(isset($_SESSION['KUMBIA_CONTROLLERS'][$_SESSION['KUMBIA_PATH']][$active_app][$module][$app_controller])){
					$_SESSION['KUMBIA_CONTROLLERS'][$_SESSION['KUMBIA_PATH']][$active_app][$module][$app_controller] = serialize($controller);
				}
			}
			catch(PDOException $e){
				throw new KumbiaException($e->getMessage(), $e->getCode());
			}

			return self::$controller;

		} else {
			throw new DispatcherException("
					No se encontr&oacute; el Clase Controladora \"{$controller}Controller\".
					Debe definir esta clase para poder trabajar este controlador", self::NOT_FOUND_CONTROLLER);
		}
	}


	/**
 	 * Obtener el controlador en ejecucion
	 *
	 * @return mixed
	 */
	public static function get_controller(){
		return self::$controller;
	}


}

?>