<?php
			
	class actividadMaestro extends ActiveRecord {
		
		// Regresa la lista de actividades complementarias que el maestro ha escogido para este periodo
		//para la parte del 60%
		function get_lista_actividad_maestro($nomina){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select l.actividad as actividad, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades l
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.actividad_maestro_lista_actividades = l.clave
					and am.tipoActividad = 1
					limit 1" ) as $actividad ){
				$array_ActividadMaestro = $actividad;
			}
			return $array_ActividadMaestro;
		} // function get_lista_actividad_maestro($nomina)
		
		// Regresa la actividade que el usuario quiere editar
		function get_actividad_maestro($nomina, $editID){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select l.actividad as actividad, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades l
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.actividad_maestro_lista_actividades = l.clave
					and id = ".$editID ) as $actividad ){
				$array_ActividadMaestro = $actividad;
			}
			return $array_ActividadMaestro;
		} // function get_actividad_maestro($nomina, $editID)
		
		// Regresa la lista de actividades complementarias que el maestro ha escogido para este periodo
		//para la parte del 40%
		function get_lista_actividad_maestro_40Porciento($nomina){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select l.actividad as actividad, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades l
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.actividad_maestro_lista_actividades = l.clave
					and am.tipoActividad = 2
					limit 1" ) as $actividad ){
				$array_ActividadMaestro = $actividad;
			}
			return $array_ActividadMaestro;
		} // function get_lista_actividad_maestro_40Porciento($nomina)
		
		// Regresa las horas clase que esta impartiendo en dicho periodo, tanto en plantel colomos como en tonalá
		function get_horasclase($nomina){
			$xchorascursos = new Xchorascursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$count_hours = 0;
			foreach( $xchorascursos -> find_all_by_sql(
					"select count(*) horas
					from xchorascursos xch
					inner join xccursos xcc
					on xcc.id = xch.curso_id
					and xch.periodo = ".$periodo."
					and xcc.nomina = ".$nomina."
					group by dia, hora" ) as $horas ){
				$count_hours += 1;
			}
			
			foreach( $xchorascursos -> find_all_by_sql(
					"select count(*) horas
					from xthorascursos xch
					inner join xtcursos xcc
					on xcc.id = xch.curso_id
					and xch.periodo = ".$periodo."
					and xcc.nomina = ".$nomina."
					group by dia, hora" ) as $horas ){
				$count_hours += 1;
			}
			return $count_hours;
		} // function get_horasclase($nomina)
		
		// Regresa información importante del maestro
		function get_info_maestro($nomina){
			$maestros = new Maestros();
			
			foreach( $maestros -> find_all_by_sql(
					"select nombre, nombramiento, nombramiento_horas, coordinacion
					from maestros
					where nomina = ".$nomina) as $maestro ){
				$infoMaestro = $maestro;
			}
			return $infoMaestro;
		} // function get_info_maestro($nomina)
		
		// Regresa la lista de actividades complementarias que el maestro por asignatura
		//ha escogido.
		function get_lista_actividad_maestro_asignatura($nomina){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$array_ActividadMaestro = array();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select l.actividad as actividad, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades l
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.actividad_maestro_lista_actividades = l.clave
					and am.tipoActividad = 2" ) as $actividad ){
				array_push($array_ActividadMaestro, $actividad);
			}
			return $array_ActividadMaestro;
		} // function get_lista_actividad_maestro_40Porciento($nomina)
		
		// Regresa la lista de actividades complementarias que el maestro 
		//ha escogido para poder imprimir sus actividades en PDF.
		function get_lista_actividad_maestro_actividadPDF($nomina){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$array_ActividadMaestro = array();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"Select l.actividad as actividad, l.objetivo objetivoo, l.meta metaa, l.evidencia evidenciaa, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades l
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.actividad_maestro_lista_actividades = l.clave
					order by am.id" ) as $actividad ){
				array_push($array_ActividadMaestro, $actividad);
			}
			return $array_ActividadMaestro;
		} // function get_lista_actividad_maestro_actividadPDF($nomina)
		
		// Regresar el objetivo, meta, evidencia de una actividad, proporcionando su clave
		function get_objetivo_meta_evidencia($clave, $nomina){
			$ActividadMaestro = new ActividadMaestro();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select objetivo, meta, evidencia
					from actividad_maestro
					where actividad_maestro_lista_actividades = '".$clave."'
					and nomina = ".$nomina."
					and periodo = ".$periodo ) as $actividad ){
				$array_ActividadMaestro = $actividad;
			}
			return $array_ActividadMaestro;
		} // function get_objetivo_meta_evidencia($clave, $nomina)
		
		// Regresar el objetivo, meta, evidencia de una actividad sustantiva, proporcionando su clave
		function get_objetivo_meta_evidencia_sustantiva($clave){
			$ActividadMaestro = new ActividadMaestro();
			foreach( $ActividadMaestro -> find_all_by_sql(
					"select objetivo, meta, evidencia
					from actividad_maestro_lista_actividades
					where clave = '".$clave."'" ) as $actividad ){
				$array_ActividadMaestro = $actividad;
			}
			return $array_ActividadMaestro;
		} // function get_objetivo_meta_evidencia_sustantiva($clave, $nomina)
		
	}
	
?>