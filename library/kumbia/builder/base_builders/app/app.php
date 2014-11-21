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
 * @package Builder
 * @subpackage BaseBuilders
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Construye una aplicacion
 *
 * @param array
 * @return boolean
 */
class AppBuilder implements BuilderInterface {

	/**
		directorio donde se encuentra la plantilla base para una aplicacion
	**/
	public $template_dir = 'library/kumbia/builder/base_builders/app/template'; 


	/**
 	 * Ejecuta el builder
 	 *
 	 * @param array $params
 	 * @return mixed
 	 */
	public function execute($params){
		$success = false;
	
		/**
			Leo el archivo de configuracion "config.ini"
		**/
		$sections = Config::read('config.ini');
	
		/**
			Construyo la seccion para las aplicaciones indicadas
		**/
		$p = new stdClass();
		$p->mode = isset($params['mode']) ? $params['mode'] : 'development';
		$p->charset = isset($params['charset']) ? $params['charset'] : 'ISO-8859-1';
		$p->name = isset($params['name']) ? "\"$params[name]\"" : '"KUMBIA PROJECT"';
		$p->interactive = isset($params['interactive']) ? ucfirst($params['interactive']) : 'On';
		$p->dbdate = isset($params['dbdate']) ? $params['dbdate'] : 'YYYY-MM-DD';
		$p->debug = isset($params['debug']) ? ucfirst($params['debug']) : 'On';
		$p->log_exceptions = isset($params['log_exceptions']) ? ucfirst($params['log_exceptions']) : 'On';
		
		for($i=0; isset($params[$i]); $i++) {
			$p->controllers_dir = isset($params['controllers_dir']) ? $params['controllers_dir'] : join_path($params[$i], 'controllers');
			$p->models_dir = isset($params['models_dir']) ? $params['models_dir'] : join_path($params[$i], 'models');
			$p->views_dir = isset($params['views_dir']) ? $params['views_dir'] : join_path($params[$i], 'views');
			$p->plugins_dir = isset($params['plugins_dir']) ? $params['plugins_dir'] : join_path($params[$i], 'views');
			$p->helpers_dir = isset($params['hepers_dir']) ? $params['helpers_dir'] : join_path($params[$i], 'views');
			
			$app = $params[$i];
			if($app != 'kumbia') {
				/**
					Cargo en las secciones, la seccion para la aplicacion
				**/
				$sections->$app = $p;
				
				/**
					Creo la estructura de directorios
				**/
				$dir = join_path('apps', $p->controllers_dir);
				if(!is_dir($dir)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Generando directorio de controladores:\r\n$dir\r\n";
					}
					
					mkpath($dir);
					copy_dir(join_path($this->template_dir, 'controllers'), $dir);
				}
				
				$dir = join_path('apps', $p->models_dir);
				if(!is_dir($dir)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Generando directorio de modelos:\r\n$dir\r\n";
					}
					
					mkpath($dir);
					copy_dir(join_path($this->template_dir, 'models'), $dir);
				}
				
				$dir = join_path('apps', $p->views_dir);
				if(!is_dir($dir)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Generando directorio de vistas:\r\n$dir\r\n";
					}
					
					mkpath($dir);
					copy_dir(join_path($this->template_dir, 'views'), $dir);
				}
				
				$dir = join_path('apps', $p->plugins_dir);
				if(!is_dir($dir)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Generando directorio de plugins:\r\n$dir\r\n";
					}
					mkpath($dir);
					copy_dir(join_path($this->template_dir, 'plugins'), $dir);
				}
				
				$dir = join_path('apps', $p->helpers_dir);
				if(!is_dir($dir)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Generando directorio de helpers:\r\n$dir\r\n";
					}
					mkpath($dir);
					copy_dir(join_path($this->template_dir, 'helpers'), $dir);
				}
			}
		}
		
		/**
			Escribo en el archivo de configuracion "config.ini"
		**/
		if($f = fopen('config/config.ini', 'w')) {
			if(defined('KUMBIA_TERMINAL')) {
				echo "\r\n-> Generando configuracion para la aplicacion\r\n";
			}
			
			fwrite($f,";; Configuracion de Aplicaciones

; Explicación de la Configuración:

; default_app: es la aplicacion por defecto
; mode: Es el entorno en el que se esta trabajando que esta definido en config/config
; name: Es el nombre de la aplicación
; timezone: Es la zona horaria que usará el framework
; interactive: Indica si la aplicación se encuentra en modo interactivo
; controllers_dir: Indica en que directorio se encuentran los controladores
; models_dir: Indica en que directorio se encuentran los modelos
; views_dir: Indica en que directorio se encuentran las vistas
; helpers_dir: Indica en que directorio se encuentran los helpers de usuario
; dbdate: Formato de Fecha por defecto de la Applicación
; charset: codificacion de caracteres de la aplicacion

");
			Config::write($f, $sections);
			fclose($f);
			
			$success = true;
		}
		
		return $success;
	}

}

?>