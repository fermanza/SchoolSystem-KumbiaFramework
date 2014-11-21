<?php
			
	class SubirarchivosController extends ApplicationController {
		
		public $antesAnterior=12009;
		public $anterior	= 32009;
		public $actual		= 12010;
		public $proximo		= 32010;
		
		function index(){
			
		}
		
		function subirControlador(){
			//datos del arhivo
			
			$nombre_archivo = $_FILES["controlador"]["name"];
			$tipo_archivo = $_FILES["controlador"]["type"]; 
			$tamano_archivo = $_FILES["controlador"]["size"];
			@unlink("/datos/calculo/ingenieria/apps/default/controllers/".$nombre_archivo);
			if (move_uploaded_file($_FILES['controlador']['tmp_name'], 
					"/datos/calculo/ingenieria/apps/default/controllers/".$nombre_archivo)){ 
			   echo "El archivo ha sido cargado correctamente."; 
			}else{ 
			   echo "Ocurrió algún error al subir el fichero. No pudo guardarse."; 
			}
		}
		
		function subirVista(){
			//tomo el valor de un elemento de tipo texto del formulario
			$nombreVista = $_POST["nombreVista"];
			echo "Escribió en el campo de texto: " . $nombreVista . "<br><br>";
			//datos del arhivo
			$nombre_archivo = $_FILES['vista']['name'];
			$tipo_archivo = $_FILES['vista']['type'];
			$tamano_archivo = $_FILES['vista']['size'];
			if (move_uploaded_file($_FILES['vista']['tmp_name'], 
					"/datos/calculo/ingenieria/apps/default/views/".$nombreVista."/".$nombre_archivo)){ 
			   echo "El archivo ha sido cargado correctamente."; 
			}else{ 
			   echo "Ocurrió algún error al subir el fichero. No pudo guardarse."; 
			}
		}
		
		function subirModelo(){
			//datos del arhivo
			$nombre_archivo = $_FILES['modelo']['name'];
			$tipo_archivo = $_FILES['modelo']['type'];
			$tamano_archivo = $_FILES['modelo']['size'];
			if (move_uploaded_file($_FILES['modelo']['tmp_name'], 
					"/datos/calculo/ingenieria/apps/default/models/".$nombre_archivo)){ 
			   echo "El archivo ha sido cargado correctamente."; 
			}else{ 
			   echo "Ocurrió algún error al subir el fichero. No pudo guardarse."; 
			}
		}
	}
?>