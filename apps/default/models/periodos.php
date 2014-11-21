<?php

class Periodos extends ActiveRecord {

    function get_periodo_antesanterior() {
		return 12012;
    } // function get_periodo_antesanterior()

    function get_periodo_anterior() {
		return 32012;
    } // function get_periodo_anterior()

    function get_periodo_actual_() {
		return 12013;
    } // function get_periodo_actual_()

    function get_periodo_actual() {
		return 12013;
    } // function get_periodo_actual()

    function get_periodo_proximo() {
        return 12013;
    } // function get_periodo_proximo()

    function get_periodo_actual_intersemestral() {
        //return 42011; // Enero del 2012
        //return 22012; // Junio del 2012
		return 42012; // Enero del 2013
    } // function get_periodo_actual_intersemestral()

    function get_periodo_proximo_intersemestral() {
		//return 22012; // Julio del 2012
        //return 42012; // Enero del 2013
		return 22013; // Julio del 2013
    } // function get_periodo_proximo_intersemestral()

    function get_todos_periodos_desde_xccursos() {
        $periodos = Array();
		array_push($periodos, 12013);
		array_push($periodos, 32012);
        array_push($periodos, 12012);
        array_push($periodos, 32011);
        array_push($periodos, 12011);
        array_push($periodos, 32010);
        array_push($periodos, 12010);
        array_push($periodos, 32009);
        array_push($periodos, 12009);
        array_push($periodos, 32008);

        return $periodos;
    } // function get_todos_periodos_desde_xccursos()

    function get_datetime() {
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        $hour = date("H");
        $minute = date("i");
        $second = date("s");

        return $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second;
    } // function get_datetime()

    function get_datetime_cero() {
        return "0000-00-00 00:00:00";
    } // function get_datetime_cero()

    function get_unixtimestamp() {
        $day = date("d");
        $month = date("m");
        $year = date("Y");
        $hour = date("H");
        $minute = date("i");
        $second = date("s");

        return mktime($hour, $minute, $second, $month, $day, $year);
    } // function get_unixtimestamp()

    function convertirPeriodo() {
        $anio = substr($this->get_periodo_actual(), 1);
        $numInicial = (String) $this->get_periodo_actual();

        if ($numInicial[0] == 1) {
            return "Febrero - Junio " . $anio;
        } else if ($numInicial[0] == 2) {
            return "Julio " . $anio;
        } else if ($numInicial[0] == 3) {
            return "Agosto - Diciembre " . $anio;
        } else if ($numInicial[0] == 4) {
            return "Enero " . ($anio + 1);
        }
        return "";
    } // function convertirPeriodo()

    function convertirPeriodo_($periodo) {
        $anio = substr($periodo, 1);
        $numInicial = (String) $periodo;

        if ($numInicial[0] == 1) {
            return "Febrero - Junio " . $anio;
        } else if ($numInicial[0] == 2) {
            return "Julio " . $anio;
        } else if ($numInicial[0] == 3) {
            return "Agosto - Diciembre " . $anio;
        } else if ($numInicial[0] == 4) {
            return "Enero " . ($anio + 1);
        }
        return "";
    } // function convertirPeriodo_($periodo)

    function convertirPeriodo_a_intersemestral($periodo) {
        $anio = substr($periodo, 1);
        $numInicial = (String) $periodo;

        if ($numInicial[0] == 1) {
            return '2' . $anio;
        } else if ($numInicial[0] == 3) {
            return '4' . $anio;
        }
    } // function convertirPeriodo_a_intersemestral($periodo)
	
    function convertirPeriodo_a_intersemestral_letra($periodo) {
        $anio = substr($periodo, 1);
        $numInicial = (String) $periodo;

        if ($numInicial[0] == 1) {
            return 'Julio ' . $anio;
        } else if ($numInicial[0] == 3) {
            return 'Enero ' . $anio;
        }
    } // function convertirPeriodo_a_intersemestral_letra($periodo)
	
	function get_periodo_calculable($periodo){
		// Regresa el periodo de forma calculable.
		// 32012 -> 20123
		$periodo_ = substr($periodo,1,4).substr($periodo,0,1);
		return $periodo_;
	} // function get_periodo_calculable($periodo)
	
	function fecha_dia_mes_anio($inputField){
		return split("-",$inputField);
	} // function fecha_dia_mes_anio($inputField)
	
	/*
	 * Funcion que sirve para obtener un periodo anterior al activo 
	*/
	function periodoAnterior(){
	  
	   $periodoActivo = Session::get_data('periodo');
	   
	   if(substr($periodoActivo,0,1) == 3)
		 $result = "1".substr($periodoActivo,1,4);
		 
	   else if(substr($periodoActivo,0,1) == 1){
		 $pastPeriodo = substr(Session::get_data('periodo'),1,4) - 1;
		 
		 $result = "3".$pastPeriodo;
	   
	   }
	   
	   return $result;
	}
	
		/*
		 * Funcion que sirve para obtener el nombre del periodo solicitado
		*/
		static public function nombre_periodo($periodoSol){
		
		  $periodo = new Periodos();
		  
		  $periodoNombre = $periodo -> find_first("periodo = ".$periodoSol);
		  $result = strtoupper($periodoNombre -> nombre_corto);

		  return $result;		  
		}
}

?>