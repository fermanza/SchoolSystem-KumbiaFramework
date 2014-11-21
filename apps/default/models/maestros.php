<?php
	class Maestros extends ActiveRecord {
		
		function get_nombre_maestro($nomina){
			foreach( $this -> find_all_by_sql("
					select nombre
					from maestros
					where nomina = '".$nomina."'") as $maestro ){
				return $nombre = $maestro -> nombre;
			}
		} // function get_nombre_maestro($nomina)
		
		function get_todos_los_maestros(){
			$maestros = Array();
			foreach( $this -> find_all_by_sql("
					select nombre, nomina, nombramiento, nombramiento_horas, coordinacion
					from maestros
					order by nomina" ) as $maestro ){
				array_push($maestros, $maestro);
			}
			return $maestros;
		} // function get_todos_los_maestros()
		
		function get_nomina_by_nombre($nombreProfesor){
			foreach( $this -> find_all_by_sql("
					select nomina from maestros
					where nombre like '%".$nombreProfesor."%'
					limit 1") as $profe){
				return $profe -> nomina;				
			}
		} // function get_nomina_by_nombre($nombreProfesor)
		
		function get_horas_totales_en_ambos_planteles($nomina){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$horasTotal = 0;
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql("
						select count(*) as horas
						from(
							select count(*)
							from x".$plantel."cursos xcc, x".$plantel."horascursos xch
							where xcc.periodo = ".$periodo."
							and xcc.nomina = ".$nomina."
							and xcc.id = xch.curso_id
							group by xch.dia, xch.hora
						)t1") as $horas ){
					$horasTotal += $horas -> horas;
				}
			}
			return $horasTotal;
		} // function get_horas_totales_en_ambos_planteles($nomina)
		
		function get_horas_totales_en_ambos_planteles_by_periodo($nomina, $periodo){
			$horasTotal = 0;
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql("
						select count(*) as horas
						from(
							select count(*)
							from x".$plantel."cursos xcc, x".$plantel."horascursos xch
							where xcc.periodo = ".$periodo."
							and xcc.nomina = ".$nomina."
							and xcc.id = xch.curso_id
							group by xch.dia, xch.hora
						)t1") as $horas ){
					$horasTotal += $horas -> horas;
				}
			}
			return $horasTotal;
		} // function get_horas_totales_en_ambos_planteles_by_periodo($nomina, $periodo)
		
		function get_cursos_horas_salones_ambos_planteles($nomina){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql("
						select xcc.clavecurso, xch.dia, xch.hora, xch.presencial,
						xch.x".$plantel."salones_id, xcs.id as salonid, m.nombre as nombremateria,
						xcs.edificio, xcs.nombre as nombresalon, xcc.materia
						from x".$plantel."cursos xcc, x".$plantel."horascursos xch, x".$plantel."salones xcs, materia m
						where xcc.periodo = ".$periodo."
						and xcc.nomina = ".$nomina."
						and xcc.id = xch.curso_id
						and xch.x".$plantel."salones_id = xcs.id
						and xcc.materia = m.clave
						group by xch.id") as $horas ){
					if( !isset($cursosHoras[$horas -> dia][$horas -> hora]) ){
						$cursosHoras[$horas -> dia][$horas -> hora] = $horas;
					}
					else{
						$cursosHoras[1][$horas -> dia][$horas -> hora] = $horas;
					}
				}
			}
			return $cursosHoras;
		} // function get_cursos_horas_salones_ambos_planteles($nomina)
		
		function get_all_info_cursos_ambos_planteles_by_periodo($nomina, $periodo){
			$Plantel = new Plantel();
			$array_plantel = $Plantel -> get_array_plantel();
			foreach( $array_plantel as $plantel ){
				foreach( $this -> find_all_by_sql("
						Select ma.nomina, ma.nombre nombreProf, xcc.clavecurso, xcc.materia,
						m.nombre, xch.dia, xch.hora, xcs.edificio, xcs.nombre nombreSalon
						from maestros ma, x".$plantel."cursos xcc, x".$plantel."horascursos xch, x".$plantel."salones xcs, materia m
						where ma.nomina = ".$nomina."
						and ma.nomina = xcc.nomina
						and xcc.periodo = ".$periodo."
						and xcc.id = xch.curso_id
						and xch.x".$plantel."salones_id = xcs.id
						and xcc.materia = m.clave
						group by xch.id") as $Horas ){
					$this -> horas++;
					if( isset($horarios[$Horas -> dia][$Horas -> hora]) )
						$horarios[1][$Horas -> dia][$Horas -> hora] = $Horas;
					else
						$horarios[$Horas -> dia][$Horas -> hora] = $Horas;
				}
			}
			return $horarios;
		} // function get_all_info_cursos_ambos_planteles_by_periodo($nomina, $periodo)
	}
?>