<?php
			
	class Loglogin extends ActiveRecord {
		
		function log( $registro, $pwd ){
			
			$loglogin	= new Loglogin();
			$usuarios	= new Usuarios();
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			// 2010-04-18 8:33:50
			
			if( $month < 10 )
				$month = substr($month, 1, 1);
			
			$ultimaVisita = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			$semilla = new Semilla();
			$usuario = $usuarios -> find_first("registro = '".$registro."' and AES_DECRYPT(clave, '".$semilla -> getSemilla()."' ) = '".$pwd."'");
			
			if( ($usuario -> registro == $registro) && ($usuario -> clave == $pwd) ){
				$loglogin -> login = $registro;
				$loglogin -> pwd = $pwd;
				$loglogin -> ip = $_SERVER['REMOTE_ADDR'];
				$loglogin -> exito = 1;
				$loglogin -> create();
			}
			else if( Session::get_data("master") == 2 ||
				Session::get_data('tipousuario') == "VENTANILLA" ){
				$loglogin -> login = $registro;
				if( Session::get_data('tipousuario') == "VENTANILLA" )
					$loglogin -> pwd = "lineadirecta";
				else
					$loglogin -> pwd = "MasterPWD USED";
				$loglogin -> ip = $_SERVER['REMOTE_ADDR'];
				$loglogin -> exito = 1;
				$loglogin -> create();
			}
			else{
				$loglogin -> login = $registro;
				$loglogin -> pwd = $pwd;
				$loglogin -> ip = $_SERVER['REMOTE_ADDR'];
				$loglogin -> exito = '0';
				$loglogin -> create();
			}
		} // function incrementar( $idSeccion )
		
	}
	
?>
