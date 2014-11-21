<?php

class estadisticasController extends ApplicationController {

    function index() {

        $this->redirect("profesor/informacion");
    }

    function indicesReprobacionListado() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xalumnocursos();
        $xccursos = new Xccursos();
        $xchoras = new Xchorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);
        unset($this->parcial);
        unset($this->parcialNum);

        $parcial = 1;
        $this->parcialNum = $parcial;

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina
						from xccursos
						where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina
						from xccursos
						where periodo = " . $periodo . "
						and division = '" . Session::get_data("coordinacion") . "'";
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
							From xalumnocursos xal, maestros ma, materia m
							where xal.periodo = " . $periodo . "
							and xal.curso_id = '" . $xcc->id . "'
							and ma.nomina = " . $xcc->nomina . "
							and m.clave = '" . $xcc->materia . "'
							group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($this->aprobados[$i]))
                        $this->aprobados[$i] += 1;
                    else
                        $this->aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($this->reprobados[$i]))
                        $this->reprobados[$i] += 1;
                    else
                        $this->reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($this->np[$i]))
                        $this->np[$i] += 1;
                    else
                        $this->np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($this->sinCalif[$i]))
                        $this->sinCalif[$i] += 1;
                    else
                        $this->sinCalif[$i] = 1;
                }

                $totAl++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta
					From xchorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }
    }

// function indicesReprobacionListado()

    function indicesReprobacionListado2($parcial) {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xalumnocursos();
        $xccursos = new Xccursos();
        $xchoras = new Xchorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();

		$this->periodoLetra($periodo);
		
        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);
        unset($this->parcial);
        unset($this->parcialNum);

        if ($parcial == "" || $parcial == null) {
            $parcial = 1;
        } else {
            $this->parcialNum = $parcial;
            $this->set_response("view");
            $aux = 1;
        }

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina
						from xccursos
						where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina
						from xccursos
						where periodo = " . $periodo . "
						and division = '" . Session::get_data("coordinacion") . "'";
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
							From xalumnocursos xal, maestros ma, materia m
							where xal.periodo = " . $periodo . "
							and xal.curso_id = '" . $xcc->id . "'
							and ma.nomina = " . $xcc->nomina . "
							and m.clave = '" . $xcc->materia . "'
							group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($this->aprobados[$i]))
                        $this->aprobados[$i] += 1;
                    else
                        $this->aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($this->reprobados[$i]))
                        $this->reprobados[$i] += 1;
                    else
                        $this->reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($this->np[$i]))
                        $this->np[$i] += 1;
                    else
                        $this->np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($this->sinCalif[$i]))
                        $this->sinCalif[$i] += 1;
                    else
                        $this->sinCalif[$i] = 1;
                }

                $totAl++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta
					From xchorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        if ($aux == 1)
            $this->render_partial("indicesReprobacionListado2");
    }

// function indicesReprobacionListado2()

    function indicesReprobacionCurso() {
        
    }

    function indicesReprobacionCurso2() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $this->set_response("view");

        $curso = $this->post("buscarcurso");
        $parcial = $this->post("parcial");

        $xalumnocursos = new Xalumnocursos();
        $xccursos = new Xccursos();
        $xchoras = new Xchorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);
		
        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;
        foreach ($xccursos->find_all_by_sql
                ("Select id, clavecurso, materia, nomina from xccursos
				where periodo = " . $periodo . "
				and clavecurso = '" . $curso . "'") as $xcc) {
            $totalAlumnos = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
							From xalumnocursos xal, maestros ma, materia m
							where xal.periodo = " . $periodo . "
							and xal.curso_id = '" . $xcc->id . "'
							and ma.nomina = " . $xcc->nomina . "
							and m.clave = '" . $xcc->materia . "'
							group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    $this->aprobados[$i] += 1;
                } else if ($xal->$tmp < 70) {
                    $this->reprobados[$i] += 1;
                } else if ($xal->$tmp == 999) {
                    $this->np[$i] += 1;
                } else if ($xal->$tmp == 300) {
                    $this->sinCalif[$i] += 1;
                }

                $totalAlumnos++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] += $totalAlumnos;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xchorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        echo $this->render_partial("indicesReprobacionCurso2");
    }

