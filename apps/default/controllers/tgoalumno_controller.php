<?php
			
	class TgoalumnoController extends ApplicationController {
		
		function index(){
			$this -> valida();			
		}
		
		function informacion(){
			$this -> valida();
			$this -> ajax = 0;
			echo $this -> render_partial("info");
			echo '<br> <div id="encabezado">'.img_tag("adorno.gif").'&nbsp; INFORMACION DEL ALUMNO</div>';
			echo $this -> render_partial('informacion');
		}
		
		function actualizarInformacion(){
			$this -> set_response('view');
			$this -> ajax = 1;
			$Alumnos = new AlumnosInformacion();
			if($Alumnos -> find_first('registro = '.Session::get_data('registro'))){
			
			}else{
				$Alumnos = new AlumnosInformacion();
			}
			$Alumnos -> registro = Session::get_data('registro');
			$Alumnos -> nombres = utf8_decode($this -> post('nombres','upper'));			
			$Alumnos -> paterno = utf8_decode($this -> post('paterno','upper'));
			$Alumnos -> materno = utf8_decode($this -> post('materno','upper'));
			$Alumnos -> direccion = utf8_decode($this -> post('direccion','upper'));
			$Alumnos -> colonia = utf8_decode($this -> post('colonia','upper'));
			$Alumnos -> municipio = utf8_decode($this -> post('municipio','upper'));
			$Alumnos -> cp = utf8_decode($this -> post('cp','upper'));			
			$Alumnos -> curp = utf8_decode($this -> post('curp','upper'));
			$Alumnos -> telefono = $this -> post('telefono');
			$Alumnos -> celular = $this -> post('celular');
			$Alumnos -> sangre = $this -> post('sangre');
			$Alumnos -> genero = $this -> post('genero');
			$Alumnos -> email = $this -> post('email','upper');
			$Alumnos -> fecha_nacimiento = $this -> post('fecha_nacimiento');
			if($Alumnos -> save()){
				Flash::success('Tu Informaci&oacute;n ha sido guardada correctamente');
			}else{
				Flash::error('Tu Informaci&oacute;n no ha podido ser guardada');
			}
			echo $this -> render_partial('informacion');
		}
		
		function tiras(){
			$this -> valida();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> registro = Session::get_data('registro');
			
			$Calificaciones = new Calificaciones();
			$this -> calificaciones = $Calificaciones -> find("periodo = ".$periodo." and registro = ".$this -> registro);
		}

		function horario(){
			$this -> valida();
			$this -> registro = Session::get_data('registro');			
			$Calificaciones = new Calificaciones();			
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$calificaciones = $Calificaciones -> find("periodo = ".$periodo." and registro = ".$this -> registro);			
			for($h=1;$h<15;$h++){
					for($d=1;$d<7;$d++){
						$this -> materia[$d][$h]="";
						$this -> grupo[$d][$h]="";
						$this -> aula[$d][$h]="";
						$this -> edificio[$d][$h]="";
					}					
				}			
			foreach($calificaciones as $c){
				$Horas = new Horas();
				$Horarios = new Horarios();
				$Grupos = new Grupos();
				$Salones = new Salones();				
				$MateriasHorarios = new MateriasHorarios();
				
				if($h = $Horarios -> find_first("id = ".$c -> horario_id)){
					if($m = $MateriasHorarios -> find_first("id = '".$h -> materia_id."'")){
						if($horas = $Horas -> find("horario_id = ".$c -> horario_id)){
							foreach($horas as $h){
								$this -> materia[$h -> dia][$h->hora]=$m -> nombre;
								$g = $Grupos -> find_first("id = ".$h -> grupo_id);
								$this -> grupo[$h -> dia][$h->hora]=$g -> nombre;
								$s = $Salones -> find_first("id = ".$h -> salon_id);
								$this -> salon[$h -> dia][$h->hora]=$s -> nombre;
								$this -> edificio[$h -> dia][$h->hora]=$s -> modulo;
							}
						}
					}
				}
			}			
		}
		
		function calificaciones($var=0){
			$this -> valida();
			$this -> registro = Session::get_data('registro');
			
			$Calificaciones = new Calificaciones();
			$Periodo = new Periodos();
			$Extras = new Extras();
			
			$Periodo -> find_first("activo = 1");
			$this -> calificaciones = $Calificaciones -> find("periodo = ".$Periodo -> periodo." and registro = ".$this -> registro);			
			$this -> extras = $Extras -> find("registro = ".$this -> registro." and periodo = ".$Periodo -> periodo);
		}
		
		function calificacionesPDF(){
			$this -> valida();
			$this -> registro = Session::get_data('registro');
						
			$MateriasHorarios = new MateriasHorarios();
			$Calificaciones = new Calificaciones();			
			$Horarios = new Horarios();			
			$Maestros = new Maestros();
			$Alumno = new Alumnos();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$cont = 0;

			$calificaciones = $Calificaciones -> find("periodo = ".$periodo." and registro = ".$this -> registro);

			$Alumno -> find_first("registro = ".$this -> registro);
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
						
			$reporte -> SetDrawColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(190,6,"REPORTE DE CALIFICACIONES",1,0,'C',1);
			$reporte -> Ln(8);
			
			$reporte -> SetFont('Arial','B',6);
			$reporte -> SetDrawColor(0x00,0x00,0x00);
			$reporte -> SetFillColor(0x99,0x99,0x99);
			$reporte -> Cell(20,4,"REGRISTRO",1,0,'C',1);				
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$this -> registro,1,0,'C',1);
			$reporte -> SetFillColor(0x99,0x99,0x99);
			$reporte -> Cell(20,4,"NOMBRE",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(80,4,$Alumno -> nombre_completo,1,0,'C',1);
			$reporte -> SetFillColor(0x99,0x99,0x99);
			$reporte -> Cell(30,4,"NIVEL Y GRUPO",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(20,4,$Alumno -> grupo,1,0,'C',1);
			
			$reporte -> Ln(8);
			$reporte -> SetFillColor(0x99,0x99,0x99);
			$reporte -> SetFont('Arial','B',4);
			$reporte -> Cell(8,3,"ACTA",1,0,'C',1);
			$reporte -> Cell(8,3,"CLAVE",1,0,'C',1);
			$reporte -> Cell(40,3,"MATERIA",1,0,'C',1);
			$reporte -> Cell(45,3,"PROFESOR",1,0,'C',1);
			$reporte -> Cell(8,3,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,3,"CALIFICACIÓN",1,0,'C',1);
			$reporte -> Cell(8,3,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,3,"CALIFICACIÓN",1,0,'C',1);
			$reporte -> Cell(8,3,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,3,"CALIFICACIÓN",1,0,'C',1);
			$reporte -> Cell(8,3,"FALTAS",1,0,'C',1);
			$reporte -> Cell(12,3,"CALIFICACIÓN",1,0,'C',1);
			$reporte -> Cell(9,3,"SITUACIÓN",1,0,'C',1);	

			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			
			$reporte -> Output("public/files/alumnosCalificaciones/".$this -> registro.".pdf");				
			$this->redirect("public/files/alumnosCalificaciones/".$this -> registro.".pdf");		
			
		}
		
		
		function kardex(){
			$this -> valida();
			$this -> registro = Session::get_data('registro');
			
			$Kardex = new Kardex();
			$this -> kardex = $Kardex -> find("registro = ".$this -> registro, "order: nivel asc, clave");
		}
		
		function evaluacion(){
			$this -> valida();
		}
		
		function apoyo(){
			$this -> valida();
			$this -> registro = Session::get_data('registro');
			$Ticket = new Ticket();			
			$this -> tickets = $Ticket -> find("usuario_id = ".$this -> registro);
		}
		
		function generarTicket(){
			$this -> set_response("view");
			$this -> registro = Session::get_data('registro');
			
			$contenido = "";
			$Ticket = new Ticket();
						
			for($i=1; $i<14; $i++){
				if($this -> has_post('opcion'.$i)){
					$contenido = $contenido.'<b>'.$this -> post('opcion'.$i).'</b><br>';
				}
			}			
			$observaciones = $this -> post("observaciones");
			$contenido = $contenido."<br>".$observaciones;
			$Alumnos = new Alumnos();
			$Divisiones = new Divisiones();
			$Usuarios = new Usuarios();
			if($Alumnos -> find_first("registro = ".$this -> registro)){
				if($Divisiones -> find_first($Alumnos -> especialidad)){
					if($Usuarios -> find_first("usuario = ".$Divisiones -> maestro_id)){
						$Ticket -> dirigido_a = $Usuarios -> id;
					}else{
						$Ticket -> dirigido_a = '0';
					}
				}else{
					$Ticket -> dirigido_a = '0';
				}
			}else{
				$Ticket -> dirigido_a = '0';
			}			
			$Ticket -> usuario_id = $this -> registro;
			$Ticket -> contenido = utf8_decode($contenido);
			$Ticket -> estado = "Abierto";			
			$Ticket -> respuesta = "el ticket esta abierto";
			if($Ticket -> save()){
				echo "<br/><br/>";
				Flash::success('Se guardo el Ticket');
				echo "<br/>";
			}
			?>
			<table cellpadding="1" cellspacing="1" class="bordeazul" width="100%">
				<tr>
					<th class="azul">Ticket Abierto</th>
				</tr>
				<tr>
					<td><?= $contenido; ?></td>
				</tr>
			</table>
			<br/><br/><br/>
			<?php						
		}
		
		function actualizar_datos(){
			$this -> set_response("view");
		}
		
		function guardarActualizacionDatos(){
			$Alumnos = new Alumnos();
			if($Alumnos -> find_first("registro = ".Session::get_data('registro'))){							
				/*$Alumnos -> nombre_completo=$this -> post("");
				$Alumnos -> fecha_nacimiento;
				$Alumnos -> sexo;
				$Alumnos -> telefono;	
				$Alumnos -> celular;
				$Alumnos -> email;
				$Alumnos -> curp;
				if ($Alumnos -> save_from_request()){*/
					Flash::success('Se guardo la actualizaci&oacute;n de datos de '.$Alumnos -> nombre_completo);	
				/*}else{
					Flash::success('No se guardo la actualizaci&oacute;n');	
				}*/
			}else{
				Flash::error('No se guardo la actualizaci&oacute;n');	
			}
			
			$this -> set_response("view");
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}
		
		function ficha(){
			$this -> render_partial("info");
			echo '<center class="naranja"><h3>Por el momento no hay ninguna ficha disponible</h3></center>';
		}
		function fichaPago(){
			$this -> set_response("view");			
			/*
			$registro = Session::get_data('registro');
			
			$Alumnos = new Alumnos();
			$Alumnos -> find_first("registro = ".$registro);
			
			if($datos["situacion"] == "NUEVO INGRESO"){
			//if($registro > 910000){
				$opcion = 100;
			}
			else{
				$opcion = 200;
			}
			$referencia_sin_dv = $opcion.$registro."109";
			$referenciaPa_sin_dv = "300".$registro."109";
			$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
			$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);
			
			$reporte = new FPDF();
			$reporte -> Open();
			$reporte -> AddFont('Verdana','','verdana.php');
			
			for($i=0; $i<2; $i++ ){		
				if ($i == 0){
					$copia = "ALUMNO";
				}else{
					$copia = "BANCO";
				}						
				$reporte -> AddPage();												
				$reporte -> Ln();
				
				$reporte -> Image('http://ase.ceti.mx/tecnologo/img/formatoFichaPago.jpg', 5, 20, 200, 220);
				$reporte -> SetFont('Verdana','',10);
				
				$reporte -> SetX(45);
				$reporte -> SetY(37);
				$reporte -> MultiCell(188,3,$referencia,0,'R',0);
				
				$reporte -> SetFont('Verdana','',8);
				
				$reporte -> SetX(50);
				$reporte -> SetY(44);
				$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
				
				$reporte -> SetFont('Verdana','',7);
				
				$reporte -> Ln();
				$reporte -> SetX(2);
				$reporte -> SetY(41);
				
				$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetX(20);
				$reporte -> MultiCell(0,3,"03 / FEB / 2009",0,'L',0);
				
				if($opcion == 100){				
					$reporte -> SetY(64);
					$reporte -> MultiCell(100,3,"NUEVO INGRESO TECNÓLOGO",0,'L',0);
					$reporte -> SetY(67);
					$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
					$reporte -> SetY(70);
					$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
					$reporte -> SetY(64);
					$reporte -> MultiCell(80,3,"$750.00",0,'R',0);
					$reporte -> SetY(67);
					$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
					$reporte -> SetY(70);
					$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,"$957.00",0,'R',0);
				}
				else{
					$reporte -> SetY(64);
					$reporte -> MultiCell(100,3,"REINSCRIPCION TECNÓLOGO",0,'L',0);
								
					$reporte -> SetY(64);
					$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
				}
				
				$reporte -> SetY(101);
				$reporte -> MultiCell(179,3,$copia,0,'C',0);			
				
				$reporte -> Ln();
				
				$reporte -> SetFont('Verdana','',10);
				
				$reporte -> SetX(45);
				$reporte -> SetY(156);			
				$reporte -> MultiCell(188,3,$referenciaPa,0,'R',0);
				
				$reporte -> SetFont('Verdana','',8);
				
				$reporte -> SetX(50);
				$reporte -> SetY(163);
				$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
				
				$reporte -> SetFont('Verdana','',7);
				
				$reporte -> Ln();
				$reporte -> SetX(2);
				$reporte -> SetY(160);
				
				$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetX(20);
				$reporte -> MultiCell(0,3,"03 / FEB / 2009",0,'L',0);
							
				$reporte -> SetY(184);
				$reporte -> MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
				$reporte -> SetY(187);
				$reporte -> MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
				$reporte -> SetY(187);
				$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
				
				$reporte -> SetY(195);
				$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
	
				$reporte -> SetY(220);
				$reporte -> MultiCell(179,3,$copia,0,'C',0);
			}
			$reporte -> Output("public/files/fichas/".$registro.".pdf");
			
			$this->redirect("files/fichas/".$registro.".pdf");
			
			echo '<center class="naranja"><h3>Por el momento no hay ninguna ficha disponible</h3></center>';*/
		}
		
		function digitoVerificador($referencia){
			$tmp = "";
			$temporal = "";
			$x = 2;
			$suma = 0;
			for($i = strlen($referencia)-1;$i>=0;$i--){
				$numero = substr($referencia,$i,1)*$x;
				$tmp = " ". $numero . $tmp;
				while($numero>=10){
					$tempo = 0;
					for($k=0;$k<strlen($numero);$k++){
						$tempo += substr($numero,$k,1);
					}
					$numero = $tempo;
				}
				$temporal = $numero . $temporal;
				$suma = $suma + $numero;
				if($x==2){ $x=1; continue;}
				if($x==1){ $x=2; continue;}
			}
			$residuo = $suma % 10;
		
			$digito = 10 - $residuo;
			
			if($digito==10) $digito = 0;
		
			return $digito;
		}
		
		function valida(){
			if((Session::get("tipo")=="ALUMNO") || (Session::get("tipo")=="PROFESOR") || (Session::get("tipo")=="COORDINADOR")  || (Session::get("tipo")=="TUTOR")){
				return true;
			}
			//$this -> redirect("general/ingresar");
			return true;
		}
	}
	
?>
