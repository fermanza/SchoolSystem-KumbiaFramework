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
 * @package Config
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see ConfigException
 */
require_once "library/kumbia/config/exception.php";

/**
 * Clase para la carga de Archivos .INI y de configuración
 *
 * Aplica el patrón Singleton que utiliza un array
 * indexado por el nombre del archivo para evitar que
 * un .ini de configuración sea leido más de una
 * vez en runtime con lo que aumentamos la velocidad.
 *
 * @category Kumbia
 * @package Config
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @access public
 */
class Config extends Object {

	/**
	 * Contenido cacheado de los diferentes archivos leidos
	 *
	 * @var array
	 */
	static private $instance = array();

	/**
	 * El constructor privado impide q la clase sea
	 * instanciada y obliga a usar el metodo read
	 * para obtener la instancia del objeto
	 *
	 */
	private function __construct(){

	}

	/**
	 * Constructor de la Clase Config
	 *
	 * @return Config
	 */
	static public function read($file="environment.ini"){

		if(isset(self::$instance[$file])){
			return self::$instance[$file];
		}

		$config = new Config();

		if(!file_exists("config/$file")){
			throw new ConfigException("No existe el archivo de configuraci&oacute;n $file");
		}

		foreach(parse_ini_file('config/'.$file, true) as $conf => $value){			
			$config->$conf = new stdClass();
			
			/**
			 * Asigno los valores
			 **/
			foreach($value as $cf => $val){
				$subsections = explode('.', $cf);
				$attr = $subsections[count($subsections)-1];
				unset($subsections[count($subsections)-1]);
				
				/**
				 * Instancio las subsecciones
				 **/
				$section = $config->$conf;
				foreach($subsections as $key) {
					if(!isset($section->$key)) {
						$section->$key = new stdClass();
					}
					$section = $section->$key;
				}
			
				$section->$attr = $val;
			}
			
		}

		self::$instance[$file] = $config;
		return $config;
	}

	/**
	 * Escribir archivo de configuracion
	 *
	 * @param mixed $file string de ruta al archivo ini o resource proveido por fopen
	 * @param mixed $sections objeto o array de secciones
	 * @return mixed
	 */
	static public function write($file, $sections){
		$success = false;
		
		if(is_string($file)) {
			$f = fopen("config/$file", 'w');
		} else {
			$f = $file;
		}
		
		if($f) {
			if(is_array($sections)) {
				foreach($sections as $s => $values) {
					fwrite($f,"[$s]\r\n");
					foreach($values as $key => $value) {
						fwrite($f,"$key = $value\r\n");
					}
					fwrite($f, "\r\n");
				}
			}elseif(is_object($sections)) {
				foreach(get_object_vars($sections) as $s => $object_values) {
					fwrite($f,"[$s]\r\n");
					foreach(get_object_vars($object_values) as $key => $value) {
						fwrite($f,"$key = $value\r\n");
					}
					fwrite($f, "\r\n");
				}
			}
			
			if(is_string($file)) {
				fclose($f);
			}
			$success = true;
		}
		
		return $success;
	}
	
}

?>
