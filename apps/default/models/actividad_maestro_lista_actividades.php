<?php
class ActividadMaestroListaActividades extends ActiveRecord {
	
	function get_lista_actividades_complementarias(){
		$ActividadMaestroListaActividades = new ActividadMaestroListaActividades();
		$listaActividades = array();
		foreach( $ActividadMaestroListaActividades -> find_all_by_sql(
				"select clave, actividad
				from actividad_maestro_lista_actividades
				where tipo = 'COMPLEMENTARIA'
				or clave = 'S-1.4'
				or clave = 'S-1.5'
				or clave = 'S-1.6'
				or clave = 'S-1.7'
				or clave = 'S-1.8'" ) as $lista ){ 
			array_push($listaActividades, $lista);
		}
		return $listaActividades;
	}
}
?>