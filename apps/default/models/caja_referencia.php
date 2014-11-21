<?php			
class CajaReferencia extends ActiveRecord {
		
    /*
	 * Funcion que sirve para crear la ficha de pago en formato PDF
	 * @param int $idReferencia array que contiene los id de la referencias que fueron guardados
	 * @param int $fechaLimite  fecha limite de pago en el formato day/month/year
	 * @param int $tipoPersona  Contiene el tipo de persona que solicita el concepto (Alumno, Aspirante, Trabajador, Otros)
	*/
	public function crear_fichaPago_PDF($idReferencia, $fechaLimite,$tipoPersona,$otherTypeP){
      			
	  //Imprime todas las fichas en un solo PDF
	  $pdf2 = new FPDF();
	  $pdf2 -> Open();
	 
	  $contador = 0;
	  $countFecha = 0;	
	  
	  foreach($idReferencia AS $value)
	  {
	    $result = Array();
	    $result = $this -> find_all_by_sql("SELECT t.costo, r.referencia, r.id AS idReferencia, c.nombre, c.id, t.registro, a.vcNomAlu, t.nombre_solicitante, t.actas
			                                FROM caja_referencia r
											LEFT JOIN caja_tramites t ON t.id = r.idTramite
											LEFT JOIN caja_conceptos c ON c.id = t.concepto
											LEFT JOIN alumnos a ON a.miReg = t.registro
											WHERE r.id = ".$value); 		
	
	    foreach($result AS $value2)
	    {

		  if($tipoPersona == 2 || ($tipoPersona == 4 && $otherTypeP == 1))
		    $materia = "";
			
		
		  else if($tipoPersona == 4 && $otherTypeP == 2){
		    $exAlumno = new Alumnosh();
			$nombreEA = $exAlumno -> find_first('reg ='.$value2 -> registro);
		    
		  }
		  
		  else
		  {
			  //Se obtiene el plantel del alumno que solicita los estraordinarios o titulos
			  $plantelAlumno = $this -> find_all_by_sql("SELECT enPlantel
												         FROM alumnos 
												         WHERE miReg = ".$value2 -> registro); 
													   
			  foreach($plantelAlumno AS $plantelIng)
				$plantel = $plantelIng -> enPlantel;
				
				//Obtiene ultimo periodo cursado y ultimo pagado
				$periodoPagado = new pagoPeriodo();
				$ultimoPago = $periodoPagado -> find_first('registro ='.$value2 -> registro);
			
				$PeriodoAct = new Periodos();
				//Periodo intersemestral activo
				$Periodo = $PeriodoAct -> find_first("activo = 1");	
				
				$periodoAnterior = $PeriodoAct -> periodoAnterior();
				
				//Validacion para poner el nombre del periodo segun corresponda el pago de la inscripcion
				if(($ultimoPago -> periodoUltimoPago == 99999 && $periodoAnterior == $ultimoPago -> periodoUltimoInscrito) || ($ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito)){
				  $Periodo = $PeriodoAct -> find_first("periodo = ".$periodoAnterior);
				  $periodoFicha = strtoupper($Periodo -> nombre);			  
				}
				
				else{
				  $periodoFicha = strtoupper(Session::get_data('nombre_periodo'));
				}
				
				//$periodoFicha = strtoupper(Session::get_data('nombre_periodo'));
				//Termina validacion
				
                //Obtiene estatus del alumno
				$Alumnos = new Alumnos();
				$alumnos = $Alumnos -> find_first('miReg ='.$value2 -> registro);
				
				if($alumnos -> pago == 0 && $alumnos -> condonado == 0 && $alumnos -> miPerIng == Session::get_data('periodo')){ 	
				  $PeriodoAPagarBD = "";
				}
				
				else
				{
				   if($ultimoPago -> periodoUltimoInscrito != null || $ultimoPago -> periodoUltimoInscrito != "")
				     $PeriodoAPagarBD = $PeriodoAct -> find_first("periodo = ".$ultimoPago -> periodoUltimoInscrito);
				  
				   else
				     $PeriodoAPagarBD = "";
				
					//Se obtiene el nombre de la materia que se pagara ya sea extraordinario, titulo, cursos o examanes globales
					$xextraordinarios	= new Xextraordinarios();
					//$cursoID = substr($value2 -> referencia, 21,6); 
					/*if(Session::get('registro') == 17009)
					  $nombreMateria = $xextraordinarios -> get_extrasTitulos_PDF(Session::get('registro'),12007,$plantel,$value2 -> actas);
					 
					else*/
					  $nombreMateria = $xextraordinarios -> get_extrasTitulos_PDF($value2 -> registro,Session::get_data('periodo'),$plantel,$value2 -> actas);
				
					//Periodo Intersemestral
					$periodoInter = (Session::get_data('periodo')) + (10000);
					$nombreCursos = $xextraordinarios -> get_cursos_NIV_ACR_PDF($value2 -> registro,$periodoInter,$value2 -> actas);
					
					//Derechos de pasante
					if($value2 -> id == 54)
				      $nombreDerechoPas = $xextraordinarios -> get_derechoPasante_PDF($value2 -> actas);
					  
					else
					  $nombreDerechoPas = "";
					
					$nombreExamGlobal = $xextraordinarios -> get_Exam_Global_PDF($value2 -> registro,$periodoInter,$value2 -> actas);
					
					if(count($nombreMateria) > 0){
					  foreach($nombreMateria AS $value)
						$materia = $value -> nombre;
					}
					
					if(count($nombreCursos) > 0){
					  foreach($nombreCursos AS $valueC)
						$materiaCurso = $valueC -> nombre;
					}
					
					if(count($nombreExamGlobal) > 0){
					  foreach($nombreExamGlobal AS $valueG)
						$materiaGlobal = $valueG -> nombre;
					}
					 
					if(count($nombreDerechoPas) > 0){
					  foreach($nombreDerechoPas AS $valueDP)
						$materiaDP = $valueDP -> nombre;
					}
				
				}
		  }
		  
		  //Inicia construccion del PDF
		  $pdf = new FPDF();
		  $pdf -> Open();

		  $pdf -> AddPage();	
    	  $pdf -> Image('public/img/formatoFichaPagoLinea.JPG', 5, 20, 200, 220);
			
		  //COPIA CLIENTE
		  $pdf -> SetXY(110,35);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
		  $pdf -> SetXY(10,41);
		  $pdf -> SetFont('Arial','',7);
		  
          if($tipoPersona == 2 || ($tipoPersona == 4 && $otherTypeP == 1))		  
            $pdf -> MultiCell(80,3,strtoupper($value2 -> nombre_solicitante),0,'L',0);    
          
		  else if($tipoPersona == 4 && $otherTypeP == 2)
            $pdf -> MultiCell(80,3,$value2 -> registro." - ".strtoupper($nombreEA -> nombre),0,'L',0);
			
		  else
            $pdf -> MultiCell(80,3,$value2 -> registro." - ".$value2 -> vcNomAlu,0,'L',0);
		  
		  $pdf -> SetXY(110,44);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,$value2 -> referencia,0,'L',0);                
		  $pdf -> SetXY(20,52);
          $pdf -> MultiCell(90,4,$fechaLimite[$countFecha],0,'L',0);                
		  $pdf -> SetFont('Arial','',7);				            
          $pdf -> ln(8);
		  if(($value2 -> id == 4 || $value2 -> id == 6) && ($alumnos -> stSit == "BD" || $alumnos -> stSit == "OK") && $ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".strtoupper($PeriodoAPagarBD -> nombre),0,'L',0);  
		  }	

		  else if(($value2 -> id == 4 || $value2 -> id == 6 || $value2 -> id == 5 || $value2 -> id == 102) && $alumnos -> stSit == "OK" ){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".strtoupper(Session::get_data('nombre_periodo')),0,'L',0);  
		  }		
		  
		  else if($value2 -> id == 3  || $value2 -> id == 102){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".$periodoFicha,0,'L',0);  
		  }			  
			
		  else
		     $pdf -> MultiCell(60,4,$value2 -> nombre,0,'L',0);
			 
          if($value2 -> id == 51 || $value2 -> id == 58)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materia,0,'L',0);   
          }		
		  
          if($value2 -> id == 95 || $value2 -> id == 94)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaCurso,0,'L',0);   
          }		
		  
