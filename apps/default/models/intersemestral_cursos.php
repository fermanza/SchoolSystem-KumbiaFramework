<?php
	class IntersemestralCursos extends ActiveRecord {
		
		function get_max_ID(){
			foreach( $this -> find_all_by_sql("
					select max(id) id from intersemestral_cursos") as $interCursos ){
				$maxID = $interCursos -> id;
			}
			return $maxID;
		} // function get_max_ID()
		
		function get_datos_cursos_by_claveANDperiodo($clavemat, $periodo){
			foreach( $this -> find_all_by_sql("
					select * from intersemestral_cursos
					where clavemat = '".$clavemat."'
					and periodo = '".$periodo."'") as $inter ){
				$info = $inter;
			}
			return $info;
		} // function get_datos_cursos_by_claveANDperiodo($clavemat, periodo)
		
		function get_nomina_nombre_de_coordinador($nomina){
			foreach( $this -> find_all_by_sql("
					select nombre, nomina
					from maestros
					where nomina = ".$nomina) as $inter ){
				$info = $inter;
			}
			return $info;
		} // function get_nomina_nombre_de_coordinador($nomina)
		
		function restar_disponibilidad($clavecurso){
			foreach( $this -> find_all_by_sql("
					update intersemestral_cursos
					set disponibilidad = (disponibilidad -1)
					where clavecurso ='".$clavecurso."'") as $resulset){
			}
		} // function restar_disponibilidad($clavecurso)
		
		function sumar_disponibilidad($clavecurso){
			foreach( $this -> find_all_by_sql("
					update intersemestral_cursos
					set disponibilidad = (disponibilidad +1)
					where clavecurso ='".$clavecurso."'") as $resulset){
			}
		} // function sumar_disponibilidad($clavecurso)
		
		function desinscribir_alumno($inter_al_id){
			foreach( $this -> find_all_by_sql("
					delete from intersemestral_alumnos
					where id = ".$inter_al_id) as $resulset){
			}
		} // function desinscribir_alumno($inter_al_id)
		
		function get_todos_cursos_periodo_actual(){
			$cursos = Array();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			foreach( $this -> find_all_by_sql("
					select icursos.clavecurso, mat.clave, mat.nombre nombre_materia,
					maes.nomina, maes.nombre nombre_maestro, icursos.cupo,
					icursos.disponibilidad, icursos.creado_at
					from intersemestral_cursos icursos
					inner join maestros maes
					on icursos.nomina = maes.nomina
					inner join materia mat
					on icursos.clavemat = mat.clave
					where periodo = '".$periodo."'
					group by icursos.id") as $resulset){
				array_push($cursos, $resulset);
			}
			return $cursos;
		} // function get_todos_cursos_periodo_actual()
		
		function get_curso_by_clavecurso($clavecuro){
			foreach( $this -> find_all_by_sql("
					select icursos.clavecurso, mat.clave, mat.nombre nombre_materia,
					maes.nomina, maes.nombre nombre_maestro, icursos.cupo,
					icursos.disponibilidad, icursos.creado_at
					from intersemestral_cursos icursos
					inner join maestros maes
					on icursos.nomina = maes.nomina
					inner join materia mat
					on icursos.clavemat = mat.clave
					where icursos.clavecurso = '".$clavecuro."'
					group by icursos.id") as $resultset ){
				return $resultset;
			}
		} // function get_curso_by_clavecurso($clavecuro)
		
		function validar_si_algun_alumno_tiene_calificacion($clavecurso){
			foreach( $this -> find_all_by_sql("
					select *
					from intersemestral_alumnos
					where clavecurso = '".$clavecurso."'
					and calificacion != 300") as $resultset ){
				return false; // Si regresa false, no podrá eliminar el curso.
			}
			return true; // Si regresa true, podrá eliminar el curso.
		} // function validar_si_algun_alumno_tiene_calificacion($clavecurso)
		
		function borrar_curso($clavecurso){
			foreach( $this -> find_all_by_sql("
					delete from intersemestral_cursos
					where clavecurso = '".$clavecurso."'") as $resultset ){
			}
		} // function borrar_curso($clavecurso)
		
		function desinscribir_alumnos($clavecurso){
			foreach( $this -> find_all_by_sql("
					delete from intersemestral_alumnos
					where clavecurso = '".$clavecurso."'") as $resultset ){
			}
		} // function desinscribir_alumnos($clavecurso)
		
		function get_materias_iguales($clavemat){
			$materias = Array();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			foreach( $this -> find_all_by_sql("
					select icursos.clavemat, m.nombre, icursos.cupo, icursos.disponibilidad,
					(icursos.cupo - icursos.disponibilidad) inscritos, clavecurso
					from intersemestral_cursos icursos
					inner join materia m
					on icursos.clavemat = m.clave
					where icursos.periodo = '".$periodo."'
					and icursos.clavemat = '".$clavemat."'
					group by icursos.id;") as $resultset ){
				array_push($materias, $resultset);
			}
			return $materias;
		} // function get_materias_iguales($clavemat)
		
		function get_cursos_by_nomina_and_periodo($nomina){
			$cursos = Array();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			foreach( $this -> find_all_by_sql("
					select icur.*, m.nombre
					from intersemestral_cursos icur
					inner join materia m
					on icur.clavemat = m.clave
					where icur. nomina = '".$nomina."'
					and icur.periodo = '".$periodo."'
					group by icur.id") as $resultset ){
				array_push($cursos, $resultset);
			}
			return $cursos;
		} // function get_cursos_by_nomina_and_periodo($nomina)
		
		function get_cursos_intersemestrales_by_nomina($nomina){
			$Interemestrales = Array();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			foreach( $this -> find_all_by_sql("
					select icur.clavecurso, icur.cupo, icur.disponibilidad,
					m.clave, m.nombre nombre_materia, maes.nomina,
					maes.nombre nombre_profesor
					from intersemestral_cursos icur
					inner join materia m
					on icur.clavemat = m.clave
					inner join maestros maes
					on icur.nomina = maes.nomina
					where icur.periodo = '".$periodo."'
					and icur.nomina = '".$nomina."'
					group by icur.id")as $Inter ){
				array_push($Interemestrales, $Inter);
			}
			return $Interemestrales;
		} // function get_cursos_intersemestrales_by_nomina($nomina)
		
		function get_numero_alumnos_inscritos_a_un_curso($clavecurso){
			
			foreach( $this -> find_all_by_sql("
					select disponibilidad, cupo
					from intersemestral_cursos
					where clavecurso ='".$clavecurso."'") as $Inter ){
				if( $Inter -> disponibilidad <= 0 )
					return false; // False, no inscribirá al alumno en el curso, falta de cupo.
				else
					return true; // True, inscribirá al alumno en el curso, hay cupo disponible.
			}
			
		} // function get_numero_alumnos_inscritos_a_un_curso($clavecurso)
		
		function get_materia_de_2_periodos_atras_reprobada($clavemat, $alumno){
			
			$Materia = new Materia();
			$KardexIng = new KardexIng();
			
			$info -> si_hay_materia = false;
			if( $KardexIng -> get_if_is_in_kardex($clavemat, $alumno->miReg) ){
				return $info;
			}
			
			foreach( $this->find_all_by_sql("
					SELECT xal.periodo, xcc.materia
					from xalumnocursos xal
					join xccursos xcc
					on xcc.id = xal.curso_id
					and xcc.materia = '".$clavemat."'
					and xal.registro = ".$alumno->miReg."
					and xal.calificacion < 70") as $Inter ){
				$materia = $Materia -> find_first("clave = '".$clavemat."'".
						" and carrera_id = '".$alumno->carrera_id."'".
						" and (serie = '".$alumno->areadeformacion_id."'".
						" or serie = '-')");
				foreach( $this->find_all_by_sql("
						SELECT xext.periodo, xcc.materia
						from xextraordinarios xext
						join xccursos xcc
						on xcc.id = xext.curso_id
						and xcc.materia = '".$clavemat."'
						and xext.registro = ".$alumno->miReg."
						and (xext.calificacion < 70
						or xext.calificacion > 100)") as $Inter ){
					$info = $Inter;
					$info -> nombre_materia = $materia->nombre;
					$info -> si_hay_materia = true;
					return $info;
				}
			}
			
			foreach( $this->find_all_by_sql("
					SELECT xal.periodo, xcc.materia
					from xtalumnocursos xal
					join xtcursos xcc
					on xcc.id = xal.curso_id
					and xcc.materia = '".$clavemat."'
					and xal.registro = ".$alumno->miReg."
					and xal.calificacion < 70") as $Inter ){
				$materia = $Materia -> find_first("clave = '".$clavemat."'".
						" and carrera_id = '".$alumno->carrera_id."'".
						" and (serie = '".$alumno->areadeformacion_id."'".
						" or serie = '-')");
				foreach( $this->find_all_by_sql("
						SELECT xext.periodo, xcc.materia
						from xtextraordinarios xext
						join xtcursos xcc
						on xcc.id = xext.curso_id
						and xcc.materia = '".$clavemat."'
						and xext.registro = ".$alumno->miReg."
						and (xext.calificacion < 70
						or xext.calificacion > 100)") as $Inter ){
					$info = $Inter;
					$info -> nombre_materia = $materia->nombre;
					$info -> si_hay_materia = true;
					return $info;
				}
			}
			
			
		} // function get_if_la_materia_la_curso_2_periodos_atras($clavemat)
	}
?>