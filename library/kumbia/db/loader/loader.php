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
 * @subpackage Loader
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  Copyright (c) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see DbLoaderException
 */
require "library/kumbia/db/loader/exception.php";

/**
 * Clase encargada de cargar el adaptador de Kumbia
 *
 * @category Kumbia
 * @package Db
 * @subpackage Loader
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */
abstract class DbLoader extends Object {

	/**
	 * Carga un driver Kumbia segun lo especificado en
	 *
	 * @return boolean
	 */
	public static function load_driver(){
		$active_app = Kumbia::$active_app;

		/**
		 * Cargo el mode para mi aplicacion
		 **/
		$core = Config::read('config.ini');
		$mode = isset($core->$active_app->mode) ? $core->$active_app->mode : 'production';
		$environment = $core = Config::read('environment.ini');
		$config = $environment->$mode;

		if(isset($config->database->type)){
			if($config->database->type){
				$type = $config->database->type;
				if(isset($config->database->pdo)&&$config->database->pdo){
					/**
					 * @see DbPDO
					 */
					require_once "library/kumbia/db/adapters/pdo.php";

					require_once "library/kumbia/db/adapters/pdo/".$type.".php";

					eval("class Db extends DbPDO{$config->database->type} {}");
					return true;

				} else {

					require "library/kumbia/db/adapters/".$type.".php";
					if(!class_exists("Db{$config->database->type}")){
						throw new DbLoaderException("No existe la clase Db{$config->database->type}, necesaria para iniciar el adaptador", 0);
						return false;
					}
					eval("class Db extends Db{$config->database->type} {}");
					return true;

				}
			}
		} else {
			return true;
		}
		
	}

}

?>