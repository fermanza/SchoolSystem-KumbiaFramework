<?php
	class CambiosalumnoController extends ApplicationController {
		function index(){
			$this -> valida();
		}
		/*
		* Funcion que busca y trae los datos del alumno
		*/
		function buscarAlumno(){
			$Alumnos = new Alumnos();
			$XalumnosPersonal = new XAlumnosPersonal();
			
			$this -> set_response("view");
			if($this -> post('registro')){
				$query =  "miReg = '".$this -> post('registro')."'";
				
				$this->alumno = $Alumnos -> find_first($query);
				
				$this->get_info_for_partial_info_alumno($this->alumno);
				
				$informacion = $XalumnosPersonal -> find_all_by_sql("SELECT al.paterno, al.materno, al.nombre, al.enTurno, c.nombre AS carrera, al.enTipo, af.nombreareaformacion,
																	 al.estadoAnterior
																	 FROM alumnos AS al JOIN carrera AS c ON al.carrera_id = c.id
																	 JOIN areadeformacion AS af ON al.areadeformacion_id = af.id
																	 WHERE al.miReg = ".$this -> post('registro'));
				$this -> registro = $this -> post('registro');
				
				foreach( $informacion AS $info ){
					$this -> nombre = utf8_encode($info -> nombre);
					$this -> paterno = utf8_encode($info -> paterno);
					$this -> materno = utf8_encode($info -> materno);
					if($info -> enTurno == "V"){
						$this -> enTurno = "VESPERTINO";
					}else{
						$this -> enTurno = "MATUTINO";
					}
					$this -> estadoAnterior = $info -> estadoAnterior;
					$this -> enTipo = $info -> enTipo;
					$this -> nombreareaformacion = utf8_encode($info -> nombreareaformacion);
					$this -> carrera = utf8_encode($info -> carrera);
					
				}
			}
			if( $this->alumno == false ){
				Flash::error("El registro ".$this -> post('registro')." no existe");
			}else{
				$this -> render_partial('cambioclave');
			}
			
		}
		/*
		* Funcion que trae los datos para la tabla de informacion.
		*/
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
			
		}
		/*
		* Funcion que trae la contraseña
		*/
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
	
            $id = Session::get_data('registro');

            // Eliminar las variables que van a pertenecer a este método.
            unset ( $this->usuario );
            unset ( $this->maestro );
			unset( $this->vacia );

            $this->usuario = $usuarios->find_first( "registro = '".$id."'" );
            $this->alumno = $alumnos->find_first( "miReg = ".$id );
			$this->vacia = $vacia;
        }
		
		/*
		* Funcion para cambiar la contraseña del alumno.
		*/
		function cambiandoContrasena(){
            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();

			if(Session::get_data('registro')!="8108"){
                    $this->redirect('general/inicio');
            }
			
            $id = Session::get_data('registro');

            $contrasena = $this->post( "contrasena" );

            if( !isset($contrasena) || $contrasena == "" )
                $this->redirect('cambiosalumno/index');

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
        }
		
		/*
		* Funcion que oculta y muestra la clave del alumno seleccionado
		*/
		function ocultarVerPWD( $flag ){
			
            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();
			
			$this->set_response('view');
			
            if(Session::get_data('registro')!="8108"){
                    $this->redirect('general/inicio');
            }

            $id = $this -> registro;
			
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
        }
		

		function correccioncalificaciones(){
			$this -> valida();
		}
		
		/*
		*Funcion que trae las calificaciones del alumno seleccionado.
		*/
		function calificaciones(){
			$visitas	= new VisitasContador();
			// alumno/calificaciones le corresponde el id 4
			$visitas->incrementar(4);
			
			$usuarios = new Usuarios();
			$usuario = $usuarios->find_first("registro='".$this -> registro."'");
			
			$xalumnos = new Xalumnos();
			$xalumno = $xalumnos->find_first("registro=".$this -> registro);
			
			
		}
		
		/*
		* Funcion que busca al alumno.
		*/
		function buscarCalificaciones(){
			$this -> set_response("view");
			$xalumnos = new Xalumnos();
			$Xalumnocursos = new Xalumnocursos();
			$Xtalumnocursos = new XtAlumnocursos();
			$Xextraordinarios = new Xextraordinarios();
			$Xtextraordinarios = new Xtextraordinarios();
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			
			$periodo = array();
			$a = 0;
			if( $this -> post('registro') ){
				
				$this -> registro = $this -> post('registro');
				
				$periodos = $Xalumnocursos -> find_all_by_sql('SELECT DISTINCT(periodo) AS periodo FROM xalumnocursos WHERE registro = '.$this -> registro);
				
				foreach($periodos as $periodoC){
					$periodo[$a] = $periodoC -> periodo;
					
					$a++;
				}
				
				//for($i=0;$i < count($periodo);$i++){
				if(count($periodo) > 0){
					
					$Alumnos = new Alumnos();
					$this->alumno = $alumno = $Alumnos->get_relevant_info_from_student($this -> registro);
					$this->get_info_for_partial_info_alumno($alumno);
					
					$cursos = $Xalumnocursos->get_materias_semestre_by_periodo_colomos_tonala($this -> registro, $periodo[0]);
					$this->cursos = $Xalumnocursos->count("registro=".$this -> registro." AND periodo = '".$periodo[0]."'");
					$this->cursos += $Xtalumnocursos->count("registro=".$this -> registro." AND periodo = '".$periodo[0]."'");
					$this->cursillos = $cursos;
					$this->extras = $Xextraordinarios->count("registro=".$this -> registro." AND periodo = '".$periodo[0]."'");
					$this->extras += $Xtextraordinarios->count("registro=".$this -> registro." AND periodo = '".$periodo[0]."'");
					
					if($this -> cursos > 0){
						foreach($cursos as $curso){
							
							if( !$xccurso = $Xccursos->find_first("id='".$curso->curso_id."' AND periodo = '".$periodo[0]."'") ){
								$xccurso = $Xtcursos->find_first("id='".$curso->curso_id."' AND periodo = '".$periodo[0]."'");
							}
							
							$this->horas[$i] = $xccurso->horas1 + $xccurso->horas2 + $xccurso->horas3;
							$this->mihorario[$i] = $xccurso;
							$this->profesor[$i] = $maestros->find_first("nomina= ".$xccurso->nomina);
							$this->materia[$i] = $materias->find_first("clave= '".$xccurso->materia."' AND carrera_id = ".$alumno->carrera_id);
							
						}
						//var_dump(materia[$i]);die()
					}
				}
			
				$this -> registro = $this -> post('registro');
				$this -> render_partial('correccioncalificaciones');
				
			}
			
			
		}
		
		/*
		* Funcion que valida los permisos de los usuarios.
		*/
		function valida(){
			if( Session::get_data('registro') == 8108 ){
				return true;
			}
			$this -> redirect("/administrador/index");
			return true;
		}
	}
?>