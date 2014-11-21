<?php
			
	class Especialidades extends ActiveRecord {
		
        static public function nombre($num){
		
            $Especialidades = new Especialidades();
            if($Especialidades -> find_first($num)){
                return $Especialidades -> vcNomEsp;
            }else{
                return 'Sin Esp.';
            }
        }
	}
	
?>
