<?php
	class XalumnosPersonal extends ActiveRecord {
		
		function update_servicio_medico($servicio_medico, $registro){
			foreach($this->find_all_by_sql("update xalumnos_personal
						set seguridad_social = '".$servicio_medico -> seguridad_social."',
						dependencia_seguro = '".$servicio_medico -> dependencia_seguro."',
						parte_de_quien_seguro = '".$servicio_medico -> parte_de_quien_seguro."',
						numClinica = '".$servicio_medico -> numClinica."',
						fechaMovimiento = '".$servicio_medico -> fechaMovimiento."',
						numero_seguro_social = '".$servicio_medico -> numero_seguro_social."'
						where registro = '".$registro."'") as $servicio){
				break;
			}
		} // function update_servicio_medico($servicio_medico, $registro)
		
	}
?>