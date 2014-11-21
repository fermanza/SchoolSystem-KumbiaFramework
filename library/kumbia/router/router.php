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
 * @package Router
 * @abstract
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see RouterException
 */
require_once "library/kumbia/router/exception.php";

/**
 * Clase que Actua como router del Front-Controller
 *
 * @category Kumbia
 * @package Router
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 *
 */
abstract class Router extends Object {

	/**
	 * Nombre de la aplicación actual
	 *
	 * @var string
	 */
	static private $application;

	/**
	 * Nombre del modulo actual
	 *
	 * @var string
	 */
	static private $module;

	/**
	 * Nombre del controlador actual
	 *
	 * @var string
	 */
	static private $controller;

	/**
	 * Nombre de la accion actual
	 *
	 * @var string
	 */
	static private $action = 'index';

	/**
	 * Nombre del primer parametro despues de action
	 *
	 * @var string
	 */
	static private $id;

	/**
	 * Lista de Todos los parametros de la URL
	 *
	 * @var array
	 */
	static private $all_parameters = array();

	/**
	 * Lista los parametros adicionales de la URL
	 *
	 * @var array
	 */
	static private $parameters = array();

	/**
	 * Indica si se esta utilizando el modo de aplicacion por defecto
	 *
	 * @var boolean
	 */
	static private $default_app_mode = true;

	/**
	 * Indica si esta pendiente la ejecución de una ruta por
	 * parte del dispatcher
	 *
	 * @var boolean
	 */
	static private $routed;

	/**
	 * Detector de enrutamiento ciclico
	 */
	static private $routed_cyclic;

	/**
	 * Toma $url y la descompone en controlador, accion y argumentos
	 *
	 * @param string $url
	 */
	static function rewrite($url){

		/**
		 * Cargo la configuracion
		 */
		$config = Config::read("config.ini");
		
		/**
		 * Valor por defecto
		 */
		
		self::$application = $config->kumbia->default_app;
		
		// Si esta vacio (es root) volver con los parametros por defecto
		if (!$url) {
			return;
		}
    	
		/**
		 * Limpio la url en caso de que la hallan escrito con el 
		 * ultimo parametro sin valor es decir controller/action/
		 **/
		$url = trim($url,'/');

		/**
		 * Obtengo y asigno todos los parametros de la url
		 **/
		$url_items = explode ('/', $url);
		
		// Se limpian los parametros por seguridad
		$clean = array( '\\', '..');
		$url_items = str_replace($clean,  '', $url_items, $errors);
		
		// Si hay intento de hack TODO: añadir la ip y referer en el log
		if($errors) throw new RouterException("Posible intento de hack en URL: '$url'");
		
		/**
		* Asigna todos los parametros
		**/
		self::$all_parameters = $url_items;
	       
		/**
		 * Si el primer parametro de la url corresponde a una aplicacion
		 */  
		if (isset($config->$url_items[0])) {
			/**
			 * Indico la aplicacion que se ejecutara
			 **/
			self::$application = $url_items[0];
			/**
			 * Indico que no se utiliza el modo de aplicacion por defecto
			 **/
			self::$default_app_mode = false;
			
			// Si no hay mas parametros sale
			if (next($url_items) === FALSE) {
				return;
			}
		}
		
		/**
		* Obtengo el directorio de controladores
		**/
		$controllers_dir = $config->{self::$application}->controllers_dir;
		
		/**
		* El siguiente parametro de la url es un modulo?
		**/
		$item = current($url_items);
		if(is_dir("apps/$controllers_dir/$item")) {
		       self::$module = current($url_items);
		       
		       // Si no hay mas parametros sale y pone index como controlador
			if (next($url_items) === FALSE) {
				self::$controller = 'index';
				return;
			}       
		}       
		       
		/**
		 * Controlador
		 */
		self::$controller = current($url_items);
			
			// Si no hay mas parametros sale
			if (next($url_items) === FALSE) {
				return;
			}       
			
		/**
		* Accion
		*/
		self::$action = current($url_items);
		
			// Si no hay mas parametros sale
			if (next($url_items) === FALSE) {
				return;
			}
		
		/**
		*  id
		*/
		self::$id = current($url_items);
		
		/**
		*  Creo los parametros y los paso, depues elimino el $url_items
		*/
		$key = key($url_items);
		$rest = count($url_items) - $key;
		$parameters = array_slice($url_items, $key, $rest);
		
		self::$parameters = $parameters;
		unset ($url_items);
	}

	
	static function routed(){
		$routes = Config::read('routes.ini');
		
		/**
				Leo la politica de enrutamiento para la aplicacion
			**/
			if(isset($routes->{self::$application})){
				foreach($routes->{self::$application} as $source => $destination){
					
					/**
						Extraigo la regla de enrutamiento para el origen
					**/
					// Limpio la URL
					$source = trim($source,'/');
					
					$source_items = explode ('/', $source);
					
					$num_sep = substr_count($source, '/');
					if ($source === '/'){
						$module_source = ''; 
						$action_source = 'index';
						$controller_source = '';
						$id_source =  null;
					} elseif ($num_sep==1 && $source != '/') {
						$module_source = ''; 
						$action_source = 'index';
						$controller_source = 'ss';
						$id_source =  null;
					} elseif($num_sep==2) {
						list($controller_source, $action_source, $id_source) = explode("/", $source);
						$module_source = ''; 
					} elseif($num_sep==3) {
						list($module_source, $controller_source, $action_source, $id_source) = explode("/", $source);
					} else {
						throw new RouterException("Pol&iacute;tica de enrutamiento invalida '$source' a '$destination' en config/routes.ini");
					}
					$application_source = self::$application;
				}
				
			}
		
	}
	
