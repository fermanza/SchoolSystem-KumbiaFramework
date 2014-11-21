<?php
			
	class KardexController extends ApplicationController {
		
		public $antesAnterior=32010;
		public $anterior	= 12011;
		public $actual		= 32011;
		public $proximo		= 12012;
		
		function index(){
			$Alumnos = new Alumnos();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			
			$registro  = $this -> post("registro");
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			
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
			unset($this -> editable);
			$this -> editable = 2;
		} // function index()
		
		//function imprimir_kardex($registro = 530311, $rama = 1){
		function imprimir_kardex(){
			//$this -> valida();
			
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			//$registro = 531223;
			//$registro = 331249;
			//$registro = 331255;
			
			$registro = $this -> post('registroimprimir');
			if(!isset($registro)){
				$this->redirect("seguridad/terminarsesion");
				return;
			}
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			//$periodo = $this -> actual;
			
			$Alumnos = new Alumnos();
			
			if(!$alumno = $Alumnos -> get_relevant_info_from_student($registro)){
				$this->redirect("seguridad/terminarsesion");
				return;
			}
			
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			$carrera = $Carrera -> get_nombre_carrera($alumno -> carrera_id);
			$areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($alumno -> carrera_id, $alumno -> areadeformacion_id);
			
			$materiasArray	= $Materia -> get_all_giving_careerID($alumno -> carrera_id, $alumno -> areadeformacion_id);
			if( strtoupper($alumno->enPlan) == "PE07" ){
				$materiasAreaDeFormacion = $Materia -> get_materias_areadeformacion($alumno -> carrera_id, $alumno -> areadeformacion_id);
			}
			else if( strtoupper($alumno->enPlan) == "PE00" ){
				$materiasGenerales = $Materia -> get_materias_optativas_generales($alumno);
				$materiasEspecializantes = $Materia -> get_materias_optativas_especializantes($alumno);
			}
			
			$reporte = new FPDF();
			$reporte -> AddPage();
			
			//$reporte -> Image('http://ase.ceti.mx'.KUMBIA_PATH.'public/img/logoceti2.jpg', 16, 3,32,20);
			
			  $reporte -> SetX(50);
			    $reporte -> SetY(9);				
				$reporte -> SetFont('Arial','B',8);
     			$reporte -> MultiCell(0,3,utf8_decode("CLAVE SEP: 14DTI0001D "),0,'C',0);
				$reporte -> SetX(62);
				$reporte -> SetY(13);				
				$reporte -> SetFont('Arial','B',12);
				$reporte -> MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);
				$reporte -> SetY(17);
				$reporte -> SetX(15);
				$reporte -> SetFont('Arial','',7);
				$reporte -> MultiCell(0,3,"ORGANISMO PÚBLICO FEDERAL DESCENTRALIZADO",0,'C',0);				
				$reporte -> SetY(23);
				$reporte -> SetX(15);
				if(strtoupper($alumno -> enPlantel == "C"))
				$plantel = "COLOMOS";
			     else
				$plantel = "TONALA";
				$reporte -> SetFont('Arial','B',9);
				$reporte -> MultiCell(0,2,"PLANTEL ". $plantel,0,'C',0);
				$reporte -> SetY(26);
				$reporte -> SetX(15);
				$reporte -> SetFont('Arial','B',7);
				$reporte -> MultiCell(0,2,"NUEVA ESCOCIA 1885 COL. PROVIDENCIA 5TA. SECCIÓN, GUADALAJARA, JAL.",0,'C',0);
				
			
		
			$reporte -> Ln(4);
			if($alumno -> pago != 1){
				$reporte -> SetY(32);
				$reporte -> SetX(10);
				$reporte -> SetFont('Arial','B',8);
				$reporte -> MultiCell(0,2,"ALUMNO NO INSCRITO",0,'C',0);
			}
			$reporte -> Ln(5);
			
			$reporte -> SetFont('Arial','B',7);
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(16,4,"REGISTRO",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$reporte -> Cell(19,4,$alumno -> miReg,1,0,'C',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(12,4,"NOMBRE",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			//$nombres = iconv("latin1", "ISO-8859-1", $alumno -> vcNomAlu);
			//$apaterno = iconv("latin1", "ISO-8859-1", $alumno -> a_paterno);
			//$amaterno = iconv("latin1", "ISO-8859-1", $alumno -> a_materno);
			
			$reporte -> Cell(76,4," ".$alumno -> vcNomAlu,1,0,'L',1);
			//$reporte -> Cell(64,4," ".$nombres." ".$apaterno." ".$amaterno,1,0,'L',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(15,4,"CARRERA",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			//if( $areadeformacion -> nombreareaformacion != "" )
				//$reporte -> Cell(75,4,$carrera -> nombre." en ".$areadeformacion -> nombreareaformacion,1,0,'C',1);
			//else
				$reporte -> Cell(60,4,$carrera -> nombre,1,0,'C',1);
			
			//$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			//$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			//$reporte -> SetFillColor(0xF2,0x78,0x22);
			//$reporte -> Cell(5,4,"NO.",1,0,'C',1);
			//$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			//$reporte -> SetTextColor(0x00,0X00,0x00);
			//$reporte -> Cell(6,4,$areadeformacion -> nombreareaformacion,1,0,'C',1);
			
			$reporte -> Ln();
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(16,4,"NIVEL",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$reporte -> Cell(25,4,$Alumnos -> nivel($alumno -> enNivEdu),1,0,'C',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(18,4,"PERIODO",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$periodo1= substr($periodo,0,1);
			$periodo2= substr($periodo,1,4);
			if($periodo1==1){
				$fecha="FEB - JUN ".$periodo2;
			}
			else if($periodo1==3){
				$fecha="AGO - DIC ".$periodo2;
			}
			$reporte -> Cell(30,4,$fecha,1,0,'C',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(19,4,"PLANTEL",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$reporte -> Cell(23,4,$plantel,1,0,'C',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(12,4,"PLAN",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$reporte -> Cell(15,4,$alumno -> enPlan,1,0,'C',1);
			
			$reporte -> SetTextColor(0xFF,0XFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xCA,0x5B);
			$reporte -> SetFillColor(0xF2,0x78,0x22);
			$reporte -> Cell(15,4,"TURNO",1,0,'C',1);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0x00,0X00,0x00);
			$reporte -> Cell(25,4,$Alumnos -> get_turno($alumno -> enTurno),1,0,'C',1);
			
			$reporte -> SetFont('Arial','B',6);
			$reporte -> SetDrawColor(0xFF,0xFF,0xFF);
			$reporte -> SetFillColor(0xCC,0xCC,0xCC);
			$reporte -> SetY(50);
			$reporte -> SetX(10);
			
			// Aquí empiezan las calificaciones
			$reporte -> Cell(5,3,'NIV',1,0,'C',1);
			$reporte -> Cell(10,3,'CLAVE',1,0,'C',1);
			$reporte -> Cell(53,3,'MATERIA',1,0,'L',1);
			$reporte -> Cell(10,3,"PERIODO",1,0,'C',1);
			$reporte -> Cell(7,3,"TIPO",1,0,'C',1);
			$reporte -> Cell(6,3,"CAL",1,0,'C',1);
			$reporte -> Cell(6,3,"CRED",1,0,'C',1);
			$reporte -> SetY(50);
			$reporte -> SetX(112);
			//$reporte -> SetY(55);		
			
			$reporte -> Cell(5,3,'NIV',1,0,'C',1);
			$reporte -> Cell(10,3,'CLAVE',1,0,'C',1);
			$reporte -> Cell(53,3,'MATERIA',1,0,'L',1);
			$reporte -> Cell(10,3,"PERIODO",1,0,'C',1);
			$reporte -> Cell(7,3,"TIPO",1,0,'C',1);
			$reporte -> Cell(6,3,"CAL",1,0,'C',1);
			$reporte -> Cell(6,3,"CRED",1,0,'C',1);
			
			$reporte -> Ln(5);
			
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> SetDrawColor(0xFF,0xFF,0xFF);
			$reporte -> SetTextColor(0);
			
			$reporte -> SetFont('Arial','',6);
			$reporte -> SetX(10);
			$nivel = 1;
			$cont = 0;
			$cont1 = 0;
			//$creditos = 0;
			$KardexIng = new KardexIng();
			$CreditosEnKardex = $KardexIng -> get_ncreditos($alumno);
			foreach($materiasArray as $m){
				if( $m -> semestre == 0 )
					continue;
				$cont++;
				if($m -> semestre == 5 and $nivel == 4){
				//if($m -> semestre >= 5){
					$reporte -> SetY(50);
				}
				if($nivel != $m -> semestre){											
					$nivel = $m -> semestre;
					$reporte -> Ln(5);
				}
				if ($m -> semestre >= 5){
					
					$reporte -> SetX(112);
				}
				if($cont%2){
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				}else{
					$reporte -> SetFillColor(0xEE,0xEE,0xEE);
				}
				$reporte -> Cell(5,3,$m -> semestre,1,0,'C',1);
				$reporte -> Cell(10,3,$m -> clave,1,0,'C',1);
				$reporte -> Cell(53,3,substr($m -> nombre,0,48),1,0,'L',1);
				if($kardexIng = $KardexIng -> find_first("registro = ".$registro." and clavemat = '".$m -> clave."' and activo = '1'")){						
					$reporte -> Cell(10,3,$kardexIng -> periodo,1,0,'C',1);
					$reporte -> Cell(7,3,$kardexIng -> tipo_de_ex,1,0,'C',1);
					$reporte -> Cell(6,3,$kardexIng -> promedio,1,0,'C',1);
					$reporte -> Cell(6,3,trim($m -> creditos),1,0,'C',1);
					$cont1++;
				}else{
					$reporte -> Cell(10,3,"",1,0,'C',1);
					$reporte -> Cell(7,3,"",1,0,'C',1);
					$reporte -> Cell(6,3,"",1,0,'C',1);
					$reporte -> Cell(6,3,"",1,0,'C',1);
				}
				$reporte -> Ln();
			} // foreach($materiasArray as $m)
			
			
				$KardexIng = new KardexIng();
			if(count($materiasAreaDeFormacion) > 0){
				$reporte -> GetX();
				$reporte -> GetY();
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> SetFont('Arial','',6);
				$reporte -> SetX(127);
				$reporte -> SetFillColor(0xEE,0xEE,0xEE);
				$reporte -> Cell(80,3,'OPTATIVAS',1,0,'C',1);
				$reporte -> Ln();
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				//$materias2 = $Materias ->find("division_id=100 order by clave asc");
				//foreach($materias2 as $t){
				foreach($materiasAreaDeFormacion as $t){
					if($kardexIng = $KardexIng ->find_first("registro=".$alumno -> miReg." and clavemat='".$t->clave."' and activo = '1'")){
						$reporte -> SetX(127);
						$reporte -> Cell(10,3,$t -> clave,1,0,'C',1);
						$reporte -> Cell(48,3,substr($t -> nombre,0,35),1,0,'L',1);
						$reporte -> Cell(7,3,$kardexIng -> periodo,1,0,'C',1);
						$reporte -> Cell(5,3,$kardexIng -> tipo_de_ex,1,0,'C',1);
						$reporte -> Cell(6,3,$kardexIng -> promedio,1,0,'C',1);
						$reporte -> Cell(6,3,$t -> creditos,1,0,'C',1); 
						$reporte -> Ln();
					}
				}
			} // if(count($materiasAreaDeFormacion) > 0)
			else{
				if(count($materiasGenerales) > 0){
					$reporte -> GetX();
					$reporte -> GetY();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> SetFont('Arial','',6);
					$reporte -> SetX(127);
					$reporte -> SetFillColor(0xEE,0xEE,0xEE);
					$reporte -> Cell(80,3,'OPTATIVAS GENERALES',1,0,'C',1);
					$reporte -> Ln();
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					//$materias2 = $Materias ->find("division_id=100 order by clave asc");
					//foreach($materias2 as $t){
					foreach($materiasGenerales as $t){
						if($kardexIng = $KardexIng ->find_first("registro=".$alumno -> miReg." and clavemat='".$t->clave."' and activo = '1'")){
							$reporte -> SetX(127);
							$reporte -> Cell(10,3,$t -> clave,1,0,'C',1);
							$reporte -> Cell(48,3,substr($t -> nombre,0,35),1,0,'L',1);
							$reporte -> Cell(7,3,$kardexIng -> periodo,1,0,'C',1);
							$reporte -> Cell(5,3,$kardexIng -> tipo_de_ex,1,0,'C',1);
							$reporte -> Cell(6,3,$kardexIng -> promedio,1,0,'C',1);
							$reporte -> Cell(6,3,$t -> creditos,1,0,'C',1); 
							$reporte -> Ln();
						}
					}
				} // if(count($materiasAreaDeFormacion) > 0)
				if(count($materiasEspecializantes) > 0){
					$reporte -> GetX();
					$reporte -> GetY();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> SetFont('Arial','',6);
					$reporte -> SetX(127);
					$reporte -> SetFillColor(0xEE,0xEE,0xEE);
					$reporte -> Cell(80,3,'OPTATIVAS ESPECIALIZANTES',1,0,'C',1);
					$reporte -> Ln();
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					//$materias2 = $Materias ->find("division_id=100 order by clave asc");
					//foreach($materias2 as $t){
					foreach($materiasEspecializantes as $t){
						if($kardexIng = $KardexIng ->find_first("registro=".$alumno -> miReg." and clavemat='".$t->clave."' and activo = '1'")){
							$reporte -> SetX(127);
							$reporte -> Cell(10,3,$t -> clave,1,0,'C',1);
							$reporte -> Cell(48,3,substr($t -> nombre,0,35),1,0,'L',1);
							$reporte -> Cell(7,3,$kardexIng -> periodo,1,0,'C',1);
							$reporte -> Cell(5,3,$kardexIng -> tipo_de_ex,1,0,'C',1);
							$reporte -> Cell(6,3,$kardexIng -> promedio,1,0,'C',1);
							$reporte -> Cell(6,3,$t -> creditos,1,0,'C',1); 
							$reporte -> Ln();
						}
					}
				} // if(count($materiasAreaDeFormacion) > 0)
			}
				
				$reporte -> Ln();
		    	$reporte -> SetDrawColor(0x00,0x00,0x00);	
				$reporte -> line(138,205,207,205);
				$reporte -> SetDrawColor(0xFF,0xFF,0xFF);				
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetXY(185,207);			
				$reporte -> Cell(30,3,'PROMEDIO: '.$KardexIng -> get_average_from_kardex($alumno -> miReg),1,0,'l',1);
				$reporte -> SetXY(185,210);
				$reporte -> Cell(30,3,"CRÉDITOS: ".$CreditosEnKardex,1,0,'l',1);
			
				$reporte -> SetDrawColor(0x00,0x00,0x00);				
				$reporte -> line(95,220,20,220);
				$reporte -> SetDrawColor(0xFF,0xFF,0xFF);				
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetXY(10,222);
				if(strtoupper($alumno -> enPlantel == "C"))
				$reporte -> Cell(95,3,'ING. SALVADOR TRINIDAD PEREZ',1,0,'C',1);
			    else
				$reporte -> Cell(95,3,'ANA MARCELA LOPEZ ESTRADA',1,0,'C',1);
				$reporte -> Ln();
				$reporte -> SetFont('Arial','',6);
				$reporte -> SetX(10);
				$reporte -> Cell(95,3,'JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO',1,0,'C',1);
				$reporte -> Ln();
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetX(10);
				if(strtoupper($alumno -> enPlantel == "C"))
				$reporte -> Cell(95,3,'PLANTEL COLOMOS',1,0,'C',1);
			    else
				$reporte -> Cell(95,3,'PLANTEL TONALA',1,0,'C',1);				
				$reporte -> Ln();
			
			$reporte -> SetFont('Arial','B',5);
				$reporte -> SetXY(8,260);
				$reporte -> Cell(40,3,'TIPOS DE EXAMEN:',1,0,'L',1);				
				$reporte -> Ln(4);
				$reporte -> SetFont('Arial','',4);
				$reporte -> Cell(25,3,'D: ORDINARIO',1,0,'L',1);
				$reporte -> Cell(25,3,utf8_decode('ER: REGULARIZACION'),1,0,'L',1);				
				//$reporte -> Ln();
				$reporte -> Cell(25,3,'EE: EXTRAORDINARIO',1,0,'L',1);
				$reporte -> Ln();
				$reporte -> Cell(25,3,'ETS: TITULO DE SUFICIENCIA',1,0,'L',1);
			
				$reporte -> Cell(25,3,'NIV: NIVELACION',1,0,'L',1);
				$reporte -> Cell(25,3,utf8_decode('ACR: ACREDITACION'),1,0,'L',1);
				$reporte -> Ln();
				$reporte -> Cell(25,3,'CONV: CONVALIDACION',1,0,'L',1);
				//$reporte -> Ln();
				$reporte -> Cell(25,3,'EDP: DERECHO DE PASANTE',1,0,'L',1);				
				
				$reporte -> Cell(25,3,'EQUI: EQUIVALENCIA',1,0,'L',1);
				$reporte -> Ln();
				$reporte -> Cell(25,3,utf8_decode('REV: REVALIDACION'),1,0,'L',1);
				//$reporte -> Ln();
				$reporte -> Cell(25,3,'EG: EXAMEN GLOBAL',1,0,'L',1);
			
			$reporte -> Ln();
			
			$unixtimestamp = $KardexIng -> mktime();
			$reporte -> Output("public/files/kardex_ingenieria/k_".$registro."-".$unixtimestamp.".pdf");				
			$this->redirect("public/files/kardex_ingenieria/k_".$registro."-".$unixtimestamp.".pdf");
		} // function imprimir_kardex()
	}
?>