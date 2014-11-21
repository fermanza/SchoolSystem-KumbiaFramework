<?php
			
	class AspirantesController extends ApplicationController {

		function index(){

		}
		
		function reporte($nivel = "" , $carrera=200, $x=0){
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$maspirantes = new Aspirantes();
			
			
			if($nivel=="tgo"){
				$this -> aspirantes = $maspirantes -> find("nivel='T' AND carrera=".$carrera." ORDER BY nivel DESC,carrera LIMIT $x,25");
				$this -> total = $maspirantes -> count("nivel='T' AND carrera=".$carrera."")/25;
			}
			else{
				$this -> aspirantes = $maspirantes -> find("nivel='I' AND carrera=".$carrera." ORDER BY nivel DESC,carrera LIMIT $x,25");
				$this -> total = $maspirantes -> count("nivel='I' AND carrera=".$carrera."")/25;
			}
			
			if($nivel=="" || $nivel=="-"){
				$this -> aspirantes = $maspirantes -> find("1 ORDER BY nivel DESC,carrera LIMIT $x,25");
				$this -> total = $maspirantes -> count()/25;
			}
			
			$this -> actual = $x; 
			$this -> carrerita = $carrera;
			
			if($nivel == "tgo"){
				switch($carrera){
					case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN"; break;
					case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN"; break;
					case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN"; break;
					case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES"; break;
					case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA"; break;
					case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA"; break;
					case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ"; break;
					case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS"; break;
				}
			}
			else{
				switch($carrera){
					case 400: $carrera = "INGENIERÍA INDUSTRIAL"; break	;
					case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA Y COMPUTACIÓN"; break;
					case 801: $carrera = "INGENIERÍA EN MECATRÓNICA"; break;
				}
			}
			
			if($nivel=="" || $nivel=="-"){
				$carrera = "TODOS LAS CARRERAS Y NIVELES";
				$nivel = "-";
				
			}
			
			$this -> carrera = $carrera;
			$this -> nivel = $nivel;
			
					$this -> carreras["T"][200] = "TGO. EN INFORMÁTICA Y COMPUTACIÓN";
					$this -> carreras["T"][400] = "TGO. EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
					$this -> carreras["T"][500] = "TGO. EN CONTRUCCIÓN";
					$this -> carreras["T"][600] = "TGO. EN ELECTRÓNICA Y COMUNICACIONES";
					$this -> carreras["T"][700] = "TGO. EN ELECTROTECNIA";
					$this -> carreras["T"][800] = "TGO. EN MÁQUINAS HERRAMIENTA";
					$this -> carreras["T"][801] = "TGO. EN MECÁNICA AUTOMOTRIZ";
					$this -> carreras["T"][804] = "TGO. EN MANUFACTURA DE PLÁSTICOS";
					$this -> carreras["I"][400] = "ING. INDUSTRIAL";
					$this -> carreras["I"][600] = "ING. EN ELECTRÓNICA Y COMPUTACIÓN";
					$this -> carreras["I"][801] = "ING. EN MECATRÓNICA";
			
		}
		
		function admitidos($nivel = "" , $carrera=200, $x=0){
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$maspirantes = new Aspirantes();
			
			
			if($nivel=="tgo"){
				$this -> aspirantes = $maspirantes -> find("admitido=1 AND nivel='T' AND carrera=".$carrera." ORDER BY fecha_at LIMIT $x,25");
				$this -> total = $maspirantes -> count("admitido=1 AND nivel='T' AND carrera=".$carrera."")/25;
			}
			else{
				$this -> aspirantes = $maspirantes -> find("admitido=1 AND nivel='I' AND carrera=".$carrera." ORDER BY fecha_at LIMIT $x,25");
				$this -> total = $maspirantes -> count("admitido=1 AND nivel='I' AND carrera=".$carrera."")/25;
			}
			
			if($nivel=="" || $nivel=="-"){
				$this -> aspirantes = $maspirantes -> find("admitido=1 ORDER BY fecha_at LIMIT $x,25");
				$this -> total = $maspirantes -> count("admitido=1")/25;
			}
			
			$this -> actual = $x; 
			$this -> carrerita = $carrera;
			
			if($nivel == "tgo"){
				switch($carrera){
					case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN"; break;
					case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN"; break;
					case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN"; break;
					case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES"; break;
					case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA"; break;
					case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA"; break;
					case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ"; break;
					case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS"; break;
				}
			}
			else{
				switch($carrera){
					case 400: $carrera = "INGENIERÍA INDUSTRIAL"; break	;
					case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA Y COMPUTACIÓN"; break;
					case 801: $carrera = "INGENIERÍA EN MECATRÓNICA"; break;
				}
			}
			
			if($nivel=="" || $nivel=="-"){
				$carrera = "TODOS LAS CARRERAS Y NIVELES";
				$nivel = "-";
				
			}
			
			$this -> carrera = $carrera;
			$this -> nivel = $nivel;
			
		}
		
		function estadisticas(){
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$maspirantes = new Aspirantes();
			$this -> todos = $maspirantes -> count("periodo=".$configuracion -> periodo);
			$this -> tecnologos = $maspirantes -> count("nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> ingenieria = $maspirantes -> count("nivel='I' AND periodo=".$configuracion -> periodo);
			
			$this -> informatica = $maspirantes -> count("carrera=200 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> control = $maspirantes -> count("carrera=400 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> construccion = $maspirantes -> count("carrera=500 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> electronica = $maspirantes -> count("carrera=600 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> electrotecnia = $maspirantes -> count("carrera=700 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> maquinas = $maspirantes -> count("carrera=800 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> mecanica = $maspirantes -> count("carrera=801 AND nivel='T' AND periodo=".$configuracion -> periodo);
			$this -> manufactura = $maspirantes -> count("carrera=804 AND nivel='T' AND periodo=".$configuracion -> periodo);
			
			$this -> industrial = $maspirantes -> count("carrera=400 AND nivel='I' AND periodo=".$configuracion -> periodo);
			$this -> electronica2 = $maspirantes -> count("carrera=600 AND nivel='I' AND periodo=".$configuracion -> periodo);
			$this -> mecatronica = $maspirantes -> count("carrera=801 AND nivel='I' AND periodo=".$configuracion -> periodo);
		}
		
		function registrar_ceneval(){
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$mexanis = new AspirantesExani();
			
			$mexanis -> nivel = $this -> post("nivel");
			$mexanis -> plantel = $this -> post("plantel");
			$mexanis -> fecha = $this -> post("fecha");
			$mexanis -> lugar = $this -> post("lugar");
			$mexanis -> alumnos = $this -> post("alumnos");
			$mexanis -> periodo = $configuracion -> periodo;
			$mexanis -> tipo = 'CENEVAL';
			
			$mexanis -> save();
			
			$this -> redirect("aspirantes/config_ceneval");
		}
		
		function config_ceneval(){
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
			
			$this -> admision = $configuracion -> admision;
		
			$mexanis = new AspirantesExani();
			
			$h = date("H",time()+3600*1);
			$d = date("d",time()+3600*1);
			$m = date("m",time()+3600*1);
			$y = date("Y",time()+3600*1);
				
			$this -> rojas = $mexanis -> find("plantel='C' AND nivel='T' AND tipo='CENEVAL' AND (alumnos=0 OR fecha < '".$y."-".$m."-".$d." ".$h.":00:00') ORDER BY fecha");
			
			$this -> verde = $mexanis -> find_first("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='T' AND plantel='C' ORDER BY fecha");
			
			if(!$this -> verde -> id) $this -> verde -> id = 0;
			$this -> azules = $mexanis -> find("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='T' AND plantel='C' AND id!=".$this -> verde -> id." ORDER BY fecha");
			
			$this -> rojas2 = $mexanis -> find("plantel='C' AND nivel='I' AND tipo='CENEVAL' AND (alumnos=0 OR fecha < '".$y."-".$m."-".$d." ".$h.":00:00') ORDER BY fecha");
			
			$this -> verde2 = $mexanis -> find_first("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='I' AND plantel='C' ORDER BY fecha");
			if(!$this -> verde2 -> id) $this -> verde2 -> id = 0;
			$this -> azules2 = $mexanis -> find("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='I' AND plantel='C' AND id!=".$this -> verde2 -> id." ORDER BY fecha");
		}
		
		function registrar_exani(){
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$mexanis = new AspirantesExani();
			
			$mexanis -> nivel = $this -> post("nivel");
			$mexanis -> plantel = $this -> post("plantel");
			$mexanis -> fecha = $this -> post("fecha");
			$mexanis -> lugar = $this -> post("lugar");
			$mexanis -> alumnos = $this -> post("alumnos");
			$mexanis -> periodo = $configuracion -> periodo;
			$mexanis -> tipo = 'EXANI';
			
			$mexanis -> save();
			
			$this -> redirect("aspirantes/config_exani");
		}
		
		function config_exani(){
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
		
			$mexanis = new AspirantesExani();
			
			$h = date("H",time()+3600*1);
			$d = date("d",time()+3600*1);
			$m = date("m",time()+3600*1);
			$y = date("Y",time()+3600*1);
				
			$this -> rojas = $mexanis -> find("plantel='C' AND nivel='T' AND tipo='EXANI' AND (alumnos=0 OR fecha < '".$y."-".$m."-".$d." ".$h.":00:00') ORDER BY fecha");
			
			$this -> verde = $mexanis -> find_first("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='EXANI' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='T' AND plantel='C' ORDER BY fecha");
			
			if(!$this -> verde -> id) $this -> verde -> id = 0;
			$this -> azules = $mexanis -> find("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='EXANI' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='T' AND plantel='C' AND id!=".$this -> verde -> id." ORDER BY fecha");
			
			$this -> rojas2 = $mexanis -> find("plantel='C' AND nivel='I' AND tipo='EXANI' AND (alumnos=0 OR fecha < '".$y."-".$m."-".$d." ".$h.":00:00') ORDER BY fecha");
			
			$this -> verde2 = $mexanis -> find_first("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='EXANI' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='I' AND plantel='C' ORDER BY fecha");
			if(!$this -> verde2 -> id) $this -> verde2 -> id = 0;
			$this -> azules2 = $mexanis -> find("fecha > '".$y."-".$m."-".$d." ".$h.":00:00' AND tipo='EXANI' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='I' AND plantel='C' AND id!=".$this -> verde2 -> id." ORDER BY fecha");
		}
		
		function registro($folio){
		
			if(!is_numeric($folio)){
				$this -> redirect("ventanilla/aspirantes");
			}
			
			$maspirantes = new Aspirantes();
			$n = $maspirantes -> count("folio=".$folio);
		
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
			
			$this -> periodo = $configuracion -> periodo;
			$this -> ficha = "-";
			$this -> folio = $folio;
		}
		
		function modificacion($folio){
			if(!is_numeric($folio)){
				$this -> redirect("ventanilla/aspirantes");
			}
			
			$hora = date("H",time());
			if($hora <=15){
				$nivel = "T";
			}
			else{
				$nivel = "I";
			}
			
			if(Session::get_data('usuario')=="ventanilla_tgo"){
				$nivel = "T";
			}
			
			if(Session::get_data('usuario')=="ventanilla_ing"){
				$nivel = "I";
			}
			
			$maspirantes = new Aspirantes();
			$n = $maspirantes -> count("folio=".$folio." AND nivel='".$nivel."'");
		
			//SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
			if($n==0){
				$this -> redirect("aspirantes/registro/".$folio);
			}
			
			$maspirantes = new Aspirantes();
			$aspirante = $maspirantes -> find_first("folio=".$folio." AND nivel='".$nivel."'");
			
			$this -> periodo = $aspirante -> periodo;
			$this -> ficha = $aspirante -> ficha;
			$this -> folio = $aspirante -> folio;
			
			$this -> nivel = $aspirante -> nivel;
			$this -> carrera = $aspirante -> carrera;
			
			$this -> paterno = $aspirante -> paterno;
			$this -> materno = $aspirante -> materno;
			$this -> nombre = $aspirante -> nombre;
			$this -> calle = $aspirante -> calle;
			$this -> exterior = $aspirante -> exterior;
			$this -> interior = $aspirante -> interior;
			$this -> colonia = $aspirante -> colonia;
			$this -> cp = $aspirante -> cp;
			$this -> municipio = $aspirante -> municipio;
			$this -> estado = $aspirante -> estado;
			$this -> telefono = $aspirante -> telefono;
			$this -> celular = $aspirante -> celular;
			$this -> correo = $aspirante -> correo;
			$this -> sexo = $aspirante -> sexo;
			$this -> estadocivil = $aspirante -> estadocivil;
			
			$this -> d = substr($aspirante -> fecha_nacimiento,8,2);
			$this -> m = substr($aspirante -> fecha_nacimiento,5,2);
			$this -> a = substr($aspirante -> fecha_nacimiento,0,4);
			
			$this -> lugar_nacimiento = $aspirante -> lugar_nacimiento;
			$this -> curp = $aspirante -> curp;
		}

		function consulta($folio,$nivel=""){
			if(!is_numeric($folio)){
				$this -> redirect("ventanilla/aspirantes");
			}
			
			$maspirantes = new Aspirantes();
			$hora = date("H",time());
			if($hora <=15){
				$nivel = "T";
			}
			else{
				$nivel = "I";
			}
			
			if(Session::get_data('usuario')=="ventanilla_tgo"){
				$nivel = "T";
			}
			
			if(Session::get_data('usuario')=="ventanilla_ing"){
				$nivel = "I";
			}
			$n = $maspirantes -> count("folio=".$folio." AND nivel='".$nivel."'");
		
			//SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
			if($n==0){
				$this -> redirect("aspirantes/registro/".$folio);
			}
			
			$maspirantes = new Aspirantes();
			$aspirante = $maspirantes -> find_first("folio=".$folio." AND nivel='".$nivel."'");
			
			$this -> periodo = $aspirante -> periodo;
			$this -> ficha = $aspirante -> ficha;
			$this -> folio = $aspirante -> folio;
			$this -> carrera = $aspirante -> carrera;
			$this -> nivel = $aspirante -> nivel;
			
			$this -> paterno = $aspirante -> paterno;
			$this -> materno = $aspirante -> materno;
			$this -> nombre = $aspirante -> nombre;
			$this -> calle = $aspirante -> calle;
			$this -> exterior = $aspirante -> exterior;
			$this -> interior = $aspirante -> interior;
			$this -> colonia = $aspirante -> colonia;
			$this -> cp = $aspirante -> cp;
			$this -> municipio = $aspirante -> municipio;
			$this -> estado = $aspirante -> estado;
			$this -> telefono = $aspirante -> telefono;
			$this -> celular = $aspirante -> celular;
			$this -> correo = $aspirante -> correo;
			$this -> sexo = $aspirante -> sexo;
			$this -> estadocivil = $aspirante -> estadocivil;
			
			$this -> d = substr($aspirante -> fecha_nacimiento,8,2);
			$this -> m = substr($aspirante -> fecha_nacimiento,5,2);
			$this -> a = substr($aspirante -> fecha_nacimiento,0,4);
			
			$this -> lugar_nacimiento = $aspirante -> lugar_nacimiento;
			$this -> curp = $aspirante -> curp;
		}

		function registrar($folio){
			
			$configuracion = new Configuracion();
			$configuracion = $configuracion -> find_first("id=1");
			
			$maspirantes = new Aspirantes();
			$maspirantes -> fecha_nacimiento = $this -> post("a")."-".$this -> post("m")."-".$this -> post("d");
			
			//CHECAMOS SI EL FOLIO HABIA SIDO REGISTRADO ANTERIORMENTE
			$n = $maspirantes -> count("folio=".$folio." AND nivel='".substr($this -> post("carrera"),0,1)."'");
		
			//SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
			if($n>0){
				$maspirantes = $maspirantes -> find_first("folio=".$folio." AND nivel='".substr($this -> post("carrera"),0,1)."'");
				
				$maspirantes -> nivel = substr($this -> post("carrera"),0,1);
				$maspirantes -> carrera = substr($this -> post("carrera"),2);
				
				$maspirantes -> paterno = strtoupper($this -> post("paterno"));
				$maspirantes -> materno = strtoupper($this -> post("materno"));
				$maspirantes -> nombre = strtoupper($this -> post("nombre"));
				
				$maspirantes -> calle = strtoupper($this -> post("calle"));
				$maspirantes -> exterior = strtoupper($this -> post("exterior"));
				
				$maspirantes -> interior = $this -> post("interior");
				
				$maspirantes -> cp = $this -> post("cp");
				$maspirantes -> municipio = strtoupper($this -> post("municipio"));
				$maspirantes -> estado = strtoupper($this -> post("estado"));
				$maspirantes -> telefono = $this -> post("telefono");
				
				$maspirantes -> colonia = strtoupper($this -> post("colonia"));
				
				$maspirantes -> celular = $this -> post("celular");
				
				$maspirantes -> correo = strtolower($this -> post("correo"));
				
				$maspirantes -> estadocivil = strtoupper($this -> post("estadocivil"));
				$maspirantes -> sexo = $this -> post("sexo");
				$maspirantes -> fecha_nacimiento = $this -> post("a")."-".$this -> post("m")."-".$this -> post("d");
				$maspirantes -> lugar_nacimiento = strtoupper($this -> post("nacimiento"));
				$maspirantes -> curp = strtoupper($this -> post("curp"));
				
				if(!$this -> post("calle")){
					$maspirantes -> calle = "-";
				}
				
				if(!$this -> post("exterior")){
					$maspirantes -> exterior = "-";
				}
				
				if(!$this -> post("interior")){
					$maspirantes -> interior = "-";
				}
				
				if(!$this -> post("colonia")){
					$maspirantes -> colonia = "-";
				}
				
				if(!$this -> post("cp")){
					$maspirantes -> cp = "-";
				}
				
				if(!$this -> post("municipio")){
					$maspirantes -> municipio = "-";
				}
				
				if(!$this -> post("estado")){
					$maspirantes -> estado = "-";
				}
				
				if(!$this -> post("telefono")){
					$maspirantes -> telefono = "-";
				}
				
				if(!$this -> post("celular")){
					$maspirantes -> celular = "-";
				}
				
				if(!$this -> post("estadocivil")){
					$maspirantes -> estadocivil = "-";
				}
				
				if(!$this -> post("sexo")){
					$maspirantes -> sexo = "-";
				}
				
				if(!$this -> post("nacimiento")){
					$maspirantes -> lugar_nacimiento = "-";
				}
				
				if(!$this -> post("curp")){
					$maspirantes -> curp = "-";
				}
				
				if(!$this -> post("correo")){
					$maspirantes -> correo = "-";
				}
				
				$maspirantes -> admitido = 0;
				
				
				$maspirantes -> save();
			}
			else{
			
				$maspirantes = new Aspirantes();
				$maspirantes -> fecha_nacimiento = $this -> post("a")."-".$this -> post("m")."-".$this -> post("d");
			
				$maspirantes -> nivel = substr($this -> post("carrera"),0,1);
				$maspirantes -> carrera = substr($this -> post("carrera"),2);
				
				if($maspirantes -> nivel == 'T')
					$maspirantes -> ficha = $configuracion -> tgo_ficha;			//SACAR FICHA DE LA CONFIGURACION (CONSECUTIVO)
				else
					$maspirantes -> ficha = $configuracion -> ing_ficha;			//SACAR FICHA DE LA CONFIGURACION (CONSECUTIVO)
				$maspirantes -> folio = $folio;
				
				//SELECT *  FROM `aspirantes_exani` WHERE `fecha` > '2008-09-10 08:00:00'
				
				$exanis = new AspirantesExani();
				
				$d = date("d",time()+60*60*24);
				$m = date("m",time()+60*60*24);
				$y = date("Y",time()+60*60*24);
				
				$exani = $exanis -> find_first("fecha > '".$y."-".$m."-".$d." 08:00:00' AND tipo='EXANI' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='".$maspirantes -> nivel."' AND plantel='C'");
				$maspirantes -> exani_id = $exani -> id;
				$exani -> alumnos --;
				$exani -> save();
				
				$ceneval = $exanis -> find_first("fecha > '".$y."-".$m."-".$d." 08:00:00' AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo." AND alumnos>0 AND nivel='".$maspirantes -> nivel."' AND plantel='C'");
				$maspirantes -> ceneval_id = $ceneval -> id;
				$ceneval -> alumnos --;
				$ceneval -> save();
				
				$maspirantes -> periodo = $configuracion -> periodo;	//SACAR PERIODO DE LA CONFIGURACION 
				
				$maspirantes -> paterno = strtoupper($this -> post("paterno"));
				$maspirantes -> materno = strtoupper($this -> post("materno"));
				$maspirantes -> nombre = strtoupper($this -> post("nombre"));
				
				$maspirantes -> calle = strtoupper($this -> post("calle"));
				$maspirantes -> exterior = $this -> post("exterior");
				
				
				$maspirantes -> interior = $this -> post("interior");
				
				if(!$this -> post("interior")){
					$maspirantes -> interior = "-";
				}
				
				$maspirantes -> colonia = strtoupper($this -> post("colonia"));
				
				if(!$this -> post("colonia")){
					$maspirantes -> colonia = "-";
				}
				
				$maspirantes -> cp = $this -> post("cp");
				$maspirantes -> municipio = strtoupper($this -> post("municipio"));
				$maspirantes -> estado = strtoupper($this -> post("estado"));
				$maspirantes -> telefono = $this -> post("telefono");
				
				$maspirantes -> celular = $this -> post("celular");
				
				if(!$this -> post("celular")){
					$maspirantes -> celular = "-";
				}
				
				$maspirantes -> correo = strtolower($this -> post("correo"));
				
				$maspirantes -> estadocivil = strtoupper($this -> post("estadocivil"));
				$maspirantes -> sexo = $this -> post("sexo");
				$maspirantes -> fecha_nacimiento = $this -> post("a")."-".$this -> post("m")."-".$this -> post("d");
				$maspirantes -> lugar_nacimiento = strtoupper($this -> post("nacimiento"));
				$maspirantes -> curp = strtoupper($this -> post("curp"));
				
				if(!$this -> post("calle")){
					$maspirantes -> calle = "-";
				}
				
				if(!$this -> post("exterior")){
					$maspirantes -> exterior = "-";
				}
				
				if(!$this -> post("interior")){
					$maspirantes -> interior = "-";
				}
				
				if(!$this -> post("colonia")){
					$maspirantes -> colonia = "-";
				}
				
				if(!$this -> post("cp")){
					$maspirantes -> cp = "-";
				}
				
				if(!$this -> post("municipio")){
					$maspirantes -> municipio = "-";
				}
				
				if(!$this -> post("estado")){
					$maspirantes -> estado = "-";
				}
				
				if(!$this -> post("telefono")){
					$maspirantes -> telefono = "-";
				}
				
				if(!$this -> post("celular")){
					$maspirantes -> celular = "-";
				}
				
				if(!$this -> post("estadocivil")){
					$maspirantes -> estadocivil = "-";
				}
				
				if(!$this -> post("sexo")){
					$maspirantes -> sexo = "-";
				}
				
				if(!$this -> post("nacimiento")){
					$maspirantes -> lugar_nacimiento = "-";
				}
				
				if(!$this -> post("correo")){
					$maspirantes -> correo = "-";
				}
				
				if(!$this -> post("curp")){
					$maspirantes -> curp = "-";
				}
				
				$maspirantes -> admitido = 0;
				$maspirantes -> save();
				
				echo "HOLA";
				
				if($maspirantes -> nivel == 'T')
					$configuracion -> tgo_ficha++;
				else
					$configuracion -> ing_ficha++;
				
				$configuracion -> save();
			}
			
			//$this -> redirect("aspirantes/consulta/".$folio);
		}
	
		function ficha($folio){
			
			$maspirantes = new Aspirantes();
			$mexanis = new AspirantesExani();
			
			$hora = date("H",time());
			if($hora <=15){
				$nivel = "T";
			}
			else{
				$nivel = "I";
			}
			
			if(Session::get_data('usuario')=="ventanilla_tgo"){
				$nivel = "T";
			}
			
			if(Session::get_data('usuario')=="ventanilla_ing"){
				$nivel = "I";
			}
			
			$aspirante = $maspirantes -> find_first("folio=".$folio." AND nivel='".$nivel."'");
			
			$ficha = $aspirante -> ficha;
			
			$exani = $mexanis -> find_first("id=".$aspirante -> exani_id);
			$ceneval = $mexanis -> find_first("id=".$aspirante -> ceneval_id);
			
			$this -> set_response("view");
			
			$reporte = new FPDF();
			
			$reporte -> Open();
			$reporte -> AddPage();
			
			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Image('http://ase.ceti.mx/public/img/logoceti.jpg', 5, 8);
			
			$reporte -> Image('http://ase.ceti.mx/public/img/fotografia.jpg', 180, 15);
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);
			
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"Organismo Público Descentralizado Federal",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',10);
			$reporte -> MultiCell(0,2,"Departamento de Servicios de Apoyo Académico",0,'C',0);
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',10);
			$reporte -> MultiCell(0,2,"FICHA DE ASPIRANTE PARA ADMISIÓN",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);
			
			$reporte -> Cell(20,4,"FICHA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL ASPIRANTE",1,0,'C',1);
			$reporte -> Cell(25,4,"NIVEL",1,0,'C',1);
			$reporte -> Cell(60,4,"CARRERA",1,0,'C',1);
			$reporte -> Cell(25,4,"PERIODO",1,0,'C',1);
			
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$ficha,1,0,'C',1);
			$reporte -> Cell(60,4,$aspirante -> paterno." ".$aspirante -> materno." ".$aspirante -> nombre,1,0,'C',1);
			
			if($aspirante -> nivel == "T"){
				$nivel = "TECNÓLOGO";
				switch($aspirante -> carrera){
					case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN"; break;
					case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN"; break;
					case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN"; break;
					case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES"; break;
					case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA"; break;
					case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA"; break;
					case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ"; break;
					case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS"; break;
				}
			}
			else{
				$nivel = "INGENIERÍA";
				switch($aspirante -> carrera){
					case 400: $carrera = "INGENIERÍA INDUSTRIAL"; break	;
					case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA Y COMPUTACIÓN"; break;
					case 801: $carrera = "INGENIERÍA EN MECATRÓNICA"; break;
				}
			}
			
			if($aspirante -> periodo[0]=='1')
				$periodo = "FEB - JUN, ";
			else
				$periodo = "AGO - DIC, ";
			
			$periodo .= substr($aspirante -> periodo,1,4);
			
			$reporte -> Cell(25,4,$nivel,1,0,'C',1);
			$reporte -> Cell(60,4,$carrera,1,0,'C',1);
			$reporte -> Cell(25,4,$periodo,1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(55,6,"ACTIVIDAD",1,0,'C',1);
			$reporte -> Cell(60,6,"FECHA",1,0,'C',1);
			$reporte -> Cell(20,6,"HORA",1,0,'C',1);
			$reporte -> Cell(55,6,"AULA",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			if($exani -> nivel == 'T')
				$reporte -> Cell(55,6,"Llenado de Hoja de Registro Exani I",1,0,'L',1);
			else
				$reporte -> Cell(55,6,"Llenado de Hoja de Registro Exani II",1,0,'L',1);
				
			$fecha = substr($exani -> fecha,0,10);
			
			$d = substr($fecha,8,2);
			$m = substr($fecha,5,2);
			$y = substr($fecha,0,4);
			
			$tiempo = mktime(0,0,0,$m,$d,$y);
			
			$fecha = date("w",$tiempo);
			
			switch($fecha){
				case 0: $fecha = "Domingo"; break;
				case 1: $fecha = "Lunes"; break;
				case 2: $fecha = "Martes"; break;
				case 3: $fecha = "Miercoles"; break;
				case 4: $fecha = "Jueves"; break;
				case 5: $fecha = "Viernes"; break;
				case 6: $fecha = "Sábado"; break;
			}
			
			$fecha = $fecha . ", " . $d . " de ";
			
			switch($m){
				case 1: $fecha .= "Enero"; break;
				case 2: $fecha .= "Fabrero"; break;
				case 3: $fecha .= "Marzo"; break;
				case 4: $fecha .= "Abril"; break;
				case 5: $fecha .= "Mayo"; break;
				case 6: $fecha .= "Junio"; break;
				case 7: $fecha .= "Julio"; break;
				case 8: $fecha .= "Agosto"; break;
				case 9: $fecha .= "Septiembre"; break;
				case 10: $fecha .= "Octubre"; break;
				case 11: $fecha .= "Noviembre"; break;
				case 12: $fecha .= "Diciembre"; break;
			}
			
			$fecha .=  " de ".$y;
				
			$reporte -> Cell(60,6,$fecha,1,0,'C',1);
			$reporte -> Cell(20,6,substr($exani -> fecha,11),1,0,'C',1);
			$reporte -> Cell(55,6,substr($exani -> lugar,0,10),1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(55,6,"Examen de Admisión CENEVAL",1,0,'L',1);
			
			$fecha = substr($ceneval -> fecha,0,10);
			
			$d = substr($fecha,8,2);
			$m = substr($fecha,5,2);
			$y = substr($fecha,0,4);
			
			$tiempo = mktime(0,0,0,$m,$d,$y);
			
			$fecha = date("w",$tiempo);
			
			switch($fecha){
				case 0: $fecha = "Domingo"; break;
				case 1: $fecha = "Lunes"; break;
				case 2: $fecha = "Martes"; break;
				case 3: $fecha = "Miercoles"; break;
				case 4: $fecha = "Jueves"; break;
				case 5: $fecha = "Viernes"; break;
				case 6: $fecha = "Sábado"; break;
			}
			
			$fecha = $fecha . ", " . $d . " de ";
			
			switch($m){
				case 1: $fecha .= "Enero"; break;
				case 2: $fecha .= "Fabrero"; break;
				case 3: $fecha .= "Marzo"; break;
				case 4: $fecha .= "Abril"; break;
				case 5: $fecha .= "Mayo"; break;
				case 6: $fecha .= "Junio"; break;
				case 7: $fecha .= "Julio"; break;
				case 8: $fecha .= "Agosto"; break;
				case 9: $fecha .= "Septiembre"; break;
				case 10: $fecha .= "Octubre"; break;
				case 11: $fecha .= "Noviembre"; break;
				case 12: $fecha .= "Diciembre"; break;
			}
			
			$fecha .=  " de ".$y;
				
			$reporte -> Cell(60,6,$fecha,1,0,'C',1);
			$reporte -> Cell(20,6,substr($ceneval -> fecha,11),1,0,'C',1);
			$reporte -> Cell(55,6,"SE INDICARÁ EL DÍA DEL EXAMEN",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> Cell(70,25,"",0,0,'C',0);
			$reporte -> Cell(60,25,"",1,0,'C',1);
			$reporte -> Cell(60,25,"",1,0,'C',1);
				
			$reporte -> SetY(110);			
				
			$reporte -> Cell(190,6,"Conserva bien tu ficha, es tu única identificación para que puedas entrar al plantel y hacer tus examenes",0,0,'C',1);
			$reporte -> Ln();
			$reporte -> Cell(190,6,"Es necesario realizar estas dos actividades para poder participar en el proceso de admisión",0,0,'C',1);
			$reporte -> Ln();
			$reporte -> Cell(190,6,"REVISIÓN 3                                    A partir del 16 de Abril de 2007                                    FR-02-DPL-CE-PO-024",0,0,'C',1);
			
			$reporte -> SetY(100);			
			
			$reporte -> SetFont('Verdana','',8);
			if($exani -> nivel == 'T')
				$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani I");
			else
				$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani II");
			$reporte -> Text(100, 102, "Firma y fecha");
			
			$reporte -> Text(148, 98, "Examen de Admisión CENEVAL");
			$reporte -> Text(160, 102, "Firma y fecha");
			
			$reporte -> Output("/var/www/htdocs/calculo/public/files/pdfs/aspirantes/".$ficha.".pdf");
			
			$this->redirect("public/files/pdfs/aspirantes/".$ficha.".pdf");
		}
		
		function fichablanco(){
			$this -> set_response("view");
			
			$reporte = new FPDF();
			
			$reporte -> Open();
			$reporte -> AddPage();
			
			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Image('http://ase.ceti.mx/public/img/logoceti.jpg', 5, 8);
			
			$reporte -> Image('http://ase.ceti.mx/public/img/fotografia.jpg', 180, 15);
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',14);
			$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);
			
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',12);
			$reporte -> MultiCell(0,3,"Organismo Público Descentralizado Federal",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',10);
			$reporte -> MultiCell(0,2,"Departamento de Servicios de Apoyo Académico",0,'C',0);
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',8);
			$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> SetFont('Verdana','',10);
			$reporte -> MultiCell(0,2,"FICHA DE ASPIRANTE PARA ADMISIÓN",0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',6);
			
			$reporte -> Cell(20,4,"FICHA",1,0,'C',1);
			$reporte -> Cell(60,4,"NOMBRE DEL ASPIRANTE",1,0,'C',1);
			$reporte -> Cell(25,4,"NIVEL",1,0,'C',1);
			$reporte -> Cell(60,4,"CARRERA",1,0,'C',1);
			$reporte -> Cell(25,4,"PERIODO",1,0,'C',1);
			
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,"",1,0,'C',1);
			$reporte -> Cell(60,4,"",1,0,'C',1);
			
			if($aspirante -> nivel == "T"){
				$nivel = "TECNÓLOGO";
				switch($aspirante -> carrera){
					case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN"; break;
					case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN"; break;
					case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN"; break;
					case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES"; break;
					case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA"; break;
					case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA"; break;
					case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ"; break;
					case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS"; break;
				}
			}
			else{
				$nivel = "INGENIERÍA";
				switch($aspirante -> carrera){
					case 400: $carrera = "INGENIERÍA INDUSTRIAL"; break	;
					case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA Y COMPUTACIÓN"; break;
					case 801: $carrera = "INGENIERÍA EN MECATRÓNICA"; break;
				}
			}
			
			if($aspirante -> periodo[0]=='1')
				$periodo = "FEB - JUN, ";
			else
				$periodo = "AGO - DIC, ";
			
			$periodo .= substr($aspirante -> periodo,1,4);
			
			$reporte -> Cell(25,4,"",1,0,'C',1);
			$reporte -> Cell(60,4,"",1,0,'C',1);
			$reporte -> Cell(25,4,"",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> SetTextColor(0);
			$reporte -> SetDrawColor(0xFF,0x66,0x33);
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetFillColor(0xDD,0xDD,0xDD);
			$reporte -> Cell(55,6,"ACTIVIDAD",1,0,'C',1);
			$reporte -> Cell(60,6,"FECHA",1,0,'C',1);
			$reporte -> Cell(20,6,"HORA",1,0,'C',1);
			$reporte -> Cell(55,6,"AULA",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			if($exani -> nivel == 'T')
				$reporte -> Cell(55,6,"",1,0,'L',1);
			else
				$reporte -> Cell(55,6,"",1,0,'L',1);
				
			$fecha = substr($exani -> fecha,0,10);
			
			$d = substr($fecha,8,2);
			$m = substr($fecha,5,2);
			$y = substr($fecha,0,4);
			
			$tiempo = mktime(0,0,0,$m,$d,$y);
			
			$fecha = date("w",$tiempo);
			
			switch($fecha){
				case 0: $fecha = "Domingo"; break;
				case 1: $fecha = "Lunes"; break;
				case 2: $fecha = "Martes"; break;
				case 3: $fecha = "Miercoles"; break;
				case 4: $fecha = "Jueves"; break;
				case 5: $fecha = "Viernes"; break;
				case 6: $fecha = "Sábado"; break;
			}
			
			$fecha = $fecha . ", " . $d . " de ";
			
			switch($m){
				case 1: $fecha .= "Enero"; break;
				case 2: $fecha .= "Fabrero"; break;
				case 3: $fecha .= "Marzo"; break;
				case 4: $fecha .= "Abril"; break;
				case 5: $fecha .= "Mayo"; break;
				case 6: $fecha .= "Junio"; break;
				case 7: $fecha .= "Julio"; break;
				case 8: $fecha .= "Agosto"; break;
				case 9: $fecha .= "Septiembre"; break;
				case 10: $fecha .= "Octubre"; break;
				case 11: $fecha .= "Noviembre"; break;
				case 12: $fecha .= "Diciembre"; break;
			}
			
			$fecha .=  " de ".$y;
				
			$reporte -> Cell(60,6,"",1,0,'C',1);
			$reporte -> Cell(20,6,"",1,0,'C',1);
			$reporte -> Cell(55,6,"",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(55,6,"",1,0,'L',1);
			
			$fecha = substr($ceneval -> fecha,0,10);
			
			$d = substr($fecha,8,2);
			$m = substr($fecha,5,2);
			$y = substr($fecha,0,4);
			
			$tiempo = mktime(0,0,0,$m,$d,$y);
			
			$fecha = date("w",$tiempo);
			
			switch($fecha){
				case 0: $fecha = "Domingo"; break;
				case 1: $fecha = "Lunes"; break;
				case 2: $fecha = "Martes"; break;
				case 3: $fecha = "Miercoles"; break;
				case 4: $fecha = "Jueves"; break;
				case 5: $fecha = "Viernes"; break;
				case 6: $fecha = "Sábado"; break;
			}
			
			$fecha = $fecha . ", " . $d . " de ";
			
			switch($m){
				case 1: $fecha .= "Enero"; break;
				case 2: $fecha .= "Fabrero"; break;
				case 3: $fecha .= "Marzo"; break;
				case 4: $fecha .= "Abril"; break;
				case 5: $fecha .= "Mayo"; break;
				case 6: $fecha .= "Junio"; break;
				case 7: $fecha .= "Julio"; break;
				case 8: $fecha .= "Agosto"; break;
				case 9: $fecha .= "Septiembre"; break;
				case 10: $fecha .= "Octubre"; break;
				case 11: $fecha .= "Noviembre"; break;
				case 12: $fecha .= "Diciembre"; break;
			}
			
			$fecha .=  " de ".$y;
				
			$reporte -> Cell(60,6,"",1,0,'C',1);
			$reporte -> Cell(20,6,substr("",11),1,0,'C',1);
			$reporte -> Cell(55,6,"",1,0,'C',1);
			
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> Cell(70,25,"",0,0,'C',0);
			$reporte -> Cell(60,25,"",1,0,'C',1);
			$reporte -> Cell(60,25,"",1,0,'C',1);
				
			$reporte -> SetY(110);			
				
			$reporte -> Cell(190,6,"Conserva bien tu ficha, es tu única identificación para que puedas entrar al plantel y hacer tus examenes",0,0,'C',1);
			$reporte -> Ln();
			$reporte -> Cell(190,6,"Es necesario realizar estas dos actividades para poder participar en el proceso de admisión",0,0,'C',1);
			$reporte -> Ln();
			$reporte -> Cell(190,6,"REVISIÓN 3                                    A partir del 16 de Abril de 2007                                    FR-02-DPL-CE-PO-024",0,0,'C',1);
			
			$reporte -> SetY(100);			
			
			$reporte -> SetFont('Verdana','',8);
			if($exani -> nivel == 'T')
				$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani I");
			else
				$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani II");
			$reporte -> Text(100, 102, "Firma y fecha");
			
			$reporte -> Text(148, 98, "Examen de Admisión CENEVAL");
			$reporte -> Text(160, 102, "Firma y fecha");
			
			$reporte -> Output("/var/www/htdocs/calculo/public/files/pdfs/aspirantes/fichablanco.pdf");
			
			$this->redirect("public/files/pdfs/aspirantes/fichablanco.pdf");
		}
		
	
	}
	
?>