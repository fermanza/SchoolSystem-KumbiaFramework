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
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see DestroyerInterface
 */
require "library/kumbia/destroyer/interface.php";


/**
 * @see DestroyerException
 */
require "library/kumbia/destroyer/exception.php";

/**
 * @see AppDestroyer
 */
require "library/kumbia/destroyer/base_destroyers/app.php";

/**
 * @see ModelDestroyer
 */
require "library/kumbia/destroyer/base_destroyers/model.php";

/**
 * @see ControllerDestroyer
 */
require "library/kumbia/destroyer/base_destroyers/controller.php";

/**
 * @see HelperDestroyer
 */
require "library/kumbia/destroyer/base_destroyers/helper.php";

/**
 * Implementaci&oacute;n de Destructores para Kumbia
 *
 * @category Kumbia
 * @package Destroyer
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */
class Destroyer {

	/**
	 * Ejecuta un destroyer
	 *
	 * @param string $destroyer nombre del destroyer (destructor) a ejecutar
	 * @param array $params array de parametros para el destructor
	 * @return mixed
	 */
	public function destroy($destroyer, $params) {
		$success = false;
		
		if (!is_array($params)) {
			$params = array_slice(get_params(func_get_args()), 1);
		}
		
		if (!empty($destroyer)) {
			$b = ucfirst(camelize($destroyer)).'Destroyer';
			if (class_exists($b)) {
				$obj_destroyer = new $b();
				$success = $obj_destroyer->execute($params);
			}
		}
		
		return $success;
	}

}

?>