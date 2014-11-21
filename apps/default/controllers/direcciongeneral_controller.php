 <?php
	class DirecciongeneralController extends ApplicationController {
		function index(){
			
		}
		
		function buscar(){
			$this -> valida();	
			$this -> registro="";
			$this -> nombre="";
		}
		function buscarProfesores(){
			$this -> valida();
			$this -> nombre="";
			$this -> nomina="";			
		}
		
		function profesoresXDivision(){
			$this -> set_response('view');
			$MaestroDivision = new MaestroDivisiones();			
			if($this -> maestros = $MaestroDivision -> find("division_id = ".$this -> post('division_id'), 'order: maestro_id ASC')){				
				echo $this -> render_partial('profesores');
				
			}else{
				Flash::error('No se encontraron profesores asignados a esa division');
			}
			
		}
		
		function buscando(){
			$this -> valida();
			$this -> set_response("view");
			$this -> registro="";
			$this -> nombre="";
			
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
		
		function buscandoProfesores(){
			$this -> valida();
			$this -> set_response("view");
			$this -> nombre="";
			$this -> nomina="";
			
			if($this -> post("nomina") != ''){
				$this -> nomina = " and id = ".$this -> post("nomina");
			}else{
				$this -> nomina = "";
			}
			if($this -> post("nombre") != ''){
				$this -> nombre = " and nombre like '%".utf8_decode($this -> post("nombre"))."%'";
			}else{
				$this -> nombre = "";
			}
			if($this -> nomina != "" || $this -> nombre != ""){
				$Maestros = new Maestros();
				if($this -> maestros = $Maestros -> find("1".$this -> nomina.$this -> nombre)){
					$this -> render_partial("busquedaProfesores");
				}else{
					Flash::error("No se encontro ningun profesor");
				}	
			}else{
				Flash::error("No se encontro ningun profesor");
			}
			$this -> nombre="";
			$this -> nomina="";
		}
		
		function salir(){
			Session::set_data("tipo","");
			Session::set_data("registro","");
			Session::set_data('division', ""); 
			Session::set_data('nombre', ""); 
			Session::set_data('coordinacion', ""); 
			Session::set_data('div', ""); 
			Session::set_data('temporal', '');
			Session::set_data('temporal2', '');	
						
			$this -> redirect("general/ingresar");
		}	
		
		function valida(){
			if(Session::get("tipo")=="DIRECTOR"){
				return true;
			}
			$this -> redirect("general/ingresar");			
		}
	}
?>