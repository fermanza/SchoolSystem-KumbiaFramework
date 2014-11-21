<?php
	class Areadeformacion extends ActiveRecord {
		
		function get_nombre_areadeformacion($carrera_id, $areadeformacion_id){
			foreach($this -> find_all_by_sql(
					"select nombreareaformacion
					from areadeformacion
					where carrera_id = ".$carrera_id."
					and idareadeformacion = ".$areadeformacion_id) as $area){
				$areadeformacion = $area;
			}
			return $areadeformacion;
		} // function get_nombre_areadeformacion($areadeformacion_id)
	}
?>