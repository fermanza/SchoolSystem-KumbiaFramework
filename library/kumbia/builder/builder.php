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
 * @copyright Copyright (c) 2007-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * @see BuilderInterface
 */
require "library/kumbia/builder/interface.php";


/**
 * @see BuilderException
 */
require "library/kumbia/builder/exception.php";

/**
 * @see AppBuilder
 */
require "library/kumbia/builder/base_builders/app/app.php";

/**
 * @see ModelBuilder
 */
require "library/kumbia/builder/base_builders/model.php";

/**
 * @see ControllerBuilder
 */
require "library/kumbia/builder/base_builders/controller.php";

/**
 * @see HelperBuilder
 */
require "library/kumbia/builder/base_builders/helper.php";

/**
 * Implementaci&oacute;n de Constructores para Kumbia
 *
 * @category Kumbia
 * @package Builder
 * @copyright Copyright (c) 2007-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */
class Builder {

	/**
	 * Ejecuta un builder
	 *
	 * @param string $builder nombre del builder (constructor) a ejecutar
	 * @param array $params array de parametros para el builder
	 * @return mixed
	 */
	public function build($builder, $params) {
		$success = false;
		
		if (!is_array($params)) {
			$params = array_slice(get_params(func_get_args()), 1);
		}
		
		if (!empty($builder)) {
			$b = ucfirst(camelize($builder)).'Builder';
			if (class_exists($b)) {
				$obj_builder = new $b();
				$success = $obj_builder->execute($params);
			}
		}
		
		return $success;
	}

}

?>