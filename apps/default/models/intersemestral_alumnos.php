<?php

class IntersemestralAlumnos extends ActiveRecord {

    function get_all_students_by_clavecurso($clavecurso) {
        $alumnos = Array();
        foreach ($this->find_all_by_sql("
                select inter_al.id inter_al_id, al.miReg, al.vcNomAlu, c.nombre carrera_nombre,
                al.enPlan, inter_al.tipo_ex, inter_al.creado_at, maes.nombre maestro_nombre, maes.nomina
                from intersemestral_alumnos inter_al
                inner join alumnos al
                on inter_al.registro = al.miReg
                inner join maestros maes
                on maes.nomina = inter_al.creado_by
                inner join carrera c
                on al.carrera_id = c.id
                where clavecurso = '" . $clavecurso . "'") as $resulset) {
            array_push($alumnos, $resulset);
        }
        return $alumnos;
    } // function get_all_students_by_clavecurso($clavecurso)

    function get_max_id() {
        foreach ($this->find_all_by_sql("
                select max(id) inter_al_id
                from intersemestral_alumnos") as $resulset) {
            return $resulset->maxid;
        }
    } // function get_max_id($clavecurso)

    function get_cursos_de_un_alumno($registro) {
        $Periodos = new Periodos();
        $periodo_intersemestral = $Periodos->get_periodo_actual_intersemestral();
        $cursos_intersemestrales_alumno = Array();
        foreach ($this->find_all_by_sql("
                select icursos.clavecurso,icursos.id AS cursoID, m.clave clave_materia, ialumnos.pago,
                m.nombre nombre_materia, maes.nomina, maes.nombre nombre_maestro,
                ialumnos.calificacion, ialumnos.faltas, ialumnos.tipo_ex, ialumnos.id
                from intersemestral_alumnos ialumnos
                inner join intersemestral_cursos icursos
                on ialumnos.clavecurso = icursos.clavecurso
                inner join materia m
                on icursos.clavemat = m.clave
                inner join maestros maes
                on maes.nomina = icursos.nomina
                inner join carrera c
                on c.id = m.carrera_id
                and ialumnos.registro = '" . $registro . "'
                and ialumnos.periodo = '" . $periodo_intersemestral . "'
                group by ialumnos.id") as $ialumnos) {
            array_push($cursos_intersemestrales_alumno, $ialumnos);
        }
        return $cursos_intersemestrales_alumno;
    } // function get_cursos_de_un_alumno($registro)

    function get_cursos_de_un_alumno_by_periodo($registro, $periodo) {
        $Periodos = new Periodos();
        $periodo_intersemestral = $Periodos->convertirPeriodo_a_intersemestral($periodo);
        $cursos_intersemestrales_alumno = Array();
        foreach ($this->find_all_by_sql("
                select icursos.clavecurso, m.clave clave_materia, ialumnos.pago,
                m.nombre nombre_materia, maes.nomina, maes.nombre nombre_maestro,
                ialumnos.calificacion, ialumnos.faltas, ialumnos.tipo_ex, ialumnos.id
                from intersemestral_alumnos ialumnos
                inner join intersemestral_cursos icursos
                on ialumnos.clavecurso = icursos.clavecurso
                inner join materia m
                on icursos.clavemat = m.clave
                inner join maestros maes
                on maes.nomina = icursos.nomina
                inner join carrera c
                on c.id = m.carrera_id
                and ialumnos.registro = '" . $registro . "'
                and ialumnos.periodo = '" . $periodo_intersemestral . "'
                group by ialumnos.id") as $ialumnos) {
            array_push($cursos_intersemestrales_alumno, $ialumnos);
        }
        return $cursos_intersemestrales_alumno;
    } // function get_cursos_de_un_alumno_by_periodo($registro)

    function get_cursos_de_un_alumno_($registro, $periodo_intersemestral) {
        $cursos_intersemestrales_alumno = Array();
        foreach ($this->find_all_by_sql("
                select icursos.clavecurso, m.clave clave_materia, ialumnos.pago,
                m.nombre nombre_materia, maes.nomina, maes.nombre nombre_maestro,
                ialumnos.calificacion, ialumnos.faltas, ialumnos.tipo_ex, ialumnos.id
                from intersemestral_alumnos ialumnos
                inner join intersemestral_cursos icursos
                on ialumnos.clavecurso = icursos.clavecurso
                inner join materia m
                on icursos.clavemat = m.clave
                inner join maestros maes
                on maes.nomina = icursos.nomina
                inner join carrera c
                on c.id = m.carrera_id
                and ialumnos.registro = '" . $registro . "'
                and ialumnos.periodo = '" . $periodo_intersemestral . "'
                group by ialumnos.id") as $ialumnos) {
            array_push($cursos_intersemestrales_alumno, $ialumnos);
        }
        return $cursos_intersemestrales_alumno;
    } // function get_cursos_de_un_alumno_($registro, $periodo_intersemestral)

    function get_clave_curso_by_inter_al_id($inter_al_id) {
        foreach ($this->find_all_by_sql("
                select clavecurso
                from intersemestral_alumnos
                where id = '" . $inter_al_id . "'") as $inter) {
            return $inter->clavecurso;
        }
    } // function get_clave_curso_by_inter_al_id($inter_al_id)

    function get_listado_de_alumnos_by_clavecurso($clavecurso) {
        $Alumnos = Array();
        foreach ($this->find_all_by_sql("
                select ialumnos.clavecurso,
                al.miReg, al.enPlan, al.vcNomAlu,
                m.nombre nombre_materia, c.nombre nombre_carrera
                from intersemestral_alumnos ialumnos
                inner join intersemestral_cursos icursos on ialumnos.clavecurso = icursos.clavecurso
                inner join alumnos al on ialumnos.registro = al.miReg
                inner join carrera c on c.id= al.carrera_id
                inner join materia m on m.clave = icursos.clavemat
                where m.carrera_id = al.carrera_id
                and ialumnos.clavecurso = '" . $clavecurso . "'") as $alumno) {
            array_push($Alumnos, $alumno);
        }
        return $Alumnos;
    } // function get_listado_de_alumnos_by_clavecurso($clavecurso)
}

?>