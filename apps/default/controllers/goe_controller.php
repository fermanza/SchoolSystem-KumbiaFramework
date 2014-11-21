<?php
			
	class GoeController extends ApplicationController {						
		
		function index(){
			$this -> valida();						
		}
		
		function convertirAAlumno($registro, $coor=0){
			$this -> valida();
			
			Session::set_data('temporal', Session::get("registro"));	// se pone en temporal la nomina del Prof o Coor		
			Session::set_data('temporal2', $coor);	// se pone en temporal la nomina del Prof o Coor
			Session::set_data('registro', $registro);  //se asigna a la session el registro del alumno
						
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> find_first("registro = ".$registro);
			Session::set_data('nombre', $alumno -> nombre_completo); 
			Session::set_data('grupo', $alumno -> grupo); 
			Session::set_data('fecha', $alumno -> fecha_nacimiento);
			Session::set_data('turno', $alumno -> turno);
			Session::set_data('especialidad', $alumno -> especialidad);
			Session::set_data('nivel', $alumno -> semestre);
			Session::set_data('plantel', $alumno -> plantel);
			
			$this -> route_to('controller: tgoalumno','action: index');
		}
		
		function convertirAGoe(){
			$this -> valida();
			
			Session::set_data('registro', Session::get("temporal"));
			Session::unset_data('temporal');
			$registro = Session::get("registro");
			
			
			$divisiones = new Divisiones();
			$division = $divisiones -> find_first("maestro_id=".$registro); 

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("id=".$registro);
			
			Session::set_data('division', $division -> id); 
			Session::set_data('nombre', $maestro -> nombre); 
			Session::set_data('coordinacion', $division -> nombre); 
			Session::set_data('div', $division -> clave); 							
			$temporal2 = Session::get("temporal2");
			$this -> route_to('controller: goe','action: buscar');			
		}
		
		function buscar(){
			$this -> valida();
		}
		
		function buscando(){
			$this -> valida();
			$this -> set_response("view");
			
			if($this -> post("registro") != ''){
				$this -> registro = " and registro = ".$this -> post("registro");
			}else{
				$this -> registro = "";
			}
			if($this -> post("nombre") != ''){
				$this -> nombre = " and nombre_completo like '%".utf8_decode($this -> post("nombre"))."%'";
			}else{
				$this -> nombre = "";
			}
			if($this -> registro != "" || $this -> nombre != ""){
				$Alumnos = new Alumnos();
				if($this -> alumnos = $Alumnos -> find("1".$this -> registro.$this -> nombre)){
					$this -> render_partial("busquedaAlumnos");
				}else{
					Flash::error("No se encontro ningun alumno");
				}	
			}else{
				Flash::error("No se encontro ningun alumno");
			}
			$this -> registro="";
			$this -> nombre="";
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}
		

		function valida(){
			if((Session::get("tipo")=="GOE")){
				return true;
			}
			//$this -> redirect("general/ingresar");
			return true;
		}

	}
	
?>