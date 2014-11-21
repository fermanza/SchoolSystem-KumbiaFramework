<?php
			
	class GeneralController extends ApplicationController {

		function index(){
			echo mktime(14,0,0,10,1,2008);
		}
		
		function password($registro){
			$usuarios = new Usuarios();
			
			$usuario = $usuarios -> find_first("registro='".$registro."'");
			
			echo $usuario -> clave;
		}
		
		function fecha($d,$m,$y,$h,$i){
			echo mktime($h,$i,0,$m,$d,$y);
		}
		
		function aspirantes(){

		}
		
		function calendario(){

		}
		
		function accesourl(){
		
		}
		
		function reglamentos(){

		}
		
		function carreras(){

		}
		
		function ficha(){

		}

		function descargas(){

		}
		
		function contactanos(){

		}
		
		function inicio(){
			$this -> set_response("view");
		}
		
		function inicionuevo(){

		}
		
		function web(){
			$this -> set_response("view");
		}
		
		function crearAnuncios(){
			
		}
		
		function crearAnuncios2(){
			
		}
		
		function pago(){
			echo "<br><br>";
				echo "<H2>ACCESO RESTRINGIDO</H2>";
				
				echo "<br>";
				echo "<table><tr><td width=\"500\" align=\"center\"><h3>PAGO NO REGISTRADO</h3><br><br>
				PARA SOLUCIONAR ESTE PROBLEMA PRESENTA UNA COPIA DE TU FICHA DE PAGO EN EL DEPARTAMENTO DE APOYO ACADEMICO</td></tr></table>";
			echo "<br><br>";
		} // function pago()
		
		function acudir_calculo(){
			switch(Session::get_data("alumnos_problema")){
				case "KARDEX":
						echo "<br><br>";
							echo "<H2>ACCESO RESTRINGIDO</H2>";
							
							echo "<br>";
							echo "<table><tr><td width=\"500\" align=\"center\"><h3>Problemas con su Kardex</h3>";
							echo "<br><br>PARA SOLUCIONAR ESTE PROBLEMA FAVOR DE ACUDIR A APOYO ACADÉMICO<br />";
							echo "HORARIO DE 8:00AM A 4:00PM</td></tr></table>";
						echo "<br><br>";
					break;
				case "PAGO":
						echo "<br><br>";
							echo "<H2>ACCESO RESTRINGIDO</H2>";
							
							echo "<br>";
							echo "<table><tr><td width=\"500\" align=\"center\"><h3>Problemas de Pago</h3>";
							echo "<br><br>Tu sistema ha sido bloqueada por problemas con tu pago, 
								favor de acudir con Alicia o Aldo a Control Escolar<br />";
							echo "<br />HORARIO DE 9:00AM A 8:00PM</td></tr></table>";
						echo "<br><br>";
					break;
					case "EXTRANJERO":
						echo "<br><br>";
							echo "<H2>ACCESO RESTRINGIDO</H2>";
							
							echo "<br>";
							echo "<table><tr><td width=\"500\" align=\"center\"><h3>Actualización de información</h3>";
							echo "<br><br>Tu sistema ha sido bloqueado por cuestiones administrativas ya que estamos actualizando informacion<br>
							 y nos gustaria revisar tus datos de nacionalidad, te sugerimos que por favor<br>
								te dirigas con la Lic. Claudia Lara a Trabajo Social a un costado de Enfermería<br />";
							echo "<br />HORARIO DE 10:00 a 17:00 hrs.</td></tr></table>";
						echo "<br><br>";
					break;
			}
		} // acudir_calculo
		
		function sistema_no_disponible(){
			echo "<br><br>";
				echo "<h2>ACCESO RESTRINGIDO</h2>";
				
				echo "<br>";
				echo "<table><tr><td width=\"500\" align=\"center\"><h3>";
				echo "<br /><br />POR EL MOMENTO EL SISTEMA NO ESTA DISPONIBLE PARA ALUMNOS.<br />";
				echo "FAVOR DE REVISAR NUEVAMENTE APARTIR DEL JUEVES 14 DE MARZO DESPUES DE LAS 8 DE LA MAÑANA</h3></td></tr></table>";
			echo "<br><br>";
		} // sistema_no_disponible
		
		function baja(){
			echo "<br><br>";
				echo "<H2>ACCESO RESTRINGIDO</H2>";
				
				echo "<br>";
				echo "<table><tr><td width=\"500\" align=\"center\"><h3>BAJA DEFINITIVA</h3>
				<br><br>PARA SOLUCIONAR ESTE PROBLEMA PRESENTATE EN EL DEPARTAMENTO DE APOYO ACADEMICO</td></tr></table>";
			echo "<br><br>";
		}
		
		function avizoSinDocumentos(){
		    echo '<center>
					<br/>
					<p style="font-size:20px; font-family:arial;">
					  <b>Aviso estudiantes Ingenier&iacute;a</b><br/><br/>
					</p>
					<p style="font-size:17px; text-align: justify; width:700px; font-family:arial;line-height: 24px;">	
						Les informamos que de acuerdo al reglamento vigente de estudiantes de Educaci&oacute;n Superior, tu status de alumno es baja administrativa, debido a la omisi&oacute;n de la entrega de documentaci&oacute;n de nuevo ingreso.
						<br/><br/>
						Favor de presentarse en la ventanilla de Depto. de Servicios de Apoyo Acad&eacute;mico  en horario de lunes a viernes de 8:00 a 20:00 hrs.
						<br/><br/>
						Cualquier duda o aclaraci&oacute;n favor de enviar un correo <a href="mailto:apoyo.academico.col@ceti.mx">apoyo.academico.col@ceti.mx</a> o al tel&eacute;fono 36413250 ext. 229 y 501.
						<br/><br/>
					</p>
					<p style="font-size:17px; font-family:arial;">
						<b>Atte.</b>
						<br/>
						<b>Depto. de Servicios de Apoyo Acad&eacute;mico</b>
					</p>  
				  </center>';
		}
	}
	
?>
