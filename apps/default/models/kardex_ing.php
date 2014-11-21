<?php		
	class KardexIng extends ActiveRecord{
	
	function get_kardex_by_periodo_join_with_materia($alumno, $periodo){
		if($alumno->carrera_id==4 || $alumno->carrera_id==5){
			$second_id = $this->get_second_areadeformacion_id($alumno);
		}
		$kardexArray = array();
		foreach( $this -> find_all_by_sql("
				select *
				from kardex_ing k
				join materia m on k.clavemat= m.clave
				where k.periodo = '".$periodo."'
				and k.registro = '".$alumno -> miReg."'
				and m.carrera_id = ".$alumno -> carrera_id."
				and (m.serie = '".$alumno -> areadeformacion_id."'
				or m.serie = '-'
				or m.serie = '".$second_id."')
				and k.activo = 1") as $king ){
			array_push($kardexArray, $king);
		}
		return $kardexArray;
	} // function get_kardex_by_periodo_join_with_materia($alumno, $periodo)
	
	function get_ncreditos($alumno){
		if($alumno->carrera_id==4 || $alumno->carrera_id==5){
			$second_id = $this->get_second_areadeformacion_id($alumno);
		}
		
		foreach( $this -> find_all_by_sql("
				select sum(m.creditos) as sumadecreditos
				from kardex_ing k
				inner join materia m on k.clavemat = m.clave
				and k.registro = ".$alumno -> miReg."
				and m.carrera_id = ".$alumno -> carrera_id."
				and (m.serie = '".$alumno -> areadeformacion_id."'
				or m.serie = '-'
				or m.serie = '".$second_id."')
				and activo = 1") as $king ){
			$ncreditos = $king -> sumadecreditos;
		}
		return $ncreditos;
	} // function get_ncreditos($alumno)
		
	//obtiene cantidad de creditos
	function get_creditos($registro, $carrera_id, $areadeformacion){
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> get_relevant_info_from_student($registro);
			if($alumno->carrera_id==4 || $alumno->carrera_id==5){
				$second_id = $this->get_second_areadeformacion_id($alumno);
			}
			foreach( $this -> find_all_by_sql("
					select sum(m.creditos) as sumadecreditos
					from kardex_ing k
					inner join materia m on k.clavemat = m.clave
					and k.registro = ".$registro."
					and m.carrera_id = ".$carrera_id."
					and (m.serie = '".$areadeformacion."'
					or m.serie = '-'
					or m.serie = '".$second_id."')
					and activo = 1") as $king ){
				$ncreditos = $king -> sumadecreditos;
				
			}
			return $ncreditos;
			//var_dump($ncreditos); die();
		} // function get_creditos($alumno)
		
		function get_all_kardex_from_student($registro){
			
			foreach( $this -> find_all_by_sql(
					"select registro, clavemat, periodo, tipo_de_ex, promedio
					from kardex_ing
					where registro = ".$registro) as $kardexx ){
				$kardex = $kardexx;
			}
			return $kardex;
		} // function get_all_kardex_from_student($registro)
		
		function get_all_kardex_from_student_only_clave($registro){
			$materiasEnKardex = array();
			foreach( $this -> find_all_by_sql(
					"select clavemat
					from kardex_ing
					where registro = ".$registro) as $kardexx ){
				array_push($materiasEnKardex, $kardexx -> clavemat);
			}
			return $materiasEnKardex;
		} // function get_all_kardex_from_student_only_clave($registro)
		
		function sumarCreditosDelKardex($alumno){
			if($alumno->carrera_id==4 || $alumno->carrera_id==5){
				$second_id = $this->get_second_areadeformacion_id($alumno);
			}
			// Checar cuántos créditos tiene en el kardex
			foreach( $this -> find_all_by_sql
					( "Select sum(creditos) as creditos
					from kardex_ing king, materia m
					where king.registro = ".$alumno -> miReg."
					and king.clavemat = m.clave
					and m.carrera_id = '".$alumno -> carrera_id."'
					and (m.serie = '".$alumno -> areadeformacion_id."'
					or m.serie = '-'
					or m.serie = '".$second_id."')") as $k ){
				$creditos = $k -> creditos;
			}
			return $creditos;
		} // function sumarCreditosDelKardex($alumno)
		
		function get_average_from_kardex($registro){
			// Checar cuántos créditos tiene en el kardex
			$Alumnos = new Alumnos();
			$alumno = $Alumnos -> get_relevant_info_from_student($registro);
			
			if($alumno->carrera_id==4 || $alumno->carrera_id==5){
				$second_id = $this->get_second_areadeformacion_id($alumno);
			}
			foreach( $this -> find_all_by_sql( 
						"select avg(promedio) as promedio
						from kardex_ing k
						inner join materia m on k.clavemat = m.clave
						where k.registro = '".$registro."'
						and k.activo = 1
						and m.carrera_id = '".$alumno->carrera_id."'
						and (m.serie = '".$alumno->areadeformacion_id."'
						or m.serie = '-'
						or m.serie = '".$second_id."');") as $k ){
				$promedio = $k -> promedio;
			}
			$promedio = round($promedio * 100)/100;
			//$promedio .=  '.00';
			//return substr($promedio, 0, 6);
			return number_format($promedio, 2);
		} // function get_average_from_kardex($registro)
		
		function get_second_areadeformacion_id($alumno){
			
			$array_cuenta_serie = array();
			$array_cuenta = array();
			foreach( $this->find_all_by_sql(
					"select count(*) cuenta, serie
					from kardex_ing k
					join materia m
					on k.clavemat = m.clave
					and m.carrera_id = '".$alumno->carrera_id."'
					and serie != '-'
					and serie != '".$alumno->areadeformacion_id."'
					where k.registro = '".$alumno->miReg."'
					group by serie" ) as $k ) {
				
				array_push($array_cuenta_serie, $k);
				array_push($array_cuenta, $k->cuenta);
			}
			$max_value = max($array_cuenta);
			foreach( $array_cuenta_serie as $cuenta_serie ){
				if($cuenta_serie->cuenta == $max_value){
					return $cuenta_serie->serie;
				}
			}
			return '-';
		} // function get_second_areadeformacion_id($alumno)
		
		function incrementaPeriodoKardex($periodo){
			$tmp = $periodo;
			$elPeriodo = substr($periodo,0,1);
			// 42009 SE CURSA EN ENERO DEL 2010
			
			// 12010 FEB - JUN DEL 2010
			// 22010 JULIO DEL 2010
			// 32010 AGO - DIC DEL 2010
			// 42010 SE CURSA EN ENERO DEL 2011
			
			if( $elPeriodo == 1 ){
				$periodo = "2".substr($periodo,1,4);
			}
			elseif( $elPeriodo == 2 ){
				$periodo = "3".substr($periodo,1,4);
			}
			elseif( $elPeriodo == 3 ){
				$periodo = "4".substr($periodo,1,4);
			}
			else{
				$periodo = "1".(substr($periodo,1,4) + 1);
			}
			
			return $periodo;
		} // incrementaPeriodoKardex($periodo)
		
		function get_periodosKardex_editable($registro){
			$periodos = array();
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select periodo
						from kardex_ing
						where registro = '".$registro."'
						group by periodo" ) as $k ){
				array_push($periodos, $k -> periodo);
			}
			return $periodos;
		} // get_periodosKardex_editable($registro)
		
		function get_periodosKardex($registro){
			$periodos = array();
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select periodo
						from kardex_ing
						where registro = '".$registro."'
						and activo = '1'
						group by periodo" ) as $k ){
				array_push($periodos, $k -> periodo);
			}
			return $periodos;
		} // get_periodosKardex($registro)
		
		function get_periodoMayor($periodos){
			// Checar los periodos que tiene en Kardex, y regresar el periodo
			//mas reciente.
			$periodoMayor = 0;
			for( $i = 0; $i < count($periodos); $i++ ){
				$periodos[$i] = substr($periodos[$i],1,4).substr($periodos[$i],0,1);
			}
			foreach($periodos as $periodo){
				if($periodo > $periodoMayor)
					$periodoMayor = $periodo;
			}
			$periodoMayor = substr($periodoMayor,4,1).substr($periodoMayor,0,4);
			
			$periodoMayor = $this -> incrementaPeriodoKardex($periodoMayor);
			return $periodoMayor;
		} // get_periodoMayor($periodos)
		
		function get_periodoMenor($periodos){
			// Checar los periodos que tiene en Kardex, y regresar el periodo
			//mas antiguo de su kardex.
			$periodoMenor = 0;
			for( $i = 0; $i < count($periodos); $i++ ){
				$periodos[$i] = substr($periodos[$i],1,4).substr($periodos[$i],0,1);
			}
			foreach($periodos as $periodo){
				if( $periodoMenor == 0 )
					$periodoMenor = $periodo;
				if($periodo < $periodoMenor)
					$periodoMenor = $periodo;
			}
			$periodoMenor = substr($periodoMenor,4,1).substr($periodoMenor,0,4);
			
			return $periodoMenor;
		} // get_periodoMenor($periodos)
		
		function get_kardex_from_student_periodo($periodo, $registro){
			$kardexIng = array();
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select *
						from kardex_ing
						where registro = '".$registro."'
						and periodo = ".$periodo."
						and activo = 1" ) as $k ){
				array_push($kardexIng, $k);
			}
			// Si el periodo es 4, o sea 42011, ó 42010, sumarle un 1
			//para que en el kardex que se muestra, se vea como: Enero 2012, Enero 2011
			$i=0;
			foreach($kardexIng as $k){
				if( substr($k->periodo, 0, 1) == 4 )
					$kardexIng[$i]->periodo += 1;
				$i++;
			}
			return $kardexIng;
		} // get_kardex_from_student_periodo($periodo, $registro)
		
		function get_kardex_from_student_periodo_editable($periodo, $registro){
			$kardexIng = array();
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select *
						from kardex_ing
						where registro = '".$registro."'
						and periodo = ".$periodo ) as $k ){
				array_push($kardexIng, $k);
			}
			// Si el periodo es 4, o sea 42011, ó 42010, sumarle un 1
			//para que en el kardex que se muestra, se vea como: Enero 2012, Enero 2011
			$i=0;
			foreach($kardexIng as $k){
				if( substr($k->periodo, 0, 1) == 4 )
					$kardexIng[$i]->periodo += 1;
				$i++;
			}
			return $kardexIng;
		} // function get_kardex_from_student_periodo_editable($periodo, $registro)
		
		function get_count_kardex_from_student_editable($registro){
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select count(*) cuenta
						from kardex_ing
						where registro = '".$registro."'") as $k ){
				$cuantasMaterias = $k -> cuenta;
			}
			return $cuantasMaterias;
		} // function get_count_kardex_from_student_editable($registro)
		
		function get_count_kardex_from_student($registro){
			// Checar los periodos que tiene en Kardex
			foreach( $this -> find_all_by_sql( 
						"select count(*) cuenta
						from kardex_ing
						where registro = '".$registro."'
						and activo = 1") as $k ){
				$cuantasMaterias = $k -> cuenta;
			}
			return $cuantasMaterias;
		} // function get_count_kardex_from_student($registro)
		
		function get_materia_info($clavemat, $carrera_id, $areadeformacion_id){
			// Obtiene información de una materia
			foreach( $this -> find_all_by_sql( 
						"select *
						from materia
						where clave = '".$clavemat."'
						and carrera_id = '".$carrera_id."'
						and (serie = '".$areadeformacion_id."'
						or serie = '-')") as $k ){
				$materiaInfo = $k;
			}
			if(isset($materiaInfo)){
				return $materiaInfo;
			}
		} // function get_materia_info($clavemat, $carrera_id, $areadeformacion_id)
		
		function get_completeKardex($registro, $editable){
			//if(!$this -> validarEncuesta()){
				//$this -> redirect("alumno/encuestas");
				//return;
			//}
			
			//$registro = Session::get_data('registro');
			$usuarios = new Usuarios();
			$usuario = $usuarios -> find_first("registro='".$registro."'");
			
			//if($xalumno -> nombre == ""){
			//	$this -> redirect("alumno/actualizacion");
			//}
			
			if($usuario -> clave == $registro){
				$this->redirect('alumnos/index');
			}
			$periodo = $this -> actual;
			//$periodo = 12011;
			
			// Validar si ya hizo preselecci&oacute;n
			//$this -> validarPreseleccion();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> periodo);
			unset($this -> registro);
			unset($this -> alumno);
			unset($this -> avancePeriodos);
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
			unset($this -> materias);
			
			$Alumnos	= new Alumnos();
			$Materia	= new Materia();
			
			// Se trae informacióm importante del alumno.
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			
			if( substr($periodo,0,1) == 1)
				$this -> periodo = "FEB - JUN, ";
			else
				$this -> periodo = "AGO - DIC, ";
			$this -> periodo .= substr($periodo,1,4);
			
			$cuantasMaterias = 0;
			if( $editable == 1  ){
				$cuantasMaterias = $this -> get_count_kardex_from_student_editable($registro);
				$periodos = $this -> get_periodosKardex_editable($registro);
			}
			else{
				$cuantasMaterias = $this -> get_count_kardex_from_student($registro);
				$periodos = $this -> get_periodosKardex($registro);
			}
			$periodoMaximo = $this -> get_periodoMayor($periodos);
			$periodoMenor = $this -> get_periodoMenor($periodos);
			
			$avancePeriodos = Array();
			$periodosBuenos = Array();
			$noencontro = 0;
			if( $cuantasMaterias > 0 ){
				$avancePeriodos[0] = $periodoMenor;
				$i = 0;
				$promedio = 0;
				do{
					if( $editable == 1  )
						$kardexIng = $this -> get_kardex_from_student_periodo_editable($avancePeriodos[$i], $registro);
					else
						$kardexIng = $this -> get_kardex_from_student_periodo($avancePeriodos[$i], $registro);
					//echo "avancePeriodos[$i]: ".$avancePeriodos[$i]." registro: ".$registro." clavemat: ".$kardexIng[0] -> clavemat;die;
					$j = 0;
					$aux = false;
					foreach( $kardexIng as $kardex ){
						switch($kardex -> tipo_de_ex){
							case 'D': $kardex -> tipo_de_ex = "ORDINARIO (D)"; break;
							case 'E': $kardex -> tipo_de_ex = "EXTRAORDINARIO (E)"; break;
							case 'T': $kardex -> tipo_de_ex = "TITULO SUFICIENCIA (T)"; break;
							case 'R': $kardex -> tipo_de_ex = "REGULARIZACION (R)"; break;
							case 'A': $kardex -> tipo_= "ACREDITACION (A)"; break;
							case 'V': $kardex -> tipo_de_ex = "REVALIDACION (V)"; break;
							case "EQ": $kardex -> tipo_de_ex = "EQUIVALENCIA (EQ)"; break;
							case "EG": $kardex -> tipo_de_ex = "EXAMEN GLOBAL (EG)"; break;
							case "NIV": $kardex -> tipo_de_ex = "NIVELACI&Oacute;N (NIV)"; break;
							case "ACR": $kardex -> tipo_de_ex = "ACREDITACI&Oacute;N (ACR)"; break;
							case "DP": $kardex -> tipo_de_ex = "DERECHO DE PASANTE (DP)"; break;
							case "CO": $kardex -> tipo_de_ex = "CONVALIDACI&Oacute;N (CO)"; break;
							//case "CON": $kardex -> tipo_de_ex = "CONVALIDACI&Oacute;N (CO)"; break;
							case "I": $kardex -> tipo_de_ex = "INTERSEMESTRAL (I)"; break;
						}
						$materiaInfo = $this -> get_materia_info
								($kardex -> clavemat, $this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
						if( !isset($materiaInfo) ){
							$noencontro++;
							continue;
						}
						$kardex -> nombre = $materiaInfo -> nombre;
						$kardex -> creditos = $materiaInfo -> creditos;
						$kardex -> periodosBuenos = $kardex -> periodo;
						
						$kardexReturn[count($periodosBuenos)][$j] = $kardex;
						$kardexReturn[0][0] -> sumaCreditos += $kardex -> creditos;
						$promedio += $kardex -> promedio;
						$j++;
						$aux = true;
					}
				if( $aux )
					array_push($periodosBuenos, $avancePeriodos[$i]);
				$i++;
				$avancePeriodos[$i] = $this -> incrementaPeriodoKardex($avancePeriodos[$i-1]);
				}while($avancePeriodos[$i] != $periodoMaximo);
			}
			$promedio = $promedio / ($cuantasMaterias-$noencontro);
			$promedio = round($promedio * 100)/100;
			$kardexReturn[0][0] -> promedioTotal = $promedio;
			
			return $kardexReturn;
		} // function get_completeKardex($registro, $editable)
		
		function get_materia_info_kardex($kardexID){
			// Obtiene información de una materia
			foreach( $this -> find_all_by_sql( 
						"select *
						from kardex_ing
						where id = ".$kardexID) as $k ){
				$materiaInfo = $k;
			}
			return $materiaInfo;
		} // function get_materia_info_kardex($kardexID)
		
		function corregir_kardex($kardexID, $periodo, $tipo_de_ex, $promedio){
			// Obtiene información de una materia
			foreach( $this -> find_all_by_sql( 
					"update kardex_ing
					set periodo = ".$periodo.",
					tipo_de_ex = '".$tipo_de_ex."',
					promedio = ".$promedio."
					where id = ".$kardexID) as $k ){
			}
			return 1;
		} // function corregir_kardex($kardexID, $periodo, $tipo_de_ex, $promedio)
		
		function get_materias_para_agregar($alumno){
			$materiasEnKardex = Array();
			foreach($this -> find_all_by_sql("
					select clavemat
					from kardex_ing
					where registro = ".$alumno -> miReg."
					and activo = 1")as $k){
				array_push($materiasEnKardex, $k -> clavemat);
			}
			$materiasQuePuedeAgregar = Array();
			foreach( $this -> find_all_by_sql("
				select * From materia
				where carrera_id  = ".$alumno -> carrera_id."
				and (serie = '".$alumno -> areadeformacion_id."'
				or serie = '-')") as $m ){
				if( !in_array($m -> clave, $materiasEnKardex)  )
					array_push($materiasQuePuedeAgregar, $m);
			}
			return $materiasQuePuedeAgregar;
		} // function get_materias_para_agregar($alumno)
		
		function mktime(){
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			return mktime( $hour, $minute, $second, $month, $day, $year );
		} // function mktime()
		
		function borradoLogico($kardexID){
			$kardexIng = $this -> find_first("id = ".$kardexID);
			
			$kardexIng -> activo = '0';
			if( $kardexIng -> save() ) 
				$exito = 1;
			else
				$exito = 0;
			
			return $exito;
		} // function borradoLogico($kardexID)
		
		function borradoFisico($kardexID){
			$kardexIng = $this -> find_first("id = ".$kardexID);
			
			if( $kardexIng -> delete() )
				$exito = 1;
			else
				$exito = 0;
				
			return $exito;
		} // function borradoFisico($kardexID)
		
		function get_if_is_in_kardex($clavemat, $registro){
			if( $this -> find_first("clavemat = '".$clavemat."' and registro = '".$registro."'") )
				return true; // Regresa true, si existe en el kardex del alumno
			
			return false; // Regresa false, si no existe en el kardex del alumno.
			
		} // function get_if_is_in_kardex($clavemat, $registro)
	}
?>