	/**
 	 * Busca en la tabla de entutamiento si hay una ruta en config/routes.ini
 	 * para el controlador, accion, id actual
     *
     */
	static function if_routed(){
		unset($_SESSION['KUMBIA_STATIC_ROUTES']);
		if(!isset($_SESSION['KUMBIA_STATIC_ROUTES'])){
			$routes = Config::read('routes.ini');
			$config = Config::read('config.ini');
			
			/**
				Leo la politica de enrutamiento para la aplicacion
			**/
			if(isset($routes->{self::$application})){
				//echo "aplicacion";
				foreach($routes->{self::$application} as $source => $destination){
				
					/**
						Extraigo la regla de enrutamiento para el origen
					**/
					$num_sep = substr_count($source, '/');
					if ($source === '/'){
						$module_source = ''; 
						$action_source = 'index';
						$controller_source = '';
						$id_source =  null;
					} elseif ($num_sep==1 && $source != '/') {
						$module_source = ''; 
						$action_source = 'index';
						$controller_source = 'ss';
						$id_source =  null;
					} elseif($num_sep==2) {
						list($controller_source, $action_source, $id_source) = explode("/", $source);
						$module_source = ''; 
					} elseif($num_sep==3) {
						list($module_source, $controller_source, $action_source, $id_source) = explode("/", $source);
					} else {
						throw new RouterException("Pol&iacute;tica de enrutamiento invalida '$source' a '$destination' en config/routes.ini");
					}
					$application_source = self::$application;

					/**
					 * Evaluo la regla de enrutamiento
					 **/
					$num_sep = substr_count($destination, '/');
					if($num_sep==1) {
						$module_destination = ''; 
						$action_destination = 'index';
						$controller_destination = '';
						$id_destination = null;
						//echo "--destino sep1";
					} elseif($num_sep==2) {
						list($controller_destination, $action_destination, $id_destination) = explode("/", $destination);
						$module_destination = '';
						//echo "--destino sep2";
					} elseif($num_sep==3) {
						list($module_destination, $controller_destination, $action_destination, $id_destination) = explode("/", $destination);
					} else {
						throw new RouterException("Pol&iacute;tica de enrutamiento invalida '$source' a '$destination' en config/routes.ini");
					}
					$application_destination = self::$application;
					//echo "[destino contr:".$controller_destination." source contr:".$controller_source."]";
					/**
						Si la politica de enrutamiento no es ciclica, entonces la cargo en la variable de sesion
					**/
					if($application_source==$application_destination && $module_source==$module_destination && $controller_source==$controller_destination && $action_source==$action_destination && $id_source==$id_destination){
						throw new KumbiaException("Politica de enrutamiento ciclica de '$source' a '$destination' en config/routes.ini");
					} else {
						$_SESSION['KUMBIA_STATIC_ROUTES'][$application_source][$module_source][$controller_source][$action_source][$id_source] = array(
							'application' => $application_destination,
							'module' => $module_destination,
							'controller' => $controller_destination,
							'action' => $action_destination,
							'id' => $id_destination
						);
					}
				}
			}
		}

		$application = self::$application;
		$module = self::$module;
		$controller = self::$controller;
		$action = self::$action;
		$id = self::$id;

		$new_route = array(
			'application' => '*',
			'module' => '*',
			'controller' => '*',
			'action' => '*',
			'id' => '*'
		);
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*']['*'][$action]['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*']['*'][$action]['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller]['*']['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller]['*']['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller]['*'][$id])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller]['*'][$id];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller][$action]['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller][$action]['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller][$action][$id])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*'][$controller][$action][$id];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module]['*'][$action]['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application]['*']['*'][$action]['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller]['*']['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller]['*']['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller]['*'][$id])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller]['*'][$id];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller][$action]['*'])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller][$action]['*'];
		}
		if(isset($_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller][$action][$id])){
			$new_route = $_SESSION['KUMBIA_STATIC_ROUTES'][$application][$module][$controller][$action][$id];
		}
		
		/**
		 * Asigno la nueva ruta
		 **/
		if($new_route['application']!='*'){
			self::$application = $new_route['application'];
		}		

		if($new_route['module']!='*'){
			self::$module = $new_route['module'];
		}
		
		if($new_route['controller']!='*'){
			self::$controller = $new_route['controller'];
		}
		
		if($new_route['action']!='*'){
			self::$action = $new_route['action'];
		}
		
		if($new_route['id']!='*'){
			self::$id = $new_route['id'];
		}

		return;
	}

	/**
	 * Devuelve el estado del router
	 *
	 * @return boolean
	 */
	public static function get_routed(){
		return self::$routed;
	}

	/**
	 * Devuelve el nombre de la aplicación actual
	 *
	 * @return string
	 */
	public static function get_application(){
		return self::$application;
	}

	/**
	 * Devuelve el nombre del modulo actual
	 *
	 * @return string
	 */
	public static function get_module(){
		return self::$module;
	}



	/**
	 * Devuelve el nombre del controlador actual
	 *
	 * @return string
	 */
	public static function get_controller(){
		return self::$controller;
	}

	/**
	 * Devuelve el nombre del controlador actual
	 *
	 * @return string
	 */
	public static function get_action(){
		return self::$action;
	}

	/**
	 * Devuelve el primer parametro (id)
	 *
	 * @return mixed
	 */
	public static function get_id(){
		return self::$id;
	}

	/**
	 * Devuelve los parametros de la ruta
	 *
	 * @return array
	 */
	public static function get_parameters(){
		return self::$parameters;
	}

	/**
	 * Devuelve los parametros de la ruta
	 *
	 * @return array
	 */
	public static function get_all_parameters(){
		return self::$all_parameters;
	}

	/**
	 * Establece el estado del Router
	 *
	 */
	public static function set_routed($value){
		self::$routed = $value;
	}

	/**
	 * Enruta el controlador actual a otro controlador, &oacute; a otra acción
	 * Ej:
	 * <code>
	 * kumbia::route_to(["module: modulo"], "controller: nombre", ["action: accion"], ["id: id"])
	 * </code>
	 *
	 * @return null
	 */
	static public function route_to(){
		self::$routed = false;
		$cyclic_routing = false;
		$url = get_params(func_get_args());
		
		if(isset($url['module'])){
			if(self::$module==$url['module']){
				$cyclic_routing = true;
			}
			
			/**
			 * Verifico para asignar correctamente los parametros en all_parameters,
			 * efectuando los debidos corrimientos de ser necesario
			 **/
			if(self::is_default_app_mode() && self::get_module()) {
				self::$all_parameters[0] = $url['module'];
			} elseif(self::is_default_app_mode()) {
				array_num_insert(self::$all_parameters, 0, $url['module']);
			} elseif(self::get_module()) {
				self::$all_parameters[1] = $url['module'];
			} else {
				array_num_insert(self::$all_parameters, 1, $url['module']);
			}
			
			self::$module = $url['module'];
			self::$controller = 'index';
			self::$action = "index";
			self::$routed = true;
		}
		if(isset($url['controller'])){
			if(self::$controller==$url['controller']){
				$cyclic_routing = true;
			}
			self::$controller = $url['controller'];
			
			/**
			 * Verifico para asignar correctamente los parametros en all_parameters,
			 * efectuando los debidos corrimientos de ser necesario
			 **/
			if(self::is_default_app_mode() && self::get_module()) {
				self::$all_parameters[1] = $url['controller'];
			} elseif(self::is_default_app_mode()) {
				self::$all_parameters[0] = $url['controller'];
			} elseif(self::get_module()) {
				self::$all_parameters[2] = $url['controller'];
			} else {
				self::$all_parameters[1] = $url['controller'];
			}
			
			self::$action = "index";
			self::$routed = true;
		}
		if(isset($url['action'])){
			if(self::$action==$url['action']){
				$cyclic_routing = true;
			}
			self::$action = $url['action'];

			/**
			 * Verifico para asignar correctamente los parametros en all_parameters,
			 * efectuando los debidos corrimientos de ser necesario
			 **/
			if(self::is_default_app_mode() && self::get_module()) {
				self::$all_parameters[2] = $url['action'];
			} elseif(self::is_default_app_mode()) {
				self::$all_parameters[1] = $url['action'];
			} elseif(self::get_module()) {
				self::$all_parameters[3] = $url['action'];
			} else {
				self::$all_parameters[2] = $url['action'];
			}

			self::$routed = true;
		}
		if(isset($url['id'])){
			if(self::$id==$url['id']){
				$cyclic_routing = true;
			}
			self::$id = $url['id'];

			/**
			 * Verifico para asignar correctamente los parametros en all_parameters,
			 * efectuando los debidos corrimientos de ser necesario
			 **/
			if(self::is_default_app_mode() && self::get_module()) {
				self::$all_parameters[3] = $url['action'];
			} elseif(self::is_default_app_mode()) {
				self::$all_parameters[2] = $url['action'];
			} elseif(self::get_module()) {
				self::$all_parameters[4] = $url['action'];
			} else {
				self::$all_parameters[3] = $url['action'];
			}

			self::$parameters[0] = $url['id'];
			self::$routed = true;
		}
		
		if($cyclic_routing){
			self::$routed_cyclic++;
			if(self::$routed_cyclic>=1000){
				throw new RouterException("Se ha detectado un enrutamiento ciclico. Esto puede causar problemas de estabilidad", 1000);
			}
		} else {
			self::$routed_cyclic = 0;
		}
		return null;
	}

	/**
	 * Nombre de la aplicaci&oacute;n activa actual devuelve "" en caso de
	 * que la aplicación sea default
	 *
	 * @return string
	 */
	static public function get_active_app(){
		return self::$default_app_mode ? "" : self::$application;
	}
	
	/**
	 * Indica si se esta ejecutando en modo de aplicacion por defecto, es decir en la url
	 * no se indico a que aplicacion debia accesar y kumbia tomo la aplicacion por defecto para ejecutar
	 *
	 * @return boolean
	 **/
	static public function is_default_app_mode() {
		return self::$default_app_mode;
	}
}