// function indicesReprobacionCurso()

    function indicesReprobacionMateria() {
        $this->valida();
    }

    function indicesReprobacionMateria2() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $this->set_response("view");

        $buscarmateria = $this->post("buscarmateria");
        $parcial = $this->post("parcial");

        $xalumnocursos = new Xalumnocursos();
        $xccursos = new Xccursos();
        $xchoras = new Xchorascursos();
        $materia = new Materia();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        foreach ($materia->find_all_by_sql
                ("Select clave from materia
						where nombre like '%" . $buscarmateria . "%'
						limit 0, 30") as $mat) {
            foreach ($xccursos->find_all_by_sql
                    ("Select id, clavecurso, materia, nomina from xccursos
					where periodo = " . $periodo . "
					and materia = '" . $mat->clave . "'") as $xcc) {
                $totalAlumnos = 0;
                $this->clavecurso[$i] = $xcc->clavecurso;
                foreach ($xalumnocursos->find_all_by_sql
                        ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
						From xalumnocursos xal, maestros ma, materia m
						where xal.periodo = " . $periodo . "
						and xal.curso_id = '" . $xcc->id . "'
						and ma.nomina = " . $xcc->nomina . "
						and m.clave = '" . $xcc->materia . "'
						group by xal.id") as $xal) {

                    if ($xal->$tmp >= 70 &&
                            $xal->$tmp <= 100) {
                        $this->aprobados[$i] += 1;
                    } else if ($xal->$tmp < 70) {
                        $this->reprobados[$i] += 1;
                    } else if ($xal->$tmp == 999) {
                        $this->np[$i] += 1;
                    } else if ($xal->$tmp == 300) {
                        $this->sinCalif[$i] += 1;
                    }

                    $totalAlumnos++;

                    $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                    $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
                }
                $this->totalAlumnos[$i] += $totalAlumnos;

                foreach ($xchoras->find_all_by_sql
                        ("select count(*) cuenta From xchorascursos
						where curso_id = '" . $xcc->id . "'") as $horas) {
                    $this->horasClase[$i] = $horas->cuenta;
                }
                $i++;
            }
        }
        echo $this->render_partial("indicesReprobacionMateria2");
    }

// function indicesReprobacionCurso()

    function exportarAExcel($parcial) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=archivo.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xalumnocursos();
        $xccursos = new Xccursos();
        $xchoras = new Xchorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        switch ($parcial) {
            case 1: $parcialLetra = "Primer Parcial";
                break;
            case 2: $parcialLetra = "Segundo Parcial";
                break;
            case 3: $parcialLetra = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        if ($parcial == "" || $parcial == null) {
            $parcial = 1;
        } else {
            $aux = 1;
        }

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina, division from xccursos
							where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina from xccursos
						where periodo = " . $periodo . "
						and division = '" . Session::get_data("coordinacion") . "'";
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $clavecurso[$i] = $xcc->clavecurso;
            $division[$i] = $xcc->division;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, m.plan, ma.nomina, m.nombre as nombremat, m.clave
							From xalumnocursos xal, maestros ma, materia m
							where xal.periodo = " . $periodo . "
							and xal.curso_id = '" . $xcc->id . "'
							and ma.nomina = " . $xcc->nomina . "
							and m.clave = '" . $xcc->materia . "'
							group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($aprobados[$i]))
                        $aprobados[$i] += 1;
                    else
                        $aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($reprobados[$i]))
                        $reprobados[$i] += 1;
                    else
                        $reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($np[$i]))
                        $np[$i] += 1;
                    else
                        $np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($sinCalif[$i]))
                        $sinCalif[$i] += 1;
                    else
                        $sinCalif[$i] = 1;
                }

                $totAl++;
                $plan[$i] = $xal->plan;
                $clavemateria[$i] = $xal->clave;
                $materia[$i] = $xal->nombremat;
                $nomina[$i] = $xal->nomina;
                $maestro[$i] = $xal->nombreprof;
            }
            $totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xchorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        echo
        "<h3 align='center'>$parcialLetra</h3>
			<table cellspacing='1' cellpadding='1' style='font-size: 8px;' border='1' align='center' width='80%'>
				<tr>
					<th style='background:#FF7F27' width='100' align='center'>
						<h3>Clave Curso</h3>
					</th>
					<th style='background:#FF7F27' width='75' align='center'>
						<h3>Division</h3>
					</th>
					<th style='background:#FF7F27' width='75' align='center'>
						<h3>Clave Materia</h3>
					</th>
					<th style='background:#FF7F27' width='300' align='center'>
						<h3>Materia</h3>
					</th>
					<th style='background:#FF7F27' width='75' align='center'>
						<h3>Plan</h3>
					</th>
					<th style='background:#FF7F27' width='75' align='center'>
						<h3>Nomina</h3>
					</th>
					<th style='background:#FF7F27' width='300' align='center'>
						<h3>Maestro</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Horas Clase</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos Inscritos</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos con NP</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos sin Calif</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos Reprobados</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos Aprobados</h3>
					</th>
				</tr>";
        for ($i = 0; $i < count($clavecurso); $i++) {
            echo "<tr>
					<td width='100' align='center'>
						<h4>$clavecurso[$i]</h4>
					</td>
					<td width='75' align='center'>
						<h4>$division[$i]</h4>
					</td>
					<td width='75' align='center'>
						<h3>$clavemateria[$i]</h3>
					</th>
					<td width='300' align='center'>
						<h4>$materia[$i]</h4>
					</td>
					<td width='75' align='center'>
						<h4>$plan[$i]</h4>
					</td>
					<td width='75' align='center'>
						<h4>$nomina[$i]</h4>
					</td>
					<td width='300' align='center'>
						<h4>$maestro[$i]</h4>
					</td>
					<td width='110' align='center'>
						<h4>$horasClase[$i]</h4>
					</td>
					<td width='110' align='center'>
						<h4>$totalAlumnos[$i]</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($np[$i])) {
                echo "$np[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($sinCalif[$i])) {
                echo "$sinCalif[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($reprobados[$i])) {
                echo "$reprobados[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($aprobados[$i])) {
                echo "$aprobados[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
				</tr>";
        }
        echo "
			</table>";
        exit(1);
    }