          if($value2 -> id == 106)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaGlobal,0,'L',0);   
          }	
		  
          if($value2 -> id == 54)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaDP,0,'L',0);    
          }		

		  if($value2 -> id == 3 || $value2 -> id == 4){
			$pdf -> SetXY(8,118);
			$pdf -> SetFont('Arial','B',8);
			$pdf -> MultiCell(192,4,'NOTA IMPORTANTE: EL PAGO DEL PRESENTE SEMESTRE NO EXIME LOS COMPROMISOS DE LOS PAGOS ANTERIORES.',0,'J',0);			
            $pdf -> SetFont('Arial','',7);		 
		 }
		 
		  $pdf -> SetXY(78,65);
		  $pdf -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0); 
             
		  $pdf -> SetXY(78,76);
          $pdf -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);                
		  $pdf -> SetXY(92,100);
          $pdf -> MultiCell(60,4,"BANCO",0,'L',0);   
		  
		  $pdf -> SetXY(8,128);
		  $pdf -> SetFont('Arial','BU',8);
		  $pdf -> MultiCell(185,4,'VERIFICA QUE LA INFORMACIÓN SEA CORRECTA ANTES DE EFECTUAR TU PAGO YA QUE NO SE APLICARAN REEMBOLSOS',0,'J',0);
		  
		  //COPIA BANCO
		  $pdf -> SetXY(110,155);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
		  $pdf -> SetXY(10,160);
		  $pdf -> SetFont('Arial','',7);		
          if($tipoPersona == 2 || ($tipoPersona == 4 && $otherTypeP == 1))			  
            $pdf -> MultiCell(80,3,strtoupper($value2 -> nombre_solicitante),0,'L',0);   

		  else if($tipoPersona == 4 && $otherTypeP == 2)
            $pdf -> MultiCell(80,3,$value2 -> registro." - ".strtoupper($nombreEA -> nombre),0,'L',0);
						
		 else
            $pdf -> MultiCell(80,3,$value2 -> registro." - ".$value2 -> vcNomAlu,0,'L',0);
			
		  $pdf -> SetXY(110,163);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,$value2 -> referencia,0,'L',0);                
		  $pdf -> SetXY(20,170);
          $pdf -> MultiCell(90,4,$fechaLimite[$countFecha],0,'L',0);                
		  $pdf -> SetFont('Arial','',7);				            
          $pdf -> ln(8);
		  if(($value2 -> id == 4 || $value2 -> id == 6) && ($alumnos -> stSit == "BD" || $alumnos -> stSit == "OK") && $ultimoPago -> periodoUltimoPago != $ultimoPago -> periodoUltimoInscrito){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".strtoupper($PeriodoAPagarBD -> nombre),0,'L',0);  
		  }	

		  else if(($value2 -> id == 4 || $value2 -> id == 6  || $value2 -> id == 5 || $value2 -> id == 102) && $alumnos -> stSit == "OK" ){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".strtoupper(Session::get_data('nombre_periodo')),0,'L',0);  
		  }		
		  
		  else if($value2 -> id == 3  || $value2 -> id == 102){
            $pdf -> MultiCell(60,4,$value2 -> nombre." - ".$periodoFicha,0,'L',0);  
		  }			  
			
		  else
		     $pdf -> MultiCell(60,4,$value2 -> nombre,0,'L',0);
			 
		  if($value2 -> id == 51 || $value2 -> id == 58)
          {
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materia,0,'L',0);  
          }

          if($value2 -> id == 95 || $value2 -> id == 94)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaCurso,0,'L',0);   
          }		

          if($value2 -> id == 106)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaGlobal,0,'L',0);   
          }		
		  
          if($value2 -> id == 54)
          {		  
            $pdf -> ln(2);
            $pdf -> MultiCell(60,4,$materiaDP,0,'L',0);    
          }		
		  
		  if($value2 -> id == 3 || $value2 -> id == 4){
			$pdf -> SetXY(8,250);
			$pdf -> SetFont('Arial','B',8);
			$pdf -> MultiCell(192,4,'NOTA IMPORTANTE: EL PAGO DEL PRESENTE SEMESTRE NO EXIME LOS COMPROMISOS DE LOS PAGOS ANTERIORES.',0,'J',0);			
            $pdf -> SetFont('Arial','',7);
		  }
		  
		    $pdf -> SetXY(78,183);
		    $pdf -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0); 
		  
		  
		  /*$pdf -> SetXY(78,183);
          $pdf -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);  */              
		  $pdf -> SetXY(78,195);
          $pdf -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);                
		  $pdf -> SetXY(92,219);
          $pdf -> MultiCell(60,4,"CLIENTE",0,'L',0);    
		  
		  $pdf -> SetXY(8,262);
		  $pdf -> SetFont('Arial','BU',8);
		  $pdf -> MultiCell(192,4,'VERIFICA QUE LA INFORMACIÓN SEA CORRECTA ANTES DE EFECTUAR TU PAGO YA QUE NO SE APLICARAN REEMBOLSOS',0,'J',0);
		  		  
		  $pdf -> Output("public/files/pdfs/caja/Referencia_".$value2 -> referencia.".pdf"); 

		  $filePDF[] = "public/files/pdfs/caja/Referencia_".$value2 -> referencia.".pdf"; 
	
	      //Se crean todas las fichas en un solo PDF
		 if(count($idReferencia) > $contador )
		  {
		      $pdf2 -> AddPage();
		  }
			  //$pdf2 -> Image('C:/Archivos de programa/VertrigoServ/www/ControlEscolar/tecnologo/public/img/formato_ficha.jpg', 5, 8);
			  $pdf2 -> Image('public/img/formatoFichaPagoLinea.JPG', 5, 20, 200, 220);
			  
			  //COPIA CLIENTE
			  $pdf2 -> SetXY(110,35);
			  $pdf2 -> SetFont('Arial','',10);				            
			  $pdf2 -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
			  $pdf2 -> SetXY(10,41);
			  $pdf2 -> SetFont('Arial','',7);
			  if($tipoPersona == 2 || ($tipoPersona == 4 && $otherTypeP == 1))		  
				$pdf2 -> MultiCell(80,3,strtoupper($value2 -> nombre_solicitante),0,'L',0);    
			  
			  else
				$pdf2 -> MultiCell(80,3,$value2 -> registro." - ".$value2 -> vcNomAlu,0,'L',0);
			  
			  $pdf2 -> SetXY(110,44);
			  $pdf2 -> SetFont('Arial','',10);				            
			  $pdf2 -> MultiCell(90,4,$value2 -> referencia,0,'L',0);                
			  $pdf2 -> SetXY(20,52);
			  $pdf2 -> MultiCell(90,4,$fechaLimite[$countFecha],0,'L',0);                
			  $pdf2 -> SetFont('Arial','',7);				            
			  $pdf2 -> ln(8);
			  $pdf2 -> MultiCell(60,4,$value2 -> nombre,0,'L',0); 
              if($value2 -> id == 51 || $value2 -> id == 58)
              {			  
                $pdf2 -> ln(2);
                $pdf2 -> MultiCell(60,4,$materia,0,'L',0);   
              }		

			  if($value2 -> id == 95 || $value2 -> id == 94)
			  {		  
				$pdf2 -> ln(2);
				$pdf2 -> MultiCell(60,4,$materiaCurso,0,'L',0);   
			  }		
			  
			  if($value2 -> id == 54)
			  {		  
				$pdf -> ln(2);
				$pdf -> MultiCell(60,4,$materiaDP,0,'L',0);    
			  }		

			  
			  if($value2 -> id == 3 || $value2 -> id == 4){
			    $pdf2 -> SetXY(8,118);
				$pdf2 -> SetFont('Arial','B',8);
				$pdf2 -> MultiCell(192,4,'NOTA IMPORTANTE: EL PAGO DEL PRESENTE SEMESTRE NO EXIME LOS COMPROMISOS DE LOS PAGOS ANTERIORES.',0,'J',0);			
				$pdf2 -> SetFont('Arial','',7);		 
			 }
			 
			  $pdf2 -> SetXY(78,65);
			  $pdf2 -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0); 
           
			  $pdf2 -> SetXY(78,76);
			  $pdf2 -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);                
			  $pdf2 -> SetXY(92,100);
			  $pdf2 -> MultiCell(60,4,"BANCO",0,'L',0);   
			  
			  $pdf2 -> SetXY(8,128);
			  $pdf2 -> SetFont('Arial','BU',8);
			  $pdf2 -> MultiCell(185,4,'VERIFICA QUE LA INFORMACIÓN SEA CORRECTA ANTES DE EFECTUAR TU PAGO YA QUE NO SE APLICARAN REEMBOLSOS',0,'J',0);
		  
			  //COPIA BANCO
			  $pdf2 -> SetXY(110,155);
			  $pdf2 -> SetFont('Arial','',10);				            
			  $pdf2 -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
			  $pdf2 -> SetXY(10,160);
			  $pdf2 -> SetFont('Arial','',7);		
			  if($tipoPersona == 2 || ($tipoPersona == 4 && $otherTypeP == 1))			  
				$pdf2 -> MultiCell(80,3,strtoupper($value2 -> nombre_solicitante),0,'L',0);   
			  else
				$pdf2 -> MultiCell(80,3,$value2 -> registro." - ".$value2 -> vcNomAlu,0,'L',0);
				
			  $pdf2 -> SetXY(110,163);
			  $pdf2 -> SetFont('Arial','',10);				            
			  $pdf2 -> MultiCell(90,4,$value2 -> referencia,0,'L',0);                
			  $pdf2 -> SetXY(20,170);
			  $pdf2 -> MultiCell(90,4,$fechaLimite[$countFecha],0,'L',0);                
			  $pdf2 -> SetFont('Arial','',7);				            
			  $pdf2 -> ln(8);
			  $pdf2 -> MultiCell(60,4,$value2 -> nombre,0,'L',0);
			  if($value2 -> id == 51 || $value2 -> id == 58)
              {
                $pdf2 -> ln(2);
                $pdf2 -> MultiCell(60,4,$materia,0,'L',0); 
              }		

			  if($value2 -> id == 95 || $value2 -> id == 94)
			  {		  
				$pdf2 -> ln(2);
				$pdf2 -> MultiCell(60,4,$materiaCurso,0,'L',0);   
			  }		
			  
			  if($value2 -> id == 54)
			  {		  
				$pdf -> ln(2);
				$pdf -> MultiCell(60,4,$materiaDP,0,'L',0);    
			  }		

			  
			  if($value2 -> id == 3 || $value2 -> id == 4){
				$pdf2 -> SetXY(8,250);
				$pdf2 -> SetFont('Arial','B',8);
				$pdf2 -> MultiCell(192,4,'NOTA IMPORTANTE: EL PAGO DEL PRESENTE SEMESTRE NO EXIME LOS COMPROMISOS DE LOS PAGOS ANTERIORES.',0,'J',0);			
				$pdf2 -> SetFont('Arial','',7);
			  }
			  
			  $pdf2 -> SetXY(78,183);
			  $pdf2 -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0); 
		  
			  /*$pdf2 -> SetXY(78,183);
			  $pdf2 -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);   */             
			  $pdf2 -> SetXY(78,195);
			  $pdf2 -> MultiCell(60,4,"$".$value2 -> costo,0,'L',0);                
			  $pdf2 -> SetXY(92,219);
			  $pdf2 -> MultiCell(60,4,"CLIENTE",0,'L',0);    
			  
			  $pdf2 -> SetXY(8,262);
			  $pdf2 -> SetFont('Arial','BU',8);
			  $pdf2 -> MultiCell(192,4,'VERIFICA QUE LA INFORMACIÓN SEA CORRECTA ANTES DE EFECTUAR TU PAGO YA QUE NO SE APLICARAN REEMBOLSOS',0,'J',0);
		  
		   $countFecha++;
		}
		
	  }	
	      //PDF con todas las fichas
	  	  //$pdf2 -> Output("public/files/pdfs/caja/ficha_".$value2 -> idReferencia.$contador.".pdf"); 
		  //$filePDFAll = "public/files/pdfs/caja/ficha_".$value2 -> idReferencia.$contador.".pdf";
		  
		  ++$contador; 
	//} quiitar si se descomenta el demas codigo
	
	  if($filePDF != null)
	  { 
	    $tabla = "<table style='width:100%; margin-top:50px; font-size:13px;'>
	              <tr style='background-color: #13539B;  color: #FFFFFF;  font-weight: bold; text-align: center;'>
				    <td>
					  No. FICHA
					</td>
					<td>
					  IMPRIMIR
					</td>
				  </tr>";
	    
		$x = 1;
		
	    for($i = 0; $i < count($filePDF); $i++)
	    {
		  
		  if($i%2 == 0)
		    $color = "background-color: #DDDBDB;";
		
		  else
		    $color = "background-color: #FFFFFF;";
			
		  $tabla .= '<tr height="30" style="'.$color.'">
		              <td align="center">
					    '.$x.'
					  </td>
					  <td align="center">
					    <a style="color:#000000;" href="'.KUMBIA_PATH.$filePDF[$i].'"> Imprimir Ficha&nbsp;'.$x.'</a>
					  </td>
		            </tr>';
			++$x;
	    }
		
	   /* $tabla .= "<tr>
		             <td colspan='2' align='right'>
					   <br><br>
					   <a style='color:#000000;' href='".KUMBIA_PATH.$filePDFAll."'> Imprimir todas las fichas</a>
					 </td>
		           </tr>
		          </table>";*/
	
		echo $tabla;
	  }

	}
	
  //} quiitar si se descomenta el demas codigo
}	

?>