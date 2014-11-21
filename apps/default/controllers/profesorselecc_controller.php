<?php
			
	class ProfesorseleccController extends ApplicationController {
		
		public $actualselecc = 12010;
		public $proximoselecc = 32010;
	
		function index(){
			
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			//if($xalumno -> nombre == ""){
			//	$this -> redirect("alumno/actualizacion");
			//}
			// Si el usuario aun no cambia su password, se manda a alumnos/index, es decir
			// si el usuario aun tiene su registro como password...
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
			
			// Si el usuario no es un alumno, no podrá ingresar...
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			unset($this -> calificacion);
			unset($this -> pinicial);
			unset($this -> kardex);
			unset($this -> creditos);
			unset($this -> ncreditos);
			unset($this -> periodos);
			unset($this -> pendientes);
			unset($this -> pmaterias);
			unset($this -> matelocas);
			unset($this -> matitas);
			
			$planesmateria = new Planmateria();
			$alumnos = new Alumnos();
			$xpkardex = new Xpkardex();
			$especialidades = new Especialidades();
			$materias = new Materia();
			
			if($this -> actualselecc[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($this -> actualselecc,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10) $di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this -> acceso = 0;
			}
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 7;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
						
			$kardexes = new KardexIng();
			$primera = $kardexes -> find_first("registro=".$id." ORDER BY id ASC");
			$this -> pinicial = $alumno -> miPerIng;
			
			$this -> kardexs = $kardexes -> count("registro=".$id);
			$this -> ncreditos = 0;
			$this -> promedio = 0;
			$total = 0;
					
			$mmm=0;
			$i = 0;
			while($this -> actualselecc != $this -> pinicial){
				$i++;
				$resultados = $kardexes -> distinct("clavemat","conditions: registro=".$id." AND periodo='".$this -> pinicial."'");
				$this -> periodos[$mmm] = $this -> pinicial;
				
				$j=0;
				
				foreach($resultados as $resultado){
					$resultado = $kardexes -> find_first("registro=".$id." AND clavemat='".$resultado."' AND periodo='".$this -> pinicial."'");
					
					$x = $materias -> count("clave='".$resultado -> clavemat."' AND plan=20".substr($alumno -> enPlan,2,2));
					if($x==0)
						continue;
					
					$creditos[$i][$j] = $this -> obtenerCreditos($resultado -> clavemat,$this -> idplan);
					$this -> ncreditos += $creditos[$i][$j];
					$calificaciones[$i][$j] = $this -> numero_letra($resultado -> promedio);
					$this -> promedio += $resultado -> promedio;
					$j++;
					$total++;
				}
				
				$this -> pinicial = $this -> incrementaPeriodo($this -> pinicial);
				$mmm++;
			}
			
			$this -> promedio /= $total;
			$this -> promedio = round($this -> promedio * 100)/100;
			
		} // function index()
		
		function seleccion1( $registroo ){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('coordinador')!="OK"){
				$this->redirect('/');
			}
			
			unset( $this -> pendientes );
			unset( $this -> pmaterias );
			unset( $this -> alumno );
			unset( $this -> registro );
			unset( $this -> promedio );
			unset( $this -> ingreso );
			
			$id = $registroo;
			
			// Para que se vea el view bien.
			$this -> set_response("view");
			
			$planesmaterias 	= new Planmateria();
			$xpkardex			= new Xpkardex();
			$kardex				= new KardexIng();
			$alumnos			= new Alumnos();
			$especialidades		= new Especialidades();
			$materias			= new Materia();
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			
			$this -> alumno = htmlentities($alumno -> vcNomAlu);
			$this -> registro = $alumno -> miReg;
			$this -> ingreso = $alumnos -> miPerIng;
			
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			$pmaterias = $planesmaterias -> find("idplan=".$this -> idplan." ORDER BY clavemateria");
			
			
			// Sacar el promedio
			$kardexes = new KardexIng();
			$primera = $kardexes -> find_first("registro=".$id." ORDER BY id ASC");
			$this -> pinicial = $alumno -> miPerIng;
			
			$this -> kardexs = $kardexes -> count("registro=".$id);
			$this -> ncreditos = 0;
			$this -> promedio = 0;
			$total = 0;
					
			$mmm=0;
			$i = 0;
			while($this -> actualselecc != $this -> pinicial){
				$i++;
				$resultados = $kardexes -> distinct("clavemat","conditions: registro=".$id." AND periodo='".$this -> pinicial."'");
				$this -> periodos[$mmm] = $this -> pinicial;
				
				$j=0;
				
				foreach($resultados as $resultado){
					$resultado = $kardexes -> find_first("registro=".$id." AND clavemat='".$resultado."' AND periodo='".$this -> pinicial."'");
					
					$x = $materias -> count("clave='".$resultado -> clavemat."' AND plan=20".substr($alumno -> enPlan,2,2));
					if($x==0)
						continue;
					
					$creditos[$i][$j] = $this -> obtenerCreditos($resultado -> clavemat,$this -> idplan);
					$this -> ncreditos += $creditos[$i][$j];
					$calificaciones[$i][$j] = $this -> numero_letra($resultado -> promedio);
					$this -> promedio += $resultado -> promedio;
					$j++;
					$total++;
				}
				
				$this -> pinicial = $this -> incrementaPeriodo($this -> pinicial);
				$mmm++;
			}
			
			$this -> promedio /= $total;
			$this -> promedio = round($this -> promedio * 100)/100;
			// Fin Sacar Promedio
			
			
			
			//QUITAR MATERIAS QUE YA ESTAN EN KARDEX O COMO MATERIA SOLICITADA PARA KARDEX
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				$n = $kardex -> count("registro=".$id." AND clavemat='".$p -> clavemateria."'");
				if($n==0){
					$n = $xpkardex -> count("registro=".$id." AND materia='".$p -> clavemateria."'");
					if($n==0){
						$this -> pmaterias[$xxx] = $p;
						$xxx++;
					}
				}
			}
			
			if($this -> pmaterias){ 
				$nnn = 0;
				foreach($this -> pmaterias as $tmp){
					$this -> matitas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria, $this -> plan);
					$nnn++;
				}
			}
			
			$this -> pendientes = $xpkardex -> find("registro=".$id);

			$nnn=0;
			if($this -> pendientes){ 
				foreach($this -> pendientes as $tmp){
					$this -> matelocas[$nnn] = $tmp -> materia." - ".$this -> sacarMateria($tmp -> materia, $this -> plan);
					$nnn++;
				}
			}
			
			echo $this -> render_partial("seleccion1");
			
		} // function seleccion1()
		
		function seleccion2(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10)
				$di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumno/seleccion');
			}
			
			$pmaterias = $this -> pmaterias;
			
			$kardex = new KardexIng();
			$seleccion = new Seleccionalumno();
			$xpkardex = new Xpkardex();
			
			unset($this -> pendientes);
			unset($this -> matitas);
			unset($this -> matelocas);
			unset($this -> pmaterias);
			
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				if($p -> prerrequisito=="-"){
					$n = $seleccion -> count("registro=".$id."
							AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
					if($n==0){
						if($this -> ingreso == $this -> actualselecc){
							if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
									$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" ||
											$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" ||
													$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16" ||
															$p -> clavemateria == "CS-11" ){
								
								$alumnos = new Alumnos();
								$alumno = $alumnos -> find_first("miReg=".$id);
								if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
						}
						else{
							$this -> pmaterias[$xxx] = $p;
							$xxx++;
						}
					}
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($p -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						
						$y = $kardex -> count("registro=".$id." AND clavemat='".$p -> prerrequisito."'");
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						/*
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$p -> prerrequisito."'");
							if($z>0) break;
						}
						*/
						$m = $xpkardex -> count("registro=".$id." AND materia='".$p -> prerrequisito."'");
						
						if($y>0 || $m>0){
							$n = $seleccion -> count("registro=".$id."
									AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
								
							if($n==0){
								if($this -> ingreso == $this -> actualselecc){
									if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
											$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" ||
													$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" ||
															$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16" ||
																	$p -> clavemateria == "CS-11" ){
										$alumnos = new Alumnos();
										$alumno = $alumnos -> find_first("miReg=".$id);
										if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
										else{
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
									}
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
							else{
								if($m>0){
									$n = $seleccion -> count("registro=".$id." AND 
											clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
								
									if($n==0){
										$this -> pmaterias[$xxx] = $p;
										$xxx++;
									}
								}
							}
						}
						continue;
					}
					else{
						if($p -> prerrequisito - 100 <= $this -> ncreditos){
							$n = $seleccion -> count("registro=".$id."
									AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
							if($n==0){
								if($this -> ingreso == $this -> actualselecc){
									if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
											$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" || 
													$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" || 
															$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16"){
										$alumnos = new Alumnos();
										$alumno = $alumnos -> find_first("miReg=".$id);
										if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
										else{
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
									}
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
						}
					}
				}
			}
			
			if($this -> pmaterias) $nnn = 0;
			
			foreach($this -> pmaterias as $tmp){
				$this -> matitas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria, $this -> plan);
				$nnn++;
			}
			
			$this -> pendientes = $seleccion -> find("registro=".$id." AND periodo=".$this -> actualselecc);

			$nnn=0;
			$this -> creditos = 0;
			if($this -> pendientes)
			foreach($this -> pendientes as $tmp){
				$this -> creditos += $this -> obtenerCreditos($tmp -> clavemateria, $this -> idplan);
				$this -> matelocas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria,$this -> plan);
				$nnn++;
			}
		} // function seleccion2()
		
		function seleccion3( $error = 0 ){
			$periodo = $this -> actualselecc;
				
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			// Para saber si aún está a tiempo de seguir escogiendo materias.
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			 
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10) $di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumno/seleccion');
			}
			
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xalumnocursos();
			$materias		= new Materia();
			$xccursos		= new Xccursos();
			$xcsalones		= new Xcsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xchorascursos();
			
			foreach( $alumnos -> find_all_by_sql
					( "Select enPlan from alumnos where miReg = ".$id ) as $alumno){
				$this -> enPlan = $alumno -> enPlan;
			}
			
			$seleccAl = $seleccAlumno -> find( "registro = ".$id." and periodo = ".$periodo );
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xal.*, xcc.materia2000 as materia2000, xcc.materia2007 as materia2007
							From xalumnocursos xal, xccursos xcc
							where xal.registro = ".$id."
							and xal.periodo = ".$periodo."
							and xal.curso = xcc.clavecurso" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			$i = 0;
			
			if( $this -> enPlan == "PE00" ){ // plan 2000
				$tipoPlan = "materia2000";
			}
			else{ // plan 2007
				$tipoPlan = "materia2007";
			}
			
			foreach( $seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> $tipoPlan == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> find
					( "registro = ".$id." AND periodo = ".$periodo);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
					
					$this -> seleccionados[$sel -> curso] = 
							$xccursos -> find_first("clavecurso = '".$sel -> curso."'");
					
					$this -> maestros[$sel -> curso] = $this -> nombreProfesor($xccursos -> nomina);
					
					$this -> materiales[$this -> seleccionados[$sel -> curso] -> $tipoPlan] = 
							$this -> sacarMateria($this -> seleccionados[$sel -> curso] -> $tipoPlan, $this -> plan);
					$j = 0;
					foreach( $xchoras -> find( "clavecurso = '"
							.$xccursos -> clavecurso."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccursos -> id][$j] = $xchora;
						$this -> salon[$xccursos -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xchora -> clavecurso;
					}
					
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			$i = 0;
			foreach( $this -> pendientes as $pend ){
//				$xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					Select * from xccursos xc
					where ".$tipoPlan." = '".$pend -> clavemateria."'
					and periodo = 12010
					and activo = 1
					" );
				$i++;
			}
			
			$i = 0;
			if( $this -> grupos ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "clavecurso = '"
							.$gpo -> clavecurso."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xcsalones_id );
						$j++;
					}
				}
			}
			
			//exit(1);
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
			
		} // function seleccion3($error=0)
		
		function pseleccion(){
			
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$seleccion = new Seleccionalumno();
			
			$seleccion -> registro = $id;
			$seleccion -> clavemateria = $this -> post("materia");
			$seleccion -> periodo = $periodo;
			$seleccion -> save();
			
			$this->redirect('alumnoselecc/seleccion2');
		} // function pseleccion()
		
		function pkardex( $registroo, $materiaa, $periodoo, $tipoo ){
			
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				//$this->redirect('/');
			}
			
			$id = $registroo;
			
			$xpkardex = new Xpkardex();
			
			$xpkardex -> registro = $id;
			$xpkardex -> materia = $materiaa;
			$xpkardex -> periodo = $periodoo;
			$xpkardex -> tipo = $tipoo;
			
			$xpkardex -> promedio = '0';
	
			$xpkardex -> save();
			
			$this->redirect('alumnoselecc/seleccion1');
		} // function pkardex()
		
		function epkardex(){
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$xpkardex = new Xpkardex();
			
			$tmp = $xpkardex -> find($this -> post("id"));
			$tmp -> delete();
			
			$this->redirect('alumno/seleccion1');
		} // function epkardex()
		
		function epseleccion(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$id = Session::get_data('registro');
			$seleccion = new Seleccionalumno();
			
			$tmp = $seleccion -> find_first($this -> post("id"));
			
			$tmp -> delete();
			
			$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex = new KardexIng();
			
			$pmaterias = $planesmaterias -> find("idplan=".$this -> idplan." ORDER BY clavemateria");
			
			//QUITAR MATERIAS QUE YA ESTAN EN KARDEX O COMO MATERIA SOLICITADA PARA KARDEX
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				$n = $kardex -> count("registro=".$id." AND clavemat='".$p -> clavemateria."'");
				if($n==0){
					$n = $xpkardex -> count("registro=".$id." AND materia='".$p -> clavemateria."'");
					if($n==0){
						$this -> pmaterias[$xxx] = $p;
						$xxx++;
					}
				}
			}
			
			$this->redirect('alumnoselecc/seleccion2');
		} // function epseleccion()
		
		function deseleccionar(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$xccursos		= new Xccursos();
			$xalumnocursos 	= new Xalumnocursos();
			
			$xccurso = $xccursos -> find_first( "clavecurso = '".$this -> post("clavecurso")."'" );
			
			$xalumnocurso = $xalumnocursos -> find_first
					( "registro = ".$id.
							" and curso = '".$xccurso -> clavecurso."'".
								"and periodo = ".$periodo );
			$xalumnocurso -> delete();
			
			$xccurso -> disponibilidad += 1;
			$xccurso -> save();
			
			$this->redirect('alumnoselecc/seleccion3');
		} // function deseleccionar()
		
		function seleccionar(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$xccursos 		= new Xccursos();
			$xalumnocursos	= new Xalumnocursos();
			$xchorascursos	= new Xchorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llegar por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$id." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion3/1' );
				return;
			}
			else{				
				
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$id." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "clavecurso = '".
							$xalumnocurso -> curso."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "clavecurso = '".$xccurso -> clavecurso."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora ) ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				
				if( $ocupado == 0 ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xalumnocursos();
					$xalumnocurso -> registro = $id;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso = $xccurso -> clavecurso;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					$xccurso -> save();
					
				}
				else{
					if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
							$autCruce -> registro == $id &&
									$autCruce -> horasautorizadas >= $ocupado ){
						
						$xalumnocurso = new Xalumnocursos();
						$xalumnocurso -> registro = $id;
						$xalumnocurso -> periodo = $periodo;
						
						$xalumnocurso -> curso = $xccurso -> clavecurso;
						$xalumnocurso -> faltas1 = '0';
						$xalumnocurso -> faltas2 = '0';
						$xalumnocurso -> faltas3 = '0';
						$xalumnocurso -> calificacion1 = 300;
						$xalumnocurso -> calificacion2 = 300;
						$xalumnocurso -> calificacion3 = 300;
						$xalumnocurso -> faltas = '0';
						$xalumnocurso -> calificacion = 300;
						$xalumnocurso -> situacion = "-";
						
						$xalumnocurso -> create();
						
						$xccurso -> disponibilidad -= 1;
						$xccurso -> save();
					}
					else{
						if( $cursoscomunes -> find_first
								( "clavecurso1 = '".$xccurso -> clavecurso."'"."
									or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
							$xalumnocurso = new Xalumnocursos();
							$xalumnocurso -> registro = $id;
							$xalumnocurso -> periodo = $periodo;
							
							$xalumnocurso -> curso = $xccurso -> clavecurso;
							$xalumnocurso -> faltas1 = '0';
							$xalumnocurso -> faltas2 = '0';
							$xalumnocurso -> faltas3 = '0';
							$xalumnocurso -> calificacion1 = 300;
							$xalumnocurso -> calificacion2 = 300;
							$xalumnocurso -> calificacion3 = 300;
							$xalumnocurso -> faltas = '0';
							$xalumnocurso -> calificacion = 300;
							$xalumnocurso -> situacion = "-";
							
							$xalumnocurso -> create();
							
							$xccurso -> disponibilidad -= 1;
							$xccurso -> save();
						}
						else{
							// El 2 es un mensaje de cruce de horario 
							$this -> redirect( 'alumnoselecc/seleccion3/2' );
							return;
						}
					}
				}
			}
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion3/7');
		} // function seleccionar()
		
		
		
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		// Tonala
		/////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
		
		function seleccion1T(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this -> pendientes);
			unset($this -> pmaterias);
			
			$id = Session::get_data('registro');
			
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10)
				$di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumno/seleccion');
			}
			
			$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex = new KardexIng();
			
			$pmaterias = $planesmaterias -> find("idplan=".$this -> idplan." ORDER BY clavemateria");
			
			//QUITAR MATERIAS QUE YA ESTAN EN KARDEX O COMO MATERIA SOLICITADA PARA KARDEX
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				$n = $kardex -> count("registro=".$id." AND clavemat='".$p -> clavemateria."'");
				if($n==0){
					$n = $xpkardex -> count("registro=".$id." AND materia='".$p -> clavemateria."'");
					if($n==0){
						$this -> pmaterias[$xxx] = $p;
						$xxx++;
					}
				}
			}
			
			if($this -> pmaterias){ 
				$nnn = 0;
				foreach($this -> pmaterias as $tmp){
					$this -> matitas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria, $this -> plan);
					$nnn++;
				}
			}
			
			$this -> pendientes = $xpkardex -> find("registro=".$id);

			$nnn=0;
			if($this -> pendientes){ 
				foreach($this -> pendientes as $tmp){
					$this -> matelocas[$nnn] = 
							$tmp -> materia." - ".$this -> sacarMateria($tmp -> materia, $this -> plan);
					$nnn++;
				}
			}
		} // function seleccion1T()
		
		function seleccion2T(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10)
				$di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumno/seleccion');
			}
			
			$pmaterias = $this -> pmaterias;
			
			$kardex = new KardexIng();
			$seleccion = new Seleccionalumno();
			$xpkardex = new Xpkardex();
			
			unset($this -> pendientes);
			unset($this -> matitas);
			unset($this -> matelocas);
			unset($this -> pmaterias);
			
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				if($p -> prerrequisito=="-"){
					$n = $seleccion -> count("registro=".$id."
							AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
					if($n==0){
						if($this -> ingreso == $this -> actualselecc){
							if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
									$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" ||
											$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" ||
													$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16" ||
															$p -> clavemateria == "CS-11" ){
								
								$alumnos = new Alumnos();
								$alumno = $alumnos -> find_first("miReg=".$id);
								if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
						}
						else{
							$this -> pmaterias[$xxx] = $p;
							$xxx++;
						}
					}
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($p -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						
						$y = $kardex -> count("registro=".$id." AND clavemat='".$p -> prerrequisito."'");
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						/*
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$p -> prerrequisito."'");
							if($z>0) break;
						}
						*/
						$m = $xpkardex -> count("registro=".$id." AND materia='".$p -> prerrequisito."'");
						
						if($y>0 || $m>0){
							$n = $seleccion -> count("registro=".$id."
									AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
								
							if($n==0){
								if($this -> ingreso == $this -> actualselecc){
									if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
											$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" ||
													$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" ||
															$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16" ||
																	$p -> clavemateria == "CS-11" ){
										$alumnos = new Alumnos();
										$alumno = $alumnos -> find_first("miReg=".$id);
										if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
										else{
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
									}
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
							else{
								if($m>0){
									$n = $seleccion -> count("registro=".$id." AND 
											clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
								
									if($n==0){
										$this -> pmaterias[$xxx] = $p;
										$xxx++;
									}
								}
							}
						}
						continue;
					}
					else{
						if($p -> prerrequisito - 100 <= $this -> ncreditos){
							$n = $seleccion -> count("registro=".$id."
									AND clavemateria='".$p -> clavemateria."' AND periodo=".$this -> actualselecc);
							if($n==0){
								if($this -> ingreso == $this -> actualselecc){
									if($p -> clavemateria == "CB-10" || $p -> clavemateria == "CB-40" ||
											$p -> clavemateria == "CB-70" || $p -> clavemateria == "CI-10" || 
													$p -> clavemateria == "CS-10" || $p -> clavemateria == "CS-12" || 
															$p -> clavemateria == "CIE-10" || $p -> clavemateria == "CI-16"){
										$alumnos = new Alumnos();
										$alumno = $alumnos -> find_first("miReg=".$id);
										if($p -> clavemateria == "CI-16" && $alumno -> idtiEsp == 16){
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
										else{
											$this -> pmaterias[$xxx] = $p;
											$xxx++;
										}
									}
								}
								else{
									$this -> pmaterias[$xxx] = $p;
									$xxx++;
								}
							}
						}
					}
				}
			}
			
			if($this -> pmaterias) $nnn = 0;
			
			foreach($this -> pmaterias as $tmp){
				$this -> matitas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria, $this -> plan);
				$nnn++;
			}
			
			$this -> pendientes = $seleccion -> find("registro=".$id." AND periodo=".$this -> actualselecc);

			$nnn=0;
			$this -> creditos = 0;
			if($this -> pendientes)
			foreach($this -> pendientes as $tmp){
				$this -> creditos += $this -> obtenerCreditos($tmp -> clavemateria, $this -> idplan);
				$this -> matelocas[$nnn] = $tmp -> clavemateria ." - ". $this -> sacarMateria($tmp -> clavemateria,$this -> plan);
				$nnn++;
			}
		} // function seleccion2T()
		
		function seleccion3T( $error = 0 ){
			$periodo = $this -> actualselecc;
				
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			// Para saber si aún está a tiempo de seguir escogiendo materias.
			$tiempos = new SeleccionTiempo();
			
			$tiempo = $tiempos -> find_first("registro=".$id." AND periodo=".$this -> actualselecc);
			
			$yi = substr($tiempo -> inicio,0,4);
			$mi = substr($tiempo -> inicio,5,2);
			$di = substr($tiempo -> inicio,8,2);
			$hi = substr($tiempo -> inicio,11,2);
			$ii = substr($tiempo -> inicio,14,2);
			 
			$yf = substr($tiempo -> fin,0,4);
			$mf = substr($tiempo -> fin,5,2);
			$df = substr($tiempo -> fin,8,2);
			$hf = substr($tiempo -> fin,11,2);
			$if = substr($tiempo -> fin,14,2);
			
//			$di++;$di++;
			if($di<10) $di = "0".$di;
			
			$this -> inicio = $di."-".$mi."-".$yi." (".$hi.":".$ii.")";
			$this -> fin = $df."-".$mf."-".$yf." (".$hf.":".$if.")";
			
			$inicio = mktime($hi,$ii,0,$mi,$di,$yi);
			$fin = mktime($hf,$if,0,$mf,$df,$yf);
			
			if($inicio<time() && $fin>time()){
				$this -> acceso = 1;
			}
			else{
				$this->redirect('alumno/seleccion');
			}
			
			unset($this -> pendientes);
			unset($this -> grupos);
			unset($this -> maestros);
			unset($this -> horas);
			unset($this -> salon);
			unset($this -> esSeleccionable);
			unset($this -> miHorario);
			
			///////////////////////////////////////////////////////////////////
			
			$xalumnocursos	= new Xtalumnocursos();
			$materias		= new Materia();
			$xccursos		= new Xtcursos();
			$xcsalones		= new Xtsalones();
			$seleccAlumno	= new Seleccionalumno();
			$alumnos		= new Alumnos();
			$xchoras		= new Xthorascursos();
			
			
			foreach( $alumnos -> find_all_by_sql
					( "Select enPlan from alumnos where miReg = ".$id ) as $alumno){
				$this -> enPlan = $alumno -> enPlan;
			}
			
			$seleccAl = $seleccAlumno -> find( "registro = ".$id." and periodo = ".$periodo );
			
			$materiasQueYaSelecciono = $xalumnocursos -> find_all_by_sql
							( "Select xtal.*, xtc.materia
							From xtalumnocursos xtal, xtcursos xtc
							where xtal.registro = ".$id."
							and xtal.periodo = ".$periodo."
							and xtal.curso = xtc.clavecurso" );
			// echo $materiasQueYaSelecciono[0] -> materia2007;
			$i = 0;
			
			$tipoPlan = "materia2007";
			
			foreach( $seleccAl as $selAl ){
				$aux = 0;
				foreach( $materiasQueYaSelecciono as $matSeleccionadas ){
					if( $matSeleccionadas -> materia == $selAl -> clavemateria )
						$aux++;
				}
				if( $aux == 0 ){
					$this -> pendientes[$i] = $selAl;
					$i++;
				}
			}
			
			for( $j = 1; $j < 7; $j++ ){ // Dias
				for( $i = 7; $i < 22; $i++ ){ // Horas
					$this -> ocupado1[$j][$i] = "";
				}
			}
			
			$this -> seleccion = $xalumnocursos -> find
					( "registro = ".$id." AND periodo = ".$periodo);
			
			if( $this -> seleccion ){
				foreach( $this -> seleccion as $sel ){
					
					$this -> seleccionados[$sel -> curso] = 
							$xccursos -> find_first("clavecurso = '".$sel -> curso."'");
					
					$this -> maestros[$sel -> curso] = $this -> nombreProfesor($xccursos -> nomina);
					
					$this -> materiales[$this -> seleccionados[$sel -> curso] -> materia] = 
							$this -> sacarMateria($this -> seleccionados[$sel -> curso] -> materia, $this -> plan);
					$j = 0;
					foreach( $xchoras -> find( "clavecurso = '"
							.$xccursos -> clavecurso."'", "order: id asc" ) as $xchora ){
						$this -> horas[$xccursos -> id][$j] = $xchora;
						$this -> salon[$xccursos -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
						
						$this -> ocupado1[$xchora -> dia][$xchora -> hora] = 
								$xchora -> clavecurso;
					}
					
				} // foreach( $this -> seleccion as $sel )
			} // if( $this -> seleccion )
			//exit(1);
			$i = 0;
			foreach( $this -> pendientes as $pend ){
//				$xcur = $xcursos -> find_first("materia='".$tmp -> clavemateria."' AND periodo=".$periodo);
				// En grupos guardan los pendientes
				$this -> grupos[$i] = $xccursos -> find_all_by_sql( "
					Select * from xtcursos xc
					where materia = '".$pend -> clavemateria."'
					and periodo = 12010
					and activo = 1
					" );
					
				$i++;
			}
			
			
			$i = 0;
			if( $this -> grupos ) foreach( $this -> grupos as $tmp ){
				if( $tmp ) foreach( $tmp as $gpo ){
					$this -> maestros[$gpo -> nomina] = $this -> nombreProfesor($gpo -> nomina);
					if($this -> maestros[$gpo -> nomina]==""){
						$this -> maestros[$gpo -> nomina] = "MAESTRO POR DESIGNAR";
					}
					$j = 0;
					foreach( $xchoras -> find( "clavecurso = '"
							.$gpo -> clavecurso."'", "order: id asc" ) as $xchora ){
						$this -> horas[$gpo -> id][$j] = $xchora;
						$this -> salon[$gpo -> id][$j] = $xcsalones -> 
								find_first( "id = ".$xchora -> xtsalones_id );
						$j++;
					}
				}
			}
			
			//exit(1);
			switch($error){
				case 0:
					$this -> mensaje = "";
						break;
				case 1:
					$this -> mensaje = "El grupo que seleccionaste no tiene lugares disponibles";
						break;
				case 2:
					$this -> mensaje = "El grupo que seleccionaste te ocasiona un cruce de horarios";
						break;
				case 5:
					$this -> mensaje = "S&oacute;lo se autoriza un cruce de una materia de a una hora";
						break;
				case 7:
					$this -> mensaje = "El grupo ha sido seleccionado correctamente";
						break;
			}
			
		} // function seleccion3T($error=0)
		
		function pseleccionT(){
			
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$seleccion = new Seleccionalumno();
			
			$seleccion -> registro = $id;
			$seleccion -> clavemateria = $this -> post("materia");
			$seleccion -> periodo = $periodo;
			$seleccion -> save();
			
			$this->redirect('alumnoselecc/seleccion2T');
		} // function pseleccionT()
		
		function pkardexT(){
			
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				//$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$xpkardex = new Xpkardex();
			
			$xpkardex -> registro = $id;
			$xpkardex -> materia = $this -> post("materia");
			$xpkardex -> periodo = $this -> post("periodo");
			$xpkardex -> tipo = $this -> post("tipo");
			
			$xpkardex -> promedio = '0';
	
			$xpkardex -> save();
			
			$this->redirect('alumnoselecc/seleccion1T');
		} // function pkardexT()
		
		function epkardexT(){
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$xpkardex = new Xpkardex();
			
			$tmp = $xpkardex -> find($this -> post("id"));
			$tmp -> delete();
			
			$this->redirect('alumnoselecc/seleccion1T');
		} // function epkardexT()
		
		function epseleccionT(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}

			$id = Session::get_data('registro');
			$seleccion = new Seleccionalumno();
			
			$tmp = $seleccion -> find_first($this -> post("id"));
			
			$tmp -> delete();
			
			$planesmaterias = new Planmateria();
			$xpkardex = new Xpkardex();
			$kardex = new KardexIng();
			
			$pmaterias = $planesmaterias -> find("idplan=".$this -> idplan." ORDER BY clavemateria");
			
			//QUITAR MATERIAS QUE YA ESTAN EN KARDEX O COMO MATERIA SOLICITADA PARA KARDEX
			$xxx = 0;
			if($pmaterias) foreach($pmaterias as $p){
				$n = $kardex -> count("registro=".$id." AND clavemat='".$p -> clavemateria."'");
				if($n==0){
					$n = $xpkardex -> count("registro=".$id." AND materia='".$p -> clavemateria."'");
					if($n==0){
						$this -> pmaterias[$xxx] = $p;
						$xxx++;
					}
				}
			}
			
			$this->redirect('alumnoselecc/seleccion2T');
		} // function epseleccionT()
		
		function deseleccionarT(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
		
			$xccursos		= new Xtcursos();
			$xalumnocursos 	= new Xtalumnocursos();
			
			$xccurso = $xccursos -> find_first( "clavecurso = '".$this -> post("clavecurso")."'" );
			
			$xalumnocurso = $xalumnocursos -> find_first
					( "registro = ".$id.
							" and curso = '".$xccurso -> clavecurso."'".
								"and periodo = ".$periodo );
			$xalumnocurso -> delete();
			
			$xccurso -> disponibilidad += 1;
			$xccurso -> save();
			
			$this->redirect('alumnoselecc/seleccion3T');
		} // function deseleccionarT()
		
		function seleccionarT(){
			$periodo = $this -> actualselecc;
			
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			
			$xccursos 		= new Xtcursos();
			$xalumnocursos	= new Xtalumnocursos();
			$xchorascursos	= new Xthorascursos();
			$autorizarCru	= new AutorizarCruces();
			$cursoscomunes	= new CursosComunes();
			
			// Traerme el curso de xccursos, con el ID del curso que me llegar por POST
			$xccurso = $xccursos -> find_first( "id = ".$this -> post("grupo") );
			
			// 
			$autCruce = $autorizarCru -> find_first
												( "registro = ".$id." 
													and clavecurso = '".
														$xccurso -> clavecurso."'" );
					
			
			if( $xccurso -> disponibilidad <= 0 ){
				// El 1 significa que el curso no tiene cupos disponibles
				$this -> redirect( 'alumnoselecc/seleccion3T/1' );
				return;
			}
			else{				
				
				// $ocupado me sirve para saber si a esa hora y día el alumno tiene
				//espacio libre o no.
				$ocupado = 0;
				
				$i = 0;
				foreach( $xalumnocursos -> find( "registro = ".$id." and periodo = ".$periodo ) as $xalumnocurso ){
					$xalumncur[$i] = $xalumnocurso;
					$j = 0;
					foreach( $xchorascursos -> find( "clavecurso = '".
							$xalumnocurso -> curso."' ORDER BY id ASC" ) as $xchorascurso ){
						if( $xchorascursos -> find_first
								( "clavecurso = '".$xccurso -> clavecurso."'".
									" and dia = ".$xchorascurso -> dia.
										" and hora = ".$xchorascurso -> hora ) ){
							$ocupado++;
						}
						$j++;
					}
					$i++;
				}
				
				
				if( $ocupado == 0 ){ // No tiene cruces de materias, por lo que puede agregar una nueva materia.
					
					$xalumnocurso = new Xtalumnocursos();
					$xalumnocurso -> registro = $id;
					$xalumnocurso -> periodo = $periodo;
					
					$xalumnocurso -> curso = $xccurso -> clavecurso;
					$xalumnocurso -> faltas1 = '0';
					$xalumnocurso -> faltas2 = '0';
					$xalumnocurso -> faltas3 = '0';
					$xalumnocurso -> calificacion1 = 300;
					$xalumnocurso -> calificacion2 = 300;
					$xalumnocurso -> calificacion3 = 300;
					$xalumnocurso -> faltas = '0';
					$xalumnocurso -> calificacion = 300;
					$xalumnocurso -> situacion = "-";
					
					$xalumnocurso -> create();
					
					$xccurso -> disponibilidad -= 1;
					$xccurso -> save();
					
				}
				else{
					if( $autCruce -> clavecurso == $xccurso -> clavecurso && 
							$autCruce -> registro == $id &&
									$autCruce -> horasautorizadas >= $ocupado ){
						
						$xalumnocurso = new Xtalumnocursos();
						$xalumnocurso -> registro = $id;
						$xalumnocurso -> periodo = $periodo;
						
						$xalumnocurso -> curso = $xccurso -> clavecurso;
						$xalumnocurso -> faltas1 = '0';
						$xalumnocurso -> faltas2 = '0';
						$xalumnocurso -> faltas3 = '0';
						$xalumnocurso -> calificacion1 = 300;
						$xalumnocurso -> calificacion2 = 300;
						$xalumnocurso -> calificacion3 = 300;
						$xalumnocurso -> faltas = '0';
						$xalumnocurso -> calificacion = 300;
						$xalumnocurso -> situacion = "-";
						
						$xalumnocurso -> create();
						
						$xccurso -> disponibilidad -= 1;
						$xccurso -> save();
					}
					else{
						if( $cursoscomunes -> find_first
								( "clavecurso1 = '".$xccurso -> clavecurso."'"."
									or clavecurso2 = '".$xccurso -> clavecurso."'" ) ){
							$xalumnocurso = new Xtalumnocursos();
							$xalumnocurso -> registro = $id;
							$xalumnocurso -> periodo = $periodo;
							
							$xalumnocurso -> curso = $xccurso -> clavecurso;
							$xalumnocurso -> faltas1 = '0';
							$xalumnocurso -> faltas2 = '0';
							$xalumnocurso -> faltas3 = '0';
							$xalumnocurso -> calificacion1 = 300;
							$xalumnocurso -> calificacion2 = 300;
							$xalumnocurso -> calificacion3 = 300;
							$xalumnocurso -> faltas = '0';
							$xalumnocurso -> calificacion = 300;
							$xalumnocurso -> situacion = "-";
							
							$xalumnocurso -> create();
							
							$xccurso -> disponibilidad -= 1;
							$xccurso -> save();
						}
						else{
							// El 2 es un mensaje de cruce de horario 
							$this -> redirect( 'alumnoselecc/seleccion3T/2' );
							return;
						}
					}
				}
			}
			// Si le mando el 7 a seleccion3, es un mensaje de éxito...
			$this -> redirect('alumnoselecc/seleccion3T/7');
		} // function seleccionarT()
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function grupos($clavemateria,$plan=2000){
			$periodo = $this -> actualselecc;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			unset($this -> cursos);
			
			
			
			$id = Session::get_data('registro');
			$periodo = "32008";
			
			$maestros = new Maestros();
			$materias = new Materia();
			$precurso = new Precurso();
			
			
			$total = 0;
			
			if($periodo[0]=='3'){
				$this -> periodo = "FEB - JUN, ";
				$this -> periodo .= (substr($periodo,1,4)+1);
			}
			else{
				$this -> periodo = "AGO - DIC, ";
				$this -> periodo .= substr($periodo,1,4);
			}
			
			$precursos = $precurso -> find("clavemateria='".$clavemateria."'");
			
			$i=0;
			if($precursos) foreach($precursos as $curso){ 
				$this -> cursos[$i] = $curso;
				$this -> materia[$i] = $materias -> find_first("clave='".$curso -> clavemateria."'");
				$this -> profesor[$i] = $maestros -> find_first("nomina='".$curso -> registroprofesor."'");
				$i++;
			}

		}
		
		
		function numero_letra($numero){
			if($numero==300 || $numero=="-"){
				return "-";
			}
			
			if($numero==999 || $numero=="NP"){
				return "NO PRESENTO";
			}
			
			if($numero==500 || $numero=="PND"){
				return "PENDIENTE";
			}
		
			if($numero<30){
				if($numero<20){
					switch($numero){
						case 0: return "CERO";
						case 1: return "UNO";
						case 2: return "DOS";
						case 3: return "TRES";
						case 4: return "CUATRO";
						case 5: return "CINCO";
						case 6: return "SEIS";
						case 7: return "SIETE";
						case 8: return "OCHO";
						case 9: return "NUEVE";
						case 10: return "DIEZ";
						case 11: return "ONCE";
						case 12: return "DOCE";
						case 13: return "TRECE";
						case 14: return "CATORCE";
						case 15: return "QUINCE";
						case 16: return "DIECISEIS";
						case 17: return "DIECISIETE";
						case 18: return "DIECIOCHO";
						case 19: return "DIECINUEVE";
					}
				}
				else{
					$resultado = "";
					
					if($numero==20){
						return "VEINTE";
					}
					
					switch(floor($numero/10)){
						case 2: $resultado = "VEINTI"; break;
					}
				
					switch($numero%10){
						case 1: $resultado .= "UNO"; break;
						case 2: $resultado .= "DOS"; break;
						case 3: $resultado .= "TRES"; break;
						case 4: $resultado .= "CUATRO"; break;
						case 5: $resultado .= "CINCO"; break;
						case 6: $resultado .= "SEIS"; break;
						case 7: $resultado .= "SIETE"; break;
						case 8: $resultado .= "OCHO"; break;
						case 9: $resultado .= "NUEVE"; break;
					}
					return $resultado;
				}
			}
			else{
				$resultado = "";
				switch(floor($numero/10)){
					case 3: $resultado = "TREINTA"; break;
					case 4: $resultado = "CUARENTA"; break;
					case 5: $resultado = "CINCUENTA"; break;
					case 6: $resultado = "SESENTA"; break;
					case 7: $resultado = "SETENTA"; break;
					case 8: $resultado = "OCHENTA"; break;
					case 9: $resultado = "NOVENTA"; break;
					case 10: $resultado = "CIEN"; break;
				}
				
				switch($numero%10){
					case 1: $resultado .= " Y UNO"; break;
					case 2: $resultado .= " Y DOS"; break;
					case 3: $resultado .= " Y TRES"; break;
					case 4: $resultado .= " Y CUATRO"; break;
					case 5: $resultado .= " Y CINCO"; break;
					case 6: $resultado .= " Y SEIS"; break;
					case 7: $resultado .= " Y SIETE"; break;
					case 8: $resultado .= " Y OCHO"; break;
					case 9: $resultado .= " Y NUEVE"; break;
				}
				return $resultado;
			}
		}
		
		function nombreProfesor($nomina){
			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina= ".(int)$nomina);
			return $maestro -> nombre;
		}
		
		function nombreSalon($salon){
			$xsalones = new Xsalones();
			$xsalon = $xsalones -> find_first("id=".$salon."");
			return $xsalon -> edificio.":".$xsalon -> nombre;
		}
		
		function obtenerMateria($clave, $especialidad){
			$materias = new Materiasing();
			$materia = $materias -> find_first("clavemat='".$clave."' AND especialidad=".$especialidad);
			return $materia -> nombre;
		}
		
		function sacarMateria( $clave, $plan ){
			$materias = new materia();
			$materia = $materias -> find_first("clave='".$clave."' AND plan=".$plan);
			return $materia -> nombre;
		}
		
		function obtenerCreditos($clave, $plan){
			$materias = new Planmateria();
			$materia = $materias -> find_first("clavemateria='".$clave."' AND idplan=".$plan);
			$materia = $materias -> find_first("clavemateria='".$clave."' AND idplan=".$plan);
			return $materia -> creditos;
		}
		
		function incrementaPeriodo($periodo){
		
			if(date("m",time())<7){
				$actual = "1".date("Y",time());
			}
			else{
				$actual = "3".date("Y",time());
			}
			
			$tmp = $periodo;
		
			if($periodo[0]==1){
				$periodo = "3".substr($periodo,1,4);				
			}
			else{
				$periodo = "1".(substr($periodo,1,4) + 1);
			}
			
			return $periodo;
		}
		
		function horarioT(){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			if(Session::get_data('plantel')!="N"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xtcursos();
			$xthcursos = new Xthorascursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xtalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			$xtsalones = new Xtsalones();
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			$cursos = $xacursos -> find("registro=".$id." AND periodo=".$periodo);
			$this -> cuantosCursos = $xacursos -> count("registro=".$id." AND periodo=".$periodo);
			$i=1;
			
			for ( $l = 1; $l <= 42; $l++ ){
				$this -> salon[$l] = "";
				$this -> clavcurso[$l] = "";
				$this -> clavemat[$l] = "";
				$this -> nombremat[$l] = "";
				$this -> bloque [$l] = 0;
			}
			
			if($this -> cuantosCursos > 0 ){
				foreach($cursos as $curso){
					if($curso -> curso!=""){
						$this -> tmp[$i] = $xcursos -> find_first("id=".(substr($curso -> curso,3)+0));
					}
					
					if($this -> tmp[$i] -> nomina!="")
						$this -> profesor[$i] = $maestros -> find_first("nomina=".$this -> tmp[$i] -> nomina."");
					if($this -> tmp[$i] -> materia!="" && $especialidad -> siNumEsp != "")
						// $this -> SacarMateria es una función...
						$this -> materia[$i] = $this -> sacarMateria($this -> tmp[$i] -> materia, $this -> plan);
					
					foreach ( $xthcursos -> find ( "clavecurso = '"
							.$this -> tmp[$i] -> clavecurso."'" ) as $xthcur ){
						
						if ( $xthcur -> bloque == 2 )
							continue;
						
						$dia = $xthcur -> dia;
						$hora = $xthcur -> hora;
						
						$xtsal = $xtsalones -> find_first ( "id = ".$xthcur -> xtsalones_id );
						
						// Le resto uno para el momento de hacer la multiplicación
						// para obtener las coordenadas correctas...
						$hora -= 1;
						// Saco la coordenada para el horario
						$coord = ( ( $hora * 6 ) + $dia );
						$this -> salon[$coord] = $xtsal -> edificio.":".$xtsal -> nombre;
						$this -> clavcurso[$coord] = $this -> tmp[$i] -> clavecurso;
						$this -> clavemat[$coord] = $this -> tmp[$i] -> materia;
						$this -> nombremat[$coord] = $this -> materia[$i];
						$this -> bloque [$coord] = $xthcur -> bloque;
					}
					$i++;
				}
			}
			
			$i=1;
			if($this -> cuantosCursos > 0 ){
				foreach($cursos as $curso){
					if($curso -> curso!=""){
						$this -> tmp2[$i] = $xcursos -> find_first("id=".(substr($curso -> curso,3)+0));
					}
					
					if($this -> tmp2[$i] -> nomina!="")
						$this -> profesor2[$i] = $maestros -> find_first("nomina=".$this -> tmp2[$i] -> nomina."");
					if($this -> tmp2[$i] -> materia!="" && $especialidad -> siNumEsp != "")
						// $this -> SacarMateria es una función...
						$this -> materia2[$i] = $this -> sacarMateria($this -> tmp2[$i] -> materia, $this -> plan);
						
					foreach ( $xthcursos -> find ( "clavecurso = 
							'".$this -> tmp2[$i] -> clavecurso."'" ) as $xthcur ){
						
						if ( $xthcur -> bloque == 0 || $xthcur -> bloque == 1)
							continue;
						
						$dia = $xthcur -> dia;
						$hora = $xthcur -> hora;
						
						$xtsal = $xtsalones -> find_first ( "id = ".$xthcur -> xtsalones_id );
						
						// Le resto uno para el momento de hacer la multiplicación
						// para obtener las coordenadas correctas...
						$hora -= 1;
						// Saco la coordenada para el horario
						$coord = ( ( $hora * 6 ) + $dia );
						$this -> salon2[$coord] = $xtsal -> edificio.":".$xtsal -> nombre;
						$this -> clavcurso2[$coord] = $this -> tmp2[$i] -> clavecurso;
						$this -> clavemat2[$coord] = $this -> tmp2[$i] -> materia;
						$this -> nombremat2[$coord] = $this -> materia2[$i];
						$this -> bloque2 [$coord] = $xthcur -> bloque;
					}
					$i++;
				}
			}
		} // function horarioT()
		
		function horariogeneral(){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			$cursos = $xacursos -> find("registro=".$id." AND periodo=".$periodo);
			$this -> cursos = $xacursos -> count("registro=".$id." AND periodo=".$periodo);
			$i=0;
			
			if($this -> cursos > 0 ){
				foreach($cursos as $curso){ 
					if($curso -> curso!=""){
						$tmp = $xcursos -> find_first("id=".(substr($curso -> curso,3)+0));
					}
					$this -> mihorario[$i] = $tmp;
					if($tmp -> nomina!="")
						$this -> profesor[$i] = $maestros -> find_first("nomina=".$tmp -> nomina."");
					if($tmp -> materia!="" && $especialidad -> siNumEsp != "")
						$this -> materia[$tmp -> materia] = $this -> sacarMateria($tmp -> materia,$this -> plan);
					
					$xsalones = new Xsalones();
					
					if($tmp->luness != "-" && $this -> salones[$tmp->luness] == 0)
						$this -> salones[$tmp->luness] = $tmp->luness;
					
					if($tmp->martess != "-" && $this -> salones[$tmp->martess] == 0)
						$this -> salones[$tmp->martess] = $tmp->martess;
					
					if($tmp->miercoless != "-" && $this -> salones[$tmp->miercoless] == 0)
						$this -> salones[$tmp->miercoless] = $tmp->miercoless;
						
					if($tmp->juevess != "-" && $this -> salones[$tmp->juevess] == 0)
						$this -> salones[$tmp->juevess] = $tmp->juevess;
					
					if($tmp->vierness != "-" && $this -> salones[$tmp->vierness] == 0)
						$this -> salones[$tmp->vierness] = $tmp->vierness;
					
					if($tmp->sabados != "-" && $this -> salones[$tmp->sabados] == 0)
						$this -> salones[$tmp->sabados] = $tmp->sabados;
					
					
					$i++;
					
				}
			}
		}
		
		
		function evaluacion(){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			$cursos = $xacursos -> find("registro=".$id);
			$this -> cursos = $xacursos -> count("registro=".$id);
			
			$i=0;
			
			if($this -> cursos > 0 ){
				foreach($cursos as $curso){
	
					$xevaluacion = new Evaluacion();
					$n = $xevaluacion -> count("registro=".$id." AND curso='".$curso -> curso."'");
					
					if($n>0){
						$this -> evaluacion[$curso -> curso] = 1;
					}
					else{
						$this -> evaluacion[$curso -> curso] = 0;
					}
				
					if($curso -> curso!=""){
						$tmp = $xcursos -> find_first("id=".(substr($curso -> curso,3)+0));
					}
					$this -> mihorario[$i] = $tmp;
					if($tmp -> nomina!="")
						$this -> profesor[$i] = $maestros -> find_first("nomina=".$tmp -> nomina."");
					if($tmp -> materia!="" && $especialidad -> siNumEsp != "")
						$this -> materia[$tmp -> materia] = $this -> sacarMateria($tmp -> materia,$this -> plan);
					
					$xsalones = new Xsalones();
					
					if($tmp->luness != "-" && $this -> salones[$tmp->luness] == 0)
						$this -> salones[$tmp->luness] = $xsalones -> find_first("id=".$tmp->luness);
					
					if($tmp->martess != "-" && $this -> salones[$tmp->martess] == 0)
						$this -> salones[$tmp->martess] = $xsalones -> find_first("id=".$tmp->martess);
					
					if($tmp->miercoless != "-" && $this -> salones[$tmp->miercoless] == 0)
						$this -> salones[$tmp->miercoless] = $xsalones -> find_first("id=".$tmp->miercoless);
						
					if($tmp->juevess != "-" && $this -> salones[$tmp->juevess] == 0)
						$this -> salones[$tmp->juevess] = $xsalones -> find_first("id=".$tmp->juevess);
					
					if($tmp->vierness != "-" && $this -> salones[$tmp->vierness] == 0)
						$this -> salones[$tmp->vierness] = $xsalones -> find_first("id=".$tmp->vierness);
					
					if($tmp->sabados != "-" && $this -> salones[$tmp->sabados] == 0)
						$this -> salones[$tmp->sabados] = $xsalones -> find_first("id=".$tmp->sabados);
					
					
					$i++;
					
				}
			}
		}
		
		function evaluando($curso){
		
			$id = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$id."'");
			
			$xalumnos = new Xalumnos();
			
			$xalumno = $xalumnos -> find_first("registro=".$id);
			
			/*
			if($xalumno -> nombre == ""){
				$this -> redirect("alumno/actualizacion");
			}
			*/
			
			if($usuario -> clave == $id){
				$this->redirect('alumnos/index');
			}
		
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> profesor);
			unset($this -> mihorario);
			unset($this -> especialidad);
			unset($this -> materia);
			
			
			
			$id = Session::get_data('registro');
			
			$xcursos = new Xcursos();
			$maestros = new Maestros();
			$materias = new Materia();
			$xacursos = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$this -> alumno = $alumnos -> vcNomAlu;
			$this -> ingreso = $alumnos -> miPerIng;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$plan = substr($alumno -> enPlan,2,2);
			$carreras = new Carrera();
			$carrera = $carreras -> find_first("clave=".$especialidad -> siNumEsp." AND modelo=20".$plan);
			
			if($especialidad -> siNumEsp == 601 && $plan=="07"){
				$carrera -> id = 6;
			}
			if($especialidad -> siNumEsp == 800 && $plan=="07"){
				$carrera -> id = 8;
			}
			
			$planes = new Plan();
			$plan = $planes -> find_first("idcarrera=".$carrera -> id." AND nombre=20".$plan);
			$this -> idplan = $plan -> id;
			$this -> plan = $plan -> nombre;
			
			//$xcurso = $xacursos -> find_first("curso='".$curso."'");
			
			$xcurso = $xcursos -> find_first("curso='".$curso."'");
			$this -> curso = $xcurso;
			
			$this -> profesor = $maestros -> find_first("nomina=".$xcurso -> nomina);
			$this -> profesor = $this -> profesor -> nombre;
		}
		
		function evaluar($curso){
		
			$registro = Session::get_data('registro');
			
			$xevaluacion = new Evaluacion();
			
			$xevaluacion -> registro = $registro;
			$xevaluacion -> curso = $curso;
			
			$xevaluacion -> p1 = $this -> post("p1");
			$xevaluacion -> p2 = $this -> post("p2");
			$xevaluacion -> p3 = $this -> post("p3");
			$xevaluacion -> p4 = $this -> post("p4");
			$xevaluacion -> p5 = $this -> post("p5");
			$xevaluacion -> p6 = $this -> post("p6");
			$xevaluacion -> p7 = $this -> post("p7");
			$xevaluacion -> p8 = $this -> post("p8");
			$xevaluacion -> p9 = $this -> post("p9");
			$xevaluacion -> p10 = $this -> post("p10");
			$xevaluacion -> p11 = $this -> post("p11");
			$xevaluacion -> p12 = $this -> post("p12");
			$xevaluacion -> p13 = $this -> post("p13");
			$xevaluacion -> p14 = $this -> post("p14");
			$xevaluacion -> p15 = $this -> post("p15");
			$xevaluacion -> p16 = $this -> post("p16");
			$xevaluacion -> p17 = $this -> post("p17");
			$xevaluacion -> p18 = $this -> post("p18");
			$xevaluacion -> p19 = $this -> post("p19");
			$xevaluacion -> p20 = $this -> post("p20");
			$xevaluacion -> p21 = $this -> post("p21");
			
			if($this -> post("comentarios")=="")
				$xevaluacion -> comentarios = "-";
			else
				$xevaluacion -> comentarios = $this -> post("comentarios");
			
			$xevaluacion -> periodo = 32008;
			$xevaluacion -> fecha = date("Y-m-d H:i:s",time());
			$xevaluacion -> save();
			
			$this -> redirect("alumno/evaluacion");
		}
		
		function actualizacion(){
		
		}
		
		function cgruposprofesor($nomina=0){
				
				
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			$xcursos = new Xcursos();
			
			unset($this -> maestros);
			unset($this -> xmaterias);
			unset($this -> maestros);
			unset($this -> materias);
			unset($this -> xmaestro);
			unset($this -> xmateria);
			unset($this -> xsalones);
			unset($this -> cursos);
			unset($this -> profesor);
		
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('general/inicio');
			}
			
			$this -> cursos = $xcursos -> find("nomina=".$nomina);
			$this -> total = $xcursos -> count("nomina=".$nomina)/10;
			
			$this -> actualselecc= $i; 
			
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			
			$i=0;
			if($this -> cursos) foreach($this -> cursos as $curso){
				
				$xmat = $xmaterias -> find_first("clave='".$curso -> materia."'");
				$this -> materias[$i] = $xmat -> clave." - ".$xmat -> nombre;
				
				$xmae = $maestros -> find_first("nomina=".$curso -> nomina);
				$this -> maestros[$i] = $xmae -> nombre." ".$xmae -> paterno." ".$xmae -> materno." (".$xmae -> nomina.")";
				
				$i++;
			}
			
		}	
		
		function cgruposmateria($materia=0){
				
				
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			$xcursos = new Xcursos();
			
			unset($this -> maestros);
			unset($this -> xmaterias);
			unset($this -> maestros);
			unset($this -> materias);
			unset($this -> xmaestro);
			unset($this -> xmateria);
			unset($this -> xsalones);
			unset($this -> cursos);
			unset($this -> profesor);
		
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('general/inicio');
			}
			
			$this -> cursos = $xcursos -> find("materia='".$materia."'");
			$this -> total = $xcursos -> count("materia='".$materia."'")/10;
			
			$this -> actualselecc= $i; 
			
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			
			$i=0;
			if($this -> cursos) foreach($this -> cursos as $curso){
				
				$xmat = $xmaterias -> find_first("clave='".$curso -> materia."'");
				$this -> materias[$i] = $xmat -> clave." - ".$xmat -> nombre;
				
				$xmae = $maestros -> find_first("nomina=".$curso -> nomina);
				$this -> maestros[$i] = $xmae -> nombre." ".$xmae -> paterno." ".$xmae -> materno." (".$xmae -> nomina.")";
				
				$i++;
			}
			
		}
		
		function cgrupos($i=0){
				
				
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			$xcursos = new Xcursos();
			
			unset($this -> maestros);
			unset($this -> xmaterias);
			unset($this -> maestros);
			unset($this -> materias);
			unset($this -> xmaestro);
			unset($this -> xmateria);
			unset($this -> xsalones);
			unset($this -> cursos);
			unset($this -> profesor);
		
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('general/inicio');
			}
			
			
			$this -> cursos = $xcursos -> find("carrera='TODOS' OR carrera='' OR carrera='".Session::get_data('carrerita')."' ORDER BY materia LIMIT ".$i.",10");
			$this -> total = $xcursos -> count("carrera='TODOS' OR carrera='' OR carrera='".Session::get_data('carrerita')."'")/10;
			
			$this -> actualselecc= $i; 
			
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			$xsalones = new Xsalones();
			
			$i=0;
			if($this -> cursos) foreach($this -> cursos as $curso){
				
				$xmat = $xmaterias -> find_first("clave='".$curso -> materia."'");
				$this -> materias[$i] = $xmat -> clave." - ".$xmat -> nombre;
				$this -> claves[$i] = $xmat -> clave;
				
				$xmae = $maestros -> find_first("nomina=".$curso -> nomina);
				$this -> maestros[$i] = $xmae -> nombre." ".$xmae -> paterno." ".$xmae -> materno." (".$xmae -> nomina.")";
				$this -> nominas[$i] = $xmae -> nomina;
				
				if($curso -> luness!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> luness);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				if($curso -> martess!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> martess);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				if($curso -> miercoless!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> miercoless);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				if($curso -> juevess!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> juevess);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				if($curso -> vierness!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> vierness);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				if($curso -> sabados!="-"){
					$xsalon = $xsalones -> find_first("id=".$curso -> sabados);
					$this -> salones[$xsalon -> id] = $xsalon -> edificio.":".$xsalon -> nombre;
				}
				
				$i++;
			}
			
		}
				
		function cmodificagrupos($tipo,$idcurso,$materia="", $maestro=""){
				
			if($error == "ERROR")
				$this -> error = 1;
			else
				$this -> error = 0;
				
			$xmaterias = new Xmaterias();
			$maestros = new Maestros();
			$xsalones = new Xsalones();
			$xcursos = new Xcursos();
			
			unset($this -> maestros);
			unset($this -> xmaterias);
			unset($this -> xcurso);
			unset($this -> xmaestro);
			unset($this -> xmateria);
			unset($this -> xsalones);
			unset($this -> cursos);
			unset($this -> maestros);
			unset($this -> materias);
			
			$this -> curso = $idcurso;	
			$this -> xcurso = $xcursos -> find_first("id=".$idcurso);

		
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('general/inicio');
			}
			
			if($tipo=="ver"){
				$materia = $this -> xcurso -> materia;
				$maestro = $this -> xcurso -> nomina;
			}
			
			if($materia=="")
				$this -> xmaterias = $xmaterias -> find("1 ORDER BY clave");
			
			if($materia!="" && $maestro==""){
				unset($this -> xmaterias);
				$xmateria = $xmaterias -> find_first("clave='".$materia."'");
				$this -> xclave = $xmateria -> clave;
				$xmateria = $xmateria -> clave ." - ". $xmateria -> nombre;
				$this -> xmateria = $xmateria;
				
				$this -> maestros = $maestros -> find("nomina>0 AND nomina<9999 ORDER BY nombre");
			}
			
			if($materia!="" && $maestro!=""){
				unset($this -> maestros);
				unset($this -> xmaterias);
				
				$xmateria = $xmaterias -> find_first("clave='".$materia."'");
				$this -> xclave = $xmateria -> clave;
				$xmateria = $xmateria -> clave ." - ". $xmateria -> nombre;
				$this -> xmateria = $xmateria;
				
				$xmaestro = $maestros -> find_first("nomina=".$maestro);
				$this -> xnomina = $xmaestro -> nomina;
				$this -> xmaestro = $xmaestro -> nomina ." - ". $xmaestro -> nombre." " .$xmaestro -> paterno." ".$xmaestro -> materno;
				
				$this -> xsalones = $xsalones -> find("1 ORDER BY edificio,nombre");
			}
			
		}
			
		function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			$this->redirect('/');
		}
	
		function encuestas(){
			//borra las variables que se quedaban con persistencia
			
			
			/*
			$this -> activarFormulario = 0;
			$reg = Session::get_data('registro');
			
			for( $i = 1; $i < 14; $i++ ){
				$this -> preguntas[$i]= NULL;
			}
				
			if (substr($reg,0,4)== "prof" || substr($reg,0,4)== "elch" ){				
					$this -> activarFormulario = 1;
					$this -> prof = 1;
			}
			else{
				$this -> prof = 0;
				$this -> registroAlumno = $reg;
				$Usuarios = new Usuariosencuestas();
				if($Usuarios -> find_first("registro = ".$reg)){
					$suma=0;
					for( $i = 1; $i < 13 ; $i++ ){
						$campo = "s".$i;
						$suma = $suma + $Usuarios -> $campo;		
					}				
					if ( $suma == 12 ){
						$this -> activarFormulario = 1;
						
					}else{
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
					$nuevoUsuario -> save();
					$Usuarios -> find_first("registro = ".$reg);
				}
				$Preguntas = new Preguntas();
				for( $i = 1; $i < 14; $i++ ){
						$var= "s".$i;
						if ( $Usuarios -> $var == 0 )	
							$this -> preguntas[$i] = $Preguntas -> find("clave = $i order by id ASC");
				}
			}
			
			if($this -> activarFormulario == 1){
				$this -> redirect("alumno/".$origen);
				return;
			}
			*/
			
			$this -> activarFormulario = 0;
			$reg = Session::get_data('registro');
			
			for( $i = 1; $i < 14; $i++ ){
				$this -> preguntas[$i]= NULL;
			}
				
			if (substr($reg,0,4)== "prof" || substr($reg,0,4)== "elch" ){
				$this -> activarFormulario = 1;
				$this -> prof = 1;
			}
			else{
				$this -> prof = 0;
//				$reg = 9310001; // borrar esto, yo lo puse para hacer pruebas....
				$this -> registroAlumno = $reg;
				$Usuarios = new Usuariosencuestas();			
				if($Usuarios -> find_first("registro = ".$reg." and periodo = ".$this -> actual)){
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
					$nuevoUsuario -> periodo = $this -> actual;
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
			
			/*
			$this -> set_response('view');			
			$seccionNombre = $this -> request("seccionNombre");
			$seccionNumero = $this -> request("seccionNumero");
			$registroAlumno = $this -> request("registroAlumno");
			
			for($i=0; $i<50; $i++){
				if ($this -> post("r".$i) != ''){
					$Preguntas = new Preguntas();
					$Preguntas -> find_first("id = ".$i);
					
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
					//echo utf8_encode("<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>");
					//echo "<b>".$Preguntas -> pregunta."</b> <br> Respuesta:".$this -> post("r".$i)."<br>";
					$Preguntas -> save();					
				}
			}
			//Flash::success(utf8_encode('Se guardo exitosamente la sección: ').'<span class="resaltado">'.$seccionNombre.'</span>'); 
			//Flash::success('Se guardo exitosamente la sección: <span class="resaltado">'.$seccionNombre.'</span>'); 
			
			$seccionNumero= "s".$seccionNumero;
			$Usuarios = new Usuariosencuestas();			
			$Usuarios -> find_first("registro = ".$registroAlumno);
			$Usuarios -> $seccionNumero = 1;
			$Usuarios -> save();
			
			$suma=0;
			for($i=1;$i<12;$i++){
				$campo = "s".$i;
				$suma = $suma + $Usuarios -> $campo;		
			}				
	
			$this -> set_response('view');
			*/
			
			
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
			$date1 = date( "Y-m-d H:i:s", mktime( $hour, $min, $sec, $month, $day, $year ) );
			
			for( $i = 1; $i < 53; $i++ ){
				if ( $this -> post("r".$i) != '' || 
						$this -> post("comentario".$i) != '' || 
								$this -> post("opctexto".$i) != '' ||
										$this -> post("opc".$i) != '' ||
												$this -> post("sino".$i) != '' ){
					$Preguntas = new PreguntasSatisfaccion();
					$Respuestas = new RespuestasSatisfaccion();
					$Preguntas -> find_first("id = ".$i);
					
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
						echo "<br />".$this -> post("r".$i)."<br />";
					}
					else if ( $this -> post("tipo".$i) == 1 ){
						echo "<br />".$this -> post( "comentario".$i )."<br />";
						$Respuestas -> preguntas_satisfaccion_id = $i;
						$Respuestas -> fecha = $date1;
						$Respuestas -> respuesta = '0';
						$Respuestas -> comentario = $this -> post("comentario".$i);
						$Respuestas -> registro = Session::get_data("registro");
						$Respuestas -> create();
					}
					else if ( $this -> post("tipo".$i) == 2 ){
						if ( $this -> post("sino".$i ) == 1 )
							echo "<br />Si<br />";
						else
							echo "<br />No<br />";
						
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
							echo "<br />".$this -> post("opctexto".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = $date1;
							$Respuestas -> respuesta = '0';
							$Respuestas -> comentario = $this -> post("opctexto".$i);
							$Respuestas -> registro = Session::get_data("registro");
							$Respuestas -> create();
						}
						else{
							echo "<br />".$this -> post("opc".$i )."<br />";
							$Respuestas -> preguntas_satisfaccion_id = $i;
							$Respuestas -> fecha = "'".$date1."'";
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
			$Usuarios -> find_first( "registro = ".$registroAlumno." and periodo = ".$this -> actualselecc);
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
		
		function prev(){
			
		}
		
		function validarEncuesta(){
			$Usuarios = new Usuariosencuestas();
			if($Usuarios -> find_first( "registro = ".Session::get_data('registro').
					" and periodo = ".$this -> actualselecc) ){
				$suma = 0;
				for( $i = 1; $i < 13; $i++ ){
					$campo = "s".$i;
					$suma = $suma + $Usuarios -> $campo;		
				}
				if ( $suma == 12 ){
					return true;
				}
			}
			if( Session::get_data('master') == 2 )
				return true;
			
			return false;
		}
		
		function validarEvaluacion(){
		
			$registro = Session::get_data('registro');
			
			$mevaluacion = new Evaluacion();
			$mxalumnocursos = new Xalumnocursos();
			
			$m = $mevaluacion -> count("registro=".$registro);
			$n = $mxalumnocursos -> count("registro=".$registro);
			
			if($m>=$n || Session::get_data('OMISION') == "OK"){
				return true;
			}
			
			return true;
		}
		
		function validarActualizacion(){
		
			$registro = Session::get_data('registro');
			
			$xalumnos = new XalumnosPersonal();
			
			if($registro>911000 && $registro<912000){
				return false;
			}
			
			return true;
		}
		
		function validarPreseleccion(){
		
			$registro = Session::get_data('registro');
			
			$mpreseleccion = new Preseleccionalumno();
			
			$m = $mpreseleccion -> count("registro=".$registro." AND periodo=32009");
			
			if( $m > 0 || Session::get_data('OMISION') == "OK" ){
				return true;
			}
			return false;
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
			// Aqui se modifica la fecha para las fichas de pago, en PDF
			// Estos archivos se encuentran en  " /sitios/htdocs/ingenieria/public/files/extraordinarios "
			$reporte -> MultiCell(0,3,"10 / JUL / 2009",0,'L',0);
			
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
			$reporte -> MultiCell(0,3,"10 / JUL / 2009",0,'L',0);
			
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
		
		function fichaPago(){
			$this -> set_response("view");
			//$registro = 911006; 
			$registro = $this -> post("registro");
			$cnx = mysql_connect("localhost","calculo","super dios");
//			$cnx = mysql_connect("localhost","root","vertrigo");
			mysql_select_db("ingenieria",$cnx);
			
			// Este if se hizo en una urgencia ya que no habia tiempo
			// y es para verificar si se acepto o no su carta...
			if ( $registro == 811135 || $registro == 431217 || $registro == 831033 ||
					$registro == 831035 || $registro == 431103 ||
							$registro == 831020 || $registro == 731072 || $registro == 711142 ){
				echo "<h3>Tu carta fue rechazada, te sugerimos que vayas a Control Escolar</h3>";
				exit (1);
			}
			
			$query = "SELECT * FROM alumnos WHERE miReg=".$registro." and (situacion = 'activo' or situacion = 'nuevo ingreso')";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos = mysql_fetch_array($con);
			}
			
			if($datos["vcNomAlu"]==""){
				echo "<center><h1>Tu ficha no esta disponible aun o estas dado de baja</h1></center>";
				echo '<center><b><a href="https://ase.ceti.mx/ingenieria"></a></b></center>';
				
				//$this->redirect('tecnologo/ficha');
				return;
			}
			$query = "SELECT * FROM mifolio WHERE mireg=".$registro." AND periodo=32009";
			$con = mysql_query($query, $cnx);
			if($con){
				$datos2 = mysql_fetch_array($con);
			}
		
			if($registro > 9300000){
				$opcion = 101;
			}
			else{
				$opcion = 201;
			}
			$referencia_sin_dv = $opcion.$registro."309";
			$referenciaPa_sin_dv = "301".$registro."309";
			$referencia = $referencia_sin_dv.$this -> digitoVerificador($referencia_sin_dv);
			$referenciaPa = $referenciaPa_sin_dv.$this -> digitoVerificador($referenciaPa_sin_dv);

			$reporte = new FPDF();
			$reporte -> Open();
			$reporte -> AddFont('Verdana','','verdana.php');
			for($i=0; $i<2; $i++ ){		
				if ($i == 0){
					$copia = "ALUMNO";
				}else{
					$copia = "BANCO";
				}						
				$reporte -> AddPage();												
				$reporte -> Ln();
				
				$reporte -> Image('http://ase.ceti.mx/ingenieria/img/formatoFichaPago.jpg', 5, 20, 200, 220);
				$reporte -> SetFont('Verdana','',10);
				
				$reporte -> SetX(45);
				$reporte -> SetY(37);
				$reporte -> MultiCell(188,3,$referencia,0,'R',0);
				
				$reporte -> SetFont('Verdana','',8);
				
				$reporte -> SetX(50);
				$reporte -> SetY(44);
				$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (00999)          RUTINA (1111)",0,'R',0);
				
				$reporte -> SetFont('Verdana','',7);
				
				$reporte -> Ln();
				$reporte -> SetX(2);
				$reporte -> SetY(41);
				
				$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetX(20);
				$reporte -> MultiCell(0,3,"31 / AGO / 2009",0,'L',0);
				
				if($opcion == 101){				
					$reporte -> SetY(63);
					$reporte -> MultiCell(100,3,"NUEVO INGRESO INGENIERÍA",0,'L',0);
					$reporte -> SetY(66);
					$reporte -> MultiCell(100,3,"CREDENCIAL ALUMNO",0,'L',0);
					$reporte -> SetY(69);
					$reporte -> MultiCell(100,3,"MANUAL DE NUEVO INGRESO",0,'L',0);
					$reporte -> SetY(72);
					$reporte -> MultiCell(100,3,"RECARGO",0,'L',0);
					$reporte -> SetY(63);
					$reporte -> MultiCell(80,3,"$820.00",0,'R',0);
					$reporte -> SetY(66);
					$reporte -> MultiCell(80,3,"$70.00",0,'R',0);
					$reporte -> SetY(69);
					$reporte -> MultiCell(80,3,"$137.00",0,'R',0);
					$reporte -> SetY(72);
					$reporte -> MultiCell(80,3,"$230.00",0,'R',0);
					
//					$reporte -> SetY(76);
//					$reporte -> MultiCell(80,3,"$1027.00",0,'R',0);
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,"$1257.00",0,'R',0);
				}
				else{
					$reporte -> SetY(64);
					$reporte -> MultiCell(100,3,"REINSCRIPCION INGENIERÍA",0,'L',0);
					$reporte -> SetY(67);
					$reporte -> MultiCell(100,3,"RECARGO",0,'L',0);
								
					$reporte -> SetY(64);
					$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
					$reporte -> SetY(67);
					$reporte -> MultiCell(80,3,"$230.00",0,'R',0);
					
//					$reporte -> SetY(76);
//					$reporte -> MultiCell(80,3,"$690.00",0,'R',0);
					$reporte -> SetY(76);
					$reporte -> MultiCell(80,3,"$920.00",0,'R',0);
				}
				$reporte -> SetY(101);
				$reporte -> MultiCell(179,3,$copia,0,'C',0);			
				
				$reporte -> Ln();
				
				$reporte -> SetFont('Verdana','',10);
				
				$reporte -> SetX(45);
				$reporte -> SetY(156);			
				$reporte -> MultiCell(188,3,$referenciaPa,0,'R',0);
				
				$reporte -> SetFont('Verdana','',8);
				
				$reporte -> SetX(50);
				$reporte -> SetY(163);
				$reporte -> MultiCell(188,3,"No. DE EMPRESA CEP (01042)          RUTINA (1111)",0,'R',0);
				
				$reporte -> SetFont('Verdana','',7);
				
				$reporte -> Ln();
				$reporte -> SetX(2);
				$reporte -> SetY(160);
				
				$reporte -> MultiCell(80,3,$registro." - ".$datos["vcNomAlu"],0,'C',0);
				
				$reporte -> Ln();
				$reporte -> Ln();
				$reporte -> Ln();
				
				$reporte -> SetX(20);
				$reporte -> MultiCell(0,3,"31 / AGO / 2009",0,'L',0);
							
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
			}
			$reporte -> Output("public/files/fichas/".$registro.".pdf");
			
			$this->redirect("files/fichas/".$registro.".pdf");
		}
	}
