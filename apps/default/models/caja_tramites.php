<?php			
class CajaTramites extends ActiveRecord {
		
		/*
		 * Esta funcion sirve para  obtener el total general de los conceptos
		 * que fueron solicitados y los cuales pertenecen a ventanilla
		*/
		public function get_total_conceptos_por_ventanilla(){
		    
			$record = $this->find_all_by_sql("SELECT IFNULL(SUM(ct.cantidad),0) AS totalConceptoV
											  FROM caja_tramites ct
											  JOIN caja_conceptos cc ON cc.id = ct.concepto
											  WHERE cc.tipo_tramite = 1");
			return $record;
		}

		
        /*
		 * Esta funcion sirve para obtener el monto ($$$) total de los conceptos
		 * que fueron solicitados y los cuales pertenecen a ventanilla
        */
		public function get_totalMonto_conceptos_por_ventanilla(){
		
			$record = $this->find_all_by_sql("SELECT IFNULL(SUM(ct.monto_total),'0.00') AS montoVentanilla
			                                  FROM caja_tramites ct
											  JOIN caja_conceptos cc ON cc.id = ct.concepto
											  WHERE cc.tipo_tramite = 1");
												 
			return $record;
		}
		
		
		/*
		 * Esta funcion sirve para obtener el total general de los conceptos
		 * que fueron solicitados y los cuales pertenecen a caja
		*/
		public function get_total_conceptos_por_caja(){
		
			$record = $this->find_all_by_sql("SELECT SUM(ct.cantidad) AS totalConceptoC
			                                  FROM caja_tramites ct
											  JOIN caja_conceptos cc ON cc.id = ct.concepto
											  WHERE cc.tipo_tramite = 0");
												 
			return $record;
		}
		
		
		/* 
         * Esta funcion sirve para obtener el monto ($$$) total de los conceptos
         * que fueron solicitados y los cuales pertenecen a caja		 
		*/
		public function get_totalMonto_conceptos_por_caja(){
			
			$record = $this->find_all_by_sql("SELECT SUM(ct.monto_total) AS montoCaja
			                                  FROM caja_tramites ct
											  JOIN caja_conceptos cc ON cc.id = ct.concepto
											  WHERE cc.tipo_tramite = 0");
												 
			return $record;		
		}
		
		
		/*
		 * Esta funcion sirve para obtener el total de conceptos que fueron solicitados regresando
		 * el codigo del concepto, tipo de tramite, cantidad, precio y el monto total
		*/
		public function get_reporte_conceptos_general(){
              
			  //Obtiene todos los conceptos
			  $cajaConceptos = new CajaConceptos();   
              
              $obtieneConceptos = $cajaConceptos -> find("columns: id");	
  
              $record[] = array();
			  
              foreach($obtieneConceptos AS $value)	
			  {

	            //Obtiene el reporte por concepto solicitado mostrando el codigo de concepto, tipo de tramite, cantidad, precio y monto total
			    $record[] = $this -> find_all_by_sql("SELECT ct.concepto AS concepto, cc.tipo_tramite, cc.nombre, cc.costo, SUM(ct.cantidad) AS cantidad, SUM(monto_total) AS total
			                                                                  FROM caja_tramites ct
																			  JOIN caja_conceptos cc ON cc.id = ct.concepto
																			  WHERE ct.concepto = ".$value -> id."
																			  GROUP BY concepto"); 
			  }	
			  
			  return $record;
		}
		
		
		/*
		 * Funcion que sirve para general una nueva fecha al sumar dias a una fecha
		 * @param String $fechaInicio fecha de inicio de pago day-month-year (a este parametro s ele sumaran los dias para general nueva fecha)
		 * @param int $diasAgregados parametro que contiene la cantidad de dias que se sumaran a la fecha de inicio 
		*/
		public function create_fecha_limite_pago($fechaInicio,$diasAgregados){
		
		  if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fechaInicio))
            list($dia,$mes,$anio)=split("/", $fechaInicio);
            
          if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fechaInicio))
              list($dia,$mes,$anio) = split("-",$fechaInicio);
			  
		  //Primer digito es la cantidad de dias que se desea sumar	  
          $fechaPago = mktime(0,0,0, $mes,$dia,$anio) + $diasAgregados * 24 * 60 * 60;
         
		  $fechaLimitePago = date("d-m-Y",$fechaPago);
		  
		  return $fechaLimitePago;
		}
		
		
		/*
		 * Funcion que sirve para crear la referencia que solicita el banco.
		 * @param String $fechaLimite fecha limite de pago en el formato  day/month/year (con este parametro obtendremos la fecha condesada)
		 * @param int $registro Registro del alumno que solicito los conceptos
		 * @param int $idTramite array que contiene el id de los tramites que fueron guardados (se usara para crear la referencia)
		 * @param int $tipoPersona Contiene el tipo de persona que solicita un concepto (alumno, aspirante, empleado u otros)
		*/
		public function create_referencia($fechaLimite, $registro, $idTramite, $tipoPersona, $otherTypeP){

		  $cajaReferencia = new CajaReferencia();

		  //Validacion de los digitos del registro segun el tipo de persona
		  if($tipoPersona == 2)
		  {
		    $registroRef = "00000002";
			$registro = "";
		  }
		  
		  else if($tipoPersona == 4 && $otherTypeP == 1)
		  {
		    $registroRef = "00000041";
			$registro = "";
		  }
			
		  else
		  {
		    if(strlen($registro) == 3)
		      $registroRef = "00000".$registro;
			  
		    else if(strlen($registro) == 4)
		      $registroRef = "0000".$registro;
			  
		    else if(strlen($registro) == 5)
		      $registroRef = "000".$registro;
			  
		    else if(strlen($registro) == 6)
		      $registroRef = "00".$registro;
			
	        else if(strlen($registro) == 7)
		      $registroRef = "0".$registro;
			
		    else
		      $registroRef = $registro;
		  }
		  
		  //Se genera la fecha condesada
		  foreach($fechaLimite AS $valueFecha)
		  {
			//Se genera la fecha condesada
			list($diaPago,$mesPago,$anioPago) = split("-",$valueFecha);
			  
			$anioCondesado = ($anioPago - 1988) * 372;
			$mesCondesado  = ($mesPago - 1) * 31;
			$diaCondesado  = ($diaPago - 1);
			  
			//Se obtiene la fecha condesada
			$fechaCondesada[] = ($anioCondesado + $mesCondesado + $diaCondesado);
		  }
		
		  $actaExtrasTitulos = Array();
		  //Se obtienen el costo de los conceptos solicitados para posteriormente sacar el importe condesado 
		  foreach($idTramite AS $value)
		  { 
			//la variable $importe de tipo array guarda el costo de los conceptos solicitados en un arreglo
			$importe = Array();
			$importe = $this -> find_all_by_sql("SELECT c.costo, t.actas
			                                     FROM caja_conceptos c
												 JOIN caja_tramites t ON t.concepto = c.id
												 WHERE t.id = ".$value); 
     
            //Obtiene el importe por cada concepto solicitado 			
		    foreach($importe AS $value)
		    {
		      $importeCon = $value -> costo;
			  $importeConcepto = explode(".",$importeCon);
			  $importeEntero = $importeConcepto[0].$importeConcepto[1];

			  //Se valida el importe y se obtendra el importe a multiplicar
			  if(strlen($importeEntero) == 3)
			    $importeMultiplicar = "00000".$importeEntero;

			  else if(strlen($importeEntero) == 4)
			    $importeMultiplicar = "0000".$importeEntero;

			  else if(strlen($importeEntero) == 5)
			    $importeMultiplicar = "000".$importeEntero;
				
			  else if(strlen($importeEntero) == 6)
			    $importeMultiplicar = "00".$importeEntero;
				
			  else if(strlen($importeEntero) == 7)
			    $importeMultiplicar = "0".$importeEntero;
				
			  else
			    $importeMultiplicar = $importeEntero;
			  
			  //Esta variable contiene el numero de factor de peso el cual se multiplicara con $importeMultiplicar
			  $numFactoPeso = "37137137";
			  
			  //Iniciamos la variable suma en cero
			  $suma = 0;
			  
			  //For que recorre el arreglo y realiza la multipplicacion
			  for($i = strlen($importeMultiplicar)-1; $i >= 0; $i--)
			  {
				//Se realiza la multiplicacion numero x numero
			    $numero = substr($importeMultiplicar,$i,1) * substr($numFactoPeso,$i,1);
				  
				//Se realiza la suma con los resultados de la multiplicacion
				$suma = $suma + $numero;
			  }
			  
			  //Se obtiene el importe condesado
			  $importeCondesado[] =  $suma % 10;
			  

              //Valida el numero de caracterede de extras, titulos y cursos
			  if(strlen($value -> actas) == 0)
				 $actaExtrasTitulos[] = "00000";
				 
			  else if(strlen($value -> actas) == 1)
				 $actaExtrasTitulos[] = "0000".$value -> actas;
					
			  else if(strlen($value -> actas) == 2)
				 $actaExtrasTitulos[] = "000".$value -> actas;
					  
			  else if(strlen($value -> actas) == 3)
				 $actaExtrasTitulos[] = "00".$value -> actas;
					  
			  else if(strlen($value -> actas) == 4)
				 $actaExtrasTitulos[] = "0".$value -> actas;
					  
			  /*else if(strlen($value -> actas) == 5)
				 $actaExtrasTitulos[] = "0".$value -> actas;*/
					  
			  else
				 $actaExtrasTitulos[] = $value -> actas;
		    }
		  }

		  //Segun el nivel que solicito el concepto es la variable que se utilizara
		  $tecnologo = "1";
		  $ingenieria = "2";
			  
		  //Se declara la variable constante y se le da el valor 2 (EL VALOR NO CAMBIA) 
		  $constante = "2";
			 
		  //Plantel (Colomos - 2 ______ Tonala - 6)
		  $colomos = "2";
		  
		  //Numero de factor de peso a multiplicar para obtener el numero verificador
		   //Para 40 digitos $factorPesoDV = "1713112319171311231917131123191713112319171311231917131123191713112319171311";
		  $factorPesoDV = "2319171311231917131123191713112319171311231917131123191713112319171311"; //Para 37 digitos
		 //Se inicia la variable $sumRef en cero
		  $sumRef = 0;
		  
		  for($i=0; $i < count($idTramite); $i++)
		  {
		    //la variable $consulCon de tipo array guarda el concepto
			$consulCon = Array();
			$consulCon = $this -> find_all_by_sql("SELECT concepto
			                                         FROM caja_tramites 
												     WHERE id = ".$idTramite[$i]);  
			
            //Se validan los digitos del id para ser usados para la referencia (debe de ser de 6 digitos)			
		    if(strlen($idTramite[$i]) == 1)
			  $tramiteRef = "00000".$idTramite[$i];

            else if(strlen($idTramite[$i]) == 2)
			  $tramiteRef = "0000".$idTramite[$i];
				
			else if(strlen($idTramite[$i]) == 3)
			  $tramiteRef = "000".$idTramite[$i];
				
			else if(strlen($idTramite[$i]) == 4)
			  $tramiteRef = "00".$idTramite[$i];
				   
			else if(strlen($idTramite[$i]) == 5)
			  $tramiteRef = "0".$idTramite[$i];
				   
			else
			  $tramiteRef= $idTramite[$i];  
		  
			
			//Valida la cantidad de caracteres del concepto (debe ser de 3 digitos) y se guarda en un arreglo
			foreach($consulCon AS $key=>$value)
		    {
			  if(strlen($value -> concepto) == 1)
				$conceptoRef[] = "00".$value -> concepto;
				  
			  else if(strlen($value -> concepto) == 2)
				$conceptoRef[] = "0".$value -> concepto;
				  
			  else if(strlen($value -> concepto) == 3)
				$conceptoRef[] = $value -> concepto;
				  
			 /* else
				$conceptoRef[] = $value -> concepto;*/
			}

			//Periodo que se cursa
		    $periodo = Session::get_data('periodo');
			
			//Se declara la variable que guardara la primera parte de la referencia
			$referencia1[] = $colomos.$tramiteRef.$registroRef.$conceptoRef[$i].$ingenieria.$actaExtrasTitulos[$i].$periodo.$fechaCondesada[$i].$importeCondesado[$i].$constante;

			//For que recorre el arrelo y realiza la multipplicacion
			for($x = strlen($referencia1[$i])-1; $x >= 0; $x--)
			{
			  $y = $x+$x;
			  //Se realiza la multiplicacion numero x numero
			  $numeroRef = substr($referencia1[$i],$x,1) * substr($factorPesoDV,$y,2);
				
			  //Se realiza la suma con los resultados de la multiplicacion
			  $sumRef = $sumRef + $numeroRef;
			}
			
			//Se obtiene el remanente 
			$digitoVer[] = ($sumRef % 97) + 1;
		    
			//Se reinicia la variable en cero
			$sumRef = 0;
	      }

		  //For para obtener la $referencia1 
		  for($i=0; $i < count($referencia1); $i++)
		  {
		    //Se valida el digito verificador (debe de ser de 2 digitos)
		    if(strlen($digitoVer[$i]) == 1)
			  $digitoVerificador = "0".$digitoVer[$i];
			  
			else
			  $digitoVerificador = $digitoVer[$i];
			
            //Se obtiene la referencia completa			
			$referenciaCompleta = $referencia1[$i].$digitoVerificador;
             
            //Se guarda la referencia completa junto con el id del tramite y el registro de quien solicita
			$cajaReferencia -> idTramite = $idTramite[$i];
			$cajaReferencia -> referencia = $referenciaCompleta;
			$cajaReferencia -> registro = $registro;	
			$cajaReferencia -> create();	
			
		    //Obtiene el ultimo id insertado
			$idReferencia[] = mysql_insert_id();
		  }
		    $cajaReferencia -> crear_fichaPago_PDF($idReferencia,$fechaLimite,$tipoPersona,$otherTypeP);
            die();
		}
		

		/*
		 * Funcion que sirve para obtener los tramites solicitados y el estatus donde se encuentra.
		 * @param String $statusTramites manda el estatus del tramite que se solicita(solicitado(0), en proceso(1) y entregado(2))
		 * @param int $statusPago manda el estatus de pago que tiene el tramite
		 * @param int $tipoPersona manda el tipo de persona
		 * @param int $registro se recibe este parametro en caso de realizar una busqueda
		*/
		 static public function show_Tramites($statusTramites = "", $statusPago = 0, $tipoPersona = "", $registro = ""){

		  $cajaTramites = new CajaTramites();
		  
		  //Se valida el tipo de tramite
		  if($statusTramites == "Todos")
		    $tramitesStatus = "";
			
		  else
		    $tramitesStatus = "status_tramite = ".$statusTramites;
			
		  //Se valida el tipo de pago
		  if($statusPago == 0)//$statusPago == "Todos"
		     $PagoStatus = "status_pago = 0";
			
		  else if($statusPago == 1)
		    $PagoStatus = "status_pago = 1";
			
		  else if($statusPago == "Todos")
		    $PagoStatus = "(status_pago = 1 OR status_pago = 0)";
			
		  else
		    $PagoStatus = "";
			
		  //Se valida el tipo de persona
		  if($tipoPersona == "Todos")
		    $typePerson = "";
			
		  else
		    $typePerson = "tipo_persona = ".$tipoPersona;
			
		  if($registro != "")
		    $searchRegistro = "AND registro = ".$registro;
			
		  else
		    $searchRegistro = "";
			
		  //Se valida si se coloca en AND en la consulta
		  if($tramitesStatus == "" || $PagoStatus == "")
		    $and = "";
			
		  else
		    $and = "AND";
			
		  //Se valida si se coloca el segundo AND en la consulta
		  if($tipoPersona == "" || $tipoPersona == "Todos" || $PagoStatus == "")
		    $and2 = "";
			
		  else
		    $and2 = "AND";
			
		  if(($statusTramites == "Todos" && $statusPago == "Todos" && $tipoPersona == "Todos") || ($statusTramites == "" && $statusPago == "" && $tipoPersona == ""))
		  {
		    $sql = "SELECT t.*, c.nombre
					FROM caja_conceptos c
					JOIN caja_tramites t ON t.concepto = c.id
					WHERE t.status_pago = 1 OR t.status_pago = 0";
	      }
		  
		  else
		  {
		    $sql = "SELECT t.*, c.nombre
					FROM caja_conceptos c
					JOIN caja_tramites t ON t.concepto = c.id
					WHERE ".$tramitesStatus." ".$and." ".$PagoStatus." ".$and2." ".$typePerson." ".$searchRegistro;
	      }
		  
		  $tramites = $cajaTramites -> find_all_by_sql($sql);
		  
		  return $tramites;
		}
		
		/*
		 * Funcion que sirve para obtener los tramites de propedeutico,aspirantes.
		*/
		static public function tramites_propedeutico(){

		  $cajaTramites = new CajaTramites();
		  
		    $sql = "SELECT t.*, c.nombre, r.referencia
					FROM caja_conceptos c
					JOIN caja_tramites t ON t.concepto = c.id
					JOIN caja_referencia r ON r.idTramite = t.id
					WHERE t.concepto = 93 OR t.concepto = 111";

		  $tramites = $cajaTramites -> find_all_by_sql($sql);
		 
		  return $tramites;
		}
		
		/*
		 * Funcion que sirve para mostrar las fichas de los conceptos que se encuentran con estatus sin pagar (0).
		 * @param string $nombre Se recibe este parametro en caso de realizar la busqueda por nombre
		 * @param string $apellidoP Se recibe este parametro en caso de realizar la busqueda por apellido paterno
		 * @param string $apellidoM Ser recibe este parametro en caso de realizar la busqueda por apellido materno
		 * @param int    $tipoPersona Se recibe el tipo de persona solicitado
		 * @param int    $registro Se recibe este parametro en caso de realizar la busqueda por registro
		*/
		public function show_Fichas($registro, $nombre, $apellidoP, $apellidoM, $tipoPersona){
		
		  $cajaTramites = new CajaTramites();
		  
		  /*if($tipoPersona == 1)
		  {
		    $sql = "SELECT a.registro, ct.id AS idTramite, cr.id AS idReferencia, 
			        FROM alumnos a
					JOIN caja_tramites ct ON ct.registro = a.registro
					JOIN caja_referencia cr ON cr.idTramite = ct.id"
		  }
		  $sql = "SELECT 
		          FROM aspirantes ";*/
		}
		

		/*
		 * Funcion que sirve para eliminar las fichas que ya fueron pagadas
		*/
		static public function delete_fichas_pagadas(){
		
          $cajaTramites = new CajaTramites();
		  
		  //Obtiene las fichas que ya fueron pagadas
		  $sqlDelete = "SELECT ct.*, cr.referencia
					    FROM alumnos a
					    JOIN caja_tramites ct ON ct.registro = a.miReg
					    JOIN caja_referencia cr ON cr.idTramite = ct.id
					    JOIN caja_conceptos c ON c.id = ct.concepto
					    WHERE ct.status_pago = 1"; 
			
           $deleteFile = $cajaTramites -> find_all_by_sql($sqlDelete);	
        
          foreach($deleteFile AS $value)
		  {
		    $deleteFilePDF = $_SERVER['DOCUMENT_ROOT']."/ControlEscolar/ingenieria/public/files/pdfs/caja/Referencia_".$value -> referencia.".pdf"; 

			if(file_exists($deleteFilePDF) == true)
			  unlink($deleteFilePDF);
		
			else
			  echo "";
		  }		  
		}
		
		
	    /*
		 * Funcion que sirve para mostrar las fichas de los conceptos que se encuentran con estatus sin pagar (0).
		 * estas fichas seran mostradas en  la sesion del alumno.
		*/
		static public function show_Fichas_Alumno(){
		
		  $cajaTramites = new CajaTramites();
		  
		  //CajaTramites::delete_fichas_pagadas();
		  
		  //Fichas que no han sido pagadas
		  $sql = "SELECT ct.*, a.miReg,cr.referencia, cr.id AS idReferencia, c.nombre
				  FROM alumnos a
				  JOIN caja_tramites ct ON ct.registro = a.miReg
				  JOIN caja_referencia cr ON cr.idTramite = ct.id
				  JOIN caja_conceptos c ON c.id = ct.concepto
				  WHERE ct.registro = ".Session::get('registro'); 
				  
		   //$sql .= "AND ct.status_pago = 0";
		
		   $record = $cajaTramites -> find_all_by_sql($sql);
		   
		   return $record;
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
			
		/*
		 * convierte una fecha formato datetime (2010-01-29 10:28:03) al formato (miercoles 09-10-2010 10:28:03)
		 * @param String $datetime si es true el dia de la semana se regresa coon una letra ejem. (l,m,i,j,v,s,d)
		 * @param bolean $split por default false si es true regresa Array(dia,fecha,hora)
		*/
		  static public function getFullDateTime($datetime, $split = false)
		  {
		    $cajaTramites = new CajaTramites();
			
			list($date,$time) = explode(" ",$datetime);
			$weekday = CajaTramites::getDay($date,true);
			$date    = explode("-",$date);
			$date    = $date[2]."&#47;".$date[1]."&#47;".substr($date[0],2,2);

			$time = substr($time,0,5). " Hrs";

			$fullDatetime = $weekday."&#45;".$date."&nbsp;&nbsp;".$time;

			if($split)
			  $fullDatetime = explode(" ",$fullDatetime);

			return $fullDatetime;
		  }
		
		/*
		 * Funcion que permite guardar la acicon realizadas por los usuarios
		 * @param int       $registro Contiene el registro o nomina del usuario que realiza la accion
		 * @param dateTime  $fecha Contiene la fecha y hora que se realizo la accion
		 * @param text      $accion Contiene la accion que realizo el usuario dentro del sistema (agregar, modificar, eliminar, cancerlar, etc...)
  	    */
		function recordLog($registro, $accion, $fecha){
         
		   $logCaja = new LogCaja();

           $logCaja -> usuario = $registro;	
           $logCaja -> accion = $accion;	
           $logCaja -> fecha_accion = $fecha;	
		   $logCaja -> create();
		}
		
		
    	/*
		 * Funcion que sirve para general el reporte general de caja segun el rango de fechas solicitadas. (reporte por conceptos conteo GENERAL)
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoNivel Tipo de nivel que se solicita (Ambos = 0, Tecnolo = 1, Ingenieria = 2)
		*/ 
		 static public function reporte_general_caja($fechaDesde, $fechaHasta, $tipoNivel){
		
           $cajaTramites = new CajaTramites();
		   //Validacion segun el nivel que se desea ver
		   if($tipoNivel == 1)
		     $nivel = " AND p.nivel = 1 ";
			 
		   else if($tipoNivel == 2)
		     $nivel = " AND p.nivel = 2 ";
			 
		   else
			 $nivel = "";
			 
		   //Consulta que obtiene el reporte de caja
		   $sql = "SELECT p.periodo, p.conceptoRef, t.costo, p.tipo_pago, c.nombre, p.nivel
				   FROM pagosreferencia p 
				   JOIN caja_conceptos c ON p.conceptoRef = c.id
				   JOIN caja_tramites t ON t.concepto = c.id
				   WHERE  t.fecha_pago between '$fechaDesde' AND '$fechaHasta' AND p.fecha_pago between '$fechaDesde' AND '$fechaHasta' $nivel 
				   GROUP BY p.conceptoRef,p.nivel
				   ORDER BY p.conceptoRef ASC";

           $result = $cajaTramites -> find_all_by_sql($sql);	
		   
		   return $result;
		 }
		 
		 
    	/*
		 * Funcion que sirve para general el reporte desglosado de caja segun el rango de fechas solicitadas.
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoNivel Tipo de nivel que se solicita (Ambos = 0, Tecnolo = 1, Ingenieria = 2)
		*/ 
		 static public function reporte_desglosado_caja($fechaDesde, $fechaHasta, $tipoNivel){
		
           $cajaTramites = new CajaTramites();
		   //Validacion segun el nivel que se desea ver
		   if($tipoNivel == 1)
		     $nivel = " AND p.nivel = 1 ";
			 
		   else if($tipoNivel == 2)
		     $nivel = " AND p.nivel = 2 ";
			 
		   else
			 $nivel = "";
			 
		   //Consulta que obtiene el reporte de caja
		   $sql = "SELECT substr(p.referencia,2,6) as idTramite, p.registro, p.nivel, p.periodo, p.conceptoRef, t.costo, p.referencia, p.tipo_pago, p.fecha_pago, c.nombre
                   FROM pagosreferencia p 
				   JOIN caja_conceptos c ON p.conceptoRef = c.id
				   JOIN caja_referencia r ON r.referencia = p.referencia
				   JOIN caja_tramites t ON t.id = r.idTramite
                   WHERE p.fecha_pago between '$fechaDesde' and '$fechaHasta' $nivel ORDER BY fecha_pago DESC ";
			
           $result = $cajaTramites -> find_all_by_sql($sql);	
		   
		   return $result;
		 }
		 
		 
    	/*
		 * Funcion que sirve para general el reporte desglosado de caja segun el rango de fechas solicitadas.
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoEstatus Estatus que se encuentra el pago del folio(Sin Pagar = 0, Pagado = 1, Ambos = 2)
		*/ 
		 static public function reporte_caja_folios($fechaDesde, $fechaHasta, $tipoEstatus){
		
           $cajaTramites = new CajaTramites();
		   //Validacion segun el nivel que se desea ver
		   if($tipoEstatus == 1)
		     $statusPago = " AND t.status_pago = 1 ";
			 
		   else if($tipoEstatus == 0)
		     $statusPago = " AND t.status_pago = 0 ";
			 
		   else
			 $statusPago = "";
			 
		   //Consulta que obtiene el reporte de caja
		   $sql = "SELECT t.id as idTramite, t.registro, t.periodo, t.concepto, t.costo, r.referencia, t.status_pago, t.fecha_tramite, c.nombre
                   FROM caja_tramites t 
				   JOIN caja_conceptos c ON c.id = t.concepto
				   JOIN caja_referencia r ON r.idTramite = t.id 
                   WHERE t.fecha_tramite between '$fechaDesde' and '$fechaHasta' $statusPago ORDER BY t.id ASC";
			
			
           $result = $cajaTramites -> find_all_by_sql($sql);	
		   
		   return $result;
		 }
		 
		 
    	/*
		 * Funcion que sirve para contar el numero de datos de tiene el reporte solicitado (Esta funcion es para el paginador)
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $idConcepto Numero de concepto que se desea realizar el conteo
		 * @param int $tipoNivel Tipo de nivel que se solicita (Ambos = 0, Tecnolo = 1, Ingenieria = 2)
		*/
		static public function count_reporte_general($fechaDesde, $fechaHasta, $idConcepto, $tipoNivel){
		  
		  $cajaTramites = new CajaTramites();
		   //Validacion segun el nivel que se desea ver
		   if($tipoNivel == 1)
		     $nivel = " AND p.nivel = 1 ";
			 
		   else if($tipoNivel == 2)
		     $nivel = " AND p.nivel = 2 ";
			 
		   else
			 $nivel = "";
			 
		   //Consulta que obtiene el reporte de caja
	       $sql = "SELECT COUNT(p.conceptoRef) AS totalConcepto
				   FROM pagosreferencia p 
				   JOIN caja_conceptos c ON p.conceptoRef = c.id
				   WHERE p.fecha_pago between '$fechaDesde' and '$fechaHasta' AND p.conceptoRef = $idConcepto $nivel
				   GROUP BY p.conceptoRef
				   ORDER BY p.fecha_pago DESC";
				   
           $result = $cajaTramites -> find_all_by_sql($sql);	
		   
		   foreach($result AS $value)
		     $countResult =  $value -> totalConcepto;
			 
		   return $countResult;
		}
		
    	/*
		 * Funcion que sirve para convertir el mes de numero a letra
		 * @param date (m) $fechaMes Fecha para obtener el nombre del mes
		*/
		static public function nombre_mes($fechaMes){
		
		  //Funcion para obtener el nombre del mes
		  switch ($fechaMes)
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
		
    	/*
		 * Funcion que sirve para generar en PDF el reporte general de caja
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoNivel Tipo de nivel que se solicita (Ambos = 0, Tecnolo = 1, Ingenieria = 2)
		*/
		function reporteGeneral_PDF($fechaDesde,$fechaHasta,$tipoNivel){
		     
		      $cajaTramites = new CajaTramites();
			  
			  //Obtiene fecha de impresion
			  list($anioImp,$mesImp,$diaImp)= split("/", date('Y/m/d'));
			  $nombreMesImp = CajaTramites::nombre_mes($mesImp);
			  
			  //Obtiene fecha de inicio (Desde) de reporte
			  list($anioDesde,$mesDesde,$diaDesde)= split("/", $fechaDesde);
			  $nombreMesD = CajaTramites::nombre_mes($mesDesde);
			  
			  //Obtiene fecha final (Hasta) del reporte
			  list($anioHasta,$mesHasta,$diaHasta)= split("/", $fechaHasta);
			  $nombreMesH = CajaTramites::nombre_mes($mesHasta);
			  
			  $pdf = new FPDF();
			  $pdf -> Open();
			  $pdf -> AddFont('Verdana','','verdana.php');
			  $pdf -> AddPage();
			  $pdf -> Image('public/img/CETI_n.jpg', 5,5,40,40);
			  
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', 'B', 14);
			  $pdf->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);
			  $pdf->Ln();
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', '', 10);
			  $pdf->MultiCell(0, 3, "MODULO DE CAJA PLANTEL COLOMOS", 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(38);
			  $pdf->SetFont('Arial', 'B', 10);
			  $pdf->Cell(80, 3, "CORTE GENERAL", 0, 'C', 0);
			  $pdf->SetX(138);
			  $pdf->SetFont('Arial', '', 8);
			  $pdf->MultiCell(0, 3, "Fecha de Impresión : ".$diaImp." / ".$nombreMesImp." / ".$anioImp, 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(50);
			  $pdf->SetFont('Arial', 'B', 9);
			  $pdf->MultiCell(144, 8, "CORTE DE CAJA               DEL  ".$diaDesde." / ".$nombreMesD." / ".$anioDesde."              AL  ".$diaHasta." / ".$nombreMesH." / ".$anioHasta, 1, 'L', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetDrawColor(60);
			  $pdf->SetFillColor(100);
			  $pdf->SetTextColor(250);
			  $pdf->SetFont("Arial","B",8);
			  $pdf->GetStringWidth(100);
			  $pdf->SetX(8);

			  //Titulos de la tabla
			  $pdf->Cell(24,8,"No. CONCEPTO",1,0,"C",1);
			  $pdf->Cell(61,8,"NOMBRE CONCEPTO",1,0,"C",1);
			  $pdf->Cell(23,8,"PERIODO",1,0,"C",1);
			  $pdf->Cell(23,8,"NIVEL",1,0,"C",1);
			  $pdf->Cell(22,8,"CANTIDAD",1,0,"C",1);
			  $pdf->Cell(22,8,"COSTO",1,0,"C",1);
			  $pdf->Cell(22,8,"TOTAL",1,0,"C",1);
  
			  //Obtiene los registros
			  $result = CajaTramites::reporte_general_caja($fechaDesde,$fechaHasta,$tipoNivel);
			  $y = 66; 
			  
			  //Se inician variables que se utilizan
			  $numPage = 1;
			  $i = 0;
			  $montoTotal = 0;
			  
			  //Se colocan los datos
			  foreach($result AS $value)
			  {
			    $i++;
				
				if($i%2 == 0)
				 $Fila = 0;

				else
				   $Fila = 1;
				
				//Obtiene el nobre del periodo
				$Periodos = new Periodos();
				$nombrePeriodo = Periodos::nombre_periodo($value -> periodo);
				
		        //Obtiene la cantidad por concepto solicitado
	            $cajaTramites = new CajaTramites();
		        $countConcepto = CajaTramites::count_reporte_general($fechaDesde, $fechaHasta, $value -> conceptoRef, $value -> nivel);
       
				//Suma los costos para obtener el monto total
		        $total = $countConcepto * $value -> costo;
		  
				//Se realiza la suma del monto total
				$montoTotal = $montoTotal + $total;
				
			    //Validacion de caracteres de los conceptos
				if(strlen($value -> conceptoRef) == 1)
				  $concepto = "000".$value -> conceptoRef;

				else if(strlen($value -> conceptoRef) == 2)	
				  $concepto = "00".$value -> conceptoRef;	  
					
				else if(strlen($value -> conceptoRef) == 3)	
				  $concepto = "0".$value -> conceptoRef;	 

				else
				  $concepto = $value -> conceptoRef;	  
				//Termina validacion conceptos
				
				//Se valida el nombre del nivel
				if($value -> nivel == 1)
				  $nivel = "TECNÓLOGO";
				  
				else
				  $nivel = "INGENIERÍA";
				  
				//Termina validacion del nivel
				$pdf->SetDrawColor(0);
				$pdf->SetFillColor(230);
				$pdf->SetTextColor(1);
				$pdf->SetXY(8,$y);
				$pdf->SetFont("Arial","",6);
				$pdf->Cell(24,5,$concepto,1,0,"C",$Fila); 
				$pdf->Cell(61,5,$value -> nombre,1,0,"C",$Fila);
				$pdf->Cell(23,5,$nombrePeriodo,1,0,"C",$Fila);
				$pdf->Cell(23,5,$nivel,1,0,"C",$Fila);
				$pdf->Cell(22,5,$countConcepto,1,0,"C",$Fila);
				$pdf->Cell(22,5,"$ ".number_format($value -> costo,2),1,0,"C",$Fila);
				$pdf->Cell(22,5,"$ ".number_format($total,2),1,0,"C",$Fila);
				

				if($numPage > 1)
				  $NoLineas = 51;
				  
				else
				  $NoLineas = 40;
				  
				if($i == $NoLineas){
				   $y = 10; 
				   $numPage++;
				   $i = 0;
				   
				   $pdf -> AddPage();
				   $pdf->SetDrawColor(60);
				   $pdf->SetFillColor(100);
				   $pdf->SetTextColor(250);
				   $pdf->SetFont("Arial","B",8);
				   $pdf->GetStringWidth(100);
				   $pdf->SetXY(8,8);
				   //Titulos de la tabla
				   $pdf->Cell(24,8,"No. CONCEPTO",1,0,"C",1);
				   $pdf->Cell(61,8,"NOMBRE CONCEPTO",1,0,"C",1);
				   $pdf->Cell(23,8,"PERIODO",1,0,"C",1);
				   $pdf->Cell(23,8,"NIVEL",1,0,"C",1);
				   $pdf->Cell(22,8,"CANTIDAD",1,0,"C",1);
				   $pdf->Cell(22,8,"COSTO",1,0,"C",1);
				   $pdf->Cell(22,8,"TOTAL",1,0,"C",1);
				   
				}
				
				$y +=5; 
				
				//Numeracion pagina
				$pdf->SetXY(188,268);
				$pdf->SetTextColor(1);
				$pdf->SetFont("Arial","",9);
				$pdf->Cell(25,8,$numPage,0,0,"C",0);
			  }
		
			  $pdf->SetDrawColor(60);
			  $pdf->SetFillColor(100);
			  $pdf->SetTextColor(250);
			  $pdf->SetFont("Arial","B",8);
			  $pdf->GetStringWidth(100);
			  $pdf->SetXY(139,$y);
			  //Titulos de la tabla
			  $pdf->Cell(44,8,"TOTAL",1,0,"C",1);
			  $pdf->Cell(22,8,"$ ".number_format($montoTotal,2),1,0,"C",1);
			  
			  $pdf -> Output("public/files/pdfs/reporte_caja/Reporte_General.pdf","F");
		}
		
		
   	    /*
		 * Funcion que sirve para generar en PDF el reporte general de caja
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoNivel Tipo de nivel que se solicita (Ambos = 0, Tecnolo = 1, Ingenieria = 2)
		*/
		function reporteDesglosado_PDF($fechaDesde,$fechaHasta,$tipoNivel){
		     
		      $cajaTramites = new CajaTramites();
			  
			  //Obtiene fecha de impresion
			  list($anioImp,$mesImp,$diaImp)= split("/", date('Y/m/d'));
			  $nombreMesImp = CajaTramites::nombre_mes($mesImp);
			  
			  //Obtiene fecha de inicio (Desde) de reporte
			  list($anioDesde,$mesDesde,$diaDesde)= split("/", $fechaDesde);
			  $nombreMesD = CajaTramites::nombre_mes($mesDesde);
			  
			  //Obtiene fecha final (Hasta) del reporte
			  list($anioHasta,$mesHasta,$diaHasta)= split("/", $fechaHasta);
			  $nombreMesH = CajaTramites::nombre_mes($mesHasta);
			  
			  $pdf = new FPDF();
			  $pdf -> Open();
			  $pdf -> AddFont('Verdana','','verdana.php');
			  $pdf -> AddPage();
			  $pdf -> Image('public/img/CETI_n.jpg', 5,5,40,40);
			  
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', 'B', 14);
			  $pdf->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);
			  $pdf->Ln();
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', '', 10);
			  $pdf->MultiCell(0, 3, "MODULO DE CAJA PLANTEL COLOMOS", 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(38);
			  $pdf->SetFont('Arial', 'B', 10);
			  $pdf->Cell(80, 3, "CORTE DESGLOSADO", 0, 'C', 0);
			  $pdf->SetX(138);
			  $pdf->SetFont('Arial', '', 8);
			  $pdf->MultiCell(0, 3, "Fecha de Impresión : ".$diaImp." / ".$nombreMesImp." / ".$anioImp, 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(50);
			  $pdf->SetFont('Arial', 'B', 9);
			  $pdf->MultiCell(144, 8, "CORTE DE CAJA               DEL  ".$diaDesde." / ".$nombreMesD." / ".$anioDesde."              AL  ".$diaHasta." / ".$nombreMesH." / ".$anioHasta, 1, 'L', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetDrawColor(60);
			  $pdf->SetFillColor(100);
			  $pdf->SetTextColor(250);
			  $pdf->SetFont("Arial","B",6);
			  $pdf->GetStringWidth(100);
			  $pdf->SetX(8);
			  //Titulos de la tabla
			  $pdf->Cell(16,8,"No. TRAMITE",1,0,"C",1);
			  $pdf->Cell(18,8,"No. CONCEPTO",1,0,"C",1);
			  $pdf->Cell(64,8,"NOMBRE CONCEPTO",1,0,"C",1);
			  $pdf->Cell(16,8,"REGISTRO",1,0,"C",1);
			  $pdf->Cell(16,8,"NIVEL",1,0,"C",1);
			  $pdf->Cell(20,8,"PERIODO",1,0,"C",1);
			  $pdf->Cell(20,8,"FECHA DE PAGO",1,0,"C",1);
			  $pdf->Cell(16,8,"COSTO",1,0,"C",1);

               
			  //Obtiene los registros
			  $result = CajaTramites::reporte_desglosado_caja($fechaDesde,$fechaHasta,$tipoNivel);
			  $y = 66; 
			  //Se inician variables que se utilizan
			  $numPage = 1;
			  $i = 0;
			  $montoTotal = 0;
			  
			  //Se colocan los datos
			  foreach($result AS $value)
			  {
			    $i++;
				
				if($i%2 == 0)
				 $Fila = 0;

				else
				   $Fila = 1;
				
				
		        //Validacion de tipo de pago
		        if($value -> nivel == "1")
			      $nivel = "TECNÓLOGO";
			
		        else
			      $nivel = "INGENIERÍA";
				  
			    //Validacion de caracteres de los conceptos
				if(strlen($value -> conceptoRef) == 1)
				  $concepto = "000".$value -> conceptoRef;

				else if(strlen($value -> conceptoRef) == 2)	
				  $concepto = "00".$value -> conceptoRef;	  
					
				else if(strlen($value -> conceptoRef) == 3)	
				  $concepto = "0".$value -> conceptoRef;	 

				else
				  $concepto = $value -> conceptoRef;	  
				//Termina validacion conceptos
				
		        //Validacion del registro
                if($value -> registro == 41 || $value -> registro == "")
                  $registroRef = "SIN REGISTRO";	
         
                else
		          $registroRef = $value -> registro;	
				  
				//Obtiene el nobre del periodo
				$Periodos = new Periodos();
				$nombrePeriodo = Periodos::nombre_periodo($value -> periodo);
				
				//Se realiza la suma del monto total
				$montoTotal = $montoTotal + $value -> costo;
				
				$pdf->SetDrawColor(0);
				$pdf->SetFillColor(230);
				$pdf->SetTextColor(1);
				$pdf->SetXY(8,$y);
				$pdf->SetFont("Arial","",6);
				$pdf->Cell(16,5,$value -> idTramite,1,0,"C",$Fila); 
				$pdf->Cell(18,5,$concepto,1,0,"C",$Fila); 
				$pdf->Cell(64,5,$value -> nombre,1,0,"C",$Fila);
				$pdf->Cell(16,5,$registroRef,1,0,"C",$Fila);
				$pdf->Cell(16,5,$nivel,1,0,"C",$Fila);
				$pdf->Cell(20,5,$nombrePeriodo,1,0,"C",$Fila);
				$pdf->Cell(20,5,$value -> fecha_pago,1,0,"C",$Fila);
				$pdf->Cell(16,5,"$ ".number_format($value -> costo,2),1,0,"C",$Fila);
				
				if($numPage > 1)
				  $NoLineas = 51;
				  
				else
				  $NoLineas = 40;
				  
				if($i == $NoLineas){
				   $y = 10; 
				   $numPage++;
				   $i = 0;
				   
				   $pdf -> AddPage();
				   $pdf->SetDrawColor(60);
				   $pdf->SetFillColor(100);
				   $pdf->SetTextColor(250);
				   $pdf->SetFont("Arial","B",6);
				   $pdf->GetStringWidth(100);
			  	   $pdf->SetXY(8,8);
				   //Titulos de la tabla
				   $pdf->Cell(16,8,"No. TRAMITE",1,0,"C",1);
				   $pdf->Cell(18,8,"No. CONCEPTO",1,0,"C",1);
				   $pdf->Cell(64,8,"NOMBRE CONCEPTO",1,0,"C",1);
				   $pdf->Cell(16,8,"REGISTRO",1,0,"C",1);
				   $pdf->Cell(16,8,"NIVEL",1,0,"C",1);
				   $pdf->Cell(20,8,"PERIODO",1,0,"C",1);
				   $pdf->Cell(20,8,"FECHA DE PAGO",1,0,"C",1);
				   $pdf->Cell(16,8,"COSTO",1,0,"C",1);
				}
				
				$y +=5; 
				
				//Numeracion pagina
				$pdf->SetXY(188,268);
				$pdf->SetTextColor(1);
				$pdf->SetFont("Arial","",9);
				$pdf->Cell(25,8,$numPage,0,0,"C",0);
				
			  }
		
			  $pdf->SetDrawColor(60);
			  $pdf->SetFillColor(100);
			  $pdf->SetTextColor(250);
			  $pdf->SetFont("Arial","B",8);
			  $pdf->GetStringWidth(100);
			  $pdf->SetXY(119,$y);
			  //Titulos de la tabla
			  $pdf->Cell(50,8,"TOTAL",1,0,"C",1);
			  $pdf->Cell(25,8,"$ ".number_format($montoTotal,2),1,0,"C",1);

			  $pdf -> Output("public/files/pdfs/reporte_caja/Reporte_Desglosado.pdf","F"); 
		}
		
   	    /*
		 * Funcion que sirve para generar en PDF el reporte general de caja
		 * @param date (Y/m/d) $fechaDesde Fecha para generar el reporte de caja
		 * @param date (Y/m/d) $fechaHasta Fecha para generar el reporte de caja
		 * @param int $tipoEstatus Estatus que se encuentra el pago del folio(Sin Pagar = 0, Pagado = 1, Ambos = 2)
		*/
		function reporteFolios_PDF($fechaDesde,$fechaHasta,$tipoEstatus){
		     
		      $cajaTramites = new CajaTramites();
			  
			  //Obtiene fecha de impresion
			  list($anioImp,$mesImp,$diaImp)= split("/", date('Y/m/d'));
			  $nombreMesImp = CajaTramites::nombre_mes($mesImp);
			  
			  //Obtiene fecha de inicio (Desde) de reporte
			  list($anioDesde,$mesDesde,$diaDesde)= split("/", $fechaDesde);
			  $nombreMesD = CajaTramites::nombre_mes($mesDesde);
			  
			  //Obtiene fecha final (Hasta) del reporte
			  list($anioHasta,$mesHasta,$diaHasta)= split("/", $fechaHasta);
			  $nombreMesH = CajaTramites::nombre_mes($mesHasta);
			  
			  $pdf = new FPDF();
			  $pdf -> Open();
			  $pdf -> AddFont('Verdana','','verdana.php');
			  $pdf -> AddPage();
			  $pdf -> Image('public/img/CETI_n.jpg', 5,5,40,40);
			  
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', 'B', 14);
			  $pdf->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);
			  $pdf->Ln();
			  $pdf->SetX(18);
			  $pdf->SetFont('Arial', '', 10);
			  $pdf->MultiCell(0, 3, "MODULO DE CAJA PLANTEL COLOMOS", 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(38);
			  $pdf->SetFont('Arial', 'B', 10);
			  $pdf->Cell(80, 3, "CORTE POR FOLIOS", 0, 'C', 0);
			  $pdf->SetX(138);
			  $pdf->SetFont('Arial', '', 8);
			  $pdf->MultiCell(0, 3, "Fecha de Impresión : ".$diaImp." / ".$nombreMesImp." / ".$anioImp, 0, 'C', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetX(50);
			  $pdf->SetFont('Arial', 'B', 9);
			  $pdf->MultiCell(144, 8, "CORTE DE CAJA               DEL  ".$diaDesde." / ".$nombreMesD." / ".$anioDesde."              AL  ".$diaHasta." / ".$nombreMesH." / ".$anioHasta, 1, 'L', 0);
			  
			  $pdf->Ln();
			  $pdf->Ln();
			  $pdf->SetDrawColor(60);
			  $pdf->SetFillColor(100);
			  $pdf->SetTextColor(250);
			  $pdf->SetFont("Arial","B",6);
			  $pdf->GetStringWidth(100);
			  $pdf->SetX(8);
			  //Titulos de la tabla
			  $pdf->Cell(9,8,"FOLIO",1,0,"C",1);
			  $pdf->Cell(13,8,"CONCEPTO",1,0,"C",1);
			  $pdf->Cell(64,8,"NOMBRE CONCEPTO",1,0,"C",1);
			  $pdf->Cell(16,8,"REGISTRO",1,0,"C",1);
			  $pdf->Cell(16,8,"NIVEL",1,0,"C",1);
			  $pdf->Cell(20,8,"PERIODO",1,0,"C",1);
			  $pdf->Cell(20,8,"FECHA TRAMITE",1,0,"C",1);
			  $pdf->Cell(16,8,"COSTO",1,0,"C",1);
			  $pdf->SetFont("Arial","B",5.8);
			  $pdf->Cell(16,8,"ESTATUS PAGO",1,0,"C",1);
 
              $pdf->SetFont("Arial","B",6);
			  
			  //Obtiene los registros
			  $result = CajaTramites::reporte_caja_folios($fechaDesde,$fechaHasta,$tipoEstatus);
			  $y = 66; 
			  //Se inician variables que se utilizan
			  $numPage = 1;
			  $i = 0;
			  $montoTotal = 0;
			  
			  //Se colocan los datos
			  foreach($result AS $value)
			  {
			    $i++;
				
				if($i%2 == 0)
				 $Fila = 0;

				else
				   $Fila = 1;
				
				
			    $nivel = "INGENIERÍA";
				  
			    //Validacion de caracteres de los conceptos
				if(strlen($value -> concepto) == 1)
				  $concepto = "000".$value -> concepto;

				else if(strlen($value -> concepto) == 2)	
				  $concepto = "00".$value -> concepto;	  
					
				else if(strlen($value -> concepto) == 3)	
				  $concepto = "0".$value -> concepto;	 

				else
				  $concepto = $value -> concepto;	  
				//Termina validacion conceptos
				
		        //Validacion del registro
                if($value -> registro == 41 || $value -> registro == "")
                  $registroRef = "SIN REGISTRO";	
         
                else
		          $registroRef = $value -> registro;	
				 
                //Validacion del estatus de pago
			    if($value -> status_pago == "1")
				  $statusPago = "PAGADO";
				
			    else
				  $statusPago = "SIN PAGAR";
                //Termina validacion de estatus de pago		
				
				//Obtiene el nobre del periodo
				$Periodos = new Periodos();
				
				if($value -> periodo != null | $value -> periodo != "")
				  $nombrePeriodo = Periodos::nombre_periodo($value -> periodo);
		        
				else
		          $nombrePeriodo = "";
				  
				//Se realiza la suma del monto total
				$montoTotal = $montoTotal + $value -> costo;
				
				$pdf->SetDrawColor(0);
				$pdf->SetFillColor(230);
				$pdf->SetTextColor(1);
				$pdf->SetXY(8,$y);
				$pdf->SetFont("Arial","",6);
				$pdf->Cell(9,5,$value -> idTramite,1,0,"C",$Fila); 
				$pdf->Cell(13,5,$concepto,1,0,"C",$Fila); 
				$pdf->Cell(64,5,$value -> nombre,1,0,"C",$Fila);
				$pdf->Cell(16,5,$registroRef,1,0,"C",$Fila);
				$pdf->Cell(16,5,$nivel,1,0,"C",$Fila);
				$pdf->Cell(20,5,$nombrePeriodo,1,0,"C",$Fila);
				$pdf->Cell(20,5,substr($value -> fecha_tramite,0,11),1,0,"C",$Fila);
				$pdf->Cell(16,5,"$ ".number_format($value -> costo,2),1,0,"C",$Fila);
				$pdf->Cell(16,5,$statusPago,1,0,"C",$Fila);
				
				if($numPage > 1)
				  $NoLineas = 51;
				  
				else
				  $NoLineas = 40;
				  
				if($i == $NoLineas){
				   $y = 10; 
				   $numPage++;
				   $i = 0;
				   
				   $pdf -> AddPage();
				   $pdf->SetDrawColor(60);
				   $pdf->SetFillColor(100);
				   $pdf->SetTextColor(250);
				   $pdf->SetFont("Arial","B",6);
				   $pdf->GetStringWidth(100);
			  	   $pdf->SetXY(8,8);
				   //Titulos de la tabla
				   $pdf->Cell(9,8,"FOLIO",1,0,"C",1);
				   $pdf->Cell(13,8,"CONCEPTO",1,0,"C",1);
				   $pdf->Cell(64,8,"NOMBRE CONCEPTO",1,0,"C",1);
				   $pdf->Cell(16,8,"REGISTRO",1,0,"C",1);
				   $pdf->Cell(16,8,"NIVEL",1,0,"C",1);
				   $pdf->Cell(20,8,"PERIODO",1,0,"C",1);
				   $pdf->Cell(20,8,"FECHA TRAMITE",1,0,"C",1);
				   $pdf->Cell(16,8,"COSTO",1,0,"C",1);
				   $pdf->Cell(16,8,"ESTATUS PAGO",1,0,"C",1);
				}
				
				$y +=5; 
				
				//Numeracion pagina
				$pdf->SetXY(188,268);
				$pdf->SetTextColor(1);
				$pdf->SetFont("Arial","",9);
				$pdf->Cell(25,8,$numPage,0,0,"C",0);
				
			  }
		
			  //$pdf->SetDrawColor(60);
			  //$pdf->SetFillColor(100);
			  //$pdf->SetTextColor(250);
			  //$pdf->SetFont("Arial","B",8);
			  //$pdf->GetStringWidth(100);
			  //$pdf->SetXY(119,$y);
			  //Titulos de la tabla
			  //$pdf->Cell(50,8,"TOTAL",1,0,"C",1);
			  //$pdf->Cell(25,8,"$ ".number_format($montoTotal,2),1,0,"C",1);

			  $pdf -> Output("public/files/pdfs/reporte_caja/Reporte_Folios.pdf","F"); 
		}
	
}	

?>