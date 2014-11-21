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
 * @see CacheException
 */
include 'library/kumbia/cache/exception.php';

/**
 * Clase que implementa un componente de cacheo (En Desarrollo)
 *
 * @category  Kumbia
 * @package   Cache
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2007-2009 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @license   http://www.kumbia.org/license.txt GNU/GPL
 * @access    public
 */
class Cache extends Object 
{
    /**
    * Adaptador par cacheo por archivo
    *
    **/
    const FILE = 'file';
	/**
	 * Id de ultimo elemento solicitado
	 *
	 * @var string
	 */
	protected static $_id = null;
	/**
	 * Grupo de ultimo elemento solicitado
	 *
	 * @var string
	 */
	protected static $_group = 'default';
	/**
	 * Carga un elemento cacheado
	 *
	 * @param string $id
	 * @param string $lifetime tiempo de vida con formato strtotime
	 * @param string $group
	 * @return mixed
     * @throw CacheException
	 */
	public static function get($id, $lifetime=null, $group='default') 
    {
        self::$_id = $id;
        self::$_group = $group;
        
        if(is_string($lifetime)) {
            $lifetime = strtotime($lifetime);
            if($lifetime!=-1) {
                return unserialize(call_user_func(array(self::getDriver(), 'get'), $id, $group, $lifetime));
            } else {
                throw new CacheException('El formato indicado para lifetime en la cache es erroneo, siga el formato proporcionado por la función strtotime');
            }
        } else {
            $value = call_user_func(array(self::getDriver(), 'get'), $id, $group, $lifetime);
            if($value) {
                $value = unserialize($value);
            }
            return $value;
        }
    }
	/**
	 * Guarda un elemento en la cache con nombre $id y valor $value
	 *
	 * @param mixed $value
	 * @param string $id
	 * @param string $group
	 * @return boolean
	 */
	public static function save($value, $id=false, $group='default')
    {
        /**
         * Verifica si se ha pasado un id
         **/
        if(!$id) {
            $id = self::$_id;
            $group = self::$_group;
        }
        return call_user_func(array(self::getDriver(), 'save'), $id, $group, serialize($value));
    }
	/**
	 * Inicia el cacheo del buffer de salida hasta que se llame a end
	 *
	 * @param string $id
	 * @param string $group
	 */
	public static function start($id=false, $group='default')
    {
        /**
         * Verifica si se ha pasado un id
         **/
        if($id) {
            self::$_id = $id;
            self::$_group = $group;
        }
        ob_start();
    }
	/**
	 * Termina el buffer de salida
	 *
	 * @return boolean
	 */
	public static function end()
    {
        $value = ob_get_contents();
        ob_end_flush();
        return self::save($value);
    }
	/**
	 * Limpia la cache
	 *
	 * @param string $group
	 * @return boolean
	 */
	public static function clean($group=false)
    {
        return call_user_func(array(self::getDriver(), 'clean'), $group);
    }
	/**
	 * Elimina un elemento de la cache
	 *
	 * @param string $id
	 * @param string $group
	 * @return boolean
	 */
	public static function remove($id, $group='default')
    {
        return call_user_func(array(self::getDriver(), 'remove'), $id, $group);
    }
    /**
     * Obtiene el driver para cache
     *
     * @return string
     **/
    public static function getDriver()
    {
        return 'FileCache';
    }
}
