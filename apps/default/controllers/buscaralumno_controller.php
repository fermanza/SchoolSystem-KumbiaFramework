<?php
	
	class BuscaralumnoController extends ApplicationController {
	
		public $antesAnterior=32009;
		public $anterior	= 12010;
		public $actual		= 32010;
		public $proximo		= 12011;
		
		function index(){
			
		} // function index()
		
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
		
		function menubuscaralumno( $reg ){
			$this -> set_response("view");
			
			unset( $this -> reg );
			unset( $this -> al );
			unset( $this -> plantel );
			
			$this -> reg = $reg;
			$alumno = new Alumnos();
			$this -> al = $alumno -> find_first("miReg = ".$reg );
			
			unset($this -> periodos);
			$Periodos = new Periodos();
			$this -> periodos = $Periodos -> get_todos_periodos_desde_xccursos();
			
			$this -> render_partial("menubuscaralumno");
		} // function menubuscaralumno( $reg )
		
        function buscandoAlumno(){
			$this -> set_response("view");

			if($this -> post("registro") != ''){
				$this -> registro = " and miReg = ".$this -> post("registro");
			}
			else{
				$this -> registro = "";
			}

			if($this -> post("nombre") != ''){
				$this -> nombre = " and vcNomAlu like '%".utf8_decode($this -> post("nombre"))."%'";
			}
			else{
				$this -> nombre = "";
			}

			if($this -> registro != "" || $this -> nombre != ""){
				$Alumnos = new Alumnos();
				if($this -> alumnos = $Alumnos -> find("1".$this -> registro.$this -> nombre)){
					$Especialidades = new Especialidades();
					$j = 0;
					foreach( $this -> alumnos as $alumn ){
						$this -> especialidad[$j] = $Especialidades ->
										find_first( "idtiEsp = ".$alumn -> idtiEsp );
						$j++;
					}
					$this -> render_partial("busquedaAlumnos");
				}
				else{
					Flash::error("No se encontro ningun alumno");
				}
			}
			else{
				Flash::error("No se encontro ningun alumno");
			}
			$this -> registro="";
			$this -> nombre="";
        } // function buscandoAlumno()
		
		function calificacion( $registro, $periodo ){
			$this -> valida();
			
			$this->redirect("alumno/calificaciones/$registro/$periodo/1");
			
		} // function calificacion
		
		function calificacionT( $registro, $periodo ){
			$this -> valida();
			
			$this -> set_response("view");
			
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
					$tmp = $xcursos->find_first("id='".$curso->curso_id."'");
					
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
						$extraordinarios = $xextraordinarios->find_first("curso_id='".$curso->curso_id."' AND registro=".$registro);
						
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
						if($calificaciones->situacion == "EXTRAORDINARIO" ||
								$calificaciones->situacion == "EXTRAORDINARIO FALTAS"){ 
							$this->extra[$i] = "NP";
							$this->titulo[$i] = " - ";
						}
						else{
							if($calificaciones->situacion == "TITULO POR CALIFICACION" || 
									$calificaciones->situacion == "TITULO POR FALTAS" || 
											$calificaciones->situacion == "TITULO POR NO REGISTRO"){
								$this->extra[$i] = " - ";
								$this->titulo[$i] = "NP";
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
			$Xalumnocursos = new Xalumnocursos();
			$this->BajaDefinitiva = $Xalumnocursos->get_materia_con_baja_definitiva_by_periodo($registro, $periodo);
			
			unset($this->cursos_intersemestrales_alumno);
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$this->cursos_intersemestrales_alumno = $IntersemestralAlumnos->get_cursos_de_un_alumno_by_periodo($registro, $periodo);
			
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
				//$this->antesAnterior = 12011;
				//$this->anterior = 32011;
				//$this->actual = 12012;
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
			
			$this -> buscaralumno = 1;
			$this -> render("../alumno/calificacionesT");
		} // function calificacionT(){
		
		function kardex($registro){
			//if(!$this -> validarEncuesta()){
				//$this -> redirect("alumno/encuestas");
				//return;
			//}
			$this -> valida();
			$this -> set_response("view");
			
			//$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$registro."'");
			
			//////////////////////////////////////////////
			$Alumnos = new Alumnos();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			unset($this -> editable);
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> kardex = $KardexIng -> get_completeKardex($registro, 0);
			$this -> cuantasMaterias = $KardexIng -> get_count_kardex_from_student($registro);
			
			$this -> ncreditos = $this -> kardex[0][0] -> sumaCreditos;
			$this -> promedio = $this -> kardex[0][0] -> promedioTotal;
			$i = 0;
			while( isset($this -> kardex[$i][0] -> periodosBuenos) ){
				$this -> avancePeriodos[$i] = $this -> kardex[$i][0] -> periodosBuenos;
				$i++;
			}
			
			if( strtoupper(Session::get_data('tipousuario')) == "VENTANILLA" )
				$this -> editable = 4; // Editable 4, no puede editar kardex, pero si puede imprimir kardex
			else
				$this -> editable = 3; // Editable 3, no puede editar kardex, ni imprimir kardex, pero tampoco es usuario alumno
			
			echo $this -> render_partial("kardex");
		} // function kardex()
		
		function horario( $registro, $periodo ){
			$this -> valida();
			//$periodo = $this -> actual;
			
			$this -> set_response("view");
			
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
			unset($this->buscaralumno);
			
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
			
			$this->xccur = array();
			$this->materias = array();
			$this->maestros = array();
			
			if(count($xalcursos)>0){
				foreach($xalcursos as $xalcurso){
					$xccurso = $xccursos->find_first( "id = '".$xalcurso->curso_id."'" );
					array_push($this->xccur, $xccurso);
					$j = 0;
					
					$materia = $materias->find_first( "clave = '".$xccurso->materia."'" );
					array_push($this->materias, $materia);
					
					$maestro = $maestros->find_first( "nomina = ".$xccurso->nomina );
					array_push($this->maestros, $maestro);
					
					foreach( $xchorascursos->find_all_by_sql( "
							Select xcc.clavecurso, xcc.materia,
							m.nombre, xch.dia, xch.hora, xcs.edificio, xcs.nombre nombreSalon
							from xccursos xcc, xchorascursos xch, xcsalones xcs, materia m
							where xcc.id = '".$xccurso->id."'
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
					$xccurso = $xtcursos->find_first( "id = '".$xalcurso->curso_id."'" );
					array_push($this->xccur, $xccurso);
					$j = 0;
					
					$materia = $materias->find_first( "clave = '".$xccurso->materia."'" );
					array_push($this->materias, $materia);
					
					$maestro = $maestros->find_first( "nomina = ".$xccurso->nomina );
					array_push($this->maestros, $maestro);
					
					foreach( $xchorascursos->find_all_by_sql( "
							Select xcc.clavecurso, xcc.materia,
							m.nombre, xch.dia, xch.hora, xcs.edificio, xcs.nombre nombreSalon
							from xtcursos xcc, xthorascursos xch, xtsalones xcs, materia m
							where xcc.id = '".$xccurso->id."'
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
			
			$this -> buscaralumno = 1;
			$this -> render("../alumno/horario");
		} // function horario()
		
		function horarioT( $registro, $periodo ){
			$this -> valida();
			//$periodo = $this -> actual;
			//$periodo = $this -> actual;
			
			$this -> set_response("view");
			
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
			
			$xccursos		= new Xtcursos();
			$maestros		= new Maestros();
			$materias		= new Materia();
			$xalumnocursos	= new Xtalumnocursos();
			$Alumnos		= new Alumnos();
			$xchorascursos	= new Xthorascursos();
			$xcsalones		= new Xtsalones();
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
			
			$this->ingreso = $alumno->miPerIng;
			
			$plan = substr($alumno->enPlan,2,2);
			
			if( $this->promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			
			$this->promedio = substr( $this->promedio, 0, $cuanto );
			
			$this->idplan = $plan->id;
			$this->plan = $plan->nombre;
			
			$xalcursos = $xalumnocursos->find("registro = ".$registro." AND periodo = ".$periodo);
			$this->cursos = $xalumnocursos->count("registro= ".$registro." AND periodo= ".$periodo);
			
			$i = 0;
			$matutino = 0;
			$vespertino = 0;
			if($this->cursos > 0 ){
				foreach($xalcursos as $xalcurso){
					
					$xccurso = $xccursos->find_first( "id = '".$xalcurso->curso_id."'" );
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
							where xcc.id = '".$xccurso->id."'
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
			} // if($this->cursos > 0 )
			
			$this -> buscaralumno = 1;
			$this -> render("../alumno/horarioT");
		} // function horarioT()
		
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
		} // function incrementaPeriodo($periodo)
		
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
		} // function incrementaPeriodoKardex($periodo)
		
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
		} // function numero_letra($numero)
		
		function determinarPlantelCalificacion( $registroo, $periodo ){
			
			$xalumnocursos = new Xalumnocursos();
			$xtalumnocursos = new Xtalumnocursos();
			if( $xalumnocursos -> find_first("periodo = ".$periodo.
					" and registro = ".$registroo) ){
				$this -> calificacion( $registroo, $periodo );
			}else if ( $xtalumnocursos -> find_first("periodo = ".$periodo.
					" and registro = ".$registroo) ){
				$this -> calificacion( $registroo, $periodo );
			}
			die;
		} // function determinarPlantelCalificacion( $registroo, $periodo )
		
		function determinarPlantelHorario( $registroo, $periodo ){
			$this -> horario($registroo, $periodo);
			
			return;
		} // function determinarPlantelHorario( $registroo, $periodo )
		
		function valida(){
			if( Session::get_data('tipousuario') != "PROFESOR" && 
					Session::get_data('tipousuario') != "VENTANILLA" &&
						Session::get_data('tipousuario') != "OBSERVADOR" &&
							Session::get_data("tipousuario") != "GOE" && 
								Session::get_data("tipousuario") != "INGCALCULO" &&
									Session::get_data("tipousuario") != "TUTOR" && 
										Session::get_data("tipousuario") != "DIRECCION" ){
				$this->redirect('general/inicio');
			}
		} // function valida()
	}
