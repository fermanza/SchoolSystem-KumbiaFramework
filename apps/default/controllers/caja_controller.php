<?php
/*
 *@author Ing. Carlos Adolfo Lizaola Zepeda
 *@category Kumbia
 *@package Controler   
 *GNU General Publical License v.3.0.
 */	
	class CajaController extends ApplicationController {
	
		function accesourlCaja(){
		
		}
		
		function index(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();  	  
		}
		
		function indexAlumno(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAlumno();     
		}
				
		function indexAspPrope(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAspPrope();     
		}
		
		function indexReportes(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaFinanzas();     
		}
		
		function propedeutico(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAspPrope();    
		  $this -> otroTipoPer = "1";
		  $this -> tipoPersona = "4";		  
		}
		
		function FichasPrope(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAspPrope();    	  
		}
		
		function alumnoConcepto(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAlumno();     
		}
		
		function tramites(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> validaAlumno();     
		}

		function reportes(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
	    
		function verTramites(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
		
		function imprimirFichas(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
        
		function actualizarPagos(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida(); 
		}
		
		function guardarReferencia(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida(); 
		}
		
		function periodosAlumnos(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
		
		function fechasConceptos(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
		
		function adminConceptos(){
		  $CajaController = new CajaController();
		  CajaController::url_validacion();
		  
		  $this -> valida();     
		}
		
        function buscar($tipo = 'alumno'){
            $this -> set_response('view');
            $this -> tipo = $tipo;
            
            if($tipo == 'alumno'){   
			    $this -> tipoPersona = 1;
                echo $this -> render_partial('buscadorAlum'); 
            }elseif($tipo == 'aspirante'){
			    $this -> tipoPersona = 2;
                echo $this -> render_partial('buscadorAsp');
            }elseif($tipo == 'trabajador'){
			    $this -> tipoPersona = 3;
                echo $this -> render_partial('buscadorTrabajador');
            }elseif($tipo == 'otro'){
			     $this -> tipoPersona = 4;
                echo $this -> render_partial('buscadorOtros');
            }else{
                Flash::error('NO SE ENCONTRO EL TIPO DE BUSQUEDA SELECCIONADA');
            }
        }
        
        function buscarXNombre(){
            $this -> set_response('view');
            $tipo = $this -> post('tipo');
            $this -> tipo = $this -> post('tipo');
			
            if($this -> post('nombre')){
                if($tipo == 'alumno'){
                    if($this -> listado = Alumnos::buscarXNombre($this -> post('nombre')))
                        echo $this -> render_partial('listado');
						
                    else
                        Flash::error('No se encontraron Resultados'); 
                }elseif($tipo == 'aspirante'){
                    Aspirantes::buscarXNombre($this -> post('nombre'));
                    echo $this -> render_partial('listado');
                }elseif($tipo == 'trabajador'){
                    Trabajadores::buscarXNombre($this -> post('nombre'));
                    echo $this -> render_partial('listado');
                }elseif($tipo == 'otro'){
                    Otros::buscarXNombre($this -> post('nombre'));
                    echo $this -> render_partial('listado');
                }else{
                    Flash::error('NO SE ENCONTRO EL TIPO DE BUSQUEDA SELECCIONADA');
                }
            }
			else{
                Flash::error('Escribe un nombre para la busqueda');
            }
        }
		 
		function periodo()
		{ 
		  $this -> set_response('view');
		  $tipo = $this -> post('tipo');
		  $this -> tipo = $this -> post('tipo');

		  if($this -> post('periodo'))
		  {
		    if($tipo == 'alumno'){
			 if($this -> listado = Alumnos::buscarXPeriodo($this -> post('periodo')))
                        echo $this -> render_partial('listado');

		      else
			    Flash::error('No se encontraron resultados');
			}
		  }
		  
		  else
		  {
		    Flash::error('Selecciona un periodo');
		  }
		}
		
		
        function llenado($tipo = '',$registro = ''){
            $this -> set_response('view');
            if($this -> has_post('tipo','registro')){
                $registro = $this -> post('registro');
                $this -> registro = $this -> post('registro');   
                $tipo = $this -> post('tipo');
            }
			
            if($registro){
                $this -> registro = $registro;
                $this -> fecha = date('d-m-Y');
                //$this -> folio = (CajaPagos::ultimoFolio())+1;
                $this -> alumno = Alumnos::buscar($registro);
                $this -> sql = '';   
				
				if($this -> alumno)
                  echo $this -> render_partial('formulario');
				  
				else
				  Flash::error('No se encontro el Registro');
            }
			
			else{
                Flash::error('No se encontro el Registro');
            }
        }		
        
        function buscarConceptos(){
		   
		  $Objeto = new CajaConceptos(); 

          if($this -> post('activoBusquedaEA') != '' && $this -> post('tipoBusqueda'))
		  {
            if($this -> post('concepto') != '' && $this -> post('activoBusquedaEA') == 2 && is_numeric($this -> post('concepto')) ==  false){ 
			    $this -> result = $Objeto -> find("activo = 1 AND nombre like '%".$this -> post('concepto')."%' AND (nivel = 'I' OR nivel = 'TI')");				
            }
			
            else if($this -> post('concepto') != '' && $this -> post('activoBusquedaEA') == 1 && is_numeric($this -> post('concepto')) ==  true ){ 
			    $this -> result = $Objeto -> find("activo = 1 AND id = ".$this -> post('concepto')." AND (nivel = 'I' OR nivel = 'TI')");						
            }
			
			else if($this -> post('concepto') == ''){
                Flash::error('Es necesario ingresar el concepto que se desea buscar');
            }
			
			else if(is_numeric($this -> post('concepto')) == true && $this -> post('activoBusquedaEA') == 2)
			   Flash::error('Es necesario realizar la busqueda con el nombre del concepto');
			
			else if(is_numeric($this -> post('concepto')) == false && $this -> post('activoBusquedaEA') == 1)
			   Flash::error('Es necesario realizar la busqueda con el numero de concepto');
			   
            else
                Flash::error('No se encontraron resultados');
		  }
		  
			else{
                Flash::error('Es necesario seleccionar el tipo de busqueda');
            }
		  
            $this -> set_response('view');
		  
            echo $this -> render_partial('listadoConceptos');
        }
		
		/*
		 * Funcion que sirve para realizar la busqueda de un ex-alumno, la busqueda se puede realizar por registro o por el nombre completo del ex-alumno
		*/
        function buscarExAlumnos(){
		   
		  $Objeto = new Alumnosh(); 
          
		  //Inician las validaciones para realizar la busqueda
          if($this -> post('activoBusqueda') != '' && $this -> post('tipoBusqueda'))
		  {
			if($this -> post('exAlumno') == ''){
			    $this -> resultEA = "";
			    echo "<div id='msgStatus' name='msgStatus' style='display:none; color:#F6F6F6;'>FALSE</div>";
			    echo "<div id='msgExAlumnos' name='msgExAlumnos' style='display:none; color:#F6F6F6;'>Es necesario ingresar el nombre o registro que se desea buscar</div>";
            }
			
			
			else if(is_numeric($this -> post('exAlumno')) == false && $this -> post('activoBusqueda') == 1){
			   $this -> resultEA = "";
			   echo "<div id='msgStatus' name='msgStatus' style='display:none; color:#F6F6F6;'>FALSE</div>";
			   echo "<div id='msgExAlumnos' name='msgExAlumnos' style='display:none; color:#F6F6F6;'>Es necesario realizar la busqueda con el registro del ex-alumno</div>";
			}
			
			else if(is_numeric($this -> post('exAlumno')) == true && $this -> post('activoBusqueda') == 2){
			   $this -> resultEA = "";
			   echo "<div id='msgStatus' name='msgStatus' style='display:none; color:#F6F6F6;'>FALSE</div>";
			   echo "<div id='msgExAlumnos' name='msgExAlumnos' style='display:none; color:#F6F6F6;'>Es necesario realizar la busqueda con el nombre del ex-alumno</div>";
			}  
			
			else
			{
			  //Se realiza la busqueda por registro del ex-alumno
			  if($this -> post('exAlumno') != '' && $this -> post('activoBusqueda') == 1 && is_numeric($this -> post('exAlumno')) ==  true){ 
				$this -> resultEA = $Objeto -> buscar_ExAlumno_por_registro($this -> post('exAlumno'));				
			  }
			  //Se realiza la busqueda por nombre completo del ex-alumno
		      else if($this -> post('exAlumno') != '' && $this -> post('activoBusqueda') == 2 && is_numeric($this -> post('exAlumno')) ==  false ){ 
				$this -> resultEA = $Objeto -> buscar_ExAlumno_por_nombre($this -> post('exAlumno'));					
			  }
			  
              else{
			    $this -> resultEA = "";
			    echo "<div id='msgStatus' name='msgStatus' style='display:none; color:#F6F6F6;'>FALSE</div>";
			    echo "<div id='msgExAlumnos' name='msgExAlumnos' style='display:none; color:#F6F6F6;'>No se encontraron resultados</div>";
			  }
			}
		  }
		  
			else{
			   $this -> resultEA = "";
			   echo "<div id='msgStatus' name='msgStatus' style='display:none; color:#F6F6F6;'>FALSE</div>";
			   echo "<div id='msgExAlumnos' name='msgExAlumnos' style='display:none; color:#F6F6F6;'>Es necesario seleccionar el tipo de busqueda</div>";
            }
		    
			//Se le da el valor a la variable 2 = ex-Alumno
		    $this -> otroTipoPer = 2;
			
            $this -> set_response('view');
            echo $this -> render_partial('datosExAlumno');
        }
		
		
		/*
		 * Funcion que obtiene los datos del ex-alumno solicitado
		*/
        function viewDataExAlum(){
		 
		   $Objeto = new Alumnosh(); 
		  
		   $result = $Objeto -> buscar_ExAlumno_por_registro($_GET['registro']);	
          
		   foreach($result AS $value){
		     $this -> registro = $value -> reg;
		     $this -> fecha = date('d-m-Y');
             $this -> nombreExAl = $value -> nomExAlumno;
			 $this -> semestre = $value -> nivel;
			 $this -> periodo = $value -> periodo;
			 $this -> carrera = $value -> especialidad;
		   }

            $this -> set_response('view');
            echo $this -> render_partial('formularioExAlumno');
        }
        
		
        function complementario($concepto_id=0){
		
            $this -> set_response('view');
            $this -> concepto_id = $concepto_id;
			
			$this -> render_partial('complementario');
			
           /* if($concepto_id != 3 && $concepto_id != 4){
                $this -> render_partial('complementario');
            }
			else{
                switch($concepto_id){
                    case 3: $sql = "tipo = 'E' and periodo = 32009"; 
                            break;
                    case 4: $sql = "tipo = 'T' and periodo = 32009";
                            break;
                }
                $this -> examenes = Extras::reporte($sql);
                $this -> render_partial('complementarioDetallado');
            }*/
		}
        
        function agregarPagoConcepto(){
            $this -> set_response('view');
            $this -> render_partial('nota');
        }
		
		
		
		/*
		 * Esta funcion es la encargada de guardar los conceptos solicitados
		*/
		function guardarConcepto()
		{
		
		  if($this -> post('typeP') == 2 && (strlen($this -> post('nombrePost')) == 0 || strlen($this -> post('nombrePost')) < 6)){
		     Flash::error('EL NOMBRE DEL ASPIRANTE DEBE DE SER MAYOR DE 6 CARACTERES');
			 DIE();
		  }
		  
		  if($this -> post('typeP') == 4 && $this -> post('typeOtherP') == 1 && (strlen($this -> post('nombrePostPrope')) == 0 || strlen($this -> post('nombrePostPrope')) < 6)){
		     Flash::error('EL NOMBRE DEBE DE SER MAYOR DE 6 CARACTERES');
			 DIE();
		  }
		  
		  if($this -> post('typeP') == 4 && $this -> post('typeOtherP') == 1 && (strlen($this -> post('telPostPrope')) == 0 || strlen($this -> post('telPostPrope')) < 8)){
		     Flash::error('EL TELEFONO DEBE TENER MINIMO 8 CARACTERES');
			 DIE();
		  }
		 
		  //Se valida las variables segun el tipo de usuario
		  if($this -> post('typeP') == 2){
		    $nombreSol = $this -> post('nombrePost');
			$telefonoSol = "";
			$otherTypeP = 0;
		  }
		  
		  else if($this -> post('typeP') == 4 && $this -> post('typeOtherP') == 1){
		    $nombreSol = $this -> post('nombrePostPrope');
		    $telefonoSol = $this -> post('telPostPrope');
			$otherTypeP = $this -> post('typeOtherP'); 
		  }
		  
		  else if($this -> post('typeP') == 4 && $this -> post('typeOtherP') == 2){
		    $nombreSol = "";
			$telefonoSol = "";
			$otherTypeP = $this -> post('typeOtherP'); 
		  }
		  
		  else{
		     $nombreSol = "";
			 $telefonoSol = "";
			 $otherTypeP = 0;
		  }
		  

		  if($this -> post('con') != ""){
		  
			  $cajaTramites = new CajaTramites();
			  $cajaConceptos = new CajaConceptos();
			  $Alumnos = new Alumnos();
			  $periodoPagado = new pagoPeriodo();
			  
			  //Funcion que obtiene la fecha limite de pago
			  //$fechaLimitePago = $cajaTramites -> create_fecha_limite_pago();
			 // $fechaLimitePago = "22-06-2012"; 
			  
			  //variable obtiene periodo
			  /*IF(Session::get('registro') == '12310020')
			  $periodo = 32012;
			  
			  ELSE*/
			  $periodo = Session::get_data('periodo');
		      
			  //Arreglo guarda el id de los tramites agregados
			  $idTramite = Array();
			  //Arreglo guarda los extras solicitados
			  $extras = Array();
			  //Arreglo guarda los titulos solicitados
			  $titulos = Array();
			  
			  //Se valida el registro segun el tipo de persona
			  if($this -> post('typeP') == 2 || ($this -> post('typeP') == 4 && $this -> post('typeOtherP') == 1))
			    $registro = "";
				
			  else if($this -> post('typeP') == 5)
			    $registro = Session::get('registro');
				
			  else
			    $registro = $this -> post('registro');
				
				
			  if($this -> post('typeP') == 5)
			    $typeP = 1;
				
			  else
			     $typeP = $this -> post('typeP');
			  
		       
			  
		       //Obtiene el estatus del alumno(OK,BD,BT,EG) si se obtiene el registro
			   if($registro == ""){
			     $alumnos = "";
				 $ultimoPago = "";
			   }
			   
			   else{
		         $alumnos = $Alumnos -> find_first('miReg ='.$registro);
			     $ultimoPago = $periodoPagado -> find_first('registro ='.$registro);
			   }
			   
			if($this->post('extra') != ""){
			  foreach($this->post('extra') AS $valueE)  
		        $extrasInsert[] = $valueE;
			}
          
            if($this->post('titulo') != ""){		  
		      foreach($this->post('titulo') AS $valueT)  
		        $tituloInsert[] = $valueT;
			}	
				
			if($this->post('cursoNV') != ""){
		      foreach($this->post('cursoNV') AS $valueCN)  
		        $CNVInsert[] = $valueCN;
            }
			
			if($this->post('cursoAC') != ""){
		      foreach($this->post('cursoAC') AS $valueAC)  
		        $CACRInsert[] = $valueAC;
            }
			
			if($this->post('global') != ""){
		      foreach($this->post('global') AS $valueG)  
		        $globalInsert[] = $valueG;
            }
			
			if($this->post('DP') != ""){
			  foreach($this->post('DP') AS $valueDP)  
				$DPInsert[] = $valueDP;
			}
			
			 // $count = 0;
		      //Se guardan los datos de los conceptos solicitados
			  foreach( $this->post("con") as $concepto )
			  {	

			    //Se valida concepto para agregar el precio del dolar
				if($concepto == 102) 
				  $precioDolar = "12.99";
				  
				else
				   $precioDolar = "0.00";
				   
			    //Se carga los recargos
			    if($concepto == 4 && $alumnos -> stSit == "BD" && $ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito){
				    $cajaTramites -> concepto = 6;
					$cajaTramites -> registro = $registro;
					$cajaTramites -> nombre_solicitante = $nombreSol;
					$cajaTramites -> telefono_solicitante = $telefonoSol;
					$cajaTramites -> nombre_solicitante = $this -> post('nombrePost');
					$cajaTramites -> periodo =  $periodo;
					$cajaTramites -> cantidad = $this->post("cantidad".$concepto);
					$cajaTramites -> costo = "363.00";
					$cajaTramites -> monto_total = "363.00";
					$cajaTramites -> fecha_tramite = date('Y-m-d H:i:s');
					$cajaTramites -> tipo_persona = $typeP;
					$cajaTramites -> create();
				    //Obtiene el ultimo id insertado
				    $idTramite[] = mysql_insert_id();
					
					  if(date('Y') == '2012')
					    $fechaLimitePago[] = "31-12-2012";
						
					  if(date('Y') == '2013')
					    $fechaLimitePago[] = "25-03-2013";
				}
				
			    //Se carga los recargos de la primera ficha
			    /*if($concepto == 3 || $concepto == 4 && $alumnos -> stSit == "OK"){
				    $cajaTramites -> concepto = 5;
					$cajaTramites -> registro = $registro;
					$cajaTramites -> nombre_solicitante = $nombreSol;
					$cajaTramites -> telefono_solicitante = $telefonoSol;
					$cajaTramites -> periodo =  $periodo;
					$cajaTramites -> cantidad = 1;
					$cajaTramites -> costo = "279.00";
					$cajaTramites -> monto_total = "279.00";
					$cajaTramites -> fecha_tramite = date('Y-m-d H:i:s');
					$cajaTramites -> tipo_persona = $typeP;
					$cajaTramites -> create();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id = 5");
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
					  
				      //Obtiene el ultimo id insertado
				      $idTramite[] = mysql_insert_id();
				}*/
				
			    //Se carga los recargos de la segunda ficha
			    if($concepto == 3 || $concepto == 4 && $alumnos -> stSit == "OK"){
				    $cajaTramites -> concepto = 6;
					$cajaTramites -> registro = $registro;
					$cajaTramites -> nombre_solicitante = $nombreSol;
					$cajaTramites -> telefono_solicitante = $telefonoSol;
					$cajaTramites -> periodo =  $periodo;
					$cajaTramites -> cantidad = 1;
					$cajaTramites -> costo = "363.00";
					$cajaTramites -> monto_total = "363.00";
					$cajaTramites -> fecha_tramite = date('Y-m-d H:i:s');
					$cajaTramites -> tipo_persona = $typeP;
					$cajaTramites -> create();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id = 6");
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
					  
				      //Obtiene el ultimo id insertado
				      $idTramite[] = mysql_insert_id();
				}
				
				$cajaTramites -> concepto = $concepto;
				$cajaTramites -> registro = $registro;
				$cajaTramites -> nombre_solicitante = $nombreSol;
				$cajaTramites -> telefono_solicitante = $telefonoSol;
				$cajaTramites -> periodo =  $periodo;
				$cajaTramites -> cantidad = $this->post("cantidad".$concepto);
				$cajaTramites -> costo = $this->post("costo".$concepto);
				$cajaTramites -> monto_total = $this->post("total".$concepto);
				$cajaTramites -> fecha_tramite = date('Y-m-d H:i:s');
				$cajaTramites -> tipo_persona = $typeP;
				$cajaTramites -> tipo_otros = $this -> post('typeOtherP');
				$cajaTramites -> precio_dolar = $precioDolar;
				
				if($concepto != 51 && $concepto != 58 && $concepto != 94 && $concepto != 95 && $concepto != 106 && $concepto != 54){
                  $cajaTramites -> actas = "";
				  //Se guardan la cantidad por conceptos solicitados
				  for($x = 0; $x < $this->post("cantidad".$concepto); $x++) {
				    $cajaTramites -> create();
				    //Obtiene el ultimo id insertado
				    $idTramite[] = mysql_insert_id();
					
					if($concepto == 93 || $concepto == 111 ||$concepto == 3 || $concepto == 4 || $concepto == 16 || $concepto == 102){
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
					}
					else{
					  if(date('Y') == '2012')
					    $fechaLimitePago[] = "31-12-2012";
						
					  if(date('Y') == '2013')
					    $fechaLimitePago[] = "21-06-2013";
					}
				  }
                }
				
				//Se guardan los extraordinarios
				if($concepto == 58 ){
                  for($e = 0; $e < count($extrasInsert); $e++){
                    $cajaTramites -> actas = $extrasInsert[$e];
					$cajaTramites -> create();
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					
					 //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					 //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }
				}
				
				//Se guardan los derechos de pasante
				if($concepto == 54){
                  for($e = 0; $e < count($DPInsert); $e++){
                    $cajaTramites -> actas = $DPInsert[$e];
					$cajaTramites -> create();
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }
				}
				
				//Se guardan los titulo
				if($concepto == 51 ){
                  for($t = 0; $t < count($tituloInsert); $t++){
                    $cajaTramites -> actas = $tituloInsert[$t];
					$cajaTramites -> create();	
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }			  

				}
				
				//Se guardan los cursos de acreditacion
				if($concepto == 94){
                  for($a = 0; $a < count($CACRInsert); $a++){
                    $cajaTramites -> actas = $CACRInsert[$a];
					$cajaTramites -> create();
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }
				}
				
				//Se guardan los cursos de nivelacion
				if($concepto == 95){
                  for($n = 0; $n < count($CNVInsert); $n++){
                    $cajaTramites -> actas = $CNVInsert[$n];
					$cajaTramites -> create();
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }
				}
				
				//Se guardan los examenes globales
				if($concepto == 106){
                  for($n = 0; $n < count($globalInsert); $n++){
                    $cajaTramites -> actas = $globalInsert[$n];
					$cajaTramites -> create();
					//Obtiene los id de la tabla tramites
					$idTramite[] .= mysql_insert_id();
					
					  //Obtiene la informacion del conceto solicitado
					  $cajaConceptos -> find_first("id =".$concepto);
					 
					  //Cambiamos el formato de la fecha de pago
					  list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
					  $fechaInicio = $dia."-".$mes."-".$anio;
					  
					  //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
					  $fechaLimitePago[] = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);
				  }
				  
				}
				  //Variable que contiene descripcion de la accion para log_caja
				  $accionLog = "Solicito el concepto ".$concepto." para el usuario ".$registro." ".$nombreSol;
			  } 
			
			   echo "<div id='msgMuestra' name='msgMuestra' style='display:none; color: #FFFFFF;'>TRUE</div>";
			  //Funcion que guarda la accion, fecha y accion de lo solicitado en caja
			  $cajaTramites -> recordLog(Session::get_data('registro'), $accionLog, date("Y-m-d H:i:s"));
			  //Funcion que sirve para crear el numero de referencia  
			  $referencia = $cajaTramites -> create_referencia($fechaLimitePago, $registro ,$idTramite, $typeP, $otherTypeP);
		  }
		  
		  else{
             echo "<div id='msgMuestra' name='msgMuestra' style='display:none; color: #FFFFFF;'>FALSE</div>";
			 DIE();
		  }
		  
		  DIE();
		}
		
		
		/*
		 * Esta funcion es la encargada de general el reporte que fue solicitado
		*/ 
		function crearReporte()
		{
	      $this -> set_response("view");
		  $cajaTramites = new cajaTramites();
		  
		  if($this -> post('fechaDesde') > $this -> post('fechaHasta')){
		    //Mensaje de error
		    echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
		    echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="La fecha de inicio debe ser menor a la fecha final" maxlength="0"/>';
			$this -> reporteCaja = "";
		  }
		  
		  else if($this -> post('fechaDesde') == "" || $this -> post('fechaHasta') == ""){
		    //Mensaje de error
		    echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
		    echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar ambas fechas" maxlength="0"/>';
			$this -> reporteCaja = "";
		  }
		  
		  else{
		    //Reporte general por conceptos solicitados
		    if($this -> post('tipoReporte') == "General"){
		      //Obtiene el conteo general por conceptos segun la fechas solicitadas
		      $this -> reporteCaja = $cajaTramites -> reporte_general_caja($this -> post('fechaDesde'), $this -> post('fechaHasta'), $this -> post('tipoNivel'));
			  $this -> fechaD = $this -> post('fechaDesde');
			  $this -> fechaH = $this -> post('fechaHasta');
			  $this -> nivelTI = $this -> post('tipoNivel');
			  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="TRUE" maxlength="0"/>';
			  //Vista donde se muestran los resultados
			  $this -> render_partial('reporteGeneral');
		    }
		    //Reporte desglosado solicitado
		    else if($this -> post('tipoReporte') == "Desglosado"){
		      //Obtiene el reporte general de los conceptos segun la fecha solicitada
		      $this -> reporteCaja = $cajaTramites -> reporte_desglosado_caja($this -> post('fechaDesde'), $this -> post('fechaHasta'), $this -> post('tipoNivel'));
			  $this -> fechaD = $this -> post('fechaDesde');
			  $this -> fechaH = $this -> post('fechaHasta');
			  $this -> nivelTI = $this -> post('tipoNivel');
  		      echo '<input type="hidden" id="status" name="status" readonly="readonly" value="TRUE" maxlength="0"/>';
			  //Vista donde se muestran los resultados
			  $this -> render_partial('reporteDesglosado');
			}
			//Reporte de folios solicitados
			else if($this -> post('tipoReporte') == 'Folio'){
			  //Obtiene el reporte por folios segun la fecha solicitada  
			  $this -> reporteCaja = $cajaTramites -> reporte_caja_folios($this -> post('fechaDesde'), $this -> post('fechaHasta'), $this -> post('tipoEstatus'));
			  $this -> fechaD = $this -> post('fechaDesde');
			  $this -> fechaH = $this -> post('fechaHasta');
			  $this -> nivelTI = $this -> post('tipoNivel');
			  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="TRUE" maxlength="0"/>';
			  //Vista donde se muestran los resultados
			  $this -> render_partial('reporteFolios');
			}
		  }
		}
		
		/*
		 * Funcion que genera el reporte solicitado en caja en formato PDF
		*/
		function reporte_caja_PDF(){
		  $this -> set_response("view");
		  $cajaTramites = new cajaTramites();
		  
		  //Genera el reporte Genereal
		  if($_GET['tipoReporte'] == "General"){
		    $generalPDF = $cajaTramites -> reporteGeneral_PDF($_GET['fechaDesde'], $_GET['fechaHasta'], $_GET['tipoNivel']);
			//Funcion que abre el PDF
            echo "<script type='text/javascript'>	
			        var url = '/ingenieria/public/files/pdfs/reporte_caja/Reporte_General.pdf';
	                document.location = url;
				   </script>";
			$this -> render_partial('reporteGeneral');
		  }
		  
		  //Genera el reporte Desglosado
		  if($_GET['tipoReporte'] == "Desglosado"){
		    $generalPDF = $cajaTramites -> reporteDesglosado_PDF($_GET['fechaDesde'], $_GET['fechaHasta'], $_GET['tipoNivel']);
			//Funcion que abre el PDF
            echo "<script type='text/javascript'>	
			        var url = '/ingenieria/public/files/pdfs/reporte_caja/Reporte_Desglosado.pdf';
	                document.location = url;
				   </script>";
			$this -> render_partial('reporteDesglosado');
		  }
		  
		  //Genera el reporte de Folios
		  if($_GET['tipoReporte'] == "Folio"){
		     $generalPDF = $cajaTramites -> reporteFolios_PDF($_GET['fechaDesde'], $_GET['fechaHasta'], $_GET['tipoEstatus']);
			//Funcion que abre el PDF
            echo "<script type='text/javascript'>	
			        var url = '/ingenieria/public/files/pdfs/reporte_caja/Reporte_Folios.pdf';
	                document.location = url;
				  </script>";
			$this -> render_partial('reporteFolios');
		  }
		}
		
		
        /*
		 * Funcion que genera el reporte solicitado en caja en formato EXCEL
        */
        function reporte_caja_EXCEL($tipoReporte){
		  $this -> set_response("view");
		  
		  //Genera el reporte General
		  if($tipoReporte == "1"){
			$this -> render_partial('reporteGeneralExcel');
		  }
		  //Genera el reporte Desglosado
		  if($tipoReporte == "2"){
			$this -> render_partial('reporteDesglosadoExcel');
		  }
		  //Generea le reporte de Folios
		  if($tipoReporte == "3"){
		    $this -> render_partial('reporteFoliosExcel');
		  }
        }
		
		
		/*
		 * Funcion encargada de mostrar los tramites solicitados
		*/
		function muestraTramites(){

		  $this -> set_response("view");
		 
		  $cajaTramites = new CajaTramites();
		 
		  $this -> getTramites = $cajaTramites -> show_Tramites($this -> post('statusTramite'), $this -> post('statusPago'), $this -> post('tipoPersona'), $this -> post('registro'));
		 
		  //Vista donde se muestran los resultados
		  $this -> render_partial('obtenerTramites');
		}
		
		
		/*
		 * Funcion que se encarga de modificar el estatus del tramite
		*/
		function updateTramite($param)
		{
		  $cajaTramites = new CajaTramites();
		  
		  $this -> set_response("view");
		  
		  $result = explode(",",$param);
		  //Se modifica el estatus del tramite
		  $db = DbBase::raw_connect();
		  $db->query("UPDATE caja_tramites SET status_tramite = ".$result[0]." WHERE id = ".$result[1]."");
		  
		  //Variable que contiene la descripcion para el log_caja y la validacion de la variable del estatus del tramite
		  if($result[0] == 1)
		    $status = "EN PROCESO";
			
		  else if($result[0] == 2)
		    $status = "REALIZADO";
			
		  else if($result[0] == 3)
		    $status = "ENTREGADO";
			
		  else
		    $status = "";
		   	
		  $accionLog = "Se modifico el estatus del tramite con el id ".$result[1]." y se cambio a estatus : ".$status;
		  
		  //Funcion que guarda la accion en log_caja
		  $cajaTramites -> recordLog(Session::get_data('registro'), $accionLog, date("Y-m-d H:i:s"));
		  //Vista donde se muestran los resultados
		  $this -> render_partial('obtenerTramites');
		}
		
		
		/*
		 * Funcion que busca las fichas de pago de los conceptos solo las que se encuentran en estado de sin pagar
		*/
		function imprimeFichas()
		{
		  $this -> set_response("view");
		  
		  $cajaTramites = new CajaTramites();
		  
		  $this -> getFichas = $cajaTramites -> show_Fichas($this -> post('registro'), $this -> post('nombre'), $this -> pos('typePerson'), $this -> pos('apellidoP'), $this -> pos('apellidoM'));
		  
		  //Vista donde se muestran los resultados
		  $this -> render_partial('obtieneFichas');
		}
		
		/*
		* Funcio que trae los datos del alumno
		*/
		static function get_info_for_partial_info_alumno(){
		
			$alumnos = new Alumnos();
			$KardexIng = new KardexIng();
			
			
		    $alumno = $alumnos->find_first("miReg=".Session::get('registro'));
	
	
			// Nombre carrera
			$career = $alumnos->get_careername_from_student($alumno->carrera_id, $alumno->areadeformacion_id);
		
			// Nombre Plantel
			$plantel = $alumnos->get_nombre_plantel($alumno->enPlantel);
			// Turno Matutino o Vespertino
			$turno = $alumnos->get_turno($alumno->enTurno);
			// Obtener Creditos
		    $ncreditos = $KardexIng->get_creditos(Session::get('registro'),$alumno->carrera_id,$alumno->areadeformacion_id);
			// Obtener promedio
			$promedio = $KardexIng->get_average_from_kardex(Session::get('registro'));
		
			
			return $array = array(
						    "career" => $career->nombre,
							"plantel" => $plantel,
							"turno" => $turno,
							"ncreditos" => $ncreditos,
							"promedio" => $promedio,
							"enTipo" => $alumno -> enTipo,
							"vcNomAlu" => $alumno -> vcNomAlu,
							"miReg" => $alumno -> miReg,
					      );


		} // function get_info_for_partial_info_alumno($alumno)
		
		/*
		 * Funcion que obtiene los extras de los alumnos
		*/
		function extrasAlumno(){
		  $this -> set_response("view");
		  
		  $tipo = "E";
		  
		  $xextraordinarios	= new Xextraordinarios();
          $CajaConceptos = new CajaConceptos();   
		  
		  
		  /*if(Session::get('registro') == 831173){
		    $registro = 831173;
			$periodo = 32012; 
		  }

		  
		  else{*/
		  	$registro = Session::get('registro');
			$periodo = Session::get_data('periodo');
		  //}
          //Obtiene el plantel del alumno		  
		  $plantel = $xextraordinarios -> get_plantel(Session::get('registro'));
          
		  //Se obtiene el total de materias que cursa un alumno
		  $totalMaterias = $xextraordinarios -> cantidad_cursos_alumno($registro, $periodo, $plantel);
		  
		  //Se obtiene las materias que tiene el alumno como regularizacion
		  $materiasRegularizacion = $xextraordinarios -> cantidad_cursos_en_regularizacion($registro, $periodo, $plantel);
		  
		  //Se obtiene el porcentaje de las materias en regularizacion
		  $porcenRegularizacion = $xextraordinarios -> obtiene_porcentaje($totalMaterias);
		  
		  //Se obtiene las materias que tiene el alumno como BAJA DEFINITIVA
		  $materiasBD = $xextraordinarios -> cantidad_cursos_en_BD($registro, $periodo, $plantel);

		  //No se encontraron extraordinarios a pagar
	      if($materiasBD >= 1)
		     $this -> msg = "No tienes derecho a extraordinarios estas dado de baja";
		  
		 /* else if(floor($porcenRegularizacion) <= $materiasRegularizacion &&  $registro != 831173)//&&  $registro != 17009
		     $this -> msg = "No tienes derecho a extraordinarios";*/
			  
		  else
		  {
		      $this -> extras = $xextraordinarios -> get_extrasTitulos_alumno($registro ,$periodo, $plantel, $tipo);
			
			  if(count($this -> extras) == 0 )
			    $this -> msg = "No se encontraron extraordinarios a pagar";
				
			  else
			    $this -> msg = "0";
		  }

          //Obtiene el costo del extraordinario
          $this -> concepto = $CajaConceptos->find_first("id = 58");	

		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaExtras');
		}
		
		function titulosAlumno(){
		  $this -> set_response("view");
		  
		  $xextraordinarios	= new Xextraordinarios();
          $CajaConceptos = new CajaConceptos();   
		  $Alumnos = new Alumnos();
		  
		  $alumnos = $Alumnos -> find_first('miReg ='.Session::get('registro'));
		  
		  $tipo = "T";
		  
          //Obtiene el plantel del alumno		  
		  $plantel = $xextraordinarios -> get_plantel(Session::get('registro'));
			
		  //Se obtiene el total de materias que cursa un alumno
		  $totalMaterias = $xextraordinarios -> cantidad_cursos_alumno(Session::get('registro'),Session::get_data('periodo'), $plantel);
		  
		  //Se obtiene las materias que tiene el alumno como regularizacion
		  $materiasRegularizacion = $xextraordinarios -> cantidad_cursos_en_regularizacion(Session::get('registro'),Session::get_data('periodo'),$plantel);
		  
		  //Se obtiene el porcentaje de las materias en regularizacion
		  $porcenRegularizacion = $xextraordinarios -> obtiene_porcentaje($totalMaterias);
		  
		  //Se obtiene las materias que tiene el alumno como BAJA DEFINITIVA
		  $materiasBD = $xextraordinarios -> cantidad_cursos_en_BD(Session::get('registro'),Session::get_data('periodo'),$plantel);
		 
		  //No se encontraron titulos a pagar
	      if($materiasBD >= 1)
		     $this -> msg = "No tienes derecho a t&iacute;tulos estas dado de baja";
		  
		  else if($materiasRegularizacion > 4) //$alumnos -> enTipo  == "C" && 
		     $this -> msg = "No tienes derecho a t&iacute;tulos";
			 
		  /*else if(floor($porcenRegularizacion) <= $materiasRegularizacion && $alumnos -> enTipo  != "C")
		     $this -> msg = "No tienes derecho a t&iacute;tulos";*/
			  
		  else
		  {
		    //Se obtiene los titulos de un alumno
		    $this -> titulos = $xextraordinarios -> get_extrasTitulos_alumno(Session::get('registro'),Session::get_data('periodo'),$plantel,$tipo);
			
			  if(count($this -> titulos) == 0 )
			    $this -> msg = "No se encontraron t&iacute;tulos a pagar";
				
			  else
			    $this -> msg = "0";
		  }

          //Obtiene el costo del extraordinario
          $this -> concepto = $CajaConceptos->find_first("id = 51");	

		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaTitulos');
		}
		
		/*
		 * Funcion que obtiene los cursos de nivelacion de los alumnos
		*/
		function cursosNIVAlumno(){
		
          $this -> set_response("view");
		  
		  $xextraordinarios	= new Xextraordinarios();
		  $CajaConceptos = new CajaConceptos();
		  
		  //Tipo de curso
		  $tipoCurso = "NIV";
		  //Periodo INtersemestral
		  //$periodoInter = (Session::get_data('periodo')) + (10000);
		  $periodoInter = '42012';

		  $this -> cursoNiv = $xextraordinarios -> get_cursos_NIV_ACR(Session::get('registro'),$periodoInter,$tipoCurso);
		  
		  if(count($this -> cursoNiv) == 0 )
			       $this -> msg = "No se encontraron cursos de nivelacion a pagar";
		
          else
 		    $this -> msg = "0";
		  
		  //Obtiene el costo del curso de nivelacion
          $this -> concepto = $CajaConceptos->find_first("id = 95");
		  
		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaCursosNIV');
		}
		
		/*
		 * Funcion que obtiene los cursos de acreditacion de los alumnos
		*/
		function cursosACRAlumno(){
          $this -> set_response("view");
		  
		  $xextraordinarios	= new Xextraordinarios();
		  $CajaConceptos = new CajaConceptos();
		  
		  //Tipo de curso
		  $tipoCurso = "ACR";
		  //Periodo INtersemestral
		  //$periodoInter = (Session::get_data('periodo')) + (10000);
		  $periodoInter = '42012';
		  
		  $this -> cursoAcred = $xextraordinarios -> get_cursos_NIV_ACR(Session::get('registro'),$periodoInter,$tipoCurso);
		
		  if(count($this -> cursoAcred) == 0 )
			       $this -> msg = "No se encontraron cursos de acreditacion a pagar";
				
          else
 		    $this -> msg = "0";
				   
		  //Obtiene el costo del curso de nivelacion
          $this -> concepto = $CajaConceptos->find_first("id = 94");
		  
		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaCursosACR');
		}
		
		/*
		 * Funcion que obtiene los examenes globales del alumno
		*/
		function examGlobalAlumno(){
          $this -> set_response("view");
		  
		  $xextraordinarios	= new Xextraordinarios();
		  $CajaConceptos = new CajaConceptos();
		  
		  //Variable tipo de examen global
		  $tipoCurso = "EG";
		  //Periodo INtersemestral
		  //$periodoInter = (Session::get_data('periodo')) + (10000);
		  $periodoInter = '12013';
		  
		  $this -> examGlobal = $xextraordinarios -> get_Examen_Global(Session::get('registro'),$periodoInter,$tipoCurso);
		
		
		  if(count($this -> examGlobal) == 0 )
			       $this -> msg = "No se encontraron examenes globales a pagar";
				
          else
 		    $this -> msg = "0";
				   
		  //Obtiene el costo del curso de nivelacion
          $this -> concepto = $CajaConceptos->find_first("id = 106");
		  
		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaExamGlobal');
		}
		
		/*
		 * Funcion que obtiene los derechos de pasante de los alumnos
		*/
		function derechoPasante(){
		
          $this -> set_response("view");
		  
		  $xextraordinarios	= new Xextraordinarios();
		  $CajaConceptos = new CajaConceptos();

		  $this -> derechoPasante = $xextraordinarios -> muestra_derechoPasante(Session::get_data('registro'),Session::get_data('periodo'));

		  if(count($this -> derechoPasante) == 0 )
			       $this -> msg = "No se encontraron derechos de pasante a pagar";
		
          else
 		    $this -> msg = "0";
		  
		  //Obtiene el costo del curso de nivelacion
          $this -> concepto = $CajaConceptos->find_first("id = 54");
		  
		  //Vista donde se muestran los resultados
		  $this -> render_partial('ventanaDerechoPasante');
		}
		
		
		/*
		 * Funcion la cual actualiza los pagos de extras, titulos, cursos y conceptos
		*/
     function realizarPagos2()
 	 {  
	    //Abre el archivo que se indico.
        $archivo = fopen($_FILES['miArchivo']['tmp_name'], "r");
		
		$fecha = explode(".",$_FILES['miArchivo']['name']);
		$dia = substr($fecha[1],0,2);
		$mes = substr($fecha[1],2,2);
		$anio = substr($fecha[1],4,4);
		
		$fechaPago = $anio."-".$mes."-".$dia;
     
	    $xextraordinarios	= new Xextraordinarios();
		
		//Va contando la cantidad de lineas que tiene el archivo
		$totalPagos = 0;
        $otros = 0;
		$caja = 0;
		$extrasTitulos = 0;
		$extrasTitulosC = 0;
		$cursos = 0;
		$cursosC = 0;
		$examGlob = 0;
		
		while (!feof ($archivo)) {
		  if ($linea = fgets($archivo)){

		    $plantel = substr($linea,72,1);
		
		   //Valida que el pago sea de Colomos
		   if($plantel == "2")
		   {
		     $db = DbBase::raw_connect();
			 
		     //Referencia de 37 digitos
		     $referencia = substr($linea,72,37);
			
			 //Se corta la referencia 
			 $tramiteRef  = substr($referencia,1,6);
			 $registroRef = substr($referencia,7,8);
			 $conceptoRef = substr($referencia,15,3);
			 $nivel = substr($referencia,18,1); //1 = TECNOLOGO  Y 2 = INGENIERIA
			 $actas = substr($referencia,19,5);
			 $periodo = substr($referencia,24,5);
			 $fechaCondesada = substr($referencia,29,4);
			 $importeCondesado = substr($referencia,33,1);
			 $constante = substr($referencia,34,1);
			 $DV = substr($referencia,35,2);
             
			  //Plantel del alumno
			  $plantelA = $xextraordinarios -> find_all_by_sql("SELECT enPlantel
			                                                    FROM alumnos 
											                    WHERE miReg = ".$registroRef); 
			 foreach($plantelA AS $plantelIng)
		       $ingePlantel = $plantelIng -> enPlantel;				
				
		     //Valida de donde fue solicitado el pago (OTROS) y el nivel (ingenieria o tecnologo)
		     if($tramiteRef == "000000" && $nivel == "2"){
                
			  
			   //Se valida el tipo de concepto (extras y titulos)
			   if($conceptoRef == "051" || $conceptoRef == "058"){
			   
			     IF($ingePlantel == "C"){
				   //Se modifica el estatus de pago de extras o titulos
				   $resultET = $db->query("UPDATE xextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
			     IF($ingePlantel == "N"){
				   //Se modifica el estatus de pago de extras o titulos
				   $resultET = $db->query("UPDATE xtextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
				 $extrasTitulos++;
				 
				 if($resultET)
				   $this -> tituloExtra = $extrasTitulos;
			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "094" || $conceptoRef == "095"){

				  //Se modifica el estatus de pago de extras o titulos
				  $resultC = $db->query("UPDATE intersemestral_alumnos SET pago = 'OK' WHERE id = '".$actas."' AND registro = '".$registroRef."'");
	
				$cursos++;
				 
				 if($resultC)
				   $this -> cursosAN = $cursos;
			   }
			   
			   $otros++;
			   
			   $this -> otroPago = $otros;
			 }
			 
			 //Valida de donde fue solicitado el pago (CAJA) y el nivel (ingenieria o tecnologo)
		     if($tramiteRef != "000000" && $nivel == "2"){
               
			    $db->query("UPDATE caja_tramites SET status_pago = '1', fecha_pago = '".$fechaPago."' WHERE id = '".$tramiteRef."' AND registro = '".$registroRef."'");
				
			   //Se valida el tipo de concepto (extras y titulos)
			   if($conceptoRef == "051" || $conceptoRef == "058"){
			   
			     IF($ingePlantel == "C"){
				   //Se modifica el estatus de pago de extras o titulos
				   $resultETC = $db->query("UPDATE xextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
			     IF($ingePlantel == "N"){
				   //Se modifica el estatus de pago de extras o titulos
				   $resultETC = $db->query("UPDATE xtextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
				
				 $extrasTitulosC++;
				 
				 if($resultETC)
				   $this -> tituloExtraC = $extrasTitulosC;
			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "094" || $conceptoRef == "095"){
				 //Se modifica el estatus de pago de extras o titulos
				 $resultCC = $db->query("UPDATE intersemestral_alumnos SET pago = 'OK' WHERE id = '".$actas."' AND registro = '".$registroRef."'");
				 
				 $cursosC++;
				 
				 if($resultCC)
				   $this -> cursosANC = $cursosC;
			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "106"){
				 //Se modifica el estatus de pago de extras o titulos
				 $resultG = $db->query("UPDATE globales SET pago = 'OK' WHERE id = '".$actas."' AND registro = '".$registroRef."'");
				 
				 $examGlob++;
				 
				 if($resultG)
				   $this -> examenGlob = $examGlob;
			   }
			   
			   
			    $caja++;
				$this -> cajaPago = $caja;
			 }
			 
			  $totalPagos++;
			  $this -> pagosTotal = $totalPagos;
		   }
		  
		  }
		}
		
		  //Vista donde se muestran los resultados
		  $this -> render_partial('viewPagosRealizados');
     }
	 
	 
	 /*
	  * Funcion que sirve para guardar los ex-alumnos en la tabla de alumnosh
	 */
	 function guardarExAlumno(){
	 
	    if($this -> post('nameComplete') == "" || count($this -> post('nameComplete')) == 0)
		  Flash::error('Es necesario ingresar el nombre completo');
		  
		else if($this -> post('genero') == "" || count($this -> post('genero')) == 0)
		  Flash::error('Es necesario ingresar el genero');
		  
		else if($this -> post('anio') == '0' || $this -> post('mes') == '0' || $this -> post('dia') == '0')
		  Flash::error('Es necesario que ingrese una fecha de nacimiento valida');
		  
		else if($this -> post('registro') == "" || count($this -> post('registro')) == 0)
		  Flash::error('Es necesario que ingrese el registro');
		  
		else if($this -> post('vcNomEsp') == "" || count($this -> post('vcNomEsp')) == 0)
		  Flash::error('Es necesario que seleccione una especialidad');
		  
		else if($this -> post('curp') == "" || count($this -> post('curp')) == 0)
		  Flash::error('Es necesario que ingrese el CURP');
		  
		else{
	      $exAlumno = new alumnosh();

		  $exAlumno -> nombre = $this -> post('nameComplete');
		  $exAlumno -> sexo = $this -> post('genero');
		  $exAlumno -> fechanac = $this -> post('anio')."-".$this -> post('mes')."-".$this -> post('dia');
		  $exAlumno -> reg = $this -> post('registro');
		  $exAlumno -> esp = $this -> post('vcNomEsp');
		  $exAlumno -> plantel = $this -> post('plantel');
		  $exAlumno -> periodo = $this -> post('periodo');
		  $exAlumno -> curp = $this -> post('curp');
		  $exAlumno -> niv_edu = "I";
		  $exAlumno -> create();
		  
		  //Funcion que guarda en log_caja la accion
		  $cajaTramites = new CajaTramites();
		  $accionLog = "Se guardo al ex-alumno ".$this -> post('nameComplete')." con el registro ".$this -> post('registro');
		  
		  //Funcion que guarda la accion en log_caja
		  $cajaTramites -> recordLog(Session::get_data('registro'), $accionLog, date("Y-m-d H:i:s"));
		  
		  Flash::success('El registro fue guardado');
          die();
	      //$this -> set_response("view");
	      //$this -> render_partial('ventanaAgregarExAl');
		}
		
		die();
	 }
	 
	 
		//funcion que muestra el formulario para agregar a un Ex-Alumno
		function viewAgregarExalumno()
		{
		  $this -> set_response("view");
		  echo $this -> render_partial('ventanaAgregarExAl');
		}
		
		
		//Funcion que redirecciona la busqueda o formulario segun la seleccion del usuario
		function redirectViewOther()
		{
		  $this -> set_response("view");
		  
		  //Segun el tipo de usuario es la vista que muestra
		  if($_GET['typeSelect'] == 1){
		     $this -> otroTipoPer = "1";
		     echo $this -> render_partial('buscadorOtroPrope');
		  }

			 
		  else if($_GET['typeSelect']  == 2)
		     echo $this -> render_partial('buscadorExAlumnos');


		  else if($_GET['typeSelect'] == 3)
		    die('Externos');
			
		  else
		    die('Se debe de seleccionar una opcion');
		}
	 

	/*
	 * Funcion la cual actualiza los pagos de extras, titulos, cursos y conceptos
	*/
     function realizarPagos()
 	 {  
	    //Abre el archivo que se indico.
        $archivo = fopen($_FILES['miArchivo']['tmp_name'], "r");
		
		$fecha = explode(".",$_FILES['miArchivo']['name']);
		$dia = substr($fecha[1],0,2);
		$mes = substr($fecha[1],2,2);
		$anio = substr($fecha[1],4,4);
		
		$fechaPago = $anio."-".$mes."-".$dia;
     
	    $xextraordinarios	= new Xextraordinarios();
        $intermemestral	= new IntersemestralAlumnos();
		
		//Va contando la cantidad de lineas que tiene el archivo
		$totalPagos = 0;
        $otros = 0;
		$caja = 0;
		$extrasTitulos = 0;
		$extrasTitulosC = 0;
		$cursos = 0;
		$cursosC = 0;
		$examGlob = 0;
		$reinscripcion = 0;
		$derechoPas = 0;
		
		while (!feof ($archivo)) {
		  if ($linea = fgets($archivo)){

		    $plantel = substr($linea,72,1);
		
		   //Valida que el pago sea de Colomos
		   if($plantel == "2")
		   {
		     $db = DbBase::raw_connect();
			 
		     //Referencia de 37 digitos
		     $referencia = substr($linea,72,37);
			
			 //Se corta la referencia 
			 $tramiteRef  = substr($referencia,1,6);
			 $registroRef = substr($referencia,7,8);
			 $conceptoRef = substr($referencia,15,3);
			 $nivel = substr($referencia,18,1); //1 = TECNOLOGO  Y 2 = INGENIERIA
			 $actas = substr($referencia,19,5);
			 $periodo = substr($referencia,24,5);
			 $fechaCondesada = substr($referencia,29,4);
			 $importeCondesado = substr($referencia,33,1);
			 $constante = substr($referencia,34,1);
			 $DV = substr($referencia,35,2);
             
			  //Plantel del alumno
			  $plantelA = $xextraordinarios -> find_all_by_sql("SELECT enPlantel
			                                                    FROM alumnos 
											                    WHERE miReg = ".$registroRef); 
			 foreach($plantelA AS $plantelIng)
		       $ingePlantel = $plantelIng -> enPlantel;				
				
             
			 //Obtiene el ID de intersemestral alumno
			 $interAlumo = $intermemestral->find_all_by_sql("SELECT ia.id AS ia_id
														     FROM intersemestral_cursos ic
														     JOIN intersemestral_alumnos ia ON ia.clavecurso = ic.clavecurso
														     WHERE ic.id = '".$actas."' AND ia.registro =".$registroRef);

			  foreach($interAlumo AS $value)
		         $interID = $value -> ia_id;
		     
			 //Valida de donde fue solicitado el pago (OTROS) y el nivel (ingenieria o tecnologo)
		     if($tramiteRef == "000000" && $nivel == "2"){
                
			  
			   //Se valida el tipo de concepto (extras y titulos)
			   if($conceptoRef == "051" || $conceptoRef == "058"){
			   
			     IF($ingePlantel == "C"){
				   //Se modifica el estatus de pago de extras o titulos
				   $db->query("UPDATE xextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
			     IF($ingePlantel == "N"){
				   //Se modifica el estatus de pago de extras o titulos
				   $db->query("UPDATE xtextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
				  ECHO "EXTRAS-TITULOS-OTROS ---- ".$db->affected_rows()."---- id ---".$actas."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> tituloExtra = $extrasTitulos;
				   $extrasTitulos++;
				 }

			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "094" || $conceptoRef == "095"){
			   
				  //Se modifica el estatus de pago de extras o titulos
				  $db->query("UPDATE intersemestral_alumnos SET pago = 'OK' WHERE id = '".$interID."' AND registro = '".$registroRef."'");
				
				ECHO "CURSOS-OTROS ---- ".$db->affected_rows()."---- id ---".$interID."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> cursosAN = $cursos;
				   $cursos++;
				 }
			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "003" || $conceptoRef == "004"){
			   
				  //Se modifica el estatus de pago de extras o titulos
				  $db->query("UPDATE alumnos SET pago = '1' WHERE miReg = '".$registroRef."'");
				
				 ECHO "REINSCRIPCION -INSCRIPCION---- ".$db->affected_rows()."----registro------".$registroRef."<br>"; 
				 
				 if($db->affected_rows() == "1"){
				   $this -> re_inscripcion = $reinscripcion; 
				   $reinscripcion++;
				 }
			   }
			   
			   $otros++;
			   
			   $this -> otroPago = $otros;
			 }
			 
			 //Valida de donde fue solicitado el pago (CAJA) y el nivel (ingenieria o tecnologo)
		     if($tramiteRef != "000000" && $nivel == "2"){
               
			    if($registroRef == "00000041" || $registroRef == "41")
			      $db->query("UPDATE caja_tramites SET status_pago = '1', fecha_pago = '".$fechaPago."' WHERE id = '".$tramiteRef."'");
				
				else
			      $db->query("UPDATE caja_tramites SET status_pago = '1', fecha_pago = '".$fechaPago."' WHERE id = '".$tramiteRef."' AND registro = '".$registroRef."'");
				
				ECHO "CAJA ---- ".$db->affected_rows()."---- id_Tramite ---".$tramiteRef."----registro------".$registroRef."<br>"; 
				
			   //Se valida el tipo de concepto (cursos inscripcion y reinscripcion)
			   if($conceptoRef == "003" || $conceptoRef == "004" || $conceptoRef == "006"){
			   
			      //Se valida que tenga el pago de los recargos
				  $CajaTramites = new CajaTramites();

					if($conceptoRef == "006")
					   $pagoRecargos = $CajaTramites -> find_first("concepto = 6 AND id = '".$tramiteRef."' AND registro =".$registroRef);
					   
					else
				      $pagoRecargos = $CajaTramites -> find_first("concepto = 6 AND registro =".$registroRef);
				 
				    if($pagoRecargos -> status_pago == 1 ){
			          //Se modifica el estatus de pago de inscripcion o reinscripcion
			          $db->query("UPDATE alumnos SET pago = '1' WHERE miReg = '".$registroRef."'");
                        ECHO "REVIZAR REINSCRIPCION -INSCRIPCION CAJA---- ".$db->affected_rows()."----registro------".$registroRef."<br>"; 
					  
						 if($db->affected_rows() == "1"){
						   $this -> re_inscripcion = $reinscripcion; 
						   $reinscripcion++;
						 }
				    }
				  else{
				    ECHO "AUN NO PAGA RECARGOS REINSCRIPCION - INSCRIPCION CAJA---- ".$db->affected_rows()."----registro------".$registroRef."<br>"; 
				  }
				 //}
				 
				/* else if(substr($fechaTramite -> fecha_tramite,0,10) <= "2012-08-19"){
			          //Se modifica el estatus de pago de inscripcion o reinscripcion
			          $db->query("UPDATE alumnos SET pago = '1' WHERE miReg = '".$registroRef."'");
                        ECHO "REINSCRIPCION -INSCRIPCION CAJA---- ".$db->affected_rows()."----registro------".$registroRef."<br>";
					  
				      if($db->affected_rows() == "1"){
				        $this -> re_inscripcion = $reinscripcion; 
				        $reinscripcion++;
				      }
				 }*/
			   }
			   
			   //Se valida el tipo de concepto (extras y titulos)
			   if($conceptoRef == "051" || $conceptoRef == "058"){
			   
			     IF($ingePlantel == "C"){
				   //Se modifica el estatus de pago de extras o titulos
				   $db->query("UPDATE xextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
			     IF($ingePlantel == "N"){
				   //Se modifica el estatus de pago de extras o titulos
				   $db->query("UPDATE xtextraordinarios SET estado = 'OK' WHERE curso_id = '".$actas."' AND registro = '".$registroRef."'");
				 }
				 
				 ECHO "EXTRAS-TITULOS-CAJA ---- ".$db->affected_rows()."---- id ---".$actas."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> tituloExtraC = $extrasTitulosC;
				   $extrasTitulosC++;
				 }
			   }
			   
			   //Se valida el tipo de concepto (extras y titulos)
			   if($conceptoRef == "054"){
			   
				 //Se modifica el estatus de pago de derecho de pasante
				 $db->query("UPDATE derechopasante SET estado = 'OK' WHERE idDerecho = '".$actas."' AND registro = '".$registroRef."'");

				 ECHO "DERECHO DE PASANTE ---- ".$db->affected_rows()."---- id ---".$actas."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> derechoPas = $derechoPas;
				   $derechoPas++;
				 }
			   }
			   
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "094" || $conceptoRef == "095"){
				 //Se modifica el estatus de pago de extras o titulos
				 $db->query("UPDATE intersemestral_alumnos SET pago = 'OK' WHERE id = '".$interID."' AND registro = '".$registroRef."'");
				 
				ECHO "CURSOS-CAJA ---- ".$db->affected_rows()."---- id ---".$interID."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> cursosANC = $cursosC;
				   $cursosC++;
				 }
			   }
			    
			   //Se valida el tipo de concepto (cursos nivelacion y acreditacion)
			   if($conceptoRef == "106"){
				 //Se modifica el estatus de pago de extras o titulos
				 $resultG = $db->query("UPDATE globales SET pago = 'OK' WHERE id = '".$actas."' AND registro = '".$registroRef."'");
				 
				 ECHO "GLOBALES-CAJA ---- ".$db->affected_rows()."---- id ---".$actas."----registro------".$registroRef."<br>"; 

				 if($db->affected_rows() == "1"){
				   $this -> examenGlob = $examGlob;
				   $examGlob++;
				 }
			   }
			    $caja++;
				$this -> cajaPago = $caja;
			 }
			 
			  $totalPagos++;
			  $this -> pagosTotal = $totalPagos;
		   }
		  
		  }
		}
		
		  //Vista donde se muestran los resultados
		  $this -> render_partial('viewPagosRealizados');
     }
	 
	 /*
	  * Funcion la cual guarda la referencia en otra tabla
	 */
     function guardarPagos()
 	 {  
	    //Abre el archivo que se indico.
        $archivo = fopen($_FILES['miArchivo']['tmp_name'], "r");
		
		$fecha = explode(".",$_FILES['miArchivo']['name']);
		$dia = substr($fecha[1],0,2);
		$mes = substr($fecha[1],2,2);
		$anio = substr($fecha[1],4,4);
		
		$fechaPago = $anio."-".$mes."-".$dia;
     
	  
		while (!feof ($archivo)) {
		  if ($linea = fgets($archivo)){

		    $plantel = substr($linea,72,1);
		    $consecutivo = substr($linea,106,1);
		//var_dump($plantel); 
		   //Valida que el pago sea de Colomos
		   if($plantel == "2")
		   {
		     $db = DbBase::raw_connect();
			 
		     //Referencia de 37 digitos
		     $referencia = substr($linea,72,37);
			
			 //Se corta la referencia 
			 $tramiteRef  = substr($referencia,1,6);
			 $registroRef = substr($referencia,7,8);
			 $conceptoRef = substr($referencia,15,3);
			 $nivel = substr($referencia,18,1); //1 = TECNOLOGO  Y 2 = INGENIERIA
			 $actas = substr($referencia,19,5);
			 $periodo = substr($referencia,24,5);
			 $fechaCondesada = substr($referencia,29,4);
			 $importeCondesado = substr($referencia,33,1);
			 $constante = substr($referencia,34,1);
			 $DV = substr($referencia,35,2);
             
			 //Se corta la referencia 
			 $tramiteRef2  = substr($referencia,1,6);
			 $registroRef2 = substr($referencia,7,8);
			 $conceptoRef2 = substr($referencia,15,4);
			 $nivel2 = substr($referencia,19,3); //1 = TECNOLOGO  Y 2 = INGENIERIA
			 $plantel2 = substr($referencia,22,2);
			 $periodo2 = substr($referencia,24,5);
			 $fechaCondesada2 = substr($referencia,29,4);
			 $importeCondesado2 = substr($referencia,33,1);
			 $constante2 = substr($referencia,34,1);
			 $DV2 = substr($referencia,35,2);

			 if($tramiteRef == "000000")
			   $tipoPago = "2";
			   
			 else if($tramiteRef != "000000")
			   $tipoPago = "1";
			  

			if(($plantel2 == "02" || $plantel2 == "06" || $plantel2 == "06") && $nivel2 == "002" ){
			
				 $pagosReferencia = new pagosreferencia();
				 //SE GUARDA LA REFERENCIA
				 $pagosReferencia -> registro = $registroRef2;
				 $pagosReferencia -> nivel = $nivel2;
				 $pagosReferencia -> periodo = $periodo2;
				 $pagosReferencia -> conceptoRef = $conceptoRef2;
				 //$pagosReferencia -> actas = "";
				 $pagosReferencia -> referencia = $referencia;
				 $pagosReferencia -> tipo_pago = "1";
				 $pagosReferencia -> fecha_pago = $fechaPago;
				 $pagosReferencia -> create();
				 $this -> result =  "<div style='font-size:15px; margin-top:15px; font-weight:bold; font-family:arial;margin-bottom:50px;'>Los datos fueron guardados</div>";
			}
			
            else if($nivel == "1" || $nivel == "2"){
				 $pagosReferencia = new pagosreferencia();
				 //SE GUARDA LA REFERENCIA
				 $pagosReferencia -> registro = $registroRef;
				 $pagosReferencia -> nivel = $nivel;
				 $pagosReferencia -> periodo = $periodo;
				 $pagosReferencia -> conceptoRef = $conceptoRef;
				 $pagosReferencia -> actas = $actas;
				 $pagosReferencia -> referencia = $referencia;
				 $pagosReferencia -> tipo_pago = $tipoPago;
				 $pagosReferencia -> fecha_pago = $fechaPago;
				 $pagosReferencia -> create();
				 $this -> result =  "<div style='font-size:15px; margin-top:15px; font-weight:bold; font-family:arial;margin-bottom:50px;'>Los datos fueron guardados</div>";
			}
		  }
		  
		  }
		}
		  //$this -> result =  "";
		  //Vista donde se muestran los resultados
		  $this -> render_partial('viewPagosGuardados');
     }
	 
		 //Funcion que busca el registro solicitado y muestra los periodos pagados
		 function muestra_periodos(){
		 
		   $this -> set_response("view");
		   
		   if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
			 //Mensaje de error
			 echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
			 echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
				$this -> result = "";
			}
			
		   else{
			 $objeto = new alumnosh(); 
			 $this -> result = $objeto -> buscar_registro_alumno($this -> post('registro'));
			 
			 IF(count($this -> result) <= 0 || $this -> result == null){
				echo '<div style="font-weight:bold; width:790px;text-align:left;font-size:16px;"></div>';
			 }
			 
			 else{
			   foreach($this -> result AS $value)
				 $nombre = " Nombre : ".utf8_encode($value -> nombre);
		
			   echo '<div style="font-weight:bold; width:790px;text-align:left;font-size:16px;">'.$nombre.'</div>';
			 }
			 
			 $this -> render_partial('muestraPeriodos');
		   }
		 }
		 
		 
		/*
		 * Funcion que se encarga de modificar las fechas de los conceptos
		*/ 
		function modificaFechas()
		{
		  $cajaTramites = new CajaTramites();

	      if($this->post('concepto') == 0)
		    Flash::error('Es necesario seleccionar el concepto que se desea modificar');
			
		  else if($this->post('fechaVencimiento') == "")
		    Flash::error('Es necesario ingresar la fecha');
			
		  else{
		    $concepto = split(',', $this->post('concepto'));
			
			if($this->post('activar') == "on")
			  $activo = 1;
			  
			else 
			  $activo = 0;
			  
		    //Se modifica el estatus del concepto
		    $db = DbBase::raw_connect();
		    $result = $db->query("UPDATE caja_conceptos SET fecha_pago = '".$this->post('fechaVencimiento')."', activo = '".$activo."' WHERE id = ".$concepto[0]."");
		  
		    if($result = true){
		      Flash::success('la fecha del concepto '.$this->post('concepto').' fue modificado');
			  echo '<script type="text/javascript">clearCampos();</script>';
		    }
		    else{
		      Flash::error('la fecha del concepto '.$this->post('concepto').' no pudo ser modificado');
		    }
		  }
		  
		  die();
		}
		
		//Funcion importa archivo y modifica conceptos
		function updateConceptos(){
		
			if($_FILES['file']['name'] != '')
			{
               //Abre el archivo que se indico.
               $archivo = fopen($_FILES['file']['tmp_name'], "r");	
         
		       while (!feof ($archivo)){
                 if ($linea = fgets($archivo)){	
                   $conceptos = split(',',$linea);	
                    
                   $idConcepto = $conceptos[0];					
                   $costoConcepto = $conceptos[1];					
				   
				   $db = DbBase::raw_connect();
				   $db->query("UPDATE caja_conceptos SET costo = '".$costoConcepto."' WHERE id = '".$idConcepto."'");
				   
				   if($db->affected_rows() == "1"){
				     ECHO "El concepto <b>".$idConcepto."</b> fue modificado con el costo <b>$ ".$costoConcepto."</b><br>"; 
				   }
				   else{
				     ECHO "El concepto <b>".$idConcepto." NO</b> pudo ser modificado<br>"; 
				   }
				   
			     }
			   } 
			}
			
			else{
			  Flash::error('Es necesario seleccionar un archivo');
			}
			
			//Vista donde se muestran los resultados
		    $this -> render_partial('resultAdminConceptos');
		}
		
		 
		function valida()
		{
		   if(Session::get_data('registro') == 8108 || Session::get_data('registro') == 1833 || Session::get_data('registro') == 2374  || Session::get_data('registro') == 2307 || Session::get_data('registro') == 9122 || Session::get_data('registro') == 2448 || Session::get_data('registro') == 9124 || Session::get("tipousuario") == "PROPEDEUTICO" || Session::get("tipousuario") == "FINANZAS" || Session::get("tipousuario") == "VENTANILLA"){//Session::get("tipo") == "VENTANILLA"{
			return true;
		  }
		
		  else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
		
		function validaAlumno()
		{
		  if(Session::get("tipousuario" ) == "ALUMNO"){
			return true;
		  }
		
		  else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
		
		function validaAspPrope()
		{
		  if(Session::get("tipousuario") == "PROPEDEUTICO"){
		 
			return true;
		  }
		
		  else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
		
		function validaFinanzas()
		{
		  if(Session::get("tipousuario") == "FINANZAS"){
			return true;
		  }
		
		  else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
		
		
	    /*
		 * Esta funcion se encarga de validar que no ingresen atravez de la URL
		*/
		public function url_validacion(){

		    $url= !isset($_SERVER['HTTP_REFERER']);
			
			if($url == true)
              $this->redirect('caja/accesourlCaja');
			  
	    }

	} 
	
?>

                          