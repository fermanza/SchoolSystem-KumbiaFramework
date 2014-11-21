<?php
	class CursosPlanRigido extends ActiveRecord {
		
		function get_all_salones_by_periodo($periodo){
			$salones = array();
			foreach( $this->find_all_by_sql("
					select cpr.salon_id, CONCAT(xcs.edificio,':',xcs.nombre) nombre_salon
					from cursos_plan_rigido cpr
					join xcsalones xcs on cpr.salon_id = xcs.id
					where cpr.periodo = '".$periodo."'
					group by salon_id") as $salon ){
				array_push($salones, $salon);
			}
			return $salones;
		} // function get_all_salones_by_periodo($periodo)
		
		function get_all_cursos_by_periodo_and_salon($periodo, $salones){
			$cursos = array();
			foreach($salones as $salon){
				unset($curso_tmp);
				$curso_tmp = array();
				foreach( $this->find_all_by_sql("
						select xcc.id curso_id, xcc.clavecurso, xcc.materia, xcc.periodo,
						xcc.nomina, xcc.cupo, xcc.disponibilidad, (xcc.cupo-xcc.disponibilidad) inscritos,
						cpr.salon_id, cpr.plantel, cpr.nivel
						from xccursos xcc
						join cursos_plan_rigido cpr on cpr.curso_id = xcc.id
						where xcc.periodo = '".$periodo."'
						and cpr.salon_id = '".$salon->salon_id."'") as $cur){
					array_push($curso_tmp, $cur);
				}
				$cursos[$salon->salon_id] = $curso_tmp;
			};
			return $cursos;
		} // function get_all_cursos_by_periodo_and_salon($periodo, $salones)
		
		function get_ids_cursos_by_salon_id($salon_id){
			$cursos = array();
			foreach( $this->find_all_by_sql("
						select *
						from cursos_plan_rigido
						where salon_id = '".$salon_id."'") as $curso){
				array_push($cursos, $curso->curso_id);
			}
			return $cursos;
		} // function get_ids_cursos_by_salon_id($salon_id)
		
	}
?>