<?php		
	class AlumnosProblemas extends ActiveRecord{
		
		function get_alumnos_con_problemas(){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$AlumnosConProblemas = Array();
			foreach( $this -> find(
					"where periodo = ".$periodo." and aun_con_problemas = 1") as $resulset){
				array_push($AlumnosConProblemas, $resulset);
			}
			return $AlumnosConProblemas;
		} // function get_alumnos_con_problemas()
		
		function get_alumnos_con_problemas_resueltos(){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$AlumnosConProblemas = Array();
			foreach( $this -> find(
					"where periodo = ".$periodo." and aun_con_problemas = 0") as $resulset){
				array_push($AlumnosConProblemas, $resulset);
			}
			return $AlumnosConProblemas;
		} // function get_alumnos_con_problemas_resueltos()
		
		function get_todos_alumnos(){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$AlumnosConProblemas = Array();
			foreach( $this -> find_all_by_sql("
					select ap.*, al.vcNomAlu
					from alumnos_problemas ap
					inner join alumnos al
					on ap.registro = al.miReg
					where ap.periodo = ".$periodo."") as $resulset){
				array_push($AlumnosConProblemas, $resulset);
			}
			return $AlumnosConProblemas;
		} // function get_todos_alumnos()
		
		function get_info_problema($id_problema){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			foreach( $this -> find_all_by_sql("
					select ap.*, al.vcNomAlu
					from alumnos_problemas ap
					inner join alumnos al
					on ap.registro = al.miReg
					where ap.periodo = ".$periodo."
					and ap.id = ".$id_problema) as $resulset){
				$alumno = $resulset;
			}
			return $alumno;
		} // get_info_problema($id_problema)
	}
?>