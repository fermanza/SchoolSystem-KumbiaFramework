<?php
/**
 * Kumbia PHP Framework
 *
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbiaphp.com/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package  Exceptions
 * 
 * @author   Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @license  http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU/GPL
 * @version  SVN:$id
 * @see      Object
 */
/**
 * Clase principal de Implementacion de Excepciones
 *
 */
class KumbiaException extends Exception {
    /**
     * Codigo de error de la Excepcion
     */
    protected $error_code = 0;
    /**
     * Mostrar Trace o no
     *
     * @var boolean
     */
    protected $show_trace = true;
    /**
     * Constructor de la clase;
     *
     */
    public function __construct($message, $error_code = 0, $show_trace = true) {
        $this->show_trace = $show_trace;
        if (is_numeric($error_code)) {
            parent::__construct($message, $error_code);
        } else {
            $this->error_code = $error_code;
            parent::__construct($message, 0);
        }
    }
    /**
     * Genera la salida de la excepción
     *
     */
    public function show_message() {
        print "\n<div style='background: #FFFFFF; padding: 5px'>\n";
        Flash::error(__CLASS__ . ": $this->message ({$this->getCode() })<br>
		<span style='font-size:12px'>En el archivo <i>{$this->getFile() }</i> en la l&iacute;nea: <i>{$this->getLine() }</i><", true);
        if ($this->show_trace) {
            $config = Config::read("config.ini");
            $active_app = Kumbia::$active_app;
            if (isset($config->$active_app->debug) && $config->$active_app->debug) {
                $traceback = $this->getTrace();
                print "<div style='font-size:14px; margin: 10px; border:1px solid #333333; background: #676767; font-family: Lucida Console; margin:10px; color: white; padding: 3px' align='left'>
				<pre style='font-family: Lucida Console; margin:10px; color: white;'>";
                foreach($traceback as $trace) {
                    if (isset($trace['file'])) {
                        print $trace['file'] . "(" . $trace['line'] . ")\n";
                        if (strpos($trace['file'], "apps")) {
                            $file = $trace['file'];
                            $line = $trace['line'];
                            print "</pre><strong>La excepci&oacute;n se ha generado en el archivo '$file' en la l&iacute;nea '$line':</strong><br>";
                            print "<div style='color: #000000; margin: 10px; border: 1px solid #333333; background: #FFFFFF; padding:0px'><table cellspacing='0' cellpadding='0'>";
                            $lines = file($file);
                            $eline = $line;
                            for ($i = (($eline - 4) < 0 ? 0 : $eline - 4);$i <= ($eline + 2 > count($lines) - 1 ? count($lines) - 1 : $eline + 2);$i++) {
                                if ($i == $eline - 1) {
                                    print "<tr><td style='background:#c0c0c0; font-size:13px'><strong>" . ($i + 1) . ".</strong></td><td>&nbsp;<span style='background: #FFDDDD;font-family: Lucida Console;; font-size:13px;'><strong>" . htmlentities($lines[$i], ENT_NOQUOTES) . "</strong></span></td></tr>\n";
                                } else {
                                    print "<tr><td style='background:#c0c0c0; font-size:13px'><strong>" . ($i + 1) . ".</strong></td><td style='font-family: Lucida Console; font-size:13px;'>&nbsp;" . htmlentities($lines[$i], ENT_NOQUOTES) . "</td></tr>";
                                }
                            }
                            print "</table></div><pre style='font-family: Lucida Console; margin:10px; color: white;'>";
                        }
                    }
                }
                print "</div>";
                print "<div style='font-size:12px; margin: 0px 15px 0px 15px; padding: 5px; border:1px solid #333333; background: #f2f2f2;' align='left'>";
                print "<strong>Informaci&oacute;n Adicional:</strong><br>";
                print "<strong>Aplicaci&oacute;n actual:</strong> " . Kumbia::$active_app . "<br>";
                print "<strong>Entorno actual:</strong> " . $config->$active_app->mode . "<br>";
                $url = Router::get_application() . "/" . Router::get_controller() . "/" . Router::get_action();
                print "<strong>Ubicaci&oacute;n actual:</strong> " . $url . "<br>";
                print "<strong>Modelos Cargados:</strong> " . join(", ", array_keys(Kumbia::$models)) . "<br>";
                print "<strong>Modulos Cargados:</strong> " . join(", ", $_SESSION['KUMBIA_MODULES'][$_SESSION['KUMBIA_PATH']][$active_app]) . "<br>";
                print "<strong>Plugins Cargados:</strong> " . join(", ", $_SESSION['KUMBIA_PLUGINS_CLASSES'][$_SESSION['KUMBIA_PATH']][$active_app]) . "<br>";
                if (is_array($_SESSION['session_data'])) {
                    print "<strong>Datos de Session:</strong> " . join(", ", $_SESSION['session_data']) . "<br>";
                } else {
                    print "<strong>Datos de Session:</strong> " . print_r(unserialize($_SESSION['session_data']), 1) . "<br>";
                }
                print "</div>";
            } else {
                print "<pre style='font-family: Lucida Console; margin: 10px; border:1px solid #969696; background: #fafafa'>";
                print $this->getTraceAsString() . "\n";
                print "</pre>";
            }
        }
        print "</div>";
    }
}