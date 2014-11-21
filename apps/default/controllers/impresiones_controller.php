<?php
	class ImpresionesController extends ApplicationController {
		
		function imprimiendo_certificado(){
			$this -> render_partial("buscar_alumno_impresiones");
		}
		
		function imprimir_certificado($registro = 831265,$numero=0,$curp='',$tipo = 'T',$inputField=''){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			
			$nombre = $this -> post("nombreAlumno");
			$registro = $this -> post("registroAlumno");
			
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			//echo $registro." ".$corregirKardexExito;
			if( ($nombre == "" && $registro == "") || 
					$nombre == "Nombre Alumno" && $registro == "Registro Alumno" ){ // Ademas checar que el registro sea integer...
				$this -> redirect("impresiones/imprimiendo_certificado");
				return;
			}
			
			$KardexIng = new Kardexing();
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			// Si ingreso un registro, usar solo el registro y verificar que sea integer.
			if( $registro != "" && $registro != "Registro Alumno" )
				$query =  "miReg = '".$registro."'";
			else
				$query =  "vcNomAlu like '%".$nombre."%'";
			
			$alumno = $Alumnos -> find_first($query);
			if( !isset($alumno -> miReg) ){
				$this -> redirect("impresiones/imprimiendo_certificado");
				return;
			}
			$registro = $alumno -> miReg;
			
			if($this -> has_post('curp'))
				$curp = $this -> post('curp');
			if($this -> has_post('numero'))
				$numero = $this -> post('numero');
			if($this -> has_post('tipo'))
				$tipo = $this -> post('tipo');
			if($this -> has_post('inputField'))
				$inputField = $this -> post('inputField');
			if($this -> has_post('pasante'))
				$pasante = $this -> post('pasante');
			
			$Periodos = new Periodos();
			list($dia, $mes, $anio) = $Periodos->fecha_dia_mes_anio($inputField);
			$dia = $this->decena($dia);
			$mes = $this->letra_mes($mes);
			$anio = $this->num2letras($anio);
			
			
			$this -> materias = $KardexIng -> get_materias_para_agregar($alumno);
			
			if(isset($alumno->miReg)){
				if(($alumno -> stSit=='EG')||($alumno ->stSit=='BD')||($alumno ->stSit=='BT')){
					$Egresados = new Egresados();
					$tipokardex="egresado";
				}
				else{
					$Xalumnocursos = new Xalumnocursos();
				}
				
				$KardexIng = new KardexIng();
				$Areadeformacion = new Areadeformacion();
				$Areadeformacion -> find_first($alumno -> areadeformacion_id);
				setlocale(LC_ALL,'es_MX'); 
				$Carrera = new Carrera();
				$carrera_y_areaformacion = $Carrera->get_nombre_carrera_and_areadeformacion_($alumno);
				//$carrera_y_areaformacion2 =  mb_strtoupper($carrera_y_areaformacion,'UTF-8');
				$carrera_y_areaformacion2 = strtoupper($carrera_y_areaformacion);
				//if($kardex = $Kardex -> find("registro = ".$registro, "order: periodo ASC, clave ASC")){
				if($materias = $Materia -> get_all_giving_alumno($alumno)){
					$reporte = new FPDF('P','mm','Legal');
					///QUERY PARA REVISAR ULTIMO PERIODO CURSADO
					$periodos = $KardexIng -> get_periodosKardex($alumno->miReg);
					$periodoMenor = $KardexIng -> get_periodoMenor($periodos);
					$periodoMayor = $KardexIng -> get_periodoMayor($periodos);
					
					if ($alumno -> plan=='PE07'){
						$totalcreditos = $Materias -> sum("creditos", 
								"conditions: plan='".$alumno -> plan."' and especialidad_id= ".$alumno -> especialidad.
								" and nivel !=0 and rama in (1,0) group by especialidad_id order by nivel ASC, clave ASC");
					}					
					//$reporte -> SetAutoPageBreak(1);
					$reporte -> AddPage();						
					$reporte -> SetMargins(0,0,0);
					$reporte -> SetXY(0,30);
					$reporte -> SetFont('Arial','B',9);
					
					if($tipo == "P"){
						$reporte -> MultiCell(0,3,"CERTIFICADO DE ESTUDIOS",0,'C',0);
					}else if($tipo == "T"){
						$reporte -> MultiCell(0,3,"CERTIFICADO DE TERMINACION DE ESTUDIOS",0,'C',0);
					}else if($tipo == "D"){
						$reporte -> MultiCell(0,3,"CERTIFICADO DE TERMINACIÓN DE ESTUDIOS",0,'C',0);
					}else{
						$reporte -> MultiCell(0,3,"ERROR",0,'C',0);
					}
					if ($tipo == "D"){
						$reporte -> SetXY(172,30);
						$reporte -> SetFont('Arial','',9);
						$reporte -> MultiCell(0,3,"DUPLICADO",0,'L',0);
					}
					$reporte -> SetFont('Arial','',9);
					$reporte -> SetXY(68,49);
					$reporte -> MultiCell(0,3,"14DNT0001P",0,'L',0);
					$reporte -> SetXY(68,56);
					$nombres = iconv("latin1", "ISO-8859-1", $alumno -> vcNomAlu);
					//$apaterno = iconv("latin1", "ISO-8859-1", $alumno -> a_paterno);
					//$amaterno = iconv("latin1", "ISO-8859-1", $alumno -> a_materno);
					//$reporte -> MultiCell(0,3,$nombres." ".$apaterno." ".$amaterno,0,'L',0);
					$reporte -> MultiCell(0,3,$nombres,0,'L',0);
					$reporte -> SetXY(30,63);
					if($tipo == "P"){
						$reporte -> MultiCell(0,3,"PARCIALMENTE",0,'L',0);
					}elseif($tipo == "T"){
						$reporte -> MultiCell(0,3,"TOTALMENTE",0,'L',0);
					}elseif($tipo == "D"){
						$reporte -> MultiCell(0,3,"TOTALMENTE",0,'L',0);
					}else{
						$reporte -> MultiCell(0,3,"ERROR",0,'L',0);
					}
					$reporte -> SetFont('Arial','B',9);
					$reporte -> SetXY(0,66);
					$reporte -> MultiCell(0,3, $carrera_y_areaformacion2,0,'C',0);
					$reporte -> SetFont('Arial','',9);
					$reporte -> SetXY(24,91);
					$reporte -> MultiCell(0,3,$numero,0,'L',0);
					$reporte -> SetXY(20,108);
					$reporte -> MultiCell(0,3,'PS/'.$alumno->miReg,0,'L',0);
					$reporte -> SetFont('Arial','',8);
					$reporte -> SetXY(9,212);
					$reporte -> MultiCell(0,3,$curp,0,'L',0);
					
					$periodos = 0;
					$suma = 0;
					
					$cuantas = 0;
					$creditos = 0;
					$x = 70;
					$y = 86;
					$cont = 0;
					$first_time = 0;				
					$reporte -> SetFont('Arial','',7);
					$nivel = 0;
					$paginas=1;
					$Materia = new Materia();
					
					// $periodoMenor <- Contiene el periodo más antiguo del alumno en que tenga una materia registrada en el kardex
					// $periodoMayor_ <- Contiene el periodo más antiguo del alumno en que tenga una materia registrada en el kardex, solo en forma calculable es decir: 20093 en lugar de 32009
					// $periodo <- Será la variable que irá cambiando periodo por periodo.
					// $periodo_ <- Será la variable que irá cambiando periodo por periodo, solo en forma calculable es decir: 20093 en lugar de 32009
					$periodo = $periodoMenor;
					$periodo_ = $Periodos->get_periodo_calculable($periodo);
					$periodoMayor_ = $Periodos->get_periodo_calculable($periodoMayor);
					
					while($periodo_ <= $periodoMayor_){
						$x=50;
						$aux = 0;
						$materia_no_encontrada_en_dicho_perido = 0;
						
						$kardex_ing = $KardexIng -> get_kardex_by_periodo_join_with_materia($alumno, $periodo);
						$count_kardIng = count($kardex_ing);
						foreach( $kardex_ing as $KardIng ){
								if( (($count_kardIng + $cont) > 58) || $aux==0 ){
									if( ($count_kardIng + $cont) > 58 ){
										$cont = 0; // Contador de página
										$y=15;
										$reporte -> AddPage();
										$paginas++;
									}
									$aux++;
									$y=$y + 4;
									$reporte -> SetXY($x,$y);
									$reporte -> MultiCell(89,3,$this -> semestre($periodo)." SEMESTRE",0,'C',0);
									$cont += 2;
									$y=$y + 4;
									$reporte -> SetXY($x,$y);
									$reporte -> MultiCell(89,3," <".$this -> periodo(substr($periodo,0,1))." ".substr($periodo,1,5)."> ",0,'C',0);
									$reporte -> SetXY($x,$y);
								}
							$cont++;
							$nivel = $KardIng -> semestre;
							$y=$y +4;
							
							$reporte -> SetXY(50,$y);
							$reporte -> MultiCell(87,4,$KardIng -> nombre,0,'L',0);
							$reporte -> SetXY(138,$y);
							$reporte -> MultiCell(10,4,"   ".$KardIng -> promedio,0,'C',0);
							$reporte -> SetXY(149,$y);
							$reporte -> MultiCell(37,4,"     ".$this -> decena($KardIng -> promedio,0),0,'L',0);
							$reporte -> SetXY(190,$y);
							if($alumno -> plan == 'PE07'){
								$reporte -> MultiCell(11,4,"  ".$KardIng -> creditos,0,'L',0);
								$creditos = $creditos + $KardIng -> creditos;
							}else{
								$reporte -> MultiCell(11,4,"   --",0,'L',0);
							}
							if($nivel > 5){
								$reporte -> SetXY(204,$y);
							}else{
								$reporte -> SetXY(204,$y);
							}								
							$reporte -> MultiCell(11,4,"  ".$this -> obs($KardIng -> tipo_de_ex),0,'L',0);
							//$y=$y +4;
							$suma = $suma + $KardIng -> promedio;
							$cuantas++;
						}
						$periodo = $KardexIng->incrementaPeriodoKardex($periodo);
						$periodo_ = $Periodos->get_periodo_calculable($periodo);
					} // while($periodo_ <= $periodoMayor_)
				$y=$y + 8;
				if ($tipo=='P'){ // si es parcial
					if($nivel <= 6 && $paginas==1){
						$reporte -> SetFont('Arial','B',15);
						$reporte -> SetXY(50,$y);
						$reporte -> MultiCell(160,3,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,'L',0);	
						$reporte -> AddPage();
						$cont = 0;
						$y=10;
					}
					else if($nivel<6 && $paginas==1){
						$reporte -> AddPage();
						$cont = 0;
						$y=10;						
						
						$reporte -> SetFont('Arial','B',15);
						$reporte -> SetXY(50,$y);
						$reporte -> MultiCell(160,3,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,'L',0);	
					}
				 }else{ //inicio del else si no es parcial
					if ($nivel <= 6 && $paginas==1){
						$reporte -> AddPage();
						$cont = 0;
						$y=10;
					}
					$reporte -> SetFont('Arial','B',15);
					$y = $y +1;
					$reporte -> SetXY(50,$y);
					$reporte -> MultiCell(160,3,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,'L',0);
					$reporte -> SetFont('Arial','',7);
					$y = $y +4;					
					$reporte -> SetXY(50,$y);
					$reporte -> MultiCell(160,3,'LOS ESTUDIOS CURSADOS SON EQUIVALENTES AL NIVEL MEDIO SUPERIOR O BACHILLERATO '.'TECNOLÓGICO'.' ANTECEDENTE DEL NIVEL SUPERIOR.',0,'L',0);
					
						$y = $y +8;
						$reporte -> SetXY(50,$y);
						/*
						$reporte -> MultiCell(160,3,utf8_decode('CARRERA ACREDITADA SEGÚN ACTA ').$Especialidad -> acta_cacei.utf8_decode(' DEL CONSEJO DE ACREDITACIÓN DE LA ENSEÑANZA DE LA INGENIERÍA A.C. (CACEI) DE FECHA ').substr($Especialidad -> fecha,8,2).' DE '.$this -> mes(substr($Especialidad -> fecha,5,2)).' DE '.substr($Especialidad -> fecha,0,4),0,'L',0);
						*/
						
						$reporte -> MultiCell(160,3,'CARRERA ACREDITADA SEGÚN ACTA '.' DEL CONSEJO DE ACREDITACIÓN DE LA ENSEÑANZA DE LA INGENIERÍA A.C. (CACEI) DE FECHA '.' DE '.' DE ',0,'L',0);
					
					$y = $y +8;
					$reporte -> SetXY(50,$y);
					$promTotal2 = round(($suma/$cuantas),2);
					if(strlen($promTotal2)==2){
						$promTotal2 = $promTotal2.".00";
					} else if (strlen($promTotal2)==4){
						$promTotal2 = $promTotal2."0";
					}
					
					if (substr($promTotal2,3,2)!='00'){
						$reporte -> MultiCell(160,3,'PROMEDIO GENERAL DE APROVECHAMIENTO: '.substr(($promTotal2),0,5).' ('.$this -> decena(substr(($promTotal2),0,2),0).' PUNTO '.$this -> decena(substr(($promTotal2),3,2)).')',0,'L',0);
					} else {
						$reporte -> MultiCell(160,3,'PROMEDIO GENERAL DE APROVECHAMIENTO: '.substr(($promTotal2),0,5).' ('.$this -> decena(substr(($promTotal2),0,2),0).' PUNTO CERO CERO'.')',0,'L',0);
					}
					//$reporte -> MultiCell(160,3,'PROMEDIO GENERAL DE APROVECHAMIENTO: 81.00 (OCHENTA Y UNO PUNTO CERO)',0,'L',0);
					$y = $y +4;
					$reporte -> SetFont('Arial','B',15);
					$reporte -> SetXY(50,$y);
					$reporte -> MultiCell(160,3,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,'L',0);
					$y = $y +4;
					$reporte -> SetXY(50,$y);
					$reporte -> SetFont('Arial','',7);
					$reporte -> MultiCell(160,3,$alumno->miReg.$numero.$alumno->carrera_id,0,'L',0);
					if($tipo =='D'){
						$reporte -> SetXY(155,$y);
						$reporte -> SetFont('Arial','',7);
						$reporte -> MultiCell(160,3,"DUPLICADO",0,'L',0);
					}
				}//fin del else
					
				$reporte -> SetFont('Arial','',7);
				$reporte -> SetXY(4,140);
				$reporte -> MultiCell(40,4,'ING. SALVADOR',0,'C',0);
				$reporte -> SetXY(4,144);
				$reporte -> MultiCell(40,4,'TRINIDAD PEREZ',0,'C',0);					
				
				$reporte -> SetFont('Arial','',10);
				if ($tipo=='P'){
					$reporte -> SetXY(16,210);
					$reporte -> MultiCell(12,4,'-----',0,'C',0);
				}
				$reporte -> SetXY(16,210);
				$reporte -> MultiCell(12,4,substr($promTotal2,0,5),0,'C',0);
				
				///
				$reporte -> SetFont('Arial','',9);
				$reporte -> SetXY(98,245);
				$reporte -> MultiCell(40,4,'70',0,'L',0);
				
				//$reporte -> MultiCell(12,4,substr('81.00',0,5),0,'C',0);
				if ($alumno -> plan =='PE07'){
					$reporte -> SetFont('Arial','',8);
					$reporte -> SetXY(50,271);
					$reporte -> MultiCell(40,4,$creditos.' CRÉDITOS',0,'L',0);
					$reporte -> SetXY(165,271);
					$reporte -> MultiCell(40,4,$totalcreditos.' CRÉDITOS',0,'L',0);
				}
				else{
					$reporte -> SetFont('Arial','',8);
					$reporte -> SetXY(50,271);
					$reporte -> MultiCell(40,4,$cuantas.' MATERIAS',0,'L',0);
					$reporte -> SetXY(165,271);
					$reporte -> MultiCell(40,4,$cuantas.' MATERIAS',0,'L',0);
				}
				
				$reporte -> SetXY(142,276);
				$reporte -> MultiCell(50,4,'GUADALAJARA, JALISCO',0,'C',0);
				
				$reporte -> SetXY(49,280);
				$reporte -> MultiCell(35,4,$dia,0,'L',0);
				$reporte -> SetXY(127,280);
				$reporte -> MultiCell(35,4,$mes,0,'L',0);
				$reporte -> SetXY(49,285);
				$reporte -> MultiCell(35,4,$anio,0,'L',0);
				$reporte -> SetFont('Arial','',7);
				$reporte -> SetXY(11,327);
				$reporte -> MultiCell(67,4,'ING. WILIBALDO RUIZ AREVALO',0,'C',0);
				//$reporte -> MultiCell(67,4,'M. EN I. LUIS FERNANDO LAPHAM CARDENAS',0,'C',0);
				$reporte -> SetXY(78,327);
				$reporte -> MultiCell(66,4,'ING. JAIME ARREOLA OBREGON',0,'C',0);
				$reporte -> SetXY(144,324);
				$reporte -> MultiCell(69,4,'ING. JUAN PABLO AVIÑA MACIAS',0,'C',0);
				
				$reporte -> SetFont('Arial','',7);
				$reporte -> SetXY(11,330);
				$reporte -> MultiCell(67,3,'DIRECTOR',0,'C',0);
				$reporte -> SetXY(78,330);
				$reporte -> MultiCell(66,3,'SUBDIRECTOR DE SERVICIOS',0,'C',0);
				$reporte -> SetXY(147,328);
				$reporte -> MultiCell(66,3,'JEFE DEL DEPARTAMENTO DE EVALUACIÓN Y CERTIFICACIÓN DEL APRENDIZAJE',0,'C',0);
				
				if ($pasante!=""){
					//$reporte -> AddPage();	
					$reporte -> SetMargins(0,0,0);
					$reporte -> SetXY(0,36);
					$reporte -> SetFont('Arial','B',10);
					$reporte -> SetFont('Arial','',10);
					$reporte -> SetXY(55,44);
					$reporte -> MultiCell(0,3,"PS/ ".$alumno->miReg,0,'L',0);
					$reporte -> SetXY(180,44);
					$reporte -> MultiCell(0,3, $numero,0,'L',0);

					$reporte -> SetXY(113,77);
					$reporte -> MultiCell(0,3, "14DNT0001P",0,'L',0);
					$reporte -> SetFont('Arial','',11);
					$reporte -> SetXY(75,89);
					$nombres = iconv("latin1", "ISO-8859-1", $Alumno -> nombres);
					$apaterno = iconv("latin1", "ISO-8859-1", $Alumno -> a_paterno);
					$amaterno = iconv("latin1", "ISO-8859-1", $Alumno -> a_materno);
					$reporte -> MultiCell(0,3,$nombres." ".$apaterno." ".$amaterno,0,'L',0);
				  
					$reporte -> SetFont('Arial','',10);
					$reporte -> SetXY(143,97);
					$reporte -> MultiCell(0,3,$curp,0,'L',0);
					$reporte -> SetFont('Arial','B',11);
					$reporte -> SetXY(18,116);
					if($Especialidad -> id == 800){
						$reporte -> MultiCell(0,3,'TECNÓLOGO '.$Especialidad -> nombre,0,'C',0);
					}else{
						$reporte -> MultiCell(0,3,'TECNÓLOGO EN '.$Especialidad -> nombre,0,'C',0);
					}
					$reporte -> SetFont('Arial','',10);
					$reporte -> SetXY(100,167);
					$reporte -> MultiCell(50,4,'GUADALAJARA, JALISCO',0,'C',0);
					$reporte -> SetFont('Arial','',9);
					$reporte -> SetXY(70,172);
					$reporte -> MultiCell(35,4,$dia,0,'L',0);
					$reporte -> SetXY(142,172);
					$reporte -> MultiCell(35,4,$mes,0,'L',0);
					$reporte -> SetXY(185,172);
					$reporte -> MultiCell(35,4,$anio,0,'L',0);
					$reporte -> SetFont('Arial','',11);
					$reporte -> SetXY(88,197);
					$reporte -> MultiCell(67,3,'DIRECTOR',0,'C',0);
					$reporte -> SetXY(88,228);
					$reporte -> MultiCell(67,4,'ING. WILIBALDO RUIZ AREVALO',0,'C',0);

					//$reporte -> AddPage();	
					$reporte -> SetMargins(0,0,0);
					$reporte -> SetFont('Arial','',11);
					$reporte -> SetXY(52,29);

					$reporte -> SetXY(148,29);

					$reporte -> SetFont('Arial','',8);
					$reporte -> SetXY(15,52);
					$reporte -> MultiCell(87,4,"ING. SALVADOR TRINIDAD PEREZ",0,'C',0);
					$reporte -> SetXY(110,52);
					$reporte -> MultiCell(87,4,"ING. JUAN PABLO AVIÑA MACIAS",0,'C',0);
					$reporte -> SetXY(15,59);
					$reporte -> MultiCell(87,4,"JEFE DEL DEPARTAMENTO DE SERVICIOS DE APOYO ACADEMICO",0,'C',0);
					$reporte -> SetXY(110,59);
					$reporte -> MultiCell(87,4,"JEFE DEL DEPARTAMENTO DE EVALUACION Y CERTIFICACION DEL APRENDIZAJE",0,'C',0);			 
				}
					
				//$reporte->Ln();
				//$reporte->SetX(170);
				//$reporte->Cell(30, 3, $Periodos->get_datetime(), 1, 0, 'C', 1);
				
				$numRan = rand(1, 1000);
				$reporte -> Output("public/files/ingcalificaciones/c_".$alumno->miReg.$numRan.".pdf");				
				$this->redirect("public/files/ingcalificaciones/c_".$alumno->miReg.$numRan.".pdf");
				}else{
					echo 'El alumno no tiene materias en kardex';
				}
			}else{
				echo 'No hay alumno';
			}
			
		} // function imprimir_certificado($registro = 831265,$numero=0,$curp='',$tipo = 'T',$dia='',$mes='',$anio='')
		
		
		function obs($opc){
			$res="";
			switch($opc){
				case'D': $res="";
					break;
				case'EG': $res="";
					break;
				case'ACR': $res="ACR";
					break;
				case'EE': $res="EE";
					break;
				case'NIV': $res="NIV";
					break;
				case'ER': $res="ER";
					break;
				case'ETS': $res="ETS";
					break;
				case'EDP': $res="EDP";
					break;
				case'CONV': $res="CONV";
					break;
				case'EQUI': $res="EQUI";
					break;
				case'REV': $res="REV";
					break;				
				case'T': $res="TIT";
					break;
			}
			return $res;
		}
		
		function decena($numero){		
			if	($numero == 999){
				$num_letra = "NO PRESENTO ";
				
			}else if ($numero == 500){
				$num_letra = "PENDIENTE ";				
				
			}else if	($numero == 300){
				$num_letra = "NO REPORTADA ";
				
			}else if ($numero == 100){
				$num_letra = "CIEN ";				
			}
			else if ($numero >= 90 && $numero <= 99){
				$num_letra = "NOVENTA ";
				
				if ($numero > 90){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 90);
				}
			}
			else if ($numero >= 80 && $numero <= 89){
				$num_letra = "OCHENTA ";
				
				if ($numero > 80){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 80);
				}
			}
			else if ($numero >= 70 && $numero <= 79){
					$num_letra = "SETENTA ";
				
				if ($numero > 70){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 70);
				}
			}
			else if ($numero >= 60 && $numero <= 69){
				$num_letra = "SESENTA ";
				
				if ($numero > 60){
					echo $v;
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 60);
				}
			}
			else if ($numero >= 50 && $numero <= 59){
				$num_letra = "CINCUENTA ";
				
				if ($numero > 50){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 50);
				}
			}
			else if ($numero >= 40 && $numero <= 49){
				$num_letra = "CUARENTA ";
				
				if ($numero > 40){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 40);
				}
			}
			else if ($numero >= 30 && $numero <= 39){
				$num_letra = "TREINTA ";
				
				if ($numero > 30){
					$num_letra = $num_letra."Y ".$this -> unidad($numero - 30);
				}
			}
			else if ($numero >= 20 && $numero <= 29){
				if ($numero == 20){
					$num_letra = "VEINTE ";
				}else{
					$num_letra = "VEINTI".$this -> unidad($numero - 20);
				}
			}
			else if ($numero >= 10 && $numero <= 19){
				switch ($numero){
					case 10:{
						$num_letra = "DIEZ ";
						break;
					}
					case 11:{
						$num_letra = "ONCE ";
						break;
					}
					case 12:{
						$num_letra = "DOCE ";
						break;
					}
					case 13:{
						$num_letra = "TRECE ";
						break;
					}
					case 14:{
						$num_letra = "CATORCE ";
						break;
					}
					case 15:{
						$num_letra = "QUINCE ";
						break;
					}
					case 16:{
						$num_letra = "DIECISEIS ";
						break;
					}
					case 17:{
						$num_letra = "DIECISIETE ";
						break;
					}
					case 18:{
						$num_letra = "DIECIOCHO ";
						break;
					}
					case 19:{
						$num_letra = "DIECINUEVE ";
						break;
					}
				}
			  }
			 
			else{
					$num_letra = "CERO ".$this -> unidad($numero);
			}
			return $num_letra;
		}
		
		function unidad($numero, $v=0){			
			switch ($numero){
					case 9:{
						$num = "NUEVE";
						break;
					}
					case 8:{
						$num = "OCHO";
						break;
					}
					case 7:{
						$num = "SIETE";
						break;
					}
					case 6:{
						$num = "SEIS";
						break;
					}
					case 5:{
						$num = "CINCO";
						break;
					}
					case 4:{
						$num = "CUATRO";
						break;
					}
					case 3:{
						$num = "TRES";
						break;
					}
					case 2:{
						$num = "DOS";
						break;
					}
					case 1:{
						$num = "UNO";
						break;
					}
					case 0:{
						$num = "CERO";
						break;
					}				
				}
				return $num;			
		}
		
		function letra_mes($mes){
			$letra = "";
			switch ($mes){
				case "JAN":{
					$letra = "ENERO";
					break;
				}
				case "FEB":{
					$letra = "FEBRERO";
					break;
				}
				case "MAR":{
					$letra = "MARZO";
					break;
				}
				case "APR":{
					$letra = "ABRIL";
					break;
				}
				case "MAY":{
					$letra = "MAYO";
					break;
				}
				case "JUN":{
					$letra = "JUNIO";
					break;
				}
				case "JUL":{
					$letra = "JULIO";
					break;
				}
				case "AUG":{
					$letra = "AGOSTO";
					break;
				}
				case "SEP":{
					$letra = "SEPTIEMBRE";
					break;
				}
				case "OCT":{
					$letra = "OCTUBRE";
					break;
				}
				case "NOV":{
					$letra = "NOVIEMBRE";
					break;
				}
				case "DIC":{
					$letra = "DICIEMBRE";
					break;
				}
			}
			return $letra;
		}
		
		/*
		@function num2letras () 
		@abstract Dado un n?mero lo devuelve escrito. 
		@param $num number - N?mero a convertir. 
		@param $fem bool - Forma femenina (true) o no (false). 
		@param $dec bool - Con decimales (true) o no (false). 
		@result string - Devuelve el n?mero escrito en letr
		*/
		function num2letras($num, $fem = false, $dec = true){
			$matuni[2] = "dos";
			$matuni[3] = "tres";
			$matuni[4] = "cuatro";
			$matuni[5] = "cinco";
			$matuni[6] = "seis";
			$matuni[7] = "siete";
			$matuni[8] = "ocho";
			$matuni[9] = "nueve";
			$matuni[10] = "diez";
			$matuni[11] = "once";
			$matuni[12] = "doce";
			$matuni[13] = "trece";
			$matuni[14] = "catorce";
			$matuni[15] = "quince";
			$matuni[16] = "dieciseis";
			$matuni[17] = "diecisiete";
			$matuni[18] = "dieciocho";
			$matuni[19] = "diecinueve";
			$matuni[20] = "veinte";
			$matunisub[2] = "dos";
			$matunisub[3] = "tres";
			$matunisub[4] = "cuatro";
			$matunisub[5] = "quin";
			$matunisub[6] = "seis";
			$matunisub[7] = "sete";
			$matunisub[8] = "ocho";
			$matunisub[9] = "nove";

			$matdec[2] = "veint";
			$matdec[3] = "treinta";
			$matdec[4] = "cuarenta";
			$matdec[5] = "cincuenta";
			$matdec[6] = "sesenta";
			$matdec[7] = "setenta";
			$matdec[8] = "ochenta";
			$matdec[9] = "noventa";
			$matsub[3] = 'mill';
			$matsub[5] = 'bill';
			$matsub[7] = 'mill';
			$matsub[9] = 'trill';
			$matsub[11] = 'mill';
			$matsub[13] = 'bill';
			$matsub[15] = 'mill';
			$matmil[4] = 'millones';
			$matmil[6] = 'billones';
			$matmil[7] = 'de billones';
			$matmil[8] = 'millones de billones';
			$matmil[10] = 'trillones';
			$matmil[11] = 'de trillones';
			$matmil[12] = 'millones de trillones';
			$matmil[13] = 'de trillones';
			$matmil[14] = 'billones de trillones';
			$matmil[15] = 'de billones de trillones';
			$matmil[16] = 'millones de billones de trillones';

			//Zi hack
			$float = explode('.', $num);
			$num = $float[0];

			$num = trim((string) @$num);
			if ($num[0] == '-') {
				$neg = 'menos ';
				$num = substr($num, 1);
			}else
				$neg = '';
			while ($num[0] == '0')
				$num = substr($num, 1);
			if ($num[0] < '1' or $num[0] > 9)
				$num = '0' . $num;
			$zeros = true;
			$punt = false;
			$ent = '';
			$fra = '';
			for ($c = 0; $c < strlen($num); $c++) {
				$n = $num[$c];
				if (!(strpos(".,'''", $n) === false)) {
					if ($punt)
						break;
					else {
						$punt = true;
						continue;
					}
				} elseif (!(strpos('0123456789', $n) === false)) {
					if ($punt) {
						if ($n != '0')
							$zeros = false;
						$fra .= $n;
					}else
						$ent .= $n;
				}else
					break;
			}
			$ent = '     ' . $ent;
			if ($dec and $fra and !$zeros) {
				$fin = ' coma';
				for ($n = 0; $n < strlen($fra); $n++) {
					if (($s = $fra[$n]) == '0')
						$fin .= ' cero';
					elseif ($s == '1')
						$fin .= $fem ? ' una' : ' un';
					else
						$fin .= ' ' . $matuni[$s];
				}
			}else
				$fin = '';
			if ((int) $ent === 0)
				return 'Cero ' . $fin;
			$tex = '';
			$sub = 0;
			$mils = 0;
			$neutro = false;
			while (($num = substr($ent, -3)) != '   ') {
				$ent = substr($ent, 0, -3);
				if (++$sub < 3 and $fem) {
					$matuni[1] = 'una';
					$subcent = 'as';
				} else {
					$matuni[1] = $neutro ? 'un' : 'uno';
					$subcent = 'os';
				}
				$t = '';
				$n2 = substr($num, 1);
				if ($n2 == '00') {
					
				} elseif ($n2 < 21)
					$t = ' ' . $matuni[(int) $n2];
				elseif ($n2 < 30) {
					$n3 = $num[2];
					if ($n3 != 0)
						$t = 'i' . $matuni[$n3];
					$n2 = $num[1];
					$t = ' ' . $matdec[$n2] . $t;
				}else {
					$n3 = $num[2];
					if ($n3 != 0)
						$t = ' y ' . $matuni[$n3];
					$n2 = $num[1];
					$t = ' ' . $matdec[$n2] . $t;
				}
				$n = $num[0];
				if ($n == 1) {
					$t = ' ciento' . $t;
				} elseif ($n == 5) {
					$t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
				} elseif ($n != 0) {
					$t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
				}
				if ($sub == 1) {
					
				} elseif (!isset($matsub[$sub])) {
					if ($num == 1) {
						$t = ' mil';
					} elseif ($num > 1) {
						$t .= ' mil';
					}
				} elseif ($num == 1) {
					$t .= ' ' . $matsub[$sub] . '?n';
				} elseif ($num > 1) {
					$t .= ' ' . $matsub[$sub] . 'ones';
				}
				if ($num == '000')
					$mils++;
				elseif ($mils != 0) {
					if (isset($matmil[$sub]))
						$t .= ' ' . $matmil[$sub];
					$mils = 0;
				}
				$neutro = true;
				$tex = $t . $tex;
			}
			$tex = $neg . substr($tex, 1) . $fin;
			return strtoupper(ucfirst($tex));
			//Zi hack --> return ucfirst($tex);
			//$end_num = ucfirst($tex) . ' pesos ' . $float[1] . '/100 M.N.';
			//return $end_num;
		} // function num2letras($num, $fem = false, $dec = true)
		
		function periodo($opc){
			if($opc == 1){
				return 'FEB/JUN';
			}else{
				return 'AGO/DIC';
			}
		} // function periodo($opc)
		
		function semestre($opc){
			switch($opc){
				case'1': return 'PRIMER';
					break;
				case'2': return 'SEGUNDO';
					break;
				case'3': return 'TERCER';
					break;
				case'4': return 'CUARTO';
					break;
				case'5': return 'QUINTO';
					break;
				case'6': return 'SEXTO';
					break;
				case'7': return 'SEPTIMO';
					break;
				case'8': return 'OCTAVO';
					break;
			}
		} // function semestre($opc)
		
		function primeroanterior($registro, $especialidad, $nivel, $plan){
			
			$Alumnos = new Alumnos();
			$alumnos = $Alumnos -> find_first("miReg=".$registro);
			
			if(($alumnos -> estado=='EG')||($alumnos ->estado=='BD')||($alumnos ->estado=='BT')||($alumnos ->estado=='OK')){
				$Materias = new MateriasHorarioseg();
			}
			else{
				$Materias = new MateriasHorarios();
			}
				
			$Kardex = new Kardex();
			$periodos = NULL;
			$nuevo = 0;			
			$viejo = 0;			
			
			if($materias = $Materias -> find("plan = '".$plan."' and especialidad_id = ".$especialidad." and nivel = ".$nivel, "order: nivel ASC, clave ASC")){
				$cont = 0;
				foreach($materias as $m){
					if($Kardex -> find_first("clave = '".$m -> clave."' and registro = '".$registro."'")){
						$periodos[$cont++]=$Kardex -> periodo;						
					}
				}
				$cont = 0;
				foreach($periodos as $p){
					if($cont == 0){
						$viejo= substr($p,1,4).substr($p,0,1);
					}						
					$nuevo= substr($p,1,4).substr($p,0,1);
					if($nuevo > $viejo){
						$viejo = $nuevo;
					}
					//echo substr($p,1,4).substr($p,0,1).'<br>';
					$cont++;
				}		
				//echo "<br>".$viejo;
				//echo '<br><br><br><br><br><br>';
				return substr($viejo,4,5).substr($viejo,0,4);
				//return $p;
			}
		} // function primeroanterior($registro, $especialidad, $nivel, $plan)
		
		function primeroanterior2($registro, $especialidad, $nivel, $plan){
			
			$Alumnos = new Alumnos();
			$alumnos = $Alumnos -> find_first("miReg=".$registro);
			
			if(($alumnos -> estado=='EG')||($alumnos ->estado=='BD')||($alumnos ->estado=='BT')||($alumnos ->estado=='OK')){
			$Materias = new MateriasHorarioseg();
			}
			else{
			$Materias = new MateriasHorarios();
			}
			
			$Kardex = new Kardex();
			$periodos = NULL;
			$nuevo = 0;			
			$viejo = 0;			
			
			if($materias = $Materias -> find("plan = '".$plan."' and especialidad_id = ".$especialidad." and nivel = ".$nivel, "order: nivel ASC, clave ASC")){
				$cont = 0;
				foreach($materias as $m){
					if($Kardex -> find_first("clave = '".$m -> clave."' and registro = '".$registro."'")){
						$periodos[$cont++]=$Kardex -> periodo;						
					}
				}
				$cont = 0;
				foreach($periodos as $p){
					
					$nuevosperiodos[$cont++]= substr($p,1,4).substr($p,0,1);
					
					//echo substr($p,1,4).substr($p,0,1).'<br>';
					$cont++;
				}
				$minimo=min($nuevosperiodos);		
				//echo "<br>".$viejo;
				//echo '<br><br><br><br><br><br>';
				return substr($minimo,4,5).substr($minimo,0,4);
				//return $p;
			}
		} // function primeroanterior2($registro, $especialidad, $nivel, $plan)
	}
?>
