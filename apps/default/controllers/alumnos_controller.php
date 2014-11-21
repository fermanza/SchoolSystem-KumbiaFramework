<?php
			
	class alumnosController extends ApplicationController {
		function index( $vacia ){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			echo "vacia: ".$vacia;
			//die;
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			unset( $this -> registro );
			unset( $this -> vacia );
			
			if($usuario -> clave != $id && $vacia == ""){
				$this->redirect('alumno/informacion');
			}
			
			$this -> registro = Session::get_data('registro');
			$this -> vacia = $vacia;
		}
	}
	
?>
