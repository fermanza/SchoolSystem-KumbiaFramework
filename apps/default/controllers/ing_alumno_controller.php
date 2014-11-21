<?php
	class Ing_alumnoController extends ApplicationController {
	
		function actualizar(){
			
			$registro = $this -> post("registro");
			
			echo $registro;
			
			$xalumnos = new Xalumnos();
			$alumno = $xalumnos -> find_first("registro=".$registro);
			
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
			
			$this -> redirect("alumno/informacion");
		}
	}
?>