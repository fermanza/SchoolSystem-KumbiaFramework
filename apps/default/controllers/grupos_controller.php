<?php			
	class GruposController extends ApplicationController {
		
		function asignarMaterias($semestre = 2, $carrera = 200, $turno = 'M'){
			$Alumnos = new Alumnos();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			$Horarios = new Horarios();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			if ($semestre != 0){
				if($alumnos = $Alumnos -> find("semestre = ".$semestre." AND (tipo = 'R' or tipo = 'I') AND estado = 'OK' AND especialidad = ".$carrera." and turno = '".$turno."'")){
					foreach($alumnos as $a){
						if($grupoTronco = $Grupos -> find_first("nivel = ".$a -> semestre." and nombre like '%".$a->semestre.substr($a -> grupo,0,1)."%' and periodo = ".$periodo." and especialidad_id = 999 and turno = '".$turno."'")){
							if($grupoEsp = $Grupos -> find_first("nivel = ".$a -> semestre." and nombre like '%".$a->semestre.$a -> grupo."%' and periodo = ".$periodo." and especialidad_id = ".$a -> especialidad." and turno = '".$turno."'")){										
								echo '</tr>';
								echo '<tr>';
								echo '<td>'.$grupoTronco -> nombre.'</td><td>'.$grupoEsp -> nombre.'</td>';
								if($aCargar = $Horarios -> find("(grupo_id = ".$grupoTronco -> id." or grupo_id = ".$grupoEsp -> id.") and periodo = ".$periodo)){																								
									foreach($aCargar as $ac){ 
										$Calificaciones = new Calificaciones();
										if($Calificaciones -> find_first("horario_id = ".$ac -> id." and registro = ".$a -> registro)){
										
										}else{
											$Calificaciones = new Calificaciones();
										}
										$Calificaciones -> registro = $a -> registro;
										$Calificaciones -> periodo = $periodo;
										$Calificaciones -> horario_id = $ac -> id;
										if($Calificaciones -> save()){
											echo '</tr>';
											echo '<tr>';
											echo '<td colspan="3">Se guardo el acta '.$ac -> id.' al registro '.$a -> registro.'<td>';
										}else{
											echo '</tr>';
											echo '<tr>';
											echo '<td colspan="3">No se pudo guardar<td>';
										}
									}
								}
							}
						}
					}
				}
			}else{
				$this -> set_response('view');
				echo "coloca lo que falta en el link segun corresponda..   grupos/asignarMaterias/1/200/M   primer valor Semestre, Especialidad, Turno";
			}
		}
						
		function asignarGrupo($carrera = 200, $turno = "M"){
			$Alumnos = new Alumnos();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			$Horarios = new Horarios();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			for($semestre = 1; $semestre <9; $semestre++){
				$calumnos = 0;
				$cgrupos = 0;
				if($calumnos = $Alumnos -> count("semestre = ".$semestre." AND (tipo = 'R' or tipo = 'I') AND estado = 'OK' AND especialidad = ".$carrera." and turno = '".$turno."'")){
					if($calumnos > 0){
						if($cgrupos = $Grupos -> count("especialidad_id = ".$carrera." and nivel = ".$semestre."  and periodo = ".$periodo." and turno = '".$turno."'")){
							if($cgrupos > 0){
								echo "Alumnos: ".$calumnos." tipo = 'NORMAL', Grupos:  and ".$cgrupos." Semestre: ".$semestre." Carrera: ".$carrera." Cuantos X grupo: ";
								$cuantosXgrupo= round($calumnos/$cgrupos);
								echo $cuantosXgrupo;
								echo '<center><table><tr><th>No.</th><th>No. Lista</th><th>Registro</th><th>Grupo</th><th>Grupo Anterior</th></tr>';
								$cont=0;
								$contGrupo=0;							
								$grupos = $Grupos -> distinct("nombre","conditions: tipo = 'NORMAL' and especialidad_id = ".$carrera." and nivel = ".$semestre."  and periodo = ".$periodo." and turno = '".$turno."'", "order: nombre ASC");
								foreach($grupos as $g){
									$alumnos = $Alumnos -> find("semestre = ".$semestre." AND (tipo = 'R' or tipo = 'I') AND estado = 'OK' AND especialidad = ".$carrera." and turno = '".$turno."' ORDER BY grupo ASC, registro ASC LIMIT ".$cont.", ".$cuantosXgrupo);
									foreach($alumnos as $a){											
										$contGrupo++;
										$cont++;
										echo tr_color('#bbbbbb','#dddddd');
										$Alumno = new Alumnos();
										if($Alumno -> find_first("registro = ".$a -> registro)){
											$Alumno -> grupo = substr($g,1,2);
											if($Alumno -> save()){
											}else{										
												echo "no se pudo cambiar el grupo";
											}									
										}
										echo '<td>'.$cont.'</td><td>'.$contGrupo.'</td><td>'.$a -> registro.'</td><td>'.substr($g,1,2).'</td><td>'.$a -> grupo_anterior.'</td>';																		
									}
									$contGrupo=0;
								}
								echo '</table></center>';
								
							}else{
								echo 'Hay 0 grupos de esta carrera '.$carrera.' en este semestre'.$semestre;
							}
						}else{
							echo 'No hay grupos';
						}				
					}else{
						echo 'Hay 0 alumnos de esta carrera '.$carrera.' en este semestre'.$semestre;
					}
				}else{
					echo 'No se encontraron alumnos';
				}			
			}
		}
		
		function asignarGrupo1($carrera = 200, $turno = "M"){
			$Alumnos = new Alumnos();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			$Horarios = new Horarios();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			$semestre = 1;
				$calumnos = 0;
				$cgrupos = 0;
				if($calumnos = $Alumnos -> count("semestre = ".$semestre." AND (tipo = 'R' or tipo = 'I') AND estado = 'OK' AND especialidad = ".$carrera." and turno = '".$turno."'")){
					if($calumnos > 0){
						if($cgrupos = $Grupos -> count("especialidad_id = ".$carrera." and nivel = ".$semestre."  and periodo = ".$periodo." and turno = '".$turno."'")){
							if($cgrupos > 0){
								echo "Alumnos: ".$calumnos." tipo = 'NORMAL', Grupos:  and ".$cgrupos." Semestre: ".$semestre." Carrera: ".$carrera." Cuantos X grupo: ";
								$cuantosXgrupo= round($calumnos/$cgrupos);
								echo $cuantosXgrupo;
								echo '<center><table><tr><th>No.</th><th>No. Lista</th><th>Registro</th><th>Grupo</th><th>Grupo Anterior</th></tr>';
								$cont=0;
								$contGrupo=0;							
								$grupos = $Grupos -> distinct("nombre","conditions: tipo = 'NORMAL' and especialidad_id = ".$carrera." and nivel = ".$semestre."  and periodo = ".$periodo." and turno = '".$turno."'", "order: nombre ASC");
								foreach($grupos as $g){
									$alumnos = $Alumnos -> find("semestre = ".$semestre." AND (tipo = 'R' or tipo = 'I') AND estado = 'OK' AND especialidad = ".$carrera." and turno = '".$turno."' ORDER BY grupo ASC, registro ASC LIMIT ".$cont.", ".$cuantosXgrupo);
									foreach($alumnos as $a){											
										$contGrupo++;
										$cont++;
										echo tr_color('#bbbbbb','#dddddd');
										$Alumno = new Alumnos();
										if($Alumno -> find_first("registro = ".$a -> registro)){
											$Alumno -> grupo = substr($g,1,2);
											if($Alumno -> save()){
											}else{										
												echo "no se pudo cambiar el grupo";
											}									
										}
										echo '<td>'.$cont.'</td><td>'.$contGrupo.'</td><td>'.$a -> registro.'</td><td>'.substr($g,1,2).'</td><td>'.$a -> grupo_anterior.'</td>';																		
									}
									$contGrupo=0;
								}
								echo '</table></center>';
								
							}else{
								echo 'Hay 0 grupos de esta carrera '.$carrera.' en este semestre'.$semestre;
							}
						}else{
							echo 'No hay grupos';
						}				
					}else{
						echo 'Hay 0 alumnos de esta carrera '.$carrera.' en este semestre'.$semestre;
					}
				}else{
					echo 'No se encontraron alumnos';
				}			
			
		}
		
		
		
		function reasignar($nivel,$inicio,$final){
			?>
			<div style="font-size:10px;">			
			<?php
			echo '<h1>Alumnos Regulares del Semestre '.$nivel.'</h1>';
			$Alumnos = new Alumnos();
			$Calificaciones = new Calificaciones();
			$CalificacionesN = new Calificaciones();
			$Horarios = new Horarios();
			$Grupos = new Grupos();
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){			
				$periodo = $Periodo -> periodo;
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			$cont=1;
			
			$alumnos = $Alumnos -> find("semestre = ".$nivel." and tipo = 'R' and modificado = 1 LIMIT ".$inicio.",".$final);
			
			foreach($alumnos as $a){
				$grupoEspecialidad = $Grupos -> find_first("nombre = '".$nivel.$a -> grupo."' AND periodo = ".$periodo);
				$grupoParcial = $Grupos -> find_first("nombre = '".$nivel.substr($a -> grupo, 0,1)."' AND periodo = ".$periodo);
				echo "<br>".$cont++." - <b>".$a -> registro." - ".$a -> nombre_completo."</b> ".$grupoEspecialidad -> nombre." ".$grupoParcial -> nombre."<br>";
				
				$horariosEspecialidad = $Horarios -> find("grupo_id = ".$grupoEspecialidad -> id." and periodo =".$periodo." ORDER BY id");				
				foreach($horariosEspecialidad as $he){
					if($Calificaciones -> find_first("horario_id = ".$he -> id." and registro = ".$a->registro." and periodo = ".$periodo)){
						$estado ='<span style="color: #00EE00" >SI</span>';
						$seAsigno="";
					}else{
						$estado='<span style="color: #EE0000" >NO</span>';						
						$seAsigno=", Se asigno la acta numero =".$he -> id." materia_id = ".$he -> materia_id;
						$CalificacionesN = new Calificaciones();
						$CalificacionesN -> horario_id = $he -> id;
						$CalificacionesN -> registro = $a-> registro;
						/*if($CalificacionesN -> save())
							$seAsigno=$seAsigno." - BIEN HECHO";
						else{
							$seAsigno=$seAsigno." - MAL HECHO";
						}*/					
					}
					echo $he -> id." - ".$estado." ".$seAsigno."<br>";
				}
				echo "---------<br>";
				
				$horariosParcial = $Horarios -> find("grupo_id = ".$grupoParcial -> id."  ORDER BY id");
				foreach($horariosParcial as $hp){
					if($Calificaciones -> find_first("horario_id = ".$hp -> id." and registro = ".$a->registro." and periodo = ".$periodo)){
						$estado ='<span style="color: #00EE00" >SI</span>';
						$seAsigno="";
					}else{
						$estado='<span style="color: #EE0000" >NO</span>';
						$seAsigno=", Se asigno la acta numero =".$hp -> id." materia_id = ".$hp -> materia_id;
						$CalificacionesN = new Calificaciones();
						$CalificacionesN -> horario_id = $hp -> id;
						$CalificacionesN -> registro = $a-> registro;
						/*if($CalificacionesN -> save())
							$seAsigno=$seAsigno." - BIEN HECHO";
						else{
							$seAsigno=$seAsigno." - MAL HECHO";
						}*/
					}
					echo $hp -> id." ".$estado." ".$seAsigno."<br>";
				}
			}
			?>
			</div>
			<?php
		}
	}
?>
	
	
	