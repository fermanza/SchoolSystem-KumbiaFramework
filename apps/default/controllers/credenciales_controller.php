<?php
class credencialesController extends ApplicationController {

  //Funcion que muestra el index
  function index(){
	$this -> valida();  	  
  }
  
  //Funcion que muestra la pantalla para imprimir las credenciales
  function imprimirProvicional(){
    $this -> valida();     
  }
  
  //Funcion que muestra la pantalla para Actualizar Datos
  function actualizarDatos(){
    $this -> valida();
  }
  
  //Funcion que muestra la pantalla para crear el reporte
  function generarReporte(){
    $this -> valida();
	
	//Se vacian los registros de la tabla temporal
	$registrosTemp = new registrosTemp();
	$result = $registrosTemp -> truncate_registrosTemp('1');
	
  }
  
  //funcion que busca a un alumno por registro oara imprimir la credencial provisional
  function buscarAlumno(){	   
    $this -> set_response("view");
		   
	if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
	  //Mensaje de error
	  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
	  echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
	  $this -> result = "";
	}
			
	else{
	  $objeto = new alumnos(); 
	  $this -> result = $objeto -> buscar_registro_alumno($this -> post('registro'));
	  $this -> render_partial('muestraAlumnos');
	}
  }
	
	
  //Funcion que imprime en PDF la credencial provicional
  function imprimir_credencial_PDF($registro){
    $this -> set_response("view");
		  
	//Se obtienen los datos del alumno
	$objeto = new alumnos();
	$result = $objeto -> buscar_registro_alumno($registro);
		  
	foreach($result AS $value){
	  $registroAl = $value -> registro;
	  $nombre = $value -> nombres;
	  $apellidoP = $value -> a_paterno;
	  $apellidoM = $value -> a_materno;
	  $carrera = $value -> carrera;
	  $telefono = $value -> telefono;
	  $direccion = $value -> direccion;
	  $colonia = $value -> colonia;
	  $cp = $value -> cp;
	  $curp = $value -> curp;
	  $sangre = $value -> sangre;
	  
	  IF($value -> plantel == "C")
	    $plantel = "COLOMOS";
		
	  ELSE
	    $plantel = "TONALA";
	}
	
    //Se obtienen la fecha de vigencia
	$vigenciaCreden = new Credenciales();
	$fechaVigencia = $vigenciaCreden -> dias_restantes();
		  
	//Se inicia la creacion del PDF
	$pdf = new FPDF();
	$pdf -> Open();
	$pdf -> AddFont('Verdana','','verdana.php');
	$pdf -> AddPage();
		  
	//Se crea el frente de la credencial
	$pdf -> Image('public/img/CETI_n.jpg', 90,9,8,10);
	$pdf -> Image('public/img/SEP_logo.jpg', 7,3,35,30);
	$pdf -> Rect(5,7,97,65);
    //Valida si existe existe la imagen
	if(file_exists("/var/www/htdocs/calculo/ingenieria/public/img/fotos/".$registro.".jpg"))
	  $pdf -> Image('public/img/fotos/'.$registro.".jpg", 7,25,27,30);
  
	if(file_exists("/var/www/htdocs/calculo/ingenieria/public/img/fotos/".$registro.".JPG"))
	  $pdf -> Image('public/img/fotos/'.$registro.".JPG", 7,25,27,30);

	if(file_exists("/var/www/htdocs/calculo/ingenieria/public/img/fotos/".$registro.".jpg") == false && file_exists("/var/www/htdocs/calculo/ingenieria/public/img/fotos/".$registro.".JPG") == false)
	  $pdf -> Rect(7,25,27,30);
          

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(48,15);
	$pdf->Cell(22,2,"PLANTEL ".$plantel, 0, 0,"C");
    $pdf->SetFont('Arial', '', 7);
	$pdf->SetXY(33,30);
	$pdf->Cell(16, 3,"Registro : ", 0, 0,"R");
	$pdf->SetXY(48,30);
	$pdf->Cell(82, 3,$registroAl, 0, 0,"L");
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(28.5,38);
	$pdf->MultiCell(20, 3,"Alumno/a :",0,"R",0);
	$pdf->SetXY(48,38);
	$pdf->MultiCell(55, 3,strtoupper($nombre)." ".strtoupper($apellidoP)." ".strtoupper($apellidoM), 0,"L",0);
	$pdf->SetXY(33,46);
	$pdf->Cell(16, 3,"Carrera : ", 0, 0,"R");
	$pdf->SetXY(48,46); 
	$pdf->MultiCell(55,3,"ING. ".strtoupper($carrera),0,"L",0);
	$pdf->SetXY(8,62);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(90,3,"Nueva Escocia 1885 Col. Providencia 5ta. Sección, Guadalajara, Jalisco",0,"C",0);
	$pdf->SetXY(15,66);
	$pdf->MultiCell(50,3,"C.P. 44638    Tel. (01-33) 36413250",0,"C",0);
	$pdf->SetFont('Arial','B',6);
	$pdf->SetXY(25,66);
	$pdf->MultiCell(90,3,"SEP  14DTI0001D",0,"C",0);
		 
    //Se crea la parte de atras de la credencial
    $pdf -> Rect(5,72,97,65);
	$pdf->SetXY(5,72.5);
	$pdf->SetFont('Arial','B',5);
	$pdf->MultiCell(97,3,"Esta identificación es propiedad del CETI, es intransferible e invalida si presenta alteraciones. Su uso está sujeto a las normas del centro. La vigencia de la credencial es provisional de acuerdo a la fecha que indica la misma.",0,"C",0);
	$pdf->SetFont('Arial', '', 7);
	$pdf->SetXY(6,83);
	$pdf->Cell(16, 3,"Domicilio : ", 0, 0,"R");
	$pdf->SetXY(20,83);
	$pdf->Cell(82, 3,$direccion, 0, 0,"L");
	$pdf->SetXY(6,87);
	$pdf->Cell(16, 3,"Colonia : ", 0, 0,"R");
	$pdf->SetXY(20,87);
	$pdf->Cell(82, 3,$colonia, 0, 0,"L");
	$pdf->SetXY(6,91);
	$pdf->Cell(16, 3,"CP : ", 0, 0,"R");
	$pdf->SetXY(20,91);
	$pdf->Cell(82, 3,$cp, 0, 0,"L");
	$pdf->SetXY(6,95);
	$pdf->Cell(16, 3,"Teléfono : ", 0, 0,"R");
	$pdf->SetXY(20,95);
	$pdf->Cell(82, 3,$telefono, 0, 0,"L");
	//$pdf->SetXY(6,99);
	//$pdf->Cell(16, 3,"Tipo Sangre : ", 0, 0,"R");
	//$pdf->SetXY(20,99);
	//$pdf->Cell(82, 3,$sangre, 0, 0,"L");
	$pdf->SetXY(6,99);//103
	$pdf->Cell(16, 3,"CURP : ", 0, 0,"R");
	$pdf->SetXY(20,99);//103
	$pdf->Cell(82, 3,$curp, 0, 0,"L");
	$pdf->SetXY(64,91);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(38, 3,'Vigencia',0, 0,"C");
	$pdf->SetXY(65.5,96);
	$pdf->SetFont('Arial', '', 11);
	$pdf->Cell(35, 3,$fechaVigencia." - ".date("Y"), 0, 0,"C");
	$pdf->SetXY(5,105);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(97, 3,'P R O V I S I O N A L', 0, 0,"C");
	
	IF($plantel == "COLOMOS"){
	  $pdf -> line(25,128,82,128);
	  $pdf->SetXY(5,130);
	  $pdf->SetFont('Arial', '', 6);
	  $pdf->Cell(97, 3,"ING. SALVADOR TRINIDAD PÉREZ", 0, 0,"C");
	  $pdf->SetXY(5,133);
	  $pdf->Cell(97, 3,"JEFE DEL DEPTO. DE SERVICIOS DE APOYO ACADEMICO", 0, 0,"C");
    }
	ELSE{
	  $pdf -> line(25,128,82,128);
	  $pdf->SetXY(5,130);
	  $pdf->SetFont('Arial', '', 6);
	  $pdf->Cell(97, 3,"LIC. ANA MARSELA LÓPEZ ESTRADA", 0, 0,"C");
	  $pdf->SetXY(5,133);
	  $pdf->Cell(97, 3,"JEFA DEL DEPTO. DE SERVICIOS DE APOYO ACADÉMICO", 0, 0,"C");
	}

		  
	$pdf -> Output("public/files/pdfs/Credencial.pdf","F"); 
	$this->redirect("files/pdfs/Credencial.pdf");
		  
	$this -> render_partial('muestraAlumnos');
  }
	
	
  //funcion que busca a un alumno por registro oara imprimir la credencial provisional
  function buscarAlumnoInfo(){
    $this -> set_response("view");
		   
	if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
	  //Mensaje de error
	  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
	  echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
	  $this -> result = "";
	}
			
	else{
	  $objeto = new alumnos(); 
	  $this -> result = $objeto -> buscar_registro_alumno($this -> post('registro'));
	  $this -> registroB = $this -> post('registro');
	  $this -> render_partial('actualizarDatos');
	}
  }
  
  //Funcion que modifica los datos del alumno
  function updateDatosAlumno(){
    $this -> set_response("view");
	
	$Alumnos = new XalumnosPersonal();
	
	$Alumnos -> find_first('registro = '.$this -> post('registroAlumno'));
	$Alumnos -> domicilio = utf8_decode($this -> post('direccion','upper'));
	$Alumnos -> colonia = utf8_decode($this -> post('colonia','upper'));
	$Alumnos -> cp = utf8_decode($this -> post('cp','upper'));			
	$Alumnos -> curp = utf8_decode($this -> post('curp','upper'));
	$Alumnos -> telefono = $this -> post('telefono');
	$Alumnos -> telefono_otro = $this -> post('telOtro');
	$Alumnos -> celular = $this -> post('celular');
	$Alumnos -> sangre = $this -> post('sangre');
	$Alumnos -> hid = $this -> post('hid','upper');

	if($Alumnos -> update()){
	  //Funcion que guarda el log del formulario
	  $db = DbBase::raw_connect();
      $log = $db->query("INSERT INTO log_datosalumno (usuario,accion,alumno,fecha_accion) VALUES
			 ('".Session::get_data('registro')."','MODIFICACION DE DATOS - CREDENCIALES',".$this -> post('registroAlumno').",'".date('Y-m-d H:i:s')."')");	
			 
	  echo "<script type='text/javascript'> showDialog('MENSAJE','Tu Informaci&oacute;n ha sido actualizada correctamente','success',4); </script>";
	  $objeto = new alumnos(); 
	  $this -> result = $objeto -> buscar_registro_alumno($this -> post('registroAlumno'));
	  $this -> registroB = $this -> post('registroAlumno');
	}
	else{
	  echo "<script type='text/javascript'> showDialog('ALERTA','Tu Informaci&oacute;n no ha podido ser actualizada','warning',4); </script>";
	}
	
	$this -> render_partial('actualizarDatos');
  }
  
  
  //funcion que busca a un alumno por registro para generar un reporte
  function buscarAlumnoReporte(){	   
    $this -> set_response("view");
		   
	if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
	  //Mensaje de error
	  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
	  echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
	  $this -> result = "";
	  $this -> render_partial('generarReporte');
	}
			
	else{
	  $objeto = new alumnos(); 
	  $this -> result = $objeto -> buscar_registro_alumno($this -> post('registro'));
	  
	  if(count($this -> result) <= 0 || $this -> result == ""){
	    echo "<script type='text/javascript'> showDialog('ALERTA','No se encontro el registro ".$this -> post('registro')."','warning',4); </script>";
	    $this -> result = "";
		$this -> registroB = $this -> post('registro');
	    $this -> render_partial('generarReporte');
	  }
	  else{
	    $this -> registroB = $this -> post('registro');
	    $this -> render_partial('generarReporte');
	  }
	}
  }
  
  
  //Funcion que exporta el la lista (reporte) en excel
  function reporteExcel(){
  
    $this -> set_response("view");
	
	$this -> valida();
	
	$registrosTemp = new registrosTemp();

	$result = $registrosTemp -> get_registros('1');

	$this -> registros = $result ;

	$this -> render_partial('reporteExcel');
  }
  
  //Funcion que sirve para guardar o eliminar los reistros temporalmente en una tabla
  function agregar_registros(){
	$this -> set_response("view");
	
	$registrosTemp = new registrosTemp();
    //Se guardan los registros
	if($_GET['tipoAccion'] == 1){
	  $registrosTemp -> registro = $_GET['registro'];
	  $registrosTemp -> tipo = "1";
	  
	  $registrosTemp -> save();
	}
	
	//Se eliminan los registros
	if($_GET['tipoAccion'] == 2){  
      $db = DbBase::raw_connect();
	  $db->query("DELETE FROM registros_temp WHERE tipo = 1 AND registro = ".$_GET['registro']);
	}
	
	 $this -> render_partial('generarReporte');
  }
  
  
  function subirfotos(){
	
  }
  //funcion para subir varios archivos
  function enviarfotos(){
  		//validacion de input
	if(count($_FILES["fotos"]["name"]) == 0 ){
      Flash::error('Es necesario seleccionar por lo menos una imagen');
	}
	else{
		if(isset ($_FILES['fotos']['tmp_name'])) {
		
			$imagenes = count($_FILES["fotos"]["name"]);
			if( $imagenes < 12){
				for($i = 0;$i < $imagenes; $i++){
					//obtiene el nombre y la extencion del archivo para hacer las validaciones.
					$conteo = strlen($_FILES["fotos"]["name"][$i]);
					$conteo = $conteom - 3;
					$tipo = substr($_FILES["fotos"]["name"][$i],$conteo,3);
					$nombre = $_FILES["fotos"]["name"][$i];
					
					$nombreCorto = substr($_FILES['fotos']['name'][$i],1,$conteo);
					
					if(is_numeric($nombreCorto)){
						if($tipo != "jpg" & $tipo != "JPG" ){
							Flash::error('No se acepta el tipo de extencin '.$tipo.' del archivo '.$nombre);
						}else{
							$conteo2 = strlen($nombre);
							$conteo2 = $conteo2 - 3;
							//sube los archivos de imagen al servidor.
							if( move_uploaded_file($_FILES['fotos']['tmp_name'][$i],"/var/www/htdocs/calculo/ingenieria/public/img/fotos/".substr($nombre,0,$conteo2)."jpg") ){
								Flash::success('El archivo '.$_FILES["fotos"]["name"][$i].' a sido cargado');
							}else{
								Flash::error('El archivo '.$_FILES["fotos"]["name"][$i].' no ha sido posible cargarlo');
							}
						}
					}
					else{
						Flash::error('El nombre del archivo '.$_FILES["fotos"]["name"][$i].' es incorrecto, debe ser numerico');
					}
				}
			}
			else{
				Flash::error('No se pueden cargar los '.count($_FILES["fotos"]["name"]).' archivos seleccionados');
			}
			
		}
		else{
			Flash::error('fallo');
		}
	}
		$this -> render_partial('subirarchivos');
  }

  //funcion para subir varios archivos
  function enviarfirmas(){
	//validacion del input
  	if(count($_FILES["firmas"]["name"]) == 0 ){
      Flash::error('Es necesario seleccionar por lo menos una imagen');
	}
	else{
		if(isset ($_FILES['firmas']['tmp_name'])) {
		
			$imagenes = count($_FILES["firmas"]["name"]);
			if( $imagenes < 12){
				for($i = 0;$i < $imagenes; $i++){
					//obtiene el nombre y la extencion del archivo para realizar las validaciones.
					$conteo = strlen($_FILES["firmas"]["name"][$i]);
					$conteo = $conteom - 3;
					$tipo = substr($_FILES["firmas"]["name"][$i],$conteo,3);
					$nombre = $_FILES["firmas"]["name"][$i];
					
					$nombreCorto = substr($_FILES['firmas']['name'][$i],1,$conteo);
					
					if(is_numeric($nombreCorto)){
						if($tipo != "jpg" & $tipo != "JPG" ){
							Flash::error('No se acepta el tipo de extencin '.$tipo.' del archivo '.$nombre);
						}else{
							$conteo2 = strlen($nombre);
							$conteo2 = $conteo2 - 3;
							//Sube los archivos al servidor.
							if( move_uploaded_file($_FILES['firmas']['tmp_name'][$i],"/var/www/htdocs/calculo/ingenieria/public/img/firmas/".substr($nombre,0,$conteo2)."jpg") ){
								Flash::success('El archivo '.$_FILES["firmas"]["name"][$i].' a sido cargado');
							}else{
								Flash::error('El archivo '.$_FILES["firmas"]["name"][$i].' no ha sido posible cargarlo');
							}
						}
					}
					else{
						Flash::error('El nombre del archivo '.$_FILES["firmas"]["name"][$i].' es incorrecto, debe ser numerico');
					}
				}
			}
			else{
				Flash::error('No se pueden cargar los '.count($_FILES["firmas"]["name"]).' archivos seleccionados');
			}
			
		}
		else{
			Flash::error('fallo');
		}
	}
	
	$this -> render_partial('subirarchivos');
	
  }
   
  //Funcion que valida el acceso al modulo   
  function valida()
  {
	if(Session::get('registro') == "1861" || Session::get('registro') == "2551" || Session::get('registro') == "yael"  || Session::get('registro') == "2717" || Session::get('registro') == "2465" || Session::get('registro') == "2734" || Session::get('registro') == "2087"){
	  return true;
	}
		
	else{
      $this -> redirect("general/ingresar");
		return true;
	}
  }
  
	
}
 	
?>