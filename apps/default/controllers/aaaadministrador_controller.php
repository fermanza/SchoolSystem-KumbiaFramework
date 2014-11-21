<?php
			
	class AdministradorController extends ApplicationController {
		function index(){
		
		}
		
		function parche(){
			$xcursos = new Xcursos();
			
			$cursos = $xcursos -> find();
			
			if($cursos) foreach($cursos as $curso){
				$idcurso = $curso -> id;
				
				if($idcurso <100 && $idcurso >= 10){
					$idcurso = "0".$idcurso;
				}
				if($idcurso < 10){
					$idcurso = "00".$idcurso;
				}
				
				$curso -> curso = $curso -> division . $idcurso;
				
				$curso -> save();
				
				echo $curso -> curso."<br>";
			}
		}
		
		function formato($n,$x){
			return $n*100;
		}
		
		function quitarPreCurso(){
			$cursos = new Precurso();
			$cursos -> delete("id=".$this -> post("id"));
			$this -> redirect('administrador/vercursos/'.$this -> post("clavemateria"));
		}
		
		function registrarPreCurso($periodo=32008){
		
			$cursos = new Precurso();
			
			if
			(
				$this -> post("nomina")=="" 
				|| 
				(
					($this -> post("entrada1")=="" || $this -> post("salida1")=="" || $this -> post("lugar1")=="")
					&&
					($this -> post("entrada2")=="" || $this -> post("salida2")=="" || $this -> post("lugar2")=="")
					&&
					($this -> post("entrada3")=="" || $this -> post("salida3")=="" || $this -> post("lugar3")=="")
					&&
					($this -> post("entrada4")=="" || $this -> post("salida4")=="" || $this -> post("lugar4")=="")
					&&
					($this -> post("entrada5")=="" || $this -> post("salida5")=="" || $this -> post("lugar5")=="")
					&&
					($this -> post("entrada6")=="" || $this -> post("salida6")=="" || $this -> post("lugar6")=="")
				)
			)
			{
				$this -> redirect('administrador/vercursos/'.$this -> post("clavemateria"));
			}
			else{
					$cursos = new Precurso();
					$cursos -> clave = strtoupper($this -> post("clave"));
					$cursos -> clavemateria = strtoupper($this -> post("clavemateria"));
					$cursos -> registroprofesor = $this -> post("nomina");
					
					$cursos -> lunesi = $this -> post("entrada1");$cursos -> lunesf = $this -> post("salida1");$cursos -> lunesu = $this -> post("lugar1");
					$cursos -> martesi = $this -> post("entrada2");$cursos -> martesf = $this -> post("salida2");$cursos -> martesu = $this -> post("lugar2");
					$cursos -> miercolesi = $this -> post("entrada3");$cursos -> miercolesf = $this -> post("salida3");$cursos -> miercolesu = $this -> post("lugar3");
					$cursos -> juevesi = $this -> post("entrada4");$cursos -> juevesf = $this -> post("salida4");$cursos -> juevesu = $this -> post("lugar4");
					$cursos -> viernesi = $this -> post("entrada5");$cursos -> viernesf = $this -> post("salida5");$cursos -> viernesu = $this -> post("lugar5");
					$cursos -> sabadoi = $this -> post("entrada6");$cursos -> sabadof = $this -> post("salida6");$cursos -> sabadou = $this -> post("lugar6");
					
					$cursos -> activo = 0;
					$cursos -> periodo = $this -> post("periodo");
					$cursos -> preselecciones = 0;
					
					$cursos -> save();
					$this -> redirect('administrador/vercursos/'.$this -> post("clavemateria"));
			}
		}
		
		function vercursos($clavemateria,$plan=2000,$periodo=32008){
			unset($this -> cursos);
			unset($this -> materia);
			unset($this -> planmateria);
			unset($this -> idplan);
			unset($this -> prerrequisito);
			unset($this -> materias);
			unset($this -> maestros);
			unset($this -> maestritos);
			
			$this -> periodito = $periodo;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
				
			$this -> periodo .= substr($periodo,1,4);
			
			$cursos = new Precurso();
			$maestros = new Maestros();
			$materias = new Materia();
			$planes = new Planmateria();
			$p = new Plan();
			$pla = $p -> find($idplan);
			$c = new Carrera();
			$car = $c -> find($pla -> idcarrera);
			
			switch($car -> clavenivel){
				case 'I': $this -> nivel = "INGENIERÍA"; break;
				case 'T': $this -> nivel = "TECNOLÓGO"; break;
			}			
			$this -> cursos = $cursos -> find("clavemateria='".$clavemateria."'");
			
			$i=0;
			if($this -> cursos)
			foreach($this -> cursos as $cursillo){
				$this -> materias[$i] = $materias -> find_first("clave='".$cursillo -> clavemateria."'");
				$this -> maestros[$i] = $maestros -> find_first("nomina=".$cursillo -> registroprofesor."");
				$i++;
			}
			
			$this -> maestritos = $maestros -> find();
			
			$this -> materia = $materias -> find_first("clave='".$clavemateria."' AND plan=".$plan);
			$this -> planmateria = $planes -> find_first("clavemateria='".$clavemateria."'");
			
			$this -> prerrequisito = $materias -> find_first("clave='".$this -> planmateria -> prerrequisito."' AND plan=".$plan);
			$this -> idplan = $idplan;
			
		}
		
		function quitarMateria(){
			$materias = new Planmateria();
			$materias -> delete("clavemateria='".$this -> post("clave")."'");
			//echo $this -> post("id");
			$this->redirect('administrador/carreraplan/'.$this -> post("plan").'/'.$this -> post("modelo").'/4');
		}
		
		function agregarMateria(){
		
			$plancarrera = new Planmateria();
			
			$n = $plancarrera -> count("clavemateria='".strtoupper($this -> post("clave"))."' AND idplan=".$this -> post("plan")."");
			
			if($this -> post("clave")=="" || $this -> post("creditos")==""){
				$this -> redirect('administrador/carreraplan/'.$this -> post("plan").'/'.$this -> post("modelo").'/2');
			}
			else{
				if($n > 0){
					$this->redirect('administrador/carreraplan/'.$this -> post("plan").'/'.$this -> post("modelo").'/3');
				}
				else{
					$materia = new Planmateria();
					$materia -> idplan = strtoupper($this -> post("plan"));
					$materia -> clavemateria = strtoupper($this -> post("clave"));
					$materia -> creditos = $this -> post("creditos");
					
					$materia -> obligatoria = 0;
					$materia -> especializante = 0;
					$materia -> optativa = 0;
					
					switch($this -> post("tipo")){
						case 0: case 1: $materia -> obligatoria = 1; break;
						case 2: $materia -> especializante = 1; break;
						case 3: $materia -> optativa = 1; break;
					}
					
					$materia -> semestre = 0;
					$materia -> prerrequisito = $this -> post("prerrequisito");
					$materia -> save();
					$this->redirect('administrador/carreraplan/'.$this -> post("plan").'/'.$this -> post("modelo").'/1');
				}
			}
		}
		
		function carreraPlan($idcarrera, $plan, $mensaje=0,$editando=0){
		
			unset($this -> carrera);
			unset($this -> plan);
			unset($this -> obligatorias);
			unset($this -> especializantes);
			unset($this -> optativas);
			unset($this -> pobligatorias);
			unset($this -> pespecializantes);
			unset($this -> poptativas);
			unset($this -> materias);
			unset($this -> mensaje);
			unset($this -> modelo);
			unset($this -> edicion);
			
			$this -> edicion = $editando;
			
			switch($mensaje){
				case 1: $this -> mensaje = "LA MATERIA HA SIDO REGISTRADA"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "LA MATERIA YA HABIA SIDO REGISTRADA"; break;
				case 4: $this -> mensaje = "LA MATERIA HA SIDO ELIMINADA"; break;
			}
			
			$carreras = new Carrera();
			$carrera = $carreras -> find($idcarrera);
			
			$planes = new Plan();
			$plan = $planes -> find($carrera -> id);
			
			$this -> plan = $plan;
			$this -> carrera = $carrera;
			
			$this -> modelo = $plan -> nombre;
			
			$i=0;
			
			$materiales = new Materia();
			
			$this -> materias = $materiales -> find("plan='".$plan -> nombre."' ORDER BY nombre");
		
			$plancarrera = new Planmateria();
			$materias = $plancarrera -> find("idplan=".$plan -> id." AND obligatoria=1 ORDER BY clavemateria");
		
			if($materias)
				foreach($materias as $materia){
					$this -> obligatorias[$i] = $materiales -> find_first("clave='".$materia -> clavemateria."'");
					$this -> pobligatorias[$i] = $materia;
					$i++;
				}
			
			$i=0;
			
			$plancarrera = new Planmateria();
			$materias = $plancarrera -> find("idplan=".$plan -> id." AND especializante=1 ORDER BY clavemateria");
			
			if($materias)
				foreach($materias as $materia){
					$this -> especializantes[$i] = $materiales -> find_first("clave='".$materia -> clavemateria."'");
					$this -> pespecializantes[$i] = $materia;
					$i++;
				}
			
			$i=0;
			
			$plancarrera = new Planmateria();
			$materias = $plancarrera -> find("idplan=".$plan -> id." AND optativa=1 ORDER BY clavemateria");
			
			if($materias)
				foreach($materias as $materia){
					$this -> optativas[$i] = $materiales -> find_first("clave='".$materia -> clavemateria."'");
					$this -> poptativas[$i] = $materia;
					$i++;
				}
			
		}
		
		function eliminarCarrera(){
			$carreras = new Carrera();
			$carreras -> delete($this -> post("id"));
			
			$this->redirect('administrador/carreras/4');
		}
		
		function eliminarMateria(){
			$materias = new Materia();
			$materias -> delete($this -> post("id"));
			
			$this->redirect('administrador/materias/'.$this -> post("modelo").'/4');
		}
		
		function materias($modelo=2000, $mensaje=0){
			$materias = new Materia();
			
			unset($this -> materias);
			unset($this -> prerrequisitos);
			unset($this -> mensaje);
			unset($this -> modelo);
			
			switch($mensaje){
				case 1: $this -> mensaje = "LA MATERIA HA SIDO REGISTRADA"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "LA MATERIA YA HABIA SIDO REGISTRADA"; break;
				case 4: $this -> mensaje = "LA MATERIA HA SIDO ELIMINADA"; break;
			}
			
			foreach($materias -> find("plan=".$modelo." ORDER BY clave") as $materia){
				$this -> materias[$m1] = $materia; $m1++; 
			}
			
			foreach($materias -> find("plan=".$modelo." ORDER BY nombre") as $materia){
				$this -> prerrequisitos[$m2] = $materia; $m2++; 
			}
			
			$this -> modelo = $modelo;
		}
		
		function registrarMateria(){
		
			$materias = new Materia();
			
			$n = $materias -> count("clave='".strtoupper($this -> post("clave"))."'");
			
			if($this -> post("clave")=="" || $this -> post("nombre")==""){
				$this -> redirect('administrador/materias/'.$this -> post("modelo").'/2');
			}
			else{
				if($n > 0){
					$this->redirect('administrador/materias/'.$this -> post("modelo").'/3');
				}
				else{
					$materia = new Materia();
					$materia -> clave = strtoupper($this -> post("clave"));
					$materia -> nombre = strtoupper($this -> post("nombre"));
					$materia -> creditos = 0;
					$materia -> prerrequisito = 0;
					$materia -> plan = $this -> post("modelo");
					$materia -> ponderacion1 = 0;
					$materia -> ponderacion2 = 0;
					$materia -> ponderacion3 = 0;
					$materia -> ponderada = 0;
					$materia -> ponderacionextra = 0;
					$materia -> division = $this -> post("division");
					$materia -> save();
					$this->redirect('administrador/materias/'.$this -> post("modelo").'/1');
				}
			}
		}
		
		function carreras($mensaje=0,$editando=0){
			$carreras = new Carrera();
						
			$m1=0; $m2=0; $m3=0;
			
			unset($this -> carreras);
			unset($this -> mensaje);
			unset($this -> modelo1998);
			unset($this -> modelo2000);
			unset($this -> modelo2007);
			unset($this -> edicion);
			
			$this -> edicion = $editando;
			
			foreach($carreras -> find() as $carrera){
			
				switch($carrera -> modelo){
					case 1998: $this -> modelo1998[$m1] = $carrera; $m1++; break;
					case 2000: $this -> modelo2000[$m2] = $carrera; $m2++; break;
					case 2007: $this -> modelo2007[$m3] = $carrera; $m3++; break;
				}
			}
			
			switch($mensaje){
				case 1: $this -> mensaje = "LA CARRERA HA SIDO REGISTRADA"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "LA CARRERA YA HABIA SIDO REGISTRADA"; break;
				case 4: $this -> mensaje = "LA CARRERA HA SIDO ELIMINADA"; break;
			}
		}
		
		function registrarCarrera(){
		
			$carrera = $this -> post("carrera");
			$carreras = new Carrera();
			
			$n = $carreras -> count("clavenivel='".strtoupper($this -> post("nivel"))."' AND clave='".strtoupper($this -> post("clave"))."' AND nombre='".strtoupper($this -> post("nombre"))."'");
			
			if($this -> post("nivel")=="" || $this -> post("clave")=="" || $this -> post("nombre")==""){
				$this -> redirect('administrador/carreras/2');
			}
			else{
				if($n > 0){
					$this->redirect('administrador/carreras/3');
				}
				else{
					$carrera = new Carrera();
					$carrera -> clavenivel = $this -> post("nivel");
					$carrera -> clave = $this -> post("clave");
					$carrera -> nombre = $this -> post("nombre");
					$carrera -> modelo = "2007";
					$carrera -> save();
					$this->redirect('administrador/carreras/1');
				}
			}
		}
		
		
		function agregarCarrera(){
		
			$plantelcarreras = new Plantelcarrera();
			
			$carrera = $this -> post("carrera");
			$nivel = $carrera[0];
			$carrera = substr($carrera,1);
			
			$p = new Plantel();
			$p = $p -> find_first("clave='".$this -> post("plantel")."'");
			
			$n = $plantelcarreras -> count("clavenivel='".strtoupper($nivel)."' AND clavecarrera='".strtoupper($carrera)."' AND claveplantel='".strtoupper($this -> post("plantel"))."'");
			
			if($this -> post("carrera")=="" || $this -> post("plantel")==""){
				$this -> redirect('administrador/carrerasPlantel/2/'.$p -> id);
			}
			else{
				if($n > 0){
					$this->redirect('administrador/carrerasPlantel/3/'.$p -> id);
				}
				else{
					$plantelcarrera = new Plantelcarrera();
					$plantelcarrera -> clavecarrera = strtoupper($carrera);
					$plantelcarrera -> clavenivel = strtoupper($nivel);
					$plantelcarrera -> claveplantel = strtoupper($this -> post("plantel"));
					$plantelcarrera -> estado = 1;
					$plantelcarrera -> save();
					$this->redirect('administrador/carrerasPlantel/1/'.$p -> id);
				}
			}
		}
		
		
		function carrerasPlantel($mensaje=0,$id=1){
			$planteles = new Plantel();
			
			if($this -> post("id"))
				$plantel = $planteles -> find_first($this -> post("id"));
			else
				$plantel = $planteles -> find_first($id);
			
			$this -> plantel = $plantel;
			
			$plantelcarreras = new Plantelcarrera();
			$carreras = new Carrera();
						
			$i=0; $t=0;
			
			unset($this -> ingenieria);
			unset($this -> tecnologo);
			unset($this -> carreras);
			unset($this -> mensaje);
			
			foreach($plantelcarreras -> find("claveplantel='".$plantel -> clave."' ORDER BY clavecarrera") as $plantelcarrera){
				$carrera = $carreras -> find_first("clavenivel='".$plantelcarrera -> clavenivel."' AND clave='".$plantelcarrera -> clavecarrera."'");
				if($plantelcarrera -> clavenivel == "I"){
					$this -> ingenieria[$i] = $carrera;
					$i++;
				}
				else{
					$this -> tecnologo[$t] = $carrera;
					$t++;
				}
			}
			
			$this -> carreras = $carreras -> find("modelo=2007");
			
			switch($mensaje){
				case 1: $this -> mensaje = "LA CARRERA HA SIDO AGREGADA AL PLANTEL"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "LA CARRERA YA SE OFRECE EN ESTE PLANTEL"; break;
			}
		}
		
		function planes($mensaje=0,$id=1){
			$planteles = new Plantel();
			
			if($this -> post("id"))
				$plantel = $planteles -> find_first($this -> post("id"));
			else
				$plantel = $planteles -> find_first($id);
			
			$this -> plantel = $plantel;
			
			$plantelcarreras = new Plantelcarrera();
			$carreras = new Carrera();
						
			$i=0; $t=0;
			
			unset($this -> ingenieria);
			unset($this -> tecnologo);
			unset($this -> carreras);
			unset($this -> mensaje);
			unset($this -> modelo2007);
			
			foreach($plantelcarreras -> find("claveplantel='".$plantel -> clave."' ORDER BY clavecarrera") as $plantelcarrera){
				$carrera = $carreras -> find_first("clavenivel='".$plantelcarrera -> clavenivel."' AND clave='".$plantelcarrera -> clavecarrera."'");
				if($plantelcarrera -> clavenivel == "I"){
					$this -> ingenieria[$i] = $carrera;
					$i++;
				}
				else{
					$this -> tecnologo[$t] = $carrera;
					$t++;
				}
			}
			
			$this -> modelo2007 = 0;
			$this -> carreras = $carreras -> find("modelo=2007");
			
			switch($mensaje){
				case 1: $this -> mensaje = "LA CARRERA HA SIDO AGREGADA AL PLANTEL"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "LA CARRERA YA SE OFRECE EN ESTE PLANTEL"; break;
			}
		}
		
		function reportePlanteles($mensaje=0,$editando=0){
			$planteles = new Plantel();
			
			unset($this -> planteles);
			unset($this -> mensaje);
			
			unset($this -> edicion);
			
			$this -> edicion = $editando;
			
			$this -> totalplanteles = $planteles -> count();
			$this -> planteles = $planteles -> find();
			
			switch($mensaje){
				case 1: $this -> mensaje = "EL PLANTEL HA SIDO REGISTRADO CORRECTAMENTE"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "EL PLANTEL YA HABIA SIDO REGISTRADO"; break;
				case 4: $this -> mensaje = "EL PLANTEL HA SIDO ACTUALIZADO CORRECTAMENTE"; break;
				case 5: $this -> mensaje = "EL PLANTEL HA SIDO ELIMINADO CORRECTAMENTE"; break;
			}
		}
		
		function planteles($mensaje=0,$editando=0){
			$planteles = new Plantel();
			
			unset($this -> planteles);
			unset($this -> mensaje);
			
			unset($this -> edicion);
			
			$this -> edicion = $editando;
			
			$this -> totalplanteles = $planteles -> count();
			$this -> planteles = $planteles -> find();
			
			switch($mensaje){
				case 1: $this -> mensaje = "EL PLANTEL HA SIDO REGISTRADO CORRECTAMENTE"; break;
				case 2: $this -> mensaje = "LOS DATOS PROPORCIONADOS NO SON CORRECTOS"; break;
				case 3: $this -> mensaje = "EL PLANTEL YA HABIA SIDO REGISTRADO"; break;
				case 4: $this -> mensaje = "EL PLANTEL HA SIDO ACTUALIZADO CORRECTAMENTE"; break;
				case 5: $this -> mensaje = "EL PLANTEL HA SIDO ELIMINADO CORRECTAMENTE"; break;
			}
		}
		
		function modificacionPlantel(){
			unset($this -> planteles);
			$planteles = new Plantel();
		
			unset($this -> planteles);
		
			$this -> plantel = $planteles -> find($this -> post("id"));
		}
		
		function eliminarPlantel(){
			$planteles = new Plantel();
			$planteles -> delete($this -> post("id"));
			
			$this->redirect('administrador/reportePlanteles/5');
		}
		
		function modificarPlantel(){
			$planteles = new Plantel();
			
			$n = $planteles -> count("id!=".$this -> post("id")." AND (clave='".strtoupper($this -> post("clave"))."' OR nombre='".strtoupper($this -> post("nombre"))."')");
			
			if($this -> post("clave")=="" || $this -> post("nombre")==""){
				$this -> redirect('administrador/reportePlanteles/2');
			}
			else{
				if($n > 0){
					$this->redirect('administrador/reportePlanteles/3');
				}
				else{
					$plantel = $planteles -> find($this -> post("id"));
					$plantel -> clave = strtoupper($this -> post("clave"));
					$plantel -> nombre = strtoupper($this -> post("nombre"));
					$plantel -> save();
					$this->redirect('administrador/reportePlanteles/4');
				}
			}
		}
		
		function registrarPlantel(){
		
			$planteles = new Plantel();
			
			$n = $planteles -> count("clave='".strtoupper($this -> post("clave"))."' OR nombre='".strtoupper($this -> post("nombre"))."'");
			
			if($this -> post("clave")=="" || $this -> post("nombre")==""){
				$this -> redirect('administrador/reportePlanteles/2');
			}
			else{
				if($n > 0){
					$this->redirect('administrador/reportePlanteles/3');
				}
				else{
					$planteles -> clave = strtoupper($this -> post("clave"));
					$planteles -> nombre = strtoupper($this -> post("nombre"));
					$planteles -> save();
					$this->redirect('administrador/reportePlanteles/1');
				}
			}
		}
		
		function consultaPlantel($plantel){
			
		}
		
		function checarPagos(){
			unset($this -> concepto);
			unset($this -> registro);
			unset($this -> periodo);
			unset($this -> pago);
			unset($this -> nombre);
			unset($this -> lugar);
			
			$temporal = new Temporal2();
			
			$this -> flag = 0;
			
			$i = 0;
			if ( isSet($_POST["periodo"]) ){
				if ( $temp = $temporal -> find_first ( "periodo = ".$this->post("periodo") ) ){
					
					foreach ( $temporal -> find ("periodo= ". 
							$this -> post ("periodo") )as $temp ){
						
						$this -> concepto[$i]	= $temp -> concepto;
						$this -> registro[$i]	= $temp -> registro;
						$this -> periodo[$i]	= $temp -> periodo;
						$this -> pago[$i]		= $temp -> pago;
						$this -> nombre[$i]		= $temp -> nombre;
						$this -> recargo[$i]	= $temp -> recargo;
						$this -> lugar[$i]		= $temp -> lugar;
						$i++;
					}
				}
				else
					$this -> flag = 1;
			}
		} // function checarPagos()
		
		function buscarAlumno(){
		} // function buscarAlumno()
		
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
		
		function verAlumno(){
			// 
			echo "En construcci&oacute;n";
		} // function verAlumno()
	}
	
?>
