<?php
	class Usuarios extends ActiveRecord {
		
		function update_clave_by_registro($registro){
			$Semilla = new Semilla();
			foreach($this->find_all_by_sql("
				update usuarios
				set clave= AES_ENCRYPT(clave, '".$Semilla->getSemilla()."')
				where registro = '".$registro."'") as $usuario){
				break;
			}
		} // function update_clave_by_registro($registro)
		
		
		
        static public function foto($registro){
            $foto = "fotos/".$registro.".JPG";
            $foto1 = "fotos/".$registro.".jpg";					
            if(file_exists("C:/xampp/htdocs/tecnologo/public/img/".$foto)){					
            	return img_tag($foto, 'width: 100px');
            }elseif(file_exists("C:/xampp/htdocs/tecnologo/public/img/".$foto1)){					
                return img_tag($foto1, 'width: 100px');		
            }else{
                return img_tag('icons/usr1.png', 'height: 70px');
            } 
        }
		
		public function get_registro_jefe_departamento(){
			return 1861;
		}
		
	}
?>
