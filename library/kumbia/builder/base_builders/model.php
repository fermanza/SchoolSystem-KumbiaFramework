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
 * Construye un modelo
 *
 * @param array
 * @return boolean
 */
class ModelBuilder implements BuilderInterface {

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
				$model = uncamelize(lcfirst($params[$i]));
				$Model = ucfirst(camelize($model));

				$dir = join_path('apps', $s->$app->models_dir, $params['module']);
				if(!is_dir($dir)) {
					mkdir($dir);
				}
				$model_file = join_path($dir, "$model.php");
				
				
				if(!file_exists($model_file)) {
					if($f=fopen($model_file, 'w')) {
						if(defined('KUMBIA_TERMINAL')) {
							echo "\r\n-> Generando modelo: $Model\r\n$model_file\r\n";
						}
						
						$code="<?php\r\nclass $Model extends ActiveRecord {";
						
						if(isset($params['source'])) {
							$code.="\r\n\tprotected \$source = '$params[source]';";
						}
						
						if(isset($params['schema'])) {
							$code.="\r\n\tprotected \$schema = '$params[schema]';";
						}

						if(isset($params['is_view'])) {
							$code.="\r\n\tpublic \$is_view = $params[is_view];";
						}

						$code.="\r\n}";
						$code.="\r\n?>";
						
						fwrite($f, $code);
						fclose($f);
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