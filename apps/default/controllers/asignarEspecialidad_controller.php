<?php

    class AsignarEspecialidadController extends ApplicationController {

        public $antesAnterior=12010;
        public $anterior	= 32010;
        public $actual		= 12011;
        public $proximo		= 32011;

		function asignarEspecialidadSegunMateriasTomadas(){
            if(Session::get_data('tipousuario')!="CALCULO"){
				//$this->redirect('/');
            }
			
			$logcambiosespecialidades = new Logcambiosespecialidades();
			$materiasespecializantes = new Materiasespecializantes();
			$areadeformacion = new Areadeformacion();
			$kardexing = new Kardexing();
			$alumnos = new Alumnos();
			$alumnos2 = new Alumnos();
			
			foreach( $alumnos -> find("enPlan = 'PE07' and miPerIng != ".$this -> actual) as $alumno ){
				foreach( $kardexing -> find_all_by_sql("
						select *
						from (
							select count(mesp.areadeformacion_id) cuenta, mesp.areadeformacion_id, k.registro
							From kardex_ing k, materiasespecializantes mesp
							where k.registro = ".$alumno -> miReg."
							and mesp.carrera_id = ".$alumno -> carrera_id."
							and k.clavemat = mesp.clave
							group by areadeformacion_id, k.registro
							order by k.registro
						)t1
						order by t1.registro, t1.cuenta desc") as $king ){
					if( $alumno -> areadeformacion != 0 ){
						continue;
					}
					if( !$alumnos2 -> find_first("miReg = ".$alumno -> miReg.
							" and areadeformacion_id = 0") ){
						continue;
					}
					if( $alumno -> correo == "" || $alumno -> correo == null )
						$alumno -> correo = "0";
					if( $alumno -> stSit == "" || $alumno -> stSit == null )
						$alumno -> stSit = "NI";
					if( $alumno -> situacion == "" || $alumno -> situacion == null )
						$alumno -> situacion = "-";
					if( $alumno -> enSexo == "" || $alumno -> enSexo == null )
						$alumno -> enSexo = "H";
					$alumno -> areadeformacion_id = $king -> areadeformacion_id;
					echo $alumno -> miReg." ".$alumno -> areadeformacion_id."<br />";
					$alumno -> save();
					
					$logcambiosespecialidades -> nomina = "Calculo";
					$logcambiosespecialidades -> registro = $alumno -> miReg;
					$logcambiosespecialidades -> carrera_id = $alumno -> carrera_id;
					$logcambiosespecialidades -> areadeformacion_id = $alumno -> areadeformacion_id;
					$year = date ("Y"); $month = date ("m"); $day = date ("d");
					$hour = date ("H"); $minute = date ("i"); $second = date ("s");
					$logcambiosespecialidades -> fecha = mktime($hour, $minute, $second, $month, $day, $year);
					$logcambiosespecialidades -> create();
				}
			}
		} // function asignarEspecialidadSegunMateriasTomadas()
		
		function alumnosConEsp(){
			//$this -> valida();
			
			$alumnos = new Alumnos();
			$kardex_ing = new KardexIng();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			
			unset($this -> alumnos);
			unset($this -> carrera);
			unset($this -> areadeformacion);
			unset($this -> p1);
			unset($this -> p2);
			unset($this -> numAlumnoConEsp);
			
			$i = 0;
			foreach( $alumnos -> find("areadeformacion_id != 0 and enPlan = 'PE07' 
					ORDER BY miReg LIMIT 0,30") as $alumno ){
				$this -> alumnos[$i] = $alumno;
				$this -> carrera[$i] = $carrera -> find_first("id = ".$alumno -> carrera_id);
				$this -> areadeformacion[$i] = $areadeformacion -> find_first
						("idareadeformacion = ".$alumno -> areadeformacion_id.
								" and carrera_id = ".$alumno -> carrera_id);
				$i++;
			}
			$this -> numAlumnoConEsp = $alumnos->count("areadeformacion_id != 0 and enPlan = 'PE07'");
			$this -> p1 = 0;
			$this -> p2 = 30;
		} // function alumnosConEsp()
		
		function alumnosConEspAjax($p){
			//$this -> valida();
			$this -> set_response("view");
			
			$alumnos = new Alumnos();
			$kardex_ing = new KardexIng();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			
			unset($this -> alumnos);
			unset($this -> carrera);
			unset($this -> areadeformacion);
			unset($this -> ii);
			unset($this -> numAlumnoConEsp);
			
			$i = 0;
			$this -> ii = $p;
			foreach( $alumnos -> find("areadeformacion_id != 0 and enPlan = 'PE07' 
					ORDER BY miReg LIMIT ".$p.",30") as $alumno ){
				$this -> alumnos[$i] = $alumno;
				$this -> carrera[$i] = $carrera -> find_first("id = ".$alumno -> carrera_id);
				$this -> areadeformacion[$i] = $areadeformacion -> find_first
						("idareadeformacion = ".$alumno -> areadeformacion_id.
								" and carrera_id = ".$alumno -> carrera_id);
				$i++;
			}
			$this -> numAlumnoConEsp = $alumnos->count("areadeformacion_id != 0 and enPlan = 'PE07'");
			
			$this -> p1 = $p - 30; if($this -> p1 <= 0) $this -> p1 = 0;
			$this -> p2 = $p + 30; if($this -> p2 >= $this -> numAlumnoConEsp) $this -> p2 = $p;
			echo $this -> render_partial("alumnosConEspAjax");
		} // function alumnosConEspAjax($p)
		
		function mostrandoAlumnosConEsp(){
			//$this -> valida();
			
			$this -> set_response("view");
			
			$logcambiosespecialidades = new Logcambiosespecialidades();
			$alumnos = new Alumnos();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			$materiasespecializantes = new Materiasespecializantes();
			
			unset($this -> alumnos);
			unset($this -> areadeformacion);
			unset($this -> carrera);
			unset($this -> mEspDeSuCarreraYEsp);
			unset($this -> mEspQueNoSonDeSuEsp);
			unset($this -> espDeMEspQueNoSonDeSuCarrera);
			
			$registro = $this -> post("registro");
			
			$this -> alumno = $alumnos -> find_first("miReg=".$registro);
			
			$this -> areadeformacion = $areadeformacion -> find_first
					("idareadeformacion = ".$this -> alumno -> areadeformacion_id.
					" and carrera_id = ".$this -> alumno -> carrera_id);
			$this -> carrera = $carrera -> find_first("id = ".$this -> alumno -> carrera_id);
			$i = 0;
			foreach( $materiasespecializantes -> find_all_by_sql("
					select *
					from kardex_ing k
					inner join materiasespecializantes mesp on k.clavemat = mesp.clave
					inner join areadeformacion af on mesp.areadeformacion_id = af.idareadeformacion
					and k.registro = ".$this -> alumno -> miReg."
					and af.carrera_id = ".$this -> alumno -> carrera_id."
					and af.idareadeformacion = ".$this -> alumno -> areadeformacion_id."
					and af.carrera_id = mesp.carrera_id") as $mesp ){
				// Mostrar materias especializantes de su Carrera...
				$this -> mEspDeSuCarreraYEsp[$i] = $mesp;
				$i++;
			}
			$i = 0; $j = 0;
			foreach( $materiasespecializantes -> find_all_by_sql("
					select k.periodo, k.tipo_de_ex, mesp.carrera_id, mesp.areadeformacion_id,
					mesp.clave, mesp.nombre
					from kardex_ing k, materiasespecializantes mesp
					where k.registro = ".$this -> alumno -> miReg."
					and k.clavemat = mesp.clave
					and mesp.clave not in (
						select m.clave
						From materia m
						where ( m.carrera_id = ".$this -> alumno -> carrera_id."
						and serie = '-' )
						or ( m.carrera_id = ".$this -> alumno -> carrera_id."
						and serie = ".$this -> alumno -> areadeformacion_id." )
					)
					group by mesp.clave;" ) as $mesp ){
				// Mostrar materias especializantes que ya tomó, que no son de su carrera...
				$this -> mEspQueNoSonDeSuEsp[$i] = $mesp;
				foreach( $areadeformacion -> find_all_by_sql("
						select af.nombreareaformacion, c.nombre
						from materiasespecializantes mesp
						inner join areadeformacion af
						on af.idareadeformacion = mesp.areadeformacion_id
						inner join carrera c
						on c.id = af.carrera_id
						and af.carrera_id = mesp.carrera_id
						where mesp.clave = '".$mesp -> clave."'
						and af.carrera_id not in (
							select carrera_id
							from areadeformacion
							where carrera_id = ".$this -> alumno -> carrera_id."
							and af.idareadeformacion = ".$this -> alumno -> areadeformacion_id."
						)" ) as $area ){
					$this -> espDeMEspQueNoSonDeSuCarrera[$mesp -> clave][$j] = $area;
					$j++;
				}
				$i++;
			}
			echo $this -> render_partial("mostrandoAlumnosConEsp");
		} // function mostrandoAlumnosConEsp()
		
		function alumnosSinEsp(){
			//$this -> valida();
			
			$alumnos = new Alumnos();
			$kardex_ing = new KardexIng();
			$carrera = new Carrera();
			
			unset($this -> alumnos);
			unset($this -> carrera);
			unset($this -> numAlumnoSinEsp);
			
			$i = 0;
			$p = 30;
			foreach( $alumnos -> find("areadeformacion_id = 0 and enPlan = 'PE07' 
					and miPerIng != ".$this -> actual."
					and miPerIng != ".$this -> anterior." ORDER BY miReg") as $alumno ){
				foreach( $kardex_ing -> find_all_by_sql("
						select * from kardex_ing k, materia m
						where k.registro = ".$alumno -> miReg."
						and k.clavemat = m.clave
						and m.semestre >= 4
						and m.carrera_id = ".$alumno -> carrera_id."
						limit 1") as $kardex ){
					if( $i < $p ){
						$this -> alumnos[$i] = $alumno;
						$this -> carrera[$i] = $carrera -> find_first("id = ".$alumno -> carrera_id);
					}
					$i++;
				}
			}
			$this -> numAlumnoSinEsp = $i;
			$this -> p1 = 0;
			$this -> p2 = 30;
		} // function alumnosSinEsp()
		
		function alumnosSinEspAjax($p){
			//$this -> valida();
			$this -> set_response("view");
			
			$alumnos = new Alumnos();
			$kardex_ing = new KardexIng();
			$carrera = new Carrera();
			
			unset($this -> alumnos);
			unset($this -> carrera);
			unset($this -> ii);
			
			$i = 0;
			$this -> ii = $p;
			foreach( $alumnos -> find("areadeformacion_id = 0 and enPlan = 'PE07' 
					and miPerIng != ".$this -> actual."
					and miPerIng != ".$this -> anterior." ORDER BY miReg") as $alumno ){
				foreach( $kardex_ing -> find_all_by_sql("
						select * from kardex_ing k, materia m
						where k.registro = ".$alumno -> miReg."
						and k.clavemat = m.clave
						and m.semestre >= 4
						and m.carrera_id = ".$alumno -> carrera_id."
						limit 1") as $kardex ){
					$this -> alumnos[$i] = $alumno;
					$this -> carrera[$i] = $carrera -> find_first("id = ".$alumno -> carrera_id);
					$i++;
				}
				if( $i == $p+30 || $i == $this -> numAlumnoSinEsp )
					break;
			}
			
			$this -> p1 = $p - 30; if($this -> p1 <= 0) $this -> p1 = 0;
			$this -> p2 = $p + 30; if($this -> p2 >= $this -> numAlumnoSinEsp) $this -> p2 = $p;
			echo $this -> render_partial("alumnosSinEspAjax");
		} // function alumnosSinEspAjax($p)
		
		function asignandoEsp(){
			//$this -> valida();
			
			$this -> set_response("view");
			
			$logcambiosespecialidades = new Logcambiosespecialidades();
			$alumnos = new Alumnos();
			$kardex_ing = new KardexIng();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			
			unset($this -> alumnos);
			unset($this -> areadeformacion);
			unset($this -> carrera);
			unset($this -> areadeformacionactual);
			
			$registro = $this -> post("registro");
			
			$this -> alumno = $alumnos -> find_first("miReg=".$registro);
			
			$i = 0;
			foreach( $areadeformacion -> find("carrera_id = ".$this -> alumno -> carrera_id) as $area ){
				// Mostrar las diferentes areas de formación disponibles de su carrera.
				$this -> areadeformacion[$i] = $area;
				$i++;
			}
			$this -> carrera = $carrera -> find_first("id = ".$this -> alumno -> carrera_id);
			$i = 0;
			foreach( $kardex_ing -> find_all_by_sql("
					select * from kardex_ing k, materia m
					where k.registro = ".$this -> alumno -> miReg."
					and k.clavemat = m.clave
					and m.semestre >= 4
					and m.carrera_id = ".$this -> alumno -> carrera_id) as $kardex ){
				// Mostrar materias que tenga en kardex arriba de 4 semestre...
				$this -> kardex[$i] = $kardex;
				$i++;
			}
			echo $this -> render_partial("asignandoEsp");
		} // function asignandoEsp()
		
		function asignarEsp(){
			//$this -> valida();
			
			$logcambiosespecialidades = new Logcambiosespecialidades();
			$alumnos = new Alumnos();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			
			unset($this -> registro);
			unset($this -> areadeformacion_id);
			
			$registro = $this -> post("registro");
			$areadeformacion_id = $this -> post("areadeformacion_id");
			
			$alumno = $alumnos -> find_first("miReg=".$registro);
			$this -> alumno = $alumno;
			
			if( $alumno -> correo == "" || $alumno -> correo == null )
				$alumno -> correo = "0";
			if( $alumno -> stSit == "" || $alumno -> stSit == null )
				$alumno -> stSit = "NI";
			if( $alumno -> situacion == "" || $alumno -> situacion == null )
				$alumno -> situacion = "-";
			if( $alumno -> enSexo == "" || $alumno -> enSexo == null )
				$alumno -> enSexo = "H";
			// Guardando la especialidad del alumno.
			$alumno -> areadeformacion_id = $areadeformacion_id;
			$alumno -> update();
			
			$logcambiosespecialidades -> nomina = Session::get_data("registro");
			$logcambiosespecialidades -> registro = $registro;
			$logcambiosespecialidades -> carrera_id = $alumno -> carrera_id;
			$logcambiosespecialidades -> areadeformacion_id = $areadeformacion_id;
			$year = date ("Y"); $month = date ("m"); $day = date ("d");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$logcambiosespecialidades -> fecha = mktime($hour, $minute, $second, $month, $day, $year);
			$logcambiosespecialidades -> create();
			
			$this -> areadeformacion = $areadeformacion -> find_first("carrera_id = ".$this -> alumno -> carrera_id);
			$this -> carrera = $carrera -> find_first("id = ".$this -> alumno -> carrera_id);
			
		} // function asignarEsp($registro, $areadeformacion_id)
		
		function buscarAlumno(){
			//$this -> valida();
			
			$this -> set_response("view");
			
			$alumnos = new Alumnos();
			$carrera = new Carrera();
			$areadeformacion = new Areadeformacion();
			$logcambiosespecialidades = new Logcambiosespecialidades();
			$maestros = new Maestros();
			
			$buscarregistro = $this -> post("buscarregistro");
			
			if( $buscarregistro == null || $buscarregistro == "" ){
				$this -> mensaje = "Favor de ingresar un registro v&aacute;lido";
				echo $this -> render_partial("noIngresoRegistro");
				return;
			}
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> areadeformacion);
			unset($this -> areasdeformacion);
			unset($this -> logcambiosespecialidades);
			unset($this -> quienHizoMov);
			
			$registro = $this -> post("registro");
			
			if( !$this -> alumno = $alumnos -> find_first("miReg='".$buscarregistro."'") ){
				$this -> mensaje = "Favor de ingresar un registro v&aacute;lido";
				echo $this -> render_partial("noIngresoRegistro");
				return;
			}
			$this -> carrera = $carrera -> find_first("id = ".$this -> alumno -> carrera_id);
				
			if( $this -> alumno -> areadeformacion_id != 0 ){
				// El alumno ya tiene area de formación asignada
				$this -> areadeformacion = $areadeformacion -> find_first
						("idareadeformacion = ".$this -> alumno -> areadeformacion_id.
								" and carrera_id = ".$this -> alumno -> carrera_id);
				$i = 0;
				foreach( $logcambiosespecialidades -> find( "registro = ".
						$this -> alumno -> miReg." order by id" ) as $log ){
					$this -> logcambiosespecialidades[$i] = $log;
					if( $log -> nomina != "Calculo" ){
						$maestro = $maestros -> find_first("nomina = '".$log -> nomina."'");
						$this -> quienHizoMov[$i] = $maestro -> nombre;
					}
					else
						$this -> quienHizoMov[$i] = $log -> nomina;
					
					$this -> areasdeformacion[$i] = $areadeformacion -> find_first
							("idareadeformacion = ".$log -> areadeformacion_id.
									" and carrera_id = ".$log -> carrera_id);
					$this -> carreras[$i] = $carrera -> find_first("id = ".$log -> carrera_id);
					$i++;
				}
			}
			echo $this -> render_partial("buscarAlumno");
		} // function buscarAlumno()
		
		function valida(){
			if(Session::get_data('coordinador')!="OK")
				$this->redirect('general/inicio');
		} // function valida()
	}
?>