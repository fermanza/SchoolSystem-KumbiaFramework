<?php
			
	class TutorController extends ApplicationController {
		
		function index(){
			$this -> valida();
		}	
		
		function estadisticas(){
			$this -> valida();
		}
		
		function reporteProfesores(){
			$this -> valida();
			$this -> set_response("view");
			$this -> render_partial("listadoProfesores");
		}
		
		function reporteProfesoresExcel(){
			$this -> set_response("view");
			header("content-disposition: attachment;filename=programa_trabajo_academico.xls"); 
			$this -> render_partial("listadoProfesores");
		}
		
		function asignacion(){
			$this -> valida();
			$Maestros = new Maestros();
			$Alumnos = new Alumnos();
			//ini_set('memory_limit', '128M');
			//set_time_limit(100);
			
			$this -> maestros = $Maestros -> find("order: nomina ASC");
			$this -> alumnos = $Alumnos -> find("stSit='OK' ORDER BY miReg ASC");
		}
		
		function asignar(){
			$this -> valida();
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$nomina = $this -> post("nomina");
			$this -> nomina = $nomina;
			
			$Tutorias = new Tutorias();
			
			if($Tutorias -> find_first("registro = ".$registro)){
				Flash::error('El alumno con registro: '.$registro.' Ya se encuentara asignado al Profesor: '.$Tutorias -> nomina);
			}else{
				$Tutorias = new Tutorias();
				$Tutorias -> registro = $registro;
				$Tutorias -> nomina = $nomina;
				$Tutorias -> activo = 1;
				if($Tutorias -> save()){
					Flash::success('Operaci&oacute;n exitosa - Se guardo el tutorado');
				}
			}
			$this -> render_partial('listado');
		}
		
		function cambiarTutorados(){
			$this -> valida();
			$this -> set_response("view");
			$nomina = $this -> post("nomina");
			$nomina2 = $this -> post("nomina2");
			
			$Tutorias = new Tutorias();
			if($tutorias = $Tutorias -> find("nomina = ".$nomina)){
				foreach($tutorias as $t){
					$Tutorias -> find_first("id = ".$t -> id);
					$Tutorias -> nomina = $nomina2;
					if($Tutorias -> save()){
						Flash::success("La tutoria del profesor ".$t -> nomina." con el alumno ".$Tutorias -> registro." se cambio por el profesor ".$nomina2."<br>");
					}else{
						Flash::error('No se pudo guardar el cambio de tutor para el alumno'.$Tutorias -> registro);
					}
				}
			}else{
				Flash::error('No hay alumnos con tutorias para este profesor');
			}
		}
		
		function eliminarAlumno($id,$vista){
			$this -> valida();
			$this -> set_response("view");
			$Tutorias = new Tutorias();
			$Tutorias -> find_first("id = ".$id);
			if($Tutorias -> delete()){
				Flash::success('Se borro el tutorado al alumno');
			}else{
				Flash::error('ERROR No se pudo eliminar');
			}	
			if ($vista == 1){
				$this -> render_partial('listado');
			}
			if($vista == 2){
				$this -> render_partial('listado2');
			}
		}
		
		function valida(){
			if(Session::get("tipousuario")=="TUTOR"){
				return true;
			}
			$this -> route_to("controller: general", "action: inicio");
			return true;
		}
		
		function busqueda(){
			$this -> valida();
		
		}
		
		function buscar(){
			$this -> valida();
			$this -> set_response("view");
			
			$nomina = $this -> post("profesorNomina");
			$this -> nomina = $this -> post("profesorNomina");
			$nombreProfesor = $this -> post("profesorNombre");
			
			$registro = $this -> post("alumnoRegistro");			
			$nombreAlumno = $this -> post("alumnoNombre");
			
			//echo "nomina = ".$nomina." nombre profe= ".$nombreProfesor."<br>";
			//echo "registro = ".$registro." nombre alumno= ".$nombreAlumno."<br>";
			
			$imprimirPartial=0;
			
			$Tutorias = new Tutorias();
			
			if($nomina != '' && $registro != ''){
				$this -> tutorias = $Tutorias -> find_first("nomina = ".$nomina." and registro = ".$registro);
			}else{			
				if($nomina != '' || $nombreProfesor != ''){
					if($nombreProfesor != ''){
						$Maestro = new Maestros();
						if($Maestro -> find_first("nombre like '%".$nombreProfesor."%'")){
							$this -> nomina = $Maestro -> nomina;
							$nomina = $Maestro -> nomina;
							echo "Se encontro al profesor:<h3>".$Maestro -> nombre."</h3>";
							if($this -> tutorias = $Tutorias -> find("nomina = ".$nomina)){
								$imprimirPartial=1;
							}else{
									Flash::error("No tiene Tutorias Asignadas");
							}
						}else{
							Flash::error("no se pudo encontrar el nombre del Profesor");
							$imprimirPartial=0;
						}										
					}else{
						if($this -> tutorias = $Tutorias -> find("nomina = ".$nomina)){
							$imprimirPartial=1;
						}else{
								Flash::error("No tiene Tutorias Asignadas");
						}
					}
				}
				
				if($registro != '' || $nombreAlumno != ''){
					if($nombreAlumno != ''){
						$Alumno = new Alumnos();
						if($Alumno -> find_first("vcNomAlu like '%".$nombreAlumno."%'")){
							$registro = $Alumno -> miReg;
							echo "Se encontro al alumno: <h3>".$Alumno -> miReg." - ".$Alumno -> vcNomAlu."</h3>";
							if($this -> tutorias = $Tutorias -> find_first("registro = ".$registro)){
								$this -> nomina = $Tutorias -> nomina;
								$imprimirPartial=1;
							}else{
									Flash::error("No tiene Tutor Asignado");
							}
						}else{
							Flash::error("no se pudo encontrar el nombre del alumno");
						}									
					}else{
						if($this -> tutorias = $Tutorias -> find_first("registro = ".$registro)){
							$imprimirPartial=1;
							$this -> nomina = $Tutorias -> nomina;
						}else{
								Flash::error("No tiene Tutor Asignado");
						}
					}
				}
			}
			//echo $imprimirPartial." nomina ".$nomina;
			if ($imprimirPartial==1){
				$this -> render_partial('listado2');
			}
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("tipousuario","");
			
			$this -> redirect("general/inicio");
		}
		
		function info(){
			echo phpinfo();
		}
	}
?>