<?php
			
	class TgotutorController extends ApplicationController {
		
		function index(){
			
		}	
		
		function estadisticas(){
			
		}
		
		function reporteProfesores(){
			$this -> set_response("view");
			$this -> render_partial("listadoProfesores");
		}
		
		function reporteProfesoresExcel(){
			$this -> set_response("view");
			header("content-disposition: attachment;filename=programa_trabajo_academico.xls"); 
			$this -> render_partial("listadoProfesores");
		}
		
		function asignacion(){
			$Maestros = new Maestros();
			$Alumnos = new Alumnos();
			
			$this -> maestros = $Maestros -> find("order: id ASC");
			$this -> alumnos = $Alumnos -> find("estado = 'OK' or estado = 'OK'", "order: registro ASC");
		}
		
		function asignar(){
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
				if($Tutorias -> save()){
					Flash::success('Operaci&oacute;n exitosa - Se guardo el tutorado');
				}
			}
			$this -> render_partial('listado');
		}
		
		function cambiarTutorados(){
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
			if(Session::get("tipo")=="TUTOR"){
				return true;
			}
			//$this -> redirect("general/ingresar");
			return true;
		}
		
		function busqueda(){
		
		}
		
		function buscar(){
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
							$nomina = $Maestro -> id;
							echo "entro";
						}else{
							Flash::error("no se pudo encontrar el nombre del Profesor");
						}										
					}
					if($this -> tutorias = $Tutorias -> find("nomina = ".$nomina)){
						$imprimirPartial=1;
					}else{
							Flash::error("No tiene Tutorias Asignadas");
					}
				}
				
				if($registro != '' || $nombreAlumno != ''){
					if($nombreAlumno != ''){
						$Alumno = new Alumnos();
						if($Alumno -> find_first("nombre_completo like '%".$nombreAlumno."%'")){
							$registro = $Alumno -> registro;							
						}else{
							Flash::error("no se pudo encontrar el nombre del alumno");
						}									
					}
					if($this -> tutorias = $Tutorias -> find_first("registro = ".$registro)){
						$this -> nomina = $Tutorias -> nomina;
						$imprimirPartial=1;
					}else{
							Flash::error("No tiene Tutor Asignado");
					}
				}
			}
			//echo $imprimirPartial." nomina ".$nomina;
			if ($imprimirPartial==1){
				$this -> render_partial('listado2');
			}
		}
		
		function prueba($xls=0){	
			$this -> set_response("view");
			if ($xls == 1){
				header("content-disposition: attachment;filename=programa_trabajo_academico.xls");
			}
			$TutoriasNivelGrupo = new TutoriasNivelGrupo();			
			$tng = $TutoriasNivelGrupo -> find_all_by_sql("SELECT * FROM tutorias_nivel_grupo");			
			echo '<center><table style="font-size:11px;" class="bordeAzul">';
			echo '<tr class="NARANJA">';
				echo '<th colspan="5">REPORTE DE NUMERO DE TUTORADOS DE PROFESORES POR GRUPOS</th>';				
			echo "</tr>";
			echo '<tr class="azul">';
				echo "<th>NOMINA</th>";
				echo "<th>NOMBRE</th>";
				echo "<th>SEMESTRE</th>";
				echo "<th>GRUPO</th>";
				echo "<th>NO. DE ALUMNOS</th>";
			echo "</tr>";
			foreach($tng as $t){
				$cont++;
				if ($cont%2){
					echo '<tr class="grisClaro">';
				}else{
					echo "<tr>";
				}				
					echo "<td>".$t -> id."</td>";
					if ($xls == 1){
						echo "<td>".$t -> nombre."</td>";
					}else{
						echo utf8_decode("<td>".$t -> nombre."</td>");
					}
					echo "<td>".$t -> semestre."</td>";
					echo "<td>".$t -> grupo."</td>";
					echo "<td>".$t -> cuantos."</td>";			
				echo "</tr>";
			}
			echo "</table></center>";
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			
			$this -> redirect("general/ingresar");
		}
		
		function info(){
			echo phpinfo();
		}
	}
?>