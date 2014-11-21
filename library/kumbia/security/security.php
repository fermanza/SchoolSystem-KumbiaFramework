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
 * @package Security
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license    http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Clase que contiene metodos utiles para manejar seguridad
 *
 * @category Kumbia
 * @package Security
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @access public
 */

session_register("rsa_key");

abstract class Security extends Object {

	static public function generateRSAKey($kumbia){
		$h = date("G")>12 ? 1 : 0;
		$time = uniqid().mktime($h, 0, 0, date("m"), date("d"), date("Y"));
		$key = sha1($time);
		$_SESSION['rsa_key'] = $key;
		$xCode = "<input type='hidden' id='rsa32_key' value='$key' />\r\n";
		if($kumbia)
		formsPrint($xCode);
		else
		return $xCode;
		return "";
	}

	static public function createSecureRSAKey($kumbia=true){
		$config = Config::read('config.ini');
		if($config->kumbia->secure_ajax){
			if($_SESSION['rsa_key']){
				if((time()%8)==0){
					return generateRSAKey($kumbia);
				} else {
					if($kumbia)
					formsPrint("<input type='hidden' id='rsa32_key' value=\"{$_SESSION['rsa_key']}\"/> \r\n");
					else
					print "<input type='hidden' id='rsa32_key' value=\"{$_SESSION['rsa_key']}\"/> \r\n";
				}
			} else {
				return generateRSAKey($kumbia);
			}
		}
		return null;
	}

}

?>