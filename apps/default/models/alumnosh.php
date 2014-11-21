<?php			
	class alumnosh extends ActiveRecord {	
	
		/*
		 * Esta funcion sirve para buscar un ex-alumno segun el registro ingresado
	     * @param int $registro Registro que se desea buscar en la tabla de ex-alumnos
		*/
		public function buscar_ExAlumno_por_registro($registro)
		{
		  $Objeto = new Alumnosh(); 
		  
		  $result = $Objeto->find_all_by_sql("SELECT a.reg, a.nombre AS nomExAlumno,a.periodo, a.plantel, a.nivel, e.vcNomEsp AS especialidad
											  FROM alumnosh a
											  JOIN especialidades e ON e.siNumEsp = a.esp
											  WHERE a.reg = '".$registro."'
											  GROUP BY a.reg");
		  return $result;
		}
		
		/*
		 * Esta funcion sirve para buscar un ex-alumno segun el registro ingresado
	     * @param int $registro Registro que se desea buscar en la tabla de ex-alumnos
		*/
		public function buscar_ExAlumno_por_nombre($name)
		{
		  $Objeto = new Alumnosh(); 
		  
		  $result = $Objeto->find_all_by_sql("SELECT a.reg, a.nombre AS nomExAlumno,a.periodo, a.plantel, a.nivel, e.vcNomEsp AS especialidad
											  FROM alumnosh a
											  JOIN especialidades e ON e.siNumEsp = a.esp
											  WHERE a.nombre LIKE '%".$name."%'
											  GROUP BY a.reg");

		  return $result;
		}
		
		/*
		 * Esta funcion sirve para buscar un ex-alumno segun el registro ingresado
	     * @param int $registro Registro que se desea buscar en la tabla de ex-alumnos
		*/
		static public function buscar_registro_alumno($registro)
		{
		  $Objeto = new Alumnosh(); 

	      $sql = "SELECT reg, nombre, periodo, stsist
				  FROM alumnosh 
				  WHERE reg =".$registro;
			
		  $result = $Objeto->find_all_by_sql($sql);
	 
		  return $result;
		}
	}	
?>