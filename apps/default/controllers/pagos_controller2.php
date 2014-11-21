<?php		
	class PagosController extends ApplicationController {
		
		public $anterior= 12011;
		public $actual	= 32011;
		public $proximo = 12012;
		
		function index(){
			echo "<center><h1>Modulo de pagos</h1></center>";
		}
		
		function salir(){
			
		}
		
		function verificarPagoExtrasYTitulos(){
			
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			//abro el archivo para lectura
			// if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos32009.txt", "r") )
			//if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos12010.txt", "r") )
			//if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos32010.txt", "r") )
			//if ( !$archivo = fopen ("http://ase.ceti.mx/ingenieria/public/files/ExtrasYTitulos12011.txt", "r") )
			if ( !$archivo = fopen ("http://ase.ceti.mx/ingenieria/public/files/ExtrasYTitulos32011.txt", "r") )
				return;
			
			//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
			$num_lineas = 0; 
			//$caracteres = 0; 
			$TGO = 0;
			$ING = 0;
			$colomos = 0;
			$tonala = 0;
			$noencontroenXalXtalcursos = 0;
			
			// $patron = "^[[:digit:]]+$";
			
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
			while (!feof ($archivo)) {
				//si extraigo una línea del archivo y no es false 
				if ($linea = fgets($archivo)){
					//acumulo una en la variable número de líneas 
				   $num_lineas++;
					//acumulo el número de caracteres de esta línea 
					//$caracteres += strlen($linea);
					$referencia = substr($linea,65,18);
					$pago = substr($linea,58,3);
					$nombre = substr($linea,83,20);
					
					if ( substr($referencia, 0, 3) == 400 || substr($referencia, 0, 3) == 500 
							|| substr($referencia, 0, 1) == 0 ) {
						$TGO++;
						
					}else{
						// Costo Examenes
						// 150 Extraordinario
						// 170 Titulo De Suficiencia
						//print "<p>La cadena $cadena1 son sólo números.</p>\n";
						// http:/localhost/ingenieria/pagos/verificarPagoExtrasYTitulos
						$ING++;
						$PlantelC = 0;
						$PlantelT = 0;
						if( (int)substr($referencia, 6, 1) > 0 ){
							
							$xalumnocursos	= new Xalumnocursos();
							$xextras		= new Xextraordinarios();
							$xtextras		= new Xtextraordinarios();
							$xtalumnocursos = new Xtalumnocursos();
							
							if( $xal = $xalumnocursos -> find_first( "id = ".substr($referencia, 6, 6)." and periodo = ".$this -> actual ) ){
								$PlantelC++;
							}
							else if( $xal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 6, 6)." and periodo = ".$this -> actual ) ){
								$PlantelT++;
							}
							else{
								echo $referencia."<br />";
								$noencontroenXalXtalcursos++;
								continue;
							}
							
							if( $PlantelC > 0 ){
								$xextras -> find_first
										( "clavecurso = '".$xal -> curso."'"."
											and registro = ".$xal -> registro."
												and periodo  = ".$this -> actual );
								$xextras -> estado = "OK";
								$xextras -> periodo = $this -> actual;
								$xextras -> save();
								$colomos++;
							}
							else if( $PlantelT > 0 ){
								$xtal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 6, 6) );
								$xtextras -> find_first
										( "clavecurso = '".$xtal -> curso."'"."
											and registro = ".$xtal -> registro."
												and periodo  = ".$this -> actual );
								$xtextras -> estado = "OK";
								$xtextras -> periodo = $this -> actual;
								$xtextras -> save();
								$tonala++;
							}
							
						}
						else if( substr($referencia, 7, 1) != 0 ){
							
							$xalumnocursos	= new Xalumnocursos();
							$xextras		= new Xextraordinarios();
							$xtextras		= new Xtextraordinarios();
							$xtalumnocursos = new Xtalumnocursos();
							
							if( $xal = $xalumnocursos -> find_first( "id = ".substr($referencia, 7, 5) ) ){
								$PlantelC++;
							}
							else if( $xal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 7, 5) ) ){
								$PlantelT++;
							}
							else{
								echo $referencia."<br />";
								$noencontroenXalXtalcursos++;
								continue;
							}
							
							if( $PlantelC > 0 ){
								$xextras -> find_first
										( "clavecurso = '".$xal -> curso."'"."
											and registro = ".$xal -> registro );
								$xextras -> estado = "OK";
								$xextras -> periodo = $this -> actual;
								$xextras -> save();
								$colomos++;
							}
							else if( $PlantelT > 0 ){
								$xtal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 7, 5) );
								$xtextras -> find_first
										( "clavecurso = '".$xtal -> curso."'"."
											and registro = ".$xtal -> registro );
								$xtextras -> estado = "OK";
								$xtextras -> periodo = $this -> actual;
								$xtextras -> save();
								$tonala++;
							}
						}
						else if( substr($referencia, 8, 1) != 0 ){
							
							$xalumnocursos	= new Xalumnocursos();
							$xextras		= new Xextraordinarios();
							$xtextras		= new Xtextraordinarios();
							$xtalumnocursos = new Xtalumnocursos();
							
							if( $xal = $xalumnocursos -> find_first( "id = ".substr($referencia, 8, 4) ) ){
								$PlantelC++;
							}
							else if( $xal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 8, 4) ) ){
								$PlantelT++;
							}
							else{
								echo $referencia."<br />";
								$noencontroenXalXtalcursos++;
								continue;
							}
							
							if( $PlantelC > 0 ){
								$xextras -> find_first
										( "clavecurso = '".$xal -> curso."'"."
											and registro = ".$xal -> registro );
								$xextras -> estado = "OK";
								$xextras -> periodo = $this -> actual;
								$xextras -> save();
								$colomos++;
							}
							else if( $PlantelT > 0 ){
								$xtal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 8, 4) );
								$xtextras -> find_first
										( "clavecurso = '".$xtal -> curso."'"."
											and registro = ".$xtal -> registro );
								$xtextras -> estado = "OK";
								$xtextras -> periodo = $this -> actual;
								$xtextras -> save();
								$tonala++;
							}
						}
						else if( substr($referencia, 9, 1) != 0 ){
							
							$xalumnocursos	= new Xalumnocursos();
							$xextras		= new Xextraordinarios();
							$xtextras		= new Xtextraordinarios();
							$xtalumnocursos = new Xtalumnocursos();
							
							if( $xal = $xalumnocursos -> find_first( "id = ".substr($referencia, 9, 3) ) ){
								$PlantelC++;
							}
							else if( $xal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 9, 3) ) ){
								$PlantelT++;
							}
							else{
								echo $referencia."<br />";
								$noencontroenXalXtalcursos++;
								continue;
							}
							
							if( $PlantelC > 0 ){
								$xextras -> find_first
										( "clavecurso = '".$xal -> curso."'"."
											and registro = ".$xal -> registro );
								$xextras -> estado = "OK";
								$xextras -> periodo = $this -> actual;
								$xextras -> save();
								$colomos++;
							}
							else if( $PlantelT > 0 ){
								$xtal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 9, 3) );
								$xtextras -> find_first
										( "clavecurso = '".$xtal -> curso."'"."
											and registro = ".$xtal -> registro );
								$xtextras -> estado = "OK";
								$xtextras -> periodo = $this -> actual;
								$xtextras -> save();
								$tonala++;
							}
						}
						else if( substr($referencia, 10, 1) != 0 ){
							
							$xalumnocursos	= new Xalumnocursos();
							$xextras		= new Xextraordinarios();
							$xtextras		= new Xtextraordinarios();
							$xtalumnocursos = new Xtalumnocursos();
							
							if( $xal = $xalumnocursos -> find_first( "id = ".substr($referencia, 10, 2) ) ){
								$PlantelC++;
							}
							else if( $xal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 10, 2) ) ){
								$PlantelT++;
							}
							else{
								echo $referencia."<br />";
								$noencontroenXalXtalcursos++;
								continue;
							}
							
							if( $PlantelC > 0 ){
								$xextras -> find_first
										( "clavecurso = '".$xal -> curso."'"."
											and registro = ".$xal -> registro );
								$xextras -> estado = "OK";
								$xextras -> periodo = $this -> actual;
								$xextras -> save();
								$colomos++;
							}
							else if( $PlantelT > 0 ){
								$xtal = $xtalumnocursos -> find_first( "id = ".substr($referencia, 10, 2) );
								$xtextras -> find_first
										( "clavecurso = '".$xtal -> curso."'"."
											and registro = ".$xtal -> registro );
								$xtextras -> estado = "OK";
								$xtextras -> periodo = $this -> actual;
								$xtextras -> save();
								$tonala++;
							}
						}
						else
							echo "$referencia ES DE ING.<br>";
					}
				} 
			}
			fclose ($archivo); 
			echo "Líneas: " . $num_lineas." TGO: ".$TGO.
			" ING: ".$ING." Colomos: ".$colomos." Tonala: ".$tonala." NoEncontrados: ".$noencontroenXalXtalcursos;
			//echo "Caracteres: " . $caracteres;
			
		} // function verificarPagoExtrasYTitulos()
		
		function verificar_pago_intersemestrales(){
			
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			//abro el archivo para lectura
			// if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos32009.txt", "r") )
			//if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos12010.txt", "r") )
			//if ( !$archivo = fopen ("http://localhost/ingenieria/files/ExtrasYTitulos32010.txt", "r") )
			//if ( !$archivo = fopen ("http://ase.ceti.mx/ingenieria/public/files/ExtrasYTitulos12011.txt", "r") )
			if ( !$archivo = fopen ("http://ase.ceti.mx/ingenieria/public/files/42011_intersemestrales.txt", "r") )
				return;
			
			//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
			$num_lineas = 0; 
			//$caracteres = 0; 
			$TGO = 0;
			$ING = 0;
			$noencontrados = 0;
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual_intersemestral();
			// $patron = "^[[:digit:]]+$";
			
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
			while (!feof ($archivo)) {
				//si extraigo una línea del archivo y no es false 
				if ($linea = fgets($archivo)){
					//acumulo una en la variable número de líneas 
				   $num_lineas++;
					//acumulo el número de caracteres de esta línea 
					//$caracteres += strlen($linea);
					$referencia = substr($linea,65,18);
					$pago = substr($linea,58,3);
					$nombre = substr($linea,83,20);
					echo "linea: ".$referencia."<br />";
					if ( substr($referencia, 0, 3) == 400 || substr($referencia, 0, 3) == 500
							|| substr($referencia, 0, 3) == 600 || substr($referencia, 0, 3) == 700
							|| substr($referencia, 0, 1) == 0 ) {
						$TGO++;
						
					}else{
						// Costo Examenes
						// 150 Extraordinario
						// 170 Titulo De Suficiencia
						//print "<p>La cadena $cadena1 son sólo números.</p>\n";
						// http:/localhost/ingenieria/pagos/verificarPagoExtrasYTitulos
						//echo "referencia: ".$referencia."<br />";
						
						$ING++;
						if( (int)substr($referencia, 6, 1) > 0 ){
							$IntersemestralAlumnos	= new IntersemestralAlumnos();
							if( $ialumno = $IntersemestralAlumnos -> find_first( "id = ".substr($referencia, 6, 6)) ){
								$ialumno -> pago = "OK";
								$ialumno -> modificado_by = "CALCULO";
								$ialumno -> save();
							}
						}
						else if( substr($referencia, 7, 1) != 0 ){
							$IntersemestralAlumnos	= new IntersemestralAlumnos();
							if( $ialumno = $IntersemestralAlumnos -> find_first( "id = ".substr($referencia, 7, 5)) ){
								$ialumno -> pago = "OK";
								$ialumno -> modificado_by = "CALCULO";
								$ialumno -> save();
							}
						}
						else if( substr($referencia, 8, 1) != 0 ){
							$IntersemestralAlumnos	= new IntersemestralAlumnos();
							if( $ialumno = $IntersemestralAlumnos -> find_first( "id = ".substr($referencia, 8, 4)) ){
								$ialumno -> pago = "OK";
								$ialumno -> modificado_by = "CALCULO";
								$ialumno -> save();
							}
						}
						else if( substr($referencia, 9, 1) != 0 ){
							$IntersemestralAlumnos	= new IntersemestralAlumnos();
							if( $ialumno = $IntersemestralAlumnos -> find_first( "id = ".substr($referencia, 9, 3)) ){
								$ialumno -> pago = "OK";
								$ialumno -> modificado_by = "CALCULO";
								$ialumno -> save();
							}
						}
						else if( substr($referencia, 10, 1) != 0 ){
							$IntersemestralAlumnos	= new IntersemestralAlumnos();
							if( $ialumno = $IntersemestralAlumnos -> find_first( "id = ".substr($referencia, 10, 2)) ){
								$ialumno -> pago = "OK";
								$ialumno -> modificado_by = "CALCULO";
								$ialumno -> save();
							}
						}
						else{
							$noencontrados++;
							echo "$referencia ES DE ING.<br>";
						}
					}
				} 
			}
			fclose ($archivo); 
			echo "Líneas: " . $num_lineas." TGO: ".$TGO." ING: ".$ING." NoEncontrados: ".$noencontrados;
			//echo "Caracteres: " . $caracteres;
			
		} // function verificar_pago_intersemestrales()
		
		
		function migrar(){
			$Calificaciones = new Calificaciones();
			$Materias = new MateriasHorarios();
			$Temporal2 = new Temporal2();
			$Temporal = new Temporal();			
			$Horarios = new Horarios();
			$Extras = new Extras();
			
			$Periodo = new Periodos();
			
			$Periodo -> find_first("activo = 1");
			$cont = 0;
			$registros = $Temporal2 -> distinct('registro');			
			foreach($registros as $r){				
				if($extras = $Extras -> find("registro = ".$r)){				
					foreach($extras as $e){								
						$Horarios -> find_first($e -> horario_id);						
						$Materias -> find_first($Horarios -> materia_id);
						echo "<b>".$r."</b> ".$Materias -> nombre." <b>".$Materias -> clave."</b> ".$e -> calificacion." ";
						if($Temporal2 -> find_first("registro= ".$r." and clave = '".$Materias -> clave."'")){	
							echo '<span style="color: #009900">Encontrado</span><br>';
							$Temporal = new Temporal();	
							if($Temporal2 -> examen == 'E'){
								$Temporal -> concepto = 400;
								$Temporal -> pago = 120;
							}
							if($Temporal2 -> examen == 'T'){
								$Temporal -> concepto = 500;
								$Temporal -> pago = 150;
							}
							$Temporal -> registro = $r;
							$Temporal -> acta = $Horarios -> id;
							$Temporal -> resto = "0";
							$Temporal -> lugar = "CAJA";
							/*if($Temporal -> save()){
								$cont++;
							}else{
								echo "no se pudo guardar el temporal2 = ".$t2 -> id;
							}*/
							$cont++;
						}else{
							echo "<br>";
						}
					}
				}
			}
			echo "<br><br>Encontro a ".$cont;
		}		
		
		function activarPago(){
			$Temporal = new Temporal();
			$Temporal2 = new Temporal2();
			$Periodo = new Periodos();
			$Extras = new Extras();
			$Horarios = new Horarios();
			$Materias = new MateriasHorarios();
			$registros = $Temporal -> distinct("registro");
			
			$Periodo -> find_first("activo = 1");
			echo "<center><table>";
			foreach($registros as $r){
				$cuantosTemporales = $Temporal -> count("registro = ".$r);
				if($cuantosTemporal2 = $Temporal2 -> count("registro = ".$r)){}else{$cuantosTemporal2 = 0;}
				$cuantosExtras = $Extras -> count("registro = ".$r);
				$cuantosRealizo = $Extras -> count("registro = ".$r." and calificacion < 101");
				/***
					Aqui es los que tienen mas examenes pagados que los examenes hechos.	Aqui no hay problema por
					que fue abstencion del alumno.
				***/
				/*if($cuantosTemporales > $cuantosRealizo){
					echo '<span style = "color: #009900; font-weight: bold;" >';
					echo $r." - ".$cuantosTemporales." - ".$cuantosRealizo." - ".$cuantosExtras."</span>";
					$extras = $Extras ->find("registro = ".$r." and periodo = ".$Periodo -> periodo);
					$cont = 0;
					foreach($extras as $ex){
						$Extras -> find_first($ex -> id);
						$Extras -> pago = 1;
						if($Extras -> save()){
							$cont++;
						}else{
							echo "no se pudo";
						}
					}
					echo " registrados = ".$cont."<br>";
				}*/
				
				/***
					Aqui es los que tienen menos examenes pagados que los examenes hechos.
				***/
				if($cuantosTemporales < $cuantosRealizo){
					echo tr_color('#cccccc','#aaaaaa');
					echo '<td style = "color: #990000; font-weight: bold;" >';
					echo $r."</td><td>Pago: ".$cuantosTemporales."</td><td>Realizo: ".$cuantosRealizo."</td><td>Tenia: ".$cuantosExtras."</td><td>".$cuantosTemporal2."</td><td>&nbsp;</td>";
					$extras = $Extras ->find("registro = ".$r." and periodo = ".$Periodo -> periodo." and (calificacion <= 100 or calificacion = 300 or calificacion = 999)", "order: tipo DESC, calificacion DESC");
					foreach($extras as $ex){
						echo tr_color('#abcabc','#89a89a');
						$Horarios -> find_first($ex -> horario_id);
						$Materias -> find_first($Horarios -> materia_id);
						if ($ex -> calificacion >= 70)
							echo '<td style="color: #009900; font-weight: bold;">';
						else
							echo '<td style="color: #990000">';						
						echo $ex -> horario_id."</td><td>".$Materias -> nombre."</td><td>".$Materias -> clave."</td><td>".$ex -> calificacion."</td><td>".$ex -> tipo."</td><td>";						
						if($Temporal -> find_first("registro = ".$r." and acta = ".$ex -> horario_id)){					
							echo "encontrado</td>";
						}else{
							echo "&nbsp;</td>";
						}
					}				
					echo '<tr><td colspan="6">&nbsp;</td></tr>';
				}
				
				/***
					Aqui es los que tienen los mismos examenes hechos que los pagados.				
				***/
				/*if($cuantosTemporales == $cuantosRealizo){
					//echo '<span style = "color: #000000;" >';
					//echo $r." - ".$cuantosTemporales." - ".$cuantosRealizo." - ".$cuantosExtras."</span><br>";
					$extras = $Extras ->find("registro = ".$r." and periodo = ".$Periodo -> periodo);
					foreach($extras as $ex){
						$Extras -> find_first($ex -> id);
						$Extras -> pago = 1;
						if($Extras -> save()){
							
						}else{
							echo "no se pudo";
						}
						//echo $ex -> registro." - ".$ex -> horario_id."<br>";					
					}
					
				}*/
					
			}
			echo "</table></center>";
		}
		
		function verificarPago(){
			
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			//abro el archivo para lectura
			// if ( !$archivo = fopen ("http://localhost/ingenieria/files/todos3.txt", "r") )
			// if ( !$archivo = fopen ("http://localhost/ingenieria/files/pagosCETIInscripcion12010.txt", "r") )
			//if ( !$archivo = fopen ("http://localhost/ingenieria/files/InscripcionesYReinscripciones32010.txt", "r") )
				return;
			
			//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
			$num_lineas = 0;
			//$caracteres = 0; 
			$TGO = 0;
			$ING = 0; 
			
			$patron = "^[[:digit:]]+$";
			
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
			while (!feof ($archivo)) {
				//si extraigo una línea del archivo y no es false 
				if ($linea = fgets($archivo)){
					//acumulo una en la variable número de líneas 
				   $num_lineas++;
					//acumulo el número de caracteres de esta línea 
					//$caracteres += strlen($linea);
					$referencia = substr($linea,65,18);
					$pago = substr($linea,58,3);
					$nombre = substr($linea,83,20);
					if (eregi($patron, $referencia)) {
						$TGO++;
						
					}else{
						// Se necesita crear la tabla Temporal según sus necesidades antes de usar esta función
						// Abajo se encuentra el sql para la creación como se necesita dicha tabla ...
						// create table temporal (
						// id int auto_increment,
						// concepto int,
						// materia varchar (10),
						// registro int,
						// periodo int,
						// pago int,
						// resto int,
						// nombre varchar(21),
						// lugar varchar (10),
						// Primary Key (id) );
						$Temporal = new Temporal();
						$Temporal -> id = 0;
						// Dependiendo del periodo en el que se esté, se debe de cambiar este campo...
						$Temporal -> periodo = 32010;
						//print "<p>La cadena $cadena1 son sólo números.</p>\n";
						if(substr($referencia,0,1) == 0){
							$referencia = substr($referencia,1,18);
							if(substr($referencia,0,1) == 0){
								$referencia = substr($referencia,1,17);
								if(substr($referencia,0,1) == 0){
									$referencia = substr($referencia,1,16);
									if(substr($referencia,0,1) == 0){
										$referencia = substr($referencia,1,15);
										$Temporal -> concepto = substr($referencia,0,3);
										if ( is_numeric (substr($referencia, 3, 1)) )
											$Temporal -> materia = substr($referencia,3,3);
										else{
											if ( !is_numeric (substr($referencia, 5, 1)) ){
												$aux = substr($referencia,3,3);
												$aux .= "-";
												$aux .= substr($referencia,6,2);
												$Temporal -> materia = $aux;
											}
											else{
												$aux = substr($referencia,3,2);
												$aux .= "-";
												$aux .= substr($referencia,5,2);
												$Temporal -> materia = $aux;
											}
										}
										
										$Temporal -> registro = substr($referencia,6,5);	
										$Temporal -> resto = substr($referencia,10,4);
										$Temporal -> pago = $pago;
										$Temporal -> nombre = $nombre;
										$Temporal -> lugar = "banco";
										echo "concepto: ".$Temporal->concepto."<br />";
										echo "materia: ".$Temporal->materia."<br />";
										echo "registro: ".$Temporal->registro."<br />";
										echo "resto: ".$Temporal->resto."<br />";
										echo "pago: ".$Temporal->pago."<br />";
										echo "nombre: ".$Temporal->nombre."<br />";
										echo "<br /><br />";
										if($Temporal -> save()){
										
										}else{
											echo "no se pudo<br>";
										}
									}else{
										$Temporal -> concepto = substr($referencia,0,3);
										if ( is_numeric (substr($referencia, 3, 1)) )
											$Temporal -> materia = substr($referencia,3,3);
										else{
											if ( !is_numeric (substr($referencia, 5, 1)) ){
												$aux = substr($referencia,3,3);
												$aux .= "-";
												$aux .= substr($referencia,6,2);
												$Temporal -> materia = $aux;
											}
											else{
												$aux = substr($referencia,3,2);
												$aux .= "-";
												$aux .= substr($referencia,5,2);
												$Temporal -> materia = $aux;
											}
										}
										
										$Temporal -> registro = substr($referencia,6,5);
										$Temporal -> resto = substr($referencia,11,4);
										$Temporal -> pago = $pago;
										$Temporal -> nombre = $nombre;
										$Temporal -> lugar = "banco";
										echo "concepto: ".$Temporal->concepto."<br />";
										echo "materia: ".$Temporal->materia."<br />";
										echo "registro: ".$Temporal->registro."<br />";
										echo "resto: ".$Temporal->resto."<br />";
										echo "pago: ".$Temporal->pago."<br />";
										echo "nombre: ".$Temporal->nombre."<br />";
										echo "<br /><br />";
										if($Temporal -> save()){
										
										}else{
											echo "no se pudo<br>";
										}
									}
								}else{
									$Temporal -> concepto = substr($referencia,0,3);
									if ( is_numeric (substr($referencia, 3, 1)) )
										$Temporal -> materia = substr($referencia,3,3);
									else{
										if ( !is_numeric (substr($referencia, 5, 1)) ){
											$aux = substr($referencia,3,3);
											$aux .= "-";
											$aux .= substr($referencia,6,2);
											$Temporal -> materia = $aux;
										}
										else{
											$aux = substr($referencia,3,2);
											$aux .= "-";
											$aux .= substr($referencia,5,2);
											$Temporal -> materia = $aux;
										}
									}
									
									$Temporal -> registro = substr($referencia,6,6);
									$Temporal -> resto = substr($referencia,12,4);									
									$Temporal -> pago = $pago;
									$Temporal -> nombre = $nombre;
									$Temporal -> lugar = "banco";
									echo "concepto: ".$Temporal->concepto."<br />";
									echo "materia: ".$Temporal->materia."<br />";
									echo "registro: ".$Temporal->registro."<br />";
									echo "resto: ".$Temporal->resto."<br />";
									echo "pago: ".$Temporal->pago."<br />";
									echo "nombre: ".$Temporal->nombre."<br />";
									echo "<br /><br />";
									if($Temporal -> save()){
									
									}else{
										echo "no se pudo<br>";
									}
								}
							}else{
								$Temporal -> concepto = substr($referencia,0,3);
								if ( is_numeric (substr($referencia, 3, 1)) )
									$Temporal -> materia = substr($referencia,3,3);
								else{
									if ( !is_numeric (substr($referencia, 5, 1)) ){
										$aux = substr($referencia,3,3);
										$aux .= "-";
										$aux .= substr($referencia,6,2);
										$Temporal -> materia = $aux;
									}
									else{
										$aux = substr($referencia,3,2);
										$aux .= "-";
										$aux .= substr($referencia,5,2);
										$Temporal -> materia = $aux;
									}
								}
								
								$Temporal -> registro = substr($referencia,7,6);
								$Temporal -> resto = substr($referencia,13,4);
								$Temporal -> pago = $pago;
								$Temporal -> nombre = $nombre;
								$Temporal -> lugar = "banco";
								echo "concepto: ".$Temporal->concepto."<br />";
								echo "materia: ".$Temporal->materia."<br />";
								echo "registro: ".$Temporal->registro."<br />";
								echo "resto: ".$Temporal->resto."<br />";
								echo "pago: ".$Temporal->pago."<br />";
								echo "nombre: ".$Temporal->nombre."<br />";
								echo "<br /><br />";
								if($Temporal -> save()){
								
								}else{
									echo "no se pudo<br>";
								}
							}
						}else{
							$Temporal -> concepto = substr($referencia,0,3);
							
							if ( is_numeric (substr($referencia, 3, 1)) )
								$Temporal -> materia = substr($referencia,3,3);
							else{
								if ( !is_numeric (substr($referencia, 5, 1)) ){
									$aux = substr($referencia,3,3);
									$aux .= "-";
									$aux .= substr($referencia,6,2);
									$Temporal -> materia = $aux;
								}
								else{
									$aux = substr($referencia,3,2);
									$aux .= "-";
									$aux .= substr($referencia,5,2);
									$Temporal -> materia = $aux;
								}
							}
							
							if ( substr ($referencia, 7, 1) == 9 )
								$Temporal -> registro = substr($referencia,7,7);
							else
								$Temporal -> registro = substr($referencia,8,6);
							
							$Temporal -> resto = substr($referencia,14,4);
							$Temporal -> pago = $pago;
							$Temporal -> nombre = $nombre;
							$Temporal -> lugar = "banco";
							
							echo "concepto: ".$Temporal->concepto."<br />";
							echo "materia: ".$Temporal->materia."<br />";
							echo "registro: ".$Temporal->registro."<br />";
							echo "resto: ".$Temporal->resto."<br />";
							echo "pago: ".$Temporal->pago."<br />";
							echo "nombre: ".$Temporal->nombre."<br />";
							echo "<br /><br />";
							if($Temporal -> save()){
							
							}else{
								echo "no se pudo<br>";
							}
						}
						$ING++;
						//echo "$referencia ES DE ING.<br>";
					}
				} 
			}
			fclose ($archivo); 
			echo "Líneas: " . $num_lineas." TGO: ".$TGO." ING: ".$ING; 
			//echo "Caracteres: " . $caracteres; 
		}
		
		function verificarPagoCajas(){
			
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			//abro el archivo para lectura
			if ( !$archivo = fopen ("http://localhost/ingenieria/files/bancoCajas12009.txt", "r") )
				return;
			
			//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
			$num_lineas = 0; 
			//$caracteres = 0; 
			$TGO = 0;
			$ING = 0; 
			
			$counter = 0;
			$counter2 = 0;
			
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
			while (!feof ($archivo)){
				//si extraigo una línea del archivo y no es false 
				if ($linea = fgets($archivo)){
					//acumulo una en la variable número de líneas 
					$num_lineas++;
					
					$alumnos = new Alumnos ();
					$xalumnocursos = new Xalumnocursos();
					
					list($no_act, $registro, $materia, $calificacion, $fecha, $periodo,
							$tipo_examen, $pago, $fechapag, $folio) = split(";", $linea, 10);
					
					if ( $alumno = $alumnos -> find_first("miReg = ".$registro) ){
//					echo "miReg: ".$alumno -> miReg." registro: ".$registro."<br />";
//					if ( $alumno -> miReg == $registro ){
						
						$xcursos = new Xcursos();
						$xextras = new Xextraordinarios();
						
						$xextras -> clavecurso;
					
						// Traerme la materia del curso, de xalumnocursos.
						if ( ($xalumnocursos -> find_all_by_sql ("
							Select xa.curso from xalumnocursos as xa, xcursos as xc
							where xa.periodo = ". $periodo. "
							and xa.registro = ". $registro ."
							and xa.curso = xc.clavecurso
							and xc.materia like '". $materia ."'
						") ) )
						foreach ($xalumnocursos -> find_all_by_sql ("
							Select xa.curso from xalumnocursos as xa, xcursos as xc
							where xa.periodo = ". $periodo. "
							and xa.registro = ". $registro ."
							and xa.curso = xc.clavecurso
							and xc.materia like '". $materia ."'
						")as $xalumcur){
							// Poner en xextraordinario, su estado en Ok, ya que si aparece en esta lista
							// significa que si pago.
							if ( $xextras -> find ("clavecurso = '". $xalumcur -> curso ."'
														and registro = ". $registro ."
														and periodo = ". $periodo) )
							foreach ( $xextras -> find ("clavecurso = '". $xalumcur -> curso ."'
														and registro = ". $registro ."
														and periodo = ". $periodo) as $xextra ){
								$xextra -> estado = 'OK';
								$xextra -> save();
								$counter ++;
							}
							else{
								echo "Los que no estan en XExtraordinarios
										Registro: ".$registro." materia: ".$materia."<br />";
							}
							$counter2++;
						}
						else{
							echo "Registro: ".$registro." materia: ".$materia."<br />";
						}
						echo "Contador: ".$counter." Contador2: ".$counter2."<br />";
						$ING++;
					}
					else
						$TGO++;
				}
			}
			fclose ($archivo); 
			echo "Líneas: " . $num_lineas." TGO: ".$TGO." ING: ".$ING; 
			//echo "Caracteres: " . $caracteres; 
		}
		
		function verificarPagoInicio(){ // el chido
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			// Abro el archivo para lectura
			//if( !$archivo = fopen("http://localhost/ingenieria/files/pagosInicio.txt", "r") )
			//if( !$archivo = fopen("http://localhost/ingenieria/files/pagosCETIInscripcion12010.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripciones32010.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripciones32010PrimerRecargo.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo12011.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo12011PrimerRecargo.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo32011.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo12012.txt", "r") )
			//if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo12012-07y08Feb.txt", "r") )
			if( !$archivo = fopen("http://ase.ceti.mx/ingenieria/files/InscripcionesYReinscripcionesSinRecargo12012-09y10Feb.txt", "r") )
				return;
			
			//inicializo una variable para llevar la cuenta de las líneas y los caracteres 
			$num_lineas = 0; 
			//$caracteres = 0; 
			$TGO = 0;
			$ING = 0; 
			
			$patron = "^[[:digit:]]+$";
			$k = 0;
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo 
			while (!feof ($archivo)){
				//si extraigo una línea del archivo y no es false 
				if ($linea = fgets($archivo)){
					//acumulo una en la variable número de líneas 
				   $num_lineas++;
					//acumulo el número de caracteres de esta línea 
					//$caracteres += strlen($linea);
					
					$referencia = substr($linea,65,18);
					
					if ( substr($linea,57,1) == 0 )
						if( substr($linea,58,1) == 0 )
							$pago = substr( $linea, 59, 3 );
						else
							$pago = substr( $linea, 58, 3 );
					else
						$pago = substr( $linea, 57, 4 );
					
					$nombre = substr($linea,83,20);
					
					// Se necesita crear la tabla Temporal según sus necesidades antes de usar esta función
					// Abajo se encuentra el sql para la creación como se necesita dicha tabla ...
					// Create table if not exists temporal2 (
					// id int auto_increment,
					// concepto int,
					// registro int,
					// periodo int,
					// pago int,
					// nombre varchar (45),
					// recargo tinyint,
					// lugar varchar (15),
					// Primary Key (id)
					// );
					$Temporal	= new Temporal2();
					$temporal2	= new Temporal2();
					// Dependiendo del periodo en el que se esté, se debe de cambiar este campo...
					$Periodos = new Periodos();
					$periodo = $Periodos -> get_periodo_actual();
					$Temporal -> periodo = $periodo;
					//print "<p>La cadena $cadena1 son sólo números.</p>\n";
					//echo $referencia;
					//exit(1);
					//002019310110120100
					
					$aux = 0;
					
					//if( $pago == 1027 || $pago == 1257 || $pago == 690 || $pago == 920 ){
					if( $pago == 1767 || $pago == 1257 || $pago == 806 || $pago == 920 ){
						$ING++;
					}
					else{
						$TGO++;
						continue;
					}
					
					if ( substr($referencia,0,3) == 101 || substr($referencia,1,3) == 101 
							|| substr($referencia,2,3) == 101 || substr($referencia,3,3) == 101 ){
						if( substr($linea,69,1) != 0 ){
							$registro = substr($linea,69,8);
						}
						else if( substr($linea,70,1) != 0 ){
							$registro = substr($linea,70,7);
						}
						else if( substr($linea,71,1) != 0 ){
							$registro = substr($linea,71,6);
						}
						else if( substr($linea,72,1) != 0 ){
							$registro = substr($linea,72,5);
						}
						if( $temporal2 -> find_first
								( "registro = ".$registro."
									and periodo = ".$Temporal -> periodo ) ){
							continue;
						}
						echo "<br />concepto: ".$concepto = substr($referencia,0,3);
						echo "<br />";
						echo $Temporal -> registro = $registro;
						echo "<br />primero";
						$Temporal -> concepto = $concepto;
						$Temporal -> pago = $pago;
						$Temporal -> nombre = $nombre;
						
						if( $pago == 1767 )
							$Temporal -> recargo = '0';
						else if( $pago == 1257 )
							$Temporal -> recargo = 1;
						else
							$Temporal -> recargo = 2;
						
						
						$Temporal -> lugar = "banco";
						echo "<br />concepto: ".$Temporal->concepto;
						echo "<br />registro: ".$Temporal->registro;
						echo "<br />pago: ".$Temporal->pago;
						echo "<br />nombre: ".$Temporal->nombre;
						echo "<br /><br />";
						if($Temporal -> create()){
						
						}else{
							echo "no se pudo<br>";
						}
						$aux++;
						
					}
					else if ( substr($referencia,0,3) == 201 || substr($referencia,1,3) == 201 
							|| substr($referencia,2,3) == 201 || substr($referencia,3,3) == 201
								|| substr($referencia,4,3) == 201 ){
						if( substr($linea,69,1) != 0 ){
							$registro = substr($linea,69,8);
						}
						else if( substr($linea,70,1) != 0 ){
							$registro = substr($linea,70,7);
						}
						else if( substr($linea,71,1) != 0 ){
							$registro = substr($linea,71,6);
						}
						else if( substr($linea,72,1) != 0 ){
							$registro = substr($linea,72,5);
						}
						if( $temporal2 -> find_first
								( "registro = ".$registro."
									and periodo = ".$Temporal -> periodo ) ){
							continue;
						}
						
						echo "<br />concepto: ".$concepto = substr($referencia,0,3);
						echo "<br />";
						echo $Temporal -> registro = $registro;
						echo "<br />primero";
						$Temporal -> concepto = $concepto;
						$Temporal -> pago = $pago;
						$Temporal -> nombre = $nombre;
						
						if( $pago == 806 )
							$Temporal -> recargo = '0';
						else if( $pago == 920 )
							$Temporal -> recargo = 1;
						else
							$Temporal -> recargo = 2;
						
						
						$Temporal -> lugar = "banco";
						echo "<br />concepto: ".$Temporal->concepto;
						echo "<br />registro: ".$Temporal->registro;
						echo "<br />pago: ".$Temporal->pago;
						echo "<br />nombre: ".$Temporal->nombre;
						echo "<br /><br />";
						if($Temporal -> create()){
						
						}else{
							echo "no se pudo<br>";
						}
						$aux++;
					}
					if( $aux == 0 ){
						echo "<br />linea: ".$linea;
						echo "<br />ParteDeLinea: ".$pago." ref: ".$referencia." 69,3: ".substr($linea,70,3);
						echo " 70,1: ".substr($linea,70,1)." 71,1: ".substr($linea,71,1)." 65,18: ".substr($linea,65,18);
					}
				} // if ($linea = fgets($archivo))
			} // while (!feof ($archivo))
			fclose ($archivo);
			echo "Líneas: " . $num_lineas." TGO: ".$TGO." ING: ".$ING;
			//echo "Caracteres: " . $caracteres;
		} // function verificarPagoInicio()
		
		function verificarSiPago(){
			
			// Verifico si el Usuario es Calculo, si no, no podrá ejecutar esta función
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
			
			$xalumnocursos = new Xalumnocursos();
			$xcursos = new Xcursos();
			$xextras = new Xextraordinarios();
			// La tabla temporal necesita tener datos, por lo que es necesario ejecutar primero
			// la funcion de verificarPago.
			$temporal = new Temporal ();
			$counter = 0;
			foreach ( $temporal -> find_all_by_sql("Select * from temporal") as $temp ){
				foreach ($xalumnocursos -> find_all_by_sql ("
					Select xa.curso from xalumnocursos as xa, xcursos as xc
					where xa.periodo = ". $temp -> periodo. "
					and xa.registro = ". $temp -> registro ."
					and xa.curso = xc.clavecurso
					and xc.materia like '". $temp-> materia ."'
				")as $xalumcur){
					foreach ( $xextras -> find ("clavecurso = '". $xalumcur -> curso ."'
												and registro = ". $temp -> registro ."
												and periodo = ". $temp -> periodo) as $xextra ){
						
						$xextra -> estado = 'OK';
						$xextra -> save();
						$counter ++;
						echo $counter;
					}
				}
			}
		}
		
		function fichaAdmitidos(){
			$this -> set_response("view");
			
			// $NumConcepto=101, $Registro = 10110042
			
			$NumConcepto = 103; // Concepto, Inscripción Ingeniería 820 con Recargo
			//$NumConcepto = 103; // Concepto, Inscripción 1er Recargo Ingeniería 230
			//$NumConcepto = 105; // Concepto, Inscripción 2do Recargo Ingeniería 300
			//$NumConcepto = 105; // Concepto, Inscripción 3er Recargo Ingeniería 300
			
			$Registro = $this -> post("registro");
			
			$Alumno			= new Alumnos();
			$Periodo		= new Periodos();
			$ConceptoPago	= new ConceptosPago();
			$tablaAdmitidos	= new Admitidos();
			
			$RegistroSolo = $Registro;
			if (strlen($Registro)== 8){
				$Registro="0".$Registro;
			}
			if (strlen($Registro)== 7){
				$Registro="00".$Registro;
			}
			if (strlen($Registro)== 6){
				$Registro="000".$Registro;
			}
			
			$Periodo -> find_first("activo = 1");
			
			$nombre="";
			$concepto="";			
			$pagoConcepto="";			
			$recargo="";
			$pagoRecargo="";
			$fechaVencimiento="";
			$cuenta="";
			$copia = "ALUMNO";
			//$subConcepto=NULL;			
			
			if( $tAdmitidos = $tablaAdmitidos -> find_first( "registro = ".$RegistroSolo ) ){
				
				$reporte = new FPDF();
				$reporte -> Open();
				$reporte -> AddFont('Verdana','','verdana.php');
				$reporte -> AddPage();
				
				for($i=0; $i<2; $i++ ){
					
					//echo $i."".$copia;
					$nombre = $tAdmitidos -> nombre;
					// echo $NumConcepto;
					if($NumConcepto < 301){
						if ($Periodo -> periodo == 12010){
							$NumConcepto = 101;
							$ConceptoPago -> find_first("id = 101");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							
							$subConcepto[1] [1] = "CREDENCIAL ALUMNO";
							$subConcepto[2] [1] = "MANUAL DE NUEVO INGRESO";
							$subConcepto[1] [2] = "70";
							$subConcepto[2] [2] = "137";
							
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 103");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;							
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 105");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 107");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
						else{
							$NumConcepto = 201;
							$ConceptoPago -> find_first("id = 201");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 203");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 205");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 207");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
					}else{
						if($NumConcepto >= 401 && $NumConcepto < 501){
							$ConceptoPago -> find_first("id = 401"); // Extraordinario Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 501 && $NumConcepto < 601){
							$ConceptoPago -> find_first("id = 501"); //Titulo Suficiencia Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 601 && $NumConcepto < 701){
							$ConceptoPago -> find_first("id = 601"); // Curso de Nivelación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
						}
						if($NumConcepto >= 701 && $NumConcepto < 801){							
							$ConceptoPago -> find_first("id = 701"); // Curso de Acreditación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
						}
					}
					
					$referencia_sin_dv = $NumConcepto.$Registro."32010";
					$referenciaPa_sin_dv = "30".$RegistroSolo."110"; // 30 es el patronato
					$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
					$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);
					/*foreach($subConcepto as $sc)
						echo $sc -> concepto." - ".$sc -> costo."<br>";				*/
					//echo $registro." - ".$nombre." - ".$concepto." - ".$NumConcepto." - ".$pagoConcepto." - ".$recargo." - ".$pagoRecargo." - ".$fechaVencimiento." - ".$cuenta;
					
					$reporte -> Ln();
					//$reporte -> Image('http://localhost/tecnologo/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					// $reporte -> Image('/var/www/htdocs/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> Image('/datos/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> SetFont('Verdana','',10);
					
					$posRef = 37;
					$reporte -> SetX(45);
					$reporte -> SetY($posRef);
					$reporte -> MultiCell(190,3,$referencia,0,'R',0);
					
					$reporte -> SetFont('Verdana','',8);
					
					$reporte -> SetX(50);
					$posEmpresa = 44;
					$reporte -> SetY($posEmpresa);
					$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
					
					$reporte -> SetFont('Verdana','',7);
					
					$reporte -> Ln();
					$reporte -> SetX(2);
					$reporte -> SetY(41);
					
					$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
					
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					
					$reporte -> SetX(20);
					$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
					
					$py = 63;	
					$total = 0;
					$reporte -> SetY($py);
					$reporte -> MultiCell(100,3,$concepto,0,'L',0);				
					$reporte -> SetY($py);
					
					$reporte -> MultiCell(80,3,'$'.$pagoConcepto.'.00',0,'R',0);
					
					$total = $pagoConcepto;
					if ($Periodo -> recargo > 0 && $NumConcepto <= 300){						
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$recargo,0,'L',0);	
						$reporte -> SetY($py);
						$reporte -> MultiCell(80,3,'$'.$pagoRecargo.'.00',0,'R',0);
						$total = $total + $pagoRecargo;						
					}
					
					if ($subConcepto != NULL){
						foreach($subConcepto as $sc){
							$py = $py + 3;
							$reporte -> SetY($py);
							$reporte -> MultiCell(100,3,$sc [1],0,'L',0);	
							$reporte -> SetY($py);
							$reporte -> MultiCell(80,3,'$'.$sc [2].'.00',0,'R',0);
							$total = $total + $sc [2];
						}
					}
					
					if ($NumConcepto > 300){
						$Materia = new Materia();
						$Materia -> find_first("clave = '".$clavemat."'");
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);							
					}
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,'$'.$total.'.00',0,'R',0);
										
					$reporte -> SetY(101);
					$reporte -> MultiCell(179,3,$copia,0,'C',0);					
						
					if($NumConcepto <= 301){
						
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referenciaPa,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
									
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
			
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
						if ($i < 1)						
							$reporte -> AddPage();	
						if ($i == 1){
							$copia = "BANCO";
						}
					}else{														
						if ($i == 1){
							$copia = "BANCO";
						}
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referencia,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,$concepto,0,'L',0);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(80,3,"$".$pagoConcepto.".00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$".$total.".00",0,'R',0);
						
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
					}
				}
				$reporte -> Output("public/files/fichas/".$NumConcepto.$RegistroSolo.".pdf");
				
				$this->redirect("files/fichas/".$NumConcepto.$RegistroSolo.".pdf");
			}
			else{
				echo "<br><br><br><center><h1>El alumno no se encuentra registrado o se encuentra dado de baja</h1>";
				echo '<br><br><b><a href="http://cira.ceti.mx"> << Volver</a></b></center>';				
			}
		} // function fichaAdmitidos()
		
		function fichaReinscripcion(){
			$this -> set_response("view");
			
			// $NumConcepto=101, $PreRegistro = 30000001
			
			$NumConcepto = $this -> post("NumConcepto");
			//$NumConcepto = 203; // Primer Recardo Ingeniería
			//$NumConcepto = 201; 
			// Concepto, Reinscripción Ingeniería 201
			//$NumConcepto = 203; // Concepto, Reinscripción 1er Recargo Ingeniería 230
			//$NumConcepto = 205; // Concepto, Reinscripción 2do Recargo Ingeniería 300
			//$NumConcepto = 205; // Concepto, Reinscripción 3er Recargo Ingeniería 300
			
			$Registro = $this -> post("registro");
			
			$Alumno			= new Alumnos();
			$Periodo		= new Periodos();
			$ConceptoPago	= new ConceptosPago();
			
			$RegistroSolo = $Registro;
			// 611117   6  digitos
			// 10110031  8 digitos para el patronato debe tener 8
			
			if (strlen($Registro)== 7){
				$Registro="00".$Registro;
			}
			if (strlen($Registro)== 6){
				$Registro="000".$Registro;
			}
			if (strlen($Registro)== 5){
				$Registro="0000".$Registro;
			}
			if (strlen($Registro)== 4){
				$Registro="00000".$Registro;
			}
			
			if (strlen($RegistroSolo)== 7){
				$RegistroPatronato="0".$RegistroSolo;
			}
			if (strlen($RegistroSolo)== 6){
				$RegistroPatronato="00".$RegistroSolo;
			}
			if (strlen($RegistroSolo)== 5){
				$RegistroPatronato="000".$RegistroSolo;
			}
			if (strlen($RegistroSolo)== 4){
				$RegistroPatronato="0000".$RegistroSolo;
			}
			
			$Periodo -> find_first("activo = 1");
			
			$nombre="";
			$concepto="";			
			$pagoConcepto="";			
			$recargo="";
			$pagoRecargo="";
			$fechaVencimiento="";
			$cuenta="";
			$copia = "ALUMNO";
			//$subConcepto=NULL;			
			
			if( $alumno = $Alumno -> find_first( "miReg = ".$RegistroSolo ) ){
				
				$reporte = new FPDF();
				$reporte -> Open();
				$reporte -> AddFont('Verdana','','verdana.php');
				$reporte -> AddPage();
				
				for($i=0; $i<2; $i++ ){
					
					//echo $i."".$copia;
					$nombre = $alumno -> vcNomAlu;
					// echo $NumConcepto;
					if($NumConcepto < 301){
						if ($alumno -> miPerIng == 12012){
							$NumConcepto = 101;
							$ConceptoPago -> find_first("id = 101");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							
							$subConcepto[1] [1] = "CREDENCIAL ALUMNO";
							$subConcepto[2] [1] = "MANUAL DE NUEVO INGRESO";
							$subConcepto[1] [2] = "120";
							$subConcepto[2] [2] = "160";
							
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 103");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;							
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 105");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 107");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
						else{
							$NumConcepto = 201;
							$ConceptoPago -> find_first("id = 201");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 203");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 205");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 207");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
					}else{
						if($NumConcepto >= 401 && $NumConcepto < 501){
							$ConceptoPago -> find_first("id = 401"); // Extraordinario Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 501 && $NumConcepto < 601){
							$ConceptoPago -> find_first("id = 501"); //Titulo Suficiencia Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 601 && $NumConcepto < 701){
							$ConceptoPago -> find_first("id = 601"); // Curso de Nivelación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
						}
						if($NumConcepto >= 701 && $NumConcepto < 801){							
							$ConceptoPago -> find_first("id = 701"); // Curso de Acreditación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
						}
					}
					
					$referencia_sin_dv = $NumConcepto.$Registro."12012";
					//$referenciaPa_sin_dv = "30".$RegistroPatronato."110"; // 30 es el patronato
					$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
					//$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);
					/*foreach($subConcepto as $sc)
						echo $sc -> concepto." - ".$sc -> costo."<br>";				*/
					//echo $registro." - ".$nombre." - ".$concepto." - ".$NumConcepto." - ".$pagoConcepto." - ".$recargo." - ".$pagoRecargo." - ".$fechaVencimiento." - ".$cuenta;
					
					$reporte -> Ln();
					//$reporte -> Image('http://localhost/tecnologo/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					// $reporte -> Image('/var/www/htdocs/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> Image('/datos/calculo/ingenieria/public/img/formatoFichaReinscripcion.jpg', 5, 20, 200, 220);
					$reporte -> SetFont('Verdana','',10);
					
					$posRef = 37;
					$reporte -> SetX(45);
					$reporte -> SetY($posRef);
					$reporte -> MultiCell(190,3,$referencia,0,'R',0);
					
					$reporte -> SetFont('Verdana','',8);
					
					$reporte -> SetX(50);
					$posEmpresa = 44;
					$reporte -> SetY($posEmpresa);
					$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
					
					$reporte -> SetFont('Verdana','',7);
					
					$reporte -> Ln();
					$reporte -> SetX(2);
					$reporte -> SetY(41);
					
					$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
					
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					
					$reporte -> SetX(20);
					$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
					
					$py = 63;	
					$total = 0;
					$reporte -> SetY($py);
					$reporte -> MultiCell(100,3,$concepto,0,'L',0);				
					$reporte -> SetY($py);
					
					$reporte -> MultiCell(80,3,'$'.$pagoConcepto.'.00',0,'R',0);
					
					$total = $pagoConcepto;
					if ($Periodo -> recargo > 0 && $NumConcepto <= 300){						
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$recargo,0,'L',0);	
						$reporte -> SetY($py);
						$reporte -> MultiCell(80,3,'$'.$pagoRecargo.'.00',0,'R',0);
						$total = $total + $pagoRecargo;						
					}
					
					if ($subConcepto != NULL){
						foreach($subConcepto as $sc){
							$py = $py + 3;
							$reporte -> SetY($py);
							$reporte -> MultiCell(100,3,$sc [1],0,'L',0);	
							$reporte -> SetY($py);
							$reporte -> MultiCell(80,3,'$'.$sc [2].'.00',0,'R',0);
							$total = $total + $sc [2];
						}
					}
					
					if ($NumConcepto > 300){
						$Materia = new Materia();
						$Materia -> find_first("clave = '".$clavemat."'");
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);							
					}
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,'$'.$total.'.00',0,'R',0);
										
					$reporte -> SetY(101);
					$reporte -> MultiCell(179,3,$copia,0,'C',0);					
						
					if($NumConcepto <= 301){
						/*
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referenciaPa,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
									
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
			
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
						*/
						if ($i < 1)					
							$reporte -> AddPage();	
						if ($i == 1){
							$copia = "BANCO";
						}
					}else{
						if ($i == 1){
							$copia = "BANCO";
						}
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referencia,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$RegistroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,$concepto,0,'L',0);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(80,3,"$".$pagoConcepto.".00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$".$total.".00",0,'R',0);
						
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
					}
				}
				
				$reporte -> Output("public/files/fichas/".$NumConcepto.$RegistroSolo.".pdf");
				
				$this->redirect("files/fichas/".$NumConcepto.$RegistroSolo.".pdf");
			}
			else{
				echo "<br><br><br><center><h1>El alumno no se encuentra registrado o se encuentra dado de baja</h1>";
				echo '<br><br><b><a href="http://ase.ceti.mx"> << Volver</a></b></center>';				
			}			
		} // function fichaReinscripcion()
		
		function ficha2($NumConcepto=101, $registro = 831001, $clavemat= 0, $idxalumnocurso= 0){
			$this -> set_response("view");
			
			$Alumno = new Alumnos();
			$Periodo = new Periodos();
			$ConceptoPago = new ConceptosPago();
			
			$Periodo -> find_first("activo = 1");
			$periodo = $this -> actual;
			$registroSolo = $registro;
			if (strlen($registro)== 6){
				$registro="0".$registro;
			}
			if (strlen($registro)== 5){
				$registro="00".$registro;
			}
			if (strlen($registro)== 4){
				$registro="000".$registro;
			}
			
			$nombre="";
			$concepto="";			
			$pagoConcepto="";			
			$recargo="";
			$pagoRecargo="";
			$fechaVencimiento="";
			$cuenta="";
			$copia = "ALUMNO";
			//$subConcepto=NULL;
			
			if($Alumno -> find_first("miReg = ".$registro)){
				
				$reporte = new FPDF();
				$reporte -> Open();
				$reporte -> AddFont('Verdana','','verdana.php');
				$reporte -> AddPage();
				
				if ($idxalumnocurso != 0){
					$cuantosCeros = strlen($idxalumnocurso);
					switch($cuantosCeros){
						case 1: $idxalumnocurso = "00000000".$idxalumnocurso;
								break;
						case 2: $idxalumnocurso = "0000000".$idxalumnocurso;
								break;
						case 3: $idxalumnocurso = "000000".$idxalumnocurso;
								break;
						case 4: $idxalumnocurso = "00000".$idxalumnocurso;
								break;
						case 5: $idxalumnocurso = "0000".$idxalumnocurso;
								break;
						case 6: $idxalumnocurso = "000".$idxalumnocurso;
								break;
						case 7: $idxalumnocurso = "00".$idxalumnocurso;
								break;
						case 8: $idxalumnocurso = "0".$idxalumnocurso;
								break;
					}
				} // if ($idxalumnocurso != 0)
				
				for($i=0; $i<2; $i++ ){
					
					//echo $i."".$copia;
					$nombre = $Alumno -> vcNomAlu;
					// echo $NumConcepto;
					if($NumConcepto < 301){
						if ($Periodo -> periodo == $Alumno -> miPerIng){
							$NumConcepto = 100;
							$ConceptoPago -> find_first("id = 101");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							
							$subConcepto[1] [1] = "CREDENCIAL ALUMNO";
							$subConcepto[2] [1] = "MANUAL DE NUEVO INGRESO";
							$subConcepto[1] [2] = "70";
							$subConcepto[2] [2] = "137";
							
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 103");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;							
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 105");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 107");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}else{
							$NumConcepto = 201;
							$ConceptoPago -> find_first("id = 201");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 203");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 205");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 207");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
					}else{
						if($NumConcepto >= 401 && $NumConcepto < 501){
							$ConceptoPago -> find_first("id = 401"); // Extraordinario Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 501 && $NumConcepto < 601){
							$ConceptoPago -> find_first("id = 501"); //Titulo Suficiencia Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 601 && $NumConcepto < 701){
							$ConceptoPago -> find_first("id = 601"); // Curso de Nivelación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							$Periodos = new Periodos();
							$periodo = $Periodos -> get_periodo_actual_intersemestral();
						}
						if($NumConcepto >= 701 && $NumConcepto < 801){							
							$ConceptoPago -> find_first("id = 701"); // Curso de Acreditación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							$Periodos = new Periodos();
							$periodo = $Periodos -> get_periodo_actual_intersemestral();
						}
					}
					$referencia_sin_dv = $NumConcepto.$idxalumnocurso.$periodo;
					$referenciaPa_sin_dv = "300".$idxalumnocurso.$periodo;
					$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
					$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);
					/*foreach($subConcepto as $sc)
						echo $sc -> concepto." - ".$sc -> costo."<br>";				*/
					//echo $registro." - ".$nombre." - ".$concepto." - ".$NumConcepto." - ".$pagoConcepto." - ".$recargo." - ".$pagoRecargo." - ".$fechaVencimiento." - ".$cuenta;
										
					$reporte -> Ln();
					//$reporte -> Image('http://localhost/tecnologo/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					// $reporte -> Image('/var/www/htdocs/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> Image('/datos/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> SetFont('Verdana','',10);
					
					$posRef = 37;
					$reporte -> SetX(45);
					$reporte -> SetY($posRef);
					$reporte -> MultiCell(190,3,$referencia,0,'R',0);
					
					$reporte -> SetFont('Verdana','',8);
					
					$reporte -> SetX(50);
					$posEmpresa = 44;
					$reporte -> SetY($posEmpresa);
					$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
					
					$reporte -> SetFont('Verdana','',7);
					
					$reporte -> Ln();
					$reporte -> SetX(2);
					$reporte -> SetY(41);
					
					$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
					
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					
					$reporte -> SetX(20);
					$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
					
					$py = 63;	
					$total = 0;
					$reporte -> SetY($py);
					$reporte -> MultiCell(100,3,$concepto,0,'L',0);				
					$reporte -> SetY($py);
					$reporte -> MultiCell(80,3,'$'.$pagoConcepto.'.00',0,'R',0);
					$total = $pagoConcepto;
					if ($Periodo -> recargo > 0 && $NumConcepto <= 300){						
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$recargo,0,'L',0);	
						$reporte -> SetY($py);
						$reporte -> MultiCell(80,3,'$'.$pagoRecargo.'.00',0,'R',0);
						$total = $total + $pagoRecargo;						
					}
										
					if ($subConcepto != NULL){
						foreach($subConcepto as $sc){
							$py = $py + 3;
							$reporte -> SetY($py);
							$reporte -> MultiCell(100,3,$sc [1],0,'L',0);	
							$reporte -> SetY($py);
							$reporte -> MultiCell(80,3,'$'.$sc [2].'.00',0,'R',0);
							$total = $total + $sc [2];
						}
					}
					
					if ($NumConcepto > 300){
						$Materia = new Materia();
						$Materia -> find_first("clave = '".$clavemat."'");
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);							
					}
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,'$'.$total.'.00',0,'R',0);
										
					$reporte -> SetY(101);
					$reporte -> MultiCell(179,3,$copia,0,'C',0);					
						
					if($NumConcepto <= 301){
						if ($i == 1){
							$copia = "BANCO";
						}
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referenciaPa,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
									
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
			
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
						if ($i < 1)						
							$reporte -> AddPage();	
						if ($i == 1){
							$copia = "BANCO";
						}
					}else{														
						if ($i == 1){
							$copia = "BANCO";
						}
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referencia,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,$concepto,0,'L',0);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(80,3,"$".$pagoConcepto.".00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$".$total.".00",0,'R',0);
						
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
					}
				}
				$reporte -> Output("public/files/fichas/".$registro.$NumConcepto.$clavemat.".pdf");
				
				$this->redirect("files/fichas/".$registro.$NumConcepto.$clavemat.".pdf");
			}
			else{
				echo "<br><br><br><center><h1>El alumno no se encuentra registrado o se encuentra dado de baja</h1>";
				echo '<br><br><b><a href="http://cira.ceti.mx"> << Volver</a></b></center>';				
			}
		} // function ficha($NumConcepto=100, $registro = 830001, $clavemat= 0, $idxalumnocurso = 0)
		
		function ficha($NumConcepto=100, $registro = 830001, $materia_id= 0, $horario_id = 0){
		
		 //Se llaman las clases 
		 $cajaTramites = new CajaTramites();
		 $cajaConceptos = new CajaConceptos(); 
		 $Alumno = new Alumnos();
		
		 //Obtiene la informacion del conceto solicitado
		 $cajaConceptos -> find_first("id =".$NumConcepto);
		 
		 //Obtiene Datos del alumno
		 $Alumno -> find_first("miReg = ".$registro);
		 
		 //Cambiamos el formato de la fecha de pago 
		 list($anio,$mes,$dia)=split("-", $cajaConceptos -> fecha_pago);
		 $fechaInicio = $dia."-".$mes."-".$anio;
		
		 //Esta funcion sirve para obtener la fecha limite de pago, el primer para metro es la fecha inicio de pago y el segundo la cantidad de dias que se suman a esa fecha
		 $fechaLimitePago = $cajaTramites -> create_fecha_limite_pago($fechaInicio,$cajaConceptos -> diasActivos);

		 //Plantel (Colomos - 2 ______ Tonala - 6)
		 $colomos = "2";
		 
		 //Se agrega 6 digitos para rellenar los id de la referencia
		 $tramiteRef = "000000";
		  
		 //Validacion de los digitos del registro (DEBE DE SER DE 8)
		 if(strlen($registro) == 3)
		   $registroRef = "00000".$registro;
			  
		 else if(strlen($registro) == 4)
		   $registroRef = "0000".$registro;
			  
		 else if(strlen($registro) == 5)
		   $registroRef = "000".$registro;
			  
		 else if(strlen($registro) == 6)
		   $registroRef = "00".$registro;
			
	     else if(strlen($registro) == 7)
		   $registroRef = "0".$registro;
			
		 else
		   $registroRef = $registro;
		   
			  
		 //Se valida la cantidad de digitos del concepto
		 if(strlen($NumConcepto) == 1)
		   $conceptoRef = "00".$NumConcepto;
				  
		 else if(strlen($NumConcepto) == 2)
		   $conceptoRef = "0".$NumConcepto;
				  
		 else if(strlen($NumConcepto) == 3)
		   $conceptoRef = $NumConcepto;
			 
			 
         //Segun el nivel que solicito el concepto es la variable que se utilizara (debe de ser de 1 digito) Tenologo -1 2 -Ingenieria 
		 $ingenieria = "2";
		  
		 //Valida el numero de caracterede de extras y titulos (Debe de ser de 5 digitos)
		 if(strlen($horario_id) == 0)
		   $actaExtrasTitulos = "00000";
				 
		 else if(strlen($horario_id) == 1)
		   $actaExtrasTitulos = "0000".$horario_id;
					
		 else if(strlen($horario_id) == 2)
		   $actaExtrasTitulos = "000".$horario_id;
					  
		 else if(strlen($horario_id) == 3)
		   $actaExtrasTitulos = "00".$horario_id;
					  
		 else if(strlen($horario_id) == 4)
		   $actaExtrasTitulos = "0".$horario_id;	  
					  
		 else
		   $actaExtrasTitulos = $horario_id;
		   
		 //Periodo que se cursa (ACTIVO)
		 $periodo = Session::get_data('periodo');
		 
		 //Se genera la fecha condesada
		 list($diaPago,$mesPago,$anioPago) = split("-",$fechaLimitePago);
		  
		 $anioCondesado = ($anioPago - 1988) * 372;
		 $mesCondesado  = ($mesPago - 1) * 31;
		 $diaCondesado  = ($diaPago - 1);
		  
		 //Se obtiene la fecha condesada
		 $fechaCondesada = ($anioCondesado + $mesCondesado + $diaCondesado);
		
		 //Se obtiene el importe del concepto
		 $importeCon = $cajaConceptos -> costo;
	     $importeConcepto = explode(".",$importeCon);
		 $importeEntero = $importeConcepto[0].$importeConcepto[1];
		 
	      //Se valida los digitos del importe y se obtendra el importe a multiplicar
		  if(strlen($importeEntero) == 3)
			$importeMultiplicar = "00000".$importeEntero;

		  else if(strlen($importeEntero) == 4)
			$importeMultiplicar = "0000".$importeEntero;

		  else if(strlen($importeEntero) == 5)
			$importeMultiplicar = "000".$importeEntero;
				
		  else if(strlen($importeEntero) == 6)
			$importeMultiplicar = "00".$importeEntero;
				
		  else if(strlen($importeEntero) == 7)
			$importeMultiplicar = "0".$importeEntero;
				
		  else
			$importeMultiplicar = $importeEntero;
		
		   
		 //Esta variable contiene el numero de factor de peso el cual se multiplicara con $importeMultiplicar
		 $numFactoPeso = "37137137";
		 
		 //Iniciamos la variable suma en cero
	     $suma = 0;
		
		 //For que recorre el arrelo y realiza la multipplicacion
		 for($i = strlen($importeMultiplicar)-1; $i >= 0; $i--)
		 {
			//Se realiza la multiplicacion numero x numero
		    $numero = substr($importeMultiplicar,$i,1) * substr($numFactoPeso,$i,1);
				  
			//Se realiza la suma con los resultados de la multiplicacion
			$suma = $suma + $numero;
		 }
		 
		 //Se obtiene el importe condesado
		 $importeCondesado =  $suma % 10;
		 
		 //Se declara la variable constante y se le da el valor 2 (EL VALOR NO CAMBIA) 
		 $constante = "2";
		 
		 //Se crea la referencia
		 $referencia1 = $colomos.$tramiteRef.$registroRef.$conceptoRef.$ingenieria.$actaExtrasTitulos.$periodo.$fechaCondesada.$importeCondesado.$constante;
		 
		 //Para referencia de 37 digitos
		 $factorPesoDV = "2319171311231917131123191713112319171311231917131123191713112319171311";
		 
		 //Se inicia la variable $sumRef en cero
		 $sumRef = 0;
		  
         //For que recorre el arrelo y realiza la multipplicacion
		 for($x = strlen($referencia1)-1; $x >= 0; $x--)
		 {
		    $y = $x+$x;
			//Se realiza la multiplicacion numero x numero
			$numeroRef = substr($referencia1,$x,1) * substr($factorPesoDV,$y,2);
				
			//Se realiza la suma con los resultados de la multiplicacion
			$sumRef = $sumRef + $numeroRef;
		 }
		 
		 //Se obtiene el remanente
		 $digitoVer = ($sumRef % 97) + 1;
		 
		 if(strlen($digitoVer) == 1)
		   $digitoVerif = "0".$digitoVer;
		   
		 else
			  $digitoVerif = $digitoVer;
			  
         $referenciaCompleta = $referencia1.$digitoVerif;		
    		 
	
		  //Se obtiene el nombre de la materia 
		  $xextraordinarios	= new Xextraordinarios();
		  $nombreMateria = $xextraordinarios -> get_extrasTitulos($registro,Session::get_data('periodo'),$Alumno -> enPlantel,$horario_id);
		  
		  foreach($nombreMateria AS $value)
		    $materia = $value -> nombre;

		
		  //Se inicia la creacion del PDF
		  $pdf = new FPDF();
		  $pdf -> Open();
		  
		  $pdf -> AddPage();	
    	  $pdf -> Image('public/img/formatoFichaPagoLinea.JPG', 5, 20, 200, 220);
		  
		  //COPIA CLIENTE
		  $pdf -> SetXY(110,35);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
		  $pdf -> SetXY(10,41);
		  $pdf -> SetFont('Arial','',7);
		  $pdf -> MultiCell(80,3,$registro." - ".$Alumno -> vcNomAlu,0,'L',0);
		  $pdf -> SetXY(110,44);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,$referenciaCompleta,0,'L',0);                
		  $pdf -> SetXY(20,52);
          $pdf -> MultiCell(90,4,$fechaLimitePago,0,'L',0);                
		  $pdf -> SetFont('Arial','',7);				            
          $pdf -> ln(8);
          $pdf -> MultiCell(60,4,$cajaConceptos -> nombre,0,'L',0); 
          $pdf -> ln(2);
          $pdf -> MultiCell(60,4,$materia,0,'L',0);  
		  $pdf -> SetXY(78,65);
          $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
		  $pdf -> SetXY(78,76);
          $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
		  $pdf -> SetXY(92,100);
          $pdf -> MultiCell(60,4,"BANCO",0,'L',0);  

		  //COPIA BANCO
		  $pdf -> SetXY(110,155);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,"CETI SERVICIOS - NO.EMPRESA (109931)",0,'L',0);                
		  $pdf -> SetXY(10,160);
		  $pdf -> SetFont('Arial','',7);		
          $pdf -> MultiCell(80,3,$registro." - ".$Alumno -> vcNomAlu,0,'L',0);
		  $pdf -> SetXY(110,163);
		  $pdf -> SetFont('Arial','',10);				            
          $pdf -> MultiCell(90,4,$referenciaCompleta,0,'L',0);                
		  $pdf -> SetXY(20,170);
          $pdf -> MultiCell(90,4,$fechaLimitePago,0,'L',0);                
		  $pdf -> SetFont('Arial','',7);				            
          $pdf -> ln(8);
          $pdf -> MultiCell(60,4,$cajaConceptos -> nombre,0,'L',0);   
          $pdf -> ln(2);
          $pdf -> MultiCell(60,4,$materia,0,'L',0);  
		  $pdf -> SetXY(78,183);
          $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
		  $pdf -> SetXY(78,195);
          $pdf -> MultiCell(60,4,"$".$cajaConceptos -> costo,0,'L',0);                
		  $pdf -> SetXY(92,219);
          $pdf -> MultiCell(60,4,"CLIENTE",0,'L',0);    

          $pdf -> Output("public/files/fichas/".$registro.$NumConcepto.$materia_id.".pdf");	
		  $this->redirect("files/fichas/".$registro.$NumConcepto.$materia_id.".pdf");		  
		}
		
		function fichaT($NumConcepto=101, $registro = 831001, $clavemat= 0, $idxalumnocurso= 0){
			$this -> set_response("view");
			
			$Alumno = new Alumnos();
			$Periodo = new Periodos();
			$ConceptoPago = new ConceptosPago();
			
			$Periodo -> find_first("activo = 1");
			$periodo = $this -> actual;
			$registroSolo = $registro;
			if (strlen($registro)== 6){
				$registro="0".$registro;
			}
			if (strlen($registro)== 5){
				$registro="00".$registro;
			}
			if (strlen($registro)== 4){
				$registro="000".$registro;
			}
			
			$nombre="";
			$concepto="";			
			$pagoConcepto="";			
			$recargo="";
			$pagoRecargo="";
			$fechaVencimiento="";
			$cuenta="";
			$copia = "ALUMNO";
			//$subConcepto=NULL;			
			
			if($Alumno -> find_first("miReg = ".$registro)){
				
				$reporte = new FPDF();
				$reporte -> Open();
				$reporte -> AddFont('Verdana','','verdana.php');
				$reporte -> AddPage();
				
				if ($idxalumnocurso != 0){
					
					$cuantosCeros = strlen($idxalumnocurso);
					switch($cuantosCeros){
						case 1: $idxalumnocurso = "50000000".$idxalumnocurso;
								break;
						case 2: $idxalumnocurso = "5000000".$idxalumnocurso;
								break;
						case 3: $idxalumnocurso = "500000".$idxalumnocurso;
								break;
						case 4: $idxalumnocurso = "50000".$idxalumnocurso;
								break;
						case 5: $idxalumnocurso = "5000".$idxalumnocurso;
								break;
						case 6: $idxalumnocurso = "500".$idxalumnocurso;
								break;
						case 7: $idxalumnocurso = "50".$idxalumnocurso;
								break;
						case 8: $idxalumnocurso = "5".$idxalumnocurso;
								break;
					}
				} // if ($idxalumnocurso != 0)
				
				for($i=0; $i<2; $i++ ){
					
					//echo $i."".$copia;
					$nombre = $Alumno -> vcNomAlu;
					// echo $NumConcepto;
					if($NumConcepto < 301){
						if ($Periodo -> periodo == $Alumno -> miPerIng){
							$NumConcepto = 100;
							$ConceptoPago -> find_first("id = 101");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							
							$subConcepto[1] [1] = "CREDENCIAL ALUMNO";
							$subConcepto[2] [1] = "MANUAL DE NUEVO INGRESO";
							$subConcepto[1] [2] = "70";
							$subConcepto[2] [2] = "137";
							
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 103");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;							
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 105");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 107");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}else{
							$NumConcepto = 201;
							$ConceptoPago -> find_first("id = 201");
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							if ($Periodo -> recargo == 0){
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							}
							if ($Periodo -> recargo == 1){
								$ConceptoPago -> find_first("id = 203");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 2){
								$ConceptoPago -> find_first("id = 205");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
							if ($Periodo -> recargo == 3){
								$ConceptoPago -> find_first("id = 207");
								$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
								$recargo = $ConceptoPago -> concepto;
								$pagoRecargo = $ConceptoPago -> costo;
							}
						}
					}else{
						if($NumConcepto >= 401 && $NumConcepto < 501){
							$ConceptoPago -> find_first("id = 401"); // Extraordinario Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 501 && $NumConcepto < 601){
							$ConceptoPago -> find_first("id = 501"); //Titulo Suficiencia Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							
						}
						if($NumConcepto >= 601 && $NumConcepto < 701){
							$ConceptoPago -> find_first("id = 601"); // Curso de Nivelación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							$Periodos = new Periodos();
							$periodo = $Periodos -> get_periodo_actual_intersemestral();
						}
						if($NumConcepto >= 701 && $NumConcepto < 801){							
							$ConceptoPago -> find_first("id = 701"); // Curso de Acreditación Ingeniería
							$concepto = $ConceptoPago -> concepto;
							$pagoConcepto = $ConceptoPago -> costo;
							$cuenta = $ConceptoPago -> cuenta;
							$fechaVencimiento = $ConceptoPago -> fecha_vencimiento;
							$Periodos = new Periodos();
							$periodo = $Periodos -> get_periodo_actual_intersemestral();
						}
					}
					
					$referencia_sin_dv = $NumConcepto.$idxalumnocurso.$periodo;
					$referenciaPa_sin_dv = "300".$idxalumnocurso.$periodo;
					$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
					$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);
					/*foreach($subConcepto as $sc)
						echo $sc -> concepto." - ".$sc -> costo."<br>";				*/
					//echo $registro." - ".$nombre." - ".$concepto." - ".$NumConcepto." - ".$pagoConcepto." - ".$recargo." - ".$pagoRecargo." - ".$fechaVencimiento." - ".$cuenta;
										
					$reporte -> Ln();
					//$reporte -> Image('http://localhost/tecnologo/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					// $reporte -> Image('/var/www/htdocs/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> Image('/datos/calculo/ingenieria/public/img/formatoFichaPago.jpg', 5, 20, 200, 220);
					$reporte -> SetFont('Verdana','',10);
					
					$posRef = 37;
					$reporte -> SetX(45);
					$reporte -> SetY($posRef);
					$reporte -> MultiCell(190,3,$referencia,0,'R',0);
					
					$reporte -> SetFont('Verdana','',8);
					
					$reporte -> SetX(50);
					$posEmpresa = 44;
					$reporte -> SetY($posEmpresa);
					$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
					
					$reporte -> SetFont('Verdana','',7);
					
					$reporte -> Ln();
					$reporte -> SetX(2);
					$reporte -> SetY(41);
					
					$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
					
					$reporte -> Ln();
					$reporte -> Ln();
					$reporte -> Ln();
					
					$reporte -> SetX(20);
					$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
					
					$py = 63;	
					$total = 0;
					$reporte -> SetY($py);
					$reporte -> MultiCell(100,3,$concepto,0,'L',0);				
					$reporte -> SetY($py);
					$reporte -> MultiCell(80,3,'$'.$pagoConcepto.'.00',0,'R',0);
					$total = $pagoConcepto;
					if ($Periodo -> recargo > 0 && $NumConcepto <= 300){						
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$recargo,0,'L',0);	
						$reporte -> SetY($py);
						$reporte -> MultiCell(80,3,'$'.$pagoRecargo.'.00',0,'R',0);
						$total = $total + $pagoRecargo;						
					}
										
					if ($subConcepto != NULL){
						foreach($subConcepto as $sc){
							$py = $py + 3;
							$reporte -> SetY($py);
							$reporte -> MultiCell(100,3,$sc [1],0,'L',0);	
							$reporte -> SetY($py);
							$reporte -> MultiCell(80,3,'$'.$sc [2].'.00',0,'R',0);
							$total = $total + $sc [2];
						}
					}
					
					if ($NumConcepto > 300){
						$Materia = new Materia();
						$Materia -> find_first("clave = '".$clavemat."'");
						$py = $py + 3;
						$reporte -> SetY($py);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);							
					}
					
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,'$'.$total.'.00',0,'R',0);
										
					$reporte -> SetY(101);
					$reporte -> MultiCell(179,3,$copia,0,'C',0);					
						
					if($NumConcepto <= 301){
						
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referenciaPa,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
									
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,"CUOTA DE PATRONATO DE PADRES",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(100,3,"DE ALUMNOS DEL CETI",0,'L',0);
						$reporte -> SetY(187);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$250.00",0,'R',0);
			
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
						if ($i < 1)						
							$reporte -> AddPage();	
						if ($i == 1){
							$copia = "BANCO";
						}
					}else{														
						if ($i == 1){
							$copia = "BANCO";
						}
						$reporte -> Ln();					
						$reporte -> SetFont('Verdana','',10);					
						$reporte -> SetX(45);
						$posRef = $posRef +119;
						$reporte -> SetY($posRef);			
						$reporte -> MultiCell(190,3,$referencia,0,'R',0);
						
						$reporte -> SetFont('Verdana','',8);					
						$reporte -> SetX(50);
						$posEmpresa = $posEmpresa +119;
						$reporte -> SetY($posEmpresa);
						$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (".$cuenta.")          RUTINA (1111)",0,'R',0);
						
						$reporte -> Ln();
						$reporte -> SetFont('Verdana','',7);
						$reporte -> SetX(2);
						$reporte -> SetY(160);
						
						$reporte -> MultiCell(80,3,$registroSolo." - ".$nombre,0,'C',0);
						
						$reporte -> Ln();
						$reporte -> Ln();
						$reporte -> Ln();
						
						$reporte -> SetX(20);
						$reporte -> MultiCell(0,3,$fechaVencimiento,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(100,3,$concepto,0,'L',0);
						$reporte -> MultiCell(100,3,$Materia -> nombre,0,'L',0);
						
						$reporte -> SetY(184);
						$reporte -> MultiCell(80,3,"$".$pagoConcepto.".00",0,'R',0);
						
						$reporte -> SetY(195);
						$reporte -> MultiCell(80,3,"$".$total.".00",0,'R',0);
						
						$reporte -> SetY(220);
						$reporte -> MultiCell(179,3,$copia,0,'C',0);
					}
				}
				$reporte -> Output("public/files/fichas/".$registro.$NumConcepto.$clavemat.".pdf");
				
				$this->redirect("files/fichas/".$registro.$NumConcepto.$clavemat.".pdf");
			}
			else{
				echo "<br><br><br><center><h1>El alumno no se encuentra registrado o se encuentra dado de baja</h1>";
				echo '<br><br><b><a href="http://cira.ceti.mx"> << Volver</a></b></center>';				
			}
		} // function fichaT($NumConcepto=100, $registro = 830001, $clavemat= 0, $idxalumnocurso = 0)
		
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
		} // function digitoVerificador($referencia)
	}