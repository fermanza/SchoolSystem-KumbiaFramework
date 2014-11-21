<?php
			
	class Seleccionalumno extends ActiveRecord {
		
		function get_materias_seleccionAlumno_actual($periodo, $alumno){
			$SeleccionAlumno = new SeleccionAlumno();
			
			$materiasActualSelecc = array();
			foreach( $SeleccionAlumno -> find_all_by_sql(
					"select sa.id, m.clave, m.nombre, sa.periodo
					From seleccionalumno sa
					inner join materia m
					on sa.clavemateria = m.clave
					and sa.periodo = ".$periodo."
					and sa.registro = ".$alumno->miReg."
					and m.carrera_id = ".$alumno->carrera_id."
					and (m.serie = '".$alumno->areadeformacion_id."'
					or m.serie = '-')") as $selecc ){
				array_push($materiasActualSelecc, $selecc);
			}
			return $materiasActualSelecc;
		} // function get_materias_seleccionAlumno_actual($periodo, $alumno)
		
		function get_creditos_seleccionAlumno_actual($periodo, $alumno){
			$SeleccionAlumno = new SeleccionAlumno();
			
			foreach( $SeleccionAlumno -> find_all_by_sql(
					"select sum(creditos) as creditos
					From seleccionalumno sa
					inner join materia m
					on sa.clavemateria = m.clave
					and sa.periodo = ".$periodo."
					and sa.registro = ".$alumno->miReg."
					and m.carrera_id = ".$alumno->carrera_id."
					and (m.serie = '".$alumno->areadeformacion_id."'
					or m.serie = '-')" ) as $selecc ){
				$creditos = $selecc -> creditos;
			}
			return $creditos;
		} // function get_creditos_seleccionAlumno_actual($periodo, $alumno)
		
	}
	
?>
