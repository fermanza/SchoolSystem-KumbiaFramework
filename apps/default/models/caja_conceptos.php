<?php			
	class CajaConceptos extends ActiveRecord {
	
        static public function reporte($registro, $tipoPersona, $tipoOtro = 0){
		                                            
		    $Alumnos = new Alumnos();
			
			$Objeto = new cajaConceptos();
			  
			//Obtengo los datos del alumno (desde la sesion de ventanilla)
			if($tipoPersona == 1){
			   $alumnos = $Alumnos -> find_first('miReg ='.$registro);

			  //Obtiene el ultimo pariodo que el alumno pago
			  $periodoPagado = new pagoPeriodo();
			  $ultimoPago = $periodoPagado -> find_first('registro ='.$registro); 
			  
			  //Alumnos de nuevo ingreso
			  if($alumnos -> pago == 0 && $alumnos -> condonado == 0 && $alumnos -> miPerIng == Session::get_data('periodo')){
			   
                $CajaTramites = new CajaTramites();
                $pagoRecargos = $CajaTramites -> find("concepto = 3 AND status_pago = 1 AND registro =".$registro);	
				
			   //Si es extranjero se le cobra el concepto 101 en dolares		
               if($alumnos -> nacionalidad == 'E')
                 $conceptoExtran = ",102";
               
               else		
                  $conceptoExtran = "";		
				  
				  
				$pagoInscripcion = "";
				
				foreach($pagoRecargos AS $value){
				
				  if($value -> status_pago)
					$pagoInscripcion = 1;
		
				}
				
                if($pagoInscripcion == 1){				
				  $objetos = $Objeto -> find("activo = 1 AND id IN(6,16$conceptoExtran) ORDER BY nombre ASC");
				}
				else{
				  $objetos = $Objeto -> find("activo = 1 AND id IN(3,16$conceptoExtran) ORDER BY nombre ASC");
				}
				//$objetos = $Objeto -> find('activo = 1 AND id IN(3,16,102)  ORDER BY nombre ASC'); //,53
			  }
			
		      //Alumnos dados de baja con pago atrasado
	          if(($alumnos -> stSit == "BD"|| $alumnos -> stSit == "BT") && $ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito ){    
                $Objeto = new cajaConceptos();			  
				$objetos = $Objeto -> find('activo = 1 AND id IN(4,102)  ORDER BY nombre ASC'); //5,
			  }
			  //|| ($alumnos -> stSit == "OK"  && ($alumnos -> pago == 0 || $alumnos -> condonado == 0) && $alumnos -> miPerIng != Session::get_data('periodo'))
              if(($alumnos -> stSit == "BD" || $alumnos -> stSit == "BT") && $ultimoPago -> periodoUltimoPago == $ultimoPago -> periodoUltimoInscrito) {
			    
				//Se inicializan las variables
				$registroDP = 0;
				$periodo = 0;
				//Valida a si se muestra el derecho de pasante
				$DP = new derechopasante(); 
				$derePas = $DP -> find('tipo = "DP" AND periodo = "'.Session::get_data('periodo').'" AND registro='.$registro);
				
				foreach($derePas AS $value){
				  $registroDP = $value -> registro;
				  $periodo = $value -> periodo;
				}
				
				if($alumnos -> stSit == "OK"  && ($registroDP != 0 || $registroDP != null || $periodo != 0 || $periodo == Session::get_data('periodo')))
				  $conceptoDP = " OR id = 54"; 
				  
				else
				  $conceptoDP = "";
				  
				//Termina validacion del concepto derecho de pasante
				
				//validacion de ficha de reinscripcion (solo OK)
				if($alumnos -> stSit == "OK")
				  $reinscripcion = ",4";
				
				else
				  $reinscripcion = "";
				  
				  
				//Si es extranjero se le cobra el concepto 101 en dolares		
				if($alumnos -> nacionalidad == 'E' && $alumnos -> stSit == "OK")
				  $conceptoExtran = ",102";
				   
				else		
				  $conceptoExtran = "";	
				  
				
				 $Objeto = new cajaConceptos();
				 $objetos = $Objeto = $Objeto -> find("activo = 1 AND id IN(11,14,50$reinscripcion $conceptoExtran) $conceptoDP ORDER BY nombre ASC"); //5,
			  }
			  
			  //Alumno Egresados
			  if($alumnos -> stSit == "EG")
			  {
			    $Objeto = new cajaConceptos();
			    $objetos = $Objeto -> find('activo = 1 AND id IN(100, 48, 109, 8, 74, 18, 98,7,12,108,50, 13,102)  ORDER BY nombre ASC');
			  }
			  
			  //Alumnos con estatus ok y pago actualizado
			  if(($alumnos -> stSit == "OK") && ($alumnos -> pago == 1 || $alumnos -> condonado == 1) ){

				//Se inicializan las variables
				$registroDP = 0;
				$periodo = 0;
				//Valida a si se muestra el derecho de pasante
				$DP = new derechopasante(); 
				$derePas = $DP -> find('tipo = "DP" AND periodo = "'.Session::get_data('periodo').'" AND registro='.$registro);
				
				foreach($derePas AS $value){
				  $registroDP = $value -> registro;
				  $periodo = $value -> periodo;
				}
				
				if($registroDP != 0 || $registroDP != null || $periodo != 0 || $periodo == Session::get_data('periodo'))
				  $conceptoDP = " OR id = 54"; 
				  
				else
				  $conceptoDP = "";
				  
				//Termina validacion del concepto derecho de pasante
                //Se agrega el concepto de credencial para los que aun no la tienen	
				$concepAddC = "";
				$concepAddM = "";
                $cajaTramites = new CajaTramites();	
				$credencial = $cajaTramites -> find('concepto = 16 AND registro ='.$registro); //OR concepto = 53 
				
				/*foreach($credencial AS $result){
				  $concepAddC = $result -> concepto;
				}*/

				if(($concepAddC == NULL || $concepAddC == "") && $alumnos -> miPerIng == Session::get_data('periodo')){
				  $concepCredencial = " OR id = 16 ";
				  $concepCredencialNO = "";
                }
				
                else{
  				  $concepCredencialNO = " AND id != 16 ";
				  $concepCredencial = "";
				}
				
				$manual = $cajaTramites -> find('concepto = 53 AND registro ='.$registro);
				
				foreach($manual AS $result2){
				  $concepAddM = $result2 -> concepto;
				}

				if(($concepAddM == NULL || $concepAddM == "") && $alumnos -> miPerIng == Session::get_data('periodo') ){
				  $concepManual = " OR id = 53 ";
				  $concepManualNO = "";
                }
				
                else{
  				  $concepManualNO = " AND id != 53 ";
				  $concepManual = "";
				}
				//Termina validacion
				
				
				//Si es extranjero se le cobra el concepto 101 en dolares		
				if($alumnos -> nacionalidad == 'E')
				  $conceptoExtran = " OR id = 102";
					   
				else		
				  $conceptoExtran = "";	
				 
				  
				$Objeto = new cajaConceptos();
			    $objetos = $Objeto -> find("activo = 1 AND id != 3 $concepCredencialNO $concepManualNO AND id != 4 AND id != 5 AND id != 6 AND id != 19 AND id != 111 AND id != 93 AND (nivel = 'I' OR nivel = 'TI') $conceptoDP $concepCredencial $concepManual $conceptoExtran ORDER BY nombre ASC");
			  }
			  
			  //Quitar Si No tiene realizado el pago solo podra ver el concepto de inscripcion
			  /*if($alumnos -> pago == 0 && $alumnos -> condonado == 0){        	
				 $objetos = $Objeto -> find('activo = 1 AND id IN(3,16,53,6) ORDER BY nombre ASC');//5,
			  }
			  
			  else{
			    $objetos = $Objeto -> find('activo = 1 AND id != 3 AND id != 16 AND id != 53 AND id != 2 AND id != 5 AND id != 6 AND (nivel = "I" OR nivel = "TI") ORDER BY nombre ASC');
			  }*/
			  //Quitar
			}
			//Aspirantes
			else if($tipoPersona == 2)
		    {
			  $objetos = $Objeto -> find('activo = 1 AND id IN(19,111,93) ');
			}
			//Alumnos
			else if($tipoPersona == 5)
			{
			 
			  $alumnos = $Alumnos -> find_first('miReg ='.$registro);
			  
			  //Obtiene el ultimo pariodo que el alumno pago
			  $periodoPagado = new pagoPeriodo();
			  $ultimoPago = $periodoPagado -> find_first('registro ='.$registro); 
			 
			  //Alumnos de nuevo ingreso
			  if($alumnos -> pago == 0 && $alumnos -> condonado == 0 && $alumnos -> miPerIng == Session::get_data('periodo')){ 	
                $CajaTramites = new CajaTramites();
                $pagoRecargos = $CajaTramites -> find("concepto = 3 AND status_pago = 1 AND periodo = ".Session::get_data('periodo')." AND registro =".$registro);	
				
			  //Si es extranjero se le cobra el concepto 101 en dolares		
			  if($alumnos -> nacionalidad == 'E')
				$conceptoExtran = ",102";
				   
			  else		
			    $conceptoExtran = "";	
				
				$pagoInscripcion = "";
				
				foreach($pagoRecargos AS $value){
				
				  if($value -> status_pago == 1)
					$pagoInscripcion = 1;
					
				  else
				    $pagoInscripcion = 0;
					
				}
				
                if($pagoInscripcion == 1){				
				  $objetos = $Objeto -> find("activo = 1 AND id IN(6,16$conceptoExtran)  ORDER BY nombre ASC");
				}
				else{
				  $objetos = $Objeto -> find("activo = 1 AND id IN(3,16)  ORDER BY nombre ASC"); //,53
				}			  
				
			  }
			  
			  /*if($alumnos -> stSit == "BT" || ($alumnos -> pago == 0 && $alumnos -> condonado == 0 && $alumnos -> miPerIng != Session::get_data('periodo') && $alumnos -> stSit == "OK")){        	
				$objetos = $Objeto -> find('activo = 1 AND id IN(4,6,7)  ORDER BY nombre ASC');//5,
			  }*/
			  
			  //Alumnos dados de baja con pago atrasado
	          if(($alumnos -> stSit == "BD" || $alumnos -> stSit == "BT")&& $ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito ){    
			 
                $Objeto = new cajaConceptos();			  
				$objetos = $Objeto -> find('activo = 1 AND id IN(4)  ORDER BY nombre ASC'); //5,
			  }
			  //&& "13-08-2012" >= DATE("d-m-Y") 
              if((($alumnos -> stSit == "BD" || $alumnos -> stSit == "BT") && ($ultimoPago -> periodoUltimoPago == $ultimoPago -> periodoUltimoInscrito)) || ($alumnos -> stSit == "OK"  && ($alumnos -> pago == 0 || $alumnos -> condonado == 0) && $alumnos -> miPerIng != Session::get_data('periodo'))){
			    
				//Se inicializan las variables
				$registro = 0;
				$periodo = 0;
				//Valida a si se muestra el derecho de pasante
				$DP = new derechopasante(); 
				$derePas = $DP -> find('tipo = "DP" AND periodo = "'.Session::get_data('periodo').'" AND registro='.Session::get('registro'));
				
				foreach($derePas AS $value){
				  $registro = $value -> registro;
				  $periodo = $value -> periodo;
				}
				
				if($alumnos -> stSit == "OK"  && ($registro != 0 || $registro != null || $periodo != 0 || $periodo == Session::get_data('periodo')))
				  $conceptoDP = " OR id = 54"; 
				  
				else
				  $conceptoDP = "";
				  
				//Termina validacion del concepto derecho de pasante
				
				//validacion de ficha de reinscripcion (solo OK)
				if($alumnos -> stSit == "OK")
				$reinscripcion = ",4";
				
				else
				$reinscripcion = "";
				
				if($alumnos -> stSit == "OK" && ($alumnos -> pago == 0 || $alumnos -> condonado == 0)){

					$CajaTramites = new CajaTramites();
					$pagoRecargos = $CajaTramites -> find("concepto = 4 AND status_pago = 1 AND periodo = ".Session::get_data('periodo')." AND registro =".Session::get('registro'));
		
					  //Si es extranjero se le cobra el concepto 101 en dolares		
					  if($alumnos -> nacionalidad == 'E')
						$conceptoExtran = ",102";
						   
					  else		
						$conceptoExtran = "";		
				  
                    $pagoReinscripcion = "";
					
					foreach($pagoRecargos AS $value){
					
					  if($value -> status_pago == 1)
					    $pagoReinscripcion = 1;
						
				      else
					    $pagoReinscripcion = 0;
	
					}
					 
					if($pagoReinscripcion == 1){	
                      $Objeto = new cajaConceptos();				
					  $objetos = $Objeto -> find('activo = 1 AND id IN(6,16)  ORDER BY nombre ASC');
					  //$objetos = $Objeto -> find('activo = 1 AND id IN(4,6,95,94,7,11,14,13,50,54,15)  ORDER BY nombre ASC');
					}
					else{
					  $Objeto = new cajaConceptos();
					  $objetos = $Objeto -> find("activo = 1 AND id IN(4,16) ORDER BY nombre ASC"); //5,,58
					}
				 //$objetos = $Objeto = $Objeto -> find("activo = 1 AND id IN(11,14,50,13$reinscripcion) $conceptoDP ORDER BY nombre ASC"); //5,
				}
				
				else{
				 $Objeto = new cajaConceptos();
				 $objetos = $Objeto = $Objeto -> find("activo = 1 AND id IN(15,7,11,13,14,50,54$reinscripcion) $conceptoDP ORDER BY nombre ASC"); //5,
				}
				
			  }
			  
			  //Alumno Egresados
			  if($alumnos -> stSit == "EG")
			  {
			    $Objeto = new cajaConceptos();
			    $objetos = $Objeto -> find('activo = 1 AND id IN(100, 48, 109, 8, 74, 18, 98,7,12,108,50, 13)  ORDER BY nombre ASC');
			  }
			  
			  //Alumnos con estatus ok y pago actualizado
			  if(($alumnos -> stSit == "OK") && ($alumnos -> pago == 1 || $alumnos -> condonado == 1) ){

				//Se inicializan las variables
				$registro = 0;
				$periodo = 0;
				//Valida a si se muestra el derecho de pasante
				$DP = new derechopasante(); 
				$derePas = $DP -> find('tipo = "DP" AND periodo = "'.Session::get_data('periodo').'" AND registro='.Session::get('registro'));
				
				foreach($derePas AS $value){
				  $registro = $value -> registro;
				  $periodo = $value -> periodo;
				}
				
				if($registro != 0 || $registro != null || $periodo != 0 || $periodo == Session::get_data('periodo'))
				  $conceptoDP = " OR id = 54"; 
				  
				else
				  $conceptoDP = ""; 
				  
				//Termina validacion del concepto derecho de pasante
                //Se agrega el concepto de credencial para los que aun no la tienen	
				$concepAddC = "";
				$concepAddM = "";
                $cajaTramites = new CajaTramites();	
				$credencial = $cajaTramites -> find('concepto = 16 AND registro ='.Session::get('registro')); //OR concepto = 53 
				
				/*foreach($credencial AS $result){
				  $concepAddC = $result -> concepto;
				}*/

				if(($concepAddC == NULL || $concepAddC == "") && $alumnos -> miPerIng == Session::get_data('periodo') ){
				  $concepCredencial = " OR id = 16 ";
				  $concepCredencialNO = "";
                }
				
                else{
  				  $concepCredencialNO = " AND id != 16 ";
				  $concepCredencial = "";
				}
				
				$manual = $cajaTramites -> find('concepto = 53 AND registro ='.Session::get('registro'));
				
				foreach($manual AS $result2){
				  $concepAddM = $result2 -> concepto;
				}

				if(($concepAddM == NULL || $concepAddM == "") && $alumnos -> miPerIng == Session::get_data('periodo') ){
				  $concepManual = " OR id = 53 ";
				  $concepManualNO = "";
                }
				
                else{
  				  $concepManualNO = " AND id != 53 ";
				  $concepManual = "";
				}
				//Termina validacion
				
				
				//Si es extranjero se le cobra el concepto 101 en dolares		
				if($alumnos -> nacionalidad == 'E')
				  $conceptoExtran = " OR id = 102";
					   
				else		
				  $conceptoExtran = "";	 
				  
				  
				$Objeto = new cajaConceptos();
			    $objetos = $Objeto -> find("activo = 1 AND id != 3 AND id != 102 $concepCredencialNO $concepManualNO AND id != 4 AND id != 5 AND id != 6 AND id != 19 AND id != 111 AND id != 93 AND (nivel = 'I' OR nivel = 'TI') $conceptoDP $concepCredencial $concepManual $conceptoExtran ORDER BY nombre ASC");
			  }
			}
			//Propedeutico
			else if($tipoPersona == 4 && $tipoOtro == 1){
			  $Objeto = new cajaConceptos();
			  $objetos = $Objeto -> find('activo = 1 AND id IN(93,111,75)');
			}
			
			//Validacion que muestra los conceptos para los ex-alumnos desde sesion de ventanilla
			else if($tipoPersona == 4 && $tipoOtro == 2){
			  $Objeto = new cajaConceptos();
			  //$objetos = $Objeto -> find('activo = 1 AND id IN((99, 49, 109, 8, 10, 18, 97, 12, 107, 7, 14, 50, 13) ORDER BY nombre ASC');
			  $objetos = $Objeto -> find('activo = 1 AND id IN(100, 48, 109, 8, 74, 18, 98,7,12,108,50, 13,11,14,13,4,11)  ORDER BY nombre ASC');
			}
			
			//Obtiene los conceptos que aplican para tecnologo
			else{
			  $objetos = $Objeto -> find('activo = 1 AND id != 3 AND id != 16 AND id != 53 AND id != 4 AND id != 5 AND id != 6 AND (nivel = "I" OR nivel = "TI") ORDER BY nombre ASC');
			}
		
            return $objetos;
        }
		
       
		static public function conceptos($tipoPersona = ""){
		
		  $objeto = new cajaConceptos();
		  
		  if($tipoPersona == 2)
		    $objetos = $Objeto -> find('id = 20');
		}
        
		static public function status(){
	
		    $statusSearch = "Sin busqueda";
           
		   return $statusSearch;
        }
		

		
        static public function nombre($id){
	
            $Objeto = new CajaConceptos();
            $Objeto -> find_first('id = '.$id);
			
            return $Objeto;
        }
        
        public static function registrar(){
            $Objeto = new CajaConceptos();
         
            $Objeto -> save();
            
            return $Objeto;                           
        }
        
        static public function consultar($id){
            $Objeto = new CajaConceptos();            
          
            $Objeto -> find_first('id = '.$id);
            
            return $Objeto;
        }        
        
        static public function eliminar($id){
            $Objeto = new CajaConceptos();
                        
            $Objeto -> delete($id);
            
            return $Objeto;
        }
        
	}	
?>