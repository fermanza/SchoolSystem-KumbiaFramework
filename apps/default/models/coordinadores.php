<?php
	class Coordinadores extends ActiveRecord {
		
		function validar_coordinacion(){
			$coordinacion = Session::get_data('coordinacion');
			// Si regresa false, significa que si es un coordinador
			switch( $coordinacion ){
				case "IIM": return false;
				case "IEC": return false;
				case "TCB": return false;
				case "MCT": return false;
				case "TCT": return false;
			}
			return true;
		} // function validar_coordinacion()
	}
?>