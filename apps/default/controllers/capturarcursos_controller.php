<?php
	class CapturarcursosController extends ApplicationController {
		
		function index(){
			
		}
		
		function cupo_en_cursos_plan_rigido(){
			$this -> valida_coordinador_ing_calculo();
			
			$CursosPlanRigido = new CursosPlanRigido();
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			unset($this->cursos);
			$this->salones= $CursosPlanRigido->get_all_salones_by_periodo($periodo);
			$this->cursos = $CursosPlanRigido->get_all_cursos_by_periodo_and_salon($periodo, $this->salones);
			
		} // function cupo_en_cursos_plan_rigido()
		
		function cupo_en_xccursos_plan_rigido_ver_alumnos_by_curso_id(){
			$this -> set_response("view");
			
			$this -> valida_coordinador_ing_calculo();
			
			$Xalumnocursos = new Xalumnocursos();
			$Xccursos = new Xccursos();
			
			$curso_id = $this -> post ("curso_id");
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			$info_curso= $Xccursos->get_relevant_info_from_xccursos_by_curso_id($curso_id);
			
			$alumnos= $Xalumnocursos->get_alumnos_relevant_info_by_curso_id($curso_id);
			
			echo $curso_id."@";
			echo $info_curso->clavecurso." - ".$info_curso->materia." - ".$info_curso->materia_nombre." - ".
					$info_curso->nombre_profesor." - ".$info_curso->nomina;
			echo "&&&";
			foreach($alumnos as $alumno){
					echo $alumno->registro."/".$alumno->vcNomAlu."/".$alumno->enPlan."/".$alumno->enTurno."/".$alumno->enPlantel."/".
							$alumno->tiNivel."/".$alumno->chGpo."/".$alumno->enTipo."/".$alumno->carrera_nombre."&&&";
			}
		} // function cupo_en_xccursos_plan_rigido_ver_alumnos_by_curso_id()
		
		function view_alumnos_by_salon_id(){
			$this -> set_response("view");
			
			$this -> valida_coordinador_ing_calculo();
			
			$CursosPlanRigido = new CursosPlanRigido();
			$Xalumnocursos = new Xalumnocursos();
			$Xcsalones = new Xcsalones();
			$Xccursos = new Xccursos();
			
			$salon_id = $this -> post ("salon_id");
			
			$cursos_id = $CursosPlanRigido->get_ids_cursos_by_salon_id($salon_id);
			$salon = $Xcsalones->find_first("id= '".$salon_id."'");
			
			echo $salon_id."@";
			echo $salon->edificio.":".$salon->nombre."@";
			
			foreach($cursos_id as $curso_id){
				$info_curso= $Xccursos->get_relevant_info_from_xccursos_by_curso_id($curso_id);
				echo $info_curso->clavecurso." - ".$info_curso->materia." - ".$info_curso->materia_nombre." - ".
					$info_curso->nombre_profesor." - ".$info_curso->nomina;
			}
			echo "&&&";
			
			$alumnos = $Xalumnocursos->get_alumnos_relevant_info_by_cursos_id($cursos_id);
			
			foreach($alumnos as $alumno){
				echo $alumno->cuenta."/".$alumno->registro."/".$alumno->vcNomAlu."/".$alumno->enPlan."/".$alumno->enTurno."/".$alumno->enPlantel."/".
						$alumno->tiNivel."/".$alumno->chGpo."/".$alumno->enTipo."/".$alumno->carrera_nombre."&&&";
			}
			
		} // function view_alumnos_by_salon_id()
		
		function view_alumnos_by_registro(){
			$this -> set_response("view");
			
			$this -> valida_coordinador_ing_calculo();
			
			$Xalumnocursos = new Xalumnocursos();
			$Alumnos = new Alumnos();
			
			$registro = $this -> post ("registro");
			
			$alumno = $Alumnos->get_relevant_info_from_student($registro);
			$cursos = $Xalumnocursos->get_relevant_info_from_xccursos_by_registro_sem_actual($registro);
			
			echo $alumno->miReg." - ".$alumno->vcNomAlu;
			echo "@";
			
			foreach($cursos as $info_curso){
				echo $info_curso->clavecurso." - ".$info_curso->materia." - ".$info_curso->materia_nombre." - ".
					$info_curso->nombre_profesor." - ".$info_curso->nomina."&&&";
			}
			
		} // function view_alumnos_by_registro()
		
		function captura(){
			$this -> valida();
			$xccursos = new Xccursos();
			$xalumnocursos = new Xalumnocursos();
			$xsalones = new Xcsalones();
			$materias = new Materia();
			$maestros = new Maestros();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			unset( $this -> materias );
			unset( $this -> nomina );
			unset( $this -> coordinador );
			unset( $this -> division );
			unset( $this -> materias2000 );
			unset( $this -> materias2007 );
			unset( $this -> maestros );
			unset( $this -> salones );
			unset( $this -> periodoCompleto );
			
			$this -> materias = null;
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$i = 0 ;
			foreach( $materias -> find( "plan = 2000 order by clave"  ) as $materia ){
				$this -> materias2000[$i] = $materia;
				$i++;
			}
			
			$i = 0 ;
			foreach( $materias -> find( "plan = 2007 order by clave"  ) as $materia ){
				$this -> materias2007[$i] = $materia;
				$i++;
			}
			
			$i = 0;
			foreach( $maestros -> find_all_by_sql( "Select * from maestros order by nomina" ) as $maestro ){
				$this -> maestros[$i] = $maestro;
				$i++;
			}
			
			$i = 0;
			foreach( $xsalones -> find_all_by_sql( "Select * from xcsalones order by id" ) as $xsalon ){
				$this -> salones[$i] = $xsalon;
				$i++;
			}
			
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
		} // function captura()
		
		function capturando(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			unset( $this -> nomina );
			unset( $this -> coordinador );
			unset( $this -> division );
			unset( $this -> exito );
			unset( $this -> clavecursoo );
			unset( $this -> clavecursooespejo );
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$profesor		= $this -> post("profesor");
			$materia2000	= $this -> post("materias2000");
			$materia2007	= $this -> post("materias2007");
			// ID del salon
			$salon			= $this -> post("salon");
			$minimo			= $this -> post("minimo");
			$maximo			= $this -> post("maximo");
			$horapresencial	= $this -> post("horapresencial");
			
			$presencial		= $this -> post("presencial");
			// El cupo es el espacio igual al máximo de
			//alumnos que pueden inscribirse.
			$cupo 			= $maximo;
			// La disponibilidad es igual al tamaño máximo de alumnos
			//que pueden inscribirse, ya que al momento de crearse el curso
			//no hay aún alumnos inscritos.
			$disponibilidad	= $maximo;
			// Bandera para saber si se va a dejar apartado este curso,
			//para los alumnos de primer ingreso.
			$primeringreso	= $this -> post("primeringreso");
			$carrera = "TODOS";
			
			print_r($salon);
			
			
			unset($this -> error);
			$error = "";
			$this -> exito = 0;
			for( $i = 7; $i < 22; $i++ ){ // Horas
				for( $j = 1; $j < 7; $j++ ){ // Dias
					if( !isset($salon) && $salon[$i][$j] > 0 ){
						if( $salones = $xchoras -> find_first( "xcsalones_id = ".$salon[$i][$j].
																" and dia = ".$j.
																" and hora = ".$i.
																" and periodo = ".$periodo ) ){
							switch($j){
								case 1:
									$dia = "Lunes";
										break;
								case 2:
									$dia = "Martes";
										break;
								case 3:
									$dia = "Mi&eacute;rcoles";
										break;
								case 4:
									$dia = "Jueves";
										break;
								case 5:
									$dia = "Viernes";
										break;
								case 6:
									$dia = "S&aacute;bado";
										break;
							}
							if( isset($horapresencial[$i][$j]) && 
								( $horapresencial[$i][$j] != "on" )){
								$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
								$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
										  $salonn -> nombre." el ".$dia." a las hora: ".$i." aa ".$horapresencial[$i][$j]."<br />";
							}
							if(!isset($horapresencial[$i][$j])){
								$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
								$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
										  $salonn -> nombre." el ".$dia." a las hora: ".$i."<br />";
							}
						}
					}
				}
			}
			$resto = 0;
			if( $error == "" ){
				foreach( $xccursos -> find_all_by_sql( "Select max(id) as maximo 
													   from xccursos" )as $cursos ){
					
					if( $materia2000 != "-1" ){
						if( $cursos -> maximo < 10 )
							$resto = "000".($cursos -> maximo + 1);
						else if( $cursos -> maximo < 100 )
							$resto = "00".($cursos -> maximo + 1);
						else if( $cursos -> maximo < 1000 )
							$resto = "0".($cursos -> maximo + 1);
						else
							$resto = ($cursos -> maximo + 1);
						
						$xccursos -> id = ($cursos -> maximo + 1);
						$xccursos -> clavecurso = Session::get_data("coordinacion").$resto;
						$xccursos -> materia = $materia2000;
						$xccursos -> nomina = $profesor;
						$xccursos -> cupo = $cupo;
						$xccursos -> disponibilidad = $disponibilidad;
						$xccursos -> minimo = $minimo;
						$xccursos -> maximo = $maximo;
						$xccursos -> activo = $primeringreso;
						$xccursos -> asesoria = $presencial;
						$xccursos -> periodo = $periodo;
						$xccursos -> carrera = $carrera;
						$xccursos -> curso = Session::get_data("coordinacion").$resto;
						$xccursos -> division = Session::get_data("coordinacion");
						$xccursos -> horas1 = '0';
						$xccursos -> avance1 = '0';
						$xccursos -> horas2 = '0';
						$xccursos -> avance2 = '0';
						$xccursos -> horas3 = '0';
						$xccursos -> avance3 = '0';
						
						if( $materia2007 != "-1" ){
							$xccursos -> paralelo = Session::get_data("coordinacion").($resto + 1);
						}
						else{
							$xccursos -> paralelo = "-1";
						}
						$xccursos -> create();
						
						// Lo guardo de está forma, para que en la base de datos
						//se guarden en orden de día y hora, para una
						//manipulación de datos más sencilla.
						for( $j = 1; $j < 7; $j++ ){ // Dias
							for( $i = 7; $i < 22; $i++ ){ // Horas
								if( isset($salon[$i][$j]) && $salon[$i][$j] > 0 ){
									$xchoras -> curso_id = $resto;
									$xchoras -> xcsalones_id = $salon[$i][$j];
									$xchoras -> dia = $j;
									$xchoras -> hora = $i;
									$xchoras -> bloque = '0';
									$xchoras -> periodo = $periodo;
									if( isset($horapresencial[$i][$j]) && 
											($horapresencial[$i][$j] == "on" || 
												$horapresencial[$i][$j] == "ON") ){
										$xchoras -> presencial = '0';
									}
									else{
										$xchoras -> presencial = 1;
									}
									$xchoras -> create();
								}
							}
						}
						$this -> exito = 1;
						$this -> clavecursoo = Session::get_data("coordinacion").$resto;
					} // if( $materia2000 != "" || $materia2000 != null )
					
					if( $materia2007 != "-1" ){
						if( $this -> exito == 1 )
							$restar = 0;
						else
							$restar = 1;
						
						$maxIDdelcurso = $cursos -> maximo + 2 - $restar;
						
						if( $cursos -> maximo < 10 )
							$resto = "000".($maxIDdelcurso);
						else if( $cursos -> maximo < 100 )
							$resto = "00".($maxIDdelcurso);
						else if( $cursos -> maximo < 1000 )
							$resto = "0".($maxIDdelcurso);
						else
							$resto = ($maxIDdelcurso);
						
						$xccursos -> id = ($maxIDdelcurso);
						$xccursos -> clavecurso = Session::get_data("coordinacion").$resto;
						$xccursos -> materia = $materia2007;
						$xccursos -> nomina = $profesor;
						$xccursos -> cupo = $cupo;
						$xccursos -> disponibilidad = $disponibilidad;
						$xccursos -> minimo = $minimo;
						$xccursos -> maximo = $maximo;
						$xccursos -> activo = $primeringreso;
						$xccursos -> asesoria = $presencial;
						$xccursos -> periodo = $periodo;
						$xccursos -> carrera = $carrera;
						$xccursos -> curso = Session::get_data("coordinacion").$resto;
						$xccursos -> division = Session::get_data("coordinacion");
						$xccursos -> horas1 = '0';
						$xccursos -> avance1 = '0';
						$xccursos -> horas2 = '0';
						$xccursos -> avance2 = '0';
						$xccursos -> horas3 = '0';
						$xccursos -> avance3 = '0';
						
						if( $materia2000 != "-1" ){
							$xccursos -> paralelo = Session::get_data("coordinacion").($resto -1 );
						}
						else{
							$xccursos -> paralelo = "-1";
						}
						
						$xccursos -> create();
						// Lo guardo de está forma, para que en la base de datos
						//se guarden en orden de día y hora, para una
						//manipulación de datos más sencilla.
						for( $j = 1; $j < 7; $j++ ){ // Dias
							for( $i = 7; $i < 22; $i++ ){ // Horas
								if( isset($salon[$i][$j]) && $salon[$i][$j] > 0 ){
									$xchoras -> curso_id = $resto;
									$xchoras -> xcsalones_id = $salon[$i][$j];
									$xchoras -> dia = $j;
									$xchoras -> hora = $i;
									$xchoras -> bloque = '0';
									$xchoras -> periodo = $periodo;
									if( isset($horapresencial[$i][$j]) && 
											($horapresencial[$i][$j] == "on" || 
												$horapresencial[$i][$j] == "ON") ){
										$xchoras -> presencial = '0';
									}
									else{
										$xchoras -> presencial = 1;
									}
									$xchoras -> create();
								}
							}
						}
						if( $this -> exito == 1 ){
							$this -> exito = 2;
							$this -> clavecursooespejo = Session::get_data("coordinacion").$resto;
						}
						else{
							$this -> exito = 1;
							$this -> clavecursoo = Session::get_data("coordinacion").$resto;
						}
					} // if( $materia2007 != "" || $materia2007 != null )
				} // foreach( $xccursos -> find_all_by_sql( "Select max(id) as maximo from xccursos" )as $cursos )
			} // if( $error == "" )
			else{
				$this -> exito = 0;
			}
			$this -> error = $error;
			
		} // function capturando()
		
		function salones(){
			$this -> valida();
			$xchoras	= new Xchorascursos();
			$xsalones	= new Xcsalones();
			$maestros	= new Maestros();
			$Periodos	= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$k = 0;
			foreach( $xsalones -> find( "periodo = ".$periodo, 
					"order: edificio, nombre asc LIMIT 0, 25" ) as $xsalon ){
				$this -> salones[$k] = $xsalon;
				$k++;
			}
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					if ( $xchora = $xchoras -> find_first( "periodo = ".$periodo.
													  " and dia = ".$j.
													  " and hora = ".$i.
													  " and xcsalones_id = ".$this -> salones[0] -> id ) ){
						$this -> horas[$j][$i][$this -> salones[0] -> id] = $xchora;
					}
					else{
						$this -> horas[$j][$i][$this -> salones[0] -> id] = null;
					}
				}
			}
			
			$n = $xsalones -> count();
			$this -> p1 = 0;
			$this -> p2 = -1;
			$this -> p1 = $p - 25; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 25; if($this -> p2 >= $n) $this -> p2 = $p;
			
		} // function salones()
		
		function salonesajax( $p ){
			$this -> valida();
			
			$this -> set_response("view");
			
			//$division = Session::get_data('division');
			
			$xchoras	= new Xchorascursos();
			$xsalones	= new Xcsalones();
			$maestros	= new Maestros();
			$Periodos	= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			for( $i = 0; $i < 25; $i ++ )
				$this -> salones[$i] = null;
			
			unset($this -> salones);
			$k = 0;
			foreach( $xsalones -> find
					( "periodo = ".$periodo.
							" ORDER BY edificio, nombre LIMIT ".$p.",25" ) as $xsalon ){
				$this -> salones[$k] = $xsalon;
				$k++;
			}
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					if ( $xchora = $xchoras -> find_first( "periodo = ".$periodo.
													  " and dia = ".$j.
													  " and hora = ".$i.
													  " and xcsalones_id = ".$this -> salones[0] -> id ) ){
						$this -> horas[$j][$i][$this -> salones[0] -> id] = $xchora;
					}
					else{
						$this -> horas[$j][$i][$this -> salones[0] -> id] = null;
					}
				}
			}
			
			$n = $xsalones -> count();
			
			$this -> p1 = $p - 25; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 25; if($this -> p2 >= $n) $this -> p2 = $p;
			echo $this -> render_partial("listadosalones");
		} // function salonesajax($p=0)
		
		function jornadasalon( $idSalon ){
			$this -> valida();
			$this -> set_response("view");
			$xchoras	= new Xchorascursos();
			$xsalones	= new Xcsalones();
			//$xcsalones= new Xcsalones();
			$maestros	= new Maestros();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset($this -> nombreSalon);
			unset($this -> horas);
			
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					$this -> horas[$j][$i] = null;
				}
			}
			foreach( $xchoras -> find
					( "xcsalones_id = ".$idSalon." 
						and periodo = ".$periodo ) as $xchora ){
				$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
			}
			$this -> nombreSalon = $xsalones -> find_first
					("id = ".$idSalon);
			
			echo $this -> render_partial("horario");
			
		} // function jornadasalon()
		
		function cursos(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$i = 0;
			$limit_cursos = 75;
			
			unset($this -> cursos);
			unset($this -> materias2000);
			unset($this -> materias2007);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this->num_cursos);
			unset($this->p1);
			unset($this->p2);
			
			foreach( $xccursos -> find_all_by_sql
					( "Select * from xccursos where periodo = ".$periodo." order by clavecurso limit 0, ".$limit_cursos ) as $xccurso ){
				$this -> cursos[$i] = $xccurso;
				$j = 0;
				
				$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
				$this -> materias[$i] = $materia;
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maestros[$i] = $maestro;
				
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'", "order: id asc" ) as $xchora ){
					
					$this -> horas[$xccurso -> id][$j] = $xchora;
					$this -> salon[$xccurso -> id][$j] = $xsalones -> 
							find_first( "id = ".$xchora -> xcsalones_id );
					$j++;
				}
				$i++;
			}
			
			$this -> num_cursos = $xccursos->count("periodo = '".$periodo."'");
			$this -> p1 = 0;
			$this -> p2 = $limit_cursos;
		} // function cursos()
		
		function cursos_ajax($p){
			$this -> valida();
			$this -> set_response("view");
			
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$i = 0;
			$limit_cursos = 75;
			
			unset($this -> cursos);
			unset($this -> materias2000);
			unset($this -> materias2007);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			
			foreach( $xccursos -> find_all_by_sql
					( "Select * from xccursos where periodo = ".$periodo.
						" order by clavecurso limit ".$p.",".$limit_cursos ) as $xccurso ){
				$this -> cursos[$i] = $xccurso;
				$j = 0;
				
				$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
				$this -> materias[$i] = $materia;
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maestros[$i] = $maestro;
				
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'", "order: id asc" ) as $xchora ){
					
					$this -> horas[$xccurso -> id][$j] = $xchora;
					$this -> salon[$xccurso -> id][$j] = $xsalones -> 
							find_first( "id = ".$xchora -> xcsalones_id );
					$j++;
				}
				$i++;
			}
			
			$this -> p1 = $p - $limit_cursos; if($this -> p1 <= 0) $this -> p1 = 0;
			$this -> p2 = $p + $limit_cursos; if($this -> p2 >= $this -> num_cursos) $this -> p2 = $p;
			echo $this -> render_partial("cursos_ajax");
		} // function cursos_ajax()
		
		function buscarElCurso(){
			$this -> valida();
			$this -> set_response("view");
			
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos 		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset($this -> mensaje);
			
			$curso = $this -> post("buscarcurso");
			
			if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
				$this -> cursos = $xccurso;
				$j = 0;
				
				$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
				$this -> materias = $materia;
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maestros = $maestro;
				
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'", "order: id asc" ) as $xchora ){
					
					$this -> horas[$xccurso -> id][$j] = $xchora;
					$this -> salon[$xccurso -> id][$j] = $xsalones -> 
							find_first( "id = ".$xchora -> xcsalones_id );
					$j++;
				}
			}
			else{
				$this -> mensaje = "El curso \"".$curso."\" no fue encontrado";
			}
			
			echo $this -> render_partial("buscarElCurso");
			
		} // function buscarElCurso()
		
		function borrarCurso(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$curso = $this -> post("deleteID");
			
			// Primero me traigo el objeto xccurso, y sus relaciones con xchorascursos,
			//esto con el motivo de dar tiempo, que si alguien se inscribe a ese curso,
			//a la hora de verificar en xalumnocurso, y si ya existe alguien, no se borre el curso,
			//porque si no, podría darse el caso en el me estoy traiendo los objetos de xccursos,
			//y xchorascursos para borrarlos, y se inscribe alguien a ese curso, y empieza a haber
			//incoherencia de datos.
			$i = 0;
			$xccurso = $xccursos -> find_first("clavecurso = '".$curso."'");
			if( $xccurso -> clavecurso != $curso )
				$this->redirect('capturarcursos/cursos');
			
			if( substr($xccurso -> clavecurso, 0, 3) != $this -> division ){
				$this->redirect('capturarcursos/borrarDivisionErr');
			}
			
			foreach( $xchoras -> find("curso_id = '".$xccurso->id."'") as $xchora ){
				$hora[$i] = $xchora;
				$i++;
			}
			
			if( $xalcurso = $xalumnocursos -> find_first("curso_id = '".$xccurso->id."'") ){
				// No se puede borrar el Curso ya que existen alumnos inscritos en él...
				$this -> mensaje = "No se puede borrar el curso, debido ah que existen
						alumnos inscritos en &eacute;l";
			}
			else{
				$xccurso -> delete();
				foreach( $hora as $h ){
					$h -> delete();
				}
				$this -> mensaje = "Curso borrado exit&oacute;samente";
			}
		} // function borrarCurso()
		
		function editarCurso(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$curso = $this -> post("editID");
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			// Primero me traigo el objeto xccurso, y sus relaciones con xchorascursos,
			//esto con el motivo de dar tiempo, que si alguien se inscribe a ese curso,
			//a la hora de verificar en xalumnocurso, y si ya existe alguien, no se borre el curso,
			//porque si no, podría darse el caso en el me estoy traiendo los objetos de xccursos,
			//y xchorascursos para borrarlos, y se inscribe alguien a ese curso, podría haber
			//incoherencia de datos.
			$i = 0;
			$xccurso = $xccursos -> find_first("clavecurso = '".$curso."'");
			if( $xccurso -> clavecurso != $curso )
				$this->redirect('capturarcursos/cursos');
			
			if( substr($xccurso -> clavecurso, 0, 3) != $this -> division ){
				$this->redirect('capturarcursos/editarDivisionErr');
			}
			
			$this -> nombreDelCurso = $xccurso -> clavecurso;
			
			foreach( $xchoras -> find("curso_id = '".$xccurso->id."'") as $xchora ){
				$hora[$i] = $xchora;
				$i++;
			}
			unset($this -> materia);
			unset($this -> yaHayAlumnos);
			unset($this -> mensaje);
			unset($this -> cursoo);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> materiaaa);
			unset($this -> salones);
			
			if( $xalcurso = $xalumnocursos -> find_first("curso_id = '".$xccurso->id."'") ){
				// No se puede borrar el Curso ya que existen alumnos inscritos en él...
				$this -> yaHayAlumnos = 1;
				$this -> mensaje = "No se puede editar el curso, debido ah que existen
						alumnos inscritos en &eacute;l";
				
				if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
					$this -> cursoo = $xccurso;
					$j = 0;
					
					
					if( $materia = $materias -> find_first( "plan = 2000 and clave = '".$xccurso -> materia."'" ) ){
						$this -> materiaaa = 2000;
						$this -> materia = $materia;
					}
					if( $materia = $materias -> find_first( "plan = 2007 and clave = '".$xccurso -> materia."'" ) ){
						$this -> materiaaa = 2007;
						$this -> materia = $materia;
					}
					
					$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
					$this -> maest = $maestro;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'" ) as $xchora ){
						
						$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
						$this -> salon[$xchora -> dia][$xchora -> hora] = $xsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
				$i = 0;
				if( $this -> materiaaa == 2000 ){
					foreach( $materias -> find( "plan = 2000"  ) as $materia ){
						$this -> materias2000[$i] = $materia;
						$i++;
					}
				}
				
				$i = 0 ;
				if( $this -> materiaaa == 2007 ){
					foreach( $materias -> find( "plan = 2007"  ) as $materia ){
						$this -> materias2007[$i] = $materia;
						$i++;
					}
				}
				
				$i = 0;
				foreach( $maestros -> find_all_by_sql( "Select * from maestros" ) as $maestro ){
					$this -> maestros[$i] = $maestro;
					$i++;
				}
				
				$i = 0;
				foreach( $xsalones -> find_all_by_sql( "
						Select * from xcsalones 
						where periodo = ".$periodo ) as $xsalon ){
					$this -> salones[$i] = $xsalon;
					$i++;
				}
				
				$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
				
			}
			else{
				$this -> yaHayAlumnos = 0;
				if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
					$this -> cursoo = $xccurso;
					$j = 0;
					
					if( $materia = $materias -> find_first( "plan = 2000 and clave = '".$xccurso -> materia."'" ) ){
						$this -> materiaaa = 2000;
						$this -> materia = $materia;
					}
					if( $materia = $materias -> find_first( "plan = 2007 and clave = '".$xccurso -> materia."'" ) ){
						$this -> materiaaa = 2007;
						$this -> materia = $materia;
					}
					
					$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
					$this -> maest = $maestro;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'" ) as $xchora ){
						
						$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
						$this -> salon[$xchora -> dia][$xchora -> hora] = $xsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
				$i = 0;
				foreach( $materias -> find( "plan = 2000"  ) as $materia ){
					$this -> materias2000[$i] = $materia;
					$i++;
				}
				
				$i = 0 ;
				foreach( $materias -> find( "plan = 2007"  ) as $materia ){
					$this -> materias2007[$i] = $materia;
					$i++;
				}
				
				$i = 0;
				foreach( $maestros -> find_all_by_sql( "Select * from maestros" ) as $maestro ){
					$this -> maestros[$i] = $maestro;
					$i++;
				}
				
				$i = 0;
				foreach( $xsalones -> find_all_by_sql( "
						Select * from xcsalones 
						where periodo = ".$periodo ) as $xsalon ){
					$this -> salones[$i] = $xsalon;
					$i++;
				}
				$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
			}
			
		} // function editarCurso()
		
		function editandoCurso(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			
			$cursoID		= $this -> post("cursoID");
			if( $cursoID == "" ){
				$this -> redirect("capturarcursos/cursos");
			}
			
			$yaHayAlumnos	= $this -> post("yaHayAlumnos");
			
			$profesor		= $this -> post("profesor");
			$materias		= $this -> post("materias");
			$minimo			= $this -> post("minimo");
			$maximo			= $this -> post("maximo");
			$primeringreso	= $this -> post("primeringreso");
			$presencial		= $this -> post("presencial");
			$salon			= $this -> post("salon");
			$horapresencial	= $this -> post("horapresencial");
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			unset($this -> error);
			$error = "";
			
			if( $yaHayAlumnos == 1 ){
				// Editar lo básico. Una lista de lo que se puede editar aparece abajo.
				// Editar Profesor.
				// Editar el minimo y maximo de alumnos. (Validando que el 
				//máximo de alumnos que quiera cambiar sea mayor al número
				//de alumnos inscritos hasta el momento).
				
				$xccur = $xccursos -> find_first( "id = ".$cursoID );
				if( $maximo < $xccur -> disponibilidad ){
					$error += "Actualmente se encuentran ".$xccur -> disponibilidad.
							"alumnos por lo que no puede poner un m&aacute;ximo
							de alumnos m&aacute;s pequeño que el que hay inscritos";
				}
				else{
					$xccur -> nomina = $profesor;
					$xccur -> cupo = $maximo;
					if( $maximo > $xccur -> maximo )
						$xccur -> disponibilidad += ($maximo - $xccur -> maximo);
					else
						$xccur -> disponibilidad -= ($xccur -> maximo - $maximo);
					$xccur -> minimo = $minimo;
					$xccur -> maximo = $maximo;
					
					if($xccur -> disponibilidad <= 0)
						$xccur -> disponibilidad = '0';
					
					if( $error == "" )
						$xccur -> save();
				}
				
				// No ocurrieron errores
				$this -> exito = 1;
			}
			else{
				// Se podrá editar todo acerca del curso, ya que aún no se encuentran
				//alumnos inscritos al curso, por lo que no hay riesgo si se mueve información.
				
				$xccur = $xccursos -> find_first( "id = ".$cursoID );
				
				$error = ""; // $error ayuda a saber si existen cruces de horas
				//en las nuevas horas editadas...
				
				for( $i = 7; $i < 22; $i++ ){ // Horas
					for( $j = 1; $j < 7; $j++ ){ // Dias
						if( $salon[$i][$j] > 0 ){
							if( $salones = $xchoras -> find_first( "xcsalones_id = ".$salon[$i][$j].
																	" and dia = ".$j.
																	" and hora = ".$i.
																	" and periodo = ".$periodo ) ){
							
								if( $salones -> curso_id != $xccur -> id ){
								
									switch($j){
										case 1:
											$dia = "Lunes";
												break;
										case 2:
											$dia = "Martes";
												break;
										case 3:
											$dia = "Mi&eacute;rcoles";
												break;
										case 4:
											$dia = "Jueves";
												break;
										case 5:
											$dia = "Viernes";
												break;
										case 6:
											$dia = "S&aacute;bado";
												break;
									}
									if( isset($horapresencial[$i][$j]) && 
										( $horapresencial[$i][$j] != "on" )){
										$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
										$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
												  $salonn -> nombre." el ".$dia." a las hora: ".$i." aa ".$horapresencial[$i][$j]."<br />";
									}
									if(!isset($horapresencial[$i][$j])){
										$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
										$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
												  $salonn -> nombre." el ".$dia." a las hora: ".$i."<br />";
									}
								}
							}
						} // if( $salon[$i][$j] > 0 )
					} // for( $j = 1; $j < 7; $j++ )
				} // for( $i = 7; $i < 22; $i++ )
				$resto = 0;
				if( $error == "" ){
					
					foreach( $xchoras -> find( "curso_id = '".
							$xccur -> id."'" ) as $xchorcur ){
						$xchorcur -> delete();
					}
					$xccur = $xccursos -> find_first( "id = ".$cursoID );
					$xccur -> materia = $materias;
					$xccur -> nomina = $profesor;
					$xccur -> cupo = $maximo;
					$xccur -> disponibilidad += ($maximo - $xccur -> maximo);
					if($xccur -> disponibilidad <= 0)
						$xccur -> disponibilidad = '0';
					$xccur -> minimo = $minimo;
					$xccur -> maximo = $maximo;
					$xccur -> activo = $primeringreso;
					$xccur -> asesoria = $presencial;
					$xccur -> save();
					
					// Lo guardo de está forma, para que en la base de datos
					//se guarden en orden de día y hora, para una
					//manipulación de datos más sencilla.
					for( $j = 1; $j < 7; $j++ ){ // Dias
						for( $i = 7; $i < 22; $i++ ){ // Horas
							if( $salon[$i][$j] > 0 ){
								$xchoras -> curso_id = $xccur -> id;
								$xchoras -> xcsalones_id = $salon[$i][$j];
								$xchoras -> dia = $j;
								$xchoras -> hora = $i;
								$xchoras -> bloque = '0';
								$xchoras -> periodo = $periodo;
								if( isset($horapresencial[$i][$j]) && 
										($horapresencial[$i][$j] == "on" || 
											$horapresencial[$i][$j] == "ON") ){
									$xchoras -> presencial = '0';
								}
								else{
									$xchoras -> presencial = 1;
								}
								$xchoras -> create();
							}
						}
					}
					// No ocurrieron errores
					$this -> exito = 1;
				}
				else{
					// Ocurrio algún error
					$this -> exito = 0;
				}
			}
			$this -> error = $error;
		} // function editandoCurso()
		
		function editarDivisionErr(){
			$this -> valida();
			$maestros = new Maestros();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			unset( $this -> error );
			
			$this -> error = "<br />No puede editar un curso que no sea de su 
					divisi&oacute;n<br />";
			
		} // function editarDivisionErr()
		
		function borrarDivisionErr(){
			$this -> valida();
			$maestros = new Maestros();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$this -> error = "<br />No puede borrar un curso que no sea de su 
					divisi&oacute;n<br />";
			
		} // function borrarDivisionErr()
		
		// Tonala!
		function capturaT(){
			$this -> valida();
			$xccursos = new Xtcursos();
			$xalumnocursos = new Xtalumnocursos();
			$xsalones = new Xtsalones();
			$materias = new Materia();
			$maestros = new Maestros();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			unset($this -> materias);
			unset($this -> maestros);
			unset($this -> salones);
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			/*
			$i = 0 ;
			foreach( $materias -> find( "plan = 2000"  ) as $materia ){
				$this -> materias2000[$i] = $materia;
				$i++;
			}
			*/
			
			$i = 0 ;
			foreach( $materias -> find( "plan = 2007 order by clave"  ) as $materia ){
				$this -> materias[$i] = $materia;
				$i++;
			}
			
			$i = 0;
			foreach( $maestros -> find_all_by_sql( "Select * from maestros order by nomina" ) as $maestro ){
				$this -> maestros[$i] = $maestro;
				$i++;
			}
			
			$i = 0;
			foreach( $xsalones -> find( "periodo = ".$periodo ) as $xsalon ){
				$this -> salones[$i] = $xsalon;
				$i++;
			}
			
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
		} // function capturaT()
		
		function capturandoT(){
			$this -> valida();
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$profesor		= $this -> post("profesor");
			$materia		= $this -> post("materias");
			//$materia2007	= $this -> post("materias2007");
			// ID del salon
			$salon			= $this -> post("salon");
			$minimo			= $this -> post("minimo");
			$maximo			= $this -> post("maximo");
			
			$presencial		= $this -> post("presencial");
			// El cupo es el espacio igual al máximo de
			//alumnos que pueden inscribirse.
			$cupo 			= $maximo;
			// La disponibilidad es igual al tamaño máximo de alumnos
			//que pueden inscribirse, ya que al momento de crearse el curso
			//no hay aún alumnos inscritos.
			$disponibilidad	= $maximo;
			// Bandera para saber si se va a dejar apartado este curso,
			//para los alumnos de primer ingreso.
			$primeringreso	= $this -> post("primeringreso");
			$carrera = "TODOS";
			
			unset($this -> error);
			$error = "";
			
			for( $i = 14; $i < 22; $i++ ){ // Horas
				for( $j = 1; $j < 7; $j++ ){ // Dias
					if( $salon[$i][$j] > 0 ){
						if( $salones = $xchoras -> find_first( "xtsalones_id = ".$salon[$i][$j].
																" and dia = ".$j.
																" and hora = ".$i.
																" and periodo = ".$periodo ) ){
							switch($j){
								case 1:
									$dia = "Lunes";
										break;
								case 2:
									$dia = "Martes";
										break;
								case 3:
									$dia = "Mi&eacute;rcoles";
										break;
								case 4:
									$dia = "Jueves";
										break;
								case 5:
									$dia = "Viernes";
										break;
								case 6:
									$dia = "S&aacute;bado";
										break;
							}
							$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
							$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
									  $salonn -> nombre." el ".$dia." a las hora: ".$i."<br />";
						}
					}
				}
			}
			$resto = 0;
			if( $error == "" ){
				foreach( $xccursos -> find_all_by_sql( "Select max(id) as maximo ".
													   "from xtcursos" )as $cursos ){
					if( $cursos -> maximo < 10 )
						$resto = "000".($cursos -> maximo + 1);
					else if( $cursos -> maximo < 100 )
						$resto = "00".($cursos -> maximo + 1);
					else if( $cursos -> maximo < 1000 )
						$resto = "0".($cursos -> maximo + 1);
					else
						$resto = ($cursos -> maximo + 1);
					
					$xccursos -> id = ($cursos -> maximo + 1);
					$xccursos -> clavecurso = Session::get_data("coordinacion").$resto;
					$xccursos -> materia = $materia;
					$xccursos -> nomina = $profesor;
					$xccursos -> cupo = $cupo;
					$xccursos -> disponibilidad = $disponibilidad;
					$xccursos -> minimo = $minimo;
					$xccursos -> maximo = $maximo;
					$xccursos -> activo = $primeringreso;
					$xccursos -> asesoria = $presencial;
					$xccursos -> periodo = $periodo;
					$xccursos -> carrera = $carrera;
					$xccursos -> curso = Session::get_data("coordinacion").$resto;
					$xccursos -> division = Session::get_data("coordinacion");
					$xccursos -> horas1 = '0';
					$xccursos -> avance1 = '0';
					$xccursos -> horas2 = '0';
					$xccursos -> avance2 = '0';
					$xccursos -> horas3 = '0';
					$xccursos -> avance3 = '0';
					$xccursos -> create();
					
					
					// Lo guardo de está forma, para que en la base de datos
					//se guarden en orden de día y hora, para una
					//manipulación de datos más sencilla.
					for( $j = 1; $j < 7; $j++ ){ // Dias
						for( $i = 14; $i < 22; $i++ ){ // Horas
							if( $salon[$i][$j] > 0 ){
								$xchoras -> curso_id = $resto;
								$xchoras -> xtsalones_id = $salon[$i][$j];
								$xchoras -> dia = $j;
								$xchoras -> hora = $i;
								$xchoras -> bloque = '0';
								$xchoras -> periodo = $periodo;
								$xchoras -> create();
							}
						}
					}
					$this -> exito = 1;
				}
			}
			else{
				$this -> exito = 0;
			}
			$this -> error = $error;
			
		} // function capturandoT()
		
		function salonesT(){
			$this -> valida();
			$xchoras	= new Xthorascursos();
			$xsalones	= new Xtsalones();
			$maestros	= new Maestros();
			$Periodos	= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			unset( $this -> p1 );
			unset( $this -> p2 );
			$p = 0;
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$k = 0;
			foreach( $xsalones -> find( "periodo = ".$periodo, 
					"order: edificio, nombre asc LIMIT 0, 25" ) as $xsalon ){
				$this -> salones[$k] = $xsalon;
				$k++;
			}
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					if ( $xchora = $xchoras -> find_first( "periodo = ".$periodo.
													  " and dia = ".$j.
													  " and hora = ".$i.
													  " and xtsalones_id = ".$this -> salones[0] -> id ) ){
						$this -> horas[$j][$i][$this -> salones[0] -> id] = $xchora;
					}
					else{
						$this -> horas[$j][$i][$this -> salones[0] -> id] = null;
					}
				}
			}
			
			$n = $xsalones -> count();
			$this -> p1 = 0;
			$this -> p2 = -1;
			$this -> p1 = $p - 25; 
			if($this -> p1 < 0)
				$this -> p1 = 0;
			
			$this -> p2 = $p + 25;
			if($this -> p2 >= $n)
				$this -> p2 = $p;
			
		} // function salonesT()
		
		function salonesajaxT( $p ){
			$this -> valida();
			
			$this -> set_response("view");
			
			//$division = Session::get_data('division');
			
			$xchoras	= new Xthorascursos();
			$xsalones	= new Xtsalones();
			$maestros	= new Maestros();
			$Periodos	= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			for( $i = 0; $i < 25; $i ++ )
				$this -> salones[$i] = null;
			
			unset($this -> salones);
			$k = 0;
			
			$cuantossalones = $xsalones -> count( "periodo =".$periodo );
			if( $cuantossalones < 25 )
				$p = 0;
			
			foreach( $xsalones -> find
					( "periodo = ".$periodo.
							" ORDER BY edificio, nombre LIMIT ".$p.",25" ) as $xsalon ){
				$this -> salones[$k] = $xsalon;
				$k++;
			}
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					if ( $xchora = $xchoras -> find_first( "periodo = ".$periodo.
													  " and dia = ".$j.
													  " and hora = ".$i.
													  " and xtsalones_id = ".$this -> salones[0] -> id ) ){
						$this -> horas[$j][$i][$this -> salones[0] -> id] = $xchora;
					}
					else{
						$this -> horas[$j][$i][$this -> salones[0] -> id] = null;
					}
				}
			}
			
			$n = $xsalones -> count();
			
			$this -> p1 = $p - 25; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 25; if($this -> p2 >= $n) $this -> p2 = $p;
			echo $this -> render_partial("listadosalonest");
		} // function salonesajaxT($p=0)
		
		function jornadasalonT( $idSalon ){
			$this -> valida();
			$this -> set_response("view");
			$xchoras	= new Xthorascursos();
			$xsalones	= new Xtsalones();
			//$xcsalones= new Xcsalones();
			$maestros	= new Maestros();
			$Periodos	= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			for( $i = 7; $i < 22; $i++ ){
				for( $j = 1; $j < 7; $j++ ){
					$this -> horas[$j][$i] = null;
				}
			}
			foreach( $xchoras -> find
					( "xtsalones_id = ".$idSalon." 
						and periodo = ".$periodo ) as $xthora ){
				$this -> horas[$xthora -> dia][$xthora -> hora] = $xthora;
			}
			$this -> nombreSalon = $xsalones -> find_first
					("id = ".$idSalon);
			
			echo $this -> render_partial("horariot");
			
		} // function jornadasalonT( $idSalon )
		
		function cursosT(){
			$this -> valida();
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina." order by nomina" );
			$this -> division = Session::get_data("coordinacion");
			
			$i = 0;
			
			unset($this -> cursos);
			unset($this -> materias);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			
			foreach( $xccursos -> find_all_by_sql
					( "Select * from xtcursos where periodo = ".$periodo ) as $xccurso ){
				$this -> cursos[$i] = $xccurso;
				$j = 0;
				
				$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
				$this -> materias[$i] = $materia;
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maestros[$i] = $maestro;
				
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'", "order: id asc" ) as $xchora ){
					
					$this -> horas[$xccurso -> id][$j] = $xchora;
					$this -> salon[$xccurso -> id][$j] = $xsalones -> 
							find_first( "id = ".$xchora -> xtsalones_id );
					$j++;
				}
				$i++;
			}
		} // function cursosT()
		
		function buscarElCursoT(){
			$this -> valida();
			$this -> set_response("view");
			
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset($this -> mensaje);
			
			$curso = $this -> post("buscarcurso");
			
			if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
				$this -> cursos = $xccurso;
				$j = 0;
				
				$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
				$this -> materias = $materia;
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maestros = $maestro;
				
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'", "order: id asc" ) as $xchora ){
					
					$this -> horas[$xccurso -> id][$j] = $xchora;
					$this -> salon[$xccurso -> id][$j] = $xsalones -> 
							find_first( "id = ".$xchora -> xtsalones_id );
					$j++;
				}
			}
			else{
				$this -> mensaje = "El curso \"".$curso."\" no fue encontrado";
			}
			
			echo $this -> render_partial("buscarElCursoT");
			
		} // function buscarElCurso()
		
		function borrarCursoT(){
			$this -> valida();
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$curso = $this -> post("deleteID");
			
			// Primero me traigo el objeto xccurso, y sus relaciones con xchorascursos,
			//esto con el motivo de dar tiempo, que si alguien se inscribe a ese curso,
			//a la hora de verificar en xalumnocurso, y si ya existe alguien, no se borre el curso,
			//porque si no, podría darse el caso en el me estoy traiendo los objetos de xccursos,
			//y xchorascursos para borrarlos, y se inscribe alguien a ese curso, y empieza a haber
			//incoherencia de datos.
			$i = 0;
			$xccurso = $xccursos -> find_first("clavecurso = '".$curso."'");
			if( $xccurso -> clavecurso != $curso )
				$this->redirect('capturarcursos/cursosT');
			
			if( substr($xccurso -> clavecurso, 0, 3) != $this -> division ){
				$this->redirect('capturarcursos/borrarDivisionErrT');
			}
			
			foreach( $xchoras -> find("curso_id = '".$xccurso->id."'") as $xchora ){
				$hora[$i] = $xchora;
				$i++;
			}
			
			if( $xalcurso = $xalumnocursos -> find_first("curso = '".$curso."'") ){
				// No se puede borrar el Curso ya que existen alumnos inscritos en él...
				$this -> mensaje = "No se puede borrar el curso, debido ah que existen
						alumnos inscritos en &eacute;l";
			}
			else{
				$xccurso -> delete();
				foreach( $hora as $h ){
					$h -> delete();
				}
				$this -> mensaje = "Curso borrado exit&oacute;samente";
			}
		} // function borrarCurso()
		
		function editarCursoT(){
			$this -> valida();
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$curso = $this -> post("editID");
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			unset( $this -> yaHayalumnos );
			unset( $this -> mensaje );
			unset( $this -> cursoo );
			unset( $this -> nombreDelCurso );
			unset( $this -> mat );
			unset( $this -> maest );
			unset( $this -> horas );
			unset( $this -> salon );
			unset( $this -> materias );
			unset( $this -> maestros );
			unset( $this -> salones );
			unset( $this -> periodoCompleto );
			
			// Primero me traigo el objeto xccurso, y sus relaciones con xchorascursos,
			//esto con el motivo de dar tiempo, que si alguien se inscribe a ese curso,
			//a la hora de verificar en xalumnocurso, y si ya existe alguien, no se borre el curso,
			//porque si no, podría darse el caso en el me estoy traiendo los objetos de xccursos,
			//y xchorascursos para borrarlos, y se inscribe alguien a ese curso, podría haber
			//incoherencia de datos.
			$i = 0;
			$xccurso = $xccursos -> find_first("clavecurso = '".$curso."'");
			if( $xccurso -> clavecurso != $curso )
				$this->redirect('capturarcursos/cursosT');
			
			if( $xccurso -> division != $this -> division && $this -> division != "MCT" ){
					$this->redirect('capturarcursos/editarDivisionErrT');
			}
			
			$this -> nombreDelCurso = $xccurso -> clavecurso;
			
			foreach( $xchoras -> find("curso_id = '".$xccurso->id."'") as $xchora ){
				$hora[$i] = $xchora;
				$i++;
			}
			
			if( $xalcurso = $xalumnocursos -> find_first("curso_id = '".$xccurso->id."'") ){
				// No se puede borrar el Curso ya que existen alumnos inscritos en él...
				$this -> yaHayAlumnos = 1;
				$this -> mensaje = "No se puede editar el curso, debido ah que existen
						alumnos inscritos en &eacute;l";
				
				if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
					$this -> cursoo = $xccurso;
					$j = 0;
					
					$this -> mat = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
					
					$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
					$this -> maest = $maestro;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'" ) as $xchora ){
						
						$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
						$this -> salon[$xchora -> dia][$xchora -> hora] = $xsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
				
				$i = 0 ;
				foreach( $materias -> find( "plan = 2007"  ) as $materia ){
					$this -> materias[$i] = $materia;
					$i++;
				}
				
				$i = 0;
				foreach( $maestros -> find_all_by_sql( "Select * from maestros" ) as $maestro ){
					$this -> maestros[$i] = $maestro;
					$i++;
				}
				
				$i = 0;
				foreach( $xsalones -> find_all_by_sql
						( "Select * from xtsalones where periodo = ".$periodo ) as $xsalon ){
					$this -> salones[$i] = $xsalon;
					$i++;
				}
				
				$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
				
			}
			else{
				$this -> yaHayAlumnos = 0;
				if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
					$this -> cursoo = $xccurso;
					$j = 0;
					
					$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
					$this -> mat = $materia;
					
					$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
					$this -> maest = $maestro;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'" ) as $xchora ){
						
						$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
						$this -> salon[$xchora -> dia][$xchora -> hora] = $xsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
				
				$i = 0 ;
				foreach( $materias -> find( "plan = 2007"  ) as $materia ){
					$this -> materias[$i] = $materia;
					$i++;
				}
				
				$i = 0;
				foreach( $maestros -> find_all_by_sql( "Select * from maestros" ) as $maestro ){
					$this -> maestros[$i] = $maestro;
					$i++;
				}
				
				$i = 0;
				foreach( $xsalones -> find_all_by_sql
						( "Select * from xtsalones where periodo = ".$periodo ) as $xsalon ){
					$this -> salones[$i] = $xsalon;
					$i++;
				}
				
				$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
			}
			
		} // function editarCurso()
		
		function editandoCursoT(){
			$this -> valida();
			$xccursos		= new Xtcursos();
			$xchoras		= new Xthorascursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xsalones		= new Xtsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$cursoID		= $this -> post("cursoID");
			if( $cursoID == "" ){
				$this -> redirect("capturarcursos/cursosT");
			}
			
			$yaHayAlumnos	= $this -> post("yaHayAlumnos");
			
			$profesor		= $this -> post("profesor");
			$materias		= $this -> post("materias200");
			$minimo			= $this -> post("minimo");
			$maximo			= $this -> post("maximo");
			$primeringreso	= $this -> post("primeringreso");
			$presencial		= $this -> post("presencial");
			$salon			= $this -> post("salon");
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			unset($this -> error);
			$error = "";
			
			if( $yaHayAlumnos == 1 ){
				// Editar lo básico. Una lista de lo que se puede editar aparece abajo.
				// Editar Profesor.
				// Editar el minimo y maximo de alumnos. (Validando que el 
				//máximo de alumnos que quiera cambiar sea mayor al número
				//de alumnos inscritos hasta el momento).
				
				$xccur = $xccursos -> find_first( "id = ".$cursoID );
				if( $maximo < $xccur -> disponibilidad ){
					$error += "Actualmente se encuentran ".$xccur -> disponibilidad.
							"alumnos por lo que no puede poner un m&aacute;ximo
							de alumnos m&aacute;s pequeño que el que hay inscritos";
				}
				else{
					$xccur -> nomina = $profesor;
					$xccur -> cupo = $maximo;
					if( $maximo > $xccur -> maximo )
						$xccur -> disponibilidad += ($maximo - $xccur -> maximo);
					else
						$xccur -> disponibilidad -= ($xccur -> maximo - $maximo);
					if($xccur -> disponibilidad <= 0)
						$xccur -> disponibilidad = '0';
					$xccur -> minimo = $minimo;
					$xccur -> maximo = $maximo;
					
					if( $error == "" )
						$xccur -> save();
				}
				
				// No ocurrieron errores
				$this -> exito = 1;
			}
			else{
				// Se podrá editar todo acerca del curso, ya que aún no se encuentran
				//alumnos inscritos al curso, por lo que no hay riesgo si se mueve información.
				
				$xccur = $xccursos -> find_first( "id = ".$cursoID );
				
				$error = ""; // $error ayuda a saber si existen cruces de horas
				//en las nuevas horas editadas...
				
				for( $i = 14; $i < 22; $i++ ){ // Horas
					for( $j = 1; $j < 7; $j++ ){ // Dias
						if( $salon[$i][$j] > 0 ){
							if( $salones = $xchoras -> find_first( "xtsalones_id = ".$salon[$i][$j].
																	" and dia = ".$j.
																	" and hora = ".$i.
																	" and periodo = ".$periodo ) ){
							
								if( $salones -> curso_id != $xccur -> id ){
								
									switch($j){
										case 1:
											$dia = "Lunes";
												break;
										case 2:
											$dia = "Martes";
												break;
										case 3:
											$dia = "Mi&eacute;rcoles";
												break;
										case 4:
											$dia = "Jueves";
												break;
										case 5:
											$dia = "Viernes";
												break;
										case 6:
											$dia = "S&aacute;bado";
												break;
									}
									$salonn = $xsalones -> find_first( "id = ".$salon[$i][$j] );
									$error .= "Existe un cruce en el sal&oacute;n: ".$salonn -> edificio.
											  $salonn -> nombre." el ".$dia." a las hora: ".$i."<br />";
								}
							}
						} // if( $salon[$i][$j] > 0 )
					} // for( $j = 1; $j < 7; $j++ )
				} // for( $i = 14; $i < 22; $i++ )
				$resto = 0;
				if( $error == "" ){
					
					foreach( $xchoras -> find( "curso_id = '".
							$xccur -> id."'" ) as $xchorcur ){
						$xchorcur -> delete();
					}
					$xccur = $xccursos -> find_first( "id = ".$cursoID );
					$xccur -> materia = $materia;
					$xccur -> nomina = $profesor;
					$xccur -> cupo = $maximo;
					$xccur -> disponibilidad += ($maximo - $xccur -> maximo);
					if($xccur -> disponibilidad <= 0)
						$xccur -> disponibilidad = '0';
					$xccur -> minimo = $minimo;
					$xccur -> maximo = $maximo;
					$xccur -> activo = $primeringreso;
					$xccur -> asesoria = $presencial;
					$xccur -> save();
					
					// Lo guardo de está forma, para que en la base de datos
					//se guarden en orden de día y hora, para una
					//manipulación de datos más sencilla.
					for( $j = 1; $j < 7; $j++ ){ // Dias
						for( $i = 14; $i < 22; $i++ ){ // Horas
							if( $salon[$i][$j] > 0 ){
								$xchoras -> curso_id = $xccur -> id;
								$xchoras -> xtsalones_id = $salon[$i][$j];
								$xchoras -> dia = $j;
								$xchoras -> hora = $i;
								$xchoras -> bloque = '0';
								$xchoras -> periodo = $periodo;
								$xchoras -> create();
							}
						}
					}
					// No ocurrieron errores
					$this -> exito = 1;
				}
				else{
					// Ocurrio algún error
					$this -> exito = 0;
				}
			}
			$this -> error = $error;
		} // function editandoCursoT()
		
		function editarDivisionErrT(){
			$this -> valida();
			$maestros		= new Maestros();
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$this -> error = "<br />No puede editar un curso que no sea de su 
					divisi&oacute;n<br />";
			
		} // function editarDivisionErrT()
		
		function borrarDivisionErrT(){
			$this -> valida();
			$maestros		= new Maestros();
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$this -> error = "<br />No puede borrar un curso que no sea de su 
					divisi&oacute;n<br />";
			
		} // function borrarDivisionErrT()
		
		function listadoDeAlumnos(){
			$xalumnocursos	= new Xalumnocursos();
			$xccursos		= new Xccursos();
			$materia		= new Materia();
			$alumnos		= new Alumnos();
			$especialidades = new Especialidades();
			$Periodos		= new Periodos();
			$Maestros		= new Maestros();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset($this -> registroAlumno);
			unset($this -> nombreAlumno);
			unset($this -> planAlumno);
			unset($this -> materiaAlumno);
			unset($this -> carreraAlumno);
			unset($this -> nomina);
			unset($this -> coordinador);
			unset($this -> division);
			
			$cursoID = $this -> post( "cursoID" );
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $Maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$this -> cursoClave = $cursoID;
			
			$Xccursos = new Xccursos();
			$xccurso = $Xccursos -> find_first("clavecurso = '".$cursoID."'");
			
			$i = 0;
			foreach( $xalumnocursos -> find( "curso_id = '".$xccurso->id."'" ) as $xalumncur ){
				$alumno = $alumnos -> find_first( "miReg = ".$xalumncur -> registro );
				$xccurso = $xccursos -> find_first( "clavecurso = '".$cursoID."'" );
				if( $alumno -> enPlan == "PE00" ){
					$mat = $materia -> find_first( "clave = '".$xccurso -> materia2000."'" );
				}
				else{
					$mat = $materia -> find_first( "clave = '".$xccurso -> materia2007."'" );
				}
				$carrera = $especialidades -> find_first( "idtiEsp = ".$alumno -> idtiEsp );
				
				$this -> registroAlumno[$i]		= $xalumncur -> registro;
				$this -> nombreAlumno[$i]		= $alumno -> vcNomAlu;
				$this -> planAlumno[$i]			= $alumno -> enPlan;
				$this -> materiaAlumno[$i]		= $mat -> nombre;
				$this -> carreraAlumno[$i]		= $carrera -> vcNomEsp;
				
				$i ++;
			}
			
		} // function listadoDeAlumnos()
		
		function listadoDeAlumnosT(){
			$xalumnocursos	= new Xtalumnocursos();
			$xccursos		= new Xtcursos();
			$materia		= new Materia();
			$alumnos		= new Alumnos();
			$especialidades = new Especialidades();
			$Periodos		= new Periodos();
			$Maestros		= new Maestros();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset($this -> registroAlumno);
			unset($this -> nombreAlumno);
			unset($this -> planAlumno);
			unset($this -> materiaAlumno);
			unset($this -> carreraAlumno);
			unset($this -> nomina);
			unset($this -> coordinador);
			unset($this -> division);
			
			$cursoID = $this -> post( "cursoID" );
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $Maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$this -> cursoClave = $cursoID;
			$Xccursos = new Xtcursos();
			$xccurso = $Xccursos -> find_first("clavecurso = '".$cursoID."'");
			
			$i = 0;
			foreach( $xalumnocursos -> find( "curso_id  = '".$xccurso->id."'" ) as $xalumncur ){
				$alumno = $alumnos -> find_first( "miReg = ".$xalumncur -> registro );
				$xccurso = $xccursos -> find_first( "clavecurso = '".$cursoID."'" );
				$mat = $materia -> find_first( "clave = '".$xccurso -> materia."'" );
				$carrera = $especialidades -> find_first( "idtiEsp = ".$alumno -> idtiEsp );
				
				$this -> registroAlumno[$i]		= $xalumncur -> registro;
				$this -> nombreAlumno[$i]		= $alumno -> vcNomAlu;
				$this -> planAlumno[$i]			= $alumno -> enPlan;
				$this -> materiaAlumno[$i]		= $mat -> nombre;
				$this -> carreraAlumno[$i]		= $carrera -> vcNomEsp;
				
				$i ++;
			}
			
		} // function listadoDeAlumnosT()
		
		function mostrarSalones( $i, $j ){
			$this -> valida();
			$this -> set_response("view");
			
			$xsalones = new Xcsalones();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset( $this -> salones );
			
			$k = 0;
			foreach( $xsalones -> find_all_by_sql
					( "Select * from xcsalones
						where periodo = ".$periodo ) as $xsalon ){
				$this -> salones[$k] = $xsalon;
				$k++;
			}
			$this -> i = $i;
			$this -> j = $j;
			echo $this -> render_partial("mostrarsalones");
		} // function mostrarSalones( $i, $j )
		
		function crearCursoEspejo(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			$curso = $this -> post("espejoID");
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			// Primero me traigo el objeto xccurso, y sus relaciones con xchorascursos,
			//esto con el motivo de dar tiempo, que si alguien se inscribe a ese curso,
			//a la hora de verificar en xalumnocurso, y si ya existe alguien, no se borre el curso,
			//porque si no, podría darse el caso en el me estoy traiendo los objetos de xccursos,
			//y xchorascursos para borrarlos, y se inscribe alguien a ese curso, podría haber
			//incoherencia de datos.
			$i = 0;
			$xccurso = $xccursos -> find_first("clavecurso = '".$curso."'");
			if( $xccurso -> clavecurso != $curso )
				$this->redirect('capturarcursos/cursos');
			
			
			$this -> nombreDelCurso = $xccurso -> clavecurso;
			
			foreach( $xchoras -> find("curso_id = '".$xccurso->id."'") as $xchora ){
				$hora[$i] = $xchora;
				$i++;
			}
			unset($this -> materia);
			unset($this -> yaHayAlumnos);
			unset($this -> mensaje);
			unset($this -> cursoo);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> materiaaa);
			unset($this -> salones);
			unset($this -> mat2000);
			unset($this -> mat2007);
			unset($this -> maest);
			unset($this -> materias2000);
			unset($this -> materias2007);
			unset($this -> maestros);
			
			if( $xccurso = $xccursos -> find_first("clavecurso = '".$curso."'") ){
				$this -> cursoo = $xccurso;
				$j = 0;
				
				if( $materia = $materias -> find_first( "clave = '".$xccurso -> materia."' and plan = 2000" ) ){
					$this -> materiaaa = 2000;
					$this -> mat2000 = $materia;
				}
				if( $materia = $materias -> find_first( "clave = '".$xccurso -> materia."' and plan = 2007" ) ){
					$this -> materiaaa = 2007;
					$this -> mat2007 = $materia;
				}
				
				$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
				$this -> maest = $maestro;
				foreach( $xchoras -> find( "curso_id = '"
						.$xccurso -> id."'" ) as $xchora ){
					
					$this -> horas[$xchora -> dia][$xchora -> hora] = $xchora;
					$this -> salon[$xchora -> dia][$xchora -> hora] = $xsalones -> 
							find_first( "id = ".$xchora -> xcsalones_id );
					$j++;
				}
			}
			$i = 0;
			foreach( $materias -> find( "plan = 2000"  ) as $materia ){
				$this -> materias2000[$i] = $materia;
				$i++;
			}
			
			$i = 0 ;
			foreach( $materias -> find( "plan = 2007"  ) as $materia ){
				$this -> materias2007[$i] = $materia;
				$i++;
			}
			
			$i = 0;
			foreach( $maestros -> find_all_by_sql( "Select * from maestros" ) as $maestro ){
				$this -> maestros[$i] = $maestro;
				$i++;
			}
			
			$i = 0;
			foreach( $xsalones -> find_all_by_sql
					( "Select * from xcsalones where periodo = ".$periodo ) as $xsalon ){
				$this -> salones[$i] = $xsalon;
				$i++;
			}
			
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_($periodo);
			
		} // function crearCursoEspejo()
		
		function creandoCursoEspejo(){
			$this -> valida();
			$xccursos		= new Xccursos();
			$xchoras		= new Xchorascursos();
			$xalumnocursos	= new Xalumnocursos();
			$xsalones		= new Xcsalones();
			$materias		= new Materia();
			$maestros		= new Maestros();
			$Periodos		= new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			///Session::set_data("coordinacion", "IIM");
			///Session::set_data("registro", 1737);
			
			unset( $this -> nomina );
			unset( $this -> coordinador );
			unset( $this -> division );
			unset( $this -> exito );
			
			$this -> nomina = Session::get_data("registro");
			$this -> coordinador = $maestros -> find_first( "nomina = ".$this -> nomina );
			$this -> division = Session::get_data("coordinacion");
			
			$profesor		= $this -> post("profesor");
			$materias		= $this -> post("materias");
			// ID del salon
			$salon			= $this -> post("salon");
			$minimo			= $this -> post("minimo");
			$maximo			= $this -> post("maximo");
			$horapresencial	= $this -> post("horapresencial");
			
			$presencial		= $this -> post("presencial");
			// El cupo es el espacio igual al máximo de
			//alumnos que pueden inscribirse.
			$cupo 			= $maximo;
			// La disponibilidad es igual al tamaño máximo de alumnos
			//que pueden inscribirse, ya que al momento de crearse el curso
			//no hay aún alumnos inscritos.
			$disponibilidad	= $maximo;
			// Bandera para saber si se va a dejar apartado este curso,
			//para los alumnos de primer ingreso.
			$primeringreso	= $this -> post("primeringreso");
			$carrera = "TODOS";
			
			unset($this -> error);
			unset($this -> exito);
			unset($this -> clavecursoo);
			$error = "";
			
			$this -> exito = 0;
			$resto = 0;
			if( $error == "" ){
				foreach( $xccursos -> find_all_by_sql( "Select max(id) as maximo 
													   from xccursos" )as $cursos ){
					
					if( $materias != "-1" && $materias != null && $materias != "" ){
						if( $cursos -> maximo < 10 )
							$resto = "000".($cursos -> maximo + 1);
						else if( $cursos -> maximo < 100 )
							$resto = "00".($cursos -> maximo + 1);
						else if( $cursos -> maximo < 1000 )
							$resto = "0".($cursos -> maximo + 1);
						else
							$resto = ($cursos -> maximo + 1);
						
						$xccursos -> id = ($cursos -> maximo + 1);
						$xccursos -> clavecurso = Session::get_data("coordinacion").$resto;
						$this -> clavecursoo = $xccursos -> clavecurso;
						$xccursos -> materia = $materias;
						$xccursos -> nomina = $profesor;
						$xccursos -> cupo = $cupo;
						$xccursos -> disponibilidad = $disponibilidad;
						$xccursos -> minimo = $minimo;
						$xccursos -> maximo = $maximo;
						$xccursos -> activo = $primeringreso;
						$xccursos -> asesoria = $presencial;
						$xccursos -> periodo = $periodo;
						$xccursos -> carrera = $carrera;
						$xccursos -> curso = Session::get_data("coordinacion").$resto;
						$xccursos -> division = Session::get_data("coordinacion");
						$xccursos -> horas1 = '0';
						$xccursos -> avance1 = '0';
						$xccursos -> horas2 = '0';
						$xccursos -> avance2 = '0';
						$xccursos -> horas3 = '0';
						$xccursos -> avance3 = '0';
						
						if( $materia2007 != "-1" ){
							$xccursos -> paralelo = Session::get_data("coordinacion").($resto + 1);
						}
						else{
							$xccursos -> paralelo = "-1";
						}
						$xccursos -> create();
						
						// Lo guardo de está forma, para que en la base de datos
						//se guarden en orden de día y hora, para una
						//manipulación de datos más sencilla.
						for( $j = 1; $j < 7; $j++ ){ // Dias
							for( $i = 7; $i < 22; $i++ ){ // Horas
								if( $salon[$i][$j] > 0 ){
									$xchoras -> curso_id = $resto;
									$xchoras -> xcsalones_id = $salon[$i][$j];
									$xchoras -> dia = $j;
									$xchoras -> hora = $i;
									$xchoras -> bloque = '0';
									$xchoras -> periodo = $periodo;
									if( isset($horapresencial[$i][$j]) && 
											($horapresencial[$i][$j] == "on" || 
												$horapresencial[$i][$j] == "ON") ){
										$xchoras -> presencial = '0';
									}
									else{
										$xchoras -> presencial = 1;
									}
									$xchoras -> create();
								}
							}
						}
						$this -> exito = 1;
					} // if( $materias != "-1" && $materias != null && $materias != "" )
				} // foreach( $xccursos -> find_all_by_sql( "Select max(id) as maximo from xccursos" )as $cursos )
			} // if( $error == "" )
			else{
				$this -> exito = 0;
			}
			$this -> error = $error;
		} // function creandoCursoEspejo()
		
		function masCupoEnCursosCyT(){
			$this -> valida();
			
			$xccursos = new Xccursos();
			$xtcursos = new Xtcursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			// Colomos
			unset( $this -> curso );
			unset( $this -> materia );
			unset( $this -> matnombre );
			unset( $this -> nomina );
			unset( $this -> maestro );
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			
			$this->xccursos = array();
			foreach( $xccursos -> find_all_by_sql
					("select xcc.curso, xcc.materia, m.nombre as matnombre, xcc.nomina, ma.nombre nombre_profesor,
						xcc.cupo, xcc.disponibilidad, xcc.id curso_id
						from xccursos xcc, materia m, maestros ma
						where xcc.periodo = ".$periodo."
						and xcc.materia = m.clave
						and xcc.nomina = ma.nomina
						group by xcc.clavecurso;
						") as $xccur ){
				array_push($this->xccursos, $xccur);
			}
			// Fin Colomos
			
			// Tonala
			unset( $this -> tcurso );
			unset( $this -> tmateria );
			unset( $this -> tmatnombre );
			unset( $this -> tnomina );
			unset( $this -> tmaestro );
			unset( $this -> tcupo );
			unset( $this -> tdisponibilidad );
			
			$this->xtcursos = array();
			foreach( $xccursos -> find_all_by_sql
						("select xcc.curso, xcc.materia, m.nombre as matnombre, xcc.nomina, ma.nombre nombre_profesor,
							xcc.cupo, xcc.disponibilidad, xcc.id curso_id
							from xtcursos xcc, materia m, maestros ma
							where xcc.periodo = ".$periodo."
							and xcc.materia = m.clave
							and xcc.nomina = ma.nomina
							group by xcc.clavecurso") as $xccur ){
				array_push($this->xtcursos, $xccur);
			}
			// Fin Tonala
		} // function masCupoEnCursosCyT()
		
		function masCupoEnCursosCyTExcel(){
			$this -> valida();
			
			header('Content-type: application/vnd.ms-excel');
			header("Content-Disposition: attachment; filename=archivo.xls");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			$xccursos = new Xccursos();
			$xtcursos = new Xtcursos();
			$xchorascursos = new Xchorascursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			// Colomos
			unset( $this -> curso );
			unset( $this -> paralelo );
			unset( $this -> materia );
			unset( $this -> matnombre );
			unset( $this -> nomina );
			unset( $this -> maestro );
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			
			$i = 0;
			foreach( $xccursos -> find_all_by_sql
						("select xcc.curso, xcc.paralelo, xcc.materia, m.nombre as matnombre, xcc.nomina, ma.nombre,
							xcc.cupo, xcc.disponibilidad
							from xccursos xcc, materia m, maestros ma
							where xcc.periodo = ".$periodo."
							and xcc.materia = m.clave
							and xcc.nomina = ma.nomina
							group by xcc.clavecurso;
							") as $xccur ){
				$this -> curso[$i] = $xccur -> curso;
				$this -> paralelo[$i] = $xccur -> paralelo;
				$this -> materia[$i] = $xccur -> materia;
				$this -> matnombre[$i] = $xccur -> matnombre;
				$this -> nomina[$i] = $xccur -> nomina;
				$this -> maestro[$i] = $xccur -> nombre;
				$this -> cupo[$i] = $xccur -> cupo;
				$this -> disponibilidad[$i] = $xccur -> disponibilidad;
				$i++;
			}
			// Fin Colomos
			
			// Tonala
			unset( $this -> tcurso );
			unset( $this -> tparalelo );
			unset( $this -> tmateria );
			unset( $this -> tmatnombre );
			unset( $this -> tnomina );
			unset( $this -> tmaestro );
			unset( $this -> tcupo );
			unset( $this -> tdisponibilidad );
			
			$i = 0;
			foreach( $xccursos -> find_all_by_sql
						(" Select xcc.curso, xcc.materia, m.nombre as matnombre, 
							xcc.nomina, ma.nombre, xcc.cupo, xcc.disponibilidad
							from xtcursos xcc, materia m, maestros ma
							where xcc.periodo = ".$periodo."
							and xcc.materia = m.clave
							and xcc.nomina = ma.nomina
							group by xcc.clavecurso ") as $xccur ){
				$this -> tcurso[$i] = $xccur -> curso;
				$this -> tmateria[$i] = $xccur -> materia;
				$this -> tmatnombre[$i] = $xccur -> matnombre;
				$this -> tnomina[$i] = $xccur -> nomina;
				$this -> tmaestro[$i] = $xccur -> nombre;
				$this -> tcupo[$i] = $xccur -> cupo;
				$this -> tdisponibilidad[$i] = $xccur -> disponibilidad;
				$i++;
			}
			
			echo
			"<table cellspacing='1' cellpadding='1' style='font-size: 8px;' border='1' align='center' width='80%'>
				<tr>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Clave Curso</h3>
					</th>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Clave Materia</h3>
					</th>
					<th style='background:#FF7F27' width='250' align='center'>
						<h3>Materia</h3>
					</th>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Nomina Profesor</h3>
					</th>
					<th style='background:#FF7F27' width='250' align='center'>
						<h3>Nombre Profesor</h3>
					</th>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Num AlumnosInscritos</h3>
					</th>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Disponibilidad en el Curso</h3>
					</th>
					<th style='background:#FF7F27' width='150' align='center'>
						<h3>Curso Espejo</h3>
					</th>
				</tr>";
			$i = 0;
			foreach( $this -> curso as $cur ) {
				echo "
					<tr>
						<td width='100' align='center'>".$cur."</td>
						<td width='100' align='center'>".$this -> materia[$i]."</td>
						<td width='100' align='center'>".$this -> matnombre[$i]."</td>
						<td width='100' align='center'>".$this -> nomina[$i]."</td>
						<td width='100' align='center'>".$this -> maestro[$i]."</td>
						<td width='100' align='center'>".$this -> cupo[$i]."</td>
						<td width='100' align='center'>".$this -> disponibilidad[$i]."</td>
						<td width='100' align='center'>";
						if( $this -> paralelo[$i] != -1 )
							echo $this -> paralelo[$i];
						echo "</td>
					</tr>";
				$i++;
			}
			echo "</table>";
			die;
			// Fin Tonala
		} // function masCupoEnCursosCyTExcel()
		
		function aumentar_cupo_en_xccursos(){
			
			$this -> valida();
			if(	!$this->has_post("curso_id_plus") ){
				redirect("seguridad/terminarsesion");
			}
			$curso_id = $this->post("curso_id_plus");
			
			$this -> set_response("view");
			
			$xccursos = new Xccursos();
			$xtcursos = new Xtcursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			unset( $this -> exito );
			unset( $this -> clavecurso );
			
			$i = 0;
			$this -> exito = 0;
			foreach( $xccursos -> find
						(" id ='".$curso_id."'" ) as $xccur ){
				$cupo = $xccur -> cupo = $xccur -> cupo + 1;
				$disponibilidad = $xccur -> disponibilidad = $xccur -> disponibilidad + 1;
				$xccur -> save();
			}
			echo $cupo." / ".$disponibilidad;
		} // function aumentar_cupo_en_xccursos()
		
		function disminuir_cupo_en_xccursos(){
			
			$this -> valida();
			if(	!$this->has_post("curso_id_less") ){
				redirect("seguridad/terminarsesion");
			}
			$curso_id = $this->post("curso_id_less");
			
			$this -> set_response("view");
			
			$xccursos = new Xccursos();
			$Xalumnocursos = new Xalumnocursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			unset( $this -> exito );
			unset( $this -> clavecurso );
			
			$i = 0;
			$this -> exito = 0;
			$noentro=0;
			foreach( $xccursos -> find
						(" id ='".$curso_id."'" ) as $xccur ){
				foreach ( $Xalumnocursos -> find_all_by_sql ( "
							Select count(registro) counter, curso_id from xalumnocursos
							where periodo = ".$periodo."
							and curso_id = '".$curso_id."'
							group by curso_id" ) as $xalumncur ){
					
					$disponibilidad = $xccur -> disponibilidad;
					if(($xccur -> cupo-$xalumncur->counter) > 0){
						$cupo = $xccur -> cupo = $xccur -> cupo - 1;
						$disponibilidad = $xccur -> disponibilidad = ($xccur -> cupo-$xalumncur->counter);
					}
					
					if(($xccur -> cupo-$xalumncur->counter) <= 0){
						$disponibilidad = $xccur -> disponibilidad = '0';
						$cupo = $xccur -> cupo = $xccur -> cupo;
					}
					$xccur -> save();
					$noentro++;
				}
				if($noentro==0){
					$cupo = $xccur -> cupo = $xccur -> cupo - 1;
					$disponibilidad = $xccur->disponibilidad = ($xccur -> cupo);
					$xccur -> save();
				}
			}
			echo $cupo." / ".$disponibilidad;
		} // function disminuir_cupo_en_xccursos()
		
		function aumentar_cupo_en_xtcursos(){
			
			$this -> valida();
			if(	!$this->has_post("curso_id_plus") ){
				redirect("seguridad/terminarsesion");
			}
			$curso_id = $this->post("curso_id_plus");
			
			$this -> set_response("view");
			
			$xccursos = new Xtcursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			unset( $this -> exito );
			unset( $this -> clavecurso );
			
			$i = 0;
			$this -> exito = 0;
			foreach( $xccursos -> find
						(" id ='".$curso_id."'" ) as $xccur ){
				$cupo = $xccur -> cupo = $xccur -> cupo + 1;
				$disponibilidad = $xccur -> disponibilidad = $xccur -> disponibilidad + 1;
				$xccur -> save();
			}
			echo $cupo." / ".$disponibilidad;
		} // function aumentar_cupo_en_xtcursos()
		
		function disminuir_cupo_en_xtcursos(){
			
			$this -> valida();
			if(	!$this->has_post("curso_id_less") ){
				redirect("seguridad/terminarsesion");
			}
			$curso_id = $this->post("curso_id_less");
			
			$this -> set_response("view");
			
			$xccursos = new Xtcursos();
			$Xalumnocursos = new Xtalumnocursos();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_proximo();
			
			unset( $this -> cupo );
			unset( $this -> disponibilidad );
			unset( $this -> exito );
			unset( $this -> clavecurso );
			
			$i = 0;
			$this -> exito = 0;
			$noentro=0;
			foreach( $xccursos -> find
						(" id ='".$curso_id."'" ) as $xccur ){
				foreach ( $Xalumnocursos -> find_all_by_sql ( "
							Select count(registro) counter, curso_id from xtalumnocursos
							where periodo = ".$periodo."
							and curso_id = '".$curso_id."'
							group by curso_id" ) as $xalumncur ){
					
					$disponibilidad = $xccur -> disponibilidad;
					if(($xccur -> cupo-$xalumncur->counter) > 0){
						$cupo = $xccur -> cupo = $xccur -> cupo - 1;
						$disponibilidad = $xccur -> disponibilidad = ($xccur -> cupo-$xalumncur->counter);
					}
					
					if(($xccur -> cupo-$xalumncur->counter) <= 0){
						$disponibilidad = $xccur -> disponibilidad = '0';
						$cupo = $xccur -> cupo = $xccur -> cupo;
					}
					$xccur -> save();
					$noentro++;
				}
				if($noentro==0){
					$cupo = $xccur -> cupo = $xccur -> cupo - 1;
					$disponibilidad = $xccur->disponibilidad = ($xccur -> cupo);
					$xccur -> save();
				}
			}
			echo $cupo." / ".$disponibilidad;
		} // function disminuir_cupo_en_xtcursos()
		
		
		function valida(){
			if(Session::get_data('coordinador')!="OK")
				$this->redirect('general/inicio');
		} // function valida()
		
		function valida_coordinador_ing_calculo(){
			if(Session::get_data('coordinador')!="OK" && Session::get_data('tipousuario')!="INGCALCULO")
				$this->redirect('general/inicio');
		} // function valida_coordinador_ing_calculo()
		
		function cursos_intersemestrales_unset_vars(){
			unset($this -> periodoCompleto);
			unset($this -> nomina);
			unset($this -> division);
			unset($this -> coordinador);
			unset($this -> maestros);
			unset($this -> carreras);
			unset($this -> materias);
			unset($this -> exito);
			unset($this -> clavecurso);
			unset($this -> alumnos);
			unset($this -> alumnosinscritos);
			unset($this -> clave_materia);
			unset($this -> nombre_materia);
			unset($this -> mensajes);
			unset($this -> cursos);
			unset($this -> mensaje);
			unset($this -> curso);
			unset($this -> exito_en_borrar);
		} //function cursos_intersemestrales_unset_vars()
		
		function cursos_intersemestrales_checar_materias_iguales($materia){
			// $this -> validar_si_es_coordinador();
			//$this -> set_response("view");
			//$materia = $this -> post("materias");
			unset($this -> mensaje);
			
			$IntersemestralCursos = new IntersemestralCursos();
			$materias_iguales = $IntersemestralCursos -> get_materias_iguales($materia);
			
			if( count($materias_iguales) > 0 ){
				if( count($materias_iguales) > 1 )
					$this -> mensaje = "Se encontraron los siguientes cursos con la misma clave de materia<br /><br />";
				else
					$this -> mensaje = "Se encontro el siguiente curso<br /><br />";
				foreach( $materias_iguales as $m ){
					$this -> mensaje .= "ClaveCurso: ".$m -> clavecurso.
						" Espacios Disponibles: ".$m -> disponibilidad.
						" Alumnos Inscritos: ".($m -> inscritos)."<br />";
				}
				$this -> mensaje .= "<br />&iquest;Desea proseguir con la creacion del curso?";
				return 1;
			}
			return 0;
		} // function cursos_intersemestrales_checar_materias_iguales()
		
		function cursos_intersemestrales(){
			// $this -> validar_si_es_coordinador();
			$this -> cursos_intersemestrales_unset_vars();
			
			$Periodos = new Periodos();
			$Maestros = new Maestros();
			
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_a_intersemestral_letra($Periodos->get_periodo_actual());
			$this -> get_info_from_coordinador();
			
			// Obtener informacion con la que llenara el curso el coordinador...
			$this -> maestros = $Maestros -> get_todos_los_maestros();
		} // function cursos_intersemestrales()
		
		function get_carreras_cursos_intersemestrales($plan_estudios){
			// $this -> validar_si_es_coordinador();
			$this -> set_response("view");
			
			$this -> cursos_intersemestrales_unset_vars();
			
			$division = Session::get_data("coordinacion");
			$Carrera = new Carrera();
			//if( $division == "TCB" )
				$this -> carreras = $Carrera -> get_todas_las_carreras_segun_plan_de_estudios($plan_estudios);
			//else
				//$this -> carreras = $Carrera -> get_carreras_by_division_and_plan_estudios($division, $plan_estudios);
			
			$this -> render_partial("cursos_intersemestrales_carreras");
		} // function get_carreras_cursos_intersemestrales()()
		
		function get_materias_cursos_intersemestrales($carrera_id){
			// $this -> validar_si_es_coordinador();
			$this -> set_response("view");
			
			$this -> cursos_intersemestrales_unset_vars();
			
			$division = Session::get_data("coordinacion");
			$Materia = new Materia();
			
			if( $division == "TCB" )
				$this -> materias = $Materia -> get_all_giving_careerID_($carrera_id);
			else
				$this -> materias = $Materia -> get_all_giving_careerID_no_basicas($carrera_id);
			
			$this -> render_partial("cursos_intersemestrales_materias");
		} // function get_materias_cursos_intersemestrales()
		
		function cursos_intersemestrales_creando(){
			//$this -> validar_si_es_coordinador();
			
			$materia = $this -> post("materias");
			$carrera = $this -> post("carreras");
			//$plan_de_estudios = $this -> post("plan_de_estudios");
			$profesor = $this -> post("profesor");
			
			$crearcurso = $this -> post("crearcurso");
			
			$this -> cursos_intersemestrales_unset_vars();
			
			$Periodos = new Periodos();
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_a_intersemestral_letra($Periodos->get_periodo_actual());
			$this -> get_info_from_coordinador();
			
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			
			$division = Session::get_data('coordinacion');
			
			// Validar si existe alguna otra materia creada para este periodo
			if( $crearcurso != 1 && $this -> cursos_intersemestrales_checar_materias_iguales($materia) == 1 ){
				unset($this -> materia);unset($this -> carrera);unset($this -> profesor);
				$this -> materia = $materia;
				$this -> carrera = $carrera;
				$this -> profesor = $profesor;
				$this -> render('cursos_intersemestrales_checar_materias_iguales');
				return;
			}
			$IntersemestralCursos = new IntersemestralCursos();
			$maxID = $IntersemestralCursos -> get_max_ID()+1;
			if( $maxID  ){
				if( $maxID < 10 )
					$maxID = '000'.$maxID;
				else if( $maxID < 100 )
					$maxID = '00'.$maxID;
				else if( $maxID < 1000 )
					$maxID = '0'.$maxID;
			}
			$this -> clavecurso = "I-".$division."".$maxID;
			
			$IntersemestralCursos -> clavecurso = $this -> clavecurso;
			$IntersemestralCursos -> clavemat = $materia;
			$IntersemestralCursos -> nomina = $profesor;
			$IntersemestralCursos -> cupo = 50;
			$IntersemestralCursos -> disponibilidad = 50;
			$IntersemestralCursos -> minimo = 18;
			$IntersemestralCursos -> activo = 1;
			$IntersemestralCursos -> periodo = $periodo;
			$IntersemestralCursos -> division = $division;
			$IntersemestralCursos -> creado_at = $Periodos -> get_datetime();
			
			$this -> get_info_from_coordinador();
			
			if( $IntersemestralCursos -> create() ){
				$this -> exito = 1;
			}
			else{
				$this -> exito = 0;
				Flash::error("Ocurrio un error!, favor de contactar al administrador del sistema.");
				return;
			}
			
			$this -> clave_materia = $materia;
			$Materia = new Materia();
			$mat = $Materia -> find_first("clave = '".$materia."'");
			$this -> nombre_materia = $mat -> nombre;
			$this -> render("cursos_intersemestrales_creando_");
		} // function cursos_intersemestrales_creando()
		
		function cursos_intersemestrales_inscribir_alumno(){
			// Quitar esta linea, cuando se quiera inscribir a los alumnos a los cursos intersemestrales.
			//$this->redirect('capturarcursos/cursos_intersemestrales_listado');
			
			// $this -> validar_si_es_coordinador();
			//$this -> set_response("view");
			
			// Si intenta acceder directo a la URL, no existirá la variable de clavecurso
			//por lo que se redireccionará a cursos_intersemestrales_listado
			$clavecurso = $this -> post("clavecurso");
			
			if( $clavecurso == "" ){
				$this -> redirect("capturarcursos/cursos_intersemestrales_listado");
				return;
			}
			$this -> cursos_intersemestrales_unset_vars();
			
			$this -> clavecurso = $clavecurso;
			
			$Periodos = new Periodos();
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_a_intersemestral_letra($Periodos->get_periodo_actual());
			$this -> get_info_from_coordinador();
			
			$registro = $this -> post("alumnos");
			$tipo_ex = $this -> post("tipo_ex");
			
			//Traer listado de alumnos para poderlos inscribir
			//$Alumnos = new Alumnos();
			//$this -> alumnos = $Alumnos -> get_info_from_student_for_intersemetral();
			// Traer listado de alumnos inscritos a dicho curso...
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$this -> alumnosinscritos = $IntersemestralAlumnos -> get_all_students_by_clavecurso($clavecurso);
			
			$IntersemestralCursos = new IntersemestralCursos();
			$interCursos = $IntersemestralCursos -> find_first("clavecurso = '".$clavecurso."'");
			$this -> clave_materia = $interCursos -> clavemat;
			$Materia = new Materia();
			$mat = $Materia -> find_first("clave = '".$interCursos -> clavemat."'");
			$this -> nombre_materia = $mat -> nombre;
			
			// Si entra al if de abajo, significa que el coordinador está intentando inscribir a algún alumno.
			if( ($registro != "" && $clavecurso != "" && $tipo_ex != "") ){
				// Validar que sea un registro valido
				$Alumnos = new Alumnos();
				if( !$Alumnos -> find_first("miReg = '".$registro."'") ){
					$this -> mensajes ="<p class='letraRoja'>El registro que ingreso no existe, intente de nuevo.</p>";
					$this -> render('cursos_intersemestrales_inscribir_alumno');
					return;
				}
				$IntersemestralAlumnos = new IntersemestralAlumnos();
				
				if($IntersemestralAlumnos -> find_first("registro = ".$registro." and clavecurso = '".$clavecurso."'")){
					//echo "NO"."#"."No se puede inscribir a un alumno en el mismo curso";
					$this -> mensajes = "<p class='letraRoja'>No se puede inscribir a un alumno en el mismo curso</p>";
					$this -> render('cursos_intersemestrales_inscribir_alumno');
					return;
					//$this -> render_partial("cursos_intersemestrales_mensajes");
					//return;
				}
				
				$validacion_basica = array();
				$validacion_basica = $this -> cursos_intersemestrales_validaciones_basicas_para_inscribir_a_intersemestrales($registro, $interCursos);
				if( $this -> cursos_intersemestrales_procesar_validacion_basica($validacion_basica) == 99 ){
					$this -> render('cursos_intersemestrales_inscribir_alumno');
					return;
				}
				
				switch($tipo_ex){
					// Hacer las validaciones específicas para un curso de Nivelación
					case "NIV":
						$validacion_nivelacion = $this -> cursos_intersemestrales_validar_nivelacion($registro, $interCursos -> clavemat);
						if( ($validacion_nivelacion) == 2 ){
							$this -> mensajes = "<p class='letraRoja'>No se puede inscribir al alumno en el curso de nivelaci&oacute;n, ya que<br />".
							" no cursaba la materia en el semestre inmedia anterior.</p>";
							$this -> render('cursos_intersemestrales_inscribir_alumno');
							return;
						}
					break;
					// Hacer las validaciones específicas para un curso de Acreditación
					case "ACR": 
						$validacion_acreditacion = $this -> cursos_intersemestrales_validar_acreditacion($registro, $interCursos -> clavemat);
						if( $this -> cursos_intersemestrales_procesar_validacion_acreditacion($validacion_acreditacion) == 99 ){
							$this -> render('cursos_intersemestrales_inscribir_alumno');
							return;
						}
					break;
				}
				
				$periodo_actual_intersemestral = $Periodos -> get_periodo_actual_intersemestral();
				$creado_at = $Periodos -> get_datetime();
				
				$IntersemestralAlumnos -> periodo = $periodo_actual_intersemestral;
				$IntersemestralAlumnos -> registro = $registro;
				$IntersemestralAlumnos -> clavecurso = $clavecurso;
				$IntersemestralAlumnos -> faltas = '0';
				$IntersemestralAlumnos -> calificacion = '300';
				$IntersemestralAlumnos -> tipo_ex = $tipo_ex;
				$IntersemestralAlumnos -> activo = 1;
				$IntersemestralAlumnos -> creado_at = $creado_at;
				$IntersemestralAlumnos -> creado_by = Session::get_data('registro');
				$IntersemestralAlumnos -> modificado_at = $Periodos -> get_datetime_cero();
				$IntersemestralAlumnos -> modificado_by = "-";
				
				$IntersemestralAlumnos -> create();
				
				// this -> error = "ALUMNO INSCRITO";
				
				$Alumnos = new Alumnos();
				$alumno = $Alumnos -> get_relevant_info_from_student($registro);
				$carrera = $Alumnos -> get_careername_from_student($alumno -> carrera_id, $alumno -> areadeformacion_id);
				
				$IntersemestralCursos = new IntersemestralCursos();
				$datosProfe = $IntersemestralCursos -> get_nomina_nombre_de_coordinador(Session::get_data("registro"));
				$IntersemestralCursos -> restar_disponibilidad($clavecurso);
				
				$inter_al_id = $IntersemestralAlumnos -> get_max_id();
				
				$nuevoInscrito -> id = ($inter_al_id);
				$nuevoInscrito -> inter_al_id = ($inter_al_id);
				$nuevoInscrito -> miReg = $registro;
				$nuevoInscrito -> vcNomAlu = $alumno -> vcNomAlu;
				$nuevoInscrito -> carrera_nombre = $carrera -> nombre;
				$nuevoInscrito -> enPlan = $alumno -> enPlan;
				$nuevoInscrito -> tipo_ex = $tipo_ex;
				$nuevoInscrito -> creado_at = $creado_at;
				$nuevoInscrito -> nomina = $datosProfe -> nomina;
				$nuevoInscrito -> maestro_nombre = $datosProfe -> nombre;
				array_push($this -> alumnosinscritos, $nuevoInscrito);
				
				$this -> mensajes = "<p class='letraAzul'>Alumno Inscrito Correctamente</p>";
			}
		} // function cursos_intersemestrales_inscribir_alumno()
		
		function get_info_from_coordinador(){
			$Maestros = new Maestros();
			
			$this -> nomina = Session::get_data("registro");
			$this -> division = Session::get_data("coordinacion");
			$this -> coordinador -> nombre = $Maestros -> get_nombre_maestro($this -> nomina);
			if( $this -> division == "" || $this -> coordinador -> nombre == "" || $this -> nomina == "" ){
				$this -> redirect( "seguridad/terminarsesion" );
			}
		} // function get_info_from_coordinador()
		
		function validar_si_es_coordinador(){
			$Coordinadores = new Coordinadores();
			if( $Coordinadores -> validar_coordinacion() ){
				$this->redirect('seguridad/terminarsesion');
			}
		} // function validar_si_es_coordinador()
		
		function cursos_intersemestrales_validaciones_basicas_para_inscribir_a_intersemestrales($registro, $interCursos){
			//$this -> validar_si_es_coordinador();
			$Alumnos = new Alumnos();
			$plantel = $Alumnos -> getXalORXtal($registro);
			$alumno = $Alumnos -> get_relevant_info_from_student($registro);
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			// Asignar variable de clave de materia.
			$clavemat = $interCursos -> clavemat;
			
			// Situaciones en el campo situacion de xalumnocursos o xtalumnocursos
			// -
			// BAJA DEFINITIVA
			// EXTRAORDINARIO
			// EXTRAORDINARIO FALTAS
			// ORDINARIO
			// REGULARIZACION
			// REGULARIZACION DIRECTA
			// TITULO DE SUFICIENCIA
			// TITULO FALTAS
			
			$status_id = array();
			
			// Checar cuantos alumnos hay inscritos en el curso actual, para que no deje inscribir más de 50
			$IntersemestralCursos = new IntersemestralCursos();
			if( !$IntersemestralCursos -> get_numero_alumnos_inscritos_a_un_curso($interCursos -> clavecurso) ){ // Si regresa false, no hay cupo
				array_push($status_id, 11);
				return $status_id; // 11: No hay cupo disponible en el curso.
			}
			// Checar el stSit del alumno.
			if( $alumno -> stSit != "OK" ){
				array_push($status_id, 10);
				return $status_id; // 10: El alumno ya está dado de baja, se checa el $alumno -> stSit
			}
			// Validar si no la tiene ya en kardex_ing
			$KardexIng = new KardexIng();
			if( $KardexIng -> find_first("registro = ".$registro." and clavemat = '".$clavemat."'") ){
				array_push($status_id, 6);
				return $status_id; // 6: El alumno ya tiene la materia aprobada en su kardex
			}
			// Validar si la materia es de su plan de estudios
			$Materia = new Materia();
			if( !$Materia -> find_first("carrera_id = ".$alumno -> carrera_id." and 
					(serie = '".$alumno -> areadeformacion_id."' or serie = '-') and clave = '".$clavemat."'") ){
				array_push($status_id, 7);
				return $status_id; // 7: La materia no es del plan de estudios del alumno
			}
			
			$Xalumnocursos = new Xalumnocursos();
			foreach( $Xalumnocursos -> find_all_by_sql("
					select calificacion, faltas, situacion
					from x".$plantel[0]."alumnocursos xal
					where xal.periodo = ".$periodo."
					and xal.registro = ".$registro) as $xal ){
				// Si el alumno tiene baja definitiva en cualquiera de sus materias. No podrá crearse curso
				//intersemestral a menos de que ya se haya aceptado y este capturada en el sistema
				//su carta de reconsideración...
				
				switch( $xal -> situacion ){
					case "BAJA DEFINITIVA":
						// Revisar si ya tiene su carta de reconsideración aceptada
						$ReconsideracionBaja = new ReconsideracionBaja();
						if( !$ReconsideracionBaja -> find_first("periodo = ".$periodo.
								" and registro = ".$registro." and procede = 1") ){
							array_push($status_id, 2);
							return $status_id; // 2: Significa que está dado de baja por BAJA DEFINITIVA...
						}
					//case: "BAJA DEFINITIVA"
				}
				// Prosigue con las siguientes validaciones en caso de no entrar al if...
			}
			
			// Validar si cumple con sus prerrequisitos.
			$Materia = new Materia();
			$materias_prereq = $Materia -> get_prerrequisitos_de_una_materia($clavemat, $alumno -> carrera_id, $alumno -> areadeformacion_id);
			$KardexIng = new KardexIng();
			$kardexIng = $KardexIng -> get_all_kardex_from_student_only_clave($registro);
			unset($MateriasFaltantes);
			foreach( $materias_prereq as $materia_prereq ){
				if( !in_array($materia_prereq -> clavemat, $kardexIng) ){
					$infoMateria = $Materia -> get_nombre_materia($materia_prereq -> clavemat, $alumno);
					$MateriasFaltantes .= $infoMateria -> clave." - ".$infoMateria -> nombre."<br />";
				}
			}
			if( count($MateriasFaltantes) > 1 ){
				array_push($status_id, 8);
				array_push($status_id, $MateriasFaltantes);
				return $status_id; // 8: No cumple con al menos un prerrequisito y se le mostrará
			}
			
			// Revisar en que planteles ha estado.
			$SiHaEstadoEnColomos = $Alumnos -> get_si_al_menos_ha_estado_alguna_vez_en_colomos($registro);
			$SiHaEstadoEnTonala = $Alumnos -> get_si_al_menos_ha_estado_alguna_vez_en_tonala($registro);
			
			// 13: El alumno tiene en Regularización, o Titulo dicha materia.
			$Xalumnocursos = new Xalumnocursos();
			if( $infoRegularizacion_Titulo = $Xalumnocursos -> get_if_materia_es_regularizacion_o_titulo($alumno, $clavemat) ){
				foreach( $infoRegularizacion_Titulo as $info ){
					$matRegularizacion .= $info -> situacion." - ".$info -> clavecurso." - ".$info -> materia." - ".
									$info -> nombre_materia." - ".$info -> calificacion."<br />";
				}
				array_push($status_id, 13);
				array_push($status_id, $matRegularizacion);
				return $status_id; // 13: El alumno tiene en Regularización, o Titulo dicha materia.
			}
			
			// Validar si no tiene alguna materia como Baja Definitiva o Titulo de Suficiencia Reprobado.
			unset($MatConBajaTitulo);
			$Xalumnocursos = new Xalumnocursos();
			
			if( $SiHaEstadoEnColomos &&	$SiHaEstadoEnTonala ){
				$MateriasConBajaOTitulo = Array();
				$MateriasConBajaOTituloColomos = $Xalumnocursos -> get_materias_con_baja_definitiva_o_titulo_colomos($registro);
				$MateriasConBajaOTituloTonala = $Xalumnocursos -> get_materias_con_baja_definitiva_o_titulo_tonala($registro);
				
				if(isset($MateriasConBajaOTituloColomos))
					array_push($MateriasConBajaOTitulo, $MateriasConBajaOTituloColomos);
				if(isset($MateriasConBajaOTituloTonala))
					array_push($MateriasConBajaOTitulo, $MateriasConBajaOTituloTonala);
			}
			else if( $SiHaEstadoEnColomos )
				$MateriasConBajaOTitulo = $Xalumnocursos -> get_materias_con_baja_definitiva_o_titulo_colomos($registro);
			else if( $SiHaEstadoEnTonala )
				$MateriasConBajaOTitulo = $Xalumnocursos -> get_materias_con_baja_definitiva_o_titulo_tonala($registro);
			if( $SiHaEstadoEnColomos || $SiHaEstadoEnTonala ){
				unset($MatConBajaTitulo);
				foreach( $MateriasConBajaOTitulo as $MBajaTitulo ){
					$MatConBajaTitulo .= $MBajaTitulo -> clavecurso." - ".$MBajaTitulo -> materia." - ".
									$MBajaTitulo -> nombre_materia." - ".$MBajaTitulo -> calificacion." - ".
									$MBajaTitulo -> situacion."<br />";
				}
				if( isset($MatConBajaTitulo) ){
					array_push($status_id, 9);
					array_push($status_id, $MatConBajaTitulo);
					return $status_id; // 9: Tiene una materia como Baja Definitiva, o tiene un titulo reprobado.
				}
			}
			
			// Revisar si tiene el 60% de reprobadas como Regularizacion Directa
			$Xalumnocursos = new Xalumnocursos();
			$MateriasRegularizacionDirecta = $Xalumnocursos -> get_materias_con_regularizacion_directa($alumno);
			if($MateriasRegularizacionDirecta[0]-> noPresentaCurso){ // Si es True, no podrá cursar
				// Si es true, revisar el sig elemento para ver el nombre de materia, y en que periodo la curso
				unset ($matRegularizacionDirecta);
				foreach( $MateriasRegularizacionDirecta as $materiaReg ){
					$matRegularizacionDirecta .= $materiaReg -> clavecurso." - ".$materiaReg -> materia." - ".
									$materiaReg -> nombre_materia." - ".$materiaReg -> calificacion." - ".
									$materiaReg -> situacion."<br />";
				}
				array_push($status_id, 14);
				array_push($status_id, $matRegularizacionDirecta);
				return $status_id; // 14: Si tiene el 60% de sus materias como regularización directa, no podrá cursar ningun curso.
			}
			
			// Obtener información de los extraordinarios anteriores...
			$Xextraordinarios = new Xextraordinarios();
			foreach( $Xalumnocursos -> find_all_by_sql("
					select xext.calificacion, xext.estado, xext.tipo, xext.curso_id
					from x".$plantel[0]."alumnocursos xal
					inner join x".$plantel[0]."extraordinarios xext
					on xal.curso_id = xext.curso_id
					and xal.periodo = ".$periodo."
					and xal.registro = ".$registro."
					and xext.registro = xal.registro
					and xext.tipo = 'TT'" ) as $xext ){
				// Si tiene algun extraordinario como Titulo, y la tiene reprobada
				//se mandara a baja... Por lo que no puede presentar ninguna materia como intersemestral
				if( $xext -> calificacion < 70 ){
					// Revisar si ya tiene su carta de reconsideración aceptada
					$ReconsideracionBaja = new ReconsideracionBaja();
					if( !$ReconsideracionBaja -> find_first("periodo = ".$periodo.
							" and registro = ".$registro." and procede = 1") ){
						array_push($status_id, 3);
						return $status_id; // 3: Esta dado de baja por reprobar AL MENOS UN TITULO
					}
					$Xccursos = new Xccursos();
					foreach( $Xccursos -> find_all_by_sql("
							select mat.clave, mat.nombre, xcc.periodo
							from xccursos xcc
							inner join materia mat
							on xcc.materia = mat.clave
							inner join alumnos al
							on al.carrera_id = mat.carrera_id
							and xcc.curso_id = '".$xext -> curso_id."'
							and al.miReg = ".$registro) as $xcc ){
						if( $xext -> calificacion == 300 ){
							array_push($status_id, 4);
							array_push($status_id, $xcc -> clave." ".$xcc -> nombre." ".$Periodos -> convertirPeriodo_($periodo));
							return $status_id; // 4: No tiene calificación el alumno.
						}
						if( $xext -> calificacion == 999 ){
							array_push($status_id, 5);
							array_push($status_id, $xcc -> clave." ".$xcc -> nombre." ".$Periodos -> convertirPeriodo_($periodo));
							return $status_id; // 5: No presento su Titulo de Suficiencia.
						}
					}
				}
				// Prosigue con las siguientes validaciones en caso de no entrar al if...
			}
			
			/*
			// Si la materia la curso en periodos anteriores, dígase 2 periodos atras, no podrá cursarse.
			$IntersemestralCursos = new IntersemestralCursos();
			$info = $IntersemestralCursos -> get_materia_de_2_periodos_atras_reprobada($interCursos -> clavemat, $alumno);
			if($info -> si_hay_materia){ // Regresará true, si la materia la curso 2 periodos atras, y no la ha vuelto a cursar.
				// Si es true, revisar el sig elemento para ver el nombre de materia, y en que periodo la curso
				array_push($status_id, 12);
				array_push($status_id, $Periodos -> convertirPeriodo_($info->periodo)." materia: ".$info->nombre_materia);
				return $status_id; // 12: Si la materia la curso en periodos anteriores, dígase 2 periodos atras, no podrá cursarse.
			}
			*/
			
			array_push($status_id, 1);
			return $status_id; // Puede proseguir con las siguientes validaciones...
			
		} // function cursos_intersemestrales_validaciones_basicas_para_inscribir_a_intersemestrales($registro, $clavecurso)
		
		function cursos_intersemestrales_procesar_validacion_basica($validacion_basica){
			switch($validacion_basica[0]){
				// 1: Puede proseguir con las siguientes validaciones...
				case 1: return 1;
				
				// 2: Está dado de baja por BAJA DEFINITIVA...
				case 2:  $this -> mensajes = "<p class='letraRoja'>No se puede inscribir al alumno.<br />
						El alumno presenta BAJA DEFINITIVA.<br />";
					break;
				// 3: Está dado de baja por reprobar AL MENOS UN TITULO
				case 3: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno reprob&oacute; un TITULO DE SUFICIENCIA.<br />".
						"Deber&aacute; presentar su CARTA DE RECONSIDERACI&Oacute;N.</p>";
					break;
				// 4: No tiene calificación el alumno.
				case 4: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno no tiene calificaci&oacute;n en su TITULO DE SUFICIENCIA de: ".$validacion_basica[1]."<br />".
						"Deber&aacute; acudir con su profesor para la captura de dicha calificaci&oacute;n.</p>";
					break;
				// 5: No presentó su titulo de suficiencia.
				case 5: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno no present&oacute; su TITULO DE SUFICIENCIA de: ".$validacion_basica[1]."<br />".
						"Deber&aacute; presentar su CARTA DE RECONSIDERACI&Oacute;N.</p>";
					break;
				// 6: El alumno ya tiene dicha materia aprobada en su kardex
				case 6: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno ya tiene acreditada dicha materia en su Kardex.</p>";
					break;
				// 7: Dicha materia no corresponde al plan de estudios de la carrera del alumno.
				case 7: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"La materia a la que intenta inscribir el alumno no es de su plan de estudios.</p>";
					break;
				// 8: No cumple con al menos un prerrequisito y se le mostrará.
				case 8: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"No cumple con los prerrequisitos siguientes: <br /><br />".
						$validacion_basica[1]."</p>";
					break;
				// 9: Tiene una materia como Baja Definitiva, o tiene un titulo reprobado.
				case 9: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"Tiene al menos una materia con Baja Definitiva o Titulo de Suficiencia: <br /><br />".
						$validacion_basica[1]."</p>";
					break;
				// 10: El alumno ya está dado de baja, se checa el $alumno -> stSit
				case 10: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno está dado de baja.</p>";
					break;
				// 11: No hay cupo disponible en el curso.
				case 11: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"No hay cupo suficiente en el curso.</p>";
					break;
				// 12: Si la materia la curso en periodos anteriores, dígase 2 periodos atras, no podrá cursarse.
				case 12: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno curs&oacute; esa materia en el periodo: ".$validacion_basica[1]." y no la ha vuelto a cursar,<br />".
						"no podr&aacute; cursarla debido a que necesita cursarla como Titulo.</p>";
					break;
					// Revisar este punto
				// 13: El alumno tiene en Regularización, o Titulo dicha materia.
				case 13: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El alumno tiene como ".$validacion_basica[1]."<br />";
					break;
				// 14: Si tiene el 60% de sus materias como regularización directa, no podrá cursar ningun curso.
				case 14: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						"El Alumno no tiene un mínimo de 60% de sus materias acreditadas,<br />".
						"tiene reprobadas como regularizaci&oacute;n directa: <br/><br />".
						$validacion_basica[1]."<br />";
					break;
			}
			return 99; // No se debe inscribir a dicho alumno
		} // function cursos_intersemestrales_procesar_validacion_basica($validacion_basica)
		
		function cursos_intersemestrales_validar_nivelacion($registro, $clavemat){
			//$this -> validar_si_es_coordinador();
			//NIV es el mismo curso pero para una materia reprobada en el semestre inmediato anterior
			$Alumnos = new Alumnos();
			$plantel = $Alumnos -> getXalORXtal($registro);
			$Periodos = new Periodos();
			$periodo_anterior = $Periodos -> get_periodo_actual_();
			
			foreach( $Alumnos -> find_all_by_sql("
					select xal.*, xcc.materia
					from x".$plantel[0]."alumnocursos xal
					inner join x".$plantel[1]."cursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = ".$registro."
					and xal.periodo = ".$periodo_anterior."
					and xcc.materia = '".$clavemat."'") as $resultset){
				return 1;// 1: Significa que si se podrá inscribir a un curso de Nivelación
			}
			return 2; // 2 No se puede inscribir a un curso de nivelación, ya que no cursaba la materia en el
			//semestre inmediato anterior...
		} // function cursos_intersemestrales_validar_nivelacion($registro, $clavemat)
		
		function cursos_intersemestrales_validar_acreditacion($registro, $clavemat){
			//$this -> validar_si_es_coordinador();
			// ACR Nunca la ha tomado antes
			$Alumnos = new Alumnos();
			$Periodos = new Periodos();
			
			$status_id = array();
			
			foreach( $Alumnos -> find_all_by_sql("
					select xal.*, xcc.materia, xcc.clavecurso
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = ".$registro."
					and xcc.materia = '".$clavemat."'") as $resultset){
				array_push($status_id, 2);
				array_push($status_id, "Materia cursada en el plantel Colomos, con clavecurso: ".
						$resultset -> clavecurso.".<br />En el periodo ".$Periodos -> convertirPeriodo_($resultset -> periodo));
				return $status_id; // 2: El alumno ya ha cursado la materia con anterioridad.
			}
			foreach( $Alumnos -> find_all_by_sql("
					select xal.*, xcc.materia, xcc.clavecurso
					from xtalumnocursos xal
					inner join xtcursos xcc
					on xal.curso_id = xcc.id
					and xal.registro = ".$registro."
					and xcc.materia = '".$clavemat."'") as $resultset){
				array_push($status_id, 2);
				array_push($status_id, "Materia cursada en el plantel Tonala, con clavecurso: ".
						$resultset -> clavecurso.".<br />En el periodo ".$Periodos -> convertirPeriodo_($resultset -> periodo));
				return $status_id; // 2: El alumno ya ha cursado la materia con anterioridad.
			}
			// Checar que no la haya cursado antes en Intersemestral
			foreach( $Alumnos -> find_all_by_sql("
					select ialumnos.*
					from intersemestral_cursos icursos
					inner join intersemestral_alumnos ialumnos
					on icursos.clavecurso = ialumnos.clavecurso
					and ialumnos.registro = ".$registro."
					and icursos.clavemat = '".$clavemat."'") as $resultset){
				array_push($status_id, 3);
				array_push($status_id, "Materia cursada en Intersemestral.".
						"<br />En el periodo ".$Periodos -> convertirPeriodo_($periodo));
				return $status_id; // 3: El alumno ya ha tomado la materia en un curso Intersemestral.
			}
			
			array_push($status_id, 1);
			return $status_id; // 1: Significa que si se podrá inscribir a un curso de Acreditación
			
			// 2: No se puede inscribir a un curso de Acreditación, ya que ya ha cursado dicha materia anteriormente
			
		} // function cursos_intersemestrales_validar_acreditacion($registro, $clavemat)
		
		function cursos_intersemestrales_procesar_validacion_acreditacion($validacion_acreditacion){
			switch($validacion_acreditacion[0]){
				// 1: Puede proseguir con las siguientes validaciones...
				case 1: return 1;
				// 2: El alumno ya ha cursado la materia con anterioridad.
				case 2: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						$validacion_acreditacion[1]."<br /></p>";
					break;
				// 3: El alumno ya ha tomado la materia en un curso Intersemestral.
				case 3: $this -> mensajes ="<p class='letraRoja'>No se puede inscribir al alumno.<br />".
						$validacion_acreditacion[1]."<br /></p>";
					break;
			}
			return 99; // No se debe inscribir a dicho alumno
		} // function cursos_intersemestrales_procesar_validacion_basica($validacion_acreditacion)
		
		function cursos_intersemestrales_desinscribir_alumno(){
			//$this -> validar_si_es_coordinador();
			$this -> set_response("view");
			
			$inter_al_id = $this -> post("inter_al_id");
			
			$IntersemestralCursos = new IntersemestralCursos();
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			
			$clavecurso = $IntersemestralAlumnos -> get_clave_curso_by_inter_al_id($inter_al_id);
			$IntersemestralCursos -> sumar_disponibilidad($clavecurso);
			
			$IntersemestralCursos -> desinscribir_alumno($inter_al_id);
		} // function cursos_intersemestrales_desinscribir_alumno()
		
		function cursos_intersemestrales_editar_alumno(){
			$this -> set_response("view");
			
			$inter_al_id = $this -> post("inter_al_id");
			
			
			//echo "NO"."#"."No se puede inscribir a un alumno en el mismo curso";
			
		} // function cursos_intersemestrales_editar_alumno()
		
		function cursos_intersemestrales_listado(){
			//$this -> validar_si_es_coordinador();
			$this -> cursos_intersemestrales_unset_vars();
			
			$Periodos = new Periodos();
			$Maestros = new Maestros();
			
			$this -> periodoCompleto = $Periodos -> convertirPeriodo_a_intersemestral_letra($Periodos->get_periodo_actual());
			$this -> get_info_from_coordinador();
			
			// Obtener informacion con la que llenara el curso el coordinador...
			$this -> maestros = $Maestros -> get_todos_los_maestros();
			
			$IntersemestralCursos = new IntersemestralCursos();
			$this -> cursos = $IntersemestralCursos -> get_todos_cursos_periodo_actual();
		} // function cursos_intersemestrales_listado()
		
		function cursos_intersemestrales_buscar_curso(){
			//$this -> validar_si_es_coordinador();
			$this -> set_response("view");
			
			$this -> cursos_intersemestrales_unset_vars();
			
			$clavecurso = $this -> post("buscarcurso");
			
			$IntersemestralCursos = new IntersemestralCursos();
			
			if( !($this -> curso = $IntersemestralCursos -> get_curso_by_clavecurso($clavecurso)) ){
				$this -> mensaje = "El curso \"".$clavecurso."\" no fue encontrado";
			}
			
			echo $this -> render_partial("cursos_intersemestrales_buscar_curso");
		} // function cursos_intersemestrales_buscar_curso()
		
		function cursos_intersemestrales_borrar_curso(){
			//$this -> validar_si_es_coordinador();
			unset($this -> exito_en_borrar);
			
			$clavecurso = $this -> post("delete_clavecurso");
			
			$IntersemestralCursos = new IntersemestralCursos();
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			
			if( !($IntersemestralAlumnos -> find_first("clavecurso = '".$clavecurso."'")) ){
				$IntersemestralCursos -> borrar_curso($clavecurso);
				$IntersemestralCursos -> desinscribir_alumnos($clavecurso);
				$this -> exito_en_borrar = 1;
				$this -> render('cursos_intersemestrales_borrar_curso');
				return;
			}
		} // function cursos_intersemestrales_borrar_curso()
	}
?>