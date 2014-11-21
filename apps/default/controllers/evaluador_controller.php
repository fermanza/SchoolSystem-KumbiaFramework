<?php
			
	class EvaluadorController extends ApplicationController {
	
		function reprobados($periodo = "32008", $limite=0){
			
			$califing = new Califing();
			$xcalificacion = new Xcalificacion();
			$horarios = new Horarios();
			$mishorarios = new Mishorarios();
			$examenes = new Codexttit();
			$kardex = new KardexIng();
			$profavance = new Profavance();

			$calificaciones = $xcalificacion -> find("periodo=".$periodo." ORDER BY mireg LIMIT $limite,500");
			echo "<table border=\"0\" width=\"800\">";
			$i=0;
			
			if($calificaciones) foreach($calificaciones as $calificacion){
				$horario = $mishorarios -> find_first("clavecurso='".$calificacion -> clavecurso."'");
					
				if($calificacion -> cal1 != 300 && $calificacion -> cal2 != 300 && $calificacion -> cal3 != 300){
					$xc1 = $calificacion -> cal1;
					$xc2 = $calificacion -> cal2;
					$xc3 = $calificacion -> cal3;
									
					if($xc1>100) $xc1 = 0;
					if($xc2>100) $xc2 = 0;
					if($xc3>100) $xc3 = 0;
				
					$promedio = round(($xc1 + $xc2 + $xc3)/3);
				
					if($promedio<70){
						$faltas = $calificacion -> falt1 + $calificacion -> falt2 + $calificacion -> falt3;
						$faltas += 0;
						if($calificacion -> situacion == "" || $calificacion -> situacion == "-"){
							echo "<tr><td><font color=\"#FF0000\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><font color=\"#FF0000\">EXTRAORDINARIO CALIFICACION</td></tr></b>";
							$x++;
							$calificacion -> situacion = "EXTRAORDINARIO POR CALIFICACION";
							$calificacion -> falta = $faltas;
							$calificacion -> califnal = $promedio;
							$calificacion -> save();
						}
						else{
							echo "<tr><td><b><font color=\"#FF0000\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><b><font color=\"#FF0000\">".$calificacion -> situacion."</td></tr></b>";
						}
					}
					else{
						$faltas = $calificacion -> falt1 + $calificacion -> falt2 + $calificacion -> falt3;
						$faltas += 0;
						$avance = $profavance -> find_first("id='".$calificacion -> clavecurso."'");
						$faltastotales = $avance -> horas1 + $avance -> horas2 + $avance -> horas3;
					
						if($faltas > $faltastotales * 0.20){
							if($faltas <= $faltastotales * 0.40){
								if($calificacion -> situacion == "" || $calificacion -> situacion == "-"){
									echo "<tr><td><font color=\"#FF00FF\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)." | FALTAS: ".$faltas."$".$faltastotales."</td><td><font color=\"#FF00FF\">EXTRAORDINARIO FALTAS</td></tr></b>";
									$calificacion -> situacion = "EXTRAORDINARIO POR FALTAS";
									$calificacion -> falta = $faltas;
									$calificacion -> califnal = $promedio;
									$calificacion -> save();
								}
								else{
									echo "<tr><td><b><font color=\"#FF00FF\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><b><font color=\"#FF00FF\">".$calificacion -> situacion."</td></tr></b>";
								}
							}
							else{
								if($calificacion -> situacion == "" || $calificacion -> situacion == "-"){
									echo "<tr><td><font color=\"#FF00FF\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)." | FALTAS: ".$faltas."$".$faltastotales."</td><td><font color=\"#FF00FF\">REGULARIZACION</td></tr></b>";
									$calificacion -> situacion = "REGULARIZACION DIRECTA";
									$calificacion -> falta = $faltas;
									$calificacion -> califnal = $promedio;
									$calificacion -> save();
								}
								else{
									echo "<tr><td><b><font color=\"#FF00FF\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><b><font color=\"#FF00FF\">".$calificacion -> situacion."</td></tr></b>";
								}
							}
						}
						else{
							if($calificacion -> situacion == "" || $calificacion -> situacion == "-"){
								echo "<tr><td><b><font color=\"#0000FF\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><b><font color=\"#0000FF\">ORDINARIO</td></tr></b>";
								$calificacion -> situacion = "ORDINARIO";
								$calificacion -> falta = $faltas;
								$calificacion -> califnal = $promedio;
								$calificacion -> save();
							}
							else{
								echo "<tr><td><b><font color=\"#0000FF\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)."</td><td><b><font color=\"#0000FF\">".$calificacion -> situacion."</td></tr></b>";
							}
						}
					}
				}
				else{
					echo "<tr><td><b><font color=\"#00FF00\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clavemat ." | ORDINARIO:".($promedio)." | PENDIENTE(".$calificacion -> cal1."|".$calificacion -> cal2."|".$calificacion -> cal3.")</td><td><b><font color=\"#00FF00\">PENDIENTE</td></tr></b>";
				}
			}
			
			echo "</table>";
		}
	
		function titulos($periodo = "32007", $limite=0){
			
			$califing = new Califing();
			$xcalificacion = new Xcalificacion();
			$horarios = new Horarios();
			$mishorarios = new Mishorarios();
			$examenes = new Codexttit();
			$kardex = new KardexIng();
			$profavance = new Profavance();
			$alumnos = new Alumnos();

			$calificaciones = $califing -> find("periodo=".$periodo." AND cal1!=300 AND cal2!=300 AND cal3!=300 ORDER BY mireg LIMIT $limite,600");
			echo "<table border=\"0\" width=\"800\">";
			$i=0;$xxx=0;
			if($calificaciones) foreach($calificaciones as $calificacion){
				if($calificacion -> cal1>100) $calificacion -> cal1 = 0;
				if($calificacion -> cal2>100) $calificacion -> cal2 = 0;
				if($calificacion -> cal3>100) $calificacion -> cal3 = 0;
				
				$promedio = round(($calificacion -> cal1 + $calificacion -> cal2 + $calificacion -> cal3)/3);
				
				if($promedio<70){
					$horario = $horarios -> find_first("id=".$calificacion -> curso);

					$examen = $examenes -> find_first("(examen='E' OR examen='T') AND estatus='Ok' AND idmicoe=".$calificacion -> curso." AND mireg=".$calificacion -> mireg);
					if($examen -> sical==""){
						$examen -> sical = "NP";
					}
					unset($promedio2);
					
					
					if($examen -> sical<70){
						$xcalificaciones = $xcalificacion -> find("mireg=".$calificacion -> mireg);
					
						$bandera=0;
						if($xcalificaciones) foreach($xcalificaciones as $xcal){ $faltastotales=0; $faltas=0;
							unset($mihorario);
							
							
							$mihorario = $mishorarios -> find_first("clavecurso='".$xcal -> clavecurso."'");
							if(trim($horario -> clave) == trim($mihorario -> clavemat)){
								$bandera=1;
								
								if($xcal -> cal1 != 300 && $xcal -> cal2 != 300 && $xcal -> cal3 != 300){
								
									$xc1 = $xcal -> cal1;
									$xc2 = $xcal -> cal2;
									$xc3 = $xcal -> cal3;
									
									if($xc1>100) $xc1 = 0;
									if($xc2>100) $xc2 = 0;
									if($xc3>100) $xc3 = 0;
								
									$faltas = $xcal -> falt1 + $xcal -> falt2 + $xcal -> falt3;
									$avance = $profavance -> find_first("id='".$xcal -> clavecurso."'");
									$faltastotales = $avance -> horas1 + $avance -> horas2 + $avance -> horas3;
									
									$promedio2 = round(($xc1 + $xc2 + $xc3)/3);
									if($promedio2 >= 70){
										if($faltas > $faltastotales * 0.40){
											echo "<tr><td>".$xxx."<font color=\"#FF00FF\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: ".$promedio2." | FALTAS: ".$faltas."$".floor($faltastotales)."</td><td><font color=\"#FF00FF\">TITULO FALTAS</td></tr></b>";
											$xcal -> situacion = "TITULO POR FALTAS";
											$xcal -> falta = $faltas;
											$xcal -> califnal = $promedio2;
											$xcal -> save();
											$xxx++;
										}
										else{
											echo "<tr><td>".$xxx."<font color=\"#0000FF\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: ".$promedio2."</td><td><font color=\"#0000FF\">REGULARIZACION</td></tr></b>";
											$x++;
											$xcal -> situacion = "REGULARIZACION";
											$xcal -> falta = $faltas;
											$xcal -> califnal = $promedio2;
											$xcal -> save();
											$xxx++;
										}
									}
									else{
										if($faltas <= $faltastotales * 0.40){
											echo "<tr><td>".$xxx."<font color=\"#FF0000\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: ".$promedio2."</td><td><font color=\"#FF0000\">TITULO CALIFICACION</td></tr></b>";
											$z++;
											$xcal -> situacion = "TITULO POR CALIFICACION";
											$xcal -> falta = $faltas;
											$xcal -> califnal = $promedio2;
											$xcal -> save();
											$xxx++;
										}
										else{
											echo "<tr><td>".$xxx."<b><font color=\"#FF0000\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: ".$promedio2." | FALTAS: ".$faltas."$".floor($faltastotales)."</td><td><b><font color=\"#FF0000\">BAJA DEFINITIVA</td></tr></b>";
											$xcal -> situacion = "BAJA DEFINITIVA";
											$xcal -> falta = $faltas;
											$xcal -> califnal = $promedio2;
											$xcal -> save();
											$xxx++;
										}
									}
								}
								else{
									echo "<tr><td>".$xxx."<font color=\"#00FF00\"><b>REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: PENDIENTE (".$xcal -> cal1."|".$xcal -> cal2."|".$xcal -> cal3.")</td><td><font color=\"#00FF00\">PENDIENTE</td></tr></b>";
									$y++;
								}
								break;
							}
						}
							
							$n = $kardex -> count("registro=".$calificacion -> mireg." AND clavemat='".$horario -> clave."'");
							if($n==0 && $bandera==0){
								
								$tmp = $alumnos -> find_first("mireg=".$calificacion -> mireg);
								
								if($tmp -> stSit != "BD"){
								
								echo "<tr><td><b>".$xxx."<font color=\"#660000\">REG: ".$calificacion -> mireg." | MAT: ".$horario -> clave ." | ORDINARIO:".($promedio)." | EXTRAORDINARIO: ".$examen -> sical." | REGULARIZACION: NP</td><td><b><font color=\"#660000\">TITULO REGISTRO</td></tr></b>";
								$z++;
								
								$mihorario = $mishorarios -> find_first("clavemat='".$horario -> clave."'");
								
								$xcal = new Xcalificacion();
								$xcal -> periodo = 32008;
								if($mihorario -> clavecurso)
									$xcal -> clavecurso = $mihorario -> clavecurso;
								else
									$xcal -> clavecurso = "-";
								$xcal -> claveextra = $horario -> clave;
								$xcal -> mireg = $calificacion -> mireg;
								$xcal -> falt1 = 0;
								$xcal -> cal1 = 0;
								$xcal -> falt2 = 0;
								$xcal -> cal2 = 0;
								$xcal -> falt3 = 0;
								$xcal -> cal3 = 0;
								$xcal -> falta = 0;
								$xcal -> califnal = 0;
								$xcal -> situacion = "TITULO POR NO REGISTRO";
								$xcal -> create();
								$xxx++;
								}
							}
					}
				}
			}
			echo "</table>";
		}
	}
?>
