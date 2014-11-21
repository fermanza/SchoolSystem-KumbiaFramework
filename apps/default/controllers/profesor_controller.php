<?php

    class ProfesorController extends ApplicationController {

        public $antesAnterior	= 12012;
        public $pasado			= 32012;
        public $actual			= 12013;
        public $proximo			= 32013;

        function index(){
			$this -> redirect( "profesor/informacion" );
        }
        function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			Session::unset_data('TMPregistro');
			Session::unset_data('coordinador');
			Session::unset_data('TMPtipousuario');
			$this->redirect('seguridad/terminarsesion');
        }
        function informacion(){
			if( ($nomina = Session::get_data('registro')) == "" )
				$this->redirect('seguridad/terminarsesion');
			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" && 
						Session::get_data("tipousuario") != "GOE" &&
							Session::get_data("tipousuario") != "DIRECCION"&&
							Session::get_data("tipousuario") != "CULTURA" ){
				$this->redirect('seguridad/terminarsesion');
			}
			if(Session::get_data('coordinador')=="OK"){
				$this -> coordinador = "OK";
				switch(Session::get_data('coordinacion')){
					case "TCB": $this -> coordinacion = "Ciencias Básicas"; break;
					case "IIM": $this -> coordinacion = "Ingeniería Industrial"; break;
					case "MCT": $this -> coordinacion = "Ingeniería Mecatrónica"; break;
					case "TCT": $this -> coordinacion = "Ingeniería Mecatrónica"; break;
					case "IEC": $this -> coordinacion = "Ingeniería Electrónica"; break;
				}
			}
			$maestros = new Maestros();
			unset($this -> maestro);
			unset($this -> nomina);
			
			$this -> nomina = $nomina;
			if( is_numeric($nomina) )
				$this -> maestro = $maestros -> find_first("nomina = '".$nomina."'");
			if( isset($this -> maestro) ){
				if( $this -> maestro -> correo != "" )
					$this -> checkPWDDatE();
			}
        } // function informacion()
		
		function checarSessioncambiarcontrasena(){
			if( Session::get_data("cambiarcontrasena") == 1 ){
				// $this -> redirect("profesor/renovandoContrasena");
			}
		}
		
		function checkPWDDate(){
			if( ($nomina = Session::get_data('registro')) == "" )
				$this->redirect('seguridad/terminarsesion');
			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" && 
						Session::get_data("tipousuario") != "GOE" ){
				$this->redirect('seguridad/terminarsesion');
			}
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro = '".$nomina."'");
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			
			$fechaActual = mktime( $hour, $minute, $second, $month, $day, $year );
			
			if( ($fechaActual - $usuario -> passwd_last_change) > 2700000 ){
				// Es tiempo de cambiar la contraseña, cada mes debe cambiarse.
				//Session::set_data('cambiarcontrasena', 1);
				//$this -> redirect( "profesor/cambiarContrasena" );
			}
		} // function checkPWDDate()
		
		function guardandoCorreoAjax($nomina, $correo){
			$nomina = Session::get_data('registro');
			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" ){
				$this->redirect('seguridad/terminarsesion');
			}
			
			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$nomina);
			$maestro -> sexo = "-";
			$maestro -> domicilio = "-";
			$maestro -> telefono = "-";
			$maestro -> celular = "-";
			$maestro -> paterno = "-";
			$maestro -> correo = $correo;
			$maestro -> save();
			
			$this->redirect('profesor/informacion');
		} // function guardandoCorreoAjax($nomina, $correo)
		
		function cambiarCorreo($vacia){
			if( ($nomina = Session::get_data('registro')) == "" )
				$this->redirect('seguridad/terminarsesion');
			if( Session::get_data('tipousuario')!="PROFESOR"  && 
					Session::get_data("tipousuario") != "DIRECCION" ){
				$this->redirect('seguridad/terminarsesion');
			}
			
			unset($this -> maestro);
			$maestros = new Maestros();
			
			$this -> maestro = $maestros -> find_first("nomina = ".$nomina);
			$this -> vacia = $vacia;
		} // function cambiarCorreo()
		
		function cambiandoCorreo(){
			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" && 
						Session::get_data("tipousuario") != "GOE" && 
							Session::get_data("tipousuario") != "DIRECCION" ){
				$this->redirect('seguridad/terminarsesion');
            }
			unset($this -> maestro);
			$maestros	= new Maestros();
            $nomina = Session::get_data('registro');

            $correo = $this -> post( "correo" );

            if( !isset($correo) || $correo == "" ){
                $this->redirect('profesor/cambiarCorreo/1');
				return;
			}
			$filter='^[A-Za-z][A-Za-z0-9_.]*@[A-Za-z0-9_.]+.[A-Za-z0-9_.]+[A-za-z]$';
            $this -> exito = 0;
			if( !ereg($filter, $correo) ){
				$this->redirect('profesor/cambiarCorreo/2');
				return;
			}
			
			$this -> maestro = $maestros -> find_first( "nomina = '".$nomina."'" );
			$this -> maestro -> correo = $correo;
			$this -> maestro -> save();
			$this -> exito = 1;
		}
		
        function cprofesores(){

        }

        function ccalificaciones(){

        }

        function calumnos(){

        }

        function encuestas(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
        }

        function tutorados(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}else{
				$Tutorias = new Tutorias();
				$nomina = Session::get_data('registro');

				$this -> tutorias = $Tutorias -> find('nomina = '.$nomina);
			}
        }

        function evaluaciones(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
        }

        function historico(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
        }
		
		function enviarCorreo(){
			mail('fermanza@gmail.com', 'My Subject', "Pruebaaaa<br />Pruebaaa2");
		}
		
        function horario(){

			$id = Session::get_data('registro');

			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();

			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
				$this->redirect('profesor/cambiarContrasena');

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> ingreso);
			unset($this -> materia);

			unset($this -> creditos);
			unset($this -> ncreditos);
			unset($this -> periodos);
			unset($this -> pinicial);
			unset($this -> materias00);
			unset($this -> materias07);
			unset($this -> cursos);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> maestros);
			unset($this -> xccur);

			unset($this -> thoras);
			unset($this -> tsalon);
			unset($this -> tmaestros);
			unset($this -> xtcur);
			
			unset($this -> Intersemestrales);
			
			$xcursos = new Xccursos();
			$maestros = new Maestros();
			$materias = new Materia();

			$this -> nomina = $id;
			$this -> profesor = $maestros -> find_first("nomina=".$this -> nomina."");

			$total = 0;

			if( substr($periodo, 0, 1) == 1)
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";

			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;

			$cursos = $xcursos -> find("nomina=".$id." AND periodo=".$periodo."");
			$this -> cursos = $xcursos -> count("nomina=".$id." AND periodo=".$periodo);
			$i=0;
			
			$xccursos		= new Xccursos();
			$maestros		= new Maestros();
			$materias		= new Materia();
			$xalumnocursos	= new Xalumnocursos();
			$alumnos		= new Alumnos();
			$especialidades = new Especialidades();
			$xchorascursos	= new Xchorascursos();
			$xcsalones		= new Xcsalones();
			$kardexes		= new KardexIng();
			$planes			= new Plan();

			$total = 0;

			if( substr($periodo, 0, 1) == 1)
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";

			$this -> periodo .= substr($periodo, 1, 4);
			$this -> registro = $id;

			$total = 0;
			$mmm=0;
			$i = 0;


			$xccursoo = $xccursos -> find("periodo = ".$periodo." and nomina = ".$id);
			$this -> cursos = $xccursos -> count("periodo = ".$periodo." and nomina = ".$id);

			$i = 0;

			if($this -> cursos > 0 ){
				foreach($xccursoo as $xccur){

						$xccurso = $xccursos -> find_first( "clavecurso = '".$xccur -> clavecurso."'" );
						$this -> xccur[$i] = $xccurso;
						$j = 0;

						$materia = $materias -> find_first( "clave = '".$xccurso -> materia."'" );
						$this -> materias[$i] = $materia;

						$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
						$this -> maestros[$i] = $maestro;

						foreach( $xchorascursos -> find( "curso_id = '"
								.$xccurso -> id."'", "order: id asc" ) as $xchora ){

								$this -> horas[$xccurso -> id][$j] = $xchora;
								$this -> salon[$xccurso -> id][$j] = $xcsalones ->
												find_first( "id = ".$xchora -> xcsalones_id );
								$j++;
						}

						$i++;
				} // foreach($xalcursos as $xalcurso)
			} // if($this -> cursos > 0 )

			/*
			if($this -> cursos > 0 ){
				foreach($xccursoo as $xccur){

						$xccurso = $xccursos -> find_first( "clavecurso = '".$xccur -> clavecurso."'" );
						$this -> xccur[$i] = $xccurso;
						$j = 0;

						$materia = $materias -> find_first( "clave = '".$xccurso -> materia2000."'" );
						$this -> materias00[$i] = $materia;

						$materia = $materias -> find_first( "clave = '".$xccurso -> materia2007."'" );
						$this -> materias07[$i] = $materia;

						$maestro = $maestros -> find_first( "nomina = ".$xccurso -> nomina );
						$this -> maestros[$i] = $maestro;

						foreach( $xchorascursos -> find( "clavecurso = '"
								.$xccurso -> clavecurso."'", "order: id asc" ) as $xchora ){

								$this -> horas[$xccurso -> id][$j] = $xchora;
								$this -> salon[$xccurso -> id][$j] = $xcsalones ->
												find_first( "id = ".$xchora -> xcsalones_id );
								$j++;
						}

						$i++;
				} // foreach($xalcursos as $xalcurso)
			} // if($this -> cursos > 0 )
			*/

			$xtcursos		= new Xtcursos();
			$xtalumnocursos	= new Xtalumnocursos();
			$xthorascursos	= new Xthorascursos();
			$xtsalones		= new Xtsalones();

			$xtcursoo = $xtcursos -> find("periodo = ".$periodo." and nomina = ".$id);
			$this -> tcursos = $xtcursos -> count("periodo = ".$periodo." and nomina = ".$id);

			$i = 0;

			if($this -> tcursos > 0 ){
				foreach($xtcursoo as $xtcur){

						$xtcurso = $xtcursos -> find_first( "clavecurso = '".$xtcur -> clavecurso."'" );
						$this -> xtcur[$i] = $xtcurso;
						$j = 0;

						$materia = $materias -> find_first( "clave = '".$xtcurso -> materia."'" );
						$this -> tmaterias[$i] = $materia;

						$maestro = $maestros -> find_first( "nomina = ".$xtcurso -> nomina );
						$this -> tmaestros[$i] = $maestro;

						foreach( $xthorascursos -> find( "curso_id = '"
								.$xtcurso -> id."'", "order: id asc" ) as $xthora ){

								$this -> thoras[$xtcurso -> id][$j] = $xthora;
								$this -> tsalon[$xtcurso -> id][$j] = $xtsalones ->
												find_first( "id = ".$xthora -> xtsalones_id );
								$j++;
						}

						$i++;
				} // foreach($xalcursos as $xalcurso)
			} // if($this -> cursos > 0 )
			
			$IntersemestralCursos = new IntersemestralCursos();
			$this -> Intersemestrales = $IntersemestralCursos -> get_cursos_intersemestrales_by_nomina($this -> nomina);
			//exit(1);
        } // function horario()
		
		function intersemestral_listado_alumnos(){
			$inter_clavecurso = $this -> post("inter_clavecurso");
			
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			
			unset($this -> ListadoAlumnos);
			unset($this -> clavecurso);
			
			$this -> clavecurso = $inter_clavecurso;
			$this -> ListadoAlumnos = $IntersemestralAlumnos -> get_listado_de_alumnos_by_clavecurso($inter_clavecurso);
			
		} // function intersemestral_listado_alumnos()

        function sacarMateria($clave){
			$materias = new materia();
			$materia = $materias -> find_first("clave='".$clave."'");
			return $materia -> nombre;
        }

        function lista($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
				$this->redirect('profesor/cambiarContrasena');
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);

			$id = Session::get_data('registro');
			$periodo = $this -> actual;

			$Xccursos = new Xccursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xalumnocursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$total = 0;
			
			$xccurso = $Xccursos->find_first("id = '".$curso."'");
			
			foreach($xalumnocursos -> find("curso_id='".$xccurso->id."' ORDER BY registro") as $alumno){

				$this -> registro = $alumno -> registro;
				$this -> curso = $curso;
				$this -> materia = $this -> post("materia");
				$this -> clave = $this -> post("clave");

				foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
						$this -> nombre = $a -> vcNomAlu;
						$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
						$situacion = $a -> enTipo;
						$especialidad = $a -> idtiEsp;
						break;
				}

				switch($situacion){
						case 'R': $this -> situacion = "-"; break;
						case 'I': $this -> situacion = "IRREGULAR"; break;
						case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
						case 'C': $this -> situacion = "CONDICIONADO"; break;
				}

				foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
						$this -> especialidad = $e -> vcNomEsp;
						break;
				}

						$this -> alumnado[$total]["registro"] = $this -> registro;
						$this -> alumnado[$total]["nombre"] = $this -> nombre;
						$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
						$this -> alumnado[$total]["situacion"] = $this -> situacion;

				$total++;
			}


			foreach($maestros -> find("nomina=".$id) as $maestro){
				$this -> profesor = $maestro -> nombre;
			}


			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";

			$this -> periodo .= substr($periodo,1,4);
			$this -> nomina = $id;
			
        } // function lista($curso)

        function listat($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
				$this->redirect('profesor/cambiarContrasena');
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);

			$id = Session::get_data('registro');
			$periodo = $this -> actual;

			$xcursos = new Xtcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xalumnocursos = new Xtalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$total = 0;
			
			$xcurso = $xcursos -> find_first("id= '".$curso."'");

			foreach($xalumnocursos -> find("curso_id='".$xcurso->id."' ORDER BY registro") as $alumno){

				$this -> registro = $alumno -> registro;
				$this -> curso = $curso;
				$this -> materia = $this -> post("materia");
				$this -> clave = $this -> post("clave");

				foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
						$this -> nombre = $a -> vcNomAlu;
						$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
						$situacion = $a -> enTipo;
						$especialidad = $a -> idtiEsp;
						break;
				}

				switch($situacion){
						case 'R': $this -> situacion = "-"; break;
						case 'I': $this -> situacion = "IRREGULAR"; break;
						case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
						case 'C': $this -> situacion = "CONDICIONADO"; break;
				}

				foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
						$this -> especialidad = $e -> vcNomEsp;
						break;
				}

						$this -> alumnado[$total]["registro"] = $this -> registro;
						$this -> alumnado[$total]["nombre"] = $this -> nombre;
						$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
						$this -> alumnado[$total]["situacion"] = $this -> situacion;

				$total++;
			}


			foreach($maestros -> find("nomina=".$id) as $maestro){
				$this -> profesor = $maestro -> nombre;
			}


			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";

			$this -> periodo .= substr($periodo,1,4);
			$this -> nomina = $id;
			
        } // function listat($curso)
		
        function getIP() {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			elseif (isset($_SERVER['HTTP_VIA'])) {
				$ip = $_SERVER['HTTP_VIA'];
			}
			elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			else {
				$ip = "DESCONOCIDA";
			}
			return $ip;
        }

        function capturar(){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			
			$curso = $this -> post("curso");
			
			$Xccursos = new Xccursos();
			$xccurso = $Xccursos -> find_first("clavecurso='".$curso."'");
			
			$idcapturaesp	= 	$this -> post("idcapturaesp");
			
			$Xpermisoscapturaesp = new Xpermisoscapturaesp();
			if( $idcapturaesp > 0 ){
				$registros_captura_esp = $Xpermisoscapturaesp -> get_alumnos_conpermisosespeciales($idcapturaesp);
			}
			
			if( count($registros_captura_esp) > 0 ){
				$query = "curso_id='".$xccurso->id."'";
				$aux_esp = 0;
				foreach($registros_captura_esp as $reg){
					if( $aux_esp > 0 ) $query .= " OR registro = ".$reg;
					else $query .= " AND ( registro = ".$reg;
					$aux_esp++;
				}
				$query .= ") ORDER BY id";
			}
			else{
				$query = "curso_id='".$xccurso->id."' ORDER BY id";
			}
			$calificaciones = new Xalumnocursos();
			$flag = 3;
			foreach($calificaciones -> find($query) as $alumno){
				if( (strtoupper($this -> post("calificacion".$alumno -> id)) == "NP") || 
					(is_numeric($this -> post("calificacion".$alumno -> id))) ){
					// Asignamos 1 a flag para informar que puede seguir capturando.
					$flag = 1;
				}
				if( !is_numeric($this -> post("calificacion".$alumno -> id)) &&
					(strtoupper($this -> post("calificacion".$alumno -> id)) != "NP") ){
					// Asignamos 3 a flag para saber que NO puede seguir capturando.
					$flag = 3;
				}if(!is_numeric($this -> post("faltas".$alumno -> id)) ){
					// Asignamos 4 a flag para saber que NO puede seguir capturando.
					$flag = 4;
				}if( $this -> post("faltas".$alumno -> id) > $this -> post("horas") ){
					// Asignamos 2 a flag para saber que NO puede seguir capturando.
					$flag = 2;
				}if( $flag == 3 || $flag == 2 || $flag == 4 ){
					Session::set_data('flag', $flag);
					$this->redirect('profesor/captura');
					return;
				}
			}
			switch($this -> post("parcial")){
				case 1: $xccurso -> horas1 = $this -> post("horas"); break;
				case 2: $xccurso -> horas2 = $this -> post("horas"); break;
				case 3: $xccurso -> horas3 = $this -> post("horas"); break;
			}
			
			switch($this -> post("parcial")){
				case 1: $xccurso -> avance1 = $this -> post("avance"); break;
				case 2: $xccurso -> avance2 = $this -> post("avance"); break;
				case 3: $xccurso -> avance3 = $this -> post("avance"); break;
			}
			
			$xccurso -> paralelo = -1;
			
			$xccurso -> save();
			$conpermiso = new Xpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xccurso->id."'");
			
			$xpermesp		= 	new Xpermisoscapturaesp();
			
			switch($this -> post("parcial")){
				case 1: $permiso -> ncapturas1 ++; break;
				case 2: $permiso -> ncapturas2 ++; break;
				case 3: $permiso -> ncapturas3 ++; break;
			}
			
			$permiso -> save();
			
			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES PARCIAL ".$this -> post("parcial");
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			/*
			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES PARCIAL ".$this -> post("parcial");
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			*/
			
			$aux1 = 0;
			foreach($calificaciones -> find("curso_id='".$xccurso->id."' ORDER BY id") as $alumno){
				
				$calificacion = $calificaciones -> find_first("id=".$alumno -> id);
				
				$tmp1 = "calificacion".$alumno -> id;
				$tmp2 = "faltas".$alumno -> id;
				
				switch($this -> post("parcial")){
					case 1:
						$calificacion -> calificacion1 = $this -> post($tmp1);
						$calificacion -> faltas1 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion1 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion1 = 500;
						//}
							break;
					case 2:
						$calificacion -> calificacion2 = $this -> post($tmp1);
						$calificacion -> faltas2 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion2 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion2 = 500;
						//}
							break;
					case 3:
						$calificacion -> calificacion3 = $this -> post($tmp1);
						$calificacion -> faltas3 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion3 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion3 = 500;
						//}
							break;
				}
				
				$calificacion -> situacion = "-";
				
				if( $this -> condicionado($calificacion -> registro, $calificacion -> curso)
					&& $calificacion -> calificacion < 70 ){
					$calificacion -> situacion = "BAJA DEFINITIVA";
				}
				
				if( $calificacion -> calificacion == null || $calificacion -> calificacion == '0' )
					$calificacion -> calificacion = '0';
				if( $calificacion -> faltas == null )
					$calificacion -> faltas = '0';
				
				$calificacion -> save();
				
				// Aquí se captura el tiempo en que se hizo la captura especial
				//si es que se había autorizado la captura en destiempo.
				
				if( $aux1 == 0 && ($idcapturaesp != "" || $idcapturaesp != null) ){
					$Periodos = new Periodos();
					
					$fechacaptura = $Periodos -> get_unixtimestamp();
					$xpermesp -> id = $idcapturaesp;
					$xpermesp -> captura = 1;
					$xpermesp -> fecha_captura = $fechacaptura;
					$xpermesp -> update();
					$aux1 = 1;
				}
			}
			$this->redirect('profesor/captura');
        } // function capturar($curso)
		
		function tcapturar(){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			
			$curso = $this -> post("curso");
			
			$Xccursos = new Xtcursos();

			$xcurso = $Xccursos -> find_first("clavecurso='".$curso."'");
			
			$idcapturaesp	= 	$this -> post("idcapturaesp");
			
			$Xpermisoscapturaesp = new Xpermisoscapturaesp();
			if( $idcapturaesp > 0 ){
				$registros_captura_esp = $Xpermisoscapturaesp -> get_alumnos_conpermisosespeciales($idcapturaesp);
			}
			
			if( count($registros_captura_esp) > 0 ){
				$query = "curso_id='".$xcurso->id."'";
				$aux_esp = 0;
				foreach($registros_captura_esp as $reg){
					if( $aux_esp > 0 ) $query .= " OR registro = ".$reg;
					else $query .= " AND ( registro = ".$reg;
					$aux_esp++;
				}
				$query .= ") ORDER BY id";
			}
			else{
				$query = "curso_id='".$xcurso->id."' ORDER BY id";
			}
			
			$calificaciones = new Xtalumnocursos();
			$flag = 3;
			foreach($calificaciones -> find($query) as $alumno){
				if( (strtoupper($this -> post("calificacion".$alumno -> id)) == "NP") || 
					(is_numeric($this -> post("calificacion".$alumno -> id))) ){
					// Asignamos 1 a flag para informar que puede seguir capturando.
					$flag = 1;
				}
				if( !is_numeric($this -> post("calificacion".$alumno -> id)) &&
					(strtoupper($this -> post("calificacion".$alumno -> id)) != "NP") ){
					// Asignamos 3 a flag para saber que NO puede seguir capturando.
					$flag = 3;
				}if(!is_numeric($this -> post("faltas".$alumno -> id)) ){
					// Asignamos 4 a flag para saber que NO puede seguir capturando.
					$flag = 4;
				}if( $this -> post("faltas".$alumno -> id) > $this -> post("horas") ){
					// Asignamos 2 a flag para saber que NO puede seguir capturando.
					$flag = 2;
				}if( $flag == 3 || $flag == 2 || $flag == 4 ){
					Session::set_data('flag', $flag);
					$this->redirect('profesor/captura');
					return;
				}
			}
			
			switch($this -> post("parcial")){
				case 1: $xcurso -> horas1 = $this -> post("horas"); break;
				case 2: $xcurso -> horas2 = $this -> post("horas"); break;
				case 3: $xcurso -> horas3 = $this -> post("horas"); break;
			}
			
			switch($this -> post("parcial")){
				case 1: $xcurso -> avance1 = $this -> post("avance"); break;
				case 2: $xcurso -> avance2 = $this -> post("avance"); break;
				case 3: $xcurso -> avance3 = $this -> post("avance"); break;
			}
			
			$xcurso -> save();
			
			$conpermiso = new Xtpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xcurso->id."'");
			
			$xpermesp		=  new Xpermisoscapturaesp();
			$idcapturaesp	= $this -> post("idcapturaesp");
			
			switch($this -> post("parcial")){
					case 1: $permiso -> ncapturas1 ++; break;
					case 2: $permiso -> ncapturas2 ++; break;
					case 3: $permiso -> ncapturas3 ++; break;
			}

			$permiso -> save();
			
			$log = new Xtlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES PARCIAL ".$this -> post("parcial");
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			
			$aux1 = 0;
			
			foreach($calificaciones -> find("curso_id='".$xcurso->id."' ORDER BY id") as $alumno){
				
				$calificacion = $calificaciones -> find_first("id=".$alumno -> id);
				
				$tmp1 = "calificacion".$alumno -> id;
				$tmp2 = "faltas".$alumno -> id;
				
				switch($this -> post("parcial")){
					case 1:
						$calificacion -> calificacion1 = $this -> post($tmp1);
						$calificacion -> faltas1 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion1 = 999;
						}
						if(strtoupper($this -> post($tmp1)) == "PD"){
							$calificacion -> calificacion1 = 500;
						}
							break;
					case 2:
						$calificacion -> calificacion2 = $this -> post($tmp1);
						$calificacion -> faltas2 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion2 = 999;
						}
						if(strtoupper($this -> post($tmp1)) == "PD"){
							$calificacion -> calificacion2 = 500;
						}
							break;
					case 3:
						$calificacion -> calificacion3 = $this -> post($tmp1);
						$calificacion -> faltas3 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion3 = 999;
						}
						if(strtoupper($this -> post($tmp1)) == "PD"){
							$calificacion -> calificacion3 = 500;
						}
							break;
				}
				
				$calificacion -> situacion = "-";
				
				if( $this -> condicionado($calificacion -> registro, $calificacion -> curso)
					&& $calificacion -> calificacion < 70 ){
					$calificacion -> situacion = "BAJA DEFINITIVA";
				}
				
				if( $calificacion -> calificacion == null || $calificacion -> calificacion == '0' )
					$calificacion -> calificacion = '0';
				if( $calificacion -> faltas == null )
					$calificacion -> faltas = '0';
				
				$calificacion -> save();
				
				// Aquí se captura el tiempo en que se hizo la captura especial
				//si es que se había autorizado la captura en destiempo.
				if( $aux1 == 0 && ($idcapturaesp != "" || $idcapturaesp != null) ){
					$day = date ("d");
					$month = date ("m");
					$year = date ("Y");
					$hour = date ("H");
					$minute = date ("i");
					$second = date ("s");
					
					$fechacaptura = mktime( $hour, $minute, $second, $month, $day, $year );
					$xpermesp -> id = $idcapturaesp;
					$xpermesp -> captura = 1;
					$xpermesp -> fecha_captura = $fechacaptura;
					$xpermesp -> update();
					$aux1 = 1;
				}
			}
			
			$this->redirect('profesor/captura');
        } // function tcapturar($curso)
		
		function capturar_intersemestral(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			
			$periodo = $this -> actual;
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			
			$curso = $this -> post("curso");
			
			$IntersemestralCursos = new IntersemestralCursos();
			
			$IntersemestralCurso = $IntersemestralCursos -> find_first("clavecurso='".$curso."'");
			
			$query = "clavecurso='".$curso."' ORDER BY id";
			
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$flag = 3;
			foreach($IntersemestralAlumnos -> find($query) as $alumno){
				if( (strtoupper($this -> post("calificacion".$alumno -> id)) == "NP") || 
					(is_numeric($this -> post("calificacion".$alumno -> id))) ){
					// Asignamos 1 a flag para informar que puede seguir capturando.
					$flag = 1;
				}
				if( !is_numeric($this -> post("calificacion".$alumno -> id)) &&
					(strtoupper($this -> post("calificacion".$alumno -> id)) != "NP") ){
					// Asignamos 3 a flag para saber que NO puede seguir capturando.
					$flag = 3;
				}if(!is_numeric($this -> post("faltas".$alumno -> id)) ){
					// Asignamos 4 a flag para saber que NO puede seguir capturando.
					$flag = 4;
				}if( $flag == 3 || $flag == 2 || $flag == 4 ){
					Session::set_data('flag', $flag);
					//$this->redirect('profesor/captura');
					//return;
				}
			}
			$IntersemestralCurso -> ncapturas ++;
			$IntersemestralCurso -> save();
			
			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES INTERSEMESTARL";
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			/*
			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES PARCIAL ".$this -> post("parcial");
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			*/
			$Periodos = new Periodos();
			
			$aux1 = 0;
			foreach($IntersemestralAlumnos -> find($query) as $alumno){
				
				$Intersemestral_Alumno = $IntersemestralAlumnos -> find_first("id=".$alumno -> id);
				
				$tmp1 = "calificacion".$alumno -> id;
				$tmp2 = "faltas".$alumno -> id;
				
				$Intersemestral_Alumno -> calificacion = $this -> post($tmp1);
				$Intersemestral_Alumno -> faltas = $this -> post($tmp2);
				if(strtoupper($this -> post($tmp1)) == "NP"){
					$Intersemestral_Alumno -> calificacion = 999;
				}
				
				$Intersemestral_Alumno -> situacion = "-";
				
				$Intersemestral_Alumno -> modificado_at = $Periodos -> get_datetime();
				
				if( $Intersemestral_Alumno -> calificacion == null || $Intersemestral_Alumno -> calificacion == '0' )
					$Intersemestral_Alumno -> calificacion = '0';
				if( $Intersemestral_Alumno -> faltas == null )
					$Intersemestral_Alumno -> faltas = '0';
				
				$Intersemestral_Alumno -> save();
			}
			
			$this->redirect('profesor/captura');
		} // function capturar_intersemestral()
		
        function materiaIrregular($registro, $clavemat){
			
			//$registro contiene el registro del alumno
			//$clavemat contiene la clave de la materia
			
			$xccursos	= new Xccursos();
			$Alumnos	= new Alumnos();
			
			$alumno = $Alumnos -> find_first( "miReg = ".$registro );
			
			if( $alumno -> enPlantel == "c" || $alumno -> enPlantel == "C" ){
				
				//$xccurso = $xccursos -> find_first("clavecurso='".$curso."'");
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc
					inner join xalumnocursos xal
					on xal.curso = xcc.clavecurso
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$clavemat."'" ) as $xcc ){
					// Si el periodo que se trae es el pasado, o uno antes del pasado
					//si nos importa y por lo que esta materia si es irregular
					//asi que debera de tratarse como tal.
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						return true;
					}
				}
				// También reviso en las tablas de Tonala
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xccursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc
					inner join xtalumnocursos xal
					on xal.curso = xtc.clavecurso
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$clavemat."'" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						return true;
					}
				}
			}
			else{
				//$xtcurso = $xtcursos -> find_first("clavecurso='".$curso."'");
				foreach( $xccursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc
					inner join xtalumnocursos xal
					on xal.curso = xtc.clavecurso
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$clavemat."'" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						return true;
					}
				}
				// También reviso en las tablas de Colomos
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc
					inner join xalumnocursos xal
					on xal.curso = xcc.clavecurso
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$clavemat."'" ) as $xcc ){
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						return true;
					}
				}
			}
			return false;
        } // function materiaIrregular($registro, $clavemat)
		
        function condicionado($alumno, $curso){
			
			//$alumno contiene el registro del alumno
			//$curso contiene la clave del curso del alumno
			
			// Para saber si un alumno está condicionado
			$xccursos	= new Xccursos();
			$xtcursos	= new Xtcursos();
			$xalcursos	= new Xalumnocursos();
			$xtalcursos	= new Xtalumnocursos();
			
			$mmateria	= new Materia();
			$alumnos	= new Alumnos();
			
			$alumno = $alumnos -> find_first( "miReg = ".$alumno );
			
			// Si la variable condicionado llega a 2, significa que el alumno si estaba condicionado...
			$condicionado = 0;
			
			if( $alumno -> enPlantel == "c" || $alumno -> enPlantel == "C" ){
				
				$xccurso = $xccursos -> find_first("clavecurso='".$curso."'");
				
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc, xalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$xccurso -> materia."'
					and xal.curso_id = xcc.id" ) as $xcc ){
					// Si el periodo que se trae es el pasado, o uno antes del pasado
					//si nos importa y por lo que esta materia si es irregular
					//asi que debera de tratarse como tal.
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				// También reviso en las tablas de Tonala
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro periodo.
				foreach( $xtcursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc, xtalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$xccurso -> materia."'
					and xal.curso_id = xtc.id" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				if( $xalcursos -> find_all_by_sql("
						select xcc.materia, xal.*
						from xalumnocursos xal
						inner join xccursos xcc
						on xal.curso_id = xcc.id
						and xcc.materia = '".$xccurso -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.registro = '".$alumno -> miReg."'
						and xal.periodo = '".$this -> pasado."'" ) ){
					$condicionado += 2;
				}
				if( $xalcursos -> find_all_by_sql("
						select xcc.materia, xal.*
						from xtalumnocursos xal
						inner join xtcursos xcc
						on xal.curso_id = xcc.id
						and xcc.materia = '".$xtcurso -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.registro = '".$alumno -> miReg."'
						and xal.periodo = '".$this -> pasado."'" ) ){
					$condicionado += 2;
				}
			}
			else{
				$xtcurso = $xtcursos -> find_first("clavecurso='".$curso."'");
				
				foreach( $xtcursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc, xtalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$xtcurso -> materia."'
					and xal.curso_id = xtc.id" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				// También reviso en las tablas de Colomos
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro periodo.
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc, xalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$xtcurso -> materia."'
					and xal.curso_id = xcc.id" ) as $xcc ){
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				if( $xalcursos -> find_all_by_sql("
						select xcc.materia, xal.*
						from xtalumnocursos xal
						inner join xtcursos xcc
						on xal.curso_id = xcc.id
						and xcc.materia = '".$xtcurso -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.registro = '".$alumno -> miReg."'
						and xal.periodo = '".$this -> pasado."'" ) ){
					$condicionado += 2;
				}
				if( $xalcursos -> find_all_by_sql("
					select xcc.materia, xal.*
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso_id = xcc.id
					and xcc.materia = '".$xccurso -> materia."'
					and xal.situacion = 'BAJA DEFINITIVA'
					and xal.registro = '".$alumno -> miReg."'
					and xal.periodo = '".$this -> pasado."'" ) ){
					$condicionado += 2;
				}
			}
			if( $condicionado > 1 )
				return true;
			else
				return false;
        } // function condicionado($alumno, $curso)
		
        function capturarextra($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}

			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			
			$calificaciones = new Xextraordinarios();
			$cursitos = new Registracursos();
			$Xccursos = new Xccursos();
			$xccursos = $Xccursos -> find_first("clavecurso = '".$curso."'");

			$conpermiso = new Xpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xccursos->id."'");
			
			
			// Inicio, Validar que no llegue ningun valor nulo.
			$califsArray = Array();
			foreach($calificaciones -> find("curso_id='".$xccursos->id."' AND periodo=".$periodo." AND tipo='E' AND estado = 'OK'") as $calificacion){
				$tmp1 = "calificacion".$calificacion -> id;
				array_push($califsArray, $this -> post($tmp1));
			}
			if( in_array("", $califsArray) ){
				$this->redirect('profesor/captura');
				return;
			}
			// Fin, Validar que no llegue ningun valor nulo.

			$permiso -> ncapturas4++;
			$permiso -> save();

			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES EXTRAORDINARIO";
			$log -> ip = $this -> getIP();;
			$log -> fecha = time();
			$log -> save();
			
			foreach($calificaciones -> find("curso_id='".$xccursos->id."' AND periodo=".$periodo." AND tipo='E' AND estado = 'OK'") as $calificacion){
				$tmp1 = "calificacion".$calificacion -> id;
				$calificacion -> calificacion = $this -> post($tmp1);

				if(strtoupper($calificacion -> calificacion) == "NP"){
					$calificacion -> calificacion = 999;
				}
				if(strtoupper($calificacion -> calificacion) == "PD"){
					$calificacion -> calificacion = 500;
				}
				$calificacion -> save();
			}
			$this->redirect('profesor/captura');
        } // function capturarextra($curso)

        function Tcapturarextra($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

				$Periodos = new Periodos();
                $periodo = $Periodos -> get_periodo_actual_();

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);

                $calificaciones = new Xtextraordinarios();
                $cursitos = new Registracursos();

				$Xtcursos = new Xtcursos();
				$xtcursos = $Xtcursos->find_first("clavecurso = '".$curso."'");
				
                $conpermiso = new Xtpermisoscaptura();
                $permiso = $conpermiso -> find_first("curso_id='".$xtcursos->id."'");

                $permiso -> ncapturas4++;
                $permiso -> save();

                $log = new Xtlogcalificacion();
                $log -> clavecurso = $curso;
                $log -> nomina = Session::get_data('registro');
                $log -> accion = "ACTUALIZAR CALIFICACIONES EXTRAORDINARIO";
                $log -> ip = $this -> getIP();;
                $log -> fecha = time();
                $log -> save();

                foreach($calificaciones -> find("curso_id='".$xtcursos->id."' AND periodo=".$periodo." AND tipo='E' AND estado = 'OK'") as $calificacion){

						$tmp1 = "calificacion".$calificacion -> id;
						$calificacion -> calificacion = $this -> post($tmp1);

						if(strtoupper($calificacion -> calificacion )== "NP"){
								$calificacion -> calificacion = 999;
						}
						if(strtoupper($calificacion -> calificacion) == "PD"){
								$calificacion -> calificacion = 500;
						}
						$calificacion -> save();

                }
                $this->redirect('profesor/captura');
        } // function Tcapturarextra($curso)

        function capturartitulo($curso){

			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}

			$periodo = $this -> actual;

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);

			$calificaciones = new Xextraordinarios();
			$Xccursos = new Xccursos();
			$xccursos = $Xccursos -> find_first("clavecurso = '".$curso."'");
			$conpermiso = new Xpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xccursos->id."'");

			$permiso -> ncapturas5++;
			$permiso -> save();

			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES TITULO DE SUFICIENCIA";
			$log -> ip = $this -> getIP();;
			$log -> fecha = time();
			$log -> save();

			foreach($calificaciones -> find("curso_id='".$xccursos->id."' AND periodo=".$periodo." AND tipo='T' AND estado = 'OK'") as $calificacion){

				$tmp1 = "calificacion".$calificacion -> id;
				$calificacion -> calificacion = $this -> post($tmp1);

				if(strtoupper($calificacion -> calificacion) == "NP"){
						$calificacion -> calificacion = 999;
				}
				if(strtoupper($calificacion -> calificacion) == "PD"){
						$calificacion -> calificacion = 500;
				}
				$calificacion -> save();
			}
			$this->redirect('profesor/captura');
        } // function capturartitulo($curso)
		
        function Tcapturartitulo($curso){

			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}

			$periodo = $this -> actual;

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);

			$calificaciones = new Xtextraordinarios();
			
			$Xtcursos = new Xtcursos();
			$xtcursos = $Xtcursos -> find_first("clavecurso='".$curso."'");
			
			$conpermiso = new Xtpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xtcursos->id."'");

			$permiso -> ncapturas5++;
			$permiso -> save();

			$log = new Xtlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = Session::get_data('registro');
			$log -> accion = "ACTUALIZAR CALIFICACIONES TITULO DE SUFICIENCIA";
			$log -> ip = $this -> getIP();;
			$log -> fecha = time();
			$log -> save();

			foreach($calificaciones -> find("curso_id='".$xtcursos->id."' AND periodo=".$periodo." AND tipo='T' AND estado = 'OK'") as $calificacion){

				$tmp1 = "calificacion".$calificacion -> id;
				$calificacion -> calificacion = $this -> post($tmp1);

				if(strtoupper($calificacion -> calificacion) == "NP"){
						$calificacion -> calificacion = 999;
				}
				if(strtoupper($calificacion -> calificacion) == "PD"){
						$calificacion -> calificacion = 500;
				}
				$calificacion -> save();
			}
			$this->redirect('profesor/captura');
        } // function Tcapturartitulo($curso){
		
		
        function capturando($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);
			unset($this -> idcapturaesp);

			$maestros = new Maestros();
			$id = Session::get_data('registro');
			$this -> nomina = $id;
			$profesor = $maestros -> find_first("nomina=".$this -> nomina."");
			$this -> profesor = $profesor -> nombre;

			$periodo = $this -> actual;
			
			$xcursos = new Xccursos();
			$pertenece = $xcursos -> count("clavecurso='".$curso."' AND nomina=".$id." AND periodo='".$periodo."'");

			if($pertenece<1){
				$log = new Xlogcalificacion();
				$log -> clavecurso = $curso;
				$log -> nomina = Session::get_data('registro');
				$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
				$log -> ip = $this -> getIP();;
				$log -> fecha = time();
				$log -> save();

				$this->redirect('profesor/captura');
			}


			$Xccursos = new Xccursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xalumnocursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$xccursos = $Xccursos -> find_first("clavecurso='".$curso."'");
			
			if( $this -> post("parcial") == "" ){
				$this->redirect('profesor/captura');
				return;
			}
			
			switch($this -> post("parcial")){
				case 1: $this -> horas = $xccursos -> horas1; break;
				case 2: $this -> horas = $xccursos -> horas2; break;
				case 3: $this -> horas = $xccursos -> horas3; break;
			}

			$this -> horas1 = $xccursos -> horas1;
			$this -> horas2 = $xccursos -> horas2;
			$this -> horas3 = $xccursos -> horas3;

			if($this -> horas==0){
				$this -> horas = "";
			}

			if($this -> horas1==0){
				$this -> horas1 = "-";
			}

			if($this -> horas2==0){
				$this -> horas2 = "-";
			}

			if($this -> horas3==0){
				$this -> horas3 = "-";
			}

			switch($this -> post("parcial")){
				case 1: $this -> avance = $xccursos -> avance1; break;
				case 2: $this -> avance = $xccursos -> avance2; break;
				case 3: $this -> avance = $xccursos -> avance3; break;
			}

			$this -> avance1 = $xccursos -> avance1;
			$this -> avance2 = $xccursos -> avance2;
			$this -> avance3 = $xccursos -> avance3;

			if($this -> avance==0){
				$this -> avance = "";
			}

			if($this -> avance1==0){
				$this -> avance1 = "-";
			}
			else{
				$this -> avance1 .= "%";
			}

			if($this -> avance2==0){
				$this -> avance2 = "-";
			}
			else{
				$this -> avance2 .= "%";
			}

			if($this -> avance3==0){
				$this -> avance3 = "-";
			}
			else{
				$this -> avance3 .= "%";
			}
			$total = 0;
			
			
			$xpermcapturaesp	= new Xpermisoscapturaesp();
			$xpermcapturaespdet	= new XpermisoscapturaespDetalle();
			
			$Periodos = new Periodos();
			
			$fecha = $Periodos -> get_unixtimestamp();
			
			$aux = 0;
			$aux5 = 0;
			foreach( $xpermcapturaesp -> find_all_by_sql
					( "select * from xpermisoscapturaesp
						where clavecurso = '".$xccursos->clavecurso."'
						and parcial = ".$this -> post("parcial")."
						and fin > ".$fecha."
						and captura = 0
						order by id desc
						limit 1" ) as $xpcapesp ){
			$aux5 = 1;
			// Para checar los permisosdecapturasespeciales
				if( $xpermcapturaespdet -> find_first
						( "xpermisoscapturaesp_id = ".$xpcapesp -> id ) ){
					$aux ++;
				}
				$this -> idcapturaesp  = $xpcapesp -> id;
				if( $aux == 1 ){
					foreach( $xpermcapturaespdet -> find
							( "xpermisoscapturaesp_id = ".$xpcapesp -> id ) as $xpdetalle ){
						
						foreach($xalumnocursos -> find
								("curso_id='".$xccursos->id."' 
									and registro = ".$xpdetalle -> registro."
										ORDER BY registro") as $alumno){
							$this -> registro = $alumno -> registro;
							$this -> alumnado[$total]["id"] = $alumno -> id;
							$this -> curso = $curso;
							$this -> materia = $this -> post("materia");
							$this -> clave = $this -> post("clave");

							$parcial = $this -> post("parcial");
							$this -> parcialito = $parcial;

							switch($parcial){
								case 1: $this -> parcial = "PRIMER PARCIAL"; break;
								case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
								case 3: $this -> parcial = "TERCER PARCIAL"; break;
							}

							foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
								$this -> nombre = $a -> vcNomAlu;
								$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
								$situacion = $a -> enTipo;
								$especialidad = $a -> idtiEsp;
								break;
							}

							switch($situacion){
								case 'R': $this -> situacion = "REGULAR"; break;
								case 'I': $this -> situacion = "IRREGULAR"; break;
								case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
								case 'C': $this -> situacion = "CONDICIONADO"; break;
							}

							foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
								$this -> especialidad = $e -> siNumEsp;
								break;
							}

							$this -> alumnado[$total]["registro"] = $this -> registro;
							$this -> alumnado[$total]["nombre"] = $this -> nombre;
							$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
							$this -> alumnado[$total]["situacion"] = $this -> situacion;

							switch($parcial){
								case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
								case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
								case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
							}

							$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
							$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
							$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

							switch($parcial){
								case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
								case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
								case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
							}

							$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
							$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
							$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

							if($this -> alumnado[$total]["calificacion"]==300){
									$this -> alumnado[$total]["calificacion"]="";
									$this -> alumnado[$total]["faltas"]="";
							}

							if($this -> alumnado[$total]["calificacion1"]==300){
									$this -> alumnado[$total]["calificacion1"]="-";
									$this -> alumnado[$total]["faltas1"]="-";
							}

							if($this -> alumnado[$total]["calificacion2"]==300){
									$this -> alumnado[$total]["calificacion2"]="-";
									$this -> alumnado[$total]["faltas2"]="-";
							}

							if($this -> alumnado[$total]["calificacion3"]==300){
									$this -> alumnado[$total]["calificacion3"]="-";
									$this -> alumnado[$total]["faltas3"]="-";
							}

							if($this -> alumnado[$total]["calificacion"]==999){
									$this -> alumnado[$total]["calificacion"]="NP";
							}

							if($this -> alumnado[$total]["calificacion1"]==999){
									$this -> alumnado[$total]["calificacion1"]="NP";
							}

							if($this -> alumnado[$total]["calificacion2"]==999){
									$this -> alumnado[$total]["calificacion2"]="NP";
							}

							if($this -> alumnado[$total]["calificacion3"]==999){
									$this -> alumnado[$total]["calificacion3"]="NP";
							}

							if($this -> alumnado[$total]["calificacion"]==500){
									$this -> alumnado[$total]["calificacion"]="PD";
							}

							if($this -> alumnado[$total]["calificacion1"]==500){
									$this -> alumnado[$total]["calificacion1"]="PD";
							}

							if($this -> alumnado[$total]["calificacion2"]==500){
									$this -> alumnado[$total]["calificacion2"]="PD";
							}

							if($this -> alumnado[$total]["calificacion3"]==500){
									$this -> alumnado[$total]["calificacion3"]="PD";
							}
							
							$total++;
						}
					}
				}
				else{
					foreach($xalumnocursos -> find("curso_id='".$xccursos->id."' ORDER BY registro") as $alumno){
						$this -> registro = $alumno -> registro;
						$this -> alumnado[$total]["id"] = $alumno -> id;
						$this -> curso = $curso;
						$this -> materia = $this -> post("materia");
						$this -> clave = $this -> post("clave");

						$parcial = $this -> post("parcial");
						$this -> parcialito = $parcial;

						switch($parcial){
							case 1: $this -> parcial = "PRIMER PARCIAL"; break;
							case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
							case 3: $this -> parcial = "TERCER PARCIAL"; break;
						}

						foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
							$this -> nombre = $a -> vcNomAlu;
							$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
							$situacion = $a -> enTipo;
							$especialidad = $a -> idtiEsp;
							break;
						}

						switch($situacion){
							case 'R': $this -> situacion = "REGULAR"; break;
							case 'I': $this -> situacion = "IRREGULAR"; break;
							case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
							case 'C': $this -> situacion = "CONDICIONADO"; break;
						}

						foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
							$this -> especialidad = $e -> siNumEsp;
							break;
						}

						$this -> alumnado[$total]["registro"] = $this -> registro;
						$this -> alumnado[$total]["nombre"] = $this -> nombre;
						$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
						$this -> alumnado[$total]["situacion"] = $this -> situacion;

						switch($parcial){
							case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
							case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
							case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
						}

						$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
						$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
						$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

						switch($parcial){
							case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
							case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
							case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
						}

						$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
						$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
						$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

						if($this -> alumnado[$total]["calificacion"]==300){
								$this -> alumnado[$total]["calificacion"]="";
								$this -> alumnado[$total]["faltas"]="";
						}

						if($this -> alumnado[$total]["calificacion1"]==300){
								$this -> alumnado[$total]["calificacion1"]="-";
								$this -> alumnado[$total]["faltas1"]="-";
						}

						if($this -> alumnado[$total]["calificacion2"]==300){
								$this -> alumnado[$total]["calificacion2"]="-";
								$this -> alumnado[$total]["faltas2"]="-";
						}

						if($this -> alumnado[$total]["calificacion3"]==300){
								$this -> alumnado[$total]["calificacion3"]="-";
								$this -> alumnado[$total]["faltas3"]="-";
						}

						if($this -> alumnado[$total]["calificacion"]==999){
								$this -> alumnado[$total]["calificacion"]="NP";
						}

						if($this -> alumnado[$total]["calificacion1"]==999){
								$this -> alumnado[$total]["calificacion1"]="NP";
						}

						if($this -> alumnado[$total]["calificacion2"]==999){
								$this -> alumnado[$total]["calificacion2"]="NP";
						}

						if($this -> alumnado[$total]["calificacion3"]==999){
								$this -> alumnado[$total]["calificacion3"]="NP";
						}

						if($this -> alumnado[$total]["calificacion"]==500){
								$this -> alumnado[$total]["calificacion"]="PD";
						}

						if($this -> alumnado[$total]["calificacion1"]==500){
								$this -> alumnado[$total]["calificacion1"]="PD";
						}

						if($this -> alumnado[$total]["calificacion2"]==500){
								$this -> alumnado[$total]["calificacion2"]="PD";
						}

						if($this -> alumnado[$total]["calificacion3"]==500){
								$this -> alumnado[$total]["calificacion3"]="PD";
						}
						
						$total++;
					}
				}
			}
			if( $aux5 == 0 ){
				foreach($xalumnocursos -> find("curso_id='".$xccursos->id."' ORDER BY registro") as $alumno){
					$this -> registro = $alumno -> registro;
					$this -> alumnado[$total]["id"] = $alumno -> id;
					$this -> curso = $curso;
					$this -> materia = $this -> post("materia");
					$this -> clave = $this -> post("clave");

					$parcial = $this -> post("parcial");
					$this -> parcialito = $parcial;

					switch($parcial){
						case 1: $this -> parcial = "PRIMER PARCIAL"; break;
						case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
						case 3: $this -> parcial = "TERCER PARCIAL"; break;
					}

					foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
						$this -> nombre = $a -> vcNomAlu;
						$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
						$situacion = $a -> enTipo;
						$especialidad = $a -> idtiEsp;
						break;
					}

					switch($situacion){
						case 'R': $this -> situacion = "REGULAR"; break;
						case 'I': $this -> situacion = "IRREGULAR"; break;
						case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
						case 'C': $this -> situacion = "CONDICIONADO"; break;
					}

					foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
						$this -> especialidad = $e -> siNumEsp;
						break;
					}

					$this -> alumnado[$total]["registro"] = $this -> registro;
					$this -> alumnado[$total]["nombre"] = $this -> nombre;
					$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
					$this -> alumnado[$total]["situacion"] = $this -> situacion;

					switch($parcial){
						case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
						case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
						case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
					}

					$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
					$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
					$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

					switch($parcial){
						case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
						case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
						case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
					}

					$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
					$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
					$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

					if($this -> alumnado[$total]["calificacion"]==300){
							$this -> alumnado[$total]["calificacion"]="";
							$this -> alumnado[$total]["faltas"]="";
					}

					if($this -> alumnado[$total]["calificacion1"]==300){
							$this -> alumnado[$total]["calificacion1"]="-";
							$this -> alumnado[$total]["faltas1"]="-";
					}

					if($this -> alumnado[$total]["calificacion2"]==300){
							$this -> alumnado[$total]["calificacion2"]="-";
							$this -> alumnado[$total]["faltas2"]="-";
					}

					if($this -> alumnado[$total]["calificacion3"]==300){
							$this -> alumnado[$total]["calificacion3"]="-";
							$this -> alumnado[$total]["faltas3"]="-";
					}

					if($this -> alumnado[$total]["calificacion"]==999){
							$this -> alumnado[$total]["calificacion"]="NP";
					}

					if($this -> alumnado[$total]["calificacion1"]==999){
							$this -> alumnado[$total]["calificacion1"]="NP";
					}

					if($this -> alumnado[$total]["calificacion2"]==999){
							$this -> alumnado[$total]["calificacion2"]="NP";
					}

					if($this -> alumnado[$total]["calificacion3"]==999){
							$this -> alumnado[$total]["calificacion3"]="NP";
					}

					if($this -> alumnado[$total]["calificacion"]==500){
							$this -> alumnado[$total]["calificacion"]="PD";
					}

					if($this -> alumnado[$total]["calificacion1"]==500){
							$this -> alumnado[$total]["calificacion1"]="PD";
					}

					if($this -> alumnado[$total]["calificacion2"]==500){
							$this -> alumnado[$total]["calificacion2"]="PD";
					}

					if($this -> alumnado[$total]["calificacion3"]==500){
							$this -> alumnado[$total]["calificacion3"]="PD";
					}
					
					$total++;
				}
			}
			
        } // function capturando($curso)

        // Capturar para Tonala
        function tcapturando($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);
			unset($this -> idcapturaesp);

			$maestros = new Maestros();
			$id = Session::get_data('registro');
			$this -> nomina = $id;
			$profesor = $maestros -> find_first("nomina=".$this -> nomina."");
			$this -> profesor = $profesor -> nombre;

			$periodo = $this -> actual;

			$xcursos = new Xtcursos();
			$pertenece = $xcursos -> count("clavecurso='".$curso."' AND nomina=".$id." AND periodo='".$periodo."'");

			if($pertenece<1){
				$log = new Xtlogcalificacion();
				$log -> clavecurso = $curso;
				$log -> nomina = Session::get_data('registro');
				$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
				$log -> ip = $this -> getIP();;
				$log -> fecha = time();
				$log -> save();

				$this->redirect('profesor/captura');
			}


			$Xccursos = new Xtcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xtalumnocursos = new Xtalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();


			$xccursos = $Xccursos -> find_first("clavecurso='".$curso."'");
			if( $this -> post("tparcial") == "" ){
				$this->redirect('profesor/captura');
				return;
			}
			
			switch($this -> post("tparcial")){
					case 1: $this -> horas = $xccursos -> horas1; break;
					case 2: $this -> horas = $xccursos -> horas2; break;
					case 3: $this -> horas = $xccursos -> horas3; break;
			}

			$this -> horas1 = $xccursos -> horas1;
			$this -> horas2 = $xccursos -> horas2;
			$this -> horas3 = $xccursos -> horas3;

			if($this -> horas==0){
					$this -> horas = "";
			}

			if($this -> horas1==0){
					$this -> horas1 = "-";
			}

			if($this -> horas2==0){
					$this -> horas2 = "-";
			}

			if($this -> horas3==0){
					$this -> horas3 = "-";
			}
			
			switch($this -> post("tparcial")){
					case 1: $this -> avance = $xccursos -> avance1; break;
					case 2: $this -> avance = $xccursos -> avance2; break;
					case 3: $this -> avance = $xccursos -> avance3; break;
			}

			$this -> avance1 = $xccursos -> avance1;
			$this -> avance2 = $xccursos -> avance2;
			$this -> avance3 = $xccursos -> avance3;

			if($this -> avance==0){
					$this -> avance = "";
			}

			if($this -> avance1==0){
					$this -> avance1 = "-";
			}
			else{
					$this -> avance1 .= "%";
			}

			if($this -> avance2==0){
					$this -> avance2 = "-";
			}
			else{
					$this -> avance2 .= "%";
			}

			if($this -> avance3==0){
					$this -> avance3 = "-";
			}
			else{
					$this -> avance3 .= "%";
			}
			$total = 0;

			$xpermcapturaesp	= new Xpermisoscapturaesp();
			$xpermcapturaespdet	= new XpermisoscapturaespDetalle();
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			
			$fecha = mktime( $hour, $minute, $second, $month, $day, $year );
			
			$aux = 0;
			$aux5 = 0;
			foreach( $xpermcapturaesp -> find_all_by_sql
					( "select * from xpermisoscapturaesp
						where clavecurso = '".$curso."'
						and parcial = ".$this -> post("tparcial")."
						and fin > ".$fecha."
						and captura = 0
						order by id desc
						limit 1" ) as $xpcapesp ){
				$aux5 = 1;
				// Para checar los permisosdecapturasespeciales
				if( $xpermcapturaespdet -> find_first
						( "xpermisoscapturaesp_id = ".$xpcapesp -> id ) ){
					$aux ++;
				}
				$this -> idcapturaesp  = $xpcapesp -> id;
				if( $aux == 1 ){
					foreach( $xpermcapturaespdet -> find
							( "xpermisoscapturaesp_id = ".$xpcapesp -> id ) as $xpdetalle ){
						
						foreach($xtalumnocursos -> find
								("curso_id='".$xccursos->id."' 
								and registro = ".$xpdetalle -> registro."
								ORDER BY registro") as $alumno){
							$this -> registro = $alumno -> registro;
							$this -> alumnado[$total]["id"] = $alumno -> id;
							$this -> curso = $curso;
							$this -> materia = $this -> post("tmateria");
							$this -> clave = $this -> post("tclave");

							$parcial = $this -> post("tparcial");
							$this -> parcialito = $parcial;

							switch($parcial){
								case 1: $this -> parcial = "PRIMER PARCIAL"; break;
								case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
								case 3: $this -> parcial = "TERCER PARCIAL"; break;
							}

							foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
								$this -> nombre = $a -> vcNomAlu;
								$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
								$situacion = $a -> enTipo;
								$especialidad = $a -> idtiEsp;
								break;
							}

							switch($situacion){
								case 'R': $this -> situacion = "REGULAR"; break;
								case 'I': $this -> situacion = "IRREGULAR"; break;
								case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
								case 'C': $this -> situacion = "CONDICIONADO"; break;
							}

							foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
								$this -> especialidad = $e -> siNumEsp;
								break;
							}

							$this -> alumnado[$total]["registro"] = $this -> registro;
							$this -> alumnado[$total]["nombre"] = $this -> nombre;
							$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
							$this -> alumnado[$total]["situacion"] = $this -> situacion;

							switch($parcial){
								case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
								case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
								case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
							}

							$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
							$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
							$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

							switch($parcial){
								case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
								case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
								case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
							}

							$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
							$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
							$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

							if($this -> alumnado[$total]["calificacion"]==300){
									$this -> alumnado[$total]["calificacion"]="";
									$this -> alumnado[$total]["faltas"]="";
							}

							if($this -> alumnado[$total]["calificacion1"]==300){
									$this -> alumnado[$total]["calificacion1"]="-";
									$this -> alumnado[$total]["faltas1"]="-";
							}

							if($this -> alumnado[$total]["calificacion2"]==300){
									$this -> alumnado[$total]["calificacion2"]="-";
									$this -> alumnado[$total]["faltas2"]="-";
							}

							if($this -> alumnado[$total]["calificacion3"]==300){
									$this -> alumnado[$total]["calificacion3"]="-";
									$this -> alumnado[$total]["faltas3"]="-";
							}

							if($this -> alumnado[$total]["calificacion"]==999){
									$this -> alumnado[$total]["calificacion"]="NP";
							}

							if($this -> alumnado[$total]["calificacion1"]==999){
									$this -> alumnado[$total]["calificacion1"]="NP";
							}

							if($this -> alumnado[$total]["calificacion2"]==999){
									$this -> alumnado[$total]["calificacion2"]="NP";
							}

							if($this -> alumnado[$total]["calificacion3"]==999){
									$this -> alumnado[$total]["calificacion3"]="NP";
							}

							if($this -> alumnado[$total]["calificacion"]==500){
									$this -> alumnado[$total]["calificacion"]="PD";
							}

							if($this -> alumnado[$total]["calificacion1"]==500){
									$this -> alumnado[$total]["calificacion1"]="PD";
							}

							if($this -> alumnado[$total]["calificacion2"]==500){
									$this -> alumnado[$total]["calificacion2"]="PD";
							}

							if($this -> alumnado[$total]["calificacion3"]==500){
									$this -> alumnado[$total]["calificacion3"]="PD";
							}
							$total++;
						}
					}
				}
				else{
					foreach($xtalumnocursos -> find("curso_id='".$xccursos->id."' ORDER BY registro") as $alumno){
						$this -> registro = $alumno -> registro;
						$this -> alumnado[$total]["id"] = $alumno -> id;
						$this -> curso = $curso;
						$this -> materia = $this -> post("tmateria");
						$this -> clave = $this -> post("tclave");

						$parcial = $this -> post("tparcial");
						$this -> parcialito = $parcial;

						switch($parcial){
							case 1: $this -> parcial = "PRIMER PARCIAL"; break;
							case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
							case 3: $this -> parcial = "TERCER PARCIAL"; break;
						}

						foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
							$this -> nombre = $a -> vcNomAlu;
							$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
							$situacion = $a -> enTipo;
							$especialidad = $a -> idtiEsp;
							break;
						}

						switch($situacion){
							case 'R': $this -> situacion = "REGULAR"; break;
							case 'I': $this -> situacion = "IRREGULAR"; break;
							case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
							case 'C': $this -> situacion = "CONDICIONADO"; break;
						}

						foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
							$this -> especialidad = $e -> siNumEsp;
							break;
						}

						$this -> alumnado[$total]["registro"] = $this -> registro;
						$this -> alumnado[$total]["nombre"] = $this -> nombre;
						$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
						$this -> alumnado[$total]["situacion"] = $this -> situacion;

						switch($parcial){
							case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
							case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
							case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
						}

						$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
						$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
						$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

						switch($parcial){
							case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
							case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
							case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
						}

						$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
						$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
						$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

						if($this -> alumnado[$total]["calificacion"]==300){
								$this -> alumnado[$total]["calificacion"]="";
								$this -> alumnado[$total]["faltas"]="";
						}

						if($this -> alumnado[$total]["calificacion1"]==300){
								$this -> alumnado[$total]["calificacion1"]="-";
								$this -> alumnado[$total]["faltas1"]="-";
						}

						if($this -> alumnado[$total]["calificacion2"]==300){
								$this -> alumnado[$total]["calificacion2"]="-";
								$this -> alumnado[$total]["faltas2"]="-";
						}

						if($this -> alumnado[$total]["calificacion3"]==300){
								$this -> alumnado[$total]["calificacion3"]="-";
								$this -> alumnado[$total]["faltas3"]="-";
						}

						if($this -> alumnado[$total]["calificacion"]==999){
								$this -> alumnado[$total]["calificacion"]="NP";
						}

						if($this -> alumnado[$total]["calificacion1"]==999){
								$this -> alumnado[$total]["calificacion1"]="NP";
						}

						if($this -> alumnado[$total]["calificacion2"]==999){
								$this -> alumnado[$total]["calificacion2"]="NP";
						}

						if($this -> alumnado[$total]["calificacion3"]==999){
								$this -> alumnado[$total]["calificacion3"]="NP";
						}

						if($this -> alumnado[$total]["calificacion"]==500){
								$this -> alumnado[$total]["calificacion"]="PD";
						}

						if($this -> alumnado[$total]["calificacion1"]==500){
								$this -> alumnado[$total]["calificacion1"]="PD";
						}

						if($this -> alumnado[$total]["calificacion2"]==500){
								$this -> alumnado[$total]["calificacion2"]="PD";
						}

						if($this -> alumnado[$total]["calificacion3"]==500){
								$this -> alumnado[$total]["calificacion3"]="PD";
						}
						
						$total++;
					}
				}
			}
			if( $aux5 == 0 ){
                foreach($xtalumnocursos -> find("curso_id='".$xccursos->id."' ORDER BY registro") as $alumno){
					$this -> registro = $alumno -> registro;
					$this -> alumnado[$total]["id"] = $alumno -> id;
					$this -> curso = $curso;
					$this -> materia = $this -> post("tmateria");
					$this -> clave = $this -> post("tclave");

					$parcial = $this -> post("tparcial");
					$this -> parcialito = $parcial;

					switch($parcial){
							case 1: $this -> parcial = "PRIMER PARCIAL"; break;
							case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
							case 3: $this -> parcial = "TERCER PARCIAL"; break;
					}

					foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
							$this -> nombre = $a -> vcNomAlu;
							$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
							$situacion = $a -> enTipo;
							$especialidad = $a -> idtiEsp;
							break;
					}

					switch($situacion){
							case 'R': $this -> situacion = "REGULAR"; break;
							case 'I': $this -> situacion = "IRREGULAR"; break;
							case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
							case 'C': $this -> situacion = "CONDICIONADO"; break;
					}

					foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
							$this -> especialidad = $e -> siNumEsp;
							break;
					}
					
					$this -> alumnado[$total]["registro"] = $this -> registro;
					$this -> alumnado[$total]["nombre"] = $this -> nombre;
					$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
					$this -> alumnado[$total]["situacion"] = $this -> situacion;

					switch($parcial){
							case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
							case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
							case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
					}

					$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
					$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
					$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

					switch($parcial){
							case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
							case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
							case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
					}

					$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
					$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
					$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

					if($this -> alumnado[$total]["calificacion"]==300){
							$this -> alumnado[$total]["calificacion"]="";
							$this -> alumnado[$total]["faltas"]="";
					}

					if($this -> alumnado[$total]["calificacion1"]==300){
							$this -> alumnado[$total]["calificacion1"]="-";
							$this -> alumnado[$total]["faltas1"]="-";
					}

					if($this -> alumnado[$total]["calificacion2"]==300){
							$this -> alumnado[$total]["calificacion2"]="-";
							$this -> alumnado[$total]["faltas2"]="-";
					}

					if($this -> alumnado[$total]["calificacion3"]==300){
							$this -> alumnado[$total]["calificacion3"]="-";
							$this -> alumnado[$total]["faltas3"]="-";
					}

					if($this -> alumnado[$total]["calificacion"]==999){
							$this -> alumnado[$total]["calificacion"]="NP";
					}

					if($this -> alumnado[$total]["calificacion1"]==999){
							$this -> alumnado[$total]["calificacion1"]="NP";
					}

					if($this -> alumnado[$total]["calificacion2"]==999){
							$this -> alumnado[$total]["calificacion2"]="NP";
					}

					if($this -> alumnado[$total]["calificacion3"]==999){
							$this -> alumnado[$total]["calificacion3"]="NP";
					}

					if($this -> alumnado[$total]["calificacion"]==500){
							$this -> alumnado[$total]["calificacion"]="PD";
					}

					if($this -> alumnado[$total]["calificacion1"]==500){
							$this -> alumnado[$total]["calificacion1"]="PD";
					}

					if($this -> alumnado[$total]["calificacion2"]==500){
							$this -> alumnado[$total]["calificacion2"]="PD";
					}

					if($this -> alumnado[$total]["calificacion3"]==500){
							$this -> alumnado[$total]["calificacion3"]="PD";
					}
					$total++;
                }
			}
        } // function tcapturando($curso)

        function capturando_intersemestral($curso){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);
			unset($this -> idcapturaesp);
			
			$Maestros = new Maestros();
			$nomina = Session::get_data('registro');
			$this -> nomina = $nomina;
			$profesor = $Maestros -> find_first("nomina=".$nomina."");
			$this -> profesor = $profesor -> nombre;
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			//$periodo = $this -> actual;
			
			$IntersemestralCursos = new IntersemestralCursos();
			$cuenta_icursos = $IntersemestralCursos -> count("clavecurso='".$curso."' AND nomina=".$nomina." AND periodo='".$periodo."'");
			
			if($cuenta_icursos<1){
				$log = new Xlogcalificacion();
				$log -> clavecurso = $curso;
				$log -> nomina = Session::get_data('registro');
				$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
				$log -> ip = $this -> getIP();;
				$log -> fecha = time();
				$log -> save();
				
				$this->redirect('profesor/captura');
			}
			
			$IntersemestralCursos = new IntersemestralCursos();
			$IntersemestralCurso = $IntersemestralCursos -> find_first("clavecurso='".$curso."'");
			
			$Maestros = new Maestros();
			$Materia = new Materia();
			$this -> materia = $Materia -> find_first("clave = '".$IntersemestralCurso->clavemat."'");
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$Alumnos = new Alumnos();
			
			
			$total = 0;
			
			$Periodos = new Periodos();
			$fecha = $Periodos -> get_unixtimestamp();
			
			$aux5 = 0;
			$aux5 = 1;
			
			$this -> curso = $curso;
			foreach($IntersemestralAlumnos -> find("clavecurso='".$curso."' and activo = 1 ORDER BY registro") as $alumno){
				//$this -> registro = $alumno -> registro;
				$this -> alumnado[$total]["id"] = $alumno -> id;
				//$this -> materia = $this -> post("materia");
				//$this -> clave = $this -> post("clave");
				
				//$parcial = $this -> post("parcial");
				//$this -> parcialito = $parcial;
				
				//switch($parcial){
					//case 1: $this -> parcial = "PRIMER PARCIAL"; break;
					//case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
					//case 3: $this -> parcial = "TERCER PARCIAL"; break;
				//}
				
				foreach($Alumnos -> find("miReg=".$alumno->registro) as $a){
					$this -> nombre = $a -> vcNomAlu;
					$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
					$situacion = $a -> enTipo;
					//$especialidad = $a -> idtiEsp;
				}
				
				switch($situacion){
					case 'R': $this -> situacion = "REGULAR"; break;
					case 'I': $this -> situacion = "IRREGULAR"; break;
					case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
					case 'C': $this -> situacion = "CONDICIONADO"; break;
				}
				
				$this -> alumnado[$total]["registro"] = $alumno -> registro;
				$this -> alumnado[$total]["nombre"] = $this -> nombre;
				$this -> alumnado[$total]["situacion"] = $this -> situacion;
				
				$this -> alumnado[$total]["faltas"] = $alumno -> faltas;
				
				$this -> alumnado[$total]["calificacion"] = $alumno -> calificacion;
				
				if($this -> alumnado[$total]["calificacion"]==300){
					$this -> alumnado[$total]["calificacion"]="";
					$this -> alumnado[$total]["faltas"]="";
				}

				if($this -> alumnado[$total]["calificacion"]==999){
					$this -> alumnado[$total]["calificacion"]="NP";
				}
				
				if($this -> alumnado[$total]["calificacion"]==500){
					$this -> alumnado[$total]["calificacion"]="PD";
				}
				
				$total++;
			}
        } // function capturando_intersemestral($curso)

		
        function capturandoCalc($curso){
                //if(Session::get_data('tipousuario')!="CALCULO"){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }
                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $maestros = new Maestros();
                $id = Session::get_data('registro');
                $this -> nomina = $id;
                $profesor = $maestros -> find_first("nomina=".$this -> nomina."");
                $this -> profesor = $profesor -> nombre;

               $periodo = $this -> actual;

                $xcursos = new Xccursos();
                $pertenece = $xcursos -> count("clavecurso='".$curso."' AND nomina=".$id." AND periodo='".$periodo."'");

                if($pertenece<1){
                        $log = new Xlogcalificacion();
                        $log -> clavecurso = $curso;
                        $log -> nomina = Session::get_data('registro');
                        $log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
                        $log -> ip = $this -> getIP();;
                        $log -> fecha = time();
                        $log -> save();

                        $this->redirect('profesor/capturaCalculo');
                }


                $xcursos = new Xcursos();
                $maestros = new Maestros();
                $materias = new Materia();
                $xcalificaciones = new Xalumnocursos();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();


                $avance = $xcursos -> find_first("clavecurso='".$curso."'");

                switch($this -> post("parcial")){
                        case 1: $this -> horas = $avance -> horas1; break;
                        case 2: $this -> horas = $avance -> horas2; break;
                        case 3: $this -> horas = $avance -> horas3; break;
                }

                $this -> horas1 = $avance -> horas1;
                $this -> horas2 = $avance -> horas2;
                $this -> horas3 = $avance -> horas3;

                if($this -> horas==0){
                        $this -> horas = "";
                }

                if($this -> horas1==0){
                        $this -> horas1 = "-";
                }

                if($this -> horas2==0){
                        $this -> horas2 = "-";
                }

                if($this -> horas3==0){
                        $this -> horas3 = "-";
                }

                switch($this -> post("parcial")){
                        case 1: $this -> avance = $avance -> avance1; break;
                        case 2: $this -> avance = $avance -> avance2; break;
                        case 3: $this -> avance = $avance -> avance3; break;
                }

                $this -> avance1 = $avance -> avance1;
                $this -> avance2 = $avance -> avance2;
                $this -> avance3 = $avance -> avance3;

                if($this -> avance==0){
                        $this -> avance = "";
                }

                if($this -> avance1==0){
                        $this -> avance1 = "-";
                }
                else{
                        $this -> avance1 .= "%";
                }

                if($this -> avance2==0){
                        $this -> avance2 = "-";
                }
                else{
                        $this -> avance2 .= "%";
                }

                if($this -> avance3==0){
                        $this -> avance3 = "-";
                }
                else{
                        $this -> avance3 .= "%";
                }
                $total = 0;

                foreach($xcalificaciones -> find("curso='".$curso."' ORDER BY registro") as $alumno){
                        $this -> registro = $alumno -> registro;
                        $this -> alumnado[$total]["id"] = $alumno -> id;
                        $this -> curso = $curso;
                        $this -> materia = $this -> post("materia");
                        $this -> clave = $this -> post("clave");

                        $parcial = $this -> post("parcial");
                        $this -> parcialito = $parcial;

                        switch($parcial){
                                case 1: $this -> parcial = "PRIMER PARCIAL"; break;
                                case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
                                case 3: $this -> parcial = "TERCER PARCIAL"; break;
                        }

                        foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
                                $this -> nombre = $a -> vcNomAlu;
                                $this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
                                $situacion = $a -> enTipo;
                                $especialidad = $a -> idtiEsp;
                                break;
                        }

                        switch($situacion){
                                case 'R': $this -> situacion = "REGULAR"; break;
                                case 'I': $this -> situacion = "IRREGULAR"; break;
                                case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
                                case 'C': $this -> situacion = "CONDICIONADO"; break;
                        }

                        foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
                                $this -> especialidad = $e -> siNumEsp;
                                break;
                        }

                                $this -> alumnado[$total]["registro"] = $this -> registro;
                                $this -> alumnado[$total]["nombre"] = $this -> nombre;
                                $this -> alumnado[$total]["especialidad"] = $this -> especialidad;
                                $this -> alumnado[$total]["situacion"] = $this -> situacion;

                                switch($parcial){
                                        case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
                                        case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
                                        case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
                                }

                                $this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
                                $this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
                                $this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

                                switch($parcial){
                                        case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
                                        case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
                                        case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
                                }

                                $this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
                                $this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
                                $this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

                                if($this -> alumnado[$total]["calificacion"]==300){
                                        $this -> alumnado[$total]["calificacion"]="";
                                        $this -> alumnado[$total]["faltas"]="";
                                }

                                if($this -> alumnado[$total]["calificacion1"]==300){
                                        $this -> alumnado[$total]["calificacion1"]="-";
                                        $this -> alumnado[$total]["faltas1"]="-";
                                }

                                if($this -> alumnado[$total]["calificacion2"]==300){
                                        $this -> alumnado[$total]["calificacion2"]="-";
                                        $this -> alumnado[$total]["faltas2"]="-";
                                }

                                if($this -> alumnado[$total]["calificacion3"]==300){
                                        $this -> alumnado[$total]["calificacion3"]="-";
                                        $this -> alumnado[$total]["faltas3"]="-";
                                }

                                if($this -> alumnado[$total]["calificacion"]==999){
                                        $this -> alumnado[$total]["calificacion"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion1"]==999){
                                        $this -> alumnado[$total]["calificacion1"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion2"]==999){
                                        $this -> alumnado[$total]["calificacion2"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion3"]==999){
                                        $this -> alumnado[$total]["calificacion3"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion"]==500){
                                        $this -> alumnado[$total]["calificacion"]="PD";
                                }

                                if($this -> alumnado[$total]["calificacion1"]==500){
                                        $this -> alumnado[$total]["calificacion1"]="PD";
                                }

                                if($this -> alumnado[$total]["calificacion2"]==500){
                                        $this -> alumnado[$total]["calificacion2"]="PD";
                                }

                                if($this -> alumnado[$total]["calificacion3"]==500){
                                        $this -> alumnado[$total]["calificacion3"]="PD";
                                }
                        $total++;

                }
        } // function capturandoCalc($curso)

        function capturandoextras($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }
				
                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');

				$Periodos = new Periodos();
                $periodo = $Periodos->get_periodo_actual_();

                $Xccursos = new Xccursos();
                $pertenece = $Xccursos -> count("clavecurso='".$curso."' and nomina=".$id." and periodo='".$periodo."'");
				
				$xccursos = $Xccursos -> find_first("clavecurso = '".$curso."'");

                if($pertenece<1){
					$log = new Xlogcalificacion();
					$log -> clavecurso = $curso;
					$log -> nomina = Session::get_data('registro');
					$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
					$log -> ip = $this -> getIP();;
					$log -> fecha = time();
					$log -> save();

					$this->redirect('profesor/captura');
                }

                $maestros = new Maestros();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
                $xextraordinarios = new Xextraordinarios();
                $xcalificacion = new Xalumnocursos();
                $avances = new Profavance();

                $this -> curso = $curso;
                $this -> materia = $this -> post("materia");
                $this -> clave = $this -> post("clave");

                //foreach($cursitos -> find_all_by_sql("SELECT mireg FROM registracursos WHERE curso='".$curso."' ORDER BY mireg") as $registro){
                        //$mireg = $registro -> mireg;

				foreach($xextraordinarios -> find("tipo='E' AND curso_id='".$xccursos->id."' AND periodo=".$periodo." AND estado = 'OK' ORDER BY registro") as $alumno){

					$this -> registro = $alumno -> registro;
					$this -> alumnado[$total]["id"] = $alumno -> id;

					$calificacion = $xcalificacion -> find_first("registro=".$alumno -> registro." AND curso_id='".$xccursos->id."' AND periodo=".$periodo);

					$this -> alumnado[$total]["c1"] = $calificacion -> calificacion1;
					$this -> alumnado[$total]["c2"] = $calificacion -> calificacion2;
					$this -> alumnado[$total]["c3"] = $calificacion -> calificacion3;

					$parcial = $this -> post("parcial");
					$this -> parcialito = $parcial;

					foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
							$this -> nombre = $a -> vcNomAlu;
							$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
							$situacion = $a -> enTipo;
							$especialidad = $a -> idtiEsp;
							break;
					}

					switch($situacion){
							case 'R': $this -> situacion = "REGULAR"; break;
							case 'I': $this -> situacion = "IRREGULAR"; break;
							case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
							case 'C': $this -> situacion = "CONDICIONADO"; break;
					}

					foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
							$this -> especialidad = $e -> siNumEsp;
							break;
					}

					$this -> alumnado[$total]["registro"] = $this -> registro;
					$this -> alumnado[$total]["nombre"] = $this -> nombre;
					$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
					$this -> alumnado[$total]["situacion"] = $this -> situacion;

					$this -> alumnado[$total]["calificacion"] = $alumno -> calificacion;

					if($this -> alumnado[$total]["calificacion"]==300){
							$this -> alumnado[$total]["calificacion"]="";
					}

					if($this -> alumnado[$total]["calificacion"]==999){
							$this -> alumnado[$total]["calificacion"]="NP";
					}

					if($this -> alumnado[$total]["calificacion"]==500){
							$this -> alumnado[$total]["calificacion"]="PD";
					}
					$total++;

				}
                //}

                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }

				if( substr($periodo, 0, 1) == 1)
					$this -> periodo = "FEB - JUN, ";
				else
					$this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        } // function capturandoextras($curso)

        function Tcapturandoextras($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }
                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');

                $periodo = $this -> actual;
				
                $xcursos = new Xtcursos();
                $pertenece = $xcursos -> count("clavecurso='".$curso."' and nomina=".$id." and periodo='".$periodo."'");

				$xccursos = $xcursos -> find_first("clavecurso = '".$curso."'");
				
                if($pertenece<1){
					$log = new Xtlogcalificacion();
					$log -> clavecurso = $curso;
					$log -> nomina = Session::get_data('registro');
					$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
					$log -> ip = $this -> getIP();;
					$log -> fecha = time();
					$log -> save();

					$this->redirect('profesor/captura');
                }


                $maestros = new Maestros();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
                $xextraordinarios = new Xtextraordinarios();
                $xcalificacion = new Xtalumnocursos();
                $avances = new Profavance();

                $this -> curso = $curso;
                $this -> materia = $this -> post("materia");
                $this -> clave = $this -> post("clave");


                //foreach($cursitos -> find_all_by_sql("SELECT mireg FROM registracursos WHERE curso='".$curso."' ORDER BY mireg") as $registro){
                        //$mireg = $registro -> mireg;

                        foreach($xextraordinarios -> find("tipo='E' AND curso_id='".$xccursos->id."' AND periodo=".$periodo." AND estado = 'OK' ORDER BY registro") as $alumno){

                                $this -> registro = $alumno -> registro;
                                $this -> alumnado[$total]["id"] = $alumno -> id;

                                $calificacion = $xcalificacion -> find_first("registro=".$alumno -> registro." AND curso_id='".$xccursos->id."' AND periodo=".$periodo);

                                $this -> alumnado[$total]["c1"] = $calificacion -> calificacion1;
                                $this -> alumnado[$total]["c2"] = $calificacion -> calificacion2;
                                $this -> alumnado[$total]["c3"] = $calificacion -> calificacion3;

                                $parcial = $this -> post("parcial");
                                $this -> parcialito = $parcial;

                                foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
                                        $this -> nombre = $a -> vcNomAlu;
                                        $this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
                                        $situacion = $a -> enTipo;
                                        $especialidad = $a -> idtiEsp;
                                        break;
                                }

                                switch($situacion){
                                        case 'R': $this -> situacion = "REGULAR"; break;
                                        case 'I': $this -> situacion = "IRREGULAR"; break;
                                        case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
                                        case 'C': $this -> situacion = "CONDICIONADO"; break;
                                }

                                foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
                                        $this -> especialidad = $e -> siNumEsp;
                                        break;
                                }

                                $this -> alumnado[$total]["registro"] = $this -> registro;
                                $this -> alumnado[$total]["nombre"] = $this -> nombre;
                                $this -> alumnado[$total]["especialidad"] = $this -> especialidad;
                                $this -> alumnado[$total]["situacion"] = $this -> situacion;

                                $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion;

                                if($this -> alumnado[$total]["calificacion"]==300){
                                        $this -> alumnado[$total]["calificacion"]="";
                                }

                                if($this -> alumnado[$total]["calificacion"]==999){
                                        $this -> alumnado[$total]["calificacion"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion"]==500){
                                        $this -> alumnado[$total]["calificacion"]="PD";
                                }
                                $total++;

                        }
                //}

                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }


                if($periodo[0]=='1')
                        $this -> periodo = "FEB - JUN, ";
                else
                        $this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        } // function Tcapturandoextras($curso)

        function capturandotitulos($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }
                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
                $periodo = $this -> actual;
				
                $xcursos = new Xccursos();
                $pertenece = $xcursos -> count("clavecurso='".$curso."' and nomina=".$id." and periodo='".$periodo."'");
                if( $pertenece < 1 )
                        $pertenece = $xcursos -> count("clavecurso='".$curso."'");
				
				$xccursos = $xcursos -> find_first("clavecurso = '".$curso."'");
				
                if($pertenece<1){
                        $log = new Xlogcalificacion();
                        $log -> clavecurso = $curso;
                        $log -> nomina = Session::get_data('registro');
                        $log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
                        $log -> ip = $this -> getIP();
                        $log -> fecha = time();
                        $log -> save();

                        $this->redirect('profesor/captura');
                }


                $maestros = new Maestros();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
                $xextraordinarios = new Xextraordinarios();
                $xcalificacion = new Xalumnocursos();
                $avances = new Profavance();

                $this -> curso = $curso;
                $this -> materia = $this -> post("materia");
                $this -> clave = $this -> post("clave");


                //foreach($cursitos -> find_all_by_sql("SELECT mireg FROM registracursos WHERE curso='".$curso."' ORDER BY mireg") as $registro){
                        //$mireg = $registro -> mireg;

                        foreach($xextraordinarios -> find("tipo='T' AND curso_id='".$xccursos->id."' AND periodo=".$periodo." AND estado = 'OK' ORDER BY registro") as $alumno){

                                $this -> registro = $alumno -> registro;
                                $this -> alumnado[$total]["id"] = $alumno -> id;

                                $calificacion = $xcalificacion -> find_first("registro=".$alumno -> registro." AND curso_id='".$xccursos->id."' AND periodo=".$periodo);

                                $this -> alumnado[$total]["c1"] = $calificacion -> calificacion1;
                                $this -> alumnado[$total]["c2"] = $calificacion -> calificacion2;
                                $this -> alumnado[$total]["c3"] = $calificacion -> calificacion3;

                                $parcial = $this -> post("parcial");
                                $this -> parcialito = $parcial;

                                foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
                                        $this -> nombre = $a -> vcNomAlu;
                                        $this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
                                        $situacion = $a -> enTipo;
                                        $especialidad = $a -> idtiEsp;
                                        break;
                                }

                                switch($situacion){
                                        case 'R': $this -> situacion = "REGULAR"; break;
                                        case 'I': $this -> situacion = "IRREGULAR"; break;
                                        case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
                                        case 'C': $this -> situacion = "CONDICIONADO"; break;
                                }

                                foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
                                        $this -> especialidad = $e -> siNumEsp;
                                        break;
                                }

                                $this -> alumnado[$total]["registro"] = $this -> registro;
                                $this -> alumnado[$total]["nombre"] = $this -> nombre;
                                $this -> alumnado[$total]["especialidad"] = $this -> especialidad;
                                $this -> alumnado[$total]["situacion"] = $this -> situacion;

                                $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion;

                                if($this -> alumnado[$total]["calificacion"]==300){
                                        $this -> alumnado[$total]["calificacion"]="";
                                }

                                if($this -> alumnado[$total]["calificacion"]==999){
                                        $this -> alumnado[$total]["calificacion"]="NP";
                                }

                                if($this -> alumnado[$total]["calificacion"]==500){
                                        $this -> alumnado[$total]["calificacion"]="PD";
                                }
                                $total++;

                        }
                //}

                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }


                if($periodo[0]=='1')
                        $this -> periodo = "FEB - JUN, ";
                else
                        $this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        }

        function Tcapturandotitulos($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }
                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
                $periodo = $this -> actual;

                $xcursos = new Xtcursos();
                $pertenece = $xcursos -> count("clavecurso='".$curso."' and nomina=".$id." and periodo='".$periodo."'");
                if( $pertenece < 1 )
                        $pertenece = $xcursos -> count("clavecurso='".$curso."'");
				
				$xccursos = $xcursos -> find_first("clavecurso = '".$curso."'");
				
                if($pertenece<1){
                        $log = new Xtlogcalificacion();
                        $log -> clavecurso = $curso;
                        $log -> nomina = Session::get_data('registro');
                        $log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
                        $log -> ip = $this -> getIP();
                        $log -> fecha = time();
                        $log -> save();

                        $this->redirect('profesor/captura');
                }


                $maestros = new Maestros();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
                $xextraordinarios = new Xtextraordinarios();
                $xcalificacion = new Xtalumnocursos();
                $avances = new Profavance();

                $this -> curso = $curso;
                $this -> materia = $this -> post("materia");
                $this -> clave = $this -> post("clave");


                //foreach($cursitos -> find_all_by_sql("SELECT mireg FROM registracursos WHERE curso='".$curso."' ORDER BY mireg") as $registro){
                        //$mireg = $registro -> mireg;

					foreach($xextraordinarios -> find("tipo='T' AND curso_id='".$xccursos->id."' AND periodo=".$periodo." AND estado = 'OK' ORDER BY registro") as $alumno){

							$this -> registro = $alumno -> registro;
							$this -> alumnado[$total]["id"] = $alumno -> id;

							$calificacion = $xcalificacion -> find_first("registro=".$alumno -> registro." AND curso_id='".$xccursos->id."' AND periodo=".$periodo);

							$this -> alumnado[$total]["c1"] = $calificacion -> calificacion1;
							$this -> alumnado[$total]["c2"] = $calificacion -> calificacion2;
							$this -> alumnado[$total]["c3"] = $calificacion -> calificacion3;

							$parcial = $this -> post("parcial");
							$this -> parcialito = $parcial;

							foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
									$this -> nombre = $a -> vcNomAlu;
									$this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
									$situacion = $a -> enTipo;
									$especialidad = $a -> idtiEsp;
									break;
							}

							switch($situacion){
									case 'R': $this -> situacion = "REGULAR"; break;
									case 'I': $this -> situacion = "IRREGULAR"; break;
									case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
									case 'C': $this -> situacion = "CONDICIONADO"; break;
							}

							foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
									$this -> especialidad = $e -> siNumEsp;
									break;
							}

							$this -> alumnado[$total]["registro"] = $this -> registro;
							$this -> alumnado[$total]["nombre"] = $this -> nombre;
							$this -> alumnado[$total]["especialidad"] = $this -> especialidad;
							$this -> alumnado[$total]["situacion"] = $this -> situacion;

							$this -> alumnado[$total]["calificacion"] = $alumno -> calificacion;

							if($this -> alumnado[$total]["calificacion"]==300){
									$this -> alumnado[$total]["calificacion"]="";
							}

							if($this -> alumnado[$total]["calificacion"]==999){
									$this -> alumnado[$total]["calificacion"]="NP";
							}

							if($this -> alumnado[$total]["calificacion"]==500){
									$this -> alumnado[$total]["calificacion"]="PD";
							}
							$total++;

					}
                //}

                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }


                if($periodo[0]=='1')
                        $this -> periodo = "FEB - JUN, ";
                else
                        $this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        } // function Tcapturandotitulos($curso)

		
        function veracta($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                $periodo = $this -> actual;

                //Tendrá que llegar por medio de un post
                $parcial = $this -> post("parcial");

                $this->redirect("public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");
        }

        function generaracta($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
                $periodo = $this -> actual;

                //Tendrá que llegar por medio de un post
                $parcial = $this -> post("parcial");

                $maestros = new Maestros();
                $maestro = $maestros -> find_first("nomina=".$id);

                $xcursos = new Xccursos();
                $materias = new Materia();
                $calificaciones = new Xalumnocursos();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();

                switch($parcial){
                        case 1: $parcialito = "PRIMER PARCIAL"; break;
                        case 2: $parcialito = "SEGUNDO PARCIAL"; break;
                        case 3: $parcialito = "TERCER PARCIAL"; break;
                }


                $xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
                $materia = $materias -> find_first("clave='".$xcurso -> materia."'");

                $this -> set_response("view");

                $reporte = new FPDF();

                $reporte -> Open();
                $reporte -> AddPage();

                $reporte -> AddFont('Verdana','','verdana.php');

                $reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',14);
                $reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: FEB - JUN, 2008",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
                $reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
                $reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
                $reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
                $reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(20,4,$id,1,0,'C',1);
                $reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
                $reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
                $reporte -> Cell(25,4,$curso,1,0,'C',1);
                $reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',8);

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(121,6,"",1,0,'C',1);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(69,6,$parcialito,1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(8,4,"No.",1,0,'C',1);
                $reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
                $reporte -> Cell(60,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
                $reporte -> Cell(20,4,"ESPECIALIDAD",1,0,'C',1);
                $reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
                $reporte -> Cell(30,4,"FALTAS",1,0,'C',1);
                $reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                foreach($calificaciones -> find("clavecurso='".$curso."' ORDER BY registro") as $calificacion){
                        $n++;

                        switch($parcial){
                                case 1: if($calificacion -> calificacion1 > 60 && $calificacion -> calificacion1 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion1 >= 0 && $calificacion -> calificacion1 <= 100 ) { $promedio += $calificacion -> calificacion1; $np++;} break;
                                case 2: if($calificacion -> calificacion2 > 60 && $calificacion -> calificacion2 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion2 >= 0 && $calificacion -> calificacion2 <= 100 ) { $promedio += $calificacion -> calificacion2; $np++;} break;
                                case 3: if($calificacion -> calificacion3 > 60 && $calificacion -> calificacion3 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion3 >= 0 && $calificacion -> calificacion3 <= 100 ) { $promedio += $calificacion -> calificacion3; $np++;} break;
                        }

                        switch($parcial){
                                case 1: $faltas = $calificacion -> faltas1; break;
                                case 2: $faltas = $calificacion -> faltas2; break;
                                case 3: $faltas = $calificacion -> faltas3; break;
                        }

                        switch($parcial){
                                case 1: $cal = $calificacion -> calificacion1; break;
                                case 2: $cal = $calificacion -> calificacion2; break;
                                case 3: $cal = $calificacion -> calificacion3; break;
                        }

                        switch($cal){
                                case 300: $cal="-"; $faltas="-"; break;
                                case 500: $cal="PND"; break;
                                case 999: $cal="NP"; $nps++; break;
                        }

                        $faltasletra = $this -> numero_letra($faltas);
                        $calletra = $this -> numero_letra($cal);

                        $alumno = $alumnos -> find_first("miReg=".$calificacion -> mireg);

                        $especialidad = $especialidades -> find_first("idtiEsp=".$alumno -> idtiEsp);

                        $reporte -> Cell(8,4,$n,1,0,'C',1);
                        $reporte -> Cell(18,4,$calificacion -> mireg,1,0,'C',1);
                        $reporte -> Cell(60,4,$alumno -> vcNomAlu,1,0,'L',1);
                        $reporte -> Cell(20,4,$especialidad -> siNumEsp,1,0,'C',1);
                        $reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
                        $reporte -> Cell(10,4,$faltas,1,0,'C',1);
                        $reporte -> Cell(20,4,$faltasletra,1,0,'C',1);
                        $reporte -> Cell(10,4,$cal,1,0,'C',1);
                        $reporte -> Cell(29,4,$calletra,1,0,'C',1);
                        $reporte -> Ln();
                }

                $promedio /= $np;
                $aprobados += 0;
                $reprobados += 0;
                $nps += 0;

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',7);

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(25,5,"HORAS CLASE",1,0,'C',1);
                $reporte -> Cell(25,5,"AVANCE",1,0,'C',1);
                $reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

                $reporte -> Ln();

                $avance = $xcursos -> find_first("clavecurso='".$curso."'");

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                switch($parcial){
                        case 1: $reporte -> Cell(25,5,$avance -> horas1,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance1."%",1,0,'C',1);break;
                        case 2: $reporte -> Cell(25,5,$avance -> horas2,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance2."%",1,0,'C',1);break;
                        case 3: $reporte -> Cell(25,5,$avance -> horas3,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance3."%",1,0,'C',1);break;
                }

                $reporte -> Cell(26,5,$nps,1,0,'C',1);
                $reporte -> Cell(38,5,$aprobados,1,0,'C',1);
                $reporte -> Cell(38,5,$reprobados,1,0,'C',1);

                $promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

                $reporte -> Cell(38,5,$promedio,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(70,15,"",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);

                $reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");

                $this->redirect("public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");
        }

        function tacta($curso){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = Session::get_data('registro');
			//$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();

			//Tendrá que llegar por medio de un post
			$parcial = $this -> post("tparcial");

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$xcursos = new Xtcursos();
			$materias = new Materia();
			$calificaciones = new Xtalumnocursos();
			$alumnos = new Alumnos();
			$Carrera = new Carrera();

			switch($parcial){
				case 1: $parcialito = "PRIMER PARCIAL"; break;
				case 2: $parcialito = "SEGUNDO PARCIAL"; break;
				case 3: $parcialito = "TERCER PARCIAL"; break;
			}


			$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
			$materia = $materias -> find_first("clave='".$xcurso -> materia."'");

			$this -> set_response("view");

			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');

			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL TONALA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			if( substr( $periodo, 0, 1) == 1 )
				$periodo2 = "FEB - JUN, ";
			else
				$periodo2 = "AGO - DIC, ";

			$periodo2 .= substr($periodo,1,4);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"TONALA",1,0,'C',1);
			$reporte -> Cell(25,4,$curso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(127,6,"",1,0,'C',1);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(69,6,$parcialito,1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(8,4,"No.",1,0,'C',1);
			$reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(31,4,"CARRERA",1,0,'C',1);
			$reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
			$reporte -> Cell(30,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$np = 0;
			foreach($calificaciones -> find("curso_id='".$xcurso->id."' ORDER BY registro") as $calificacion){
					$n++;

					switch($parcial){
							case 1: if($calificacion -> calificacion1 >= 70 && $calificacion -> calificacion1 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion1 >= 0 && $calificacion -> calificacion1 <= 100 ) { $promedio += $calificacion -> calificacion1; $np++;} break;
							case 2: if($calificacion -> calificacion2 >= 70 && $calificacion -> calificacion2 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion2 >= 0 && $calificacion -> calificacion2 <= 100 ) { $promedio += $calificacion -> calificacion2; $np++;} break;
							case 3: if($calificacion -> calificacion3 >= 70 && $calificacion -> calificacion3 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion3 >= 0 && $calificacion -> calificacion3 <= 100 ) { $promedio += $calificacion -> calificacion3; $np++;} break;
					}

					switch($parcial){
							case 1: $faltas = $calificacion -> faltas1; break;
							case 2: $faltas = $calificacion -> faltas2; break;
							case 3: $faltas = $calificacion -> faltas3; break;
					}

					switch($parcial){
							case 1: $cal = $calificacion -> calificacion1; break;
							case 2: $cal = $calificacion -> calificacion2; break;
							case 3: $cal = $calificacion -> calificacion3; break;
					}

					switch($cal){
							case 300: $cal="-"; $faltas="-"; break;
							case 500: $cal="PND"; break;
							case 999: $cal="NP"; $nps++; break;
					}

					$faltasletra = $this -> numero_letra($faltas);
					$calletra = $this -> numero_letra($cal);

					if($alumno = $alumnos -> find_first("miReg=".$calificacion -> registro)){
						$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
						$reporte -> Cell(8,4,$n,1,0,'C',1);
						$reporte -> Cell(18,4,$calificacion -> registro,1,0,'C',1);
						$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
						$reporte -> Cell(31,4, substr($carrera, 0, 26),1,0,'C',1);
						$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
						$reporte -> Cell(10,4,$faltas,1,0,'C',1);
						$reporte -> Cell(20,4,$faltasletra,1,0,'C',1);
						$reporte -> Cell(10,4,$cal,1,0,'C',1);
						$reporte -> Cell(29,4,$calletra,1,0,'C',1);
						$reporte -> Ln();
					}
			}

			$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(25,5,"HORAS CLASE",1,0,'C',1);
			$reporte -> Cell(25,5,"AVANCE",1,0,'C',1);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();

			$avance = $xcursos -> find_first("id='".$xcurso->id."'");

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			switch($parcial){
				case 1: $reporte -> Cell(25,5,$avance -> horas1,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance1."%",1,0,'C',1);break;
				case 2: $reporte -> Cell(25,5,$avance -> horas2,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance2."%",1,0,'C',1);break;
				case 3: $reporte -> Cell(25,5,$avance -> horas3,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance3."%",1,0,'C',1);break;
			}

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"FSGC-217-7.INS-005",0,'C',0);
			
			// /datos/calculo/ingenieria/apps/default/controllers
			$reporte -> Output("public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");

			$this->redirect("public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");
        } // function tacta($curso)

        function acta($curso){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');

			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = Session::get_data('registro');
			//$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();

			//Tendrá que llegar por medio de un post
			$parcial = $this -> post("parcial");

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$Xccursos = new Xccursos();
			$materias = new Materia();
			$Xalumnocursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$Carrera = new Carrera();

			switch($parcial){
				case 1: $parcialito = "PRIMER PARCIAL"; break;
				case 2: $parcialito = "SEGUNDO PARCIAL"; break;
				case 3: $parcialito = "TERCER PARCIAL"; break;
			}
			
			$xccursos = $Xccursos -> find_first("clavecurso='".$curso."'");
			$materia = $materias -> find_first("clave='".$xccursos -> materia."'");

			$this -> set_response("view");

			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');

			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			switch($hour){
				case 0:
					$hour = 22;
						break;
				case 1:
					$hour = 23;
						break;
				default:
					$hour -= 2;
						break;
			}
			$minute = date ("i");
			$second = date ("s");
			$fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			$reporte -> SetX(155);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"$fecha",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			if( substr( $periodo, 0, 1) == 1 )
				$periodo2 = "FEB - JUN, ";
			else
				$periodo2 = "AGO - DIC, ";

			$periodo2 .= substr($periodo,1,4);
			
			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			
			$xpermcapturaesp = new Xpermisoscapturaesp();
			
			if( $xpermcapturaesp -> find_first( "clavecurso = '".$xccursos->clavecurso."'" ) ){
				$reporte -> MultiCell(0,2,"CORRECIÓN DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);
			}
			else{
				$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);
			}
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
			$reporte -> Cell(25,4,$xccursos->clavecurso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(127,6,"",1,0,'C',1);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(69,6,$parcialito,1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(8,4,"No.",1,0,'C',1);
			$reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(31,4,"CARRERA",1,0,'C',1);
			$reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
			$reporte -> Cell(30,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			foreach($Xalumnocursos -> find("curso_id='".$xccursos->id."' ORDER BY registro") as $calificacion){
				$n++;

				switch($parcial){
						case 1: if($calificacion -> calificacion1 >= 70 && $calificacion -> calificacion1 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion1 >= 0 && $calificacion -> calificacion1 <= 100 ) { $promedio += $calificacion -> calificacion1; $np++;} break;
						case 2: if($calificacion -> calificacion2 >= 70 && $calificacion -> calificacion2 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion2 >= 0 && $calificacion -> calificacion2 <= 100 ) { $promedio += $calificacion -> calificacion2; $np++;} break;
						case 3: if($calificacion -> calificacion3 >= 70 && $calificacion -> calificacion3 <= 100){ $aprobados++; } else { $reprobados++; } if( $calificacion -> calificacion3 >= 0 && $calificacion -> calificacion3 <= 100 ) { $promedio += $calificacion -> calificacion3; $np++;} break;
				}

				switch($parcial){
						case 1: $faltas = $calificacion -> faltas1; break;
						case 2: $faltas = $calificacion -> faltas2; break;
						case 3: $faltas = $calificacion -> faltas3; break;
				}

				switch($parcial){
						case 1: $cal = $calificacion -> calificacion1; break;
						case 2: $cal = $calificacion -> calificacion2; break;
						case 3: $cal = $calificacion -> calificacion3; break;
				}

				switch($cal){
						case 300: $cal="-"; $faltas="-"; break;
						case 500: $cal="PND"; break;
						case 999: $cal="NP"; $nps++; break;
				}

				$faltasletra = $this -> numero_letra($faltas);
				$calletra = $this -> numero_letra($cal);

				if($alumno = $alumnos -> find_first("miReg=".$calificacion -> registro)){
					$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
					$reporte -> Cell(8,4,$n,1,0,'C',1);
					$reporte -> Cell(18,4,$calificacion -> registro,1,0,'C',1);
					$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
					$reporte -> Cell(31,4, substr($carrera, 0, 26),1,0,'C',1);
					$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
					$reporte -> Cell(10,4,$faltas,1,0,'C',1);
					$reporte -> Cell(20,4,$faltasletra,1,0,'C',1);
					$reporte -> Cell(10,4,$cal,1,0,'C',1);
					$reporte -> Cell(29,4,$calletra,1,0,'C',1);
					$reporte -> Ln();
				}
			}

			$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(25,5,"HORAS CLASE",1,0,'C',1);
			$reporte -> Cell(25,5,"AVANCE",1,0,'C',1);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();

			$avance = $Xccursos -> find_first("id='".$xccursos->id."'");

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			switch($parcial){
				case 1: $reporte -> Cell(25,5,$avance -> horas1,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance1."%",1,0,'C',1);break;
				case 2: $reporte -> Cell(25,5,$avance -> horas2,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance2."%",1,0,'C',1);break;
				case 3: $reporte -> Cell(25,5,$avance -> horas3,1,0,'C',1); $reporte -> Cell(25,5,$avance -> avance3."%",1,0,'C',1);break;
			}

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"FSGC-217-7.INS-005",0,'C',0);
			
			// /datos/calculo/ingenieria/apps/default/controllers
			$reporte -> Output("public/files/reportes/".$periodo."/".$parcial."/".$xccursos->clavecurso.".pdf");
			
			$this->redirect("public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");
        } // function acta($curso)
		
        function acta_intersemestral($curso){

			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$nomina = Session::get_data('registro');
			//$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			$periodo_inter = $Periodos -> get_periodo_actual_intersemestral();
			//$periodo_inter += 10000;

			$Maestros = new Maestros();
			$maestro = $Maestros -> find_first("nomina=".$nomina);

			$IntersemestralCursos = new IntersemestralCursos();
			$Materia = new Materia();
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			$Alumnos = new Alumnos();
			
			$icurso = $IntersemestralCursos -> find_first("clavecurso='".$curso."'");
			$materia = $Materia -> find_first("clave='".$icurso -> clavemat."'");
			
			$this -> set_response("view");
			
			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');

			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			switch($hour){
				case 0: $hour = 22; break;
				case 1: $hour = 23; break;
				default: $hour -= 2; break;
			}
			$minute = date ("i");
			$second = date ("s");
			$fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			
			$reporte -> SetX(155);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"$fecha",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			if( substr( $periodo_inter, 0, 1) == 2 )
				$periodo2 = "JULIO, ";
			else
				$periodo2 = "ENERO, ";

			$periodo2 .= substr($periodo_inter,1,4);
			
			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
			$reporte -> Cell(25,4,$curso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(86,6,"",1,0,'C',1);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(69,6,"Intersemestrales",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(8,4,"No.",1,0,'C',1);
			$reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(30,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			foreach($IntersemestralAlumnos -> find("clavecurso='".$curso."' ORDER BY registro") as $ialumno){
				$n++;
				
				if($ialumno -> calificacion >= 70 && $ialumno -> calificacion <= 100){ $aprobados++; } 
				else { $reprobados++; } 
				if( $ialumno -> calificacion >= 0 && $ialumno -> calificacion <= 100 ) { $promedio += $ialumno -> calificacion; $np++;}
				
				$faltas = $ialumno -> faltas;
				$cal = $ialumno -> calificacion;

				switch($cal){
					case 300: $cal="-"; $faltas="-"; break;
					case 500: $cal="PND"; break;
					case 999: $cal="NP"; $nps++; break;
				}
				
				$faltasletra = $this -> numero_letra($faltas);
				$calletra = $this -> numero_letra($cal);

				//if($alumno = $alumnos -> find_first("miReg=".$calificacion -> registro." and stSit = 'OK'")){
				if($alumno = $Alumnos -> find_first("miReg=".$ialumno -> registro)){
					//$carrera = $Carrera -> find_first("id=".$alumno -> carrera_id);
					$reporte -> Cell(8,4,$n,1,0,'C',1);
					$reporte -> Cell(18,4,$ialumno -> registro,1,0,'C',1);
					$reporte -> Cell(60,4,$alumno -> vcNomAlu,1,0,'L',1);
					//$reporte -> Cell(20,4,$carrera -> nombre,1,0,'C',1);
					//$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
					$reporte -> Cell(10,4,$faltas,1,0,'C',1);
					$reporte -> Cell(20,4,$faltasletra,1,0,'C',1);
					$reporte -> Cell(10,4,$cal,1,0,'C',1);
					$reporte -> Cell(29,4,$calletra,1,0,'C',1);
					$reporte -> Ln();
				}
			}

			$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"FSGC-217-7.INS-005",0,'C',0);
			
			// /datos/calculo/ingenieria/apps/default/controllers
			//$reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/".$parcial."/".$curso.".pdf");
			//$reporte -> Output("/ingenieria/public/files/reportes/".$periodo."/intersemestrales/".$curso.".pdf");
			$reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/intersemestrales/".$curso.".pdf");
			
			$this->redirect("public/files/reportes/".$periodo."/intersemestrales/".$curso.".pdf");
        } // function acta_intersemestral($curso)

		function actaTodos($curso){

			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = Session::get_data('registro');
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$xcursos = new Xccursos();
			$materias = new Materia();
			$calificaciones = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
			$materia = $materias -> find_first("clave='".$xcurso -> materia."'");

			$this -> set_response("view");

			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();
			
			if( substr($periodo, 0, 1) == 1)
				$periodo2 = "FEB - JUN, ";
			else
				$periodo2 = "AGO - DIC, ";
			
			$periodo2 .= substr($periodo,1,4);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
			$reporte -> Cell(25,4,$curso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',5);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,4,"",1,0,'C',1);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(20,4,"PRIMER PARCIAL",1,0,'C',1);
			$reporte -> Cell(20,4,"SEGUNDO PARCIAL",1,0,'C',1);
			$reporte -> Cell(20,4,"TERCER PARCIAL",1,0,'C',1);

			$reporte -> SetFillColor(0xC0,0xC0,0xC0);
			$reporte -> Cell(59,4,"CALIFICACIÓN FINAL",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',5);

			//$reporte -> Cell(5,4,"No.",1,0,'C',1);
			$reporte -> Cell(12,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(45,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(8,4,"ESP",1,0,'C',1);
			$reporte -> Cell(5,4,"SIT",1,0,'C',1);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> SetFillColor(0xC0,0xC0,0xC0);
			$reporte -> Cell(12,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,4,"CALIF",1,0,'C',1);
			$reporte -> Cell(23,4,"LETRA",1,0,'C',1);
			$reporte -> Cell(12,4,"SITUACIÓN",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			$aprobados = 0;
			$reprobados = 0;
			$n = 0;

			foreach($calificaciones -> find
					("curso_id='".$xcurso->id."' ORDER BY registro") as $calificacion){
				$n++;

				$faltas1 = $calificacion -> faltas1;
				$faltas2 = $calificacion -> faltas2;
				$faltas3 = $calificacion -> faltas3;

				$faltas = $faltas1 + $faltas2 + $faltas3;

				$cal1 = $calificacion -> calificacion1;
				$cal2 = $calificacion -> calificacion2;
				$cal3 = $calificacion -> calificacion3;

				if( $cal1 <= 100 && $cal2 <= 100 && $cal3 <= 100 ){
					$cal = ( ( $cal1 + $cal2 + $cal3 ) / 3 );
				}
				else{
					$cal11 = $cal1;
					$cal22 = $cal2;
					$cal33 = $cal3;

					if( $cal1 == 999 || $cal1 == 300 )
						$cal11 = 0;
					if( $cal2 == 999 || $cal2 == 300 )
						$cal22 = 0;
					if( $cal3 == 999 || $cal3 == 300 )
						$cal33 = 0;

					$cal = ( ( $cal11 + $cal22 + $cal33 ) / 3 );

					if( $cal1 == 999 && $cal2 == 999 && $cal3 == 999 ){
						$cal = "NP";
						$nps++;
					}
				}
				if( $cal != 100 && $cal != "NP" ){
					if( $cal < 70 ){
						$cal = (int)$cal = substr( $cal, 0, 2 );
					}
					else{
						if( strlen( $cal ) > 2 ){
							if( substr( $cal, 3, 1 ) > 5 )
								$cal = $cal + 1;
						}
						if( $cal < 100 )
							$cal = (int)$cal = substr( $cal, 0, 2 );
						else
							$cal = (int)$cal = substr( $cal, 0, 3 );
					}
				}

				$calletra = $this -> numero_letra($cal);

				if( $cal >= 70 && $cal <= 100){
					$aprobados++;
				}
				else{
					$reprobados++;
				}
				if( $cal >= 0 && $cal <= 100 ){
					$promedio += $cal;
					$np++;
				}

				if( $calificacion -> situacion == "BAJA DEFINITIVA" ){
						$situacionFinal = "BD";
				}
				else if( $calificacion -> situacion == "EXTRAORDINARIO" ){
						$situacionFinal = "EC";
				}
				else if( $calificacion -> situacion == "EXTRAORDINARIO FALTAS" ){
						$situacionFinal = "EF";
				}
				else if( $calificacion -> situacion == "ORDINARIO" ){
						$situacionFinal = "OR";
				}
				else if( $calificacion -> situacion == "PROCESO" ){
						$situacionFinal = "BA";
				}
				else if( $calificacion -> situacion == "REGULARIZACION" ){
						$situacionFinal = "RE";
				}
				else if( $calificacion -> situacion == "REGULARIZACION DIRECTA" ){
						$situacionFinal = "RD";
				}
				else if( $calificacion -> situacion == "TITULO DE SUFICIENCIA" ){
						$situacionFinal = "TC";
				}
				else if( $calificacion -> situacion == "TITULO FALTAS" ){
						$situacionFinal = "TF";
				}
				else if( $calificacion -> situacion == "-" ){
						$situacionFinal = "-";
				}

				switch($cal1){
						case 300: $cal1="-"; $faltas="-"; break;
						case 500: $cal1="PND"; break;
						case 999: $cal1="NP"; break;
				}

				switch($cal2){
						case 300: $cal2="-"; $faltas="-"; break;
						case 500: $cal2="PND"; break;
						case 999: $cal2="NP"; break;
				}

				switch($cal3){
						case 300: $cal3="-"; $faltas="-"; break;
						case 500: $cal3="PND"; break;
						case 999: $cal3="NP"; break;
				}

				//if($alumno = $alumnos -> find_first("miReg=".$calificacion -> registro." and stSit = 'OK'")){
				if( $alumno = $alumnos -> find_first( "miReg = ".$calificacion -> registro ) ){
						$especialidad = $especialidades -> find_first("idtiEsp=".$alumno -> idtiEsp);
						//$reporte -> Cell(5,4,$n,1,0,'C',1);
						$reporte -> Cell(12,4,$calificacion -> registro,1,0,'C',1);
						$reporte -> Cell(45,4,htmlentities($alumno -> vcNomAlu, ENT_QUOTES),1,0,'L',1);
						$reporte -> Cell(8,4,$especialidad -> siNumEsp,1,0,'C',1);
						$reporte -> Cell(5,4,$alumno -> enTipo,1,0,'C',1);

						$reporte -> Cell(10,4,$faltas1,1,0,'C',1);
						$reporte -> Cell(10,4,$cal1,1,0,'C',1);

						$reporte -> Cell(10,4,$faltas2,1,0,'C',1);
						$reporte -> Cell(10,4,$cal2,1,0,'C',1);

						$reporte -> Cell(10,4,$faltas3,1,0,'C',1);
						$reporte -> Cell(10,4,$cal3,1,0,'C',1);

						$reporte -> Cell(12,4,$faltas,1,0,'C',1);
						$reporte -> Cell(12,4,$cal,1,0,'C',1);
						$reporte -> Cell(23,4,$calletra,1,0,'C',1);
						$reporte -> Cell(12,4,$situacionFinal,1,0,'C',1);

						$reporte -> Ln();
				} // if( $alumno = $alumnos -> find_first( "miReg = ".$calificacion -> registro ) )
			} // foreach($calificaciones -> find
					//	("curso='".$curso."' ORDER BY registro") as $calificacion)

			$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(25,5,"HORAS CLASE",1,0,'C',1);
			$reporte -> Cell(25,5,"AVANCE",1,0,'C',1);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();

			$avance = $xcursos -> find_first("clavecurso='".$curso."'");

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);


			$horasTotal = $avance -> horas1 + $avance -> horas2 + $avance -> horas3;
			$avanceTotal = $avance -> avance1 + $avance -> avance2 + $avance -> avance3;

			if( $avanceTotal > 100 && $avanceTotal < 300 )
					$avanceTotal = ( $avanceTotal / 2 );
			if( $avanceTotal == 300 )
					$avanceTotal = ( $avanceTotal / 3 );
			if(  $avanceTotal + 1 < 100 )
					$avanceTotal += 1;

			if(  $avanceTotal > 100 ){
					$restar = $avanceTotal - 100;
					$avanceTotal -= $restar;
			}

			$reporte -> Cell(25,5,$horasTotal,1,0,'C',1);
			$reporte -> Cell(25,5,$avanceTotal."%",1,0,'C',1);

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			$fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			
			$reporte -> MultiCell(0,2,"FSGC-217-7.INS-005",0,'C',0);
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,$fecha,0,'C',0);
			
			// /datos/calculo/ingenieria/apps/default/controllers
			$reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/CalifFinal/".$curso.".pdf");
			//$reporte -> Output("ingenieria/public/files/reportes/".$periodo."/CalifFinal/".$curso.".pdf");

			$this->redirect("public/files/reportes/".$periodo."/CalifFinal/".$curso.".pdf");
		} // function actaTodos($curso)

        function actaTodosT($curso){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = Session::get_data('registro');
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			
			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$xcursos = new Xtcursos();
			$materias = new Materia();
			$calificaciones = new Xtalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
			$materia = $materias -> find_first("clave='".$xcurso -> materia."'");

			$this -> set_response("view");

			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');

			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL TONALA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			if( substr($periodo, 0, 1) == 1)
			$periodo2 = "FEB - JUN, ";
			else
			$periodo2 = "AGO - DIC, ";

			$periodo2 .= substr($periodo,1,4);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$periodo2."",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"TONALA",1,0,'C',1);
			$reporte -> Cell(25,4,$curso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',5);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,4,"",1,0,'C',1);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(20,4,"PRIMER PARCIAL",1,0,'C',1);
			$reporte -> Cell(20,4,"SEGUNDO PARCIAL",1,0,'C',1);
			$reporte -> Cell(20,4,"TERCER PARCIAL",1,0,'C',1);

			$reporte -> SetFillColor(0xC0,0xC0,0xC0);
			$reporte -> Cell(59,4,"CALIFICACIÓN FINAL",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',5);

			//$reporte -> Cell(5,4,"No.",1,0,'C',1);
			$reporte -> Cell(12,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(45,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(8,4,"ESP",1,0,'C',1);
			$reporte -> Cell(5,4,"SIT",1,0,'C',1);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> Cell(10,4,"FALT",1,0,'C',1);
			$reporte -> Cell(10,4,"CALF",1,0,'C',1);

			$reporte -> SetFillColor(0xC0,0xC0,0xC0);
			$reporte -> Cell(12,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,4,"CALIF",1,0,'C',1);
			$reporte -> Cell(23,4,"LETRA",1,0,'C',1);
			$reporte -> Cell(12,4,"SITUACIÓN",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			$aprobados = 0;
			$reprobados = 0;
			$n = 0;
			foreach($calificaciones -> find
						("curso_id='".$xcurso->id."' ORDER BY registro") as $calificacion){
				$n++;
				
				$faltas1 = $calificacion -> faltas1;
				$faltas2 = $calificacion -> faltas2;
				$faltas3 = $calificacion -> faltas3;
				
				$faltas = $faltas1 + $faltas2 + $faltas3;
				
				$cal1 = $calificacion -> calificacion1;
				$cal2 = $calificacion -> calificacion2;
				$cal3 = $calificacion -> calificacion3;
				
				if( $cal1 <= 100 && $cal2 <= 100 && $cal3 <= 100 ){
					$cal = ( ( $cal1 + $cal2 + $cal3 ) / 3 );
				}
				else{
					$cal11 = $cal1;
					$cal22 = $cal2;
					$cal33 = $cal3;

					if( $cal1 == 999 || $cal1 == 300 )
							$cal11 = 0;
					if( $cal2 == 999 || $cal2 == 300 )
							$cal22 = 0;
					if( $cal3 == 999 || $cal3 == 300 )
							$cal33 = 0;

					$cal = ( ( $cal11 + $cal22 + $cal33 ) / 3 );

					if( $cal1 == 999 && $cal2 == 999 && $cal3 == 999 ){
						$cal = "NP";
						$nps++;
					}
				}
				if( $cal != 100 && $cal != "NP" ){
					if( $cal < 70 ){
						$cal = (int)$cal = substr( $cal, 0, 2 );
					}
					else{
						if(  strlen( $cal ) > 2 ){
							if( substr( $cal, 3, 1 ) > 5 )
								$cal = $cal + 1;
						}
						if( $cal < 100 )
							$cal = (int)$cal = substr( $cal, 0, 2 );
						else
							$cal = (int)$cal = substr( $cal, 0, 3 );
					}
				}
				if( $cal1 == 100 && $cal2 == 100 && $cal3 == 100 ){
					$cal = ( ( $cal1 + $cal2 + $cal3 ) / 3 );
				}
				
				$calletra = $this -> numero_letra($cal);
				
				if( $cal >= 70 && $cal <= 100){
					$aprobados++;
				}
				else{
					$reprobados++;
				}
				if( $cal >= 0 && $cal <= 100 ){
					$promedio += $cal;
					$np++;
				}
				
				if( $calificacion -> situacion == "BAJA DEFINITIVA" ){
					$situacionFinal = "BD";
				}
				else if( $calificacion -> situacion == "EXTRAORDINARIO" ){
					$situacionFinal = "EC";
				}
				else if( $calificacion -> situacion == "EXTRAORDINARIO FALTAS" ){
					$situacionFinal = "EF";
				}
				else if( $calificacion -> situacion == "ORDINARIO" ){
					$situacionFinal = "OR";
				}
				else if( $calificacion -> situacion == "PROCESO" ){
					$situacionFinal = "BA";
				}
				else if( $calificacion -> situacion == "REGULARIZACION" ){
					$situacionFinal = "RE";
				}
				else if( $calificacion -> situacion == "REGULARIZACION DIRECTA" ){
					$situacionFinal = "RD";
				}
				else if( $calificacion -> situacion == "TITULO DE SUFICIENCIA" ){
					$situacionFinal = "TC";
				}
				else if( $calificacion -> situacion == "TITULO FALTAS" ){
					$situacionFinal = "TF";
				}
				else if( $calificacion -> situacion == "-" ){
					$situacionFinal = "-";
				}
				
				switch($cal1){
					case 300: $cal1="-"; $faltas="-"; break;
					case 500: $cal1="PND"; break;
					case 999: $cal1="NP"; break;
				}
				switch($cal2){
					case 300: $cal2="-"; $faltas="-"; break;
					case 500: $cal2="PND"; break;
					case 999: $cal2="NP"; break;
				}
				switch($cal3){
					case 300: $cal3="-"; $faltas="-"; break;
					case 500: $cal3="PND"; break;
					case 999: $cal3="NP"; break;
				}
				
				//if($alumno = $alumnos -> find_first("miReg=".$calificacion -> registro." and stSit = 'OK'")){
				if( $alumno = $alumnos -> find_first( "miReg = ".$calificacion -> registro ) ){
					$especialidad = $especialidades -> find_first("idtiEsp=".$alumno -> idtiEsp);
					//$reporte -> Cell(5,4,$n,1,0,'C',1);
					$reporte -> Cell(12,4,$calificacion -> registro,1,0,'C',1);
					$reporte -> Cell(45,4,htmlentities($alumno -> vcNomAlu, ENT_QUOTES),1,0,'L',1);
					$reporte -> Cell(8,4,$especialidad -> siNumEsp,1,0,'C',1);
					$reporte -> Cell(5,4,$alumno -> enTipo,1,0,'C',1);

					$reporte -> Cell(10,4,$faltas1,1,0,'C',1);
					$reporte -> Cell(10,4,$cal1,1,0,'C',1);

					$reporte -> Cell(10,4,$faltas2,1,0,'C',1);
					$reporte -> Cell(10,4,$cal2,1,0,'C',1);

					$reporte -> Cell(10,4,$faltas3,1,0,'C',1);
					$reporte -> Cell(10,4,$cal3,1,0,'C',1);

					$reporte -> Cell(12,4,$faltas,1,0,'C',1);
					$reporte -> Cell(12,4,$cal,1,0,'C',1);
					$reporte -> Cell(23,4,$calletra,1,0,'C',1);
					$reporte -> Cell(12,4,$situacionFinal,1,0,'C',1);

					$reporte -> Ln();
				} // if( $alumno = $alumnos -> find_first( "miReg = ".$calificacion -> registro ) )
			} // foreach($calificaciones -> find
				//	("curso='".$curso."' ORDER BY registro") as $calificacion)

			$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(25,5,"HORAS CLASE",1,0,'C',1);
			$reporte -> Cell(25,5,"AVANCE",1,0,'C',1);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();

			$avance = $xcursos -> find_first("clavecurso='".$curso."'");

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);


			$horasTotal = $avance -> horas1 + $avance -> horas2 + $avance -> horas3;
			$avanceTotal = $avance -> avance1 + $avance -> avance2 + $avance -> avance3;

			if( $avanceTotal > 100 && $avanceTotal < 300 )
				$avanceTotal = ( $avanceTotal / 2 );
			if( $avanceTotal == 300 )
				$avanceTotal = ( $avanceTotal / 3 );
			if(  $avanceTotal + 1 < 100 )
				$avanceTotal += 1;

			if(  $avanceTotal > 100 ){
				$restar = $avanceTotal - 100;
				$avanceTotal -= $restar;
			}


			$reporte -> Cell(25,5,$horasTotal,1,0,'C',1);
			$reporte -> Cell(25,5,$avanceTotal."%",1,0,'C',1);

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);

			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			$fecha = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;

			$reporte -> MultiCell(0,2,"FSGC-217-7.INS-005",0,'C',0);
			$reporte -> Ln();
			$reporte -> SetX(165);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,$fecha,0,'C',0);

			// /datos/calculo/ingenieria/apps/default/controllers
			$reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/CalifFinalT/".$curso.".pdf");
			//$reporte -> Output("/public/files/reportes/".$periodo."/CalifFinalT/".$curso.".pdf");

			$this->redirect("public/files/reportes/".$periodo."/CalifFinalT/".$curso.".pdf");
        } // function actaTodosT($curso)

        function actaextras($curso){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = Session::get_data('registro');
			//$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();

			//Tendrá que llegar por medio de un post
			$parcial = $this -> post("parcial");

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$mishorarios = new Mishorarios();
			$materias = new Materiasing();
			$cursitos = new Registracursos();
			$xextraordinarios = new Xextraordinarios();
			$alumnos = new Alumnos();
			$Carrera = new Carrera();
			$especialidades = new Especialidades();

			$Xccursos = new Xccursos();
			$materias = new Materia();

			$xccurso = $Xccursos -> find_first("clavecurso='".$curso."' AND periodo=".$periodo);
			$materia = $materias -> find_first("clave='".$xccurso -> materia."'");

			$this -> set_response("view");

			$reporte = new FPDF();

			$reporte -> Open();
			$reporte -> AddPage();

			$reporte -> AddFont('Verdana','','verdana.php');

			$reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(45);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$Periodos->convertirPeriodo_($periodo),0,'C',0);

			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
			$reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
			$reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
			$reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$id,1,0,'C',1);
			$reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
			$reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
			$reporte -> Cell(25,4,$xccurso->clavecurso,1,0,'C',1);
			$reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(121,6,"",1,0,'C',1);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(69,6,"EXAMEN EXTRAORDINARIO",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',6);

			$reporte -> Cell(8,4,"No.",1,0,'C',1);
			$reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
			$reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
			$reporte -> Cell(25,4,"CARRERA",1,0,'C',1);
			$reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
			$reporte -> Cell(20,4,"FALTAS",1,0,'C',1);
			$reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);
			$reporte -> Cell(10,4,"PAGO",1,0,'C',1);

			$reporte -> Ln();

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			foreach($xextraordinarios -> find("curso_id='".$xccurso->id."'  AND tipo='E' AND periodo=".$periodo." ORDER BY registro") as $extra){
				$n++;

				if($extra -> calificacion > 60 && $extra -> calificacion <= 100){ $aprobados++; } else { $reprobados++; }
				if( $extra -> calificacion >= 0 && $extra -> calificacion <= 100 ) { $promedio += $extra -> calificacion; $np++;}

				$cal = $extra -> calificacion;

				switch($cal){
					case 300: $cal="-"; $faltas="-"; break;
					case 500: $cal="PND"; break;
					case 999: $cal="NP"; $nps++; break;
				}
				if( $extra->estado != "OK" ){
					$cal="NP"; $faltas="-";
				}

				$faltasletra = $this -> numero_letra($faltas);
				$calletra = $this -> numero_letra($cal);

				if($alumno = $alumnos -> find_first("miReg=".$extra -> registro)){
					$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
						
					$reporte -> Cell(8,4,$n,1,0,'C',1);
					$reporte -> Cell(18,4,$extra -> registro,1,0,'C',1);
					$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
					$reporte -> Cell(25,4, substr($carrera, 0, 22),1,0,'C',1);
					$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
					$reporte -> Cell(10,4,"-",1,0,'C',1);
					$reporte -> Cell(10,4,"-",1,0,'C',1);
					$reporte -> Cell(10,4,$cal,1,0,'C',1);
					$reporte -> Cell(29,4,$calletra,1,0,'C',1);
					if($extra -> estado == "?") $pago = "No"; else $pago = "Si";
					$reporte -> Cell(10,4,$pago,1,0,'C',1);
					$reporte -> Ln();
				}
			}
			if($np==0)
				$promedio = 0;
			else
				$promedio /= $np;
			$aprobados += 0;
			$reprobados += 0;
			$nps += 0;

			$reporte -> Ln();

			$reporte -> SetFont('Verdana','',7);
			$reporte -> SetX(35);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
			$reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(35);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);

			$reporte -> Cell(26,5,$nps,1,0,'C',1);
			$reporte -> Cell(38,5,$aprobados,1,0,'C',1);
			$reporte -> Cell(38,5,$reprobados,1,0,'C',1);

			$promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

			$reporte -> Cell(38,5,$promedio,1,0,'C',1);

			$reporte -> Ln();
			$reporte -> Ln();

			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(70,15,"",1,0,'C',1);

			$reporte -> Ln();
			$reporte -> SetX(70);
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);

//			$reporte -> Output("/sitios/htdocs/ingenieria".$periodo."/extra/".$curso.".pdf");
			$reporte -> Output("public/files/reportes/".$periodo."/extra/".$xccurso->clavecurso.".pdf");

			$this->redirect("public/files/reportes/".$periodo."/extra/".$xccurso->clavecurso.".pdf");
        }

        function Tactaextras($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
                //$periodo = $this -> actual;
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual_();

                //Tendrá que llegar por medio de un post
                $parcial = $this -> post("parcial");

                $maestros = new Maestros();
                $maestro = $maestros -> find_first("nomina=".$id);

                $mishorarios = new Mishorarios();
                $materias = new Materiasing();
                $cursitos = new Registracursos();
                $xextraordinarios = new Xtextraordinarios();
                $alumnos = new Alumnos();
				$Carrera = new Carrera();
                $especialidades = new Especialidades();

                $xcursos = new Xtcursos();
                $materias = new Materia();

                $xcurso = $xcursos -> find_first("clavecurso='".$curso."' AND periodo=".$periodo);
                $materia = $materias -> find_first("clave='".$xcurso -> materia."'");

                $this -> set_response("view");

                $reporte = new FPDF();

                $reporte -> Open();
                $reporte -> AddPage();

                $reporte -> AddFont('Verdana','','verdana.php');

                $reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',14);
                $reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"PLANTEL TONALA",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ".$Periodos->convertirPeriodo_($periodo),0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
                $reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
                $reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
                $reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
                $reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(20,4,$id,1,0,'C',1);
                $reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
                $reporte -> Cell(25,4,"TONALA",1,0,'C',1);
                $reporte -> Cell(25,4,$curso,1,0,'C',1);
                $reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',8);

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(121,6,"",1,0,'C',1);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(69,6,"EXAMEN EXTRAORDINARIO",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(8,4,"No.",1,0,'C',1);
                $reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
                $reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
                $reporte -> Cell(25,4,"CARRERA",1,0,'C',1);
                $reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
                $reporte -> Cell(20,4,"FALTAS",1,0,'C',1);
                $reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);
				$reporte -> Cell(10,4,"PAGO",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                foreach($xextraordinarios -> find("curso_id='".$xcurso->id."'  AND tipo='E' AND periodo=".$periodo."  ORDER BY registro") as $extra){
					$n++;

					if($extra -> calificacion > 60 && $extra -> calificacion <= 100){ $aprobados++; } else { $reprobados++; }
					if( $extra -> calificacion >= 0 && $extra -> calificacion <= 100 ) { $promedio += $extra -> calificacion; $np++;}

					$cal = $extra -> calificacion;

					switch($cal){
						case 300: $cal="-"; $faltas="-"; break;
						case 500: $cal="PND"; break;
						case 999: $cal="NP"; $nps++; break;
					}
					if( $extra->estado != "OK" ){
						$cal="NP"; $faltas="-";
					}

					$faltasletra = $this -> numero_letra($faltas);
					$calletra = $this -> numero_letra($cal);

					if($alumno = $alumnos -> find_first("miReg=".$extra -> registro)){
						$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
							
						$reporte -> Cell(8,4,$n,1,0,'C',1);
						$reporte -> Cell(18,4,$extra -> registro,1,0,'C',1);
						$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
						$reporte -> Cell(25,4, substr($carrera, 0, 22),1,0,'C',1);
						$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,$cal,1,0,'C',1);
						$reporte -> Cell(29,4,$calletra,1,0,'C',1);
						if($extra -> estado == "?") $pago = "No"; else $pago = "Si";
						$reporte -> Cell(10,4,$pago,1,0,'C',1);
						$reporte -> Ln();
					}
                }
                if($np==0)
                        $promedio = 0;
                else
                        $promedio /= $np;
                $aprobados += 0;
                $reprobados += 0;
                $nps += 0;

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',7);
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                $reporte -> Cell(26,5,$nps,1,0,'C',1);
                $reporte -> Cell(38,5,$aprobados,1,0,'C',1);
                $reporte -> Cell(38,5,$reprobados,1,0,'C',1);

                $promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

                $reporte -> Cell(38,5,$promedio,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(70,15,"",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);

//			$reporte -> Output("/sitios/htdocs/ingenieria".$periodo."/extra/".$curso.".pdf");
                $reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/extra/".$curso.".pdf");

                $this->redirect("public/files/reportes/".$periodo."/extra/".$curso.".pdf");
        } // function Tactaextras($curso)

        function actatitulos($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual_();

                //Tendrá que llegar por medio de un post
                $parcial = $this -> post("parcial");

                $maestros = new Maestros();
                $maestro = $maestros -> find_first("nomina=".$id);

                $mishorarios = new Mishorarios();
                $materias = new Materiasing();
                $cursitos = new Registracursos();
                $xextraordinarios = new Xextraordinarios();
                $alumnos = new Alumnos();
				$Carrera = new Carrera();
                $especialidades = new Especialidades();

                $xcursos = new Xccursos();
                $materias = new Materia();

                $xcurso = $xcursos -> find_first("clavecurso='".$curso."' AND periodo=".$periodo);
                $materia = $materias -> find_first("clave='".$xcurso -> materia."'");

                $this -> set_response("view");

                $reporte = new FPDF();

                $reporte -> Open();
                $reporte -> AddPage();

                $reporte -> AddFont('Verdana','','verdana.php');

                $reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',14);
                $reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ENE - JUN, 2010",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
                $reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
                $reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
                $reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
                $reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(20,4,$id,1,0,'C',1);
                $reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
                $reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
                $reporte -> Cell(25,4,$curso,1,0,'C',1);
                $reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',8);

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(121,6,"",1,0,'C',1);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(69,6,"EXAMEN TITULO SUFICIENCIA",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(8,4,"No.",1,0,'C',1);
                $reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
                $reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
                $reporte -> Cell(25,4,"CARRERA",1,0,'C',1);
                $reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
                $reporte -> Cell(20,4,"FALTAS",1,0,'C',1);
                $reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);
				$reporte -> Cell(10,4,"PAGO",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                foreach($xextraordinarios -> find("curso_id='".$xcurso->id."' AND tipo='T' AND periodo=".$periodo." ORDER BY registro") as $extra){
					$n++;

					if($extra -> calificacion >= 70 && $extra -> calificacion <= 100){ $aprobados++; } else { $reprobados++; }
					if( $extra -> calificacion >= 0 && $extra -> calificacion <= 100 ) { $promedio += $extra -> calificacion; $np++;}

					$cal = $extra -> calificacion;

					switch($cal){
						case 300:$cal="-"; $faltas="-";break;
						case 500: $cal="PND"; break;
						case 999: $cal="NP"; $nps++; break;
					}
					if( $extra->estado != "OK" ){
						$cal="NP"; $faltas="-";
					}
					
					$faltasletra = $this -> numero_letra($faltas);
					$calletra = $this -> numero_letra($cal);

					if($alumno = $alumnos -> find_first("miReg=".$extra -> registro)){
						$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
							
						$reporte -> Cell(8,4,$n,1,0,'C',1);
						$reporte -> Cell(18,4,$extra -> registro,1,0,'C',1);
						$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
						$reporte -> Cell(25,4, substr($carrera, 0, 22),1,0,'C',1);
						$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,$cal,1,0,'C',1);
						$reporte -> Cell(29,4,$calletra,1,0,'C',1);
						if($extra -> estado == "?") $pago = "No"; else $pago = "Si";
						$reporte -> Cell(10,4,$pago,1,0,'C',1);
						$reporte -> Ln();
					}
                }
                if($np==0)
                        $promedio = 0;
                else
                        $promedio /= $np;
                $aprobados += 0;
                $reprobados += 0;
                $nps += 0;

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',7);
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                $reporte -> Cell(26,5,$nps,1,0,'C',1);
                $reporte -> Cell(38,5,$aprobados,1,0,'C',1);
                $reporte -> Cell(38,5,$reprobados,1,0,'C',1);

                $promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

                $reporte -> Cell(38,5,$promedio,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(70,15,"",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);
				
                $reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/titulo/".$curso.".pdf");

                $this->redirect("public/files/reportes/".$periodo."/titulo/".$curso.".pdf");

        } // function actatitulos($curso)
		
        function Tactatitulos($curso){

                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);
                unset($this -> parcial);

                $id = Session::get_data('registro');
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual_();
				
                //Tendrá que llegar por medio de un post
                $parcial = $this -> post("parcial");

                $maestros = new Maestros();
                $maestro = $maestros -> find_first("nomina=".$id);

                $mishorarios = new Mishorarios();
                $materias = new Materiasing();
                $cursitos = new Registracursos();
                $xextraordinarios = new Xtextraordinarios();
                $alumnos = new Alumnos();
				$Carrera = new Carrera();
                $especialidades = new Especialidades();

                $xcursos = new Xtcursos();
                $materias = new Materia();

                $xcurso = $xcursos -> find_first("clavecurso='".$curso."' AND periodo=".$periodo);
                $materia = $materias -> find_first("clave='".$xcurso -> materia."'");

                $this -> set_response("view");

                $reporte = new FPDF();

                $reporte -> Open();
                $reporte -> AddPage();

                $reporte -> AddFont('Verdana','','verdana.php');

                $reporte -> Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',14);
                $reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);

                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',12);
                $reporte -> MultiCell(0,2,"NIVEL INGENIERÍA",0,'C',0);
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(45);
                $reporte -> SetFont('Verdana','',8);
                $reporte -> MultiCell(0,2,"REPORTE DE CALIFICACIONES          PERIODO: ENE - JUN, 2010",0,'C',0);

                $reporte -> Ln();
                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(20,4,"NOMINA",1,0,'C',1);
                $reporte -> Cell(60,4,"NOMBRE DEL PROFESOR",1,0,'C',1);
                $reporte -> Cell(25,4,"PLANTEL",1,0,'C',1);
                $reporte -> Cell(25,4,"CLAVE CURSO",1,0,'C',1);
                $reporte -> Cell(60,4,"MATERIA",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(20,4,$id,1,0,'C',1);
                $reporte -> Cell(60,4,$maestro -> nombre,1,0,'C',1);
                $reporte -> Cell(25,4,"COLOMOS",1,0,'C',1);
                $reporte -> Cell(25,4,$curso,1,0,'C',1);
                $reporte -> Cell(60,4,$materia -> clave." - ".$materia -> nombre,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> SetTextColor(0);
                $reporte -> SetDrawColor(0xFF,0x66,0x33);
                $reporte -> SetFont('Verdana','',8);

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(121,6,"",1,0,'C',1);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(69,6,"EXAMEN TITULO SUFICIENCIA",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',6);

                $reporte -> Cell(8,4,"No.",1,0,'C',1);
                $reporte -> Cell(18,4,"REGISTRO",1,0,'C',1);
                $reporte -> Cell(55,4,"NOMBRE DEL ALUMNO",1,0,'C',1);
                $reporte -> Cell(25,4,"CARRERA",1,0,'C',1);
                $reporte -> Cell(15,4,"SITUACION",1,0,'C',1);
                $reporte -> Cell(20,4,"FALTAS",1,0,'C',1);
                $reporte -> Cell(39,4,"CALIFICACION",1,0,'C',1);
				$reporte -> Cell(10,4,"PAGO",1,0,'C',1);

                $reporte -> Ln();

                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                foreach($xextraordinarios -> find("curso_id='".$xcurso->id."'  AND tipo='T' AND periodo=".$periodo."  ORDER BY registro") as $extra){
					$n++;

					if($extra -> calificacion >= 70 && $extra -> calificacion <= 100){ $aprobados++; } else { $reprobados++; }
					if( $extra -> calificacion >= 0 && $extra -> calificacion <= 100 ) { $promedio += $extra -> calificacion; $np++;}

					$cal = $extra -> calificacion;

					switch($cal){
						case 300: $cal="-"; $faltas="-";break;
						case 500: $cal="PND"; break;
						case 999: $cal="NP"; $nps++; break;
					}
					if( $extra->estado != "OK" ){
						$cal="NP"; $faltas="-";
					}

					$faltasletra = $this -> numero_letra($faltas);
					$calletra = $this -> numero_letra($cal);

					if($alumno = $alumnos -> find_first("miReg=".$extra -> registro)){
						$carrera = $Carrera -> get_nombre_carrera_and_areadeformacion_($alumno);
							
						$reporte -> Cell(8,4,$n,1,0,'C',1);
						$reporte -> Cell(18,4,$extra -> registro,1,0,'C',1);
						$reporte -> Cell(55,4,$alumno -> vcNomAlu,1,0,'L',1);
						$reporte -> Cell(25,4, substr($carrera, 0, 22),1,0,'C',1);
						$reporte -> Cell(15,4,$alumno -> enTipo,1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,"-",1,0,'C',1);
						$reporte -> Cell(10,4,$cal,1,0,'C',1);
						$reporte -> Cell(29,4,$calletra,1,0,'C',1);
						if($extra -> estado == "?") $pago = "No"; else $pago = "Si";
						$reporte -> Cell(10,4,$pago,1,0,'C',1);
						$reporte -> Ln();
					}
                }
                if($np==0)
					$promedio = 0;
                else
					$promedio /= $np;
                $aprobados += 0;
                $reprobados += 0;
                $nps += 0;

                $reporte -> Ln();

                $reporte -> SetFont('Verdana','',7);
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(26,5,"NUMERO DE NPs",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS APROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"ALUMNOS REPROBADOS",1,0,'C',1);
                $reporte -> Cell(38,5,"PROMEDIO DEL GRUPO",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(35);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);

                $reporte -> Cell(26,5,$nps,1,0,'C',1);
                $reporte -> Cell(38,5,$aprobados,1,0,'C',1);
                $reporte -> Cell(38,5,$reprobados,1,0,'C',1);

                $promedio = round($promedio*100)/100;	//REDONDEO A DOS DECIMALES

                $reporte -> Cell(38,5,$promedio,1,0,'C',1);

                $reporte -> Ln();
                $reporte -> Ln();

                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,4,"FIRMA DEL PROFESOR",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $reporte -> Cell(70,15,"",1,0,'C',1);

                $reporte -> Ln();
                $reporte -> SetX(70);
                $reporte -> SetFillColor(0xDD,0xDD,0xDD);
                $reporte -> Cell(70,5,$maestro -> nombre,1,0,'C',1);

                $reporte -> Output("/datos/calculo/ingenieria/public/files/reportes/".$periodo."/titulo/".$curso.".pdf");

                $this->redirect("public/files/reportes/".$periodo."/titulo/".$curso.".pdf");

        } // function Tactatitulos($curso)
		
		
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
			if( $numero == 100 ){
				return "CIEN";
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

        function listaexcel($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                $this -> set_response("view");

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);

                $this -> excel = "OK";

                $id = Session::get_data('registro');
                $periodo = $this -> actual;

                $maestros = new Maestros();
                $materias = new Materia();
                $xalumnocursos = new Xalumnocursos();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
				$Xccursos = new Xccursos();

                $total = 0;
				
				$xccurso = $Xccursos ->find_first("id = '".$curso."'");

                foreach($xalumnocursos -> find("curso_id='".$xccurso->id."' ORDER BY registro") as $alumno){

                        $this -> registro = $alumno -> registro;
                        $this -> curso = $curso;
                        $this -> materia = $this -> post("materia");
                        $this -> clave = $this -> post("clave");

                        foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
                                $this -> nombre = $a -> vcNomAlu;
                                $this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
                                $situacion = $a -> enTipo;
                                $especialidad = $a -> idtiEsp;
                                break;
                        }

                        switch($situacion){
                                case 'R': $this -> situacion = "-"; break;
                                case 'I': $this -> situacion = "IRREGULAR"; break;
                                case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
                                case 'C': $this -> situacion = "CONDICIONADO"; break;
                        }

                        foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
                                $this -> especialidad = $e -> vcNomEsp;
                                break;
                        }

                                $this -> alumnado[$total]["registro"] = $this -> registro;
                                $this -> alumnado[$total]["nombre"] = $this -> nombre;
                                $this -> alumnado[$total]["especialidad"] = $this -> especialidad;
                                $this -> alumnado[$total]["situacion"] = $this -> situacion;

                        $total++;
                }


                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }


                if($periodo[0]=='1')
                        $this -> periodo = "FEB - JUN, ";
                else
                        $this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        } // function listaexcel($curso)
		
        function listaexcelt($curso){
                if(Session::get_data('tipousuario')!="PROFESOR"){
                        $this->redirect('/');
                }

                $this -> set_response("view");

                //ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
                unset($this -> excel);
                unset($this -> alumnado);
                unset($this -> registro);
                unset($this -> nombre);
                unset($this -> curso);
                unset($this -> materia);
                unset($this -> clave);
                unset($this -> situacion);
                unset($this -> especialidad);
                unset($this -> profesor);
                unset($this -> periodo);
                unset($this -> nomina);

                $this -> excel = "OK";

                $id = Session::get_data('registro');
                $periodo = $this -> actual;

                $maestros = new Maestros();
                $materias = new Materia();
                $xalumnocursos = new Xtalumnocursos();
                $alumnos = new Alumnos();
                $especialidades = new Especialidades();
				$Xtcursos = new Xtcursos();

                $total = 0;
				
				$xtcurso = $Xtcursos ->find_first("id = '".$curso."'");

                foreach($xalumnocursos -> find("curso_id='".$xtcurso->id."' ORDER BY registro") as $alumno){

                        $this -> registro = $alumno -> registro;
                        $this -> curso = $curso;
                        $this -> materia = $this -> post("materia");
                        $this -> clave = $this -> post("clave");

                        foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
                                $this -> nombre = $a -> vcNomAlu;
                                $this -> nombre = iconv("latin1", "ISO-8859-1", $this -> nombre);
                                $situacion = $a -> enTipo;
                                $especialidad = $a -> idtiEsp;
                                break;
                        }

                        switch($situacion){
                                case 'R': $this -> situacion = "-"; break;
                                case 'I': $this -> situacion = "IRREGULAR"; break;
                                case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
                                case 'C': $this -> situacion = "CONDICIONADO"; break;
                        }

                        foreach($especialidades -> find("idtiEsp=".$especialidad) as $e){
                                $this -> especialidad = $e -> vcNomEsp;
                                break;
                        }

                                $this -> alumnado[$total]["registro"] = $this -> registro;
                                $this -> alumnado[$total]["nombre"] = $this -> nombre;
                                $this -> alumnado[$total]["especialidad"] = $this -> especialidad;
                                $this -> alumnado[$total]["situacion"] = $this -> situacion;

                        $total++;
                }


                foreach($maestros -> find("nomina=".$id) as $maestro){
                        $this -> profesor = $maestro -> nombre;
                }


                if($periodo[0]=='1')
                        $this -> periodo = "FEB - JUN, ";
                else
                        $this -> periodo = "AGO - DIC, ";

                $this -> periodo .= substr($periodo,1,4);
                $this -> nomina = $id;
        } // function listaexcelt($curso)

        function listas(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
				$this->redirect('profesor/cambiarContrasena');
				
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();

			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> excel);
			unset($this -> mihorario);
			unset($this -> cursos);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> inscritos);
			unset($this -> plantel);

			$id = Session::get_data('registro');
			

			$Xalumnocursos = new Xalumnocursos();
			$Xccursos = new Xccursos();
			$maestros = new Maestros();
			$materias = new Materia();

			$i=0;$x=0;$n=0;$mm=0;
			if( $Xccursos -> find("nomina=".$id." and periodo='".$periodo."'") ){
				foreach( $Xccursos -> find("nomina=".$id." and periodo='".$periodo."'") as $xccurso ){
					$this -> mihorario[$mm] = $xccurso;
					foreach($materias -> find("clave='".$xccurso -> materia."'") as $materia){
						$this -> cursos[$i] = $materia -> nombre;
						$i++;
						break;
					}
					
					if($xccurso->id <1000 && $xccurso->id >= 100){
						$xccurso->id = "0".$xccurso->id;
					}
					if($xccurso->id <100 && $xccurso->id >= 10){
						$xccurso->id = "00".$xccurso->id;
					}
					if($xccurso->id < 10){
						$xccurso->id = "000".$xccurso->id;
					}
					
					$this -> inscritos[$n] = $Xalumnocursos -> count("curso_id='".$xccurso->id."'");
					
					$n++;
					foreach($maestros -> find("nomina=".$id) as $maestro){
						$this -> profesor = $maestro -> nombre;
					}
					$this -> plantel[$mm] = "C";
					$mm++;
				}
			}

			if($periodo[0]=='1'){
					$this -> periodo = "FEB - JUN, ";
			}else{
					$this -> periodo = "AGO - DIC, ";}

			$this -> periodo .= substr($periodo,1,4);
			$this -> nomina = $id;
			
			
			// Tonala
			$Xtalumnocursos = new Xtalumnocursos();
			$Xtcursos = new Xtcursos();
			$maestros = new Maestros();
			$materias = new Materia();

			if( $Xtcursos -> find("nomina=".$id." and periodo='".$periodo."'") ){
				foreach( $Xtcursos -> find("nomina=".$id." and periodo='".$periodo."'") as $xtcurso ){
					$this -> mihorario[$mm] = $xtcurso;
					foreach($materias -> find("clave='".$xtcurso -> materia."'") as $materia){
						$this -> cursos[$i] = $materia -> nombre;
						$i++;
						break;
					}
					
					if($xtcurso->id <1000 && $xtcurso->id >= 100){
						$xtcurso->id = "0".$xtcurso->id;
					}
					if($xtcurso->id <100 && $xtcurso->id >= 10){
						$xtcurso->id = "00".$xtcurso->id;
					}
					if($xtcurso->id < 10){
						$xtcurso->id = "000".$xtcurso->id;
					}
					
					$this -> inscritos[$n] = $Xtalumnocursos -> count("curso_id='".$xtcurso->id."'");
					
					$n++;
					foreach($maestros -> find("nomina=".$id) as $maestro){
							$this -> profesor = $maestro -> nombre;
					}
					$this -> plantel[$mm] = "T";
					$mm++;
				}
			}
			// Fin tonala
        } // function listas()
		
		function captura(){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
				$this->redirect('profesor/cambiarContrasena');
			
			unset($this -> excel);
			unset($this -> mihorario);
			unset($this -> cursos);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> profesor);
			unset($this -> permisos);
			unset($this -> xpermesp);
			unset($this -> xtpermesp);
			unset($this -> permiso);
			unset($this -> inscritos);
			unset($this -> extras);
			unset($this -> titulos);
			unset($this -> tpermiso);
			unset($this -> tinscritos);
			unset($this -> textras);
			unset($this -> ttitulos);
			unset($this -> tcursos);
			unset($this -> flag);

			//$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_();
			
			if( Session::get_data('flag') == 3 )
				$this -> flag = "Las calificaciones sólo puede ser entre 0 y 100 ó NP";
			if( Session::get_data('flag') == 4 )
				$this -> flag = "Las faltas deben ser números";
			else if( Session::get_data('flag') == 2 )
				$this -> flag = "Las faltas no pueden ser mayor al número de horas capturadas en el parcial";
			Session::unset_data('flag');
			//$mishorarios = new Mishorarios();
			// Horarios de Colomos
			$xcursos = new Xccursos();
			
			// Horarios de Tonala
			$txcursos = new Xtcursos();
			
			$materias = new Materia();
			
			$maestros = new Maestros();
			$nomina = Session::get_data('registro');
			$this -> nomina = $nomina;
			$profesor = $maestros -> find_first("nomina=".$this -> nomina."");
			$this -> profesor = $profesor -> nombre;

			//$cursitos = new Registracursos();
			$Xalumnocursos = new Xalumnocursos();

			// Cursos de los Alumnos de Tonala
			$tcursitos = new Xtalumnocursos();

			$extraordinarios = new Xextraordinarios();

			// Extraordinarios de Tonala
			$textraordinarios = new Xtextraordinarios();

			$conpermiso = new Xpermisoscaptura();
			$permisos2 = new Xpermisos();

			// Permisos de Tonala
			$tconpermiso = new Xtpermisoscaptura();
			
			$xpermcapturaesp	= new Xpermisoscapturaesp();
			
			foreach($permisos2 -> find("periodo=".$periodo."") as $permiso){
					$this -> permisos = $permiso;
					break;
			}

			//$this -> mihorario = $mishorarios -> find("nomina=".$nomina." and periodo='".$periodo."' order by clavecurso");
			$this -> xcursos = $xcursos -> find("nomina=".$nomina." and periodo='".$periodo."' order by id");
			$i=0;$x=0;$n=0;
			
			// Colomos
			foreach($this -> xcursos as $curso){
					foreach($materias -> find("clave='".$curso -> materia."'") as $materia){
							$this -> cursos[$i] = $materia -> nombre;
							$i++;
							break;
					}
					
					$this -> permiso[$curso -> clavecurso] = $conpermiso -> find_first("curso_id='".$curso -> id."'");
					$this -> inscritos[$n] = $Xalumnocursos -> count("curso_id='".$curso -> clavecurso."'");
					
					$this -> extras[$n] = $extraordinarios -> count("curso_id='".$curso -> id."' AND tipo='E'");
					$this -> titulos[$n] = $extraordinarios -> count("curso_id='".$curso -> id."' AND tipo='T'");
					
					foreach( $xpermcapturaesp -> find_all_by_sql
							( "Select * from xpermisoscapturaesp
								where clavecurso = '".$curso -> clavecurso."'
								and captura = 0
								order by id desc limit 1" )
								as $xpermcapturaesp ){
						$this -> xpermesp[$curso -> clavecurso] = $xpermcapturaesp;
					}
					
					$n++;
			}

			$this -> txcursos = $txcursos -> find("nomina=".$nomina." and periodo='".$periodo."' order by id");
			$i=0;$x=0;$n=0;

			// Tonala
			foreach($this -> txcursos as $tcurso){
					foreach($materias -> find("clave='".$tcurso -> materia."'") as $materia){
							$this -> tcursos[$i] = $materia -> nombre;
							$i++;
							break;
					}

					$this -> tpermiso[$tcurso -> clavecurso] = $tconpermiso -> find_first("curso_id='".$tcurso -> id."'");

					$this -> tinscritos[$n] = $tcursitos -> count("curso_id='".$tcurso -> id."'");

					$this -> textras[$n] = $textraordinarios ->
									count("curso_id='".$tcurso -> id."' AND tipo='E'");
					$this -> ttitulos[$n] = $textraordinarios ->
									count("curso_id='".$tcurso -> id."' AND tipo='T'");
					
					foreach( $xpermcapturaesp -> find_all_by_sql
							( "Select * from xpermisoscapturaesp
								where clavecurso = '".$tcurso -> clavecurso."'
								and captura = 0
								order by id desc limit 1" )
								as $xpermcapturaesp ){
						$this -> xtpermesp[$tcurso -> clavecurso] = $xpermcapturaesp;
					}
					$n++;
			}

			$this -> periodo = $Periodos->convertirPeriodo_($periodo);

			$xextraaa 	= new Xextraordinarios();
			$xcursooo	= new Xcursos();
			$materiaaa	= new Materia();
			$xpermisos	= new Xpermisoscaptura();

			unset( $this -> materiasTit );
			unset( $this -> divisionTit );
			unset( $this -> registroTit );
			unset( $this -> permisosTit );
			unset( $this -> clavecursoTit );


			$i = 0;
			if( Session::get_data('coordinacion') == "IIM" ||
					Session::get_data('coordinacion') == "MCT" ||
							Session::get_data('coordinacion') == "TCB" ||
									Session::get_data('coordinacion') == "IEC" ){

				foreach( $xextraaa -> find_all_by_sql
						( "Select xext.registro, xcc.clavecurso, xcc.materia as materia, xcc.division, xext.curso_id
							from xextraordinarios xext, xcursos xcc, xalumnocursos xal
							where xext.curso_id = xcc.id
							and xal.curso_id = xext.curso_id
							and xal.registro = xext.registro
							and xext.periodo = ".$periodo."
							and xal.periodo = ".$this -> pasado."
							and xext.tipo = 'T'" ) as $xextra ){
					
					if( $xcursooo -> find_first( "clavecurso = '".$xextra -> clavecurso."'"."
							and periodo = ".$periodo ) ){

					}
					else{
						$this -> clavecursoTit[$i] = $xextra -> clavecurso;
						$this -> materiasTit[$i] = $materiaaa -> find_first( "clave = '".$xextra -> materia."'" );
						$this -> divisionTit[$i] = $xextra -> division;
						$this -> registroTit[$i] = $xextra -> registro;
						$this -> permisosTit[$i] = $xpermisos -> find_first
														( "curso_id = '".$xextra -> curso_id."'"."
																and periodo = ".$periodo );

						$i++;
					}
				}
			}

			foreach($this -> xextra as $xextra){
				foreach($materias -> find("clave='".$curso -> materia."'") as $materia){
						$this -> cursos[$i] = $materia -> nombre;
						$i++;
						break;
				}

				$this -> permiso[$curso -> clavecurso] = $conpermiso -> find_first("curso='".$curso -> clavecurso."' AND periodo='".$periodo."'");
				$this -> inscritos[$n] = $Xalumnocursos -> count("curso_id='".$curso -> id."'");

				$this -> extras[$n] = $extraordinarios -> count("clavecurso='".$curso -> clavecurso."' AND tipo='E'");
				$this -> titulos[$n] = $extraordinarios -> count("clavecurso='".$curso -> clavecurso."' AND tipo='T'");

				$n++;
			}
			
			unset($this -> icursos);
			// Obtener los Cursos Intersemestrales que tiene el profesor asignado para este periodo
			$IntersemestralCursos = new IntersemestralCursos();
			$this -> icursos = $IntersemestralCursos -> get_cursos_by_nomina_and_periodo($nomina);
			
        } // function captura()
		
        function capturaCalculo(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
					$this->redirect('/');
			}
			unset($this -> excel);
			unset($this -> mihorario);unset($this -> cursos);
			unset($this -> periodo);unset($this -> nomina);unset($this -> profesor);
			unset($this -> permisos);

			$periodo = $this -> actual;

			//$mishorarios = new Mishorarios();
			$xcursos = new Xcursos();
			//$materias = new Materiasing();
			$materias = new Materia();

			$maestros = new Maestros();
			$id = Session::get_data('registro');
			$this -> nomina = $id;
			$profesor = $maestros -> find_first("nomina=".$this -> nomina."");
			$this -> profesor = $profesor -> nombre;

			//$cursitos = new Registracursos();
			$cursitos = new Xalumnocursos();
			$extraordinarios = new Xextraordinarios();

			$conpermiso = new Xpermisoscaptura();
			$permisos2 = new Xpermisos();

			foreach($permisos2 -> find("periodo=".$periodo."") as $permiso){
					$this -> permisos = $permiso;
					break;
			}

			//$this -> mihorario = $mishorarios -> find("nomina=".$id." and periodo='".$periodo."' order by clavecurso");
			$this -> xcursos = $xcursos -> find("nomina=".$id." and periodo='".$periodo."' order by id");
			$i=0;$x=0;$n=0;

			foreach($this -> xcursos as $curso){
				foreach($materias -> find("clave='".$curso -> materia."'") as $materia){
					$this -> cursos[$i] = $materia -> nombre;
					$i++;
					break;
				}

				$this -> permiso[$curso -> clavecurso] = $conpermiso -> find_first("curso='".$curso -> clavecurso."' AND periodo='".$periodo."'");
				$this -> inscritos[$n] = $cursitos -> count("curso='".$curso -> clavecurso."'");

				$this -> extras[$n] = $extraordinarios -> count("clavecurso='".$curso -> clavecurso."' AND tipo='E'");
				$this -> titulos[$n] = $extraordinarios -> count("clavecurso='".$curso -> clavecurso."' AND tipo='T'");

				$n++;
			}
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";

			$this -> periodo .= substr($periodo,1,4);
			$this -> nomina = $id;
        } // function capturaCalculo()

        function convertirAAlumno($registro){
			// guardo el estado anterior del usuario.  ingresar
			Session::set_data('TMPregistro', Session::get_data('registro'));
			Session::set_data('TMPtipousuario', Session::get_data('tipousuario'));
			// le asigno el nivel de alumno con el registro que llego
			Session::set_data('tipousuario', "ALUMNO");
			Session::set_data('registro', $registro);

			$especialidades = new Especialidades();
			$alumnos = new Alumnos();
			$planes = new Plan();

			$alumno = $alumnos -> find_first("miReg=".$registro);
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);

			if($alumno -> enPlan == "PE07" || substr($alumno -> miPerIng,1,4) >= 2008 || $alumno -> miPerIng == 32007){
				switch($alumnos -> idtiEsp){
					case 13:
					case 14: $idcarrera = 6; Session::set_data('carrerita', "ELECTRONICA"); Session::set_data('nombrecarrera', "ELECTRONICA");	break;
					case 12:
					case 15: $idcarrera = 7; Session::set_data('carrerita', "INDUSTRIAL"); Session::set_data('nombrecarrera', "INDUSTRIAL");break;
					case 16: $idcarrera = 8; Session::set_data('carrerita', "MECATRONICA"); Session::set_data('nombrecarrera', "MECATRONICA");break;
				}
			}
			else{
				switch($alumnos -> idtiEsp){
					case 12: $idcarrera = 1; Session::set_data('carrerita', "INDUSTRIAL"); break;
					case 13: $idcarrera = 2; Session::set_data('carrerita', "ELECTRONICA"); break;
					case 14: $idcarrera = 3; Session::set_data('carrerita', "ELECTRONICA"); break;
					case 15: $idcarrera = 4; Session::set_data('carrerita', "INDUSTRIAL"); break;
					case 16: $idcarrera = 5; Session::set_data('carrerita', "MECATRONICA"); break;
				}
			}
			if(substr($alumno -> miPerIng,1,4) >= 2008 || $alumno -> miPerIng == 32007 || $alumno -> enPlan == "PE07"){
				$ciclo = 2007;
			}
			else{
				$ciclo = 2000;
			}

			$plan = $planes -> find_first("idcarrera=".$idcarrera." AND nombre='".$ciclo."'");
			$idplan = $plan -> id;

			Session::set_data('idplan', $idplan);
			Session::set_data('idcarrera', $idcarrera);
			Session::set_data('nombre', $alumno -> vcNomAlu);
			Session::set_data("plantel", $alumno -> enPlantel);

			$this -> route_to("controller: alumno","action: index");
        }

        function abrirCaptura(){
			if( $this -> validaAbrirCaptura() ){
					$this -> redirect("profesor/informacion");
			}
			if( Session::get_data('cambiarcontrasena') == 1 )
			$this->redirect('profesor/cambiarContrasena');

			$xcursos			= new Xcursos();
			$alumnos 			= new Alumnos();

			$periodo = $this -> actual;
			$i = 0;

			foreach ( $xcursos -> find ( "periodo = ".$periodo ) as $xcurso ){
					$this -> cursos[$i] = $xcurso -> clavecurso;
					$this -> materia[$i] = $xcurso -> materia;
					$this -> nominaProf[$i] = $xcurso -> nomina;
					$i++;
			}

			$this -> day = date ("d");
			$this -> month = date ("m");
			$this -> year = date ("Y");
			$this -> hour = date ("H");
			$this -> minute = date ("i");
			$this -> second = date ("s");


			// Al dia de hoy, le sumo uno y asi sucesivamente para tener el
			$this -> fechas[0] = mktime( $this -> hour, $this -> minute, $this -> second,
											$this -> month, $this -> day+1, $this -> year );

			$this -> fechas[1] = mktime( $this -> hour, $this -> minute, $this -> second,
											$this -> month, $this -> day+2, $this -> year );

        } // function abrirCaptura()

        function abrirCaptura2(){

                if( $this -> validaAbrirCaptura() ){
                        $this -> redirect("profesor/informacion");
                }

                $xpermisoscaptura	= new Xpermisoscaptura();
                $Xccursos			= new Xccursos();

                $curso	= $this -> post ("curso");
                $fecha	= $this -> post ("fecha");
                $parcial= $this -> post ("parcial");
				
				$xccursos = $Xccursos -> find_first("clavecurso = '".$curso."'");

                $periodo = $this -> actual;

                $this -> mensaje = "";
                if ( $xpermcaptura = $xpermisoscaptura -> find_first ( "curso_id = '".$xccursos->id."'
                                and periodo = ".$periodo ) ){

                        $maxcapturas = "maxcapturas".$parcial;
                        if ( $xpermcaptura -> $maxcapturas = 3 ){
                                $this -> mensaje = "No se puede abrir m&aacute;s veces el sistema,
                                                favor de conctactar a control escolar para tramitar una futura apertura";
                        }

                        $xpermcaptura -> $maxcapturas = $maxcapturas + 1;

                        $fin = "fin".$parcial;
                        $xpermcaptura -> $fin = $fecha;

                        $ncapturas = "ncapturas".$parcial;
                        $xpermcaptura -> $ncapturas = $xpermcaptura;

                        $xpermcaptura -> save();

                        //$fp = fopen("../../public/files/abrirCaptura".$periodo.".txt", "a");
                        $fp = fopen( "public/files/abrirCaptura".$periodo.".txt", "a" );
                        $ip = $_SERVER['REMOTE_ADDR'];
                        fwrite( $fp, Session::get_data('coordinacion').", ".
                                        Session::get_data('registro').", ".date ("Y-m-d H:i:s", $fecha).", ".
                                        $curso.", parcial: ".$parcial.", ip: ".$ip. PHP_EOL );
                        fclose( $fp );

                        $this -> mensaje = "Se ha hecho la apertura de horario correctamente,
                                        para el curso: ".$curso;
                }
        } // function abrirCaptura2()

        function convertirAProfesor(){
                Session::set_data('registro', Session::get_data('TMPregistro'));
                Session::set_data('tipousuario', Session::get_data('TMPtipousuario'));

                Session::unset_data('TMPregistro');
                Session::unset_data('TMPtipousuario');

                $this -> route_to("controller: profesor","action: informacion");
        }

        function validaAbrirCaptura(){

                // Con esto valido si quiero abrir el curso o no...
                return true;
        }

        function buscarAlumno(){
			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" && 
						Session::get_data("tipousuario") != "GOE" && 
							Session::get_data("tipousuario") != "TUTOR" && 
								Session::get_data("tipousuario") != "INGCALCULO" && 
									Session::get_data("tipousuario") != "DIRECCION"  && 
									    Session::get_data("tipousuario") != "CULTURA" ){
				$this->redirect('/');
			}
        } // function buscarAlumno()
		
        function verAlumno(){
			echo "En construcci&oacute;n";
        } // function verAlumno()

        function autorizarCruces(){
			
		}
		
		function permitirCrucesColomos(){
			
			$xalumnocursos	= new Xalumnocursos();
			$alumnos		= new Alumnos();

			unset( $this -> alumn );
			unset( $this -> tcursos );
			$division = Session::get_data('coordinacion');
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$i = 0;
			if( $division == "MCT" ){
				foreach( $xalumnocursos -> find_all_by_sql
					 ( "Select xcc.*
						from xccursos xcc
						where xcc.periodo =  ".$periodo."
						and ( xcc.division = '".$division."'
						or xcc.division = 'TCB' )") as $xalumncur ){
					$this -> xccursos[$i] = $xalumncur;
					$i ++;
				}
			}
			else{
				foreach( $xalumnocursos -> find_all_by_sql
					 ( "Select xcc.*
						from xccursos xcc
						where xcc.periodo = ".$periodo."
						and xcc.division = '".$division."'") as $xalumncur ){
					$this -> xccursos[$i] = $xalumncur;
					$i ++;
				}
			}
			
			//$i = 0;
			//foreach( $alumnos -> find( "pago = 1 and enPlantel = 'C' order by miReg" ) as $alumno ){
			//foreach( $alumnos -> find( "enPlantel = 'C' order by miReg limit 0, 750" ) as $alumno ){
				//$this -> alumn[$i] = $alumno;
				//$i ++;
			//}
		} // function permitirCrucesColomos()
		
		function permitirCrucesTonala(){
			$xalumnocursos	= new Xalumnocursos();
			$alumnos		= new Alumnos();
			$xtalumnocursos	= new Xtalumnocursos();

			unset( $this -> talumn );
			unset( $this -> xtcursos );
			$division = Session::get_data('coordinacion');
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			if( $division == "MCT" ){
				foreach( $xtalumnocursos -> find_all_by_sql
					 ( "Select xtc.*
						from xtcursos xtc
						where xtc.periodo = ".$periodo."
						and ( xtc.division = '".$division."'
						or xtc.division = 'TCB'
						or xtc.division = 'TCT' )") as $xtalumncur ){
					$this -> xtcursos[$i] = $xtalumncur;
					$i ++;
				}
			}
			else{
				foreach( $xtalumnocursos -> find_all_by_sql
					 ( "Select xtc.*
						from xtcursos xtc
						where xtc.periodo = ".$periodo."
						and xtc.division = '".$division."'") as $xtalumncur ){
					$this -> xtcursos[$i] = $xtalumncur;
					$i ++;
				}
			}
			
			$i = 0;
			//foreach( $alumnos -> find( "pago = 1 and enPlantel = 'N' order by miReg" ) as $talumno ){
			foreach( $alumnos -> find( "enPlantel = 'N' order by miReg" ) as $talumno ){
					$this -> talumn[$i] = $talumno;
					$i ++;
			}
		} // function permitirCrucesTonala()
		
		function permitirCrucesComunesC(){
			$xccursos = new xccursos();
			
			unset( $this -> xccursos2 );
			$division = Session::get_data('coordinacion');
			
			foreach( $xccursos -> find_all_by_sql
					 ( "Select xcc.*
						from xccursos xcc
						where xcc.periodo = ".$this -> actual."
						order by xcc.clavecurso" ) as $xalumncur ){
				$this -> xccursos2[$i] = $xalumncur;
				$i ++;
			}
		} // function permitirCrucesComunesC()
		
		function permitirCrucesComunesT(){
			$xtcursos = new xtcursos();
			
			unset( $this -> xtcursos2 );
			$division = Session::get_data('coordinacion');
			
			foreach( $xtcursos -> find_all_by_sql
					 ( "Select xcc.*
						from xtcursos xcc
						where xcc.periodo = ".$this -> actual."
						order by xcc.clavecurso" ) as $xtc ){
				$this -> xtcursos2[$i] = $xtc;
				$i ++;
			}
		} // function permitirCrucesComunesT()

        function permitiendoCruces(){
			
			unset( $this -> registro );
			unset( $this -> curso );
			unset( $this -> horas );
			unset( $this -> autorizacion );
			
			$registro	= $this -> post( "registro" );
			$curso 		= $this -> post( "curso" );
			$horas		= $this -> post( "horas" );
			
			$autorizarcruces	= new AutorizarCruces();
			$autorizando		= new AutorizarCruces();
			
			if( $autorizarcruces -> find_first
						( "registro = ".$registro."
								and periodo = ".$this -> actual."" ) ){
				// Si lo encuentra, significa que ya permitió el cruce de 1 hora
				//a ese registro. Por lo que sólo está permitido el cruce de 1 hora
				//por alumno.
				$this -> autorizacion = 0;
			}
			else{
				// No encontró el registro, por lo que se puede autorizar
				//su cruce.
				$autorizando -> clavecurso = $curso;
				$autorizando -> registro = $registro;
				$autorizando -> periodo = $this -> actual;
				$autorizando -> horasautorizadas = $horas;
				$autorizando -> create();

				$this -> registro = $registro;
				$this -> curso = $curso;
				$this -> horas = $horas;
				$this -> autorizacion = 1;
			}
        } // function autorizandoCruces()
		
        function permitiendoCrucesComunes(){
			
			$curso1		= $this -> post( "curso1" );
			$curso2		= $this -> post( "curso2" );

			unset( $this -> curso111 );
			unset( $this -> curso222 );

			$xccursos			= new Xccursos();
			$cursosComunes		= new cursosComunes();
			$autorizando		= new cursosComunes();

			if( $cursosComunes -> find_first
					( "clavecurso1 = '".$curso1."'"."
						or clavecurso2 = '".$curso1."'"."
							or clavecurso1 = '".$curso2."'"."
								or clavecurso2 = '".$curso2."'" ) ){
				// Si lo encuentra, significa que ese curso ya tiene su
				//curso en común, por lo que no se podrá volver
				//a dar el cruce.
				$this -> autorizacion = 0;
				//$this -> render_partial("autorizandoComunes");
			}
			else{
				// No encontró el curso, por lo que no tiene aún
				//asignado ningún curso en común, por lo que
				//es posible asignarle el curso en común.

				$autorizando -> clavecurso1 = $curso1;
				$autorizando -> clavecurso2 = $curso2;

				$this -> curso111 = $curso1;
				$this -> curso222 = $curso2;

				$autorizando -> create();
				
				$this -> autorizacion = 1;
			}
        } // function permitiendoCrucesComunes()

        function titulosPendientes(){

                $xextraordinarios		= new Xextraordinarios();
                $xalumnocursos			= new Xalumnocursos();
                $xcursos				= new Xcursos();
                $materias				= new Materia();

                unset( $this -> infoTitulos );

                $i = 0;
                foreach( $xextraordinarios -> find_all_by_sql
                         ( "Select xext.*, xc.materia, m.nombre, al.vcNomAlu
                                from xextraordinarios xext, xcursos xc, materia m, alumnos al
                                where xext.clavecurso = xc.clavecurso
                                and xext.periodo = 32010
                                and xc.periodo = 12010
                                and xc.materia = m.clave
                                and xext.registro = al.miReg
                                and tipo = \"T\"
                                group by xext.id
                                order by xext.registro" ) as $xextra ){
                        $this -> infoTitulos[$i] = $xextra;
                        $i++;
                }

        } // function titulosPendientes()

        function autorizarCargasDeMaterias(){

                // $xccursos		= new Xccursos();
                $xclogmovalumnocuros	= new Xclogmovimientosalumnocursos();
                $xalumnocursos			= new Xalumnocursos();
                $xccursos				= new Xccursos();
                $xchorascursos			= new Xchorascursos();
                $xcsalones				= new Xcsalones();
                $alumnos				= new Alumnos();
                $seleccionTiempo		= new SeleccionTiempo();
                $condicionadoss			= new ReconsideracionBaja();


                $nomina = Session::get_data('registro');
                $division = Session::get_data('coordinacion');

                unset( $this -> noCondicionados );
                unset( $this -> condicionados );
				
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual();
                $i = 0;
                foreach( $seleccionTiempo -> find_all_by_sql
                                 ( "Select sl.registro as registroo, al.vcNomAlu, al.enPlan
                                        from seleccion_tiempo sl, alumnos al
                                        where sl.registro = al.miReg
                                        and sl.periodo = ".$periodo."
                                        and sl.registro not in
                                                ( Select registro
                                                from reconsideracion_baja )
                                        order by sl.registro" ) as $selTiempo ){

                        $this -> noCondicionados[$i] = $selTiempo;
                        $i ++;
                }

                $i = 0;
                foreach( $condicionadoss -> find_all_by_sql
                                 ( "Select rb.registro as registroo, al.vcNomAlu, al.enPlan
                                        from reconsideracion_baja rb, alumnos al
                                        where rb.procede = 1
                                        and rb.periodo = ".$periodo."
                                        and rb.registro = al.miReg
                                        order by registro" ) as $condicionado ){

                        $this -> condicionados[$i]		= $condicionado;
                        $i ++;
                }

        } // function autorizarCargasDeMaterias()

        function opcionesCargarMateria( $registro, $tipoBusqueda ){

                $xclogmovalumnocuros	= new Xclogmovimientosalumnocursos();
                $xalumnocursos			= new Xalumnocursos();
                $xccursos				= new Xccursos();
                $xchorascursos			= new Xchorascursos();
                $xcsalones				= new Xcsalones();
                $alumnos				= new Alumnos();
                $seleccionTiempo		= new SeleccionTiempo();
                $condicionadoss			= new ReconsideracionBaja();

                unset( $this -> registroo );
                unset( $this -> tipoBusqueda );
                unset( $this -> cond );
				$Periodos = new Periodos();
				$periodo = $Periodos -> get_periodo_actual();
				
                $registro = (int)$registro;
                foreach( $condicionadoss -> find_all_by_sql
                                                         ( "Select * from reconsideracion_baja
                                                                where procede = 1
                                                                and periodo = ".$periodo."
                                                                and registro = ".$registro ) as $condicionado){
                        $this -> cond = $condicionado;
                }

                $this -> registroo = $registro;
                $this -> tipoBusqueda = $tipoBusqueda;

                $this -> set_response("view");

                echo $this -> render_partial("opcionesCargarMateria");
        } // function opcionesCargarMateria( $registro, $tipoDeBusqueda )

        function abrirSeleccionAlumno(){
			$seleccionTiempo		= new SeleccionTiempo();
			//$condicionadoss			= new ReconsideracionBaja();

			$this->redirect("profesor/informacion");
			return;
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

			$nomina = Session::get_data('registro');
			$division = Session::get_data('coordinacion');

			unset( $this -> noCondicionados );
			unset( $this -> condicionados );
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();

			$i = 0;
			foreach( $seleccionTiempo -> find_all_by_sql
					 ( "select al.miReg registroo, al.vcNomAlu, al.enPlan
						From alumnos al
						where (pago = 1
						or condonado = 1)
						and al.miReg not in
						( select registro
						  from reconsideracion_baja
						  where periodo = '".$periodo."')
						  order by al.miReg" ) as $selTiempo ){

				$this -> noCondicionados[$i] = $selTiempo;
				$i ++;
			}
			/*
			$i = 0;
			foreach( $condicionadoss -> find_all_by_sql
					( "Select rb.registro as registroo, al.vcNomAlu, al.enPlan
						from reconsideracion_baja rb, alumnos al
						where rb.procede = 1
						and rb.periodo = ".$periodo."
						and rb.registro = al.miReg
						order by registro" ) as $condicionado ){

				$this -> condicionados[$i]		= $condicionado;
				$i ++;
			}
			*/
        } // function abrirSeleccionAlumno()


        function abrirSeleccionAlumno2( $registroo, $tiempo ){
			$seleccionTiempo		= new SeleccionTiempo();

			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			$date1 = mktime($hour, $minute, $second, $month, $day, $year);
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$this -> set_response("view");
			
			if( !isset($tiempo) || $tiempo == -1 ){
				$date1 +=(($tiempo+1) * 60);
			}else{
				$date1 += ($tiempo * 3600);
			}
			foreach( $seleccionTiempo -> find
					( "registro = ".$registroo."
							and periodo = ".$periodo) as $selTiempo ){
				$selTiempo -> inicio = $year."-".$month."-".($day)." ".$hour.":".($minute -2).":33";
				//$selTiempo -> fin = $year."-".$month."-".$day." ".$hour.":".$minute.":33";
				$selTiempo -> fin = date("Y-m-d H-i-s", $date1);
				// $year."-".$month."-".$day." ".($hour-2).":".($minute + 17).":33"
				$selTiempo -> save();
				// 2008-08-11 09:00:00
			}
			if( !$selTiempo = $seleccionTiempo -> find_first
					( "registro = ".$registroo."
							and periodo = ".$periodo) ){
				$seleccionTiempo -> registro = $registroo;
				$seleccionTiempo -> promedio = '0';
				$seleccionTiempo -> inicio = $year."-".$month."-".($day)." ".$hour.":".($minute-2).":33";
				/*
				if( ($minute + 30) > 60 ){
					$minute = ( ($minute + 30) ) - 60;
					$hour = $hour + 1;
				}else{
					$hour += $tiempo;
				}
				if( ($hour + $tiempo) > 24 ){
					$hour = ( $hour + $tiempo ) - 24;
					$day = $day + 1;
				}else{
					$hour += $tiempo;
				}
				*/
				//$seleccionTiempo -> fin = $year."-".$month."-".$day." ".$hour.":".$minute.":33";
				$selTiempo -> fin = date("Y-m-d H-i-s", $date1);
				$seleccionTiempo -> periodo = $this -> actual;
				$seleccionTiempo -> create();
			}

			echo "$registroo Apertura exitosa<br />";

			echo "Hora actual: ".
					$year."-".$month."-".$day." ".$hour.":".$minute.":".$second."<br />";

			echo "El sistema se cerrar&aacute; a las: ".date("Y-m-d H-i-s", $date1)."<br />";


			$this -> render_partial("abrirSeleccionAlumno2");
        } // function abrirSeleccionAlumno2()

        function listadoDeAlumnos(){

			$xalumnocursos	= new Xalumnocursos();
			$xccursos		= new Xccursos();
			$materia		= new Materia();
			$Alumnos		= new Alumnos();
			$Carrera		= new Carrera();

			unset( $this -> registroAlumno );
			unset( $this -> nombreAlumno );
			unset( $this -> planAlumno );
			unset( $this -> pago );
			unset( $this -> materiaAlumno );
			unset( $this -> cursoAlumno );

			$cursoID = $this -> post( "cursoID" );


			$this -> cursoClave = $cursoID;
			
			$xccurso = $xccursos -> find_first( "clavecurso = '".$cursoID."'" );

			$i = 0;
			foreach( $xalumnocursos -> find( "curso_id  = '".$xccurso->id."' order by registro asc" ) as $xalumncur ){
				$alumno = $Alumnos -> get_relevant_info_from_student($xalumncur -> registro);
				
				$mat = $materia -> find_first( "clave = '".$xccurso -> materia."'" );
				$carrera = $Carrera -> get_nombre_carrera($alumno -> carrera_id);

				$this -> registroAlumno[$i]		= $xalumncur -> registro;
				$this -> nombreAlumno[$i]		= $alumno -> vcNomAlu;
				$this -> planAlumno[$i]			= $alumno -> enPlan;
				$this -> pago[$i]				= $alumno -> pago;
				$this -> materiaAlumno[$i]		= $mat -> nombre;
				$this -> carreraAlumno[$i]		= $carrera -> nombre;

				$i ++;
			}

        } // function listadoDeAlumnos()

        function tlistadoDeAlumnos(){

            $xtalumnocursos	= new Xtalumnocursos();
            $xtcursos		= new Xtcursos();
            $materia		= new Materia();
            $alumnos		= new Alumnos();
            $especialidades = new Especialidades();
			$Carrera		= new Carrera();

            unset( $this -> registroAlumno );
            unset( $this -> nombreAlumno );
            unset( $this -> cursoAlumno );
            unset( $this -> materiaAlumno );

            $cursoID = $this -> post( "cursoID" );
            $this -> cursoClave = $cursoID;

			$xccurso = $xtcursos -> find_first( "clavecurso = '".$cursoID."'" );
			
            $i = 0;
            foreach( $xtalumnocursos -> find( "curso_id  = '".$xccurso->id."'" ) as $xalumncur ){
                    $alumno = $alumnos -> find_first( "miReg = ".$xalumncur -> registro );

                    $mat = $materia -> find_first( "clave = '".$xccurso -> materia."'" );
                    $carrera = $Carrera -> get_nombre_carrera($alumno -> carrera_id);
					
					$this -> registroAlumno[$i]		= $xalumncur -> registro;
					$this -> nombreAlumno[$i]		= $alumno -> vcNomAlu;
					$this -> planAlumno[$i]			= $alumno -> enPlan;
					$this -> pago[$i]				= $alumno -> pago;
					$this -> materiaAlumno[$i]		= $mat -> nombre;
					$this -> carreraAlumno[$i]		= $carrera -> nombre;
					
                    $i ++;
            }

        } // function tlistadoDeAlumnos()

        function cambiarContrasena( $vacia ){

            $usuarios	= new Usuarios();
            $maestros	= new Maestros();

			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" &&
						Session::get_data("tipousuario") != "GOE" && 
							Session::get_data("tipousuario") != "DIRECCION" && 
							   Session::get_data("tipousuario") != "CULTURA" ){
				$this->redirect('seguridad/terminarsesion');
            }

            $id = Session::get_data('registro');
			
            // Eliminar las variables que van a pertenecer a este método.
            unset($this -> usuario);
            unset($this -> maestro);
			unset($this -> vacia);
			unset($this -> last);
			unset($this -> date);
			
			$semilla = new Semilla();
			foreach( $usuarios -> find_all_by_sql( "
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave, passwd_last_change
					from usuarios
					where registro = '".$id."'") as $usuario){
				$this -> usuario = $usuario;
			}
            $this -> maestro = $maestros -> find_first( "nomina = '".$id."'" );
			$this -> vacia = $vacia;
			
			if( $this -> usuario -> passwd_last_change == 0 ){
				$this -> date = "No ha modificado su contrase&ntilde;a recientemente, por motivos de seguridad
				es necesario cambiarla";
				$this -> last = 0;
			}
			else if( Session::get_data('cambiarcontrasena') == 1 ){
				$this -> date = date("F j, Y, g:i a", $usuarios -> passwd_last_change);
				$this -> last = 1;
			}
        } // function cambiarContrasena()

        function cambiandoContrasena(){

            $usuarios	= new Usuarios();
            $maestros	= new Maestros();

			if( Session::get_data('tipousuario')!="PROFESOR" &&
					Session::get_data("tipousuario") != "OBSERVADOR" &&
						Session::get_data("tipousuario") != "GOE" && 
							Session::get_data("tipousuario") != "DIRECCION" && 
							Session::get_data("tipousuario") != "CULTURA" ){
				$this->redirect('seguridad/terminarsesion');
            }

            $id = Session::get_data('registro');

            $contrasena = $this -> post( "contrasena" );

            if( !isset($contrasena) || $contrasena == "" )
                $this->redirect('profesor/cambiarContrasena/1');
            // Eliminar las variables que van a pertenecer a este método.
            unset( $this -> usuario );
            unset( $this -> exito );

            $this -> exito = 0;
			$contrasenaAnterior = "";
			$semilla = new Semilla();
            foreach( $usuarios -> find_all_by_sql( "
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave,
					AES_DECRYPT(passwd_old,'".$semilla -> getSemilla()."') passwd_old
					from usuarios
					where registro = '".$id."'") as $usuario){
				if( $contrasena == $usuario -> passwd_old || $contrasena == $usuario -> clave ){
					$this->redirect('profesor/cambiarContrasena/2'); return;
				}
				if( strlen($contrasena) < 6 || strlen($contrasena) > 15	){
					$this->redirect('profesor/cambiarContrasena/3'); return;
				}
				$contrasenaAnterior = $usuario -> clave;
			}
			$day = date ("d"); $month = date ("m"); $year = date ("Y");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$semilla = new Semilla();
            foreach( $usuarios -> find_all_by_sql( "
					update usuarios
					set clave = AES_ENCRYPT('".$contrasena."','".$semilla -> getSemilla()."'),
					passwd_old = '".$contrasenaAnterior."',
					passwd_last_change = ".mktime( $hour, $minute, $second, $month, $day, $year )."
					where registro = '".$id."'" ) as $usuario ){
            }
			$this -> exito = 1;
			Session::unset_data('cambiarcontrasena');
			foreach( $usuarios -> find_all_by_sql( "
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave,
					AES_DECRYPT(passwd_old,'".$semilla -> getSemilla()."') passwd_old, registro
					from usuarios
					where registro = '".$id."'") as $usuario){
				$this -> usuario = $usuario;
			}
			$this -> maestro = $maestros -> find_first( "nomina = '".$id."'" );

        } // function cambiandoContrasena()
		
		function cambiarContrasenaZopilote(){
			
            $zopilote	= new Zopilote();
            $maestros	= new Maestros();
			
            $id = Session::get_data('registro');
			
            // Eliminar las variables que van a pertenecer a este método.
            unset ( $this -> login );
            unset ( $this -> maestro );
			
            $this -> login = $zopilote -> find_first( "login = ".$id );
			
            $this -> maestro = $maestros -> find_first( "nomina = ".$id );
			
		} // function cambiarContrasenaZopilote()
		
		function cambiandoContrasenaZopilote(){
			
            $Zopilote	= new Zopilote();
            $maestros	= new Maestros();

            $contrasena = $this -> post( "contrasena" );
			$login = $this -> post( "login" );
			
            if( !isset($contrasena) )
				$this->redirect('profesor/index');

            // Eliminar las variables que van a pertenecer a este método.
            unset( $this -> usuario );
            unset( $this -> exito );

            $this -> exito = 0;
			
			if( $Zopilote -> find_first("pwd = '".$contrasena."'") ){
				$this -> exito = 2; // No se aceptan contraseñas zopilotes iguales...
				$this -> render("cambiandoContrasenaZopilote");
				return;
			}
			
            foreach( $Zopilote -> find( "login = ".$login." limit 1" ) as $zop ){
				$zop -> pwd = $contrasena;
				
				$zop -> save();

				$this -> zopilotee = $zop;
				
				$this -> maestro = $maestros -> find_first( "nomina = '".$login."'" );

				$this -> exito = 1;
            }
			
		} // function cambiandoContrasenaZopilote()
		
		function sacarPromFinal($idXalumnocursos, $calif3){
			
			$this -> set_response("view");
			
			unset( $this -> promedioFinal );
			unset( $prom );
			unset( $xalcursos );
			unset( $xalumnocursos );
			unset( $calificacion1 );
			unset( $calificacion2 );
			unset( $calificacion3 );
			
			$xalumnocursos = new Xalumnocursos();
			
			$xalcursos = $xalumnocursos -> find_first( "id = ".$idXalumnocursos );
			
			$calificacion1 = $xalcursos -> calificacion1;
			$calificacion2 = $xalcursos -> calificacion2;
			
			if( $calificacion1 == 300 ||
					$calificacion1 == 999 )
				$calificacion1 = 0;
			
			if( $calificacion2 == 300 ||
					$calificacion2 == 999 )
				$calificacion2 = 0;
			
			$prom = $calificacion1 + $calificacion2 + $calif3;
			$prom /= 3;
			
			$prom = substr($prom, 0, 5);
			
			$this -> promedioFinal = "<th width='50'>$prom</th>";
			
			$this -> render_partial("promedioFinal");
			
		} // function sacarPromFinal()
		
		function sacarPromFinalT($idXalumnocursos, $calif3){
			
			$this -> set_response("view");
			
			unset( $this -> promedioFinal );
			unset( $prom );
			unset( $xalcursos );
			unset( $xalumnocursos );
			unset( $calificacion1 );
			unset( $calificacion2 );
			unset( $calificacion3 );
			
			$Xtalumnocursos = new Xtalumnocursos();
			
			$xalcursos = $Xtalumnocursos -> find_first( "id = ".$idXalumnocursos );
			
			$calificacion1 = $xalcursos -> calificacion1;
			$calificacion2 = $xalcursos -> calificacion2;
			
			if( $calificacion1 == 300 ||
					$calificacion1 == 999 )
				$calificacion1 = 0;
			
			if( $calificacion2 == 300 ||
					$calificacion2 == 999 )
				$calificacion2 = 0;
			
			$prom = $calificacion1 + $calificacion2 + $calif3;
			$prom /= 3;
			
			$prom = substr($prom, 0, 5);
			
			$this -> promedioFinal = "<th width='50'>$prom</th>";
			
			$this -> render_partial("promedioFinal");
			
		} // function sacarPromFinalT()
		
		function permisosCapturaEspeciales(){
			
			// Está función sirve para abilitar los permisos de
			//captura de parciales, que esten fuera del tiempo que
			//se estableció desde un principio.
			
			// Está función guarda que coordinador fue el que autorizó la
			//apertura de curso, además de que es necesario ingresar el
			//registro del alumno para poder filtrar y guardar a que registros
			//se les quiere hacer correción de calificaciones.
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			// El módulo está cerrado temporalmente
			//hasta que Cristy o Ricardo vuelvan
			//a aceptar su apertura.
			// Descomentar la siguiente línea para desactivarlo...
			//$this->redirect('profesor/informacion');
			
			$xperm_detalle		= new XpermisoscapturaespDetalle();
			$xpermcapturaesp	= new Xpermisoscapturaesp();
			$xperiodoscaptura	= new Xperiodoscaptura();
			$xccursos			= new Xccursos();
			
			unset( $this -> fecha );
			unset( $this -> inicio );
			unset( $this -> fin );
			unset( $this -> parcial );
			unset( $this -> plantelAux );
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			
			$fecha = mktime( $hour, $minute, $second, $month, $day, $year );
			
			$i = 0;
			
			foreach( $xperiodoscaptura -> 
					find( "periodo = ".$this -> actual."
						and plantel = 'C'
						and fin < ".$fecha." "."
						order by id" ) as $xperiodoscapt ){
				
				$this -> inicio[$i] = $xperiodoscapt -> inicio;
				$this -> fin[$i] 	= $xperiodoscapt -> fin;
				$this -> parcial[$i]= $xperiodoscapt -> parcial;
				
				$i++;
			}
			
			if( Session::get_data('coordinacion') == "TCB" // ){
					|| Session::get_data('coordinacion') == "MCT" 
						|| Session::get_data('coordinacion') == "TCT" ){
				$this -> plantelAux = 1;
			}
			
		} // function permisosCapturaEspeciales()
		
		function cursosCapturaEspecial( $plantel ){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			$this -> set_response("view");
			
			$xccursos			= new Xccursos();
			$materia			= new Materia();
			
			unset( $this -> xccursos );
			
			$i = 0;
			
			if( Session::get_data('coordinacion') == "MCT" && $plantel == "t" ){
				$query = "Select xcc.clavecurso, m.clave, m.nombre, xcc.nomina, ma.nombre as nomprof
					from x".$plantel."cursos xcc, materia m, maestros ma
					where xcc.periodo = ".$this -> actual."
					and xcc.materia = m.clave
					and xcc.nomina = ma.nomina
					and ( xcc.division = '".Session::get_data('coordinacion')."'
					or xcc.division = 'TCB'
					or xcc.division = 'TCT' )
					group by xcc.id";
			}
			else{
				$query = "Select xcc.clavecurso, m.clave, m.nombre, xcc.nomina, ma.nombre as nomprof
					from x".$plantel."cursos xcc, materia m, maestros ma
					where xcc.periodo = ".$this -> actual."
					and xcc.materia = m.clave
					and xcc.nomina = ma.nomina
					and xcc.division = '".Session::get_data('coordinacion')."'
					group by xcc.id";
			}
			
			foreach( $xccursos ->  find_all_by_sql
				( $query ) as $xcc ){
				
				$this -> xccursos[$i] = $xcc;
				$i++;
			}
			
			$this -> render_partial("cursosCapturaEsp");
			
		} // function cursosCapturaEspecial()
		
		function alumnosCapturaEsp( $clavecurso, $plantel ){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			$this -> set_response("view");
			
			$Xccursos			= new Xccursos();
			$Alumnos			= new Alumnos();
			
			unset( $this -> alumnos );
			unset( $this -> fechas );
			
			if( $plantel == "c" )
				$plantel = "";
			
			$i = 0;
			
			$xccursos = $Xccursos -> find_first("clavecurso= '".$clavecurso."'");
			
			$this->alumnos = $Alumnos -> get_info_xalumnocursos_alumnos_carrera($xccursos->id, $plantel);
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			
			// Al dia de hoy, le sumo uno y asi sucesivamente para tener el
			$this -> fechas[0] = mktime( 23, 59, 59,
											$month, $day+1, $year );
			
			$this -> fechas[1] = mktime( 23, 59, 59,
											$month, $day+2, $year );
			
			$this -> render_partial("alumnosCapturaEsp");
			
		} // function alumnosCapturaEsp()
		
		function capturandoCapturaEspecial(){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('seguridad/terminarsesion');
			}
			
			unset( $this -> fechas );
			
			$xccursos				= new Xccursos();
			$xalumnocursos			= new Xalumnocursos();
			$xpermcaptura			= new Xpermisoscaptura();
			$xpermcapturaesp		= new Xpermisoscapturaesp();
			$xpermcapturaespdet		= new XpermisoscapturaespDetalle();
			
			$fin		= $this -> post( "fechaFin" );
			$plantel	= $this -> post( "plantel" );
			$clavecurso = $this -> post( "curso" );
			$registro	= $this -> post( "alumnoRadioTodos" );
			if( $registro != "-99" )
				$registro	= $this -> post( "alumnoRadio" );
			
			// Checar que parcial fue el que se habilito para la captura.
			$parcial1 = $this -> post( "parcial1");
			$parcial2 = $this -> post( "parcial2");
			$parcial3 = $this -> post( "parcial3");
			
			$parcial4 = $this -> post( "parcial4");
			$parcial5 = $this -> post( "parcial5");
			
			if( $parcial1 != null )
				$parcial = $parcial1;
			if( $parcial2 != null )
				$parcial = $parcial2;
			if( $parcial3 != null )
				$parcial = $parcial3;
			if( $parcial4 != null )
				$parcial = $parcial4;
			if( $parcial5 != null )
				$parcial = $parcial5;
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			
			$fecha_autorizacion = mktime( $hour, $minute, $second, $month, $day, $year );
			
			$xpermcapturaesp -> nomina		= Session::get_data('registro');
			$xpermcapturaesp -> plantel		= $plantel;
			$xpermcapturaesp -> clavecurso	= $clavecurso;
			$xpermcapturaesp -> parcial		= $parcial;
			$xpermcapturaesp -> fin			= $fin;
			$xpermcapturaesp -> fecha_autorizacion = $fecha_autorizacion;
			
			$xpermcapturaesp -> captura = '0';
			$xpermcapturaesp -> fecha_captura = '0';
			
			$xpermcapturaesp -> create();
			
			foreach( $xpermcapturaesp -> find_all_by_sql
					( "Select max(id) from xpermisoscapturaesp" ) as $xpermesp ){
				foreach( $registro as $reg ){
					$xpermcapturaespdet -> xpermisoscapturaesp_id = $xpermesp -> id;
					$xpermcapturaespdet -> registro = $reg;
					
					$xpermcapturaespdet -> create();
				}
			}
		} // function capturandoCapturaEspecial()
		
		function capturarListaActividad(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			// Eliminar variables que perteneceran a la clase.
			unset($this -> listaActividades);
			unset($this -> actividadMaestro);
			unset($this -> horasClase);
			unset($this -> actividadMaestro);
			
			unset($this -> actividadComplementaria);
			unset($this -> horasParaActComplementarias);
			unset($this -> IrConCoordinadorParaAsignacionDeHorario);
			unset($this -> horasClaseActComplementaria);
			unset($this -> horarioTerminado);
			unset($this -> horasParaCompletarComoAsignatura);
			$this -> actividadComplementaria = 2;
			
			$listaActividades = new ActividadMaestroListaActividades();
			$actividadMaestro = new ActividadMaestro();
			$maestrosHorarioterminado = new maestrosHorarioterminado();
			
			$this -> infoMaestro = $actividadMaestro -> get_info_maestro($nomina);
			$this -> horasClase = $actividadMaestro -> get_horasclase($nomina);
			
			if( $this -> infoMaestro -> nombramiento_horas == 0 ){
				switch($this -> infoMaestro -> nombramiento){
					case 1: $this -> infoMaestro -> nombramiento_horas = 37.5; break;
					case 2: $this -> infoMaestro -> nombramiento_horas = 28; break;
					case 3: $this -> infoMaestro -> nombramiento_horas = 19; break;
				}
			}
			
			// Primeramente checar si el profesor ya termino su horario,
			//si es así mandarlo a imprimir horario...
			if( $maestrosHorarioterminado -> get_profesor_horario_status($nomina) ){
				$this -> horarioTerminado = 1;
				$this -> actividadMaestro = $actividadMaestro -> get_lista_actividad_maestro_actividadPDF($nomina);
			}
			else if( $this -> infoMaestro -> nombramiento == 4 ){
				// Maestro por asignatura, Puede capturar actividades Complementarias...
				$this -> actividadMaestro = $actividadMaestro -> get_lista_actividad_maestro_asignatura($nomina);
				$horasActividades = 0;
				foreach( $this -> actividadMaestro as $act ){
					$horasActividades += $act -> horas;
				}
				$this -> listaActividades = $listaActividades -> get_lista_actividades_complementarias();
				$this -> horasParaCompletarComoAsignatura = ((37.5) -( $this -> horasClase + $horasActividades));
			}
			else{
				$this -> listaActividades = $listaActividades -> get_lista_actividades_complementarias();
				$this -> actividadMaestro = $actividadMaestro -> get_lista_actividad_maestro($nomina);
				
				if( isset($this -> actividadMaestro) ){
					//Ya tiene actividad complementaria, por lo que su
					//60% ya esta completo, no es necesario revisar más acerca de su 60%.
					$this -> horasClaseActComplementaria = 1;
					$this -> segundaParteDeHorario();
				}
				else{
					if( $this -> horasClase > 0 ){
						// Si tiene horas asignadas para este periodo, por lo que
						//podrá "completar" su horario.
						$this -> horasClaseMayorACero();
					}
					else{
						// Acudir con coordinador para que el pueda asignarle un horario al profesor.
						$this -> IrConCoordinadorParaAsignacionDeHorario = 1;
					}
				}
			}
		} // capturarListaActividad()
		
		function guardarActividadAsignatura(){
			
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			
			$listaactividades = $this -> post("listaactividades");
			
			$objetivo = $this -> post("objetivo");
			$meta = $this -> post("meta");
			$evidencia = $this -> post("evidencia");
			$horasParaCompletarComoAsignatura2 = $this -> post("horasParaCompletarComoAsignatura2");
			
			unset($this -> actividadMaestro);
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			// Guardando las actividades
			$actividadMaestro = new ActividadMaestro();
			$actividadMaestro -> nomina = $nomina;
			$actividadMaestro -> actividad_maestro_lista_actividades = $listaactividades;
			$actividadMaestro -> horas = $horasParaCompletarComoAsignatura2;
			$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
			$actividadMaestro -> objetivo = $objetivo;
			$actividadMaestro -> meta = $meta;
			$actividadMaestro -> evidencia = $evidencia;
			// cambiar por this -> actual
			$actividadMaestro -> periodo = $periodo;
			//$actividadMaestro -> periodo = $this -> actual;
			$actividadMaestro -> avance1 = '0';
			$actividadMaestro -> avance2 = '0';
			$actividadMaestro -> avance3 = '0';
			$actividadMaestro -> create();
			
			$listaActividades = new ActividadMaestroListaActividades();
			$actividadMaestro = new ActividadMaestro();
			
			$this -> actividadMaestro = $actividadMaestro -> get_lista_actividad_maestro_asignatura($nomina);
			$this -> horasClase = $actividadMaestro -> get_horasclase($nomina);
			
			$horasActividades = 0;
			foreach( $this -> actividadMaestro as $act ){
				$horasActividades += $act -> horas;
			}
			
			$this -> listaActividades = $listaActividades -> get_lista_actividades_complementarias();
			$this -> horasParaCompletarComoAsignatura = ((37.5) - ($this -> horasClase + $horasActividades));
			
			$this -> redirect("profesor/capturarListaActividad");
		} // function guardarActividadAsignatura()
		
		function editarActividad(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$this -> set_response("view");
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			unset($this -> actividadMaestro);
			$editID = $this -> post("editID");
			$actividadMaestro = new ActividadMaestro();
			
			$this -> actividadMaestro = $actividadMaestro -> get_actividad_maestro($nomina, $editID);
			
			
			unset($this -> unixtimestampparcial1);
			unset($this -> unixtimestampparcial2);
			unset($this -> unixtimestampparcial3);
			unset($this -> actualunixtime);
			
			$xperiodoscaptura = new Xperiodoscaptura();
			$this -> unixtimestampparcial1 = $xperiodoscaptura -> get_unixtimestamp_from_partial(1);
			$this -> unixtimestampparcial2 = $xperiodoscaptura -> get_unixtimestamp_from_partial(2);
			$this -> unixtimestampparcial3 = $xperiodoscaptura -> get_unixtimestamp_from_partial(3);
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			$this -> actualunixtime = mktime($hour, $minute, $second, $month, $day, $year);
			
			$this -> render_partial("editandoActividad");
			die;
		} // editarActividad()
		
		function verActividad(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$this -> set_response("view");
			
			$nomina = Session::get_data('registro');
			unset($this -> actividadMaestro);
			$viewID = $this -> post("viewID");
			unset( $this -> actividadMaestro );
			unset( $this -> actividadhoras );
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$actividadMaestro = new ActividadMaestro();
			//$actividadMaestroHoras = new ActividadMaestroHoras();
			foreach( $actividadMaestro -> find_all_by_sql(
					"select lact.actividad as nombreActividad, am.*
					from actividad_maestro am, actividad_maestro_lista_actividades lact
					where am.nomina = ".$nomina."
					and am.periodo = ".$periodo."
					and am.id = ".$viewID."
					and am.actividad_maestro_lista_actividades = lact.clave") as $actividad ){
				$this -> actividadMaestro = $actividad;
			}
			/*
			$i = 0;
			foreach( $actividadMaestro -> find_all_by_sql(
					"select horasreales, semana
					from actividad_maestro_horas
					where actividad_maestro_id = ".$viewID."
					order by semana") as $actividadhoras ){
				$this -> actividadhoras[$i] = $actividadhoras;
				$i++;
			}
			*/
			$this -> render_partial("verActividad");
			die;
		} // verActividads()
		
		function editandoActividad(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			unset($this -> actividadMaestro);
			
			$actividadid = $this -> post("actividadid");
			$horas = $this -> post("horas");
			//$objetivo = $this -> post("objetivo");
			//$meta = $this -> post("meta");
			$periodo = $this -> post("periodo");
			$semana = $this -> post("semana");
			//$autoevaluacion1 = $this -> post("autoevaluacion1");
			$avance1 = $this -> post("avance1");
			//$autoevaluacion2 = $this -> post("autoevaluacion2");
			$avance2 = $this -> post("avance2");
			//$autoevaluacion3 = $this -> post("autoevaluacion3");
			$avance3 = $this -> post("avance3");
			
			$actividadMaestro = new ActividadMaestro();
			$actividad = $actividadMaestro -> find_first("id = '".$actividadid."'");
			/*
			$actividadMaestroHoras = new ActividadMaestroHoras();
			if( $actividadHora = $actividadMaestroHoras -> find_first
					("actividad_maestro_id = ".$actividad -> id.
						" and semana = ".$semana) ){
				//$actividadHora -> horasreales = $horasreales;
				$actividadHora -> save();
			}else{
				$actividadMaestroHoras -> actividad_maestro_id = $actividad -> id;
				//$actividadMaestroHoras -> horasreales = $horasreales;
				$actividadMaestroHoras -> semana = $semana;
				$actividadMaestroHoras -> create();
			}
			*/
			$actividadMaestro = new ActividadMaestro();
			$actividad -> horas = '0';
			$actividad -> objetivo = $objetivo;
			$actividad -> meta = $meta;
			if( $avance1 == null || $avance1 == 0 )
				$avance1 = '0';
			$actividad -> avance1 = $avance1;
			if( $avance2 == null || $avance2 == 0 )
				$avance2 = '0';
			$actividad -> avance2 = $avance2;
			if( $avance3 == null || $avance3 == 0 )
				$avance3 = '0';
			$actividad -> avance3 = $avance3;
			$actividad -> save();
			
			$this->redirect('profesor/capturarListaActividad');
		} // editandoActividad()
		
		function borrandoActividad(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$nomina = Session::get_data('registro');
			unset($this -> actividadMaestro);
			
			$maestrosHorarioterminado = new maestrosHorarioterminado();
			if( $maestrosHorarioterminado -> get_profesor_horario_status($nomina) ){
				$this->redirect('profesor/capturarListaActividad');
				return;
			}
			
			$deleteID = $this -> post("deleteID");
			
			$actividadMaestro = new ActividadMaestro();
			$actividad = $actividadMaestro -> find_first("id = ".$deleteID);
			/*
			$actividadMaestroHoras = new ActividadMaestroHoras();
			foreach( $actividadMaestroHoras -> find
					("actividad_maestro_id = ".$actividad -> id) as $actividadHora){
				$actividadHora -> delete();
			}
			*/
			$actividad -> delete();
			
			$this->redirect('profesor/capturarListaActividad');
		} // function borrandoActividad()
		
		function horasClaseMayorACero(){
			if( $this -> infoMaestro -> nombramiento_horas == 0 ){
				switch($this -> infoMaestro -> nombramiento){
					case 1: $this -> infoMaestro -> nombramiento_horas = 37.5; break;
					case 2: $this -> infoMaestro -> nombramiento_horas = 28; break;
					case 3: $this -> infoMaestro -> nombramiento_horas = 19; break;
				}
			}
			if( $this -> horasClase >= $this -> infoMaestro -> nombramiento_horas * 0.6 
					|| $this -> horasClase >= $this -> infoMaestro -> nombramiento_horas * 0.55 ){
				// Tiene entre el 55% y 60% de clases de su nombramiento, por lo que no es necesario agregar
				//ninguna actividad complementaria a su 60%.
				$this -> actividadComplementaria = 0;
				$this -> segundaParteDeHorario();
			}
			else{
				// Podrá escoger actividades complementarias a su horario.
				$this -> actividadComplementaria = 1;
				if (preg_match("/.5/i", (($this -> infoMaestro -> nombramiento_horas)*.6))){
					$horasClaseSesentaPorciento = (($this -> infoMaestro -> nombramiento_horas)*.6);
					$this -> horasParaActComplementarias = $horasClaseSesentaPorciento - $this -> horasClase;
				}
				else{
					//$this -> horasParaActComplementarias
					$horasClaseSesentaPorciento = (int)(($this -> infoMaestro -> nombramiento_horas)*.6);
					$this -> horasParaActComplementarias = $horasClaseSesentaPorciento - $this -> horasClase;
				}
			}
		} // function horasClaseMayorACero()
		
		function segundaParteDeHorario(){
			// En esta sección se terminara de crear el horario del profesor. Su otro 40%
			// Por default deberán cargarsele 4 horas de tutoría, o las que se puedan.
			
			// Variables que me sirven para hacer operaciones con la segunda parte.
			//$this -> listaActividades
			//$this -> actividadMaestro
			//$this -> horasClase
			//$this -> infoMaestro
			
			unset($this -> horasClaseActComp);
			unset($this -> horasRestantesParaLaSegundaParte);
			unset($this -> horasTutorias);
			unset($this -> sinHorasTutoriasNiPreparacionClase);
			unset($this -> maximoHorasPreparacion);
			unset($this -> horasClaseActCompTutorias);
			unset($this -> horasParaCursosAparte);
			unset($this -> horasParaPreparacionClase);
			
			$this -> horasClaseActComp = $this -> horasClase + $this -> actividadMaestro -> horas;
			
			$this -> horasRestantesParaLaSegundaParte = ($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActComp);
			// Dependiendo del numero de horas restantes, será el número de horas
			//que se le asignará a tutorías y a preparación de clase.
			if( ($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActComp) >= 4 )
				$this -> horasTutorias = 4;
			else{
				$this -> horasTutorias = ($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActComp);
				if( $this -> horasTutorias == 0 ){
					$this -> sinHorasTutoriasNiPreparacionClase = 1;
				}
			}
			
			// Si ni asi completa su total de horas que tiene en 
			//$this -> infoMaestro -> nombramiento_horas, se le asignará
			//máximo el 50% que tiene en horas clase a preparación de clase.
			if( !isset($this -> sinHorasTutoriasNiPreparacionClase) &&
					(($this -> horasClaseActComp + $this -> horasTutorias) < $this -> infoMaestro -> nombramiento_horas) ){
				// Saco el maximo de horas para preparación de clases.
				if (preg_match("/.5/i", ($this -> horasClase / 2.5))){
					// Se encontró una media hora, por lo que se puede asignar.
					$this -> maximoHorasPreparacion = ($this -> horasClase / 2.5);
				}else{
					// Solo existen horas completas o medias horas, por lo que si se encuentra
					//un cuarto de horas, o algo diferente a media hora, se hará un truncamiento de horas.
					$this -> maximoHorasPreparacion = (int)($this -> horasClase / 2.5);
				}
				// Suma de HorasClase con Actividades Complementaras más Horas de Tutorías.
				$this -> horasClaseActCompTutorias = ($this -> horasClaseActComp+$this -> horasTutorias);
				
				//echo "(this -> infoMaestro -> nombramiento_horas - this -> horasClaseActCompTutorias): ".
					//($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActCompTutorias)."<br />";
				 //echo "maximoHorasPreparacion: ".$this -> maximoHorasPreparacion."<br />";
				// Se obtendrán las horas complementarías que máximo puede obtener el profesor.
				if( ($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActCompTutorias) >= $this -> maximoHorasPreparacion ){
					// Si las horas máximas que puede tener como preparación aún son menor a la resta de actividades totales
					//que lleva el maestro y al nombramientto de horas, entonces podrá asignar aún el total del máximo de horas
					//de preparación para su clase.
					$this -> horasParaPreparacionClase = $this -> maximoHorasPreparacion;
				}else{
					// Si no, se asignará la resta de nombramiento_horas con las horas totales de actividades que lleva.
					$this -> horasParaPreparacionClase = 
						($this -> infoMaestro -> nombramiento_horas - $this -> horasClaseActCompTutorias);
				}
				$this -> horasParaCursosAparte = 
				(($this -> infoMaestro -> nombramiento_horas)-($this -> horasClaseActCompTutorias+$this -> horasParaPreparacionClase));
			}
			//die;
		} // function segundaParteDeHorario()
		
		function asignandoNuevaActividadComplementaria(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			$actividadID = $this -> post("actividadID");
			
			$objetivo = $this -> post("objetivoActSust");
			$meta = $this -> post("metaActSust");
			$evidencia = $this -> post("evidenciaActSust");
			
			$horasActComplementaria = $this -> post("horasActComplementaria");
			
			$actividadMaestro = new ActividadMaestro();
			
			$actividadMaestro -> nomina = $nomina;
			$actividadMaestro -> actividad_maestro_lista_actividades = $actividadID;
			$actividadMaestro -> horas = $horasActComplementaria;
			$actividadMaestro -> tipoActividad = 1; // 1. Actividad del 60%, 2. Actividad del 40%
			$actividadMaestro -> objetivo = $objetivo;
			$actividadMaestro -> meta = $meta;
			$actividadMaestro -> evidencia = $evidencia;
			// cambiar por this -> actual
			$actividadMaestro -> periodo = $periodo;
			//$actividadMaestro -> periodo = $this -> actual;
			//$actividadMaestro -> horasreales = '0';
			//$actividadMaestro -> semana = '0';
			$actividadMaestro -> avance1 = '0';
			$actividadMaestro -> avance2 = '0';
			$actividadMaestro -> avance3 = '0';
			$actividadMaestro -> create();
			
			$this->redirect('profesor/capturarListaActividad');
		} // function asignandoNuevaActividadComplementaria()
		
		function terminarHorario(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			// Clave de Actividad complementaria y Las Horas
			$listaactividades = $this -> post("listaactividades");
			$horasParaCursosAparte2 = $this -> post("horasParaCursosAparte2");
			
			// Horas de Tutorías
			$horasTutorias = $this -> post("horasTutorias");
			// Horas para Preparación de Clase
			$horasParaPreparacionClase = $this -> post("horasParaPreparacionClase");
			
			if( $horasTutorias > 0 ){
			// Actividad De Tutorías
				$actividadMaestro = new ActividadMaestro();
				$actividadMaestro -> nomina = $nomina;
				$actividadMaestro -> actividad_maestro_lista_actividades = "S-1.3";
				$actividadMaestro -> horas = $horasTutorias;
				$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
				$actividadMaestro -> objetivo = '-';
				$actividadMaestro -> meta = '-';
				// cambiar por this -> actual
				$actividadMaestro -> periodo = $periodo;
				//$actividadMaestro -> periodo = $this -> actual;
				$actividadMaestro -> avance1 = '0';
				$actividadMaestro -> avance2 = '0';
				$actividadMaestro -> avance3 = '0';
				$actividadMaestro -> create();
			}
			if( $horasParaPreparacionClase > 0 ){
				// Preparación de Cursos
				$actividadMaestro = new ActividadMaestro();
				$actividadMaestro -> nomina = $nomina;
				$actividadMaestro -> actividad_maestro_lista_actividades = "S-1.2";
				$actividadMaestro -> horas = $horasParaPreparacionClase;
				$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
				$actividadMaestro -> objetivo = '-';
				$actividadMaestro -> meta = '-';
				// cambiar por this -> actual
				$actividadMaestro -> periodo = $periodo;
				//$actividadMaestro -> periodo = $this -> actual;
				$actividadMaestro -> avance1 = '0';
				$actividadMaestro -> avance2 = '0';
				$actividadMaestro -> avance3 = '0';
				$actividadMaestro -> create();
			}
			if( $horasParaCursosAparte2 > 0 ){
				// Guardando las actividades
				$objetivoComp1 = $this -> post("objetivoActComp1");
				$metaComp1 = $this -> post("metaActComp1");
				$evidenciaComp1 = $this -> post("evidenciaActComp1");
				$actividadMaestro = new ActividadMaestro();
				$actividadMaestro -> nomina = $nomina;
				$actividadMaestro -> actividad_maestro_lista_actividades = $listaactividades;
				$actividadMaestro -> horas = $horasParaCursosAparte2;
				$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
				$actividadMaestro -> objetivo = $objetivoComp1;
				$actividadMaestro -> meta = $metaComp1;
				$actividadMaestro -> evidencia = $evidenciaComp1;
				// cambiar por this -> actual
				$actividadMaestro -> periodo = $periodo;
				//$actividadMaestro -> periodo = $this -> actual;
				$actividadMaestro -> avance1 = '0';
				$actividadMaestro -> avance2 = '0';
				$actividadMaestro -> avance3 = '0';
				$actividadMaestro -> create();
				
				
				$listaactividades2 = $this -> post("listaactividades2");
				$horasParaCursosAparte3 = $this -> post("horasParaCursosAparte3");
				// Guardando las actividades, la segunda
				if( $listaactividades2 != -1 ){
					$objetivoComp2 = $this -> post("objetivoActComp2");
					$metaComp2 = $this -> post("metaActComp2");
					$evidenciaComp2 = $this -> post("evidenciaActComp2");
					$actividadMaestro = new ActividadMaestro();
					$actividadMaestro -> nomina = $nomina;
					$actividadMaestro -> actividad_maestro_lista_actividades = $listaactividades2;
					$actividadMaestro -> horas = $horasParaCursosAparte3;
					$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
					$actividadMaestro -> objetivo = $objetivoComp2;
					$actividadMaestro -> meta = $metaComp2;
					$actividadMaestro -> evidencia = $evidenciaComp2;
					// cambiar por this -> actual
					$actividadMaestro -> periodo = $periodo;
					//$actividadMaestro -> periodo = $this -> actual;
					$actividadMaestro -> avance1 = '0';
					$actividadMaestro -> avance2 = '0';
					$actividadMaestro -> avance3 = '0';
					$actividadMaestro -> create();
				}
			}
			
			$maestros_horarioterminado = new maestrosHorarioterminado();
			$maestros_horarioterminado -> nomina = $nomina;
			$maestros_horarioterminado -> periodo = $periodo;
			$maestros_horarioterminado -> create();
			
			$this->redirect('profesor/capturarListaActividad');
		} // function terminarHorario()
		
		function terminarHorarioAsignatura(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
			}
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			//$nomina = 1975;
			$nomina = Session::get_data('registro');
			if( !isset($nomina) ){
				$this->redirect('/');
			}
			
			// Horas de Tutorías
			$horasTutorias = $this -> post("horasTutorias");
			if( $horasTutorias > 0 ){
			// Actividad De Tutorías
				$actividadMaestro = new ActividadMaestro();
				$actividadMaestro -> nomina = $nomina;
				$actividadMaestro -> actividad_maestro_lista_actividades = "S-1.3";
				$actividadMaestro -> horas = $horasTutorias;
				$actividadMaestro -> tipoActividad = 2; // 1. Actividad del 60%, 2. Actividad del 40%
				$actividadMaestro -> objetivo = '-';
				$actividadMaestro -> meta = '-';
				// cambiar por this -> actual
				$actividadMaestro -> periodo = $periodo;
				//$actividadMaestro -> periodo = $this -> actual;
				$actividadMaestro -> avance1 = '0';
				$actividadMaestro -> avance2 = '0';
				$actividadMaestro -> avance3 = '0';
				$actividadMaestro -> create();
			}
			
			$maestros_horarioterminado = new maestrosHorarioterminado();
			$maestros_horarioterminado -> nomina = $nomina;
			$maestros_horarioterminado -> periodo = $periodo;
			$maestros_horarioterminado -> create();
			
			$this->redirect('profesor/capturarListaActividad');
		} // function terminarHorarioAsignatura()
		
		function cambiarSalonesAPrimero(){
			
			unset( $this -> primero );
			unset( $this -> salones );
			unset( $this -> plantel );
			
			$cursosprimero	= new Cursosprimero();
			$xalumnocursos	= new Xalumnocursos();
			$alumnos		= new Alumnos();
			
			$i=0;
			foreach( $alumnos -> find_all_by_sql("
					select al.miReg, al.vcNomAlu, cp.salon, cp.plantel
					From alumnos al, xalumnocursos xal, cursosprimero cp
					where al.miPerIng = ".$this -> actual."
					and al.miReg = xal.registro
					and xal.curso = cp.clavecurso
					and cp.plantel = 'C'
					group by al.miReg") as $al ){
				$this -> alumnos[$i] = $al;
				$this -> plantel = $al -> plantel;
				$i++;
			}
			$i=0;
			foreach( $cursosprimero -> find_all_by_sql("
					select salon, plantel from cursosprimero
					where periodo = ".$this -> actual."
					and plantel = 'C'
					group by salon")
					as $primero ){
				$this -> primero[$i] = $primero;
				$i++;
			}
			
		} // function cambiarSalonesAPrimero()
		
		function cambiandoSalonesPrimero(){
			
			unset( $this -> registro );
			unset( $this -> salon );
			unset( $this -> exito );
			unset( $this -> plantel );
			
			$registro = $this -> post( "registro" );
			$salon = $this -> post( "salones" );
			
			$cursosprimero	= new Cursosprimero();
			$xalumnocursos	= new Xalumnocursos();
			
			$i=0;
			$plantel = "";
			
			$primero = $cursosprimero -> find_first("salon = '".$salon."'");
			
			if( $primero -> plantel == "T" ){
				$plantel = "t";
				$this -> exito = 2;
			}
			else{
				foreach( $xalumnocursos -> find_all_by_sql("
						select * from x".$plantel."alumnocursos
						where registro = ".$registro."
						and periodo = ".$this -> actual) as $xal ){
					if( $plantel == "t" )
						$xccursos = new Xtcursos();
					else
						$xccursos = new Xccursos();
					$xcc = $xccursos -> find_first("clavecurso = '".$xal -> curso."'");
					$xal -> delete();
					$xcc -> disponibilidad += 1;
					$xcc -> save();
				}
				foreach( $cursosprimero -> find("
						periodo = ".$this -> actual."
							and salon = '".$salon."'")
						as $primero ){
					$xall = new Xalumnocursos();
					$xall -> registro = $registro;
					$xall -> periodo = $this -> actual;
					
					$xall -> curso = $primero -> clavecurso;
					$xall -> faltas1 = '0';
					$xall -> faltas2 = '0';
					$xall -> faltas3 = '0';
					$xall -> calificacion1 = 300;
					$xall -> calificacion2 = 300;
					$xall -> calificacion3 = 300;
					$xall -> faltas = '0';
					$xall -> calificacion = 300;
					$xall -> situacion = "-";
					
					$xall -> create();
					
					if( $primero -> plantel == "T" )
						$xccursos = new Xtcursos();
					else
						$xccursos = new Xccursos();
					$xcc = $xccursos -> find_first("clavecurso = '".$primero -> clavecurso."'");
					$xcc -> disponibilidad -= 1;
					if( $xcc -> disponibilidad == null )
						$xcc -> disponibilidad = '0';
					$xcc -> save();
					
					$this -> exito = 1;
					$this -> registro = $registro;
					$this -> salon = $primero -> salon;
				}
			}
		} // function cambiandoSalonesPrimero()
		
		function cambiar_turno_alumno(){
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('/');
			}
			unset($this->alumnos);
			unset($this->turnos_disponibles);
			
			$carrera_id = 7;
			$Alumnos = new Alumnos();
			$this -> alumnos = $Alumnos -> get_student_by_carrera_id($carrera_id);
			
			$this -> turnos_disponibles = $Alumnos -> get_turnos_disponibles();
			
		} // function cambiar_turno_alumno()
		
		function cambiando_turno_alumno(){
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('/');
			}
			unset($this->status_exito);
			
			$registro = $this -> post("registro");
			$nuevo_turno = $this -> post("nuevo_turno");
			
			$exito = false;
			$Alumnos = new Alumnos();
			$exito = $Alumnos -> update_turno($registro, $nuevo_turno);
			
			$temp_turno = $Alumnos -> get_nombre_turno($nuevo_turno);
			
			if( $exito )
				$this -> status_exito = "El turno del alumno ".$registro." ahora es ".$temp_turno;
			else
				$this -> status_exito = "Ocurri&oacute; un error";
			
		} // function cambiando_turno_alumno()
    }
?>