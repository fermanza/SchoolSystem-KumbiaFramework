<?php			
	class ServicioMedico extends ActiveRecord {
 
		/*
		 * Esta funcion sirve para obtener a los alumnos que ya imprimieron su seguro facultativo
		*/
		static public function alumnos_seguroFacultativo()
		{ 
		  $Objeto = new ServicioMedico(); 

	      $sql = "SELECT a.miReg AS registro, a.nombre AS nombres, a.paterno AS a_paterno, a.materno AS a_materno, c.nombre AS carrera,ai.curp,sm.numero_seguro_social AS seguro
				  FROM alumnos a
				  LEFT JOIN carrera c ON c.id = a.carrera_id
				  LEFT JOIN areadeformacion af ON af.carrera_id = a.carrera_id AND af.idareadeformacion = a.areadeformacion_id
				  LEFT JOIN xalumnos_personal ai ON ai.registro = a.miReg
				  LEFT JOIN servicio_medico sm ON sm.registro = a.miReg
				  WHERE sm.impresion = 1";
			
		  $result = $Objeto->find_all_by_sql($sql);
	 
		  return $result;
		}
	}	
?>