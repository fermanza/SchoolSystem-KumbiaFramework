<?php

    class Profesor1Controller extends ApplicationController {

        public $antesAnterior	= 32010;
        public $pasado			= 12011;
        public $actual			= 32011;
        public $proximo			= 12012;
        public $actual2			= 32012;

        function index(){
			$this -> redirect( "profesor/informacion" );
        }
		
		function actaTodos(){
			//define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
			//require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			$curso = "TCB5078";
			
			unset($this -> excel);
			unset($this -> alumnado);
			unset($this -> registro);
			unset($this -> nombre);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> situacion);
			unset($this -> especialidad);
			unset($this -> profesor);
			unset($this -> periodo);
			unset($this -> nomina);
			unset($this -> parcial);

			$id = "2052";
			$periodo = $this -> actual;

			$maestros = new Maestros();
			$maestro = $maestros -> find_first("nomina=".$id);

			$xcursos = new Xccursos();
			$materias = new Materia();
			$calificaciones = new Xalumnocursos();
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
			$materia = $materias -> find_first("clave='".$xcurso -> materia."'");
			
			if( substr($periodo, 0, 1) == 1)
				$periodo2 = "FEB - JUN, ";
			else
				$periodo2 = "AGO - DIC, ";
			
			$periodo2 .= substr($periodo,1,4);

			$aprobados = 0;
			$reprobados = 0;
			$n = 0;

			foreach($calificaciones -> find
					("curso='".$curso."' ORDER BY registro") as $calificacion){
				$n++;

				$faltas1 = $calificacion -> faltas1;
				$faltas2 = $calificacion -> faltas2;
				$faltas3 = $calificacion -> faltas3;

				$faltas = $faltas1 + $faltas2 + $faltas3;

				$cal1 = $calificacion -> calificacion1;
				$cal2 = $calificacion -> calificacion2;
				$cal3 = $calificacion -> calificacion3;

				if( $cal1 <= 100 && $cal2 <= 100 && $cal3 <= 100 ){
					$cal = ( ( $cal1 + $cal2 + $cal3 ) / 3 );
				}
				else{
					$cal11 = $cal1;
					$cal22 = $cal2;
					$cal33 = $cal3;

					if( $cal1 == 999 || $cal1 == 300 )
						$cal11 = 0;
					if( $cal2 == 999 || $cal2 == 300 )
						$cal22 = 0;
					if( $cal3 == 999 || $cal3 == 300 )
						$cal33 = 0;

					$cal = ( ( $cal11 + $cal22 + $cal33 ) / 3 );

					if( $cal1 == 999 && $cal2 == 999 && $cal3 == 999 ){
						$cal = "NP";
						$nps++;
					}
				}
				if( $cal != 100 && $cal != "NP" ){
					if( $cal < 70 ){
						$cal = (int)$cal = substr( $cal, 0, 2 );
					}
					else{
						if( strlen( $cal ) > 2 ){
							if( substr( $cal, 3, 1 ) > 5 )
								$cal = $cal + 1;
						}
						if( $cal < 100 )
							$cal = (int)$cal = substr( $cal, 0, 2 );
						else
							$cal = (int)$cal = substr( $cal, 0, 3 );
					}
				}
				echo "registro: ".$calificacion -> registro." calificacion: ".$cal."<br />";
			}
		}
		
    }
?>