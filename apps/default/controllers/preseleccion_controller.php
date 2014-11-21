<?php
	class PreseleccionController extends ApplicationController {

		function index(){
			$this-> valida ();
		}
		
		function inicio (){
			
		}
		
		
		function cambiarEstado(){
			$this-> valida ();
		}
		
		function hola(){
		 echo "jajaja";
		}
		function buscar2(){
			$this -> set_response("view");
			echo $this -> post("registro");
		}
		
		function buscar(){
			$this-> valida ();
			
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$nombre = $this -> post("nombre");	
			$entro = 0;
			
			$HistorialAlumno = new HistorialAlumno();
			$Alumnos = new Alumnos();
			
			if ($registro != '' || $nombre != '' ){
				$db = DbBase::raw_connect();
				$db -> query("select * from alumnos where miReg = '$registro' or vcNomAlu like '%".$nombre."%'");
				print "Hay ".$db->num_rows()." alumnos ";
				if ( $db-> num_rows() == 0 )
					exit ();
			}else{
				exit();
			}
			
			if($registro != ''){
				$this -> alumno = $Alumnos -> find_first("miReg = ".$registro);
				$entro = 1;
			}elseif($nombre != ''){
				$this -> alumno = $Alumnos -> find_first("vcNomAlu like '%".$nombre."%'");
				$registro = $this -> alumno -> miReg;
				
				$entro = 1;
			}else{
				Flash::error('no se encontro al Alumno');
			}
			
			$Situaciones = new Situaciones();
			$this -> situaciones = $Situaciones -> find("order: nombre ASC");
			
			if($entro == 1){
				$this -> render_partial("alumno");
			}
			echo "<br /><br /><br />";
			if($this -> alumno1 = $HistorialAlumno -> find("registro = ".$this -> alumno -> miReg)){
				$this -> render_partial("historialAlumno");
			}
			
		}
		
		function guardarCambioEstado(){
			$this-> valida ();
			
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$nuevaSituacion = $this -> post("nuevaSituacion");
			$fecha = $this -> post("fecha");
			
			$HistorialAlumno = new HistorialAlumno();
			
			$HistorialAlumno -> registro = $registro;
			$HistorialAlumno -> situacion_id = $nuevaSituacion;
			$HistorialAlumno -> fecha = $fecha;
			if($HistorialAlumno -> save()){
				Flash::success('Se guardo el nuevo estado del alumno '.$registro.' - '.$nuevaSituacion.' - '.$fecha);
			}else{
				Flash::error('No se pudo guardar el nuevo estado del alumno '.$registro.' - '.$nuevaSituacion.' - '.$fecha);
			}				
			if($this -> alumno1 = $HistorialAlumno -> find("registro = ".$registro)){
				$this -> render_partial("historialAlumno");
			}
		}
		
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
			if(Session::get("tipousuario")=="VENTANILLA" || Session::get("tipousuario")=="ADMINISTRADOR" ){
			
			// if ( alumno < 8tavo semestre ){
			//		No podrá ingresar al Sistema hasta que no complete su Preelección
			// }else{
			// 		Si es de 8tavo semestre podrá entrar sin necesidad de que sea obligatorio la elección de Preelección
			//}
			
			
			/* if ( alumno ya hizo su preleccion )
				No mostrarle la prelección
			*/
			
			
			
				return true;
			}
			$this -> redirect("general/index");
			return true;
		}	
	}
	
?>
