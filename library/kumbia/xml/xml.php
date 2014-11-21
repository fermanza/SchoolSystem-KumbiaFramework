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
 * @package XML
 * @copyright  Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Clase que contiene metodos utiles para manejar seguridad
 *
 * @category Kumbia
 * @package XML
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 * @access public
 */

class SimpleXMLResponse extends Object {

	private $code;

	function __construct(){
		$config = Config::read("config.ini");
		$active_app = Kumbia::$active_app;
		$this->code.="<?xml version='1.0' encoding='{$config->$active_app->charset}'?>\r\n<response>\r\n";
	}

	function add_node($arr){
		$this->code.="\t<row ";
		foreach($arr as $k => $v){
			$this->code.="$k='".$v."' ";
		}
		$this->code.="/>\r\n";

	}

	function add_data($val){
		$this->code.="\t<data><![CDATA[$val]]></data>\n";
	}

	function out_response(){
		$this->code.="</response>";
		header('Content-Type: text/xml');
		header("Pragma: no-cache");
		header("Expires: 0");
		print $this->code;
	}
}

?>