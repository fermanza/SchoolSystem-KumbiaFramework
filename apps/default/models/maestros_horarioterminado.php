<?php
			
	class maestrosHorarioterminado extends ActiveRecord {
		
		function get_profesor_horario_status($nomina){
			$maestrosHorarioterminado = new maestrosHorarioterminado();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			// Regresa true si el horario está terminado
			if( $maestrosHorarioterminado -> find_first("nomina = ".$nomina." and periodo = ".$periodo) )
				return true;
			else
				return false;
		} // function get_profesor_horario_status($nomina)
	}
	
?>