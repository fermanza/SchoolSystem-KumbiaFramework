<?php
class ModificacionesController extends ApplicationController {
	function index(){
			
	}
	
	function alumnos(){
		$Alumnos = new Alumnos();
		$Alumnos2 = new Alumnos();
		echo "<center><h2>Modificaciones de la Tabla Alumnos a los registro > 910000 </h2></center>";
		
		foreach($Alumnos -> find("miReg > 910000 and miReg < 920000") as $a){
			if($Alumnos2 -> find_first("id = ".$a->id)){
				echo "SI ";
				$Alumnos2 -> miReg = substr($a -> miReg,0,3)."0".substr($a -> miReg,3,6);
				if ($Alumnos2 -> correo == ""){
					$Alumnos2 -> correo = "-";
				}
				$Alumnos2 -> save();
				echo $a -> id." - ".$a -> miReg." --> ";
				echo substr($a -> miReg,0,3)."0".substr($a -> miReg,3,6)."<br />";
			}
			else
				echo "NO ";			
		}
	}
	
	function xalumnocursos(){
		$Alumnos = new Xalumnocursos();
		$Alumnos2 = new Xalumnocursos();
		echo "<center><h2>Modificaciones de la Tabla > Xalumnocursos a los registro > 910000 </h2></center>";
		
		foreach($Alumnos -> find("registro > 910000 and registro < 920000") as $a){
			if($Alumnos2 -> find_first("id = ".$a->id)){
				echo "SI ";
				$Alumnos2 -> registro = substr($a -> registro,0,3)."0".substr($a -> registro,3,6);
				$Alumnos2 -> save();
				echo $a -> id." - ".$a -> registro." --> ";
				echo substr($a -> registro,0,3)."0".substr($a -> registro,3,6)."<br />";
			}
			else
				echo "NO ";			
		}
	}
	
	function usuarios(){
		$Alumnos = new Usuarios();
		$Alumnos2 = new Usuarios();
		echo "<center><h2>Modificaciones de la Tabla > Usuarios a los registro > 910000 </h2></center>";
		
		foreach($Alumnos -> find("registro > '910000' and registro < '920000'") as $a){
			if($Alumnos2 -> find_first("id = ".$a->id)){
				echo "SI ";
				$Alumnos2 -> registro = substr($a -> registro,0,3)."0".substr($a -> registro,3,6);
				$Alumnos2 -> save();
				echo $a -> id." - ".$a -> registro." --> ";
				echo substr($a -> registro,0,3)."0".substr($a -> registro,3,6)."<br />";
			}
			else
				echo "NO ";			
		}
	}
	
	function tutorias(){
		$Alumnos = new Tutorias();
		$Alumnos2 = new Tutorias();
		echo "<center><h2>Modificaciones de la Tabla > Tutorias a los registro > 910000 </h2></center>";
		
		foreach($Alumnos -> find("registro > 910000 and registro < 920000") as $a){
			if($Alumnos2 -> find_first("id = ".$a->id)){
				echo "SI ";
				$Alumnos2 -> registro = substr($a -> registro,0,3)."0".substr($a -> registro,3,6);
				$Alumnos2 -> save();
				echo $a -> id." - ".$a -> registro." --> ";
				echo substr($a -> registro,0,3)."0".substr($a -> registro,3,6)."<br />";
			}
			else
				echo "NO ";			
		}
	}
	
	function kardex(){
		$Alumnos = new KardexIng();
		$Alumnos2 = new KardexIng();		
		
		echo "<center><h2>Modificaciones de la Tabla > Kardex_ing a los registro > 910000 </h2></center>";		
		foreach($Alumnos -> find("registro > 910000 and registro < 920000 and periodo = 32008") as $a){			
			if($Alumnos2 -> find_first("id = ".$a->id)){
				echo "SI ";
				$Alumnos2 -> registro = substr($a -> registro,0,3)."0".substr($a -> registro,3,6);
				$Alumnos2 -> save();
				echo $a -> id." - ".$a -> registro." --> ";
				echo substr($a -> registro,0,3)."0".substr($a -> registro,3,6)."<br />";
			}
			else
				echo "NO ";		
		}
	}
}

?>
