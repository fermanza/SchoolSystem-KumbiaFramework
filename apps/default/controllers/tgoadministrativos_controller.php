<?php
			
	class TgoadministrativosController extends ApplicationController {
	
		function index(){
			$this -> valida();
		}
		
		function informacion(){
			$this -> valida();
		}
		
		function alumnos(){
			$this -> valida();
		}
		
		function profesores($p=0){
			$this -> valida();
		}

		function infraestructura($p=0){
			$this -> valida();
		}
		
		function materias(){
			$this -> valida();
		}
		
		function grupos(){
			$this -> valida();						
		}
		
		function horarios(){
			$this -> valida();						
		}
		
		function calificaciones(){
			$this -> valida();
		}
		
		function apoyo(){			
			$this -> valida();
			$Ticket = new Ticket();
			$Usuarios = new Usuarios();
			$usuario = $Usuarios -> find_first("usuario = '".Session::get_data("registro")."'");
			$this -> ticket = $Ticket -> find("(estado like 'Abierto' or estado like 'Proceso') and dirigido_a = ".$usuario -> id);
		}
		
		function buscar(){
			$this -> set_response("view");
			if($this -> post('numero') != "") {
				$numero = " and id = ".$this->post("numero");	
			}else{
				$numero ="";
			}			
			
			if($this -> post('registro')!= "") {
				$registro = " and usuario_id = ".$this->post("registro");	
			}else{
				$registro="";
			}
			
			$Ticket = new Ticket();
			$this -> ticket = $Ticket -> find("1 ".$numero.$registro); 
			$this -> render_partial('buscar');
			//echo "actulizado<br>$registro<br>$numero";
		}
		
		function ticketSolucionado($id){
			$this -> set_response("view");
			$Ticket = new Ticket();
			if ($Ticket -> find_first("id = ".$id)){
				$Ticket -> estado= "Cerrado";
				$Ticket -> fecha_final = date("Y-m-d H:i:s",time());
				if ($Ticket -> save())
					Flash::success("<center>Se soluciono el ticket numero $id</center>");
			}
			else
				Flash::error("<center>No se pudo guardar el ticket numero $id o no existe</center>");
		}
		
		function digidoA(){
			$this -> set_response("view");
			$dirigido_a = $this->post("dirigido_a");
			$id = $this -> post("id");
			
			$Usuario = new Usuarios();
			if($usuario = $Usuario -> find_first("id = ".$dirigido_a)){
				$Ticket = new Ticket();
				if ($Ticket -> find_first("id = ".$id)){
					$Ticket -> dirigido_a= $dirigido_a;
					$Ticket -> estado= "Proceso";
					if ($Ticket -> save())
						Flash::success("<center>Se redirecciono el ticket numero $id al usuario $dirigido_a </center>");
				}else{
					Flash::error("<center>No se pudo guardar el redireccionamiento del ticket numero $id</center>");			
				}
			}else{
					Flash::error("<center>No se pudo redireccionar el ticket numero $id por que no existe el usuario</center>");			
			}
		}
		
		function responder(){
			$this -> set_response("view");

			$id = $this -> post("id");
			$respuesta = $this -> post("respuesta");
			$Ticket = new Ticket();
			if ($Ticket -> find_first("id = ".$id)){
				$Ticket -> respuesta = utf8_decode($respuesta);
				$Ticket -> estado= "Proceso";
				if ($Ticket -> save())
					Flash::success("<center>Se guardo la respuesta de el ticket numero $id</center>");
			}else
				Flash::error("<center>No se pudo guardar la respuesta de el ticket numero $id o no existe</center>");
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}
		
		function valida(){
			if(Session::get("tipo")=="JEFE"){
				return true;
			}
			$this -> redirect("general/ingresar");
			return true;
		}						
	}
	
?>
