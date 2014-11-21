<?php
			
	class EvaluacionController extends ApplicationController {
		function index(){
		
		}
		
		function cuestionario($periodo=32008){
			
		}
		
		function reportes($periodo=32008){
			
			if($periodo==32008){
				$mrespuestas = new Eva32008Respuestas();
				
				$profesores = $mrespuestas -> distinct("profesor");
				
				if($profesores) foreach($profesores as $profesor){
					echo $profesor."<br>";
				}
				
				echo $periodo;
			}
		}
		
		function alumnos(){
		
		}
		
		function profesores(){
		
		}
		
		function reporte(){
			$this -> set_response("view");
			
			
		}
		
		function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			$this->redirect('/');
		}
	}
