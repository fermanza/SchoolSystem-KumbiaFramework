<?php

/** Kumbia - PHP Rapid Development Framework *****************************
*
* Copyright (C) 2005-2007 Andrés Felipe Gutiérrez (andresfelipe at vagoogle.net)
* Copyright (C) 2007-2007 Emilio Rafael Silveira Tovar (emilio.rst at gmail.com)
*
* This framework is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This framework is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this framework; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
* Este framework es software libre; puedes redistribuirlo y/o modificarlo
* bajo los terminos de la licencia pública general GNU tal y como fue publicada
* por la Fundación del Software Libre; desde la versión 2.1 o cualquier
* versión superior.
*
* Este framework es distribuido con la esperanza de ser util pero SIN NINGUN
* TIPO DE GARANTIA; sin dejar atrás su LADO MERCANTIL o PARA FAVORECER ALGUN
* FIN EN PARTICULAR. Lee la licencia publica general para más detalles.
*
* Debes recibir una copia de la Licencia Pública General GNU junto con este
* framework, si no es asi, escribe a Fundación del Software Libre Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*****************************************************************************/

/**
	al estar definida, determina que se esta ejecutando desde el terminal
**/
define('KUMBIA_TERMINAL', true);

require_once "library/kumbia/kumbia.php";
require_once 'library/kumbia/exception.php';
require_once 'library/kumbia/builder/builder.php';
require_once 'library/kumbia/destroyer/destroyer.php';
require_once 'library/kumbia/helpers/helpers.php';
require_once "library/kumbia/config/config.php";
require_once "library/kumbia/plugin/plugin.php";
require_once "library/kumbia/router/router.php";

/**
* Obtiene un arreglo con los parametros pasados por terminal
*
* @param string $params arreglo de parametros con nombres con el formato de terminal
* @return array
*/
function get_params_from_term($params){
	$data = array();
	$i = 0;
	foreach ($params as $p) {
		if(is_string($p) && preg_match("/--([a-z_0-9]+)[=](.+)/", $p, $regs)){
			$data[$regs[1]] = $regs[2];
		} else $data[$i] = $p;
		$i++;
	}
	return $data;
}

?>