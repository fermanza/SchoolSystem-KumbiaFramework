<?php
			
	class EncuestasController extends ApplicationController {
		
		public $periodo = 32010;
		
		function index(){
			//borra las variables que se quedaban con persistencia
			
			$this -> registroForm = $this -> post("registro");
			$this -> passwordForm = $this -> post("password");
			$this -> activarFormulario = 0;
			$reg = $this -> post("registro");
			
			for( $i = 1; $i < 14; $i++ ){
				$this -> preguntas[$i]= NULL;
			}
				
			if (substr($reg,0,4)== "prof" || substr($reg,0,4)== "elch" ){
				$this -> activarFormulario = 1;
				$this -> prof = 1;
			}
			else{
				$this -> prof = 0;
				//$reg = 9310001; // borrar esto, yo lo puse para hacer pruebas....
				$this -> registroAlumno = $reg;
				$Usuarios = new Usuariosencuestas();			
				if($Usuarios -> find_first("registro = ".$reg)){
					$suma = 0;
					for( $i = 1; $i < 13; $i++ ){
						$campo = "s".$i;
						$suma = $suma + $Usuarios -> $campo;
					}
					if ($suma == 13){
						$this -> activarFormulario = 1;
					
					}
					else{
						$this -> activarFormulario = 0;
					}
					
					//no hace nada solo es para validar
				}else{
					$nuevoUsuario = new Usuariosencuestas();
					$nuevoUsuario -> registro = $reg;
					$nuevoUsuario -> s1 = '0';
					$nuevoUsuario -> s2 = '0';
					$nuevoUsuario -> s3 = '0';
					$nuevoUsuario -> s4 = '0';
					$nuevoUsuario -> s5 = '0';
					$nuevoUsuario -> s6 = '0';
					$nuevoUsuario -> s7 = '0';
					$nuevoUsuario -> s8 = '0';
					$nuevoUsuario -> s9 = '0';
					$nuevoUsuario -> s10 = '0';
					$nuevoUsuario -> s11 = '0';
					$nuevoUsuario -> s12 = '0';
					$nuevoUsuario -> periodo = $this -> periodo;
					$nuevoUsuario -> save();
					$Usuarios -> find_first("registro = ".$reg);
				}
				$Preguntas = new PreguntasSatisfaccion();
				$OpcPreguntas = new OpcpreguntasSatisfaccion();
				for( $i = 1; $i < 14; $i++ ){
					$var= "s".$i;
					if ($Usuarios -> $var == 0){
						$this -> preguntas[$i] = $Preguntas -> find("clave = $i order by id ASC");
						foreach ( $this -> preguntas[$i] as $preguntas ){
							$this -> opciones[$preguntas -> id] = $OpcPreguntas -> 
									find_first ( "preguntas_satisfaccion_id = ".$preguntas -> id);
						}
					}
				}
			}
		}
		
		function guardar(){
			$this -> set_response('view');			
			$seccionNombre = $this -> request("seccionNombre");
			$seccionNumero = $this -> request("seccionNumero");
			$registroAlumno = $this -> request("registroAlumno");
			$day = date("d");
			$month = date("m");
			$year = date("Y");
			$hour = date("H");
			$min = date("i");
			$sec = date("s");
//			$date = date( "M/d/Y", mktime( 0, 0, 0, $month, $day, $year ) );
			$date1 = date( "Y-m-d H-i-s", mktime( $hour, $min, $sec, $month, $day, $year ) );
			
			for( $i = 1; $i < 53; $i++ ){
				if ( $this -> post("r".$i) != '' || 
						$this -> post("comentario".$i) != '' || 
								$this -> post("opctexto".$i) != '' ||
										$this -> post("opc".$i) != '' ||
												$this -> post("sino".$i) != '' ){
					$Preguntas = new PreguntasSatisfaccion();
					$Respuestas = new RespuestasSatisfaccion();
					$Preguntas -> find_first("id = ".$i);
					
					echo "tipo: ".$this -> post("tipo".$i)."<br />";
					echo "<b>". htmlentities($Preguntas -> pregunta, ENT_QUOTES)."</b>";
					if ( $this -> post("tipo".$i) == 0 ){
						switch ($this -> post("r".$i)){
							case 5: $Preguntas -> r1 = $Preguntas -> r1 +1;
									break;
							case 6: $Preguntas -> r2 = $Preguntas -> r2 +1;
									break;
							case 7: $Preguntas -> r3 = $Preguntas -> r3 +1;
									break;
							case 8: $Preguntas -> r4 = $Preguntas -> r4 +1;
									break;
							case 9: $Preguntas -> r5 = $Preguntas -> r5 +1;
									break;
							case 10: $Preguntas -> r6 = $Preguntas -> r6 +1;
									break;
						}
						$Preguntas -> save();
						echo "<br />opcionesdeR: ".$this -> post("r".$i)."<br />";
					}
					else if ( $this -> post("tipo".$i) == 1 ){
						echo "<br />comment: ".$this -> post( "comentario".$i )."<br />";
						$Respuestas -> preguntas_satisfaccion_id = $i;
						$Respuestas -> fecha = $date1;
						$Respuestas -> respuesta = '0';
						$Respuestas -> comentario = $this -> post("comentario".$i);
						$Respuestas -> registro = Session::get_data("registro");
						$Respuestas -> create();
					}
					else if ( $this -> post("tipo".$i) == 2 ){
						echo "<br />Si-No: ".$this -> post("sino".$i )."<br />";
						$Respuestas -> preguntas_satisfaccion_id = $i;
						$Respuestas -> fecha = $date1;
						$Respuestas -> respuesta = $this -> post("sino".$i);
						$Respuestas -> comentario = " ";
						$Respuestas -> registro = Session::get_data("registro");
						$Respuestas -> create();
					}
					else if ( $this -> post("tipo".$i) == 3 ){
						if ( $this -> post("opctexto".$i) != "" ||
										$this -> post("opctexto".$i) != null ){
							echo "<br />MultiplesOpcTexto: ".$this -> post("opctexto".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = $date1;
							$Respuestas -> respuesta = '0';
							$Respuestas -> comentario = $this -> post("opctexto".$i);
							$Respuestas -> registro = Session::get_data("registro");
							$Respuestas -> create();
						}
						else{
							echo "<br />MultiplesOpciones: ".$this -> post("opc".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = $date1;
							$Respuestas -> respuesta = $this -> post("opc".$i);
							$Respuestas -> comentario = " ";
							$Respuestas -> registro = Session::get_data("registro");
							$Respuestas -> create();
						}
					}
					//echo utf8_encode("<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>");
					//echo "<br/ > Respuesta:".$this -> post("r".$i)."<br>";
				}
			}
			
			//Flash::success(utf8_encode('Se guardo exitosamente la sección: ').'<span class="resaltado">'.$seccionNombre.'</span>');
			Flash::success('Se guardo exitosamente la secci&oacute;n: <span class="resaltado">'.$seccionNombre.'</span>');
			
			$seccionNumero= "s".$seccionNumero;
			$Usuarios = new Usuariosencuestas();
			$Usuarios -> find_first("registro = ".$registroAlumno);
			$Usuarios -> $seccionNumero = 1;
			$Usuarios -> save();
			
			$suma=0;
			for( $i = 1; $i < 14; $i++ ){
				$campo = "s".$i;
				$suma = $suma + $Usuarios -> $campo;
			}
			if ( $suma == 13 ){
				$this -> render_partial("formulario");
			}
			$this -> set_response('view');
		}
		
		function grafica (){
			$this -> set_response('view');
			unset($this -> Preguntas);
			
			$Preguntas = new PreguntasSatisfaccion();
			$OpcPreguntas = new OpcpreguntasSatisfaccion();
			$Usuarios = new Usuariosencuestas();
			
			$i = 0;
			foreach( $Preguntas -> find_all_by_sql(
						"select * from preguntas_satisfaccion
						where periodo = 32010
						and tipo = 0") as $pregunta ) {
				$this -> Preguntas[$i] = $pregunta;
				$i++;
			}
			$this -> render_partial("grafica2");
		} // function grafica()
		
		function grafica3(){
			
		} // function grafica3
		
		function prev(){
			$Usuariosencuestas = new Usuariosencuestas();				
			$this -> cuantos = $Usuariosencuestas -> count();
			$this -> contestaron = $Usuariosencuestas -> count
					("s1 = 1 and s2 = 1 and s3 = 1 and s4 = 1 and s5 = 1 and 
					s6 = 1 and s7 = 1 and s8 = 1 and s9 = 1 and s10 = 1 and s11 = 1 and s12 = 1");
		}
	}
	
?>
