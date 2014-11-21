<?php
			
	class XController extends ApplicationController {
	
		function y(){
			echo date("d/m/Y (H:i)",mktime(7,0,0,6,23,2008))." - ".mktime(9,0,0,6,23,2008)."<br>";
			echo date("d/m/Y (H:i)",mktime(22,0,0,6,30,2008))." - ".mktime(22,0,0,6,30,2008)."<br>";
		}
		
		function fecha($d=0,$m=0,$y=0,$h=0,$i=0,$s=0){
			if($d==0 || $m==0 || $y==0){
				echo date("d/m/Y (H:i)",time())." - ".time()."<br>";
			}
			else{
				echo date("d/m/Y (H:i)",mktime($h,$i,$s,$m,$d,$y))." - ".mktime($h,$i,$s,$m,$d,$y)."<br>";
			}
		}
	
		function x(){
			
			unset($calificaciones);
			unset($cursitos);
			
			$calificaciones = new Xcalificacion();
			$cursitos = new Registracursos();
			
			$si = 0; $no = 0;
			
			foreach($cursitos -> find_all_by_sql("SELECT * FROM registracursos WHERE id>4000 AND id<=6000 ORDER BY mireg") as $alumno){
				$numero = $calificaciones -> count("mireg=".$alumno -> mireg." AND clavecurso='".$alumno -> curso."'");

				if($numero==0){
					echo "INSERT INTO xcalificacion(mireg,clavecurso) VALUES(".$alumno -> mireg.",'".$alumno -> curso."');";
					echo "<br>";
					$no++;
				}
			}
			/*
			echo "<br>";
			foreach($cursitos -> find_all_by_sql("SELECT * FROM registracursos WHERE id>2020 AND id<=4000 ORDER BY mireg") as $alumno){
				$numero = $calificaciones -> count("mireg=".$alumno -> mireg." AND clavecurso='".$alumno -> curso."'");

				if($numero==0){
					$no++;
				}
				else{
					$si++;
				}
			}
			
			*/
			/*
			echo "<br>";
			foreach($cursitos -> find_all_by_sql("SELECT * FROM registracursos WHERE id>4000 AND id<=6000 ORDER BY mireg") as $alumno){
				$numero = $calificaciones -> count("mireg=".$alumno -> mireg." AND clavecurso='".$alumno -> curso."'");

				if($numero==0){
					$no++;
				}
				else{
					$si++;
				}
			}
			echo "<br>";
			foreach($cursitos -> find_all_by_sql("SELECT * FROM registracursos WHERE id>6000 AND id<=8000 ORDER BY mireg") as $alumno){
				$numero = $calificaciones -> count("mireg=".$alumno -> mireg." AND clavecurso='".$alumno -> curso."'");

				if($numero==0){
					$no++;
				}
				else{
					$si++;
				}
			}
			echo "<br>";
			foreach($cursitos -> find_all_by_sql("SELECT * FROM registracursos WHERE id>8000 AND id<=10000 ORDER BY mireg") as $alumno){
				$numero = $calificaciones -> count("mireg=".$alumno -> mireg." AND clavecurso='".$alumno -> curso."'");

				if($numero==0){
					$no++;
				}
				else{
					$si++;
				}
			}
			*/
			echo "<br>";
			echo $no;
			
			
			
		}
	
		function reparador(){
			print "HOLA";
			$calificaciones = new Xcalificacion();
			$cursitos = new Registracursos();
			
			$si = 0; $no = 0;
			
			foreach($cursitos -> find_all_by_sql("SELECT mireg FROM registracursos") as $registro){
				$mireg = $registro -> mireg;
				$curso = $registro -> curso;
				
				foreach($calificaciones -> find_all_by_sql("SELECT * FROM xcalificacion WHERE clavecurso='".$curso."' AND mireg=".$mireg." ORDER BY id") as $tmp){
					if($tmp){
						$si++;
					}
					else{
						$no++;
					}
				}
				
				
				
			}
			echo "SI: ".$si."<br>";
			print "NO:".$no;
		}
	}
?>