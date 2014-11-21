<?php
			
	class Aspirantes extends ActiveRecord {
      
	    /*
		 * Esta funcion sirve para obtener los datos y documentos entregados de un aspirante
		 * @param int $registro Registro del aspirante que se desea buscar
        */
		public function get_aspirante($registro)
		{
		   $aspirante = new Aspirantes();
		   
		   $sql = "SELECT a.*, ad.*, e.vcNomEsp AS carrera, al.enPlantel AS plantel, a.id AS aspirante
		           FROM aspirantes a
				   LEFT JOIN aspirantes_documentacion ad ON ad.aspirante_id = a.id
				   JOIN alumnos al ON al.miReg = a.registro
				   JOIN especialidades e ON e.idtiEsp = al.idtiEsp 
				   WHERE a.registro = ".$registro;

		   $result = $aspirante -> find_all_by_sql($sql);
		   
		   return $result;
		}
		
        /*
		 * Esta funcion sirve para obtener los datos y documentos entregados de un aspirante
		 * @param int $idAspirante ID del aspirante que se desea buscar
        */
		public function get_aspirante_Fichas($idAspirante)
		{
		   $aspirante = new Aspirantes();
		   
		   $sql = "SELECT a.*, ad.*, e.vcNomEsp AS carrera, al.enPlantel AS plantel, a.id AS aspirante, p.nombre AS periodo
		           FROM aspirantes a
				   LEFT JOIN aspirantes_documentacion ad ON ad.aspirante_id = a.id
				   LEFT JOIN alumnos al ON al.miReg = a.registro
				   LEFT JOIN especialidades e ON e.idtiEsp = al.idtiEsp 
				   LEFT JOIN periodos p ON p.periodo = a.periodo
				   WHERE ad.aspirante_id = ".$idAspirante;
	
		   $result = $aspirante -> find_all_by_sql($sql);
		   
		   return $result;
		}


        /*
         * 	Esta funcion sirve para obtener los documentos entregados por el alumno
		 * @param int $registro Registro del alumno
        */		
		public static function get_documentos($registro)
		{
		  $aspirante = new Aspirantes();

          $sql = "SELECT ad.*,al.nacionalidad,al.miPerIng
		          FROM aspirantes a
				  LEFT JOIN aspirantes_documentacion ad ON ad.aspirante_id = a.id
				  LEFT JOIN alumnos al ON al.miReg = a.registro
				  WHERE a.registro = ".$registro;

          $result = $aspirante -> find_all_by_sql($sql);
  
          foreach($result AS $value){
		    $actao = $value -> acta;
		    $certificadoo = $value -> certificadoo;
		    $ficha_pago = $value -> ficha_pago;
		    $certificadom = $value -> certificadom;
		    $carta_compromiso = $value -> carta_compromiso;
		    $reglamento = $value -> reglamento;
		    $antidoping = $value -> antidoping;
		    $cuota_extranjero = $value -> cuota_extranjero;
		    $prorroga = $value -> prorroga;
		    $nacionalidad = $value -> nacionalidad;
			$miPerIng = $value -> miPerIng;
		  }
		 
		 if($miPerIng == Session::get_data('periodo')){
		  //Se valida que tenga los documentos
		  if($nacionalidad == "M" && $actao == "si" && $certificadoo == "si" && $ficha_pago == "si" && $certificadom == "si" && $carta_compromiso == "si" && $reglamento == "si" && $antidoping == "si" )
            return true;
			
		  else if($nacionalidad == "E" && $actao == "si" && $certificadoo == "si" && $ficha_pago == "si" && $certificadom == "si" && $carta_compromiso == "si" && $reglamento == "si" && $antidoping == "si" && $cuota_extranjero == "si")
            return true;
			
		  else if(strlen($prorroga) > 3 && ($prorroga != "" || $prorroga != NULL))
		    return true;
			
		  else
		    return false;
	     }
		 else{
		   return true;
		 }
		  		  
		}		
	}
	
?>