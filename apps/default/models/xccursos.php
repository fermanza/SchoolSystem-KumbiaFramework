<?php
	class Xccursos extends ActiveRecord {
		
		function get_si_existe_materia_en_turno_vespertino($clavemat){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $this -> find_all_by_sql("
					select *
					from xccursos xcc
					inner join xchorascursos xch
					on xcc.id = xch.curso_id
					and xcc.periodo = '".$periodo."'
					and xcc.materia = '".$clavemat."'
					and xch.hora >= 15") as $xcc ){
				return true;
			}
			return false;
		} // function get_si_existe_materia_en_turno_vespertino($clavemat)
		
		function get_si_existe_materia_en_turno_matutino($clavemat){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $this -> find_all_by_sql("
					select *
					from xccursos xcc
					inner join xchorascursos xch
					on xcc.id = xch.curso_id
					and xcc.periodo = '".$periodo."'
					and xcc.materia = '".$clavemat."'
					and xch.hora <= 14") as $xcc ){
				return true;
			}
			return false;
		} // function get_si_existe_materia_en_turno_matutino($clavemat)
		
		function get_materias_en_turno_vespertino($clavemat){
			$materias = Array();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $this -> find_all_by_sql("
					select xcc.*
					from xccursos xcc
					inner join xchorascursos xch
					on xcc.id = xch.curso_id
					where xcc.materia = '".$clavemat."'
					and xcc.periodo = '".$periodo."'
					and xcc.activo = 1
					and xch.hora >= 15
					group by xcc.id") as $xcc ){
				array_push($materias, $xcc);
			}
			return $materias;
		} // function get_materias_en_turno_vespertino($clavemat)
		
		function get_materias_en_turno_matutino($clavemat){
			$materias = Array();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			foreach( $this -> find_all_by_sql("
					select xcc.*
					from xccursos xcc
					inner join xchorascursos xch
					on xcc.id = xch.curso_id
					where xcc.materia = '".$clavemat."'
					and xcc.periodo = '".$periodo."'
					and xcc.activo = 1
					and xch.hora <= 14
					group by xcc.id") as $xcc ){
				array_push($materias, $xcc);
			}
			return $materias;
		} // function get_materias_en_turno_matutino($clavemat)
		
		function get_relevant_info_from_xccursos($nomina){
			$xccursos = Array();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql( "
						Select ma.nomina, ma.nombre nombreProf, xcc.clavecurso, xcc.materia,
						m.nombre
						from maestros ma, x".$plantel."cursos xcc, materia m
						where ma.nomina = ".$nomina."
						and ma.nomina = xcc.nomina
						and xcc.periodo = ".$periodo."
						and xcc.materia = m.clave
						group by xcc.clavecurso" ) as $xcc ){
					array_push($xccursos, $xcc);
				}
			}
			return $xccursos;
		} // function get_relevant_info_from_xccursos($nomina)
		
		function get_relevant_info_from_xccursos_by_periodo($nomina, $periodo){
			$xccursos = Array();
			
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql( "
						Select ma.nomina, ma.nombre nombreProf, xcc.clavecurso, xcc.materia,
						m.nombre
						from maestros ma, x".$plantel."cursos xcc, materia m
						where ma.nomina = ".$nomina."
						and ma.nomina = xcc.nomina
						and xcc.periodo = ".$periodo."
						and xcc.materia = m.clave
						group by xcc.clavecurso" ) as $xcc ){
					array_push($xccursos, $xcc);
				}
			}
			return $xccursos;
		} // function get_relevant_info_from_xccursos_by_periodo($nomina, $periodo)
		
		function get_relevant_info_from_xccursos_by_curso_id($curso_id){
			foreach( $this -> find_all_by_sql( "
					Select ma.nomina, ma.nombre nombre_profesor, xcc.clavecurso, xcc.materia,
					m.nombre materia_nombre
					from maestros ma
					join xccursos xcc on xcc.nomina = ma.nomina
					join materia m on m.clave = xcc.materia
					and xcc.id = '".$curso_id."'
					group by xcc.id" ) as $xcc ){
				return $xcc;
			}
		} // function get_relevant_info_from_xccursos_by_curso_id($curso_id)
		
	}
?>
