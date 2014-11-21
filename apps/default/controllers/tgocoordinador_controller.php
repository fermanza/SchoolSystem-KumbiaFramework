<?php
			
	class TgocoordinadorController extends ApplicationController {

		function index(){
			$this -> valida();			
		}
		
		function informacion(){
			$this -> valida();
		}
		
		function alumnos(){
			$this -> valida();
		}
		
		function profesores($p=0){
			$this -> valida();
			
			$division = Session::get_data('division');
			
			$maestros = new Maestros();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
				$this -> periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> maestros = $maestros -> find("1 ORDER BY nombre");
			foreach($this -> maestros as $profesor){
				$this -> profesores[$profesor -> id] = $profesor -> nombre;
			}
			
			$maestrodivisiones = new MaestroDivisiones();
			$this -> listado = $maestrodivisiones -> find("division_id=".$division." ORDER BY maestro_id LIMIT ".$p.",15");
			
			$n = $maestrodivisiones -> count("division_id=".$division);
			
			$this -> p1 = $p - 15; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 15; if($this -> p2 >= $n) $this -> p2 = $p;
			
			$this  -> propietario = "SELECCIONA EL PROFESOR QUE QUIERES REVISAR";
		}		
		
		function horarioJornada($nomina,$periodo){			
			$this -> set_response("view");
			$this -> nomina= $nomina;
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
				$this -> periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			$this -> horarioJornada = new HorarioJornada();
			$this -> horarioJornada -> find_first("periodo = ".$periodo." and maestro_id = ".$nomina);
		}
		
		function capturarHorarioJornada($nomina){
			$this -> set_response("view");
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
				$this -> periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			$HorarioJornada = new HorarioJornada();
			//lunes
			if($this -> post("lunes")!= ""){
				$lunes = $this -> post("lunes");
			}else{
				$lunes =" - - -";
			}
			//martes
			if($this -> post("martes")!= ""){
				$martes = $this -> post("martes");
			}else{
				$martes =" - - -";
			}
			//miercoles
			if($this -> post("miercoles")!= ""){
				$miercoles = $this -> post("miercoles");
			}else{
				$miercoles =" - - -";
			}
			//jueves			
			if($this -> post("jueves")!= ""){
				$jueves = $this -> post("jueves");
			}else{
				$jueves =" - - -";
			}
			if($this -> post("viernes")!= ""){
				$viernes = $this -> post("viernes");
			}else{
				$viernes =" - - -";
			}
			if($this -> post("sabado")!= ""){
				$sabado = $this -> post("sabado");
			}else{
				$sabado =" - - -";
			}													
			
			if ($horarioJornada = $HorarioJornada -> find_first("periodo = ".$periodo." and maestro_id = ".$nomina)){
				$horarioJornada -> maestro_id = $nomina;
				$horarioJornada -> periodo = $periodo;
				$horarioJornada -> lunes = $lunes;
				$horarioJornada -> martes = $martes;
				$horarioJornada -> miercoles = $miercoles;
				$horarioJornada -> jueves = $jueves;
				$horarioJornada -> viernes = $viernes;
				$horarioJornada -> sabado = $sabado;
				if($horarioJornada -> save()){
					Flash::success('Se ha guardado la Joranda');
				}else{
					Flash::error('Ocurrio un error');
				}	
			}else{						
				$HorarioJornada -> maestro_id = $nomina;
				$HorarioJornada -> periodo = $this -> periodo;
				$HorarioJornada -> lunes = $lunes;
				$HorarioJornada -> martes = $martes;
				$HorarioJornada -> miercoles = $miercoles;
				$HorarioJornada -> jueves = $jueves;
				$HorarioJornada -> viernes = $viernes;
				$HorarioJornada -> sabado = $sabado;
				if($HorarioJornada -> save()){
					Flash::success('Se ha guardado la Joranda');
				}else{
					Flash::error('Ocurrio un error');
				}
			}
		}
		
		function infraestructura($p=0){
			$this -> valida();
			
			$division = Session::get_data('division');
			
			$salones = new Salones();
			$this -> salones = $salones -> find("1 ORDER BY nombre");
			foreach($this -> salones as $salon){
				$this -> salones[$salon -> id] = $salon -> nombre;
			}
			
			$salones = new Salones();
			$this -> listado = $salones -> find("1 ORDER BY modulo,nombre,tipo LIMIT ".$p.",20");
			
			$n = $salones -> count();
			
			$this -> p1 = $p - 20; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 20; if($this -> p2 >= $n) $this -> p2 = $p;
			
			$this  -> propietario = "SELECCIONA EL AULA QUE QUIERES REVISAR";
			
			unset($this -> jornada);
		}
		
		function materias(){
			$this -> valida();
		}
		
		function grupos($p=0){
			$this -> valida();
			$division="";
			$division = Session::get_data('division');
			$especialidades = new Especialidades();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			$Divisiones = new Divisiones();
			if($Divisiones -> find_first("maestro_id = ".Session::get_data('registro'))){
				if($Divisiones -> id == 110 || $Divisiones -> id == 160){
					$division_id = 999;
				}else{
					$division_id = $Divisiones -> id;
				}
			}else{
				echo "no se hayo la division";
				$division_id = 999;
			}
			
			$this -> especialidades = $especialidades -> find("1 ORDER BY nombre");
			foreach($this -> especialidades as $especialidad){
				$this -> especialidad[$especialidad -> id] = $especialidad -> nombre;
			}
			
			$grupos = new Grupos();
			$this -> listado = $grupos -> find("periodo = ".$periodo." and especialidad_id = ".$division_id." ORDER BY nombre ASC LIMIT ".$p.",15");
			
			$n = $grupos -> count('periodo = '.$periodo);
			
			$this -> p1 = $p - 15; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 15; if($this -> p2 >= $n) $this -> p2 = $p;
			
			$this  -> propietario = "SELECCIONA EL GRUPO QUE QUIERES REVISAR ";
		}
		
		function horarios0000($profesor = ""){
			$this -> valida();						
			$this -> limpiarHorario();
			$this -> ajax = 0;			
			
			$division = Session::get_data('division');
			
			$maestros = new Maestros();
			$divisiones = new MaestroDivisiones();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			if($profesor==""){
				if(isset($_POST["profesor"]))
					$profesor = $_POST["profesor"];
				else
					$profesor = Session::get_data('registro');
			}
			
			$x = $maestros -> count("id=".$profesor);
			
			if($x==0){
				$profesor = Session::get_data('registro');
			}			
			
			$profes = $divisiones -> find("division_id=".Session::get_data('division'));
			
			$i=0;
			
			if($profes) foreach($profes as $profe){
				$this -> maestros[$i++] = $maestros -> find_first("id=".$profe -> maestro_id);
			}			
			
			$turno = 'M';
			
			$materias = new MateriasHorarios();
			$grupos = new Grupos();
			$salones = new Salones();
			$horarios = new Horarios();
			
			
			$maestro = $maestros -> find_first("id=".$profesor);
			$this  -> maestro = $maestro;
			
			$horarios = $horarios -> find("maestro_id=".$profesor." AND periodo=".$periodo);
			
			for($i=0;$i<200;$i++){
				$this -> horarios[$i] = "-";
			}
			
			if($horarios) foreach($horarios as $horario){
				$materia = $materias -> find_first("id=".$horario -> materia_id);
				$this -> horarios[$horario -> auxiliar] = $materia -> nombre;
			}
			
			$this -> materias = $materias -> find("1 ORDER BY clave");
			$this -> grupos = $grupos -> find('periodo = '.$periodo);
			$this -> salones = $salones -> find();
			
			$this -> turno = $turno;
			
			$this  -> propietario = $maestro -> nombre;
			
		}
		
		function cambiarProfesor(){
			$this -> valida();						
			$division = Session::get_data('division');			
						
			$divisiones = new MaestroDivisiones();
			
			$this -> md = $divisiones -> find("division_id = ".$division);
			
		}
		
		function materiasProf(){
			$this -> valida();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			$this -> set_response("view");			
			$Horarios = new Horarios();
			$divisiones = new MaestroDivisiones();
			
			$this -> maestro_id = $this -> post("maestro_id");
			$division = Session::get_data('division');
			
			$this -> md = $divisiones -> find("division_id = ".$division);						
			$this -> horarios = $Horarios -> find("maestro_id = ".$this -> maestro_id." and materia_id != 0 and periodo = ".$periodo);
			
		}
		
		function asignarCambioMateria(){
			$this -> set_response("view");
			$horario_id = $this -> post("horario_id");
			$maestroNuevo_id = $this -> post("maestroNuevo_id");
			
			$Horarios = new Horarios();
			$Horas = new Horas();
			
			if($Horarios -> find_first("id = ".$horario_id)){
				echo "Si se encontro el horario ".$horario_id."<br>";
				$Horarios -> maestro_id = $maestroNuevo_id;
				if($Horarios -> save()){
					echo "Si se pudo actualizar horarios.<br>";
					if ($Horas -> update_all("maestro_id = ".$maestroNuevo_id, "horario_id = ".$horario_id)){
						echo "SI se pudo actualizar horas.<br>";
					}else{
						echo "NO se pudo actualizar horas.<br>";
					}
				}else{
					echo "NO se pudo actualizar horarios.<br>";
				}
			}else{
				echo "NO se encontro el horario_id";
			}
		}
		
		function limpiarHorario(){
			unset($this -> horarios);
			unset($this -> maestros);
		}
		
		function calificaciones(){
			$this -> valida();
		}
		
		function apoyo(){
			$this -> valida();	
			$Ticket = new Ticket();
			$Usuarios = new Usuarios();
			$usuario = $Usuarios -> find_first("usuario = ".Session::get_data("registro"));
			$this -> ticket = $Ticket -> find("(estado like 'Abierto' or estado like 'Proceso') and dirigido_a = ".$usuario -> id, "order: estado ASC",  "limit: 8");
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}			
		
		function agregarmaestro(){
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$maestros = new Maestros();
			$maestrodivisiones = new MaestroDivisiones();
			$jornadalaboral = new JornadaLaboral();
			
			$n = $maestrodivisiones -> count("maestro_id=".$_POST["maestro"]." AND division_id=".$division);
			$m = $jornadalaboral -> count("maestro_id=".$this -> post("maestro")." AND periodo=".$periodo);
			
			if($n==0){
				$maestrodivisiones -> maestro_id = $this -> post("maestro");
				$maestrodivisiones -> division_id = $division;
			
				if($maestrodivisiones -> save()){
					Flash::success('Se agrego el profesor '.$this -> post("maestro")." a la division: ".$division);
				}
			}
			
			if($m==0){
				$jornadalaboral = new JornadaLaboral();
				$jornadalaboral -> maestro_id = $this -> post("maestro");
				$jornadalaboral -> periodo = $periodo;
				$jornadalaboral -> lunes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> martes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> miercoles = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> jueves = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> viernes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> sabado = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadalaboral -> save();
			}
			
			$this -> maestros = $maestros -> find("1 ORDER BY nombre");
			
			foreach($this -> maestros as $profesor){
				$this -> profesores[$profesor -> id] = $profesor -> nombre;
			}
						
			$this -> listado = $maestrodivisiones -> find("division_id=".$division." ORDER BY maestro_id LIMIT 0,15");
			
			$this -> render_partial("listado");
		}
		
		function jornada($id){
			$this -> set_response("view");
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
		
			$jornada = new JornadaLaboral();
			if($this -> jornada = $jornada -> find_first("maestro_id=".$id." and periodo = ".$periodo)){
			
			}else{
				$this -> jornada = new JornadaLaboral();
			}
			
			$maestro = new Maestros();
			$this -> profesor = $maestro -> find_first("id=".$id);
			$this -> propietario = $this -> profesor -> nombre;
		}
		
		function quitarmaestro($id){
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			
			$maestros = new Maestros();
			$maestrodivisiones = new MaestroDivisiones();
			
			$maestro = $maestrodivisiones -> find_first("id=".$id);
			$nominaT = $maestro -> maestro_id;
			$divisionT = $maestro -> division_id;
			if($maestro -> delete()){
				Flash::success('Se Borro el profesor '.$nominaT." a la division: ".$divisionT);
			}
			
			$this -> maestros = $maestros -> find("1 ORDER BY nombre");
			
			foreach($this -> maestros as $profesor){
				$this -> profesores[$profesor -> id] = $profesor -> nombre;
			}
			
			$this -> listado = $maestrodivisiones -> find("division_id=".$division." ORDER BY maestro_id LIMIT 0,15");
			$this -> render_partial("listado");
		}
		
		function profesoresajax($p=0){
			$this -> valida();
			
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			
			$maestros = new Maestros();
			$this -> maestros = $maestros -> find("1 ORDER BY nombre");
			foreach($this -> maestros as $profesor){
				$this -> profesores[$profesor -> id] = $profesor -> nombre;
			}
			
			$maestrodivisiones = new MaestroDivisiones();
			$this -> listado = $maestrodivisiones -> find("division_id=".$division." ORDER BY maestro_id LIMIT ".$p.",15");
			
			$n = $maestrodivisiones -> count("division_id=".$division);
			
			$this -> p1 = $p - 15; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 15; if($this -> p2 >= $n) $this -> p2 = $p;
		}
		
		function infraestructuraajax($p=0){
			$this -> valida();
			
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			
			$salones = new Salones();
			$this -> salones = $salones -> find("1 ORDER BY nombre");
			foreach($this -> salones as $salon){
				$this -> salones[$salon -> id] = $salon -> nombre;
			}
			
			$salones = new Salones();
			$this -> listado = $salones -> find("1 ORDER BY modulo,nombre,tipo LIMIT ".$p.",20");
			
			$n = $salones -> count();
			
			$this -> p1 = $p - 20; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 20; if($this -> p2 >= $n) $this -> p2 = $p;
		}
		
		function jornadasalon($id){
			$this -> set_response("view");
		
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$salon = new Salones();
			$this -> salon = $salon -> find_first("id=".$id);
			$this -> propietario = $this -> salon -> nombre;
			
			$jornada = new JornadaSalon();
			$m = $jornada -> count("salon_id=".$id." AND periodo=".$periodo);
			
			if($m==0){
				$jornadasalon = new JornadaSalon();
				$jornadasalon -> salon_id = $id;
				$jornadasalon -> periodo = $periodo;
				$jornadasalon -> lunes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> martes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> miercoles = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> jueves = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> viernes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> sabado = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadasalon -> save();
			}
			
			$this -> jornada = $jornada -> find_first("salon_id=".$id." AND periodo=".$periodo);
		}
		
		function agregarcurso(){
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$grupo = new Grupos();
			$grupo -> nombre = strtoupper($this -> post("nombre"));
			$grupo -> nivel = substr($this -> post("nombre"),0,1);
			$grupo -> especialidad_id = $this -> post("especialidad");
			
			if($grupo -> save()){
				Flash::success('Se guardo el grupo: '.$grupo -> nombre." para la Division: ".$grupo -> especialidad_id);
			}else{
				Flash::error('No se pudo guardar');
			}
			
			
			$jornadagrupo = new JornadaGrupo();
			$m = $jornadagrupo -> count("grupo='".$grupo -> nombre."' AND periodo=".$periodo);
				
			if($m==0){
				$jornadagrupo = new JornadaGrupo();
				$jornadagrupo -> grupo = $grupo -> nombre;
				$jornadagrupo -> periodo = $periodo;
				$jornadagrupo -> lunes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> martes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> miercoles = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> jueves = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> viernes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> sabado = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> save();
			}
			
			
			//$this  -> propietario = $grupo -> nombre;
			$Divisiones = new Divisiones();
			if($Divisiones -> find_first("maestro_id = ".Session::get_data('registro'))){
				if($Divisiones -> id == 110 || $Divisiones -> id == 160){
					$division_id = 999;
				}else{
					$division_id = $Divisiones -> id;
				}
			}else{
				echo "no se hayo la division";
				$division_id = 999;
			}
			
			$this -> listado = $grupo -> find("periodo = ".$periodo." and especialidad_id = ".$division_id." ORDER BY id DESC LIMIT 0,15");
			$this -> render_partial("listadogrupos");
			
		}
		
		function gruposajax($p=0){
			$this -> set_response("view");
			
			$division = Session::get_data('division');
			
			$especialidades = new Especialidades();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$Divisiones = new Divisiones();
			if($Divisiones -> find_first("maestro_id = ".Session::get_data('registro'))){
				if($Divisiones -> id == 110 || $Divisiones -> id == 160){
					$division_id = 999;
				}else{
					$division_id = $Divisiones -> id;
				}
			}else{
				echo "no se hayo la division";
				$division_id = 999;
			}
			
			$this -> especialidades = $especialidades -> find("1 ORDER BY nombre");
			foreach($this -> especialidades as $especialidad){
				$this -> especialidad[$especialidad -> id] = $especialidad -> nombre;
			}
			
			$grupos = new Grupos();
			$this -> listado = $grupos -> find("periodo = ".$periodo." and especialidad_id = ".$division_id." ORDER BY nombre LIMIT ".$p.",15");
			
			$n = $grupos -> count('periodo = '.$periodo);
			
			$this -> p1 = $p - 15; if($this -> p1 < 0) $this -> p1 = 0;
			$this -> p2 = $p + 15; if($this -> p2 >= $n) $this -> p2 = $p;
			
			$this  -> propietario = "SELECCIONA EL GRUPO QUE QUIERES REVISAR";
		}
		
		function quitargrupo($id){
			$this -> set_response("view");
			$division = Session::get_data('division');$division = 1;
						
			$grupo = new Grupos();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$grupo = $grupo -> find_first("id=".$id);			
			$nombreT = $grupo -> nombre;
			$divisionT = $grupo -> especialidad_id;
			if($grupo -> delete()){
				Flash::success('Se borro el grupo: '.$nombreT." para la Division: ".$divisionT);
			}else{
				Flash::error('No se pudo guardar');
			}
			$this -> listado = $grupo -> find("periodo = ".$periodo." ORDER BY nombre LIMIT 0,15");
			$this -> render_partial("listadogrupos");
		}
		
		function jornadagrupo($id){
			$this -> set_response("view");
		
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$grupo = new Grupos();
			$this -> grupo = $grupo -> find_first("id=".$id);
			$this -> propietario = "GRUPO: ".$this -> grupo -> nombre;
			
			$jornada = new JornadaGrupo();
			$m = $jornada -> count("grupo='".$this -> grupo -> nombre."' AND periodo=".$periodo);
			
			if($m==0){
				$jornadagrupo = new JornadaGrupo();
				$jornadagrupo -> grupo = $this -> grupo -> nombre;
				$jornadagrupo -> periodo = $periodo;
				$jornadagrupo -> lunes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> martes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> miercoles = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> jueves = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> viernes = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> sabado = "XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX-XXX";
				$jornadagrupo -> save();
			}
			
			$this -> jornada = $jornada -> find_first("grupo='".$this -> grupo -> nombre."' AND periodo=".$periodo);
		}
		
		function asignarHorario1($maestro=""){
			$this -> set_response('view');
			echo 'hola';
		}
		
		function asignarHorario($maestro=""){
			$this -> set_response("view");
			
			$horas = new Horas();
			$grupos = new Grupos();			
			$salones = new Salones();			
			$horarios = new Horarios();
			$maestros = new Maestros();
			$materias = new MateriasHorarios();
			$division = Session::get_data('div');
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			if($maestro==""){
				$maestro = Session::get_data('registro');
			}
			$this -> maestro = $maestros -> find_first("id=".$maestro);		
			
			if($_POST["salon_id"]=="" || $_POST["horario_id"]==""){
				$_POST["salon_id"]=0;
				$_POST["horario_id"]=0;
			}
			/*
			$total  = $horarios -> count("maestro_id=".$maestro." AND periodo=".$periodo." AND materia_id=".$horarios -> materia_id." AND grupo_id=".$horarios -> grupo_id);
			if($total>0){
				$horarios  = $horarios -> find_first("maestro_id=".$maestro." AND periodo=".$periodo." AND materia_id=".$horarios -> materia_id." AND grupo_id=".$horarios -> grupo_id);
			}			
			$horarios -> maestro_id = $maestro;
			$horarios -> materia_id = $horarios -> materia_id;
			$horarios -> periodo = $periodo;
			$horarios -> grupo_id = $horarios -> grupo_id;
			$horarios -> division = $division;				
			$horarios -> save(); */	
			if($this -> post('horario_id') == 0){
			
			}else{
				if($horarios  = $horarios -> find_first($this -> post("horario_id"))){
				
				}else{
					echo "no se encontro el horario";
				}
			}
				
			$total  = $horas -> count("maestro_id=".$maestro." AND auxiliar=".$this -> post("tmp"));
			if($total>0){
				if($horas  = $horas -> find_first("maestro_id=".$maestro." AND auxiliar=".$this -> post("tmp"))){
				
				}else{
					echo "no se puede";
				}
			}
				
			$dia = $this -> post("tmp");
			$hora=1;
			while($dia > 6){
				$dia -= 6;
				$hora++;
			}
			
			$tempo = $this -> post('horario_id');			
			
			if($this -> post('horario_id')==0){
				$horas -> delete();
				
				$horas2 = new Horas();							
				$clases = $horas2 -> count("horario_id=".$tempo);
				
				///  JORNADA LABORAL BORRAR
				$jornadalaboral = new JornadaLaboral();
				if($jornadalaboral -> find_first("maestro_id=".$maestro." AND periodo=".$periodo)){				
					switch($horas -> dia){
						case 1: $jornada = $jornadalaboral -> lunes; break;
						case 2: $jornada = $jornadalaboral -> martes; break;
						case 3: $jornada = $jornadalaboral -> miercoles; break;
						case 4: $jornada = $jornadalaboral -> jueves; break;
						case 5: $jornada = $jornadalaboral -> viernes; break;
						case 6: $jornada = $jornadalaboral -> sabado; break;
					}				
					$p1 = substr($jornada,0,($horas -> hora-1)*4);
					$p2 = substr($jornada,($horas -> hora-1)*4 + 3);
					
					switch($horas -> dia){
						case 1: $jornadalaboral -> lunes = $p1."XXX".$p2; break;
						case 2: $jornadalaboral -> martes = $p1."XXX".$p2; break;
						case 3: $jornadalaboral -> miercoles = $p1."XXX".$p2; break;
						case 4: $jornadalaboral -> jueves = $p1."XXX".$p2; break;
						case 5: $jornadalaboral -> viernes = $p1."XXX".$p2; break;
						case 6: $jornadalaboral -> sabado = $p1."XXX".$p2; break;
					}				
					$jornadalaboral -> save();					
				}
				
				///  JORNADA SALON BORRAR
				/*
				$jornadasalon = new JornadaSalon();
				if($jornadasalon -> find_first("salon_id=".$this -> post("salon_id")." AND periodo=".$periodo)){
					switch($horas -> dia){
					case 1: $jornada = $jornadasalon -> lunes; break;
					case 2: $jornada = $jornadasalon -> martes; break;
					case 3: $jornada = $jornadasalon -> miercoles; break;
					case 4: $jornada = $jornadasalon -> jueves; break;
					case 5: $jornada = $jornadasalon -> viernes; break;
					case 6: $jornada = $jornadasalon -> sabado; break;
					}				
					$p1 = substr($jornada,0,($horas -> hora-1)*4);
					$p2 = substr($jornada,($horas -> hora-1)*4 + 3);
				
					switch($horas -> dia){
						case 1: $jornadasalon -> lunes = $p1.'XXX'.$p2; break;
						case 2: $jornadasalon -> martes = $p1.'XXX'.$p2; break;
						case 3: $jornadasalon -> miercoles = $p1.'XXX'.$p2; break;
						case 4: $jornadasalon -> jueves = $p1.'XXX'.$p2; break;
						case 5: $jornadasalon -> viernes = $p1.'XXX'.$p2; break;
						case 6: $jornadasalon -> sabado = $p1.'XXX'.$p2; break;
					}
					$jornadasalon -> save();									
				}*/
				
			}else{								
				$horas -> maestro_id = $maestro;
				$horas -> materia_id = $horarios -> materia_id;
				$horas -> grupo_id = $horarios -> grupo_id;
				$horas -> salon_id = $this -> post("salon_id");
				$horas -> horario_id = $horarios -> id;
				$horas -> dia = $dia;
				$horas -> periodo = $periodo;
				$horas -> hora = $hora;
				$horas -> auxiliar = $this -> post("tmp");
					
				if($horas -> save()){
					//echo 'si se pudo';
				}else{
					echo 'no se puede!';
				}
				
				$jornadalaboral = new JornadaLaboral();
				if($jornadalaboral -> find_first("maestro_id=".$maestro." AND periodo=".$periodo)){				
				}else{
					$jornadalaboral = new JornadaLaboral();
				}
					
				switch($horas -> dia){
					case 1: $jornada = $jornadalaboral -> lunes; break;
					case 2: $jornada = $jornadalaboral -> martes; break;
					case 3: $jornada = $jornadalaboral -> miercoles; break;
					case 4: $jornada = $jornadalaboral -> jueves; break;
					case 5: $jornada = $jornadalaboral -> viernes; break;
					case 6: $jornada = $jornadalaboral -> sabado; break;
				}
				
				$p1 = substr($jornada,0,($horas -> hora-1)*4);
				$p2 = substr($jornada,($horas -> hora-1)*4 + 3);
				
				switch($horas -> dia){
					case 1: $jornadalaboral -> lunes = $p1.$division.$p2; break;
					case 2: $jornadalaboral -> martes = $p1.$division.$p2; break;
					case 3: $jornadalaboral -> miercoles = $p1.$division.$p2; break;
					case 4: $jornadalaboral -> jueves = $p1.$division.$p2; break;
					case 5: $jornadalaboral -> viernes = $p1.$division.$p2; break;
					case 6: $jornadalaboral -> sabado = $p1.$division.$p2; break;
				}
					
				$jornadalaboral -> save();
				
				if($this -> post("salon_id")>0){
					
					$jornadasalon = new JornadaSalon();
					if($jornadasalon -> find_first("salon_id=".$this -> post("salon_id")." AND periodo=".$periodo)){
					}else{
						$jornadasalon = new JornadaSalon();
					}
					
					switch($horas -> dia){
						case 1: $jornada = $jornadasalon -> lunes; break;
						case 2: $jornada = $jornadasalon -> martes; break;
						case 3: $jornada = $jornadasalon -> miercoles; break;
						case 4: $jornada = $jornadasalon -> jueves; break;
						case 5: $jornada = $jornadasalon -> viernes; break;
						case 6: $jornada = $jornadasalon -> sabado; break;
					}
				
					$p1 = substr($jornada,0,($horas -> hora-1)*4);
					$p2 = substr($jornada,($horas -> hora-1)*4 + 3);
				
					switch($horas -> dia){
						case 1: $jornadasalon -> lunes = $p1.$division.$p2; break;
						case 2: $jornadasalon -> martes = $p1.$division.$p2; break;
						case 3: $jornadasalon -> miercoles = $p1.$division.$p2; break;
						case 4: $jornadasalon -> jueves = $p1.$division.$p2; break;
						case 5: $jornadasalon -> viernes = $p1.$division.$p2; break;
						case 6: $jornadasalon -> sabado = $p1.$division.$p2; break;
					}
					
					$jornadasalon -> save();
				}
				
				if($horarios -> grupo_id>0){
					
					$grupos = new Grupos();
					$grupo = $grupos -> find_first("id=".$horarios -> grupo_id." AND periodo = ".$periodo);
					
					$jornadagrupo = new JornadaGrupo();
					if($jornadagrupo -> find_first("grupo='".$grupo -> nombre."' AND periodo=".$periodo)){
					}else{
						$jornadagrupo = new JornadaGrupo();
					}
					
					switch($horas -> dia){
						case 1: $jornada = $jornadagrupo -> lunes; break;
						case 2: $jornada = $jornadagrupo -> martes; break;
						case 3: $jornada = $jornadagrupo -> miercoles; break;
						case 4: $jornada = $jornadagrupo -> jueves; break;
						case 5: $jornada = $jornadagrupo -> viernes; break;
						case 6: $jornada = $jornadagrupo -> sabado; break;
					}
				
					$p1 = substr($jornada,0,($horas -> hora-1)*4);
					$p2 = substr($jornada,($horas -> hora-1)*4 + 3);
				
					switch($horas -> dia){
						case 1: $jornadagrupo -> lunes = $p1.$division.$p2; break;
						case 2: $jornadagrupo -> martes = $p1.$division.$p2; break;
						case 3: $jornadagrupo -> miercoles = $p1.$division.$p2; break;
						case 4: $jornadagrupo -> jueves = $p1.$division.$p2; break;
						case 5: $jornadagrupo -> viernes = $p1.$division.$p2; break;
						case 6: $jornadagrupo -> sabado = $p1.$division.$p2; break;
					}					
					$jornadagrupo -> save();
				}
				
				if($total>0){
					if($tempo != $horarios -> id && $horarios -> id>0){			
						$horas2 = new Horas();
						$horarios2 = new Horarios();
					
						$clases = $horas2 -> count("horario_id=".$tempo);
					
						if($clases==0){
							$horarios2 = new Horarios();
							$horario = $horarios2 -> find_first("id=".$tempo);
							$horario -> delete();
						}
					}
				}			
				if($materia = $materias -> find_first($horarios -> materia_id)){
					$this -> materia = $materia -> nombre;
					$this -> clave = $materia -> clave;
					$this -> materiaid = $materia -> id;
				}
					
				if($grupo = $grupos -> find_first($horarios -> grupo_id)){
					$this -> grupo = $grupo -> nombre;
					$this -> grupoid = $grupo -> id;
				}
				
				if($salon = $salones -> find_first($this -> post("salon_id"))){
					$this -> salon = $salon -> nombre;
					$this -> salonid = $salon -> id;
				}
			}
			echo $this -> render_partial("hora");
		}
			
		function abrirHorario($temporal,$maestro=""){
			$this -> set_response("view");
						
			$horarios = new Horarios();
			$salones = new Salones();
			$Periodo = new Periodos();			
			
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }					
			
			if($maestro=="")
				$maestro = Session::get_data('registro');
			$this -> tmp = $temporal;
			/*		
			// comente todo esto que era la forma en lo que lo hacia ramiro
			$this -> materia = "";
			$this -> clave = "";
			$this -> materiaid = "";				
			$this -> grupo = "";
			$this -> grupoid = "";				
			$this -> salon = "";
			$this -> salonid = "";														
			$total  = $horarios -> count("maestro_id=".$maestro." AND periodo=".$periodo);
			
			if($horas -> count("maestro_id=".$maestro." AND auxiliar=".$temporal)>0){
				$hora = $horas -> find_first("maestro_id=".$maestro." AND auxiliar=".$temporal);
			
				$this -> materiaid = $hora -> materia_id;
				$materia = $materias -> find_first("id=".$hora -> materia_id);
				$this -> clave = $materia -> clave;
				$this -> materia = $materia -> nombre;
			
				$this -> grupoid = $hora -> grupo_id;
				$grupo = $grupos -> find_first("id=".$hora -> grupo_id);
				$this -> grupo = $grupo -> nombre;
				
				$this -> salonid = $hora -> salon_id;
				$salon = $salones -> find_first("id=".$hora -> salon_id);
				$this -> salon = $salon -> nombre;
			}
			else{
				$this -> materiaid = 0;
				$this -> clave = "";
				$this -> materia = "";
			
				$this -> grupoid = 0;
				$this -> grupo = "";
				
				$this -> salonid = 0;
				$this -> salon = "";
			}						
			$this -> materias = $materias -> find("1 ORDER BY clave");
			$this -> grupos = $grupos -> find("1 ORDER BY nombre");*/
			$Horarios = new Horarios();
			$Divisiones = new Divisiones();
			if($Divisiones -> find_first("maestro_id = ".Session::get_data('registro'))){				
					$division_id = $Divisiones -> id;
					if($Divisiones -> id == 800)
						$division_id = 804;
			}else{
				echo "no se hayo la division";
				$division_id = 999;
			}
			$this -> horarios = $Horarios -> find("maestro_id = ".$maestro." and periodo = ".$periodo);
			$this -> salones = $salones -> find("divisiones_id = ".$division_id." ORDER BY nombre ASC");			
			//$this -> salones = $salones -> find("1 ORDER BY nombre ASC");
			
			$this -> maestro = $maestro;
		}
		
		function agregarActa(){
			$this -> valida();
			$this -> set_response("view");
			$this -> maestro = new Maestros();
			$Periodo = new Periodos();
			$Divisiones = new Divisiones();
			
			$materia_id = $this -> post('materia_id');
			$grupo_id = $this -> post('grupo_id');
			$maestro_id = $this -> post('maestro_id');
			
			if($Periodo -> find_first("activo = 1")){			
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			if($Divisiones -> find_first('maestro_id = '.Session::get_data('registro'))){							
				$Horario = new Horarios();
				$Horario -> maestro_id = $maestro_id;
				$Horario -> materia_id = $materia_id;
				$Horario -> periodo = $Periodo -> periodo;
				$Horario -> grupo_id = $grupo_id;
				$Horario -> division = $Divisiones -> clave;
				if($Horario -> save()){
				
				}else{ 
					Flash::error('No se pudo GUARDAR la acta nueva para el profesor '.$maestro_id.' grupo '.$grupo_id.' materia '.$materia_id); 			
				}						
			}else{ 
				echo "No se encontro a este profesor como coordinador de alguna division<br />"; 
			}
			$this -> maestro -> id = $this -> post('maestro_id');								
			$this -> ajax = 1;
			echo $this -> render_partial("actasCargadas");
		}
		
		function borrarActa($acta=0,$maestro_id){
			$this -> valida();
			$this -> set_response("view");
			$Horarios = new Horarios();	
			$Horas = new Horas();				
			$Calificaciones = new Calificaciones();
			if($acta != 0){
				if($Horarios -> find_first($acta)){
					if($Calificaciones -> find_first('horario_id = '.$acta)){
						Flash::error('No se pudo borrar por que tiene registrados alumnos. Acta no.'.$acta);
					}else{						
						if($Horarios -> delete()){
							if($horas = $Horas -> find("horario_id = ".$acta)){
								foreach($horas as $hora){
									if($Horas -> find_first($hora -> id)){
										if($Horas -> delete()){
											
										}else{
											Flash::error('No se pudo borrar la hora en el Acta '.$acta);
										}
									}else{
										Flash::error('No se encontro una de las horas del acta '.$acta);
									}
								}
							}else{
								Flash::notice('No tenia Horas Cargadas en el Acta '.$acta);
							}														
						}else{
							Flash::error('No se pudo borrar');
						}						
					}					
				}else{
					Flash::error('No se encontro el acta');
				}
			}
			$this -> maestro = new Maestros();
			$this -> maestro -> id = $maestro_id;
			$this -> ajax = 1;
			echo $this -> render_partial("actasCargadas");
		}
		
		function buscar(){
			$this -> valida();
		}
		
		function buscando(){
			$this -> valida();
			$this -> set_response("view");
			
			if($this -> post("registro") != ''){
				$this -> registro = " and registro = ".$this -> post("registro");
			}else{
				$this -> registro = "";
			}
			if($this -> post("nombre") != ''){
				$this -> nombre = " and nombre_completo like '%".utf8_decode($this -> post("nombre"))."%'";
			}else{
				$this -> nombre = "";
			}
			if($this -> registro != "" || $this -> nombre != ""){
				$Alumnos = new Alumnos();
				if($this -> alumnos = $Alumnos -> find("1".$this -> registro.$this -> nombre)){
					$this -> render_partial("busquedaAlumnos");
				}else{
					Flash::error("No se encontro ningun alumno");
				}	
			}else{
				Flash::error("No se encontro ningun alumno");
			}
			$this -> registro="";
			$this -> nombre="";
		}
		
		function valida(){
			if(Session::get("tipo")=="COORDINADOR"){
				return true;
			}
			//$this -> redirect("general/ingresar");
			return true;
		}
	}	
?>
