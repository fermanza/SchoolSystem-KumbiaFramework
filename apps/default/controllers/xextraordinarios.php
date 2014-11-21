<?php
			
	class Xextraordinarios extends ActiveRecord {
		
        /*
		 * Funcion que obtiene un porcentaje
	     * @param int $porcentaje La cantidad a la cual se aplicara el porcentaje
		*/
		public function obtiene_porcentaje($porcentaje){
		   
		   $result = 60*$porcentaje/100; 
		   
		   return $result;
		}
		
		/*
		 * Obtiene el Plantel del alumno
		 * @param int $registro Registro del alumno que se desea obtener el plantel
		*/
		public function get_plantel($registro){
		  
		  $xextraordinarios	= new Xextraordinarios();
		  
		  $plantelInge = $xextraordinarios -> find_all_by_sql("SELECT enPlantel
			                                                    FROM alumnos 
											                    WHERE miReg = ".$registro); 
												   
          foreach($plantelInge AS $plantelIng)
		     $ingePlantel = $plantelIng -> enPlantel;
		
		  return $ingePlantel;
		}
		
		
		/*
		 * Esta funcion sirve para obtener la cantidad de cursos que tiene un alumno en regularizacion
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		*/
		public function cantidad_cursos_en_regularizacion($registro, $periodo, $plantel)
		{
		  $xextraordinarios	= new Xextraordinarios();
		  
		  if($plantel == "C")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xalumnocursos xal
												                  INNER JOIN xccursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo = '".$periodo."' AND situacion = 'REGULARIZACION DIRECTA'");
          }
		  
		  if($plantel == "N")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xtalumnocursos xal
												                  INNER JOIN xtcursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo = '".$periodo."' AND situacion = 'REGULARIZACION DIRECTA'");
          }
		  
		  foreach($regularizacion AS $value)
		     $countRegularizacion = $value -> cantidadCursos;

		  
		  return $countRegularizacion;
		}
		
		/*
		 * Esta funcion sirve para obtener la cantidad de cursos que tiene con la situacion BAJA DEFINITIVA
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		*/
		public function cantidad_cursos_en_BD($registro, $periodo, $plantel)
		{
		  $xextraordinarios	= new Xextraordinarios();
		  
		  if($plantel == "C")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xalumnocursos xal
												                  INNER JOIN xccursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo = '".$periodo."' AND situacion = 'BAJA DEFINITIVA'");
          }
		  
		  if($plantel == "N")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xtalumnocursos xal
												                  INNER JOIN xtcursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo = '".$periodo."' AND situacion = 'BAJA DEFINITIVA'");
          }
		  
		  foreach($regularizacion AS $value)
		     $countRegularizacion = $value -> cantidadCursos;
			
			
		  return $countRegularizacion;
		}
		
		
		/*
		 * Esta funcion sirve para obtener la cantidad de cursos que tiene un alumno
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		*/
		public function cantidad_cursos_alumno($registro, $periodo, $plantel)
		{
		  $xextraordinarios	= new Xextraordinarios();
		  
		  if($plantel == "C")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xalumnocursos xal
												                  INNER JOIN xccursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo =".$periodo);
          }
		  
		  if($plantel == "N")
		  {
		    $regularizacion = $xextraordinarios->find_all_by_sql("SELECT COUNT(xal.curso_id) AS cantidadCursos
												                  FROM xtalumnocursos xal
												                  INNER JOIN xtcursos xcc ON xal.curso_id = xcc.id AND xal.registro = '".$registro."' AND xal.periodo =".$periodo);
		  }
		  
		  foreach($regularizacion AS $value)
		     $countRegularizacion = $value -> cantidadCursos;

			
		  return $countRegularizacion;
		}
		
		
		/*
		 * Esta funcion sirve para obtener los extras y titulos que tiene un alumno
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		 * @param string $tipo Tipo de examen (E = Extras o T = Titulos)
		*/
		public function get_extrasTitulos_alumno($registro, $periodo, $plantel,$tipo)
		{
		  $xextraordinarios	= new Xextraordinarios();

		  if($plantel == "C")
		  {
			$extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion, IFNULL(ct.actas,'False') AS Agregado 
														  FROM xextraordinarios e
														  JOIN xalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
														  JOIN xccursos c ON c.id = e.curso_id
														  JOIN materia m ON m.clave = c.materia
														  LEFT JOIN caja_tramites ct ON ct.registro = e.registro AND ct.actas = e.curso_id
														  WHERE e.tipo = '".$tipo."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."' GROUP BY e.curso_id");
		  }		

		  if($plantel == "N")
		  {
			  $extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion, IFNULL(ct.actas,'False') AS Agregado 
															FROM xtextraordinarios e
															JOIN xtalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
															JOIN xtcursos c ON c.id = e.curso_id
															JOIN materia m ON m.clave = c.materia
															LEFT JOIN caja_tramites ct ON ct.registro = e.registro AND ct.actas = e.curso_id
															WHERE e.tipo = '".$tipo."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."' GROUP BY e.curso_id");
		  }		
		  
		  return $extras;  
		}
		
		
		/*
		 * Esta funcion sirve para obtener el nombre de la materia de los extras y titulos de un alumno
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		 * @param string $cursoId Curso_Id de la materia
		*/
		public function get_extrasTitulos_PDF($registro, $periodo, $plantel,$cursoID)
		{
		  $xextraordinarios	= new Xextraordinarios();
		  
		  if($plantel == "C")
		  {
			$extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion 
														  FROM xextraordinarios e
														  JOIN xalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
														  JOIN xccursos c ON c.id = e.curso_id
														  JOIN materia m ON m.clave = c.materia
														  WHERE e.curso_id = '".$cursoID."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."' GROUP BY e.curso_id");
		  }		

		  if($plantel == "N")
		  {
			  $extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion 
															FROM xtextraordinarios e
															JOIN xtalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
															JOIN xtcursos c ON c.id = e.curso_id
															JOIN materia m ON m.clave = c.materia
															WHERE e.curso_id = '".$cursoID."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."' GROUP BY e.curso_id");
		  }		
		  
		  return $extras;  
		}
		
		
		/*
		 * Esta funcion sirve para obtener los cursos de nvelacion o acreditacion segun lo solicite el alumno
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo intersemestral inicia con 2 o 4 
		 * @param int $tipoCurso Tipo de curso que solicita ver el alumno
		*/
		public function get_cursos_NIV_ACR($registro, $periodo, $tipoCurso)
		{
		  
		  $xextraordinarios	= new Xextraordinarios();
		  									  
		  $extras = $xextraordinarios->find_all_by_sql("SELECT m.nombre, ic.clavecurso,ia.tipo_ex, ic.id, m.clave, IFNULL(ct.actas,'False') AS Agregado 
														FROM intersemestral_cursos ic
														JOIN intersemestral_alumnos ia ON ia.clavecurso = ic.clavecurso
														JOIN materia m ON m.clave = ic.clavemat
														LEFT JOIN caja_tramites ct ON ct.registro = ia.registro AND ct.actas = ic.id
														WHERE ia.tipo_ex = '".$tipoCurso."' AND ic.periodo = '".$periodo."' AND ia.registro =".$registro);
														
		  return $extras;  
		}
		
		/*
		 * Esta funcion sirve para obtener los cursos de nvelacion y su nombres
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo intersemestral inicia con 2 o 4 
		 * @param int $tipoCurso Tipo de curso que solicita ver el alumno
		*/
		public function get_cursos_NIV_ACR_PDF($registro, $periodo, $cursoID)
		{
		  
		  $xextraordinarios	= new Xextraordinarios();
		  											
		  $extras = $xextraordinarios->find_all_by_sql("SELECT m.nombre, ic.clavecurso,ia.tipo_ex, ic.id, m.clave
														FROM intersemestral_cursos ic
														JOIN intersemestral_alumnos ia ON ia.clavecurso = ic.clavecurso
														JOIN materia m ON m.clave = ic.clavemat
														WHERE ic.id = '".$cursoID."' AND ic.periodo = '".$periodo."' AND ia.registro =".$registro);
													
		  return $extras;  
		}
		
		
		/*
		 * Esta funcion sirve para obtener los nombres de las materias en extras y titulos 
	     * @param int $registro Registro del alumno que solicito el pago del extraordinario
		 * @param int $periodo Periodo que esta activo
		 * @param int $plantel Plantel donde pertenece el alumno
		 * @param string $curso_id Contiene 
		*/
		public function get_extrasTitulos($registro, $periodo, $plantel,$curso_id)
		{
		  $xextraordinarios	= new Xextraordinarios();
		  
		  if($plantel == "C")
		  {
			$extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion 
														  FROM xextraordinarios e
														  JOIN xalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
														  JOIN xccursos c ON c.id = e.curso_id
														  JOIN materia m ON m.clave = c.materia
														  WHERE e.curso_id = '".$curso_id."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."'");
		  }		

		  if($plantel == "N")
		  {
			  $extras = $xextraordinarios->find_all_by_sql("SELECT e.*,c.*,m.nombre,c.materia, a.situacion 
															FROM xtextraordinarios e
															JOIN xtalumnocursos a ON a.curso_id = e.curso_id and a.registro = '".$registro."' AND a.periodo = '".$periodo."'
															JOIN xtcursos c ON c.id = e.curso_id
															JOIN materia m ON m.clave = c.materia
															WHERE e.curso_id = '".$tipo."' AND e.periodo = '".$periodo."' AND e.registro ='".$registro."'");
		  }		
		  
		  return $extras;  
		}
		
	}
	
?>