// function exportarAExcel()

    function indicesReprobacionListadoT() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xtalumnocursos();
        $xccursos = new Xtcursos();
        $xchoras = new Xthorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);
        unset($this->parcial);
        unset($this->parcialNum);

        $parcial = 1;
        $this->parcialNum = $parcial;

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina from xtcursos
						where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina from xtcursos
						where periodo = " . $periodo;
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
					From xtalumnocursos xal, maestros ma, materia m
					where xal.periodo = " . $periodo . "
					and xal.curso_id = '" . $xcc->id . "'
					and ma.nomina = " . $xcc->nomina . "
					and m.clave = '" . $xcc->materia . "'
					group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($this->aprobados[$i]))
                        $this->aprobados[$i] += 1;
                    else
                        $this->aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($this->reprobados[$i]))
                        $this->reprobados[$i] += 1;
                    else
                        $this->reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($this->np[$i]))
                        $this->np[$i] += 1;
                    else
                        $this->np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($this->sinCalif[$i]))
                        $this->sinCalif[$i] += 1;
                    else
                        $this->sinCalif[$i] = 1;
                }

                $totAl++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xthorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }
    }

// function indicesReprobacionListadoT()

    function indicesReprobacionListadoT2($parcial) {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xtalumnocursos();
        $xccursos = new Xtcursos();
        $xchoras = new Xthorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);
        unset($this->parcial);
        unset($this->parcialNum);

        if ($parcial == "" || $parcial == null) {
            $parcial = 1;
        } else {
            $this->parcialNum = $parcial;
            $this->set_response("view");
            $aux = 1;
        }

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina from xtcursos
						where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina from xtcursos
						where periodo = " . $periodo;
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
					From xtalumnocursos xal, maestros ma, materia m
					where xal.periodo = " . $periodo . "
					and xal.curso_id = '" . $xcc->id . "'
					and ma.nomina = " . $xcc->nomina . "
					and m.clave = '" . $xcc->materia . "'
					group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($this->aprobados[$i]))
                        $this->aprobados[$i] += 1;
                    else
                        $this->aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($this->reprobados[$i]))
                        $this->reprobados[$i] += 1;
                    else
                        $this->reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($this->np[$i]))
                        $this->np[$i] += 1;
                    else
                        $this->np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($this->sinCalif[$i]))
                        $this->sinCalif[$i] += 1;
                    else
                        $this->sinCalif[$i] = 1;
                }

                $totAl++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xthorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        if ($aux == 1)
            $this->render_partial("indicesReprobacionListado2");
    }

// function indicesReprobacionListadoT2()

    function indicesReprobacionCursoT() {
        
    }

    function indicesReprobacionCursoT2() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $this->set_response("view");

        $curso = $this->post("buscarcurso");
        $parcial = $this->post("parcial");
		
        $xalumnocursos = new Xtalumnocursos();
        $xccursos = new Xtcursos();
        $xchoras = new Xthorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;
        foreach ($xccursos->find_all_by_sql
                ("Select id, clavecurso, materia, nomina from xtcursos
				where periodo = " . $periodo . "
				and clavecurso = '" . $curso . "'") as $xcc) {
            $totalAlumnos = 0;
            $this->clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
                    ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
					From xtalumnocursos xal, maestros ma, materia m
					where xal.periodo = " . $periodo . "
					and xal.curso_id = '" . $xcc->id . "'
					and ma.nomina = " . $xcc->nomina . "
					and m.clave = '" . $xcc->materia . "'
					group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    $this->aprobados[$i] += 1;
                } else if ($xal->$tmp < 70) {
                    $this->reprobados[$i] += 1;
                } else if ($xal->$tmp == 999) {
                    $this->np[$i] += 1;
                } else if ($xal->$tmp == 300) {
                    $this->sinCalif[$i] += 1;
                }

                $totalAlumnos++;

                $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $this->totalAlumnos[$i] += $totalAlumnos;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xthorascursos
					where curso_id = '" . $xcc->id . "'") as $horas) {
                $this->horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        echo $this->render_partial("indicesReprobacionCurso2");
    }

