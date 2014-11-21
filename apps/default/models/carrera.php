<?php
	class Carrera extends ActiveRecord {
		function get_nombre_carrera($carrera_id){
			foreach( $this -> find_all_by_sql("
				select nombre
				from carrera
				where id = ".$carrera_id) as $resultset){
				$carrera = $resultset;
			}
			return $carrera;
		} // get_nombre_carrera($carrera_id)
		
		function get_nombre_carrera_and_areadeformacion($alumno){
			foreach( $this -> find_all_by_sql("
				select c.nombre, a.nombreareaformacion
				from carrera c
				inner join areadeformacion a
				on c.id = a.carrera_id
				where c.id = '".$alumno -> carrera_id."'
				and idareadeformacion = '".$alumno -> areadeformacion_id."'") as $resultset){
				$nombrecompleto = $resultset;
			}
			return $nombrecompleto;
		} // get_nombre_carrera_and_areadeformacion($alumno)
		
		function get_nombre_carrera_and_areadeformacion_($alumno){
			foreach( $this -> find_all_by_sql("
				select nombre
				from carrera
				where id = ".$alumno->carrera_id) as $resultset){
				$nombrecompleto = $resultset -> nombre;
			}
			foreach( $this -> find_all_by_sql("
				select c.nombre, a.nombreareaformacion
				from carrera c
				inner join areadeformacion a
				on c.id = a.carrera_id
				where c.id = '".$alumno -> carrera_id."'
				and idareadeformacion = '".$alumno -> areadeformacion_id."'") as $resultset){
				$nombrecompleto = $resultset->nombre." ".$resultset->nombreareaformacion;
			}
			return $nombrecompleto;
		} // get_nombre_carrera_and_areadeformacion_($alumno)
		
		function get_todas_las_carreras_segun_plan_de_estudios($plan_estudios){
			$carrera = Array();
			foreach( $this -> find_all_by_sql("
					select nombre, id
					from carrera
					where modelo = '".$plan_estudios."'
					and id < 10") as $resultset){
				array_push($carrera, $resultset);
			}
			return $carrera;
		} // function get_todas_las_carreras_segun_plan_de_estudios($plan_estudios)
		
		function get_carreras_by_division($division){
			$carrera = Array();
			foreach( $this -> find_all_by_sql("
					select *
					from carrera
					where division = '".$division."'") as $resultset){
				array_push($carrera, $resultset);
			}
			return $carrera;
		} // function get_carreras_by_division($plan_estudios)
		
		function get_carreras_by_division_and_plan_estudios($division, $plan_estudios){
			$carrera = Array();
			foreach( $this -> find_all_by_sql("
					select *
					from carrera
					where division = '".$division."'
					and modelo = '".$plan_estudios."'") as $resultset){
				array_push($carrera, $resultset);
			}
			return $carrera;
		} // function get_carreras_by_division_and_plan_estudios($division, $plan_estudios)
	}
?>