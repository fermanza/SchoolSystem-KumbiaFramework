<?php
			
	class ScriptsController extends ApplicationController {
	
		function kardex($registro){
			$this -> set_response("view");
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
			
			$id = $registro;
			$periodo = "32008";
		
			$materias = new Materiasing();
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
			
			$kardexes = new KardexIng();
			$primera = $kardexes -> find_first("registro=".$id." ORDER BY id ASC");
			$this -> pinicial = $primera -> periodo;
			
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
					$this -> materias[$i][$j] = $this -> obtenerMateria($resultado -> clavemat, $especialidad -> siNumEsp);
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
		
		function kardexexcel($registro){
			$this -> set_response("view");
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
			
			$id = $registro;
			$periodo = "32008";
		
			$materias = new Materiasing();
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
			
			$kardexes = new KardexIng();
			$primera = $kardexes -> find_first("registro=".$id." ORDER BY id ASC");
			$this -> pinicial = $primera -> periodo;
			
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
					$this -> materias[$i][$j] = $this -> obtenerMateria($resultado -> clavemat, $especialidad -> siNumEsp);
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
			
		function alumnospreseleccion($m){
		
			$this -> set_response("view");
		
			$preseleccion = new Preseleccionalumno();
			$materias = new Materia();
			
			$materia = $materias -> find_first("clave='".$m."'");
			
			echo $materia -> nombre." (".$materia -> clave.")";
			
			$preselecciones = $preseleccion -> find("");
		}
		
		function preseleccion($periodo=12008,$limite=0){
		
			$this -> set_response("view");
			
			$preseleccion = new Preseleccionalumno();
			$materias = new Materia();
			
			$preselecciones = $preseleccion -> distinct("registro","ORDER BY clavemateria");
			
			$x=0;
			foreach($preselecciones as $p){
				$x++;
			}
			
			$preselecciones = $preseleccion -> distinct("clavemateria","ORDER BY clavemateria");
			$m = "";
			
			?>
			<center>
			<table border="1" width="700">
			<tr>
				<th class="fondonaranja">
					Clave
				</th>
				<th class="fondonaranja">
					Nombre de la materia
				</th>
				<th class="fondonaranja">
					Preselección
				</th>
				<th class="fondonaranja">
					&nbsp;
				</th>
			</tr>

			<?php
			foreach($preselecciones as $p){
				$xxx++;
				$bg = 'bgcolor="#FFFFFF"';
				if($xxx%2==0){
					$bg = 'bgcolor="#DDDDDD"';
				}
			
				if($m==$p){
					
				}
				else{
					$m = $p;
					$materia = $materias -> find_first("clave='".$m."'");
					$total = $preseleccion -> count("clavemateria='".$m."' AND periodo=".$periodo);
				?>
				<tr <?= $bg; ?>>
					<th width="100">
						<?= $p ?>
					</th>
					<th width="400">
						<?= $materia -> nombre ?>
					</th>
					<th width="100">
						<?= $total ?>
					</th>
					<th width="100">
						<a href="<?= KUMBIA_PATH ?>scripts/alumnospreseleccion/<?= $materia -> clave ?>">VER ALUMNOS</a>
					</th>
				</tr>
				<?php
					
				}
				
			}
			?>
			</table>
			<br><br>
			Alumnos que realizaron su preselección: <b><?= $x; ?></b><br><br>
			<?php
		}
	}
?>