<?php
			
	class Xpkardex extends ActiveRecord {
		
		function get_all_xpkardex_from_student($alumno){
			$xpkardexx = array();
			foreach( $this -> find_all_by_sql(
					"select xpk.id, xpk.materia, m.nombre, xpk.periodo, xpk.tipo, xpk.promedio
					from xpkardex xpk
					inner join materia m
					on xpk.materia = m.clave
					and xpk.registro = ".$alumno -> miReg."
					and m.carrera_id = ".$alumno -> carrera_id."
					and xpk.id > 27315") as $xpk ){
				array_push($xpkardexx, $xpk);
			}
			return $xpkardexx;
		} // function get_all_xpkardex_from_student($alumno)
		
		function get_all_xpkardex_from_student_only_clave($materiasEnKardex, $registro){
			foreach( $this -> find_all_by_sql(
					"select materia as clave
					from xpkardex
					where registro = ".$registro."
					and id > 27315") as $xpk ){
				array_push($materiasEnKardex, $xpk -> clave);
			}
			return $materiasEnKardex;
		} // function get_all_xpkardex_from_student_only_clave($materiasEnKardex, $registro)
		
		function get_all_xpkardex_from_student_only_clave_($registro){
			$materiasEnXpkardex = array();
			foreach( $this -> find_all_by_sql(
					"select materia as clave
					from xpkardex
					where registro = ".$registro."
					and id > 27315") as $xpk ){
				array_push($materiasEnXpkardex, $xpk -> clave);
			}
			return $materiasEnXpkardex;
		} // function get_all_xpkardex_from_student_only_clave_($registro)
	}
?>
