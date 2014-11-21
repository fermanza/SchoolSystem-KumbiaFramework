<?php
	class Xalumnocursos extends ActiveRecord {
	
		function get_materias_semestre_actual($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			$xalumnocursos = Array();
			foreach( $this->find_all_by_sql("
					select xal.*, xcc.clavecurso curso, xcc.clavecurso
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = '".$registro."'
					and xal.periodo = '".$periodo."'") as $xal ){
				array_push($xalumnocursos, $xal);
			}
			return $xalumnocursos;
		} // function get_materias_semestre_actual($registro)
		
		function get_materias_semestre_by_periodo($registro, $periodo){
			
			$xalumnocursos = Array();
			foreach( $this->find_all_by_sql("
					select xal.*, xcc.clavecurso curso, xcc.clavecurso
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = '".$registro."'
					and xal.periodo = '".$periodo."'") as $xal ){
				array_push($xalumnocursos, $xal);
			}
			return $xalumnocursos;
		} // function get_materias_semestre_by_periodo($registro, $periodo)
		
		function get_materias_semestre_by_periodo_colomos_tonala($registro, $periodo){
			$xalumnocursos = Array();
			foreach( $this->find_all_by_sql("
					select xal.*, xcc.clavecurso curso, xcc.clavecurso, xcc.materia
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = '".$registro."'
					and xal.periodo = '".$periodo."'") as $xal ){
				array_push($xalumnocursos, $xal);
			}
			foreach( $this->find_all_by_sql("
					select xal.*, xcc.clavecurso curso, xcc.clavecurso, xcc.materia
					from xtalumnocursos xal
					inner join xtcursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = '".$registro."'
					and xal.periodo = '".$periodo."'") as $xal ){
				array_push($xalumnocursos, $xal);
			}
			
			return $xalumnocursos;
		} // function get_materias_semestre_by_periodo_colomos_tonala($registro, $periodo)
		
		function get_materia_con_baja_definitiva($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			$Xtalumnocursos = new Xtalumnocursos();
			if( $this -> find_first("situacion = 'BAJA DEFINITIVA'".
					" and periodo = '".$periodo."'".
					" and registro = '".$registro."'") )
				return true;
			else if( $Xtalumnocursos -> find_first("situacion = 'BAJA DEFINITIVA'".
					" and periodo = '".$periodo."'".
					" and registro = '".$registro."'") )
				return true;
			else
				return false;
		} // function get_materia_con_baja_definitiva($registro)
		
		function get_materia_con_baja_definitiva_by_periodo($registro, $periodo){
			if( $this -> find_first("situacion = 'BAJA DEFINITIVA'".
					" and periodo = '".$periodo."'".
					" and registro = '".$registro."'") )
				return true;
			else
				return false;
		} // function get_materia_con_baja_definitiva_by_periodo($registro, $periodo)
		
		function get_materias_con_baja_definitiva_o_titulo_colomos($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			$MateriasConBajaOTitulo = Array();
			foreach( $this -> find_all_by_sql("
					select xal.*
					from xalumnocursos xal
					where periodo = '".$periodo."'
					and registro = '".$registro."'
					and situacion = 'BAJA DEFINITIVA'") as $xal ){
				foreach( $this -> find_all_by_sql("
						select xcc.materia, m.nombre nombre_materia, xcc.clavecurso
						from xccursos xcc
						inner join materia m
						on xcc.materia = m.clave
						and xcc.id = '".$xal -> curso_id."'
						group by xcc.id") as $xcc ){
					$BajasYTitulos -> materia = $xcc -> materia;
					$BajasYTitulos -> nombre_materia = $xcc -> nombre_materia;
					$BajasYTitulos -> calificacion = $xal -> calificacion;
					$BajasYTitulos -> situacion = $xal -> situacion;
					$BajasYTitulos -> clavecurso = $xcc->clavecurso;
					array_push($MateriasConBajaOTitulo, $BajasYTitulos);
				}
			}
			foreach( $this -> find_all_by_sql("
					select xal.*
					from xextraordinarios xal
					where periodo = '".$periodo."'
					and registro = '".$registro."'
					and tipo = 'TT'
					and calificacion < 70") as $xal ){
				foreach( $this -> find_all_by_sql("
						select xcc.materia, m.nombre nombre_materia, xcc.clavecurso
						from xccursos xcc
						inner join materia m
						on xcc.materia = m.clave
						and xcc.id = '".$xal -> curso_id."'
						group by xcc.id") as $xcc ){
					$BajasYTitulos -> materia = $xcc -> materia;
					$BajasYTitulos -> nombre_materia = $xcc -> nombre_materia;
					$BajasYTitulos -> calificacion = $xal -> calificacion;
					$BajasYTitulos -> situacion = $xal -> situacion;
					$BajasYTitulos -> clavecurso = $xcc -> clavecurso;
					array_push($MateriasConBajaOTitulo, $BajasYTitulos);
				}
			}
			return $MateriasConBajaOTitulo;
		} // function get_materias_con_baja_definitiva_o_titulo_colomos($registro)
		
		function get_materias_con_baja_definitiva_o_titulo_tonala($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			$MateriasConBajaOTitulo = Array();
			foreach( $this -> find_all_by_sql("
					select xal.*
					from xtalumnocursos xal
					where periodo = '".$periodo."'
					and registro = '".$registro."'
					and situacion = 'BAJA DEFINITIVA'") as $xal ){
				foreach( $this -> find_all_by_sql("
						select xcc.materia, m.nombre nombre_materia, xcc.clavecurso
						from xtcursos xcc
						inner join materia m
						on xcc.materia = m.clave
						and xcc.id = '".$xal -> curso_id."'
						group by xcc.id") as $xcc ){
					$BajasYTitulos -> materia = $xcc -> materia;
					$BajasYTitulos -> nombre_materia = $xcc -> nombre_materia;
					$BajasYTitulos -> calificacion = $xal -> calificacion;
					$BajasYTitulos -> situacion = $xal -> situacion;
					$BajasYTitulos -> clavecurso = $xcc -> clavecurso;
					array_push($MateriasConBajaOTitulo, $BajasYTitulos);
				}
			}
			foreach( $this -> find_all_by_sql("
					select xal.*
					from xtextraordinarios xal
					where periodo = '".$periodo."'
					and registro = '".$registro."'
					and tipo = 'TT'
					and calificacion < 70") as $xal ){
				foreach( $this -> find_all_by_sql("
						select xcc.materia, m.nombre nombre_materia, xcc.clavecurso
						from xtcursos xcc
						inner join materia m
						on xcc.materia = m.clave
						and xcc.id = '".$xal -> curso_id."'
						group by xcc.id") as $xcc ){
					$BajasYTitulos -> materia = $xcc -> materia;
					$BajasYTitulos -> nombre_materia = $xcc -> nombre_materia;
					$BajasYTitulos -> calificacion = $xal -> calificacion;
					$BajasYTitulos -> situacion = $xal -> situacion;
					$BajasYTitulos -> clavecurso = $xcc -> clavecurso;
					array_push($MateriasConBajaOTitulo, $BajasYTitulos);
				}
			}
			return $MateriasConBajaOTitulo;
		} // function get_materias_con_baja_definitiva_o_titulo_tonala($registro)
		
		function get_if_materia_es_regularizacion_o_titulo($alumno, $clavemat){
			
			$Intersemestrales = Array();
			foreach($this->find_all_by_sql("
					select xal.situacion, xcc.clavecurso, xcc.materia, m.nombre nombre_materia, xal.calificacion
					from xalumnocursos xal
					join xccursos xcc
					on xal.curso_id = xcc.id
					join materia m
					on xcc.materia = m.clave
					and m.carrera_id = '".$alumno->carrera_id."'
					and xal.registro = '".$alumno->miReg."'
					and xcc.materia = '".$clavemat."'
					and xal.situacion = 'REGULARIZACION DIRECTA'") as $Inter){
				array_push($Intersemestrales, $Inter);
			}
			
			foreach($this->find_all_by_sql("
					select xal.situacion, xcc.clavecurso, xcc.materia, m.nombre nombre_materia, xal.calificacion
					from xtalumnocursos xal
					join xtcursos xcc
					on xal.curso_id = xcc.id
					join materia m
					on xcc.materia = m.clave
					and m.carrera_id = '".$alumno->carrera_id."'
					and xal.registro = '".$alumno->miReg."'
					and xcc.materia = '".$clavemat."'
					and xal.situacion = 'REGULARIZACION DIRECTA'") as $Inter){
				array_push($Intersemestrales, $Inter);
			}
			
			return $Intersemestrales;
		} // function get_if_materia_es_regularizacion_o_titulo($alumno, $clavemat)
		
		function get_materias_con_regularizacion_directa($alumno){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$MateriasRegularizacionDirecta = Array();
			foreach($this -> find_all_by_sql("
					select xal.situacion, xcc.clavecurso, xcc.materia, m.nombre nombre_materia, xal.calificacion
					from xalumnocursos xal
					join xccursos xcc
					on xal.curso_id = xcc.id
					join materia m
					on xcc.materia = m.clave
					and m.carrera_id = '".$alumno->carrera_id."'
					and xal.registro = '".$alumno->miReg."'
					and xal.periodo = ".$periodo."
					and xal.situacion = 'REGULARIZACION DIRECTA'") as $Inter){
				array_push($MateriasRegularizacionDirecta, $Inter);
			}
			
			$cuenta = $this->contarCuantasMateriasCargoEnEsteSemestre($alumno->miReg);
						
			if( ( (($cuenta - (count($MateriasRegularizacionDirecta))) * 100) / ($cuenta) ) < 60 )
				$MateriasRegularizacionDirecta[0]->noPresentaCurso = true;
			else
				$MateriasRegularizacionDirecta[0]->noPresentaCurso = false;
			
			return $MateriasRegularizacionDirecta;
		} // function get_materias_con_regularizacion_directa($registro)
		
		function contarCuantasMateriasCargoEnEsteSemestre($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$cuenta = 0;
			foreach($this->find_all_by_sql("
					select count(*) cuenta
					from xalumnocursos
					where registro = '".$registro."'
					and periodo = '".$periodo."'") as $xal){
				$cuenta += $xal->cuenta;
			}
			foreach($this->find_all_by_sql("
					select count(*) cuenta
					from xtalumnocursos
					where registro = '".$registro."'
					and periodo = '".$periodo."'") as $xal){
				$cuenta += $xal->cuenta;
			}
			
			return $cuenta;
		} // function contarCuantasMateriasCargoEnEsteSemestre($registro)
		
		function contarCuantasMateriasCargoEnEsteSemestre_($registro, $periodo){
			$cuenta = 0;
			foreach($this->find_all_by_sql("
					select count(*) cuenta
					from xalumnocursos
					where registro = '".$registro."'
					and periodo = '".$periodo."'") as $xal){
				$cuenta += $xal->cuenta;
			}
			foreach($this->find_all_by_sql("
					select count(*) cuenta
					from xtalumnocursos
					where registro = '".$registro."'
					and periodo = '".$periodo."'") as $xal){
				$cuenta += $xal->cuenta;
			}
			
			return $cuenta;
		} // function contarCuantasMateriasCargoEnEsteSemestre_($registro, $periodo)
		
		function borrado_logico_materias_by_periodo($registro, $periodo){
			$who = Session::get_data('registro');
			$Periodos = new Periodos();
			$timestamp = $Periodos->get_datetime();
			foreach($this->find_all_by_sql("
				update xalumnocursos
				set activo = '0',
				who = '".$who."',
				last_modified = '".$timestamp."'
				where registro = '".$registro."'
				and periodo = '".$periodo."'") as $alumno){
					break;
			}
		} // function borrado_logico_materias_by_periodo($registro, $periodo)
		
		function activar_materias_by_periodo($registro, $periodo){
			$who = Session::get_data('registro');
			$Periodos = new Periodos();
			$timestamp = $Periodos->get_datetime();
			foreach($this->find_all_by_sql("
				update xalumnocursos
				set activo = '1',
				who = '".$who."',
				last_modified = '".$timestamp."'
				where registro = '".$registro."'
				and periodo = '".$periodo."'") as $alumno){
					break;
			}
		} // function activar_materias_by_periodo($registro, $periodo)
		
		function pasar_materias_a_xalumnocursosbt_by_periodo_registro($registro, $periodo){
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			foreach($this->find_all_by_sql("
					select * from xalumnocursos
					where registro = '".$registro."'
					and periodo = '".$periodo."'") as $xalumnocursos){
				
				$Xalumnocursosbt = new Xalumnocursosbt();
				$Xalumnocursosbt->id_xc = $xalumnocursos->id;
				$Xalumnocursosbt->periodo = $xalumnocursos->periodo;
				$Xalumnocursosbt->curso_id = $xalumnocursos->curso_id;
				$Xalumnocursosbt->registro = $xalumnocursos->registro;
				$Xalumnocursosbt->faltas1 = $xalumnocursos->faltas1;
				$Xalumnocursosbt->calificacion1 = $xalumnocursos->calificacion1;
				$Xalumnocursosbt->faltas2 = $xalumnocursos->faltas;
				$Xalumnocursosbt->calificacion2 = $xalumnocursos->calificacion2;
				$Xalumnocursosbt->faltas3 = $xalumnocursos->faltas3;
				$Xalumnocursosbt->calificacion3 = $xalumnocursos->calificacion3;
				$Xalumnocursosbt->faltas = $xalumnocursos->faltas;
				$Xalumnocursosbt->calificacion = $xalumnocursos->calificacion;
				$Xalumnocursosbt->situacion = $xalumnocursos->situacion;
				$Xalumnocursosbt->tipo = $xalumnocursos->tipo;
				
				if( $Xalumnocursosbt->tipo == null || $Xalumnocursosbt->tipo == ""){
					$Xalumnocursosbt->tipo = "-";
				}
				$Xalumnocursosbt->create();
				
				$xalumnocursos->delete();
			}
		} // function pasar_materias_a_xalumnocursosbt_by_periodo_registro($registro, $periodo)
		
		function get_horas_cruces_de_materias($registro, $periodo){
			$ocupado = 0;
			unset($horas);
			$Xchorascursos = new Xchorascursos();
			foreach( $this -> find( "registro = ".$registro." and periodo = ".$periodo ) as $xalumnocurso ){
				foreach( $Xchorascursos -> find( "curso_id = '".
						$xalumnocurso -> curso_id."' ORDER BY id ASC" ) as $xchorascurso ){
					
					if(isset($horas[$xchorascurso->dia][$xchorascurso->hora])){
						$ocupado++;
					}
					else{
						$horas[$xchorascurso->dia][$xchorascurso->hora] = 1;
					}
				}
			}
			return $ocupado;
		} // get_horas_cruces_de_materias($registro, $periodo)
		
		function get_estado_de_extraordinarios_by_clavemat($alumno, $clavemat) {
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			foreach($this->find_all_by_sql("
					select *
					from xextraordinarios xext
					join xccursos xcc
					on xcc.id = xext.curso_id
					where xext.periodo = '".$periodo."'
					and xext.registro= '".$alumno->miReg."'
					and xcc.materia = '".$clavemat."'" ) as $xextra ){
				return $xextra;
			}
		}
		
		function get_all_students_by_periodo_plan_creditos($periodo){
			$registros = Array();
			foreach($this->find_all_by_sql("
					select distinct(xal.registro) registro
					from xalumnocursos xal
					join alumnos al
					where xal.periodo = '".$periodo."'
					and xal.registro = al.miReg
					and al.chGpo != '**'") as $alumno){
				array_push($registros, $alumno->registro);
			}
			
			return $registros;
		} // function get_all_students_by_periodo_plan_creditos($periodo)
		
		function get_all_students_by_periodo_plan_rigido($periodo){
			$registros = Array();
			foreach($this->find_all_by_sql("
					select distinct(xal.registro) registro
					from xalumnocursos xal
					join alumnos al
					where xal.periodo = '".$periodo."'
					and xal.registro = al.miReg
					and al.chGpo = '**'") as $alumno){
				array_push($registros, $alumno->registro);
			}
			return $registros;
		} // function get_all_students_by_periodo_plan_rigido($periodo)
		
		function get_materias_reprobadas_en_titulo_by_registro($registro, $periodo){
			$materias = Array();
			
			foreach($this->find_all_by_sql("
					select * from xextraordinarios
					where periodo = '".$periodo."'
					and tipo = 'T'
					and registro = '".$registro."'
					and ( calificacion = 300
					or calificacion < 70
					or ( calificacion >= 70
					and calificacion <= 100
					and estado != 'OK') ) ") as $materia){
				array_push($materias, $materia);
			}
			return $materias;
		} // function get_materias_reprobadas_en_titulo_by_registro($registro, $periodo)
		
		function reprobadas_extra_yno_acreditadas_en_inter($registro, $periodo){
			$Periodos = new Periodos();
			$periodo_inter = $Periodos->convertirPeriodo_a_intersemestral($periodo);
			
			$extras = Array();
			foreach($this->find_all_by_sql("
					select xext.*, xcc.materia
					from xextraordinarios xext
					join xccursos xcc on xext.curso_id = xcc.id
					join xalumnocursos xal on xal.curso_id = xcc.id
					where xext.periodo = '".$periodo."'
					and xal.situacion != 'REGULARIZACION DIRECTA'
					and xext.registro = '".$registro."'
					and xal.registro = xext.registro
					and ( xext.calificacion = 300
					or xext.calificacion < 70
					or ( xext.calificacion >= 70
					and xext.calificacion <= 100
					and xext.estado != 'OK') )") as $extra){
				array_push($extras, $extra);
			}

			$intersemestrales = array();
			foreach($this->find_all_by_sql("
					select ic.clavemat
					from intersemestral_alumnos ia
					join intersemestral_cursos ic
					where ia.registro = '".$registro."'
					and ia.periodo = '".$periodo_inter."'
					and ia.clavecurso = ic.clavecurso
					and ia.calificacion >= 70
					and ia.calificacion <= 100
					and ia.pago = 'OK'") as $inter){ // Solo traera las materias que haya acreditado y que tengan pago = OK
				array_push($intersemestrales, $inter->clavemat);
			}
			
			// Array de materias reprobadas en Extras y no Acreditadas en Intersemestrales
			$materiasReprobadas = Array();
			foreach($extras as $extra){
				if( !in_array($extra->materia, $intersemestrales) ){ // Si no se encuentra en el arreglo, significa que si la tiene reprobada.
					array_push($materiasReprobadas, $extra);
				}
			}
			
			return $materiasReprobadas;
		} // function reprobadas_extra_yno_acreditadas_en_inter($registro, $periodo)
		
		function get_materias_regularizacion_directa($registro, $periodo){
			$regDirecta = Array();
			foreach($this->find_all_by_sql("
					select *
					from xalumnocursos
					where periodo = '".$periodo."'
					and situacion = 'REGULARIZACION DIRECTA'
					and registro = '".$registro."'") as $materias){
				array_push($regDirecta, $materias);
			}
			return $regDirecta;
		} // function get_materias_regularizacion_directa($registro, $periodo)
		
		function get_alumnos_relevant_info_by_curso_id($curso_id){
			$alumnos = array();
			foreach( $this -> find_all_by_sql( "
					select xal.*, al.enPlan, al.enTurno, al.enPlantel,
					al.carrera_id, al.areadeformacion_id, al.tiNivel, al.chGpo,
					al.enTipo, c.nombre carrera_nombre, al.vcNomAlu
					from xalumnocursos xal
					join alumnos al on al.miReg = xal.registro
					join carrera c on c.id = al.carrera_id
					where curso_id = '".$curso_id."'" ) as $xal ){
				array_push($alumnos, $xal);
			}
			return $alumnos;
		} // function get_alumnos_relevant_info_by_curso_id($curso_id)
		
		function get_alumnos_relevant_info_by_cursos_id($cursos_id){
			$alumnos = array();
			$query = "select xal.*, al.enPlan, al.enTurno, al.enPlantel,
					al.carrera_id, al.areadeformacion_id, al.tiNivel, al.chGpo,
					al.enTipo, c.nombre carrera_nombre, al.vcNomAlu, t1.cuenta
					from xalumnocursos xal, alumnos al, carrera c,
					(
					  select count(*) cuenta, registro, curso_id
					  from xalumnocursos
					  where curso_id = ";
			$flag = 0;
			foreach($cursos_id as $curso_id){
				if($flag==0)	
					$query .= $curso_id;
				else
					$query .= " or curso_id = ".$curso_id;
				$flag++;
			}
			$query .= " group by registro
					)t1
					where t1.registro = xal.registro
					and al.miReg = xal.registro
					and c.id = al.carrera_id
					and xal.curso_id = t1.curso_id
					order by t1.cuenta desc";
			foreach( $this -> find_all_by_sql( $query ) as $xal ){
				array_push($alumnos, $xal);
			}
			return $alumnos;
		} // function get_alumnos_relevant_info_by_cursos_id($cursos_id)
		
		function get_relevant_info_from_xccursos_by_registro_sem_actual($registro){
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			$cursos = array();
			foreach( $this -> find_all_by_sql( "
					Select ma.nomina, ma.nombre nombre_profesor, xcc.clavecurso, xcc.materia,
					m.nombre materia_nombre
					from maestros ma
					join xccursos xcc on xcc.nomina = ma.nomina
					join materia m on m.clave = xcc.materia
					join xalumnocursos xal on xal.curso_id = xcc.id
					and xal.periodo = '".$periodo."'
					and xal.registro = '".$registro."'
					group by xcc.id" ) as $xcc ){
				array_push($cursos, $xcc);
			}
			return $cursos;
		} // function get_relevant_info_from_xccursos_by_registro_sem_actual($registro)
		
	}
?>