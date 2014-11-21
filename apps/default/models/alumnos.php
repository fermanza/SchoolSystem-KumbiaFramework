<?php
	class Alumnos extends ActiveRecord {
		
		function getXalORXtal($registro){
			$Xalumnocursos = new Xalumnocursos();
			$Xtalumnocursos = new Xtalumnocursos();
			$Periodos = new Periodos();
			
			$periodo = $Periodos -> get_periodo_actual();
			//$periodo = $Periodos -> get_periodo_anterior();
			
			$plantel = array();
			if( $Xalumnocursos -> find_first("registro = ".$registro." and periodo = ".$periodo) ){
				$plantel[0] = "";
				$plantel[1] = "c";
			}
			else if( $Xtalumnocursos -> find_first("registro = ".$registro." and periodo = ".$periodo) ){
				$plantel[0] = "t";
				$plantel[1] = "t";
			}
			else{
				$plantel[0] = "";
				$plantel[1] = "";
			}
			return $plantel;
		} // function getXalORXtal($registro)
		
		function get_relevant_info_from_student($registro){
			if(!isset($registro))
				$registro = Session::get_data('registro');
			
			foreach( $this -> find_all_by_sql(
					"select enPlantel,enPlan, enTurno, idtiEsp, carrera_id, areadeformacion_id, enNivEdu,
					miReg, enTurno, enTipo, stSit, vcNomAlu, miPerIng, pago, correoOficial, chGpo, condonado
					from alumnos
					where miReg = ".$registro) as $alumno ){
				$al = $alumno;
			}
			return $al;
		} // function get_relevant_info_from_student()
		
		function get_student_by_carrera_id($carrera_id){
			$alumnos = Array();
			foreach( $this -> find_all_by_sql(
					"select carrera_id, areadeformacion_id,
					miReg, enTurno, enTipo, stSit, vcNomAlu, miPerIng, pago
					from alumnos
					where carrera_id = ".$carrera_id."
					order by miReg") as $alumno ){
				array_push($alumnos, $alumno);
			}
			return $alumnos;
		} // function get_student_by_carrera_id()
		
		function get_info_from_student_for_intersemetral(){
			$alumnos = Array();
			foreach( $this -> find_all_by_sql("
					select al.miReg miReg, al.vcNomAlu vcNomAlu, c.nombre carrera_nombre, al.enPlan plan_de_estudios
					from alumnos al
					inner join carrera c
					on al.carrera_id = c.id") as $resulset ){
				array_push($alumnos, $resulset);
			}
			return $alumnos;
		} // function get_info_from_student_for_intersemetral()
		
		// Regresa el nombre de la carrera y su areadeformacion si es que tiene.
		function get_careername_from_student($carrera_id, $areadeformacion_id){
			$registro = Session::get_data('registro');
			
			if( $areadeformacion_id != 0 ){
				foreach( $this -> find_all_by_sql(
						"select c.nombre, a.nombreareaformacion
						from carrera c
						inner join areadeformacion a
						on c.id = a.carrera_id
						and a.carrera_id = ".$carrera_id."
						and a.idareadeformacion = ".$areadeformacion_id) as $alumno ){
					$al = $alumno;
				}
			}
			else{
				foreach( $this -> find_all_by_sql(
						"select nombre
						From carrera
						where id = ".$carrera_id) as $alumno ){
					$al = $alumno;
				}
			}
			return $al;
		} // function get_careername_from_student($carrera_id, $areadeformacion_id)
		
		function get_nombre_plantel($enPlantel){
			if(strtoupper($enPlantel == "C"))
				return "COLOMOS";
			else
				return "TONALA";
		} // function get_nombre_plantel($enPlantel)
		
		function get_turno($turno){
			if(strtoupper($turno == "V"))
				return "VESPERTINO";
			else
				return "MATUTINO";
		} // function get_turno($turno)
		
		function nivel($enNivEdu){
			if(strtoupper($enNivEdu == "I"))
				$enNivEdu = "INGENIERIA";
			else
				$enNivEdu = "TECNOLOGO";
			return $enNivEdu;
		} // function nivel($enNivEdu)
		
		function imprimir_ficha_pago($registro){
			
			$Periodos = new Periodos();
			$periodoantesanterior = $Periodos -> get_periodo_antesanterior();
			$periodoanterior = $Periodos -> get_periodo_anterior();
			$periodoactual = $Periodos -> get_periodo_actual();
			$periodoproximo = $Periodos -> get_periodo_proximo();
			
			// $noreinscripcion->0 = $status->0			<- No se puede reinscribir
			// $inscripcion->1 = $status->1				<- Su ficha de pago aparecerá como Inscripción
			// $noreinscripcion->1 = $status->2			<- Su ficha de pago aparecerá como Reinscripción
			
			$Admitidos = new Admitidos();
			if( $Admitidos -> find_first("registro =".$registro." and periodo = ".$periodoactual) )
				return $status = 1;
			
			$ReconsideracionBaja = new ReconsideracionBaja();
			if( $ReconsideracionBaja -> find_first
						( "periodo = ".$periodoactual."
						and registro = ".$registro."
						and procede = 1" )){
				return $status = 2;
			} // if( $ReconsideracionBaja -> find_first
			
			// Si queda en 0, significa que no se puede Reinscribir el sig semestre.
			$status = 2;
			
			$Xalumnocursos	= new Xalumnocursos();
			$Xtalumnocursos	= new Xtalumnocursos();
			// Revisar en que plantel estaba en el semestre pasado.
			if( $Xalumnocursos -> find_first( "registro = ".$registro." and periodo = ".$periodoanterior ) ){
				$enPlantel = "C";
			}
			else if( $Xtalumnocursos -> find_first( "registro = ".$registro." and periodo = ".$periodoanterior ) ){
				$enPlantel = "N";
			}
			//$periodo = 12011;
			//$periodoanterior = 32010;
			//-
			//BAJA DEFINITIVA
			//EXTRAORDINARIO
			//EXTRAORDINARIO FALTAS
			//ORDINARIO
			//REGULARIZACION
			//REGULARIZACION DIRECTA
			//TITULO DE SUFICIENCIA
			//TITULO FALTAS
			$extras = 0;
			$titulos = 0;
			$proceso = 0;
			$Xalumnocursos	= new Xalumnocursos();
			$Xtalumnocursos	= new Xtalumnocursos();
			if( $enPlantel == "C" ){
				foreach( $Xalumnocursos -> find
						( "registro = ".$registro." and periodo = ".$periodoanterior ) as $xal ){
					if( $xal -> situacion == "EXTRAORDINARIO" ||
							$xal -> situacion == "EXTRAORDINARIO FALTAS" ||
								$xal -> situacion == "REGULARIZACION DIRECTA" ){
						$extras++;
					}
					if( $xal -> situacion == "TITULO DE SUFICIENCIA" ||
							$xalumnocurso -> situacion == "TITULO FALTAS" ){
						$titulos++;
					}
					if( $xal -> situacion == "BAJA DEFINITIVA" ){
						return $status = 0;
					}
				}
				$SeleccionTiempo		= new SeleccionTiempo();
				if( !$SeleccionTiempo -> find_first( "periodo = ".$periodoactual." and registro = ".$registro ) )
					$status = 0;
				
				$Xalumnocursos = new Xalumnocursos();
				foreach( $Xalumnocursos -> find_all_by_sql
						 ( "Select count(*) as cuantos from xalumnocursos
							where registro = ".$registro."
							and periodo = ".$periodoanterior."
							and ( situacion = \"TITULO DE SUFICIENCIA\"
							or situacion = \"TITULO FALTAS\")" ) as $xal){
					$xalcurr = $xal;
				}
				$Xextraordinarios = new Xextraordinarios();
				foreach( $Xextraordinarios -> find_all_by_sql
						 ( "Select count(*) as cuantosTitulos
							from xextraordinarios
							where registro = ".$registro."
							and periodo = ".$periodoanterior."
							and calificacion > 69
							and calificacion < 100
							and tipo = \"T\"" ) as $xextras ){
					$xextraas = $xextras;
				}
				if( $xalcurr -> cuantos == $xextraas -> cuantosTitulos ){
					$status = 2;
				}
				
				// Para ver si aun debe materias del semestre pasado... EXTRAS O TITULOS RESAGADOS
				foreach( $Xextraordinarios -> find_all_by_sql
							( "Select * from xccursos xcc, xextraordinarios xext
							where xext.curso_id = xcc.id
							and xcc.periodo = ".$periodoantesanterior."
							and xext.registro = ".$registro."
							and xext.periodo = ".$periodoanterior."
							and calificacion < 70" ) as $xextras ){
					$status = 0;
				}
				$Xalumnocursos = new Xalumnocursos();
				foreach( $Xalumnocursos -> find_all_by_sql(
						"select count(*) cuenta from xalumnocursos
						where registro = ".$registro."
						and periodo = ".$periodoanterior) as $cuenta){
					if( $cuenta -> cuenta == 0 ){
						$status = 0;
					}
				}
				$Bajastemporales = new Bajastemporales();
				foreach( $Bajastemporales -> find_all_by_sql(
						"select * from bajastemporales
						where registro = ".$registro."
						and periodocomienza = ".$periodoanterior) as $bt){
					$status = 2;
				}
				
				$Alumnos = new Alumnos();
				if($Alumnos -> find_first("miReg = ".$registro.
						" and miPerIng = ".$periodoanterior)){ // Cambiar a this -> anterior cuando cambie de periodo
					$status = 2;
				}
				if( $extras > 6 )
					$status = 0;
				if( $titulos > 0 ){
					$Xextraordinarios = new Xextraordinarios();
					if( $Xextraordinarios -> find_first(
							"registro = ".$registro.
							" and periodo = ".$periodoanterior.
							" and calificacion < 70".
							" and tipo = 'T'") ){
						$status = 0;  // Si aun tiene titulos de suficiencia sin pasar, no podrá imprimir la ficha...
					}
				}
				
				$HistorialAlumno = new HistorialAlumno();
				if( $HistorialAlumno -> find_first
							( "periodo = ".$periodoactual."
							and registro = ".$registro."
							and situacion_id = 9" )){
					$status = 2;
				}
			}
			else{ // Tonala
				foreach( $Xtalumnocursos -> find
						( "registro = ".$registro." and periodo = ".$periodoanterior ) as $xal ){
					if( $xal -> situacion == "EXTRAORDINARIO" ||
							$xal -> situacion == "EXTRAORDINARIO FALTAS" ||
								$xal -> situacion == "REGULARIZACION DIRECTA" ){
						$extras++;
					}
					if( $xal -> situacion == "TITULO DE SUFICIENCIA" ||
							$xalumnocurso -> situacion == "TITULO FALTAS" ){
						$titulos++;
					}
					if( $xal -> situacion == "BAJA DEFINITIVA" ){
						return $status = 0;
					}
				}
				$SeleccionTiempo		= new SeleccionTiempo();
				if( !$SeleccionTiempo -> find_first( "periodo = ".$periodoactual." and registro = ".$registro ) )
					$status = 0;
				
				$Xtalumnocursos = new Xtalumnocursos();
				foreach( $Xtalumnocursos -> find_all_by_sql
						 ( "Select count(*) as cuantos from xtalumnocursos
							where registro = ".$registro."
							and periodo = ".$periodoanterior."
							and ( situacion = \"TITULO DE SUFICIENCIA\"
							or situacion = \"TITULO FALTAS\")" ) as $xal){
					$xalcurr = $xal;
				}
				$Xtextraordinarios = new Xtextraordinarios();
				foreach( $Xtextraordinarios -> find_all_by_sql
						 ( "Select count(*) as cuantosTitulos
							from xtextraordinarios
							where registro = ".$registro."
							and periodo = ".$periodoanterior."
							and calificacion > 69
							and calificacion < 100
							and tipo = \"T\"" ) as $xextras ){
					$xextraas = $xextras;
				}
				if( $xalcurr -> cuantos == $xextraas -> cuantosTitulos ){
					$status = 2;
				}
				
				// Para ver si aun debe materias del semestre pasado... EXTRAS O TITULOS RESAGADOS
				foreach( $Xtextraordinarios -> find_all_by_sql
							( "Select * from xtcursos xc, xtextraordinarios xext
							where xext.curso_id = xc.id
							and xc.periodo = ".$periodoantesanterior."
							and xext.registro = ".$registro."
							and xext.periodo = ".$periodoanterior."
							and calificacion < 70" ) as $xextras ){
					$status = 0;
				}
				$Xtalumnocursos = new Xtalumnocursos();
				foreach( $Xtalumnocursos -> find_all_by_sql(
						"select count(*) cuenta from xtalumnocursos
						where registro = ".$registro."
						and periodo = ".$periodoanterior) as $cuenta){
					if( $cuenta -> cuenta == 0 ){
						$status = 0;
					}
				}
				$Bajastemporales = new Bajastemporales();
				foreach( $Bajastemporales -> find_all_by_sql(
						"select * from bajastemporales
						where registro = ".$registro."
						and periodocomienza = ".$periodoanterior) as $bt){
					$status = 2;
				}
				
				$Alumnos = new Alumnos();
				if($Alumnos -> find_first("miReg = ".$registro.
						" and miPerIng = ".$periodoanterior)){ // Cambiar a this -> anterior cuando cambie de periodo
					$status = 2;
				}
				if( $extras > 6 )
					$status = 0;
				if( $titulos > 0 ){
					$Xtextraordinarios = new Xtextraordinarios();
					if( $Xtextraordinarios -> find_first(
							"registro = ".$registro.
							" and periodo = ".$periodoanterior.
							" and calificacion < 70".
							" and tipo = 'T'") ){
						$status = 0; // Si aun tiene titulos de suficiencia sin pasar, no podrá imprimir la ficha...
					}
				}
				
				$HistorialAlumno = new HistorialAlumno();
				if( $HistorialAlumno -> find_first
							( "periodo = ".$periodoactual."
							and registro = ".$registro."
							and situacion_id = 9" )){
					$status = 2;
				}
			}
			return $status;
		} // function imprimir_ficha_pago($registro)
		
		function get_si_al_menos_ha_estado_alguna_vez_en_colomos($registro){
			$Xalumnocursos = new Xalumnocursos();
			if( $Xalumnocursos -> find_first("registro=".$registro) )
				return true;
			else
				return false;
		} // function get_si_al_menos_ha_estado_alguna_vez_en_colomos($registro)
		
		function get_si_al_menos_ha_estado_alguna_vez_en_tonala($registro){
			$Xtalumnocursos = new Xtalumnocursos();
			if( $Xtalumnocursos -> find_first("registro=".$registro) )
				return true;
			else
				return false;
		} // function get_si_al_menos_ha_estado_alguna_vez_en_tonala($registro)
		
		function update_turno($registro, $nuevo_turno){
			foreach($this->find_all_by_sql("
				update alumnos
				set enTurno = '".$nuevo_turno."'
				where miReg = '".$registro."'") as $turno){
			}
			return true;
		} // function update_turno($registro, $nuevo_turno)
		
		function get_turnos_disponibles(){
			$turnos_disponibles = Array();
			
			$turno -> clave = "V";
			$turno -> nombre = "VESPERTINO";
			array_push($turnos_disponibles, $turno);
			
			unset($turno);
			
			$turno -> clave = "M";
			$turno -> nombre = "MATUTINO";
			array_push($turnos_disponibles, $turno);
			
			return $turnos_disponibles;
		} // function get_turnos_disponibles()
		
		function get_nombre_turno($turno){
			switch($turno){
				case "V": return "VESPERTINO";
				case "M": return "MATUTINO";
			}
		} // function get_nombre_turno($turno)
		
		function get_info_xalumnocursos_alumnos_carrera($curso_id, $plantel){
			$alumnos = Array();
			foreach( $this->find_all_by_sql("
					select xal.registro, al.vcNomAlu,
					c.id, c.nombre nombre_carrera,
					xal.calificacion1, xal.faltas1,
					xal.calificacion2, xal.faltas2,
					xal.calificacion3, xal.faltas3,
					xal.calificacion, xal.faltas
					from x".$plantel."alumnocursos xal, alumnos al, carrera c
					where xal.curso_id = '".$curso_id."'
					and xal.registro = al.miReg
					and al.carrera_id = c.id") as $alumno ){
				array_push($alumnos, $alumno);
			}
			return $alumnos;
		} // function get_info_xalumnocursos_alumnos_carrera($curso->id)
		
		function update_stsit($situacion, $registro){
			foreach($this->find_all_by_sql("
				update alumnos
				set stSit = '".$situacion."'
				where miReg = '".$registro."'") as $alumno){
					break;
			}
		} // function update_stsit($situacion, $registro)
		
		function update_enTipo($situacion, $registro){
			foreach($this->find_all_by_sql("
				update alumnos
				set enTipo = '".$situacion."'
				where miReg = '".$registro."'") as $alumno){
					break;
			}
		} // function update_enTipo($situacion, $registro)
		
		function update_tinivel($situacion, $registro){
			foreach($this->find_all_by_sql("
				update alumnos
				set tiNivel = '".$situacion."'
				where miReg = '".$registro."'") as $alumno){
					break;
			}
		} // function update_tinivel($situacion, $registro)
		
        static public function buscar($registro){
            $Objeto = new Alumnos();
            $Objeto -> find_first('miReg = '.$registro);
            return $Objeto;
		}
		
		
        static function buscarXNombre ($nombre=''){
            $Modelo = new Alumnos();
            if($modelo = $Modelo -> find("vcNomAlu like '%".$nombre."%'","order: miReg")){
                return $modelo;
            }else{
                return NULL;
            }
        }
		
			
		/*
		 * Esta funcion sirve para buscar un alumno segun el registro ingresado
	     * @param int $registro Registro que se desea buscar en la tabla de alumnos
		*/
		static public function buscar_registro_alumno($registro)
		{
		  $Objeto = new Alumnos(); 

	      $sql = "SELECT a.enPlantel AS plantel, a.enNivEdu AS nivel, a.stSit AS estado, a.tiNivel AS semestre, a.chGpo AS grupo, a.miReg AS registro, a.miPerIng AS periodo_ingreso, a.vcNomAlu AS nombre_completo,a.nombre AS nombres, a.paterno AS a_paterno, a.materno AS a_materno,IF(a.areadeformacion_id != 0,af.nombreareaformacion,'SIN AREA DE FORMACIÓN') AS areaFormacion, c.nombre AS carrera,ai.estado AS estadoAl,ai.ciudad AS municipio, ai.domicilio AS direccion, ai.correo, ai.telefono, ai.telefono_otro, ai.estadoNac, ai.celular, ai.colonia, ai.curp, ai.cp, ai.sangre, ai.hid, ai.correo AS email, ai.municipioNac, ai.ciudad AS municipio, a.enSexo AS sexo, ai.fnacimiento AS fecha_nacimiento,ai.nombre_padre,ai.nombre_madre,sm.numero_seguro_social AS seguro, sm.depende_economicamente, sm.trabaja, sm.clinica AS numClinica, sm.seguridad_social
				  FROM alumnos a
				  LEFT JOIN carrera c ON c.id = a.carrera_id
				  LEFT JOIN areadeformacion af ON af.carrera_id = a.carrera_id AND af.idareadeformacion = a.areadeformacion_id
				  LEFT JOIN xalumnos_personal ai ON ai.registro = a.miReg
				  LEFT JOIN servicio_medico sm ON sm.registro = a.miReg
				  WHERE a.miReg =".$registro;
			
		  $result = $Objeto->find_all_by_sql($sql);
	 
		  return $result;
		}		
		
	}
?>