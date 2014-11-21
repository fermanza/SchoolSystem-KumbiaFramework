<?php
			
	class TecnologoController extends ApplicationController {
		function evaluaciondocente(){
			$this -> set_response("view");
			
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("escolar",$cnx);
			$query = "SELECT * FROM xusuarios WHERE referencia=".$this -> post("registro");
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
				$tipo = $datos["tipousuario"];
			}
			
			$query = "SELECT * FROM recibos WHERE registro=".$this -> post("registro");
			$con = mysql_query($query, $cnx);
			if($con)
				$datos = mysql_fetch_array($con);
			
			
			$con = mysql_query("SELECT * FROM passwd WHERE registro=".$this -> post("registro"), $cnx);
				if($con)
					$datos = mysql_fetch_array($con);
				
		
				?>
				<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
				<html xmlns='http://www.w3.org/1999/xhtml'>
				<head>
				<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
				<title>Evaluación de profesores</title>
				<style type='text/css'>
				@charset "utf-8";
/* CSS Document */

table, th, td {
	border: 1px solid #FFDCB9;
	border-collapse: collapse;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	font-size: 10px;
}

caption {
	font-size: 16px;
	font-weight: bold;
	background-color: #009966;
	font-family: Arial, Helvetica, sans-serif;
	color: #330066;
	text-align: center;
}

td, th {
	padding: 4px;
}

thead th {
	text-align: center;
	color: #FDFCEA;
	font-size: 100% !important;
	background: #FFCC66;
}

tbody th {
	font-weight: bold;
}

tbody tr {
	background: #FDFCEA;
	;
}
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #333333;
	text-transform: uppercase;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
}
.boton01 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-variant: normal;
	text-transform: uppercase;
	border: thin solid #000033;
	color: #FFFFFF;
	background-color: #336699;
}
.boton02 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-variant: normal;
	text-transform: uppercase;
	border: thin solid #003333;
	color: #000000;
	background-color: #CCCC99;
}
.boton03 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-variant: normal;
	text-transform: uppercase;
	border: thin solid #FFCC00;
	color: #FFFFFF;
	background-color: #990000;
}
</style>
				<style type='text/css'><!--.Estilo1 {color: #FFFFFF}a:link {        color: #006699;}a:visited {        color: #006699;}a:hover {        color: #006699;}a:active {        color: #006699;}.Estilo4 {font-family: Arial, Helvetica, sans-serif}.Estilo5 {        font-size: 14px;        color: #003333;        font-family: Arial, Helvetica, sans-serif;        font-weight: bold;}--></style>
				</head><body><p align='center' class='Estilo5'>PROCESO DE EVALUACI&Oacute;N PARA EL PER&Iacute;ODO FEB JUN 2008.<br />  <br /></p><div align='center'>  <table width='482' border='1' align='center' cellpadding='1'>    <caption align='top'>      <span class='Estilo1'>EVALUACI&Oacute;N DEL PROFESOR POR PARTE DEL ALUMNADO</span>    </caption>    <tr>      <th colspan='2' scope='col'><p>
				<img src='https://cira.ceti.mx/cira/imgs/logo_ceti_aniv.jpg' alt='CETI' width='102' height='97' align='left' />
				COMPA&Ntilde;ERO (a), SE TE INVITA A EVALUAR, CON &Eacute;TICA Y PROFESIONALISMO, A TUS MAESTROS PARA ESTE CICLO <br />        FEB - JUN 2008.</p>        <p><br />      INGRESA A LA SIGUIENTE DIRECCI&Oacute;N DE INTERNET, ESCRIBE TU C&Oacute;DIGO Y TU CONTRASE&Ntilde;A PERSONALIDOS PARA ESTA ACCIÓN (INFORMACIÓN CITADA ABAJO) PARA COMIENZAR EL PROCESO DE EVALUACI&Oacute;N.</p></th>    </tr>    <tr>      <th width='170' scope='row'>DIRECCI&Oacute;N DE INTERNET</th>      <td width='296'><a href='http://evaluaciondocente.ceti.mx'>http://evaluaciondocente.ceti.mx</a></td>    </tr>
				<tr>
					<th scope='row'>CLAVE (LOGIN)</th>
					<td><?= $datos["registro"] ?></td>
				</tr>
				<tr>
					<th scope='row'>CONTRASE&Ntilde;A (PASSWORD)</th>
					<td><?= $datos["passwd"] ?></td>
				</tr>
				</table></div> <br /><br /><p align='center' class='Estilo5'>UNA VEZ CONTESTADA LA ENCUESTA, PODR&Aacute;S INGRESAR NORMALMENTE A TU CUENTA.</p></body></html>
				<?php
			
		}
		
		function actualizacion(){
		$this -> set_response("view");
		
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("escolar",$cnx);
			$query = "SELECT * FROM alumnos WHERE miReg=".$this -> post("clave");
			$con = mysql_query($query, $cnx);
			
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
		if(!$datos["nombre"]){ ?>
					<center><H1>TUS DATOS YA FUERON ACTUALIZADOS ANTERIORMENTE</H1>
					<form action="<?= KUMBIA_PATH ?>tecnologo/ficha" method="POST" name="forma1">
						<input type="hidden" name="clave" id="clave" value="<?= $this -> post("clave") ?>"/>
						<input type="hidden" name="contra" id="contra" value="<?= $this -> post("contra") ?>"/>
						<input name="ingresar" type="submit" class="boton03" id="ingresar" value="FICHA DE PAGO &gt;&gt;" />
					</form>
		<?php
		}
		else{
		?>
		
		
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <title>Actualización de datos</title>
        <style type="text/css">
            .columna1 {
                width: 200px;
            }
            
            .columna2 {
                width: 310px;
            }
            
            table input {
                width: 99%;
            }

        </style>
    </head>
    <body><center>
		<table>
			<tr>
				<td width="600">
					<center><H1>ACTUALIZACION DE DATOS</H1></center>
					<p align="justify">
						Para poder ingresar al sistema es necesario realizar una actualización de tus datos. Donde debes registrar tu nombre y apellidos correctamente, así como tu teléfono, celular y correo electrónico. Además es necesario capturar la clave CURP si no la tienes puedes consultarla <a hred="http://www.sre.gob.mx/CurpPS_HTML/jsp/CurpTDP.html" target="_blank">AQUÍ</a>.
					</p>
				</td>
			</tr>
		</table><br>
      <form action="<?= KUMBIA_PATH ?>tecnologo/actualizar" method="POST" name="forma1">
			<input type="hidden" name="clave" id="clave" value="<?= $this -> post("clave") ?>"/>
			<input type="hidden" name="contra" id="contra" value="<?= $this -> post("contra") ?>"/>
            <table cellpadding="2" cellspacing="2" border="1">
				<tr>
                    <td bgcolor="#CCCCCC"><b>
                        Registro:&nbsp;
                    </td>
                    <td width="300">
                        <input type="text" name="nombre" value="<?= $this -> post("clave") ?>" DISABLED>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Nombre:&nbsp;
                    </td>
                    <td width="300">
                        <input type="text" name="nombre">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Apellido Paterno:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="aPaterno">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Apellido Materno:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="aMaterno">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Sexo:&nbsp;
                    </td>
                    <td>
                        <select name="sexo">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Telefono:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="telefono">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Celular:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="celular">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Email:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="email">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        Fecha de Nacimiento:&nbsp;
                    </td>
                    <td>
                        Dia
                        <select name="dia">
                            <?php 
						for($i=1;$i<32;$i++)
							echo '<option value="'.$i.'">'.$i.'</option>';
						?>
                        </select>
                        Mes
                        <select name="mes">
                            <?php
						$mes=array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
						for($i=0;$i<12;$i++)
							echo '<option value="'.($i+1).'">'.$mes[$i].'</option>'
						?>
                        </select>
                        A&ntilde;o
                        <select name="ano">
                            <?php 
						for($i=1960;$i<2000;$i++)
							echo '<option value="'.$i.'">'.$i.'</option>';
						?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCCC"><b>
                        CURP:&nbsp;
                    </td>
                    <td>
                        <input type="text" name="curp">
                    </td>
                </tr>
            </table>
			<br>
				<input type="submit" name="Enviar" value="Enviar información">
        </form>
    </body>
</html>
		<?php
		}
	}
		
		function actualizar(){
			$this -> set_response("view");
			if($this -> post("mes")<10){
				$m = "0".$this -> post("mes");
			}
			if($this -> post("dia")<10){
				$d = "0".$this -> post("dia");
			}
			
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("escolar",$cnx);
			
			$query = "UPDATE alumnos SET enSexo='".$this -> post("sexo")."', daFechNac='".$this -> post("ano")."-".$m."-".$d."', nombre='".$this -> post("nombre")."', apaterno='".$this -> post("aPaterno")."', amaterno='".$this -> post("aMaterno")."', telefono='".$this -> post("telefono")."', celular='".$this -> post("celular")."', email='".$this -> post("email")."', curp='".$this -> post("curp")."' WHERE miReg=".$this -> post("clave");
			mysql_query($query, $cnx);

			?>
					<center><H1>TUS DATOS HAN SIDO ACTUALIZADOS</H1>
					<form action="<?= KUMBIA_PATH ?>tecnologo/ficha" method="POST" name="forma1">
						<input type="hidden" name="clave" id="clave" value="<?= $this -> post("clave") ?>"/>
						<input type="hidden" name="contra" id="contra" value="<?= $this -> post("contra") ?>"/>
						<input name="ingresar" type="submit" class="boton03" id="ingresar" value="FICHA DE PAGO &gt;&gt;" />
					</form>
			<?php
		}
		
		function ficha(){
			$this -> set_response("view"); 
			?>
			<center><H1>El alumno no se encuentra registrado o se encuentra dado de baja</H1>
			<?php
		}
		
		function pagoTgo(){
			$this -> set_response("view");
			$registro = 730110;
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("escolar",$cnx);
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro;
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			if($datos["vcNomAlu"]==""){
				echo "El alumno no se encuentra registrado o se encuentra dado de baja";
				$this->redirect('tecnologo/ficha');
				return;
			}
			
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=32008";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
			
			$reporte = new FPDF();
			
			$reporte -> Open();
			$reporte -> AddPage();
			
			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 20, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(38);
			$reporte -> MultiCell(188,3,$datos2["banco"],0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(46);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(42);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"13 / AGO / 2008",0,'L',0);
			
			if($registro>830000 && $registro<840000){
				
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"NUEVO INGRESO TECNÓLOGO",0,'L',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$750.00",0,'R',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$957.00",0,'R',0);
			}
			else{
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"REINSCRIPCION TECNÓLOGO",0,'L',0);
							
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
			}
			
			$reporte -> SetY(106);
			$reporte -> MultiCell(179,3,"BANCO",0,'C',0);
			
			$reporte -> SetY(126);
			$reporte -> MultiCell(0,3,"REVISIÓN 2                                                    A partir del 01 de Julio del 2006                                                    FR-02-DPL-CE-PO-004",0,'C',0);

			//////////////////////////////////////////////
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 150, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(168);
			$reporte -> MultiCell(188,3,$datos2["banco"],0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(176);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(172);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"13 / AGO / 2008",0,'L',0);
			
			
			if($registro>830000 && $registro<840000){
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"NUEVO INGRESO TECNÓLOGO",0,'L',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
			
			
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"$750.00",0,'R',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$957.00",0,'R',0);
			}
			else{
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"REINSCRIPCION TECNÓLOGO",0,'L',0);
							
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$620.00",0,'R',0);
			}
			
			$reporte -> SetY(236);
			$reporte -> MultiCell(179,3,"ALUMNO",0,'C',0);
			
			$reporte -> SetY(256);
			$reporte -> MultiCell(0,3,"***** CONSERVA TU COPIA PARA CUALQUIER ACLARACIÓN POSTERIOR *****",0,'C',0);
						
			$reporte -> Output("public/files/fichas/".$registro.".pdf");
			
			$this->redirect("files/fichas/".$registro.".pdf");
		}
		
		function pagoIng(){
			$this -> set_response("view");
			$registro = $this -> post("registro");
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("ingenieria",$cnx);
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro;
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			if($datos["vcNomAlu"]==""){
				echo "El alumno no se encuentra registrado o se encuentra dado de baja";
				$this->redirect('tecnologo/ficha');
				return;
			}
			
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=32008";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
			
			$reporte = new FPDF();
			
			$reporte -> Open();
			$reporte -> AddPage();
			
			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 20, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(38);
			$reporte -> MultiCell(188,3,$datos2["banco"],0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(46);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(42);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"13 / AGO / 2008",0,'L',0);
			
			if($registro>830000 && $registro<840000){
				
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"NUEVO INGRESO INGENIERÍA",0,'L',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$820.00",0,'R',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$1027.00",0,'R',0);
			}
			else{
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"REINSCRIPCION INGENIERÍA",0,'L',0);
							
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
			}
			
			$reporte -> SetY(106);
			$reporte -> MultiCell(179,3,"BANCO",0,'C',0);
			
			$reporte -> SetY(126);
			$reporte -> MultiCell(0,3,"REVISIÓN 2                                                    A partir del 01 de Julio del 2006                                                    FR-02-DPL-CE-PO-004",0,'C',0);

			//////////////////////////////////////////////
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 150, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(168);
			$reporte -> MultiCell(188,3,$datos2["banco"],0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(176);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(172);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"13 / AGO / 2008",0,'L',0);
			
			
			if($registro>830000 && $registro<840000){
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"NUEVO INGRESO INGENIERÍA",0,'L',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
			
			
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"$820.00",0,'R',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$1027.00",0,'R',0);
			}
			else{
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"REINSCRIPCION INGENIERÍA",0,'L',0);
							
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
			}
			
			$reporte -> SetY(236);
			$reporte -> MultiCell(179,3,"ALUMNO",0,'C',0);
			
			$reporte -> SetY(256);
			$reporte -> MultiCell(0,3,"***** CONSERVA TU COPIA PARA CUALQUIER ACLARACIÓN POSTERIOR *****",0,'C',0);
						
			$reporte -> Output("public/files/fichas/".$registro.".pdf");
			
			$this->redirect("files/fichas/".$registro.".pdf");
		}
	
		function cambiarLetras($referencia){
			$nueva = "";
			
			$referencia = strtoupper($referencia);
			
			for($i=0;$i<strlen($referencia);$i++){
				switch(substr($referencia,$i,1)){
					case 'A': case 'B': case 'C': 	$nueva .= 2; break;
					case 'D': case 'E': case 'F': 	$nueva .= 3; break;
					case 'G': case 'H': case 'I': 	$nueva .= 4; break;
					case 'J': case 'K': case 'L': 	$nueva .= 5; break;
					case 'M': case 'N': case 'O': 	$nueva .= 6; break;
					case 'P': case 'Q': case 'R': 	$nueva .= 7; break;
					case 'S': case 'T': case 'U': 	$nueva .= 8; break;
					case 'V': case 'W': case 'X': 	$nueva .= 9; break;
					case 'Y': case 'Z': 			$nueva .= 0; break;
					default: $nueva .= substr($referencia,$i,1);
				}
			}
			
			return $nueva;
		}
		
		function digitoVerificador($referencia){
			$tmp = "";
			$temporal = "";
			$x = 2;
			$suma = 0;
			for($i = strlen($referencia)-1;$i>=0;$i--){
				$numero = substr($referencia,$i,1)*$x;
				$tmp = " ". $numero . $tmp;
				while($numero>=10){
					$tempo = 0;
					for($k=0;$k<strlen($numero);$k++){
						$tempo += substr($numero,$k,1);
					}
					$numero = $tempo;
				}
				$temporal = $numero . $temporal;
				$suma = $suma + $numero;
				if($x==2){ $x=1; continue;}
				if($x==1){ $x=2; continue;}
			}
			$residuo = $suma % 10;
		
			$digito = 10 - $residuo;
			
			if($digito==10) $digito = 0;
		
			return $digito;
		}
	
		function pagoextras($registro,$examen,$materia){
			$this -> set_response("view");
			
			if($examen=="E"){
				$examen = 401;
			}
			if($examen=="T"){
				$examen = 501;
			}
			
			$materias = new Materia();
			$material = $materias -> find_first("clave='".$materia."'");
			
			$material = $material -> clave . " - " . $material -> nombre;
			
			$materia = str_replace("-","",$materia);
			
			$referencia = $examen.$materia.$registro."308";
			
			$referenciatmp = $this -> cambiarLetras($referencia);
			$referencia .= $this -> digitoVerificador($referenciatmp);
			
			$cnx = mysql_connect("localhost","humberto","juana y susana");
			mysql_select_db("ingenieria",$cnx);
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro;
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=32008";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
			
			$reporte = new FPDF();
			
			$reporte -> Open();
			$reporte -> AddPage();
			
			$reporte -> AddFont('Verdana','','verdana.php');
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 20, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(38);
			$reporte -> MultiCell(188,3,$referencia,0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(46);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(42);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"14 / ENE / 2009",0,'L',0);
			
			if($examen==401){
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"PAGO DE EXAMEN EXTRAORDINARIO",0,'L',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(100,3,$material,0,'L',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(100,3,"",0,'L',0);
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$150.00",0,'R',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$150.00",0,'R',0);
			}
			else{
				$reporte -> SetY(67);
				$reporte -> MultiCell(100,3,"PAGO DE EXAMEN TITULO DE SUFICIENCIA",0,'L',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(100,3,$material,0,'L',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(100,3,"",0,'L',0);
				$reporte -> SetY(67);
				$reporte -> MultiCell(80,3,"$170.00",0,'R',0);
				$reporte -> SetY(70);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				$reporte -> SetY(73);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				
				$reporte -> SetY(80);
				$reporte -> MultiCell(80,3,"$170.00",0,'R',0);
			}
			
			$reporte -> SetY(106);
			$reporte -> MultiCell(179,3,"BANCO",0,'C',0);
			
			$reporte -> SetY(126);
			$reporte -> MultiCell(0,3,"REVISIÓN 2                                                    A partir del 01 de Julio del 2006                                                    FR-02-DPL-CE-PO-004",0,'C',0);

			//////////////////////////////////////////////
			
			$reporte -> Ln();
			
			$reporte -> Image('http://localhost/ingenieria/img/formatoficha.jpg', 5, 150, 200, 90);
			$reporte -> SetFont('Verdana','',10);
			
			$reporte -> SetX(50);
			$reporte -> SetY(168);
			$reporte -> MultiCell(188,3,$referencia,0,'R',0);
			
			$reporte -> SetFont('Verdana','',8);
			
			$reporte -> SetX(50);
			$reporte -> SetY(176);
			$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
			
			$reporte -> SetFont('Verdana','',7);
			
			$reporte -> Ln();
			$reporte -> SetX(2);
			$reporte -> SetY(172);
			
			$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
			
			$reporte -> Ln();
			$reporte -> Ln();
			$reporte -> Ln();
			
			$reporte -> SetX(20);
			$reporte -> MultiCell(0,3,"14 / ENE / 2009",0,'L',0);
			
			if($examen==401){
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"",0,'L',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(100,3,"PAGO DE EXAMEN EXTRAORDINARIO",0,'L',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(100,3,$material,0,'L',0);
			
			
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(80,3,"$150.00",0,'R',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$150.00",0,'R',0);
			}
			else{
				$reporte -> SetY(197);
				$reporte -> MultiCell(100,3,"",0,'L',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(100,3,"PAGO DE EXAMEN TITULO DE SUFICIENCIA",0,'L',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(100,3,$material,0,'L',0);
			
			
				$reporte -> SetY(197);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				$reporte -> SetY(200);
				$reporte -> MultiCell(80,3,"$170.00",0,'R',0);
				$reporte -> SetY(203);
				$reporte -> MultiCell(80,3,"",0,'R',0);
				
				$reporte -> SetY(210);
				$reporte -> MultiCell(80,3,"$170.00",0,'R',0);
			}
			
			$reporte -> SetY(236);
			$reporte -> MultiCell(179,3,"ALUMNO",0,'C',0);
			
			$reporte -> SetY(256);
			$reporte -> MultiCell(0,3,"***** CONSERVA TU COPIA PARA CUALQUIER ACLARACIÓN POSTERIOR *****",0,'C',0);

			$reporte -> Output("public/files/extraordinarios/".$referencia.".pdf");
			
			$this->redirect("files/extraordinarios/".$referencia.".pdf");
		}
	
	}
?>