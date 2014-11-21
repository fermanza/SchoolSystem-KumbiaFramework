<?php
/**
 * Kumbia PHP Framework
 *
 * @category  Kumbia
 * @package   Benchmark
 *
 * @author    Deivinson Jose Tejeda <deivinsontejeda@gmail.com>
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito <deivinsontejeda at gmail.com>
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU/GPL
 * @version   SVN:$id
 */
/**
 * Class Para el manejo de Benchmark y Profiling de un script
 * esto nos permite obtener tiempo de ejecucion con el fin de
 * encontrar posibles cuellos de botellas y optimizar el rendimiento
 * de la aplicacion...
 *
 */
/**
 * @see Exception
 */
require_once 'exception.php';
final class Benchmark 
{
    /**
     * Almacena los datos de un Benchmark especifico, esto para evitar colision
     *
     * @var name
     */
    private static $_benchmark;
    
    private static $_avgload = 0;

    /**
     * Inicia el reloj (profiling)
     *
     * @return array $_benchmark
     */
    public static function start_clock($name) {
        if (!isset(self::$_benchmark[$name])) {
            self::$_benchmark[$name] = array('start_time' => microtime(), 'final_time' => 0, 'memory_start' => memory_get_usage(), 'memory_stop' => 0, 'time_execution' => 0,);
        }
    }

    /**
     * Detiene el reloj para efecto del calculo del
     * tiempo de ejecucion de un script
     *
     * @return array $_benchmark
     */
    private static function _stop_clock($name) {
        if (isset(self::$_benchmark[$name])) {
	    if(PHP_OS=="Linux"){
            	$load = sys_getloadavg();
	    } else {
		$load = 0;
	    }		
            self::$_avgload = $load[0];
            self::$_benchmark[$name]['memory_stop'] = memory_get_usage();
            self::$_benchmark[$name]['final_time'] = microtime();
            list($sm, $ss) = explode(' ', self::$_benchmark[$name]['start_time']);
            list($em, $es) = explode(' ', self::$_benchmark[$name]['final_time']);
            self::$_benchmark[$name]['time_execution'] = number_format(($em + $es) - ($sm + $ss), 4);
            return self::$_benchmark[$name]['time_execution'];
        }
    }

    /**
     * Permite obtener la memoria usada por un script
     *
     * @return string memory_usage
     */
    public static function memory_usage($name) {
        if (self::$_benchmark[$name]) {
            self::$_benchmark[$name]['memory_usage'] = number_format((self::$_benchmark[$name]['memory_stop'] - self::$_benchmark[$name]['memory_start']) / 1048576, 2);
            return self::$_benchmark[$name]['memory_usage'];
        } else {
            throw new kumbiaException("No existe el Benchmark para el nombre: '$name', especificado \n");
        }
    }

    /**
     * Retorna el tiempo de ejecucion del scripts (profiling)
     * 
     * @return string time_execution
     */
    public static function time_execution($name) {
        if (isset(self::$_benchmark[$name])) {
            return self::_stop_clock($name);
        } else {
            throw new kumbiaException("No existe el Benchmark para el nombre: $name, especificado \n");
        }
    }
}

?>
