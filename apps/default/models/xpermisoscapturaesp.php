<?php
	class Xpermisoscapturaesp extends ActiveRecord {
		
		function get_alumnos_conpermisosespeciales($xpermisoscapturaesp_id){
			$registros = Array();
			foreach( $this -> find_all_by_sql("
					select registro
					from xpermisoscapturaesp_detalle
					where xpermisoscapturaesp_id = ".$xpermisoscapturaesp_id) as $resulset){
				array_push($registros, $resulset -> registro);
			}
			return $registros;
		} // function get_alumnos_conpermisosespeciales()
		
	}
?>