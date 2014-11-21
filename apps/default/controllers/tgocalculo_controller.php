<?php
			
	class TgocalculoController extends ApplicationController {

		function index(){
			$this -> valida();			
		}
		
		function promocion(){
		
		}
		
		function semestre(){		
			$this -> set_response("view");			
			$this -> semestre = $this -> post("semestre");
			$this -> registro = $this -> post("registro");
			$Alumnos = new Alumnos();
			if($this -> semestre != ''){
				$query = "semestre_anterior = ".$this -> semestre." and ";
			}else{
				$query = "";
			}
			if($this -> registro != ''){
				$query2 = "registro = ".$this -> registro." and ";
			}else{
				$query2 = "";
			}
			
			$this -> alumnos = $Alumnos -> find($query2.$query." estado_anterior = 'OK'", "order: registro ASC");			
			
		}
		
		function limpiar(){
			$Extras = new Extras();
			$Alumnos = new Alumnos();
			$Calificaciones = new Calificaciones();
			$i = 1;
			if($calificaciones = $Calificaciones -> find("situacion = 'BA' or situacion = 'RE'")){
				foreach($calificaciones as $c){					
					$Extras -> find_first("horario_id = ".$c -> horario_id."  and registro = ".$c -> registro);
					echo $i." - ".$Extras -> registro."<br>";		
					if ($Extras -> delete()){
					
					}else{
						echo "No se pudo Borrar ".$Extras -> id;
					}					
					$i++;		
				}			
			}
			
		}
		
		function limpiar2(){
			$Extras = new Extras();
			$Alumnos = new Alumnos();
			$Calificaciones = new Calificaciones();
			
			$registros = $Calificaciones -> distinct("registro");
			foreach($registros as $r){
				if($Alumnos -> find_first("registro = ".$r)){
				
				}else{					
					if($extras = $Extras -> find("registro = ".$r)){
						foreach($extras as $e){
							if($Extras -> find_first($e -> id)){
								//echo $Extras -> horario_id." ".$Extras -> registro."<br>";
								$Extras -> delete();
							}
						}
					}				
				}
			
			}
		
		}
		
		function estadisticas(){
			$this -> valida();	
			/*
			if ($xls == 1){
				header("content-disposition: attachment;filename=programa_trabajo_academico.xls");
			}
			*/		
		}				
		
		function estadisticasRegulares($xls=0){
			//$this -> valida();				
			$this -> set_response("view");
			if ($xls == 1){
				header("content-disposition: attachment;filename=alumnos_regulares_sig_semestre.xls");
			}
			
			$Periodo = new Periodos();
			$Alumnos = new Alumnos();
			$Horarios = new Horarios();
			$Extras = new Extras();
			$Calificaciones = new Calificaciones();
			
			$this -> regulares = NULL;
			$this -> irregulares = NULL;
			$this -> procesos = NULL;
			$this -> baja = NULL;			

			$cont = 0;
			$conti = 1;
			$contp = 1;	
			$contb = 1;			
			$noExisten = 0;
			$cuantosGrupo = 0;

			$this -> periodo = $Periodo -> find_first("activo = 1");		    
			$alumnos = $Alumnos -> find("estado = 'OK'");
			
			foreach($alumnos as $a){
				$todasRegulares = 1;
				$reprobadas = 0;
				$BA = 0;
				$calificaciones = $Calificaciones -> find("registro = ".$a -> registro);								
				foreach($calificaciones as $c){
					if ($c -> situacion != 'OR'){
						if ($c -> situacion == '-'){
							if($Horarios -> find_first($c -> horario_id)){
								if($Horarios -> grupo_id == 0 || $Horarios -> maestro_id == 0 ){
									$cuantosGrupo++;
								}else{
									$todasRegulares = 0;	
								}
							}else{
								$noExisten++;
							}														
						}else{
							if ($c -> situacion != 'BA'){
								if($Extras -> find_first('horario_id = '.$c -> horario_id.' and registro = '.$a -> registro)){
									if($Extras -> calificacion < 70 || $Extras -> calificacion > 100){
										$todasRegulares = 0;
										$reprobadas++;
									}								
								}else{
									$todasRegulares = 0;
								}
							}else{
								$BA = 1;								
							}
						}
						
					}
					//echo $c -> registro." - ".$c -> situacion."<br />";
				}				
				if ($BA == 1){
					$this -> baja[$contb++] = $a -> registro;
				}else{
					if ($todasRegulares == 1){
						$this -> regulares[$cont++] = $a -> registro;					
					}
					if ($reprobadas > 0 && $reprobadas < 3){
						$this -> irregulares[$conti++] = $a -> registro;
					}
					if ($reprobadas >= 3){
						$this -> procesos[$contp++] = $a -> registro;
					}
				}
				//echo "<br />";
			}
			//echo $cont."<br><br>";
			//foreach($this -> regulares as $r){
				//echo $r."<br>";
			//}
			$this -> conti = $conti;
			$this -> contp = $contp;
			$this -> contb = $contb;
			$this -> render_partial("regulares");
			//echo "Grupo y Mestros con 0: ".$cuantosGrupo."<br>";
			//echo "Horarios que no Existen:".$noExisten."<br>";
		}
		
		function apoyo(){
			$this -> valida();	
			$Ticket = new Ticket();
			$this -> ticket = $Ticket -> find("(estado like 'Abierto' or estado like 'Proceso' )and dirigido_a = 0 ORDER BY estado ASC LIMIT 0 , 10");
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
			if(Session::get("tipo")=="CALCULO"){
				return true;
			}else{
				$this -> redirect("general/ingresar");
				//return true;
			}
			
		}
		
		function info(){
			$this -> set_response("view");
			echo phpinfo();
		}
	}
	
?>