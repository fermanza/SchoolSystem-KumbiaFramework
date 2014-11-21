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
 * Destruye un helper
 *
 * @param array
 * @return boolean
 */
class HelperDestroyer implements DestroyerInterface {

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
		$s = Config::read('config.ini');
	
		$app = isset($params['app']) ? $params['app'] : $s->kumbia->default_app;
		
		if(isset($s->$app)) {
			for($i=0; isset($params[$i]); $i++) {
				$helper = $params[$i];
				$helper_file = join_path('apps', $s->$app->helpers_dir, "$helper.php");
				
				if(file_exists($helper_file)) {
					if(defined('KUMBIA_TERMINAL')) {
						echo "\r\n-> Destruyendo helper: $helper\r\n$helper_file\r\n";
					}
					$success = unlink($helper_file);
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