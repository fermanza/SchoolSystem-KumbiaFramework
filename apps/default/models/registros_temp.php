<?php			
class registrosTemp extends ActiveRecord {

  /*
   * Funcion que obtiene los registros que se encuentran temporalmente en la tabla registros_temp
  */
  function get_registros($tipo){
  
  	$record = $this->find_all_by_sql("SELECT registro
									  FROM registros_temp
									  WHERE tipo =".$tipo);
									  
	return $record;
	
  }
  
  /*
   * Funcion que vacia la tabla registros_temp
  */
  function truncate_registrosTemp($tipo){
  
    $db = DbBase::raw_connect();
	$db->query("DELETE FROM registros_temp WHERE tipo =".$tipo);
  }

}	
?>