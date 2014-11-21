<?php
			
	class ActualizacionController extends ApplicationController {
	
		function gracias(){
			$this -> set_response("view");
			echo "MUCHAS GRACIAS POR ACTUALIZAR TU INFORMACIÓN";
		}
		
		function error($registro){
			$this -> set_response("view");
			echo "EL REGISTRO (".$registro.") DE ALUMNO NO ES VALIDO O NO SE ENCUENTRA REGISTRADO";
		}
		
		function index(){
			$this -> set_response("view");
			echo '<form method="POST" action="'.KUMBIA_PATH.'actualizacion/credencial">';
				echo '<input type="text" name="registro">';
				echo '<input type="submit" value="Actualizar Datos">';
			echo '</form>';
		}

		function activar($registro){
			echo $registro." ACTIVADO";
			
			$xalumnos = new Xalumnos();
			$alumno = $xalumnos -> find_first("registro=".$registro);
		
			$alumno -> nombre = "-";
			$alumno -> paterno = "-";
			$alumno -> materno = "-";
			
			$alumno -> save();
		}
		
		function actualizacion(){
			
			$this -> set_response("view");
			
			$registro = $this -> post("registro");
			
			$registro += 0;
			
			$xalumnos = new Xalumnos();
			
			$n = $xalumnos -> count("registro=".$registro);
			
			/*
			if($n==0){
				$this -> redirect("tgo_alumno/error/".$registro);
				return;
			}
			*/
			
			$alumno = $xalumnos -> find_first("registro=".$registro);
			
			$xalumnos_personal = $xalumnos = new XalumnosPersonal();
			$personal = $xalumnos_personal -> find_first("registro=".$registro);
			
			/*
			if($alumno -> nombre != "-" && $alumno -> nombre != "" && $personal -> nombre != ""){
				$this -> redirect("tgo_alumno/gracias");
				return;
			}
			*/
			
			$this -> registro = $registro;
		}
		
		function credencial(){
			
			$this -> set_response("view");
		
			echo $this -> post("registro");
			
			$this -> registro = $this -> post("registro");
		}
		
		function credencializar(){
			$this -> set_response("view");
					
			$cnx = mysql_connect("localhost","calculo","super dios");
			mysql_select_db("calculo",$cnx);
			
			$link = mysql_connect('localhost', 'calculo', 'super dios');
			if (!$link) {
				die('Not connected : ' . mysql_error());
			}


			$db_selected = mysql_select_db('calculo', $link);
			if (!$db_selected) {
				die ('Can\'t use calculo : ' . mysql_error());
			}
			
			$query = "UPDATE xalumnos_personal SET cp='".$this -> post("cp")."',curp='".$this -> post("curp")."',correo='".$this -> post("correo")."',telefono='".strtoupper ($this -> post("telefono"))."',ciudad='".strtoupper ($this -> post("ciudad"))."',colonia='".strtoupper ($this -> post("colonia"))."',domicilio='".strtoupper ($this -> post("domicilio"))."',sangre='".strtoupper ($this -> post("sangre"))."',estadocivil='".strtoupper ($this -> post("civil"))."',sexo='".strtoupper ($this -> post("sexo"))."',nombre='".strtoupper ($this -> post("nombre"))."',paterno='".strtoupper ($this -> post("paterno"))."',materno='".strtoupper ($this -> post("materno"))."',fnacimiento='".$this -> post("y")."-".$this -> post("m")."-".$this -> post("d")."' WHERE registro=".$this -> post("registro");
			
			mysql_query($query,$link);
			
			$this -> redirect("actualizacion/");
		}
		
		function actualizar(){
			$this -> set_response("view");
			
			$registro = $this -> post("registro");
			
			$xalumnos = new Xalumnos();
			$alumno = $xalumnos -> find_first("registroa=".$registro);
		
			$alumno -> nombre = strtoupper ($this -> post("nombre"));
			$alumno -> paterno = strtoupper ($this -> post("paterno"));
			$alumno -> materno = strtoupper ($this -> post("materno"));
			
			echo strtoupper ($this -> post("nombre"));
			
			echo $alumno -> nombre." ".$alumno -> paterno." ".$alumno -> materno;
		
			$alumno -> save();
			
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
			$personal -> correo = strtolower ($this -> post("correo"));
			
			if($this -> post("curp")==""){
				$personal -> curp = "-";
			}
			else{
				$personal -> curp = strtoupper ($this -> post("curp"));
			}
			
			$personal -> save();
			
			//$this -> redirect("actualizacion/credencial/".$registro);
		}
		
		
	}
	
?>

