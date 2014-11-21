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
 * Construye un controlador
 *
 * @param array
 * @return boolean
 */
class ControllerBuilder implements BuilderInterface {

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
		$s = Config::read('config.ini');
	
		$app = isset($params['app']) ? $params['app'] : $s->kumbia->default_app;
		
		if(isset($s->$app)) {
			for($i=0; isset($params[$i]); $i++) {
				$controller = uncamelize(lcfirst($params[$i]));
				$Controller = ucfirst(camelize($params[$i]));

				$dir = join_path('apps', $s->$app->controllers_dir, $params['module']);
				if(!is_dir($dir)) {
					mkdir($dir);
				}
				$controller_file = join_path($dir, "{$controller}_controller.php");
				
				
				if(!file_exists($controller_file)) {
					if($f=fopen($controller_file, 'w')) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Generando controlador: $Controller\r\n$controller_file\r\n";
						}
						
						$code="<?php\r\nclass {$Controller}Controller extends ApplicationController {";
						
						if(isset($params['controller_name'])) {
							$code.="\r\n\tpublic \$controller_name = '$params[controller_name]';";
						}

						if(isset($params['template'])) {
							$code.="\r\n\tpublic \$template = '$params[template]';";
						}

						$code.="\r\n}\r\n?>";
						
						fwrite($f, $code);
						fclose($f);
						
						$dir = join_path('apps', $s->$app->views_dir, $params['module'], $controller);
						if(!is_dir($dir)) {
							if(defined('KUMBIA_TERMINAL')) {
								echo "\r\n-> Generando directorio de vistas:\r\n$dir\r\n";
							}
							mkpath($dir);
						}
						$success = true;
					}
				}
			}
		} else {
			if(defined('KUMBIA_TERMINAL')) {
				echo "\r\nError la aplicacion no existe\r\n";
			}
		}
		
		return $success;
	}

}

?>