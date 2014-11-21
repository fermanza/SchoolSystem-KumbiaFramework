<?php
	class SalonesController extends ApplicationController {
		function index(){
		
		}
		
		function infraestructura(){
		
		}
		
		function infraestructuraXLS ($division_id = 0, $dia = 0){			
			$Salones = new Salones();
			$this -> Division = new Divisiones();
			if($division_id == 0 || $dia == 0){
				$division_id = $this -> post('division_id');
				$dia = $this -> post('dia');
			}
			if($this -> salones = $Salones -> find("divisiones_id = ".$division_id, "order: nombre ASC")){
				$this -> Division -> find_first($division_id);
				$this -> dia = $dia;
				$this -> division_id = $division_id;
				$this -> set_response("view");
			}else{
				Flash::error('No se encontraron salones para esta division');
			}
			
		}
		
		function divisiones(){
			$this -> set_response('view');
			$this -> divisiones_id = $this -> post('divisiones_id');
			echo $this -> render_partial("salones");
		}
		
		public function horarioPDF($salon=0){	
			if($salon == 0){
				$salon = $this -> post('salon');
			}
			
			$this -> set_response("view");
			
			$Salon = new Salones();
			$Salon2 = new Salones();
			$Horarios = new Horarios();
			$Horas = new Horas();
			$Maestros = new Maestros();
			$Periodo = new Periodos();
			$Divisiones = new Divisiones();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$contador= array (0,0,0,0,0,0,0);								
			
			$horas = array("7:00 \n 7:55","7:55 \n 8:50","9:30 \n 10:25","10:25 \n 11:20","11:20 \n 12:15","12:15 \n 13:10","13:10 \n 14:05","14:05 \n 15:00","15:00 \n 15:55","15:55 \n 16:50","16:50 \n 17:45","17:45 \n 18:40","18:40 \n 19:35","19:35 \n 20:30"); 
			
			if ($Salon -> find_first($salon)){				
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
				$reporte -> SetFont('Arial','B',8);
				$reporte -> MultiCell(0,2,"HORARIO DE SALON, TALLER O LABORATORIO",0,'C',0);			
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				$Divisiones -> find_first($Salon -> divisiones_id);
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"NOMBRE:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$Salon -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(60,4,$Divisiones -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"MODULO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$Salon -> modulo,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($Salon -> plantel == 'C'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(30,4,$plantel,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(10,4,"TIPO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$Salon -> tipo,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(25,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(45,4,$Periodo -> nombre_corto,1,0,'L',1);
				
				$reporte -> SetFillColor(0x99,0x99,0x99);				
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
						$hora = new Horas();
						if ($hora -> find_first("salon_id = ".$Salon -> id." and dia = ".$d." and hora = $i+1 and periodo =".$periodo)){
							$Grupos = new Grupos();
							$Salones = new Salones();
							$Materias = new MateriasHorarios();
							$grupo = $Grupos -> find_first("id = ".$hora -> grupo_id);
							$Maestros -> find_first($hora -> maestro_id);
							$materia = $Materias -> find_first("id = ".$hora -> materia_id);
							
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,5,substr(trim($materia -> nombre_corto),0,15)." - ".trim($grupo -> nombre)." \n ".substr(trim($Maestros -> nombre),0,15),1,'L',1);	
							$contador[$d]++;
						}else{
							$reporte -> SetFillColor(0xFF,0xFF,0xFF);
							$reporte -> MultiCell(29,5," \n ",1,'C',1);							
						}
					}
					$y = $y+9;
				}
				$reporte -> Ln(15);		
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);		
							
				$reporte -> Ln(6);

				$reporte -> SetFont('Arial','B',9);								
				$reporte -> Cell(30,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"ELABORÓ",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"Vo. Bo.",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);				
				$reporte -> Ln(10);
				$reporte -> line(50,$reporte -> GetY(),90,$reporte -> GetY());
				$reporte -> line(115,$reporte -> GetY(),160,$reporte -> GetY());				
				$reporte -> Ln(1);
												
				
				$reporte -> SetFont('Arial','B',8);
				$reporte -> Cell(60,4,'',0,0,'C',1);
				$reporte -> Cell(40,4,"",0,0,'C',1);
				$reporte -> Cell(55,4,"ING. CARLOS JESAHEL VEGA GOMEZ",0,0,'C',1);
				$reporte -> Cell(15,4,"",0,0,'C',1);				
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',6);
				$reporte -> Cell(30,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"COORDINADOR ".$Divisiones -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"JEFATURA DE NIVEL TECNÓLOGO",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);				
				$reporte -> Ln(12);
				$reporte -> Cell(60,4,"REVISIÓN 3",0,0,'C',1);
				$reporte -> Cell(70,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"FR-01-DAC-NT-PO-012",0,0,'C',1);
				
				$reporte -> Output("public/files/tgohorarios/".$Salon -> id.".pdf");
				
				$this -> redirect("public/files/tgohorarios/".$Salon -> id.".pdf");
			}else{				
				//$this -> redirect("tgoprofesor/horarios");
				echo "no se encontro la nomina";
			}
		}
	}
?>