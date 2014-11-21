<?php
			
	class IrregularController extends ApplicationController {
		function asignarIrregular1(){
			echo 'hasta el lunes esta disponible esta seccion, cualquier duda hablarle a vega por favor';
		}
	
		function asignarIrregular($registro, $merror = 0){
			$this -> valida();
			$Alumnos = new Alumnos ();
			$Grupos = new Grupos();	
			$this -> registro = $registro;
			$this -> merror = $merror;
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> alumno = $Alumnos -> find_first("registro = ".$registro);			
			$this -> grupos = $Grupos -> find("(especialidad_id = 999 or especialidad_id = ".$this -> alumno -> especialidad.") AND periodo = ".$periodo." ORDER BY nombre ASC");								
		}
		
		function materiasIrregular(){
			$this -> set_response("view");
			$Horarios = new Horarios();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> grupo =  $this -> post("grupoSeleccionado");
			$this -> registro =  $this -> post("registro");
			$this -> horarios = $Horarios -> find("grupo_id = ".$this -> grupo." and periodo = ".$periodo);			
											
			$this -> render_partial("materias");
		}
		
		function agregarMateria(){
			$this -> set_response("view");
			$horarioId =  $this -> post("horarioId");
			$registro =  $this -> post("registro");
			$tipo =  $this -> post("tipo");			
			
			$Calificaciones = new Calificaciones ();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$Calificaciones -> registro = $registro;
			$Calificaciones -> periodo = $periodo;
			$Calificaciones -> horario_id = $horarioId;					
			$Calificaciones -> tipo = $tipo;
			
			if ($Calificaciones -> save()){
				Flash::success("Se agrego la materia ".$horarioId." - usuario ".$registro);
			}else{
				Flash::error("No se pudo Agregar");
			}	
			$this->render_partial("listadoMaterias");
		}
		
		
		function eliminarMateria(){
			$this -> set_response("view");
			$horarioId =  $this -> post("horarioId");
			$registro =  $this -> post("registro");			
			$this -> registro= $registro;
			$Calificaciones = new Calificaciones ();
			
			$Calificaciones -> find_first("horario_id = ".$horarioId." and registro = ".$registro);			
			
			if ($Calificaciones -> delete()){
				Flash::success("Se Elimino la materia ".$horarioId." - usuario ".$registro);
			}else{
				Flash::error("No se pudo eliminar");
			}	
			$this->render_partial("listadoMaterias");
		}
		
		function horarios($profesor = ""){
			$this -> limpiarHorario();									
			$this -> turno = $turno;
			
			$this  -> propietario = $maestro -> nombre;			
		}
		
		function valida(){
			if((Session::get("tipo")=="COORDINADOR")  || (Session::get("tipo")=="CALCULO")){
				return true;
			}
			$this -> redirect("general/ingresar");
			return true;
		}
	}
	
?>
