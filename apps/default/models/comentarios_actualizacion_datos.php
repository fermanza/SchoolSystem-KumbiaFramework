<?php			
	class comentariosActualizacionDatos extends ActiveRecord {
	
	  /*
	   * Funcion que obtiene los comentarios realizados por los alumnos
	  */
	  static public function ver_comentarios(){
	  
	    $comentarios = new comentariosActualizacionDatos();
		
	    $sql = "SELECT c.idComentario, c.registro, c.fecha, c.comentario, CONCAT(a.nombre,' ',a.paterno,' ',a.materno) AS nombre_completo
		        FROM comentarios_actualizacion_datos c
				JOIN alumnos a ON a.miReg = c.registro
				WHERE statusComentario = 1 AND statusLeido = 0
				ORDER BY c.fecha ASC ";
				
		$record = $comentarios -> find_all_by_sql($sql);
		
		return $record;
	  }
	  
	  /*
	   * convierte una fecha formato datetime (2010-01-29 10:28:03) al formato (miercoles 09-10-2010 10:28:03)
	   * @param String $datetime si es true el dia de la semana se regresa coon una letra ejem. (l,m,i,j,v,s,d)
	   * @param bolean $split por default false si es true regresa Array(dia,fecha,hora)
	  */
		static public function getFullDateTime($datetime, $split = false)
		{
		    $comentariosActualizacionDatos = new comentariosActualizacionDatos();
			
			list($date,$time) = explode(" ",$datetime);
			$weekday = comentariosActualizacionDatos::getDay($date,true);
			$date    = explode("-",$date);
			$date    = $date[2]."&#47;".$date[1]."&#47;".substr($date[0],2,2);

			$time = substr($time,0,5). " Hrs";

			$fullDatetime = $weekday." &#45; ".$date."&nbsp;&nbsp;".$time;

			if($split)
			  $fullDatetime = explode(" ",$fullDatetime);

			return $fullDatetime;
		}
		
		  /**
		   * Funcion sirve para calcular el dia de la semana
		   * @param String $date fecha en el formato  year/month/day
		   * @param bolean $short si es true regresa el dia de la semana con una letra eje.(l,m,i,j,v,s,d)
		   * @return String dia de la semana
		   */
		  static public function getDay($date, $short = false)
		  {
			$date = str_replace("/","-",$date);
			$dateExp = explode("-",$date);

			list($year,$month,$day) = $dateExp;

				// 0->domingo	 | 6->sabado
			  $weekDay = date("w",mktime(0, 0, 0, $month, $day, $year));

			switch ($weekDay)
			{
			  case 0: $weekDay = "Domingo";
				break;
			  case 1: $weekDay = "Lunes";
				break;
			  case 2: $weekDay = "Martes";
				break;
			  case 3: $weekDay = "Miercoles";
				break;
			  case 4: $weekDay = "Jueves";
				break;
			  case 5: $weekDay = "Viernes";
			  break;
			  case 6: $weekDay = "Sabado";
				break;
			}

			if($short)
			{
			  if($weekDay == "Miercoles")
				$weekDay = "I";
			  else
				$weekDay = substr($weekDay,0,1);
			}


			return $weekDay; ;
		  }

	}	
?>