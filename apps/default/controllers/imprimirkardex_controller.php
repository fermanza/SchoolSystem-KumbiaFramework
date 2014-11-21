<?php
	class imprimirkardexController extends ApplicationController {
		
		function index(){
			//$this-> valida ();
		}
		
		function kardeximpresos(){
			//$this-> valida ();
			
			$registro = $this -> post("registro");
			
			$registro = 531025;
			
			$kardex			= new KardexIng();
			$alumnos		= new Alumnos();
			$materiaIng		= new MateriasIng();
			$materia		= new Materia();
			$especialidades = new Especialidades();
			
			$i = 0;
			foreach ( $kardex -> find ( "registro = ".$registro, "order: periodo asc" ) as $kard ){
				
				$periodoAnio = substr($kard -> periodo, 1);
				$periodoPrimerDigito = substr($kard -> periodo, 0, 1);
				
				$periodoCompleto[$i] = $periodoAnio.$periodoPrimerDigito;
				$id[$i] = $kard -> id;
				
				$i++;
			}
			
			for( $i = 0; $i < ( count( $periodoCompleto ) -1 ); $i++ ){
				for( $j = 1; $j < ( count( $periodoCompleto ) - $i ); $j++ ){
					if( $periodoCompleto[$j] < $periodoCompleto[$j-1] ){
						$temp = $periodoCompleto[$j];
						$periodoCompleto[$j] = $periodoCompleto[$j - 1];
						$periodoCompleto[j - 1] = $temp;
						
						$temp2 = $id[$j];
						$id[$j] = $id[$j - 1];
						$id[$j - 1] = $temp2;
					}
				}
			}
			
			$periodoo = 0;
			for ( $i = 0; $i < count($id); $i++ ){
				$kard = $kardex -> find_first( "id = ".$id[$i] );
				if ( $periodoo != $kard -> periodo ){
					echo "<br />";
					$periodoo = $kard -> periodo;
					echo $kard -> periodo."<br />";
				}
				echo $kard -> nivel." ".$kard -> clavemat." ".
						$kard -> promedio." ".$kard -> tipo_de_ex." ".
						$kard -> periodo."<br />";
			}
			
		} // function kardeximpresos()
		
		function valida(){
			if(Session::get("tipousuario")=="VENTANILLA" || Session::get("tipousuario")=="ADMINISTRADOR" ){
				return true;
			}
			$this -> redirect("general/index");
			return true;
		}
	}
	
?>
