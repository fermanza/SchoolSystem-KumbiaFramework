<?php
			
	class AlumnoController extends ApplicationController {
		
		public $antesAnterior=12011;
		public $anterior	= 32011;
		public $actual		= 32012;
		public $proximo		= 12013;
		
		function index(){
			
		  if(Session::get("statusAlumno") == "SinActualizacion"){
			  $this->redirect("alumno/actualizarDatos");
			  return;
		  }
		  else{
			//if(!$this->validarEncuesta()){
				//$this->redirect("alumno/encuestas");
				//return;
			//}
			$registro = Session::get_data('registro');
			//$Respuestas = new DacEncuestaRespuesta();
			//if($Respuestas->find_first("registro = ".$registro." AND completado = 0 AND activo = 1 and encuesta = 10")){
			//	header('Location: http://evaluaciondocente.ceti.mx/');
				//return;
			//}
			
			//if(!$this->validarEvaluacion($registro)){
				//$this->redirect("alumno/evaluacion");
				//return;
			//}
			
			//	Esto de abajo es lo que contiene la funcion validarPreseleccion
			// 	$m = $mpreseleccion->count("registro=".$registro." AND periodo=32009");
			//	if( $m > 0 || Session::get_data('OMISION') == "OK" ){
			//		return true;
			//	}
			

			  $this->redirect("alumno/informacion");
			  return;
			}
		}
		
		function actualizarDatos(){
		  
		  $Alumnos = new AlumnoController();
		  AlumnoController::url_validacion();
		  
		  $this -> validaActualizacion();
		}
		
		function encuesta(){
			$encuestas = new Encuesta();
			$registro = Session::get_data('registro');
			$this->registro = $registro;
			
			$this->n = $encuestas->count("registro=".$registro);
			$n = $encuestas->count("registro=".$registro);
			
			//if($n>0){
				//$this->redirect("alumno/informacion");
			//}
		}
		
		
		function get_info_for_partial_info_alumno($alumno){
			$Alumnos = new Alumnos();
			$KardexIng = new KardexIng();
			
			unset($this->career);
			unset($this->plantel);
			unset($this->turno);
			unset($this->ncreditos);
			unset($this->promedio);
			
			$this->alumno = $alumno;
			// Nombre carrera
			$this->career = $Alumnos->get_careername_from_student($alumno->carrera_id, $alumno->areadeformacion_id);
			// Nombre Plantel
			$this->plantel = $Alumnos->get_nombre_plantel($alumno->enPlantel);
			// Turno Matutino o Vespertino
			$this->turno = $Alumnos->get_turno($alumno->enTurno);
			// Obtener Creditos
			$this->ncreditos = $KardexIng->get_ncreditos($alumno);
			// Obtener promedio
			$this->promedio = $KardexIng->get_average_from_kardex($alumno->miReg);
			
			$Periodos = new Periodos();
			$this->periodo_letra = $Periodos->convertirPeriodo();
			
		} // function get_info_for_partial_info_alumno($alumno)
		
		function registrarencuesta(){
			$encuestas = new Encuesta();
			
			$registro = Session::get_data('registro');
			$this->registro = $registro;
			
			$encuestas->registro = $registro;
			$encuestas->nivel = 'I';
			
			for($i=1;$i<=12;$i++){
				$p = p.$i;
				switch($this->post("p".$i)){
					case 1: $encuestas->$p = "MALO"; break;
					case 2: $encuestas->$p = "REGULAR"; break;
					case 3: $encuestas->$p = "BUENO"; break;
					case 4: $encuestas->$p = "EXCELENTE"; break;
				}
			}
			
			$encuestas->fecha = date("Y-m-d",time());
			
			if($encuestas->save()){
			Flash::success("<center>Gracias por contestar la encuesta, Te recordamos que si eres posible egresado pases por favor a Ventanilla de Octavos para comenzar los tramites de egreso y además hagas la revisión de kardex </center>");
			
			$this->redirect("alumno/encuesta");
			}
			
			
		}
		
		function validarOctavos(){
			$encuestas = new Encuesta();
			$alumnos= new Alumnos();
			$registro = Session::get_data('registro');
			$this->registro = $registro;
			$alumno=$alumnos->find_first("miReg='".$registro."'");		
			$semestre=$alumno->tiNivel;
			
			$n = $encuestas->count("registro=".$registro);
			
			if(($n>0)||($semestre!=8)){
				//$this->redirect("alumno/informacion");
				return true;
			}
			
				
			return false;
		}
	
		function informacion(){
			
			$Alumnos		= new AlumnoController();
			AlumnoController::url_validacion();		  
			
			//if(!$this->validarOctavos()){
				//$this->redirect("alumno/encuesta");
				//return;
			//}
			
			//$this->validarEncuesta();
			
			$visitas	= new VisitasContador();
			// alumno/informacion le corresponde el id 2
			$visitas->incrementar(2);
			
			$registro = Session::get_data('registro');
			//$this->octavos="no";
			/*$Respuestas = new DacEncuestaRespuesta();
			if($Respuestas->find_first("registro = ".$registro." AND completado = 0 AND activo = 1")){
				header('Location: http://evaluaciondocente.ceti.mx/');
				return;
			}*/
			// Validar si ya hizo preselección
			//$this->validarPreseleccion();
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			$periodoanterior = $Periodos->get_periodo_anterior();
			
		    $AlumnosProblemas = new AlumnosProblemas();
			// Si regresa true significa que el alumno deberá acudir a Cálculo
			//if( $alumnosProblemas = $AlumnosProblemas->find_first("registro = ".$registro.
					//" and periodo = '".$periodo."' and aun_con_problemas = 1 and descripcion='OCTAVOS'") ){
				//$this->octavos="si";
			//}
					
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$registro."'");
			
			if($usuario->clave == $registro){
				$this->redirect('alumnos/index');
			}
			// Checar si tiene problemas, con Calculo, sí es así, acudir a Cálculo
			$this->checar_si_alumno_tiene_problemas($registro);
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$xalumnos = new Xalumnos();
			$xalumno = $xalumnos->find_first("registro=".$registro);
			if($xalumno->nombre == ""){
				$this->redirect("alumno/actualizacion");
			}
			
			unset($this->alumno);
			unset($this->especialidad);
			unset($this->tutor);
			unset($this->carrera);
			unset($this->adeformacion);
			
			$Alumnos		= new Alumnos();
			$maestros		= new Maestros();
			$tutorias		= new Tutorias();
			$xalumnocursos	= new Xalumnocursos();
			$xtalumnocursos	= new Xtalumnocursos();
			$KardexIng		= new KardexIng();
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $registro;
			
			$alumno = $alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->get_info_for_partial_info_alumno($alumno);
			
			$tutor = $tutorias->find_first("registro=".$registro);
			if($tutor->nomina == "" || $tutor->nomina == 0){
				$this->tutor = "AUN NO TIENES TUTOR";
			}
			else{
				$tutor = $maestros->find_first("nomina=".$tutor->nomina);
				$this->tutor = $tutor->nombre . " " . $tutor->paterno . " " . $tutor->materno;
			}
			
			unset($this->status);
			$this->status = $Alumnos->imprimir_ficha_pago($registro);
			// $noreinscripcion->0 = $status->0			<- No se puede reinscribir
			// $inscripcion->1 = $status->1				<- Su ficha de pago aparecerá como Inscripción
			// $noreinscripcion->1 = $status->2			<- Su ficha de pago aparecerá como Reinscripción
			
			//$periodo = 12011;
			//$periodoanterior = 32010;
		} // function informacion()

		function cpassword(){
			$id = Session::get_data('registro');
		
			$usuarios = new Usuarios();
			$alumnos = new Alumnos();
			
			$usuario = $usuarios->find_first("registro='".$id."'");
			$alumno = $alumnos->find_first("miReg=".$id);
			
			$password = $this->post("password");
			$cpassword = $this->post("cpassword");
			$correo = $this->post("correo");
			
            if( (!isset($password) || $password == "") &&
					(!isset($cpassword) || $cpassword == "") ){
				$this->redirect('alumnos/index/1');
				return;
			}
			if( $password != $cpassword ){
				$this->redirect('alumnos/index/3');
				return;
			}
			if( $password == $cpassword ){
				if( $usuario->clave == $password ){
					$this->redirect('alumnos/index/2');
					return;
				}
				if( $correo == "" ){
					$this->redirect('alumnos/index/4');
					return;
				}
				$usuario->clave = $password;
				$alumno->correo = $correo;
				$usuario->update();
				$alumno->update();
				$this->redirect('alumno/informacion');
				return;
			}
			$this->redirect('alumno/informacion');
		}
		
		function despreseleccionarcurso($plan=2000){
			$periodo = $this->proximo;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			$preseleccionalumno = new Preseleccionalumno();
			
			$preseleccionalumno->delete("clavemateria='".$this->post("materia")."' AND registro=".$id);
			
			$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
		}
		
		function preseleccionarcurso($plan=2000){
			$periodo = 12009;
			$id = Session::get_data('registro');
			$idplan = Session::get_data('idplan');
			$plan = Session::get_data('idplan');
			
			$banderas = new Banderas();
			
			$x = $banderas->count("registro=".$id);
			
			if($x>0){
					$tmp = $banderas->find_first("registro=".$id);
					$tmp->preseleccion = 1;
					$tmp->save();
			}
			
			if($plan<=5){
				$plan = 2000;
			}
			else{
				$plan = 2007;
			}
			
			unset($this->pre);
			
			$planmaterias = new Planmateria();
			$mishorarios = new Mishorarios();
			$kardexes = new KardexIng();
			$registracursos = new Registracursos();
			
				$tmp = "materia";
				
				if($this->post($tmp)!=""){
				
					//VALIDACION PARA VER QUE EL GRUPO SEA DE UNA MATERIA DEL PLAN DE ESTUDIOS DEL ALUMNO
					$x = $planmaterias->count("clavemateria='".$this->post($tmp)."' AND idplan=".$idplan);
					if($x==0)
						$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
					
					//VALIDACION PARA QUE EL ALUMNO NO PRESELECCIONE LA MISMA DOS VECES
					$preseleccionalumno = new Preseleccionalumno();
					$x = $preseleccionalumno->count("registro=".$id." AND clavemateria='".$this->post($tmp)."' AND periodo=".$periodo);
					if($x>0){
						$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
						return;
					}
					
					
					//VALIDACION PARA QUE EL ALUMNO PRESELECCIONE MATERIAS DONDE SE CUMPLEN CIERTOS PRERREQUISITOS
					$mat = $planmaterias->find_first("clavemateria='".$this->post($tmp)."' AND idplan=".$idplan);
					
					if($mat->prerrequisito != "-"){
					
						$kardex = $kardexes->find("registro=".$id);
						$registracurso = $registracursos->find("mireg=".$id);
					
						//OBTENER CREDITOS AL TERMINO DEL SEMESTRE
						$kardex = $kardexes->find("registro=".$id);
						$registracurso = $registracursos->find("mireg=".$id);
						
						if($registracurso) foreach($registracurso as $regcur){
							$curso = $regcur->curso;
							$tmpx 	= $mishorarios->find_first("clavecurso='".$curso."'");
							$clave = $tmpx->clavemat;
							$mat = $planmaterias->find_first("idplan='".$idplan."' AND clavemateria='".$clave."'");
							$contador += $mat->creditos;
						}
						
						$bandera = 0;
						if($kardex) foreach($kardex as $calificacion){
							$clave = $calificacion->clavemat;
							if($clave == $this->post($tmp)){
								$bandera = 1;
							}
							$mat = $planmaterias->find_first("idplan='".$idplan."' AND clavemateria='".$clave."'");
							$contador += $mat->creditos;
						}
						
						//SI  LA MATERIA YA ESTA EN KARDEX NO PRESELECCIONARLA
						if($bandera==1)
							$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
						
						$tcreditos = $contador;
						$mat = $planmaterias->find_first("idplan='".$idplan."' AND clavemateria='".$this->post($tmp)."'");
					
						if(is_numeric($mat->prerrequisito)){
							if($mat->prerrequisito > $tcreditos){
								$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
							}
						}
						else{
							$x = $kardexes->count("registro=".$id." AND clavemat='".$mat->prerrequisito."'");
							if($x==0){
								$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
							}
						}
					}
					
					$preseleccionalumno = new Preseleccionalumno();
					$preseleccionalumno->registro = $id;
					$preseleccionalumno->clavemateria = $this->post($tmp);
					$preseleccionalumno->fecha = time();
					$preseleccionalumno->periodo = $this->proximo;
					$preseleccionalumno->create();
				}
			
			
			$this->redirect('alumno/preseleccion/'.$this->post("plan").'/'.$this->post("modelo"));
		} // function preseleccionarcurso($plan=2000)
		
		function grupos($clavemateria,$plan=2000){
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			unset($this->cursos);
			
			
			
			$id = Session::get_data('registro');
			$periodo = "32008";
			
			$maestros = new Maestros();
			$materias = new Materia();
			$precurso = new Precurso();
			
			
			$total = 0;
			
			if($periodo[0]=='3'){
				$this->periodo = "FEB - JUN, ";
				$this->periodo .= (substr($periodo,1,4)+1);
			}
			else{
				$this->periodo = "AGO - DIC, ";
				$this->periodo .= substr($periodo,1,4);
			}
			
			$precursos = $precurso->find("clavemateria='".$clavemateria."'");
			
			$i=0;
			if($precursos) foreach($precursos as $curso){ 
				$this->cursos[$i] = $curso;
				$this->materia[$i] = $materias->find_first("clave='".$curso->clavemateria."'");
				$this->profesor[$i] = $maestros->find_first("nomina='".$curso->registroprofesor."'");
				$i++;
			}

		}
		
		function oferta(){
			
			/*if(!$this->validarEncuesta()){
				$this->redirect("alumno/encuestas");
				return;
			}*/
			
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos->find_first("registro=".$id);
			
			//if($xalumno->nombre == ""){
			//	$this->redirect("alumno/actualizacion");
			//}
			
			if($usuario->clave == $id){
				$this->redirect('alumnos/index');
			}
		}
		
		function seleccion(){
			
			//if(!$this->validarEncuesta()){
			//	$this->redirect("alumno/encuestas");
				//return;
			//}
			$this->validarEncuesta();
			
			$this->redirect("alumnoselecc/");
		} // function seleccion()
		
		function preseleccion(){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this->materia);
			unset($this->profesor);
			unset($this->cursos);
			unset($this->pmaterias);
			
			//public $actual = "12009";
			//public $proximo = "32009";
			$periodo = $this->proximo;
			
			$materias = new Materia();
			$maestros = new Maestros();
			
			$this->registro = Session::get_data('registro');
			$this->nombre = Session::get_data('nombre');
			$this->carrera = Session::get_data('nombrecarrera');
			$plan = Session::get_data('idplan');
			
			$preseleccionalumno = new Preseleccionalumno();
			
			$pas = $preseleccionalumno->find("registro=".$this->registro." AND periodo=".$periodo." ORDER BY clavemateria");
			
			$i=0;
			if($pas) foreach($pas as $p){
				$this->cursos[$i] = $p; // Aqui guarda lo que ha escogido el alumno
				
				if($plan<=5)
					$plan = 2000;
				else
					$plan = 2007;
					
				$this->materia[$i] = $materias->find_first("clave='".$p->clavemateria."' AND plan=".$plan);
					
				$i++;
			}
			
			
			$id = Session::get_data('registro');
			$idplan = Session::get_data('idplan');
			$idcarrera = Session::get_data('idcarrera');
			
			switch($idplan){
				case 1: 
				case 2: 
				case 3: 
				case 4: 
				case 5: $this->modelo = 2000; break;
				case 6: 
				case 7: 
				case 8: $this->modelo = 2007; break;
			}
			
			$alumnos = new Alumnos();
			$planes = new Plan();
			$planmateria = new Planmateria();
			$kardexes = new KardexIng();
			$materias = new Planmateria();
			$material = new Materia();
			$precurso = new Precurso();
			$registracursos = new Registracursos();
			$mishorarios = new Mishorarios();
			$preseleccionalumno = new Preseleccionalumno();
			$xalumnocursos = new Xalumnocursos();
			
			$id = Session::get_data('registro');
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$alumno = $alumnos->find_first("miReg=".$id);
			
						//OBTENER CREDITOS AL TERMINO DEL SEMESTRE
						$kardex = $kardexes->find("registro=".$id);
						$registracurso = $registracursos->find("mireg=".$id);
						
						if($registracurso) foreach($registracurso as $regcur){
							$curso = $regcur->curso;
							$tmp = $mishorarios->find_first("clavecurso='".$curso."'");
							$clave = $tmp->clavemat;
							$mat = $materias->find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat->creditos;
						}
						
						if($kardex) foreach($kardex as $calificacion){
							$clave = $calificacion->clavemat;
							$mat = $materias->find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat->creditos;
						}
						
						$tcreditos = $contador;
			
			$pmaterias = $planmateria->find("idplan=".$idplan." ORDER BY clavemateria");
			
			$contador=0;
			$i=0;$j=0;$x=0;$y=0;$z=0;
			if($pmaterias) foreach($pmaterias as $pmateria){
				//SI ESTA EN KARDEX, IGNORAR LA MATERIA
								
				$x = $kardexes->count("registro=".$id." AND clavemat='".$pmateria->clavemateria."'");
				if($x>0){
					continue;
				}
				
				$todos = $preseleccionalumno->find("registro=".$id." AND periodo=".$periodo);
				$bandera=0;
				if($todos) foreach($todos as $t){
					$tmp = $planmateria->find_first("idplan=".$idplan." AND clavemateria='".$t->clavemateria."' ORDER BY clavemateria");
					if($tmp->clavemateria == $pmateria->prerrequisito)
						$bandera=1;
				}
				if($bandera==1)
					continue;
				
				
				$x = $preseleccionalumno->count("registro=".$id." AND clavemateria='".$pmateria->clavemateria."'"." AND periodo=".$periodo);
				if($x>0){
					continue;
				}
				
				
				$y = $preseleccionalumno->count("registro=".$id." AND clavemateria='".$pmateria->prerrequisito."'"." AND periodo=".$periodo);
				if($y>0){
					continue;
				}
			
				//SI LA MATERIA NO TIENEN NINGUN PRERREQUISITO,,AGREGARLA A LAS MATERIAS DISPONIBLES
				if($pmateria->prerrequisito==0){
					$this->pmaterias[$i] = $pmateria;
					$y = $preseleccionalumno->count("registro=".$id." AND clavemateria='".$pmateria->prerrequisito."' AND periodo=".$periodo);
					$i++;
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($pmateria->prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						
						$y = $kardexes->count("registro=".$id." AND clavemat='".$pmateria->prerrequisito."'");
						/*
						if($y==0){
							$xcursos = new Xcursos();
							$cursos = $xcursos->find("materia='".$pmateria->prerrequisito."'");
						
							if($cursos) foreach($cursos as $curso){
								$y += $xalumnocursos->count("registro=".$id." AND curso='".$curso->clavecurso."'");
							}
						}
						*/
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						//$tmp = $registracursos->find("mireg=".$id."");
						$tmp = $xalumnocursos->find("registro=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$xcursos = new Xcursos();
							$z = $xcursos->count("clavecurso='".$t->curso."' AND materia='".$pmateria->prerrequisito."'");
							if($z>0) break;
						}
						
						if($y>0 || $z>0){
							$this->pmaterias[$i] = $pmateria;
							$i++;
						}
						continue;
					}
					else{
						if($pmateria->prerrequisito <= $tcreditos){
							$this->pmaterias[$i] = $pmateria;
							$i++;
						}
					}
				}
			}
			$i=0;
			foreach($this->pmaterias as $pmat){
				$material2 = $material->find_first("clave='".$pmat->clavemateria."'");
				$this->pnombres[$i] = $material2->nombre;
				$i++;
			}
		} // function preseleccion()
		
		function calificaciones($registro=0, $periodo=0, $validar=0){
			if($validar==1){
				$this -> set_response("view");
			}
			
			$Alumnos = new AlumnoController();
			//Valida que no ingres por URL en caso de estar BD,BT,SIN PAGO, EG
			if($validar ==0){
				AlumnoController::url_validacion();
			}
			
			if ($validar==1){
			}else{
			/*if(!$this->validarOctavos()){
				$this->redirect("alumno/encuesta");
				return;
			} */ 
			}
			
			$visitas	= new VisitasContador();
			// alumno/calificaciones le corresponde el id 4
			$visitas->incrementar(4);
			
			if( $validar==0 ){
				$registro = Session::get_data('registro');
			}
			
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$registro."'");
			
			$xalumnos = new Xalumnos();
			$xalumno = $xalumnos->find_first("registro=".$registro);
			
			//if($xalumno->nombre == ""){
				//$this->redirect("alumno/actualizacion");
			//}
			
			if(Session::get_data('tipousuario')=="ALUMNO"){
			/*$this->validarEncuesta();
			
			$Respuestas = new DacEncuestaRespuesta();
			if($Respuestas->find_first("registro = ".$registro." AND completado = 0 AND activo = 1")){
				header('Location: http://evaluaciondocente.ceti.mx/');
				return;
			}*/
			}
			
			if($usuario->clave == $registro){
				$this->route_to('controller: alumnos','action: index');
			}
			
			$Periodos = new Periodos();
			if( $validar==0 ){
				$periodo = $Periodos->get_periodo_actual();
			}
			
			if(Session::get_data('tipousuario')!="ALUMNO" && $validar==0){
				$this->route_to('controller: general','action: inicio');
			}
			
			if(!$this->validarEvaluacion($registro)){
				//$this->route_to('controller: alumno','action: evaluacion');
				//return;
			}
			
			// Validar si ya hizo preselección
			//$this->validarPreseleccion();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			unset($this->xalumnocursos);
			unset($this->plantel);
			
			unset($this->titulosAnteriores);
			unset($this->titulosAntMateria);
			unset($this->titulosAntProf);
			unset($this->califTitAnteriores);
			unset($this->ncreditos);
			unset($this->avisoServicioSocial);
			
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();
			$Xextraordinarios = new Xextraordinarios();
			$Xtextraordinarios = new Xtextraordinarios();
			
			$maestros = new Maestros();
			$materias = new Materia();
			$Alumnos = new Alumnos();
			$Xalumnocursos = new Xalumnocursos();
			$Xtalumnocursos = new XtAlumnocursos();
			$KardexIng = new KardexIng();
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $registro;
			
			$this->alumno = $alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->get_info_for_partial_info_alumno($alumno);
			
			if( $this->ncreditos > 285 )
				$this->avisoServicioSocial = "Si a&uacute;n no te encuentras
				haciendo haciendo pr&aacute;cticas profesionales,
				es necesario que acudas al departamento de apoyo acad&eacute;mico con la
				encargada del &aacute;rea, para mayor informaci&oacute;n.";
			
			$cursos = $Xalumnocursos->get_materias_semestre_by_periodo_colomos_tonala($registro, $periodo);
			$this->cursos = $Xalumnocursos->count("registro=".$registro." AND periodo = '".$periodo."'");
			$this->cursos += $Xtalumnocursos->count("registro=".$registro." AND periodo = '".$periodo."'");
			$this->cursillos = $cursos;
			$this->extras = $Xextraordinarios->count("registro=".$registro." AND periodo = '".$periodo."'");
			$this->extras += $Xtextraordinarios->count("registro=".$registro." AND periodo = '".$periodo."'");
			
			if($this->cursos > 0 ){
				$i=0;
				foreach($cursos as $curso){
					
					if( !$xccurso = $Xccursos->find_first("id='".$curso->curso_id."' AND periodo = '".$periodo."'") ){
						$xccurso = $Xtcursos->find_first("id='".$curso->curso_id."' AND periodo = '".$periodo."'");
					}
					
					$this->horas[$i] = $xccurso->horas1 + $xccurso->horas2 + $xccurso->horas3;
					$this->mihorario[$i] = $xccurso;
					$this->profesor[$i] = $maestros->find_first("nomina= ".$xccurso->nomina);
					$this->materia[$i] = $materias->find_first("clave= '".$xccurso->materia."' AND carrera_id = ".$alumno->carrera_id);
				
					unset($xalumnocursos);
					if(!$xalumnocursos = $Xalumnocursos->find_first("id='".$curso->id."' AND periodo ='".$periodo."'")){
						$xalumnocursos = $Xtalumnocursos->find_first("id='".$curso->id."' AND periodo ='".$periodo."'");
					}
					
					if(!$next = $Xextraordinarios->count("curso_id='".$curso->curso_id."' AND registro=".$registro." AND periodo=".$periodo)){
						$next = $Xtextraordinarios->count("curso_id='".$curso->curso_id."' AND registro=".$registro." AND periodo=".$periodo);
					}
					
					unset($extraordinarios);
					if($next>0){
					
						if(!$extraordinarios = $Xextraordinarios->find_first("curso_id='".$curso->curso_id."' AND registro=".$registro)){
							$extraordinarios = $Xtextraordinarios->find_first("curso_id='".$curso->curso_id."' AND registro=".$registro);
						}
						
						if($extraordinarios->tipo == "E"){
						
							$this->extra[$i] = $extraordinarios->calificacion;
							
							if($this->extra[$i]==300)
								$this->extra[$i] = "NR";
								
							if($this->extra[$i]==999)
								$this->extra[$i] = "NP";
							
							$this->titulo[$i] = " - ";
							
						}
						else{
							$this->titulo[$i] = $extraordinarios->calificacion;
							
							if($this->titulo[$i]==300)
								$this->titulo[$i] = "NR";
								
							if($this->titulo[$i]==999)
								$this->titulo[$i] = "NP";
							
							$this->extra[$i] = " - ";
						}
					}
					else{
						if($xalumnocursos->situacion == "EXTRAORDINARIO" || $xalumnocursos->situacion == "EXTRAORDINARIO FALTAS"){ 
							$this->extra[$i] = " - ";
							$this->titulo[$i] = " - ";
						}
						else{
							if($xalumnocursos->situacion == "TITULO POR CALIFICACION" || $xalumnocursos->situacion == "TITULO FALTAS" ||
								$xalumnocursos->situacion == "TITULO POR FALTAS" || $xalumnocursos->situacion == "TITULO POR NO REGISTRO"){
								$this->extra[$i] = " - ";
								$this->titulo[$i] = " - ";
							}
							else{
								$this->extra[$i] = " - ";
								$this->titulo[$i] = " - ";
							}
						}
					}
					$this->xalumnocursos[$i] = $xalumnocursos;
					
					$i++;
				}
			}
			
			// Para bloquearle el pago de titulos si un alumno tiene al menos una materia en Baja Definitiva
			$this->BajaDefinitiva = false;
			$Xalumnocursos = new Xalumnocursos();
			$this->BajaDefinitiva = $Xalumnocursos->get_materia_con_baja_definitiva($registro);
			
			unset($this->cursos_intersemestrales_alumno);
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$this->cursos_intersemestrales_alumno = $IntersemestralAlumnos->get_cursos_de_un_alumno($registro);
			
			// Apartir de aquí hago una consulta al periodo anterior para
			//poder mostrar las fichas de títulos de suficiencia.
			
			$xcursos2			= new Xccursos();
			$materias2			= new Materia();
			$xalumnocursos2		= new Xalumnocursos();
			$xextraordinarios2	= new Xextraordinarios();
			$alumnos2			= new Alumnos();
			$maestros2			= new Maestros();
			$kardexing			= new KardexIng();
			
			$i = 0;
			$alumnos22 = $alumnos2->find_first( "miReg = ".$registro );
			
			if( $alumnos22->miPerIng != $periodo ){
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual_();
				$periodo_anterior = $Periodos -> get_periodo_anterior();
				$periodo_antesanterior = $Periodos -> get_periodo_antesanterior();
				
				foreach( $xalumnocursos2->find_all_by_sql
					     ( "Select xc.materia, xc.nomina, xal.*
							from xalumnocursos xal, xccursos xc
							where xal.curso_id = xc.id
							and xal.periodo = ".$periodo_anterior."
							and xal.registro = ".$registro."
							and xal.calificacion < 70" ) as $xalumncur2 ){
					//echo "WTF: ".$xalumncur2->curso." ".$xalumncur2->materia." m<br />";
					
					if( $xextraordinarios2->find_first
						( "curso_id = '".$xalumncur2->curso_id."'".
							" and registro = ".$registro."
								and periodo = ".$periodo ) ){
						//echo "Xtra<br />";
						if( $xalcur = $xalumnocursos2->find_all_by_sql
								 ( "Select xc.materia, xal.*
									from xalumnocursos xal, xccursos xc
									where xal.curso_id = xc.id
									and xal.periodo = ".$periodo."
									and xc.materia = '".$xalumncur2->materia."'"."
									and xal.registro = ".$registro ) ){
							// Si la encuentra quiere decir que si la cargo,
							//por lo que no es necesario ponerla aqui
							//ya que, ya fue detectada con anterioridad.
							
							
							//echo "No cae: ".$xalcur->materia." ".$xalcur->curso."<br />";
						}
						else{
							//echo "si cae<br />";
							if( $xalumnocursos2->find_all_by_sql
								  ( "Select xc.materia, xal.* from xalumnocursos xal, xccursos xc
									where xal.curso_id = xc.id
									and xc.materia = '".$xalumncur2->materia."'"."
									and xal.registro = ".$registro."
									and xal.periodo = ".$periodo_antesanterior ) ){
								// Si encuentra información, significa que también
								//había cursado esa materia 2 periodos antes del actual
								//por lo que ya no tendrá oportunidad de imprimir su
								//titulo de suficiencia
							}
							else{
								if( $kardexing->find_first
										( "clavemat = '".$xalumncur2->materia."'"."
											and registro = ".$registro ) ){
									// Si encuentra la materia en el kardex del alumno
									//no es necesario presentarsela, ya que ya la curso.
								}
								else{
									$alumnn = new Alumnos();
									if( $alumnn->find_first( "miReg = ".$registro." and enPlan = 'PE07'" ) ){
										$materiiaa = new Materia();
										if( $materiiaa->find_all_by_sql
												 ( "Select m.clave, m.plan
													From materia m, xalumnocursos xal, xccursos xc
													where xal.registro = ".$registro."
													and xal.periodo = ".$periodo_anterior."
													and xal.curso_id = xc.id
													and xc.materia = m.clave
													and m.plan = 2000" ) ){
											// Si entra aqui, significa que hizo cambio de plan...
											//por lo que no tendrá que hacer titulos pasados.
										}
										else{
										
											$this->titulosAnteriores[$i] = $xalumncur2;
											$this->titulosAntMateria[$i] = 
													$materias2->find_first( "clave = '".$xalumncur2->materia."'" );
											$this->titulosAntProf[$i] = 
													$maestros2->find_first( "nomina = ".$xalumncur2->nomina );
											$i ++;
											
										}
									}
									else{
										$xextra	= new Xextraordinarios();
										$this->titulosAnteriores[$i] = $xalumncur2;
										$this->titulosAntMateria[$i] = 
												$materias2->find_first( "clave = '".$xalumncur2->materia."'" );
										$this->titulosAntProf[$i] = 
												$maestros2->find_first( "nomina = ".$xalumncur2->nomina );
										$i ++;
									}
								}
							}
						}
					}
				}
			} // if( $alumnos22->miPerIng != $periodo )
			//exit(1);
		} // function calificaciones()
		
		function calificacionesT(){
			$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$registro."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos->find_first("registro=".$registro);
			
			//$this->validarEncuesta();
			
			//if($xalumno->nombre == ""){
				//$this->redirect("alumno/actualizacion");
			//}
			if($usuario->clave == $registro){
				$this->route_to('controller: alumnos','action: index');
			}
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual_();
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->route_to('controller: general','action: inicio');
			}
			
			/*if(!$this->validarOctavos()){
				$this->redirect("alumno/encuesta");
				return;
			}*/
			
			if(!$this->validarEvaluacion($registro)){
				//$this->route_to('controller: alumno','action: evaluacion');
				return;
			}
			
			// Validar si ya hizo preselección
			//$this->validarPreseleccion();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			unset($this->calificacion);
			unset($this->plantel);
			
			unset($this->titulosAnteriores);
			unset($this->titulosAntMateria);
			unset($this->titulosAntProf);
			unset($this->califTitAnteriores);
			unset($this->ncreditos);
			unset($this->avisoServicioSocial);
			
			$xcursos = new Xtcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$Alumnos = new Alumnos();
			$calificaciones = new Xtalumnocursos();
			$xextraordinarios = new Xtextraordinarios();
			$KardexIng = new KardexIng();
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $registro;
			
			$this->alumno = $alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->get_info_for_partial_info_alumno($alumno);
			
			foreach( $KardexIng->find_all_by_sql("
					select sum(m.creditos) as sumadecreditos
					from kardex_ing k
					inner join materia m on k.clavemat = m.clave
					and k.registro = ".$alumno->miReg."
					and m.carrera_id = ".$alumno->carrera_id) as $king ){
				$this->ncreditos = $king->sumadecreditos;
			}
			if( $this->ncreditos > 285 )
				$this->avisoServicioSocial = "Si a&uacute;n no te encuentras
				haciendo haciendo pr&aacute;cticas profesionales,
				es necesario que acudas al departamento de apoyo acad&eacute;mico con la
				encargada del &aacute;rea, para mayor informaci&oacute;n.";
			
			$cursos = $calificaciones->get_materias_semestre_by_periodo($registro, $periodo);
			$this->cursos = $calificaciones->count("registro=".$registro);
			$this->cursillos = $cursos;
			$this->extras = $xextraordinarios->count("registro=".$registro);
			
			if($this->cursos > 0 ){
				$i=0;
				foreach($cursos as $curso){
					$tmp = $xcursos->find_first("id='".$curso->curso_id."' AND periodo=".$periodo);
					
					$this->horas[$i] = $tmp->horas1 + $tmp->horas2 + $tmp->horas3;
					$this->mihorario[$i] = $tmp;
					$this->profesor[$i] = $maestros->find_first("nomina=".$tmp->nomina."");
					$this->materia[$i] = $materias->find_first("clave='".$tmp->materia."' and carrera_id = ".$alumno->carrera_id);
				
					unset($calificacion);
					$calificacion = $calificaciones->find_first("id='".$curso->id."'");
					
					$next = $xextraordinarios->count
							("curso_id='".$curso->curso_id."' 
								AND registro=".$registro." 
									AND periodo=".$periodo);
					
										unset($extraordinarios);
					if($next>0){
						$extraordinarios = $xextraordinarios->find_first
								("curso_id='".$curso->curso_id."' AND registro=".$registro);
						
						if($extraordinarios->tipo == "E"){
						
							$this->extra[$i] = $extraordinarios->calificacion;
							
							if($this->extra[$i]==300)
								$this->extra[$i] = "NR";
								
							if($this->extra[$i]==999)
								$this->extra[$i] = "NP";
							
							$this->titulo[$i] = " - ";
							
						}
						else{
							$this->titulo[$i] = $extraordinarios->calificacion;
							
							if($this->titulo[$i]==300)
								$this->titulo[$i] = "NR";
								
							if($this->titulo[$i]==999)
								$this->titulo[$i] = "NP";
							
							$this->extra[$i] = " - ";
						}
					}
					else{
						if($calificaciones->situacion == "EXTRAORDINARIO" || $calificaciones->situacion == "EXTRAORDINARIO FALTAS"){ 
							$this->extra[$i] = " - ";
							$this->titulo[$i] = " - ";
						}
						else{
							if($calificaciones->situacion == "TITULO POR CALIFICACION" || $calificaciones->situacion == "TITULO FALTAS" ||
								$calificaciones->situacion == "TITULO POR FALTAS" || $calificaciones->situacion == "TITULO POR NO REGISTRO"){
								$this->extra[$i] = " - ";
								$this->titulo[$i] = " - ";
							}
							else{
								$this->extra[$i] = " - ";
								$this->titulo[$i] = " - ";
							}
						}
					}
					$this->calificacion[$i] = $calificacion;
				
					$i++;
				}
			}
			// Para bloquearle el pago de titulos si un alumno tiene al menos una materia en Baja Definitiva
			$this->BajaDefinitiva = false;
			$Xalumnocursos = new Xtalumnocursos();
			$this->BajaDefinitiva = $Xalumnocursos->get_materia_con_baja_definitiva($registro);
			
			unset($this->cursos_intersemestrales_alumno);
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$this->cursos_intersemestrales_alumno = $IntersemestralAlumnos->get_cursos_de_un_alumno($registro);
			
			// Apartir de aquí hago una consulta al periodo anterior para
			//poder mostrar las fichas de títulos de suficiencia.
			
			$xcursos2			= new Xtcursos();
			$materias2			= new Materia();
			$xalumnocursos2		= new Xtalumnocursos();
			$xextraordinarios2	= new Xtextraordinarios();
			$alumnos2			= new Alumnos();
			$maestros2			= new Maestros();
			$kardexing			= new KardexIng();
			
			$i = 0;
			$alumnos22 = $alumnos2->find_first( "miReg = ".$registro );
			if( $alumnos22->miPerIng != $periodo ){
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual_();
				$periodo_anterior = $Periodos -> get_periodo_anterior();
				$periodo_antesanterior = $Periodos -> get_periodo_antesanterior();
				
				foreach( $xalumnocursos2->find_all_by_sql
					     ( "Select xc.materia, xc.nomina, xal.*
							from xtalumnocursos xal, xtcursos xc
							where xal.curso_id = xc.id
							and xal.periodo = ".$periodo_anterior."
							and xal.registro = ".$registro."
							and xal.calificacion < 70" ) as $xalumncur2 ){
					// echo "WTF: ".$xalumncur2->curso." ".$xalumncur2->materia." ".$registro." m<br />";
					
					if( $xextraordinarios2->find_first
						( "curso_id = '".$xalumncur2->curso_id."'".
							" and registro = ".$registro."
								and periodo = ".$periodo ) ){
						// echo "Xtra<br />";
						if( $xalcur = $xalumnocursos2->find_all_by_sql
								 ( "Select xc.materia, xal.*
									from xtalumnocursos xal, xtcursos xc
									where xal.curso_id = xc.id
									and xal.periodo = ".$periodo."
									and xc.materia = '".$xalumncur2->materia."'"."
									and xal.registro = ".$registro ) ){
							// Si la encuentra quiere decir que si la cargo,
							//por lo que no es necesario ponerla aqui
							//ya que, ya fue detectada con anterioridad.
							
							//echo "No cae: ".$xalcur->materia." ".$xalcur->curso."<br />";
						}
						else{
							//echo "si cae<br />";
							if( $xalumnocursos2->find_all_by_sql
								  ( "Select xc.materia, xal.*
									from xtalumnocursos xal, xtcursos xc
									where xal.curso_id = xc.id
									and xc.materia = '".$xalumncur2->materia."'"."
									and xal.registro = ".$registro."
									and xal.periodo = ".$periodo_antesanterior ) ){
								// Si encuentra información, significa que también
								//había cursado esa materia 2 periodos antes del actual
								//por lo que ya no tendrá oportunidad de imprimir su
								//titulo de suficiencia
							}
							else{
								if( $kardexing->find_first
										( "clavemat = '".$xalumncur2->materia."'"."
											and registro = ".$registro ) ){
									// Si encuentra la materia en el kardex del alumno
									//no es necesario presentarsela, ya que ya la curso.
								}
								else{
									$alumnn = new Alumnos();
									if( $alumnn->find_first( "miReg = ".$registro." and enPlan = 'PE07'" ) ){
										$materiiaa = new Materia();
										if( $materiiaa->find_all_by_sql
												 ( "Select m.clave, m.plan
													From materia m, xtalumnocursos xal, xtcursos xc
													where xal.registro = ".$registro."
													and xal.periodo = ".$periodo_anterior."
													and xal.curso_id = xc.id
													and xc.materia = m.clave
													and m.plan = 2000" ) ){
											// Si entra aqui, significa que hizo cambio de plan...
											//por lo que no tendrá que hacer titulos pasados.
										}
										else{
											$this->titulosAnteriores[$i] = $xalumncur2;
											$this->titulosAntMateria[$i] = 
													$materias2->find_first( "clave = '".$xalumncur2->materia."'" );
											$this->titulosAntProf[$i] = 
													$maestros2->find_first( "nomina = ".$xalumncur2->nomina );
											$i ++;
										}
									}
									else{
										$xextra	= new Xextraordinarios();
										$this->titulosAnteriores[$i] = $xalumncur2;
										$this->titulosAntMateria[$i] = 
												$materias2->find_first( "clave = '".$xalumncur2->materia."'" );
										$this->titulosAntProf[$i] = 
												$maestros2->find_first( "nomina = ".$xalumncur2->nomina );
										$i ++;
									}
								}
							}
						}
					}
				}
			} // if( $alumnos22->miPerIng != $periodo )
			// exit(1);
		} // function calificacionesT(){
		
		function kardex(){

		  $Alumnos		= new AlumnoController();
		  //Valida que no ingres por URL en caso de estar BD,BT,SIN PAGO, EG
		  AlumnoController::url_validacion();
		  
		   /*if(!$this->validarOctavos()){
				$this->redirect("alumno/encuesta");
				return;
			}
		  
			$this->validarEncuesta();
			*/
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$visitas	= new VisitasContador();
			// alumno/kardex le corresponde el id 6
			$visitas->incrementar(6);
			
			$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$registro."'");
			
			if($usuario->clave == $registro){
				$this->redirect('alumnos/index');
			}
			
			//////////////////////////////////////////////
			$Alumnos = new Alumnos();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			
			unset($this->alumno);
			unset($this->carrera);
			unset($this->kardex);
			unset($this->cuantasMaterias);
			unset($this->promedio);
			unset($this->avancePeriodos);
			
			$this->alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->carrera = $Carrera->get_nombre_carrera($this->alumno->carrera_id);
			$this->kardex = $KardexIng->get_completeKardex($registro, 0);
			$this->cuantasMaterias = $KardexIng->get_count_kardex_from_student($registro);
			
			$this->ncreditos = $this->kardex[0][0]->sumaCreditos;
			$this->promedio = $this->kardex[0][0]->promedioTotal;
			$i = 0;
			while( isset($this->kardex[$i][0]->periodosBuenos) ){
				$this->avancePeriodos[$i] = $this->kardex[$i][0]->periodosBuenos;
				$i++;
			}
			unset($this->editable);
			$this->editable = 2;
		} // function kardex()
		
		function epseleccion(){
			$periodo = $this->actual;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$id = Session::get_data('registro');
			$seleccion = new Seleccionalumno();
			
			$tmp = $seleccion->find_first($this->post("id"));
			$tmp->delete();
			
			$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex = new KardexIng();
			
			$pmaterias = $planesmaterias->find("idplan=".$this->idplan." ORDER BY clavemateria");
			
			//QUITAR MATERIAS QUE YA ESTAN EN KARDEX O COMO MATERIA SOLICITADA PARA KARDEX
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				$n = $kardex->count("registro=".$id." AND clavemat='".$p->clavemateria."'");
				if($n==0){
					$n = $xpkardex->count("registro=".$id." AND materia='".$p->clavemateria."' and id > 27315");
					if($n==0){
						$this->pmaterias[$xxx] = $p;
						$xxx++;
					}
				}
			}
			
			$this->redirect('alumno/seleccion2');
		} // function epseleccion()
		
		function deseleccionar(){
			$periodo = $this->actual;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$horarios = new Xcursos();
			$xcursos = new Xalumnocursos();
			
			$temporal = $xcursos->find_first("id=".$this->post("idcurso"));
			
			$xcurso = $horarios->find_first("id=".(substr($temporal->curso,3)+0));
			
				$xagendas = new Xalumnoagenda();
				$xagenda = $xagendas->find_first("registro=".$id." AND periodo=".$periodo);
				
				$counter = 0;
				foreach ( $mascursos = $xcursos->find ("registro = ".$id." and periodo = ".$periodo) as $mascur)
					$counter++;
				
				if ( $counter == 1 )
					$xagenda->max1cruce = '0';
				
				for($i=$xcurso->lunesi;$i<$xcurso->lunesf;$i++){
					$tmp = "l".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->martesi;$i<$xcurso->martesf;$i++){
					$tmp = "m".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->miercolesi;$i<$xcurso->miercolesf;$i++){
					$tmp = "i".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->juevesi;$i<$xcurso->juevesf;$i++){
					$tmp = "j".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->viernesi;$i<$xcurso->viernesf;$i++){
					$tmp = "v".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->sabadoi;$i<$xcurso->sabadof;$i++){
					$tmp = "s".$i;
					$xagenda->$tmp = '0';
				}
				
				//////////////////////////////////////////////////////////
				for($i=$xcurso->lunesi2;$i<$xcurso->lunesf2;$i++){
					$tmp = "l".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->martesi2;$i<$xcurso->martesf2;$i++){
					$tmp = "m".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->miercolesi2;$i<$xcurso->miercolesf2;$i++){
					$tmp = "i".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->juevesi2;$i<$xcurso->juevesf2;$i++){
					$tmp = "j".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->viernesi2;$i<$xcurso->viernesf2;$i++){
					$tmp = "v".$i;
					$xagenda->$tmp = '0';
				}
				
				for($i=$xcurso->sabadoi2;$i<$xcurso->sabadof2;$i++){
					$tmp = "s".$i;
					$xagenda->$tmp = '0';
				}
				
			
			$xagenda->save();
			$temporal->delete();
			
			$xcurso->disponibilidad += 1;
				$xcurso->save();
				
			
			$this->redirect('alumno/seleccion3');
		}
		
		function seleccionar(){
			$periodo = $this->actual;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$xcurso = $xcursos->find_first("id=".$this->post("grupo"));
			
			if($xcurso->disponibilidad <= 0){
				$this->redirect('alumno/seleccion3/1');
				return;
			}
			else{
				$xagendas = new Xalumnoagenda();
				$xagenda = $xagendas->find_first("registro= ".$id." AND periodo= ".$periodo);
				
				$aux = 0;
				
				for($i=$xcurso->lunesi;$i<$xcurso->lunesf;$i++){
					$tmp = "l".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->martesi;$i<$xcurso->martesf;$i++){
					$tmp = "m".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->miercolesi;$i<$xcurso->miercolesf;$i++){
					$tmp = "i".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->juevesi;$i<$xcurso->juevesf;$i++){
					$tmp = "j".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->viernesi;$i<$xcurso->viernesf;$i++){
					$tmp = "v".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->sabadoi;$i<$xcurso->sabadof;$i++){
					$tmp = "s".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				
				// Checar los lunesi2, martesi2, miercolesi2, juevesi2, viernesi2, sabadoi2
				for($i=$xcurso->lunesi2;$i<$xcurso->lunesf2;$i++){
					$tmp = "l".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->martesi2;$i<$xcurso->martesf2;$i++){
					$tmp = "m".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->miercolesi2;$i<$xcurso->miercolesf2;$i++){
					$tmp = "i".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->juevesi2;$i<$xcurso->juevesf2;$i++){
					$tmp = "j".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->viernesi2;$i<$xcurso->viernesf2;$i++){
					$tmp = "v".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				for($i=$xcurso->sabadoi2;$i<$xcurso->sabadof2;$i++){
					$tmp = "s".$i;
					if($xagenda->$tmp == 1){
						$aux++;
						if ( $aux > 1 ){
							$this->redirect('alumno/seleccion3/2');
							return;
						}
					}
				}
				
				if ( $xagenda->max1cruce == 1 && $aux > 0 ){
					$this->redirect('alumno/seleccion3/5');
					return;
				}
				
				// Me dispongo a guardar si llega hasta aquí...
				for($i=$xcurso->lunesi;$i<$xcurso->lunesf;$i++){
					$tmp = "l".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->martesi;$i<$xcurso->martesf;$i++){
					$tmp = "m".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->miercolesi;$i<$xcurso->miercolesf;$i++){
					$tmp = "i".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->juevesi;$i<$xcurso->juevesf;$i++){
					$tmp = "j".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->viernesi;$i<$xcurso->viernesf;$i++){
					$tmp = "v".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->sabadoi;$i<$xcurso->sabadof;$i++){
					$tmp = "s".$i;
					$xagenda->$tmp = 1;
				}
				
				//////////////////////////////////////////////////////////
				// Si tiene 0 en lunesi2 no se ejecuta el for, esto quiere decir que
				// no se cargaron horas extras de ese curso en ese mismo dia,
				// lo mismo para martesi2 ó miercolesi2 etc...
				for($i=$xcurso->lunesi2;$i<$xcurso->lunesf2;$i++){
					$tmp = "l".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->martesi2;$i<$xcurso->martesf2;$i++){
					$tmp = "m".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->miercolesi2;$i<$xcurso->miercolesf2;$i++){
					$tmp = "i".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->juevesi2;$i<$xcurso->juevesf2;$i++){
					$tmp = "j".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->viernesi2;$i<$xcurso->viernesf2;$i++){
					$tmp = "v".$i;
					$xagenda->$tmp = 1;
				}
				
				for($i=$xcurso->sabadoi2;$i<$xcurso->sabadof2;$i++){
					$tmp = "s".$i;
					$xagenda->$tmp = 1;
				}
				
				if ( $aux == 0 )
					$xagenda->max1cruce = '0';
				else
					$xagenda->max1cruce = 1;
				
				$xagenda->save();
				
				$xalumnocurso = new Xalumnocursos();
				$xalumnocurso->registro = $id;
				$xalumnocurso->periodo = $periodo;
				
				if($xcurso->id <100 && $xcurso->id >= 10){
					$xcurso->id = "0".$xcurso->id;
				}
				
				if($xcurso->id < 10){
					$xcurso->id = "00".$xcurso->id;
				}
				
				$xalumnocurso->curso = $xcurso->division.$xcurso->id;;
				$xalumnocurso->faltas1 = '0';
				$xalumnocurso->faltas2 = '0';
				$xalumnocurso->faltas3 = '0';
				$xalumnocurso->calificacion1 = 300;
				$xalumnocurso->calificacion2 = 300;
				$xalumnocurso->calificacion3 = 300;
				$xalumnocurso->faltas = '0';
				$xalumnocurso->calificacion = 300;
				$xalumnocurso->situacion = "-";
				
				$xalumnocurso->create();
				
				$xcurso->disponibilidad -= 1;
				$xcurso->save();
				
			}
			$this->redirect('alumno/seleccion3/7');
		}
		
		function pseleccion(){
			
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$seleccion = new Seleccionalumno();
			
			$seleccion->registro = $id;
			$seleccion->clavemateria = $this->post("materia");
			$seleccion->periodo = $periodo;
			$seleccion->save();
			
			$this->redirect('alumno/seleccion2');
		}
			
		function epkardex(){
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$xpkardex = new Xpkardex();
			
			$tmp = $xpkardex->find($this->post("id"));
			$tmp->delete();
			
			$this->redirect('alumno/seleccion1');
		}
		
		function pkardex(){
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$xpkardex = new Xpkardex();
			
			$xpkardex->registro = $id;
			$xpkardex->materia = $this->post("materia");
			$xpkardex->periodo = $this->post("periodo");
			$xpkardex->tipo = $this->post("tipo");
			
			$xpkardex->promedio = '0';
	
			$xpkardex->save();
			
			$this->redirect('alumno/seleccion1');
		}
		
		function numero_letra($numero){
			if($numero==300 || $numero=="-"){
				return "-";
			}
			
			if($numero==999 || $numero=="NP"){
				return "NO PRESENTO";
			}
			
			if($numero==500 || $numero=="PND"){
				return "PENDIENTE";
			}
		
			if($numero<30){
				if($numero<20){
					switch($numero){
						case 0: return "CERO";
						case 1: return "UNO";
						case 2: return "DOS";
						case 3: return "TRES";
						case 4: return "CUATRO";
						case 5: return "CINCO";
						case 6: return "SEIS";
						case 7: return "SIETE";
						case 8: return "OCHO";
						case 9: return "NUEVE";
						case 10: return "DIEZ";
						case 11: return "ONCE";
						case 12: return "DOCE";
						case 13: return "TRECE";
						case 14: return "CATORCE";
						case 15: return "QUINCE";
						case 16: return "DIECISEIS";
						case 17: return "DIECISIETE";
						case 18: return "DIECIOCHO";
						case 19: return "DIECINUEVE";
					}
				}
				else{
					$resultado = "";
					
					if($numero==20){
						return "VEINTE";
					}
					
					switch(floor($numero/10)){
						case 2: $resultado = "VEINTI"; break;
					}
				
					switch($numero%10){
						case 1: $resultado .= "UNO"; break;
						case 2: $resultado .= "DOS"; break;
						case 3: $resultado .= "TRES"; break;
						case 4: $resultado .= "CUATRO"; break;
						case 5: $resultado .= "CINCO"; break;
						case 6: $resultado .= "SEIS"; break;
						case 7: $resultado .= "SIETE"; break;
						case 8: $resultado .= "OCHO"; break;
						case 9: $resultado .= "NUEVE"; break;
					}
					return $resultado;
				}
			}
			else{
				$resultado = "";
				switch(floor($numero/10)){
					case 3: $resultado = "TREINTA"; break;
					case 4: $resultado = "CUARENTA"; break;
					case 5: $resultado = "CINCUENTA"; break;
					case 6: $resultado = "SESENTA"; break;
					case 7: $resultado = "SETENTA"; break;
					case 8: $resultado = "OCHENTA"; break;
					case 9: $resultado = "NOVENTA"; break;
					case 10: $resultado = "CIEN"; break;
				}
				
				switch($numero%10){
					case 1: $resultado .= " Y UNO"; break;
					case 2: $resultado .= " Y DOS"; break;
					case 3: $resultado .= " Y TRES"; break;
					case 4: $resultado .= " Y CUATRO"; break;
					case 5: $resultado .= " Y CINCO"; break;
					case 6: $resultado .= " Y SEIS"; break;
					case 7: $resultado .= " Y SIETE"; break;
					case 8: $resultado .= " Y OCHO"; break;
					case 9: $resultado .= " Y NUEVE"; break;
				}
				return $resultado;
			}
		}
		
		function nombreProfesor($nomina){
			$maestros = new Maestros();
			$maestro = $maestros->find_first("nomina=".$nomina."");
			return $maestro->nombre;
		}
		
		function nombreSalon($salon){
			$xsalones = new Xsalones();
			$xsalon = $xsalones->find_first("id=".$salon."");
			return $xsalon->edificio.":".$xsalon->nombre;
		}
		
		function obtenerMateria($clave,$especialidad){
			$materias = new Materiasing();
			$materia = $materias->find_first("clavemat='".$clave."' AND especialidad=".$especialidad);
			return $materia->nombre;
		}
		
		function sacarMateria($clave,$plan){
			$materias = new materia();
			$materia = $materias->find_first("clave='".$clave."' AND plan=".$plan);
			return $materia->nombre;
		}
		
		function incrementaPeriodo($periodo){
			
			if(date("m",time())<7){
				$actual = "1".date("Y",time());
			}
			else{
				$actual = "3".date("Y",time());
			}
			
			$tmp = $periodo;
		
			if( substr($periodo,0,1) == 1){
				$periodo = "3".substr($periodo,1,4);				
			}
			else{
				$periodo = "1".(substr($periodo,1,4) + 1);
			}
			
			return $periodo;
		}
		
		function incrementaPeriodoKardex($periodo){
			
			if(date("m",time())<7){
				$actual = "1".date("Y",time());
			}
			else{
				$actual = "3".date("Y",time());
			}
			
			$tmp = $periodo;
			
			$elPeriodo = substr($periodo,0,1);
			
			// 42009 SE CURSA EN ENERO DEL 2010
			
			// 12010 FEB - JUN DEL 2010
			// 22010 JULIO DEL 2010
			// 32010 AGO - DIC DEL 2010
			// 42010 SE CURSA EN ENERO DEL 2011
			
			if( $elPeriodo == 1 ){
				$periodo = "2".substr($periodo,1,4);
			}
			elseif( $elPeriodo == 2 ){
				$periodo = "3".substr($periodo,1,4);
			}
			elseif( $elPeriodo == 3 ){
				$periodo = "4".substr($periodo,1,4);
			}
			else{
				$periodo = "1".(substr($periodo,1,4) + 1);
			}
			
			return $periodo;
		}
		
		function horario(){
			
		  $Alumnos		= new AlumnoController();
		  //Valida que no ingres por URL en caso de estar BD,BT,SIN PAGO, EG
		  AlumnoController::url_validacion();
		  
		 /*  if(!$this->validarOctavos()){
				$this->redirect("alumno/encuesta");
				return;
			}*/
			
			//$this->validarEncuesta();
			
			$visitas	= new VisitasContador();
			// alumno/horario le corresponde el id 3
			$visitas->incrementar(3);
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro = '".$registro."'");
			
			/*
			if($xalumno->nombre == ""){
				$this->redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario->clave == $registro){
				$this->redirect('alumnos/index');
			}
			
			// Validar si ya hizo preselección
			//$this->validarPreseleccion();
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->ingreso);
			unset($this->plantel);
			
			unset($this->creditos);
			unset($this->ncreditos);
			unset($this->periodos);
			unset($this->pinicial);
			unset($this->materias);
			unset($this->cursos);
			unset($this->horas);
			unset($this->salon);
			unset($this->maestros);
			unset($this->xccur);
			unset($this->horarios);
			unset($this->inicioHora);
			unset($this->finHora);
			
			$xccursos		= new Xccursos();
			$xtcursos		= new Xtcursos();
			$maestros		= new Maestros();
			$materias		= new Materia();
			$Xalumnocursos	= new Xalumnocursos();
			$Xtalumnocursos	= new Xtalumnocursos();
			$Alumnos		= new Alumnos();
			$xchorascursos	= new Xchorascursos();
			$xcsalones		= new Xcsalones();
			$kardexes		= new KardexIng();
			$KardexIng		= new KardexIng();
			$planes			= new Plan();
			
			$total = 0;
			
			if( substr($periodo, 0, 1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo, 1, 4);
			$this->registro = $registro;
			
			$this->alumno = $alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->get_info_for_partial_info_alumno($alumno);
			
			$this->enPlan = $alumno->enPlan;
			$this->pinicial = $alumno->miPerIng;
			$this->ingreso = $alumno->miPerIng;
			
			
			$plan = substr($alumno->enPlan,2,2);
			//$carreras = new Carrera();
			//$carrera = $carreras->find_first("clave=".$especialidad->siNumEsp." AND modelo=20".$plan);
			
			//$plan1 = $planes->find_first("idcarrera = ".$carrera->id." AND nombre=20".$plan);
			//$this->idplan = $plan1->id;
			//$this->plan = $plan1->nombre;
			
			
			if( $this->promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this->promedio = substr( $this->promedio, 0, $cuanto );
			
			//$planes = new Plan();
			//$plan = $planes->find_first("idcarrera=".$carrera->id." AND nombre=20".$plan);
			//$this->idplan = $plan->id;
			//$this->plan = $plan->nombre;
			
			$xalcursos = $Xalumnocursos->get_materias_semestre_actual($registro);
			$xtalcursos = $Xtalumnocursos->get_materias_semestre_actual($registro);
			
			$i = 0;
			$matutino = 0;
			$vespertino = 0;
			if(count($xalcursos)>0){
				foreach($xalcursos as $xalcurso){
					$this->plantel[$i] = "C";
					$xccurso = $xccursos->find_first( "id = '".$xalcurso->curso_id."'" );
					$this->xccur[$i] = $xccurso;
					
					$materia = $materias->find_first( "clave = '".$xccurso->materia."'" );
					$this->materias[$i] = $materia;
					
					$maestro = $maestros->find_first( "nomina = ".$xccurso->nomina );
					$this->maestros[$i] = $maestro;
					
					foreach( $xchorascursos->find_all_by_sql( "
							Select xcc.clavecurso, xcc.materia,
							m.nombre, xch.dia, xch.hora, xcs.edificio, xcs.nombre nombreSalon
							from xccursos xcc, xchorascursos xch, xcsalones xcs, materia m
							where xcc.clavecurso = '".$xccurso->clavecurso."'
							and xcc.id = xch.curso_id
							and xch.xcsalones_id = xcs.id
							and xcc.materia = m.clave
							and m.carrera_id = ".$alumno->carrera_id."
							order by dia, hora" ) as $xchora ){
						if( isset($this->horarios[$horarios->dia][$horarios->hora]) )
							$this->horarios[1][$horarios->dia][$horarios->hora] = $xchora;
						else
							$this->horarios[$xchora->dia][$xchora->hora] = $xchora;
						if( $xchora->hora < 15 ){
							// Horario Matutino
							$this->inicioHora = 7;
							$this->finHora = 14;
							$matutino = 1;
						}if( $xchora->hora >= 15 ){
							// Horario Vespertino
							$this->inicioHora = 15;
							$this->finHora = 21;
							$vespertino = 1;
						}if( $matutino == 1 && $vespertino == 1 ){
							// Horario Mixto
							$this->inicioHora = 7;
							$this->finHora = 21;
						}
					}
					$i++;
				} // foreach($xalcursos as $xalcurso)
			} // if(count($xalcursos)>0)
			
			if(count($xtalcursos)>0){
				foreach($xtalcursos as $xalcurso){
					$this->plantel[$i] = "T";
					$xccurso = $xtcursos->find_first( "id = '".$xalcurso->curso_id."'" );
					$this->xccur[$i] = $xccurso;
					$j = 0;
					
					$materia = $materias->find_first( "clave = '".$xccurso->materia."'" );
					$this->materias[$i] = $materia;
					
					$maestro = $maestros->find_first( "nomina = ".$xccurso->nomina );
					$this->maestros[$i] = $maestro;
					
					foreach( $xchorascursos->find_all_by_sql( "
							Select xcc.clavecurso, xcc.materia,
							m.nombre, xch.dia, xch.hora, xcs.edificio, xcs.nombre nombreSalon
							from xtcursos xcc, xthorascursos xch, xtsalones xcs, materia m
							where xcc.clavecurso = '".$xccurso->clavecurso."'
							and xcc.id = xch.curso_id
							and xch.xtsalones_id = xcs.id
							and xcc.materia = m.clave
							and m.carrera_id = ".$alumno->carrera_id."
							order by dia, hora" ) as $xchora ){
						if( isset($this->horarios[$horarios->dia][$horarios->hora]) )
							$this->horarios[1][$horarios->dia][$horarios->hora] = $xchora;
						else
							$this->horarios[$xchora->dia][$xchora->hora] = $xchora;
						if( $xchora->hora < 15 ){
							// Horario Matutino
							$this->inicioHora = 7;
							$this->finHora = 14;
							$matutino = 1;
						}if( $xchora->hora >= 15 ){
							// Horario Vespertino
							$this->inicioHora = 15;
							$this->finHora = 21;
							$vespertino = 1;
						}if( $matutino == 1 && $vespertino == 1 ){
							// Horario Mixto
							$this->inicioHora = 7;
							$this->finHora = 21;
						}
					}
					$i++;
				} // foreach($xalcursos as $xalcurso)
			} // if(count($xtalcursos)>0)
		} // function horario()
		
		function horariogeneral(){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos->find_first("registro=".$id);
			
			/*
			if($xalumno->nombre == ""){
				$this->redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario->clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $id;
			
			$alumno = $alumnos->find_first("miReg=".$id);
			
			$this->alumno = $alumnos->vcNomAlu;
			
			$this->alumno = $alumnos->vcNomAlu;
			$this->ingreso = $alumnos->miPerIng;
			
			$especialidad = $especialidades->find_first("idtiEsp=".$alumnos->idtiEsp);
			$this->especialidad = $especialidad->vcNomEsp;
			
			$plan = substr($alumno->enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras->find_first("clave=".$especialidad->siNumEsp." AND modelo=20".$plan);
			
			if($especialidad->siNumEsp == 601 && $plan=="07"){
				$carrera->id = 6;
			}
			if($especialidad->siNumEsp == 800 && $plan=="07"){
				$carrera->id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes->find_first("idcarrera=".$carrera->id." AND nombre=20".$plan);
			$this->idplan = $plan->id;
			$this->plan = $plan->nombre;
			
			$cursos = $xacursos->find("registro=".$id." AND periodo=".$periodo);
			$this->cursos = $xacursos->count("registro=".$id." AND periodo=".$periodo);
			$i=0;
			
			if($this->cursos > 0 ){
				foreach($cursos as $curso){ 
					if($curso->curso!=""){
						$tmp = $xcursos->find_first("id=".(substr($curso->curso,3)+0));
					}
					$this->mihorario[$i] = $tmp;
					if($tmp->nomina!="")
						$this->profesor[$i] = $maestros->find_first("nomina=".$tmp->nomina."");
					if($tmp->materia!="" && $especialidad->siNumEsp != "")
						$this->materia[$tmp->materia] = $this->sacarMateria($tmp->materia,$this->plan);
					
					$xsalones = new Xsalones();
					
					if($tmp->luness != "-" && $this->salones[$tmp->luness] == 0)
						$this->salones[$tmp->luness] = $tmp->luness;
					
					if($tmp->martess != "-" && $this->salones[$tmp->martess] == 0)
						$this->salones[$tmp->martess] = $tmp->martess;
					
					if($tmp->miercoless != "-" && $this->salones[$tmp->miercoless] == 0)
						$this->salones[$tmp->miercoless] = $tmp->miercoless;
						
					if($tmp->juevess != "-" && $this->salones[$tmp->juevess] == 0)
						$this->salones[$tmp->juevess] = $tmp->juevess;
					
					if($tmp->vierness != "-" && $this->salones[$tmp->vierness] == 0)
						$this->salones[$tmp->vierness] = $tmp->vierness;
					
					if($tmp->sabados != "-" && $this->salones[$tmp->sabados] == 0)
						$this->salones[$tmp->sabados] = $tmp->sabados;
					
					
					$i++;
					
				}
			}
		}
		
		
		function evaluacion(){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos->find_first("registro=".$id);
			
			/*
			if($xalumno->nombre == ""){
				$this->redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario->clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $id;
			
			$alumno = $alumnos->find_first("miReg=".$id);
			
			$this->alumno = $alumnos->vcNomAlu;
			
			$this->alumno = $alumnos->vcNomAlu;
			$this->ingreso = $alumnos->miPerIng;
			
			$especialidad = $especialidades->find_first("idtiEsp=".$alumnos->idtiEsp);
			$this->especialidad = $especialidad->vcNomEsp;
			
			$plan = substr($alumno->enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras->find_first("clave=".$especialidad->siNumEsp." AND modelo=20".$plan);
			
			if($especialidad->siNumEsp == 601 && $plan=="07"){
				$carrera->id = 6;
			}
			if($especialidad->siNumEsp == 800 && $plan=="07"){
				$carrera->id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes->find_first("idcarrera=".$carrera->id." AND nombre=20".$plan);
			$this->idplan = $plan->id;
			$this->plan = $plan->nombre;
			
			$cursos = $xacursos->find("registro=".$id);
			$this->cursos = $xacursos->count("registro=".$id);
			
			$i=0;
			
			if($this->cursos > 0 ){
				foreach($cursos as $curso){
	
					$xevaluacion = new Evaluacion();
					$n = $xevaluacion->count("registro=".$id." AND curso='".$curso->curso."'");
					
					if($n>0){
						$this->evaluacion[$curso->curso] = 1;
					}
					else{
						$this->evaluacion[$curso->curso] = 0;
					}
				
					if($curso->curso!=""){
						$tmp = $xcursos->find_first("id=".(substr($curso->curso,3)+0));
					}
					$this->mihorario[$i] = $tmp;
					if($tmp->nomina!="")
						$this->profesor[$i] = $maestros->find_first("nomina=".$tmp->nomina."");
					if($tmp->materia!="" && $especialidad->siNumEsp != "")
						$this->materia[$tmp->materia] = $this->sacarMateria($tmp->materia,$this->plan);
					
					$xsalones = new Xsalones();
					
					if($tmp->luness != "-" && $this->salones[$tmp->luness] == 0)
						$this->salones[$tmp->luness] = $xsalones->find_first("id=".$tmp->luness);
					
					if($tmp->martess != "-" && $this->salones[$tmp->martess] == 0)
						$this->salones[$tmp->martess] = $xsalones->find_first("id=".$tmp->martess);
					
					if($tmp->miercoless != "-" && $this->salones[$tmp->miercoless] == 0)
						$this->salones[$tmp->miercoless] = $xsalones->find_first("id=".$tmp->miercoless);
						
					if($tmp->juevess != "-" && $this->salones[$tmp->juevess] == 0)
						$this->salones[$tmp->juevess] = $xsalones->find_first("id=".$tmp->juevess);
					
					if($tmp->vierness != "-" && $this->salones[$tmp->vierness] == 0)
						$this->salones[$tmp->vierness] = $xsalones->find_first("id=".$tmp->vierness);
					
					if($tmp->sabados != "-" && $this->salones[$tmp->sabados] == 0)
						$this->salones[$tmp->sabados] = $xsalones->find_first("id=".$tmp->sabados);
					
					
					$i++;
					
				}
			}
		}
		
		function evaluando($curso){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos->find_first("registro=".$id);
			
			/*
			if($xalumno->nombre == ""){
				$this->redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario->clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this->actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this->periodo);
			unset($this->registro);
			unset($this->alumno);
			unset($this->profesor);
			unset($this->mihorario);
			unset($this->especialidad);
			unset($this->materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if( substr($periodo,0,1) == 1)
				$this->periodo = "FEB - JUN, ";
			else
				$this->periodo = "AGO - DIC, ";
			
			$this->periodo .= substr($periodo,1,4);
			$this->registro = $id;
			
			$alumno = $alumnos->find_first("miReg=".$id);
			
			$this->alumno = $alumnos->vcNomAlu;
			
			$this->alumno = $alumnos->vcNomAlu;
			$this->ingreso = $alumnos->miPerIng;
			
			$especialidad = $especialidades->find_first("idtiEsp=".$alumnos->idtiEsp);
			$this->especialidad = $especialidad->vcNomEsp;
			
			$plan = substr($alumno->enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras->find_first("clave=".$especialidad->siNumEsp." AND modelo=20".$plan);
			
			if($especialidad->siNumEsp == 601 && $plan=="07"){
				$carrera->id = 6;
			}
			if($especialidad->siNumEsp == 800 && $plan=="07"){
				$carrera->id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes->find_first("idcarrera=".$carrera->id." AND nombre=20".$plan);
			$this->idplan = $plan->id;
			$this->plan = $plan->nombre;
			
			//$xcurso = $xacursos->find_first("curso='".$curso."'");
			
			$xcurso = $xcursos->find_first("curso='".$curso."'");
			$this->curso = $xcurso;
			
			$this->profesor = $maestros->find_first("nomina=".$xcurso->nomina);
			$this->profesor = $this->profesor->nombre;
		}
		
		function evaluar($curso){
		
			$registro = Session::get_data('registro');
			
			$xevaluacion = new Evaluacion();
			
			$xevaluacion->registro = $registro;
			$xevaluacion->curso = $curso;
			
			$xevaluacion->p1 = $this->post("p1");
			$xevaluacion->p2 = $this->post("p2");
			$xevaluacion->p3 = $this->post("p3");
			$xevaluacion->p4 = $this->post("p4");
			$xevaluacion->p5 = $this->post("p5");
			$xevaluacion->p6 = $this->post("p6");
			$xevaluacion->p7 = $this->post("p7");
			$xevaluacion->p8 = $this->post("p8");
			$xevaluacion->p9 = $this->post("p9");
			$xevaluacion->p10 = $this->post("p10");
			$xevaluacion->p11 = $this->post("p11");
			$xevaluacion->p12 = $this->post("p12");
			$xevaluacion->p13 = $this->post("p13");
			$xevaluacion->p14 = $this->post("p14");
			$xevaluacion->p15 = $this->post("p15");
			$xevaluacion->p16 = $this->post("p16");
			$xevaluacion->p17 = $this->post("p17");
			$xevaluacion->p18 = $this->post("p18");
			$xevaluacion->p19 = $this->post("p19");
			$xevaluacion->p20 = $this->post("p20");
			$xevaluacion->p21 = $this->post("p21");
			
			if($this->post("comentarios")=="")
				$xevaluacion->comentarios = "-";
			else
				$xevaluacion->comentarios = $this->post("comentarios");
			
			$xevaluacion->periodo = 32008;
			$xevaluacion->fecha = date("Y-m-d H:i:s",time());
			$xevaluacion->save();
			
			$this->redirect("alumno/evaluacion");
		}
		
		function actualizacion(){
			
			$aspirantes = new Aspirantes();
			
			unset($this->nombre);
			unset($this->paterno);
			unset($this->materno);
			unset($this->anio);
			unset($this->mes);
			unset($this->dia);
			unset($this->sexo);
			unset($this->sangre);
			unset($this->estadocivil);
			unset($this->domicilio);
			unset($this->colonia);
			unset($this->cp);
			unset($this->ciudad);
			unset($this->telefono);
			unset($this->correo);
			unset($this->curp);
			unset($this->seguridad_social);
			
			$registro = Session::get_data('registro');
			foreach( $aspirantes->find("registro = ".$registro) as $asp ){
				$this->nombre = $asp->nombre;
				$this->paterno = $asp->paterno;
				$this->materno = $asp->materno;
				$this->anio = substr($asp->fecha_nacimiento,0,4);
				$this->mes = substr($asp->fecha_nacimiento,5,2);
				$this->dia = substr($asp->fecha_nacimiento,8,2);
				$this->sexo = $asp->sexo;
				$this->sangre = $asp->sangre;
				$this->estadocivil = $asp->estadocivil;
				$this->domicilio = $asp->calle." ".$asp->exterior;
				$this->colonia = $asp->colonia;
				$this->cp = $asp->cp;
				$this->ciudad = $asp->lugarNacimiento;
				$this->telefono = $asp->telefono;
				$this->correo = $asp->correo;
				$this->curp = $asp->curp;
			}
			$XalumnosPersonal = new XalumnosPersonal();
			foreach( $XalumnosPersonal->find("registro = ".$registro) as $xalumno ){
				$this->seguridad_social = $xalumno->seguridad_social;
			}
			$Alumnos = new Alumnos();
			$alumno = $alumno = $Alumnos->get_relevant_info_from_student($registro);
			$this->get_info_for_partial_info_alumno($alumno);
			
		}
		
		public function seguridad_mostrar_container($seguridad_social){
			$this->set_response('view');
			
			$registro = Session::get_data('registro');
			
			unset($this->seguridad_social);
			unset($this->dependencia_seguro);
			unset($this->parte_de_quien_seguro);
			unset($this->numero_seguro_social);
			
			$this->seguridad_social = $seguridad_social;
			
			$XalumnosPersonal = new XalumnosPersonal();
			foreach( $XalumnosPersonal->find("registro = ".$registro) as $xalumno ){
				$this->dependencia_seguro = $xalumno->dependencia_seguro;
				$this->parte_de_quien_seguro = $xalumno->parte_de_quien_seguro;
				$this->numero_seguro_social = $xalumno->numero_seguro_social;
			}
			
			$this->render_partial("seguridad_social");
			
		} // public function seguridad_mostrar_container($seguridad_social)
		
		function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			$this->redirect('/');
		}
	
		function encuestas(){
			unset($this->activarFormulario);
			unset($this->preguntas);
			unset($this->prof);
			unset($this->registroAlumno);
			
			$this->activarFormulario = 0;
			$reg = Session::get_data('registro');
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			if (substr($reg,0,4)== "prof" || substr($reg,0,4)== "elch" ){
				$this->activarFormulario = 1;
				$this->prof = 1;
			}
			else{
				$this->prof = 0;
//				$reg = 9310001; // borrar esto, yo lo puse para hacer pruebas....
				$this->registroAlumno = $reg;
				$Usuarios = new Usuariosencuestas();
				if($Usuarios->find_first("registro = ".$reg." and periodo = ".$periodo)){
					$suma = 0;
					for( $i = 1; $i < 14; $i++ ){
						$campo = "s".$i;
						$suma = $suma + $Usuarios->$campo;
					}
					if ($suma == 13){
						$this->activarFormulario = 1;
					}
					else{
						$this->activarFormulario = 0;
					}
					
					//no hace nada solo es para validar
				}else{
					$nuevoUsuario = new Usuariosencuestas();
					$nuevoUsuario->registro = $reg;
					$nuevoUsuario->s1 = '0';
					$nuevoUsuario->s2 = '0';
					$nuevoUsuario->s3 = '0';
					$nuevoUsuario->s4 = '0';
					$nuevoUsuario->s5 = '0';
					$nuevoUsuario->s6 = '0';
					$nuevoUsuario->s7 = '0';
					$nuevoUsuario->s8 = '0';
					$nuevoUsuario->s9 = '0';
					$nuevoUsuario->s10 = '0';
					$nuevoUsuario->s11 = '0';
					$nuevoUsuario->s12 = '0';
					$nuevoUsuario->s13 = '0';
					$nuevoUsuario->periodo = $periodo;
					$nuevoUsuario->save();
					$Usuarios->find_first("registro = ".$reg);
				}
				$Preguntas = new PreguntasSatisfaccion();
				$OpcPreguntas = new OpcpreguntasSatisfaccion();
				for( $i = 1; $i < 14; $i++ ){
					$var= "s".$i;
					if ($Usuarios->$var == 0){
						$this->preguntas[$i] = $Preguntas->find("clave = $i and periodo = ".$periodo." order by id ASC");
						foreach ( $this->preguntas[$i] as $preguntas ){
							$this->opciones[$preguntas->id] = $OpcPreguntas->
									find_first ( "preguntas_satisfaccion_id = ".$preguntas->id);
						}
					}
				}
			}
		} // function encuestas()
		
		function guardar(){
			
			$this->set_response('view');
			$seccionNombre = $this->request("seccionNombre");
			$seccionNumero = $this->request("seccionNumero");
			$registroAlumno = $this->request("registroAlumno");
			$day = date("d");
			$month = date("m");
			$year = date("Y");
			$hour = date("H");
			$min = date("i");
			$sec = date("s");
			$date1 = date( "Y-m-d H:i:s", mktime( $hour, $min, $sec, $month, $day, $year ) );
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			for( $i = 157; $i <= 208; $i++ ){
				if ( $this->post("r".$i) != '' || 
						$this->post("comentario".$i) != '' || 
								$this->post("opctexto".$i) != '' ||
										$this->post("opc".$i) != '' ||
												$this->post("sino".$i) != '' ){
					$Preguntas = new PreguntasSatisfaccion();
					$Respuestas = new RespuestasSatisfaccion();
					$Preguntas->find_first("id = ".$i);
					
					echo "<b>". htmlentities($Preguntas->pregunta, ENT_QUOTES)."</b>";
					if ( $this->post("tipo".$i) == 0 ){
						switch ($this->post("r".$i)){
							case 5: $Preguntas->r1 = $Preguntas->r1 +1;
									break;
							case 6: $Preguntas->r2 = $Preguntas->r2 +1;
									break;
							case 7: $Preguntas->r3 = $Preguntas->r3 +1;
									break;
							case 8: $Preguntas->r4 = $Preguntas->r4 +1;
									break;
							case 9: $Preguntas->r5 = $Preguntas->r5 +1;
									break;
							case 10: $Preguntas->r6 = $Preguntas->r6 +1;
									break;
						}
						$Preguntas->save();
						echo "<br />".$this->post("r".$i)."<br />";
					}
					else if ( $this->post("tipo".$i) == 1 ){
						echo "<br />".$this->post( "comentario".$i )."<br />";
						$Respuestas->preguntas_satisfaccion_id = $i;
						$Respuestas->fecha = $date1;
						$Respuestas->respuesta = '0';
						$Respuestas->comentario = $this->post("comentario".$i);
						$Respuestas->registro = Session::get_data("registro");
						$Respuestas->create();
					}
					else if ( $this->post("tipo".$i) == 2 ){
						if ( $this->post("sino".$i ) == 1 )
							echo "<br />Si<br />";
						else
							echo "<br />No<br />";
						
						$Respuestas->preguntas_satisfaccion_id = $i;
						$Respuestas->fecha = $date1;
						$Respuestas->respuesta = $this->post("sino".$i);
						$Respuestas->comentario = " ";
						$Respuestas->registro = Session::get_data("registro");
						$Respuestas->create();
					}
					else if ( $this->post("tipo".$i) == 3 ){
						if ( $this->post("opctexto".$i) != "" ||
										$this->post("opctexto".$i) != null ){
							echo "<br />".$this->post("opctexto".$i )."<br />";
							$Respuestas->preguntas_satisfaccion_id = $i;
							$Respuestas->fecha = $date1;
							$Respuestas->respuesta = '0';
							$Respuestas->comentario = $this->post("opctexto".$i);
							$Respuestas->registro = Session::get_data("registro");
							$Respuestas->create();
						}
						else{
							echo "<br />".$this->post("opc".$i )."<br />";
							$Respuestas->preguntas_satisfaccion_id = $i;
							$Respuestas->fecha = "'".$date1."'";
							$Respuestas->respuesta = $this->post("opc".$i);
							$Respuestas->comentario = " ";
							$Respuestas->registro = Session::get_data("registro");
							$Respuestas->create();
						}
					}
					//echo utf8_encode("<b>".$Preguntas->pregunta."</b> <br> Respuesta:".$this->post("r".$i)."<br>");
					//echo "<br/ > Respuesta:".$this->post("r".$i)."<br>";
				}
			}
			//Flash::success(utf8_encode('Se guardo exitosamente la sección: ').'<span class="resaltado">'.$seccionNombre.'</span>');
			Flash::success('Se guardo exitosamente la secci&oacute;n: <span class="resaltado">'.$seccionNombre.'</span>');
			
			$seccionNumero= "s".$seccionNumero;
			$Usuarios = new Usuariosencuestas();
			$Usuarios->find_first( "registro = ".$registroAlumno." and periodo = ".$periodo );
			$Usuarios->$seccionNumero = 1;
			$Usuarios->save();
			
			$suma=0;
			for( $i = 1; $i < 14; $i++ ){
				$campo = "s".$i;
				$suma = $suma + $Usuarios->$campo;
			}
			if ( $suma == 13 ){
				$this->render_partial("formulario");
			}
			$this->set_response('view');
		} // function guardar()
		
		function validarEncuesta(){
			$Usuariosencuestas = new Usuariosencuestas();
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			if($Usuariosencuestas->find_first( "registro = ".Session::get_data('registro').
					" and periodo = ".$periodo ) ){
				$suma = 0;
				for( $i = 1; $i < 14; $i++ ){
					$campo = "s".$i;
					$suma = $suma + $Usuariosencuestas->$campo;		
				}
				if ( $suma == 13 ){
					return;
				}
			}
			if( Session::get_data('master') == 2 )
				return;
			
			$this->redirect("alumno/encuestas");
			return;
		} // function validarEncuesta()
		
		function validarEvaluacion($registro){
			
			$mevaluacion = new Evaluacion();
			$mxalumnocursos = new Xalumnocursos();
			
			$m = $mevaluacion->count("registro=".$registro);
			$n = $mxalumnocursos->count("registro=".$registro);
			
			if($m>=$n || Session::get_data('OMISION') == "OK"){
				return true;
			}
			
			return true;
		}
		
		function validarActualizacion(){
		
			$registro = Session::get_data('registro');
			
			$xalumnos = new XalumnosPersonal();
			
			if($registro>911000 && $registro<912000){
				return false;
			}
			
			return true;
		}
		
		function validarPreseleccion(){
			$registro = Session::get_data('registro');
			$preTerminada	= new PreseleccionTerminada();
			// Si ya la realizó no podrá volverla a hacer...
			if(! $preTerminada->find_first( "registro = ".$registro." and periodo = ".$this->proximo ) ){
				$this->redirect('npreseleccion/captura');
				die;
			}
		} // function validarPreseleccion()
		
		function cambiarLetras($referencia){
			$nueva = "";
			
			$referencia = strtoupper($referencia);
			
			for($i=0;$i<strlen($referencia);$i++){
				switch(substr($referencia,$i,1)){
					case 'A': case 'B': case 'C': 	$nueva .= 2; break;
					case 'D': case 'E': case 'F': 	$nueva .= 3; break;
					case 'G': case 'H': case 'I': 	$nueva .= 4; break;
					case 'J': case 'K': case 'L': 	$nueva .= 5; break;
					case 'M': case 'N': case 'O': 	$nueva .= 6; break;
					case 'P': case 'Q': case 'R': 	$nueva .= 7; break;
					case 'S': case 'T': case 'U': 	$nueva .= 8; break;
					case 'V': case 'W': case 'X': 	$nueva .= 9; break;
					case 'Y': case 'Z': 			$nueva .= 0; break;
					default: $nueva .= substr($referencia,$i,1);
				}
			}
			
			return $nueva;
		}
		
		function digitoVerificador($referencia){
			$tmp = "";
			$temporal = "";
			$x = 2;
			$suma = 0;
			for($i = strlen($referencia)-1;$i>=0;$i--){
				$numero = substr($referencia,$i,1)*$x;
				$tmp = " ". $numero . $tmp;
				while($numero>=10){
					$tempo = 0;
					for($k=0;$k<strlen($numero);$k++){
						$tempo += substr($numero,$k,1);
					}
					$numero = $tempo;
				}
				$temporal = $numero . $temporal;
				$suma = $suma + $numero;
				if($x==2){ $x=1; continue;}
				if($x==1){ $x=2; continue;}
			}
			$residuo = $suma % 10;
		
			$digito = 10 - $residuo;
			
			if($digito==10) $digito = 0;
		
			return $digito;
		}
	
		function pagoextras($registro,$examen,$materia){
			$this->set_response("view");
			
			if($examen=="E"){
				$examen = 401;
			}
			if($examen=="T"){
				$examen = 501;
			}
			
			$materias = new Materia();
			$material = $materias->find_first("clave='".$materia."'");
			
			$material = $material->clave . " - " . $material->nombre;
			
			$materia = str_replace("-","",$materia);
			
			$referencia = $examen.$materia.$registro."308";
			
			$referenciatmp = $this->cambiarLetras($referencia);
			$referencia .= $this->digitoVerificador($referenciatmp);
			
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("ingenieria",$cnx);
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro;
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=32008";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
			
			$reporte = new FPDF();
			
			$reporte->Open();
			$reporte->AddPage();
			
			$reporte->AddFont('Verdana','','verdana.php');
			
			$reporte->Ln();
			
			$reporte->Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 20, 200, 90);
			$reporte->SetFont('Verdana','',10);
			
			$reporte->SetX(50);
			$reporte->SetY(38);
			$reporte->MultiCell(188,3,$referencia,0,'R',0);
			
			$reporte->SetFont('Verdana','',8);
			
			$reporte->SetX(50);
			$reporte->SetY(46);
			$reporte->MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte->SetFont('Verdana','',7);
			
			$reporte->Ln();
			$reporte->SetX(2);
			$reporte->SetY(42);
			
			$reporte->MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte->Ln();
			$reporte->Ln();
			$reporte->Ln();
			
			$reporte->SetX(20);
			// Aqui se modifica la fecha para las fichas de pago, en PDF
			// Estos archivos se encuentran en  " /sitios/htdocs/ingenieria/public/files/extraordinarios "
			$reporte->MultiCell(0,3,"10 / JUL / 2009",0,'L',0);
			
			if($examen==401){
				$reporte->SetY(67);
				$reporte->MultiCell(100,3,"PAGO DE EXAMEN EXTRAORDINARIO",0,'L',0);
				$reporte->SetY(70);
				$reporte->MultiCell(100,3,$material,0,'L',0);
				$reporte->SetY(73);
				$reporte->MultiCell(100,3,"",0,'L',0);
				$reporte->SetY(67);
				$reporte->MultiCell(80,3,"$150.00",0,'R',0);
				$reporte->SetY(70);
				$reporte->MultiCell(80,3,"",0,'R',0);
				$reporte->SetY(73);
				$reporte->MultiCell(80,3,"",0,'R',0);
				
				$reporte->SetY(80);
				$reporte->MultiCell(80,3,"$150.00",0,'R',0);
			}
			else{
				$reporte->SetY(67);
				$reporte->MultiCell(100,3,"PAGO DE EXAMEN TITULO DE SUFICIENCIA",0,'L',0);
				$reporte->SetY(70);
				$reporte->MultiCell(100,3,$material,0,'L',0);
				$reporte->SetY(73);
				$reporte->MultiCell(100,3,"",0,'L',0);
				$reporte->SetY(67);
				$reporte->MultiCell(80,3,"$170.00",0,'R',0);
				$reporte->SetY(70);
				$reporte->MultiCell(80,3,"",0,'R',0);
				$reporte->SetY(73);
				$reporte->MultiCell(80,3,"",0,'R',0);
				
				$reporte->SetY(80);
				$reporte->MultiCell(80,3,"$170.00",0,'R',0);
			}
			
			$reporte->SetY(106);
			$reporte->MultiCell(179,3,"BANCO",0,'C',0);
			
			$reporte->SetY(126);
			$reporte->MultiCell(0,3,"REVISIÓN 2                                                    A partir del 01 de Julio del 2006                                                    FR-02-DPL-CE-PO-004",0,'C',0);

			//////////////////////////////////////////////
			
			$reporte->Ln();
			
			$reporte->Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 150, 200, 90);
			$reporte->SetFont('Verdana','',10);
			
			$reporte->SetX(50);
			$reporte->SetY(168);
			$reporte->MultiCell(188,3,$referencia,0,'R',0);
			
			$reporte->SetFont('Verdana','',8);
			
			$reporte->SetX(50);
			$reporte->SetY(176);
			$reporte->MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte->SetFont('Verdana','',7);
			
			$reporte->Ln();
			$reporte->SetX(2);
			$reporte->SetY(172);
			
			$reporte->MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte->Ln();
			$reporte->Ln();
			$reporte->Ln();
			
			$reporte->SetX(20);
			$reporte->MultiCell(0,3,"10 / JUL / 2009",0,'L',0);
			
			if($examen==401){
				$reporte->SetY(197);
				$reporte->MultiCell(100,3,"",0,'L',0);
				$reporte->SetY(200);
				$reporte->MultiCell(100,3,"PAGO DE EXAMEN EXTRAORDINARIO",0,'L',0);
				$reporte->SetY(203);
				$reporte->MultiCell(100,3,$material,0,'L',0);
			
			
				$reporte->SetY(197);
				$reporte->MultiCell(80,3,"",0,'R',0);
				$reporte->SetY(200);
				$reporte->MultiCell(80,3,"$150.00",0,'R',0);
				$reporte->SetY(203);
				$reporte->MultiCell(80,3,"",0,'R',0);
				
				$reporte->SetY(210);
				$reporte->MultiCell(80,3,"$150.00",0,'R',0);
			}
			else{
				$reporte->SetY(197);
				$reporte->MultiCell(100,3,"",0,'L',0);
				$reporte->SetY(200);
				$reporte->MultiCell(100,3,"PAGO DE EXAMEN TITULO DE SUFICIENCIA",0,'L',0);
				$reporte->SetY(203);
				$reporte->MultiCell(100,3,$material,0,'L',0);
			
			
				$reporte->SetY(197);
				$reporte->MultiCell(80,3,"",0,'R',0);
				$reporte->SetY(200);
				$reporte->MultiCell(80,3,"$170.00",0,'R',0);
				$reporte->SetY(203);
				$reporte->MultiCell(80,3,"",0,'R',0);
				
				$reporte->SetY(210);
				$reporte->MultiCell(80,3,"$170.00",0,'R',0);
			}
			
			$reporte->SetY(236);
			$reporte->MultiCell(179,3,"ALUMNO",0,'C',0);
			
			$reporte->SetY(256);
			$reporte->MultiCell(0,3,"***** CONSERVA TU COPIA PARA CUALQUIER ACLARACIÓN POSTERIOR *****",0,'C',0);

			$reporte->Output("public/files/extraordinarios/".$referencia.".pdf");
			
			$this->redirect("files/extraordinarios/".$referencia.".pdf");
		}	
		
		function fichaPago2(){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			$this->set_response("view");
			//$registro = 911006; 
			//$registro = $this->post("registro");
			
			$registro = Session::get_data('registro');
			
			$cnx = mysql_connect("localhost","fernando","damndamn");
//			$cnx = mysql_connect("localhost","root","vertrigo");
			mysql_select_db("ingenieria", $cnx);
			
			/*
			// Este if se hizo en una urgencia ya que no habia tiempo
			// y es para verificar si se acepto o no su carta...
			if ( $registro == 811135 || $registro == 431217 || $registro == 831033 ||
					$registro == 831035 || $registro == 431103 ||
							$registro == 831020 || $registro == 731072 || $registro == 711142 ){
				echo "<h3>Tu carta fue rechazada, te sugerimos que vayas a Control Escolar</h3>";
				exit (1);
			}
			*/
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro." and (situacion = 'activo' or situacion = 'nuevo ingreso')";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			if($datos["vcNomAlu"]==""){
				echo "<center><h1>Tu ficha no esta disponible aun o estas dado de baja</h1></center>";
				echo '<center><b><a href="https://ase.ceti.mx/ingenieria"></a></b></center>';
				
				//$this->redirect('tecnologo/ficha');
				return;
			}
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=".$this->actual;
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
		
			if($registro > 9300000){
				$opcion = 101;
			}
			else{
				$opcion = 201;
			}
			$referencia_sin_dv = $opcion.$registro."309";
			$referenciaPa_sin_dv = "301".$registro."309";
			$referencia = $referencia_sin_dv.$this->digitoVerificador($referencia_sin_dv);
			$referenciaPa = $referenciaPa_sin_dv.$this->digitoVerificador($referenciaPa_sin_dv);

			$reporte = new FPDF();
			$reporte->Open();
			$reporte->AddFont('Verdana','','verdana.php');
			for($i=0; $i<2; $i++ ){		
				if ($i == 0){
					$copia = "ALUMNO";
				}else{
					$copia = "BANCO";
				}						
				$reporte->AddPage();												
				$reporte->Ln();
				
				$reporte->Image('http://ase.ceti.mx/ingenieria/img/formatoFichaPago.jpg', 5, 20, 200, 220);
				$reporte->SetFont('Verdana','',10);
				
				$reporte->SetX(45);
				$reporte->SetY(37);
				$reporte->MultiCell(188,3,$referencia,0,'R',0);
				
				$reporte->SetFont('Verdana','',8);
				
				$reporte->SetX(50);
				$reporte->SetY(44);
				$reporte->MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
				
				$reporte->SetFont('Verdana','',7);
				
				$reporte->Ln();
				$reporte->SetX(2);
				$reporte->SetY(41);
				
				$reporte->MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte->Ln();
				$reporte->Ln();
				$reporte->Ln();
				
				$reporte->SetX(20);
				$reporte->MultiCell(0,3,"31 / AGO / 2009",0,'L',0);
				
				if($opcion == 101){				
					$reporte->SetY(63);
					$reporte->MultiCell(100,3,"NUEVO INGRESO INGENIERÍA",0,'L',0);
					$reporte->SetY(66);
					$reporte->MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
					$reporte->SetY(69);
					$reporte->MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
					$reporte->SetY(72);
					$reporte->MultiCell(100,3,"RECARGO",0,'L',0);
					$reporte->SetY(63);
					$reporte->MultiCell(80,3,"$820.00",0,'R',0);
					$reporte->SetY(66);
					$reporte->MultiCell(80,3,"$70.00",0,'R',0);
					$reporte->SetY(69);
					$reporte->MultiCell(80,3,"$137.00",0,'R',0);
					$reporte->SetY(72);
					$reporte->MultiCell(80,3,"$230.00",0,'R',0);
					
//					$reporte->SetY(76);
//					$reporte->MultiCell(80,3,"$1027.00",0,'R',0);
					$reporte->SetY(76);
					$reporte->MultiCell(80,3,"$1257.00",0,'R',0);
				}
				else{
					$reporte->SetY(64);
					$reporte->MultiCell(100,3,"REINSCRIPCION INGENIERÍA",0,'L',0);
					$reporte->SetY(67);
					$reporte->MultiCell(100,3,"RECARGO",0,'L',0);
								
					$reporte->SetY(64);
					$reporte->MultiCell(80,3,"$690.00",0,'R',0);
					$reporte->SetY(67);
					$reporte->MultiCell(80,3,"$230.00",0,'R',0);
					
//					$reporte->SetY(76);
//					$reporte->MultiCell(80,3,"$690.00",0,'R',0);
					$reporte->SetY(76);
					$reporte->MultiCell(80,3,"$920.00",0,'R',0);
				}
				$reporte->SetY(101);
				$reporte->MultiCell(179,3,$copia,0,'C',0);			
				
				$reporte->Ln();
				
				$reporte->SetFont('Verdana','',10);
				
				$reporte->SetX(45);
				$reporte->SetY(156);			
				$reporte->MultiCell(188,3,$referenciaPa,0,'R',0);
				
				$reporte->SetFont('Verdana','',8);
				
				$reporte->SetX(50);
				$reporte->SetY(163);
				$reporte->MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
				
				$reporte->SetFont('Verdana','',7);
				
				$reporte->Ln();
				$reporte->SetX(2);
				$reporte->SetY(160);
				
				$reporte->MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte->Ln();
				$reporte->Ln();
				$reporte->Ln();
				
				$reporte->SetX(20);
				$reporte->MultiCell(0,3,"31 / AGO / 2009",0,'L',0);
							
				$reporte->SetY(184);
				$reporte->MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
				$reporte->SetY(187);
				$reporte->MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
				$reporte->SetY(187);
				$reporte->MultiCell(80,3,"$250.00",0,'R',0);
				
				$reporte->SetY(195);
				$reporte->MultiCell(80,3,"$250.00",0,'R',0);
	
				$reporte->SetY(220);
				$reporte->MultiCell(179,3,$copia,0,'C',0);
			}
			$reporte->Output("public/files/fichas/".$registro.".pdf");
			
			$this->redirect("files/fichas/".$registro.".pdf");
		}
		
	    /*
		 * Funcion que genera la ficha de pago para la reinscripcion o inscripcion
		*/
		function fichaPago(){
			// define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			// require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			 //Se llaman las clases 
			 $cajaTramites = new CajaTramites();
			 $cajaConceptos = new CajaConceptos(); 
			 $alumnos = new Alumnos();
			
			 //Obtiene Datos del alumno
			 $alumnos -> find_first("miReg = ".Session::get('registro'));
			 
			 $registro = Session::get('registro');
			 
			 //Se valida que el alumno tenga derecho a generar su ficha
			 if($alumnos -> stSit == "OK")
			 {
				if($alumnos -> pago == 0 && $alumnos -> condonado == 0 && $alumnos -> miPerIng == Session::get_data('periodo')){
				   $NumConcepto = "3";
				 }
				 
				 else{
				   $NumConcepto = "4";
				 }
				
				 //Obtiene la informacion del conceto solicitado
				 $cajaConceptos -> find_first("id =".$NumConcepto);
				 
				 //Cambiamos el formato de la fecha de pago 
				 list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
				 $fechaInicio = $dia."-".$mes."-".$anio;
				
				 //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
				 $fechaLimitePago = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
	 
				 //Plantel (Colomos - 2 ______ Tonala - 6)
				 $colomos = "2";
				 
				 //Se agrega 6 digitos para rellenar los id de la referencia
				 $tramiteRef = "000000";
				  
				 //Validacion de los digitos del registro (DEBE DE SER DE 8)
				 if(strlen($registro) == 3)
				   $registroRef = "00000".$registro;
					  
				 else if(strlen($registro) == 4)
				   $registroRef = "0000".$registro;
					  
				 else if(strlen($registro) == 5)
				   $registroRef = "000".$registro;
					  
				 else if(strlen($registro) == 6)
				   $registroRef = "00".$registro;
					
				 else if(strlen($registro) == 7)
				   $registroRef = "0".$registro;
					
				 else
				   $registroRef = $registro;
				   
					 
				 //Se valida la cantidad de digitos del concepto
				 if(strlen($NumConcepto) == 1)
				   $conceptoRef = "00".$NumConcepto;
						  
				 else if(strlen($NumConcepto) == 2)
				   $conceptoRef = "0".$NumConcepto;
						  
				 else if(strlen($NumConcepto) == 3)
				   $conceptoRef = $NumConcepto;
					 
					 
				 //Segun el nivel que solicito el concepto es la variable que se utilizara (debe de ser de 1 digito) Tenologo -1 2 -Ingenieria 
				 $ingenieria = "2";
				  
				  
				 //Valida el numero de caracterede de extras y titulos (Debe de ser de 5 digitos)
				 $actaExtrasTitulos = "00000";

				 //Periodo que se cursa (ACTIVO)
				 $periodo = Session::get_data('periodo');
				 
				 //Se genera la fecha condesada
				 list($diaPago,$mesPago,$anioPago) = split("-",$fechaLimitePago);
				  
				 $anioCondesado = ($anioPago - 1988) * 372;
				 $mesCondesado  = ($mesPago - 1) * 31;
				 $diaCondesado  = ($diaPago - 1);
				  
				 //Se obtiene la fecha condesada
				 $fechaCondesada = ($anioCondesado + $mesCondesado + $diaCondesado);
				
				 //Se obtiene el importe del concepto
				 $importeCon = $cajaConceptos -> costo;
				 $importeConcepto = explode(".",$importeCon);
				 $importeEntero = $importeConcepto[0].$importeConcepto[1];
				 
				  //Se valida los digitos del importe y se obtendra el importe a multiplicar
				  if(strlen($importeEntero) == 3)
					$importeMultiplicar = "00000".$importeEntero;

				  else if(strlen($importeEntero) == 4)
					$importeMultiplicar = "0000".$importeEntero;

				  else if(strlen($importeEntero) == 5)
					$importeMultiplicar = "000".$importeEntero;
						
				  else if(strlen($importeEntero) == 6)
					$importeMultiplicar = "00".$importeEntero;
						
				  else if(strlen($importeEntero) == 7)
					$importeMultiplicar = "0".$importeEntero;
						
				  else
					$importeMultiplicar = $importeEntero;
				
				   
				 //Esta variable contiene el numero de factor de peso el cual se multiplicara con $importeMultiplicar
				 $numFactoPeso = "37137137";
				 
				 //Iniciamos la variable suma en cero
				 $suma = 0;
				
				 //For que recorre el arrelo y realiza la multipplicacion
				 for($i = strlen($importeMultiplicar)-1; $i >= 0; $i--)
				 {
					//Se realiza la multiplicacion numero x numero
					$numero = substr($importeMultiplicar,$i,1) * substr($numFactoPeso,$i,1);
						  
					//Se realiza la suma con los resultados de la multiplicacion
					$suma = $suma + $numero;
				 }
				 
				 //Se obtiene el importe condesado
				 $importeCondesado =  $suma % 10;
				 
				 //Se declara la variable constante y se le da el valor 2 (EL VALOR NO CAMBIA) 
				 $constante = "2";
				 
				 //Se crea la referencia
				 $referencia1 = $colomos.$tramiteRef.$registroRef.$conceptoRef.$ingenieria.$actaExtrasTitulos.$periodo.$fechaCondesada.$importeCondesado.$constante;
				  
				 //Para referencia de 37 digitos
				 $factorPesoDV = "2319171311231917131123191713112319171311231917131123191713112319171311";
				 
				 //Se inicia la variable $sumRef en cero
				 $sumRef = 0;
				  
				 //For que recorre el arrelo y realiza la multipplicacion
				 for($x = strlen($referencia1)-1; $x >= 0; $x--)
				 {
					$y = $x+$x;
					//Se realiza la multiplicacion numero x numero
					$numeroRef = substr($referencia1,$x,1) * substr($factorPesoDV,$y,2);
						
					//Se realiza la suma con los resultados de la multiplicacion
					$sumRef = $sumRef + $numeroRef;
				 }
				 
				 //Se obtiene el remanente
				 $digitoVer = ($sumRef % 97) + 1;
				 
				 if(strlen($digitoVer) == 1)
				   $digitoVerif = "0".$digitoVer;
				   
				 else
					  $digitoVerif = $digitoVer;
					  
				 $referenciaCompleta = $referencia1.$digitoVerif;		
			
				  //Se inicia la creacion del PDF
				  $pdf = new FPDF();
				  $pdf -> Open();
				  
				  $pdf -> AddPage();	
				  // $pdf -> Image('public/img/formatoFichaPagoLinea.JPG', 5, 20, 200, 220);
				  
				  //COPIA CLIENTE
				  $pdf -> SetXY(110,35);
				  $pdf -> SetFont('Arial','',10);				            
				  $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
				  $pdf -> SetXY(10,41);
				  $pdf -> SetFont('Arial','',7);
				  $pdf -> MultiCell(80,3,$registro." - ".$alumnos -> vcNomAlu,0,'L',0);
				  $pdf -> SetXY(110,44);
				  $pdf -> SetFont('Arial','',10);				            
				  $pdf -> MultiCell(90,4,$referenciaCompleta,0,'L',0);                
				  $pdf -> SetXY(20,52);
				  $pdf -> MultiCell(90,4,$fechaLimitePago,0,'L',0);                
				  $pdf -> SetFont('Arial','',7);				            
				  $pdf -> ln(8);
				  $pdf -> MultiCell(60,4,$cajaConceptos -> nombre,0,'L',0); 
				  $pdf -> ln(2);
				  $pdf -> MultiCell(60,4,Session::get_data('nombre_periodo'),0,'L',0);  
				  $pdf -> SetXY(78,65);
				  $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
				  $pdf -> SetXY(78,76);
				  $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
				  $pdf -> SetXY(92,100);
				  $pdf -> MultiCell(60,4,"BANCO",0,'L',0);  

				  //COPIA BANCO
				  $pdf -> SetXY(110,155);
				  $pdf -> SetFont('Arial','',10);				            
				  $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
				  $pdf -> SetXY(10,160);
				  $pdf -> SetFont('Arial','',7);		
				  $pdf -> MultiCell(80,3,$registro." - ".$alumnos -> vcNomAlu,0,'L',0);
				  $pdf -> SetXY(110,163);
				  $pdf -> SetFont('Arial','',10);				            
				  $pdf -> MultiCell(90,4,$referenciaCompleta,0,'L',0);                
				  $pdf -> SetXY(20,170);
				  $pdf -> MultiCell(90,4,$fechaLimitePago,0,'L',0);                
				  $pdf -> SetFont('Arial','',7);				            
				  $pdf -> ln(8);
				  $pdf -> MultiCell(60,4,$cajaConceptos -> nombre,0,'L',0);   
				  $pdf -> ln(2);
				  $pdf -> MultiCell(60,4,Session::get_data('nombre_periodo'),0,'L',0);  
				  $pdf -> SetXY(78,183);
				  $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
				  $pdf -> SetXY(78,195);
				  $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
				  $pdf -> SetXY(92,219);
				  $pdf -> MultiCell(60,4,"CLIENTE",0,'L',0);    
				  $pdf -> SetXY(5,243);
				  $pdf -> SetFont('Arial','BU',9);	
				  $pdf -> MultiCell(200,4,"VERIFICA QUE LA INFORMACIÓN SEA CORRECTA ANTES DE EFECTUAR TU PAGO, YA QUE NO SE APLICARAN REEMBOLSOS",0,'L',0);   
				  $pdf -> SetXY(5,250);
				  $pdf -> SetFont('Arial','B',9);	
				  $pdf -> MultiCell(200,4,"IMPRESO : ".date('d/m/Y')." a las ".date('H:i')." hrs.",0,'L',0);  

				  $pdf -> Output("public/files/fichas/".$registro.".pdf");	
				  $this->redirect("files/fichas/".$registro.".pdf");	
			  }		
			  else{
				Flash::error('TU FICHA NO ESTÁ DISPONIBLE AUN O ESTAS DADO DE BAJA');
				die();
			  }	
		}
		//Termina funcion de ficha de pago
		
		
        function cambiarContrasena( $vacia ){
		
		    $Alumnos		= new AlumnoController();
			//Valida Acceso por URL
		    AlumnoController::url_validacion();

			//$this->validarEncuesta();
			
            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();
			
			$visitas	= new VisitasContador();
			// alumno/cambiarContrasena le corresponde el id 7
			$visitas->incrementar(7);
			
            if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('general/inicio');
            }

            $id = Session::get_data('registro');

            // Eliminar las variables que van a pertenecer a este método.
            unset ( $this->usuario );
            unset ( $this->maestro );
			unset( $this->vacia );

            $this->usuario = $usuarios->find_first( "registro = '".$id."'" );
            $this->alumno = $alumnos->find_first( "miReg = ".$id );
			$this->vacia = $vacia;
        } // function cambiarContrasena()

        function cambiandoContrasena(){

            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();

            if(Session::get_data('tipousuario')!="ALUMNO"){
                    $this->redirect('general/inicio');
            }

            $id = Session::get_data('registro');

            $contrasena = $this->post( "contrasena" );

            if( !isset($contrasena) || $contrasena == "" )
                $this->redirect('alumno/cambiarContrasena/1');

            // Eliminar las variables que van a pertenecer a este método.
            unset( $this->usuario );
            unset( $this->exito );

            $this->exito = 0;
			$semilla = new Semilla();
            foreach( $usuarios->find_all_by_sql( "
					update usuarios
					set clave = AES_ENCRYPT('".$contrasena."','".$semilla->getSemilla()."')
					where registro = '".$id."'" ) as $usuario ){
				$this->exito = 1;
				break;
            }
			$semilla = new Semilla();
			foreach( $usuarios->find_all_by_sql( 
					"select AES_DECRYPT(clave,'".$semilla->getSemilla()."') as clave
					from usuarios
					where registro = '".$id."'" ) as $usuario ){
				$this->usuario = $usuario;
			}
			$this->alumno = $alumnos->find_first( "miReg = ".$id );

        } // function cambiandoContrasena()
		
        function ocultarVerPWD( $flag ){
			
            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();
			
			$this->set_response('view');
			
            if(Session::get_data('tipousuario')!="ALUMNO"){
                    $this->redirect('general/inicio');
            }

            $id = Session::get_data('registro');
			
			unset( $this->valor );
			unset( $this->usuario );
			$semilla = new Semilla();
			foreach( $usuarios->find_all_by_sql( 
				"select AES_DECRYPT(clave,'".$semilla->getSemilla()."') as clave
				from usuarios
				where registro = '".$id."'" ) as $usuario ){
			
				if( $flag == 1 ){
					$this->valor = 0;
					$this->usuario = $usuario->clave;
				}
				else{
					$this->valor = 1;
					$this->usuario = $clave = "****************";
				}
				$this->render_partial( "ocultarVerPWD" );
			}
        } // function ocultarVerPWD( $flag )
		
		function checar_si_alumno_tiene_problemas($registro){
			$AlumnosProblemas = new AlumnosProblemas();
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			// Si regresa true significa que el alumno deberá acudir a Cálculo
			if( $alumnosProblemas = $AlumnosProblemas->find_first("registro = ".$registro." and periodo = ".$periodo." and aun_con_problemas = 1") ){
				switch($alumnosProblemas->descripcion){
					case "KARDEX": Session::set_data('alumnos_problema', "KARDEX"); break;
					case "PAGO": Session::set_data('alumnos_problema', "PAGO"); break;
					case "EXTRANJERO": Session::set_data('alumnos_problema', "EXTRANJERO"); break;
					
				}
				$this->redirect("general/acudir_calculo");
				return;
			}
		} // function checar_si_alumno_tiene_problemas($registro)
		
		
		//Funcion que valida que solo el usuario indicado ingrese a actulizar su informacion
		function validaActualizacion(){
		  if(Session::get("statusAlumno") == "SinActualizacion"){
		    return true;
		  }
		  else{
		    $this -> redirect("general/inicio");
		  }
		}
		
		
		//Funcion que actualiza los datos del alumno
		function updateDatosAlumno(){
		
            $objeto = new alumnos(); 
			$Alumnos = new AlumnoController();
			
			$result = $objeto -> buscar_registro_alumno(Session::get('registro'));

            foreach($result AS $value){
			  $curp = $value -> curp;
			  $statusAl = $value -> estado;
			}
			
		   	//Valdia que el curp este correcta
			if($this -> post('validarCurp') == "si"){
		      $CURPValida = AlumnoController::validaCURP($this -> post('curp'));
			}
			else{
			  $CURPValida = true;
			}
			
			if($this -> post('direccion') == "" || $this -> post('direccion') == null){
			  Flash::error('Es necesario ingresar una direcci&oacute;n valida');
			  die();
			}
			  
			else if($this -> post('colonia') == "" || $this -> post('colonia') == null){
			  Flash::error('Es necesario ingresar una colonia valida');
			  die();
			}
			
			else if(strlen(trim($curp)) < 18 && strlen(trim($this -> post('curp'))) < 18){
			    Flash::error('CURP incorrecta. Es necesario corregirla');
			    die();
			}
			
			else if($CURPValida == false){
			  Flash::error('CURP incorrecta. Es necesario corregirla');
			  die();
			}
	
			else if($this -> post('sexo') == "0" || $this -> post('sexo') == null){
			  Flash::error('Es necesario ingresar el genero');
			  die();
			}
			
			else if($this -> post('dia') == "0" || $this -> post('mes') == "0" || $this -> post('anio') == "0"){
			  Flash::error('Es necesario ingresar la fecha de nacimeinto');
			  die();
			}
			
			else if($this -> post('municipioNac') == "" || $this -> post('estadoNac') == "" ){
			  Flash::error('Es necesario ingresar el municipio y estado de lugar de nacimiento');
			  die();
			}
			
			else if($this -> post('municipio') == "" || $this -> post('estado') == ""){
			  Flash::error('Es necesario ingresar el municipio y estado del lugar donde vive');
			  die();
			}
			
			else if(strlen(trim($this -> post('nombre_madre'))) < 8 || strlen(trim($this -> post('nombre_madre'))) < 8 ){
			  Flash::error('El nombre del Padre o Madre debe de tener un minimo de 8 caracteres');
			  die();
			}
			
			else if($this -> post('dependeEco') == "3" && $this -> post('otroEco') == "" && $statusAl == "OK"){
			  Flash::error('Es necesario ingresar de quien depende economicamente');
			  die();
			}
			
			else if($this -> post('seguridad_social') == "" && $statusAl == "OK"){
			  Flash::error('Es necesario contestar si cuenta con algun tipo de seguridad social');
			  die();
			}
					  
			else if($this -> post('cp') == "" || $this -> post('cp') == null){
			  Flash::error('Es necesario ingresar el codigo postal');
			  die();
			}
			 
			else if($this -> post('telefono') == "" || $this -> post('telefono') == null){
			  Flash::error('Es necesario ingresar tel&eacute;fono de casa');
			  die();
			}
			
			else if($this -> post('email') == "" || $this -> post('email') == null){
			  Flash::error('Es necesario que ingreses tu E-mail');
			  die();
			}
			  
			else{
			    //Se valida el estatus del comentario
				if($this -> post('comentario') != ""){
				  $statusC = 1;
				}
				  
				if($this -> post('comentario') == ""){
				  $statusC = 0;
				}
				  
				
				$Alumnos = new XalumnosPersonal();
				$comentariosActualizacionDatos = new comentariosActualizacionDatos();
				$informacionEgresados = new informacionEgresados();
				$seguroFacultativo = new ServicioMedico();
				
				$Alumnos -> find_first('registro = '.Session::get('registro'));
				$seguroFacultativo -> find_first('registro = '.Session::get('registro'));
				
				$Alumnos -> domicilio = utf8_decode($this -> post('direccion','upper'));
				$Alumnos -> colonia = utf8_decode($this -> post('colonia','upper'));
				$Alumnos -> cp = utf8_decode($this -> post('cp','upper'));			
				$Alumnos -> telefono = $this -> post('telefono');
				$Alumnos -> celular = $this -> post('celular');
				$Alumnos -> estado = utf8_decode($this -> post('estado','upper'));
				$Alumnos -> ciudad = utf8_decode($this -> post('municipio','upper'));
				$Alumnos -> correo = $this -> post('email');

				if(strlen(trim($curp)) < 18 )
				  $Alumnos -> curp = $this -> post('curp','upper');
				   
				  $Alumnos -> sexo = $this -> post('sexo','upper');
				  $Alumnos -> fnacimiento = $this -> post('anio')."-".$this -> post('mes')."-".$this -> post('dia');
				  $Alumnos -> municipioNac = $this -> post('municipioNac','upper');
				  $Alumnos -> estadoNac = $this -> post('estadoNac','upper');
				  $Alumnos -> nombre_padre = utf8_decode($this -> post('nombre_padre','upper'));
				  $Alumnos -> nombre_madre = utf8_decode($this -> post('nombre_madre','upper'));
				  IF($statusAl == "OK"){
				    $seguroFacultativo -> trabaja = $this -> post('Qtrabaja');
				    $seguroFacultativo -> seguridad_social = $this -> post('seguridad_social');
				    $seguroFacultativo -> depende_economicamente = $this -> post('dependeEco').",".$this -> post('otroEco');
				  }
				  IF($this -> post('seguridad_social') == "SI" && $statusAl == "OK"){
				    $seguroFacultativo -> dependencia_seguro = $this -> post('dependencia_seguro');
				    $seguroFacultativo -> parte_de_quien_seguro = $this -> post('parte_de_quien_seguro');
				    $seguroFacultativo -> numero_seguro_social = $this -> post('seguro');
				    $seguroFacultativo -> numClinica = $this -> post('numClinica');
				    $seguroFacultativo -> fechaMovimiento = $this -> post('fechaMovimiento');
				  }
				  
				$seguroFacultativo -> update();
				
                $Alumnos -> update();
				
				$comentarioAl = $comentariosActualizacionDatos -> find_first('registro = '.Session::get('registro'));
				
				if(($comentarioAl == false || $comentarioAl == null)){
				  $comentariosActualizacionDatos -> comentario = utf8_decode($this -> post('comentario'));
				  $comentariosActualizacionDatos -> registro = Session::get('registro');
				  $comentariosActualizacionDatos -> fecha = date('Y-m-d H:i:s');
				  $comentariosActualizacionDatos -> periodo = Session::get_data('periodo');
				  $comentariosActualizacionDatos -> statusComentario = "$statusC";
				  $comentariosActualizacionDatos -> save();
                }
				
				
				if($Alumnos -> update() || $comentariosActualizacionDatos -> save() || $seguroFacultativo -> update()){
				  Session::unset_data('statusAlumno');
				  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="TRUE" maxlength="0"/>';
				  Flash::success('Tu Informaci&oacute;n ha sido actualizada correctamente');
				  
				  if($this -> post('seguridad_social') == "NO" && $statusAl == "OK"){
						$Alumnos	= new AlumnoController();
						AlumnoController::solicitudSeguroPDF();
				  }
				   die();
				}
				else{
				  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
				  Flash::error('Hubo un error al actualizar tu información');
				  die();
				}
			}
		}
		
		//Funcion que modifica si el formato de seguro facultativo ya fue impreso
		function updateServicioMedico($registro){
		
		  $seguroFacultativo = new ServicioMedico();
		  $seguroFacultativo -> find_first('registro = '.$registro);
		  
		  $seguroFacultativo -> impresion = "1";
		  $result = $seguroFacultativo -> update();

		  die();
		}
		
		/*
		 * Funcion que sirve para validar el CURP con los caracteres correspondientes
		*/
		static public function validaCURP($curp){
		  
		  if(preg_match("/^([a-z]{4})([0-9]{6})([a-z]{6})([0-9]{2})$/i",trim($curp))){
		    return true;
		  }
		  else{
		    return false;
		  }
		}
		
		
		/*
		 * Funcion que genera la solicitu del seguro facultativo en PDF
		*/
        public function solicitudSeguroPDFAL($registro){

		   $objeto = new alumnos(); 
		   $result = $objeto -> buscar_registro_alumno($registro);

			foreach($result AS $value){
			  $a_materno = $value -> a_materno;
			  $a_paterno = $value -> a_paterno;
			  $nombre = $value -> nombres;
			  $registro = $value -> registro;
			  $carrera = $value -> carrera;
			  $direccion = $value -> direccion;
			  $colonia  = $value -> colonia;
			  $telefono  = $value -> telefono;
			  $curp   = $value -> curp;
			  $cp   = $value -> cp;
			  $semestre = $value -> semestre; 
			  $grupo = $value -> grupo; 
			  $periodoIngreso = $value -> periodo_ingreso;
			  if($value -> sexo == "M")
				$sexo = 2;
				
			  else
				$sexo = 1;
			  
			  $municipioNac = $value -> municipioNac;
			  $estadoNac = $value -> estadoNac;
			  $municipio = $value -> municipio;
			  $estado = $value -> estadoAl;
			  $trabaja = $value -> trabaja;
			  $depende_economicamente = split(',',$value -> depende_economicamente);
			  $economicamente = $depende_economicamente[0];
			  if($economicamente == 3)
			    $otroEco = $depende_economicamente[1];
				
			  else
			    $otroEco = "";
				
			  if(strlen($value -> celular) < 6)
			    $celular = "----";
			  
			  else
			    $celular = $value -> celular;

			  $nombre_padre = $value -> nombre_padre;
			  $nombre_madre = $value -> nombre_madre;
			  $dia = substr($value->fecha_nacimiento,8,2);
			  $mes = substr($value->fecha_nacimiento,5,2);
			  $anio = substr($value->fecha_nacimiento,0,4);
			  
			  $irandom = rand(3,4);
			}
				
		  //Se inicia el PDF
		  $pdf = new FPDF();
		  $pdf -> Open();
		  $pdf -> AddFont('Verdana','','verdana.php');
		  $pdf -> AddPage();
		  
		  //Primera pagina del PDF
	      //Encabezado
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SOLICITUD PARA LA INCORPORACIÓN DE ESTUDIANTES AL',0,'C',0);
		  $pdf->SetXY(160,10);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(35,4,'',0,'C',1);//date('d-m-Y')
		  $pdf->line(160,14,195,14);
		  $pdf->line(160,14,160,10);
		  $pdf->line(195,14,195,10);
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SEGURO FACULTATIVO DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  $pdf->SetXY(160,14);
		  $pdf->MultiCell(35,4,'FECHA',0,'C',0);
		  
		  $pdf->SetXY(40,24);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'REGISTRO DEL ALUMNO : ',0,'R',0);
		  $pdf->SetXY(90,24);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(25,4,$registro,0,'C',1);
		  $pdf->line(90,28,115,28);
		  
		  $pdf->SetXY(40,31);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'CARRERA / GRADO /GRUPO : ',0,'R',0);
		  $pdf->SetXY(83,31);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(112,4,$carrera." / ".$semestre." / ".$grupo,0,'L',1);
		  $pdf->line(83,35,195,35);
		 
		  $pdf->SetXY(40,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(13,4,'CURP : ',0,'L',0);
		  $pdf->SetXY(52,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(60,4,strtoupper($curp),0,'L',1);
		  $pdf->line(52,42,112,42);
		  
		  $pdf->SetXY(125,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(35,4,'TEL / CEL : ',0,'L',0);
		  $pdf->SetXY(142,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(53,4,$telefono.' / '.$celular,0,'L',1);
		  $pdf->line(142,42,195,42);
		  //Termina encabezado
		  
		  //Cuadros de informacion plantel e imss
		  $pdf->Rect(5,45,94,22);
		  $pdf->SetXY(5,45);
		  $pdf->MultiCell(94,4,'DATOS DEL PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(5,50);
		  $pdf->MultiCell(18,4,'NOMBRE : ',0,'R',0);
		  $pdf->SetXY(23,50);
		  $pdf->MultiCell(75,4,'        CENTRO DE ENSEÑANZA TECNICA INDUSTRIAL',0,'R',0);
		  $pdf->line(18,54,99,54);
		  $pdf->SetXY(5,55);
		  $pdf->MultiCell(18,4,'CLAVE : ',0,'R',0);
		  $pdf->SetXY(23,55);
		  $pdf->MultiCell(75,4,'           14NET0020C',0,'L',0);
		  $pdf->line(18,59,99,59);
		  $pdf->SetXY(5,60);
		  $pdf->MultiCell(30,4,'NIVEL EDUCATIVO : ',0,'R',0);
		  $pdf->SetXY(43,60);
		  $pdf->MultiCell(55.8,4,'INGENIERIA',0,'L',1);
		  $pdf->line(28,64,99,64);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->SetLineWidth(.4);
		  $pdf->Rect(101,45,94,22);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetLineWidth(.2);
		  $pdf->SetXY(101,45);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(94,4,'PARA USO EXCLUSIVO DEL  IMSS',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(101,50);
		  $pdf->MultiCell(53,4,'REGISTRO DEL IMSS DEL PLANTEL : ',0,'L',0);
		  $pdf->SetXY(152,50);
		  $pdf->MultiCell(75,4,'        	R13 99003-32-3',0,'L',0);
		  $pdf->line(146,54,193,54);
		  $pdf->SetXY(101,55);
		  $pdf->MultiCell(64,4,'NUMERO DE AFILIACIÓN DEL ESTUDIANTE : ',0,'L',0);
		  $pdf->line(158,59,193,59);
		  $pdf->SetXY(101,60);
		  $pdf->MultiCell(64,4,'NUMERO DE LA UNIDAD MEDICA FAMILIAR : ',0,'L',0);
		  $pdf->line(158,64,193,64);
		  //Termina creacion de cuadros informacion
		  
		  //Titulo Datos Estudiante
		  $pdf->SetXY(5,68);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(190,4,'DATOS DEL ESTUDIANTE',0,'C',0);
		  
		  $pdf->SetXY(8,75);
		  $pdf->MultiCell(7,4,'A)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(12,75);
		  $pdf->MultiCell(17,4,'NOMBRE:',0,'R',0);
		  $pdf->SetXY(29,75);
		  $pdf->MultiCell(50,4,strtoupper($a_paterno),0,'C',1);
		  $pdf->SetXY(29,79);
		  $pdf->MultiCell(50,4,'APELLIDO PATERNO',0,'C',0);
		  $pdf->SetXY(79,75);
		  $pdf->MultiCell(50,4,strtoupper($a_materno),0,'C',1);
		  $pdf->SetXY(79,79);
		  $pdf->MultiCell(50,4,'APELLIDO MATERNO',0,'C',0);
		  $pdf->SetXY(129,75);
		  $pdf->MultiCell(62,4,strtoupper($nombre),0,'C',1);
		  $pdf->SetXY(129,79);
		  $pdf->MultiCell(65,4,'NOMBRES',0,'C',0);
		  $pdf->line(79,79,79,75);
		  $pdf->line(129,79,129,75);
		  $pdf->line(29,79,191,79);
		  
		  $pdf->SetXY(8,85);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'B)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,85);
		  $pdf->MultiCell(17,4,'SEXO:',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(64,85);
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(69,85);
		  $pdf->MultiCell(20,4,'MASCULINO',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(115,85);
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(119,85);
		  $pdf->MultiCell(20,4,'FEMENINO',0,'C',0);
		  $pdf->SetXY(181,85);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,85);
		  $pdf->MultiCell(8,4,$sexo,0,'C',1);
		  $pdf->SetXY(187.5,85);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,89,191,89);
		  
		  $pdf->SetXY(8,91);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'C)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,91);
		  $pdf->MultiCell(40,4,'FECHA DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(103,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(106,91);
		  $pdf->MultiCell(10,4,$dia,0,'C',1);
		  $pdf->SetXY(114,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(106,95);
		  $pdf->MultiCell(10,4,'DIA',0,'C',0);
		  $pdf->line(106,95,116,95);
		  $pdf->SetXY(136,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(139,91);
		  $pdf->MultiCell(10,4,$mes,0,'C',1);
		  $pdf->SetXY(147,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(139,95);
		  $pdf->MultiCell(10,4,'MES',0,'C',0);
		  $pdf->line(139,95,149,95);
		  $pdf->SetXY(165,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(168,91);
		  $pdf->MultiCell(10,4,$anio,0,'C',1);
		  $pdf->SetXY(176,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(168,95);
		  $pdf->MultiCell(10,4,'AÑO',0,'C',0);
		  $pdf->line(168,95,178,95);
		  
		  $pdf->SetXY(8,101);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'D)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,101);
		  $pdf->MultiCell(40,4,'LUGAR DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(55,101);
		  $pdf->MultiCell(50,4, strtoupper($municipioNac),0,'C',1);
		  $pdf->line(55,105,105,105);
		  $pdf->SetXY(55,105);
		  $pdf->MultiCell(50,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(107,101);
		  $pdf->MultiCell(60,4,strtoupper($estadoNac),0,'C',1);
		  $pdf->line(107,105,167,105);
		  $pdf->SetXY(107,105);
		  $pdf->MultiCell(60,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,111);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'E)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,111);
		  $pdf->MultiCell(40,4,'DOMICILIO:',0,'L',0);
		  $pdf->SetXY(35,111);
		  $pdf->MultiCell(95,4, strtr(strtoupper($direccion),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(130,115,130,111);
		  $pdf->SetXY(130.1,111);
		  $pdf->MultiCell(62,4,strtr(strtoupper($colonia),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1); 
		  $pdf->line(35,115,192,115);
		  $pdf->SetXY(35,115);
		  $pdf->MultiCell(95,4,'CALLE Y NUMERO',0,'C',0);
		  $pdf->SetXY(130.1,115);
		  $pdf->MultiCell(62,4,'COLONIA',0,'C',0);
		  $pdf->SetXY(19,119);
		  $pdf->MultiCell(35,4, strtoupper($cp),0,'C',1);
		  $pdf->line(19,123,54,123);
		  $pdf->SetXY(19,123);
		  $pdf->MultiCell(35,4,'CODIGO POSTAL',0,'C',0);
		  $pdf->SetXY(64.5,119);
		  $pdf->MultiCell(55,4, strtoupper($municipio),0,'C',1);
		  $pdf->line(64.5,123,119,123);
		  $pdf->SetXY(64.5,123);
		  $pdf->MultiCell(55,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(130.1,119);
		  $pdf->MultiCell(62,4, strtoupper($estado),0,'C',1);
		  $pdf->line(130,123,192,123);
		  $pdf->SetXY(130.1,123);
		  $pdf->MultiCell(62,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,128);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'F)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,128);
		  $pdf->MultiCell(40,4,'NOMBRE DE LOS PADRES:',0,'L',0);
		  $pdf->SetXY(56,128);
		  $pdf->MultiCell(20,4,'PADRE:',0,'R',0);
		  $pdf->SetXY(76,128);
		  $pdf->MultiCell(101,4,strtr(strtoupper($nombre_padre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,132,177,132);
		  $pdf->SetXY(56,134);
		  $pdf->MultiCell(20,4,'MADRE:',0,'R',0);
		  $pdf->SetXY(76,134);
		  $pdf->MultiCell(101,4,strtr(strtoupper($nombre_madre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,138,177,138);
		  
		  $pdf->SetXY(8,143);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'G)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,143);
		  $pdf->MultiCell(50,4,'¿APARTE DE ESTUDIAR TRABAJA?',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(84,143);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(86,143);
		  $pdf->MultiCell(10,4,'SI',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(119,143);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(124,143);
		  $pdf->MultiCell(10,4,'NO',0,'C',0);
		  $pdf->SetXY(181,143);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,143);
		  $pdf->MultiCell(8,4,'2',0,'C',1);//$trabaja
		  $pdf->SetXY(187.5,143);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,147,191,147);
		  
		  $pdf->SetXY(8,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'H)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,148);
		  $pdf->MultiCell(65,4,'¿DE QUIEN DEPENDE ECONOMICAMENTE?',0,'L',0);
		  $pdf->SetXY(84,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetXY(87,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(15,4,'PADRES',0,'R',0);
		  $pdf->SetXY(119,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetXY(124,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(18,4,'CONYUGE',0,'R',0);
		  $pdf->SetXY(181,148);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,148);
		  $pdf->MultiCell(8,4,'1',0,'C',1);//$economicamente
		  $pdf->SetXY(187.5,148);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,152,191,152);
		  $pdf->SetXY(74,153);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'3)',0,'C',0);
		  $pdf->SetXY(81,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(50,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(117,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(60,4,'',0,'L',1);// strtoupper($otroEco)
		  $pdf->line(117,157,177,157);
		   
		  $pdf->SetXY(8,159);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'I)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,159); 
		  $pdf->MultiCell(110,4,'¿EN QUE TRABAJA LA PERSONA DE LA QUE DEPENDE ECONOMICAMENTE?',0,'L',0); 
		  $pdf->SetXY(181,159);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,159);
		  $pdf->MultiCell(8,4,$irandom,0,'C',1);
		  $pdf->SetXY(187.5,159);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,163,191,163);
		  $pdf->SetXY(18,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'SERVIDOR PUBLICO',0,'C',0);
		  $pdf->SetXY(18,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(23,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'COMERCIANTE O INDUSTRIAL',0,'L',0);
		  $pdf->SetXY(18,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(23,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'OBRERO',0,'L',0);
		  $pdf->SetXY(65,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(70,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(51,4,'EMPLEADO EMPRESA PARTICULAR',0,'L',0);
		  $pdf->SetXY(65,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(70,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'MARINO O MILITAR',0,'L',0);
		  $pdf->SetXY(65,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'8)',0,'C',0);
		  $pdf->SetXY(70,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(105,172);
		  $pdf->MultiCell(87,4,'',0,'L',1);
		  $pdf->line(105,176,192,176);
		  $pdf->SetXY(120,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(125,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(60,4,'PROFESION U OFICIO POR SU CUENTA',0,'L',0);
		  $pdf->SetXY(110,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(115,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(80,4,'AGRICULTOR,GANADERO,CAMPESINO O PESCADOR',0,'L',0);
		  
		  $pdf->SetXY(8,181);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'J)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,181); 
		  $pdf->MultiCell(155,4,'¿SE ENCUENTRA PROTEGIDO, YA SEA COMO TRABAJADOR O COMO BENEFICIARIO DE SUS PADRES O DE SU',0,'L',0); 
		  $pdf->SetXY(181,181);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,181);
		  $pdf->MultiCell(8,4,'2',0,'C',1);
		  $pdf->SetXY(187.5,181);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,185,191,185);
		  $pdf->SetXY(23,185); 
		  $pdf->MultiCell(100,4,'CONYUGE EN ALGUNA INSTITUCIÓN DE SEGURIDAD?',0,'L',0); 
		  $pdf->SetXY(105,185); 
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(100,4,'1)                                2)',0,'L',0); 
		  $pdf->SetXY(109,185); 
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(100,4,'SI                                NO',0,'L',0); 
		  
		  $pdf->SetXY(8,192);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'K)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,192); 
		  $pdf->MultiCell(130,4,'¿QUÉ INSTITUCIÓN LE DA SERVICIOS MEDICOS?',0,'L',0); 
		  $pdf->SetXY(181,192);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,192);
		  $pdf->MultiCell(8,4,'7',0,'C',1);
		  $pdf->SetXY(187.5,192);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,196,191,196);
		  $pdf->SetXY(18,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'IMSS',0,'L',0);
		  $pdf->SetXY(18,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(23,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'PEMEX',0,'L',0);
		  $pdf->SetXY(40,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(45,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'ISSSTE',0,'L',0);
		  $pdf->SetXY(62,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(67,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'SECRETARIA DE MARINA',0,'L',0);
		  $pdf->SetXY(111,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(116,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(65,4,'SECRETARIA DE LA DEFENSA NACIONAL',0,'L',0);
		  $pdf->SetXY(40,201);
		  $pdf->SetFont('Arial','B',8);  
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(45,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(70,4,'INSTITUCION NACIONAL DE CREDITO (BANCO)',0,'L',0);
		  $pdf->SetXY(111,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(116,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTRA    ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(148,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(44,4,'PARTICULAR',0,'L',1);
		  $pdf->line(148,205,192,205);
		  
		  $pdf->SetXY(19,208);
		  $pdf->MultiCell(160,4,'BAJO PROTESTA DE DECIR LA VERDAD DECLARO QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS',0,'L',0);
		  
		  $pdf->SetXY(5,235);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(60,233,140,233);
		  $pdf->MultiCell(190,4,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
          //Termina creacion DATOS ESTUDIANTE
		  
		  //Cuadro de datos del estudiante
		  $pdf->Rect(5,72,190,167);
		  //Termina cuadro de datos del estudiante
		  
		  //Cuadros firmas
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(5,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(5,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(5,247,99,247);
		  $pdf->MultiCell(94,4,'PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetXY(5,248);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(94,4,'SE CERTIFICA QUE EL SOLICITANTE ES ESTUDIANTE DEL PLANTEL',0,'C',0);
		  $pdf->SetXY(5,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(5,272);
		  $pdf->MultiCell(94,4,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(5,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->line(21,267,84,267);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(101,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(101,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(101,247,195,247);
		  $pdf->MultiCell(94,4,'IMSS DELEGACIONAL',0,'C',0);
		  $pdf->SetXY(101,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(101,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(116,267,180,267);
		  //Termina cuadros de firmas
		  
		  //Inicia segunda hoja de PDF
		  $pdf->AddPage();
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  $pdf->SetXY(167,9);
		  $pdf->MultiCell(20,4,'',1,'C',0);
		  $pdf->SetXY(167,13);
		  $pdf->SetFont('Arial','',10);
		  $pdf->MultiCell(20,4,'FOLIO',0,'C',0);
		  $pdf->SetXY(38,18);
          $pdf->SetFont('Arial','B',12);
		  $pdf->MultiCell(131,4,'COMPROBANTE DE SOLICITUD',0,'C',0);
		  $pdf->SetXY(38,23);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'INCORPORACIÓN DE ESTUDIANTES AL SEGURO FACULTATIVO',0,'C',0);
		  $pdf->SetXY(38,27);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  
		  $pdf->Rect(20,50,165,35);
		  $pdf->SetXY(20,51);
		  $pdf->SetFont('Arial','B',11);
		  $pdf->MultiCell(165,4,'IMPORTANTE',0,'C',0);
		  $pdf->line(20,55,185,55);
		  $pdf->SetXY(30,60);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(30,70);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(33,59);
		  $pdf->SetFont('Arial','',11);
		  $pdf->MultiCell(150,4,'EN CASO DE REQUERIR SERVICIO MEDICO, PRESENTE ESTE DOCUMENTO EN LA UNIDAD DE MEDICINA FAMILIAR QUE ASIGNE EL INSTITUTO.',0,'J',0);
		  $pdf->SetXY(33,69);
		  $pdf->MultiCell(150,4,'ESTE COMPROBANTE DEBERA SER CANJEADO POR EL AVISO AUTOMÁTICO DE INSCRIPCION EN EL SEGURO FACULTATIVO EN LOS SERVICIOS ESCOLARES DEL PLANTEL',0,'J',0);
		  
		  $pdf->Rect(20,87,82,40);
		  $pdf->SetXY(20,123);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
		  $pdf->line(25,122,97,122);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(103,87,82,40);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(103,88);
		  $pdf->MultiCell(82,3,'SERVICIOS ESCOLARES',0,'C',0);
		  $pdf->line(103,92,185,92);
		  $pdf->SetXY(103,119);
		  $pdf->SetFont('Arial','',6);
		  $pdf->MultiCell(82,3,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(103,104);
		  $pdf->MultiCell(20,3,'SELLO',0,'L',0);
		  $pdf->SetXY(103,116);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(82,3,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->SetXY(103,123);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(103,122,185,122);
		  
		  $pdf->Image('public/img/imss_logo.jpg',5,138,40,40);
		  $pdf->SetXY(167,140);
		  $pdf->MultiCell(20,4,'',1,'C',0);
		  $pdf->SetXY(167,144);
		  $pdf->SetFont('Arial','',10);
		  $pdf->MultiCell(20,4,'FOLIO',0,'C',0);
		  $pdf->SetXY(38,151);
          $pdf->SetFont('Arial','B',12);
		  $pdf->MultiCell(131,4,'COMPROBANTE DE SOLICITUD',0,'C',0);
		  $pdf->SetXY(38,156);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'INCORPORACIÓN DE ESTUDIANTES AL SEGURO FACULTATIVO',0,'C',0);
		  $pdf->SetXY(38,160);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  
		  $pdf->Rect(20,183,165,35);
		  $pdf->SetXY(20,184);
		  $pdf->SetFont('Arial','B',11);
		  $pdf->MultiCell(165,4,'IMPORTANTE',0,'C',0);
		  $pdf->line(20,188,185,188);
		  $pdf->SetXY(30,193);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(30,203);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(33,192);
		  $pdf->SetFont('Arial','',11);
		  $pdf->MultiCell(150,4,'EN CASO DE REQUERIR SERVICIO MEDICO, PRESENTE ESTE DOCUMENTO EN LA UNIDAD DE MEDICINA FAMILIAR QUE ASIGNE EL INSTITUTO.',0,'J',0);
		  $pdf->SetXY(33,202);
		  $pdf->MultiCell(150,4,'ESTE COMPROBANTE DEBERA SER CANJEADO POR EL AVISO AUTOMÁTICO DE INSCRIPCION EN EL SEGURO FACULTATIVO EN LOS SERVICIOS ESCOLARES DEL PLANTEL',0,'J',0);
		  
		  $pdf->Rect(20,220,82,40);
		  $pdf->SetXY(20,256);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
		  $pdf->line(25,255,97,255);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(103,220,82,40);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(103,221);
		  $pdf->MultiCell(82,3,'SERVICIOS ESCOLARES',0,'C',0);
		  $pdf->line(103,225,185,225);
		  $pdf->SetXY(103,252);
		  $pdf->SetFont('Arial','',6);
		  $pdf->MultiCell(82,3,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(103,237);
		  $pdf->MultiCell(20,3,'SELLO',0,'L',0);
		  $pdf->SetXY(103,249);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(82,3,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->SetXY(103,256);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(103,255,185,255);
		  
		  $pdf -> AddPage();
		  
		  //Primera pagina del PDF
	      //Encabezado
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SOLICITUD PARA LA INCORPORACIÓN DE ESTUDIANTES AL',0,'C',0);
		  $pdf->SetXY(160,10);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(35,4,'',0,'C',1);//date('d-m-Y')
		  $pdf->line(160,14,195,14);
		  $pdf->line(160,14,160,10);
		  $pdf->line(195,14,195,10);
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SEGURO FACULTATIVO DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  $pdf->SetXY(160,14);
		  $pdf->MultiCell(35,4,'FECHA',0,'C',0);
		  
		  $pdf->SetXY(40,24);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'REGISTRO DEL ALUMNO : ',0,'R',0);
		  $pdf->SetXY(90,24);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(25,4,$registro,0,'C',1);
		  $pdf->line(90,28,115,28);
		  
		  $pdf->SetXY(40,31);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'CARRERA / GRADO /GRUPO : ',0,'R',0);
		  $pdf->SetXY(83,31);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(112,4,$carrera." / ".$semestre." / ".$grupo,0,'L',1);
		  $pdf->line(83,35,195,35);
		 
		  $pdf->SetXY(40,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(13,4,'CURP : ',0,'L',0);
		  $pdf->SetXY(52,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(60,4,strtoupper($curp),0,'L',1);
		  $pdf->line(52,42,112,42);
		  
		  $pdf->SetXY(125,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(35,4,'TEL / CEL : ',0,'L',0);
		  $pdf->SetXY(142,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(53,4,$telefono.' / '.$celular,0,'L',1);
		  $pdf->line(142,42,195,42);
		  //Termina encabezado
		  
		  //Cuadros de informacion plantel e imss
		  $pdf->Rect(5,45,94,22);
		  $pdf->SetXY(5,45);
		  $pdf->MultiCell(94,4,'DATOS DEL PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(5,50);
		  $pdf->MultiCell(18,4,'NOMBRE : ',0,'R',0);
		  $pdf->SetXY(23,50);
		  $pdf->MultiCell(75,4,'        CENTRO DE ENSEÑANZA TECNICA INDUSTRIAL',0,'R',0);
		  $pdf->line(18,54,99,54);
		  $pdf->SetXY(5,55);
		  $pdf->MultiCell(18,4,'CLAVE : ',0,'R',0);
		  $pdf->SetXY(23,55);
		  $pdf->MultiCell(75,4,'           14NET0020C',0,'L',0);
		  $pdf->line(18,59,99,59);
		  $pdf->SetXY(5,60);
		  $pdf->MultiCell(30,4,'NIVEL EDUCATIVO : ',0,'R',0);
		  $pdf->SetXY(43,60);
		  $pdf->MultiCell(55.8,4,'INGENIERIA',0,'L',1);
		  $pdf->line(28,64,99,64);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->SetLineWidth(.4);
		  $pdf->Rect(101,45,94,22);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetLineWidth(.2);
		  $pdf->SetXY(101,45);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(94,4,'PARA USO EXCLUSIVO DEL  IMSS',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(101,50);
		  $pdf->MultiCell(53,4,'REGISTRO DEL IMSS DEL PLANTEL : ',0,'L',0);
		  $pdf->SetXY(152,50);
		  $pdf->MultiCell(75,4,'        	R13 99003-32-3',0,'L',0);
		  $pdf->line(146,54,193,54);
		  $pdf->SetXY(101,55);
		  $pdf->MultiCell(64,4,'NUMERO DE AFILIACIÓN DEL ESTUDIANTE : ',0,'L',0);
		  $pdf->line(158,59,193,59);
		  $pdf->SetXY(101,60);
		  $pdf->MultiCell(64,4,'NUMERO DE LA UNIDAD MEDICA FAMILIAR : ',0,'L',0);
		  $pdf->line(158,64,193,64);
		  //Termina creacion de cuadros informacion
		  
		  //Titulo Datos Estudiante
		  $pdf->SetXY(5,68);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(190,4,'DATOS DEL ESTUDIANTE',0,'C',0);
		  
		  $pdf->SetXY(8,75);
		  $pdf->MultiCell(7,4,'A)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(12,75);
		  $pdf->MultiCell(17,4,'NOMBRE:',0,'R',0);
		  $pdf->SetXY(29,75);
		  $pdf->MultiCell(50,4,strtoupper($a_paterno),0,'C',1);
		  $pdf->SetXY(29,79);
		  $pdf->MultiCell(50,4,'APELLIDO PATERNO',0,'C',0);
		  $pdf->SetXY(79,75);
		  $pdf->MultiCell(50,4,strtoupper($a_materno),0,'C',1);
		  $pdf->SetXY(79,79);
		  $pdf->MultiCell(50,4,'APELLIDO MATERNO',0,'C',0);
		  $pdf->SetXY(129,75);
		  $pdf->MultiCell(62,4,strtoupper($nombre),0,'C',1);
		  $pdf->SetXY(129,79);
		  $pdf->MultiCell(65,4,'NOMBRES',0,'C',0);
		  $pdf->line(79,79,79,75);
		  $pdf->line(129,79,129,75);
		  $pdf->line(29,79,191,79);
		  
		  $pdf->SetXY(8,85);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'B)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,85);
		  $pdf->MultiCell(17,4,'SEXO:',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(64,85);
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(69,85);
		  $pdf->MultiCell(20,4,'MASCULINO',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(115,85);
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(119,85);
		  $pdf->MultiCell(20,4,'FEMENINO',0,'C',0);
		  $pdf->SetXY(181,85);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,85);
		  $pdf->MultiCell(8,4,$sexo,0,'C',1);
		  $pdf->SetXY(187.5,85);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,89,191,89);
		  
		  $pdf->SetXY(8,91);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'C)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,91);
		  $pdf->MultiCell(40,4,'FECHA DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(103,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(106,91);
		  $pdf->MultiCell(10,4,$dia,0,'C',1);
		  $pdf->SetXY(114,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(106,95);
		  $pdf->MultiCell(10,4,'DIA',0,'C',0);
		  $pdf->line(106,95,116,95);
		  $pdf->SetXY(136,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(139,91);
		  $pdf->MultiCell(10,4,$mes,0,'C',1);
		  $pdf->SetXY(147,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(139,95);
		  $pdf->MultiCell(10,4,'MES',0,'C',0);
		  $pdf->line(139,95,149,95);
		  $pdf->SetXY(165,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(168,91);
		  $pdf->MultiCell(10,4,$anio,0,'C',1);
		  $pdf->SetXY(176,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(168,95);
		  $pdf->MultiCell(10,4,'AÑO',0,'C',0);
		  $pdf->line(168,95,178,95);
		  
		  $pdf->SetXY(8,101);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'D)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,101);
		  $pdf->MultiCell(40,4,'LUGAR DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(55,101);
		  $pdf->MultiCell(50,4, strtoupper($municipioNac),0,'C',1);
		  $pdf->line(55,105,105,105);
		  $pdf->SetXY(55,105);
		  $pdf->MultiCell(50,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(107,101);
		  $pdf->MultiCell(60,4,strtoupper($estadoNac),0,'C',1);
		  $pdf->line(107,105,167,105);
		  $pdf->SetXY(107,105);
		  $pdf->MultiCell(60,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,111);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'E)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,111);
		  $pdf->MultiCell(40,4,'DOMICILIO:',0,'L',0);
		  $pdf->SetXY(35,111);
		  $pdf->MultiCell(95,4, strtr(strtoupper($direccion),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(130,115,130,111);
		  $pdf->SetXY(130.1,111);
		  $pdf->MultiCell(62,4, strtr(strtoupper($colonia),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(35,115,192,115);
		  $pdf->SetXY(35,115);
		  $pdf->MultiCell(95,4,'CALLE Y NUMERO',0,'C',0);
		  $pdf->SetXY(130.1,115);
		  $pdf->MultiCell(62,4,'COLONIA',0,'C',0);
		  $pdf->SetXY(19,119);
		  $pdf->MultiCell(35,4, strtoupper($cp),0,'C',1);
		  $pdf->line(19,123,54,123);
		  $pdf->SetXY(19,123);
		  $pdf->MultiCell(35,4,'CODIGO POSTAL',0,'C',0);
		  $pdf->SetXY(64.5,119);
		  $pdf->MultiCell(55,4, strtoupper($municipio),0,'C',1);
		  $pdf->line(64.5,123,119,123);
		  $pdf->SetXY(64.5,123);
		  $pdf->MultiCell(55,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(130.1,119);
		  $pdf->MultiCell(62,4, strtoupper($estado),0,'C',1);
		  $pdf->line(130,123,192,123);
		  $pdf->SetXY(130.1,123);
		  $pdf->MultiCell(62,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,128);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'F)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,128);
		  $pdf->MultiCell(40,4,'NOMBRE DE LOS PADRES:',0,'L',0);
		  $pdf->SetXY(56,128);
		  $pdf->MultiCell(20,4,'PADRE:',0,'R',0);
		  $pdf->SetXY(76,128);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_padre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,132,177,132);
		  $pdf->SetXY(56,134);
		  $pdf->MultiCell(20,4,'MADRE:',0,'R',0);
		  $pdf->SetXY(76,134);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_madre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,138,177,138);
		  
		  $pdf->SetXY(8,143);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'G)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,143);
		  $pdf->MultiCell(50,4,'¿APARTE DE ESTUDIAR TRABAJA?',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(84,143);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(86,143);
		  $pdf->MultiCell(10,4,'SI',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(119,143);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(124,143);
		  $pdf->MultiCell(10,4,'NO',0,'C',0);
		  $pdf->SetXY(181,143);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,143);
		  $pdf->MultiCell(8,4,'2',0,'C',1);//$trabaja
		  $pdf->SetXY(187.5,143);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,147,191,147);
		  
		  $pdf->SetXY(8,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'H)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,148);
		  $pdf->MultiCell(65,4,'¿DE QUIEN DEPENDE ECONOMICAMENTE?',0,'L',0);
		  $pdf->SetXY(84,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetXY(87,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(15,4,'PADRES',0,'R',0);
		  $pdf->SetXY(119,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetXY(124,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(18,4,'CONYUGE',0,'R',0);
		  $pdf->SetXY(181,148);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,148);
		  $pdf->MultiCell(8,4,'1',0,'C',1);//$economicamente
		  $pdf->SetXY(187.5,148);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,152,191,152);
		  $pdf->SetXY(74,153);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'3)',0,'C',0);
		  $pdf->SetXY(81,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(50,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(117,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(60,4,'',0,'L',1);// strtoupper($otroEco)
		  $pdf->line(117,157,177,157);
		   
		  $pdf->SetXY(8,159);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'I)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,159); 
		  $pdf->MultiCell(110,4,'¿EN QUE TRABAJA LA PERSONA DE LA QUE DEPENDE ECONOMICAMENTE?',0,'L',0); 
		  $pdf->SetXY(181,159);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,159);
		  $pdf->MultiCell(8,4,$irandom,0,'C',1);
		  $pdf->SetXY(187.5,159);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,163,191,163);
		  $pdf->SetXY(18,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'SERVIDOR PUBLICO',0,'C',0);
		  $pdf->SetXY(18,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(23,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'COMERCIANTE O INDUSTRIAL',0,'L',0);
		  $pdf->SetXY(18,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(23,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'OBRERO',0,'L',0);
		  $pdf->SetXY(65,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(70,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(51,4,'EMPLEADO EMPRESA PARTICULAR',0,'L',0);
		  $pdf->SetXY(65,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(70,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'MARINO O MILITAR',0,'L',0);
		  $pdf->SetXY(65,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'8)',0,'C',0);
		  $pdf->SetXY(70,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(105,172);
		  $pdf->MultiCell(87,4,'',0,'L',1);
		  $pdf->line(105,176,192,176);
		  $pdf->SetXY(120,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(125,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(60,4,'PROFESION U OFICIO POR SU CUENTA',0,'L',0);
		  $pdf->SetXY(110,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(115,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(80,4,'AGRICULTOR,GANADERO,CAMPESINO O PESCADOR',0,'L',0);
		  
		  $pdf->SetXY(8,181);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'J)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,181); 
		  $pdf->MultiCell(155,4,'¿SE ENCUENTRA PROTEGIDO, YA SEA COMO TRABAJADOR O COMO BENEFICIARIO DE SUS PADRES O DE SU',0,'L',0); 
		  $pdf->SetXY(181,181);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,181);
		  $pdf->MultiCell(8,4,'2',0,'C',1);
		  $pdf->SetXY(187.5,181);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,185,191,185);
		  $pdf->SetXY(23,185); 
		  $pdf->MultiCell(100,4,'CONYUGE EN ALGUNA INSTITUCIÓN DE SEGURIDAD?',0,'L',0); 
		  $pdf->SetXY(105,185); 
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(100,4,'1)                                2)',0,'L',0); 
		  $pdf->SetXY(109,185); 
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(100,4,'SI                                NO',0,'L',0); 
		  
		  $pdf->SetXY(8,192);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'K)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,192); 
		  $pdf->MultiCell(130,4,'¿QUÉ INSTITUCIÓN LE DA SERVICIOS MEDICOS?',0,'L',0); 
		  $pdf->SetXY(181,192);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,192);
		  $pdf->MultiCell(8,4,'7',0,'C',1);
		  $pdf->SetXY(187.5,192);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,196,191,196);
		  $pdf->SetXY(18,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'IMSS',0,'L',0);
		  $pdf->SetXY(18,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(23,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'PEMEX',0,'L',0);
		  $pdf->SetXY(40,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(45,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'ISSSTE',0,'L',0);
		  $pdf->SetXY(62,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(67,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'SECRETARIA DE MARINA',0,'L',0);
		  $pdf->SetXY(111,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(116,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(65,4,'SECRETARIA DE LA DEFENSA NACIONAL',0,'L',0);
		  $pdf->SetXY(40,201);
		  $pdf->SetFont('Arial','B',8);  
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(45,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(70,4,'INSTITUCION NACIONAL DE CREDITO (BANCO)',0,'L',0);
		  $pdf->SetXY(111,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(116,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTRA    ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(148,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(44,4,'PARTICULAR',0,'L',1);
		  $pdf->line(148,205,192,205);
		  
		  $pdf->SetXY(19,208);
		  $pdf->MultiCell(160,4,'BAJO PROTESTA DE DECIR LA VERDAD DECLARO QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS',0,'L',0);
		  
		  $pdf->SetXY(5,235);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(60,233,140,233);
		  $pdf->MultiCell(190,4,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
          //Termina creacion DATOS ESTUDIANTE
		  
		  //Cuadro de datos del estudiante
		  $pdf->Rect(5,72,190,167);
		  //Termina cuadro de datos del estudiante
		  
		  //Cuadros firmas
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(5,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(5,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(5,247,99,247);
		  $pdf->MultiCell(94,4,'PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetXY(5,248);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(94,4,'SE CERTIFICA QUE EL SOLICITANTE ES ESTUDIANTE DEL PLANTEL',0,'C',0);
		  $pdf->SetXY(5,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(5,272);
		  $pdf->MultiCell(94,4,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(5,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->line(21,267,84,267);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(101,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(101,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(101,247,195,247);
		  $pdf->MultiCell(94,4,'IMSS DELEGACIONAL',0,'C',0);
		  $pdf->SetXY(101,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(101,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(116,267,180,267);
		  //Termina cuadros de firmas	  
		  
		  $pdf->Output("public/files/pdfs/seguroFacultativo/SeguroFacultativo".$registro.".pdf","F");
          $this->redirect("public/files/pdfs/seguroFacultativo/SeguroFacultativo".$registro.".pdf");	
		  die();
        } 		
		
		/*
		 * Funcion que genera la solicitu del seguro facultativo en PDF
		*/
        public function solicitudSeguroPDF(){

		   $objeto = new alumnos(); 
		   $result = $objeto -> buscar_registro_alumno(Session::get('registro'));

			foreach($result AS $value){
			  $a_materno = $value -> a_materno;
			  $a_paterno = $value -> a_paterno;
			  $nombre = $value -> nombres;
			  $registro = $value -> registro;
			  $carrera = $value -> carrera;
			  $direccion = $value -> direccion;
			  $colonia  = $value -> colonia;
			  $telefono  = $value -> telefono;
			  $curp   = $value -> curp;
			  $cp   = $value -> cp;
			  $semestre = $value -> semestre; 
			  $grupo = $value -> grupo; 
			  $periodoIngreso = $value -> periodo_ingreso;
			  if($value -> sexo == "M")
				$sexo = 2;
				
			  else
				$sexo = 1;
			  
			  $municipioNac = $value -> municipioNac;
			  $estadoNac = $value -> estadoNac;
			  $municipio = $value -> municipio;
			  $estado = $value -> estadoAl;
			  $trabaja = $value -> trabaja;
			  $depende_economicamente = split(',',$value -> depende_economicamente);
			  $economicamente = $depende_economicamente[0];
			  if($economicamente == 3)
			    $otroEco = $depende_economicamente[1];
				
			  else
			    $otroEco = "";
				
	
			  if(strlen($value -> celular) < 6)
			    $celular = "----";
			  
			  else
			    $celular = $value -> celular;

			  $nombre_padre = $value -> nombre_padre;
			  $nombre_madre = $value -> nombre_madre;
			  $dia = substr($value->fecha_nacimiento,8,2);
			  $mes = substr($value->fecha_nacimiento,5,2);
			  $anio = substr($value->fecha_nacimiento,0,4);
			  
			  $irandom = rand(3,4);
			  
			}
				
		  //Se inicia el PDF
		  $pdf = new FPDF();
		  $pdf -> Open();
		  $pdf -> AddFont('Verdana','','verdana.php');
		  $pdf -> AddPage();
		  
		  //Primera pagina del PDF
	      //Encabezado
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SOLICITUD PARA LA INCORPORACIÓN DE ESTUDIANTES AL',0,'C',0);
		  $pdf->SetXY(160,10);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(35,4,'',0,'C',1);//date('d-m-Y')
		  $pdf->line(160,14,195,14);
		  $pdf->line(160,14,160,10);
		  $pdf->line(195,14,195,10);
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SEGURO FACULTATIVO DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  $pdf->SetXY(160,14);
		  $pdf->MultiCell(35,4,'FECHA',0,'C',0);
		  
		  $pdf->SetXY(40,24);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'REGISTRO DEL ALUMNO : ',0,'R',0);
		  $pdf->SetXY(90,24);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(25,4,$registro,0,'C',1);
		  $pdf->line(90,28,115,28);
		  
		  $pdf->SetXY(40,31);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'CARRERA / GRADO /GRUPO : ',0,'R',0);
		  $pdf->SetXY(83,31);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(112,4,$carrera." / ".$semestre." / ".$grupo,0,'L',1);
		  $pdf->line(83,35,195,35);
		 
		  $pdf->SetXY(40,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(13,4,'CURP : ',0,'L',0);
		  $pdf->SetXY(52,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(60,4,strtoupper($curp),0,'L',1);
		  $pdf->line(52,42,112,42);
		  
		  $pdf->SetXY(125,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(35,4,'TEL / CEL : ',0,'L',0);
		  $pdf->SetXY(142,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(53,4,$telefono.' / '.$celular,0,'L',1);
		  $pdf->line(142,42,195,42);
		  //Termina encabezado
		  
		  //Cuadros de informacion plantel e imss
		  $pdf->Rect(5,45,94,22);
		  $pdf->SetXY(5,45);
		  $pdf->MultiCell(94,4,'DATOS DEL PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(5,50);
		  $pdf->MultiCell(18,4,'NOMBRE : ',0,'R',0);
		  $pdf->SetXY(23,50);
		  $pdf->MultiCell(75,4,'        CENTRO DE ENSEÑANZA TECNICA INDUSTRIAL',0,'R',0);
		  $pdf->line(18,54,99,54);
		  $pdf->SetXY(5,55);
		  $pdf->MultiCell(18,4,'CLAVE : ',0,'R',0);
		  $pdf->SetXY(23,55);
		  $pdf->MultiCell(75,4,'           14NET0020C',0,'L',0);
		  $pdf->line(18,59,99,59);
		  $pdf->SetXY(5,60);
		  $pdf->MultiCell(30,4,'NIVEL EDUCATIVO : ',0,'R',0);
		  $pdf->SetXY(43,60);
		  $pdf->MultiCell(55.8,4,'INGENIERIA',0,'L',1);
		  $pdf->line(28,64,99,64);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->SetLineWidth(.4);
		  $pdf->Rect(101,45,94,22);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetLineWidth(.2);
		  $pdf->SetXY(101,45);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(94,4,'PARA USO EXCLUSIVO DEL  IMSS',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(101,50);
		  $pdf->MultiCell(53,4,'REGISTRO DEL IMSS DEL PLANTEL : ',0,'L',0);
		  $pdf->SetXY(152,50);
		  $pdf->MultiCell(75,4,'        	R13 99003-32-3',0,'L',0);
		  $pdf->line(146,54,193,54);
		  $pdf->SetXY(101,55);
		  $pdf->MultiCell(64,4,'NUMERO DE AFILIACIÓN DEL ESTUDIANTE : ',0,'L',0);
		  $pdf->line(158,59,193,59);
		  $pdf->SetXY(101,60);
		  $pdf->MultiCell(64,4,'NUMERO DE LA UNIDAD MEDICA FAMILIAR : ',0,'L',0);
		  $pdf->line(158,64,193,64);
		  //Termina creacion de cuadros informacion
		  
		  //Titulo Datos Estudiante
		  $pdf->SetXY(5,68);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(190,4,'DATOS DEL ESTUDIANTE',0,'C',0);
		  
		  $pdf->SetXY(8,75);
		  $pdf->MultiCell(7,4,'A)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(12,75);
		  $pdf->MultiCell(17,4,'NOMBRE:',0,'R',0);
		  $pdf->SetXY(29,75);
		  $pdf->MultiCell(50,4,strtoupper($a_paterno),0,'C',1);
		  $pdf->SetXY(29,79);
		  $pdf->MultiCell(50,4,'APELLIDO PATERNO',0,'C',0);
		  $pdf->SetXY(79,75);
		  $pdf->MultiCell(50,4,strtoupper($a_materno),0,'C',1);
		  $pdf->SetXY(79,79);
		  $pdf->MultiCell(50,4,'APELLIDO MATERNO',0,'C',0);
		  $pdf->SetXY(129,75);
		  $pdf->MultiCell(62,4,strtoupper($nombre),0,'C',1);
		  $pdf->SetXY(129,79);
		  $pdf->MultiCell(65,4,'NOMBRES',0,'C',0);
		  $pdf->line(79,79,79,75);
		  $pdf->line(129,79,129,75);
		  $pdf->line(29,79,191,79);
		  
		  $pdf->SetXY(8,85);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'B)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,85);
		  $pdf->MultiCell(17,4,'SEXO:',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(64,85);
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(69,85);
		  $pdf->MultiCell(20,4,'MASCULINO',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(115,85);
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(119,85);
		  $pdf->MultiCell(20,4,'FEMENINO',0,'C',0);
		  $pdf->SetXY(181,85);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,85);
		  $pdf->MultiCell(8,4,$sexo,0,'C',1);
		  $pdf->SetXY(187.5,85);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,89,191,89);
		  
		  $pdf->SetXY(8,91);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'C)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,91);
		  $pdf->MultiCell(40,4,'FECHA DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(103,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(106,91);
		  $pdf->MultiCell(10,4,$dia,0,'C',1);
		  $pdf->SetXY(114,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(106,95);
		  $pdf->MultiCell(10,4,'DIA',0,'C',0);
		  $pdf->line(106,95,116,95);
		  $pdf->SetXY(136,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(139,91);
		  $pdf->MultiCell(10,4,$mes,0,'C',1);
		  $pdf->SetXY(147,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(139,95);
		  $pdf->MultiCell(10,4,'MES',0,'C',0);
		  $pdf->line(139,95,149,95);
		  $pdf->SetXY(165,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(168,91);
		  $pdf->MultiCell(10,4,$anio,0,'C',1);
		  $pdf->SetXY(176,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(168,95);
		  $pdf->MultiCell(10,4,'AÑO',0,'C',0);
		  $pdf->line(168,95,178,95);
		  
		  $pdf->SetXY(8,101);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'D)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,101);
		  $pdf->MultiCell(40,4,'LUGAR DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(55,101);
		  $pdf->MultiCell(50,4, strtoupper($municipioNac),0,'C',1);
		  $pdf->line(55,105,105,105);
		  $pdf->SetXY(55,105);
		  $pdf->MultiCell(50,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(107,101);
		  $pdf->MultiCell(60,4,strtoupper($estadoNac),0,'C',1);
		  $pdf->line(107,105,167,105);
		  $pdf->SetXY(107,105);
		  $pdf->MultiCell(60,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,111);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'E)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,111);
		  $pdf->MultiCell(40,4,'DOMICILIO:',0,'L',0);
		  $pdf->SetXY(35,111);
		  $pdf->MultiCell(95,4, strtr(strtoupper($direccion),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(130,115,130,111);
		  $pdf->SetXY(130.1,111);
		  $pdf->MultiCell(62,4, strtr(strtoupper($colonia),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(35,115,192,115);
		  $pdf->SetXY(35,115);
		  $pdf->MultiCell(95,4,'CALLE Y NUMERO',0,'C',0);
		  $pdf->SetXY(130.1,115);
		  $pdf->MultiCell(62,4,'COLONIA',0,'C',0);
		  $pdf->SetXY(19,119);
		  $pdf->MultiCell(35,4, strtoupper($cp),0,'C',1);
		  $pdf->line(19,123,54,123);
		  $pdf->SetXY(19,123);
		  $pdf->MultiCell(35,4,'CODIGO POSTAL',0,'C',0);
		  $pdf->SetXY(64.5,119);
		  $pdf->MultiCell(55,4, strtoupper($municipio),0,'C',1);
		  $pdf->line(64.5,123,119,123);
		  $pdf->SetXY(64.5,123);
		  $pdf->MultiCell(55,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(130.1,119);
		  $pdf->MultiCell(62,4, strtoupper($estado),0,'C',1);
		  $pdf->line(130,123,192,123);
		  $pdf->SetXY(130.1,123);
		  $pdf->MultiCell(62,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,128);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'F)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,128);
		  $pdf->MultiCell(40,4,'NOMBRE DE LOS PADRES:',0,'L',0);
		  $pdf->SetXY(56,128);
		  $pdf->MultiCell(20,4,'PADRE:',0,'R',0);
		  $pdf->SetXY(76,128);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_padre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,132,177,132);
		  $pdf->SetXY(56,134);
		  $pdf->MultiCell(20,4,'MADRE:',0,'R',0);
		  $pdf->SetXY(76,134);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_madre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,138,177,138);
		  
		  $pdf->SetXY(8,143);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'G)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,143);
		  $pdf->MultiCell(50,4,'¿APARTE DE ESTUDIAR TRABAJA?',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(84,143);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(86,143);
		  $pdf->MultiCell(10,4,'SI',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(119,143);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(124,143);
		  $pdf->MultiCell(10,4,'NO',0,'C',0);
		  $pdf->SetXY(181,143);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,143);
		  $pdf->MultiCell(8,4,'2',0,'C',1);//$trabaja
		  $pdf->SetXY(187.5,143);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,147,191,147);
		  
		  $pdf->SetXY(8,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'H)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,148);
		  $pdf->MultiCell(65,4,'¿DE QUIEN DEPENDE ECONOMICAMENTE?',0,'L',0);
		  $pdf->SetXY(84,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetXY(87,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(15,4,'PADRES',0,'R',0);
		  $pdf->SetXY(119,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetXY(124,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(18,4,'CONYUGE',0,'R',0);
		  $pdf->SetXY(181,148);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,148);
		  $pdf->MultiCell(8,4,'1',0,'C',1);//$economicamente
		  $pdf->SetXY(187.5,148);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,152,191,152);
		  $pdf->SetXY(74,153);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'3)',0,'C',0);
		  $pdf->SetXY(81,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(50,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(117,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(60,4,'',0,'L',1);// strtoupper($otroEco)
		  $pdf->line(117,157,177,157);
		   
		  $pdf->SetXY(8,159);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'I)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,159); 
		  $pdf->MultiCell(110,4,'¿EN QUE TRABAJA LA PERSONA DE LA QUE DEPENDE ECONOMICAMENTE?',0,'L',0); 
		  $pdf->SetXY(181,159);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,159);
		  $pdf->MultiCell(8,4,$irandom,0,'C',1);
		  $pdf->SetXY(187.5,159);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,163,191,163);
		  $pdf->SetXY(18,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'SERVIDOR PUBLICO',0,'C',0);
		  $pdf->SetXY(18,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(23,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'COMERCIANTE O INDUSTRIAL',0,'L',0);
		  $pdf->SetXY(18,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(23,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'OBRERO',0,'L',0);
		  $pdf->SetXY(65,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(70,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(51,4,'EMPLEADO EMPRESA PARTICULAR',0,'L',0);
		  $pdf->SetXY(65,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(70,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'MARINO O MILITAR',0,'L',0);
		  $pdf->SetXY(65,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'8)',0,'C',0);
		  $pdf->SetXY(70,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(105,172);
		  $pdf->MultiCell(87,4,'',0,'L',1);
		  $pdf->line(105,176,192,176);
		  $pdf->SetXY(120,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(125,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(60,4,'PROFESION U OFICIO POR SU CUENTA',0,'L',0);
		  $pdf->SetXY(110,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(115,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(80,4,'AGRICULTOR,GANADERO,CAMPESINO O PESCADOR',0,'L',0);
		  
		  $pdf->SetXY(8,181);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'J)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,181); 
		  $pdf->MultiCell(155,4,'¿SE ENCUENTRA PROTEGIDO, YA SEA COMO TRABAJADOR O COMO BENEFICIARIO DE SUS PADRES O DE SU',0,'L',0); 
		  $pdf->SetXY(181,181);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,181);
		  $pdf->MultiCell(8,4,'2',0,'C',1);
		  $pdf->SetXY(187.5,181);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,185,191,185);
		  $pdf->SetXY(23,185); 
		  $pdf->MultiCell(100,4,'CONYUGE EN ALGUNA INSTITUCIÓN DE SEGURIDAD?',0,'L',0); 
		  $pdf->SetXY(105,185); 
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(100,4,'1)                                2)',0,'L',0); 
		  $pdf->SetXY(109,185); 
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(100,4,'SI                                NO',0,'L',0); 
		  
		  $pdf->SetXY(8,192);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'K)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,192); 
		  $pdf->MultiCell(130,4,'¿QUÉ INSTITUCIÓN LE DA SERVICIOS MEDICOS?',0,'L',0); 
		  $pdf->SetXY(181,192);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,192);
		  $pdf->MultiCell(8,4,'7',0,'C',1);
		  $pdf->SetXY(187.5,192);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,196,191,196);
		  $pdf->SetXY(18,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'IMSS',0,'L',0);
		  $pdf->SetXY(18,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(23,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'PEMEX',0,'L',0);
		  $pdf->SetXY(40,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(45,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'ISSSTE',0,'L',0);
		  $pdf->SetXY(62,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(67,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'SECRETARIA DE MARINA',0,'L',0);
		  $pdf->SetXY(111,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(116,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(65,4,'SECRETARIA DE LA DEFENSA NACIONAL',0,'L',0);
		  $pdf->SetXY(40,201);
		  $pdf->SetFont('Arial','B',8);  
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(45,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(70,4,'INSTITUCION NACIONAL DE CREDITO (BANCO)',0,'L',0);
		  $pdf->SetXY(111,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(116,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTRA    ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(148,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(44,4,'PARTICULAR',0,'L',1);
		  $pdf->line(148,205,192,205);
		  
		  $pdf->SetXY(19,208);
		  $pdf->MultiCell(160,4,'BAJO PROTESTA DE DECIR LA VERDAD DECLARO QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS',0,'L',0);
		  
		  $pdf->SetXY(5,235);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(60,233,140,233);
		  $pdf->MultiCell(190,4,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
          //Termina creacion DATOS ESTUDIANTE
		  
		  //Cuadro de datos del estudiante
		  $pdf->Rect(5,72,190,167);
		  //Termina cuadro de datos del estudiante
		  
		  //Cuadros firmas
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(5,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(5,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(5,247,99,247);
		  $pdf->MultiCell(94,4,'PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetXY(5,248);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(94,4,'SE CERTIFICA QUE EL SOLICITANTE ES ESTUDIANTE DEL PLANTEL',0,'C',0);
		  $pdf->SetXY(5,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(5,272);
		  $pdf->MultiCell(94,4,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(5,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->line(21,267,84,267);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(101,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(101,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(101,247,195,247);
		  $pdf->MultiCell(94,4,'IMSS DELEGACIONAL',0,'C',0);
		  $pdf->SetXY(101,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(101,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(116,267,180,267);
		  //Termina cuadros de firmas
		  
		  //Inicia segunda hoja de PDF
		  $pdf->AddPage();
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  $pdf->SetXY(167,9);
		  $pdf->MultiCell(20,4,'',1,'C',0);
		  $pdf->SetXY(167,13);
		  $pdf->SetFont('Arial','',10);
		  $pdf->MultiCell(20,4,'FOLIO',0,'C',0);
		  $pdf->SetXY(38,18);
          $pdf->SetFont('Arial','B',12);
		  $pdf->MultiCell(131,4,'COMPROBANTE DE SOLICITUD',0,'C',0);
		  $pdf->SetXY(38,23);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'INCORPORACIÓN DE ESTUDIANTES AL SEGURO FACULTATIVO',0,'C',0);
		  $pdf->SetXY(38,27);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  
		  $pdf->Rect(20,50,165,35);
		  $pdf->SetXY(20,51);
		  $pdf->SetFont('Arial','B',11);
		  $pdf->MultiCell(165,4,'IMPORTANTE',0,'C',0);
		  $pdf->line(20,55,185,55);
		  $pdf->SetXY(30,60);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(30,70);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(33,59);
		  $pdf->SetFont('Arial','',11);
		  $pdf->MultiCell(150,4,'EN CASO DE REQUERIR SERVICIO MEDICO, PRESENTE ESTE DOCUMENTO EN LA UNIDAD DE MEDICINA FAMILIAR QUE ASIGNE EL INSTITUTO.',0,'J',0);
		  $pdf->SetXY(33,69);
		  $pdf->MultiCell(150,4,'ESTE COMPROBANTE DEBERA SER CANJEADO POR EL AVISO AUTOMÁTICO DE INSCRIPCION EN EL SEGURO FACULTATIVO EN LOS SERVICIOS ESCOLARES DEL PLANTEL',0,'J',0);
		  
		  $pdf->Rect(20,87,82,40);
		  $pdf->SetXY(20,123);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
		  $pdf->line(25,122,97,122);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(103,87,82,40);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(103,88);
		  $pdf->MultiCell(82,3,'SERVICIOS ESCOLARES',0,'C',0);
		  $pdf->line(103,92,185,92);
		  $pdf->SetXY(103,119);
		  $pdf->SetFont('Arial','',6);
		  $pdf->MultiCell(82,3,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(103,104);
		  $pdf->MultiCell(20,3,'SELLO',0,'L',0);
		  $pdf->SetXY(103,116);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(82,3,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->SetXY(103,123);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(103,122,185,122);
		  
		  $pdf->Image('public/img/imss_logo.jpg',5,138,40,40);
		  $pdf->SetXY(167,140);
		  $pdf->MultiCell(20,4,'',1,'C',0);
		  $pdf->SetXY(167,144);
		  $pdf->SetFont('Arial','',10);
		  $pdf->MultiCell(20,4,'FOLIO',0,'C',0);
		  $pdf->SetXY(38,151);
          $pdf->SetFont('Arial','B',12);
		  $pdf->MultiCell(131,4,'COMPROBANTE DE SOLICITUD',0,'C',0);
		  $pdf->SetXY(38,156);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'INCORPORACIÓN DE ESTUDIANTES AL SEGURO FACULTATIVO',0,'C',0);
		  $pdf->SetXY(38,160);
          $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(130,4,'DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  
		  $pdf->Rect(20,183,165,35);
		  $pdf->SetXY(20,184);
		  $pdf->SetFont('Arial','B',11);
		  $pdf->MultiCell(165,4,'IMPORTANTE',0,'C',0);
		  $pdf->line(20,188,185,188);
		  $pdf->SetXY(30,193);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(30,203);
		  $pdf->MultiCell(5,4,'*',0,'C',0);
		  $pdf->SetXY(33,192);
		  $pdf->SetFont('Arial','',11);
		  $pdf->MultiCell(150,4,'EN CASO DE REQUERIR SERVICIO MEDICO, PRESENTE ESTE DOCUMENTO EN LA UNIDAD DE MEDICINA FAMILIAR QUE ASIGNE EL INSTITUTO.',0,'J',0);
		  $pdf->SetXY(33,202);
		  $pdf->MultiCell(150,4,'ESTE COMPROBANTE DEBERA SER CANJEADO POR EL AVISO AUTOMÁTICO DE INSCRIPCION EN EL SEGURO FACULTATIVO EN LOS SERVICIOS ESCOLARES DEL PLANTEL',0,'J',0);
		  
		  $pdf->Rect(20,220,82,40);
		  $pdf->SetXY(20,256);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
		  $pdf->line(25,255,97,255);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(103,220,82,40);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(103,221);
		  $pdf->MultiCell(82,3,'SERVICIOS ESCOLARES',0,'C',0);
		  $pdf->line(103,225,185,225);
		  $pdf->SetXY(103,252);
		  $pdf->SetFont('Arial','',6);
		  $pdf->MultiCell(82,3,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(103,237);
		  $pdf->MultiCell(20,3,'SELLO',0,'L',0);
		  $pdf->SetXY(103,249);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(82,3,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->SetXY(103,256);
		  $pdf->SetFont('Arial','B',10);
		  $pdf->MultiCell(82,3,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(103,255,185,255);
		  
		  //SE GENERA LA COPIA DEL SEGURO FACULTATIVO PARA EL ALUMNO
		  $pdf -> AddPage();
		  
		  //Primera pagina del PDF
	      //Encabezado
		  $pdf->Image('public/img/imss_logo.jpg',5,5,40,40);
		  
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SOLICITUD PARA LA INCORPORACIÓN DE ESTUDIANTES AL',0,'C',0);
		  $pdf->SetXY(160,10);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(35,4,'',0,'C',1);//date('d-m-Y')
		  $pdf->line(160,14,195,14);
		  $pdf->line(160,14,160,10);
		  $pdf->line(195,14,195,10);
		  $pdf->SetX(38);
          $pdf->SetFont('Arial','B',9);
		  $pdf->MultiCell(122,4,'SEGURO FACULTATIVO DEL REGIMEN DEL SEGURO SOCIAL',0,'C',0);
		  $pdf->SetXY(160,14);
		  $pdf->MultiCell(35,4,'FECHA',0,'C',0);
		  
		  $pdf->SetXY(40,24);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'REGISTRO DEL ALUMNO : ',0,'R',0);
		  $pdf->SetXY(90,24);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(25,4,$registro,0,'C',1);
		  $pdf->line(90,28,115,28);
		  
		  $pdf->SetXY(40,31);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(44,4,'CARRERA / GRADO /GRUPO : ',0,'R',0);
		  $pdf->SetXY(83,31);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(112,4,$carrera." / ".$semestre." / ".$grupo,0,'L',1);
		  $pdf->line(83,35,195,35);
		 
		  $pdf->SetXY(40,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(13,4,'CURP : ',0,'L',0);
		  $pdf->SetXY(52,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(60,4,strtoupper($curp),0,'L',1);
		  $pdf->line(52,42,112,42);
		  
		  $pdf->SetXY(125,38);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(35,4,'TEL / CEL : ',0,'L',0);
		  $pdf->SetXY(142,38);
		  $pdf->SetFillColor(240);
		  $pdf->MultiCell(53,4,$telefono.' / '.$celular,0,'L',1);
		  $pdf->line(142,42,195,42);
		  //Termina encabezado
		  
		  //Cuadros de informacion plantel e imss
		  $pdf->Rect(5,45,94,22);
		  $pdf->SetXY(5,45);
		  $pdf->MultiCell(94,4,'DATOS DEL PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(5,50);
		  $pdf->MultiCell(18,4,'NOMBRE : ',0,'R',0);
		  $pdf->SetXY(23,50);
		  $pdf->MultiCell(75,4,'        CENTRO DE ENSEÑANZA TECNICA INDUSTRIAL',0,'R',0);
		  $pdf->line(18,54,99,54);
		  $pdf->SetXY(5,55);
		  $pdf->MultiCell(18,4,'CLAVE : ',0,'R',0);
		  $pdf->SetXY(23,55);
		  $pdf->MultiCell(75,4,'           14NET0020C',0,'L',0);
		  $pdf->line(18,59,99,59);
		  $pdf->SetXY(5,60);
		  $pdf->MultiCell(30,4,'NIVEL EDUCATIVO : ',0,'R',0);
		  $pdf->SetXY(43,60);
		  $pdf->MultiCell(55.8,4,'INGENIERIA',0,'L',1);
		  $pdf->line(28,64,99,64);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->SetLineWidth(.4);
		  $pdf->Rect(101,45,94,22);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetLineWidth(.2);
		  $pdf->SetXY(101,45);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(94,4,'PARA USO EXCLUSIVO DEL  IMSS',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(101,50);
		  $pdf->MultiCell(53,4,'REGISTRO DEL IMSS DEL PLANTEL : ',0,'L',0);
		  $pdf->SetXY(152,50);
		  $pdf->MultiCell(75,4,'        	R13 99003-32-3',0,'L',0);
		  $pdf->line(146,54,193,54);
		  $pdf->SetXY(101,55);
		  $pdf->MultiCell(64,4,'NUMERO DE AFILIACIÓN DEL ESTUDIANTE : ',0,'L',0);
		  $pdf->line(158,59,193,59);
		  $pdf->SetXY(101,60);
		  $pdf->MultiCell(64,4,'NUMERO DE LA UNIDAD MEDICA FAMILIAR : ',0,'L',0);
		  $pdf->line(158,64,193,64);
		  //Termina creacion de cuadros informacion
		  
		  //Titulo Datos Estudiante
		  $pdf->SetXY(5,68);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(190,4,'DATOS DEL ESTUDIANTE',0,'C',0);
		  
		  $pdf->SetXY(8,75);
		  $pdf->MultiCell(7,4,'A)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(12,75);
		  $pdf->MultiCell(17,4,'NOMBRE:',0,'R',0);
		  $pdf->SetXY(29,75);
		  $pdf->MultiCell(50,4,strtoupper($a_paterno),0,'C',1);
		  $pdf->SetXY(29,79);
		  $pdf->MultiCell(50,4,'APELLIDO PATERNO',0,'C',0);
		  $pdf->SetXY(79,75);
		  $pdf->MultiCell(50,4,strtoupper($a_materno),0,'C',1);
		  $pdf->SetXY(79,79);
		  $pdf->MultiCell(50,4,'APELLIDO MATERNO',0,'C',0);
		  $pdf->SetXY(129,75);
		  $pdf->MultiCell(62,4,strtoupper($nombre),0,'C',1);
		  $pdf->SetXY(129,79);
		  $pdf->MultiCell(65,4,'NOMBRES',0,'C',0);
		  $pdf->line(79,79,79,75);
		  $pdf->line(129,79,129,75);
		  $pdf->line(29,79,191,79);
		  
		  $pdf->SetXY(8,85);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'B)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,85);
		  $pdf->MultiCell(17,4,'SEXO:',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(64,85);
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(69,85);
		  $pdf->MultiCell(20,4,'MASCULINO',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(115,85);
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(119,85);
		  $pdf->MultiCell(20,4,'FEMENINO',0,'C',0);
		  $pdf->SetXY(181,85);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,85);
		  $pdf->MultiCell(8,4,$sexo,0,'C',1);
		  $pdf->SetXY(187.5,85);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,89,191,89);
		  
		  $pdf->SetXY(8,91);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'C)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,91);
		  $pdf->MultiCell(40,4,'FECHA DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(103,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(106,91);
		  $pdf->MultiCell(10,4,$dia,0,'C',1);
		  $pdf->SetXY(114,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(106,95);
		  $pdf->MultiCell(10,4,'DIA',0,'C',0);
		  $pdf->line(106,95,116,95);
		  $pdf->SetXY(136,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(139,91);
		  $pdf->MultiCell(10,4,$mes,0,'C',1);
		  $pdf->SetXY(147,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(139,95);
		  $pdf->MultiCell(10,4,'MES',0,'C',0);
		  $pdf->line(139,95,149,95);
		  $pdf->SetXY(165,91);
		  $pdf->MultiCell(5,4,'(',0,'C',0);
		  $pdf->SetXY(168,91);
		  $pdf->MultiCell(10,4,$anio,0,'C',1);
		  $pdf->SetXY(176,91);
		  $pdf->MultiCell(5,4,')',0,'C',0);
		  $pdf->SetXY(168,95);
		  $pdf->MultiCell(10,4,'AÑO',0,'C',0);
		  $pdf->line(168,95,178,95);
		  
		  $pdf->SetXY(8,101);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'D)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,101);
		  $pdf->MultiCell(40,4,'LUGAR DE NACIMIENTO:',0,'L',0);
		  $pdf->SetXY(55,101);
		  $pdf->MultiCell(50,4, strtoupper($municipioNac),0,'C',1);
		  $pdf->line(55,105,105,105);
		  $pdf->SetXY(55,105);
		  $pdf->MultiCell(50,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(107,101);
		  $pdf->MultiCell(60,4,strtoupper($estadoNac),0,'C',1);
		  $pdf->line(107,105,167,105);
		  $pdf->SetXY(107,105);
		  $pdf->MultiCell(60,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,111);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'E)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,111);
		  $pdf->MultiCell(40,4,'DOMICILIO:',0,'L',0);
		  $pdf->SetXY(35,111);
		  $pdf->MultiCell(95,4, strtr(strtoupper($direccion),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(130,115,130,111);
		  $pdf->SetXY(130.1,111);
		  $pdf->MultiCell(62,4,strtr(strtoupper($colonia),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(35,115,192,115);
		  $pdf->SetXY(35,115);
		  $pdf->MultiCell(95,4,'CALLE Y NUMERO',0,'C',0);
		  $pdf->SetXY(130.1,115);
		  $pdf->MultiCell(62,4,'COLONIA',0,'C',0);
		  $pdf->SetXY(19,119);
		  $pdf->MultiCell(35,4, strtoupper($cp),0,'C',1);
		  $pdf->line(19,123,54,123);
		  $pdf->SetXY(19,123);
		  $pdf->MultiCell(35,4,'CODIGO POSTAL',0,'C',0);
		  $pdf->SetXY(64.5,119);
		  $pdf->MultiCell(55,4, strtoupper($municipio),0,'C',1);
		  $pdf->line(64.5,123,119,123);
		  $pdf->SetXY(64.5,123);
		  $pdf->MultiCell(55,4,'MUNICIPIO',0,'C',0);
		  $pdf->SetXY(130.1,119);
		  $pdf->MultiCell(62,4, strtoupper($estado),0,'C',1);
		  $pdf->line(130,123,192,123);
		  $pdf->SetXY(130.1,123);
		  $pdf->MultiCell(62,4,'ENTIDAD FEDERATIVA',0,'C',0);
		  
		  $pdf->SetXY(8,128);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'F)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,128);
		  $pdf->MultiCell(40,4,'NOMBRE DE LOS PADRES:',0,'L',0);
		  $pdf->SetXY(56,128);
		  $pdf->MultiCell(20,4,'PADRE:',0,'R',0);
		  $pdf->SetXY(76,128);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_padre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,132,177,132);
		  $pdf->SetXY(56,134);
		  $pdf->MultiCell(20,4,'MADRE:',0,'R',0);
		  $pdf->SetXY(76,134);
		  $pdf->MultiCell(101,4, strtr(strtoupper($nombre_madre),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"),0,'C',1);
		  $pdf->line(76,138,177,138);
		  
		  $pdf->SetXY(8,143);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'G)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,143);
		  $pdf->MultiCell(50,4,'¿APARTE DE ESTUDIAR TRABAJA?',0,'L',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(84,143);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(86,143);
		  $pdf->MultiCell(10,4,'SI',0,'C',0);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->SetXY(119,143);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(124,143);
		  $pdf->MultiCell(10,4,'NO',0,'C',0);
		  $pdf->SetXY(181,143);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,143);
		  $pdf->MultiCell(8,4,'2',0,'C',1);//$trabaja
		  $pdf->SetXY(187.5,143);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,147,191,147);
		  
		  $pdf->SetXY(8,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'H)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,148);
		  $pdf->MultiCell(65,4,'¿DE QUIEN DEPENDE ECONOMICAMENTE?',0,'L',0);
		  $pdf->SetXY(84,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(5,4,'1)',0,'R',0);
		  $pdf->SetXY(87,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(15,4,'PADRES',0,'R',0);
		  $pdf->SetXY(119,148);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'2)',0,'C',0);
		  $pdf->SetXY(124,148);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(18,4,'CONYUGE',0,'R',0);
		  $pdf->SetXY(181,148);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,148);
		  $pdf->MultiCell(8,4,'1',0,'C',1);//$economicamente
		  $pdf->SetXY(187.5,148);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,152,191,152);
		  $pdf->SetXY(74,153);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(10,4,'3)',0,'C',0);
		  $pdf->SetXY(81,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(50,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(117,153);
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(60,4,'',0,'L',1);// strtoupper($otroEco)
		  $pdf->line(117,157,177,157);
		   
		  $pdf->SetXY(8,159);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'I)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,159); 
		  $pdf->MultiCell(110,4,'¿EN QUE TRABAJA LA PERSONA DE LA QUE DEPENDE ECONOMICAMENTE?',0,'L',0); 
		  $pdf->SetXY(181,159);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,159);
		  $pdf->MultiCell(8,4,$irandom,0,'C',1);
		  $pdf->SetXY(187.5,159);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,163,191,163);
		  $pdf->SetXY(18,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'SERVIDOR PUBLICO',0,'C',0);
		  $pdf->SetXY(18,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(23,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'COMERCIANTE O INDUSTRIAL',0,'L',0);
		  $pdf->SetXY(18,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(23,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'OBRERO',0,'L',0);
		  $pdf->SetXY(65,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(70,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(51,4,'EMPLEADO EMPRESA PARTICULAR',0,'L',0);
		  $pdf->SetXY(65,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(70,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'MARINO O MILITAR',0,'L',0);
		  $pdf->SetXY(65,172);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'8)',0,'C',0);
		  $pdf->SetXY(70,172);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTROS     ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(105,172);
		  $pdf->MultiCell(87,4,'',0,'L',1);
		  $pdf->line(105,176,192,176);
		  $pdf->SetXY(120,164);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(125,164);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(60,4,'PROFESION U OFICIO POR SU CUENTA',0,'L',0);
		  $pdf->SetXY(110,168);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(115,168);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(80,4,'AGRICULTOR,GANADERO,CAMPESINO O PESCADOR',0,'L',0);
		  
		  $pdf->SetXY(8,181);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'J)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,181); 
		  $pdf->MultiCell(155,4,'¿SE ENCUENTRA PROTEGIDO, YA SEA COMO TRABAJADOR O COMO BENEFICIARIO DE SUS PADRES O DE SU',0,'L',0); 
		  $pdf->SetXY(181,181);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,181);
		  $pdf->MultiCell(8,4,'2',0,'C',1);
		  $pdf->SetXY(187.5,181);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,185,191,185);
		  $pdf->SetXY(23,185); 
		  $pdf->MultiCell(100,4,'CONYUGE EN ALGUNA INSTITUCIÓN DE SEGURIDAD?',0,'L',0); 
		  $pdf->SetXY(105,185); 
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(100,4,'1)                                2)',0,'L',0); 
		  $pdf->SetXY(109,185); 
		  $pdf->SetFont('Arial','',8);
		  $pdf->MultiCell(100,4,'SI                                NO',0,'L',0); 
		  
		  $pdf->SetXY(8,192);
		  $pdf->SetFont('Arial','B',8);
		  $pdf->MultiCell(7,4,'K)',0,'C',0);
		  $pdf->SetFont('Arial','',8);
		  $pdf->SetXY(14,192); 
		  $pdf->MultiCell(130,4,'¿QUÉ INSTITUCIÓN LE DA SERVICIOS MEDICOS?',0,'L',0); 
		  $pdf->SetXY(181,192);
		  $pdf->MultiCell(5,4,'(',0,'L',0);
		  $pdf->SetXY(183,192);
		  $pdf->MultiCell(8,4,'7',0,'C',1);
		  $pdf->SetXY(187.5,192);
		  $pdf->MultiCell(8,4,')',0,'C',0);
		  $pdf->line(183,196,191,196);
		  $pdf->SetXY(18,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'1)',0,'C',0);
		  $pdf->SetXY(23,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'IMSS',0,'L',0);
		  $pdf->SetXY(18,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'5)',0,'C',0);
		  $pdf->SetXY(23,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'PEMEX',0,'L',0);
		  $pdf->SetXY(40,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'2)',0,'C',0);
		  $pdf->SetXY(45,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(30,4,'ISSSTE',0,'L',0);
		  $pdf->SetXY(62,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'3)',0,'C',0);
		  $pdf->SetXY(67,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(45,4,'SECRETARIA DE MARINA',0,'L',0);
		  $pdf->SetXY(111,197);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'4)',0,'C',0);
		  $pdf->SetXY(116,197);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(65,4,'SECRETARIA DE LA DEFENSA NACIONAL',0,'L',0);
		  $pdf->SetXY(40,201);
		  $pdf->SetFont('Arial','B',8);  
		  $pdf->MultiCell(7,4,'6)',0,'C',0);
		  $pdf->SetXY(45,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(70,4,'INSTITUCION NACIONAL DE CREDITO (BANCO)',0,'L',0);
		  $pdf->SetXY(111,201);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->MultiCell(7,4,'7)',0,'C',0);
		  $pdf->SetXY(116,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(40,4,'OTRA    ESPECIFIQUE',0,'L',0);
		  $pdf->SetXY(148,201);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(44,4,'PARTICULAR',0,'L',1);
		  $pdf->line(148,205,192,205);
		  
		  $pdf->SetXY(19,208);
		  $pdf->MultiCell(160,4,'BAJO PROTESTA DE DECIR LA VERDAD DECLARO QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS',0,'L',0);
		  
		  $pdf->SetXY(5,235);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(60,233,140,233);
		  $pdf->MultiCell(190,4,'NOMBRE Y FIRMA DEL ESTUDIANTE',0,'C',0);
          //Termina creacion DATOS ESTUDIANTE
		  
		  //Cuadro de datos del estudiante
		  $pdf->Rect(5,72,190,167);
		  //Termina cuadro de datos del estudiante
		  
		  //Cuadros firmas
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(5,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(5,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(5,247,99,247);
		  $pdf->MultiCell(94,4,'PLANTEL EDUCATIVO',0,'C',0);
		  $pdf->SetXY(5,248);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(94,4,'SE CERTIFICA QUE EL SOLICITANTE ES ESTUDIANTE DEL PLANTEL',0,'C',0);
		  $pdf->SetXY(5,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(5,272);
		  $pdf->MultiCell(94,4,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',0,'C',0);
		  $pdf->SetXY(5,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'ING. SALVADOR TRINIDAD PÉREZ',0,'C',0);
		  $pdf->line(21,267,84,267);
		  
		  $pdf->SetDrawColor(201,42,18);
		  $pdf->Rect(101,242,94,34);
		  $pdf->SetDrawColor(0,0,0);
		  $pdf->SetXY(101,243);
		  $pdf->SetFont('Arial','B',8); 
		  $pdf->line(101,247,195,247);
		  $pdf->MultiCell(94,4,'IMSS DELEGACIONAL',0,'C',0);
		  $pdf->SetXY(101,259);
		  $pdf->SetFont('Arial','',6); 
		  $pdf->MultiCell(10,4,'SELLO',0,'C',0);
		  $pdf->SetXY(101,268);
		  $pdf->SetFont('Arial','',8); 
		  $pdf->MultiCell(94,4,'NOMBRE Y FIRMA DEL RESPONSABLE',0,'C',0);
		  $pdf->line(116,267,180,267);
		  //Termina cuadros de firmas
		  
		  $pdf->Output("public/files/pdfs/seguroFacultativo/SeguroFacultativo".$registro.".pdf","F");
		  die();
        } 	
		
		
		/*
		* Esta funcion se encarga de validar que no puedan acceser atravez de la URL
		*/
		public function url_validacion(){		
			if(Session::get('statusAlumno') == "SinAcceso")
			{
				$url= !isset($_SERVER['HTTP_REFERER']);
				if($url == true)
				$this->redirect("general/accesourl");
			}
		}
	}
?>