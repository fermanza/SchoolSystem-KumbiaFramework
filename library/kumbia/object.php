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
 * @package Kumbia
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  Copyright (c) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Clase raiz para todas las clases del framework
 *
 * @category   Kumbia
 * @package Kumbia
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright  Copyright (c) 2008-2008 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 * @abstract
 */
abstract class Object 
{
	/**
	 * Implementacion del Metodo toString
	 * @return string
	 */
	public function __toString()
	{
		return print_r($this, true);
	}
	/**
	 * Compara si es igual
	 * @param Object $s objeto a comparar
	 * @return boolean
	 **/
	public function equals($s) 
	{
		return $this == $s;
	}
	/**
	 * Serializa el objeto
	 * @return string
	 **/
	public function serialize() 
	{
		return serialize($this);
	}
	/**
	 * Convierte el objeto en codificacion JSON
	 * @return string
	 **/
	public function to_json() 
	{
		return json_encode($this);
	}
	/**
	 * Al igual que su homologo en Ruby, permite invocar un metodo del objeto
	 * @param string $m metodo a invocar
	 * @param array $args array de argumentos (opcional)
	 **/
	public function send($m, $args=array()) 
	{
		return call_user_func_array(array($this, $m), $args);
	}
}

?>