<?php
	
	class AlumnoseleccController extends ApplicationController {
		
		public $antesanteriorselecc	= 12012;
		public $anteriorselecc		= 32012;
		public $actualselecc		= 12013;
		public $proximoselecc		= 32013;
		
		function index(){
			
			$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			
			$usuario = $usuarios -> find_first("registro='".$registro."'");
			$this -> registro = $registro;
			
			//$xalumnos = new Xalumnos();
			//$xalumno = $xalumnos -> find_first("registro=".$registro);
			//if($xalumno -> nombre == ""){
			//	$this -> redirect("alumno/actualizacion");
			//}
			// Si el usuario aun no cambia su password, se manda a alumnos/index, es decir
			// si el usuario aun tiene su registro como password...
			//if($usuario -> clave == $registro){
				//$this->redirect('alumnos/index');
			//}
			
			// Si el usuario no es un alumno, no podrá ingresar...
			$periodo = $this -> actualselecc;
			$this -> valida();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> inicio);
			unset($this -> fin);
			
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> materia);
			unset($this -> calificacion);
			unset($this -> pinicial);
			unset($this -> kardex);
			unset($this -> creditos);
			unset($this -> ncreditos);
			unset($this -> periodos);
			unset($this -> pendientes);
			unset($this -> pmaterias);
			unset($this -> matelocas);
			unset($this -> matitas);
			
			$alumnos = new Alumnos();
			$xpkardex = new Xpkardex();
			$especialidades = new Especialidades();
			$materias = new Materia();
			
			if( substr($this -> actualselecc,0,1) == 1)
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($this -> actualselecc,1,4);
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$registro." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
			if($di<10) $di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".($hf-1).":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this -> acceso = 0;
			}
			
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$this -> render("index_seleccion");
			//$this -> render("no_seleccion");
		} // function index()
		
		function get_prerrequisito($clave, $registro){
			// $this -> valida();
			
			$alumnos = new Alumnos();
			$materia = new Materia();
			
			$alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> set_response('view');
			
			unset($this -> prerrequisitos);
			unset($this -> matt);
			
			$this -> matt = $materia -> find_first("clave = '".$clave."' and carrera_id = ".$alumno -> carrera_id);
			
			$this -> prerrequisitos = $materia -> get_prerrequisitos($clave, $alumno);
			
			$this -> render_partial("prerrequisitos");
		} // function get_prerrequisito($clave, $registro)
		
		function escogerareaformacion($registro){
			unset($this -> posiblesAreasDeFormacion);
			unset($this -> arrayConMateriasDeSuArea);
			
			$areadeformacion	= new Areadeformacion();
			$materia			= new Materia();
			$alumnos			= new Alumnos();
			$kardexIng			= new KardexIng();
			
			$datosAlumno = $alumnos -> get_relevant_info_from_student($registro);
			
			if( $datosAlumno -> areadeformacion_id != 0 ){
				return false;
			}
			if( $datosAlumno -> carrera_id >= 6 && $datosAlumno -> areadeformacion_id == 0 ){
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
			}
			else if( $datosAlumno -> areadeformacion_id == 0 ){
				if( $datosAlumno -> carrera_id == 4 || $datosAlumno -> carrera_id == 1 ){
					$query_string =
						"select * from kardex_ing k, materia m
						where k.registro = ".$datosAlumno -> miReg."
						and k.clavemat = m.clave
						and m.carrera_id = ".$datosAlumno -> carrera_id."
						limit 1";
				}
				else{
					$query_string =
						"select * from kardex_ing k, materia m
						where k.registro = ".$datosAlumno -> miReg."
						and k.clavemat = m.clave
						and m.carrera_id = ".$datosAlumno -> carrera_id."
						limit 1";
				}
			}
			foreach( $kardexIng -> find_all_by_sql($query_string) as $kardex ){
				$mostrarAreasDeFormacion = 1;
			}
			if( $mostrarAreasDeFormacion == 1 ){
				$areasdeformacion = $areadeformacion -> find("carrera_id = ".$datosAlumno -> carrera_id);
				$areas = 0;
				foreach( $areasdeformacion as $area ){
					$i = 0;
					foreach( $materia -> find("carrera_id = '".$datosAlumno -> carrera_id."'
							and serie = '".$area -> idareadeformacion."'") as $mat ){
						$this -> arrayConMateriasDeSuArea[$areas][$i] = $mat -> nombre;
						$i++;
					}
					$this -> posiblesAreasDeFormacion[$areas] = $area -> nombreareaformacion;
					$areas++;
				}
				$this -> escogerareaformacion = 1;
				return true;
			}
			return false;
			
		} // function escogerareaformacion()
		
		function guardandoAreaDeFormacion(){
			$this -> valida();
			$registro = $this->obtener_registro();
			//$registro = Session::get_data('registro');
			
			$idareadeformacion = $this -> post("idareadeformacion");
			$intersemestral = $this -> post("intersemestral");
			
			
			$alumnos		= new Alumnos();
			$logcambiosesp	= new Logcambiosespecialidades();
			
			$al = $alumnos -> find_first("miReg = ".$registro);
			$al -> areadeformacion_id = $idareadeformacion;
			if( $al -> correo == "" || $al -> correo == null )
				$al -> correo = "0";
			if( $al -> stSit == "" || $al -> stSit == null )
				$al -> stSit = "OK";
			if( $al -> situacion == "" || $al -> situacion == null )
				$al -> situacion = "-";
			$al -> save();
			
			$logcambiosesp -> nomina = $registro;
			$logcambiosesp -> registro = $registro;
			$logcambiosesp -> carrera_id = $al -> carrera_id;
			$logcambiosesp -> areadeformacion_id = $idareadeformacion;
			$year = date ("Y"); $month = date ("m"); $day = date ("d");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$logcambiosesp -> fecha = mktime($hour, $minute, $second, $month, $day, $year);
			$logcambiosesp -> create();
			
			$this->redirect('alumnoselecc/seleccion1');
		} // function guardandoAreaDeFormacion()
		
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
		
		function comprobarSiEstaATiempo( $registro ){
			$tiempos = new SeleccionTiempo();
			$tiempo = $tiempos -> find_first("registro=".$registro." AND periodo=".$this -> actualselecc);
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
			if($di<10)
				$di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumnoselecc/index');
			}
		} // function comprobarSiEstaATiempo( $registro )
		
		function getMateriasQuePuedeSeleccionar($datosAlumno, $materiasEnKardex){
			//$this -> valida();
			$registro = Session::get_data('registro');
			
			$xpkardex		= new Xpkardex();
			$areadeform		= new Areadeformacion();
			$materias		= new Materia();
			$materias2		= new Materia();
			
			// Si el alumno no tiene asignada area de formacion, es porque aun no le toca seleccionar areadeformacion
			//ya que con anterioridad se le pidió que seleccionara areadeformacion, siempre y cuando contara con materias de
			//4 semestre, o en el caso de industrial de plan2007 es apartir del 3 semestre.
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
					if( $xpkardex -> find_first( "registro = ".$datosAlumno -> miReg."
							and materia = '".$materiasConEsp -> clave."' and id > 27315" ) )
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
								and m.clave = '".$materiasConEsp -> clave."'
								and (serie = '".$datosAlumno -> areadeformacion_id."'
								or serie = '-')") as $prerrequisito ){
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
					if( $xpkardex -> find_first( "registro = ".$datosAlumno -> miReg."
							and materia = '".$materiasSinEsp -> clave."' and id > 27315" ) )
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
								and m.clave = '".$materiasSinEsp -> clave."'
								and serie = '-'") as $prerrequisito ){
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
		
		function getMateriasQuePuedeSeleccionarPaso2($datosAlumno, $materiasEnKardex){
			//$this -> valida();
			$registro = Session::get_data('registro');
			
			$xpkardex		= new Xpkardex();
			$areadeform		= new Areadeformacion();
			$materias		= new Materia();
			$materias2		= new Materia();
			$seleccionAlumno= new SeleccionAlumno();
			
			// Si el alumno no tiene asignada area de formacion, es porque aun no le toca seleccionar areadeformacion
			//ya que con anterioridad se le pidió que seleccionara areadeformacion, siempre y cuando contara con materias de
			//4 semestre, o en el caso de industrial de plan2007 es apartir del 3 semestre.
			if( $datosAlumno -> areadeformacion_id != 0 ){
				//echo "areadeformacion: ".$datosAlumno -> areadeformacion_id."<br />";
				//echo "areadeformacion: ".$datosAlumno -> carrera_id."<br />";
				$area = $areadeform -> find_first("idareadeformacion = '".$datosAlumno -> areadeformacion_id."'
					and carrera_id = '".$datosAlumno -> carrera_id."'" );
				$this -> areadeformacion = $area -> nombreareaformacion;
				//echo $area -> nombreareaformacion."<br />";
				//echo "<br />";
				// $queryConMatEsp
				$query = "Select clave, nombre
					from materia
					where carrera_id = '".$datosAlumno -> carrera_id."'
					and (serie = '".$datosAlumno -> areadeformacion_id."'
					or serie = '-')";
				$i = 0;
				foreach( $materias -> find_all_by_sql( $query )
						as $materiasConEsp ){
					if( $xpkardex -> find_first( "registro = ".$datosAlumno -> miReg."
							and materia = '".$materiasConEsp -> clave."' and id > 27315" ) )
						continue;
					if( $seleccionAlumno -> find_first("registro = ".$datosAlumno -> miReg."
							and clavemateria = '".$materiasConEsp -> clave."'
								and periodo = ".$this -> actualselecc ) )
						continue;
						var_dump($materiasEnKardex);
					if( !in_array($materiasConEsp -> clave, $materiasEnKardex) ){
						//echo $materiasConEsp -> clave."<br />";
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
								and m.clave = '".$materiasConEsp -> clave."'
								and (serie = '".$datosAlumno -> areadeformacion_id."'
								or serie = '-')") as $prerrequisito ){
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
					if( $xpkardex -> find_first( "registro = ".$datosAlumno -> miReg."
							and materia = '".$materiasSinEsp -> clave."' and id > 27315" ) )
						continue;
					if( $seleccionAlumno -> find_first("registro = ".$datosAlumno -> miReg."
							and clavemateria = '".$materiasSinEsp -> clave."'
								and periodo = ".$this -> actualselecc ) )
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
								and m.clave = '".$materiasSinEsp -> clave."'
								and serie = '-'") as $prerrequisito ){
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
			// die;
		} // function getMateriasQuePuedeSeleccionarPaso2($datosAlumno)
		
		function getMateriasConPrerrequisitoCero($materiasEnKardex){
			$materia = new Materia();
			
			$temp = array();
			foreach( $this -> pendientes as $mat ){
				array_push($temp, $mat -> clave);
			}
			foreach( $materia -> find_all_by_sql(
					"Select clave, nombre
					from materia
					where carrera_id = ".$this -> alumno -> carrera_id."
					and prerrequisito = '0'
					and ( serie = '-'
					or serie = '".$this -> alumno -> areadeformacion_id."' )") as $m ){
				if( (!in_array($m -> clave, $materiasEnKardex)) &&
						(!in_array($m, $this -> materiasQuePuedeSeleccionar)) && 
							(!in_array($m -> clave, $this -> xkpardexSoloClave)) &&
								(!in_array($m -> clave, $temp)) ){
					array_push($this -> materiasQuePuedeSeleccionar, $m);
				}
			}
		} // function getMateriasConPrerrequisitoCero($materiasEnKardex)
		
		function valida_seleccion1_c(){
			if(Session::get_data('tipousuario')=="ALUMNO"){
				$this->redirect('alumnoselecc/seleccion2');
			}
		} // function $valida_seleccion1_c()
		
		function valida_seleccion1_t(){
			if(Session::get_data('tipousuario')=="ALUMNO"){
				$this->redirect('alumnoselecc/seleccion2T');
			}
		} // function $valida_seleccion1_t()
		
		function validar_registro_plantel(){
			if($this -> has_post('registro') || $this -> has_post('nombre_alumno')){
				$registro = $this->post('registro');
				$nombre = $this->post('nombre_alumno');
				// Si ingreso un registro, usar solo el registro y verificar que sea integer.
				if( $registro != "" && $registro != "Registro Alumno" )
					$query =  "miReg = '".$registro."'";
				else
					$query =  "vcNomAlu like '%".$nombre."%'";
				$Alumnos = new Alumnos();
				$this -> alumno = $Alumnos -> find_first($query);
				if( !isset($this -> alumno -> miReg) ){
					$this -> redirect("ingcalculo/seleccionar_materias/1");
					return;
				}
			}
			Session::set_data('registro_tmp', $registro);
			if($this->alumno->enPlantel =="C"){
				$this -> redirect("alumnoselecc/seleccion2");
			}
			else{
				$this -> redirect("alumnoselecc/seleccion2T");
			}
		} // function validar_registro_plantel()
		
		function obtener_registro(){
			if( Session::get_data('registro_tmp') != ""  && (!$this -> has_post('registro') && !$this -> has_post('nombre_alumno') ))
				return Session::get_data('registro_tmp');
		
			if($this -> has_post('registro') || $this -> has_post('nombre_alumno')){
				$registro = $this->post('registro');
				$nombre = $this->post('nombre_alumno');
				// Si ingreso un registro, usar solo el registro y verificar que sea integer.
				if( $registro != "" && $registro != "Registro Alumno" )
					$query =  "miReg = '".$registro."'";
				else
					$query =  "vcNomAlu like '%".$nombre."%'";
				$Alumnos = new Alumnos();
				$this -> alumno = $Alumnos -> find_first($query);
				if( !isset($this -> alumno -> miReg) ){
					$this -> redirect("ingcalculo/seleccionar_materias/1");
					return;
				}
			}
			else{
				$registro = Session::get_data('registro');
				// Checar si aún le toca subir materias
				$this -> comprobarSiEstaATiempo($registro);
			}
			
			Session::set_data('registro_tmp', $registro);
			return $registro;
		} // function obtener_registro()
		
		function seleccion1(){
			$this->redirect("alumnoselecc/seleccion2");
			return;
			
			$periodo = $this -> actualselecc;
			
			$this -> valida_seleccion1_c();
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> materiasEnXpkardex);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> periodos);
			unset($this -> xkpardexSoloClave);
			unset($this -> escogerareaformacion);
			
			$registro = $this->obtener_registro();
			
			//$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex_ing = new KardexIng();
			$materia = new Materia();
			$alumnos = new Alumnos();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			// Fin obtener promedio -------------------------------------------
			
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			$i = 0;
			$periodoInicial = $this -> alumno -> miPerIng;
			while( $periodoInicial != $this -> actualselecc ){
				$this -> periodos[$i] = $periodoInicial = $this -> incrementaPeriodoKardex($periodoInicial);
				$i++;
			}
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Obtener solo las claves de las materias de xpkardex
			$this -> xkpardexSoloClave = $xpkardex -> get_all_xpkardex_from_student_only_clave_($this -> alumno -> miReg);
			
			// Inicializamos el arreglo de materias, estas materias son las que puede escoger.
			$this -> materiasQuePuedeSeleccionar = array();
			// El arreglo de materiasQuePuedeSeleccionar se compondrá de los campos:
			// $this -> materiasQuePuedeSeleccionar -> clave
			// $this -> materiasQuePuedeSeleccionar -> nombre
			
			
			// Materias que puede escoger para esta selección.
			$this -> getMateriasQuePuedeSeleccionar($this -> alumno, $materiasEnKardex);
			// Agregar materias que tengan prerrequisito = 0 en el array de materiasQuePuedeSeleccionar
			$this -> getMateriasConPrerrequisitoCero($materiasEnKardex);
			
			// Checamos las materias que ya tiene en xpkardex
			$this -> materiasEnXpkardex = $xpkardex -> get_all_xpkardex_from_student($this -> alumno);
			
			// Checar cuántos créditos lleva en la selección
			//$this -> checarNumeroDeCreditos($datosAlumno -> carrera_id);
		} // function seleccion1()
		
		function seleccion2(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			$this -> valida_condicionado();
			
			$registro = $this->obtener_registro();
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> periodos);
			unset($this -> xkpardexSoloClave);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> pendientes);
			unset($this -> creditos);
			unset($this -> creditosSeleccion);
			unset($this -> escogerareaformacion);
			unset($this -> materiasEnXpkardex);
			unset($this -> todasLasMaterias);
			
			$SeleccionAlumno= new Seleccionalumno();
			$xpkardex		= new Xpkardex();
			$kardex_ing		= new KardexIng();
			$materia		= new Materia();
			$alumnos		= new Alumnos();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$i = 0;
			$periodoInicial = $this -> alumno -> miPerIng;
			while( $periodoInicial != $this -> actualselecc ){
				$this -> periodos[$i] = $periodoInicial = $this -> incrementaPeriodoKardex($periodoInicial);
				$i++;
			}
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Inicializamos el arreglo de materias, estas materias son las que puede escoger.
			$this -> materiasQuePuedeSeleccionar = array();
			// El arreglo de materiasQuePuedeSeleccionar se compondrá de los campos:
			// $this -> materiasQuePuedeSeleccionar -> clave
			// $this -> materiasQuePuedeSeleccionar -> nombre
			
			// Tomamos las materias que haya ingresado en xpkardex como si las hubiera acreditado
			$this -> xkpardexSoloClave = $materiasEnKardex = $xpkardex -> get_all_xpkardex_from_student_only_clave($materiasEnKardex, $this -> alumno -> miReg);
			// Materias que puede escoger para esta selección.
			$this -> getMateriasQuePuedeSeleccionarPaso2($this -> alumno, $materiasEnKardex);
			
			// Obtener las materias que lleva en la selección
			$this -> pendientes = $SeleccionAlumno -> get_materias_seleccionAlumno_actual($this -> actualselecc, $this -> alumno);
			
			// Agregar materias que tengan prerrequisito = 0 en el array de materiasQuePuedeSeleccionar
			$this -> getMateriasConPrerrequisitoCero($materiasEnKardex);
			
			// Checamos las materias que ya tiene en xpkardex
			$this -> materiasEnXpkardex = $xpkardex -> get_all_xpkardex_from_student($this -> alumno);
		
			// Obtener los créditos que lleva para la actual selección
			$this -> creditosSeleccion = $SeleccionAlumno -> get_creditos_seleccionAlumno_actual($this -> actualselecc, $this -> alumno);
			if( $this -> creditosSeleccion == null )
				$this -> creditosSeleccion = 0;
			
			// Traer materias que tenga reprobas en xextraordinarios del semestre pasado
			$this -> materiasQuePuedeSeleccionar = $materia -> get_materiasReprobadas_semPasado
				($this -> alumno, $materiasEnKardex, $this -> materiasQuePuedeSeleccionar, $this -> xkpardexSoloClave);
			
			// Traer todas las materias, para poder enseñar al alumno los prerrequisitos....
			$this -> todasLasMaterias = $materia -> get_all_giving_careerID($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
		} // function seleccion2()
		
		function seleccion3( $error = 0 ){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			$this -> valida_condicionado();
			
			$registro = Session::get_data('registro_tmp');
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			unset($this -> irregulares);
			unset($this -> ocupado1);
			unset($this -> pendientesIrregulares);
			unset($this -> grupos2);
			unset($this -> maestros2);
			unset($this -> horas2);
			unset($this -> salon2);
			unset($this -> seleccAl);
			unset($this -> materiasIrregulares);
			unset($this -> seleccAlNombre);
			unset($this -> mIrregNombre);
			unset($this -> escogerareaformacion);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xalumnocursos();
			$materias		= new Materia();
			$materias2		= new Materia();
			$xccursos		= new Xccursos();
			$xcsalones		= new Xcsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xchorascursos();
			$kardex_ing		= new KardexIng();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Materias que seleccionó el alumno en el paso2.
			$this -> seleccAl = $seleccAlumno -> find( "registro = ".$registro." and periodo = ".$periodo );
			
			foreach( $this -> seleccAl as $selAl ){
				foreach( $materias -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$selAl -> clavemateria."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> seleccAlNombre[$selAl -> clavemateria]	= $nombre;
				}
			}
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xal.*, xcc.materia as materia
							From xalumnocursos xal, xccursos xcc
							where xal.registro = ".$registro."
							and xal.periodo = ".$periodo."
							and xal.curso_id = xcc.id" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			
			//////////////// Checar si tiene materias irregulares /////////////
			// La función regresa un arreglo con las materias que tiene arrastrando
			//se verifican 2 periodos anteriores al actual.
			// Si la selección es para 32010, se verifican 12010 y 32009.
			$this -> materiasIrregulares = $this -> materiasIrregularesParaSelecc($registro);
			foreach( $this -> materiasIrregulares as $mIrreg ){
				foreach( $materias2 -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$mIrreg."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> mIrregNombre[$mIrreg] = $nombre -> nombre;
				}
			}
			///////////////////////////////////////////////////////////////////
			/*
			echo count($materiasIrregulares)."<br />";
			echo $materiasIrregulares[0]."<br />";
			echo $materiasIrregulares[1]."<br />";
			exit(1);
			*/
			$i = 0;
			foreach( $this -> seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> materia == $selAl -> clavemateria )
						$aux++;
				}
				foreach( $this -> materiasIrregulares as $mIrregulares ){
					if( $mIrregulares == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			$xccursoos	= new Xccursos();
			$xkpkardexx	= new Xpkardex();
			$xextrax	= new Xextraordinarios();
			$xall		= new xalumnocursos();
			$kingg		= new KardexIng();
			$i = 0;
			// Aquí es donde decido si quito materias irregulares por diferentes situaciones...
			foreach( $this -> materiasIrregulares as $mIrregulares ){
				$aux = 0;
				// Si la materia que ya selecciono es de las que debe, ya no mostrarla.
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $mIrregulares == $matSeleccionadas -> materia )
						$aux++;
				}
				// Si la materia que está como irregular no se dió de alta
				//en el periodo en curso, no mostrarla.
				// También checar si es matutino o vespertino
				if( strtoupper($this -> alumno -> enTurno) == "V" ){
					if(!($xccursoos -> get_si_existe_materia_en_turno_vespertino($mIrregulares))){
						$aux++;
					}
				}
				else{
					if(!($xccursoos -> get_si_existe_materia_en_turno_matutino($mIrregulares))){
						$aux++;
					}
				}
				// Si la materia está como irregular la seleccionó en el paso 1
				//no mostrarla.
				if( $xkpkardexx -> find_first("materia = '".$mIrregulares."' and registro = ".$registro) ){
					$aux++;
				}
				// Si la materia que está como irregular ya está en su kardex
				//no mostrarla.
				if( $kingg -> find_first("clavemat = '".$mIrregulares."' and registro = ".$registro) ){
					$aux++;
				}
				// Si la materia que está como irregular si la curso en
				//extraordinario o titulo, no mostrarla. Se puede dar el caso
				//en que este acreditada como extraordinario y no estar
				//en su kardex.
				foreach( $xextrax -> find_all_by_sql("select xext.calificacion
									from xextraordinarios xext, xccursos xcc
									where xext.registro = ".$registro."
									and xext.periodo = ".$this -> anteriorselecc."
									and xext.calificacion >= 70
									and xext.calificacion <= 100
									and xext.curso_id = xcc.id
									and xcc.materia = '".$mIrregulares."'" ) as $xext ){
					$aux++;
				}
				// Solo materias que esten dadas de alta y esten visibles
				//para alumnos, si no, no se mostrará su materia de irregular.
				foreach( $xextrax -> find_all_by_sql("
						select xal.id From xalumnocursos xal, xccursos xcc
						where xal.registro = ".$registro."
						and xal.curso_id = xcc.id
						and xcc.periodo = ".$this -> actualselecc."
						and xcc.materia = '".$mIrregulares."'
						and xcc.activo = 1" ) as $xext ){
					$aux++;
				}
				echo "<br />".$aux;
				// Checar si la materia irregular la acreditó en el periodo anterior.
				foreach( $xall -> find_all_by_sql("select xal.calificacion
									from xalumnocursos xal, xccursos xcc
									where xal.registro = ".$registro."
									and xal.periodo = ".$this -> anteriorselecc."
									and xal.calificacion >= 70
									and xal.calificacion <= 100
									and xal.curso_id = xcc.id
									and xcc.materia = '".$mIrregulares."'
									limit 1") as $xalll ){
					$aux++;
				}
				// Checar si esa materia corresponde a su plan de
				//estudios actual. Ya que puede darse el caso
				//en que debía una materia del plan2000
				//y luego se hizo su cambió al plan2007.
				$aux2 = 0;
				foreach( $xall -> find_all_by_sql("
						select plan from materia
						where clave = '".$mIrregulares."'
						and plan = 200".substr($this -> alumno -> enPlan, 3, 2).".
						limit 1") as $xalll ){
					$aux2++;
				}
				if( $aux2 == 0 ){
					$aux++;
				}
				// Si aux sigue en 0, significa que esa materia aún la debe
				//por lo tanto deberá seleccionarla antes de empezar a
				//seleccionar sus materias.
				if( $aux == 0 ){
					$this -> pendientesIrregulares[$i] = $mIrregulares;
					$i++;
				}
			}
			//
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> get_materias_semestre_actual($registro);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
				
					$xccurso = $xccursos -> find_first("id = '".$sel -> curso_id."'");
					
					$this -> seleccionados[$xccurso -> clavecurso] = $xccurso;
					
					$this -> maestros[$xccurso -> clavecurso] = $this -> nombreProfesor($xccurso -> nomina);
					
					$this -> materiales[$this -> seleccionados[$xccurso -> clavecurso] -> materia] = 
							$this -> sacarMateria($this -> seleccionados[$xccurso -> clavecurso] -> materia);
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccurso -> id][$j] = $xchora;
						$this -> salon[$xccurso -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xccurso -> clavecurso;
					}
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			
			//////////////////////// Materias Regulares ///////////////////////
			$i = 0;
			if( isSet($this -> pendientes) )
			foreach( $this -> pendientes as $pend ){
				// $xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				if( strtoupper($this -> alumno -> enTurno) == "V" )
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_vespertino($pend -> clavemateria);
				else
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_matutino($pend -> clavemateria);
				//$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					//Select * from xccursos xc
					//where materia = '".$pend -> clavemateria."'
					//and periodo = ".$periodo."
					//and activo = 1
					//" );
				$i++;
			}
			$i = 0;
			if( isSet($this -> grupos) ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Regulares //////////////////////////
			//////////////////////// Materias Irregulares ///////////////////////
			$i = 0;
			foreach( $this -> pendientesIrregulares as $pendIrr ){
//				$xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				$this -> grupos2[$i] = $xccursos -> find_all_by_sql( "
					Select * from xccursos xc
					where materia = '".$pendIrr."'
					and periodo = ".$periodo."
					and activo = 1
					" );
				$i++;
			}
			$i = 0;
			if( $this -> grupos2 ) foreach( $this -> grupos2 as $tmp ){
				foreach( $tmp as $gpo ){
					$this -> maestros2[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros2[$gpo -> nomina]==""){
						$this -> maestros2[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas2[$gpo -> id][$j] = $xchora;
						$this -> salon2[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Irregulares //////////////////////////
			
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
			
		} // function seleccion3($error=0)
		
		function seleccion_condicionados($error = 0){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro');
			
			// Checar si aún le toca subir materias
			$this -> comprobarSiEstaATiempo($registro);
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			unset($this -> irregulares);
			unset($this -> ocupado1);
			unset($this -> pendientesIrregulares);
			unset($this -> grupos2);
			unset($this -> maestros2);
			unset($this -> horas2);
			unset($this -> salon2);
			unset($this -> seleccAl);
			unset($this -> materiasIrregulares);
			unset($this -> seleccAlNombre);
			unset($this -> mIrregNombre);
			unset($this -> escogerareaformacion);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xalumnocursos();
			$materias		= new Materia();
			$materias2		= new Materia();
			$xccursos		= new Xccursos();
			$xcsalones		= new Xcsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xchorascursos();
			$kardex_ing		= new KardexIng();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Materias que debe seleccionar por ser condicionado.
			$this -> seleccAl = $seleccAlumno -> find( "registro = ".$registro." and periodo = ".$periodo );
			
			foreach( $this -> seleccAl as $selAl ){
				foreach( $materias -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$selAl -> clavemateria."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> seleccAlNombre[$selAl -> clavemateria]	= $nombre;
				}
			}
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xal.*, xcc.materia as materia
							From xalumnocursos xal, xccursos xcc
							where xal.registro = ".$registro."
							and xal.periodo = ".$periodo."
							and xal.curso_id = xcc.id" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			
			$i = 0;
			foreach( $this -> seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> materia == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			$xccursoos	= new Xccursos();
			$xkpkardexx	= new Xpkardex();
			$xextrax	= new Xextraordinarios();
			$xall		= new xalumnocursos();
			$kingg		= new KardexIng();
			$i = 0;
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> get_materias_semestre_actual($registro);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
				
					$xccurso = $xccursos -> find_first("id = '".$sel -> curso_id."'");
					
					$this -> seleccionados[$xccurso -> clavecurso] = $xccurso;
					
					$this -> maestros[$xccurso -> clavecurso] = $this -> nombreProfesor($xccurso -> nomina);
					
					$this -> materiales[$this -> seleccionados[$xccurso -> clavecurso] -> materia] = 
							$this -> sacarMateria($this -> seleccionados[$xccurso -> clavecurso] -> materia);
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccurso -> id][$j] = $xchora;
						$this -> salon[$xccurso -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xccurso -> clavecurso;
					}
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			
			//////////////////////// Materias Regulares ///////////////////////
			$i = 0;
			if( isSet($this -> pendientes) )
			foreach( $this -> pendientes as $pend ){
				// $xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				if( strtoupper($this -> alumno -> enTurno) == "V" )
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_vespertino($pend -> clavemateria);
				else
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_matutino($pend -> clavemateria);
				//$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					//Select * from xccursos xc
					//where materia = '".$pend -> clavemateria."'
					//and periodo = ".$periodo."
					//and activo = 1
					//" );
				$i++;
			}
			$i = 0;
			if( isSet($this -> grupos) ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Regulares //////////////////////////
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
			
		} // function seleccion_condicionados($error = 0)
		
		function pseleccion(){
			
			$periodo = $this -> actualselecc;
			$this -> valida();
			
			$id = Session::get_data('registro_tmp');
		
			$seleccion = new Seleccionalumno();
			
			$seleccion -> registro = $id;
			$seleccion -> clavemateria = $this -> post("materia");
			$seleccion -> periodo = $periodo;
			$seleccion -> save();
			
			$this->redirect('alumnoselecc/seleccion2');
		} // function pseleccion()
		
		function pkardex(){
			
			$periodo = $this -> actualselecc;
			$this -> valida();
			
			$id = Session::get_data('registro');
			
			$xpkardex = new Xpkardex();
			
			$xpkardex -> registro = $id;
			$xpkardex -> materia = $this -> post("materia");
			$xpkardex -> periodo = $this -> post("periodo");
			$xpkardex -> tipo = $this -> post("tipo");
			
			$xpkardex -> promedio = '0';
	
			$xpkardex -> save();
			
			$this->redirect('alumnoselecc/seleccion1');
		} // function pkardex()
		
		function epkardex(){
			$periodo = $this -> actualselecc;
			$this -> valida();

			$xpkardex = new Xpkardex();
			
			$tmp = $xpkardex -> find($this -> post("id"));
			$tmp -> delete();
			
			$this->redirect('alumnoselecc/seleccion1');
		} // function epkardex()
		
		function epseleccion(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();

			$id = Session::get_data('registro_tmp');
			$seleccion = new Seleccionalumno();
			
			$tmp = $seleccion -> find_first($this -> post("id"));
			
			$tmp -> delete();
			
			$this->redirect('alumnoselecc/seleccion2');
		} // function epseleccion()
		
		function deseleccionar(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = $this->post('registro');
			Session::set_data('registro_tmp', $registro);
		
			$xccursos		= new Xccursos();
			$xalumnocursos 	= new Xalumnocursos();
			
			$xccurso = $xccursos -> find_first( "clavecurso = '".$this -> post("clavecurso")."'" );
			
			$xalumnocurso = $xalumnocursos -> find_first
					( "registro = ".$registro.
							" and curso_id = '".$xccurso -> id."'".
								"and periodo = ".$periodo );
			$xalumnocurso -> delete();
			
			$xccurso -> disponibilidad += 1;
			$xccurso -> save();
			
			$Xalumnocuross_logalumnos = new XalumnocursosLogalumnos();
			$Xalumnocuross_logalumnos -> grupo_deseleccionado($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			$this->redirect('alumnoselecc/seleccion3');
		} // function deseleccionar()
		
		function seleccionar(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro_tmp');
			
			$xccursos 		= new Xccursos();
			$xalumnocursos	= new Xalumnocursos();
			$xchorascursos	= new Xchorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llega por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			// Obtener objeto alumno
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> get_relevant_info_from_student($registro);
			
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$registro." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion3/1' );
				return;
			}
			else{
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$registro." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "curso_id = '".
							$xalumnocurso -> curso_id."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "curso_id = '".$xccurso -> id."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora.
											" and presencial = 1") ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				$Xalumnocursos = new Xalumnocursos();
				$ocupado += $Xalumnocursos->get_horas_cruces_de_materias($registro, $periodo);
				$Xtalumnocursos = new Xtalumnocursos();
				$ocupado += $Xtalumnocursos->get_horas_cruces_de_materias($registro, $periodo);
				
				$horas_posibles_de_cruce = 2;
				if( $ocupado <= $horas_posibles_de_cruce || $alumno->chGpo == "**" ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					if( $xccurso -> disponibilidad == 0 )
						$xccurso -> disponibilidad = '0';
					$xccurso -> save();
					
				}
				else if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
						$autCruce -> registro == $registro &&
								$autCruce -> horasautorizadas >= $ocupado ){
					
					$xalumnocurso = new Xalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					if( $xccurso -> disponibilidad == 0 )
						$xccurso -> disponibilidad = '0';
					$xccurso -> save();
				}
				else if( $cursoscomunes -> find_first
						( "clavecurso1 = '".$xccurso -> clavecurso."'"."
							or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
					$xalumnocurso = new Xalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					if( $xccurso -> disponibilidad == 0 )
						$xccurso -> disponibilidad = '0';
					$xccurso -> save();
				}
				else{
					// El 2 es un mensaje de cruce de horario 
					$this -> redirect( 'alumnoselecc/seleccion3/2' );
					return;
				}
			}
			$Xalumnocuross_logalumnos = new XalumnocursosLogalumnos();
			$Xalumnocuross_logalumnos -> grupo_seleccionado_correctamente($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion3/7');
		} // function seleccionar()
		
		function seleccionar_condicionado(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro');
			
			$xccursos 		= new Xccursos();
			$xalumnocursos	= new Xalumnocursos();
			$xchorascursos	= new Xchorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llega por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$registro." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion_condicionados/1' );
				return;
			}
			else{
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$registro." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "curso_id = '".
							$xalumnocurso -> curso_id."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "curso_id = '".$xccurso -> id."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora.
											" and presencial = 1") ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				if( $ocupado <= 1 ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					if( $xccurso -> disponibilidad == 0 )
						$xccurso -> disponibilidad = '0';
					$xccurso -> save();
					
				}
				else{
					if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
							$autCruce -> registro == $registro &&
									$autCruce -> horasautorizadas >= $ocupado ){
						
						$xalumnocurso = new Xalumnocursos();
						$xalumnocurso -> registro = $registro;
						$xalumnocurso -> periodo = $periodo;
						
						$xalumnocurso -> curso_id = $xccurso -> id;
						$xalumnocurso -> faltas1 = '0';
						$xalumnocurso -> faltas2 = '0';
						$xalumnocurso -> faltas3 = '0';
						$xalumnocurso -> calificacion1 = 300;
						$xalumnocurso -> calificacion2 = 300;
						$xalumnocurso -> calificacion3 = 300;
						$xalumnocurso -> faltas = '0';
						$xalumnocurso -> calificacion = 300;
						$xalumnocurso -> situacion = "-";
						
						$xalumnocurso -> create();
						
						$xccurso -> disponibilidad -= 1;
						if( $xccurso -> disponibilidad == 0 )
							$xccurso -> disponibilidad = '0';
						$xccurso -> save();
					}
					else{
						if( $cursoscomunes -> find_first
								( "clavecurso1 = '".$xccurso -> clavecurso."'"."
									or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
							$xalumnocurso = new Xalumnocursos();
							$xalumnocurso -> registro = $registro;
							$xalumnocurso -> periodo = $periodo;
							
							$xalumnocurso -> curso_id = $xccurso -> id;
							$xalumnocurso -> faltas1 = '0';
							$xalumnocurso -> faltas2 = '0';
							$xalumnocurso -> faltas3 = '0';
							$xalumnocurso -> calificacion1 = 300;
							$xalumnocurso -> calificacion2 = 300;
							$xalumnocurso -> calificacion3 = 300;
							$xalumnocurso -> faltas = '0';
							$xalumnocurso -> calificacion = 300;
							$xalumnocurso -> situacion = "-";
							
							$xalumnocurso -> create();
							
							$xccurso -> disponibilidad -= 1;
							if( $xccurso -> disponibilidad == 0 )
								$xccurso -> disponibilidad = '0';
							$xccurso -> save();
						}
						else{
							// El 2 es un mensaje de cruce de horario 
							$this -> redirect( 'alumnoselecc/seleccion_condicionados/2' );
							return;
						}
					}
				}
			}
			$Xalumnocuross_logalumnos = new XalumnocursosLogalumnos();
			$Xalumnocuross_logalumnos -> grupo_seleccionado_correctamente($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion_condicionados/7');
		} // function seleccionar_condicionado()
		
		function seleccionar_condicionadot(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro');
			
			$xccursos 		= new Xtcursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xchorascursos	= new Xthorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llega por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$registro." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion_condicionadost/1' );
				return;
			}
			else{
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$registro." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "curso_id = '".
							$xalumnocurso -> curso_id."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "curso_id = '".$xccurso -> id."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora.
											" and presencial = 1") ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				if( $ocupado <= 1 ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xtalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					if( $xccurso -> disponibilidad == 0 )
						$xccurso -> disponibilidad = '0';
					$xccurso -> save();
					
				}
				else{
					if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
							$autCruce -> registro == $registro &&
									$autCruce -> horasautorizadas >= $ocupado ){
						
						$xalumnocurso = new Xtalumnocursos();
						$xalumnocurso -> registro = $registro;
						$xalumnocurso -> periodo = $periodo;
						
						$xalumnocurso -> curso_id = $xccurso -> id;
						$xalumnocurso -> faltas1 = '0';
						$xalumnocurso -> faltas2 = '0';
						$xalumnocurso -> faltas3 = '0';
						$xalumnocurso -> calificacion1 = 300;
						$xalumnocurso -> calificacion2 = 300;
						$xalumnocurso -> calificacion3 = 300;
						$xalumnocurso -> faltas = '0';
						$xalumnocurso -> calificacion = 300;
						$xalumnocurso -> situacion = "-";
						
						$xalumnocurso -> create();
						
						$xccurso -> disponibilidad -= 1;
						if( $xccurso -> disponibilidad == 0 )
							$xccurso -> disponibilidad = '0';
						$xccurso -> save();
					}
					else{
						if( $cursoscomunes -> find_first
								( "clavecurso1 = '".$xccurso -> clavecurso."'"."
									or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
							$xalumnocurso = new Xtalumnocursos();
							$xalumnocurso -> registro = $registro;
							$xalumnocurso -> periodo = $periodo;
							
							$xalumnocurso -> curso_id = $xccurso -> id;
							$xalumnocurso -> faltas1 = '0';
							$xalumnocurso -> faltas2 = '0';
							$xalumnocurso -> faltas3 = '0';
							$xalumnocurso -> calificacion1 = 300;
							$xalumnocurso -> calificacion2 = 300;
							$xalumnocurso -> calificacion3 = 300;
							$xalumnocurso -> faltas = '0';
							$xalumnocurso -> calificacion = 300;
							$xalumnocurso -> situacion = "-";
							
							$xalumnocurso -> create();
							
							$xccurso -> disponibilidad -= 1;
							if( $xccurso -> disponibilidad == 0 )
								$xccurso -> disponibilidad = '0';
							$xccurso -> save();
						}
						else{
							// El 2 es un mensaje de cruce de horario 
							$this -> redirect( 'alumnoselecc/seleccion_condicionadost/2' );
							return;
						}
					}
				}
			}
			$Xalumnocuross_logalumnos = new XtalumnocursosLogalumnos();
			$Xalumnocuross_logalumnos -> grupo_seleccionado_correctamente($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion_condicionadost/7');
		} // function seleccionar_condicionadot()
		
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		// Tonala
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		
		function seleccion1T(){
			$this->redirect("alumnoselecc/seleccion2T");
			return;
			$periodo = $this -> actualselecc;
			
			$this -> valida_seleccion1_t();
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> materiasEnXpkardex);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> periodos);
			unset($this -> xkpardexSoloClave);
			unset($this -> escogerareaformacion);
			
			$registro = $this->obtener_registro();
			
			//$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex_ing = new KardexIng();
			$materia = new Materia();
			$alumnos = new Alumnos();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			// Fin obtener promedio -------------------------------------------
			
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			$i = 0;
			$periodoInicial = $this -> alumno -> miPerIng;
			while( $periodoInicial != $this -> actualselecc ){
				$this -> periodos[$i] = $periodoInicial = $this -> incrementaPeriodoKardex($periodoInicial);
				$i++;
			}
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Obtener solo las claves de las materias de xpkardex
			$this -> xkpardexSoloClave = $xpkardex -> get_all_xpkardex_from_student_only_clave_($this -> alumno -> miReg);
			
			// Inicializamos el arreglo de materias, estas materias son las que puede escoger.
			$this -> materiasQuePuedeSeleccionar = array();
			// El arreglo de materiasQuePuedeSeleccionar se compondrá de los campos:
			// $this -> materiasQuePuedeSeleccionar -> clave
			// $this -> materiasQuePuedeSeleccionar -> nombre
			
			
			// Materias que puede escoger para esta selección.
			$this -> getMateriasQuePuedeSeleccionar($this -> alumno, $materiasEnKardex);
			// Agregar materias que tengan prerrequisito = 0 en el array de materiasQuePuedeSeleccionar
			$this -> getMateriasConPrerrequisitoCero($materiasEnKardex);
			
			// Checamos las materias que ya tiene en xpkardex
			$this -> materiasEnXpkardex = $xpkardex -> get_all_xpkardex_from_student($this -> alumno);
			
			// Checar cuántos créditos lleva en la selección
			//$this -> checarNumeroDeCreditos($datosAlumno -> carrera_id);
		} // function seleccion1T()
		
		function seleccion2T(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			$this -> valida_condicionado();
			
			$registro = $this->obtener_registro();
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> periodos);
			unset($this -> xkpardexSoloClave);
			unset($this -> materiasQuePuedeSeleccionar);
			unset($this -> pendientes);
			unset($this -> creditos);
			unset($this -> creditosSeleccion);
			unset($this -> escogerareaformacion);
			unset($this -> materiasEnXpkardex);
			unset($this -> todasLasMaterias);
			
			$SeleccionAlumno= new Seleccionalumno();
			$xpkardex		= new Xpkardex();
			$kardex_ing		= new KardexIng();
			$materia		= new Materia();
			$alumnos		= new Alumnos();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$i = 0;
			$periodoInicial = $this -> alumno -> miPerIng;
			while( $periodoInicial != $this -> actualselecc ){
				$this -> periodos[$i] = $periodoInicial = $this -> incrementaPeriodoKardex($periodoInicial);
				$i++;
			}
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Inicializamos el arreglo de materias, estas materias son las que puede escoger.
			$this -> materiasQuePuedeSeleccionar = array();
			// El arreglo de materiasQuePuedeSeleccionar se compondrá de los campos:
			// $this -> materiasQuePuedeSeleccionar -> clave
			// $this -> materiasQuePuedeSeleccionar -> nombre
			
			// Tomamos las materias que haya ingresado en xpkardex como si las hubiera acreditado
			$this -> xkpardexSoloClave = $materiasEnKardex = $xpkardex -> get_all_xpkardex_from_student_only_clave($materiasEnKardex, $this -> alumno -> miReg);
			// Materias que puede escoger para esta selección.
			$this -> getMateriasQuePuedeSeleccionarPaso2($this -> alumno, $materiasEnKardex);
			
			// Obtener las materias que lleva en la selección
			$this -> pendientes = $SeleccionAlumno -> get_materias_seleccionAlumno_actual($this -> actualselecc, $this -> alumno);
			
			// Agregar materias que tengan prerrequisito = 0 en el array de materiasQuePuedeSeleccionar
			$this -> getMateriasConPrerrequisitoCero($materiasEnKardex);
			
			// Checamos las materias que ya tiene en xpkardex
			$this -> materiasEnXpkardex = $xpkardex -> get_all_xpkardex_from_student($this -> alumno);
			
			// Obtener los créditos que lleva para la actual selección
			$this -> creditosSeleccion = $SeleccionAlumno -> get_creditos_seleccionAlumno_actual($this -> actualselecc, $this -> alumno);
			if( $this -> creditosSeleccion == null )
				$this -> creditosSeleccion = 0;
			
			// Traer materias que tenga reprobas en xextraordinarios del semestre pasado
			$this -> materiasQuePuedeSeleccionar = $materia -> get_materiasReprobadas_semPasado
				($this -> alumno, $materiasEnKardex, $this -> materiasQuePuedeSeleccionar, $this -> xkpardexSoloClave);
			
			// Traer todas las materias, para poder enseñar al alumno los prerrequisitos....
			$this -> todasLasMaterias = $materia -> get_all_giving_careerID($this -> alumno -> carrera_id);
		} // function seleccion2T()
		
		function seleccion3T( $error = 0 ){
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			$this -> valida();
			$this -> valida_condicionado();
			
			$registro = $this->obtener_registro();
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			unset($this -> irregulares);
			unset($this -> ocupado1);
			unset($this -> pendientesIrregulares);
			unset($this -> grupos2);
			unset($this -> maestros2);
			unset($this -> horas2);
			unset($this -> salon2);
			unset($this -> seleccAl);
			unset($this -> materiasIrregulares);
			unset($this -> seleccAlNombre);
			unset($this -> mIrregNombre);
			unset($this -> escogerareaformacion);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xtalumnocursos();
			$materias		= new Materia();
			$materias2		= new Materia();
			$xccursos		= new Xtcursos();
			$xcsalones		= new Xtsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xthorascursos();
			$kardex_ing		= new KardexIng();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Materias que seleccionó el alumno en el paso2.
			$this -> seleccAl = $seleccAlumno -> find( "registro = ".$registro." and periodo = ".$periodo );
			
			foreach( $this -> seleccAl as $selAl ){
				foreach( $materias -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$selAl -> clavemateria."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> seleccAlNombre[$selAl -> clavemateria]	= $nombre;
				}
			}
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xal.*, xcc.materia as materia
							From xtalumnocursos xal, xtcursos xcc
							where xal.registro = ".$registro."
							and xal.periodo = ".$periodo."
							and xal.curso_id = xcc.id" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			
			//////////////// Checar si tiene materias irregulares /////////////
			// La función regresa un arreglo con las materias que tiene arrastrando
			//se verifican 2 periodos anteriores al actual.
			// Si la selección es para 32010, se verifican 12010 y 32009.
			$this -> materiasIrregulares = $this -> materiasIrregularesParaSelecc($registro);
			foreach( $this -> materiasIrregulares as $mIrreg ){
				foreach( $materias2 -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$mIrreg."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> mIrregNombre[$mIrreg] = $nombre -> nombre;
				}
			}
			///////////////////////////////////////////////////////////////////
			/*
			echo count($materiasIrregulares)."<br />";
			echo $materiasIrregulares[0]."<br />";
			echo $materiasIrregulares[1]."<br />";
			exit(1);
			*/
			$i = 0;
			foreach( $this -> seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> materia == $selAl -> clavemateria )
						$aux++;
				}
				foreach( $this -> materiasIrregulares as $mIrregulares ){
					if( $mIrregulares == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			$xccursoos	= new Xtcursos();
			$xkpkardexx	= new Xpkardex();
			$xextrax	= new Xtextraordinarios();
			$xall		= new xtalumnocursos();
			$kingg		= new KardexIng();
			$i = 0;
			// Aquí es donde decido si quito materias irregulares por diferentes situaciones...
			foreach( $this -> materiasIrregulares as $mIrregulares ){
				$aux = 0;
				// Si la materia que ya selecciono es de las que debe, ya no mostrarla.
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $mIrregulares == $matSeleccionadas -> materia )
						$aux++;
				}
				// Si la materia que está como irregular no se dió de alta
				//en el periodo en curso, no mostrarla.
				// También checar si es matutino o vespertino
				if( strtoupper($this -> alumno -> enTurno) == "V" ){
					if(!($xccursoos -> get_si_existe_materia_en_turno_vespertino($mIrregulares))){
						$aux++;
					}
				}
				else{
					if(!($xccursoos -> get_si_existe_materia_en_turno_matutino($mIrregulares))){
						$aux++;
					}
				}
				// Si la materia está como irregular la seleccionó en el paso 1
				//no mostrarla.
				if( $xkpkardexx -> find_first("materia = '".$mIrregulares."' and registro = ".$registro) ){
					$aux++;
				}
				// Si la materia que está como irregular ya está en su kardex
				//no mostrarla.
				if( $kingg -> find_first("clavemat = '".$mIrregulares."' and registro = ".$registro) ){
					$aux++;
				}
				// Si la materia que está como irregular si la curso en
				//extraordinario o titulo, no mostrarla. Se puede dar el caso
				//en que este acreditada como extraordinario y no estar
				//en su kardex.
				foreach( $xextrax -> find_all_by_sql("select xext.calificacion
									from xtextraordinarios xext, xtcursos xcc
									where xext.registro = ".$registro."
									and xext.periodo = ".$this -> anteriorselecc."
									and xext.calificacion >= 70
									and xext.calificacion <= 100
									and xext.curso_id = xcc.id
									and xcc.materia = '".$mIrregulares."'" ) as $xext ){
					$aux++;
				}
				// Solo materias que esten dadas de alta y esten visibles
				//para alumnos, si no, no se mostrará su materia de irregular.
				foreach( $xextrax -> find_all_by_sql("
						select xal.id From xtalumnocursos xal, xtcursos xcc
						where xal.registro = ".$registro."
						and xal.curso_id = xcc.id
						and xcc.periodo = ".$this -> actualselecc."
						and xcc.materia = '".$mIrregulares."'
						and xcc.activo = 1" ) as $xext ){
					$aux++;
				}
				echo "<br />".$aux;
				// Checar si la materia irregular la acreditó en el periodo anterior.
				foreach( $xall -> find_all_by_sql("select xal.calificacion
									from xtalumnocursos xal, xtcursos xcc
									where xal.registro = ".$registro."
									and xal.periodo = ".$this -> anteriorselecc."
									and xal.calificacion >= 70
									and xal.calificacion <= 100
									and xal.curso_id = xcc.id
									and xcc.materia = '".$mIrregulares."'
									limit 1") as $xalll ){
					$aux++;
				}
				// Checar si esa materia corresponde a su plan de
				//estudios actual. Ya que puede darse el caso
				//en que debía una materia del plan2000
				//y luego se hizo su cambió al plan2007.
				$aux2 = 0;
				foreach( $xall -> find_all_by_sql("
						select plan from materia
						where clave = '".$mIrregulares."'
						and plan = 200".substr($this -> alumno -> enPlan, 3, 2).".
						limit 1") as $xalll ){
					$aux2++;
				}
				if( $aux2 == 0 ){
					$aux++;
				}
				// Si aux sigue en 0, significa que esa materia aún la debe
				//por lo tanto deberá seleccionarla antes de empezar a
				//seleccionar sus materias.
				if( $aux == 0 ){
					$this -> pendientesIrregulares[$i] = $mIrregulares;
					$i++;
				}
			}
			//
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> get_materias_semestre_actual($registro);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
				
					$xccurso = $xccursos -> find_first("id = '".$sel -> curso_id."'");
					
					$this -> seleccionados[$xccurso -> clavecurso] = $xccurso;
					
					$this -> maestros[$xccurso -> clavecurso] = $this -> nombreProfesor($xccurso -> nomina);
					
					$this -> materiales[$this -> seleccionados[$xccurso -> clavecurso] -> materia] = 
							$this -> sacarMateria($this -> seleccionados[$xccurso -> clavecurso] -> materia);
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccurso -> id][$j] = $xchora;
						$this -> salon[$xccurso -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xccurso -> clavecurso;
					}
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			
			//////////////////////// Materias Regulares ///////////////////////
			$i = 0;
			if( isSet($this -> pendientes) )
			foreach( $this -> pendientes as $pend ){
				// $xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				if( strtoupper($this -> alumno -> enTurno) == "V" )
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_vespertino($pend -> clavemateria);
				else
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_matutino($pend -> clavemateria);
				//$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					//Select * from xccursos xc
					//where materia = '".$pend -> clavemateria."'
					//and periodo = ".$periodo."
					//and activo = 1
					//" );
				$i++;
			}
			$i = 0;
			if( isSet($this -> grupos) ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Regulares //////////////////////////
			//////////////////////// Materias Irregulares ///////////////////////
			$i = 0;
			foreach( $this -> pendientesIrregulares as $pendIrr ){
//				$xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				$this -> grupos2[$i] = $xccursos -> find_all_by_sql( "
					Select * from xtcursos xc
					where materia = '".$pendIrr."'
					and periodo = ".$periodo."
					and activo = 1
					" );
				$i++;
			}
			$i = 0;
			if( $this -> grupos2 ) foreach( $this -> grupos2 as $tmp ){
				foreach( $tmp as $gpo ){
					$this -> maestros2[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros2[$gpo -> nomina]==""){
						$this -> maestros2[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas2[$gpo -> id][$j] = $xchora;
						$this -> salon2[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Irregulares //////////////////////////
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
		} // function seleccion3T($error=0)
		
		function seleccion_condicionadost($error = 0){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro');
			
			// Checar si aún le toca subir materias
			$this -> comprobarSiEstaATiempo($registro);
			
			unset($this -> alumno);
			unset($this -> career);
			unset($this -> creditos);
			unset($this -> promedio);
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			unset($this -> irregulares);
			unset($this -> ocupado1);
			unset($this -> pendientesIrregulares);
			unset($this -> grupos2);
			unset($this -> maestros2);
			unset($this -> horas2);
			unset($this -> salon2);
			unset($this -> seleccAl);
			unset($this -> materiasIrregulares);
			unset($this -> seleccAlNombre);
			unset($this -> mIrregNombre);
			unset($this -> escogerareaformacion);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xtalumnocursos();
			$materias		= new Materia();
			$materias2		= new Materia();
			$xccursos		= new Xtcursos();
			$xcsalones		= new Xtsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xthorascursos();
			$kardex_ing		= new KardexIng();
			
			$this -> alumno = $alumnos -> get_relevant_info_from_student($registro);
			$this -> career = $alumnos -> get_careername_from_student($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			// Obtiene todas las materias que el alumno tiene en el kardex
			$materiasEnKardex = $kardex_ing -> get_all_kardex_from_student_only_clave($this -> alumno -> miReg);
			// Checar cuántos créditos tiene en el kardex
			$this -> creditos = $kardex_ing -> sumarCreditosDelKardex($this -> alumno);
			// Obtener promedio -----------------------------------------------
			$this -> promedio = $kardex_ing -> get_average_from_kardex($this -> alumno -> miReg);
			if( $this -> promedio > 10 )
				$cuanto = 5;
			else
				$cuanto = 4;
			$this -> promedio = substr( $this -> promedio, 0, $cuanto );
			
			$this -> escogerareaformacion = 2;
			// validar si aun no tiene areadeformacion
			// Si aún no tiene areadeformacion mandarlo a que escoja una.
			if( $this -> escogerareaformacion($registro) ){
				return;
			}
			
			// Materias que debe seleccionar por ser condicionado.
			$this -> seleccAl = $seleccAlumno -> find( "registro = ".$registro." and periodo = ".$periodo );
			
			foreach( $this -> seleccAl as $selAl ){
				foreach( $materias -> find_all_by_sql
								("select nombre
								from materia
								where clave = '".$selAl -> clavemateria."'
								and carrera_id = ".$this -> alumno -> carrera_id) as $nombre ){
					$this -> seleccAlNombre[$selAl -> clavemateria]	= $nombre;
				}
			}
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xal.*, xcc.materia as materia
							From xtalumnocursos xal, xtcursos xcc
							where xal.registro = ".$registro."
							and xal.periodo = ".$periodo."
							and xal.curso_id = xcc.id" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			
			$i = 0;
			foreach( $this -> seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> materia == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			$xccursoos	= new Xtcursos();
			$xkpkardexx	= new Xpkardex();
			$xextrax	= new Xtextraordinarios();
			$xall		= new xtalumnocursos();
			$kingg		= new KardexIng();
			$i = 0;
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> get_materias_semestre_actual($registro);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
				
					$xccurso = $xccursos -> find_first("id = '".$sel -> curso_id."'");
					
					$this -> seleccionados[$xccurso -> clavecurso] = $xccurso;
					
					$this -> maestros[$xccurso -> clavecurso] = $this -> nombreProfesor($xccurso -> nomina);
					
					$this -> materiales[$this -> seleccionados[$xccurso -> clavecurso] -> materia] = 
							$this -> sacarMateria($this -> seleccionados[$xccurso -> clavecurso] -> materia);
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$xccurso -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccurso -> id][$j] = $xchora;
						$this -> salon[$xccurso -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xccurso -> clavecurso;
					}
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			
			//////////////////////// Materias Regulares ///////////////////////
			$i = 0;
			if( isSet($this -> pendientes) )
			foreach( $this -> pendientes as $pend ){
				// $xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				if( strtoupper($this -> alumno -> enTurno) == "V" )
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_vespertino($pend -> clavemateria);
				else
					$this -> grupos[$i] = $xccursos -> get_materias_en_turno_matutino($pend -> clavemateria);
				//$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					//Select * from xccursos xc
					//where materia = '".$pend -> clavemateria."'
					//and periodo = ".$periodo."
					//and activo = 1
					//" );
				$i++;
			}
			$i = 0;
			if( isSet($this -> grupos) ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "curso_id = '"
							.$gpo -> id."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
			}
			///////////////////// Fin Materias Regulares //////////////////////////
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
		} // function seleccion_condicionadost($error = 0)
		
		function pseleccionT(){
			
			$periodo = $this -> actualselecc;
			$this -> valida();
			
			$id = Session::get_data('registro_tmp');
		
			$seleccion = new Seleccionalumno();
			
			$seleccion -> registro = $id;
			$seleccion -> clavemateria = $this -> post("materia");
			$seleccion -> periodo = $periodo;
			$seleccion -> save();
			
			$this->redirect('alumnoselecc/seleccion2T');
		} // function pseleccionT()
		
		function pkardexT(){
			
			$periodo = $this -> actualselecc;
			$this -> valida();
			
			$id = Session::get_data('registro');
			
			$xpkardex = new Xpkardex();
			
			$xpkardex -> registro = $id;
			$xpkardex -> materia = $this -> post("materia");
			$xpkardex -> periodo = $this -> post("periodo");
			$xpkardex -> tipo = $this -> post("tipo");
			
			$xpkardex -> promedio = '0';
	
			$xpkardex -> save();
			
			$this->redirect('alumnoselecc/seleccion1T');
			
		} // function pkardexT()
		
		function epkardexT(){
			$periodo = $this -> actualselecc;
			$this -> valida();

			$xpkardex = new Xpkardex();
			
			$tmp = $xpkardex -> find($this -> post("id"));
			$tmp -> delete();
			
			$this->redirect('alumnoselecc/seleccion1T');
		} // function epkardexT()
		
		function epseleccionT(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();

			$id = Session::get_data('registro_tmp');
			$seleccion = new Seleccionalumno();
			
			$tmp = $seleccion -> find_first($this -> post("id"));
			
			$tmp -> delete();
			
			$this->redirect('alumnoselecc/seleccion2T');
		} // function epseleccionT()
		
		function deseleccionarT(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro_tmp');
		
			$xccursos		= new Xtcursos();
			$xalumnocursos 	= new Xtalumnocursos();
			
			$xccurso = $xccursos -> find_first( "clavecurso = '".$this -> post("clavecurso")."'" );
			
			$xalumnocurso = $xalumnocursos -> find_first
					( "registro = ".$registro.
							" and curso_id = '".$xccurso -> id."'".
								"and periodo = ".$periodo );
			$xalumnocurso -> delete();
			
			$xccurso -> disponibilidad += 1;
			$xccurso -> save();
			
			//$Xtalumnocuross_logalumnos = new XtalumnocursosLogalumnos();
			//$Xtalumnocuross_logalumnos -> grupo_deseleccionado($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			$this->redirect('alumnoselecc/seleccion3T');
		} // function deseleccionarT()
		
		function seleccionarT(){
			$periodo = $this -> actualselecc;
			
			$this -> valida();
			
			$registro = Session::get_data('registro_tmp');
			
			$xccursos 		= new Xtcursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xchorascursos	= new Xthorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llegar por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			// Obtener objeto alumno
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> get_relevant_info_from_student($registro);
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$registro." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion3T/1' );
				return;
			}
			else{				
				
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$registro." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "curso_id = '".
							$xalumnocurso -> curso_id."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "curso_id = '".$xccurso -> id."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora ) ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				$Xalumnocursos = new Xalumnocursos();
				$ocupado += $Xalumnocursos->get_horas_cruces_de_materias($registro, $periodo);
				$Xtalumnocursos = new Xtalumnocursos();
				$ocupado += $Xtalumnocursos->get_horas_cruces_de_materias($registro, $periodo);
				
				$horas_posibles_de_cruce = 2;
				if( $ocupado <= $horas_posibles_de_cruce || $alumno->chGpo == "**" ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xtalumnocursos();
					$xalumnocurso -> registro = $registro;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso_id = $xccurso -> id;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					$xccurso -> save();
					
				}
				else{
					if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
							$autCruce -> registro == $registro &&
									$autCruce -> horasautorizadas >= $ocupado ){
						
						$xalumnocurso = new Xtalumnocursos();
						$xalumnocurso -> registro = $registro;
						$xalumnocurso -> periodo = $periodo;
						
						$xalumnocurso -> curso_id = $xccurso -> id;
						$xalumnocurso -> faltas1 = '0';
						$xalumnocurso -> faltas2 = '0';
						$xalumnocurso -> faltas3 = '0';
						$xalumnocurso -> calificacion1 = 300;
						$xalumnocurso -> calificacion2 = 300;
						$xalumnocurso -> calificacion3 = 300;
						$xalumnocurso -> faltas = '0';
						$xalumnocurso -> calificacion = 300;
						$xalumnocurso -> situacion = "-";
						
						$xalumnocurso -> create();
						
						$xccurso -> disponibilidad -= 1;
						$xccurso -> save();
					}
					else{
						if( $cursoscomunes -> find_first
								( "clavecurso1 = '".$xccurso -> clavecurso."'"."
									or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
							$xalumnocurso = new Xtalumnocursos();
							$xalumnocurso -> registro = $registro;
							$xalumnocurso -> periodo = $periodo;
							
							$xalumnocurso -> curso_id = $xccurso -> id;
							$xalumnocurso -> faltas1 = '0';
							$xalumnocurso -> faltas2 = '0';
							$xalumnocurso -> faltas3 = '0';
							$xalumnocurso -> calificacion1 = 300;
							$xalumnocurso -> calificacion2 = 300;
							$xalumnocurso -> calificacion3 = 300;
							$xalumnocurso -> faltas = '0';
							$xalumnocurso -> calificacion = 300;
							$xalumnocurso -> situacion = "-";
							
							$xalumnocurso -> create();
							
							$xccurso -> disponibilidad -= 1;
							$xccurso -> save();
						}
						else{
							// El 2 es un mensaje de cruce de horario 
							$this -> redirect( 'alumnoselecc/seleccion3T/2' );
							return;
						}
					}
				}
			}
			//$Xtalumnocuross_logalumnos = new XtalumnocursosLogalumnos();
			//$Xtalumnocuross_logalumnos -> grupo_seleccionado_correctamente($registro, $periodo, $xccurso -> clavecurso, $xccurso -> materia, $registro);
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion3T/7');
		} // function seleccionarT()
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function nombreProfesor($nomina){
			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina= ".(int)$nomina);
			return $maestro -> nombre;
		}
		
		function nombreSalon($salon){
			$xsalones = new Xsalones();
			$xsalon = $xsalones -> find_first("id=".$salon."");
			return $xsalon -> edificio.":".$xsalon -> nombre;
		}
		
		function sacarMateria($clave){
			$materias = new materia();
			$materia = $materias -> find_first("clave='".$clave."' AND carrera_id = ".$this -> alumno -> carrera_id);
			return $materia -> nombre;
		}
		
		function obtenerCreditos($clave, $plan){
			$materias = new Planmateria();
			$materia = $materias -> find_first("clavemateria='".$clave."' AND idplan=".$plan);
			$materia = $materias -> find_first("clavemateria='".$clave."' AND idplan=".$plan);
			return $materia -> creditos;
		}
		
		function incrementaPeriodo($periodo){
		
			if(date("m",time())<7){
				$actual = "1".date("Y",time());
			}
			else{
				$actual = "3".date("Y",time());
			}
			
			$tmp = $periodo;
		
			if($periodo[0]==1){
				$periodo = "3".substr($periodo,1,4);				
			}
			else{
				$periodo = "1".(substr($periodo,1,4) + 1);
			}
			
			return $periodo;
		}
		
		
		function evaluacion(){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			$cursos = $xacursos -> find("registro=".$id);
			$this -> cursos = $xacursos -> count("registro=".$id);
			
			$i=0;
			
			if($this -> cursos > 0 ){
				foreach($cursos as $curso){
	
					$xevaluacion = new Evaluacion();
					$n = $xevaluacion -> count("registro=".$id." AND curso='".$curso -> curso."'");
					
					if($n>0){
						$this -> evaluacion[$curso -> curso] = 1;
					}
					else{
						$this -> evaluacion[$curso -> curso] = 0;
					}
				
					if($curso -> curso!=""){
						$tmp = $xcursos -> find_first("id=".(substr($curso -> curso,3)+0));
					}
					$this -> mihorario[$i] = $tmp;
					if($tmp -> nomina!="")
						$this -> profesor[$i] = $maestros -> find_first("nomina=".$tmp -> nomina."");
					if($tmp -> materia!="" && $especialidad -> siNumEsp != "")
						$this -> materia[$tmp -> materia] = $this -> sacarMateria($tmp -> materia,$this -> plan);
					
					$xsalones = new Xsalones();
					
					if($tmp->luness != "-" && $this -> salones[$tmp->luness] == 0)
						$this -> salones[$tmp->luness] = $xsalones -> find_first("id=".$tmp->luness);
					
					if($tmp->martess != "-" && $this -> salones[$tmp->martess] == 0)
						$this -> salones[$tmp->martess] = $xsalones -> find_first("id=".$tmp->martess);
					
					if($tmp->miercoless != "-" && $this -> salones[$tmp->miercoless] == 0)
						$this -> salones[$tmp->miercoless] = $xsalones -> find_first("id=".$tmp->miercoless);
						
					if($tmp->juevess != "-" && $this -> salones[$tmp->juevess] == 0)
						$this -> salones[$tmp->juevess] = $xsalones -> find_first("id=".$tmp->juevess);
					
					if($tmp->vierness != "-" && $this -> salones[$tmp->vierness] == 0)
						$this -> salones[$tmp->vierness] = $xsalones -> find_first("id=".$tmp->vierness);
					
					if($tmp->sabados != "-" && $this -> salones[$tmp->sabados] == 0)
						$this -> salones[$tmp->sabados] = $xsalones -> find_first("id=".$tmp->sabados);
					
					
					$i++;
					
				}
			}
		}
		
		function evaluando($curso){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			//$xcurso = $xacursos -> find_first("curso='".$curso."'");
			
			$xcurso = $xcursos -> find_first("curso='".$curso."'");
			$this -> curso = $xcurso;
			
			$this -> profesor = $maestros -> find_first("nomina=".$xcurso -> nomina);
			$this -> profesor = $this -> profesor -> nombre;
		}
		
		function evaluar($curso){
		
			$registro = Session::get_data('registro');
			
			$xevaluacion = new Evaluacion();
			
			$xevaluacion -> registro = $registro;
			$xevaluacion -> curso = $curso;
			
			$xevaluacion -> p1 = $this -> post("p1");
			$xevaluacion -> p2 = $this -> post("p2");
			$xevaluacion -> p3 = $this -> post("p3");
			$xevaluacion -> p4 = $this -> post("p4");
			$xevaluacion -> p5 = $this -> post("p5");
			$xevaluacion -> p6 = $this -> post("p6");
			$xevaluacion -> p7 = $this -> post("p7");
			$xevaluacion -> p8 = $this -> post("p8");
			$xevaluacion -> p9 = $this -> post("p9");
			$xevaluacion -> p10 = $this -> post("p10");
			$xevaluacion -> p11 = $this -> post("p11");
			$xevaluacion -> p12 = $this -> post("p12");
			$xevaluacion -> p13 = $this -> post("p13");
			$xevaluacion -> p14 = $this -> post("p14");
			$xevaluacion -> p15 = $this -> post("p15");
			$xevaluacion -> p16 = $this -> post("p16");
			$xevaluacion -> p17 = $this -> post("p17");
			$xevaluacion -> p18 = $this -> post("p18");
			$xevaluacion -> p19 = $this -> post("p19");
			$xevaluacion -> p20 = $this -> post("p20");
			$xevaluacion -> p21 = $this -> post("p21");
			
			if($this -> post("comentarios")=="")
				$xevaluacion -> comentarios = "-";
			else
				$xevaluacion -> comentarios = $this -> post("comentarios");
			
			$xevaluacion -> periodo = 32008;
			$xevaluacion -> fecha = date("Y-m-d H:i:s",time());
			$xevaluacion -> save();
			
			$this -> redirect("alumno/evaluacion");
		}
		
		function actualizacion(){
		
		}
		
		function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			$this->redirect('/');
		}
		
		function guardar(){
			
			/*
			$this -> set_response('view');			
			$seccionNombre = $this -> request("seccionNombre");
			$seccionNumero = $this -> request("seccionNumero");
			$registroAlumno = $this -> request("registroAlumno");
			
			for($i=0; $i<50; $i++){
				if ($this -> post("r".$i) != ''){
					$Preguntas = new Preguntas();
					$Preguntas -> find_first("id = ".$i);
					
					switch ($this -> post("r".$i)){
						case 5: $Preguntas -> r1 = $Preguntas -> r1 +1;
								break;
						case 6: $Preguntas -> r2 = $Preguntas -> r2 +1;
								break;
						case 7: $Preguntas -> r3 = $Preguntas -> r3 +1;
								break;
						case 8: $Preguntas -> r4 = $Preguntas -> r4 +1;
								break;
						case 9: $Preguntas -> r5 = $Preguntas -> r5 +1;
								break;
						case 10: $Preguntas -> r6 = $Preguntas -> r6 +1;
								break;												
					}															
					//echo utf8_encode("<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>");
					//echo "<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>";
					$Preguntas -> save();					
				}
			}
			//Flash::success(utf8_encode('Se guardo exitosamente la sección: ').'<span class="resaltado">'.$seccionNombre.'</span>'); 
			//Flash::success('Se guardo exitosamente la sección: <span class="resaltado">'.$seccionNombre.'</span>'); 
			
			$seccionNumero= "s".$seccionNumero;
			$Usuarios = new Usuariosencuestas();			
			$Usuarios -> find_first("registro = ".$registroAlumno);
			$Usuarios -> $seccionNumero = 1;
			$Usuarios -> save();
			
			$suma=0;
			for($i=1;$i<12;$i++){
				$campo = "s".$i;
				$suma = $suma + $Usuarios -> $campo;		
			}				
	
			$this -> set_response('view');
			*/
			
			
			$this -> set_response('view');			
			$seccionNombre = $this -> request("seccionNombre");
			$seccionNumero = $this -> request("seccionNumero");
			$registroAlumno = $this -> request("registroAlumno");
			$day = date("d");
			$month = date("m");
			$year = date("Y");
			$hour = date("H");
			$min = date("i");
			$sec = date("s");
//			$date = date( "M/d/Y", mktime( 0, 0, 0, $month, $day, $year ) );
			$date1 = date( "Y-m-d H:i:s", mktime( $hour, $min, $sec, $month, $day, $year ) );
			
			for( $i = 1; $i < 53; $i++ ){
				if ( $this -> post("r".$i) != '' || 
						$this -> post("comentario".$i) != '' || 
								$this -> post("opctexto".$i) != '' ||
										$this -> post("opc".$i) != '' ||
												$this -> post("sino".$i) != '' ){
					$Preguntas = new PreguntasSatisfaccion();
					$Respuestas = new RespuestasSatisfaccion();
					$Preguntas -> find_first("id = ".$i);
					
					echo "<b>". htmlentities($Preguntas -> pregunta, ENT_QUOTES)."</b>";
					if ( $this -> post("tipo".$i) == 0 ){
						switch ($this -> post("r".$i)){
							case 5: $Preguntas -> r1 = $Preguntas -> r1 +1;
									break;
							case 6: $Preguntas -> r2 = $Preguntas -> r2 +1;
									break;
							case 7: $Preguntas -> r3 = $Preguntas -> r3 +1;
									break;
							case 8: $Preguntas -> r4 = $Preguntas -> r4 +1;
									break;
							case 9: $Preguntas -> r5 = $Preguntas -> r5 +1;
									break;
							case 10: $Preguntas -> r6 = $Preguntas -> r6 +1;
									break;
						}
						$Preguntas -> save();
						echo "<br />".$this -> post("r".$i)."<br />";
					}
					else if ( $this -> post("tipo".$i) == 1 ){
						echo "<br />".$this -> post( "comentario".$i )."<br />";
						$Respuestas -> preguntas_satisfaccion_id = $i;
						$Respuestas -> fecha = $date1;
						$Respuestas -> respuesta = '0';
						$Respuestas -> comentario = $this -> post("comentario".$i);
						$Respuestas -> registro = Session::get_data("registro");
						$Respuestas -> create();
					}
					else if ( $this -> post("tipo".$i) == 2 ){
						if ( $this -> post("sino".$i ) == 1 )
							echo "<br />Si<br />";
						else
							echo "<br />No<br />";
						
						$Respuestas -> preguntas_satisfaccion_id = $i;
						$Respuestas -> fecha = $date1;
						$Respuestas -> respuesta = $this -> post("sino".$i);
						$Respuestas -> comentario = " ";
						$Respuestas -> registro = Session::get_data("registro");
						$Respuestas -> create();
					}
					else if ( $this -> post("tipo".$i) == 3 ){
						if ( $this -> post("opctexto".$i) != "" ||
										$this -> post("opctexto".$i) != null ){
							echo "<br />".$this -> post("opctexto".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = $date1;
							$Respuestas -> respuesta = '0';
							$Respuestas -> comentario = $this -> post("opctexto".$i);
							$Respuestas -> registro = Session::get_data("registro");
							$Respuestas -> create();
						}
						else{
							echo "<br />".$this -> post("opc".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = "'".$date1."'";
							$Respuestas -> respuesta = $this -> post("opc".$i);
							$Respuestas -> comentario = " ";
							$Respuestas -> registro = Session::get_data("registro");
							$Respuestas -> create();
						}
					}
					//echo utf8_encode("<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>");
					//echo "<br/ > Respuesta:".$this -> post("r".$i)."<br>";
				}
			}
			//Flash::success(utf8_encode('Se guardo exitosamente la sección: ').'<span class="resaltado">'.$seccionNombre.'</span>');
			Flash::success('Se guardo exitosamente la secci&oacute;n: <span class="resaltado">'.$seccionNombre.'</span>');
			
			$seccionNumero= "s".$seccionNumero;
			$Usuarios = new Usuariosencuestas();
			$Usuarios -> find_first( "registro = ".$registroAlumno." and periodo = ".$this -> actualselecc);
			$Usuarios -> $seccionNumero = 1;
			$Usuarios -> save();
			
			$suma=0;
			for( $i = 1; $i < 14; $i++ ){
				$campo = "s".$i;
				$suma = $suma + $Usuarios -> $campo;
			}
			if ( $suma == 13 ){
				$this -> render_partial("formulario");
			}
			$this -> set_response('view');
		}
		
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
		
		function materiasIrregularesParaSelecc($registro){
			
			$xccursos	= new Xccursos();
			$xtcursos	= new Xtcursos();
			$xalcursos	= new Xalumnocursos();
			$xtalcursos	= new Xtalumnocursos();
			
			$mmateria	= new Materia();
			$alumnos	= new Alumnos();
			
			$alumno = $alumnos -> find_first( "miReg = ".$registro );
			$i = 0;
			foreach( $xalcursos -> find_all_by_sql
				( "Select xcc.clavecurso, xal.registro, xcc.materia
					From xalumnocursos xal, xextraordinarios xext, xccursos xcc
					where xal.registro = ".$registro."
					and xal.periodo = ".$this -> antesanteriorselecc."
					and xal.calificacion < 70
					and xal.curso_id = xext.curso_id
					and xal.registro = xext.registro
					and xcc.id = xal.curso_id
					and xcc.id = xext.curso_id
					and (xext.calificacion < 70
					or xext.calificacion > 100)" ) as $xal ){
				$materiasIrregulares[$i] = $xal -> materia;
				$i++;
			}
			foreach( $xalcursos -> find_all_by_sql
				( "Select xcc.clavecurso, xal.registro, xcc.materia
					From xalumnocursos xal, xextraordinarios xext, xccursos xcc
					where xal.registro = ".$registro."
					and xal.periodo = ".$this -> anteriorselecc."
					and xal.calificacion < 70
					and xal.curso_id = xext.curso_id
					and xal.registro = xext.registro
					and xcc.id = xal.curso_id
					and xcc.id = xext.curso_id
					and (xext.calificacion < 70
					or xext.calificacion > 100)" ) as $xal ){
				if( $materiasIrregulares[($i-1)] == $xal -> materia ){
					$materiasIrregulares[($i-1)] = $xal -> materia;
				}
				else{
					$materiasIrregulares[$i] = $xal -> materia;
					$i++;
				}
			}
			// Checar si debe materias en el plantel de tonala.
			foreach( $xtalcursos -> find_all_by_sql
				( "Select xcc.clavecurso, xal.registro, xcc.materia
					From xtalumnocursos xal, xtextraordinarios xext, xtcursos xcc
					where xal.registro = ".$registro."
					and xal.periodo = ".$this -> antesanteriorselecc."
					and xal.calificacion < 70
					and xal.curso_id = xext.curso_id
					and xal.registro = xext.registro
					and xcc.id = xal.curso_id
					and xcc.id = xext.curso_id
					and (xext.calificacion < 70
					or xext.calificacion > 100)" ) as $xal ){
				$materiasIrregulares[$i] = $xal -> materia;
				$i++;
			}
			foreach( $xtalcursos -> find_all_by_sql
				( "Select xcc.clavecurso, xal.registro, xcc.materia
					From xtalumnocursos xal, xtextraordinarios xext, xtcursos xcc
					where xal.registro = ".$registro."
					and xal.periodo = ".$this -> anteriorselecc."
					and xal.calificacion < 70
					and xal.curso_id = xext.curso_id
					and xal.registro = xext.registro
					and xcc.id = xal.curso_id
					and xcc.id = xext.curso_id
					and (xext.calificacion < 70
					or xext.calificacion > 100)" ) as $xal ){
				if( $materiasIrregulares[($i-1)] == $xal -> materia ){
					$materiasIrregulares[($i-1)] = $xal -> materia;
				}
				else{
					$materiasIrregulares[$i] = $xal -> materia;
					$i++;
				}
			}
			return $materiasIrregulares;
		} // function materiasIrregularesParaSelecc($registro)
		
		function valida(){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				//$this->redirect('/');
			}
		}
		function valida_condicionado(){
			$Alumnos = new Alumnos();
			$registro = Session::get_data('registro');
			if($Alumnos->find_first("miReg = '".$registro."' and stSit = 'OK' and enTipo = 'C' and enPlantel ='C'")){
				$this->redirect('alumnoselecc/seleccion_condicionados');
			}
			if($Alumnos->find_first("miReg = '".$registro."' and stSit = 'OK' and enTipo = 'C' and enPlantel ='N'")){
				$this->redirect('alumnoselecc/seleccion_condicionadost');
			}
		}
	}
