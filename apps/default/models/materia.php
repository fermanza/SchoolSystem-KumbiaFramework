<?php
class Materia extends ActiveRecord {

    function get_all_giving_careerID_($carrera_id) {
        $arrayMateria = array();
        foreach ($this->find_all_by_sql(
				"select * from materia
				where carrera_id = " . $carrera_id . "
				order by semestre, clave") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_all_giving_careerID_($carrera_id)

    function get_all_giving_careerID_no_basicas($carrera_id) {
        $arrayMateria = array();
        foreach ($this->find_all_by_sql(
                "select * from materia
					where carrera_id = " . $carrera_id . "
					-- and division != 'TCB'
					order by semestre, clave") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_all_giving_careerID_no_basicas($carrera_id)

    function get_all_giving_careerID($carrera_id, $areadeformacion_id) {
        $arrayMateria = array();
        foreach ($this->find_all_by_sql(
                "select * from materia
					where carrera_id = " . $carrera_id . "
					and ( serie = '" . $areadeformacion_id . "'
					or serie = '-' )
					order by semestre, clave") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_all_giving_careerID($carrera_id, $areadeformacion_id)
	
    function get_all_giving_alumno($alumno){
        $arrayMateria = array();
		$second_id = '-';
		if($alumno->carrera_id==4){
			$KardexIng = new KardexIng();
			$second_id = $KardexIng->get_second_areadeformacion_id($alumno);
		}
        foreach ($this->find_all_by_sql(
                "select * from materia
					where carrera_id = " . $alumno->carrera_id . "
					and ( serie = '" . $alumno->areadeformacion_id . "'
					or serie = '-' 
					or serie = '".$second_id."')
					order by semestre, clave") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_all_giving_alumno($alumno)

    function get_materias_areadeformacion($carrera_id, $areadeformacion_id) {
        $arrayMateria = array();
        foreach ($this->find_all_by_sql(
                "select * from materia
					where carrera_id = " . $carrera_id . "
					and serie = '" . $areadeformacion_id . "'
					order by semestre, clave") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_materias_areadeformacion($carrera_id, $areadeformacion_id)

    function get_materias_optativas_generales($alumno) {
        $arrayMateria = array();
		
        foreach ($this->find_all_by_sql(
                "select m.clave, m.nombre
					from kardex_ing k
					inner join materia m
					on k.clavemat = m.clave
					where registro = '" . $alumno->miReg . "'
					and m.carrera_id = '" . $alumno->carrera_id . "'
					and optativa = 2") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_materias_optativas_generales($alumno)

    function get_materias_optativas_especializantes($alumno) {
        $arrayMateria = array();
        foreach ($this->find_all_by_sql(
                "select m.clave, m.nombre
					from kardex_ing k
					inner join materia m
					on k.clavemat = m.clave
					where registro = '" . $alumno->miReg . "'
					and m.carrera_id = '" . $alumno->carrera_id . "'
					and (serie != '-'
					and serie != 0)
					and optativa = 1") as $mat) {
            array_push($arrayMateria, $mat);
        }
        return $arrayMateria;
    } // function get_materias_optativas_especializantes($alumno)

    function get_prerrequisitos($clave, $alumno) {
        $kardexIng = new KardexIng();

        $arrayPrerrequisitos = array();

        foreach ($this->find_all_by_sql(
                "select p.clavemat
					from materia m
					inner join prerrequisito p
					on m.prerrequisito = id_prereq
					where m.clave = '" . $clave . "'
					and m.carrera_id = " . $alumno->carrera_id . "
					and (serie = '" . $alumno->areadeformacion_id . "'
					or serie = '-')") as $mat) {
            $mat2 = $this->find_first("clave = '" . $mat->clavemat . "' and carrera_id = " . $alumno->carrera_id);
            if ($kardexIng->find_first("registro = " . $alumno->miReg . " and clavemat = '" . $mat->clavemat . "'")) {
                $mat2->silatiene = 1;
            } else {
                $mat2->silatiene = 0;
            }
            array_push($arrayPrerrequisitos, $mat2);
        }
        if (count($arrayPrerrequisitos) == 0) {
            $mat2->nombre = "No tiene prerrequisitos";
            $mat2->silatiene = 1;

            array_push($arrayPrerrequisitos, $mat2);
        }
        return $arrayPrerrequisitos;
    } // function get_prerrequisitos($clave, $alumno)

    function get_materiasReprobadas_semPasado($alumno, $materiasEnKardex, $materiasQuePuedeSeleccionar, $xkpardexSoloClave) {
        $temp = array();
        foreach ($this->pendientes as $mat) {
            array_push($temp, $mat->clave);
        }
		$Periodos = new Periodos();
		$periodo_anterior = $Periodos->get_periodo_anterior();
        foreach ($this->find_all_by_sql("
						select xcc.materia clave, m.nombre nombre
						from xextraordinarios xext
						inner join xccursos xcc
						on xcc.id = xext.curso_id
						inner join materia m
						on xcc.materia = m.clave
						where xext.registro = " . $alumno->miReg . "
						and xext.periodo = '".$periodo_anterior."'
						and (xext.calificacion < 70
						or xext.calificacion > 100)
						and m.carrera_id = " . $alumno->carrera_id . "") as $m) {
            if ((!in_array($m->clave, $materiasEnKardex)) &&
                    (!in_array($m, $materiasQuePuedeSeleccionar)) &&
                    (!in_array($m->clave, $xkpardexSoloClave)) &&
                    (!in_array($m->clave, $temp))) {
                array_push($materiasQuePuedeSeleccionar, $m);
            }
        }
        foreach ($this->find_all_by_sql("
						select xcc.materia clave, m.nombre
						from xtextraordinarios xext
						inner join xtcursos xcc
						on xcc.id = xext.curso_id
						inner join materia m
						on xcc.materia = m.clave
						where xext.registro = " . $alumno->miReg . "
						and xext.periodo = '".$periodo_anterior."'
						and (xext.calificacion < 70
						or xext.calificacion > 100)
						and m.carrera_id = " . $alumno->carrera_id . "") as $m) {
            if ((!in_array($m->clave, $materiasEnKardex)) &&
                    (!in_array($m, $this->materiasQuePuedeSeleccionar)) &&
                    (!in_array($m->clave, $this->xkpardexSoloClave)) &&
                    (!in_array($m->clave, $temp))) {
                array_push($materiasQuePuedeSeleccionar, $m);
            }
        }
        return $materiasQuePuedeSeleccionar;
    } // function get_materiasReprobadas_semPasado($alumno, $materiasEnKardex, $materiasQuePuedeSeleccionar, $xkpardexSoloClave)

    function obtenerCreditos($clave, $alumno) {
        foreach ($this->find_all_by_sql("
					SELECT creditos
					FROM materia
					WHERE clave='" . $clave . "' 
					AND carrera_id=" . $alumno->carrera_id . "
					AND (serie = '" . $alumno->areadeformacion_id . "'
					OR serie = '-'")as $mat) {
            $materia = $mat->creditos;
        }
        return $materia;
    } // function obtenerCreditos($clave, $plan)

    function get_nombre_materia($clave, $alumno) {
        foreach ($this->find_all_by_sql("
					SELECT clave, nombre
					FROM materia
					WHERE clave='" . $clave . "' 
					AND carrera_id=" . $alumno->carrera_id . "
					AND (serie = '" . $alumno->areadeformacion_id . "'
					or serie = '-')")as $mat) {
            $materia = $mat;
        }
        return $materia;
    } // function get_nombre_materia($clave, $alumno)
	
    function get_materia_by_clave_alumno($clave, $alumno){
		$second_id = '-';
		if($alumno->carrera_id==4){
			$KardexIng = new KardexIng();
			$second_id = $KardexIng->get_second_areadeformacion_id($alumno);
		}
        foreach ($this->find_all_by_sql("
					SELECT *
					FROM materia
					WHERE clave='" . $clave . "' 
					AND carrera_id=" . $alumno->carrera_id . "
					AND (serie = '" . $alumno->areadeformacion_id . "'
					or serie = '-'
					or serie = '".$second_id."')
					")as $mat) {
            $materia = $mat;
        }
        return $materia;
    } // function get_materia_by_clave_alumno($clave, $alumno)

    function get_prerrequisitos_de_una_materia($clavemat, $carrera_id, $areadeformacion_id) {
        $materias = Array();
        foreach ($this->find_all_by_sql("
					select mat_p.clavemat
					from materia mat
					inner join materia_prerequisito mat_p
					on mat.prerrequisito = mat_p.id_prereq
					where clave = '" . $clavemat . "'
					and carrera_id = '" . $carrera_id . "'
					and (serie = '-'
					or serie = '" . $areadeformacion_id . "')
					and id_prereq != '0'") as $resultset) {
            array_push($materias, $resultset);
        }
        return $materias;
    } // function get_prerrequisitos_de_una_materia($clavemat, $carrera_id, $areadeformacion_id)

    function get_promedio_calificaciones($calificaciones_array, $clavemat, $promedio) {
        $calificaciones_array[$clavemat] = (int)$promedio;
        return $calificaciones_array;
    } // function get_promedio_calificaciones($calificaciones_array, $clavemat, $promedio)

}
?>