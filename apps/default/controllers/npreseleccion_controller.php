<?php
			
	class npreseleccionController extends ApplicationController {
		
		public $anterior	= 32011;
		public $actual		= 12012;
		public $proximo		= 32012;
		
		function index( $status ){
			if( isset($status) && $status == 2 ){
				$this -> redirect("/");
			}
		}
		
		function captura(){
			
			$this -> valida();
			
			$preseleccion	= new Preseleccion();
			$materias		= new Materia();
			$materias2		= new Materia();
			$alumnos		= new Alumnos();
			$kardexIng		= new KardexIng();
			$xalumnocursos	= new Xalumnocursos();
			
			unset($this -> materiasSinEsp);
			unset($this -> materiasConEsp);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> datos);
			unset($this -> miReg);
			unset($this -> vcNomAlu);
			unset($this -> carrera);
			unset($this -> carrera_id);
			unset($this -> arrayConMateriasDeSuArea);
			unset($this -> octavos);
			unset($this -> creditos);
			unset($this -> mensajeTerminarPreselecc);
			unset($this -> areadeformacion);
			unset($this -> areadeformacion_id);
			unset($this -> preseleccionadas);
			unset($this -> areaTemp);
			unset($this -> mostrarAreasDeFormacion);
			unset($this -> posiblesAreasDeFormacion);
			unset($this -> YaContestoPreguntaIntersemestral);
			
			$registro = Session::get_data('registro');
			// Inicializar arreglo de materias que puede seleccionar
			$this -> materiasQuePuedeSeleccionar = array();
			
			foreach( $alumnos -> find_all_by_sql
					("select al.miReg, al.vcNomAlu, carr.id, carr.nombre as carrera,
					al.areadeformacion_id, al.carrera_id
					from alumnos al
					inner join carrera carr
					on al.miReg = ".$registro."
					and al.carrera_id = carr.id") as $datosAlumno ){
				$this -> miReg = $datosAlumno -> miReg;
				$this -> vcNomAlu = $datosAlumno -> vcNomAlu;
				$this -> carrera = $datosAlumno -> carrera;
				$this -> carrera_id = $datosAlumno -> carrera_id;
				
				// Meter en un arreglo las materias que ya tiene acreditadas en su kardex
				$materiasEnKardex = $this -> materiasAcreditadasEnKardex();
				$materiasEnKardex2 = $materiasEnKardex;
				// Materias que está cursando actualmente, se tomarán como ya acreditadas.
				$materiasEnKardex =  $this -> materiasCursandoActualmente($materiasEnKardex);
				// Checar cuántos créditos tiene en el kardex
				$this -> sumarCreditosDelKardex($datosAlumno);
				// Checar cuántos créditos lleva en la preselección
				$this -> checarNumeroDeCreditos($datosAlumno -> carrera_id);
				
				if( $this -> creditos >= 64 ){
					$this -> mensajeTerminarPreselecc = "<h2>Ha terminado de escoger sus materias, favor de terminar
					su preselecci&oacute;n</h2>";
					$intersemestrales_estadistica = new IntersemestralesEstadistica();
					$this -> YaContestoPreguntaIntersemestral = 0;
					if( $intersemestrales_estadistica -> find_first("registro = ".$registro.
							" and periodo = ".$this -> actual) ){
						$this -> YaContestoPreguntaIntersemestral = 1;
					}
					$this -> render("captura");
					return;
				}
				// Solo para function captura()
				// Si no tiene areadeformacion, le mostrarémos
				//las diferentes áreas de formación que puede escoger
					// Validar que al menos tenga una materia de 4to sem
					//en su kardex, para mostra las AreasDeFormación
				if( $datosAlumno -> carrera_id <= 5 )
					$this -> areaTemp = 1;
				$aux = 0;
				// Obtener area de formacion temporal
				$this -> getAreaFormacionTemp();
				if( $datosAlumno -> carrera_id >= 5 && $datosAlumno -> areadeformacion_id == 0 && (!isset($this -> areaTemp)) ){
					if( $datosAlumno -> carrera_id == 7 ){
						$query_string =
							"select * from kardex_ing k, materia m
							where k.registro = ".$datosAlumno -> miReg."
							and k.clavemat = m.clave
							and m.semestre >= 3
							and m.carrera_id = ".$datosAlumno -> carrera_id."
							limit 1";
					}
					else{
						$query_string =
							"select * from kardex_ing k, materia m
							where k.registro = ".$datosAlumno -> miReg."
							and k.clavemat = m.clave
							and m.semestre >= 4
							and m.carrera_id = ".$datosAlumno -> carrera_id."
							limit 1";
					}
					foreach( $kardexIng -> find_all_by_sql($query_string) as $kardex ){
						$this -> mostrarAreasDeFormacion = 1;
						if(!isset($this -> areaTemp) || $this -> areaTemp == 0 ){
							$this -> arrayConMateriasDeSuArea = $this -> validaAreaDeFormacionTEMP($this -> carrera_id);
						}
						else{
							$datosAlumno -> areadeformacion_id = $this -> areaTemp;
						}
						$aux++;
					}
				}
				if( $aux == 0 || $datosAlumno -> areadeformacion_id != 0 )
					$this -> areaTemp = 1;
				$intersemestrales_estadistica = new IntersemestralesEstadistica();
				$this -> YaContestoPreguntaIntersemestral = 0;
				if( $intersemestrales_estadistica -> find_first("registro = ".$registro.
						" and periodo = ".$this -> actual) ){
					$this -> YaContestoPreguntaIntersemestral = 1;
				}
				// Fin solo functioncaptura
				///////////////////////////////////////////////////////////
				
				// Materias que puede escoger para esta preselección.
				$this -> getMateriasQuePuedeSeleccionar($datosAlumno, $materiasEnKardex);
				// Materias que está cursando actualmente, se tomarán como si reprobara todas.
				$this -> getMateriasCursandoActualmente();
				// Agregar materias que tengan prerrequisito = 0 en la tabla materia
				$this -> getMateriasConPrerrequisitoCero($materiasEnKardex2);
			}
			$i = 0;
			foreach( $preseleccion -> find
					( "registro = ".$registro." 
						and periodo = ".$this -> actual ) as $presel ){
				$this -> preseleccionadas[$i] = $presel; // Meter en un arreglo las materias que ya tiene preseleccionadas
				foreach( $materias -> find_all_by_sql
						( "select nombre 
						from materia
						where clave = '".$presel -> clavemat."'" ) as $mat ){
					$this -> nombremat[$i] = $mat;
				}
				$i ++;
			}
			
		} // function captura()
		
		function capturando( $registro, $clavemat ){
			
			$Preseleccion	= new Preseleccion();
			
			// $clavemat	= $this -> post("clavemat");
			// $registro	= Session::get_data( "registro ");
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			
			if( $Preseleccion -> find_first
					( "registro = ".$registro." 
							and clavemat = '".$clavemat."'
								and periodo = ".$this -> actual ) ){
				$this -> redirect($this -> captura2());
			}
			
			// Se guarda la fecha en que se hizo la selección,
			//con formato de fecha: 0000-00-00 00:00:00
			$Preseleccion -> registro = $registro;
			$Preseleccion -> clavemat = $clavemat;
			$Preseleccion -> periodo = $this -> actual;
			$Preseleccion -> fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			
			$Preseleccion -> create();
			$this -> redirect($this -> captura2());
		} // function capturando()
		
		function captura2(){
			
			$this -> set_response("view");
			
			$this -> valida();
			
			$preseleccion	= new Preseleccion();
			$materias		= new Materia();
			$materias2		= new Materia();
			$alumnos		= new Alumnos();
			$kardexIng		= new KardexIng();
			$areadeform		= new Areadeformacion();
			$xalumnocursos	= new Xalumnocursos();
			
			unset($this -> materiasSinEsp);
			unset($this -> materiasConEsp);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> datos);
			unset($this -> miReg);
			unset($this -> vcNomAlu);
			unset($this -> carrera);
			unset($this -> carrera_id);
			unset($this -> areadeformacion_id);
			unset($this -> octavos);
			unset($this -> creditos);
			unset($this -> mensajeTerminarPreselecc);
			unset($this -> areadeformacion);
			unset($this -> preseleccionadas);
			
			$registro = Session::get_data('registro');
			// Inicializar arreglo de materias que puede seleccionar
			$this -> materiasQuePuedeSeleccionar = array();
			
			foreach( $alumnos -> find_all_by_sql
					("select al.miReg, al.vcNomAlu, carr.id, carr.nombre as carrera,
					al.areadeformacion_id, al.carrera_id
					from alumnos al
					inner join carrera carr
					on al.miReg = ".$registro."
					and al.carrera_id = carr.id") as $datosAlumno ){
				$this -> miReg = $datosAlumno -> miReg;
				$this -> vcNomAlu = $datosAlumno -> vcNomAlu;
				$this -> carrera = $datosAlumno -> carrera;
				$this -> carrera_id = $datosAlumno -> carrera_id;
				
				// Meter en un arreglo las materias que ya tiene acreditadas en su kardex
				$materiasEnKardex = $this -> materiasAcreditadasEnKardex();
				$materiasEnKardex2 = $materiasEnKardex;
				// Materias que está cursando actualmente, se tomarán como ya acreditadas.
				$materiasEnKardex =  $this -> materiasCursandoActualmente($materiasEnKardex);
				// Checar cuántos créditos tiene en el kardex
				$this -> sumarCreditosDelKardex($datosAlumno);
				// Checar cuántos créditos lleva en la preselección
				$this -> checarNumeroDeCreditos($datosAlumno -> carrera_id);
				
				if( $this -> creditos >= 64 ){
					$this -> mensajeTerminarPreselecc = "<h2>Ha terminado de escoger sus materias, favor de terminar
					su preselecci&oacute;n</h2>";
					break;
				}
				
				// Materias que puede escoger para esta preselección.
				$this -> getMateriasQuePuedeSeleccionar($datosAlumno, $materiasEnKardex);
				// Materias que está cursando actualmente, se tomarán como si reprobara todas.
				$this -> getMateriasCursandoActualmente();
				// Agregar materias que tengan prerrequisito = 0 en la tabla materia
				$this -> getMateriasConPrerrequisitoCero($materiasEnKardex2);
			}
			$i = 0;
			foreach( $preseleccion -> find
					( "registro = ".$registro." 
						and periodo = ".$this -> actual ) as $presel ){
				$this -> preseleccionadas[$i] = $presel; // Meter en un arreglo las materias que ya tiene preseleccionadas
				foreach( $materias -> find_all_by_sql
						( "select nombre 
						from materia
						where clave = '".$presel -> clavemat."'" ) as $mat ){
					$this -> nombremat[$i] = $mat;
				}
				$i ++;
			}
			
			$this -> render_partial("captura2");
			exit(1);
		} // function captura2
		
		function quitando( $registro, $clavemat ){
			
			$Preseleccion	= new Preseleccion();
			$materias		= new Materia();
			
			foreach( $Preseleccion -> find_all_by_sql
					( "Select * from preseleccion
						where registro = ".$registro."
						and clavemat = '".$clavemat."'
						and periodo = ".$this -> actual ) as $pre ){
				$pre -> delete();
			}
			
			$this -> redirect( $this -> captura2() );
			
		} // function eliminar()
		
		function terminarPreseleccion(){
			
			if( $registro = $this -> post( "registro" ) ){
				$preTerminada	= new PreseleccionTerminada();
				
				$preTerminada -> registro = $registro;
				$preTerminada -> periodo = $this -> actual;
				$day = date ("d");
				$month = date ("m");
				$year = date ("Y");
				$hour = date ("H");
				$minute = date ("i");
				$second = date ("s");
				$preTerminada -> fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
				$preTerminada -> create();
			}
		} // function terminarPreseleccion()
		
		function materiasAcreditadasEnKardex(){
			$this -> valida();
			$registro = Session::get_data('registro');
			$kardexIng = new Kardexing();
			// Meter en un arreglo las materias que ya tiene acreditadas en su kardex
			$materiasEnKardex = array();
			foreach( $kardexIng -> find_all_by_sql
					("Select clavemat from kardex_ing
					where registro = '".$registro."'") as $king ){
				array_push($materiasEnKardex, $king -> clavemat);
			}
			return $materiasEnKardex;
		} // function materiasAcreditadasEnKardex()
		
		function materiasCursandoActualmente($materiasEnKardex){
			//if( !defined('BASEPATH') ) exit("No direct script access allowed");
			$this -> valida();
			$registro = Session::get_data('registro');
			$xalumnocursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$plantel = $alumnos -> getXalORXtal($registro);
			
			// Materias que está cursando actualmente, se tomarán como ya acreditadas.
			foreach( $xalumnocursos -> find_all_by_sql
					("Select xcc.materia as clavemat
					from x".$plantel[1]."cursos xcc
					inner join x".$plantel[0]."alumnocursos xal
					on xcc.clavecurso = xal.curso
					and xal.registro= '".$registro."'
					and xal.periodo = '".$this -> anterior."'") as $xcc ){
				array_push($materiasEnKardex, $xcc -> clavemat);
			}
			return $materiasEnKardex;
		} // function materiasCursandoActualmente($materiasEnKardex)
		
		function sumarCreditosDelKardex($alumno){
			$this -> valida();
			$kardexIng = new Kardexing();
			// Checar cuántos créditos tiene en el kardex
			foreach( $kardexIng -> find_all_by_sql
					( "Select sum(creditos) as creditos
					from kardex_ing king, materia m
					where king.registro = ".$alumno -> miReg."
					and king.clavemat = m.clave
					and m.carrera_id = '".$alumno -> carrera_id."'
					and (m.serie = '".$alumno -> areadeformacion_id."'
					or m.serie = '-')" ) as $king ){
				if( $king -> creditos >= 400 )
					$this -> octavos = 1;
				else
					$this -> octavos = 0;
			}
		} // function sumarCreditosDelKardex($alumno)
		
		function checarNumeroDeCreditos($carrera_id){
			$this -> valida();
			$registro = Session::get_data('registro');
			$kardexIng = new Kardexing();
			// Checar cuántos créditos lleva en la preselección
			foreach( $kardexIng -> find_all_by_sql
					( "select sum(creditos) as creditos
					from preseleccion pre, materia m
					where pre.registro = ".$registro."
					and pre.clavemat = m.clave
					and pre.periodo = ".$this -> actual."
					and m.carrera_id = ".$carrera_id) as $king ){
				$this -> creditos = $king -> creditos;
				if( $this -> creditos == null )
					$this -> creditos = 0;
			}
		} // function checarNumeroDeCreditos($carrera_id)
		
		function getMateriasQuePuedeSeleccionar($datosAlumno, $materiasEnKardex){
			$this -> valida();
			$registro = Session::get_data('registro');
			
			$areadeform		= new Areadeformacion();
			$preseleccion	= new Preseleccion();
			$materias		= new Materia();
			$materias2		= new Materia();
			
			// Si el alumno no tiene asignada area de formacion, pero ya seleccionó área de formación temporal,
			//le asignamos a $datosAlumno -> areadeformacion_id el area de formacion temporal que acaba de seleccionar.
			if( $datosAlumno -> areadeformacion_id == 0 ){
				// Obtener area de formacion temporal
				$this -> getAreaFormacionTemp();
				$datosAlumno -> areadeformacion_id = $this -> areaTemp;
			}
			if( $datosAlumno -> areadeformacion_id != 0 ){
				$area = $areadeform -> find_first("idareadeformacion = '".$datosAlumno -> areadeformacion_id."'
					and carrera_id = '".$datosAlumno -> carrera_id."'" );
				$this -> areadeformacion = $area -> nombreareaformacion;
				// $queryConMatEsp
				$query = "Select clave, nombre
					from materia
					where carrera_id = '".$datosAlumno -> carrera_id."'
					and (serie = '".$datosAlumno -> areadeformacion_id."'
					or serie = '-')";
				$i = 0;
				foreach( $materias -> find_all_by_sql( $query )
						as $materiasConEsp ){
					if( $preseleccion -> find_first( "registro = ".$datosAlumno -> miReg."
							and clavemat = '".$materiasConEsp -> clave."'
							and periodo = ".$this -> actual ) )
						continue;
					if( !in_array($materiasConEsp -> clave, $materiasEnKardex) ){
						
						// Dicha materia no se encuentra en su kardex, por lo que puede seleccionarla.
						//(aunque aún falta revisar si ya cumplió con los prerrequisitos de esa materia)
						unset($prerrequisitos);
						$j = 0;
						foreach( $materias2 -> find_all_by_sql("
								select p.clavemat
								From materia m
								inner join prerrequisito p
								on m.prerrequisito = id_prereq
								and m.carrera_id = '".$datosAlumno -> carrera_id."'
								and m.clave = '".$materiasConEsp -> clave."'") as $prerrequisito ){
							$prerrequisitos[$j] = $prerrequisito -> clavemat;
							$j++;
						}
						$noCumpleConPrerrequisitos = 0;
						foreach( $prerrequisitos as $prerrequisito ){
							// Checar que todas las materias que están en $materiasEnKardex
							//esten en prerrequisitos.
							if( !in_array($prerrequisito, $materiasEnKardex) ){
								$noCumpleConPrerrequisitos++;
								continue;
							}
						}
						if( $noCumpleConPrerrequisitos == 0 ){
							$this -> materiasQuePuedeSeleccionar[$i] = $materiasConEsp;
							$i++;
						}
					}
				}
			}
			else{
				// $querySinMatEsp
				$query = "Select clave, nombre
				from materia
				where carrera_id = '".$datosAlumno -> carrera_id."'";
				$i = 0;
				foreach( $materias -> find_all_by_sql( $query )
						as $materiasSinEsp ){
					if( $preseleccion -> find_first( "registro = ".$datosAlumno -> miReg."
							and clavemat = '".$materiasSinEsp -> clave."'
							and periodo = ".$this -> actual ) )
						continue;
					if( !in_array($materiasSinEsp -> clave, $materiasEnKardex) ){
						
						// Dicha materia no se encuentra en su kardex, por lo que puede seleccionarla.
						//(aunque aún falta revisar si ya cumplió con los prerrequisitos de esa materia)
						unset($prerrequisitos);
						$j = 0;
						foreach( $materias2 -> find_all_by_sql("
								select p.clavemat
								From materia m
								inner join prerrequisito p
								on m.prerrequisito = id_prereq
								and m.carrera_id = '".$datosAlumno -> carrera_id."'
								and m.clave = '".$materiasSinEsp -> clave."'") as $prerrequisito ){
							$prerrequisitos[$j] = $prerrequisito -> clavemat;
							$j++;
						}
						$noCumpleConPrerrequisitos = 0;
						foreach( $prerrequisitos as $prerrequisito ){
							// Checar que todas las materias que están en $materiasEnKardex
							//esten en prerrequisitos.
							if( !in_array($prerrequisito, $materiasEnKardex) ){
								$noCumpleConPrerrequisitos++;
								continue;
							}
						}
						if( $noCumpleConPrerrequisitos == 0 ){
							$this -> materiasQuePuedeSeleccionar[$i] = $materiasSinEsp;
							$i++;
						}
					}
				}
			}
		} // function getMateriasQuePuedeSeleccionar($datosAlumno)
		
		function getMateriasConPrerrequisitoCero($materiasEnKardex2){
			$materia = new Materia();
			foreach( $materia -> find_all_by_sql(
					"Select clave, nombre
					from materia
					where carrera_id = ".$this -> carrera_id."
					and prerrequisito = '0'
					and serie = '-'") as $m ){
				if( (!in_array($m -> clave, $materiasEnKardex2)) &&
						(!in_array($m, $this -> materiasQuePuedeSeleccionar)) ){
					array_push($this -> materiasQuePuedeSeleccionar, $m);
				}
			}
		} // function getMateriasConPrerrequisitoCero()
		
		function getAreaFormacionTemp(){
			$preseleccion_areaformaciontemp = new PreseleccionAreaformaciontemp();
			$registro = Session::get_data('registro');
			$areaTemp = $preseleccion_areaformaciontemp -> find_first("registro = ".$registro." and periodo = ".$this -> actual);
			$this -> areaTemp = $areaTemp -> areadeformacion_id;
		} // function getAreaFormacionTemp()
		
		function getMateriasCursandoActualmente(){
			// Materias que está cursando actualmente, se tomarán como si reprobara todas.
			$alumnos = new Alumnos();
			$registro = Session::get_data('registro');
			$plantel = $alumnos -> getXalORXtal($registro);
			$xalumnocursos = new Xalumnocursos();
			$preseleccion = new Preseleccion();
			$materia = new Materia();
			
			if( !isset($this -> materiasQuePuedeSeleccionar) )
				$this -> materiasQuePuedeSeleccionar = array();
			
			foreach( $xalumnocursos -> find_all_by_sql
					("Select xcc.materia
					from x".$plantel[1]."cursos xcc
					inner join x".$plantel[0]."alumnocursos xal
					on xcc.clavecurso = xal.curso
					and xal.periodo = ".$this -> anterior."
					and xal.registro = ".$registro) as $xcc ){
				if( !$preseleccion -> find_first
						( "registro = ".$registro." 
								and clavemat = '".$xcc -> materia."'
									and periodo = ".$this -> actual ) ){
					foreach( $materia -> find_all_by_sql(
							"Select clave, nombre
							from materia
							where clave = '".$xcc -> materia."'
							limit 1") as $mat ){
						array_push($this -> materiasQuePuedeSeleccionar, $mat);
					}
				}
			}
		} // function getMateriasCursandoActualmente()
		
		function validaAreaDeFormacionTEMP($carrera_id){
			$registro = Session::get_data('registro');
			
			$areadeformacion = new Areadeformacion();
			$materia = new Materia();
			unset($this -> posiblesAreasDeFormacion);
			
			$areasdeformacion = $areadeformacion -> find("carrera_id = ".$carrera_id);
			
			$areas = 0;
			foreach( $areasdeformacion as $area ){
				$i = 0;
				foreach( $materia -> find("carrera_id = '".$carrera_id."'
						and serie = '".$area -> idareadeformacion."'") as $mat ){
					$materias[$areas][$i] = $mat -> nombre;
					$i++;
				}
				$this -> posiblesAreasDeFormacion[$areas] = $area -> nombreareaformacion;
				$areas++;
			}
			return $materias;
			
		} // function validaAreaDeFormacionTEMP()
		
		function guardandoAreaDeFormacion(){
			$this -> valida();
			$registro = Session::get_data('registro');
			
			$idareadeformacion = $this -> post("idareadeformacion");
			$intersemestral = $this -> post("intersemestral");
			
			$preseleccion_areaformaciontemp = new PreseleccionAreaformaciontemp();
			$preseleccion_areaformaciontemp -> registro = $registro;
			$preseleccion_areaformaciontemp -> periodo = $this -> actual;
			$preseleccion_areaformaciontemp -> areadeformacion_id = $idareadeformacion;
			$preseleccion_areaformaciontemp -> create();
			
			$intersemestralesEstadistica = new IntersemestralesEstadistica();
			$intersemestralesEstadistica -> registro = $registro;
			$intersemestralesEstadistica -> periodo = $this -> actual;
			$intersemestralesEstadistica -> respuesta = $intersemestral;
			$intersemestralesEstadistica -> create();
			
			$this->redirect('npreseleccion/captura/');
		} // function guardandoAreaDeFormacion()
		
		function guardandoPreguntaIntersemestral(){
			$this -> valida();
			$registro = Session::get_data('registro');
			
			$intersemestral = $this -> post("intersemestral");
			
			$intersemestralesEstadistica = new IntersemestralesEstadistica();
			$intersemestralesEstadistica -> registro = $registro;
			$intersemestralesEstadistica -> periodo = $this -> actual;
			$intersemestralesEstadistica -> respuesta = $intersemestral;
			$intersemestralesEstadistica -> create();
			
			$this->redirect('npreseleccion/captura/');
		} // function guardandoPreguntaIntersemestral()
		
		function valida(){
			if( Session::get_data("tipousuario") != "ALUMNO" ){
				$this -> redirect("seguridad/terminarsesion");
				return;
			}
			$preTerminada	= new PreseleccionTerminada();
			
			$registro = Session::get_data('registro');
			
			// Si ya la realizó no podrá volverla a hacer...
			if( $preTerminada -> find_first( "registro = ".$registro." and periodo = ".$this -> actual ) )
				$this->redirect('npreseleccion/terminarPreseleccion');
		} // function valida()
	}
?>