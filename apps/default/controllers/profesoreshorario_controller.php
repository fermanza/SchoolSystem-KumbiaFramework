<?php

    class profesoreshorarioController extends ApplicationController {
		
        public $antesAnterior	= 12012;
        public $pasado			= 32012;
        public $actual			= 12013;
        public $proximo			= 32013;
		
        function index(){
			//$this -> valida();
        }
		
       	function horarioPorProfesor(){
			unset($this->periodos_xccursos);
			
			$Periodos = new Periodos();
			$periodos_xccursos = $Periodos->get_todos_periodos_desde_xccursos();
			
			$this->periodos_xccursos = Array();
			foreach( $periodos_xccursos as $periodo ){
				unset($periodoLetraYNum);
				if( substr($periodo, 0, 1) == 1)
					$periodo_letra = "FEB - JUN ";
				else
					$periodo_letra = "AGO - DIC ";
				
				$periodoLetraYNum->letra = $periodo_letra.substr($periodo,1,4);
				$periodoLetraYNum->numero = $periodo;
				array_push($this->periodos_xccursos, $periodoLetraYNum);
			}
		} // function horarioPorProfesor( )
		
       	function horarioPorProfesor2( ){
			//$this -> valida();
			$this -> set_response("view");
			
			unset($this->periodos_post);
			unset($this->periodos_xccursos);
			unset($this -> horarios);
			unset($this -> xccursos);
			unset($this -> periodo);
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$this->periodos_xccursos = $Periodos->get_todos_periodos_desde_xccursos();
			
			// Mostrar horario de un Profesor.
			// Se podrá hacer búsqueda por nómina o por nombre.
			// Si es por nombre se podrán mostrar varios resultados a la vez,
			//como máximo serán 5 profesores a la vez. Esto para evitar
			//sobrecarga en el servidor
			
			//$xperiodoscaptura	= new Xperiodoscaptura();
			$nomina			= $this -> post("nomina");
			$nombreProfesor	= $this -> post("nombreProfesor");
			$this->periodo_post = $this -> post("periodo_post");
			
			$periodo = $this->periodo_post;
			
			$Maestros = new Maestros();
			if( isset($nombreProfesor) && $nombreProfesor != "Nombre" && $nombreProfesor != ""){
				$nomina = $Maestros -> get_nomina_by_nombre($nombreProfesor);
			}
			
			$cualplantel = ""; // c Colomos, t Tonala
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();			
			foreach( $Xccursos -> find_all_by_sql("
							select count(*) cuenta
							from xccursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as $cursoPlantelColomos ){
				$NumCursosColomos = $cursoPlantelColomos -> cuenta;
			}
			foreach( $Xtcursos -> find_all_by_sql("
							select count(*) cuenta
							from xtcursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as$cursoPlantelTonala ){
				$NumCursosTonala = $cursoPlantelTonala -> cuenta;
			}
			if( $NumCursosColomos > $NumCursosTonala )
				$cualplantel = "c";
			else
				$cualplantel = "t";
			
			if( substr($periodo, 0, 1) == 1)
				$periodo_letra = "FEB - JUN ";
			else
				$periodo_letra = "AGO - DIC ";
			
			$this -> periodo = $periodo_letra.substr($periodo,1,4);
			
			$i = 0;
			$this -> horas = 0;
			$Xccursos = new Xccursos();			
			
			// Cuenta las horas que lleva el profesor en ambos planteles
			$this -> horasTotal = $Maestros -> get_horas_totales_en_ambos_planteles_by_periodo($nomina, $periodo);
			// Descripción de cursos, sushoras, salones etc.
			$this -> horarios = $Maestros -> get_all_info_cursos_ambos_planteles_by_periodo($nomina, $periodo);
			$this -> xccursos = $Xccursos -> get_relevant_info_from_xccursos_by_periodo($nomina, $periodo);
			
			$this -> render_partial("horarioPorProfesor2");
			
		} // function horarioPorProfesor( )
		
		
       	function horarioPorDivision(){
			
		} // function horarioPorDivision()
		
       	function horarioPorMateria(){
			
		} // function horarioPorMateria()
		
		function exportarapdf( $nomina ){
			//die;
			//$this -> valida();
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			//$nomina = $this -> post('nomina');
			//$this -> set_response("view");
			//$periodo = 32011;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			$cualplantel = ""; // c Colomos, t Tonala
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();			
			foreach( $Xccursos -> find_all_by_sql("
							select count(*) cuenta
							from xccursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as $cursoPlantelColomos ){
				$NumCursosColomos = $cursoPlantelColomos -> cuenta;
			}
			foreach( $Xtcursos -> find_all_by_sql("
							select count(*) cuenta
							from xtcursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as$cursoPlantelTonala ){
				$NumCursosTonala = $cursoPlantelTonala -> cuenta;
			}
			if( $NumCursosColomos > $NumCursosTonala )
				$cualplantel = "c";
			else
				$cualplantel = "t";
			
			$xchorascursos = new xchorascursos();
			$Maestros = new Maestros();
			unset($jornada);
			
			// Cuenta las horas que lleva el profesor en ambos planteles
			$horasTotal = $Maestros -> get_horas_totales_en_ambos_planteles($nomina);
			// Descripción de cursos, sushoras, salones etc.
			$cursosHoras = $Maestros -> get_cursos_horas_salones_ambos_planteles($nomina);
			
			
			$contador= array (0,0,0,0,0,0,0);
			$horas = array("7:00 \n 8:00","8:00 \n 9:00","9:00 \n 10:00",
			"10:00 \n 11:00","11:00 \n 12:00","12:00 \n 13:00",
			"13:00 \n 14:00","14:00 \n 15:00","15:00 \n 16:00",
			"16:00 \n 17:00","17:00 \n 18:00","18:00 \n 19:00",
			"19:00 \n 20:00","20:00 \n 21:00","21:00 \n 22:00"); 
			
			if ($maestro = $Maestros -> find_first("nomina = ".$nomina)){					
				
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
				$reporte -> MultiCell(0,2,"HORARIO DE PROFESOR NIVEL INGENIERÍA",0,'C',0);			
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				// Conseguir ah que coordinación pertenece el profesor
				$coordinadores = new Coordinadores();
				if( $coordinador = $coordinadores -> find_first("division = '".$maestro -> division."'") ){
					
				}else{
					$notienedivision = new Xccursos();
					foreach( $notienedivision -> find_all_by_sql("
							select count(*) cuenta, division, nomina
							from x".$cualplantel."cursos
							where periodo = ".$periodo."
							group by nomina, division
							order by cuenta" ) as $notienediv ){
						$coordinador = $notienediv;
					}
					$coordinador = $coordinadores -> find_first("division = '".$coordinador -> division."'");
				}
				// FIN DE -- Conseguir ah que coordinación pertenece el profesor --
				//echo "AAAAAAA";
				//die;
				//$MaestroDivisiones = new MaestroDivisiones();
				//$maestroDivisiones = $MaestroDivisiones -> find_first("maestro_id = ".$nomina);
				//$Divisiones = new Divisiones();
				//$division = $Divisiones -> find_first("id = ".$maestroDivisiones -> division_id);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"MAESTRO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$maestro -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(55,4,$coordinador -> division,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(20,4,"No. NOMINA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$maestro -> nomina,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($cualplantel == 'c'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(20,4,$plantel,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if( substr($periodo, 0, 1) == 1)
					$periodoNombre = "FEB - JUN, ".$periodo;
				else
					$periodoNombre = "AGO - DIC, ".$periodo;
				$reporte -> Cell(40,4,$periodoNombre,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(40,4,"No. DE SESIONES DE CLASE:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$horasTotal,1,0,'L',1);
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
				$y=42;
				unset( $hh );
				$yaux = 0;
				for($h=7;$h<=21;$h++){
					$pos=10;
					// Checa si en esa hora existe algún curso espejo, y si es así da más espacio para que quepa la información
					$aux = 0;
					$reporte -> SetFont('Arial','B',6);
					$y = $y + $yaux;
					$reporte -> SetXY($pos, $y);
					$reporte -> SetFillColor(0x99,0x99,0x99);
					$yaux = 0;
					 for($d=1;$d<7;$d++){ if( isset($cursosHoras[1][$d][$h])){ $yaux = 8; $aux = 1; break; } }
					if( $aux == 1 )
						$reporte -> MultiCell(16,8,$horas[$h-7],1,'C',1);
					else
						$reporte -> MultiCell(16,4,$horas[$h-7],1,'C',1);
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					$reporte -> SetFont('Arial','B',4);
					for($d=1;$d<7;$d++){
						if($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						if( isset($cursosHoras[1][$d][$h]) ){
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,4,substr(trim($cursosHoras[1][$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[1][$d][$h] -> materia." -- ".
							$cursosHoras[1][$d][$h] -> clavecurso." -- ".
							$cursosHoras[1][$d][$h] -> edificio.":".$cursosHoras[1][$d][$h] -> nombresalon."\n".
							substr(trim($cursosHoras[$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[$d][$h] -> materia." -- ".
							$cursosHoras[$d][$h] -> clavecurso." -- ".
							$cursosHoras[$d][$h] -> edificio.":".$cursosHoras[$d][$h] -> nombresalon,1,'L',1);
							$jornada[$d]++;
						}
						else if(isset($cursosHoras[$d][$h])){
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,4,substr(trim($cursosHoras[$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[$d][$h] -> materia." -- ".
							$cursosHoras[$d][$h] -> clavecurso." -- ".
							$cursosHoras[$d][$h] -> edificio.":".$cursosHoras[$d][$h] -> nombresalon,1,'L',1);
							$jornada[$d]++;
						}
						else{
							$reporte -> SetFillColor(0xFF,0xFF,0xFF);
							if( $aux == 1 )
								$reporte -> MultiCell(29,8,"\n ",1,'C',1);
							else
								$reporte -> MultiCell(29,4,"\n ",1,'C',1);
						}
						
					}
					$y = $y+8;
				}
				$y = $y + 2 + $yaux;
				$reporte -> SetFont('Arial','B',4);
				$pos=10;
				$reporte -> SetXY($pos, $y);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> MultiCell(16,4,"SESIONES / DIA",1,'C',1);
				//$reporte -> MultiCell(16,4,"SESIONES / DIA \n JORNADA",1,'C',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetFont('Arial','B',5);
				
				//$jornadaa ="- - -";
				for($d=1;$d<7;$d++){
					if ($d == 1){
						$posExtra=16;
					}else{
						$posExtra=29;
					}
					if(isset($jornada[$d])){
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,4,$jornada[$d]." hrs",1,'C',1);
						//$reporte -> MultiCell(29,2,$jornada[$d]." hrs. \n ".$jornadaa,1,'C',1);
					}else{
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,4,"0 hrs",1,'C',1);
						//$reporte -> MultiCell(29,4,"0 hrs. \n NI",1,'C',1);
					}
				}
											
				$reporte -> Ln(2);				
				
				$reporte -> SetFont('Arial','B',7);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(190,4,"ACTIVIDADES PROGRAMA DE TRABAJO ACADÉMICO",0,0,'C',1);
				$reporte -> Ln();
				
				$reporte -> SetFont('Arial','B',5);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				//$reporte -> Cell(10,4,"CLAVE",1,0,'C',1);
				$reporte -> Cell(170,4,"ACTIVIDADES SUSTANTIVAS",1,0,'C',1);
				$reporte -> Cell(20,4,"HORAS",1,0,'C',1);
				$reporte -> Ln();
				
				$ActividadMaestro = new ActividadMaestro();
				//$Actividades = new Listaactividades();
				//$Actividades -> find_first("id = 1");
				
				$reporte -> SetFont('Arial','B',5);
				$totalHoras = 0;
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				//$reporte -> Cell(10,3,$Actividades -> clave,1,0,'C',1);
				$reporte -> Cell(170,3,"IMPARTIR CLASES",1,0,'C',1);
				$reporte -> Cell(20,3,$horasTotal,1,0,'C',1);
				$totalHoras = $totalHoras + $horasTotal;
				$reporte -> Ln();
							
				$actividadMaestro60 = $ActividadMaestro -> find("nomina = ".$nomina." and periodo = ".$periodo." and tipoActividad = 1");
				$color=1;
				foreach($actividadMaestro60 as $am){
					$Actividades = new actividadMaestroListaActividades();
					$actividad = $Actividades -> find_first("clave = '".$am -> actividad_maestro_lista_actividades."'");
					
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					
					//$reporte -> Cell(10,3,$actividad -> nombre,1,0,'C',1);
					$reporte -> Cell(170,3,$actividad -> actividad,1,0,'C',1);
					$reporte -> Cell(20,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(170,4,"ACTIVIDADES SUSTANTIVAS Y COMPLEMENTARIAS",1,0,'C',1);
				$reporte -> Cell(20,4,"",1,0,'C',1);
				$reporte -> Ln();
				$actividadMaestro40 = $ActividadMaestro -> find("nomina = ".$nomina." and periodo = ".$periodo." and tipoActividad = 2");
				foreach($actividadMaestro40 as $am){
					$Actividades = new actividadMaestroListaActividades();
					$actividad = $Actividades -> find_first("clave = '".$am -> actividad_maestro_lista_actividades."'");
					
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					
					//$reporte -> Cell(10,3,$actividad -> nombre,1,0,'C',1);
					$reporte -> Cell(170,3,$actividad -> actividad,1,0,'C',1);
					$reporte -> Cell(20,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(140,3,"",1,0,'C',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(30,3,"TOTAL DE HORAS",1,0,'C',1);
				$reporte -> Cell(20,3,$totalHoras,1,0,'C',1);
				$reporte -> Ln(10);
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				
				$reporte -> line(80,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> Ln(1);
				$reporte -> SetFont('Arial','B',7);
				$reporte -> Cell(190,4,$maestro -> nombre,0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',5);
				$reporte -> Cell(190,3,"FIRMA DE CONFORMIDAD",0,0,'C',1);				
				$reporte -> Ln(3);

				$reporte -> SetFont('Arial','B',8);								
				$reporte -> Cell(60,4,"ELABORÓ",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"Vo. Bo.",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"AUTORIZÓ",0,0,'C',1);
				$reporte -> Ln(7);
				$reporte -> line(20,$reporte -> GetY(),60,$reporte -> GetY());
				$reporte -> line(85,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> line(155,$reporte -> GetY(),195,$reporte -> GetY());
				$reporte -> Ln(1);
				
				$Maestros = new Maestros();
				$maestro1 = $Maestros ->find_first("nomina = '".$nomina."'");
				$Maestros = new Maestros();
				
				$reporte -> SetFont('Arial','B',7);
				$reporte -> Cell(60,4,$coordinador -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(55,4,"ING. IVAN ALEJANDRO SALAS DURAZO",0,0,'C',1);
				$reporte -> Cell(15,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"ING. MIGUEL OROZCO ESCAMILLA",0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',5);
				$reporte -> Cell(60,4,"COORDINACIÓN ".$coordinador -> division,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"JEFATURA DE NIVEL INGENIERÍA",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"SUBDIRECCION DE OPERACION ACADEMICA",0,0,'C',1);
				$reporte -> Ln(4);
				$reporte -> SetFont('Arial','B',4);
				$reporte -> Cell(60,4,"REVISIÓN 1",0,0,'C',1);
				$reporte -> Cell(70,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"FR-01-DAC-NI-PO-001",0,0,'C',1);
				
				$reporte -> Output("public/files/inghorariosprofesores/".$nomina.".pdf");
				
				$this->redirect("public/files/inghorariosprofesores/".$nomina.".pdf");
			}else{
				//$this -> redirect("tgoprofesor/horarios");
				echo "no se encontro la nomina";
			}
			//$periodo = 12011;
		} // function exportarapdf($nomina)
		
		
		function horarioExportarPDF(){
			//die;
			//$this -> valida();
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			$nomina = $this -> post('nomina');
			$periodo_post = $this -> post('periodo_post');
			//$this -> set_response("view");
			//$periodo = 32011;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			if($periodo_post!="")
				$periodo = $periodo_post;
			
			$cualplantel = ""; // c Colomos, t Tonala
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();			
			foreach( $Xccursos -> find_all_by_sql("
							select count(*) cuenta
							from xccursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as $cursoPlantelColomos ){
				$NumCursosColomos = $cursoPlantelColomos -> cuenta;
			}
			foreach( $Xtcursos -> find_all_by_sql("
							select count(*) cuenta
							from xtcursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as$cursoPlantelTonala ){
				$NumCursosTonala = $cursoPlantelTonala -> cuenta;
			}
			if( $NumCursosColomos > $NumCursosTonala )
				$cualplantel = "c";
			else
				$cualplantel = "t";
			
			$xchorascursos = new xchorascursos();
			$Maestros = new Maestros();
			unset($jornada);
			
			// Cuenta las horas que lleva el profesor en ambos planteles
			$horasTotal = $Maestros -> get_horas_totales_en_ambos_planteles($nomina);
			// Descripción de cursos, sushoras, salones etc.
			$cursosHoras = $Maestros -> get_cursos_horas_salones_ambos_planteles($nomina);
			
			
			$contador= array (0,0,0,0,0,0,0);
			$horas = array("7:00 \n 8:00","8:00 \n 9:00","9:00 \n 10:00",
			"10:00 \n 11:00","11:00 \n 12:00","12:00 \n 13:00",
			"13:00 \n 14:00","14:00 \n 15:00","15:00 \n 16:00",
			"16:00 \n 17:00","17:00 \n 18:00","18:00 \n 19:00",
			"19:00 \n 20:00","20:00 \n 21:00","21:00 \n 22:00"); 
			
			if ($maestro = $Maestros -> find_first("nomina = ".$nomina)){					
				
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
				$reporte -> MultiCell(0,2,"HORARIO DE PROFESOR NIVEL INGENIERÍA",0,'C',0);			
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				// Conseguir ah que coordinación pertenece el profesor
				$coordinadores = new Coordinadores();
				if( $coordinador = $coordinadores -> find_first("division = '".$maestro -> division."'") ){
					
				}else{
					$notienedivision = new Xccursos();
					foreach( $notienedivision -> find_all_by_sql("
							select count(*) cuenta, division, nomina
							from x".$cualplantel."cursos
							where periodo = ".$periodo."
							group by nomina, division
							order by cuenta" ) as $notienediv ){
						$coordinador = $notienediv;
					}
					$coordinador = $coordinadores -> find_first("division = '".$coordinador -> division."'");
				}
				// FIN DE -- Conseguir ah que coordinación pertenece el profesor --
				//echo "AAAAAAA";
				//die;
				//$MaestroDivisiones = new MaestroDivisiones();
				//$maestroDivisiones = $MaestroDivisiones -> find_first("maestro_id = ".$nomina);
				//$Divisiones = new Divisiones();
				//$division = $Divisiones -> find_first("id = ".$maestroDivisiones -> division_id);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"MAESTRO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(65,4,$maestro -> nombre,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(55,4,$coordinador -> division,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(20,4,"No. NOMINA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$maestro -> nomina,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($cualplantel == 'c'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(20,4,$plantel,1,0,'L',1);				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if( substr($periodo, 0, 1) == 1)
					$periodoNombre = "FEB - JUN, ".$periodo;
				else
					$periodoNombre = "AGO - DIC, ".$periodo;
				$reporte -> Cell(40,4,$periodoNombre,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(40,4,"No. DE SESIONES DE CLASE:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(20,4,$horasTotal,1,0,'L',1);
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
				$y=42;
				unset( $hh );
				$yaux = 0;
				for($h=7;$h<=21;$h++){
					$pos=10;
					// Checa si en esa hora existe algún curso espejo, y si es así da más espacio para que quepa la información
					$aux = 0;
					$reporte -> SetFont('Arial','B',6);
					$y = $y + $yaux;
					$reporte -> SetXY($pos, $y);
					$reporte -> SetFillColor(0x99,0x99,0x99);
					$yaux = 0;
					 for($d=1;$d<7;$d++){ if( isset($cursosHoras[1][$d][$h])){ $yaux = 8; $aux = 1; break; } }
					if( $aux == 1 )
						$reporte -> MultiCell(16,8,$horas[$h-7],1,'C',1);
					else
						$reporte -> MultiCell(16,4,$horas[$h-7],1,'C',1);
					$reporte -> SetFillColor(0xFF,0xFF,0xFF);
					$reporte -> SetFont('Arial','B',4);
					for($d=1;$d<7;$d++){
						if($d == 1){
							$posExtra=16;
						}else{
							$posExtra=29;
						}
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						if( isset($cursosHoras[1][$d][$h]) ){
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,4,substr(trim($cursosHoras[1][$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[1][$d][$h] -> materia." -- ".
							$cursosHoras[1][$d][$h] -> clavecurso." -- ".
							$cursosHoras[1][$d][$h] -> edificio.":".$cursosHoras[1][$d][$h] -> nombresalon."\n".
							substr(trim($cursosHoras[$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[$d][$h] -> materia." -- ".
							$cursosHoras[$d][$h] -> clavecurso." -- ".
							$cursosHoras[$d][$h] -> edificio.":".$cursosHoras[$d][$h] -> nombresalon,1,'L',1);
							$jornada[$d]++;
						}
						else if(isset($cursosHoras[$d][$h])){
							$reporte -> SetFillColor(0xDD,0xDD,0xDD);
							$reporte -> MultiCell(29,4,substr(trim($cursosHoras[$d][$h] -> nombremateria),0,23)." \n ".
							$cursosHoras[$d][$h] -> materia." -- ".
							$cursosHoras[$d][$h] -> clavecurso." -- ".
							$cursosHoras[$d][$h] -> edificio.":".$cursosHoras[$d][$h] -> nombresalon,1,'L',1);
							$jornada[$d]++;
						}
						else{
							$reporte -> SetFillColor(0xFF,0xFF,0xFF);
							if( $aux == 1 )
								$reporte -> MultiCell(29,8,"\n ",1,'C',1);
							else
								$reporte -> MultiCell(29,4,"\n ",1,'C',1);
						}
						
					}
					$y = $y+8;
				}
				$y = $y + 2 + $yaux;
				$reporte -> SetFont('Arial','B',4);
				$pos=10;
				$reporte -> SetXY($pos, $y);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> MultiCell(16,4,"SESIONES / DIA",1,'C',1);
				//$reporte -> MultiCell(16,4,"SESIONES / DIA \n JORNADA",1,'C',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetFont('Arial','B',5);
				
				//$jornadaa ="- - -";
				for($d=1;$d<7;$d++){
					if ($d == 1){
						$posExtra=16;
					}else{
						$posExtra=29;
					}
					if(isset($jornada[$d])){
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,4,$jornada[$d]." hrs",1,'C',1);
						//$reporte -> MultiCell(29,2,$jornada[$d]." hrs. \n ".$jornadaa,1,'C',1);
					}else{
						$reporte -> SetXY($pos = $pos+$posExtra, $y);
						$reporte -> MultiCell(29,4,"0 hrs",1,'C',1);
						//$reporte -> MultiCell(29,4,"0 hrs. \n NI",1,'C',1);
					}
				}
											
				$reporte -> Ln(2);				
				
				$reporte -> SetFont('Arial','B',7);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(190,4,"ACTIVIDADES PROGRAMA DE TRABAJO ACADÉMICO",0,0,'C',1);
				$reporte -> Ln();
				
				$reporte -> SetFont('Arial','B',5);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				//$reporte -> Cell(10,4,"CLAVE",1,0,'C',1);
				$reporte -> Cell(170,4,"ACTIVIDADES SUSTANTIVAS",1,0,'C',1);
				$reporte -> Cell(20,4,"HORAS",1,0,'C',1);
				$reporte -> Ln();
				
				$ActividadMaestro = new ActividadMaestro();
				//$Actividades = new Listaactividades();
				//$Actividades -> find_first("id = 1");
				
				$reporte -> SetFont('Arial','B',5);
				$totalHoras = 0;
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				//$reporte -> Cell(10,3,$Actividades -> clave,1,0,'C',1);
				$reporte -> Cell(170,3,"IMPARTIR CLASES",1,0,'C',1);
				$reporte -> Cell(20,3,$horasTotal,1,0,'C',1);
				$totalHoras = $totalHoras + $horasTotal;
				$reporte -> Ln();
							
				$actividadMaestro60 = $ActividadMaestro -> find("nomina = ".$nomina." and periodo = ".$periodo." and tipoActividad = 1");
				$color=1;
				foreach($actividadMaestro60 as $am){
					$Actividades = new actividadMaestroListaActividades();
					$actividad = $Actividades -> find_first("clave = '".$am -> actividad_maestro_lista_actividades."'");
					
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					
					//$reporte -> Cell(10,3,$actividad -> nombre,1,0,'C',1);
					$reporte -> Cell(170,3,$actividad -> actividad,1,0,'C',1);
					$reporte -> Cell(20,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(170,4,"ACTIVIDADES SUSTANTIVAS Y COMPLEMENTARIAS",1,0,'C',1);
				$reporte -> Cell(20,4,"",1,0,'C',1);
				$reporte -> Ln();
				$actividadMaestro40 = $ActividadMaestro -> find("nomina = ".$nomina." and periodo = ".$periodo." and tipoActividad = 2");
				foreach($actividadMaestro40 as $am){
					$Actividades = new actividadMaestroListaActividades();
					$actividad = $Actividades -> find_first("clave = '".$am -> actividad_maestro_lista_actividades."'");
					
					if($color == 0){
						$reporte -> SetFillColor(0xFF,0xFF,0xFF);
						$color=1;
					}else{
						$reporte -> SetFillColor(0xDD,0xDD,0xDD);
						$color=0;
					}
					
					//$reporte -> Cell(10,3,$actividad -> nombre,1,0,'C',1);
					$reporte -> Cell(170,3,$actividad -> actividad,1,0,'C',1);
					$reporte -> Cell(20,3,$am -> horas,1,0,'C',1);
					$totalHoras = $totalHoras + $am -> horas;
					$reporte -> Ln();
				}
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(140,3,"",1,0,'C',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(30,3,"TOTAL DE HORAS",1,0,'C',1);
				$reporte -> Cell(20,3,$totalHoras,1,0,'C',1);
				$reporte -> Ln(10);
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				
				$reporte -> line(80,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> Ln(1);
				$reporte -> SetFont('Arial','B',7);
				$reporte -> Cell(190,4,$maestro -> nombre,0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',5);
				$reporte -> Cell(190,3,"FIRMA DE CONFORMIDAD",0,0,'C',1);				
				$reporte -> Ln(3);

				$reporte -> SetFont('Arial','B',8);								
				$reporte -> Cell(60,4,"ELABORÓ",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"Vo. Bo.",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"AUTORIZÓ",0,0,'C',1);
				$reporte -> Ln(7);
				$reporte -> line(20,$reporte -> GetY(),60,$reporte -> GetY());
				$reporte -> line(85,$reporte -> GetY(),130,$reporte -> GetY());
				$reporte -> line(155,$reporte -> GetY(),195,$reporte -> GetY());
				$reporte -> Ln(1);
				
				$Maestros = new Maestros();
				$maestro1 = $Maestros ->find_first("nomina = '".$nomina."'");
				$Maestros = new Maestros();
				
				$reporte -> SetFont('Arial','B',7);
				$reporte -> Cell(60,4,$coordinador -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(55,4,"ING. IVAN ALEJANDRO SALAS DURAZO",0,0,'C',1);
				$reporte -> Cell(15,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"ING. MIGUEL OROZCO ESCAMILLA",0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',5);
				$reporte -> Cell(60,4,"COORDINACIÓN ".$coordinador -> division,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"JEFATURA DE NIVEL INGENIERÍA",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"SUBDIRECCION DE OPERACION ACADEMICA",0,0,'C',1);
				$reporte -> Ln(4);
				$reporte -> SetFont('Arial','B',4);
				$reporte -> Cell(60,4,"REVISIÓN 1",0,0,'C',1);
				$reporte -> Cell(70,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"FR-01-DAC-NI-PO-001",0,0,'C',1);
				
				$reporte -> Output("public/files/inghorariosprofesores/".$nomina.".pdf");
				
				$this->redirect("public/files/inghorariosprofesores/".$nomina.".pdf");
			}else{
				//$this -> redirect("tgoprofesor/horarios");
				echo "no se encontro la nomina";
			}
			//$periodo = 12011;
		} // function horarioExportarPDF()
		
		function actividadExportarPDF(){
			//die;
			//$this -> valida();
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			$nomina = $this -> post('nomina');
			$periodo_post = $this -> post('periodo_post');
			//$this -> set_response("view");
			//$periodo = 32011;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			if($periodo_post!="")
				$periodo = $periodo_post;
			
			$cualplantel = ""; // c Colomos, t Tonala
			$Xccursos = new Xccursos();
			$Xtcursos = new Xtcursos();			
			foreach( $Xccursos -> find_all_by_sql("
							select count(*) cuenta
							from xccursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as $cursoPlantelColomos ){
				$NumCursosColomos = $cursoPlantelColomos -> cuenta;
			}
			foreach( $Xtcursos -> find_all_by_sql("
							select count(*) cuenta
							from xtcursos
							where nomina = ".$nomina."
							and periodo = ".$periodo ) as$cursoPlantelTonala ){
				$NumCursosTonala = $cursoPlantelTonala -> cuenta;
			}
			if( $NumCursosColomos > $NumCursosTonala )
				$cualplantel = "c";
			else
				$cualplantel = "t";
			
			$Maestros = new Maestros();
			
			// Contar cuantas horas tiene el profesor
			$horasTotal = $Maestros -> get_horas_totales_en_ambos_planteles($nomina);
			
			if ($maestro = $Maestros -> find_first("nomina = ".$nomina)){					
				
				$reporte = new FPDF("L", "mm", "A4");
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
				$reporte -> MultiCell(0,2,"HORARIO DE PROFESOR NIVEL INGENIERÍA",0,'C',0);			
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> SetTextColor(0);
				$reporte -> SetDrawColor(0x00,0x00,0x00);
				$reporte -> SetFont('Arial','B',7);
				
				// Conseguir ah que coordinación pertenece el profesor
				$coordinadores = new Coordinadores();
				if( $coordinador = $coordinadores -> find_first("division = '".$maestro -> division."'") ){
					
				}else{
					$notienedivision = new Xccursos();
					foreach( $notienedivision -> find_all_by_sql("
							select count(*) cuenta, division, nomina
							from x".$cualplantel."cursos
							where periodo = ".$periodo."
							group by nomina, division
							order by cuenta" ) as $notienediv ){
						$coordinador = $notienediv;
					}
					$coordinador = $coordinadores -> find_first("division = '".$coordinador -> division."'");
				}
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(25,4,"PLANTEL:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if ($cualplantel == 'c'){
					$plantel = "COLOMOS";
				}
				else{
					$plantel = "TONALA";
				}
				$reporte -> Cell(45,4,$plantel,1,0,'L',1);				
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"DIVISION:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(75,4,$coordinador -> division,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"FECHA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
                $day = date ("d"); $month = date ("m"); $year = date ("Y");
				$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
				$reporte -> Cell(55,4,$date1,1,0,'L',1);
				$reporte -> Ln();
				
				//$reporte -> SetFillColor(0x99,0x99,0x99);
				//$reporte -> Cell(40,4,"No. DE SESIONES DE CLASE:",1,0,'L',1);
				//$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				//$reporte -> Cell(20,4,$horasTotal,1,0,'L',1);
				//$reporte -> SetFillColor(0x99,0x99,0x99);
				
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(25,4,"MAESTRO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(80,4,$maestro -> nombre,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(20,4,"No. NOMINA:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(35,4,$maestro -> nomina,1,0,'L',1);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$reporte -> Cell(15,4,"PERIODO:",1,0,'L',1);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				if( substr($periodo, 0, 1) == 1)
					$periodoNombre = "FEB - JUN, ".$periodo;
				else
					$periodoNombre = "AGO - DIC, ".$periodo;
				$reporte -> Cell(55,4,$periodoNombre,1,0,'L',1);
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetFillColor(0x99,0x99,0x99);
				$posExtra=29;
				$y=40;
				$pos=10;
				$reporte -> MultiCell(12,8,"CLAVE",1,'C',1);
				$reporte -> SetXY($pos+=12, $y);
				$reporte -> MultiCell(38,8,"COMISIÓN",1,'C',1);
				$reporte -> SetXY($pos+=38, $y);
				$reporte -> MultiCell(32,8,"OBJETIVO",1,'C',1);
				$reporte -> SetXY($pos+=32, $y);
				$reporte -> MultiCell(23,8,"META",1,'C',1);
				$reporte -> SetXY($pos+=23, $y);
				$reporte -> MultiCell(23,4,"EVIDENCIA /"." \n "."PRODUCTO",1,'C',1);
				$reporte -> SetXY($pos+=23, $y);
				$reporte -> MultiCell(15,8,"HRS SEM",1,'C',1);
				$reporte -> SetXY($pos+=15, $y);
				$reporte -> MultiCell(18,4,"% AVANCE\nSEMANA 6",1,'C',1);
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> MultiCell(25,4,"SEGUIMIENTO\n1er. PARCIAL",1,'C',1);
				$reporte -> SetXY($pos+=25, $y);
				$reporte -> MultiCell(18,4,"% AVANCE\nSEMANA 12",1,'C',1);
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> MultiCell(25,4,"SEGUIMIENTO\n2do. PARCIAL",1,'C',1);
				$reporte -> SetXY($pos+=25, $y);
				$reporte -> MultiCell(18,4,"% AVANCE\nSEMANA 18",1,'C',1);
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> MultiCell(25,4,"SEGUIMIENTO\n3er. PARCIAL",1,'C',1);
				
				
			$y=48;
			// IMPARTICIÓN DE CLASES
			$actividadMaestro = new ActividadMaestro();
			$actMaestro = $actividadMaestro -> get_lista_actividad_maestro_actividadPDF($nomina);
			$this -> horasClase = $actividadMaestro -> get_horasclase($nomina);
			
			$infoAct = $actividadMaestro -> get_objetivo_meta_evidencia_sustantiva("S-1.1");
			$pos=10;
			$reporte -> SetXY($pos, $y);
			$reporte -> SetFillColor(0xFF,0xFF,0xFF);
			$reporte -> Cell(12,12,"     S-1.1",1,'C',1); // clave
			
			$reporte -> SetFont('Arial','B',5);
			$reporte -> SetXY($pos+=12, $y);
			$reporte -> MultiCell(38,12,"IMPARTICIÓN DE CLASES",1,'C',1); // Comisión
			$reporte -> SetXY($pos+=38, $y);
			
			$reporte -> SetFont('Arial','B',4);
			$reporte -> MultiCell(32,2,$infoAct -> objetivo,1,'C',1); // Objetivo
			
			$reporte -> SetFont('Arial','B',5);
			$reporte -> SetXY($pos+=32, $y);
			if( strlen($infoAct -> meta) <= 15){
				$reporte -> MultiCell(23,12,substr($infoAct -> meta, 0, 15),1,'C',1); // Meta
			}
			else if( (strlen($infoAct -> meta) > 15) && (strlen($infoAct -> meta) <= 30) ){
				$reporte -> MultiCell(23,6,substr($infoAct -> meta, 0, 15)."\n".substr($infoAct -> meta, 15, 15),1,'C',1); // Meta
			}
			else if( strlen($infoAct -> meta) > 30 ){
				$reporte -> MultiCell(23,4,substr($infoAct -> meta, 0, 15).
					"\n".substr($infoAct -> meta, 15, 15).
						"\n".substr($infoAct -> meta,30, 15),1,'C',1); // Meta
			}
			$reporte -> SetXY($pos+=23, $y);
			
			$reporte -> SetFont('Arial','B',5);
			if( strlen($infoAct -> evidencia) <= 15){
				$reporte -> MultiCell(23,12,substr($infoAct -> evidencia, 0, 15),1,'C',1); // Evidencia
			}
			else if( (strlen($infoAct -> evidencia) > 15) && (strlen($infoAct -> evidencia) <= 30) ){
				$reporte -> MultiCell(23,6,substr($infoAct -> evidencia, 0, 15)."\n".substr($infoAct -> evidencia, 15, 15),1,'C',1); // Evidencia
			}
			else if( strlen($infoAct -> evidencia) > 30 ){
				$reporte -> MultiCell(23,4,substr($infoAct -> evidencia, 0, 15).
					"\n".substr($infoAct -> evidencia, 15, 15).
						"\n".substr($infoAct -> evidencia,30, 15),1,'C',1); // Evidencia
			}
			$reporte -> SetXY($pos+=23, $y);
			
			$reporte -> SetFont('Arial','B',6);
			$reporte -> Cell(15,12,"          ".$this -> horasClase,1,'C',1); // Horas Semana
			$reporte -> SetXY($pos+=15, $y);
			$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Primer Parcial $act -> avance1
			$reporte -> SetXY($pos+=18, $y);
			$reporte -> SetFont('Arial','B',4);
			$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion1
			$reporte -> SetFont('Arial','B',6);
			$reporte -> SetXY($pos+=25, $y);
			$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Segundo Parcial $act -> avance2
			$reporte -> SetXY($pos+=18, $y);
			$reporte -> SetFont('Arial','B',4);
			$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion2
			$reporte -> SetFont('Arial','B',6);
			$reporte -> SetXY($pos+=25, $y);
			$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Tercer Parcial $act -> avance3
			$reporte -> SetXY($pos+=18, $y);
			$reporte -> SetFont('Arial','B',4);
			$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion3
			$reporte -> SetFont('Arial','B',6);
			
			$reporte -> Ln();
			$y+=12;
			// FIN IMPARTICIÓN DE CLASES
			
			foreach( $actMaestro as $act ){
				$pos=10;
				$reporte -> SetXY($pos, $y);
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);
				$reporte -> Cell(12,12,"     ".$act -> actividad_maestro_lista_actividades,1,'C',1); // clave
				$reporte -> SetFont('Arial','B', 5);
				$reporte -> SetXY($pos+=12, $y);
				
				if( $act -> objetivo != "-" ){
					$actividadd = $actividadMaestro -> get_objetivo_meta_evidencia
						($act -> actividad_maestro_lista_actividades, $nomina);
					$act -> objetivoo = $actividadd -> objetivo;
					$act -> metaa = $actividadd -> meta;
					$act -> evidenciaa = $actividadd -> evidencia;
				}
				
				$reporte -> SetFont('Arial','B',5);
				if( strlen($act -> actividad) <= 35){
					$reporte -> MultiCell(38,12,substr($act -> actividad, 0, 30),1,'C',1); // Comisión ??
				}
				else if( (strlen($act -> actividad) > 30) && (strlen($act -> actividad) <= 60) ){
					$reporte -> MultiCell(38,6,substr($act -> actividad, 0, 30)."\n".substr($act -> actividad, 30, 30),1,'C',1); // Comisión ??
				}
				else if( strlen($act -> actividad) > 60 ){
					$reporte -> MultiCell(38,4,substr($act -> actividad, 0, 30).
						"\n".substr($act -> actividad, 30, 30).
							"\n".substr($act -> actividad, 60, 30),1,'C',1); // Comisión ??
				}
				$reporte -> SetXY($pos+=38, $y);
				
				$reporte -> SetFont('Arial','B',5);
				if( strlen($act -> objetivoo) <= 22){
					$reporte -> MultiCell(32,12,substr($act -> objetivoo, 0, 22),1,'C',1); // Objetivoo
				}
				else if( (strlen($act -> objetivoo) > 22) && (strlen($act -> objetivoo) <= 44) ){
					$reporte -> MultiCell(32,6,substr($act -> objetivoo, 0, 22)."\n".substr($act -> objetivoo, 22, 22),1,'C',1); // Objetivoo
				}
				else if( strlen($act -> objetivoo) > 44 ){
					$reporte -> MultiCell(32,4,substr($act -> objetivoo, 0, 22).
						"\n".substr($act -> objetivoo, 22, 22).
							"\n".substr($act -> objetivoo,44, 22),1,'C',1); // Objetivoo
				}
				$reporte -> SetXY($pos+=32, $y);
				
				$reporte -> SetFont('Arial','B',4);
				if( strlen($act -> metaa) <= 18){
					$reporte -> MultiCell(23,12,substr($act -> metaa, 0, 18),1,'C',1); // Metaa
				}
				else if( (strlen($act -> metaa) > 18) && (strlen($act -> metaa) <= 36) ){
					$reporte -> MultiCell(23,6,substr($act -> metaa, 0, 18)."\n".substr($act -> metaa, 18, 18),1,'C',1); // Metaa
				}
				else if( strlen($act -> metaa) > 36 ){
					$reporte -> MultiCell(23,4,substr($act -> metaa, 0, 18).
						"\n".substr($act -> metaa, 18, 18).
							"\n".substr($act -> metaa,36, 18),1,'C',1); // Metaa
				}
				$reporte -> SetXY($pos+=23, $y);
				
				$reporte -> SetFont('Arial','B',4);
				if( strlen($act -> evidenciaa) <= 18){
					$reporte -> MultiCell(23,12,substr($act -> evidenciaa, 0, 18),1,'C',1); // Evidenciaa
				}
				else if( (strlen($act -> evidenciaa) > 18) && (strlen($act -> evidenciaa) <= 36) ){
					$reporte -> MultiCell(23,6,substr($act -> evidenciaa, 0, 18)."\n".substr($act -> evidenciaa, 18, 18),1,'C',1); // Evidenciaa
				}
				else if( strlen($act -> evidenciaa) > 36 ){
					$reporte -> MultiCell(23,4,substr($act -> evidenciaa, 0, 18).
						"\n".substr($act -> evidenciaa, 18, 18).
							"\n".substr($act -> evidenciaa,36, 18),1,'C',1); // Evidenciaa
				}
				$reporte -> SetXY($pos+=23, $y);
				
				$reporte -> SetFont('Arial','B',6);
				$reporte -> Cell(15,12,"          ".$act -> horas,1,'C',1); // Horas Semana
				$reporte -> SetXY($pos+=15, $y);
				$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Primer Parcial $act -> avance1
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> SetFont('Arial','B',4);
				$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion1
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetXY($pos+=25, $y);
				$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Segundo Parcial $act -> avance2
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> SetFont('Arial','B',4);
				$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion2
				$reporte -> SetFont('Arial','B',6);
				$reporte -> SetXY($pos+=25, $y);
				$reporte -> MultiCell(18,12,"",1,'C',1); // Avance Tercer Parcial $act -> avance3
				$reporte -> SetXY($pos+=18, $y);
				$reporte -> SetFont('Arial','B',4);
				$reporte -> MultiCell(25,4,substr($coordinador -> nombre, 0, 25)."\n\nNombre y Firma",1,'C',1); // Autoevaluacion3
				$reporte -> SetFont('Arial','B',6);
				
				$reporte -> Ln();
				$y+=12;
			}
				
				$reporte -> Ln(8);
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);

				$reporte -> SetFont('Arial','B',9);								
				$reporte -> Cell(100,4,"ELABORÓ",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"REVISÓ.",0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(70,4,"AUTORIZÓ",0,0,'C',1);
				$reporte -> Ln(12);
				$reporte -> line(40,$reporte -> GetY(),80,$reporte -> GetY());
				$reporte -> line(120,$reporte -> GetY(),170,$reporte -> GetY());
				$reporte -> line(205,$reporte -> GetY(),245,$reporte -> GetY());
				$reporte -> Ln(1);
				
				$Maestros = new Maestros();
				$maestro1 = $Maestros ->find_first("nomina = '".$nomina."'");
				$Maestros = new Maestros();
				
				$reporte -> SetFont('Arial','B',8);
				$reporte -> Cell(100,4,$maestro -> nombre,0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(55,4,$coordinador -> nombre,0,0,'C',1);
				$reporte -> Cell(15,4,"",0,0,'C',1);
				$reporte -> Cell(70,4,"ING. IVAN ALEJANDRO SALAS DURAZO",0,0,'C',1);
				$reporte -> Ln(3);
				$reporte -> SetFont('Arial','B',6);
				$reporte -> Cell(100,4,"",0,0,'C',1);
				$reporte -> Cell(10,4,"",0,0,'C',1);
				$reporte -> Cell(50,4,"COORDINACIÓN ".$coordinador -> division,0,0,'C',1);
				$reporte -> Cell(20,4,"",0,0,'C',1);
				$reporte -> Cell(70,4,"JEFATURA DE NIVEL INGENIERÍA",0,0,'C',1);
				
				
				$reporte -> Ln(13);
				
				$reporte -> SetFillColor(0xFF,0xFF,0xFF);

				$reporte -> SetFont('Arial','B',9);								
				$reporte -> Cell(100,4,"REVISÓ CUMPLIMIENTO",0,0,'C',1);
				$reporte -> Ln(12);
				
				
				$reporte -> line(40,$reporte -> GetY(),80,$reporte -> GetY());
				$reporte -> Ln(2);
				$reporte -> Cell(100,4,"COORDINACIÓN ".$coordinador -> division,0,0,'C',1);
				
				$reporte -> SetXY(100, $y+45);
				$reporte -> MultiCell(150,4,"OBSERVACIONES\n\n\n\n",1,1,'C',1);
				
				$reporte -> Ln(10);
				$reporte -> Cell(100,4,"REVISIÓN 1",0,0,'C',1);
				$reporte -> Cell(70,4,"",0,0,'C',1);
				$reporte -> Cell(60,4,"FR-01-DAC-NI-PO-001",0,0,'C',1);
				
				$reporte -> Output("public/files/inghorariosprofesores/".$nomina.".pdf");
				
				$this->redirect("public/files/inghorariosprofesores/".$nomina.".pdf");
			}else{
				//$this -> redirect("tgoprofesor/horarios");
				echo "no se encontro la nomina";
			}
			//$periodo = 12011;
		} // function actividadExportarPDF()
		
		function valida(){
			if( Session::get_data('coordinador')!="OK" &&
					Session::get_data("tipousuario") != "OBSERVADOR" )
				$this->redirect('general/inicio');
		} // function valida()
    }
?>