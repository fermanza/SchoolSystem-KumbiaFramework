<?php		
	class LogKardexIng extends ActiveRecord{
		
		function get_ncreditos($alumno){
			foreach( $this -> find_all_by_sql("
					select sum(m.creditos) as sumadecreditos
					from kardex_ing k
					inner join materia m on k.clavemat = m.clave
					and k.registro = ".$alumno -> miReg."
					and m.carrera_id = ".$alumno -> carrera_id."
					and (m.serie = '".$alumno -> areadeformacion."'
					or m.serie = '-')") as $king ){
				$ncreditos = $king -> sumadecreditos;
			}
			return $ncreditos;
		} // function get_ncreditos($alumno)
	}
?>