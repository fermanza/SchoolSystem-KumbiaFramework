<?php
			
	function fecha(){
		$d = date("d",time());	
		$m = date("m",time());
		$y = date("Y",time());
		
		$n = date("N",time());
	
		switch($n){
			case 1: $fecha = "Lunes"; break;
			case 2: $fecha = "Martes"; break;
			case 3: $fecha = "Miercoles"; break;
			case 4: $fecha = "Jueves"; break;
			case 5: $fecha = "Viernes"; break;
			case 6: $fecha = "Sábado"; break;
			case 7: $fecha = "Domingo"; break;
		}
		
		$fecha .= ", ".$d." de ";
		
		switch($m){
			case 1: $fecha .= "Enero"; break;
			case 2: $fecha .= "Febrero"; break;
			case 3: $fecha .= "Marzo"; break;
			case 4: $fecha .= "Abril"; break;
			case 5: $fecha .= "Mayo"; break;
			case 6: $fecha .= "Junio"; break;
			case 7: $fecha .= "Julio"; break;
			case 8: $fecha .= "Agosto"; break;
			case 9: $fecha .= "Septiembre"; break;
			case 10: $fecha .= "Octubre"; break;
			case 11: $fecha .= "Noviembre"; break;
			case 12: $fecha .= "Diciembre"; break;
		}
		
		$fecha .= " de ".$y;
		
		return $fecha;
	}
	
	function hora(){
		//$hora = date("(H:i)",time());
		$hora = date("(H:i)");
		return $hora;
	}
	
?>
