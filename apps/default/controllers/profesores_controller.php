<?php
			
	class ProfesoresController extends ApplicationController {
		function index(){
			if(Session::get_data('tipousuario')!="PROFESOR"){
				$this->redirect('/');
				return;
			}
			$this->redirect('profesor/informacion');
		}
	}
	
?>
