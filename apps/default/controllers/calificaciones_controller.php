<?php

class CalificacionesController extends ApplicationController {

    public $anterior = 32009;
    public $actual = 12010;
    public $proximo = 32010;

    function buscar() {
        
    }

    function mensaje() {
        $this->mensaje_error = "NO SE ENCONTRO EL ALUMNO O TIENE ALGUN PROBLEMA CON LA INFORMACION";
    }

    function calificaciones() {
       // define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
       // require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');

        $malumnos = new Alumnos();
        $mespecialidades = new Especialidades();
        $mmateriasing = new Materiasing();
        $mmishorarios = new Mishorarios();
        $mxcalificacion = new Xcalificacion();

        $Xalumnocursos = new Xalumnocursos();
        $Xccursos = new Xccursos();
		$Xextraordinarios = new Xextraordinarios();
		
        $Xtalumnocursos = new Xtalumnocursos();
        $Xtcursos = new Xtcursos();
		$Xtextraordinarios = new Xtextraordinarios();
		
        $mmateria = new Materia();

        $mmaestros = new Maestros();
        $mprofavance = new Profavance();


        $registro = $this->post("registro");
        $periodo = $this->post("periodo");

        if ($registro == "") {
            $this->redirect("calificaciones/buscar");
            return;
        }

        $al = $malumnos->find_first("miReg = " . $registro);

        //if ($al->enPlantel == "N" || $al->enPlantel == "n") {
            //$this->redirect("calificaciones/calificacionesT/" . $registro . "/" . $periodo);
            //return;
        //}

        $no_materias = 0;

        $alumno = $malumnos->find_first("miReg=" . $registro);

        if ($alumno) {
            $this->set_response("view");

            $reporte = new FPDF();

            $reporte->Open();
            $reporte->AddPage();

            $reporte->AddFont('Verdana', '', 'verdana.php');
            $reporte->SetFont('Verdana', '', 14);

            $reporte->Image('http://ase.ceti.mx/ingenieria/img/logoceti2.jpg', 5, 5);
			
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
				$reporte -> SetFont('Arial','B',9);
				$reporte -> MultiCell(0,2,"PLANTEL COLOMOS",0,'C',0);
				$reporte -> SetY(26);
				$reporte -> SetX(15);
				$reporte -> SetFont('Arial','B',7);
				$reporte -> MultiCell(0,2,"NUEVA ESCOCIA 1885 COL. PROVIDENCIA 5TA. SECCIÓN, GUADALAJARA, JAL.",0,'C',0);
				 if (substr($this->post("periodo"), 0, 1) == 1)
					$epoca = "FEB-JUN";
				else
					$epoca = "AGO-DIC";
				$year = substr($this->post("periodo"), 1, 4);
			
				$reporte -> SetY(34);
				$reporte -> SetX(15);
				$reporte -> SetFont('Arial','B',10);
				$reporte -> MultiCell(0,3,"REPORTE DE CALIFICACIONES NIVEL INGENIERIA $epoca  $year",0,'C',0);
				$reporte -> Ln(1);
			
			/*

            $reporte->SetX(40);
            $reporte->SetFont('Verdana', '', 12);
            $reporte->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);

            $reporte->Ln();

            $reporte->SetX(40);
            $reporte->SetFont('Verdana', '', 8);
            $reporte->MultiCell(0, 3, "ORGANISMO PÚBLICO DECENTRALIZADO", 0, 'C', 0);

            $reporte->Ln();
			
            $reporte->SetX(40);
            $reporte->SetFont('Verdana', '', 9);
            $reporte->MultiCell(0, 3, "PLANTEL COLOMOS", 0, 'C', 0);
			
            $reporte->Ln();

            $reporte->SetX(40);
            $reporte->SetFont('Verdana', '', 8);
            $reporte->MultiCell(0, 2, "NUEVA ESCOCIA 1885 COL. PROVIDENCIA 5TA. SECCIÓN CP44638, GUADALAJARA, JAL.", 0, 'C', 0);
			
            $reporte->Ln();
			
            if (substr($this->post("periodo"), 0, 1) == 1)
                $epoca = "FEB-JUN";
            else
                $epoca = "AGO-DIC";
            $year = substr($this->post("periodo"), 1, 4);
			
            $reporte->SetX(40);
            $reporte->SetFont('Verdana', '', 10);
            $reporte->MultiCell(0, 2, "REPORTE DE CALIFICACIONES NIVEL INGENIERÍA $epoca  $year", 0, 'C', 0);*/
			
           
            $reporte->Ln();

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->SetTextColor(0);
            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
            $reporte->SetFont('Verdana', '', 6);

            $reporte->Cell(25, 4, "REGISTRO", 1, 0, 'C', 1);
            $reporte->Cell(70, 4, "NOMBRE", 1, 0, 'C', 1);
            $reporte->Cell(65, 4, "ESPECIALIDAD", 1, 0, 'C', 1);
            $reporte->Cell(30, 4, "TURNO", 1, 0, 'C', 1);

            $mespecialidades->find_first("idtiEsp =" . $alumno->idtiEsp);
            if ($alumno->enTurno == 'M') {
                $turno = "MATUTINO";
            } else {
                $turno = "VESPERTINO";
            }

            $reporte->Ln();
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->Cell(25, 4, $alumno->miReg, 1, 0, 'C', 1);
            $reporte->Cell(70, 4, $alumno->vcNomAlu, 1, 0, 'C', 1);
            $reporte->Cell(65, 4, $mespecialidades->vcNomEsp, 1, 0, 'C', 1);
            $reporte->Cell(30, 4, $turno, 1, 0, 'C', 1);

            $reporte->Ln();
            $reporte->Ln();

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->SetTextColor(0);
            $reporte->SetFont('Verdana', '', 4);
            $reporte->Cell(120, 4, "", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "PRIMER PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "SEGUNDO PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "TERCER PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "FINAL", 1, 0, 'C', 1);
            $reporte->Ln();

            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
            $reporte->SetFont('Verdana', '', 4);
            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

            $reporte->Cell(7, 4, "GRUPO", 1, 0, 'C', 1);
            $reporte->Cell(7, 4, "CLAVE", 1, 0, 'C', 1);
            $reporte->Cell(12, 4, "TIPO MATERIA", 1, 0, 'C', 1);
            $reporte->Cell(47, 4, "NOMBRE", 1, 0, 'C', 1);
            $reporte->Cell(47, 4, "PROFESOR", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Ln();

            $Materia = new Materia();
            $calificaciones_array = Array();
            $Periodos = new Periodos();
            $periodos_array = $Periodos->get_todos_periodos_desde_xccursos();
            if (in_array($periodo, $periodos_array)) {
                $xalumnocursos = $Xalumnocursos->get_materias_semestre_by_periodo_colomos_tonala($registro, $periodo);

                foreach ($xalumnocursos as $xalumnocurso) {
                    if(!$xcurso = $Xccursos->find_first("id = '".$xalumnocurso->curso_id."' and periodo = '".$periodo."'")){
						$xcurso = $Xtcursos->find_first("id = '".$xalumnocurso->curso_id."' and periodo = '".$periodo."'");
					}
					$no_materias++;
					$materia = $mmateria->find_first("clave = '" . $xcurso->materia . "' and carrera_id = " . $alumno->carrera_id);
					if ($maestro = $mmaestros->find_first("nomina = " . $xcurso->nomina)) {
						$reporte->SetDrawColor(0xFF, 0x66, 0x33);
						$reporte->SetFont('Verdana', '', 4);
						$reporte->SetFillColor(0xFF, 0xFF, 0xFF);
						$reporte->Cell(7, 4, $xcurso->clavecurso, 1, 0, 'C', 1);
						$reporte->Cell(7, 4, $materia->clave, 1, 0, 'C', 1);
						$reporte->Cell(12, 4, $xalumnocurso->tipo, 1, 0, 'C', 1);
						$reporte->Cell(47, 4, trim($materia->nombre), 1, 0, 'C', 1);
						$reporte->Cell(47, 4, trim($maestro->nombre), 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xcurso->horas1, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xalumnocurso->faltas1, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion1), 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xcurso->horas2, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xalumnocurso->faltas2, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion2), 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xcurso->horas3, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $xalumnocurso->faltas3, 1, 0, 'C', 1);
						$reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion3), 1, 0, 'C', 1);
						if ($xalumnocurso->calificacion == 300) {
							$reporte->Cell(6, 4, "0", 1, 0, 'C', 1);
							$reporte->Cell(6, 4, "0", 1, 0, 'C', 1);
						} else {
							$reporte->Cell(6, 4, $xcurso->horas1 +
									$xcurso->horas2 + $xcurso->horas3, 1, 0, 'C', 1);
							$reporte->Cell(6, 4, $xalumnocurso->faltas1 +
									$xalumnocurso->faltas2 + $xalumnocurso->faltas3, 1, 0, 'C', 1);
						}
						if ($xalumnocurso->calificacion < 70)
							$reporte->SetFont('Verdana', '', 5);
						$reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion), 1, 0, 'C', 1);
						if ($xalumnocurso->calificacion < 70)
							$reporte->SetFont('Verdana', '', 4);
						$reporte->Ln();
						if ($this->valor($xalumnocurso->calificacion) == 'NR' ||
								$this->valor($xalumnocurso->calificacion) == 'NP' ||
								$this->valor($xalumnocurso->calificacion) == 'NPD') {
							$calificaciones_array = $Materia->get_promedio_calificaciones
									($calificaciones_array, $xcurso->materia, 0);
						} else {
							$calificaciones_array = $Materia->get_promedio_calificaciones
									($calificaciones_array, $xcurso->materia, $xalumnocurso->calificacion);
						}
					}
                }
				
				if(!$xextraordinarios = $Xextraordinarios->find("registro =".$registro." and periodo = '".$periodo."'")){
					$xextraordinarios = $Xtextraordinarios->find("registro =".$registro." and periodo = '".$periodo."'");
				}
				
                $contador = 0;
                foreach ($xextraordinarios as $c) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(10, 4, "GRUPO", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                        $reporte->Ln();
                    }

                    $xcur = $Xccursos->find_first("id = '" . $c->curso_id . "'");
                    $materiaa = new Materia();
                    $materia = $materiaa->find_first("clave = '" . $xcur->materia . "' and carrera_id = " . $alumno->carrera_id);
                    $contador++;

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                    $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                    if ($c->tipo == "E")
                        $tipoExamen = "EXTRAORDINARIO";
                    else
                        $tipoExamen = "TITULO";
                    $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                    $reporte->Cell(20, 4, $materia->clave, 1, 0, 'C', 1);
                    $reporte->Cell(55, 4, $materia->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(10, 4, $c->clavecurso, 1, 0, 'C', 1);
                    if ($c->calificacion < 70) {
                        $reporte->SetFont('Verdana', '', 5);
                        $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                        $reporte->SetFont('Verdana', '', 4);
                    } else {
                        $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                    }
                    if ($c->estado == 'OK') {
                        $estatus = "SI";
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $xcur->materia, $c->calificacion);
                    } else {
                        $estatus = "NO";
                        if( isset($calificaciones_array[$xcur->materia]) ){
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $xcur->materia, $calificaciones_array[$xcur->materia]);
                        }
                        else{
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $xcur->materia, 0);
                        }
                    }
                    $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                    $reporte->Ln();
                } // foreach($xextraordinarios as $c)

                $IntersemestralAlumnos = new IntersemestralAlumnos();
                $Periodos = new Periodos();
                $periodo_intersemestral = $Periodos->convertirPeriodo_a_intersemestral($periodo);
                $interAlumnos = $IntersemestralAlumnos->get_cursos_de_un_alumno_($registro, $periodo_intersemestral);

                $contador = 0;
                foreach ($interAlumnos as $inter) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(30, 4, "PROFESOR", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                        $reporte->Ln();
                    }
                    $contador++;

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                    $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                    if ($inter->tipo_ex == "ACR")
                        $tipoExamen = "ACREDITACIÓN";
                    else if ($inter->tipo_ex == "NIV")
                        $tipoExamen = "NIVELACIÓN";
                    $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                    $reporte->Cell(20, 4, $inter->clave_materia, 1, 0, 'C', 1);
                    $reporte->Cell(55, 4, $inter->nombre_materia, 1, 0, 'C', 1);
                    $reporte->Cell(30, 4, $inter->nombre_maestro, 1, 0, 'C', 1);
                    if ($inter->calificacion < 70) {
                        $reporte->SetFont('Verdana', '', 5);
                        $reporte->Cell(20, 4, $this->valor($inter->calificacion), 1, 0, 'C', 1);
                        $reporte->SetFont('Verdana', '', 4);
                    } else {
                        $reporte->Cell(20, 4, $this->valor($inter->calificacion), 1, 0, 'C', 1);
                    }
                    if ($inter->pago == 'OK') {
                        $estatus = "SI";
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $inter->clave_materia, $inter->calificacion);
                    } else {
                        $estatus = "NO";
                        if( isset($calificaciones_array[$inter->clave_materia]) ){
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $inter->clave_materia, $calificaciones_array[$inter->clave_materia]);
                        }
                        else{
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $inter->clave_materia, 0);
                        }
                    }
                    $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                    $reporte->Ln();
                } // foreach($intersemestral as $inter)

                $globales = new Globales();
                $global = $globales->find("registro =" . $registro . "
							and periodo = " . ($periodo + 10000));
                $contador = 0;
                foreach ($global as $glob) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(30, 4, "PROFESOR", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);

                        $reporte->Ln();
                    }
                    foreach ($globales->find_all_by_sql(
                            "select ma.clave, ma.nombre as nombreMateria, m.nomina, m.nombre as nombreProf
								From carrera carr, materia ma, maestros m
								where carr.id = " . $alumno->carrera_id . "
								and carr.id = ma.carrera_id
								and ma.clave = '" . $glob->clavemat . "'
								and m.nomina = " . $glob->nomina . "
								limit 1")as $info) {
                        $contador++;

                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                        $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                        if ($glob->tipo_ex == "DP")
                            $tipoExamen = "DERECHO DE PASANTE";
                        else if ($glob->tipo_ex == "EG")
                            $tipoExamen = "EXAMEN GLOBAL";
                        $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $info->clave, 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, $info->nombreMateria, 1, 0, 'C', 1);
                        if ($glob->promedio < 70) {
                            $reporte->SetFont('Verdana', '', 5);
                            $reporte->Cell(20, 4, $this->valor($glob->promedio), 1, 0, 'C', 1);
                            $reporte->SetFont('Verdana', '', 4);
                        } else {
                            $reporte->Cell(20, 4, $this->valor($glob->promedio), 1, 0, 'C', 1);
                        }
                        if ($glob->pago == 'OK') {
                            $estatus = "SI";
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $glob->clavemat, $glob->promedio);
                        } else {
                            $estatus = "NO";
                            if( isset($calificaciones_array[$glob->clavemat]) ){
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $glob->clavemat, $calificaciones_array[$glob->clavemat]);
                            }
                            else{
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $glob->clavemat, 0);
                            }
                        }
                        $reporte->Cell(10, 4, $info->nombreProf, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                        $reporte->Ln();
                    } // foreach( $intersemestrales -> find_all_by_sql
                } // foreach($global as $glob)

                $promedio = 0;
                foreach ($calificaciones_array as $calif) {
                    $promedio += $calif;
                }
                $reporte->Ln();
                $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
                $reporte->SetFont('Verdana', '', 5);
                $reporte->Cell(190, 4, "PROMEDIO: " . round($promedio / count($calificaciones_array), 2), 1, 0, 'R', 1);
            } // if (in_array($periodo, $periodos_array))
            else {
                $xcalificacion = $mxcalificacion->find("miReg =" . $registro);

                foreach ($xcalificacion as $c) {
                    $mishorarios = $mmishorarios->find_first("clavecurso = '" . $c->clavecurso . "'");
                    $no_materias++;
                    $materia = $mmateriasing->find_first("clavemat = '" . $mishorarios->clavemat . "'");
                    $maestro = $mmaestros->find_first("nomina = " . $mishorarios->nomina);
                    $profavance = $mprofavance->find_first("id = '" . $c->clavecurso . "'");

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
                    $reporte->Cell(7, 4, $c->clavecurso, 1, 0, 'C', 1);
                    $reporte->Cell(7, 4, $materia->clavemat, 1, 0, 'C', 1);
                    $reporte->Cell(7, 4, "R", 1, 0, 'C', 1);
                    $reporte->Cell(47, 4, $materia->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(50, 4, $maestro->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas1, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt1, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal1), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas2, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt2, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal2), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal3), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas1 + $profavance->horas2 + $profavance->horas3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falta, 1, 0, 'C', 1);
                    if ($c->califnal < 70)
                        $reporte->SetFont('Verdana', '', 5);
                    $reporte->Cell(6, 4, $this->valor($c->califnal), 1, 0, 'C', 1);
                    if ($c->califnal < 70)
                        $reporte->SetFont('Verdana', '', 4);
                    $reporte->Ln();
                    if ($this->valor($c->califnal) == 'NR' || $this->valor($c->califnal) == 'NP' || $this->valor($c->califnal) == 'NPD') {
                        $suma = 0;
                    } else {
                        $suma = $this->valor($c->califnal);
                    }
                    $promedio = $promedio + $suma;
                }

                $reporte->Ln();
                $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
                $reporte->SetFont('Verdana', '', 5);
                $reporte->Cell(190, 4, "PROMEDIO: " . round($promedio / $no_materias), 1, 0, 'R', 1);
                $reporte->Ln();

                $xextraordinarios = $Xextraordinarios->find("registro =" . $registro);


                $contador = 0;
                foreach ($xextraordinarios as $c) {
                    $mishorarios = $mmishorarios->find_first("clavecurso = '" . $c->clavecurso . "'");
                    if ($mishorarios->periodo == $periodo && $c->estado == 'OK') {
                        if ($contador == 0) {
                            $reporte->Ln();
                            $reporte->Ln();
                            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                            $reporte->SetFont('Verdana', '', 4);
                            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                            $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                            $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                            $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                            $reporte->Cell(10, 4, "GRUPO", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                            $reporte->Ln();
                        }
                        $materia = $mmateriasing->find_first("clavemat = '" . $mishorarios->clavemat . "'");
                        $contador++;

                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                        $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                        if ($c->examen == "E")
                            $tipoExamen = "EXTRAORDINARIO";
                        else
                            $tipoExamen = "TITULO";
                        $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $materia->clavemat, 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, $materia->nombre, 1, 0, 'C', 1);
                        $reporte->Cell(10, 4, $mishorarios->clavecurso, 1, 0, 'C', 1);
                        if ($c->calificacion < 70)
                            $reporte->SetFont('Verdana', '', 5);
                        $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                        if ($c->calificacion < 70)
                            $reporte->SetFont('Verdana', '', 4);
                        if ($c->estado == 'OK')
                            $estatus = "SI";
                        else
                            $estatus = "NO";
                        $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                        $reporte->Ln();
                    }
                }
            }

            $reporte->Ln();
            $reporte->Ln();
            $reporte->Ln();
            $reporte->Ln();
            $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
            $reporte->SetFont('Verdana', '', 7);
            $reporte->Cell(75, 4, "", 1, 0, 'C', 1);
            $reporte->Cell(35, 4, "ING. SALVADOR TRINIDAD PEREZ", 1, 0, 'C', 1);
            $reporte->SetDrawColor(0x00, 0x00, 0x00);
            $reporte->Line(70, $reporte->GetY(), 135, $reporte->GetY());
            $reporte->Ln();
            $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
            $reporte->SetFont('Verdana', '', 6);
            $reporte->Cell(75, 3, "", 1, 0, 'C', 1);
            $reporte->Cell(35, 3, "JEFE DEL DEPTO. DE SERVICIOS DE APOYO ACADEMICO", 1, 0, 'C', 1);
			
			
			$reporte->Ln();
			$reporte->SetX(170);
			$reporte->Cell(30, 3, $Periodos->get_datetime(), 1, 0, 'C', 1);


            $reporte->Output("public/files/calificaciones/" . $registro . "" . $this->post("periodo") . ".pdf");

            $this->redirect("public/files/calificaciones/" . $registro . "" . $this->post("periodo") . ".pdf");
        }else {
            $this->redirect("calificaciones/mensaje");
        }
    } // function calificaciones()

    function calificacionesT($registro, $periodo) {
       // define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
        //require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
        $malumnos = new Alumnos();
        $mespecialidades = new Especialidades();
        $mmateriasing = new Materiasing();
        $mmishorarios = new Mishorarios();
        $mxcalificacion = new Xcalificacion();

        $mxalumnocursos = new Xtalumnocursos();
        $mxcursos = new Xtcursos();
        $mmateria = new Materia();

        $mmaestros = new Maestros();
        $mxextraordinarios = new Xtextraordinarios();
        $mprofavance = new Profavance();

        $no_materias = 0;

        $alumno = $malumnos->find_first("miReg=" . $registro);

        if ($alumno) {

            $this->set_response("view");

            $reporte = new FPDF();

            $reporte->Open();
            $reporte->AddPage();

            $reporte->AddFont('Verdana', '', 'verdana.php');
            $reporte->SetFont('Verdana', '', 14);

            $reporte->Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 0);

            $reporte->SetX(45);
            $reporte->SetFont('Verdana', '', 14);
            $reporte->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);

            $reporte->Ln();

            $reporte->SetX(45);
            $reporte->SetFont('Verdana', '', 12);
            $reporte->MultiCell(0, 3, "SUBDIRECCIÓN DE OPERACION ACADÉMICA", 0, 'C', 0);

            $reporte->Ln();
            $reporte->Ln();

            $reporte->SetX(45);
            $reporte->SetFont('Verdana', '', 12);
            $reporte->MultiCell(0, 2, "NIVEL INGENIERÍA", 0, 'C', 0);
            $reporte->Ln();

            $reporte->SetX(45);
            $reporte->SetFont('Verdana', '', 8);
            $reporte->MultiCell(0, 2, "PLANTEL TONALA", 0, 'C', 0);

            $reporte->Ln();
            $reporte->Ln();

            if (substr($periodo, 0, 1) == 1)
                $epoca = "FEB-JUN";
            else
                $epoca = "AGO-DIC";
            $year = substr($periodo, 1, 4);

            $reporte->SetX(45);
            $reporte->SetFont('Verdana', '', 8);
            $reporte->MultiCell(0, 2, "REPORTE DE CALIFICACIONES  $epoca  $year", 0, 'C', 0);

            $reporte->Ln();
            $reporte->Ln();
            $reporte->Ln();

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->SetTextColor(0);
            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
            $reporte->SetFont('Verdana', '', 6);

            $reporte->Cell(25, 4, "REGISTRO", 1, 0, 'C', 1);
            $reporte->Cell(70, 4, "NOMBRE", 1, 0, 'C', 1);
            $reporte->Cell(65, 4, "ESPECIALIDAD", 1, 0, 'C', 1);
            $reporte->Cell(30, 4, "TURNO", 1, 0, 'C', 1);

            $mespecialidades->find_first("idtiEsp =" . $alumno->idtiEsp);
            if ($alumno->enTurno == 'M') {
                $turno = "MATUTINO";
            } else {
                $turno = "VESPERTINO";
            }

            $reporte->Ln();
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->Cell(25, 4, $alumno->miReg, 1, 0, 'C', 1);
            $reporte->Cell(70, 4, $alumno->vcNomAlu, 1, 0, 'C', 1);
            $reporte->Cell(65, 4, $mespecialidades->vcNomEsp, 1, 0, 'C', 1);
            $reporte->Cell(30, 4, $turno, 1, 0, 'C', 1);

            $reporte->Ln();
            $reporte->Ln();

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->SetTextColor(0);
            $reporte->SetFont('Verdana', '', 4);
            $reporte->Cell(118, 4, "", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "PRIMER PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "SEGUNDO PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "TERCER PARCIAL", 1, 0, 'C', 1);
            $reporte->Cell(18, 4, "FINAL", 1, 0, 'C', 1);
            $reporte->Ln();

            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
            $reporte->SetFont('Verdana', '', 4);
            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

            $reporte->Cell(7, 4, "GRUPO", 1, 0, 'C', 1);
            $reporte->Cell(7, 4, "CLAVE", 1, 0, 'C', 1);
            $reporte->Cell(7, 4, "ESTADO", 1, 0, 'C', 1);
            $reporte->Cell(47, 4, "NOMBRE", 1, 0, 'C', 1);
            $reporte->Cell(50, 4, "PROFESOR", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "HRS", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "FALT", 1, 0, 'C', 1);
            $reporte->Cell(6, 4, "CALF", 1, 0, 'C', 1);
            $reporte->Ln();

            $Materia = new Materia();
            $calificaciones_array = Array();
            $Periodos = new Periodos();
            $periodos_array = $Periodos->get_todos_periodos_desde_xccursos();
            if (in_array($periodo, $periodos_array)) {
                $xalumnocursos = $mxalumnocursos->find("registro =" . $registro . " 
							and periodo = " . $periodo);

                foreach ($xalumnocursos as $xalumnocurso) {
                    if ($xcurso = $mxcursos->find_first("id = '" . $xalumnocurso->curso_id . "'")) {
                        $no_materias++;
                        $materia = $mmateria->find_first("clave = '" . $xcurso->materia . "' and carrera_id=" . $alumno->carrera_id);
                        if ($maestro = $mmaestros->find_first("nomina = " . $xcurso->nomina)) {
                            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                            $reporte->SetFont('Verdana', '', 4);
                            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
                            $reporte->Cell(7, 4, $xalumnocurso->curso, 1, 0, 'C', 1);
                            $reporte->Cell(7, 4, $materia->clave, 1, 0, 'C', 1);
                            $reporte->Cell(7, 4, "R", 1, 0, 'C', 1);
                            $reporte->Cell(47, 4, trim($materia->nombre), 1, 0, 'C', 1);
                            $reporte->Cell(50, 4, trim($maestro->nombre), 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xcurso->horas1, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xalumnocurso->faltas1, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion1), 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xcurso->horas2, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xalumnocurso->faltas2, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion2), 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xcurso->horas3, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $xalumnocurso->faltas3, 1, 0, 'C', 1);
                            $reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion3), 1, 0, 'C', 1);
                            if ($xalumnocurso->calificacion == 300) {
                                $reporte->Cell(6, 4, "0", 1, 0, 'C', 1);
                                $reporte->Cell(6, 4, "0", 1, 0, 'C', 1);
                            } else {
                                $reporte->Cell(6, 4, $xcurso->horas1 +
                                        $xcurso->horas2 + $xcurso->horas3, 1, 0, 'C', 1);
                                $reporte->Cell(6, 4, $xalumnocurso->faltas1 +
                                        $xalumnocurso->faltas2 + $xalumnocurso->faltas3, 1, 0, 'C', 1);
                            }
                            if ($xalumnocurso->calificacion < 70)
                                $reporte->SetFont('Verdana', '', 5);
                            $reporte->Cell(6, 4, $this->valor($xalumnocurso->calificacion), 1, 0, 'C', 1);
                            if ($xalumnocurso->calificacion < 70)
                                $reporte->SetFont('Verdana', '', 4);
                            $reporte->Ln();
                            if ($this->valor($xalumnocurso->calificacion) == 'NR' ||
                                    $this->valor($xalumnocurso->calificacion) == 'NP' ||
                                    $this->valor($xalumnocurso->calificacion) == 'NPD') {
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $xcurso->materia, 0);
                            } else {
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $xcurso->materia, $xalumnocurso->calificacion);
                            }
                        }
                    }
                }

                $xextraordinarios = $mxextraordinarios->find("registro =" . $registro . "
							and periodo = " . $periodo);
                $contador = 0;
                foreach ($xextraordinarios as $c) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(10, 4, "GRUPO", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                        $reporte->Ln();
                    }

                    $xcur = $mxcursos->find_first("id = '" . $c->curso_id . "'");
                    $materiaa = new Materia();
                    $materia = $materiaa->find_first("clave = '" . $xcur->materia . "' and carrera_id=" . $alumno->carrera_id);
                    $contador++;

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                    $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                    if ($c->tipo == "E")
                        $tipoExamen = "EXTRAORDINARIO";
                    else
                        $tipoExamen = "TITULO";
                    $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                    $reporte->Cell(20, 4, $materia->clave, 1, 0, 'C', 1);
                    $reporte->Cell(55, 4, $materia->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(10, 4, $c->clavecurso, 1, 0, 'C', 1);
                    if ($c->calificacion < 70) {
                        $reporte->SetFont('Verdana', '', 5);
                        $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                        $reporte->SetFont('Verdana', '', 4);
                    } else {
                        $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                    }
                    if ($c->estado == 'OK') {
                        $estatus = "SI";
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $xcur->materia, $c->calificacion);
                    } else {
                        $estatus = "NO";
                        if( isset($calificaciones_array[$xcur->materia]) ){
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $xcur->materia, $calificaciones_array[$xcur->materia]);
                        }
                        else{
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $xcur->materia, 0);
                        }
                    }
                    $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                    $reporte->Ln();
                } // foreach($xextraordinarios as $c)

                $IntersemestralAlumnos = new IntersemestralAlumnos();
                $Periodos = new Periodos();
                $periodo_intersemestral = $Periodos->convertirPeriodo_a_intersemestral($periodo);
                $interAlumnos = $IntersemestralAlumnos->get_cursos_de_un_alumno_($registro, $periodo_intersemestral);

                $contador = 0;
                foreach ($interAlumnos as $inter) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(30, 4, "PROFESOR", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                        $reporte->Ln();
                    }
                    $contador++;

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                    $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                    if ($inter->tipo_ex == "ACR")
                        $tipoExamen = "ACREDITACIÓN";
                    else if ($inter->tipo_ex == "NIV")
                        $tipoExamen = "NIVELACIÓN";
                    $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                    $reporte->Cell(20, 4, $inter->clave_materia, 1, 0, 'C', 1);
                    $reporte->Cell(55, 4, $inter->nombre_materia, 1, 0, 'C', 1);
                    $reporte->Cell(30, 4, $inter->nombre_maestro, 1, 0, 'C', 1);
                    if ($inter->calificacion < 70) {
                        $reporte->SetFont('Verdana', '', 5);
                        $reporte->Cell(20, 4, $this->valor($inter->calificacion), 1, 0, 'C', 1);
                        $reporte->SetFont('Verdana', '', 4);
                    } else {
                        $reporte->Cell(20, 4, $this->valor($inter->calificacion), 1, 0, 'C', 1);
                    }
                    if ($inter->pago == 'OK') {
                        $estatus = "SI";
                        $calificaciones_array = $Materia->get_promedio_calificaciones
                                ($calificaciones_array, $inter->clave_materia, $inter->calificacion);
                    } else {
                        $estatus = "NO";
                        if( isset($calificaciones_array[$inter->clave_materia]) ){
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $inter->clave_materia, $calificaciones_array[$inter->clave_materia]);
                        }
                        else{
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $inter->clave_materia, 0);
                        }
                    }
                    $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                    $reporte->Ln();
                } // foreach($interAlumnos as $inter)

                $globales = new Globales();
                $global = $globales->find("registro =" . $registro . "
							and periodo = " . ($periodo + 10000));
                $contador = 0;
                foreach ($global as $glob) {
                    if ($contador == 0) {
                        $reporte->Ln();
                        $reporte->Ln();
                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                        $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                        $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                        $reporte->Cell(30, 4, "PROFESOR", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);

                        $reporte->Ln();
                    }
                    foreach ($globales->find_all_by_sql(
                            "select ma.clave, ma.nombre as nombreMateria, m.nomina, m.nombre as nombreProf
								From carrera carr, materia ma, maestros m
								where carr.id = " . $alumno->carrera_id . "
								and carr.id = ma.carrera_id
								and ma.clave = '" . $glob->clavemat . "'
								and m.nomina = " . $glob->nomina . "
								limit 1")as $info) {
                        $contador++;

                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                        $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                        if ($glob->tipo_ex == "DP")
                            $tipoExamen = "DERECHO DE PASANTE";
                        else if ($glob->tipo_ex == "EG")
                            $tipoExamen = "EXAMEN GLOBAL";
                        $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $info->clave, 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, $info->nombreMateria, 1, 0, 'C', 1);
                        if ($glob->promedio < 70) {
                            $reporte->SetFont('Verdana', '', 5);
                            $reporte->Cell(20, 4, $this->valor($glob->promedio), 1, 0, 'C', 1);
                            $reporte->SetFont('Verdana', '', 4);
                        } else {
                            $reporte->Cell(20, 4, $this->valor($glob->promedio), 1, 0, 'C', 1);
                        }
                        if ($glob->pago == 'OK') {
                            $estatus = "SI";
                            $calificaciones_array = $Materia->get_promedio_calificaciones
                                    ($calificaciones_array, $glob->clavemat, $glob->promedio);
                        } else {
                            $estatus = "NO";
                            if( isset($calificaciones_array[$glob->clavemat]) ){
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $glob->clavemat, $calificaciones_array[$glob->clavemat]);
                            }
                            else{
                                $calificaciones_array = $Materia->get_promedio_calificaciones
                                        ($calificaciones_array, $glob->clavemat, 0);
                            }
                        }
                        $reporte->Cell(10, 4, $info->nombreProf, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                        $reporte->Ln();
                    } // foreach( $intersemestrales -> find_all_by_sql
                } // foreach($global as $glob)
                $promedio = 0;
                foreach ($calificaciones_array as $calif) {
                    $promedio += $calif;
                }
                $reporte->Ln();
                $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
                $reporte->SetFont('Verdana', '', 5);
                $reporte->Cell(190, 4, "PROMEDIO: " . round($promedio / count($calificaciones_array), 2), 1, 0, 'R', 1);
            } // if (in_array($periodo, $periodos_array))
            else {
                $promedio = 0;
                $xcalificacion = $mxcalificacion->find("miReg =" . $registro);

                foreach ($xcalificacion as $c) {
                    $mishorarios = $mmishorarios->find_first("clavecurso = '" . $c->clavecurso . "'");
                    $no_materias++;
                    $materia = $mmateriasing->find_first("clavemat = '" . $mishorarios->clavemat . "'");
                    $maestro = $mmaestros->find_first("nomina = " . $mishorarios->nomina);
                    $profavance = $mprofavance->find_first("id = '" . $c->clavecurso . "'");

                    $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                    $reporte->SetFont('Verdana', '', 4);
                    $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
                    $reporte->Cell(7, 4, $c->clavecurso, 1, 0, 'C', 1);
                    $reporte->Cell(7, 4, $materia->clavemat, 1, 0, 'C', 1);
                    $reporte->Cell(7, 4, "R", 1, 0, 'C', 1);
                    $reporte->Cell(47, 4, $materia->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(50, 4, $maestro->nombre, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas1, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt1, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal1), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas2, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt2, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal2), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falt3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $this->valor($c->cal3), 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $profavance->horas1 + $profavance->horas2 + $profavance->horas3, 1, 0, 'C', 1);
                    $reporte->Cell(6, 4, $c->falta, 1, 0, 'C', 1);
                    if ($c->califnal < 70)
                        $reporte->SetFont('Verdana', '', 5);
                    $reporte->Cell(6, 4, $this->valor($c->califnal), 1, 0, 'C', 1);
                    if ($c->califnal < 70)
                        $reporte->SetFont('Verdana', '', 4);
                    $reporte->Ln();
                    if ($this->valor($c->califnal) == 'NR' || $this->valor($c->califnal) == 'NP' || $this->valor($c->califnal) == 'NPD') {
                        $suma = 0;
                    } else {
                        $suma = $this->valor($c->califnal);
                    }
                    $promedio = $promedio + $suma;
                }

                $reporte->Ln();
                $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
                $reporte->SetFont('Verdana', '', 5);
                $reporte->Cell(190, 4, "PROMEDIO: " . round($promedio / $no_materias), 1, 0, 'R', 1);
                $reporte->Ln();

                $xextraordinarios = $mxextraordinarios->find("registro =" . $registro);


                $contador = 0;
                foreach ($xextraordinarios as $c) {
                    $mishorarios = $mmishorarios->find_first("clavecurso = '" . $c->clavecurso . "'");
                    if ($mishorarios->periodo == $periodo && $c->estado == 'OK') {
                        if ($contador == 0) {
                            $reporte->Ln();
                            $reporte->Ln();
                            $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                            $reporte->SetFont('Verdana', '', 4);
                            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);

                            $reporte->Cell(10, 4, "No", 1, 0, 'C', 1);
                            $reporte->Cell(35, 4, "TIPO DE EXAMEN", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "CLAVE", 1, 0, 'C', 1);
                            $reporte->Cell(55, 4, "NOMBRE", 1, 0, 'C', 1);
                            $reporte->Cell(10, 4, "GRUPO", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "CALIFICACION", 1, 0, 'C', 1);
                            $reporte->Cell(20, 4, "PAGADO", 1, 0, 'C', 1);
                            $reporte->Ln();
                        }
                        $materia = $mmateriasing->find_first("clavemat = '" . $mishorarios->clavemat . "'");
                        $contador++;

                        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
                        $reporte->SetFont('Verdana', '', 4);
                        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

                        $reporte->Cell(10, 4, $contador, 1, 0, 'C', 1);
                        if ($c->examen == "E")
                            $tipoExamen = "EXTRAORDINARIO";
                        else
                            $tipoExamen = "TITULO";
                        $reporte->Cell(35, 4, $tipoExamen, 1, 0, 'C', 1);
                        $reporte->Cell(20, 4, $materia->clavemat, 1, 0, 'C', 1);
                        $reporte->Cell(55, 4, $materia->nombre, 1, 0, 'C', 1);
                        $reporte->Cell(10, 4, $mishorarios->clavecurso, 1, 0, 'C', 1);
                        if ($c->calificacion < 70) {
                            $reporte->SetFont('Verdana', '', 5);
                            $reporte->Cell(20, 4, $this->valor($c->calificacion), 1, 0, 'C', 1);
                            $reporte->SetFont('Verdana', '', 4);
                        }
                        if ($c->estado == 'OK')
                            $estatus = "SI";
                        else
                            $estatus = "NO";
                        $reporte->Cell(20, 4, $estatus, 1, 0, 'C', 1);
                        $reporte->Ln();
                    }
                }
            }

            $reporte->Ln();
            $reporte->Ln();
            $reporte->Ln();
            $reporte->Ln();
            $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
            $reporte->SetFont('Verdana', '', 7);
            $reporte->Cell(75, 4, "", 1, 0, 'C', 1);
            //$reporte -> Cell(35,4,"ING. CRISTINA GUADALUPE VELAZQUEZ ARREOLA",1,0,'C',1);
            $reporte->Cell(35, 4, "ING. SALVADOR TRINIDAD PEREZ", 1, 0, 'C', 1);
            $reporte->SetDrawColor(0x00, 0x00, 0x00);
            $reporte->Line(70, $reporte->GetY(), 135, $reporte->GetY());
            $reporte->Ln();
            $reporte->SetDrawColor(0xFF, 0xFF, 0xFF);
            $reporte->SetFont('Verdana', '', 6);
            $reporte->Cell(75, 3, "", 1, 0, 'C', 1);
            $reporte->Cell(35, 3, "JEFE DEL DEPTO. DE SERVICIOS DE APOYO ACADEMICO", 1, 0, 'C', 1);


            $reporte->Output("public/files/calificaciones/" . $registro . "" . $this->post("periodo") . ".pdf");

            $this->redirect("public/files/calificaciones/" . $registro . "" . $this->post("periodo") . ".pdf");
        }else {
            $this->redirect("calificaciones/mensaje");
        }
    } // function calificacionesT()

    function verCalificaciones() {

        // Escoger al alumno
        $alumnos = new Alumnos();

        unset($this->alumno);

        $i = 0;
        foreach ($alumnos->find_all_by_sql
                ("Select miReg, vcNomAlu, miPerIng, enPlantel
                                        from alumnos") as $alum) {
            $this->alumno[$i] = $alum;
            $i++;
        }
    } // function verCalificaciones()

    function viendoCalificaciones() {

        $xccursos = new Xccursos();
        $xalumnocursos = new Xalumnocursos();
        $xextras = new Xextraordinarios();

        $xtcursos = new Xtcursos();
        $xtalumnocursos = new Xtalumnocursos();
        $xtextras = new Xtextraordinarios();

        $materia = new Materia();
        $maestros = new Maestros();
        $alumnos = new Alumnos();

        unset($this->ingreso);
        unset($this->ingreso1);
        unset($this->exito);
        unset($this->registro);
        unset($this->nombre);

        $registro = $this->post("registro");

        $alumno = $alumnos->find_first("miReg = " . $registro);

        $this->ingreso = substr($alumno->miPerIng, 1, 4);
        if ($this->ingreso > 2008)
            $this->ingreso = $alumno->miPerIng;
        else
            $this->ingreso = 32008;

        $this->registro = $alumno->miReg;
        $this->nombre = $alumno->vcNomAlu;
        $i = 0;
        // Bandera que se utiliza para saber
        //si si se encontraron datos del alumno
        $this->exito = 0;
        $this->ingreso1 = $this->ingreso;
        while ($this->actual != $this->ingreso && $this->ingreso <= 32010) {
            if ($xalumnocursos->find_first
                            ("registro = " . $registro .
                            " and periodo = " . $this->ingreso)) {

                foreach ($xalumnocursos->find
                        ("periodo = " . $this->ingreso .
                        " and registro = " . $registro) as $xalumno) {

                    foreach ($xccursos->find_all_by_sql
                            ("Select xcc.clavecurso, m.clave, m.nombre, ma.nomina, ma.nombre
                                From xccursos xcc, maestros ma, materia m
                                where xcc.clavecurso = '" . $xalumno->curso . "'
                                and xcc.nomina = ma.nomina
                                and xcc.materia = m.clave
                                group by xcc.clavecurso") as $xccurso) {
                        $this->exito = 1;
                        $this->primerparcial[$this->ingreso][$i] = $xalumno->calificacion1;
                        $this->segundoparcial[$this->ingreso][$i] = $xalumno->calificacion2;
                        $this->tercerparcial[$this->ingreso][$i] = $xalumno->calificacion3;
                        $this->calificacion[$this->ingreso][$i] = $xalumno->calificacion;
                        $i++;
                    }
                }
            } else {
                foreach ($xtalumnocursos->find
                        ("periodo = " . $this->ingreso .
                        " and registro = " . $registro) as $xtalumno) {

                    foreach ($xtcursos->find_all_by_sql
                            ("Select xtc.clavecurso, m.clave, m.nombre, ma.nomina, ma.nombre
                                From xtcursos xtc, maestros ma, materia m
                                where xtc.clavecurso = '" . $xtalumno->curso . "'
                                and xtc.nomina = ma.nomina
                                and xtc.materia = m.clave
                                group by xtc.clavecurso") as $xccurso) {
                        $this->exito = 1;
                        $this->primerparcial[$this->ingreso][$i] = $xtalumno->calificacion1;
                        $this->segundoparcial[$this->ingreso][$i] = $xtalumno->calificacion2;
                        $this->tercerparcial[$this->ingreso][$i] = $xtalumno->calificacion3;
                        $this->calificacion[$this->ingreso][$i] = $xtalumno->calificacion;
                        $i++;
                    }
                }
            }
            $this->ingreso = $this->incrementaPeriodo($this->ingreso);
        } // while( $this -> actual != $this -> ingreso && $this -> ingreso <= 32010 )
    } // function viendoCalificaciones()

    function valor($opc) {

        switch ($opc) {
            case 250 : $r = 90;
                break;
            case 200 : $r = 60;
                break;
            case 300 : $r = "NR";
                break;
            case 999 : $r = "NP";
                break;
            case 500 : $r = "PND";
                break;
            default: $r = $opc;
        }
        return $r;
    } // function valor($opc)

    function incrementaPeriodo($periodo) {

        if (date("m", time()) < 7) {
            $actual = "1" . date("Y", time());
        } else {
            $actual = "3" . date("Y", time());
        }

        $tmp = $periodo;

        if (substr($periodo, 0, 1) == 1) {
            $periodo = "3" . substr($periodo, 1, 4);
        } else {
            $periodo = "1" . (substr($periodo, 1, 4) + 1);
        }

        return $periodo;
    } // function incrementaPeriodo($periodo)

}
?>