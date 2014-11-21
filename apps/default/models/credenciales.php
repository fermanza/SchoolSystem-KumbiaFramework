<?php			
class Credenciales extends ActiveRecord {
	
  /*
   * Funcion que sirve para contar los dias faltantes para cambiar de mes
   * 
  */
  function dias_restantes(){
   
	if(date('m') == 12){
	  $nextYear = date("Y") + 1;
	  $fecha_final = $nextYear."-01-01";
	}
	
	else{
	  $siguienteMes = date('m') + 1;
	  if(strlen($siguienteMes) <= 1)
	    $nextMes = "0".$siguienteMes;
		
	  else
	    $nextMes = $siguienteMes;
		
	  $fecha_final = date("Y")."-".$nextMes."-01";
	}

	$fecha_actual = date("Y-m-d");

	$s = strtotime($fecha_final)-strtotime($fecha_actual);
	$d = intval($s/86400);
	$diferencia = $d;
	
    //Obtiene el nombre del mes de vigencia
    if($diferencia <= 12) //5
	  $nameMes = date('m') + 2; //1
	
	else
	  $nameMes = date('m') + 1;

	 switch ($nameMes)
    {
      case '01': $mes = "Enero";
        break;
      case '02': $mes = "Febrero";
        break;
      case '03': $mes = "Marzo";
        break;
      case '04': $mes = "Abril";
        break;
      case '05': $mes = "Mayo";
        break;
      case '06': $mes = "Junio";
        break;
      case '07': $mes = "Julio";
        break;
      case '08': $mes = "Agosto";
        break;
      case '09': $mes = "Septiembre";
        break;
      case '10': $mes = "Octubre";
        break;
      case '11': $mes = "Noviembre";
        break;
      case '12': $mes = "Diciembre";
        break;
    }
	
	return $mes;
  }

}	
?>