<?php
			
	class AlumnoController extends ApplicationController {
		
		public $actual = "32008";
		public $proximo = "12009";
	
		function informacion(){
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this -> alumno);
			unset($this -> especialidad);
			unset($this -> tutor);
			
			
			$id = Session::get_data('registro');
			
			
			$banderas = new Banderas();
			$banderola = $banderas -> find_first("registro=".$id);
			
			$alumnos = new Alumnos();
			$maestros = new Maestros();
			$tutorias = new Tutorias();
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
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$tutor = $tutorias -> find_first("mireg=".$id);
			if($tutor -> nomina == "" || $tutor -> nomina == 0){
				$this -> tutor = "AUN NO TIENES TUTOR";
			}
			else{
				$tutor = $maestros -> find_first("nomina=".$tutor -> nomina);
				$this -> tutor = $tutor -> nombre . " " . $tutor -> paterno . " " . $tutor -> materno;
			}
			
		}
		
		function despreseleccionarcurso($plan=2000){
			$periodo = $this -> proximo;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			$preseleccionalumno = new Preseleccionalumno();
			
			$preseleccionalumno -> delete("clavemateria='".$this -> post("materia")."' AND registro=".$id);
			
			$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
		}
		
		function preseleccionarcurso($plan=2000){
			$periodo = $this -> proximo;
			$id = Session::get_data('registro');
			$idplan = Session::get_data('idplan');
			$plan = Session::get_data('idplan');
			
			$banderas = new Banderas();
			
			$x = $banderas -> count("registro=".$id);
			
			if($x>0){
					$tmp = $banderas -> find_first("registro=".$id);
					$tmp -> preseleccion = 1;
					$tmp -> save();
			}

			if($plan<=5){
				$plan = 2000;
			}
			else{
				$plan = 2007;
			}
			
			unset($this -> pre);
			
			$planmaterias = new Planmateria();
			$mishorarios = new Mishorarios();
			$kardexes = new KardexIng();
			$registracursos = new Registracursos();
			
				$tmp = "materia";
				
				if($this -> post($tmp)!=""){
				
					//VALIDACION PARA VER QUE LA EL GRUPO SEA DE UNA MATERIA DEL PLAN DE ESTUDIOS DEL ALUMNO
					$x = $planmaterias -> count("clavemateria='".$this -> post($tmp)."' AND idplan=".$idplan);
					if($x==0)
						$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
					
					//VALIDACION PARA QUE EL ALUMNO NO PRESELECCIONE LA MISMA DOS VECES
					$preseleccionalumno = new Preseleccionalumno();
					$x = $preseleccionalumno -> count("registro=".$id." AND clavemateria='".$this -> post($tmp)."'");
					if($x>0){
						$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
						return;
					}
					
					
					//VALIDACION PARA QUE EL ALUMNO PRESELECCIONE MATERIAS DONDE SE CUMPLEN CIERTOS PRERREQUISITOS
					$mat = $planmaterias -> find_first("clavemateria='".$this -> post($tmp)."' AND idplan=".$idplan);
					
					if($mat -> prerrequisito != "-"){
					
						$kardex = $kardexes -> find("registro=".$id);
						$registracurso = $registracursos -> find("mireg=".$id);
					
						//OBTENER CREDITOS AL TERMINO DEL SEMESTRE
						$kardex = $kardexes -> find("registro=".$id);
						$registracurso = $registracursos -> find("mireg=".$id);
						
						if($registracurso) foreach($registracurso as $regcur){
							$curso = $regcur -> curso;
							$tmpx 	= $mishorarios -> find_first("clavecurso='".$curso."'");
							$clave = $tmpx -> clavemat;
							$mat = $planmaterias -> find_first("idplan='".$idplan."' AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						$bandera = 0;
						if($kardex) foreach($kardex as $calificacion){
							$clave = $calificacion -> clavemat;
							if($clave == $this -> post($tmp)){
								$bandera = 1;
							}
							$mat = $planmaterias -> find_first("idplan='".$idplan."' AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						//SI  LA MATERIA YA ESTA EN KARDEX NO PRESELECCIONARLA
						if($bandera==1)
							$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
						
						$tcreditos = $contador;
						$mat = $planmaterias -> find_first("idplan='".$idplan."' AND clavemateria='".$this -> post($tmp)."'");
					
						if(is_numeric($mat -> prerrequisito)){
							if($mat -> prerrequisito > $tcreditos){
								$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
							}
						}
						else{
							$x = $kardexes -> count("registro=".$id." AND clavemat='".$mat -> prerrequisito."'");
							if($x==0){
								$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
							}
						}
					}
					
					$preseleccionalumno = new Preseleccionalumno();
					$preseleccionalumno -> registro = $id;
					$preseleccionalumno -> clavemateria = $this -> post($tmp);
					$preseleccionalumno -> fecha = time();
					$preseleccionalumno -> create();
				}
			
			
			$this->redirect('alumno/preseleccion/'.$this -> post("plan").'/'.$this -> post("modelo"));
		}
		
		function grupos($clavemateria,$plan=2000){
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
		
		function oferta(){
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this -> pmaterias);
			unset($this -> pmaterias2);
			unset($this -> pmaterias3);
			unset($this -> pactuales);
			unset($this -> materias);
			unset($this -> cursos);
			
			$id = Session::get_data('registro');
			
			$idplan = Session::get_data('idplan');
			$idcarrera = Session::get_data('idcarrera');
			
			$banderas = new Banderas();
			$banderola = $banderas -> find_first("registro=".$id);

			switch($idplan){
				case 1: 
				case 2: 
				case 3: 
				case 4: 
				case 5: $this -> modelo = 2000; break;
				case 6: 
				case 7: 
				case 8: $this -> modelo = 2007; break;
			}
			
			$periodo = "32008";
			
			$alumnos = new Alumnos();
			$planes = new Plan();
			$planmateria = new Planmateria();
			$kardexes = new KardexIng();
			$materias = new Planmateria();
			$material = new Materia();
			$precurso = new Precurso();
			$registracursos = new Registracursos();
			$mishorarios = new Mishorarios();
			 $especialidades = new Especialidades();
			
			$id = Session::get_data('registro');
			$periodo = "32008";
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
						//OBTENER CREDITOS AL TERMINO DEL SEMESTRE
						$kardex = $kardexes -> find("registro=".$id);
						$registracurso = $registracursos -> find("mireg=".$id);
						
						if($registracurso) foreach($registracurso as $regcur){
							$curso = $regcur -> curso;
							$tmp = $mishorarios -> find_first("clavecurso='".$curso."'");
							$clave = $tmp -> clavemat;
							$mat = $materias -> find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						if($kardex) foreach($kardex as $calificacion){
							$clave = $calificacion -> clavemat;
							$mat = $materias -> find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						$tcreditos = $contador;
			
			$pmaterias = $planmateria -> find("idplan=".$idplan." AND obligatoria=1");
			
			$contador=0;
			$i=0;$j=0;$contador=0;$x=0;$y=0;$z=0;
			if($pmaterias) foreach($pmaterias as $pmateria){
				//SI ESTA EN KARDEX, IGNORAR LA MATERIA
				$x = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> clavemateria."'");
				if($x>0){
					continue;
				}
				
				//SI LA MATERIA NO TIENEN NINGUN PRERREQUISITO,,AGREGARLA A LAS MATERIAS DISPONIBLES
				if($pmateria -> prerrequisito=="-"){
					$this -> pmaterias[$i] = $pmateria;
					$i++;
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($pmateria -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						$y = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> prerrequisito."'");
						
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$pmateria -> prerrequisito."'");
							if($z>0) break;
						}
						
						if($y>0 || $z>0){
							$this -> pmaterias[$i] = $pmateria;
							$i++;
						}
						continue;
					}
					else{
						if($pmateria -> prerrequisito <= $tcreditos){
							$this -> pmaterias[$i] = $pmateria;
							$i++;
						}
					}
				}
			}
			$i=0;
			foreach($this -> pmaterias as $pmat){
				$material = $material -> find_first("clave='".$pmat -> clavemateria."'");
				
				$this -> nombres[$i] = $material -> nombre;
				$i++;
			}
			
			
			$pmaterias = $planmateria -> find("idplan=".$idplan." AND especializante=1");
			
			$contador=0;
			$i=0;$j=0;$contador=0;$x=0;$y=0;$z=0;
			if($pmaterias) foreach($pmaterias as $pmateria){
				//SI ESTA EN KARDEX, IGNORAR LA MATERIA
				$x = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> clavemateria."'");
				if($x>0){
					continue;
				}
				
				//SI LA MATERIA NO TIENEN NINGUN PRERREQUISITO,,AGREGARLA A LAS MATERIAS DISPONIBLES
				if($pmateria -> prerrequisito=="-"){
					$this -> pmaterias2[$i] = $pmateria;
					$i++;
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($pmateria -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						$y = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> prerrequisito."'");
						
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$pmateria -> prerrequisito."'");
							if($z>0) break;
						}
						
						if($y>0 || $z>0){
							$this -> pmaterias2[$i] = $pmateria;
							$i++;
						}
						continue;
					}
					else{
						if($pmateria -> prerrequisito <= $tcreditos){
							$this -> pmaterias2[$i] = $pmateria;
							$i++;
						}
					}
				}
			}
			$i=0;
			foreach($this -> pmaterias2 as $pmat){
				$material = $material -> find_first("clave='".$pmat -> clavemateria."'");
				
				$this -> nombres2[$i] = $material -> nombre;
				$i++;
			}
			
			
			$pmaterias = $planmateria -> find("idplan=".$idplan." AND optativa=1");
			
			$contador=0;
			$i=0;$j=0;$contador=0;$x=0;$y=0;$z=0;
			if($pmaterias) foreach($pmaterias as $pmateria){
				//SI ESTA EN KARDEX, IGNORAR LA MATERIA
				$x = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> clavemateria."'");
				if($x>0){
					continue;
				}
				
				//SI LA MATERIA NO TIENEN NINGUN PRERREQUISITO,,AGREGARLA A LAS MATERIAS DISPONIBLES
				if($pmateria -> prerrequisito=="-"){
					$this -> pmaterias3[$i] = $pmateria;
					$i++;
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($pmateria -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						$y = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> prerrequisito."'");
						
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$pmateria -> prerrequisito."'");
							if($z>0) break;
						}
						
						if($y>0 || $z>0){
							$this -> pmaterias3[$i] = $pmateria;
							$i++;
						}
						continue;
					}
					else{
						if($pmateria -> prerrequisito <= $tcreditos){
							$this -> pmaterias3[$i] = $pmateria;
							$i++;
						}
					}
				}
			}
			$i=0;
			foreach($this -> pmaterias3 as $pmat){
				$material = $material -> find_first("clave='".$pmat -> clavemateria."'");
				
				$this -> nombres3[$i] = $material -> nombre;
				$i++;
			}
		}
		
		function seleccion(){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
		}
		
		function preseleccion(){
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this -> materia);
			unset($this -> profesor);
			unset($this -> cursos);
			unset($this -> pmaterias);
			
			$materias = new Materia();
			$maestros = new Maestros();
			
			$this -> registro = Session::get_data('registro');
			$this -> nombre = Session::get_data('nombre');
			$this -> carrera = Session::get_data('nombrecarrera');
			$plan = Session::get_data('idplan');
			
			$preseleccionalumno = new Preseleccionalumno();
			
			$pas = $preseleccionalumno -> find("registro=".$this -> registro." ORDER BY clavemateria");
			
			$i=0;
			if($pas) foreach($pas as $p){
				$this -> cursos[$i] = $p;
				
				if($plan<=5)
					$this -> materia[$i] = $materias -> find_first("clave='".$p -> clavemateria."' AND plan=2000");
				else
					$this -> materia[$i] = $materias -> find_first("clave='".$p -> clavemateria."' AND plan=2007");
					
				$i++;
			}
			
			
			$id = Session::get_data('registro');
			$idplan = Session::get_data('idplan');
			$idcarrera = Session::get_data('idcarrera');
			
			switch($idplan){
				case 1: 
				case 2: 
				case 3: 
				case 4: 
				case 5: $this -> modelo = 2000; break;
				case 6: 
				case 7: 
				case 8: $this -> modelo = 2007; break;
			}
			
			$periodo = "32008";
			
			$alumnos = new Alumnos();
			$planes = new Plan();
			$planmateria = new Planmateria();
			$kardexes = new KardexIng();
			$materias = new Planmateria();
			$material = new Materia();
			$precurso = new Precurso();
			$registracursos = new Registracursos();
			$mishorarios = new Mishorarios();
			$especialidades = new Especialidades();
			$preseleccionalumno = new Preseleccionalumno();
			
			$id = Session::get_data('registro');
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
						//OBTENER CREDITOS AL TERMINO DEL SEMESTRE
						$kardex = $kardexes -> find("registro=".$id);
						$registracurso = $registracursos -> find("mireg=".$id);
						
						if($registracurso) foreach($registracurso as $regcur){
							$curso = $regcur -> curso;
							$tmp = $mishorarios -> find_first("clavecurso='".$curso."'");
							$clave = $tmp -> clavemat;
							$mat = $materias -> find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						if($kardex) foreach($kardex as $calificacion){
							$clave = $calificacion -> clavemat;
							$mat = $materias -> find_first("idplan=".$idplan." AND clavemateria='".$clave."'");
							$contador += $mat -> creditos;
						}
						
						$tcreditos = $contador;
			
			$pmaterias = $planmateria -> find("idplan=".$idplan." ORDER BY clavemateria");
			
			$contador=0;
			$i=0;$j=0;$contador=0;$x=0;$y=0;$z=0;
			if($pmaterias) foreach($pmaterias as $pmateria){
				//SI ESTA EN KARDEX, IGNORAR LA MATERIA
				$x = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> clavemateria."'");
				if($x>0){
					continue;
				}
				
				$todos = $preseleccionalumno -> find("registro=".$id);
				$bandera=0;
				if($todos) foreach($todos as $t){
					$tmp = $planmateria -> find_first("idplan=".$idplan." AND clavemateria='".$t -> clavemateria."' ORDER BY clavemateria");
					if($tmp -> prerrequisito == $pmateria -> clavemateria)
						$bandera=1;
				}
				if($bandera==1) continue;
				
				$x = $preseleccionalumno -> count("registro=".$id." AND clavemateria='".$pmateria -> clavemateria."'");
				if($x>0){
					continue;
				}
				
				$y = $preseleccionalumno -> count("registro=".$id." AND clavemateria='".$pmateria -> prerrequisito."'");
				if($y>0){
					continue;
				}
				
				//SI LA MATERIA NO TIENEN NINGUN PRERREQUISITO,,AGREGARLA A LAS MATERIAS DISPONIBLES
				if($pmateria -> prerrequisito=="-"){
					$this -> pmaterias[$i] = $pmateria;
					$i++;
				}
				else{
					$z=0; $y=0;
					//SI TIENE PERREGISTRO VERIFICAR QUE NO SEA NUMERICO
					if(!is_numeric($pmateria -> prerrequisito)){
						//BUSCAR EN EL KARDEX EL PRERREQUISITO
						$y = $kardexes -> count("registro=".$id." AND clavemat='".$pmateria -> prerrequisito."'");
						
						//BUSCAR EN LAS MATERIAS TOMADAS EL PRERREQUISITO
						$tmp = $registracursos -> find("mireg=".$id."");
						
						if($tmp) foreach($tmp as $t){
							$z = $mishorarios -> count("clavecurso='".$t -> curso."' AND clavemat='".$pmateria -> prerrequisito."'");
							if($z>0) break;
						}
						
						if($y>0 || $z>0){
							$this -> pmaterias[$i] = $pmateria;
							$i++;
						}
						continue;
					}
					else{
						if($pmateria -> prerrequisito <= $tcreditos){
							$this -> pmaterias[$i] = $pmateria;
							$i++;
						}
					}
				}
			}
			$i=0;
			foreach($this -> pmaterias as $pmat){
				$material = $material -> find_first("clave='".$pmat -> clavemateria."'");
				
				$this -> pnombres[$i] = $material -> nombre;
				$i++;
			}
		}
		
		function calificaciones(){
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
			unset($this -> calificacion);
			
			
			$id = Session::get_data('registro');
			
			
			//VALIDACION DE PROCESOS NECESARIOS
			
			$cnx = mysql_connect("localhost","usereval","checar");
			mysql_select_db("evaluadoc",$cnx);
			$query = "SELECT COUNT(*) FROM recibos WHERE registro=".$id;
			$con = mysql_query($query);
			$datos = mysql_fetch_array($con);
			$recibo = $datos["COUNT(*)"];
			
			//$recibos = new Recibos();
			//$recibo = $recibos -> count("registro=".$id);
			
			$banderas = new Banderas();
			$banderola = $banderas -> find_first("registro=".$id);
			
			/*
			if($banderola -> actualizacion == 0){
				$this->redirect('alumno/actualizacion'); 
				return;
			}
			*/
			/*
			if($recibo == 0){
				$this->redirect('alumno/evaluacion'); 
				return;
			}
					
			if($banderola -> preseleccion == 0){
				$this->redirect('alumno/preseleccion'); 
				return;
			}
			*/
			
			$mishorarios = new Mishorarios();
			$maestros = new Maestros();
			$materias = new Materiasing();
			$cursitos = new Registracursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			$calificaciones = new Xcalificacion();
			$xextraordinarios = new Xextraordinarios();
			
			$total = 0;
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$cursos = $cursitos -> find("mireg=".$id);
			$this -> cursos = $cursitos -> count("mireg=".$id);
			$this -> extras = $xextraordinarios -> count("registro=".$id);
			
			if($this -> cursos > 0 ){
				$i=0;
				foreach($cursos as $curso){ 
					$tmp = $mishorarios -> find_first("clavecurso='".$curso -> curso."'");
					$this -> mihorario[$i] = $tmp;
					$this -> profesor[$i] = $maestros -> find_first("nomina=".$tmp -> nomina."");
					$this -> materia[$i] = $materias -> find_first("clavemat='".$tmp -> clavemat."' AND especialidad=".$especialidad -> siNumEsp);
				
					unset($calificacion);
					$calificacion = $calificaciones -> find_first("clavecurso='".$curso -> curso."' AND mireg=".$id);
					
					$next = $xextraordinarios -> count("clavecurso='".$curso -> curso."' AND registro=".$id);
					
					unset($extraordinarios);
					if($next>0){
						$extraordinarios = $xextraordinarios -> find_first("clavecurso='".$curso -> curso."' AND registro=".$id);
						
						if($extraordinarios -> tipo == "E"){
						
							$this -> extra[$i] = $extraordinarios -> calificacion;
							
							if($this -> extra[$i]==300)
								$this -> extra[$i] = "NR";
								
							if($this -> extra[$i]==999)
								$this -> extra[$i] = "NP";
							
							$this -> titulo[$i] = " - ";
							
						}
						else{
							$this -> titulo[$i] = $extraordinarios -> calificacion;
							
							if($this -> titulo[$i]==300)
								$this -> titulo[$i] = "NR";
								
							if($this -> titulo[$i]==999)
								$this -> titulo[$i] = "NP";
							
							$this -> extra[$i] = " - ";
						}
						
					}
					else{
						if($calificaciones -> situacion == "EXTRAORDINARIO"){
							$this -> extra[$i] = "NP";
							$this -> titulo[$i] = " - ";
						}
						else{
							if($calificaciones -> situacion == "TITULO POR CALIFICACION" || $calificaciones -> situacion == "TITULO POR FALTAS" || $calificaciones -> situacion == "TITULO POR NO REGISTRO"){
								$this -> extra[$i] = " - ";
								$this -> titulo[$i] = "NP";
							}
							else{
								$this -> extra[$i] = " - ";
								$this -> titulo[$i] = " - ";
							}
						}
					}
				
					$this -> calificacion[$i] = $calificacion;
				
					$i++;
				}
			}
		}
		
		function kardex(){
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
			unset($this -> calificacion);
			unset($this -> pinicial);
			unset($this -> kardex);
			unset($this -> creditos);
			unset($this -> ncreditos);
			
			$id = Session::get_data('registro');
		
			$materias = new Materiasing();
			$xpkardex = new Xpkardex();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();
			
			if($periodo[0]=='1')
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			
			$this -> periodo .= substr($periodo,1,4);
			$this -> registro = $id;
			
			$alumno = $alumnos -> find_first("miReg=".$id);
			
			$this -> alumno = $alumnos -> vcNomAlu;
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$this -> pmaterias = $materias -> find("idtiesp=".$alumno -> idtiEsp." AND especialidad=".$especialidad -> siNumEsp." AND plan='".$alumno -> enPlan."' ORDER BY nombre");
			$this -> pendientes = $xpkardex -> find("registro=".$id);
			
			$kardexes = new KardexIng();
			$primera = $kardexes -> find_first("registro=".$id." ORDER BY id ASC");
			$this -> pinicial = $alumno -> miPerIng;
			
			$this -> kardexs = $kardexes -> count("registro=".$id);
			$this -> ncreditos = 0;
			$this -> promedio = 0;
			$total = 0;
			
			while($periodo != $this -> pinicial){
				$i++;
				$resultados = $kardexes -> distinct("clavemat","conditions: registro=".$id." AND periodo='".$this -> pinicial."'");
				$this -> periodos[$i] = $this -> pinicial;
				
				$j=0;
				
				foreach($resultados as $resultado){
					$resultado = $kardexes -> find_first("registro=".$id." AND clavemat='".$resultado."' AND periodo='".$this -> pinicial."'");
					
					$x = $materias -> count("clavemat='".$resultado -> clavemat."' AND especialidad=".$especialidad -> siNumEsp);
					if($x==0)
						continue;
					
					$this -> kardex[$i][$j] = $resultado;
					
					switch($this -> kardex[$i][$j] -> tipo_de_ex){
						case 'D': $this -> kardex[$i][$j] -> tipo_de_ex = "ORDINARIO (D)"; break;
						case 'E': $this -> kardex[$i][$j] -> tipo_de_ex = "EXTRAORDINARIO (E)"; break;
						case 'T': $this -> kardex[$i][$j] -> tipo_de_ex = "TITULO SUFICIENCIA (T)"; break;
						case 'R': $this -> kardex[$i][$j] -> tipo_de_ex = "REGULARIZACION (R)"; break;
						case 'A': $this -> kardex[$i][$j] -> tipo_de_ex = "ACREDITACION (A)"; break;
						case 'V': $this -> kardex[$i][$j] -> tipo_de_ex = "REVALIDACION (V)"; break;
					}
					
					$this -> materias[$i][$j]	 = $this -> obtenerMateria($resultado -> clavemat, $especialidad -> siNumEsp);
					$this -> creditos[$i][$j] = $this -> obtenerCreditos($resultado -> clavemat);
					$this -> ncreditos += $this -> creditos[$i][$j];
					$this -> calificaciones[$i][$j] = $this -> numero_letra($resultado -> promedio);
					$this -> promedio += $resultado -> promedio;
					$j++;
					$total++;
				}
				
				$this -> pinicial = $this -> incrementaPeriodo($this -> pinicial);
			}
			
			$this -> promedio /= $total;
			$this -> promedio = round($this -> promedio * 100)/100;
		}
		
		function pkardex(){
			$periodo = $this -> actual;
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			unset($this -> periodo);
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
			
			$id = Session::get_data('registro');
		
			$xpkardex = new Xpkardex();
			
			$xpkardex -> registro = $id;
			$xpkardex -> materia = $this -> post("materia");
			$xpkardex -> periodo = $this -> post("periodo");
			$xpkardex -> tipo = $this -> post("tipo");
			
			$xpkardex -> promedio = 0;
	
			$xpkardex -> save();
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
		
		function obtenerMateria($clave,$especialidad){
			$materias = new Materiasing();
			$materia = $materias -> find_first("clavemat='".$clave."' AND especialidad=".$especialidad);
			return $materia -> nombre;
		}
		
		function obtenerCreditos($clave){
			$materias = new Materiasing();
			$materia = $materias -> find_first("clavemat='".$clave."'");
			return $materia -> nocreditos;
		}
		
		function incrementaPeriodo($periodo){
		
			if(date("m",time())<8){
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
		
		function evaluacion(){
			if(Session::get_data('tipousuario')!="ALUMNO"){
				$this->redirect('/');
			}
			
			$id = Session::get_data('registro');
			$evaluacion = new Evaluacion();
			
			$alumno = $evaluacion -> find_first("registro=".$id);
			
			$this -> alumno = $alumno;

		}
		
		function horario(){
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
			
			$mishorarios = new Mishorarios();
			$maestros = new Maestros();
			$materias = new Materiasing();
			$cursitos = new Registracursos();
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
			
			$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			$this -> especialidad = $especialidad -> vcNomEsp;
			
			$cursos = $cursitos -> find("mireg=".$id);
			$this -> cursos = $cursitos -> count("mireg=".$id);
			$i=0;
			
			if($this -> cursos > 0 ){
				foreach($cursos as $curso){ 
					if($curso -> curso!="")
						$tmp = $mishorarios -> find_first("clavecurso='".$curso -> curso."'");
					$this -> mihorario[$i] = $tmp;
					if($tmp -> nomina!="")
						$this -> profesor[$i] = $maestros -> find_first("nomina=".$tmp -> nomina."");
					if($tmp -> clavemat!="" && $especialidad -> siNumEsp != "")
						$this -> materia[$i] = $materias -> find_first("clavemat='".$tmp -> clavemat."' AND especialidad=".$especialidad -> siNumEsp);
					$i++;
				}
			}
		}
		
		function actualizacion(){
		
		}
		
		function salir(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			$this->redirect('/');
		}
	}
