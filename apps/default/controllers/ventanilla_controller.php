<?php
			
	class VentanillaController extends ApplicationController {
		
		public $anterior	= 12010;
		public $actual		= 32010;
		public $proximo		= 12011;
		
		function index(){
			
		}

		function cedencialProvicional(){
 
		}
		
		function aspirantes(){
			$Periodos = new Periodos();
			$this->anterior	= $Periodos->get_periodo_anterior();
			$this->actual = $Periodos->get_periodo_actual();
			$this->proximo = $Periodos->get_periodo_proximo();
		}
		
		function documentacion(){
		  
		}
		
		function calificaciones(){
			
		}
		
		function calificacionesPDF(){
			
		}
		
		//Funcion que sirve para reactiar el formulario de actualizacion de datos del alumno
		function reactivarActualizacion(){
		  $this -> valida(); 
		}
		
		function comentariosAlumnos(){
		  $this -> validaComentarios();     
		}
		
		function calificacionesEXEL(){
			$this -> Alumno = new Alumnos();						
			$Calificaciones = new Calificaciones();
			
			$registro = $this -> post('registro');
			$this -> periodo = $periodo = $this -> post('periodo');
			
			if($this -> Alumno -> find_first('registro = '.$registro)){
				if($this -> calificaciones = $Calificaciones -> find("periodo = ".$periodo." and registro = ".$registro)){
					$this -> set_response('view');
					$this -> render_partial('calificaciones');
				}else{
					Flash::error('<h2>No se encontraron calificaciones para el registro: '.$registro.', y el periodo: '.$periodo.'</h2>');
				}				
			}else{				
				Flash::error('<h2>No se encontro el alumnos</h2>');
			}
			
			
		}
		
		function checarEstado(){
		
		}
		
		function checarEstado2(){
			
			$periodo = 32009;
			
			$alumnos = new Alumnos();						
			$xalumnocursos = new Xalumnocursos();
			
			$registro = $this -> post("registro");
			
//			$alumno = $alumnos -> find_first( "miReg = ".$registro );
			
			if ( $alumnos -> find_first( "miReg = ".$registro ) )
				echo "<br />El Alumno está inscrito en los siguientes cursos ";
			else{
				echo "No se encontro el alumno: ".$registro;
				exit(1);
			}
			
			foreach ( $xalumnocursos -> find ( "registro = ".$registro." 
					and periodo = ".$periodo ) as $xalumncursos ){
				echo $xalumncursos -> curso."<br />";
			}
		}
		
		function checarContra(){
			
			if ( Session::get_data('tipousuario') != "VENTANILLA" ){
				$this -> redirect("general/inicio");
			}
		} //checarContra
		
		function checarContra2(){
			
			if ( Session::get_data('tipousuario') != "VENTANILLA" ){
				$this -> redirect("general/inicio");
			}
			
			$registro = $this -> post("registro");
			$usuarios	= new Usuarios();
			$semilla	= new Semilla();
			
			$this -> usuarioo = null;
			
			foreach( $usuarios -> find_all_by_sql( 
				"select AES_DECRYPT(clave,'".$semilla -> getSemilla()."') as clave, registro
				from usuarios
				where registro = '".$registro."'
				and categoria = 1" ) as $usuario ){
				$this -> usuarioo = $usuario;
			}
		} // checarContra2

        function resetearcontrasena(){

            $usuarios	= new Usuarios();
            $alumnos	= new Alumnos();

            if(Session::get_data('tipousuario')!="VENTANILLA"){
                    $this->redirect('general/inicio');
            }
            $id = $this -> post("registro");
			
            // Eliminar las variables que van a pertenecer a este método.
            unset( $this -> usuario );
            unset( $this -> exito );

            $this -> exito = 0;
			$semilla = new Semilla();
            foreach( $usuarios -> find_all_by_sql( "
					update usuarios
					set clave = AES_ENCRYPT('".$id."','".$semilla -> getSemilla()."')
					where registro = '".$id."'" ) as $usuario ){
				$this -> exito = 1;
				break;
            }
			$semilla = new Semilla();
			foreach( $usuarios -> find_all_by_sql( 
					"select AES_DECRYPT(clave,'".$semilla -> getSemilla()."') as clave
					from usuarios
					where registro = '".$id."'" ) as $usuario ){
				$this -> usuario = $usuario;
			}
			
			$this -> redirect("ventanilla/checarContra");
        } // function resetearcontrasena()
		
		
		
		//Funcion que modifica el estatus del comentario en caso de marcarse como leido
        function updateStatusComentario(){
		  $this -> set_response("view");
		  
		  $comentarios = new comentariosActualizacionDatos();
		  
		  $comentarios -> find_first('idComentario = '.$_GET['comentario']);
		  $comentarios -> statusLeido = "1";
		  $comentarios -> nomina = Session::get('registro');
		  $comentarios -> fecha_leido = date("Y-m-d H:i:s");
		  
		  $comentarios -> update();
		  
          $this -> render_partial('comentariosAlumnos');
		}	

		//Funcion que busca un aspirante por su registro
		function buscarAspirante(){

		  $this -> set_response('view');
		  
		  if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
		    //Se crea el mensaje de error
			echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
			echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
				$this -> result = "";
		  }
		  else{
		    $aspirante = new Aspirantes();
		    $this -> result = $aspirante -> get_aspirante($this -> post('registro'));
			
			if(count($this -> result) == 0 || $this -> result == ""){
			  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
			  echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="No se encontro el registro" maxlength="0"/>';
			}
          }
		  
		 $this -> render_partial('muestraAspirante');
		}		
		
		//Funcion que guarda los documentos de aspirante entregados
		function guardarDocs(){
		  if($this->post('acta') == "on") 
		    $acta = "si";
		  else
		    $acta = "no";
			
		  if($this->post('certificadoo') == "on") 
		    $certificadoo = "si";
		  else
		    $certificadoo = "no";
			
		  if($this->post('ficha_pago') == "on") 
		    $ficha_pago = "si";
		  else
		    $ficha_pago = "no";
			
		  if($this->post('certificadom') == "on") 
		    $certificadom = "si";
		  else
		    $certificadom = "no";
			
		  if($this->post('facultativo') == "on") 
		    $carta_compromiso = "si";
		  else
		    $carta_compromiso = "no";
			
		  if($this->post('reglamento') == "on") 
		    $reglamento = "si";
		  else
		    $reglamento = "no";
			
		  if($this->post('antidoping') == "on") 
		    $antidoping = "si";
		  else
		    $antidoping = "no";
			
		  if($this->post('extranjero') == "on") 
		    $cuota_extranjero = "si";
		  else
		    $cuota_extranjero = "no";

			
          if($this->post('prorroga') == "on" && $this->post('fechaProrroga') == ""){
             Flash::error('Es necesario ingresar la fecha de prorroga');
		  }		
          else if($this->post('prorroga') == "on" && $this->post('motivosProrroga') == ""){
             Flash::error('Es necesario ingresar el motivo de prorroga');
		  }			  
		  else if(($this->post('acta') == "on" && $this->post('certificadoo') == "on" && $this->post('ficha_pago') == "on" && $this->post('certificadom') == "on" && $this->post('facultativo') == "on" && $this->post('reglamento') == "on" && $this->post('antidoping') == "on" ) || ($this->post('prorroga') == "on" && $this->post('fechaProrroga') != "" && $this->post('motivosProrroga') != "" && ($this->post('actaPro') != "" || $this->post('certificadoPro') != "" || $this->post('fichaPagoPro') != "" || $this->post('certificadoMedPro') != "" || $this->post('cartaComPro') != "" || $this->post('reglamentoPro') != "" || $this->post('antidopingPro') != "" || $this->post('extranjeroPro') != "") )){
		  $Objeto = new AspirantesDocumentacion();

		  if($this->post('prorroga') == "on")
		    $prorrogaD = $this->post('actaPro').$this->post('certificadoPro').$this->post('fichaPagoPro').$this->post('certificadoMedPro').$this->post('cartaComPro').$this->post('reglamentoPro').$this->post('antidopingPro').$this->post('extranjeroPro');
		  
		  else
		    $prorrogaD = "";
			
		  if($Objeto -> exists('aspirante_id = '.$this->post('aspirante'))){
		  
		   if($Objeto -> find_first('aspirante_id = '.$this->post('aspirante'))){
            $Objeto-> acta = $acta;		  
            $Objeto-> certificadoo = $certificadoo;		  
            $Objeto-> ficha_pago = $ficha_pago;		  
            $Objeto-> certificadom = $certificadom;		  
            $Objeto-> carta_compromiso = $carta_compromiso;		  
            $Objeto-> reglamento = $reglamento;		  
            $Objeto-> antidoping = $antidoping;		  
            $Objeto-> cuota_extranjero = $cuota_extranjero;		  
            $Objeto-> prorroga = $prorrogaD;
            $Objeto-> fechaProrroga = $this->post('fechaProrroga');	  
            $Objeto-> motivoProrroga = $this->post('motivosProrroga');				
            
			if($Objeto -> update())
              Flash::success('Tu Informaci&oacute;n ha sido guardada correctamente');
            
			else
              Flash::error('Tu Informaci&oacute;n no ha podido ser guardada');
		   }
		   
          }
		  else{
            $Objeto-> aspirante_id = $this->post('aspirante');		  
            $Objeto-> acta = $acta;		  
            $Objeto-> certificadoo = $certificadoo;		  
            $Objeto-> ficha_pago = $ficha_pago;		  
            $Objeto-> certificadom = $certificadom;		  
            $Objeto-> carta_compromiso = $carta_compromiso;		  
            $Objeto-> reglamento = $reglamento;		  
            $Objeto-> antidoping = $antidoping;		  
            $Objeto-> cuota_extranjero = $cuota_extranjero;		  
            $Objeto-> prorroga = $prorrogaD;	 
            $Objeto-> fechaProrroga = $this->post('fechaProrroga');	  
            $Objeto-> motivoProrroga = $this->post('motivosProrroga');				
            
			if($Objeto -> save())
              Flash::success('Tu Informaci&oacute;n ha sido guardada correctamente');
            
			else
              Flash::error('Tu Informaci&oacute;n no ha podido ser guardada');
          }
		
		 }
		 else{
		   Flash::error('Hace falta documentacion por entregar. Los documentos marcados con (*) son obligatorios');
		 }
		  
          die();
		}
		
		function fichaPDF($idAspirante){
          $aspirante = new Aspirantes();
		  $result = $aspirante -> get_aspirante_Fichas($idAspirante);

		  foreach($result AS $value){
		    $ficha = $value->ficha;
		    $registro = $value->registro;
			$periodo = $value->periodo;
			$nombreAspirante = $value -> nombre." ".$value -> paterno." ".$value -> materno;
			$carrera = $value -> carrera;
			
			//SE VALIDA DOCUMENTOS
		  if($value -> acta == "si") 
		    $acta = "X";
		  else
		    $acta = "";
			
		  if($value -> certificadoo == "si") 
		    $certificadoo = "X";
		  else
		    $certificadoo = "";
			
		  if($value -> ficha_pago == "si") 
		    $ficha_pago = "X";
		  else
		    $ficha_pago = "";
			
		  if($value -> certificadom == "si") 
		    $certificadom = "X";
		  else
		    $certificadom = "";
			
		  if($value -> carta_compromiso == "si") 
		    $carta_compromiso = "X";
		  else
		    $carta_compromiso = "";
			
		  if($value -> reglamento == "si") 
		    $reglamento = "X";
		  else
		    $reglamento = "";
			
		  if($value -> antidoping == "si") 
		    $antidoping = "X";
		  else
		    $antidoping = "";
			
		  if($value -> cuota_extranjero == "si") 
		    $cuota_extranjero = "X";
		  else
		    $cuota_extranjero = "";
			
	     $prorroga = $value -> prorroga;
		 
		 if($prorroga != null || $prorroga != ""){
		  $prorrogaX = "X";
		  
		  $check = split(',', $prorroga);
		  
		  if($check[0] == "actaPro" )
		    $prorroga1 = "Acta Nacimiento (original)";
			
		  else if($check[0] == "certificadoPro" )
		    $prorroga1 = "Certificado Estudios (original y copia)";
			
		  else if($check[0] == "fichaPagoPro" )
		    $prorroga1 = "Pago De Inscripción (copia)";
			
		  else if($check[0] == "certificadoMedPro" )
		    $prorroga1 = "Certificado Médico (original)";
			
		  else if($check[0] == "cartaComPro" )
		    $prorroga1 = "Carta Compromiso (seguro facultativo)";
			
		  else if($check[0] == "reglamentoPro" )
		    $prorroga1 = "Carta de cumplimiento del reglamento";
			
		  else if($check[0] == "antidopingPro" )
		    $prorroga1 = "Resultado del examen de detección de uso de drogas";
			
		  else if($check[0] == "extranjeroPro" )
		    $prorroga1 = "Cuota de Inscripción Alumno Extranjero";
			
		  else
		    $prorroga1 = "";
			
		  if($check[1] == "actaPro" )
		    $prorroga2 = "Acta Nacimiento (original)";
			
		  else if($check[1] == "certificadoPro" )
		    $prorroga2 = "Certificado Estudios (original y copia)";
			
		  else if($check[1] == "fichaPagoPro" )
		    $prorroga2 = "Pago De Inscripción (copia)";
			
		  else if($check[1] == "certificadoMedPro" )
		    $prorroga2 = "Certificado Médico (original)";
			
		  else if($check[1] == "cartaComPro" )
		    $prorroga2 = "Carta Compromiso (seguro facultativo)";
			
		  else if($check[1] == "reglamentoPro" )
		    $prorroga2 = "Carta de cumplimiento del reglamento";
			
		  else if($check[1] == "antidopingPro" )
		    $prorroga2 = "Resultado del examen de detección de uso de drogas";
			
		  else if($check[1] == "extranjeroPro" )
		    $prorroga2 = "Cuota de Inscripción Alumno Extranjero";
			
		  else
		    $prorroga2 = "";
		  }
		  
		 else{
		   $prorrogaX = "";
		   $prorroga1 = "";
		   $prorroga2 = "";
		 }
			
			
			if($value -> plantel == "C")
			  $plantel = "Colomos";
			  
			else
			  $plantel = "Tonala";
		  }
	
		  //Se inicia el PDF
		  $pdf = new FPDF();
		  $pdf -> Open();
		  $pdf -> AddFont('Verdana','','verdana.php');
		  $pdf -> AddPage();
		  
          //Se crea el encabezado del comprobante del pago
		  $pdf->Image('public/img/CETI_n.jpg', 13,7,25,33);
		  $pdf->SetX(18);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(0,3,"CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL",0,'C',0);
		  $pdf->SetXY(18,16);
		  $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(0, 3, "Organismo Público  Federal Descentralizado", 0, 'C', 0);
		  $pdf->SetXY(0,25);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(0, 3, "Departamento de Servicio de Apoyo Académico", 0, 'C', 0);
		  $pdf->SetXY(120,25);
		  $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(0, 3, "Plantel Colomos", 0, 'C', 0);
		  $pdf->SetXY(18,38);
		  $pdf->SetFont('Arial', 'B', 14);
		  $pdf->MultiCell(0, 3, "RECIBO DE DOCUMENTACIÓN DE NUEVO INGRESO", 0, 'C', 0);
		  
		  $pdf->SetXY(7,55);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(15, 3, "Ficha:", 0, 'R', 0);
		  $pdf->SetXY(23,55);
		  $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(30, 3,$ficha, 0, 'L', 0);
		  $pdf->line(23,58,58,58);
		  $pdf->SetXY(63,55);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(20, 3, "Registro:", 0, 'R', 0);
		  $pdf->SetXY(83,55);
	      $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(30, 3,"   ".$registro, 0, 'L', 0);
		  $pdf->line(83,58,120,58);
		  $pdf->SetXY(125,55);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(20, 3, "Periodo:", 0, 'R', 0);
		  $pdf->SetXY(142,55);
	      $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(80, 3,"   ".$periodo, 0, 'L', 0);
		  $pdf->line(145,58,192,58);
		
		  $pdf->SetXY(7,65);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(20, 3, "Nombre:", 0, 'L', 0);
		  $pdf->SetXY(25,65);
	      $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(170, 3,$nombreAspirante, 0, 'L', 0);
		  $pdf->line(25,68,192,68);
		  
		  $pdf->SetXY(7,75);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(15, 3, "Nivel:", 0, 'R', 0);
		  $pdf->SetXY(23,75);
		  $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(30, 3,'Ingenieria', 0, 'L', 0);
		  $pdf->line(23,78,60,78);
		  $pdf->SetXY(63,75);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(20, 3, "Plantel:", 0, 'R', 0);
		  $pdf->SetXY(83,75);
	      $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(30, 3,"   ".$plantel, 0, 'L', 0);
		  $pdf->line(83,78,120,78);
		  
		  $pdf->SetXY(7,85);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(20, 3, "Carrera:", 0, 'L', 0);
		  $pdf->SetXY(25,85);
	      $pdf->SetFont('Arial', '', 11);
		  $pdf->MultiCell(170, 3,$carrera, 0, 'L', 0);
		  $pdf->line(25,88,192,88);
		  
		  $pdf->SetXY(7,95);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(100, 3, "Documentación entregada:", 0, 'L', 0);
		  
		  $pdf->rect(10,105,4,4);
		  $pdf->SetXY(9.5,105.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $acta, 0, 'L', 0);
		  $pdf->SetXY(14,106);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 3, "Original del Acta de Nacimiento (apostillada si eres extranjero) o Carta de Naturalización o FM2 o FM3.", 0, 'L', 0);
		  
		  $pdf->rect(10,111,4,4);
		  $pdf->SetXY(9.5,111.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $certificadoo, 0, 'L', 0);
		  $pdf->SetXY(14,112);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 4, "Original y copia fotostática legible, en tamaño carta, del Certificado de Estudios de Bachillerato, Profesional Técnico Bachiller o Tecnólogo, DEBIDAMENTE LEGALIZADO.", 0, 'L', 0);
		  
		  $pdf->rect(10,125,4,4);
	      $pdf->SetXY(9.5,125.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $ficha_pago, 0, 'L', 0);
		  $pdf->SetXY(14,126);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 3, "Copia fotostática del pago de inscripción sellado por el Banco (Orden de Pago).", 0, 'L', 0);
		  
		  $pdf->rect(10,131,4,4);
	      $pdf->SetXY(9.5,131.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $certificadom, 0, 'L', 0);
		  $pdf->SetXY(14,132);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 4, "Certificado médico original, expedido por una Institución oficial del Sector Salud (IMSS, ISSSTE, Cruz Roja, Cruz Verde, SSA, PENSIONES, SEDENA, Secretaría de Marina, Seguro Popular, etc.) con vigencia de 180 días máximo y que especifique tu tipo de sangre.", 0, 'L', 0);
		  
		  $pdf->rect(10,148,4,4);
	      $pdf->SetXY(9.5,148.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $carta_compromiso, 0, 'L', 0);
		  $pdf->SetXY(14,149);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 4, "Carta compromiso para trámite del seguro facultativo", 0, 'L', 0);
		  
		  $pdf->rect(10,154,4,4);
	      $pdf->SetXY(9.5,154.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $reglamento, 0, 'L', 0);
		  $pdf->SetXY(14,155);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 4, "Carta de cumplimiento del reglamento escolar", 0, 'L', 0);
		  
		  $pdf->rect(10,160,4,4);
	      $pdf->SetXY(9.5,160.8);
		  $pdf->SetFont('Arial', 'B', 12);
		  $pdf->MultiCell(3, 3, $antidoping, 0, 'L', 0);
		  $pdf->SetXY(14,161);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(185, 4, "Resultado del examen de detección de uso de drogas ilícitas", 0, 'L', 0);
		  
		  if($cuota_extranjero == "X"){
		    $pdf->rect(10,166,4,4);
	        $pdf->SetXY(9.5,166.8);
		    $pdf->SetFont('Arial', 'B', 12);
		    $pdf->MultiCell(3, 3, $cuota_extranjero, 0, 'L', 0);
		    $pdf->SetXY(14,167);
		    $pdf->SetFont('Arial', '', 10);
		    $pdf->MultiCell(190, 4, "En caso de ser extranjero, copia fotostática del pago de Cuota de Inscripción Alumno Extranjero sellado por el Banco", 0, 'L', 0);
		  }
		  
		  if($prorroga != null || $prorroga != ""){
		    $pdf->rect(10,175,4,4);
	        $pdf->SetXY(9.5,175.8);
		    $pdf->SetFont('Arial', 'B', 12);
		    $pdf->MultiCell(3, 3, $prorrogaX, 0, 'L', 0);
		    $pdf->SetXY(14,176);
		    $pdf->SetFont('Arial', 'B', 10);
		    $pdf->MultiCell(190, 4, "Prorroga de documentación.", 0, 'L', 0);
		    $pdf->SetXY(14,181);
		    $pdf->SetFont('Arial', '', 10);
		    $pdf->MultiCell(50, 4, "Cual documento: ", 0, 'L', 0);
		    $pdf->SetXY(42,181);
		    $pdf->SetFont('Arial', '', 10);
			
			if($prorroga2 == "")
		      $pdf->MultiCell(156, 4, $prorroga1, 0, 'L', 0);
			  
			else
			  $pdf->MultiCell(156, 4, $prorroga1."  y  ".$prorroga2, 0, 'L', 0);
			  
		    $pdf->line(42,185,198,185);
		  }
		  
		  $pdf->SetXY(10,200);
		  $pdf->SetFont('Arial', 'B', 11);
		  $pdf->MultiCell(50, 3, "Nota importante:", 0, 'L', 0);
		  $pdf->SetXY(10,205);
		  $pdf->SetFont('Arial', '', 10);
		  $pdf->MultiCell(180, 3, "Conserva bien este documento, ya que lo necesitas para futuros trámites como baja definitiva y egreso.", 0, 'L', 0);
		  
		   $pdf->rect(7,220,192,45);
		   $pdf->line(7,232,199,232);
		   $pdf->line(80,220,80,265);
		   $pdf->SetXY(7,225);
		   $pdf->SetFont('Arial', 'B', 11);
		   $pdf->MultiCell(73, 3, "Fecha de recepción", 0, 'C', 0);
		   $pdf->SetXY(7,235);
		   $pdf->SetFont('Arial', '', 9);
		   $pdf->MultiCell(73, 3, "Fecha de recepción de documentos del sistema.", 0, 'C', 0);
		   $pdf->SetXY(7,250);
		   $pdf->SetFont('Arial', 'B', 10);
		   $pdf->MultiCell(73, 3, date('d-m-y   H:i:s'), 0, 'C', 0);
		   $pdf->SetXY(80,223);
		   $pdf->SetFont('Arial', 'B', 9);
		   $pdf->MultiCell(119, 3, "Nombre, firma y sello quien recibe en Ventanilla del Depto. de Servicios de Apoyo Académico ", 0, 'L', 0);
		  
		  $pdf->Output("public/files/pdfs/aspirantes/".$idAspirante.".pdf");
		  $this->redirect("public/files/pdfs/aspirantes/".$idAspirante.".pdf");
		  
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
			$this -> render_partial('reactivarActualizacion');
		  }
		  die();
		}
		
		//Funcion que activa el formulario de actualizacion de datos
		function activarForm(){
		  $reactivar = new comentariosActualizacionDatos();
		  
		  $updateReactiva = $reactivar -> find_first('registro = '.$this -> post('registroAlumno'));
		  
		  if($updateReactiva != null || $updateReactiva != "" ){
		    $reactivar -> activar = '1';
			$result = $reactivar -> update(); 
			
			if($result == true)
			   Flash::success('El formulario de actualizacion de datos fue activado');
			   
			else
			  Flash::error('Hubo un error, el formulario no pudo ser activado');
			  
		  }
		  else{
		   Flash::error('Hubo un error, el formulario no puede ser activado');
		  }
		  
		  die();
		}
		
		function valida(){
			if(Session::get_data('tipousuario') == "VENTANILLA"){
				return true;
			}else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
		
		
		function validaComentarios(){
			if(Session::get('registro') == "1861" || Session::get('registro') == "8108"){
				return true;
			}else{
                $this -> redirect("general/inicio");
				return true;
			}
		}
				
	}
	
?>
