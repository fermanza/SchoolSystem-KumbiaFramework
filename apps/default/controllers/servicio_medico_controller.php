<?php
			
	class ServicioMedicoController extends ApplicationController {
		
		function index(){
			unset($this->usuario);
			$this->usuario = Session::get_data('registro');
		} // function buscar_alumnos_seguro_medico()
		
		function imprimirSeguro(){
		  $this -> validar();
		}
		
		//Obtiene reporte de Seguro Facultativo
		function reporteSeguro(){
		  $this -> validar();
			
		  //Se vacian los registros de la tabla temporal
		  $registrosTemp = new registrosTemp();
		  $result = $registrosTemp -> truncate_registrosTemp('2');
		}
		
		
		function buscar_alumnos_seguro_medico($status=0){
			if(isset($status) && $status ==1)
				$this -> render_partial("exito_actualizando_servicio_medico");
			$this -> render_partial("buscar_alumnos");
		} // function buscar_alumnos_seguro_medico()

		function buscar_alumnos_actualizar_datos($status=0){
			if(isset($status) && $status ==1)
				$this -> render_partial("exito_actualizando_servicio_medico");
			$this -> render_partial("buscar_alumnos_actualizar_datos");
		} // function buscar_alumnos_actualizar_datos()
		
		function mostrando_seguro_medico(){
			
			unset($this->registro);
			
			$nombre = $this -> post("nombreAlumno");
			$registro = $this -> post("registroAlumno");
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			//echo $registro." ".$corregirKardexExito;
			if( ($nombre == "" && $registro == "") || 
					$nombre == "Nombre Alumno" && $registro == "Registro Alumno" ){ // Ademas checar que el registro sea integer...
				$this -> redirect("servicio_medico/buscar_alumnos_seguro_medico");
				return;
			}
			
			$KardexIng = new Kardexing();
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			// Si ingreso un registro, usar solo el registro y verificar que sea integer.
			if( $registro != "" && $registro != "Registro Alumno" )
				$query =  "miReg = '".$registro."'";
			else
				$query =  "vcNomAlu like '%".$nombre."%'";
			
			$this->alumno = $Alumnos -> find_first($query);
			if( !isset($this->alumno -> miReg) ){
				$this -> redirect("servicio_medico/buscar_alumnos_seguro_medico");
				return;
				//return $this -> route_to("controller: ingcalculo", "action: correcionKardex", "id:1");
			}
			$this->get_info_for_partial_info_alumno($this->alumno);
			
			$XalumnosPersonal = new XAlumnosPersonal();
			$this->xalumnosPersonal = $XalumnosPersonal->find_first("registro = '".$this->alumno->miReg."'");
			
			$this->registro = $registro;
		} // function mostrando_seguro_medico()
		
		function mostrando_actualizar_datos(){
			
			unset($this->registro);
			
			$nombre = $this -> post("nombreAlumno");
			$registro = $this -> post("registroAlumno");
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			//echo $registro." ".$corregirKardexExito;
			if( ($nombre == "" && $registro == "") || 
					$nombre == "Nombre Alumno" && $registro == "Registro Alumno" ){ // Ademas checar que el registro sea integer...
				$this -> redirect("servicio_medico/buscar_alumnos_actualizar_datos");
				return;
			}
			
			$KardexIng = new Kardexing();
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			// Si ingreso un registro, usar solo el registro y verificar que sea integer.
			if( $registro != "" && $registro != "Registro Alumno" )
				$query =  "miReg = '".$registro."'";
			else
				$query =  "vcNomAlu like '%".$nombre."%'";
			
			$this->alumno = $Alumnos -> find_first($query);
			if( !isset($this->alumno -> miReg) ){
				$this -> redirect("servicio_medico/buscar_alumnos_actualizar_datos");
				return;
				//return $this -> route_to("controller: ingcalculo", "action: correcionKardex", "id:1");
			}
			$this->get_info_for_partial_info_alumno($this->alumno);
			
			$XalumnosPersonal = new XAlumnosPersonal();
			$this->xalumnosPersonal = $XalumnosPersonal->find_first("registro = '".$this->alumno->miReg."'");
			
			$this->registro = $registro;
		} // function mostrando_actualizar_datos()
		
		function actualizar_seguro_medico(){
			$registro = $this->post("registro");
			
			$servicio_medico -> seguridad_social = $this -> post("seguridad_social");
			$servicio_medico -> dependencia_seguro = $this -> post("dependencia_seguro");
			$servicio_medico -> parte_de_quien_seguro = $this -> post("parte_de_quien_seguro");
			$servicio_medico -> numero_seguro_social = $this -> post("numero_seguro_social");
			$servicio_medico -> numClinica = $this -> post("numero_clinica");
			$servicio_medico -> fechaMovimiento = $this -> post("fechaMovimiento");
			
			$XalumnosPersonal = new XAlumnosPersonal();
			
			$id="";
			$datos = $XalumnosPersonal->find_all_by_sql("SELECT id 
														 FROM servicio_medico 
														 WHERE registro = ".$registro);
														 
			if($datos){
				$XalumnosPersonal->find_all_by_sql("UPDATE servicio_medico SET numero_seguro_social = '".$this -> post("numero_seguro_social")."',
									seguridad_social = '". $this -> post("seguridad_social")."', dependencia_seguro = '".$this -> post("dependencia_seguro")."',
									parte_de_quien_seguro = '".$this -> post("parte_de_quien_seguro")."', numClinica = '".$this -> post("numero_clinica")."',
									fechaMovimiento = '".$this -> post("fechaMovimiento")."' WHERE registro = ".$registro);
			}else{
				$XalumnosPersonal->find_all_by_sql("INSERT INTO servicio_medico(numero_seguro_social,seguridad_social,dependencia_seguro,parte_de_quien_seguro,numClinica,
													fechaMovimiento, registro) VALUES ('".$this -> post("numero_seguro_social")."','".$this -> post("seguridad_social")."',
													'".$this -> post("dependencia_seguro")."', '".$this -> post("parte_de_quien_seguro")."','".$this -> post("numero_clinica").
													"','".$this -> post("fechaMovimiento")."','".$registro."')");
			}
			
			//$XalumnosPersonal = new XAlumnosPersonal();
			$XalumnosPersonal->update_servicio_medico($servicio_medico, $registro);
			
			$this->redirect("servicio_medico/buscar_alumnos_seguro_medico/1");
		} // function actualizar_seguro_medico()
		
		function actualizando_seguro_medico(){
			$registro = $this->post("registro");
			
			$xalumnos_personal = $xalumnos = new XalumnosPersonal();
			$personal = $xalumnos_personal -> find_first("registro=".$registro);
			
			$personal -> nombre = strtoupper ($this -> post("nombre"));
			$personal -> paterno = strtoupper ($this -> post("paterno"));
			$personal -> materno = strtoupper ($this -> post("materno"));
			
			$personal -> sexo = strtoupper ($this -> post("sexo"));
			
			$personal -> estadocivil = strtoupper ($this -> post("civil"));
			$personal -> sangre = strtoupper ($this -> post("sangre"));
			$personal -> domicilio = strtoupper ($this -> post("domicilio"));
			
			if($this -> post("colonia")==""){
				$personal -> colonia = "-";
			}
			else{
				$personal -> colonia = strtoupper ($this -> post("colonia"));
			}
			
			$personal -> cp = strtoupper ($this -> post("cp"));
			$personal -> ciudad = strtoupper ($this -> post("ciudad"));
			
			$personal -> estado = "JALISCO";
			
			$personal -> telefono = strtoupper ($this -> post("telefono"));
			$personal -> celular = strtoupper ($this -> post("celular"));
			$personal -> correo = strtolower ($this -> post("correo"));
			
			if($this -> post("curp")==""){
				$personal -> curp = "-";
			}
			else{
				$personal -> curp = strtoupper ($this -> post("curp"));
			}
			
			$personal -> save();
			
		    //Funcion que guarda el log del formulario
			$db = DbBase::raw_connect();
            $log =  $db->query("INSERT INTO log_datosalumno (usuario,accion,alumno,fecha_accion) VALUES
			('".Session::get_data('registro')."','MODIFICACION DE DATOS - SERVICIO MEDICO',".$registro.",'".date('Y-m-d H:i:s')."')");			

			$this -> redirect("servicio_medico/buscar_alumnos_actualizar_datos/1");
		} // function actualizando_seguro_medico()
		
		public function seguridad_mostrar_container($seguridad_social, $registro){
			$this->set_response('view');
			
			unset($this->seguridad_social);
			unset($this->dependencia_seguro);
			unset($this->parte_de_quien_seguro);
			unset($this->numero_seguro_social);
			unset($this->numero_clinica);
			unset($this->fechaMovimiento);
			
			$this->seguridad_social = $seguridad_social;
			
			$XalumnosPersonal = new XalumnosPersonal();
			foreach( $XalumnosPersonal->find("registro = '".$registro."'") as $xalumno ){
				$this->dependencia_seguro = $xalumno->dependencia_seguro;
				$this->parte_de_quien_seguro = $xalumno->parte_de_quien_seguro;
				$this->numero_seguro_social = $xalumno->numero_seguro_social;
				$this->numero_clinica = $xalumno->numClinica;
				$this->fechaMovimiento = $xalumno->fechaMovimiento;
			}
			
			$this->render_partial("seguridad_social");
			
		} // public function seguridad_mostrar_container($seguridad_social)
		
		function get_info_for_partial_info_alumno($alumno){
			$Alumnos = new Alumnos();
			$KardexIng = new KardexIng();
			
			unset($this->career);
			unset($this->plantel);
			unset($this->turno);
			unset($this->ncreditos);
			unset($this->promedio);
			
			$this->alumno = $alumno;
			// Nombre carrera
			$this->career = $Alumnos->get_careername_from_student($alumno->carrera_id, $alumno->areadeformacion_id);
			// Nombre Plantel
			$this->plantel = $Alumnos->get_nombre_plantel($alumno->enPlantel);
			// Turno Matutino o Vespertino
			$this->turno = $Alumnos->get_turno($alumno->enTurno);
			// Obtener Creditos
			$this->ncreditos = $KardexIng->get_ncreditos($alumno);
			// Obtener promedio
			$this->promedio = $KardexIng->get_average_from_kardex($alumno->miReg);
			
			$Periodos = new Periodos();
			$this->periodo_letra = $Periodos->convertirPeriodo();
			
		} // function get_info_for_partial_info_alumno($alumno)
		
		
		  //funcion que busca a un alumno por registro para generar un reporte
		  function buscarAlumnoReporte(){	   
			$this -> set_response("view");
				   
			if($this -> post('registro') == "" || count($this -> post('registro')) == 0){
			  //Mensaje de error
			  echo '<input type="hidden" id="status" name="status" readonly="readonly" value="FALSE" maxlength="0"/>';
			  echo '<input type="hidden" id="msg" name="msg" readonly="readonly" value="Es necesario ingresar un registro" maxlength="0"/>';
			  $this -> result = "";
			  $this -> render_partial('reporteSeguro');
			}
					
			else{
			  $objeto = new alumnos(); 
			  $this -> result = $objeto -> buscar_registro_alumno($this -> post('registro'));
			  
			  if(count($this -> result) <= 0 || $this -> result == ""){
				echo "<script type='text/javascript'> showDialog('ALERTA','No se encontro el registro ".$this -> post('registro')."','warning',4); </script>";
				$this -> result = "";
				$this -> registroB = $this -> post('registro');
				$this -> render_partial('reporteSeguro');
			  }
			  else{
				$this -> registroB = $this -> post('registro');
				$this -> render_partial('reporteSeguro');
			  }
			}
		  } 
		  
	
		  //Funcion que sirve para guardar o eliminar los reistros temporalmente en una tabla
		  function agregar_registros(){
			$this -> set_response("view");
			
			$registrosTemp = new registrosTemp();
			//Se guardan los registros
			if($_GET['tipoAccion'] == 1){
			  $registrosTemp -> registro = $_GET['registro'];
			  $registrosTemp -> tipo = "2";
			  
			  $registrosTemp -> save();
			}
			
			//Se eliminan los registros
			if($_GET['tipoAccion'] == 2){  
			  $db = DbBase::raw_connect();
			  $db->query("DELETE FROM registros_temp WHERE tipo = 2 AND registro = ".$_GET['registro']);
			}
			
			 $this -> render_partial('reporteSeguro');
		  }
		  
		  //Funcion que exporta el la lista (reporte) en excel
		  function reporteExcel(){
		  
			$this -> set_response("view");
			
			$this -> valida();
			
			$registrosTemp = new registrosTemp();

			$result = $registrosTemp -> get_registros('2');

			$this -> registros = $result ;

			$this -> render_partial('reporteSeguroExcel');
		  }
		  
		
		function validar(){
			if( Session::get_data('SERVICIOMEDICO')!="SERVICIOMEDICO" ){
				$this->redirect('seguridad/terminarsesion');
			}
		}
	}
?>