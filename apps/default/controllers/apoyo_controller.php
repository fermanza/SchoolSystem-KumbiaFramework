<?php
	class ApoyoController extends ApplicationController {

		function index(){
			$this-> valida ();
		}
		
		function cambiarEstado(){
			$this-> valida ();
		}
		
		function buscar2(){
			$this -> set_response("view");
			$this -> post("registro");
		}
		
		function buscar(){
			$this-> valida ();
			
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$nombre = $this -> post("nombre");	
			$entro = 0;
			$HistorialAlumno = new HistorialAlumno();
			$Alumnos = new Alumnos();
			
			if($registro != ''){
				if($this -> alumno = $Alumnos -> find_first("miReg = ".$registro)){
//					echo $this -> alumno -> vcNomAlu;
				}
				$entro = 1;
			}elseif($nombre != ''){
				if($this -> alumno = $Alumnos -> find_first("vcNomAlu like '%".$nombre."%'"))
				$registro = $this -> alumno -> miReg;	
				$entro = 1;
			}else{
				Flash::error('no se encontro al Alumno');
			}
			
			$Situaciones = new Situaciones();
			$this -> situaciones = $Situaciones -> find("order: nombre ASC");
			
			if($entro == 1){
				$this -> render_partial("alumno2");
			}
			
			if($this -> alumno1 = $HistorialAlumno -> find("registro = ".$registro)){
				$this -> render_partial("historialAlumno");
			}
			
		}
		
		function guardarCambioEstado(){
			$this-> valida ();
			
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$nuevaSituacion = $this -> post("nuevaSituacion");
			$referencianew=  $this -> post("referencia");
			$fecha = $this -> post("fecha");
			$periodo = $this -> post("periodo");
			
			$this->cambiar_estado_en_alumnos($nuevaSituacion, $registro, $periodo);
			
			$Situaciones = new Situaciones();
			
			$HistorialAlumno = new HistorialAlumno();
			$HistorialAlumno -> registro = $registro;
			$HistorialAlumno -> situacion_id = $nuevaSituacion;
			$HistorialAlumno -> referencia = $referencianew;			
			$HistorialAlumno -> fecha = $fecha;
			$HistorialAlumno -> periodo = $periodo;
			if($HistorialAlumno -> save()){
				Flash::success('Se guardo el nuevo estado del alumno '.$registro.' - '.$nuevaSituacion.' - '.$fecha);
			}else{
				Flash::error('No se pudo guardar el nuevo estado del alumno '.$registro.' - '.$nuevaSituacion.' - '.$fecha);
			}				
			if($this -> alumno1 = $HistorialAlumno -> find("registro = ".$registro)){
				$this -> render_partial("historialAlumno");
			}
		}
		
		function cambiar_estado_en_alumnos($nuevaSituacion, $registro, $periodo){
			$Alumnos = new Alumnos();
			$alumnos = $Alumnos -> find_first("miReg = '".$registro."'");
			// OK, BT, BD, EG <- Disponibles en stSit
			if($nuevaSituacion==1){ // BAJA TEMPORAL
				$Xalumnocursos = new Xalumnocursos();
				$Xalumnocursos->pasar_materias_a_xalumnocursosbt_by_periodo_registro($registro, $periodo);
				//$Xalumnocursos->borrado_logico_materias_by_periodo($registro, $periodo);
				
				$Xtalumnocursos = new Xtalumnocursos();
				$Xtalumnocursos->pasar_materias_a_xalumnocursosbt_by_periodo_registro($registro, $periodo);
				//$Xtalumnocursos->borrado_logico_materias_by_periodo($registro, $periodo);
				$alumnos->stSit = "BT";
				$alumnos->update();
			}
			else if($nuevaSituacion==2){ // BAJA DEFINITIVA TRAMITE
				$alumnos->stSit = "BD";
				$alumnos->update();
			}
			else if($nuevaSituacion==3){ // EGRESADO
				$alumnos->stSit = "EG";
				$alumnos->update();
			}
			else if($nuevaSituacion==4){ // INTERCAMBIO
				$alumnos->stSit = "BD";
				$alumnos->update();
			}
			else if($nuevaSituacion==5){ // MOVILIDAD
				$alumnos->stSit = "BD";
				$alumnos->update();
			}
			else if($nuevaSituacion==6){ // REINGRESO
				$alumnos->stSit = "OK";
				$alumnos->update();
			}
			else if($nuevaSituacion==7){ // BAJA DEFINITIVA POR REGLAMENTO
				$alumnos->stSit = "BD";
				$alumnos->update();
			}
			else if($nuevaSituacion==8){ // REINGESO POR RECONSIDERACION DE BAJA
				$alumnos->enTipo("C");
				$alumnos->stSit = "OK";
				$alumnos->update();
			}
			else if($nuevaSituacion==9){ // ALTA DE BAJA TEMPORAL
				//$Xalumnocursos = new Xalumnocursos();
				//$Xalumnocursos->activar_materias_by_periodo($registro, $periodo);
				
				//$Xtalumnocursos = new Xtalumnocursos();
				//$Xtalumnocursos->activar_materias_by_periodo($registro, $periodo);
				$alumnos->stSit = "OK";
				$alumnos->update();
			}
			else if($nuevaSituacion==10){ // REPORTE DE INDISCIPLINA
			}
			else if($nuevaSituacion==11){ // CAMBIO DE CARRERA
				// Preguntarle a Aries, si también debo cambiar la carrera,,,
			}
			else if($nuevaSituacion==12){ // CAMBIO DE TURNO
				// Preguntarle a Aries, si debo cambiarle el turno a M o V
			}
			else if($nuevaSituacion==13){ // CAMBIO DE PLANTEL
				// Preguntarle a Aries, si debo permitir el cambio de enPlantel
			}
		} // function cambiar_estado_en_alumnos($nuevaSituacion, $registro, $periodo)
		
		function becas(){
			$this-> valida ();
			
			$Alumnos = new Alumnos();
			$Becas = new Becas();
			$this -> alumnos = $Alumnos -> find("stSit like 'OK'", "order: miReg ASC");
			$this -> becas = $Becas -> find("order: categoria ASC ");		
		}
		
		function agregar(){
			$this-> valida ();
			
			$this -> set_response("view");
			$periodo = $this -> post("periodo");
			$beca = $this -> post("beca");
			$puntaje = $this -> post("puntaje");
			$registro = $this -> post("registro");
			
			if($this -> post("beca") != 0 && $this -> post("registro") != 0){
				$BecaAlumno = new BecaAlumno();
				$BecaAlumno -> registro = $registro;
				$BecaAlumno -> beca_id = $beca;
				$BecaAlumno -> puntaje = $puntaje;
				$BecaAlumno -> periodo = $periodo;
				if($BecaAlumno -> save()){
					Flash::success("Se grabo la beca para el alumno ".$registro);
				}else{
					Flash::error("No se pudo guardar");
				}
			}else{
				Flash::error("Debes seleccionar un Alumno y un tipo de beca");
			}
		}
		
		function listadoBecas(){
			$this-> valida ();
			
			unset($this -> becaAlumno);
			
			$this -> set_response("view");
			$BecaAlumno = new BecaAlumno();
			
			$beca = $this -> post("beca");
			$periodo = $this -> post("periodo");			
			
			if($beca == 0){
				$beca = " ";
			}else{
				$beca = " and beca_id = ".$beca;
			}
			
			if($periodo == 0){
				$periodo = " ";
			}else{
				$periodo = " and periodo = ".$periodo;
			}
			
			if($this -> becaAlumno = $BecaAlumno -> find("1 ".$beca.$periodo)){
				$this -> render_partial("listadoBecas");
			}else{
				Flash::error("No se encontro ningun alumno con ese tipo de beca y/o periodo");
			}
		}
		
		function salir(){
			Session::set_data("tipousuario","");
			Session::set_data("registro","");			
			$this -> route_to("controller: general", "action: inicio");
		}
		
		function valida(){
			if( Session::get("tipousuario")=="VENTANILLA" ||
					Session::get("tipousuario")=="ADMINISTRADOR" ||
						Session::get("tipousuario")=="INGCALCULO" ){
				return true;
			}
			$this -> redirect("general/index");
			return true;
		}
	}
	
?>