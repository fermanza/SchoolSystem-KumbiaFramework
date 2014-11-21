<?php
			
	class GrupoController extends ApplicationController {

		/*
		function reasignar($nivel){
			$Alumnos = new Alumnos();
			
			$alumnos = $Alumnos -> find("nivel = ".$nivel." and estado = 'R'");
			echo "jajaja";
			foreach($alumnos as $a){
				echo $a -> nombre_completo."<br>";
			}
		
		}*/
		
		function ver($esp){	
			$op = 0;
			$this -> valida();
			switch ($esp){
				case 110: $op = 1; break;
				case 160: $op = 1; break; 
				case 200:  $op = 2; break;
				case 400:  $op = 4; break;
				case 500:  $op = 5; break;
				case 600:  $op = 6; break;
				case 700:  $op = 7; break;
				case 800:  $op = 8; break;
				case 801:  $op = 8; break;
				case 804:  $op = 8; break;
			}
			
			
			$Materias = new MateriasHorarios();
			$Horarios = new Horarios();
			$Grupos = new Grupos();
			$Calificaciones = new Calificaciones();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$materias = $Materias -> find("1 ORDER BY nombre ASC");						
			echo '<center><table class="bordeazul" style="font-size:12px;">';
			echo '<tr class="naranja">';			
				echo '<th>Acta</th>';
				echo '<th>Nombre</th>';
				echo '<th>Grupo</th>';
				echo '<th>No. De Alumnos</th>';
			echo '</tr>';
			foreach($materias as $m){				
				if (substr($m -> clave,0,1) == $op){					
					$horarios = $Horarios -> find("materia_id = ".$m -> id." and periodo = ".$periodo);
					foreach($horarios as $h){
						$Grupos -> find_first("id = ".$h -> grupo_id." AND periodo = ".$periodo);
						$contador = $Calificaciones -> count("horario_id = ".$h -> id." and periodo = ".$periodo);
						echo tr_color('#CCDEFF', '#FFFFFF');
						echo '<td>'.$h -> id.'</td>';
						echo '<td><b>'.$m -> nombre.'</b></td>';
						echo '<td>'.$Grupos -> nombre.'</td>';
						echo '<td><b>'.$contador.'</b></td>';
					}
				}
			}
			echo '<table></center>';
			
		}
		
		function vergrupo($grupo=0,$actas=0){
			$materias = new MateriasHorarios();
			$grupos = new Grupos();
			$salones = new Salones();
			$horarios = new Horarios();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> gpo = $grupo;
			
			$n = $grupos -> count("nombre='".$grupo."' AND periodo = ".$periodo);
			if($n<=0) return;
			
			$grupo = $grupos -> find_first("nombre='".$grupo."'");
			$this -> grupoid = $grupo -> id;
			
			if(strlen($grupo -> nombre)==3){
				$grupobasicas = $grupos -> find_first("nombre='".substr($grupo -> nombre,0,2)."' and periodo = ".$periodo);
				$this -> grupobasicasid = $grupobasicas -> id;
			}
			
			$especialidades = new Especialidades();
			$especialidad = $especialidades -> find_first("id=".$grupo -> especialidad_id);
			
			$this -> especialidad = $especialidad -> nombre;
			if(strlen($grupo -> nombre)==3){
				$horarios = $horarios -> find("(grupo_id=".$grupobasicas -> id." OR grupo_id=".$grupo -> id.") AND periodo=".$periodo);
			}
			else{
				$horarios = $horarios -> find("grupo_id=".$grupo -> id." AND periodo=".$periodo);
			}
			
			
			for($i=0;$i<200;$i++){
				$this -> horarios[$i] = "-";
			}
			
			$m=0;
			if($horarios) foreach($horarios as $horario){
				$materia = $materias -> find_first("id=".$horario -> materia_id);
				$this -> horarios[$horario -> auxiliar] = $materia -> nombre;
				$this -> actas[$m] = $horario -> id;
				$m++;
			}
			
			$xcursos = new Xcursos();
			$this -> xcurso = $xcursos -> count("grupo='".$grupo -> nombre."'");
			
			if($actas=="OK"){
				$this -> ok = "OK";
				$alumnos = new Alumnos();
				$this -> alumnos = $alumnos -> find("plantel='C' AND tipo='R' AND grupo='**' AND semestre=".substr($grupo -> nombre,0,1)." AND especialidad=".$grupo -> especialidad_id);
			}
		}
		
		function vergrupo2($grupo,$actas){
			$this -> valida();
			
			unset($this -> horarios);
			unset($this -> actas);
			$this -> grupobasicasid = 0;
			$this -> grupoid = 0;
			
			$materias = new MateriasHorarios();
			$grupos = new Grupos();
			$salones = new Salones();
			$horarios = new Horarios();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> gpo = $grupo;
			
			$n = $grupos -> count("nombre='".$grupo."' AND periodo = ".$periodo);
			if($n<=0) return;
			
			$grupo = $grupos -> find_first("nombre='".$grupo."' AND periodo = ".$periodo);
			$this -> grupoid = $grupo -> id;
			
			if(strlen($grupo -> nombre)==3){
				$grupobasicas = $grupos -> find_first("nombre='".substr($grupo -> nombre,0,2)."' AND periodo = ".$periodo);
				$this -> grupobasicasid = $grupobasicas -> id;
			}
			
			$especialidades = new Especialidades();
			$especialidad = $especialidades -> find_first("id=".$grupo -> especialidad_id);
			
			$this -> especialidad = $especialidad -> nombre;
			if(strlen($grupo -> nombre)==3){
				$horarios = $horarios -> find("(grupo_id=".$grupobasicas -> id." OR grupo_id=".$grupo -> id.") AND periodo =".$periodo);
			}
			else{
				$horarios = $horarios -> find("grupo_id=".$grupo -> id." AND periodo =".$periodo);
			}
						
			for($i=0;$i<200;$i++){
				$this -> horarios[$i] = "-";
			}			
			$m=0;
			if($horarios) foreach($horarios as $horario){
				$materia = $materias -> find_first("id=".$horario -> materia_id);
				$this -> horarios[$horario -> auxiliar] = $materia -> nombre;
				$this -> actas[$m] = $horario -> id;
				$m++;
			}			
			$xcursos = new Xcursos();
			$this -> xcurso = $xcursos -> count("grupo='".$grupo -> nombre."' and periodo = ".$periodo);
			
			if($actas=="OK"){
				$this -> ok = "OK";				
				$alumnos = new Alumnos();
				$this -> alumnos = $alumnos -> find("modificado=1 AND estado='OK' AND (tipo='R' OR tipo='I') AND grupo='**' AND semestre=".substr($grupo -> nombre,0,1)." AND especialidad=".$grupo -> especialidad_id." ORDER BY tipo ASC, registro ASC");
			}
		}
		
		
		function autorizar($grupo){
			$xcursos = new Xcursos();
			$xcursos -> grupo = $grupo;
			$xcursos -> save();
			
			$this -> redirect("grupo/vergrupo/".$grupo);
		}
		
		function cargaregular(){
			$alumnos = $_POST["alumnos"];
			$actas = $_POST["actas"];
			
			$xalumnos = new Alumnos();
			echo substr($_POST["grupo"],1)."<br><br>";
			if($alumnos) foreach($alumnos as $alumno){
				echo "<b>".$alumno.":</b>";
				$xalumno = $xalumnos -> find_first("registro=".$alumno);
				$xalumno -> grupo = substr($_POST["grupo"],1);
				$xalumno -> save();
				if($actas) foreach($actas as $acta){
					echo " ".$acta;
					$xcalificacion = new Calificaciones();
					$xcalificacion -> registro = $alumno;
					$xcalificacion -> horario_id = $acta;
					$xcalificacion -> save();
				}
				echo "<br>";
			}
		}
		
		function asignarIrregular($registro){
			$Alumnos = new Alumnos ();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$this -> alumno = $Alumnos -> find_first("registro = ".$registro);
			$this -> grupos = $Grupos -> find("(especialidad_id = 999 or especialidad_id = ".$this -> alumno -> especialidad.") AND periodo = ".$periodo." ORDER BY nombre ASC");
		}
		
		function materiasIrregular(){
			$this -> set_response("view");
			$ja =  $this -> post("grupoSel");
			echo "jajaja";
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
