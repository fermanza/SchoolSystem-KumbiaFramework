<?php
	class IngalumnoController extends ApplicationController {
		
		function actualizar(){
			
			$registro = $this -> post("registro");
			
			$alumnos = new Alumnos();
			$xalumnos = new Xalumnos();
			$especialidades = new Especialidades();
			
			if( $alumno = $xalumnos -> find_first("registro=".$registro) ){
				
			}
			else{
				$alumno = new Xalumnos();
				$alumno -> registro = $registro;
			}
			
			if ( $alum = $alumnos -> find_first ( "miReg = ".$registro ) ){
				if ( $espec = $especialidades -> find_first ( "idtiEsp = ".$alum -> idtiEsp ) ){
				}
				else{
					echo "No encontro la carrera";
					exit(1);
				}
			}
			else{
				echo "No encontro el registro";
				exit(1);
			}
			
			$alumno -> plantel = $alum -> enPlantel;
			$alumno -> nivel = '0';
			$alumno -> carrera = $espec -> siNumEsp;
			$alumno -> plan = $alum -> enPlan;
			$alumno -> grupo = "CR";
			$alumno -> turno = 'V';
			$alumno -> semestre = 1;
			if ( $alum -> miPerIng == 32010 )
				$alumno -> tipo = 'I';
			else
				$alumno -> tipo = 'R';
			$alumno -> pago = '-';
			$alumno -> miReg = $alum -> miPerIng;
			$alumno -> nombre = strtoupper ($this -> post("nombre"));
			$alumno -> paterno = strtoupper ($this -> post("paterno"));
			$alumno -> materno = strtoupper ($this -> post("materno"));
			
			echo strtoupper ($this -> post("nombre"));
			
			echo $alumno -> nombre." ".$alumno -> paterno." ".$alumno -> materno;
			
			$alumno -> ingreso = $alum -> miPerIng;
			
			$alumno -> save();
			
			$xalumnos_personal = $xalumnos = new XalumnosPersonal();
			if($personal = $xalumnos_personal -> find_first("registro=".$registro)){
				
			}
			else{
				$personal = new XalumnosPersonal();
				$personal -> registro = $registro;
			}
			
			$personal -> fnacimiento = $this -> post("y")."-".$this -> post("m")."-".$this->post("d");
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
			
			$personal -> seguridad_social = $this -> post("seguridad_social");
			$personal -> dependencia_seguro = $this -> post("dependencia_seguro");
			$personal -> parte_de_quien_seguro = $this -> post("parte_de_quien_seguro");
			$personal -> numero_seguro_social = $this -> post("numero_seguro_social");
			
			if($personal -> save()){
			}
			else{
				echo 'No se púdo guardar';
			}
			
			$this -> redirect("alumno/informacion");
		}
		
		
	}
	
?>

