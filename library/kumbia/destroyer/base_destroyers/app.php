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
 * @package Destroyer
 * @subpackage BaseDestroyers
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Construye una aplicacion
 *
 * @param array
 * @return boolean
 */
class AppDestroyer implements DestroyerInterface {

	/**
 	 * Ejecuta el destroyer
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
		
		for($i=0; isset($params[$i]); $i++) {			
			$app = $params[$i];
			if($app != 'kumbia') {
				if($sections->$app) {
					$p = $sections->$app;
					
					/**
						Eliminando la estructura de directorios
					**/
					$dir = join_path('apps',$p->controllers_dir);
					if(is_dir($dir)) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Eliminando directorio de controladores:\r\n$dir\r\n";
						}
						remove_dir($dir);
					}
					
					$dir = join_path('apps',$p->models_dir);
					if(is_dir($dir)) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Eliminando directorio de modelos:\r\n$dir\r\n";
						}
						remove_dir($dir);
					}
					
					$dir = join_path('apps',$p->views_dir);
					if(is_dir($dir)) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Eliminando directorio de vistas:\r\n$dir\r\n";
						}
						remove_dir($dir);
					}
					
					$dir = join_path('apps',$p->plugins_dir);
					if(is_dir($dir)) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Eliminando directorio de plugins:\r\n$dir\r\n";
						}
						remove_dir($dir);
					}
					
					$dir = join_path('apps',$app);
					if(is_dir($dir)) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Eliminando directorio de aplicacion:\r\n$dir\r\n";
						}
						remove_dir($dir);
					}
				}
			}
		}
		
		/**
			Escribo en el archivo de configuracion "config.ini"
		**/
		if($f = fopen('config/config.ini', 'w')) {
			if(defined('KUMBIA_TERMINAL')) {
				echo "\r\n-> Eliminando configuracion\r\n";
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
; dbdate: Formato de Fecha por defecto de la Applicación

");
			unset($sections->$app);
			Config::write($f, $sections);
			fclose($f);
			
			$success = true;
		}
		
		return $success;
	}

}

?>