<?php		
	class XalumnocursosLogalumnos extends ActiveRecord{
		
		function grupo_seleccionado_correctamente($registro, $periodo, $clavecurso, $clave, $quien){
			$this -> registro = $registro;
			$this -> periodo = $periodo;
			$this -> clavecurso = $clavecurso;
			$this -> materia = $clave; // clave de la materia
			$this -> fecha = $this -> fecha_actual();
			$this -> activo = 1;
			$this -> quien = $quien;
			
			$this -> create();
		} // function grupo_seleccionado_correctamente($registro, $periodo, $clavecurso, $clave, $quien)
		
		function grupo_deseleccionado($registro, $periodo, $clavecurso, $clave, $quien){
			$this -> registro = $registro;
			$this -> periodo = $periodo;
			$this -> clavecurso = $clavecurso;
			$this -> materia = $clave; // clave de la materia
			$this -> fecha = $this -> fecha_actual();
			$this -> activo = '0';
			$this -> quien = $quien;
			
			$this -> create();
		} // function grupo_deseleccionado($registro, $periodo, $clavecurso, $clave, $quien)
		
		function fecha_actual(){
			return date ("Y")."-".date ("m")."-".date ("d")." ".date ("H").":".date ("i").":".date ("s");
		} // function fecha()
	}
?>