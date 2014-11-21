<?php
			
	class TgoprofesorController extends ApplicationController {				
		
		public $periodo = 12009;
		
		function index(){
			$this -> valida();
			$Materias = new Materias();
			$this -> materias = $Materias -> find("division_id = 200 and nivel = 1");			
		}
		
		function informacion(){
			$this -> valida();
		}
		
		function listas(){
			$this -> valida();
			$Horarios = new Horarios();
			$this -> horarios = $Horarios -> find("maestro_id = ".Session::get_data("registro")." and materia_id != 0");
			
		}
		
		function listado($acta){
			//$this -> valida();
			$this -> set_response("view");					
			
			$Calificaciones = new Calificaciones();
			$this -> listas = $Calificaciones -> find("horario_id = ".$acta);
			$this -> render_partial("listadoAlumnos");
		}
		
		function listadoCalificaciones($acta,$parcial){
			//$this -> valida();
			$this -> set_response("view");					
			
			$Calificaciones = new Calificaciones();
			$this -> parcial = $parcial;
			$this -> listas = $Calificaciones -> find("horario_id = ".$acta);
			$this -> render_partial("listadoAlumnosCalificaciones");
		}
		
		function calificar(){
			$this -> set_response("view");
			$idcal = $this -> post("idcal");
			foreach($idcal as $a){
				echo $a.",<br>";
			}
		}
		
		function horario(){
			$this -> valida();						
		}
		
		function calificaciones(){
			$this -> valida();
			$Horarios = new Horarios();
			$this -> horarios = $Horarios -> find("maestro_id = ".Session::get_data("registro")." and materia_id != 0");
		}
		
		function tutorados(){
			$this -> valida();
			
			$Tutorias = new Tutorias();
			$nomina = Session::get("registro");
			
			$this -> tutorias = $Tutorias -> find('nomina = '.$nomina);
			
			
		}				
		
		function estadisticas(){
			$this -> valida();
		}
		
		function apoyo(){
			$this -> valida();	
			$Ticket = new Ticket();
			$Usuarios = new Usuarios();
			$usuario = $Usuarios -> find_first("usuario = ".Session::get_data("registro"));
			$this -> ticket = $Ticket -> find("(estado like 'Abierto' or estado like 'Proceso') and dirigido_a = ".$usuario -> id);
		}
		
		function buscar(){
			$this -> set_response("view");
			if($this -> post('numero') != "") {
				$numero = " and id = ".$this->post("numero");	
			}else{
				$numero ="";
			}			
			
			if($this -> post('registro')!= "") {
				$registro = " and usuario_id = ".$this->post("registro");	
			}else{
				$registro="";
			}
			
			$Ticket = new Ticket();
			$this -> ticket = $Ticket -> find("1 ".$numero.$registro); 
			$this -> render_partial('buscar');
			//echo "actulizado<br>$registro<br>$numero";
		}
		
		function ticketSolucionado($id){
			$this -> set_response("view");
			$Ticket = new Ticket();
			if ($Ticket -> find_first("id = ".$id)){
				$Ticket -> estado= "Cerrado";
				$Ticket -> fecha_final = date("Y-m-d H:i:s",time());
				if ($Ticket -> save())
					Flash::success("<center>Se soluciono el ticket numero $id</center>");
			}
			else
				Flash::error("<center>No se pudo guardar el ticket numero $id o no existe</center>");
		}
		
		function digidoA(){
			$this -> set_response("view");
			$dirigido_a = $this->post("dirigido_a");
			$id = $this -> post("id");
			
			$Usuario = new Usuarios();
			if($usuario = $Usuario -> find_first("id = '".$dirigido_a."'")){
				$Ticket = new Ticket();
				if ($Ticket -> find_first("id = ".$id)){
					$Ticket -> dirigido_a= $dirigido_a;
					$Ticket -> estado= "Proceso";
					if ($Ticket -> save())
						Flash::success("<center>Se redirecciono el ticket numero $id al usuario $dirigido_a </center>");
				}else{
					Flash::error("<center>No se pudo guardar el redireccionamiento del ticket numero $id</center>");			
				}
			}else{
					Flash::error("<center>No se pudo redireccionar el ticket numero $id por que no existe el usuario</center>");			
			}
		}
		
		function responder(){
			$this -> set_response("view");

			$id = $this -> post("id");
			$respuesta = $this -> post("respuesta");
			$Ticket = new Ticket();
			if ($Ticket -> find_first("id = ".$id)){
				$Ticket -> respuesta = utf8_decode($respuesta);
				$Ticket -> estado= "Proceso";
				if ($Ticket -> save())
					Flash::success("<center>Se guardo la respuesta de el ticket numero $id</center>");
			}else
				Flash::error("<center>No se pudo guardar la respuesta de el ticket numero $id o no existe</center>");
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}
		
		function actividades(){
			$this -> valida();
			$this -> nomina = Session::get_data('registro');
			$Actividades = new Actividades();
			$ActividadMaestro = new ActividadMaestro();
			$ActividadMaestroEspecial = new ActividadMaestroEspecial();
			$Horas = new Horas();
			
			$this -> actividades = $Actividades -> find("id != 1 and id != 5 and id != 9 and id != 14 and id != 22 and id != 28 and id != 33");
			$this -> actividadMaestro = $ActividadMaestro -> find("maestro_id = ".$this -> nomina." and periodo = ".$this -> periodo);
			$this -> actividadMaestroEspecial = $ActividadMaestroEspecial -> find("maestro_id = ".$this -> nomina." and periodo = ".$this -> periodo);
			$this -> horasPrimeraActividad = $Horas -> count("maestro_id = ".$this -> nomina);;
			$this -> primeraActividad = $Actividades -> find_first("id = 1");
			
		}
		
		function eliminarActividad($id,$tipo){
			$this -> set_response("view");	
			if ($tipo == 1){
				$ActividadMaestro = new ActividadMaestro();
			}
			if($tipo == 2){ 
				$ActividadMaestro = new ActividadMaestroEspecial();
			}			
			
			$ActividadMaestro -> find_first("id = ".$id);
			if($ActividadMaestro -> delete()){
				Flash::success('Borraste una actividad');
			}else{
				Flash::error('No se pudo borrar la actividad');
			}			
			
			$Horas = new Horas();
			$Actividades = new Actividades();	
			$ActividadMaestro = new ActividadMaestro();
			$ActividadMaestroEspecial = new ActividadMaestroEspecial();					
			
			$this -> actividadMaestro = $ActividadMaestro -> find("maestro_id = ".$this -> nomina);
			$this -> horasPrimeraActividad = $Horas -> count("maestro_id = ".$this -> nomina);;
			$this -> actividadMaestroEspecial = $ActividadMaestroEspecial -> find("maestro_id = ".$this -> nomina." and periodo = ".$this -> periodo);
			$this -> primeraActividad = $Actividades -> find_first("id = 1");
			$this -> render_partial("listadoActividades");
		}
		
		function agregarActividad($tipo){
			$this -> set_response("view");
			$this -> nomina = Session::get_data('registro');			
			
			$horas = $this -> post("horas");
			$Horas = new Horas();	
			$ActividadMaestro = new ActividadMaestro();	
			$ActividadMaestroEspecial = new ActividadMaestroEspecial();	
			
			$Actividades = new Actividades();		
			if ($tipo == 1){
				$actividad_id = $this -> post("actividad_id");
				$meta = $this -> post("meta");
				$objetivo = $this -> post("objetivo");
													
				$ActividadMaestro -> actividad_id = $actividad_id;
				$ActividadMaestro -> maestro_id = $this -> nomina;
				$ActividadMaestro -> horas = $horas;
				$ActividadMaestro -> objetivo = $objetivo;
				$ActividadMaestro -> meta = $meta;
				$ActividadMaestro -> autoevaluacion = "-";
				$ActividadMaestro -> avance = "0";				
				$ActividadMaestro -> periodo = $this -> periodo;
				if($ActividadMaestro -> save()){
					Flash::success('Agregaste una actividad');		
				}
				else{
					Flash::error('No se pudo Agregar una actividad');
				}
			}
			if($tipo == 2){
				$actividad = $this -> post("actividad");
				$meta = $this -> post("meta");
				$objetivo = $this -> post("objetivo");

				$ActividadMaestroEspecial = new ActividadMaestroEspecial();	
				
				$ActividadMaestroEspecial -> nombre = $actividad;
				$ActividadMaestroEspecial -> maestro_id = $this -> nomina;
				$ActividadMaestroEspecial -> meta = $meta;
				$ActividadMaestroEspecial -> objetivo = $objetivo;
				$ActividadMaestroEspecial -> horas = $horas;
				$ActividadMaestroEspecial -> autoevaluacion = "-";	
				$ActividadMaestroEspecial -> avance = "0";			
				$ActividadMaestroEspecial -> periodo = $this -> periodo;
				if($ActividadMaestroEspecial -> save()){
					Flash::success('Agregaste una actividad especial');		
				}
				else{
					Flash::error('No se pudo Agregar una actividad especial');
				}
			}									
						
			$this -> actividadMaestro = $ActividadMaestro -> find("maestro_id = ".$this -> nomina);
			$this -> actividadMaestroEspecial = $ActividadMaestroEspecial -> find("maestro_id = ".$this -> nomina." and periodo = ".$this -> periodo);
			$this -> horasPrimeraActividad = $Horas -> count("maestro_id = ".$this -> nomina);;
			$this -> primeraActividad = $Actividades -> find_first("id = 1");
			$this -> render_partial("listadoActividades");
		}
		
		function evaluacionSemanal($tipo, $id){
			$this -> set_response("view");
			$this -> tipo= $tipo;
			$this -> id = $id;													
		}
		
		function agregarEvaluacionSemanal($tipo, $id){
			$this -> set_response("view");
			$this -> tipo= $tipo;
			$this -> id = $id;
			
			$semana = "s".$this -> post("semana");
			$horas = $this -> post("horas");
			
			if($tipo == 1){
				$ActividadMaestro= new ActividadMaestro();	
			}
			if($tipo == 2){
				$ActividadMaestro= new ActividadMaestroEspecial();													
			}
			
			$ActividadMaestro -> find_first("id = ".$id);
			$ActividadMaestro -> $semana = $horas;			
			if($ActividadMaestro -> save()){
				$this -> v=1;
				$this -> msj = 'Se actualizo la Semana '.$this -> post("semana")." con ".$horas." hrs.";
			}else{
				$this -> v=0;
				$this -> msj ="No se pudo actualizar las horas de la semana no. ".$this -> post("semana");
			}							 
		}
		
		function autoevaluacionFinal($tipo, $id, $parcial){
			$this -> tipo= $tipo;
			$this -> id = $id;
			$this -> parcial = $parcial;			
			
			$this -> set_response("view");						
		}
		
		function agregarAutoevaluacionFinal($tipo, $id, $parcial){
			$this -> set_response("view");	
			$this -> tipo= $tipo;
			$this -> id = $id;		
			
			$autoevaluacion = $this -> post("autoevaluacion");
			$avance = $this -> post("avance");
			
			if($tipo == 0){
				$ActividadMaestro = new ActividadClase();	
				if($ActividadMaestro -> find_first("maestro_id = ".$id)){
				}else{
					$ActividadMaestro = new ActividadClase();
					$ActividadMaestro -> maestro_id = $id;
					$ActividadMaestro -> periodo = $periodo;
				}
				$tautoevaluacion = "autoevaluacion".$parcial;
				$tavance = "avance".$parcial;
				$ActividadMaestro -> $tautoevaluacion = utf8_decode($autoevaluacion);
				$ActividadMaestro -> $tavance = $avance;
				if($ActividadMaestro -> save()){
					Flash::success('Se ha guardado la Autoevaluaci&oacute;n');
				}else{
					Flash::error('No se pudo Guardar la Autoevaluaci&oacute;n');
				}
				
			}else{			
				if($tipo == 1){
					$ActividadMaestro = new ActividadMaestro();	
				}
				if($tipo == 2){
					$ActividadMaestro = new ActividadMaestroEspecial();	
				}
				if($ActividadMaestro -> find_first("id = ".$id)){
					$tautoevaluacion = "autoevaluacion".$parcial;
					$tavance = "avance".$parcial;
					$ActividadMaestro -> $tautoevaluacion = utf8_decode($autoevaluacion);
					$ActividadMaestro -> $tavance = $avance;
					if($ActividadMaestro -> save()){
						Flash::success('Se ha guardado la Autoevaluaci&oacute;n');
					}else{
						Flash::error('No se pudo Guardar la Autoevaluaci&oacute;n');
					}
				}
			}
		}
		
		function recargarMaterias($nivel){
			$this -> set_response("view");
			$Materias = new Materias();
			$this -> materias = $Materias -> find("division_id = 200 and nivel = ".$nivel);
			echo render_partial("recargarMaterias");
			$this -> nivel = $nivel;
		}
		
		function informacionMateria($id){
			$this -> set_response("view");
			echo "INFO de la materia ".$id;
		}
				
		function impr1($nomina=300){
			$this -> set_response("view");
				$Horarios = new Horarios();
				$Horas = new Horas();
				$Maestros = new Maestros();
			
				$maestro = $Maestros -> find_first("id = ".$nomina);
				$reporte = new FPDF();
				$reporte -> AddPage();
				$horasTotal = $Horas -> count("maestro_id = ".$nomina);		
				
				$reporte -> SetX(55);				
				$reporte -> SetFont('Arial','B',12);
				$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);				
				$reporte -> Ln();
				
				$reporte -> SetX(55);
				$reporte -> SetFont('Arial','B',10);
				$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);				
				$reporte -> Ln();
				
				$reporte -> SetX(55);
				$reporte -> SetFont('Arial','B',9);
				$reporte -> MultiCell(0,2,"NIVEL TECNÓLOGO",0,'C',0);			
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				$MaestroDivisiones = new MaestroDivisiones();
				$maestroDivisiones = $MaestroDivisiones -> find_first("maestro_id = ".$nomina);
				$Divisiones = new Divisiones();
				$division = $Divisiones -> find_first("id = ".$maestroDivisiones -> division_id);
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"MAESTRO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$maestro -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(55,4,$division -> nombre,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(20,4,"No. NOMINA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$maestro -> id,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($maestro -> plantel == 'C'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(20,4,$plantel,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(40,4,"FEB - JUN 2009",1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(40,4,"No. DE SESIONES DE CLASE:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$horasTotal,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(10,4,"TURNO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(30,4,$maestro -> turno,1,0,'L',1);
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(16,4,"HORA",1,0,'C',1);
				$reporte -> Cell(29,4,"LUNES",1,0,'C',1);
				$reporte -> Cell(29,4,"MARTES",1,0,'C',1);
				$reporte -> Cell(29,4,"MIERCOLES",1,0,'C',1);
				$reporte -> Cell(29,4,"JUEVES",1,0,'C',1);
				$reporte -> Cell(29,4,"VIERNES",1,0,'C',1);
				$reporte -> Cell(29,4,"SABADO",1,0,'C',1);
				
				$posExtra=29;				
				$y=44;
				for($i=0;$i<14;$i++){	
					$pos=10;				
					if ($i == 2){
						$reporte -> SetFont('Arial','B',6);
						$reporte -> SetXY(10, $y+1);
						$reporte -> SetFillColor(0x99,0x99,0x99);					
						$reporte -> MultiCell(16,5,"8:50 \n 9:30" ,1,'C',1);
						$reporte -> SetXY(26, $y+1);
						$reporte -> MultiCell(174,5,"RECESO \n ",1,'C',1);
						$y = $y+11;
					}					
					$reporte -> SetFont('Arial','B',6);
					$reporte -> SetXY($pos, $y);
					$reporte -> SetFillColor(0x99,0x99,0x99);					
					$reporte -> MultiCell(16,5,$horas[$i],1,'C',1);					
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					$reporte -> SetFont('Arial','B',5);
					for($d=1;$d<7;$d++){
						if ($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}												
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$Horas1 = new Horas();
						if ($hora = $Horas1 -> find_first("maestro_id = ".$nomina." and dia = ".$d." and hora = $i+1")){
							$Grupos = new Grupos();
							$Salones = new Salones();
							$Materias = new MateriasHorarios();
							$grupo = $Grupos -> find_first("id = ".$hora -> grupo_id);
							$salon = $Salones -> find_first("id = ".$hora -> salon_id);
							$materia = $Materias -> find_first("id = ".$hora -> materia_id);
							
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,5,$materia -> nombre." \n ".$grupo -> nombre." - ".$salon -> corto,1,'L',1);	
							$contador[$d]++;
						}else{
							$reporte -> SetFillColor(0xFF,0xFF,0xFF);
							$reporte -> MultiCell(29,5," \n ",1,'C',1);
						}
						
					}
					$y = $y+9;
				}
				$reporte -> SetFont('Arial','B',4);
				$pos=10;
				$reporte -> SetXY($pos, $y);
				$reporte -> SetFillColor(0x99,0x99,0x99);					
				$reporte -> MultiCell(16,4,"SESIONES / DIA \n JORNADA",1,'C',1);					
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetFont('Arial','B',5);
				
				$jornada ="- - -";
				$HorarioJornada = new HorarioJornada();
				if($horarioJornada = $HorarioJornada -> find_first("periodo = ".$this -> periodo." and maestro_id = ".$nomina)){
					for($d=1;$d<7;$d++){
						if ($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}					
						if($d == 1)
							$jornada=$horarioJornada -> lunes;
						if($d == 2)
							$jornada=$horarioJornada -> martes;
						if($d == 3)
							$jornada=$horarioJornada -> miercoles;
						if($d == 4)
							$jornada=$horarioJornada -> jueves;
						if($d == 5)
							$jornada=$horarioJornada -> viernes;
						if($d == 6)
							$jornada=$horarioJornada -> sabado;					
						
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,2,$contador[$d]." hrs. \n ".$jornada,1,'C',1);
					}
				}
				
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> Image('http://ase.ceti.mx'.KUMBIA_PATH.'public/img/logoceti.jpg', 25, 3,36,22);
				$reporte -> Output("public/files/tgohorarios/iii.pdf");
				
				$this->redirect("public/files/tgohorarios/iii.pdf");
		}
		
		public function horarioPDF($nomina){	
			
			$this -> set_response("view");
			
			$Horarios = new Horarios();
			$Horas = new Horas();
			$Maestros = new Maestros();			
			
			$horasTotal = $Horas -> count("maestro_id = ".$nomina);															
			
			$contador= array (0,0,0,0,0,0,0);								
			
			$horas = array("7:00 \n 7:55","7:55 \n 8:50","9:30 \n 10:25","10:25 \n 11:20","11:20 \n 12:15","12:15 \n 13:10","13:10 \n 14:05","14:05 \n 15:00","15:00 \n 15:55","15:55 \n 16:50","16:50 \n 17:45","17:45 \n 18:40","18:40 \n 19:35","19:35 \n 20:30"); 
			
			if ($maestro = $Maestros -> find_first("id = ".$nomina)){					
				
				$reporte = new FPDF();
				$reporte -> AddPage();				
				
				$reporte -> Image('http://ase.ceti.mx'.KUMBIA_PATH.'public/img/logoceti.jpg', 25, 3,36,22);
				
				$reporte -> SetX(55);				
				$reporte -> SetFont('Arial','B',12);
				$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);				
				$reporte -> Ln();
				
				$reporte -> SetX(55);
				$reporte -> SetFont('Arial','B',10);
				$reporte -> MultiCell(0,3,"SUBDIRECCIÓN DE OPERACION ACADÉMICA",0,'C',0);				
				$reporte -> Ln();
				
				$reporte -> SetX(55);
				$reporte -> SetFont('Arial','B',9);
				$reporte -> MultiCell(0,2,"NIVEL TECNÓLOGO",0,'C',0);			
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				$MaestroDivisiones = new MaestroDivisiones();
				$maestroDivisiones = $MaestroDivisiones -> find_first("maestro_id = ".$nomina);
				$Divisiones = new Divisiones();
				$division = $Divisiones -> find_first("id = ".$maestroDivisiones -> division_id);
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"MAESTRO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$maestro -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(55,4,$division -> nombre,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(20,4,"No. NOMINA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$maestro -> id,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($maestro -> plantel == 'C'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(20,4,$plantel,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(40,4,"FEB - JUN 2009",1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(40,4,"No. DE SESIONES DE CLASE:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$horasTotal,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(10,4,"TURNO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(30,4,$maestro -> turno,1,0,'L',1);
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(16,4,"HORA",1,0,'C',1);
				$reporte -> Cell(29,4,"LUNES",1,0,'C',1);
				$reporte -> Cell(29,4,"MARTES",1,0,'C',1);
				$reporte -> Cell(29,4,"MIERCOLES",1,0,'C',1);
				$reporte -> Cell(29,4,"JUEVES",1,0,'C',1);
				$reporte -> Cell(29,4,"VIERNES",1,0,'C',1);
				$reporte -> Cell(29,4,"SABADO",1,0,'C',1);
				
				$posExtra=29;				
				$y=44;
				for($i=0;$i<14;$i++){	
					$pos=10;				
					if ($i == 2){
						$reporte -> SetFont('Arial','B',6);
						$reporte -> SetXY(10, $y+1);
						$reporte -> SetFillColor(0x99,0x99,0x99);					
						$reporte -> MultiCell(16,5,"8:50 \n 9:30" ,1,'C',1);
						$reporte -> SetXY(26, $y+1);
						$reporte -> MultiCell(174,5,"RECESO \n ",1,'C',1);
						$y = $y+11;
					}					
					$reporte -> SetFont('Arial','B',6);
					$reporte -> SetXY($pos, $y);
					$reporte -> SetFillColor(0x99,0x99,0x99);					
					$reporte -> MultiCell(16,5,$horas[$i],1,'C',1);					
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					$reporte -> SetFont('Arial','B',5);
					for($d=1;$d<7;$d++){
						if ($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}												
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$Horas1 = new Horas();
						if ($hora = $Horas1 -> find_first("maestro_id = ".$nomina." and dia = ".$d." and hora = $i+1")){
							$Grupos = new Grupos();
							$Salones = new Salones();
							$Materias = new MateriasHorarios();
							$grupo = $Grupos -> find_first("id = ".$hora -> grupo_id);
							$salon = $Salones -> find_first("id = ".$hora -> salon_id);
							$materia = $Materias -> find_first("id = ".$hora -> materia_id);
							
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,5,substr(trim($materia -> nombre),0,20)." \n ".trim($grupo -> nombre)." - ".trim($salon -> corto),1,'L',1);	
							$contador[$d]++;
						}else{
							$reporte -> SetFillColor(0xFF,0xFF,0xFF);
							$reporte -> MultiCell(29,5," \n ",1,'C',1);
						}
						
					}
					$y = $y+9;
				}
				$reporte -> SetFont('Arial','B',4);
				$pos=10;
				$reporte -> SetXY($pos, $y);
				$reporte -> SetFillColor(0x99,0x99,0x99);					
				$reporte -> MultiCell(16,4,"SESIONES / DIA \n JORNADA",1,'C',1);					
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetFont('Arial','B',5);
				
				$jornada ="- - -";
				$HorarioJornada = new HorarioJornada();
				if($horarioJornada = $HorarioJornada -> find_first("periodo = ".$this -> periodo." and maestro_id = ".$nomina)){
					for($d=1;$d<7;$d++){
						if ($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}
						
						if($d == 1)
							$jornada=$horarioJornada -> lunes;
						if($d == 2)
							$jornada=$horarioJornada -> martes;
						if($d == 3)
							$jornada=$horarioJornada -> miercoles;
						if($d == 4)
							$jornada=$horarioJornada -> jueves;
						if($d == 5)
							$jornada=$horarioJornada -> viernes;
						if($d == 6)
							$jornada=$horarioJornada -> sabado;					
						
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,2,$contador[$d]." hrs. \n ".$jornada,1,'C',1);
					}
				}else{
					for($d=1;$d<7;$d++){
						if ($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,4,$contador[$d]." hrs. \n NI",1,'C',1);
					}
				}
											
				$reporte -> Ln(9);				
				
				$reporte -> SetFont('Arial','B',7);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(190,4,"ACTIVIDADES PROGRAMA DE TRABAJO ACADÉMICO",0,0,'C',1);
				$reporte -> Ln();
				
				$reporte -> SetFont('Arial','B',5);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(10,4,"CLAVE",1,0,'C',1);
				$reporte -> Cell(170,4,"ACTIVIDAD",1,0,'C',1);
				$reporte -> Cell(10,4,"HORAS",1,0,'C',1);
				$reporte -> Ln();
				
				$ActividadMaestro = new ActividadMaestro();
				$Actividades = new Actividades();
				$Actividades -> find_first("id = 1");
				
				$reporte -> SetFont('Arial','B',5);
				$totalHoras = 0;
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(10,3,$Actividades -> clave,1,0,'C',1);
				$reporte -> Cell(170,3,$Actividades -> actividad,1,0,'C',1);
				$reporte -> Cell(10,3,$horasTotal,1,0,'C',1);
				$totalHoras = $totalHoras + $horasTotal;
				$reporte -> Ln();												
							
				$actividadMaestro = $ActividadMaestro -> find("maestro_id = ".$nomina);
				$color=1;				
				foreach($actividadMaestro as $am){
					$Actividades = new Actividades();
					$actividad = $Actividades -> find_first("id = ".$am -> actividad_id);					
					
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					
					$reporte -> Cell(10,3,$actividad -> clave,1,0,'C',1);
					$reporte -> Cell(170,3,$actividad -> actividad,1,0,'C',1);
					$reporte -> Cell(10,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				
				$ActividadMaestro = new ActividadMaestroEspecial();
				$actividadMaestro = $ActividadMaestro -> find("maestro_id = ".$nomina);
								
				foreach($actividadMaestro as $am){
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					$reporte -> Cell(10,3," - ",1,0,'C',1);
					$reporte -> Cell(170,3,utf8_decode($am -> nombre),1,0,'C',1);
					$reporte -> Cell(10,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);								
				$reporte -> Cell(150,3,"",1,0,'C',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(30,3,"TOTAL DE HORAS",1,0,'C',1);				
				$reporte -> Cell(10,3,$totalHoras,1,0,'C',1);
				$reporte -> Ln(15);		
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);		
				
				$reporte -> line(80,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> Ln(1);
				$reporte -> SetFont('Arial','B',8);
				$reporte -> Cell(190,4,$maestro -> nombre,0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',6);
				$reporte -> Cell(190,3,"FIRMA DE CONFORMIDAD",0,0,'C',1);				
				$reporte -> Ln(6);

				$reporte -> SetFont('Arial','B',9);								
				$reporte -> Cell(60,4,"ELABORÓ",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"Vo. Bo.",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"AUTORIZÓ",0,0,'C',1);
				$reporte -> Ln(10);
				$reporte -> line(20,$reporte -> GetY(),60,$reporte -> GetY());
				$reporte -> line(85,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> line(155,$reporte -> GetY(),195,$reporte -> GetY());
				$reporte -> Ln(1);
				
				$Maestros = new Maestros();
				$Divisiones = new Divisiones();
				$maestro1 = $Maestros ->find_first("id = ".$nomina);
				$maestroDivisiones = $MaestroDivisiones -> find_first("maestro_id = ".$maestro1 -> id);
				$division = $Divisiones -> find_first("id = ".$maestroDivisiones -> division_id);
				$Maestros = new Maestros();
				$maestro2 = $Maestros ->find_first("id = ".$division -> maestro_id);
				
				
				$reporte -> SetFont('Arial','B',8);
				$reporte -> Cell(60,4,$maestro2 -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(55,4,"ING. CARLOS JESAHEL VEGA GOMEZ",0,0,'C',1);
				$reporte -> Cell(15,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"ING. WILIBALDO RUIZ AREVALO",0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',6);
				$reporte -> Cell(60,4,"COORDINADOR ".$division -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"JEFATURA DE NIVEL TECNÓLOGO",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"SUBDIRECCIÓN DE OPERACIÓN ACADÉMICA",0,0,'C',1);
								
				$reporte -> Output("public/files/tgohorarios/".$nomina.".pdf");
				
				$this->redirect("public/files/tgohorarios/".$nomina.".pdf");
			}else{				
				//$this -> redirect("tgoprofesor/horarios");
				echo "no se encontro la nomina";
			}
		}
		
		function programa(){			
			$this -> set_response("view");					
			$this -> nomina = Session::get_data('registro');
			
			$Horas = new Horas();
			$this -> cuantasHoras = $Horas -> count("maestro_id = ".$this -> nomina);
		}
		
		function info(){
			echo phpinfo();
		}
		
		function convertirAAlumno($registro){
			$this -> valida();
			
			Session::set_data('temporal', Session::get("registro"));	// se pone en temporal la nomina del Prof o Coor		
			Session::set_data('registro', $registro);  //se asigna a la session el registro del alumno
						
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> find_first("registro = ".$registro);
			Session::set_data('nombre', $alumno -> nombre_completo); 
			Session::set_data('grupo', $alumno -> grupo); 
			Session::set_data('fecha', $alumno -> fecha_nacimiento);
			Session::set_data('turno', $alumno -> turno);
			Session::set_data('especialidad', $alumno -> especialidad);
			Session::set_data('nivel', $alumno -> semestre);
			Session::set_data('plantel', $alumno -> plantel);
			
			$this -> route_to('controller: tgoalumno','action: index');
		}
		
		function convertirAProfesor(){
			$this -> valida();
			
			Session::set_data('registro', Session::get("temporal"));
			Session::unset_data('temporal');
			$registro = Session::get("registro");
			
			
			$divisiones = new Divisiones();
			$division = $divisiones -> find_first("maestro_id=".$registro); 

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("id=".$registro);
			
			Session::set_data('division', $division -> id); 
			Session::set_data('nombre', $maestro -> nombre); 
			Session::set_data('coordinacion', $division -> nombre); 
			Session::set_data('div', $division -> clave); 				
			
			$this -> route_to('controller: tgoprofesor','action: tutorados');
		}
		
		
		function valida(){
			if((Session::get("tipo")=="PROFESOR") || (Session::get("tipo")=="COORDINADOR") || (Session::get("tipo")=="TUTOR")){
				return true;
			}
			$this -> redirect("general/ingresar");
			return true;
		}

	}
	
?>