// function indicesReprobacionCursoT()

    function indicesReprobacionMateriaT() {
        $this->valida();
    }

    function indicesReprobacionMateriaT2() {

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $this->set_response("view");

        $buscarmateria = $this->post("buscarmateria");
        $parcial = $this->post("parcial");

        $xalumnocursos = new Xtalumnocursos();
        $xccursos = new Xtcursos();
        $xchoras = new Xthorascursos();
        $materia = new Materia();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        unset($this->xalumnocursos);
        unset($this->aprobados);
        unset($this->reprobados);
        unset($this->np);
        unset($this->sinCalif);
        unset($this->totalAlumnos);
        unset($this->clavecurso);
        unset($this->horasClase);

        switch ($parcial) {
            case 1: $this->parcial = "Primer Parcial";
                break;
            case 2: $this->parcial = "Segundo Parcial";
                break;
            case 3: $this->parcial = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        foreach ($materia->find_all_by_sql
                ("Select clave from materia
				where nombre like '%" . $buscarmateria . "%'
				limit 0, 30") as $mat) {
            foreach ($xccursos->find_all_by_sql
                    ("Select id, clavecurso, materia, nomina from xtcursos
					where periodo = " . $periodo . "
					and materia = '" . $mat->clave . "'") as $xcc) {
                $totalAlumnos = 0;
                $this->clavecurso[$i] = $xcc->clavecurso;
                foreach ($xalumnocursos->find_all_by_sql
                        ("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
						From xtalumnocursos xal, maestros ma, materia m
						where xal.periodo = " . $periodo . "
						and xal.curso_id = '" . $xcc->id . "'
						and ma.nomina = " . $xcc->nomina . "
						and m.clave = '" . $xcc->materia . "'
						group by xal.id") as $xal) {

                    if ($xal->$tmp >= 70 &&
                            $xal->$tmp <= 100) {
                        $this->aprobados[$i] += 1;
                    } else if ($xal->$tmp < 70) {
                        $this->reprobados[$i] += 1;
                    } else if ($xal->$tmp == 999) {
                        $this->np[$i] += 1;
                    } else if ($xal->$tmp == 300) {
                        $this->sinCalif[$i] += 1;
                    }

                    $totalAlumnos++;

                    $this->materia[$i] = $xal->clave . " - " . $xal->nombremat;
                    $this->maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
                }
                $this->totalAlumnos[$i] += $totalAlumnos;

                foreach ($xchoras->find_all_by_sql
                        ("select count(*) cuenta From xthorascursos
							where curso_id = '" . $xcc->id . "'") as $horas) {
                    $this->horasClase[$i] = $horas->cuenta;
                }
                $i++;
            }
        }
        echo $this->render_partial("indicesReprobacionMateria2");
    }

// function indicesReprobacionCursoT()

    function exportarAExcelT($parcial) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=archivo.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $this->valida();

        // �ndicies de reprobaci�n. Curso, horas clase por curso,o 
        //numero de alumnos, cu�ntos alumnos con np, alumnos reprobados,
        //alumnos aprobados,  ver por filtros.
        //$xperiodoscaptura	= new Xperiodoscaptura();

        $xalumnocursos = new Xtalumnocursos();
        $xccursos = new Xtcursos();
        $xchoras = new Xthorascursos();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
		
		$this->periodoLetra($periodo);

        switch ($parcial) {
            case 1: $parcialLetra = "Primer Parcial";
                break;
            case 2: $parcialLetra = "Segundo Parcial";
                break;
            case 3: $parcialLetra = "Tercer Parcial";
                break;
        }

        $tmp = "calificacion" . $parcial;

        $i = 0;

        if ($parcial == "" || $parcial == null) {
            $parcial = 1;
        } else {
            $aux = 1;
        }

        // Si es igual a "NOESCOORDINADOR" significa
        //que el usuario que est� intentando ver
        //es Cristina o Ricardo.
        if ((Session::get_data("coordinacion") == "NOESCOORDINADOR") ||
                Session::get_data("tipousuario") == "OBSERVADOR") {
            $busqueda = "Select id, clavecurso, materia, nomina from xccursos
						where periodo = " . $periodo;
        } else {
            $busqueda = "Select id, clavecurso, materia, nomina from xtcursos
						where periodo = " . $periodo;
        }
        foreach ($xccursos->find_all_by_sql
                ($busqueda) as $xcc) {
            $totAl = 0;
            $clavecurso[$i] = $xcc->clavecurso;
            foreach ($xalumnocursos->find_all_by_sql
					("Select xal.*, ma.nombre nombreprof, ma.nomina, m.nombre as nombremat, m.clave
					From xtalumnocursos xal, maestros ma, materia m
					where xal.periodo = " . $periodo . "
					and xal.curso_id = '" . $xcc->id . "'
					and ma.nomina = " . $xcc->nomina . "
					and m.clave = '" . $xcc->materia . "'
					group by xal.id") as $xal) {

                if ($xal->$tmp >= 70 &&
                        $xal->$tmp <= 100) {
                    if (isSet($aprobados[$i]))
                        $aprobados[$i] += 1;
                    else
                        $aprobados[$i] = 1;
                }
                else if ($xal->$tmp < 70) {
                    if (isSet($reprobados[$i]))
                        $reprobados[$i] += 1;
                    else
                        $reprobados[$i] = 1;
                }
                else if ($xal->$tmp == 999) {
                    if (isSet($np[$i]))
                        $np[$i] += 1;
                    else
                        $np[$i] = 1;
                }
                else if ($xal->$tmp == 300) {
                    if (isSet($sinCalif[$i]))
                        $sinCalif[$i] += 1;
                    else
                        $sinCalif[$i] = 1;
                }

                $totAl++;

                $materia[$i] = $xal->clave . " - " . $xal->nombremat;
                $maestro[$i] = $xal->nomina . " - " . $xal->nombreprof;
            }
            $totalAlumnos[$i] = $totAl;

            foreach ($xchoras->find_all_by_sql
                    ("select count(*) cuenta From xthorascursos
						where curso_id = '" . $xcc->id . "'") as $horas) {
                $horasClase[$i] = $horas->cuenta;
            }
            $i++;
        }

        echo
        "<h3 align='center'>$parcialLetra</h3>
			<table cellspacing='1' cellpadding='1' style='font-size: 8px;' border='1' align='center' width='80%'>
				<tr>
					<th style='background:#FF7F27' width='100' align='center'>
						<h3>Clave Curso</h3>
					</th>
					<th style='background:#FF7F27' width='350' align='center'>
						<h3>Materia</h3>
					</th>
					<th style='background:#FF7F27' width='350' align='center'>
						<h3>Maestro</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Horas Clase</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Total Alumnos</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos con NP</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos sin Calif</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos Reprobados</h3>
					</th>
					<th style='background:#FF7F27' width='110' align='center'>
						<h3>Alumnos Aprobados</h3>
					</th>
				</tr>";
        for ($i = 0; $i < count($clavecurso); $i++) {
            echo "<tr>
                <td width='100' align='center'>
                        <h4>$clavecurso[$i]</h4>
                </td>
                <td width='350' align='center'>
                        <h4>$materia[$i]</h4>
                </td>
                <td width='350' align='center'>
                        <h4>$maestro[$i]</h4>
                </td>
                <td width='110' align='center'>
                        <h4>$horasClase[$i]</h4>
                </td>
                <td width='110' align='center'>
                        <h4>$totalAlumnos[$i]</h4>
                </td>
                <td width='110' align='center'>
                        <h4>";
            if (isSet($np[$i])) {
                echo "$np[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($sinCalif[$i])) {
                echo "$sinCalif[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($reprobados[$i])) {
                echo "$reprobados[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
					<td width='110' align='center'>
						<h4>";
            if (isSet($aprobados[$i])) {
                echo "$aprobados[$i]";
            } else {
                echo '-';
            } echo "</h4>
					</td>
				</tr>";
        }
        echo "
			</table>";
        exit(1);
    } // function exportarAExcelT()
	
	function periodoLetra($periodo){
		$anio = (substr($periodo, 1));
		if( substr($periodo, 0, 1) == 1)
			$this -> periodoLetra = "FEB - JUN, ".$anio;
		else
			$this -> periodoLetra = "AGO - DIC, ".$anio;
	} // function periodoLetra()
	
    function valida() {
        if (Session::get_data('coordinador') != "OK" &&
                Session::get_data("tipousuario") != "OBSERVADOR")
            $this->redirect('general/inicio');
    } // function valida()
}

?>