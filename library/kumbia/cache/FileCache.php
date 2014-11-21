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
 * @category  Kumbia
 * @package   Cache
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2007-2007 Roger Jose Padilla Camacho(rogerjose81 at gmail.com)
 * @copyright Copyright (C) 2007-2009 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @license   http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Clase que implementa un componente de cacheo
 * 
 * @category  Kumbia
 * @package   Cache
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2007-2007 Roger Jose Padilla Camacho(rogerjose81 at gmail.com)
 * @copyright Copyright (C) 2007-2009 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @license   http://www.kumbia.org/license.txt GNU/GPL
 */
class FileCache extends Object
{
    /**
     * Obtiene el nombre de archivo a partir de un id y grupo
     *
     * @param string $id
     * @param string $group
     * @return string
     **/
    protected static function _getFilename($id, $group)
    {
        return 'cache_'.md5($id).'.'.md5($group);
    }
	/**
	 * Carga un elemento cacheado
	 *
	 * @param string $id
	 * @param string $group
	 * @param int $lifetime tiempo de vida en forma timestamp de unix
	 * @return mixed
	 */
	public static function get($id, $group, $lifetime) 
    {
        $file = 'temp/cache/'.self::_getFilename($id, $group);
		if(file_exists($file)){
			if($lifetime){
                $expire_time = $lifetime - time();
				if($expire_time + filemtime($file) < time()){
					return null;
				}
			}
			return file_get_contents($file);
		} else {
			return null;
		}
    }
	/**
	 * Guarda un elemento en la cache con nombre $id y valor $value
	 *
	 * @param string $id
	 * @param string $group
	 * @param mixed $value
	 * @return boolean
	 */
	public static function save($id, $group, $value)
    {
        return @file_put_contents('temp/cache/'.self::_getFilename($id, $group), $value);
    }
	/**
	 * Limpia la cache
	 *
	 * @param string $group
	 * @return boolean
	 */
	public static function clean($group=false)
    {
        $pattern = $group ? 'temp/cache/*.'.md5($group) : 'temp/cache/*';
        foreach (glob($pattern) as $filename) {
            if(!@unlink($filename)) {
                return false;
            }
        }
    }
	/**
	 * Elimina un elemento de la cache
	 *
	 * @param string $id
	 * @param string $group
	 * @return string
	 */
	public static function remove($id, $group)
    {
        return @unlink('temp/cache/'.self::_getFilename($id, $group));
    }
}