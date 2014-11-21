<?php

    class CalculoController extends ApplicationController {

        public $anterior	= 12011;
		
		public $antesAnterior	= 12011;
        public $pasado			= 32011;
        public $actual			= 12012;
        public $proximo			= 32012;
        public $actual2			= 12012;

        function salir(){
            Session::unset_data('registro');
            Session::unset_data('tipousuario');
            Session::unset_data('TMPregistro');
            Session::unset_data('TMPtipousuario');
            $this->redirect('general/inicio');
        }

        function index (){
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
        }

        function getIP() {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isset($_SERVER['HTTP_VIA'])) {
                    $ip = $_SERVER['HTTP_VIA'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR'])) {
                    $ip = $_SERVER['REMOTE_ADDR'];
            }
            else {
                    $ip = "DESCONOCIDA";
            }

            return $ip;
        }
		
        function ingresarCurso(){
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
        }
		
        function corregirEyT(){
            $xalumnocursos = new Xalumnocursos();
            $xacursos = new Xalumnocursos();
            $temp = new Temporal();
            // Se necesita crear la tabla Temporal según sus necesidades antes de usar esta función
            // Abajo se encuentra el sql para la creación como se necesita dicha tabla ...
            // create table temporal (
            // id int auto_increment,
            // campo int,
            // Primary Key (id) );

            /*
            foreach ($xalumnocursos -> find_all_by_sql ("
                    select c32008.situacion as situacion32008, c12009.registro as registro12009,
                    c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009,
                    c12009.calificacion as calificacion12009
                            from (
                                    select xa.registro, xc.materia, xa.situacion, xa.id
                                    from xalumnocursos as xa, xcursos as xc
                                    where xa.curso = xc.clavecurso
                                    and xa.periodo = 32008
                            ) c32008,
                            (
                            select xa.registro, xc.materia, xa.situacion, xa.id, xa.calificacion
                                    from xalumnocursos as xa, xcursos as xc
                                    where xa.curso = xc.clavecurso
                                    and xa.periodo = 12009
                            ) c12009
                    where c32008.materia = c12009.materia
                    and c32008.registro = c12009.registro
            ")as $xalumcur){
            */
            foreach ($xalumnocursos -> find_all_by_sql ("
                    select c32008.situacion as situacion32008, c12009.registro as registro12009,
                    c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009,
                    c12009.calificacion as calificacion12009
                            from (
                                    select xa.registro, xc.materia, xa.situacion, xa.id
                                    from xalumnocursos as xa, xcursos as xc
                                    where xa.curso = xc.clavecurso
                                    and xa.periodo = 12009
                            ) c32008,
                            (
                            select xa.registro, xc.materia, xa.situacion, xa.id, xa.calificacion
                                    from xalumnocursos as xa, xcursos as xc
                                    where xa.curso = xc.clavecurso
                                    and xa.periodo = 32009
                            ) c12009
                    where c32008.materia = c12009.materia
                    and c32008.registro = c12009.registro
            ")as $xalumcur){
                    $variable = '0';
                    $variable = $xalumcur -> id12009;
                    $temp -> id = 'default';
                    $temp -> campo = $variable;

                    if ( $xalumcur -> situacion12009 == "-" ){

                            if( $xalumcur -> calificacion12009 <= 100 ){
                                    if( $xalumcur -> calificacion12009 >= 70 ){
                                            foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                                    $xalcur -> situacion = "ORDINARIO";
                                                    $xalcur -> save();
                                                    continue;
                                            }
                                    }
                                    else{
                                            foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                                    $xalcur -> situacion = "EXTRAORDINARIO";
                                                    $xalcur -> save();
                                                    continue;
                                            }
                                    }
                            }
                            else{
                                    continue;
                            }
                    }

                    if ( $xalumcur -> situacion32008 == "EXTRAORDINARIO" ){

                            $temp -> campo = $xalumcur -> id12009;
                            $temp -> create ();

                            if ( $xalumcur -> calificacion12009 >= 70 ){
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "REGULARIZACION";
                                            $xalcur -> save();
                                            continue;
                                    }
                            }
                            else{
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "TITULO DE SUFICIENCIA";
                                            $xalcur -> save();
                                    }
                            }
                    }

                    if ( $xalumcur -> situacion32008 == "EXTRAORDINARIO POR FALTAS" ){

                            $temp -> campo = $xalumcur -> id12009;
                            $temp -> create ();

                            if ( $xalumcur -> calificacion12009 < 70 ){
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "TITULO DE SUFICIENCIA ";
                                            $xalcur -> save();
                                            continue;
                                    }
                            }
                            else{
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "REGULARIZACION DIRECTA";
                                            $xalcur -> save();
                                    }
                            }
                    }

                    if ( $xalumcur -> situacion32008 == "TITULO DE SUFICIENCIA" ||
                                    $xalumcur -> situacion32008 == "BAJA DEFINITIVA" ){

                            $temp -> campo = $xalumcur -> id12009;
                            $temp -> create ();

                            if ( $xalumcur -> calificacion12009 >= 70 ){
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "REGULARIZACION";
                                            $xalcur -> save();
                                            continue;
                                    }
                            }
                            else{
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "PROCESO";
                                            $xalcur -> save();
                                    }
                            }
                    }

                    if ( $xalumcur -> situacion32008 == "REGULARIZACION DIRECTA" ||
                                    $xalumcur -> situacion32008 == "REGULARIZACION" ){

                            $temp -> campo = $xalumcur -> id12009;
                            $temp -> create ();

                            if ( $xalumcur -> calificacion12009 >= 70 ){
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "REGULARIZACION";
                                            $xalcur -> save();
                                    }
                            }
                            else{
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id12009) as $xalcur ){
                                            $xalcur -> situacion = "TITULO DE SUFICIENCIA";
                                            $xalcur -> save();
                                    }
                            }
                    }

                    echo $xalumcur -> situacion12009." ";
                    echo "id: ".$xalumcur -> id12009." ";
                    echo $xalumcur -> materia12009."<br /><br /><br />";
            }
?>
            <a href = "<?= KUMBIA_PATH ?>calculo/pasarAKardexIng/">pasarAKardexIng</a>
<?
        } // function corregirEyT()

        function pasarAKardexIng(){
			$xalumnocursos = new Xalumnocursos();
			$xacursos = new Xalumnocursos();
			$xcursos = new Xcursos();
			// Se necesita crear la tabla Temporal según sus necesidades antes de usar esta función
			// Abajo se encuentra el sql para la creación como se necesita dicha tabla ...
			// create table temporal (
			// id int auto_increment,
			// campo int,
			// Primary Key (id) );

			$temp = new Temporal();
			$kardexIng = new KardexIng();
			/*
			select c32008.registro as registro32008, c32008.materia as materia32008,
				c32008.situacion as situacion32008, c32008.id as id12008, c12009.registro as registro12009,
				c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009
			*/
			foreach ( $temp -> find_all_by_sql ("Select * from temporal")as $tmp ){

				foreach ( $xalumnocursos -> find ("id= ".$tmp->campo) as $xalumcur ){
				/*
				registro  clavemat  nivel  periodo  tipo_de_ex  promedio  fecha_reg
					$xalumcur -> periodo;
					$xalumcur -> registro;
					$xalumcur -> curso;
					$xalumcur -> situacion;
					$xalumcur -> calificacion;
				*/
					foreach ( $xcursos -> find ("clavecurso= '".$xalumcur -> curso."'" )as $xcur ){

						foreach ( $kardexIng -> find ("registro= ".$xalumcur -> registro.
									" and clavemat= '".$xcur -> materia."' and periodo= ".$xalumcur -> periodo ) as $kardIng ){
							$kardIng -> promedio = $xalumcur -> calificacion;
							if ( $xalumcur -> situacion == "EXTRAORDINARIO" ||
											$xalumcur -> situacion == "EXTRAORDINARIO POR FALTAS" )
									$tipoDeEx = 'E';
							if ( $xalumcur -> situacion == "REGULARIZACION" ||
											$xalumcur -> situacion == "REGULARIZACION DIRECTA" )
									$tipoDeEx = 'R';
							if ( $xalumcur -> situacion == "TITULO DE SUFICIENCIA" ||
											$xalumcur -> situacion == "TITULO" )
									$tipoDeEx = 'T';
							if ( $xalumcur -> situacion == "PROCESO" )
									$tipoDeEx = 'T';

							$kardIng -> tipo_de_ex = $tipoDeEx;
							$kardIng -> save();
						}
					}
				}
			}

        } // function pasarAKardexIng()

        function pasarAKardexIngXExt(){ // (Y)

			$this -> validar_usuario_calculo();
			
			$xalumnocursos = new Xalumnocursos();
			$xacursos = new Xalumnocursos();
			$xcursos = new Xccursos();

			$Xextra = new Xextraordinarios();
			$kardexIng = new KardexIng();
			$materiasing = new Materiasing();

			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
			
			$counter1=0;
			$counter2=0;
			$extra=0;
			$curso = 0;
			$materia2 = 0;
			$cuales1[0] = 0;
			$cuales2[0] = '';
			$cuales3[0] = 0;
			$cuales4[0] = '';
			$cuales5[0] = 0;

			foreach ( $Xextra -> find_all_by_sql
					("Select * from xextraordinarios
					where periodo = ".$this -> actual."
					and estado = 'OK'
					and calificacion >=70
					and calificacion <=100")as $Xext ){
				$extra++;
				foreach ( $xcursos -> find ("clavecurso= '".$Xext -> clavecurso."'" )as $xcurso ){
					$curso++;
					$mating = $materiasing -> find_first ( "clavemat= '".$xcurso -> materia."'" );
					$materia2++;
					$materia++;
					if ( ($kardexIng -> find_first
						( "registro = ".$Xext -> registro.
						" and clavemat = '".$xcurso -> materia."'".
						" and periodo = ".$Xext -> periodo.
						" and tipo_de_ex = '".$Xext -> tipo."'" ) ) ){
						$counter1++;
						$cuales1[$counter1] = $Xext->registro;
						$cuales2[$counter1] = $xcurso->materia;
						$cuales3[$counter1] = $Xext -> periodo;
						$cuales4[$counter1] = $Xext -> tipo;
						$cuales5[$counter1] = $Xext -> calificacion;
					}
					else{
						$kardexIng -> registro = $Xext -> registro;
						$kardexIng -> clavemat = $xcurso -> materia;
						$kardexIng -> nivel = $mating -> nivel;
						$kardexIng -> periodo = $Xext -> periodo;
						$kardexIng -> tipo_de_ex = $Xext -> tipo;
						$kardexIng -> promedio = $Xext -> calificacion;
						$kardexIng -> fecha_reg = $date1;

						if ( $kardexIng -> nivel == '' || $kardexIng -> nivel == null
										|| $kardexIng -> nivel == 0 )
								$kardexIng -> nivel = '0';
						echo "<br />registro: ".$kardexIng -> registro;
						echo "<br />clavemat: ".$kardexIng -> clavemat;
						echo "<br />nivel: ".$kardexIng -> nivel;
						echo "<br />periodo: ".$kardexIng -> periodo;
						echo "<br />tipo_de_ex: ".$kardexIng -> tipo_de_ex;
						echo "<br />promedio: ".$kardexIng -> promedio;
						echo "<br />fecha_reg: ".$kardexIng -> fecha_reg;

						if ( $kardexIng -> create() ){
								$counter2++;
								echo "<br />Si se pudo grabar";
						}
						else
								echo "<br />No se pudo";
					}
				}
			}

			// Counter 1, me dice las materias que no se grabaron
			echo "<br /><br />counter1: ".$counter1;
			// Counter 2, me dice las materias que se grabaron
			echo "<br /><br />counter2: ".$counter2." materia: ".$materia." extra: ".
			$extra." curso: ".$curso." materia2: ".$materia2;
			echo "<br /><br />Cuales: ";
			for ( $i = 0; $i < count($cuales1); $i++ )
				echo "<br />1: ".$cuales1[$i]." 2: ".$cuales2[$i]." 3: ".$cuales3[$i]." 4: ".$cuales4[$i]
				." 5: ".$cuales5[$i];

        } // function pasarAKardexIngXExt()

        function pasarAKardexIngXTExt(){ // (Y)

			$this -> validar_usuario_calculo();
			
			$xalumnocursos = new Xtalumnocursos();
			$xacursos = new Xtalumnocursos();
			$xcursos = new Xtcursos();

			$Xextra = new Xtextraordinarios();
			$kardexIng = new KardexIng();
			$materiasing = new Materiasing();

			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
			
			$counter1=0;
			$counter2=0;
			$extra=0;
			$curso = 0;
			$materia2 = 0;
			$cuales1[0] = 0;
			$cuales2[0] = '';
			$cuales3[0] = 0;
			$cuales4[0] = '';
			$cuales5[0] = 0;

			foreach ( $Xextra -> find_all_by_sql
					("Select * from xtextraordinarios
					where periodo = ".$this -> actual."
					and estado = 'OK'
					and calificacion >=70
					and calificacion <=100;")as $Xext ){
				$extra++;
				foreach ( $xcursos -> find ("clavecurso= '".$Xext -> clavecurso."'" )as $xcurso ){
					$curso++;
					$materia = 0;
					$mating = $materiasing -> find_first ( "clavemat= '".$xcurso -> materia."'" );
					if ( $materia == 1 )
						break;
					$materia2++;
					$materia++;
					if ( ($kardexIng -> find_first
						( "registro = ".$Xext -> registro.
						" and clavemat = '".$xcurso -> materia."'".
						" and periodo = ".$Xext -> periodo.
						" and tipo_de_ex = '".$Xext -> tipo."'" ) ) ){
						$counter1++;
						$cuales1[$counter1] = $Xext->registro;
						$cuales2[$counter1] = $xcurso->materia;
						$cuales3[$counter1] = $Xext -> periodo;
						$cuales4[$counter1] = $Xext -> tipo;
						$cuales5[$counter1] = $Xext -> calificacion;
					}
					else{
						$kardexIng -> registro = $Xext -> registro;
						$kardexIng -> clavemat = $xcurso -> materia;
						$kardexIng -> nivel = $mating -> nivel;
						$kardexIng -> periodo = $Xext -> periodo;
						$kardexIng -> tipo_de_ex = $Xext -> tipo;
						$kardexIng -> promedio = $Xext -> calificacion;
						$kardexIng -> fecha_reg = $date1;

						if ( $kardexIng -> nivel == '' || $kardexIng -> nivel == null
										|| $kardexIng -> nivel == 0 )
								$kardexIng -> nivel = '0';
						echo "<br />registro: ".$kardexIng -> registro;
						echo "<br />clavemat: ".$kardexIng -> clavemat;
						echo "<br />nivel: ".$kardexIng -> nivel;
						echo "<br />periodo: ".$kardexIng -> periodo;
						echo "<br />tipo_de_ex: ".$kardexIng -> tipo_de_ex;
						echo "<br />promedio: ".$kardexIng -> promedio;
						echo "<br />fecha_reg: ".$kardexIng -> fecha_reg;

						if ( $kardexIng -> create() ){
								$counter2++;
								echo "<br />Si se pudo grabar";
						}
						else
								echo "<br />No se pudo";
					}
				}
			}

			// Counter 1, me dice las materias que no se grabaron
			echo "<br /><br />counter1: ".$counter1;
			// Counter 2, me dice las materias que se grabaron
			echo "<br /><br />counter2: ".$counter2." materia: ".$materia." extra: ".
			$extra." curso: ".$curso." materia2: ".$materia2;
			echo "<br /><br />Cuales: ";
			for ( $i = 0; $i < count($cuales1); $i++ )
				echo "<br />1: ".$cuales1[$i]." 2: ".$cuales2[$i]." 3: ".$cuales3[$i]." 4: ".$cuales4[$i]
				." 5: ".$cuales5[$i];

        } // function pasarAKardexIngXTExt()

        function corregirOrdinarios(){

            $xalumnocursos = new Xalumnocursos();
            $xacursos = new Xalumnocursos();
            $temp = new Temporal();
            // Se necesita crear la tabla Temporal según sus necesidades antes de usar esta función
            // Abajo se encuentra el sql para la creación como se necesita dicha tabla ...
            // create table temporal (
            // id int auto_increment,
            // campo int,
            // Primary Key (id) );

            foreach ($xalumnocursos -> find_all_by_sql ("
                            select xa.registro, xc.materia, xa.situacion, xa.id, xa.calificacion
                                    from xalumnocursos as xa, xcursos as xc
                                    where xa.curso = xc.clavecurso
                                    and xa.periodo = 12009 -- Cambiar por el periodo que uno quiera...
                                    and situacion = 'ORDINARIO'
                                    and calificacion < 70
            ")as $xalumcur){

            $variable = 0;
            $variable = $xalumcur -> id;
            $temp -> id = 'default';
            $temp -> campo = $variable;

                    if ( $xalumcur -> situacion == "ORDINARIO" ){

                            $temp -> campo = $xalumcur -> id;
                            $temp -> create ();

                            foreach ( $xacursos -> find("id= ".$xalumcur -> id) as $xalcur ){
                                    $xalcur -> situacion = "EXTRAORDINARIO";
                                    $xalcur -> save();
                            }
                    }
            }
        } // function corregirOrdinarios()

        function cllenarXAlumnoAgenda(){
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
            $xcursos = new Xcursos();
            $xalumnoagenda = new Xalumnoagenda();
            $tiempos = new SeleccionTiempo();
            $k = 0;

            foreach ( $tiempos -> find_all_by_sql ("
                            Select * from seleccion_tiempo where periodo = 32009
                            " )as $tiempo ){
                    if ( $xalumnoagenda -> find_first("
                                    registro = ".$tiempo -> registro."
                                                    and periodo = 32009" ) ){
                            echo "Ya existia el registro: ".$tiempo -> registro.
                                            "con el periodo 32009<br /><br />";
                    }
                    else{
                            $k++;
                            echo "se insertaron: ".$k."<br />";
                            $xalumnoagenda -> id = 'default';
                            $xalumnoagenda -> registro = $tiempo -> registro;
                            $xalumnoagenda -> periodo = 32009;
                            $xalumnoagenda -> create();
                    }

            }

        } // function cllenarXAlumnoAgenda()

        function cllenarXAlumnoAgendaPrimerIngreso(){
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
            $xcursos = new Xcursos();
            $xalumnoagenda = new Xalumnoagenda();
            $alumnos = new Alumnos();
            $k = 0;

            foreach ( $alumnos -> find_all_by_sql ("
                            Select * from alumnos where miPerIng = 32009
                            " )as $alumno ){
                    if ( $xalumnoagenda -> find_first("
                                    registro = ".$alumno -> miReg."
                                                    and periodo = 32009" ) ){
                            echo "Ya existia el registro: ".$alumno -> miReg.
                                            "con el periodo 32009<br /><br />";
                    }
                    else{
                            $k++;
                            echo "se insertaron: ".$k."<br />";
                            $xalumnoagenda -> id = 'default';
                            $xalumnoagenda -> registro = $alumno -> miReg;
                            $xalumnoagenda -> periodo = 32009;
                            $xalumnoagenda -> create();
                    }

            }

        } // function cllenarXAlumnoAgenda()

        function cllenarPrimerIngreso(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $periodo = $this -> actual;

//			$id = Session::get_data('registro');

            $alumnos = new Alumnos();
            $xcursos = new Xcursos();
            $xalumnocursos = new Xalumnocursos();

//			$CLAVECURSO = "TCB1925";
            $CLAVECURSO = $this -> post ("clavecurso");
            $xcurso = $xcursos -> find_first("clavecurso= '".$CLAVECURSO."'");

/*
            foreach ( $alumnos -> find_all_by_sql ( "
                            Select * from alumnos
                            where enPlan = 'PE07'
                            and miReg >= 9300000
                            -- Estos registros eran para equivalencia
                            and miReg <> 9310411
                            and miReg <> 9310412
                            and miReg <> 9310413
                            and miReg <> 9310476
                            -- Fin registros de equivalencia
                            and enPlantel = 'C'
                            and idtiesp = 12
                            -- and idtiesp = 12 Industrial
                            -- and idtiesp = 13 13 Electrónica
                            -- and idtiesp = 16 16 Mecatrónica
                            limit 0, 40
                            " )as $alumn ){
*/
            $flag = 0;
            foreach ( $alumnos -> find_all_by_sql ( "
                            Select * from alumnos
                            where miReg = 17606
                            or miReg = 411125
                            or miReg = 511035
                            or miReg = 511069
                            or miReg = 531179
                            or miReg = 611018
                            or miReg = 611037
                            or miReg = 611038
                            or miReg = 611092
                            or miReg = 611188
                            or miReg = 631143
                            or miReg = 631160
                            or miReg = 631173
                            or miReg = 631176
                            or miReg = 631219
                            or miReg = 711136
                            " )as $alumn ){
                    $flag = 0;
//			$xcurso = $xcursos -> find_first("id=".$this -> post("grupo"));

                    if( $xcurso -> disponibilidad <= 0 ){
                            echo "NO hay disponibilidad en este curso<br />";
                            $flag++;
                    }
                    else{
                            $xcurso -> disponibilidad -= 1;
                            $xagendas = new Xalumnoagenda();
                            $xagenda = $xagendas -> find_first("registro= ".$alumn -> miReg." AND periodo=".$periodo);

                            if ( $xalumncurso = $xalumnocursos -> find_first("curso = '".$xcurso -> clavecurso."'
                                            and registro = ".$alumn -> miReg ) ){
                                    echo "El alumno ".$alumn -> miReg." ya estaba en el curso ".
                                                    $xcurso -> clavecurso."<br />";
                            }
                            else{

                                    for($i=$xcurso -> lunesi;$i<$xcurso -> lunesf;$i++){
                                            $tmp = "l".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el lunes a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }

                                    for($i=$xcurso -> martesi;$i<$xcurso -> martesf;$i++){
                                            $tmp = "m".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el martes a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }

                                    for($i=$xcurso -> miercolesi;$i<$xcurso -> miercolesf;$i++){
                                            $tmp = "i".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el miercoles a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }

                                    for($i=$xcurso -> juevesi;$i<$xcurso -> juevesf;$i++){
                                            $tmp = "j".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el jueves a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }

                                    for($i=$xcurso -> viernesi;$i<$xcurso -> viernesf;$i++){
                                            $tmp = "v".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el viernes a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }

                                    for($i=$xcurso -> sabadoi;$i<$xcurso -> sabadof;$i++){
                                            $tmp = "s".$i;
                                            if($xagenda -> $tmp == 1){
                                                    echo "El alumno ".$alumn->miReg." tiene un cruce el sabado a las ".$i."<br />";
                                                    $flag++;
                                            }
                                    }
                            }
                    }
            }
            $k = 0;
            $yaestabainscrito = 0;
            $counter = 0;
            if ( $flag == 0 ){
                    foreach ( $alumnos -> find_all_by_sql ( "
                            Select * from alumnos
                            where miReg = 17606
                            or miReg = 411125
                            or miReg = 511035
                            or miReg = 511069
                            or miReg = 531179
                            or miReg = 611018
                            or miReg = 611037
                            or miReg = 611038
                            or miReg = 611092
                            or miReg = 611188
                            or miReg = 631143
                            or miReg = 631160
                            or miReg = 631173
                            or miReg = 631176
                            or miReg = 631219
                            or miReg = 711136
                                    " )as $alumn ){
                            $k++;
                            $xagendas = new Xalumnoagenda();
                            $xagenda = $xagendas -> find_first("registro= ".$alumn -> miReg." AND periodo=".$periodo);

                            for($i=$xcurso -> lunesi;$i<$xcurso -> lunesf;$i++){
                                    $tmp = "l".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            for($i=$xcurso -> martesi;$i<$xcurso -> martesf;$i++){
                                    $tmp = "m".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            for($i=$xcurso -> miercolesi;$i<$xcurso -> miercolesf;$i++){
                                    $tmp = "i".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            for($i=$xcurso -> juevesi;$i<$xcurso -> juevesf;$i++){
                                    $tmp = "j".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            for($i=$xcurso -> viernesi;$i<$xcurso -> viernesf;$i++){
                                    $tmp = "v".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            for($i=$xcurso -> sabadoi;$i<$xcurso -> sabadof;$i++){
                                    $tmp = "s".$i;
                                    $xagenda -> $tmp = 1;
                            }

                            $xagenda -> save();

                            $xalumnocurso = new Xalumnocursos();

                            if ( $xalumncur = $xalumnocurso -> find_first ( "registro = ".$alumn -> miReg ."
                                            and curso = '".$xcurso -> division.$xcurso -> id."'" ) ){

                                    echo "El alumno con registro ".$alumn -> miReg." ya
                                                    se encontraba inscrito al curso ".$xcurso->division.$xcurso->id."<br />";
                                    $yaestabainscrito ++;
                            }
                            else{
                                    $counter++;
                                    $xalumnocurso -> id = 'default';
                                    $xalumnocurso -> registro = $alumn -> miReg;
                                    $xalumnocurso -> periodo = $periodo;

                                    $xalumnocurso -> curso = $xcurso -> division.$xcurso -> id;
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

                                    $xcurso -> save();

                            }
                    }
            }
            echo "Contador de los que se inscribieron ".$counter."<br />";
            echo "Contador de los que ya estaban inscritos ".$yaestabainscrito."<br />";
        } // function cllenarPrimerIngreso()


        function compararTemporal2(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $temporal = new Temporal2();
            $alumnos = new Alumnos();

            $counter = 0;
            foreach ( $temporal -> find( "periodo = 32009" )as $temp){
                    foreach ( $alumnos -> find("miReg = ".$temp -> registro)as $alumn ){
                            $alumn -> stSit = 'OK';
                            $alumn -> update();
                            echo "registro con ok: ".$alumn -> miReg."<br />";
                            $counter++;
                    }
            }
            echo "ok, ".$counter;
        } // function compararTemporal2()

        function ponerEnTipoEnAlumnos(){ // El Chido (Y)

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            $xalumnocursos = new Xalumnocursos();
            $xacursos = new Xalumnocursos();
            $Alumnos = new Alumnos();
            $xextras = new Xextraordinarios();
			
            $alumno = 0;
            $counter = 0;

            foreach ($xalumnocursos -> find_all_by_sql("
					select registro
					from xalumnocursos
					where periodo = 12012
					group by registro")as $xalumcur){
				
				if( !$alumn = $Alumnos -> find_first("miReg = ".$xalumcur -> registro ) ){
					echo "El registro ".$xalumcur -> registro." no se encontro en la tabla alumnos<br />";
					continue;
				}
				if( $alumn -> correo == "" || $alumn -> correo == null )
					$alumn -> correo = "0";
				if( $alumn -> stSit == "" || $alumn -> stSit == null )
					$alumn -> stSit = "OK";
				if( $alumn -> situacion == "" || $alumn -> situacion == null )
					$alumn -> situacion = "-";
				$counter++;
				if( $alumn -> miPerIng == 12012 ){
					echo $xalumcur -> registro." Regular, MiPerIng 12012<br />";
					$alumn -> enTipo = "R";
					$alumn -> save();
					continue;
				}
				$reconsideracion_baja = new ReconsideracionBaja();
				foreach( $reconsideracion_baja -> find_all_by_sql(
						"select registro from reconsideracion_baja
						where periodo = 12012
						and registro = ".$xalumcur -> registro."
						and procede = 1") as $baja ){
					echo $xalumcur -> registro." condicionado<br />";
					$alumn -> enTipo = "C";
					$alumn -> save();
					continue;
				}
				foreach( $xacursos -> find_all_by_sql( 
						"select situacion from xalumnocursos
						where periodo = 12012
						and registro = ".$xalumcur -> registro."
						and situacion = 'REGULARIZACION DIRECTA'" ) as $xal ){
					echo $xalumcur -> registro." IrregularRegularizacionDirecta<br />";
					$alumn -> enTipo = "I";
					$alumn -> save();
					continue;
				}
				$xextarordinarios = new Xextraordinarios();
				foreach( $xextarordinarios -> find_all_by_sql ("
						select * From xextraordinarios
						where periodo = 12012
						and tipo = 'E'
						and registro = ".$xalumcur -> registro."
						and ( calificacion < 70
						or calificacion > 100 )") as $xext ){
					echo $xalumcur -> registro." IrregularExtra<br />";
					$alumn -> enTipo = "I";
					$alumn -> save();
					continue;
				}
				
				$cuantosAprobo = 0;
				$cuantosReprobo = 0;
				$cuantasAproboEnExtra = 0;
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xalumnocursos
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						and calificacion >= 70
						and calificacion <= 100
						group by registro") as $xal ){
					$cuantosAprobo = $xal -> cuenta;
				}
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xalumnocursos
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						group by registro") as $xal ){
					$cuantasCurso = $xal -> cuenta;
				}
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xextraordinarios
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						and calificacion >= 70
						and calificacion <= 100
						group by registro") as $xal ){
					$cuantasAproboEnExtra = $xal -> cuenta;
				}
				if( $cuantasCurso == ($cuantosAprobo + $cuantasAproboEnExtra) ){
					echo $xalumcur -> registro." Regular<br />";
					$alumn -> enTipo = "R";
					$alumn -> save();
					continue;
				}
				/*
				$alumn -> enTipo = 'R'; Regular
				$alumn -> enTipo = 'I'; Irregular
				$alumn -> enTipo = 'P'; Proceso
				$alumn -> enTipo = 'C'; Condicionado
				*/
            } // foreach ($xalumnocursos -> find_all_by_sql
			
            foreach ($xalumnocursos -> find_all_by_sql("
					select registro
					from xtalumnocursos
					where periodo = 12012
					group by registro")as $xalumcur){
				
				if( !$alumn = $Alumnos -> find_first("miReg = ".$xalumcur -> registro ) ){
					echo "El registro ".$xalumcur -> registro." no se encontro en la tabla alumnos<br />";
					continue;
				}
				if( $alumn -> correo == "" || $alumn -> correo == null )
					$alumn -> correo = "0";
				if( $alumn -> stSit == "" || $alumn -> stSit == null )
					$alumn -> stSit = "OK";
				if( $alumn -> situacion == "" || $alumn -> situacion == null )
					$alumn -> situacion = "-";
				$counter++;
				if( $alumn -> miPerIng == 32012 ){
					echo $xalumcur -> registro." Regular, MiPerIng 12012<br />";
					$alumn -> enTipo = "R";
					$alumn -> save();
					continue;
				}
				$reconsideracion_baja = new ReconsideracionBaja();
				foreach( $reconsideracion_baja -> find_all_by_sql(
						"select registro from reconsideracion_baja
						where periodo = 12012
						and registro = ".$xalumcur -> registro."
						and procede = 1") as $baja ){
					echo $xalumcur -> registro." condicionado<br />";
					$alumn -> enTipo = "C";
					$alumn -> save();
					continue;
				}
				foreach( $xacursos -> find_all_by_sql( 
						"select situacion from xtalumnocursos
						where periodo = 12012
						and registro = ".$xalumcur -> registro."
						and situacion = 'REGULARIZACION DIRECTA'" ) as $xal ){
					echo $xalumcur -> registro." IrregularRegularizacionDirecta<br />";
					$alumn -> enTipo = "I";
					$alumn -> save();
					continue;
				}
				$xextarordinarios = new Xextraordinarios();
				foreach( $xextarordinarios -> find_all_by_sql ("
						select * From xextraordinarios
						where periodo = 12012
						and tipo = 'E'
						and registro = ".$xalumcur -> registro."
						and ( calificacion < 70
						or calificacion > 100 )" ) as $xext ){
					echo $xalumcur -> registro." IrregularExtra<br />";
					$alumn -> enTipo = "I";
					$alumn -> save();
					continue;
				}
				
				$cuantosAprobo = 0;
				$cuantosReprobo = 0;
				$cuantasAproboEnExtra = 0;
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xtalumnocursos
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						and calificacion >= 70
						and calificacion <= 100
						group by registro") as $xal ){
					$cuantosAprobo = $xal -> cuenta;
				}
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xtalumnocursos
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						group by registro") as $xal ){
					$cuantasCurso = $xal -> cuenta;
				}
				foreach( $xacursos -> find_all_by_sql ("
						select count(*) cuenta
						from xtextraordinarios
						where registro = ".$xalumcur -> registro."
						and periodo = 12012
						and calificacion >= 70
						and calificacion <= 100
						group by registro") as $xal ){
					$cuantasAproboEnExtra = $xal -> cuenta;
				}
				if( $cuantasCurso == ($cuantosAprobo + $cuantasAproboEnExtra) ){
					echo $xalumcur -> registro." Regular<br />";
					$alumn -> enTipo = "R";
					$alumn -> save();
					continue;
				}
            } // foreach ($xalumnocursos -> find_all_by_sql
			
			
        } // function ponerEnTipoEnAlumnos()
		
        function ponerstSit(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			// Antes de correr este script, es necesario hacer el siguiente update:
			/*
				
			*/
            $alumnos = new Alumnos();
            $xextras = new Xextraordinarios();
			$historial_alumno = new HistorialAlumno();
			
            $alumno = 0;
            $tipo = 'K';
            $counter = 0;
			
            foreach ($historial_alumno -> find_all_by_sql("
					select * from historial_alumno
					where situacion_id = 1
					and periodo = 32010")as $halumno){
				
				if( $alumn = $alumnos -> find_first("miReg = ".$halumno -> registro ) ){
					echo "El registro ".$halumno -> registro." se dio como baja temporal<br />";
					
					if( $alumn -> correo == "" || $alumn -> correo == null )
						$alumn -> correo = "0";
					if( $alumn -> stSit == "" || $alumn -> stSit == null )
						$alumn -> stSit = "OK";
					if( $alumn -> situacion == "" || $alumn -> situacion == null )
						$alumn -> situacion = "-";
					
					$alumn -> stSit = "BT";
					$alumn -> save();
				}
			} // foreach ($historial_alumno -> find_all_by_sql
        } // function ponerstSit()

		function ponerOkCondonados (){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
            $alumnos = new Alumnos();

            foreach ($xalumnocursos -> find_all_by_sql ("
                            Select * from alumnos al, xalumnocursos xal
                            where al.condonado = 1
                            and al.miReg = xal.registro
                            and xal.periodo = 32009
                            group by xal.registro") as $xalumcur){

                    $alumn = $alumnos -> find_first ("miReg = ".$xalumcur -> registro);

                    $alumn -> stSit = 'OK';
                    $alumn -> update();

            }
        } // function ponerOkCondonados ()

        function llenarXPermisosCapturaColomos (){ // El chido (Y)

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			$Periodos = new Periodos();
            $periodo = $Periodos -> get_periodo_actual();
			
            $xalumnocursos = new Xalumnocursos();
            $xcursos = new Xccursos();
            $xpermisosc = new XPermisoscaptura();

            $k = 0;
            $kk= 0;
            foreach ($xcursos -> find( "periodo =".$periodo ) as $xcur){

                    if ( $xpermisosc -> find_first ( "curso_id = '".$xcur -> id."'" ) ){
                            $kk ++;
                            echo "<br >El curso: ".$xcur -> clavecurso." ya se encontraba en xpermisoscaptura";
                    }
                    else{
                            $k++;
                            $xpermisosc -> id = 'default';
                            $xpermisosc -> curso_id = $xcur -> id;
                            $xpermisosc -> periodo = $periodo;
							
                            $xpermisosc -> ncapturas1 = '0';
                            $xpermisosc -> maxcapturas1 = '1';
                            $xpermisosc -> activa1 = 1;
                            $xpermisosc -> inicio1 =  1362664800; 
                            $xpermisosc -> fin1 = 1363237199;

                            $xpermisosc -> ncapturas2 = '0';
                            $xpermisosc -> maxcapturas2 = '1';
                            $xpermisosc -> activa2 = 1;
                            $xpermisosc -> inicio2 = '0';
                            $xpermisosc -> fin2 = '0';

                            $xpermisosc -> ncapturas3 = '0';
                            $xpermisosc -> maxcapturas3 = '1';
                            $xpermisosc -> activa3 = 1;
                            $xpermisosc -> inicio3 = '0';
                            $xpermisosc -> fin3 = '0';

                            $xpermisosc -> ncapturas4 = '0';
                            $xpermisosc -> maxcapturas4 = '1';
                            $xpermisosc -> activa4 = '0';
                            $xpermisosc -> inicio4 = '0';
                            $xpermisosc -> fin4 = '0';

                            $xpermisosc -> ncapturas5 = '0';
                            $xpermisosc -> maxcapturas5 = '1';
                            $xpermisosc -> activa5 = '0';
                            $xpermisosc -> inicio5 = '0';
                            $xpermisosc -> fin5 = '0';

                            $xpermisosc -> create();
                    }
            }
            echo "<br /><br />Se insertaron: ".$k;
            echo "<br />No se insertaron: ".$kk;
        } // function llenarXPermisosCapturaColomos ()

        function llenarXTPermisosCapturaTonala(){ // El Chido (Y)
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            $Periodos = new Periodos();
            $periodo = $Periodos -> get_periodo_actual();
            $xalumnocursos = new Xtalumnocursos();
            $xcursos = new Xtcursos();
            $xpermisosc = new XtPermisoscaptura();

            $k = 0;
            $kk= 0;
            foreach ($xcursos -> find( "periodo =".$periodo ) as $xcur){

                    if ( $xpermisosc -> find_first ( "curso_id = '".$xcur -> id."'" ) ){
                            $kk ++;
                            echo "<br >El curso: ".$xcur -> clavecurso." ya se encontraba en xpermisoscaptura";
                    }
                    else{
                            $k++;
                            $xpermisosc -> id = 'default';
                            $xpermisosc -> curso_id = $xcur -> id;
                            $xpermisosc -> periodo = $periodo;
							
                            $xpermisosc -> ncapturas1 = '0';
                            $xpermisosc -> maxcapturas1 = '1';
                            $xpermisosc -> activa1 = 1;
                            $xpermisosc -> inicio1 = 1362664800;
                            $xpermisosc -> fin1 = 1363237199;

                            $xpermisosc -> ncapturas2 = '0';
                            $xpermisosc -> maxcapturas2 = '1';
                            $xpermisosc -> activa2 = 1;
                            $xpermisosc -> inicio2 = '0';
                            $xpermisosc -> fin2 = '0';

                            $xpermisosc -> ncapturas3 = '0';
                            $xpermisosc -> maxcapturas3 = '1';
                            $xpermisosc -> activa3 = 1;
                            $xpermisosc -> inicio3 = '0';
                            $xpermisosc -> fin3 = '0';

                            $xpermisosc -> ncapturas4 = '0';
                            $xpermisosc -> maxcapturas4 = '1';
                            $xpermisosc -> activa4 = '0';
                            $xpermisosc -> inicio4 = '0';
                            $xpermisosc -> fin4 = '0';

                            $xpermisosc -> ncapturas5 = '0';
                            $xpermisosc -> maxcapturas5 = '1';
                            $xpermisosc -> activa5 = '0';
                            $xpermisosc -> inicio5 = '0';
                            $xpermisosc -> fin5 = '0';

                            $xpermisosc -> create();
                    }
            }
            echo "<br /><br />Se insertaron: ".$k;
            echo "<br />No se insertaron: ".$kk;
        } // function llenarXTPermisosCapturaTonala()

        function cllenarTonala(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $periodo = $this -> actual;

//			$id = Session::get_data('registro');

            //$alumnos = new Alumnos();
            $xcursos = new Xtcursos();
            $xalumnocursos = new Xtalumnocursos();

//			$CLAVECURSO = "TCB1925";
            $CLAVECURSO = $this -> post ("clavecurso");
            $xcurso = $xcursos -> find_first("clavecurso= '".$CLAVECURSO."'");

            $flag = 0;
            /*
            foreach ( $alumnos -> find_all_by_sql ( "
                            Select * from alumnos
                            where miReg = 10110035
                            " )as $alumn ){
            */
            $alumnos[0] = 10110035;
            $alumnos[1] = 10110036;
            $alumnos[2] = 10110037;
            $alumnos[3] = 10110038;
            $alumnos[4] = 10110059;
            $alumnos[5] = 10110078;
            $alumnos[6] = 10110079;
            $alumnos[7] = 10110082;
            $alumnos[8] = 10110086;
            $alumnos[9] = 10110098;
            $alumnos[10] = 10110102;
            $alumnos[11] = 10110120;
            $alumnos[12] = 10110130;
            $alumnos[13] = 10110133;
            $alumnos[14] = 10110136;
            $alumnos[15] = 10110138;
            $alumnos[16] = 10110153;
            $alumnos[17] = 10110160;
            $alumnos[18] = 10110161;
            $alumnos[19] = 10110166;
            $alumnos[20] = 10110175;
            $alumnos[21] = 10110179;
            $alumnos[22] = 10110292;
            $alumnos[23] = 10110197;
            $alumnos[24] = 10110201;
            $alumnos[25] = 10110208;
            $alumnos[26] = 10110209;
            $alumnos[27] = 10110218;
            $alumnos[28] = 10110225;
            $alumnos[29] = 10110230;
            $alumnos[30] = 10110235;
            $alumnos[31] = 10110240;
            $alumnos[32] = 10110244;
            $alumnos[33] = 10110252;
            $alumnos[34] = 10110254;
            $alumnos[35] = 10110273;
            $alumnos[36] = 10110276;
            $alumnos[37] = 10110277;
            $alumnos[38] = 10110281;
            $alumnos[39] = 10110284;
            $alumnos[40] = 10110285;
            foreach ( $alumnos as $alumn ){
                    $flag = 0;
//			$xcurso = $xcursos -> find_first("id=".$this -> post("grupo"));

                    if( $xcurso -> disponibilidad <= 0 ){
                            echo "NO hay disponibilidad en este curso<br />";
                            $flag++;
                    }
                    else{
                            $xcurso -> disponibilidad -= 1;

                            if ( $xalumncurso = $xalumnocursos -> find_first("curso = '".$xcurso -> clavecurso."'
                                            and registro = ".$alumn ) ){
                                    echo "El alumno ".$alumn." ya estaba en el curso ".
                                                    $xcurso -> clavecurso."<br />";
                            }
                    }
            }
            $k = 0;
            $yaestabainscrito = 0;
            $counter = 0;
            if ( $flag == 0 ){
                    foreach ( $alumnos as $alumn ){

                            $xalumnocurso = new Xtalumnocursos();

                            if ( $xalumncur = $xalumnocurso -> find_first ( "registro = ".$alumn ."
                                            and curso = '".$xcurso -> division.$xcurso -> id."'" ) ){

                                    echo "El alumno con registro ".$alumn." ya
                                                    se encontraba inscrito al curso ".$xcurso->division.$xcurso->id."<br />";
                                    $yaestabainscrito ++;
                            }
                            else{
                                    $counter++;
                                    $xalumnocurso -> id = 'default';
                                    $xalumnocurso -> registro = $alumn;
                                    $xalumnocurso -> periodo = $periodo;

                                    $xalumnocurso -> curso = $xcurso -> clavecurso;
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

                                    $xcurso -> save();
                            }
                    }
            }
            echo "Contador de los que se inscribieron ".$counter."<br />";
            echo "Contador de los que ya estaban inscritos ".$yaestabainscrito."<br />";
        } // function cllenarTonala()

        function cChecarDoblesHoras(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $periodo = $this -> actual;

//			$id = Session::get_data('registro');

            $alumnos = new Alumnos();
            $xcursos = new Xcursos();
            $xalumnocursos = new Xalumnocursos();

//			$CLAVECURSO = "TCB1925";
            $CLAVECURSO = $this -> post ("clavecurso");

//			foreach ( $xcursos -> find_all_by_sql ("periodo = 32009") as $xcur ){

            $flag = 0;
            foreach ( $xalumnocursos -> find ( "periodo = ".$periodo,
                            "order: registro asc" ) as $xalumn ){
                    $flag = 0;

                    $xcurso = $xcursos -> find_first ( "clavecurso = '".$xalumn -> curso."'" );

                    $xagendas = new Xalumnoagenda();
                    $xagenda = $xagendas -> find_first("registro= ".$xalumn -> registro." AND periodo=".$periodo);

                    for($i=$xcurso -> lunesi;$i<$xcurso -> lunesf;$i++){
                            $tmp = "l".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> martesi;$i<$xcurso -> martesf;$i++){
                            $tmp = "m".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> miercolesi;$i<$xcurso -> miercolesf;$i++){
                            $tmp = "i".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> juevesi;$i<$xcurso -> juevesf;$i++){
                            $tmp = "j".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> viernesi;$i<$xcurso -> viernesf;$i++){
                            $tmp = "v".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> sabadoi;$i<$xcurso -> sabadof;$i++){
                            $tmp = "s".$i;
                            $xagenda -> $tmp += 1;
                    }



                    for($i=$xcurso -> lunesi2;$i<$xcurso -> lunesf2;$i++){
                            $tmp = "l".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> martesi2;$i<$xcurso -> martesf2;$i++){
                            $tmp = "m".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> miercolesi2;$i<$xcurso -> miercolesf2;$i++){
                            $tmp = "i".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> juevesi2;$i<$xcurso -> juevesf2;$i++){
                            $tmp = "j".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> viernesi2;$i<$xcurso -> viernesf2;$i++){
                            $tmp = "v".$i;
                            $xagenda -> $tmp += 1;
                    }

                    for($i=$xcurso -> sabadoi2;$i<$xcurso -> sabadof2;$i++){
                            $tmp = "s".$i;
                            $xagenda -> $tmp += 1;
                    }

                    $xagenda -> save();
            }

        } // function cChecarDoblesHoras()

        function ponerPagoEnAlumnos(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $periodo = $this -> actual;

            $alumnos = new Alumnos();
            $temp2 = new Temporal2();

            $flag = 0;
            foreach ( $temp2 -> find_all_by_sql ( "Select * From temporal2" ) as $tmp2 ){
                    $flag = 0;
                    echo "<br />".$tmp2 -> registro." 1";
                    foreach ( $alumnos -> find ( "miReg = ".$tmp2 -> registro ) as $alumn ){

                            $alumn -> pago = 1;

                            $alumn -> update();
                    }
            }

        } // function ponerPagoEnAlumnos()

        function borrarMateriasRepetidasEnKardex(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $periodo = $this -> actual;

            $kardexIng = new KardexIng();
            $kardexIng2 = new KardexIng();
            $xalumnocursos = new Xalumnocursos();

            $flag = 0;
            $i = 1;
            $registro[0] = 0;
            $clavemat[0] = 0;
            foreach ( $kardexIng -> find_all_by_sql ( "
                            Select id, registro, clavemat, fecha_reg from kardex_ing
                            order by registro, clavemat" ) as $kIng ){
                    $registro[$i] = $kIng -> registro;
                    $clavemat[$i] = $kIng -> clavemat;
                    if ( $registro[$i-1] == $registro[$i] && $clavemat[$i-1] == $clavemat[$i] && $i != 1 ){
                    //	echo $kIng->id."<br />";
                            $kardexIng2 -> delete ( $kIng -> id );
                    }
                    else{
                            $i++;
                    }
                    if ( $kIng -> registro != $registro[$i-1] ){
                            $i = 1;
                            $registro[0] = 0;
                            $clavemat[0] = 0;
                    }
            }

        } // function borrarMateriasRepetidasEnKardex()

        function ponerPeriodoEnHistorialAlumno(){
            // Esta función se usa para poner el periodo en la tabla historial_alumno
            //ya que cuando se creo está tabla no se creia necesario dicho campo,
            //pero conforme paso el tiempo se vio la necesidad de ponerlo.

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $HAlumnos = new HistorialAlumno();

            foreach ( $HAlumnos -> find_all_by_sql( "Select * from historial_alumno" ) as $halumno ){

                    $resto1 = substr($halumno -> fecha, 0, 4);
                    $resto2 = substr($halumno -> fecha, 5, 2);

                    if( $resto2[0] == 0 )
                            $resto2 = substr($resto2, 1);

                    if( $resto2 < 8 ){
                            // Periodo de Primavera - Verano
                            $numero = 1;
                    }
                    else{
                            // Periodo de Otoño - Invierno
                            $numero = 3;
                    }
                    $halumno -> periodo = $numero.$resto1;
                    $halumno -> save();

            }
            echo "Todo bien";
        } // function ponerPeriodoEnHistorialAlumno()
		
        function llenarSeleccionTiempoPara12012RegularesColomos(){ // -> el chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
			$xextraordinarios = new Xextraordinarios();
            $k = 0;
            $hora = 8;
            $dia = 23;
            $i = 0;
			foreach ( $xalumnocursos -> find_all_by_sql ( "
						Select distinct registro
						from xalumnocursos
						-- where periodo = 12009
						-- where periodo = 32009
						-- where periodo = 12010
						-- where periodo = 32010
						-- where periodo = 12011
						where periodo = 32011
						" ) as $xacur ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 12012
											and registro = ".$xacur -> registro ) ){
					// Si entra significa que si existe y no es necesario volver a ponerlo
					continue;
				}
				foreach ( $xalumnocursos -> find_all_by_sql("
								Select AVG(calificacion) as promedio
								from xalumnocursos,
								(
									Select miReg from alumnos
									where enPlantel = 'C'
									and stSit = 'OK'
								)tabla1
								where registro = ".$xacur -> registro."
								-- and periodo = 32009
								-- and periodo = 12010
								-- and periodo = 32010
								-- and periodo = 12011
								and periodo = 32011
								and calificacion <= 100
								and calificacion >= 70
								and registro != tabla1.miReg
								")as $xacur2 ){
						foreach( $xalumnocursos -> find_all_by_sql("
								Select tabla1.CursosqueAcreditoEnExt,
								tabla2.CursosqueAcreditoSinExt,
								tabla3.CursosqueTomo
								from
								(
									Select count(*) as CursosqueAcreditoEnExt
									from xextraordinarios
									-- where periodo = 32009
									-- where periodo = 12010
									-- where periodo = 32010
									-- where periodo = 12011
									where periodo = 32011
									and registro = ".$xacur -> registro."
									and calificacion >= 70
									and calificacion <= 100
								)tabla1,
								(
									Select count(*) as CursosqueAcreditoSinExt
									from xalumnocursos as xa
									-- where xa.periodo = 32009
									-- where xa.periodo = 12010
									-- where xa.periodo = 32010
									-- where xa.periodo = 12011
									where xa.periodo = 32011
									and xa.registro = ".$xacur -> registro."
									and calificacion >= 70
									and calificacion <= 100
								)tabla2,
								(
									Select count(*) as CursosqueTomo
									from xalumnocursos as xa
									where xa.registro = ".$xacur -> registro."
									-- and xa.periodo= 32009
									-- and xa.periodo= 12010
									-- and xa.periodo= 32010
									-- and xa.periodo= 12011
									and xa.periodo= 32011
								)tabla3
									") as $xacur3 ){
							if ( (  ( ($xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt) * 100 )
										/ $xacur3 -> CursosqueTomo ) < 20 ){
								continue;
							}
							/*
							echo "<br />".$xacur -> registro."<br />";
							echo "CursosQuetomo: ".$xacur3 -> CursosqueTomo.
							" suma:".( $xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt )."<br />";
							*/
							if( $xacur3 -> CursosqueTomo >
									( ( $xacur3 -> CursosqueAcreditoSinExt +
											$xacur3 -> CursosqueAcreditoEnExt ) ) ){
								echo "<br />entro al if<br />";
								continue;
							}
							if( $xextraordinarios -> find_first("registro = ".$xacur -> registro.
									" and periodo = 32011 and tipo = 'T'" ) ){
								continue;
							}
							$xalumnocursos123 = new Xalumnocursos();
							if( $xalumnocursos123 -> find_first("registro = ".$xacur -> registro.
									" and periodo = 32011 and situacion = 'BAJA DEFINITIVA'" ) ){
								continue;
							}
							$arregloPromedios[$i] = $xacur2 -> promedio;
							$arregloRegistros[$i] = $xacur -> registro;
							$i ++;
						}
				}
			}

			for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
			}

			for( $i=0; $i < count($arregloPromedios); $i++ ){
				// echo $arregloPromedios[$i]." <br />";
				// echo $arregloRegistros[$i]." <br /><br />";

				$tiempos = new SeleccionTiempo();
				$k ++;
				if ( ( $k % 22 ) == 0 ){
					$hora ++;
					/*
					if ( $dia >= 15 && $hora == 16 ){
							$dia ++;
							$hora = 10;
					}
					*/
					if ( $hora == 22 ){
						$dia++;
						$hora = 8;
					}
					if ( $hora < 10 ){
						$cero = 0;
						$hora = $cero.$hora;
					}
				}

				$arregloPromedios[$i] = (int) $arregloPromedios[$i];
				if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
						$arregloPromedios[$i] = '0';
				$tiempos -> id = 'default';
				$tiempos -> registro = $arregloRegistros[$i];
				$tiempos -> promedio = $arregloPromedios[$i];
				$hinicio = '2012-01-'.$dia.' '.$hora.':00:00';
				$hfin = '2012-01-25 23:59:59';
				$tiempos -> inicio = $hinicio;
				$tiempos -> fin = $hfin;
				$tiempos -> periodo = 12012;
				
				if( $tiempos -> promedio == 0 || $tiempos -> promedio == "" )
						$tiempos -> promedio = '0';
				
				$tiempos -> create();
			}

        } // function llenarSeleccionTiempoPara12012RegularesColomos()

        function llenarSeleccionTiempoPara12012RegularesTonala(){ // -> el chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xtalumnocursos();
			$xextraordinarios = new Xtextraordinarios();
            $k = 0;
            $hora = 8;
            $dia = 23;
            $i = 0;
			foreach ( $xalumnocursos -> find_all_by_sql ( "
						Select distinct registro
						from xtalumnocursos
						-- where periodo = 12009
						-- where periodo = 32009
						-- where periodo = 12010
						-- where periodo = 32010
						-- where periodo = 12011
						where periodo = 32011
						" ) as $xacur ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 12012
											and registro = ".$xacur -> registro ) ){
					// Si entra significa que si existe y no es necesario volver a ponerlo
					continue;
				}
				foreach ( $xalumnocursos -> find_all_by_sql("
								Select AVG(calificacion) as promedio
								from xtalumnocursos,
								(
									Select miReg from alumnos
									where enPlantel = 'N'
									and stSit = 'OK'
								)tabla1
								where registro = ".$xacur -> registro."
								-- and periodo = 32009
								-- and periodo = 12010
								-- and periodo = 32010
								-- and periodo = 12011
								and periodo = 32011
								and calificacion <= 100
								and calificacion >= 70
								and registro != tabla1.miReg
								")as $xacur2 ){
						foreach( $xalumnocursos -> find_all_by_sql("
								Select tabla1.CursosqueAcreditoEnExt,
								tabla2.CursosqueAcreditoSinExt,
								tabla3.CursosqueTomo
								from
								(
									Select count(*) as CursosqueAcreditoEnExt
									from xtextraordinarios
									-- where periodo = 32009
									-- where periodo = 12010
									-- where periodo = 32010
									-- where periodo = 12011
									where periodo = 32011
									and registro = ".$xacur -> registro."
									and calificacion >= 70
									and calificacion <= 100
								)tabla1,
								(
									Select count(*) as CursosqueAcreditoSinExt
									from xtalumnocursos as xa
									-- where xa.periodo = 32009
									-- where xa.periodo = 12010
									-- where xa.periodo = 32010
									-- where xa.periodo = 12011
									where xa.periodo = 32011
									and xa.registro = ".$xacur -> registro."
									and calificacion >= 70
									and calificacion <= 100
								)tabla2,
								(
									Select count(*) as CursosqueTomo
									from xtalumnocursos as xa
									where xa.registro = ".$xacur -> registro."
									-- and xa.periodo= 32009
									-- and xa.periodo= 12010
									-- and xa.periodo= 32010
									-- and xa.periodo= 12011
									and xa.periodo= 32011
								)tabla3
								") as $xacur3 ){
							if ( (  ( ($xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt) * 100 )
										/ $xacur3 -> CursosqueTomo ) < 20 ){
								continue;
							}
							/*
							echo "<br />".$xacur -> registro."<br />";
							echo "CursosQuetomo: ".$xacur3 -> CursosqueTomo.
							" suma:".( $xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt )."<br />";
							*/
							if( $xacur3 -> CursosqueTomo >
									( ( $xacur3 -> CursosqueAcreditoSinExt +
											$xacur3 -> CursosqueAcreditoEnExt ) ) ){
								echo "<br />entro al if<br />";
								continue;
							}
							if( $xextraordinarios -> find_first("registro = ".$xacur -> registro.
									" and periodo = 32011 and tipo = 'T'" ) ){
								continue;
							}
							$xalumnocursos123 = new Xtalumnocursos();
							if( $xalumnocursos123 -> find_first("registro = ".$xacur -> registro.
									" and periodo = 32011 and situacion = 'BAJA DEFINITIVA'" ) ){
								continue;
							}
							$arregloPromedios[$i] = $xacur2 -> promedio;
							$arregloRegistros[$i] = $xacur -> registro;
							$i ++;
						}
				}
			}

			for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
			}

			for( $i=0; $i < count($arregloPromedios); $i++ ){
				// echo $arregloPromedios[$i]." <br />";
				// echo $arregloRegistros[$i]." <br /><br />";

				$tiempos = new SeleccionTiempo();
				$k ++;
				if ( ( $k % 22 ) == 0 ){
					$hora ++;
					/*
					if ( $dia >= 15 && $hora == 16 ){
							$dia ++;
							$hora = 10;
					}
					*/
					if ( $hora == 22 ){
						$dia++;
						$hora = 8;
					}
					if ( $hora < 10 ){
						$cero = 0;
						$hora = $cero.$hora;
					}
				}

				$arregloPromedios[$i] = (int) $arregloPromedios[$i];
				if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
						$arregloPromedios[$i] = '0';
				$tiempos -> id = 'default';
				$tiempos -> registro = $arregloRegistros[$i];
				$tiempos -> promedio = $arregloPromedios[$i];
				$hinicio = '2012-01-'.$dia.' '.$hora.':00:00';
				$hfin = '2012-01-25 23:59:59';
				$tiempos -> inicio = $hinicio;
				$tiempos -> fin = $hfin;
				$tiempos -> periodo = 12012;
				
				if( $tiempos -> promedio == 0 || $tiempos -> promedio == "" )
					$tiempos -> promedio = '0';
				
				$tiempos -> create();
			}

        } // function llenarSeleccionTiempoPara12012RegularesTonala()
		
        function llenarSeleccionTiempoPara12013RegularesColomosNuevo(){ // -> el chido
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			$Alumnos = new Alumnos();
            $xalumnocursos = new Xalumnocursos();
			$xextraordinarios = new Xextraordinarios();
            $k = 0;
            $hora = 11;
            $dia = 17;
            $i = 0;
			foreach ( $Alumnos -> find_all_by_sql ( "
					Select distinct miReg
					from alumnos
					where stSit = 'OK'
					and enTipo = 'R'
					and enPlantel = 'C'
					and chGpo != '**'" ) as $alumno ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 12013
						and registro = ".$alumno -> miReg ) ){
					// Si entra significa que si existe y no es necesario volver a ponerlo
					continue;
				}
				foreach ( $xalumnocursos -> find_all_by_sql("
								Select AVG(calificacion) as promedio
								from xalumnocursos
								where registro = ".$alumno -> miReg."
								and periodo = 32012
								and calificacion <= 100
								and calificacion >= 70
								")as $xacur2 ){
						foreach( $xalumnocursos -> find_all_by_sql("
								Select tabla1.CursosqueAcreditoEnExt,
								tabla2.CursosqueAcreditoSinExt,
								tabla3.CursosqueTomo
								from
								(
									Select count(*) as CursosqueAcreditoEnExt
									from xextraordinarios
									where periodo = 32012
									and registro = ".$alumno -> miReg."
									and calificacion >= 70
									and calificacion <= 100
								)tabla1,
								(
									Select count(*) as CursosqueAcreditoSinExt
									from xalumnocursos as xa
									where xa.periodo = 32012
									and xa.registro = ".$alumno -> miReg."
									and calificacion >= 70
									and calificacion <= 100
								)tabla2,
								(
									Select count(*) as CursosqueTomo
									from xalumnocursos as xa
									where xa.registro = ".$alumno -> miReg."
									and xa.periodo= 32012
								)tabla3
									") as $xacur3 ){
							if ( (  ( ($xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt) * 100 )
										/ $xacur3 -> CursosqueTomo ) < 20 ){
								continue;
							}
							/*
							echo "<br />".$xacur -> registro."<br />";
							echo "CursosQuetomo: ".$xacur3 -> CursosqueTomo.
							" suma:".( $xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt )."<br />";
							*/
							if( $xacur3 -> CursosqueTomo >
									( ( $xacur3 -> CursosqueAcreditoSinExt +
											$xacur3 -> CursosqueAcreditoEnExt ) ) ){
								echo "<br />entro al if<br />";
								continue;
							}
							if( $xextraordinarios -> find_first("registro = ".$alumno -> miReg.
									" and periodo = 32012 and tipo = 'T'" ) ){
								continue;
							}
							$xalumnocursos123 = new Xalumnocursos();
							if( $xalumnocursos123 -> find_first("registro = ".$alumno -> miReg.
									" and periodo = 32012 and situacion = 'BAJA DEFINITIVA'" ) ){
								continue;
							}
							$arregloPromedios[$i] = $xacur2 -> promedio;
							$arregloRegistros[$i] = $alumno -> miReg;
							$i ++;
						}
				}
			}

			for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
			}

			for( $i=0; $i < count($arregloPromedios); $i++ ){
				// echo $arregloPromedios[$i]." <br />";
				// echo $arregloRegistros[$i]." <br /><br />";

				$tiempos = new SeleccionTiempo();
				$k ++;
				if ( ( $k % 22 ) == 0 ){
					$hora ++;
					/*
					if ( $dia >= 15 && $hora == 16 ){
							$dia ++;
							$hora = 10;
					}
					*/
					if ( $hora == 22 ){
						$dia++;
						$hora = 9;
					}
					if ( $hora < 10 ){
						$cero = 0;
						$hora = $cero.$hora;
					}
				}

				$arregloPromedios[$i] = (int) $arregloPromedios[$i];
				if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
						$arregloPromedios[$i] = '0';
				$tiempos -> id = 'default';
				$tiempos -> registro = $arregloRegistros[$i];
				$tiempos -> promedio = $arregloPromedios[$i];
				$hinicio = '2013-01-'.$dia.' '.$hora.':00:00';
				$hfin = '2013-01-20 23:59:59';
				$tiempos -> inicio = $hinicio;
				$tiempos -> fin = $hfin;
				$tiempos -> periodo = 12013;
				
				if( $tiempos -> promedio == 0 || $tiempos -> promedio == "" )
					$tiempos -> promedio = '0';
				
				$tiempos -> create();
			}

        } // function llenarSeleccionTiempoPara12013RegularesColomosNuevo()

        function llenarSeleccionTiempoPara12013RegularesTonalaNuevo(){ // -> el chido
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			$Alumnos = new Alumnos();
            $xalumnocursos = new Xtalumnocursos();
			$xextraordinarios = new Xtextraordinarios();
            $k = 0;
            $hora = 11;
            $dia = 17;
            $i = 0;
			foreach ( $Alumnos -> find_all_by_sql ( "
					Select distinct miReg
					from alumnos
					where stSit = 'OK'
					and enTipo = 'R'
					and enPlantel = 'N'
					and chGpo != '**'" ) as $alumno ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 12013
						and registro = ".$alumno -> miReg ) ){
					// Si entra significa que si existe y no es necesario volver a ponerlo
					continue;
				}
				foreach ( $xalumnocursos -> find_all_by_sql("
								Select AVG(calificacion) as promedio
								from xtalumnocursos
								where registro = ".$alumno -> miReg."
								and periodo = 32012
								and calificacion <= 100
								and calificacion >= 70
								")as $xacur2 ){
						foreach( $xalumnocursos -> find_all_by_sql("
								Select tabla1.CursosqueAcreditoEnExt,
								tabla2.CursosqueAcreditoSinExt,
								tabla3.CursosqueTomo
								from
								(
									Select count(*) as CursosqueAcreditoEnExt
									from xtextraordinarios
									where periodo = 32012
									and registro = ".$alumno -> miReg."
									and calificacion >= 70
									and calificacion <= 100
								)tabla1,
								(
									Select count(*) as CursosqueAcreditoSinExt
									from xtalumnocursos as xa
									where xa.periodo = 32012
									and xa.registro = ".$alumno -> miReg."
									and calificacion >= 70
									and calificacion <= 100
								)tabla2,
								(
									Select count(*) as CursosqueTomo
									from xtalumnocursos as xa
									where xa.registro = ".$alumno -> miReg."
									and xa.periodo= 32012
								)tabla3
									") as $xacur3 ){
							if ( (  ( ($xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt) * 100 )
										/ $xacur3 -> CursosqueTomo ) < 20 ){
								continue;
							}
							/*
							echo "<br />".$xacur -> registro."<br />";
							echo "CursosQuetomo: ".$xacur3 -> CursosqueTomo.
							" suma:".( $xacur3 -> CursosqueAcreditoSinExt + $xacur3 -> CursosqueAcreditoEnExt )."<br />";
							*/
							if( $xacur3 -> CursosqueTomo >
									( ( $xacur3 -> CursosqueAcreditoSinExt +
											$xacur3 -> CursosqueAcreditoEnExt ) ) ){
								echo "<br />entro al if<br />";
								continue;
							}
							if( $xextraordinarios -> find_first("registro = ".$alumno -> miReg.
									" and periodo = 32012 and tipo = 'T'" ) ){
								continue;
							}
							$xalumnocursos123 = new Xtalumnocursos();
							if( $xalumnocursos123 -> find_first("registro = ".$alumno -> miReg.
									" and periodo = 32012 and situacion = 'BAJA DEFINITIVA'" ) ){
								continue;
							}
							$arregloPromedios[$i] = $xacur2 -> promedio;
							$arregloRegistros[$i] = $alumno -> miReg;
							$i ++;
						}
				}
			}

			for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
			}

			for( $i=0; $i < count($arregloPromedios); $i++ ){
				// echo $arregloPromedios[$i]." <br />";
				// echo $arregloRegistros[$i]." <br /><br />";

				$tiempos = new SeleccionTiempo();
				$k ++;
				if ( ( $k % 22 ) == 0 ){
					$hora ++;
					/*
					if ( $dia >= 15 && $hora == 16 ){
							$dia ++;
							$hora = 10;
					}
					*/
					if ( $hora == 22 ){
						$dia++;
						$hora = 9;
					}
					if ( $hora < 10 ){
						$cero = 0;
						$hora = $cero.$hora;
					}
				}

				$arregloPromedios[$i] = (int) $arregloPromedios[$i];
				if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
						$arregloPromedios[$i] = '0';
				$tiempos -> id = 'default';
				$tiempos -> registro = $arregloRegistros[$i];
				$tiempos -> promedio = $arregloPromedios[$i];
				$hinicio = '2013-01-'.$dia.' '.$hora.':00:00';
				$hfin = '2013-01-20 23:59:59';
				$tiempos -> inicio = $hinicio;
				$tiempos -> fin = $hfin;
				$tiempos -> periodo = 12013;
				
				if( $tiempos -> promedio == 0 || $tiempos -> promedio == "" )
					$tiempos -> promedio = '0';
				
				$tiempos -> create();
			}

        } // function llenarSeleccionTiempoPara12013RegularesTonalaNuevo()

		
        function llenarSeleccionTiempoPara12013IrregularesColomos(){ // -> (Y)

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
            $k = 0;
            $hora = 9;
            $dia = 23;
            $i = 0;
            foreach ( $xalumnocursos -> find_all_by_sql ( "
                                    Select distinct registro
                                    from xalumnocursos
									where periodo = 32012
                            " ) as $xacur ){
					$seleccTiempoo2 = new SeleccionTiempo();
					if( $seleccTiempoo2 -> find_first( "periodo = 12013
												and registro = ".$xacur -> registro ) ){
						// Si entra significa que si existe y no es necesario volver a ponerlo
						continue;
					}
					$Xalumnocursoss = new Xalumnocursos();
					if( $Xalumnocursoss -> find_first( "periodo = 32012
												and registro = ".$xacur -> registro."
												and situacion = 'BAJA DEFINITIVA'" ) ){
						// Si entra significa que tiene una baja definitiva
						continue;
					}
                    foreach ( $xalumnocursos -> find_all_by_sql("
										Select AVG(calificacion) as promedio
										from xalumnocursos,
										(
											Select miReg from alumnos
											where enPlantel = 'C'
											and stSit = 'OK'
											and enTipo = 'I'
											and chGpo != '**'
										)tabla1
										where registro = ".$xacur -> registro."
										and periodo = 32012
										and calificacion <= 100
										and registro != tabla1.miReg
								")as $xacur2 ){
								$xalcursoss = new Xalumnocursos();
								foreach( $xalcursoss -> find_all_by_sql
												 ( "Select count(*) as cuantos from xalumnocursos
													where registro = ".$xacur -> registro."
													and periodo = 32012
													and ( situacion = \"TITULO DE SUFICIENCIA\"
													or situacion = \"TITULO FALTAS\")" ) as $xalcurr){
										$xalcurr = $xalcurr;
								}
								$xextrasss = new Xextraordinarios();

								foreach( $xextrasss -> find_all_by_sql
												 ( "Select count(*) as cuantosTitulos
													from xextraordinarios
													where registro = ".$xacur -> registro."
													and periodo = 32012
													and calificacion > 69
													and calificacion < 100
													and tipo = \"T\"" ) as $xextrs ){
										$xextrs = $xextrs;
								}
								if( ($xalcurr -> cuantos - $xextrs -> cuantosTitulos) > 0 ){
										echo $xacur -> registro."<br />";
										continue;
								}
								$arregloPromedios[$i] = $xacur2 -> promedio;
								$arregloRegistros[$i] = $xacur -> registro;
								$i ++;
					}
            }

            for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
            }

            for( $i=0; $i < count($arregloPromedios); $i++ ){
                    // echo $arregloPromedios[$i]." <br />";
                    // echo $arregloRegistros[$i]." <br /><br />";

                    $tiempos = new SeleccionTiempo();
                    $k ++;
                    if ( ( $k % 35 ) == 0 ){
                            $hora ++;
                            if ( $hora == 23 ){
                                    $dia++;
                                    $hora = 7;
                            }
                            if ( $hora < 10 ){
                                    $cero = 0;
                                    $hora = $cero.$hora;
                            }
                    }

                    $arregloPromedios[$i] = (int) $arregloPromedios[$i];
                    if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
                            $arregloPromedios[$i] = '0';
                    $tiempos -> id = 'default';
                    $tiempos -> registro = $arregloRegistros[$i];
                    $tiempos -> promedio = $arregloPromedios[$i];
                    $hinicio = '2013-01-'.$dia.' '.$hora.':00:00';
                    $hfin = '2013-01-27 23:59:59';
                    $tiempos -> inicio = $hinicio;
                    $tiempos -> fin = $hfin;
                    $tiempos -> periodo = 12013;

					$tiempos -> create();
            }

        } // function llenarSeleccionTiempoPara12013IrregularesColomos()
		
        function llenarSeleccionTiempoPara12013IrregularesTonala(){ // -> (Y)

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xtalumnocursos();
            $k = 0;
            $hora = 9;
            $dia = 23;
            $i = 0;
            foreach ( $xalumnocursos -> find_all_by_sql ( "
                                    Select distinct registro
                                    from xtalumnocursos
									where periodo = 32012
                            " ) as $xacur ){
					$seleccTiempoo2 = new SeleccionTiempo();
					if( $seleccTiempoo2 -> find_first( "periodo = 12013
												and registro = ".$xacur -> registro ) ){
						// Si entra significa que si existe y no es necesario volver a ponerlo
						continue;
					}
					$Xalumnocursoss = new Xtalumnocursos();
					if( $Xalumnocursoss -> find_first( "periodo = 32012
												and registro = ".$xacur -> registro."
												and situacion = 'BAJA DEFINITIVA'" ) ){
						// Si entra significa que el alumno está dado de baja, por lo que tendrá que meter carta
						continue;
					}
                    foreach ( $xalumnocursos -> find_all_by_sql("
                                            Select AVG(calificacion) as promedio
                                            from xtalumnocursos,
                                                    (
														Select miReg from alumnos
														where enPlantel = 'N'
														and stSit = 'OK'
														and enTipo = 'I'
														and chGpo != '**'
                                                    )tabla1
                                            where registro = ".$xacur -> registro."
											and periodo = 32012
                                            and calificacion <= 100
                                            and registro != tabla1.miReg
                                    ")as $xacur2 ){
                                    $xalcursoss = new Xalumnocursos();
                                    foreach( $xalcursoss -> find_all_by_sql
                                                     ( "Select count(*) as cuantos from xtalumnocursos
														where registro = ".$xacur -> registro."
														and periodo = 32012
														and ( situacion = \"TITULO DE SUFICIENCIA\"
														or situacion = \"TITULO FALTAS\")" ) as $xalcurr){
                                            $xalcurr = $xalcurr;
                                    }
                                    $xextrasss = new Xextraordinarios();

                                    foreach( $xextrasss -> find_all_by_sql
                                                     ( "Select count(*) as cuantosTitulos
														from xtextraordinarios
														where registro = ".$xacur -> registro."
														and periodo = 32012
														and calificacion > 69
														and calificacion < 100
														and tipo = \"T\"" ) as $xextrs ){
                                            $xextrs = $xextrs;
                                    }
                                    if( ($xalcurr -> cuantos - $xextrs -> cuantosTitulos) > 0 ){
                                            echo $xacur -> registro."<br />";
                                            continue;
                                    }
                                    $arregloPromedios[$i] = $xacur2 -> promedio;
                                    $arregloRegistros[$i] = $xacur -> registro;
                                    $i ++;
                            }
            }

            for( $i = 0; $i < count($arregloPromedios); $i++ ){
				for( $j = 0; $j < $i; $j++ ){
					if( $arregloPromedios[$i] > $arregloPromedios[$j] ){
						$temp = $arregloPromedios[$i]; //swap
						$arregloPromedios[$i] = $arregloPromedios[$j];
						$arregloPromedios[$j] = $temp;

						$temp = $arregloRegistros[$i]; //swap
						$arregloRegistros[$i] = $arregloRegistros[$j];
						$arregloRegistros[$j] = $temp;
					}
				}
            }

            for( $i=0; $i < count($arregloPromedios); $i++ ){
                    // echo $arregloPromedios[$i]." <br />";
                    // echo $arregloRegistros[$i]." <br /><br />";

                    $tiempos = new SeleccionTiempo();
                    $k ++;
                    if ( ( $k % 35 ) == 0 ){
                            $hora ++;
                            if ( $hora == 23 ){
                                    $dia++;
                                    $hora = 7;
                            }
                            if ( $hora < 10 ){
                                    $cero = 0;
                                    $hora = $cero.$hora;
                            }
                    }

                    $arregloPromedios[$i] = (int) $arregloPromedios[$i];
                    if ( $arregloPromedios[$i] == 0 || $arregloPromedios[$i] == "" )
                            $arregloPromedios[$i] = '0';
                    $tiempos -> id = 'default';
                    $tiempos -> registro = $arregloRegistros[$i];
                    $tiempos -> promedio = $arregloPromedios[$i];
                    $hinicio = '2013-01-'.$dia.' '.$hora.':00:00';
                    $hfin = '2013-01-27 23:59:59';
                    $tiempos -> inicio = $hinicio;
                    $tiempos -> fin = $hfin;
                    $tiempos -> periodo = 32012;

					$tiempos -> create();
            }
        } // function llenarSeleccionTiempoPara12013IrregularesTonala()
		
		function llenarSeleccionTiempoPara32012Condicionados(){ // -> (Y)
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			$Alumnos = new Alumnos();
			foreach ( $Alumnos -> find_all_by_sql ( "
					Select distinct miReg
					from alumnos
					where stSit = 'OK'
					and enTipo = 'C'" ) as $alumno ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 32012
						and registro = ".$alumno -> miReg ) ){
					// Si entra significa que si existe y no es necesario volver a ponerlo
					continue;
				}
				$SeleccionTiempo = new SeleccionTiempo();
				$SeleccionTiempo -> id = 'default';
				$SeleccionTiempo -> registro = $alumno->miReg;
				$SeleccionTiempo -> promedio = '0';
				$hinicio = '2012-07-30 08:00:00';
				$hfin = '2012-07-31 23:59:59';
				$SeleccionTiempo -> inicio = $hinicio;
				$SeleccionTiempo -> fin = $hfin;
				$SeleccionTiempo -> periodo = 32012;
				
				$SeleccionTiempo -> create();
			}
		} // llenarSeleccionTiempoPara32012Condicionados()
		
		function llenarSeleccionTiempoPara32012Intersemestrales(){ // -> (Y)
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			$IntersemestralAlumnos = new IntersemestralAlumnos();
			foreach ( $IntersemestralAlumnos -> find_all_by_sql ( "
					select * from intersemestral_alumnos
					where periodo = 22012
					group by registro" ) as $alumno ){
				$seleccTiempoo2 = new SeleccionTiempo();
				if( $seleccTiempoo2 -> find_first( "periodo = 32012
						and registro = ".$alumno -> registro ) ){
					$seleccTiempoo2 -> promedio = '0';
					$hinicio = '2012-07-30 08:00:00';
					$hfin = '2012-07-31 23:59:59';
					$seleccTiempoo2 -> inicio = $hinicio;
					$seleccTiempoo2 -> fin = $hfin;
					$seleccTiempoo2 -> periodo = 32012;
					
					$seleccTiempoo2 -> update();
				}
				else{
					$SeleccionTiempo = new SeleccionTiempo();
					$SeleccionTiempo -> id = 'default';
					$SeleccionTiempo -> registro = $alumno->registro;
					$SeleccionTiempo -> promedio = '0';
					$hinicio = '2012-07-30 08:00:00';
					$hfin = '2012-07-31 23:59:59';
					$SeleccionTiempo -> inicio = $hinicio;
					$SeleccionTiempo -> fin = $hfin;
					$SeleccionTiempo -> periodo = 32012;
					
					$SeleccionTiempo -> create();
				}
			}
		} // llenarSeleccionTiempoPara32012Intersemestrales()
		
		function corregirAgendaDeAlumnos12013(){
			/*
				Se abrió la agenda para alumnos que no debían tener derecho.
			*/
			
			$Alumnos = new Alumnos();
			
			foreach( $Alumnos->find_all_by_sql("
					select al.*, sl.id slid
					from alumnos al
					join seleccion_tiempo sl
					on al.miReg = sl.registro
					and sl.periodo = 12013
					and al.stSit = 'BD'") as $alumno ){
				$SeleccionTiempo = new SeleccionTiempo();
				
				$sl = $SeleccionTiempo -> find_first("id = '".$alumno->slid."'");
				
				$sl->delete();
			}
			
		} // function corregirAgendaDeAlumnos12013()
		
        function pasarAKardexIng2(){
            $xalumnocursos	= new Xalumnocursos();
            $xacursos		= new Xalumnocursos();
            $xcursos		= new Xcursos();
            $materiasing	= new materiasing();

            $kardIng = new KardexIng();

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

/*
            select c32008.registro as registro32008, c32008.materia as materia32008,
                    c32008.situacion as situacion32008, c32008.id as id12008, c12009.registro as registro12009,
                    c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009
*/
            $day = date ("d");
            $month = date ("m");
            $year = date ("Y");
            $date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

            foreach ( $xalumnocursos -> find_all_by_sql("
                                                            Select xal.registro, xc.materia, xal.periodo,
                                                            xal.situacion, xal.calificacion
                                                            from xalumnocursos xal, xcursos xc
                                                            where xal.periodo = ".$this -> actual."
                                                            and xal.calificacion >= 70
                                                            and xal.calificacion <= 100
                                                            and xal.curso = xc.clavecurso" )as $tmp ){
            /*
            registro  clavemat  nivel  periodo  tipo_de_ex  promedio  fecha_reg
            */

                    if ( $king = $kardIng -> find_first("registro= ".$tmp -> registro.
                                    " and clavemat= '".$tmp -> materia."'
                                                    and periodo= ".$tmp -> periodo ) ){
                    }
                    else{
                            $kardIng -> registro = $tmp -> registro;
                            $kardIng -> clavemat = $tmp -> materia;
                            if( $mating = $materiasing -> find_first("clavemat= '".$tmp -> materia."'" ) )
                                    $kardIng -> nivel = $tmp -> nivel;
                            else
                                    $kardIng -> nivel = '0';
                            $kardIng -> periodo = $tmp -> periodo;
                            $kardIng -> promedio = $tmp -> calificacion;
                            $kardIng -> fecha_reg = $date1;

                            if ( $tmp -> situacion == "ORDINARIO" )
                                    $tipoDeEx = 'D';
                            if ( $tmp -> situacion == "EXTRAORDINARIO" ||
                                            $tmp -> situacion == "EXTRAORDINARIO POR FALTAS" )
                                    $tipoDeEx = 'E';
                            if ( $tmp -> situacion == "REGULARIZACION" ||
                                            $tmp -> situacion == "REGULARIZACION DIRECTA" )
                                    $tipoDeEx = 'R';
                            if ( $tmp -> situacion == "TITULO DE SUFICIENCIA" ||
                                            $tmp -> situacion == "TITULO" )
                                    $tipoDeEx = 'T';
                            if ( $tmp -> situacion == "PROCESO" )
                                    $tipoDeEx = 'T';

                            $kardIng -> tipo_de_ex = $tipoDeEx;
                            $kardIng -> create();
                    }
            }
        } // function pasarAKardexIng2()

        function llenarExtraordinariosDel32011Colomos(){ // Chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos = new Xalumnocursos();
            $xacursos = new Xalumnocursos();
            $alumnos = new Alumnos();
            $xextras = new Xextraordinarios();
			$this -> actual = 32011;
            // id, clavecurso, registro, calificacion, estado, tipo, periodo

            foreach( $xacursos -> find_all_by_sql
				 ( "Select * from xalumnocursos
					where periodo = ".$this -> actual."
					and ( situacion ='EXTRAORDINARIO FALTAS'
					or situacion ='TITULO FALTAS'
					or situacion ='EXTRAORDINARIO'
					or situacion ='TITULO DE SUFICIENCIA' )" ) as $xacurso ){
				if( $xextras -> find_first( "registro = ".$xacurso -> registro.
											" and periodo = ".$xacurso -> periodo.
												" and clavecurso = '".$xacurso -> curso."'" ) ){
					continue;
				}
				$xextras -> clavecurso = $xacurso -> curso;
				$xextras -> registro = $xacurso -> registro;
				$xextras -> calificacion = '300';
				if( $xacurso -> situacion == "EXTRAORDINARIO" ||
						$xacurso -> situacion == "EXTRAORDINARIO FALTAS" )
					$situacion = "E";
				else{
					if( $xacurso -> situacion == "TITULO DE SUFICIENCIA" ||
							$xacurso -> situacion == "TITULO FALTAS" )
						$situacion = "T";
					else
						continue;
				}
				$xextras -> estado = "?";
				$xextras -> tipo = $situacion;
				$xextras -> periodo = $xacurso -> periodo;
				$xextras -> create();
            }
        } // function llenarExtraordinariosDel32011Colomos()

        function llenarExtraordinariosDel32011Tonala(){ // Chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xtalumnocursos = new Xtalumnocursos();
            $xtacursos = new Xtalumnocursos();
            $alumnos = new Alumnos();
            $xtextras = new Xtextraordinarios();
			$this -> actual = 32011;
            // id, clavecurso, registro, calificacion, estado, tipo, periodo
            foreach( $xtacursos -> find_all_by_sql
					 ( "Select * from xtalumnocursos
						where periodo = ".$this -> actual."
						and ( situacion ='EXTRAORDINARIO FALTAS'
						or situacion ='TITULO FALTAS'
						or situacion ='EXTRAORDINARIO'
						or situacion ='TITULO DE SUFICIENCIA' )" ) as $xtacurso ){
				if( $xtextras -> find_first( "registro = ".$xtacurso -> registro.
											" and periodo = ".$xtacurso -> periodo.
												" and clavecurso = '".$xtacurso -> curso."'" ) ){
					continue;
				}
				$xtextras -> clavecurso = $xtacurso -> curso;
				$xtextras -> registro = $xtacurso -> registro;
				$xtextras -> calificacion = '300';
				if( $xtacurso -> situacion == "EXTRAORDINARIO" ||
						$xtacurso -> situacion == "EXTRAORDINARIO FALTAS" )
					$situacion = "E";
				else{
					if( $xtacurso -> situacion == "TITULO DE SUFICIENCIA" ||
							$xtacurso -> situacion == "TITULO FALTAS" )
						$situacion = "T";
					else
						continue;
				}
				$xtextras -> estado = "?";
				$xtextras -> tipo = $situacion;
				$xtextras -> periodo = $xtacurso -> periodo;
				$xtextras -> create();
            }
        } // function llenarExtraordinariosDel12011Tonala()

        function pasarDeKardexIng32009ADatosKardexIng(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $kardexing		= new KardexIng();
            $kardexing2		= new KardexIng2();

            // registro, clavemat, nivel, periodo, tipo_de_ex, promedio, fecha_reg, id
            foreach( $kardexing2 -> find_all_by_sql(
                    "Select * From kardex_ing2
                    where periodo = 32009
                    and (registro, clavemat, periodo) not in (
                      Select registro, clavemat, periodo
                      from kardex_ing where periodo = 32009
                    )" ) as $karding ){

                    $kardexing -> registro	= $karding -> registro;
                    $kardexing -> clavemat	= $karding -> clavemat;
                    $kardexing -> nivel		= $karding -> nivel;
                    $kardexing -> periodo	= $karding -> periodo;
                    $kardexing -> tipo_de_ex= $karding -> tipo_de_ex;
                    $kardexing -> promedio	= $karding -> promedio;
                    $kardexing -> fecha_reg	= $karding -> fecha_reg;

                    $kardexing -> create();
            }
        } // function pasarDeKardexIng32009ADatosKardexIng()

        function pasarDeKardexIng32009AEscolarKardexIng(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $kardexing		= new KardexIng();
            $kardexing2		= new KardexIng2();
            $kardexing22	= new KardexIng2();

            // registro, clavemat, nivel, periodo, tipo_de_ex, promedio, fecha_reg, id
            foreach( $kardexing -> find_all_by_sql(
                    "Select * From kardex_ing
                    where periodo = 32009" ) as $karding ){

                    if( $kardexing2 -> find_first
                                    ( "registro= ".$karding -> registro.
                                            " and clavemat = '".$karding -> clavemat."'".
                                                    " and periodo = ".$karding -> periodo ) ){
                            continue;
                    }
                    $kardexing22 -> registro	= $karding -> registro;
                    $kardexing22 -> clavemat	= $karding -> clavemat;
                    $kardexing22 -> nivel		= $karding -> nivel;
                    $kardexing22 -> periodo		= $karding -> periodo;
                    $kardexing22 -> tipo_de_ex	= $karding -> tipo_de_ex;
                    $kardexing22 -> promedio	= $karding -> promedio;
                    $kardexing22 -> fecha_reg	= $karding -> fecha_reg;

                    $kardexing22 -> create();
            }
        } // function pasarDeKardexIng32009AEscolarKardexIng()

        function sumarCalif1Calif2Calif3(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $xalumnocursos		= new Xalumnocursos();

            foreach( $xalumnocursos -> find
                            ( "periodo = ".$this -> actual ) as $xalumncur ) {

                    $calif1 = $xalumncur -> calificacion1;
                    $calif2 = $xalumncur -> calificacion2;
                    $calif3 = $xalumncur -> calificacion3;

                    $faltas1 = $xalumncur -> faltas1;
                    $faltas2 = $xalumncur -> faltas2;
                    $faltas3 = $xalumncur -> faltas3;

                    $faltas = ( $faltas1 + $faltas2 + $faltas3 );

                    if( $faltas == 0 || $faltas == "" )
                            $faltas = '0';

                    $xalumncur -> faltas = $faltas;

                    if( $calif1 <= 100 && $calif2 <= 100 && $calif3 <= 100 ){
                            $calif = ( ( $calif1 + $calif2 + $calif3 ) / 3 );
                            $xalumncur -> calificacion = $calif;
                    }
                    $xalumncur -> save();
            }

        } // function sumarCalif1Calif2Calif3()

        function calificarSituacion32009(){

            $xalumnocursos	= new Xalumnocursos();
            $xacursos		= new Xalumnocursos();

            foreach ( $xalumnocursos -> find
                            ( "periodo = ".$this -> actual ) as $xalumcur ){

                    if( $xalumcur -> calificacion <= 100 ){
                            if( $xalumcur -> calificacion >= 70 ){
                                    foreach( $xacursos -> find("id= ".$xalumcur -> id) as $xalcur ){
                                            $xalcur -> situacion = "ORDINARIO";
                                            $xalcur -> save();
                                            continue;
                                    }
                            }
                            else{
                                    foreach ( $xacursos -> find("id= ".$xalumcur -> id) as $xalcur ){
                                            $xalcur -> situacion = "EXTRAORDINARIO";
                                            $xalcur -> save();
                                            continue;
                                    }
                            }
                    }
            }
        } // function calificarSituacion32009()

        function seleccionTiempoTonala(){

            $seleccionTiempo	= new SeleccionTiempo();
            $alumnos			= new Alumnos();

            foreach( $alumnos -> find( "enPlantel = 'N'" ) as $alumn ){
                    $seleccionTiempo -> registro = $alumn -> miReg;
                    $seleccionTiempo -> promedio = 80;
                    $seleccionTiempo -> inicio = "2010-01-20 09:00:00";
                    $seleccionTiempo -> fin = "2010-01-26 23:59:59";
                    $seleccionTiempo -> periodo = 12010;
                    $seleccionTiempo -> create();
            }

        } //function seleccionTiempoTonala()

        function PonerTitulosEnExtraordinariosQueCursaronEn12010YNoCargaronEn32010Colomos(){ // <- El chido

            $xcursos2			= new Xccursos();
            $materias2			= new Materia();
            $xalumnocursos2		= new Xalumnocursos();
            $xextraordinarios2	= new Xextraordinarios();
            $alumnos2			= new Alumnos();
            $maestros2			= new Maestros();
            $kardexing			= new KardexIng();

            $i = 0;
				foreach( $alumnos2 -> find_all_by_sql
					( "Select * from alumnos where pago = 1" ) as $alumnooo ){

				$id = $alumnooo -> miReg;

				if( $alumnooo -> miPerIng != $this -> actual ){

					foreach( $xalumnocursos2 -> find_all_by_sql
							 ( "Select xc.materia, xc.nomina, xal.*
								from xalumnocursos xal, xccursos xc
								where xal.curso = xc.clavecurso
								and xal.periodo = ".$this -> anterior."
								and xal.registro = ".$id."
								and xal.calificacion < 70" ) as $xalumncur2 ){
							//echo "WTF: ".$xalumncur2 -> curso." ".$xalumncur2 -> materia." m<br />";
						
						if( $xextraordinarios2 -> find_first
							( "clavecurso = '".$xalumncur2 -> curso."'".
									" and registro = ".$id.
											" and ( calificacion < 70 ".
													" or calificacion > 100 )" ) ){
							//echo "Xtra<br />";
							if( $xalcur = $xalumnocursos2 -> find_all_by_sql
								 ( "Select xc.materia, xal.*
									from xalumnocursos xal, xccursos xc
									where xal.curso = xc.clavecurso
									and xal.periodo = ".$this -> actual."
									and xc.materia = '".$xalumncur2 -> materia."'"."
									and xal.registro = ".$id ) ){
								// Si la encuentra quiere decir que si la cargo,
								//por lo que no es necesario ponerla aqui
								//ya que, ya fue detectada con anterioridad.

								//echo "No cae: ".$xalcur -> materia." ".$xalcur -> curso."<br />";
							}
							else{
								//echo "si cae<br />";
								if( $xalumnocursos2 -> find_all_by_sql
									  ( "Select xc.materia, xal.* from xalumnocursos xal, xccursos xc
										where xal.curso = xc.clavecurso
										and xc.materia = '".$xalumncur2 -> materia."'"."
										and xal.registro = ".$id."
										and xal.periodo = ".$this -> antesAnterior ) ){
									// Si encuentra información, significa que también
									//había cursado esa materia 2 periodos antes del actual
									//por lo que ya no tendrá oportunidad de imprimir su
									//titulo de suficiencia
								}
								else{
									if( $kardexing -> find_first
										( "clavemat = '".$xalumncur2 -> materia."'"."
											and registro = ".$id ) ){
										// Si encuentra la materia en el kardex del alumno
										//no es necesario presentarsela, ya que ya la curso.
									}
									else{
										/*
										$this -> titulosAnteriores[$i] = $xalumncur2;
										$this -> titulosAntMateria[$i] =
														$materias2 -> find_first( "clave = '".$xalumncur2 -> materia."'" );
										$this -> titulosAntProf[$i] =
														$maestros2 -> find_first( "nomina = ".$xalumncur2 -> nomina );
										$i ++;
										*/
										// id, clavecurso, registro, califiacion, estado, tipo, periodo
										$xextraordinarios2 -> clavecurso = $xalumncur2 -> curso;
										$xextraordinarios2 -> registro = $id;
										$xextraordinarios2 -> calificacion = "300";
										$xextraordinarios2 -> estado = "?";
										$xextraordinarios2 -> tipo = "T";
										$xextraordinarios2 -> periodo = $this -> actual;
										$xextraordinarios2 -> create();
									}
								}
							}
						}
					}
				} // if( $alumnooo -> miPerIng != $this -> actual )
            }
        } // function PonerTitulosEnExtraordinariosQueCursaronEn12010YNoCargaronEn32010Colomos()

       function PonerTitulosEnExtraordinariosQueCursaronEn12010YNoCargaronEn32010Tonala(){ // <- El chido

            $xcursos2			= new Xtcursos();
            $materias2			= new Materia();
            $xalumnocursos2		= new Xtalumnocursos();
            $xextraordinarios2	= new Xtextraordinarios();
            $alumnos2			= new Alumnos();
            $maestros2			= new Maestros();
            $kardexing			= new KardexIng();

            $i = 0;
				foreach( $alumnos2 -> find_all_by_sql
					( "Select * from alumnos where pago = 1" ) as $alumnooo ){

				$id = $alumnooo -> miReg;

				if( $alumnooo -> miPerIng != $this -> actual ){

					foreach( $xalumnocursos2 -> find_all_by_sql
							 ( "Select xc.materia, xc.nomina, xal.*
								from xtalumnocursos xal, xtcursos xc
								where xal.curso = xc.clavecurso
								and xal.periodo = ".$this -> anterior."
								and xal.registro = ".$id."
								and xal.calificacion < 70" ) as $xalumncur2 ){
							//echo "WTF: ".$xalumncur2 -> curso." ".$xalumncur2 -> materia." m<br />";
						
						if( $xextraordinarios2 -> find_first
							( "clavecurso = '".$xalumncur2 -> curso."'".
									" and registro = ".$id.
											" and ( calificacion < 70 ".
													" or calificacion > 100 )" ) ){
							//echo "Xtra<br />";
							if( $xalcur = $xalumnocursos2 -> find_all_by_sql
								 ( "Select xc.materia, xal.*
									from xtalumnocursos xal, xtcursos xc
									where xal.curso = xc.clavecurso
									and xal.periodo = ".$this -> actual."
									and xc.materia = '".$xalumncur2 -> materia."'"."
									and xal.registro = ".$id ) ){
								// Si la encuentra quiere decir que si la cargo,
								//por lo que no es necesario ponerla aqui
								//ya que, ya fue detectada con anterioridad.

								//echo "No cae: ".$xalcur -> materia." ".$xalcur -> curso."<br />";
							}
							else{
								//echo "si cae<br />";
								if( $xalumnocursos2 -> find_all_by_sql
									  ( "Select xc.materia, xal.* from xtalumnocursos xal, xtcursos xc
										where xal.curso = xc.clavecurso
										and xc.materia = '".$xalumncur2 -> materia."'"."
										and xal.registro = ".$id."
										and xal.periodo = ".$this -> antesAnterior ) ){
									// Si encuentra información, significa que también
									//había cursado esa materia 2 periodos antes del actual
									//por lo que ya no tendrá oportunidad de imprimir su
									//titulo de suficiencia
								}
								else{
									if( $kardexing -> find_first
										( "clavemat = '".$xalumncur2 -> materia."'"."
											and registro = ".$id ) ){
										// Si encuentra la materia en el kardex del alumno
										//no es necesario presentarsela, ya que ya la curso.
									}
									else{
										/*
										$this -> titulosAnteriores[$i] = $xalumncur2;
										$this -> titulosAntMateria[$i] =
														$materias2 -> find_first( "clave = '".$xalumncur2 -> materia."'" );
										$this -> titulosAntProf[$i] =
														$maestros2 -> find_first( "nomina = ".$xalumncur2 -> nomina );
										$i ++;
										*/
										// id, clavecurso, registro, califiacion, estado, tipo, periodo
										$xextraordinarios2 -> clavecurso = $xalumncur2 -> curso;
										$xextraordinarios2 -> registro = $id;
										$xextraordinarios2 -> calificacion = "300";
										$xextraordinarios2 -> estado = "?";
										$xextraordinarios2 -> tipo = "T";
										$xextraordinarios2 -> periodo = $this -> actual;
										$xextraordinarios2 -> create();
									}
								}
							}
						}
					}
				} // if( $alumnooo -> miPerIng != $this -> actual )
            }
        } // function PonerTitulosEnExtraordinariosQueCursaronEn12010YNoCargaronEn32010Tonala()

        function corregirCalifDeExtraSiEsLaMismaEnXalumnocursos(){

            $xalumnocursos		= new Xalumnocursos();
            $xcursos			= new Xalumnocursos();
            $xpermisoscaptura	= new Xpermisoscaptura();
            $xextraordinarios	= new Xextraordinarios();

            foreach( $xalumnocursos -> find( "periodo = ".$this -> actual ) as $xalumncur ){
                    $xextra = $xextraordinarios -> find_first
                                    ( "clavecurso = '".$xalumncur -> curso."'"."
                                            and periodo = ".$this -> actual."
                                                    and registro = ".$xalumncur -> registro );
                    $ok = 0;
                    if( $xpermisoscaptura -> find_first( "ncapturas4 = 0" ) && $xextra -> tipo == "E" )
                            $ok++;
                    if( $xpermisoscaptura -> find_first( "ncapturas5 = 0" ) && $xextra -> tipo == "T" )
                            $ok++;

                    if( $xextra -> calificacion == $xalumncur -> calificacion ){
                            $xextra -> calificacion = '0';
                            $xextra -> save();
                    }
            }
        } // function corregirCalifDeExtraSiEsLaMismaEnXalumnocursos()

        function ponerPermisosenXPermLosTitulosRaros(){

            $xalumnocursos		= new Xalumnocursos();
            $xcursos			= new Xalumnocursos();
            $xpermisosc			= new Xpermisoscaptura();
            $xextraordinarios	= new Xextraordinarios();

            foreach( $xextraordinarios -> find_all_by_sql
					( "Select xext.registro, xc.clavecurso
						from xextraordinarios xext, xccursos xc
						where xext.clavecurso = xc.clavecurso
						and xext.periodo = ".$this -> actual."
						and xext.tipo = 'T'
						and ( xc.periodo = ".$this -> anterior."
						or xc.periodo = ".$this -> antesAnterior." )
						group by clavecurso" ) as $xextra ){


					$xpermisosc -> curso = $xextra -> clavecurso;
					$xpermisosc -> periodo = $this -> actual;

					$xpermisosc -> ncapturas1 = 'default';
					$xpermisosc -> maxcapturas1 = 'default';
					$xpermisosc -> activa1 = '0';
					$xpermisosc -> inicio1 = '0';
					$xpermisosc -> fin1 = '0';

					$xpermisosc -> ncapturas2 = 'default';
					$xpermisosc -> maxcapturas2 = 'default';
					$xpermisosc -> activa2 = 'default';
					$xpermisosc -> inicio2 = '0';
					$xpermisosc -> fin2 = '0';

					$xpermisosc -> ncapturas3 = 'default';
					$xpermisosc -> maxcapturas3 = 'default';
					$xpermisosc -> activa3 = 'default';
					$xpermisosc -> inicio3 = '0';
					$xpermisosc -> fin3 = '0';

					$xpermisosc -> ncapturas4 = '0';
					$xpermisosc -> maxcapturas4 = 1;
					$xpermisosc -> activa4 = 1;
					$xpermisosc -> inicio4 = '1278309600';
					$xpermisosc -> fin4 = '1278910800';

					$xpermisosc -> ncapturas5 = '0';
					$xpermisosc -> maxcapturas5 = 1;
					$xpermisosc -> activa5 = 1;
					$xpermisosc -> inicio5 = '1278309600';
					$xpermisosc -> fin5 = '1278910800';

					$xpermisosc -> create();
            }

        } // function ponerPermisosenXPermLosTitulosRaros()

        function borrarDoblesEnSeleccionTiempo(){

            $seleccTiempo	= new SeleccionTiempo();
            $seleccTime		= new SeleccionTiempo();
            $alumnos		= new Alumnos();

            foreach( $seleccTiempo -> find_all_by_sql
					( "select * from (
						Select count(*) as cuenta, tabla1.reg, tabla1.idd, tabla1.finn from
							(
							  Select id as idd, registro as reg, fin as finn From seleccion_tiempo
							  where periodo = 32010
							) tabla1
						group by tabla1.reg
						)oraa
					where oraa.cuenta > 1" ) as $sTiempo ){

				$seleccTime -> id = $sTiempo  -> idd;
				$seleccTime -> delete();
            }

        } // function borrarDoblesEnSeleccionTiempo()

        function borrarDeSeleccionTiempoLosQueNoTenganCalifEnTitulo(){

            $xextraordinario	= new Xextraordinarios();
            $xalumnocursos		= new Xalumnocursos();
            $seleccionTiempo	= new SeleccionTiempo();

            foreach( $xalumnocursos -> find_all_by_sql
                             ( "Select xal.registro from xalumnocursos xal,
                                    xextraordinarios xte
                                    where xal.registro = xte.registro
                                    and xal.periodo = 32009
                                    and ( xal.situacion = \"TITULO DE SUFICIENCIA\"
                                    or xal.situacion = \"TITULO FALTAS\" )
                                    and xte.calificacion < 70
                                    group by xal.registro" ) as $xalcur ){
                    if( $seleccTiempo = $seleccionTiempo -> find_first
                                    ( "registro = ".$xalcur -> registro."
                                            and periodo = ".$this -> proximo ) ){
                            $seleccTiempo -> delete();
                    }
            }
        } // function borrarDeSeleccionTiempoLosQueNoTenganCalifEnTitulo()

        function pasarAKardexIngLosAlumnosDeXtAlumnocursosQueHayanPasado(){

            $xalumnocursos		= new Xalumnocursos();
            $alumnos			= new Alumnos();
            $kardIng			= new KardexIng();
            $xacursos			= new Xtalumnocursos();
            $xcursos			= new Xtcursos();
            $materiasing		= new materiasing();

            $day = date ("d");
            $month = date ("m");
            $year = date ("Y");
            $date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

            foreach ( $xalumnocursos -> find_all_by_sql("
                                                            Select xal.registro, xc.materia, xal.periodo,
                                                            xal.situacion, xal.calificacion
                                                            from xtalumnocursos xal, xtcursos xc
                                                            where xal.periodo = ".$this -> actual."
                                                            and xal.calificacion >= 70
                                                            and xal.calificacion <= 100
                                                            and xal.situacion = \"ORDINARIO\"
                                                            and xal.curso = xc.clavecurso" )as $tmp ){
            /*
            registro  clavemat  nivel  periodo  tipo_de_ex  promedio  fecha_reg
            */

                    if ( $king = $kardIng -> find_first("registro= ".$tmp -> registro.
                                    " and clavemat= '".$tmp -> materia."'
                                                    and periodo= ".$tmp -> periodo ) ){
                            echo "Reg: ".$tmp -> registro." clavemat: ".
                                            $tmp -> materia." periodo ".$tmp -> periodo."<br />";
                    }
                    else{
                            $kardIng -> registro = $tmp -> registro;
                            $kardIng -> clavemat = $tmp -> materia;
                            if( $mating = $materiasing -> find_first("clavemat= '".$tmp -> materia."'" ) )
                                    $kardIng -> nivel = $tmp -> nivel;
                            else
                                    $kardIng -> nivel = '0';
                            $kardIng -> periodo = $tmp -> periodo;
                            $kardIng -> promedio = $tmp -> calificacion;
                            $kardIng -> fecha_reg = $date1;

                            if ( $tmp -> situacion == "ORDINARIO" )
                                    $tipoDeEx = 'D';
                            if ( $tmp -> situacion == "EXTRAORDINARIO" ||
                                            $tmp -> situacion == "EXTRAORDINARIO POR FALTAS" )
                                    $tipoDeEx = 'E';
                            if ( $tmp -> situacion == "REGULARIZACION" ||
                                            $tmp -> situacion == "REGULARIZACION DIRECTA" )
                                    $tipoDeEx = 'R';
                            if ( $tmp -> situacion == "TITULO DE SUFICIENCIA" ||
                                            $tmp -> situacion == "TITULO" )
                                    $tipoDeEx = 'T';
                            if ( $tmp -> situacion == "PROCESO" )
                                    $tipoDeEx = 'T';

                            $kardIng -> tipo_de_ex = $tipoDeEx;
                            $kardIng -> create();

                    }
            }

        } // function pasarAKardexIngLosAlumnosDeXtAlumnocursosQueHayanPasado()

        function InscribirALosDePrimero12012(){ // El Chido

            if(Session::get_data('tipousuario')!="CALCULO"){
				//$this->redirect('/');
            }

            $alumnosAdmitidos	= new Admitidos2();
            $xccursos			= new Xccursos();
            $xalumnocursos		= new Xalumnocursos();
            $xchorascursos		= new Xchorascursos();
            $xcsalones			= new Xcsalones();
            $autorizarCru		= new AutorizarCruces();
            $cursoscomunes		= new CursosComunes();

            $counter = 0;
            $yaestabainscrito = 0;

            $periodo = 12012;

//			$CLAVECURSO = "TCB1925";
            $CLAVECURSO = $this -> post ("clavecurso");
            $xccurso = $xccursos -> find_first("clavecurso= '".$CLAVECURSO."'");

            $flag = 0;

            $err = "";

            // $ocupado me sirve para saber si a esa hora y día el alumno tiene
            //espacio libre o no.
            $ocupado = 0;
            // Ir cambiando el ID de carrera para ir llenando los salones.
            // También debo limitar el tamaño de los grupos...

            //$alumnos[0] = 10110257;
            //$alumnos[1] = 10110261;
            //$alumnos[2] = 10110269;

            foreach( $alumnosAdmitidos -> find_all_by_sql
                             ( "select * from admitidos2
								where carrera_id = 8
								and periodo = 12012
								and plantel = 'C'
								limit 0, 42" ) as $alumno ){

            //foreach( $alumnos as $alumno ){
				$id	= $alumno -> registro;
				//$id	= $alumno;
				// Variable auxiliar, para saber cuantos cruces tuvo el último alumno
				$ocupadoFinal = 0;

				$autCruce = $autorizarCru -> find_first
						( "registro = ".$id."
								and clavecurso = '".$xccurso -> clavecurso."'" );

				if( $xalumnocursos -> find_first( "registro = ".$id."
						and curso = '".$xccurso -> clavecurso."'") ){
					$yaestabainscrito++;
					continue;
				}

				if( $xccurso -> disponibilidad <= 0 ){
					// El 1 significa que el curso no tiene cupos disponibles
					$err .=  "No hay espacio disponible<br />";
					return;
				}
				else{
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
								$err.= "Cruce con el curso: ".$xchorascurso -> clavecurso."<br />";
								$err.= "El alumno ".$id." tiene cruce el dia: ".$xchorascurso -> dia;
								$err.= "&nbsp;a la hora: ".$xchorascurso -> hora."<br />";
								$ocupado++;
								$ocupadoFinal++;
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
						if( $xccurso -> disponibilidad == null )
							$xccurso -> disponibilidad = '0';
						$xccurso -> save();
						$counter++;
					}
					else{
						if( isSet($autCruce -> clavecurso) &&
								$autCruce -> clavecurso == $xccurso -> clavecurso &&
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
							$counter++;
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
								$counter++;
							}
							else{
								$err .= "El alumno ".$id." no tiene espacio libre<br />";
							}
						}
					}
				}
			// }
			}
            echo "Curso al que se quiere inscribir: ".$CLAVECURSO."<br />";
            echo "Contador de los que se inscribieron ".$counter."<br />";
            echo "Contador de los que ya estaban inscritos ".$yaestabainscrito."<br />";
            echo "Existen cruces en: ".$err."<br />";
            echo "Ocupado ".$ocupado." Ocupado Final: ".$ocupadoFinal;

        } // function InscribirALosDePrimero12012()

        function crearCuentasDePrimerIngreso12010EnLaTablaDeUsuarios(){

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            $alumnosAdmitidos	= new Admitidos2();
            $usuarios			= new usuarios();

            /*
            Select * From usuarios
            where categoria = 1;
            -- id
            -- login
            -- paswwd
            -- categoria
            -- registro
            -- clave
            -- tipousuario
            -- (default, registro, md5(registro), 1, registro, registro, 0);
            */
            $counter = 0;
            foreach( $alumnosAdmitidos -> find_all_by_sql("Select * From admitidos2") as $alAdmitidos ){

                    if( $usuarios -> find_first( "registro = '".$alAdmitidos -> registro."'" ) ){
                            echo "El usuario ".$alAdmitidos -> registro." ya existía<br />";
                    }
                    else{
                            $pwd = "";
                            $pwd .= MD5("'".$alAdmitidos -> registro."'");

                            $usuarios -> paswwd		= $pwd;
                            $usuarios -> categoria	= 1;
                            $usuarios -> registro	= $alAdmitidos -> registro;
                            $usuarios -> clave		= $alAdmitidos -> registro;
                            $usuarios -> tipousuario= '0';
                            $usuarios -> create();
                            $counter ++;
                    }
            }
            echo "Se crearon ".$counter." nuevas cuentas";
        } // function crearCuentasDePrimerIngreso12010EnLaTablaDeUsuarios()

        function corregirElCupoEnXCcursos(){ // El chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

            //$periodo = $this -> actual;
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();

            $xcursos = new Xccursos();
            $xalumnocursos = new Xalumnocursos();

			foreach ( $xcursos -> find ( "periodo = '".$periodo."'" ) as $xcur ){
				$flag = 0;
				foreach ( $xalumnocursos -> find_all_by_sql ( "
						Select count(registro) counter, curso_id from xalumnocursos
						where periodo = ".$periodo."
						and curso_id = '".$xcur->id."'
						group by curso_id" ) as $xalumncur ){
					if ( $xalumncur -> counter > 40 ){
							$xcur -> cupo = $xalumncur -> counter;
							$xcur -> disponibilidad = '0';
							$xcur -> save();
							echo "<br />Se modifico el siguiente curso = ".$xcur -> clavecurso."<br />";
					}
					else if( $xalumncur -> counter != ($xcur -> cupo - $xcur -> disponibilidad) ){
							$xcur -> disponibilidad = ( $xcur -> cupo - $xalumncur -> counter );
							if($xcur -> disponibilidad == 0)
								$xcur -> disponibilidad = '0';
							if($xcur -> cupo > 40 && $xcur -> disponibilidad > 0)
								$xcur -> cupo = 40;
							$xcur -> save();
							echo "<br />El curso = ".$xcur -> clavecurso."
											tenía mal cuantos espacios disponibles hay en verdad<br />";
					}
					$flag++;
				}
				if($flag==0){
					$xcur -> disponibilidad = $xcur -> cupo;
					if($xcur -> disponibilidad == 0)
						$xcur -> disponibilidad = '0';
					$xcur -> save();
					echo "<br />El curso = ".$xcur -> clavecurso."
									tenía mal cuantos espacios disponibles hay en verdad<br />";
				}
            }
        } // function corregirElCupoEnXCcursos()
		
        function corregirElCupoEnXTcursos(){ // El chido

            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }

			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();

            $xcursos = new Xtcursos();
            $xalumnocursos = new Xtalumnocursos();

			foreach ( $xcursos -> find ( "periodo = '".$periodo."'" ) as $xcur ){
				$flag = 0;
				foreach ( $xalumnocursos -> find_all_by_sql ( "
						Select count(registro) counter, curso_id from xtalumnocursos
						where periodo = ".$periodo."
						and curso_id = '".$xcur->id."'
						group by curso_id" ) as $xalumncur ){
					if ( $xalumncur -> counter > 40 ){
							$xcur -> cupo = $xalumncur -> counter;
							$xcur -> disponibilidad = '0';
							$xcur -> save();
							echo "<br />Se modifico el siguiente curso = ".$xcur -> clavecurso."<br />";
					}
					else if( $xalumncur -> counter != ($xcur -> cupo - $xcur -> disponibilidad) ){
							$xcur -> disponibilidad = ( $xcur -> cupo - $xalumncur -> counter );
							if($xcur -> disponibilidad == 0)
								$xcur -> disponibilidad = '0';
							if($xcur -> cupo > 40 && $xcur -> disponibilidad > 0)
								$xcur -> cupo = 40;
							$xcur -> save();
							echo "<br />El curso = ".$xcur -> clavecurso."
											tenía mal cuantos espacios disponibles hay en verdad<br />";
					}
					$flag++;
				}
				if($flag==0){
					$xcur -> disponibilidad = $xcur -> cupo;
					if($xcur -> disponibilidad == 0)
						$xcur -> disponibilidad = '0';
					$xcur -> save();
					echo "<br />El curso = ".$xcur -> clavecurso."
									tenía mal cuantos espacios disponibles hay en verdad<br />";
				}
            }

        } // function corregirElCupoEnXTcursos()
		
        function corregirElCupoEnIntersemestrales(){ // El chido

            if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
            }

            $periodo = $this -> actual;

            $IntersemestralCursos = new IntersemestralCursos();
            $IntersemestralAlumnos = new IntersemestralAlumnos();

            $flag = 0;
            foreach ( $IntersemestralAlumnos -> find_all_by_sql ( "
                            Select count(registro) counter, clavecurso
							from intersemestral_alumnos
                            where periodo = 42011
                            group by clavecurso" ) as $InterAlumnos ){
                    foreach ( $IntersemestralCursos -> find ( "clavecurso = '".$InterAlumnos -> clavecurso."'" ) as $InterCursos ){
                            if ( $InterAlumnos -> counter > 40 ){
                                    $InterCursos -> cupo = $InterAlumnos -> counter;
                                    $InterCursos -> disponibilidad = '0';
                                    $InterCursos -> save();
                                    echo "<br />Se modifico el siguiente curso = ".$xcur -> clavecurso;
                            }
                            if( $InterAlumnos -> counter != ($InterCursos -> cupo - $InterCursos -> disponibilidad) ){
                                    $InterCursos -> disponibilidad = ( $InterCursos -> cupo - $InterAlumnos -> counter );
									if( $InterCursos -> disponibilidad == null || $InterCursos -> disponibilidad == 0 )
										$InterCursos -> disponibilidad = '0';
                                    $InterCursos -> save();
                                    echo "<br />El curso = ".$InterCursos -> clavecurso."
											tenía mal cuantos espacios disponibles hay en verdad";
                            }
                    }
            }

        } // function corregirElCupoEnIntersemestrales()

        function crearLosAlumnosAdmitidosEnAlumnos(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            $alumnos		= new Alumnos();
            $admitidos		= new Admitidos2();

            // id, enNivEdu "I", enPlantel, enTurno "V" "M", idtiEsp, tNivel,
            // chGpo "**", miReg, enTipo "R", stSit "OK", vcNomAlu, enSexo "H" "M",
            // "daFechNac", "miPerIng 12010, correo "", situacion "NUEVO INGRESO",
            // condonado 0, pago 0;

            // nombre_completo, plantel, carrera_id, registro

            // 12 Industrial  7
            // 13 Electrónica 6
            // 16 Mecatrónica 8

            foreach( $admitidos -> find_all_by_sql( "Select * from admitidos2" ) as $admitido ){
                    $alumnos -> enNivEdu	= "I";
                    $alumnos -> enPlantel	= $admitido -> plantel;
                    $alumnos -> enTurno		= "V";

                    if( $admitido -> carrera_id == 6 )
                            $alumnos -> idtiEsp = 13;
                    if( $admitido -> carrera_id == 7 )
                            $alumnos -> idtiEsp = 12;
                    if( $admitido -> carrera_id == 8 )
                            $alumnos -> idtiEsp = 16;

                    $alumnos -> tNivel		= 1;
                    $alumnos -> chGpo		= "**";
                    $alumnos -> miReg		= $admitido -> registro;
                    $alumnos -> enTipo 		= "R";
                    $alumnos -> stSit		= "OK";
                    $alumnos -> vcNomAlu	= $admitido -> nombre_completo;
                    $alumnos -> enSexo		= "H";
                    $alumnos -> daFechaNac	= "0000-00-00";
                    $alumnos -> miPerIng	= "12010";
                    $alumnos -> correo		= "";
                    $alumnos -> situacion	= "NUEVO INGRESO";
                    $alumnos -> condonado	= '0';
                    $alumnos -> pago		= '0';

                    $alumnos -> create();
            }

        } // function crearLosAlumnosAdmitidosEnAlumnos()

        function crearLasActasParaLasMateriasDelPlan2000YNoDejarlasJuntasConLasDelPlan2007(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            // Está función nace de una necesidad de crear actas independientes para
            //las materias del plan2000, esto gracias ah que se hizo un exámen minusioso
            //sobre los pros y contras de tener en una acta el plan2000 y el plan2007
            //y se opto por tener cursos independientes para cada acta.
            // Este cambio es un cambio bastante grande, pero se llevará a cabo con éxito
            //si se hace todo minusiosamente.

            $xccursos		= new Xccursos();
            $xccursos2		= new Xccursos();
            $xccursos3		= new Xccursos();
            $xccursos4		= new Xccursos();
            $xalumnocursos	= new Xalumnocursos();

            foreach( $xccursos -> find_all_by_sql
                             ( "Select * From xccursos
                                    where materia2000 <> -1
                                    and materia2007 <> -1" ) as $xcc ){

                    $auxInt = 0;
                    foreach( $xccursos4 -> find_all_by_sql
                                     ( "Select * from xalumnocursos xal, alumnos al
                                            where xal.registro = al.miReg
                                            and al.enPlan = \"PE00\"
                                            and xal.curso = '".$xcc -> clavecurso."'" ) as $xcc4 ){
                            $auxInt ++;
                    }

                    // Si no encuentra ningún alumno con plan2000 inscrito en este curso
                    //no tiene caso hacer un curso paralelo con plan2000, por lo que se procederá a eliminar
                    if( $auxInt == 0 )
                            continue;


                    $aux = "";
                    foreach( $xccursos2 -> find_all_by_sql( "Select max(id) as id from xccursos" ) as $xcc2 ){

                            $xccursos3 -> clavecurso 	= $xcc -> division.($xcc2 -> id + 1);
                            $xccursos3 -> paralelo		= $xcc -> clavecurso;
                            $xccursos3 -> materia		= $xcc -> materia2000;
                            echo $xcc -> materia2000."<br />";
                            echo $xccursos3 -> materia."<br />";
                            $xccursos3 -> materia2000	= "-1";
                            $xccursos3 -> materia2007	= "-1";
                            $xccursos3 -> nomina		= $xcc -> nomina;

                            foreach( $xalumnocursos -> find_all_by_sql
                                             ( "Select count(*) cuantosInscritos
                                                    from Xalumnocursos
                                                    where curso = '".$xcc -> clavecurso."'" ) as $xalcur ){
                                    $xccursos3 -> cupo			= $xalcur -> cuantosInscritos;
                                    $xccursos3 -> disponibilidad= '0';
                                    $xccursos3 -> minimo		= 1;
                                    $xccursos3 -> maximo		= $xalcur -> cuantosInscritos;
                            }

                            $xccursos3 -> activo		= $xcc -> activo;
                            $xccursos3 -> asesoria		= $xcc -> asesoria;
                            $xccursos3 -> periodo		= $xcc -> periodo;
                            $xccursos3 -> carrera		= $xcc -> carrera;
                            $xccursos3 -> curso			= $xcc -> division.($xcc2 -> id + 1);
                            $xccursos3 -> division		= $xcc -> division;
                            $xccursos3 -> horas1		= $xcc -> horas1;
                            $xccursos3 -> avance1		= $xcc -> avance1;
                            $xccursos3 -> horas2		= $xcc -> horas2;
                            $xccursos3 -> avance2		= $xcc -> avance2;
                            $xccursos3 -> horas3		= $xcc -> horas3;
                            $xccursos3 -> avance3		= $xcc -> avance;


                            if( $xccursos3 -> materia == NULL )
                                    $xccursos3 -> materia		= $xcc -> materia2000;

                            $xccursos3 -> create();

                            // La variable aux, guarda el curso que será paralelo al curso que guarda
                            //la materia del plan 2007.
                            $aux = $xcc -> division.($xcc2 -> id + 1);
                    }

                    $xcc -> materia = $xcc -> materia2007;
                    $xcc -> paralelo = $aux;
                    $xcc -> save();
            }

        } // function crearLasActasParaLasMateriasDelPlan2000YNoDejarlasJuntasConLasDelPlan2007()

        function pasarLosAlumnosAlCursoDelPlan2000QueActualmenteEstanEnElCursoQueTienePlan2000YPlan2007(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            // Esta función es suplimentaria a la función de:
            //"crearLasActasParaLasMateriasDelPlan2000YNoDejarlasJuntasConLasDelPlan2007".
            // En esta función se crean nuevas actas para los alumnos que estaban inscritos
            //en los cursos que compartían materia del plan2000 y plan2007.

            $xccursos		= new Xccursos();
            $xccursos2		= new Xccursos();
            $xccursos3		= new Xccursos();
            $xalumnocursos	= new Xalumnocursos();
            $xalumnocursos2	= new Xalumnocursos();
            $xalumnocursos3	= new Xalumnocursos();
            $xchorascursos	= new Xchorascursos();
            $xchorascursos2	= new Xchorascursos();

            /*
            // Pasos para pasar correctamente los cursos...

            -- Select * From xccursos;
            -- 332

            -- 191 update xccursos set materia = materia2007 where materia2000 = "-1"
            -- 102 update xccursos set materia = materia2000 where materia2007 = "-1"

            -- Despues correr el script de:
            -- crearLasActasParaLasMateriasDelPlan2000YNoDejarlasJuntasConLasDelPlan2007

            -- Select * From xccursos;
            -- 358

            -- Inscribir a los alumnos de plan2000 al nuevo curso...

            Select * from xccursos
            where paralelo <> "";

            // Para esto voy a checar los siguientes querys, para saber que cursos son los "afectados"

            -- 4 de Marzo del 2010
            Select * from xccursos;
            -- 358

            Select * from xccursos
            where paralelo = "";
            -- 306

            Select * from xccursos
            where paralelo not in
            ( select clavecurso from xchorascursos )
            and paralelo <> "";
            -- 26


            Select count(*) as cuenta, xcc.clavecurso
            from xccursos xcc, xchorascursos xch
            where xcc.paralelo = xch.clavecurso
            group by xcc.id;
            -- 26


            Select * from xccursos
            where clavecurso not in
            ( select clavecurso from xchorascursos )
            and paralelo <> "";
            -- 26
            */

            foreach( $xccursos -> find_all_by_sql
                     ( "Select * from xccursos
                            where clavecurso not in
                            ( select clavecurso from xchorascursos )
                            and paralelo <> ''" ) as $xcc ){

                    foreach( $xalumnocursos -> find_all_by_sql
                                     ( "Select * from xalumnocursos xal, alumnos al
                                            where xal.registro = al.miReg
                                            and al.enPlan = \"PE00\"
                                            and xal.curso = '".$xcc -> paralelo."'" ) as $xal ){

                            $xalumnocursos2 -> periodo			= $xcc -> periodo;
                            $xalumnocursos2 -> curso			= $xcc -> clavecurso;
                            $xalumnocursos2 -> registro			= $xal -> registro;
                            $xalumnocursos2 -> faltas1			= '0';
                            $xalumnocursos2 -> calificacion1	= 300;
                            $xalumnocursos2 -> faltas2			= '0';
                            $xalumnocursos2 -> calificacion2	= 300;
                            $xalumnocursos2 -> faltas3			= '0';
                            $xalumnocursos2 -> calificacion3	= 300;
                            $xalumnocursos2 -> faltas			= '0';
                            $xalumnocursos2 -> calificacion		= 300;
                            $xalumnocursos2 -> situacion		= "-";

                            $xalumnocursos2 -> create();

                            foreach( $xalumnocursos3 -> find_all_by_sql
                                            ( "Select * from xalumnocursos
                                            where periodo = 12010
                                            and curso = '".$xal -> curso."'
                                            and registro = ".$xal -> registro ) as $xal3 ){
                                    $xal3 -> delete();
                            }
                    }

                    foreach( $xchorascursos -> find_all_by_sql
                             ( "Select * from xchorascursos
                                    where clavecurso = '".$xcc -> paralelo."'
                                    order by id" ) as $xch ){

                        $xchorascursos2 -> clavecurso	= $xcc -> clavecurso;
                        $xchorascursos2 -> xcsalones_id	= $xch -> xcsalones_id;
                        $xchorascursos2 -> dia			= $xch -> dia;
                        $xchorascursos2 -> hora			= $xch -> hora;
                        $xchorascursos2 -> bloque		= $xch -> bloque;
                        $xchorascursos2 -> periodo		= $xch -> periodo;

                        $xchorascursos2 -> create();
                    }

            }

        } // function pasarLosAlumnosAlCursoDelPlan2000QueActualmenteEstanEnElCursoQueTienePlan2000YPlan2007()

        function XcursosAXccursos(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
            // Esta función se hizo para poder pasar los cursos de la tabla xccursos
            //a la tabla de xccursos, con la finalidad de que los cursos
            //apartir del 32008 esten en una sóla tabla y sea más manipulable
            //la información.
            
            //Después de ejecutar este script y revisar cautelosamente que
            //los cursos se pasaron correctamente, se proseguirá a eliminar
            //la tabla de Xcursos

            $xcursos        = new Xcursos();
            $xcursos2       = new Xcursos();
            $xchorascursos  = new Xchorascursos();

            $xccursos       = new Xccursos();
            $xccursos2      = new Xccursos();
            $xalumnocurso   = new Xalumnocursos();
            $xextras        = new Xextraordinarios();

            
            /*
            // Xcursos
            id, clavecurso, materia, nomina, cupo, disponibilidad
            minimo, maximo, activo, asesoria, periodo
            lunesi lunsef luness
            lunesi2 lunesf2 luness2
            martesi martesf martess
            martesi2 mastesf2 martess2
            miercolesi miercolesf miercoless
            miercolesi2 miercolesf2 miercoless2
            juevesi juevesf juevess
            juevesi2 juevesf2 juevess
            viernesi viernesf vierness
            viernesi2 viernesf2 vierness2
            sabadoi sabadof sabados
            sabadoi2 sabadof2 sabadoss2
            carrera, curso, division
            horas1 avance1
            horas2 avance2
            horas3 avance3

            // Xccursos
            id, clavecurso, paralelo, materia, materia2000, materia2007
            nomina, cupo, disponibilidad, minimo, maximo, activo, asesoria
            periodo, carrera, curso, division
            horas1 avance1
            horas2 avance2
            horas3 avance3
            */

            foreach( $xcursos -> find_all_by_sql
                    ( "Select * from xcursos where periodo = 12009
					or periodo = 32008" ) as $xcurso ){

                $xccursos -> id = $xcurso -> id;
                $xccursos -> clavecurso = $xcurso -> clavecurso;
                $xccursos -> paralelo = "-1";
                $xccursos -> materia = $xcurso -> materia;
                $xccursos -> materia2000 = "-1";
                $xccursos -> materia2007 = "-1";
                $xccursos -> nomina = $xcurso -> nomina;
                $xccursos -> cupo = $xcurso -> cupo;
                $xccursos -> disponibilidad = $xcurso -> disponibilidad;
                $xccursos -> minimo = $xcurso -> minimo;
                $xccursos -> maximo = $xcurso -> maximo;
                $xccursos -> activo = $xcurso -> activo;
                $xccursos -> asesoria = $xcurso -> asesoria;
                $xccursos -> periodo = $xcurso -> periodo;
                $xccursos -> carrera = $xcurso -> carrera;
                $xccursos -> curso = $xcurso -> curso;
                $xccursos -> division = $xcurso -> division;
                $xccursos -> horas1 = $xcurso -> horas1;
                $xccursos -> avance1 = $xcurso -> avance1;
                $xccursos -> horas2 = $xcurso -> horas2;
                $xccursos -> avance2 = $xcurso -> avance2;
                $xccursos -> horas3 = $xcurso -> horas3;
                $xccursos -> avance3 = $xcurso -> avance3;

                $xccursos -> create();
            /*
            lunesi lunsef luness
            lunesi2 lunesf2 luness2
            martesi martesf martess
            martesi2 mastesf2 martess2
            miercolesi miercolesf miercoless
            miercolesi2 miercolesf2 miercoless2
            juevesi juevesf juevess
            juevesi2 juevesf2 juevess
            viernesi viernesf vierness
            viernesi2 viernesf2 vierness2
            sabadoi sabadof sabados
            sabadoi2 sabadof2 sabadoss2
            */
            /*
             * Xchorascursos
            id, clavecurso, xcsalones_id, dia, hora, bloque, periodo
            */
                if( $xcurso -> lunesi != "0" && $xcurso -> lunesf != "0" ){

                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> luness;
                    $xchorascursos -> dia = 1;
                    $xchorascursos -> hora = $xcurso -> lunesi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();
                    
                    $aux = $xcurso -> lunesf - $xcurso -> lunesi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> lunesi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> luness;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> martesi != "0" && $xcurso -> martesf != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> martess;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> martesi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> martesf - $xcurso -> martesi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> martesi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> martess;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> miercolesi != "0" && $xcurso -> miercolesf != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> miercoless;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> miercolesi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> miercolesf - $xcurso -> miercolesi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> miercolesi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> miercoless;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> juevesi != "0" && $xcurso -> juevesf != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> juevess;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> juevesi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> juevesf - $xcurso -> juevesi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> juevesi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> juevess;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> viernesi != "0" && $xcurso -> viernesf != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> vierness;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> viernesi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> viernesf - $xcurso -> viernesi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> viernesi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> vierness;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> sabadoi != "0" && $xcurso -> sabadof != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> sabados;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> sabadoi;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> sabadof - $xcurso -> sabadoi;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> sabadoi + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> sabados;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }

                if( $xcurso -> periodo != 32009 )
                    continue;
                // Sólo para periodo 32009
                if( $xcurso -> lunesi2 != "0" && $xcurso -> lunesf2 != "0" ){

                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> luness2;
                    $xchorascursos -> dia = 1;
                    $xchorascursos -> hora = $xcurso -> lunesi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> lunesf2 - $xcurso -> lunesi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> lunesi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> luness2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> martesi2 != "0" && $xcurso -> martesf2 != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> martess2;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> martesi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> martesf2 - $xcurso -> martesi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> martesi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> martess2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> miercolesi2 != "0" && $xcurso -> miercolesf2 != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> miercoless2;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> miercolesi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> miercolesf2 - $xcurso -> miercolesi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> miercolesi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> miercoless2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> juevesi2 != "0" && $xcurso -> juevesf2 != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> juevess2;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> juevesi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> juevesf2 - $xcurso -> juevesi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> juevesi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> juevess2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> viernesi2 != "0" && $xcurso -> viernesf2 != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> vierness2;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> viernesi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> viernesf2 - $xcurso -> viernesi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> viernesi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> vierness2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
                if( $xcurso -> sabadoi2 != "0" && $xcurso -> sabadof2 != "0" ){
                    $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                    $xchorascursos -> xcsalones_id = $xcurso -> sabados2;
                    $xchorascursos -> dia = 2;
                    $xchorascursos -> hora = $xcurso -> sabadoi2;
                    $xchorascursos -> bloque = '0';
                    $xchorascursos -> periodo = $xcurso -> periodo;
                    $xchorascursos -> create();

                    $aux = $xcurso -> sabadof2 - $xcurso -> sabadoi2;
                    for( $i = 1; $i < $aux; $i++ ){
                        $xchorascursos -> dia = 1;
                        $xchorascursos -> hora = $xcurso -> sabadoi2 + $i;
                        $xchorascursos -> clavecurso = $xcurso -> clavecurso;
                        $xchorascursos -> xcsalones_id = $xcurso -> sabados2;
                        $xchorascursos -> bloque = '0';
                        $xchorascursos -> periodo = $xcurso -> periodo;
                        $xchorascursos -> create();
                    }
                }
            }

        } // function XcursosAXccursos()
		
		function PonerBienLosIDsDeSalonesEnXcursos(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
			// Esta función se hace con el fin de poner bien los
			//IDs de salones en los campos que deben llevarlo
			//ya que se manejo inadecuadamente el sistema en ese
			//momento y se puso el nombre del salón en vez
			//de poner el ID del salón.
			
			$xcursos			= new Xcursos();
			$xsalones32008y12009= new Xsalones32008y12009();
			
			foreach( $xcursos -> find_all_by_sql
					 ("select * from xcursos
						where periodo = 32008") as $xc ){
				
				if( !is_numeric($xc -> luness) && $xc -> luness != "" ){
					if( list($edificio, $nombre) = split(":", $xc -> luness, 2) ){
						if( $xsal = $xsalones32008y12009 -> find_first
								( "edificio= '".$edificio."'".
								"and nombre = '".$nombre."'".
								"and periodo = 32008" ) ){
							$xc -> luness = $xsal -> id;
						}
						else{
							$xc -> luness = "-";
						}
					}
					else{
						echo $xc -> id." ".$xc -> luness."<br />";
					}
				}
				if( !is_numeric($xc -> martess) && $xc -> martess != "" ){
					if( list($edificio, $nombre) = split(":", $xc -> martess, 2) ){
						if( $xsal = $xsalones32008y12009 -> find_first
								( "edificio= '".$edificio."'".
								"and nombre = '".$nombre."'".
								"and periodo = 32008" ) ){
							$xc -> martess = $xsal -> id;
						}
						else{
							$xc -> martess = "-";
						}
					}
					else{
						echo $xc -> id." ".$xc -> martess."<br />";
					}
				}
				if( !is_numeric($xc -> miercoless) && $xc -> miercoless != "" ){
					if( list($edificio, $nombre) = split(":", $xc -> miercoless, 2) ){
						if( $xsal = $xsalones32008y12009 -> find_first
								( "edificio= '".$edificio."'".
								"and nombre = '".$nombre."'".
								"and periodo = 32008" ) ){
							$xc -> miercoless = $xsal -> id;
						}
						else{
							$xc -> miercoless = "-";
						}
					}
					else{
						echo $xc -> id." ".$xc -> miercoless."<br />";
					}
				}
				if( !is_numeric($xc -> juevess) && $xc -> juevess != "" ){
					if( list($edificio, $nombre) = split(":", $xc -> juevess, 2) ){
						if( $xsal = $xsalones32008y12009 -> find_first
								( "edificio= '".$edificio."'".
								"and nombre = '".$nombre."'".
								"and periodo = 32008" ) ){
							$xc -> juevess = $xsal -> id;
						}
						else{
							$xc -> juevess = "-";
						}
					}
					else{
						echo $xc -> id." ".$xc -> juevess."<br />";
					}
				}
				if( !is_numeric($xc -> vierness) && $xc -> vierness != "" ){
					if( list($edificio, $nombre) = split(":", $xc -> vierness, 2) ){
						if( $xsal = $xsalones32008y12009 -> find_first
								( "edificio= '".$edificio."'".
								"and nombre = '".$nombre."'".
								"and periodo = 32008" ) ){
							$xc -> vierness = $xsal -> id;
						}
						else{
							$xc -> vierness = "-";
						}
					}
					else{
						echo $xc -> id." ".$xc -> vierness."<br />";
					}
				}
				$xc -> save();
			}
		} // function PonerBienLosIDsDeSalonesEnXcursos()
		
		
		function corregir69de12010(){
			
            if(Session::get_data('tipousuario')!="CALCULO"){
                    $this->redirect('/');
            }
			
			// Esta función se hizo para corregir la falla que ocurrió
			//al programar mal una parte del evaluador.
			
			$xalumnocursos	= new Xalumnocursos();
			$xtalumnocursos	= new Xtalumnocursos();
			
			
			foreach( $xalumnocursos -> find_all_by_sql
					("Select * from xalumnocursos
					where periodo = 12010
					and calificacion < 70") as $xalumno ){
				
				$tmp1 = $xalumno -> calificacion1;
				$tmp2 = $xalumno -> calificacion2;
				$tmp3 = $xalumno -> calificacion3;
				
				if($xalumno -> calificacion1>100){
					$tmp1 = '0';
				}
				if($xalumno -> calificacion2>100){
					$tmp2 = '0';
				}
				if($xalumno -> calificacion3>100){
					$tmp3 = '0';
				}
				
				$xalumno -> calificacion = $tmp1 + $tmp2 + $tmp3;
				
				if( ($xalumno -> calificacion /3) < 69.9 && ($xalumno -> calificacion /3) > 69.5){
					$xalumno -> calificacion = 207;
				}
				$xalumno -> calificacion = round($xalumno -> calificacion/3);
				
				$xalumno -> update();
				
			}
			
			foreach( $xtalumnocursos -> find_all_by_sql
					("Select * from xtalumnocursos
					where periodo = 12010
					and calificacion < 70") as $xtalumno ){
				
				$tmp1 = $xtalumno -> calificacion1;
				$tmp2 = $xtalumno -> calificacion2;
				$tmp3 = $xtalumno -> calificacion3;
				
				if($xtalumno -> calificacion1>100){
					$tmp1 = '0';
				}
				if($xtalumno -> calificacion2>100){
					$tmp2 = '0';
				}
				if($xtalumno -> calificacion3>100){
					$tmp3 = '0';
				}
				
				$xtalumno -> calificacion = $tmp1 + $tmp2 + $tmp3;
				
				if( ($xtalumno -> calificacion /3) < 69.9 && ($xtalumno -> calificacion /3) > 69.5){
					$xtalumno -> calificacion = 207;
				}
				$xtalumno -> calificacion = round($xtalumno -> calificacion/3);
				
				$xtalumno -> update();
				
			}
		} // function corregir69de12010()
		
		
        function evaluadorColomos(){
			
			$this -> validar_usuario_calculo();
			
			$periodo = $this -> actual;
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
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
			
			$xcursos = new Xccursos();
			
			$xccursosss = new Xccursos();
			
			foreach( $xccursosss -> find_all_by_sql
					( "Select clavecurso
						from xccursos
						where periodo = ".$this -> actual ) as $xcc ){
				$curso = $xcc -> clavecurso;
				$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
				
				$parcial = 3;
				
				$calificaciones = new Xalumnocursos();
				
				$aux = 0;
				
				foreach($calificaciones -> find("curso='".$curso."' ORDER BY id") as $alumno){
					
					$calificacion = $calificaciones -> find_first("id=".$alumno -> id);
					
					$calificacion -> situacion = "-";
					
					// Apartir de aquí se hace la evaluación final de este semestre.
					if($calificacion -> calificacion1!=300
							&& $calificacion -> calificacion2!=300
								&& $calificacion -> calificacion3!=300){
						$tmp = 0;

						if($calificacion -> calificacion1>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion1;
						}

						if($calificacion -> calificacion2>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion2;
						}

						if($calificacion -> calificacion3>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion3;
						}
						
						// Si el promedio de la calificación es menor a 69.9, no se redondea
						//y estará reprobado.
						// Por eso es que se le asigna el valor de 207, ya que 69 X 3 son 207
						//para la hora de que se haga el redondeo, la función de round() de php
						//no redondie a 70.
						if( ($tmp/3) < 69.9 && ($tmp/3) > 69.5){
							$tmp = 207;
						}
						$calificacion -> calificacion = round($tmp/3);
						$calificacion -> faltas =
										$calificacion -> faltas1 +
											$calificacion -> faltas2 +
													$calificacion -> faltas3;

						$total = 0;
						$total = $xcurso -> horas1 + $xcurso -> horas2 + $xcurso -> horas3;
						
						// Si las horas son menor a 15, es muy probable
						//que el profesor haya capturado mal las horas,
						//así que se pasa a hacer la fórmula para obtener
						//las horas por default del parcial.
						//Es decir se consiguen las veces que se da esa materia
						//por semana y se multiplica por 5, y esas serán
						//las horas estimadas que se dieron en ese parcial.
						if( $total < 15 ){
							$xccursoss = New Xccursos();
							foreach( $xccursoss -> find_all_by_sql
								( "select count(*) as contar
								from xccursos xcc, xchorascursos xch
								where xcc.clavecurso = '".$curso."'
								and xcc.clavecurso = xch.clavecurso" ) as $xch ){
								$total = $xch -> contar;
							}
							$total = $total * 5;
						}
						
						$porcentaje = round(($calificacion -> faltas / $total) * 100);
						
						if( $calificacion -> calificacion >= 70 && $calificacion -> calificacion <=100 ){
							// Pasa la materia sin complicaciones
							$situacion = "ORDINARIO";
							
							// Checar si el alumno debía la materia, y si la debe
							//asignarle a la variable un valor de true, la cuál nos ayudará
							//a seguir calificando correctamente su materia.
							if( $this -> materiaIrregular($calificacion -> registro, $calificacion -> curso) ){
								$aux = true;
							}else{
								$aux = false;
							}
							
							if( $aux ){
								// Pasa la materia, sin embargo significa
								//que la estaba repitiendo.
								$situacion = "REGULARIZACION";
							}
							
							if( $porcentaje >= 21 && $porcentaje <= 40 ){
								// Se va a extraordinario por faltas.
								$situacion = "EXTRAORDINARIO FALTAS";
								if( $aux ){
									if( $porcentaje > 30 && $porcentaje <= 40 ){
										// Se va a titulo por faltas.
										$situacion = "TITULO FALTAS";
									}
								}
							}
							else{
								if($porcentaje >= 41 && $porcentaje < 60){
									$situacion = "REGULARIZACION DIRECTA";
									if( $aux && $porcentaje >= 41 ){
										// Si ya la debía y además tiene
										//muchas faltas, se va a BAJA DEFINITIVA
										$situacion = "BAJA DEFINITIVA";
									}
								}
								else{
									if($porcentaje > 60){
										$situacion = "REGULARIZACION DIRECTA";
										if( $aux ){
											$situacion = "BAJA DEFINITIVA";
										}
									}
								}
							}
						}
						else{
							$situacion = "EXTRAORDINARIO";
							
							if( $this -> materiaIrregular($calificacion -> registro, $calificacion -> curso) ){
								$aux = true;
							}else{
								$aux = false;
							}
							
							if( $porcentaje >= 21 && $porcentaje <= 40 ){
								// Se va a extraordinario por faltas.
								$situacion = "EXTRAORDINARIO FALTAS";
							}
							
							if( $aux ){
								$situacion = "TITULO DE SUFICIENCIA";
							}
							
							if($porcentaje >= 41 && $porcentaje < 60){
								$situacion = "REGULARIZACION DIRECTA";
								if( $aux && $porcentaje >= 41 ){
									$situacion = "BAJA DEFINITIVA";
								}
								else if( $aux ){
									$situacion = "TITULO FALTAS";
								}
							}
							else{
								if($porcentaje > 60){
									$situacion = "REGULARIZACION DIRECTA";
									if( $aux ){
										$situacion = "BAJA DEFINITIVA";
									}
								}
							}
						}
						$calificacion -> situacion = $situacion;
					}
					if( $calificacion -> calificacion < 70 && 
						$this -> condicionado($calificacion -> registro, $calificacion -> curso_id) ){
						$calificacion -> situacion = "BAJA DEFINITIVA";
					}
					if( $calificacion -> calificacion == null || $calificacion -> calificacion == '0' )
						$calificacion -> calificacion = '0';
					if( $calificacion -> faltas == null )
						$calificacion -> faltas = '0';
					
					$calificacion -> save();
					
				}
				echo "Curso: ".$curso."<br />";
			}
        } // function evaluadorColomos()
		
        function evaluadorTonala(){
			
			$this -> validar_usuario_calculo();
			
			$periodo = $this -> actual;
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
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
			
			$xcursos = new Xtcursos();
			
			$xccursosss = new Xtcursos();
			
			foreach( $xccursosss -> find_all_by_sql
					( "Select clavecurso
						from xtcursos
						where periodo = ".$this -> actual ) as $xtc ){
				$curso = $xtc -> clavecurso;
				$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
				
				$parcial = 3;
				
				$calificaciones = new Xtalumnocursos();
				
				$aux = 0;
				
				foreach($calificaciones -> find("curso='".$curso."' ORDER BY id") as $alumno){
					
					$calificacion = $calificaciones -> find_first("id=".$alumno -> id);
					
					$calificacion -> situacion = "-";
					
					// Apartir de aquí se hace la evaluación final de este semestre.
					if($calificacion -> calificacion1!=300
							&& $calificacion -> calificacion2!=300
								&& $calificacion -> calificacion3!=300){
						$tmp = 0;

						if($calificacion -> calificacion1>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion1;
						}

						if($calificacion -> calificacion2>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion2;
						}

						if($calificacion -> calificacion3>100){
							$tmp += 0;
						}
						else{
							$tmp += $calificacion -> calificacion3;
						}
						
						// Si el promedio de la calificación es menor a 69.9, no se redondea
						//y estará reprobado.
						// Por eso es que se le asigna el valor de 207, ya que 69 X 3 son 207
						//para la hora de que se haga el redondeo, la función de round() de php
						//no redondie a 70.
						if( ($tmp/3) < 69.9 && ($tmp/3) > 69.5){
							$tmp = 207;
						}
						$calificacion -> calificacion = round($tmp/3);
						$calificacion -> faltas =
										$calificacion -> faltas1 +
											$calificacion -> faltas2 +
													$calificacion -> faltas3;

						$total = 0;
						$total = $xcurso -> horas1 + $xcurso -> horas2 + $xcurso -> horas3;
						
						// Si las horas son menor a 15, es muy probable
						//que el profesor haya capturado mal las horas,
						//así que se pasa a hacer la fórmula para obtener
						//las horas por default del parcial.
						//Es decir se consiguen las veces que se da esa materia
						//por semana y se multiplica por 5, y esas serán
						//las horas estimadas que se dieron en ese parcial.
						if( $total < 15 ){
							$xccursoss = New Xtcursos();
							foreach( $xccursoss -> find_all_by_sql
								( "select count(*) as contar
								from xtcursos xcc, xthorascursos xch
								where xcc.clavecurso = '".$curso."'
								and xcc.clavecurso = xch.clavecurso" ) as $xch ){
								$total = $xch -> contar;
							}
							$total = $total * 5;
						}
						
						$porcentaje = round(($calificacion -> faltas / $total) * 100);
						
						if( $calificacion -> calificacion >= 70 && $calificacion -> calificacion <=100 ){
							// Pasa la materia sin complicaciones
							$situacion = "ORDINARIO";
							
							// Checar si el alumno debía la materia, y si la debe
							//asignarle a la variable un valor de true, la cuál nos ayudará
							//a seguir calificando correctamente su materia.
							if( $this -> materiaIrregular($calificacion -> registro, $calificacion -> curso) ){
								$aux = true;
							}else{
								$aux = false;
							}
							
							if( $aux ){
								// Pasa la materia, sin embargo significa
								//que la estaba repitiendo.
								$situacion = "REGULARIZACION";
							}
							
							if( $porcentaje >= 21 && $porcentaje <= 40 ){
								// Se va a extraordinario por faltas.
								$situacion = "EXTRAORDINARIO FALTAS";
								if( $aux ){
									if( $porcentaje > 30 && $porcentaje <= 40 ){
										// Se va a titulo por faltas.
										$situacion = "TITULO FALTAS";
									}
								}
							}
							else{
								if($porcentaje >= 41 && $porcentaje < 60){
									$situacion = "REGULARIZACION DIRECTA";
									if( $aux && $porcentaje >= 41 ){
										// Si ya la debía y además tiene
										//muchas faltas, se va a BAJA DEFINITIVA
										$situacion = "BAJA DEFINITIVA";
									}
								}
								else{
									if($porcentaje > 60){
										$situacion = "REGULARIZACION DIRECTA";
										if( $aux ){
											$situacion = "BAJA DEFINITIVA";
										}
									}
								}
							}
						}
						else{
							$situacion = "EXTRAORDINARIO";
							
							if( $this -> materiaIrregular($calificacion -> registro, $calificacion -> curso) ){
								$aux = true;
							}else{
								$aux = false;
							}
							
							if( $porcentaje >= 21 && $porcentaje <= 40 ){
								// Se va a extraordinario por faltas.
								$situacion = "EXTRAORDINARIO FALTAS";
							}
							
							if( $aux ){
								$situacion = "TITULO DE SUFICIENCIA";
							}
							
							if($porcentaje >= 41 && $porcentaje < 60){
								$situacion = "REGULARIZACION DIRECTA";
								if( $aux && $porcentaje >= 41 ){
									$situacion = "BAJA DEFINITIVA";
								}
								else if( $aux ){
									$situacion = "TITULO FALTAS";
								}
							}
							else{
								if($porcentaje > 60){
									$situacion = "REGULARIZACION DIRECTA";
									if( $aux ){
										$situacion = "BAJA DEFINITIVA";
									}
								}
							}
						}
						$calificacion -> situacion = $situacion;
					}
					if( $this -> condicionado($calificacion -> registro, $calificacion -> curso_id)
						&& $calificacion -> calificacion < 70 ){
						$calificacion -> situacion = "BAJA DEFINITIVA";
					}
					if( $calificacion -> calificacion == null || $calificacion -> calificacion == '0' )
						$calificacion -> calificacion = '0';
					if( $calificacion -> faltas == null )
						$calificacion -> faltas = '0';
					
					$calificacion -> save();
					
				}
				echo "Curso: ".$curso."<br />";
			}
        } // function evaluadorTonala()
		
		function calcularHorasEnPrimerParcial(){
			
			// Calcular las horas para el primer parcial.
			//Gracias al error de que no estuvo guardando bien
			//al comienzo del periodo 12010.
			
			$xccursos		= new Xccursos();
			$xchorascursos	= new Xchorascursos();
			
			$xtcursos		= new Xtcursos();
			$xthorascursos	= new Xthorascursos();
			
			foreach( $xchorascursos -> find_all_by_sql
					("select xcc.clavecurso, count(xch.clavecurso) cuenta
					from xccursos xcc, xchorascursos xch
					where xcc.periodo = ".$this -> actual."
					and xcc.clavecurso = xch.clavecurso
					group by xcc.clavecurso, xch.clavecurso") as $xch ){
				foreach( $xccursos -> find( "clavecurso = '".$xch -> clavecurso."'" ) as $xcc ){
					$xcc -> horas1 = ($xch -> cuenta * 6);
					$xcc -> save();
				}
			}
			
			foreach( $xthorascursos -> find_all_by_sql
					("select xcc.clavecurso, count(xch.clavecurso) cuenta
					from xtcursos xcc, xthorascursos xch
					where xcc.periodo = ".$this -> actual."
					and xcc.clavecurso = xch.clavecurso
					group by xcc.clavecurso, xch.clavecurso") as $xth ){
				foreach( $xtcursos -> find( "clavecurso = '".$xth -> clavecurso."'" ) as $xtc ){
					$xtc -> horas1 = ($xth -> cuenta * 6);
					$xtc -> save();
				}
			}
			
		} // function calcularHorasEnPrimerParcial()
		
        function pasarAKardexIngColomos(){ // Este es el bueno para pasar a kardex de xalumnocursos(Enero 9 2011)
			$xalumnocursos	= new Xalumnocursos();
			$xacursos		= new Xalumnocursos();
			$xccursos		= new Xccursos();
			$xccursos2		= new Xccursos();
			$kardexIng		= new KardexIng();
			$kardexIng2		= new KardexIng();
			/*
			select c32008.registro as registro32008, c32008.materia as materia32008,
				c32008.situacion as situacion32008, c32008.id as id12008, c12009.registro as registro12009,
				c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009
			*/
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
			foreach ( $xacursos -> find_all_by_sql
				  ("Select id, curso, registro, calificacion, situacion
					from xalumnocursos
					where periodo = ".$this -> actual."
					and calificacion >= 70
					and calificacion <= 100
					and (situacion = \"ORDINARIO\"
					or situacion = \"REGULARIZACION\")")as $tmp ){
				/*
				registro  clavemat  nivel  periodo  tipo_de_ex  promedio  fecha_reg
					$xalumcur -> periodo;
					$xalumcur -> registro;
					$xalumcur -> curso;
					$xalumcur -> situacion;
					$xalumcur -> calificacion;
				*/
				foreach( $xccursos2 -> find_all_by_sql
						("select xal.registro, xcc.materia, m.semestre, xcc.periodo, xal.calificacion
						from xccursos xcc, xalumnocursos xal, materia m
						where xcc.clavecurso = '".$tmp-> curso."'
						and xcc.clavecurso = xal.curso
						and xal.registro = ".$tmp -> registro."
						and xcc.materia = m.clave
						limit 1" ) as $xcc2 ){
					if( !$kardexIng2 -> find_first
							( "registro= ".$xcc2 -> registro." and clavemat = '".$xcc2 -> materia."'" ) ){

						$kardexIng -> registro = $xcc2 -> registro;
						$kardexIng -> clavemat = $xcc2 -> materia;
						$kardexIng -> nivel = $xcc2 -> nivel;
						$kardexIng -> periodo = $xcc2 -> periodo;
						$kardexIng -> promedio = $xcc2 -> calificacion;
						$kardexIng -> fecha_reg = $date1;
						if ( $tmp -> situacion == "ORDINARIO" )
								$tipoDeEx = 'D';
						if ( $tmp -> situacion == "REGULARIZACION" )
								$tipoDeEx = 'R';

						$kardexIng -> tipo_de_ex = $tipoDeEx;
						$kardexIng -> create();
					}
				}
			}

        } // function pasarAKardexIngColomos()
		
        function pasarAKardexIngTonala(){ // Este es el bueno para pasar a kardex de xalumnocursos(Enero 9 2011)
			$xalumnocursos	= new Xtalumnocursos();
			$xacursos		= new Xtalumnocursos();
			$xccursos		= new Xtcursos();
			$xccursos2		= new Xtcursos();
			$kardexIng		= new KardexIng();
			$kardexIng2		= new KardexIng();
			/*
			select c32008.registro as registro32008, c32008.materia as materia32008,
				c32008.situacion as situacion32008, c32008.id as id12008, c12009.registro as registro12009,
				c12009.materia as materia12009, c12009.situacion as situacion12009, c12009.id as id12009
			*/
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
			foreach ( $xacursos -> find_all_by_sql
				  ("Select id, curso, registro, calificacion, situacion
					from xtalumnocursos
					where periodo = ".$this -> actual."
					and calificacion >= 70
					and calificacion <= 100
					and (situacion = \"ORDINARIO\"
					or situacion = \"REGULARIZACION\")")as $tmp ){
				/*
				registro  clavemat  nivel  periodo  tipo_de_ex  promedio  fecha_reg
					$xalumcur -> periodo;
					$xalumcur -> registro;
					$xalumcur -> curso;
					$xalumcur -> situacion;
					$xalumcur -> calificacion;
				*/
				foreach( $xccursos2 -> find_all_by_sql
						("select xal.registro, xcc.materia, m.semestre, xcc.periodo, xal.calificacion
						from xtcursos xcc, xtalumnocursos xal, materia m
						where xcc.clavecurso = '".$tmp-> curso."'
						and xcc.clavecurso = xal.curso
						and xal.registro = ".$tmp -> registro."
						and xcc.materia = m.clave
						limit 1" ) as $xcc2 ){
					if( !$kardexIng2 -> find_first
							( "registro= ".$xcc2 -> registro." and clavemat = '".$xcc2 -> materia."'" ) ){

						$kardexIng -> registro = $xcc2 -> registro;
						$kardexIng -> clavemat = $xcc2 -> materia;
						$kardexIng -> nivel = $xcc2 -> nivel;
						$kardexIng -> periodo = $xcc2 -> periodo;
						$kardexIng -> promedio = $xcc2 -> calificacion;
						$kardexIng -> fecha_reg = $date1;
						if ( $tmp -> situacion == "ORDINARIO" )
								$tipoDeEx = 'D';
						if ( $tmp -> situacion == "REGULARIZACION" )
								$tipoDeEx = 'R';

						$kardexIng -> tipo_de_ex = $tipoDeEx;
						$kardexIng -> create();
					}
				}
			}

        } // function pasarAKardexIngTonala()
		
		function quitarAlumnosDeXalumnocursosYSeleccionTiempoSiTienenBajaDefinitiva(){
			
			$alumnos = new Alumnos();
			$xalumnocursos = new Xalumnocursos();
			$seleccionTiempo = new SeleccionTiempo();
			/*
			foreach( $alumnos -> find_all_by_sql(
						"select xal.id, xal.registro, sl.id as idd
						From xalumnocursos xal, seleccion_tiempo sl
						where xal.situacion = \"BAJA DEFINITIVA\"
						and xal.periodo = ".$this -> anterior."
						and xal.registro = sl.registro
						and sl.periodo = ".$this -> actual ) as $al ){
			*/
			foreach( $alumnos -> find_all_by_sql(
						"select distinct xal.registro
						From xalumnocursos xal
						where xal.situacion = \"BAJA DEFINITIVA\"
						and xal.periodo = ".$this -> anterior ) as $al ){
				foreach ( $xalumnocursos -> find_all_by_sql(
						"delete from xalumnocursos 
						where registro = ".$al -> registro."
						and periodo = ".$this -> actual ) as $xal ){
					echo $xal -> registro." ".$al -> id."<br />";
				}
				//foreach ( $seleccionTiempo -> find_all_by_sql(
				//	"delete from seleccion_tiempo where id = ".$al -> idd ) as $sl ){
				//	echo $sl -> id."<br />";
				//}
			}
			
		} // function quitarAlumnosDeXalumnocursosYSeleccionTiempoSiTienenBajaDefinitiva()
		
		function modificarTablaUsuariosParaAlgoritmoAES_ENCRYPT(){
			$this -> validar_usuario_calculo();
			
			$usuarios	= new Usuarios();
			$usuarios2	= new Usuarios();
			
			$semilla = new Semilla();
			foreach( $usuarios -> find_all_by_sql(
					"select id, passwd_old, clave, registro
					from usuarios") as $usuario ){
				foreach( $usuarios2 -> find_all_by_sql(
						"update usuarios
						set clavee = AES_ENCRYPT('".$usuario -> clave."','".$semilla -> getSemilla()."')
						where registro = '".$usuario -> registro."'" ) as $usuario2 ){
					break;
				}
			}
		} // function modificarTablaUsuariosParaAlgoritmoAES_ENCRYPT()
		
        function mostrarCondicionados(){
			
			//$alumno contiene el registro del alumno
			//$curso contiene la clave del curso del alumno
			
			// Para saber si un alumno está condicionado
			$xccursos	= new Xccursos();
			$xtcursos	= new Xtcursos();
			$xalcursos	= new Xalumnocursos();
			$xtalcursos	= new Xtalumnocursos();
			
			$mmateria	= new Materia();
			$alumnos	= new Alumnos();
			
			foreach( $xalcursos -> find_all_by_sql("select registro from xalumnocursos where periodo = 12011") as $xal ){
			$alumno = $xal -> registro;
			$alumno = $alumnos -> find_first( "miReg = ".$alumno );
			
			$this -> pasado = 32010;
			$this -> antesAnterior = 12010;
			// Si la variable condicionado llega a 2, significa que el alumno si estaba condicionado...
			$condicionado = 0;
			
			if( $alumno -> enPlantel == "c" || $alumno -> enPlantel == "C" ){
				
				$xccurso = $xccursos -> find_first("clavecurso='".$curso."'");
				
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc, xalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$xccurso -> materia."'
					and xal.curso = xcc.clavecurso" ) as $xcc ){
					// Si el periodo que se trae es el pasado, o uno antes del pasado
					//si nos importa y por lo que esta materia si es irregular
					//asi que debera de tratarse como tal.
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				// También reviso en las tablas de Tonala
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xtcursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc, xtalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$xccurso -> materia."'
					and xal.curso = xtc.clavecurso" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
			}
			else{
				$xtcurso = $xtcursos -> find_first("clavecurso='".$curso."'");
				
				foreach( $xtcursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc, xtalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$xtcurso -> materia."'
					and xal.curso = xtc.clavecurso" ) as $xtc ){
					if( $xtc -> periodo == $this -> pasado ||
							$xtc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
				// También reviso en las tablas de Colomos
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc, xalumnocursos xal
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$xtcurso -> materia."'
					and xal.curso = xcc.clavecurso" ) as $xcc ){
					if( $xcc -> periodo == $this -> pasado ||
							$xcc -> periodo == $this -> antesAnterior ){
						$condicionado++;
					}
				}
			}
			
			if( $condicionado > 1 )
				echo "Condicionado: ".$alumno."<br />";
			else
				echo "NO: ".$alumno."<br />";
				//return false;
				
			}
        } // function mostrarCondicionados()
		
		function creandocursosintersemestrales_anteriores(){
		// Está función es temporal, y solo servirá una vez...
		// Anteriormente se creo la tabla de intersemestral, la cuál en ese momento solucionaba el registro
		//de los alumnos que estarían inscritos a cursos intersemestrales, sin embargo se creo
		//pensando que serían exámenes y no cursos, por lo que su estructura no sirve más. Y es necesario
		//crear 2 tablas, en la cuál se declara el curso intersemestral y en otra donde se tiene información
		//de los alumnos que se inscriben a dicho curso.
		// Pueden ser cursos de Nivelación o de Acreditación.
			$this -> validar_usuario_calculo();
		
		// Estructura de la tabla intersemestral
		//id, plantel, registro, clavemat, promedio, tipo_ex, pago, periodo, faltas
		
		
		// Estructura de intersemestral_alumnos
		//id, periodo, clavecurso, faltas, calificacion, tipo_ex, creadot_at, creado_by, modificado_at, modificado_by
		// Estructura de intersemestral_cursos
		//id, clavecurso, calvemat, nomina, cupo, disponibilidad, minimo, activo, periodo, division, creado_at
			
			$Intersemestral = new Intersemestral();
			
			$IntersemestralCursos = new IntersemestralCursos();
			$i = $IntersemestralCursos -> get_max_ID();
			
			foreach( $Intersemestral -> find_all_by_sql("
					select clavemat, periodo, nomina
					From intersemestral
					group by periodo, clavemat, nomina;") as $inter ){
				$i++;
				//$division = Session::get_data('coordinacion')
				$division = "NA";
				$IntersemestralCursos = new IntersemestralCursos();
				$Periodos = new Periodos();
				
				if( $i < 10 )
					$id = '000'.$i;
				else if( $i < 100 )
					$id = '00'.$i;
				else if( $i < 1000 )
					$id = '0'.$i;
				$IntersemestralCursos -> clavecurso = "I-".$division.$id;
				$IntersemestralCursos -> clavemat = $inter -> clavemat;
				$IntersemestralCursos -> nomina = $inter -> nomina;
				$IntersemestralCursos -> cupo = 30;
				$IntersemestralCursos -> disponibilidad = 30;
				$IntersemestralCursos -> minimo = 18;
				$IntersemestralCursos -> activo = 1;
				$IntersemestralCursos -> periodo = $inter -> periodo;
				$IntersemestralCursos -> division = $division;
				$IntersemestralCursos -> creado_at = $Periodos -> get_datetime();
				
				$IntersemestralCursos -> create();
			}
			
		} // function creandocursosintersemestrales_anteriores()
		
		function inscribiendoalumnos_cursosintersemestrales_anteriores(){
			$this -> validar_usuario_calculo();
			// Esta función sirve para inscribir a los alumnos a los cursos recien creados.
			// También se utilizará sólo una vez dicha función....
			
			
		// Estructura de intersemestral_alumnos
		//id, periodo, registro, clavecurso, pago, faltas, calificacion, tipo_ex, creadot_at, creado_by, modificado_at, modificado_by
		// Estructura de intersemestral_cursos
		//id, clavecurso, clavemat, nomina, cupo, disponibilidad, minimo, activo, periodo, division
			$IntersemestralCursos = new IntersemestralCursos();
			$Intersemestral = new Intersemestral();
			$Periodos = new Periodos();
			
			foreach( $Intersemestral -> find_all_by_sql("
					select * From intersemestral
					group by periodo, clavemat, registro") as $inter ){
				$IntersemestralAlumnos = new IntersemestralAlumnos();
				
				$InfoCursos = $IntersemestralCursos -> get_datos_cursos_by_claveANDperiodo($inter-> clavemat, $inter -> periodo);
				
				$IntersemestralAlumnos -> clavecurso = $InfoCursos -> clavecurso;
				
				$IntersemestralAlumnos -> registro = $inter -> registro;
				
				$IntersemestralAlumnos -> pago = $inter -> pago;
				if( $IntersemestralAlumnos -> pago == "" )
					$IntersemestralAlumnos -> pago = "?";
				
				if( $inter -> periodo != 0 )
					$IntersemestralAlumnos -> periodo = $inter -> periodo;
				else
					$IntersemestralAlumnos -> periodo = '0';
				if( $inter -> faltas != 0 )
					$IntersemestralAlumnos -> faltas = $inter -> faltas;
				else
					$IntersemestralAlumnos -> faltas = '0';
				
				if( $inter -> calificacion != 0 )
					$IntersemestralAlumnos -> calificacion = $inter -> calificacion;
				else
					$IntersemestralAlumnos -> calificacion = 300;
				$IntersemestralAlumnos -> tipo_ex = $inter -> tipo_ex;
				
				$IntersemestralAlumnos -> creado_at = $Periodos -> get_datetime();
				$IntersemestralAlumnos -> creado_by = "CALCULO";
				
				$IntersemestralAlumnos -> modificado_at = "0000-00-00 00:00:00";
				$IntersemestralAlumnos -> modificado_by = "-";
				
				$IntersemestralAlumnos -> create();
			}
			//$IntersemestralCursos
			//intersemestral_alumnos
		} // function inscribiendoalumnos_cursosintersemestrales_anteriores()
		
		// (Y), 10 Enero 2012
		function corregir_si_semestre_pasado_tiene_baja_definitiva_en_el_actua_si_reprueba_tambien_es_baja_definitiva_colomos(){
			$Xalumnocursos	= new Xalumnocursos();
			$Xalumnocursos2	= new Xalumnocursos();
			$Xalumnocursos3	= new Xalumnocursos();
			foreach( $Xalumnocursos -> find_all_by_sql("
					select xcc.materia, xcc.clavecurso, xal.registro,
					xal.situacion, xal.calificacion, xal.faltas, xal.id
					from xalumnocursos xal
					inner join xccursos xcc
					on xal.curso = xcc.clavecurso
					and xal.periodo = 32011
					and (xal.calificacion < 70
					or xal.calificacion = 999)") as $xal ){
				foreach( $Xalumnocursos2 -> find_all_by_sql("
						select xcc.materia, xal.*
						from xalumnocursos xal
						inner join xccursos xcc
						on xal.curso = xcc.clavecurso
						and xal.registro = '".$xal -> registro."'
						and xcc.materia = '".$xal -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.periodo = 12011") as $xal2 ) {
					$xal3 = $Xalumnocursos3 -> find_first("id = ".$xal -> id);
					$xal3 -> situacion = "BAJA DEFINITIVA";
					$xal3 -> save();
				}
			}
		} // function corregir_si_semestre_pasado_tiene_baja_definitiva_en_el_actua_si_reprueba_tambien_es_baja_definitiva_colomos()
		// (Y), 10 Enero 2012
		function corregir_si_semestre_pasado_tiene_baja_definitiva_en_el_actua_si_reprueba_tambien_es_baja_definitiva_tonala(){
			$Xalumnocursos	= new Xtalumnocursos();
			$Xalumnocursos2	= new Xtalumnocursos();
			$Xalumnocursos3	= new Xtalumnocursos();
			foreach( $Xalumnocursos -> find_all_by_sql("
					select xcc.materia, xcc.clavecurso, xal.registro,
					xal.situacion, xal.calificacion, xal.faltas, xal.id
					from xtalumnocursos xal
					inner join xtcursos xcc
					on xal.curso = xcc.clavecurso
					and xal.periodo = 32011
					and (xal.calificacion < 70
					or xal.calificacion = 999)") as $xal ){
				foreach( $Xalumnocursos2 -> find_all_by_sql("
						select xcc.materia, xal.*
						from xtalumnocursos xal
						inner join xtcursos xcc
						on xal.curso = xcc.clavecurso
						and xal.registro = '".$xal -> registro."'
						and xcc.materia = '".$xal -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.periodo = 12011") as $xal2 ) {
					$xal3 = $Xalumnocursos3 -> find_first("id = ".$xal -> id);
					$xal3 -> situacion = "BAJA DEFINITIVA";
					$xal3 -> save();
				}
			}
		} // function corregir_si_semestre_pasado_tiene_baja_definitiva_en_el_actua_si_reprueba_tambien_es_baja_definitiva_tonala()
		
		function crear_curso_id_en_xalumnocursos_colomos_y_tonala(){
			
			$Xalumnocursos = new Xtalumnocursos();
			$Xalumnocursos2= new Xtalumnocursos();
			
			foreach( $Xalumnocursos -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xal.id xalid
					from xtcursos xcc
					inner join xtalumnocursos xal
					on xcc.clavecurso = xal.curso" ) as $xal ){
				$xal2 = $Xalumnocursos2->find_first("id = ".$xal->xalid);
				$xal2->curso_id = $xal->id;
				$xal2->save();
			}
			
			$Xalumnocursos = new Xalumnocursos();
			$Xalumnocursos2= new Xalumnocursos();
			foreach( $Xalumnocursos -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xal.id xalid
					from xccursos xcc
					inner join xalumnocursos xal
					on xcc.clavecurso = xal.curso" ) as $xal ){
				$xal2 = $Xalumnocursos2->find_first("id = ".$xal->xalid);
				$xal2->curso_id = $xal->id;
				$xal2->save();
			}
		} // function crear_curso_id_en_xalumnocursos_colomos_y_tonala()
		
		function crear_curso_id_en_xchorascursos_colomos_y_tonala(){
			
			$Xchorascursos = new Xthorascursos();
			$Xchorascursos2= new Xthorascursos();
			foreach( $Xchorascursos -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xch.id xchid
					from xtcursos xcc
					inner join xthorascursos xch
					on xcc.clavecurso = xch.clavecurso" ) as $xal ){
				$xal2 = $Xchorascursos2->find_first("id = '".$xal->xchid."'");
				$xal2->curso_id = $xal->id;
				$xal2->save();
			}
			$Xchorascursos = new Xchorascursos();
			$Xchorascursos2= new Xchorascursos();
			foreach( $Xchorascursos -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xch.id xchid
					from xccursos xcc
					inner join xchorascursos xch
					on xcc.clavecurso = xch.clavecurso" ) as $xal ){
				$xal2 = $Xchorascursos2->find_first("id = '".$xal->xchid."'");
				$xal2->curso_id = $xal->id;
				$xal2->save();
			}
		} // function crear_curso_id_en_xchorascursos_colomos_y_tonala()
		
		function crear_curso_id_en_xextraordinarios_colomos_y_tonala(){
			$Xextraordinarios = new Xtextraordinarios();
			$Xextraordinarios2= new Xtextraordinarios();
			foreach( $Xextraordinarios -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xext.id xextid
					from xtcursos xcc
					inner join xtextraordinarios xext
					on xcc.clavecurso = xext.clavecurso" ) as $xal ){
				$Xextraordinarios2 = $Xextraordinarios2->find_first("id = ".$xal->xextid);
				$Xextraordinarios2->curso_id = $xal->id;
				$Xextraordinarios2->save();
			}
			$Xextraordinarios = new Xextraordinarios();
			$Xextraordinarios2= new Xextraordinarios();
			foreach( $Xextraordinarios -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xext.id xextid
					from xccursos xcc
					inner join xextraordinarios xext
					on xcc.clavecurso = xext.clavecurso" ) as $xal ){
				$Xextraordinarios2 = $Xextraordinarios2->find_first("id = ".$xal->xextid);
				$Xextraordinarios2->curso_id = $xal->id;
				$Xextraordinarios2->save();
			}
		} // function crear_curso_id_en_xextraordinarios_colomos_y_tonala()
		
		function crear_curso_id_en_xpermisoscaptura_colomos_y_tonala(){
			$Xpermisoscaptura = new Xtpermisoscaptura();
			$Xpermisoscaptura2= new Xtpermisoscaptura();
			foreach( $Xpermisoscaptura -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xext.id xextid
					from xtcursos xcc
					inner join xtpermisoscaptura xext
					on xcc.clavecurso = xext.curso" ) as $xal ){
				$Xpermisoscaptura2 = $Xpermisoscaptura2->find_first("id = ".$xal->xextid);
				$Xpermisoscaptura2->curso_id = $xal->id;
				$Xpermisoscaptura2->save();
			}
			$Xpermisoscaptura = new Xpermisoscaptura();
			$Xpermisoscaptura2= new Xpermisoscaptura();
			foreach( $Xpermisoscaptura -> find_all_by_sql(
					"select xcc.id, xcc.clavecurso, xp.id xextid, xp.curso
					from xpermisoscaptura xp
					join xccursos xcc
					on substr(xcc.clavecurso, 4) = substr(xp.curso, 4);" ) as $xal ){
				$Xpermisoscaptura2 = $Xpermisoscaptura2->find_first("id = ".$xal->xextid);
				$Xpermisoscaptura2->curso_id = $xal->id;
				$Xpermisoscaptura2->save();
			}
		} // function crear_curso_id_en_xpermisoscaptura_colomos_y_tonala()
		
		function crear_id_materia_en_xccursos_y_xtcursos(){
			$Xccursos = new Xccursos();
			$Materia = new Materia();
			
			
			
		} // function crear_id_materia_en_xccursos_y_xtcursos()
		
		function evaluador_final_colomos(){
			
			//$this -> validar_usuario_calculo();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
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
			
			$Xccursos = new Xccursos();
			
			$xccursosss = new Xccursos();
			
			foreach( $xccursosss -> find_all_by_sql
					( "Select clavecurso, id
						from xccursos
						where periodo = ".$periodo ) as $xcc ){
				$curso_id = $xcc -> id;
				$xccurso = $Xccursos -> find_first("id='".$curso_id."'");
				
				$parcial = 3;
				
				$Xalumnocursos = new Xalumnocursos();
				
				$materia_irregular = 0;
				
				foreach($Xalumnocursos -> find("curso_id='".$curso_id."' ORDER BY id") as $xal){
					
					$xalumnocurso = $Xalumnocursos -> find_first("id=".$xal -> id);
			
					$xalumnocurso -> situacion = "-";
				
					// Apartir de aquí se hace la evaluación final de este semestre.
					if($xalumnocurso -> calificacion1!=300
							&& $xalumnocurso -> calificacion2!=300
								&& $xalumnocurso -> calificacion3!=300){
						$tmp = 0;

						if($xalumnocurso -> calificacion1>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion1;
						}

						if($xalumnocurso -> calificacion2>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion2;
						}

						if($xalumnocurso -> calificacion3>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion3;
						}
						
						// Si el promedio de la calificación es menor a 69.9, no se redondea
						//y estará reprobado.
						// Por eso es que se le asigna el valor de 207, ya que 69 X 3 son 207
						//para la hora de que se haga el redondeo, la función de round() de php
						//no redondie a 70.
						if( ($tmp/3) < 69.9 && ($tmp/3) > 69.5){
							$tmp = 207;
						}
						$xalumnocurso -> calificacion = round($tmp/3);
						$xalumnocurso -> faltas =
										$xalumnocurso -> faltas1 +
											$xalumnocurso -> faltas2 +
													$xalumnocurso -> faltas3;

						$total = 0;
						$total = $xccurso -> horas1 + $xccurso -> horas2 + $xccurso -> horas3;
						
						// Si las horas son menor a 15, es muy probable
						//que el profesor haya capturado mal las horas,
						//así que se pasa a hacer la fórmula para obtener
						//las horas por default del parcial.
						//Es decir se consiguen las veces que se da esa materia
						//por semana y se multiplica por 5, y esas serán
						//las horas estimadas que se dieron en ese parcial.
						if( $total < 15 ){
							$xccursoss = New Xccursos();
							foreach( $xccursoss -> find_all_by_sql
								( "select count(*) as contar
								from xccursos xcc, xchorascursos xch
								where xcc.id = '".$curso_id."'
								and xcc.id = xch.curso_id" ) as $xch ){
								$total = $xch -> contar;
							}
							$total = $total * 5;
						}
						
						$porcentaje = round(($xalumnocurso -> faltas / $total) * 100);
						
						//$condicionado = $this -> condicionado($xalumnocurso -> registro, $xalumnocurso -> curso_id);
						//if( $xalumnocurso->tipo == "C" )
							//$condicionado = true;
						//if( $xalumnocurso->tipo == "I" )
							//$materia_irregular = true;
						
						// Checar si el alumno debía la materia, y si la debe
						//asignarle a la variable un valor de true, la cuál nos ayudará
						//a seguir calificando correctamente su materia.
						//if( $this -> materiaIrregular($xalumnocurso -> registro, $xccurso -> materia) ){
							//$materia_irregular = true;
						//}else{
							//$materia_irregular = false;
						//}
						
						if($porcentaje <= 20){
							  //este es el caso 1, cuando el alumno tiene =< del 20% de las faltas
							  $caso = 1;
						  }elseif($porcentaje > 20 && $porcentaje <= 40){
							  //este es el caso 2, cuando el alumno tiene > del 20% de las faltas pero <= 40%
							  $caso = 2;
						  }else{
							  //este es el caso 3, cuando el alumno tiene > del 40% de las faltas
							  $caso = 3;
						  }
						
						
							// Pasa la materia sin complicaciones
					switch($xalumnocurso -> tipo){					
					case 'R':      if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'ORDINARIO';
									  if($caso==2){
										 $situacion = 'EXTRAORDINARIO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'REGULARIZACION DIRECTA'; 
									  }
								  }else{
								     $situacion = 'EXTRAORDINARIO'; 
									 if($caso==2){
										 $situacion = 'EXTRAORDINARIO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'REGULARIZACION DIRECTA'; 
									  }
								  }
					
					             break;
					
					case 'I':   if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'REGULARIZACION';
									  if($caso==2){
										 $situacion = 'TITULO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }else{
								     $situacion = 'TITULO DE SUFICIENCIA'; 
									 if($caso==2){
										 $situacion = 'TITULO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }
					
					             break;
								 
					case 'C':   if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'REGULARIZACION';
									  if($caso==2){
										 $situacion = 'REGULARIZACION'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }else{
								     $situacion = 'BAJA DEFINITIVA'; 
									 if($caso==2){
										 $situacion = 'BAJA DEFINITIVA'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }
					
					             break;
					}
					
					
						$xalumnocurso -> situacion = $situacion;
					}
					
					
					if( $xalumnocurso -> calificacion == null || $xalumnocurso -> calificacion == '0' )
						$xalumnocurso -> calificacion = '0';
					if( $xalumnocurso -> faltas == null )
						$xalumnocurso -> faltas = '0';
					
					$xalumnocurso -> save();
				}
			}
			
			// A partir de aqui se hace la creación de extraordinarios
			$xalumnocursos = new Xalumnocursos();
            $xacursos = new Xalumnocursos();
            $alumnos = new Alumnos();
            $xextras = new Xextraordinarios();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
            // id, clavecurso, registro, calificacion, estado, tipo, periodo

            foreach( $xacursos -> find_all_by_sql
				 ( "Select * from xalumnocursos
					where periodo = ".$periodo."
					and ( situacion ='EXTRAORDINARIO FALTAS'
					or situacion ='TITULO FALTAS'
					or situacion ='EXTRAORDINARIO'
					or situacion ='TITULO DE SUFICIENCIA' )" ) as $xacurso ){
				if( $xextras -> find_first( "registro = ".$xacurso -> registro.
											" and periodo = ".$xacurso -> periodo.
												" and curso_id = '".$xacurso -> curso_id."'" ) ){
					continue;
				}
				$xextras -> curso_id = $xacurso -> curso_id;
				$xextras -> registro = $xacurso -> registro;
				$xextras -> calificacion = '300';
				if( $xacurso -> situacion == "EXTRAORDINARIO" ||
						$xacurso -> situacion == "EXTRAORDINARIO FALTAS" )
					$situacion = "E";
				else{
					if( $xacurso -> situacion == "TITULO DE SUFICIENCIA" ||
							$xacurso -> situacion == "TITULO FALTAS" )
						$situacion = "T";
					else
						continue;
				}
				$xextras -> estado = "?";
				$xextras -> tipo = $situacion;
				$xextras -> periodo = $xacurso -> periodo;
				$xextras -> create();
            }
			
		} // function evaluador_final_colomos()
		
		function evaluador_final_tonala(){
			
			//$this -> validar_usuario_calculo();
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
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
			
			$Xccursos = new Xtcursos();
			
			$xccursosss = new Xtcursos();
			
			foreach( $xccursosss -> find_all_by_sql
					( "Select clavecurso, id
						from xtcursos
						where periodo = ".$periodo ) as $xcc ){
				$curso_id = $xcc -> id;
				$xccurso = $Xccursos -> find_first("id='".$curso_id."'");
				
				$parcial = 3;
				
				$Xalumnocursos = new Xtalumnocursos();
				
				$materia_irregular = 0;
				
				foreach($Xalumnocursos -> find("curso_id='".$curso_id."' ORDER BY id") as $xal){
					
					$xalumnocurso = $Xalumnocursos -> find_first("id=".$xal -> id);
			
					$xalumnocurso -> situacion = "-";
				
					// Apartir de aquí se hace la evaluación final de este semestre.
					if($xalumnocurso -> calificacion1!=300
							&& $xalumnocurso -> calificacion2!=300
								&& $xalumnocurso -> calificacion3!=300){
						$tmp = 0;

						if($xalumnocurso -> calificacion1>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion1;
						}

						if($xalumnocurso -> calificacion2>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion2;
						}

						if($xalumnocurso -> calificacion3>100){
							$tmp += 0;
						}
						else{
							$tmp += $xalumnocurso -> calificacion3;
						}
						
						// Si el promedio de la calificación es menor a 69.9, no se redondea
						//y estará reprobado.
						// Por eso es que se le asigna el valor de 207, ya que 69 X 3 son 207
						//para la hora de que se haga el redondeo, la función de round() de php
						//no redondie a 70.
						if( ($tmp/3) < 69.9 && ($tmp/3) > 69.5){
							$tmp = 207;
						}
						$xalumnocurso -> calificacion = round($tmp/3);
						$xalumnocurso -> faltas =
										$xalumnocurso -> faltas1 +
											$xalumnocurso -> faltas2 +
													$xalumnocurso -> faltas3;

						$total = 0;
						$total = $xccurso -> horas1 + $xccurso -> horas2 + $xccurso -> horas3;
						
						// Si las horas son menor a 15, es muy probable
						//que el profesor haya capturado mal las horas,
						//así que se pasa a hacer la fórmula para obtener
						//las horas por default del parcial.
						//Es decir se consiguen las veces que se da esa materia
						//por semana y se multiplica por 5, y esas serán
						//las horas estimadas que se dieron en ese parcial.
						if( $total < 15 ){
							$xccursoss = New Xtcursos();
							foreach( $xccursoss -> find_all_by_sql
								( "select count(*) as contar
								from xtcursos xcc, xthorascursos xch
								where xcc.id = '".$curso_id."'
								and xcc.id = xch.curso_id" ) as $xch ){
								$total = $xch -> contar;
							}
							$total = $total * 5;
						}
						
						$porcentaje = round(($xalumnocurso -> faltas / $total) * 100);
						
						//$condicionado = $this -> condicionado($xalumnocurso -> registro, $xalumnocurso -> curso_id);
						if($porcentaje <= 20){
							  //este es el caso 1, cuando el alumno tiene =< del 20% de las faltas
							  $caso = 1;
						  }elseif($porcentaje > 20 && $porcentaje <= 40){
							  //este es el caso 2, cuando el alumno tiene > del 20% de las faltas pero <= 40%
							  $caso = 2;
						  }else{
							  //este es el caso 3, cuando el alumno tiene > del 40% de las faltas
							  $caso = 3;
						  }
						
						
							// Pasa la materia sin complicaciones
					switch($xalumnocurso -> tipo){					
					case 'R':      if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'ORDINARIO';
									  if($caso==2){
										 $situacion = 'EXTRAORDINARIO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'REGULARIZACION DIRECTA'; 
									  }
								  }else{
								     $situacion = 'EXTRAORDINARIO'; 
									 if($caso==2){
										 $situacion = 'EXTRAORDINARIO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'REGULARIZACION DIRECTA'; 
									  }
								  }
					
					             break;
					
					case 'I':   if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'REGULARIZACION';
									  if($caso==2){
										 $situacion = 'TITULO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }else{
								     $situacion = 'TITULO DE SUFICIENCIA'; 
									 if($caso==2){
										 $situacion = 'TITULO FALTAS'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }
					
					             break;
								 
					case 'C':   if( $xalumnocurso -> calificacion >=70 && $xalumnocurso -> calificacion <=100 ){
									  $situacion = 'REGULARIZACION';
									  if($caso==2){
										 $situacion = 'REGULARIZACION'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }else{
								     $situacion = 'BAJA DEFINITIVA'; 
									 if($caso==2){
										 $situacion = 'BAJA DEFINITIVA'; 
									  }
									  if($caso==3){
										  $situacion = 'BAJA DEFINITIVA'; 
									  }
								  }
					
					             break;
					}
					
						$xalumnocurso -> situacion = $situacion;
					}
					if( ($xalumnocurso -> calificacion < 70 && $condicionado) || ($porcentaje > 30 && $condicionado) ){
						$xalumnocurso -> situacion = "BAJA DEFINITIVA";
					}
					if( $xalumnocurso -> calificacion == null || $xalumnocurso -> calificacion == '0' )
						$xalumnocurso -> calificacion = '0';
					if( $xalumnocurso -> faltas == null )
						$xalumnocurso -> faltas = '0';
					
					$xalumnocurso -> save();
				}
			}
			
			// Llenar xtextraordinarios
			$xtalumnocursos = new Xtalumnocursos();
            $xtacursos = new Xtalumnocursos();
            $alumnos = new Alumnos();
            $xtextras = new Xtextraordinarios();
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
            // id, clavecurso, registro, calificacion, estado, tipo, periodo
            foreach( $xtacursos -> find_all_by_sql
					 ( "Select * from xtalumnocursos
						where periodo = ".$periodo."
						and ( situacion ='EXTRAORDINARIO FALTAS'
						or situacion ='TITULO FALTAS'
						or situacion ='EXTRAORDINARIO'
						or situacion ='TITULO DE SUFICIENCIA' )" ) as $xtacurso ){
				if( $xtextras -> find_first( "registro = ".$xtacurso -> registro.
											" and periodo = ".$xtacurso -> periodo.
												" and curso_id = '".$xtacurso -> curso_id."'" ) ){
					continue;
				}
				$xtextras -> curso_id = $xtacurso -> curso_id;
				$xtextras -> registro = $xtacurso -> registro;
				$xtextras -> calificacion = '300';
				if( $xtacurso -> situacion == "EXTRAORDINARIO" ||
						$xtacurso -> situacion == "EXTRAORDINARIO FALTAS" )
					$situacion = "E";
				else{
					if( $xtacurso -> situacion == "TITULO DE SUFICIENCIA" ||
							$xtacurso -> situacion == "TITULO FALTAS" )
						$situacion = "T";
					else
						continue;
				}
				$xtextras -> estado = "?";
				$xtextras -> tipo = $situacion;
				$xtextras -> periodo = $xtacurso -> periodo;
				$xtextras -> create();
            }
			
		} // function evaluador_final_tonala()
		
        function materiaIrregular($registro, $clavemat){
			
			//$registro contiene el registro del alumno
			//$clavemat contiene la clave de la materia
			
			$Periodos = new Periodos();
			$periodo_anterior = $Periodos -> get_periodo_anterior();
			$periodo_antesanterior = $Periodos -> get_periodo_antesanterior();
			
			$xccursos	= new Xccursos();
			$Alumnos	= new Alumnos();
			
			$alumno = $Alumnos -> find_first( "miReg = ".$registro );
			
			if( $alumno -> enPlantel == "c" || $alumno -> enPlantel == "C" ){
				
				//$xccurso = $xccursos -> find_first("clavecurso='".$curso."'");
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc
					inner join xalumnocursos xal
					on xal.curso_id = xcc.id
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$clavemat."'" ) as $xcc ){
					// Si el periodo que se trae es el pasado, o uno antes del pasado
					//si nos importa y por lo que esta materia si es irregular
					//asi que debera de tratarse como tal.
					if( $xcc -> periodo == $periodo_anterior ||
							$xcc -> periodo == $periodo_antesanterior ){
						return true;
					}
				}
				// También reviso en las tablas de Tonala
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xccursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc
					inner join xtalumnocursos xal
					on xal.curso_id = xtc.id
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$clavemat."'" ) as $xtc ){
					if( $xtc -> periodo == $periodo_anterior ||
							$xtc -> periodo == $periodo_antesanterior ){
						return true;
					}
				}
			}
			else{
				//$xtcurso = $xtcursos -> find_first("clavecurso='".$curso."'");
				foreach( $xccursos -> find_all_by_sql
					( "select xtc.periodo
					From xtcursos xtc
					inner join xtalumnocursos xal
					on xal.curso_id = xtc.id
					where xal.registro = ".$alumno -> miReg."
					and xtc.materia = '".$clavemat."'" ) as $xtc ){
					if( $xtc -> periodo == $periodo_anterior ||
							$xtc -> periodo == $periodo_antesanterior ){
						return true;
					}
				}
				// También reviso en las tablas de Colomos
				//para saber si el alumno no se cambio de plantel
				//y curso materias en el otro plantel.
				foreach( $xccursos -> find_all_by_sql
					( "select xcc.periodo
					From xccursos xcc
					inner join xalumnocursos xal
					on xal.curso_id = xcc.id
					where xal.registro = ".$alumno -> miReg."
					and xcc.materia = '".$clavemat."'" ) as $xcc ){
					if( $xcc -> periodo == $periodo_anterior ||
							$xcc -> periodo == $periodo_antesanterior ){
						return true;
					}
				}
			}
			return false;
        } // function materiaIrregular($registro, $clavemat)
		
       function condicionado($alumno, $curso_id){
			
			//$alumno contiene el registro del alumno
			//$curso_id contiene el id del curso del alumno
			
			// Para saber si un alumno está condicionado
			$xccursos	= new Xccursos();
			$xtcursos	= new Xtcursos();
			$xalcursos	= new Xalumnocursos();
			$xtalcursos	= new Xtalumnocursos();
			
			$mmateria	= new Materia();
			$alumnos	= new Alumnos();
			
			$Periodos = new Periodos();
			$periodo_anterior = $Periodos -> get_periodo_anterior();
			$periodo_antesanterior = $Periodos -> get_periodo_antesanterior();
			
			$alumno = $alumnos -> find_first( "miReg = ".$alumno );
			
			// Si la variable condicionado llega a 2, significa que el alumno si estaba condicionado...
			$condicionado = 0;
			
			if( $alumno -> enPlantel == "c" || $alumno -> enPlantel == "C" ){
				$xccurso = $xccursos -> find_first("id='".$curso_id."'");
				if( isset($xccurso->materia) ){
					foreach( $xccursos -> find_all_by_sql
						( "select xcc.periodo
						From xccursos xcc, xalumnocursos xal
						where xal.registro = ".$alumno -> miReg."
						and xcc.materia = '".$xccurso -> materia."'
						and xal.curso_id = xcc.id" ) as $xcc ){
						// Si el periodo que se trae es el pasado, o uno antes del pasado
						//si nos importa y por lo que esta materia si es irregular
						//asi que debera de tratarse como tal.
						if( $xcc -> periodo == $periodo_anterior ||
								$xcc -> periodo == $periodo_antesanterior ){
							$condicionado++;
						}
					}
					// También reviso en las tablas de Tonala
					//para saber si el alumno no se cambio de plantel
					//y curso materias en el otro periodo.
					foreach( $xtcursos -> find_all_by_sql
						( "select xtc.periodo
						From xtcursos xtc, xtalumnocursos xal
						where xal.registro = ".$alumno -> miReg."
						and xtc.materia = '".$xccurso -> materia."'
						and xal.curso_id = xtc.id" ) as $xtc ){
						if( $xtc -> periodo == $periodo_anterior ||
								$xtc -> periodo == $periodo_antesanterior ){
							$condicionado++;
						}
					}
					if( $xalcursos -> find_all_by_sql("
							select xcc.materia, xal.*
							from xalumnocursos xal
							inner join xccursos xcc
							on xal.curso_id = xcc.id
							and xcc.materia = '".$xccurso -> materia."'
							and xal.situacion = 'BAJA DEFINITIVA'
							and xal.registro = '".$alumno -> miReg."'
							and xal.periodo = '".$periodo_anterior."'" ) ){
						$condicionado += 2;
					}
					if( $xalcursos -> find_all_by_sql("
							select xcc.materia, xal.*
							from xtalumnocursos xal
							inner join xtcursos xcc
							on xal.curso_id = xcc.id
							and xcc.materia = '".$xccurso -> materia."'
							and xal.situacion = 'BAJA DEFINITIVA'
							and xal.registro = '".$alumno -> miReg."'
							and xal.periodo = '".$periodo_anterior."'" ) ){
						$condicionado += 2;
					}
				}
			}
			else{
				$xtcurso = $xtcursos -> find_first("id='".$curso_id."'");
				if( isset($xtcurso->materia) ){
					foreach( $xtcursos -> find_all_by_sql
						( "select xtc.periodo
						From xtcursos xtc, xtalumnocursos xal
						where xal.registro = ".$alumno -> miReg."
						and xtc.materia = '".$xtcurso -> materia."'
						and xal.curso_id = xtc.id" ) as $xtc ){
						if( $xtc -> periodo == $periodo_anterior ||
								$xtc -> periodo == $periodo_antesanterior ){
							$condicionado++;
						}
					}
					// También reviso en las tablas de Colomos
					//para saber si el alumno no se cambio de plantel
					//y curso materias en el otro periodo.
					foreach( $xccursos -> find_all_by_sql
						( "select xcc.periodo
						From xccursos xcc, xalumnocursos xal
						where xal.registro = ".$alumno -> miReg."
						and xcc.materia = '".$xtcurso -> materia."'
						and xal.curso_id = xcc.id" ) as $xcc ){
						if( $xcc -> periodo == $periodo_anterior ||
								$xcc -> periodo == $periodo_antesanterior ){
							$condicionado++;
						}
					}
					if( $xalcursos -> find_all_by_sql("
							select xcc.materia, xal.*
							from xtalumnocursos xal
							inner join xtcursos xcc
							on xal.curso_id = xcc.id
							and xcc.materia = '".$xtcurso -> materia."'
							and xal.situacion = 'BAJA DEFINITIVA'
							and xal.registro = '".$alumno -> miReg."'
							and xal.periodo = '".$periodo_anterior."'" ) ){
						$condicionado += 2;
					}
					if( $xalcursos -> find_all_by_sql("
						select xcc.materia, xal.*
						from xalumnocursos xal
						inner join xccursos xcc
						on xal.curso_id = xcc.id
						and xcc.materia = '".$xtcurso -> materia."'
						and xal.situacion = 'BAJA DEFINITIVA'
						and xal.registro = '".$alumno -> miReg."'
						and xal.periodo = '".$periodo_anterior."'" ) ){
						$condicionado += 2;
					}
				}
			}
			if( $condicionado > 1 )
				return true;
			else
				return false;
        } // function condicionado($alumno, $curso)
		
		function inscribir_primeros(){
            if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
            }
			$Periodos = new Periodos();
            $periodo = $Periodos->get_periodo_actual();

            $xccursos = new Xccursos();

            $cursos_id = $this -> post ("curso_id");
			$registros = $this -> post ("registros");
			
			//list($usuario, $contraseña, $uid, $gid, $extra);
			$array_cursos_id = split(',', $cursos_id);
			$array_registros = split(',', $registros);
			
			$counter = 0;
			$yaestabainscrito = 0;
			$inscritos = 0;
			foreach($array_cursos_id as $curso_id){
				$xccurso = $xccursos -> find_first("id= '".$curso_id."'");
				foreach($array_registros as $registro){
					$counter++;
					$Xalumnocursos = new Xalumnocursos();
					if ( $xal = $Xalumnocursos -> find_first ( "registro = ".$registro."
								and id = '".$curso_id."'" ) ){
						echo "El alumno con registro ".$registro." ya
								se encontraba inscrito al curso ".$xccurso->division.$xccurso->id."<br />";
						$yaestabainscrito++;
					}
					else{
						$inscritos++;
						$xalumnocurso = new Xalumnocursos();
						$xalumnocurso -> registro = $registro;
						$xalumnocurso -> periodo = $periodo;

						$xalumnocurso -> curso_id = $xccurso -> id;
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
						
						$xccurso->disponibilidad -= 1;
						
						if($xccurso->disponibilidad <= 0)
							$xccurso->disponibilidad = '0';
						$xccurso -> save();
					}
				}
			}
			echo "Contador de los que se inscribieron ".$inscritos." ".($inscritos/count($array_cursos_id))."<br />";
			echo "Contador de los que ya estaban inscritos ".$yaestabainscrito."<br />";
			echo "Cuantos fueron ".$counter."<br />";
		} // function inscribir_primeros()
		
		function inscribir_primeros_tonala(){
            if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
            }
			$Periodos = new Periodos();
            $periodo = $Periodos->get_periodo_actual();

            $xccursos = new Xtcursos();

            $cursos_id = $this -> post ("curso_id");
			$registros = $this -> post ("registros");
			
			//list($usuario, $contraseña, $uid, $gid, $extra);
			$array_cursos_id = split(',', $cursos_id);
			$array_registros = split(',', $registros);
			
			$counter = 0;
			$yaestabainscrito = 0;
			$inscritos = 0;
			foreach($array_cursos_id as $curso_id){
				$xccurso = $xccursos -> find_first("id= '".$curso_id."'");
				foreach($array_registros as $registro){
					$counter++;
					$Xalumnocursos = new Xtalumnocursos();
					if ( $xal = $Xalumnocursos -> find_first ( "registro = ".$registro."
								and id = '".$curso_id."'" ) ){
						echo "El alumno con registro ".$registro." ya
								se encontraba inscrito al curso ".$xccurso->division.$xccurso->id."<br />";
						$yaestabainscrito++;
					}
					else{
						$inscritos++;
						$xalumnocurso = new Xtalumnocursos();
						$xalumnocurso -> registro = $registro;
						$xalumnocurso -> periodo = $periodo;

						$xalumnocurso -> curso_id = $xccurso -> id;
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
						
						$xccurso->disponibilidad -= 1;
						
						if($xccurso->disponibilidad <= 0)
							$xccurso->disponibilidad = '0';
						$xccurso -> save();
					}
				}
			}
			echo "Contador de los que se inscribieron ".$inscritos." ".($inscritos/count($array_cursos_id))."<br />";
			echo "Contador de los que ya estaban inscritos ".$yaestabainscrito."<br />";
			echo "Cuantos fueron ".$counter."<br />";

		} // function inscribir_primeros_tonala()
		
		function poner_tipo_en_materias_colomos(){ // Chido (Y)
			
			$Xalumnocursos = new Xalumnocursos();
			$Xalumnocursos1 = new Xalumnocursos();
			
			$Periodos = new Periodos();
			$periodo_anterior = $Periodos -> get_periodo_anterior();
			$periodo = $Periodos -> get_periodo_actual();
			
			foreach($Xalumnocursos->find_all_by_sql(
					"select xal.id, xal.registro, xal.curso_id, xal.calificacion, xal.tipo, xal.situacion,
					xcc.materia
					from xalumnocursos xal
					join xccursos xcc
					on xal.curso_id = xcc.id
					and xal.periodo = '".$periodo."'
					order by xal.registro") as $xalumnocursos){
				echo $xalumnocursos->materia." ".$xalumnocursos->tipo."<br />";
				foreach($Xalumnocursos1->find_all_by_sql(
						"select xal.registro, xal.curso_id, xal.calificacion, xal.tipo, xal.situacion,
						xcc.materia
						from xalumnocursos xal
						join xccursos xcc
						on xal.curso_id = xcc.id
						and xal.periodo = '".$periodo_anterior."'
						and xal.registro = '".$xalumnocursos->registro."'
						and materia = '".$xalumnocursos->materia."'
						order by xal.registro;") as $xalumnocursos1 ){
						
					if($xalumnocursos->tipo == "R"){
						$Xalumnocursos2 = new Xalumnocursos();
						$Xalumnocursos2->find_first("id = '".$xalumnocursos->id."'");
						$Xalumnocursos2->tipo = "I";
						$Xalumnocursos2->save();
					}
					if($xalumnocursos->tipo == "I" || $xalumnocursos->tipo == "C"){
						$Xalumnocursos2 = new Xalumnocursos();
						$Xalumnocursos2->find_first("id = '".$xalumnocursos->id."'");
						$Xalumnocursos2->tipo = "C";
						$Xalumnocursos2->save();
					}
				}
			}
		} // function poner_tipo_en_materias_colomos()
		
		function poner_tipo_en_materias_tonala(){ // Chido (Y)
			
			$Xalumnocursos = new Xtalumnocursos();
			$Xalumnocursos1 = new Xtalumnocursos();
			
			$Periodos = new Periodos();
			$periodo_anterior = $Periodos -> get_periodo_anterior();
			$periodo = $Periodos -> get_periodo_actual();
			
			foreach($Xalumnocursos->find_all_by_sql(
					"select xal.id, xal.registro, xal.curso_id, xal.calificacion, xal.tipo, xal.situacion,
					xcc.materia
					from xtalumnocursos xal
					join xtcursos xcc
					on xal.curso_id = xcc.id
					and xal.periodo = '".$periodo."'
					order by xal.registro") as $xalumnocursos){
				
				foreach($Xalumnocursos1->find_all_by_sql(
						"select xal.registro, xal.curso_id, xal.calificacion, xal.tipo, xal.situacion,
						xcc.materia
						from xtalumnocursos xal
						join xtcursos xcc
						on xal.curso_id = xcc.id
						and xal.periodo = '".$periodo_anterior."'
						and xal.registro = '".$xalumnocursos->registro."'
						and materia = '".$xalumnocursos->materia."'
						order by xal.registro;") as $xalumnocursos1 ){
						
					if($xalumnocursos1->tipo == "R"){
						$Xalumnocursos2 = new Xtalumnocursos();
						$Xalumnocursos2->find_first("id = '".$xalumnocursos->id."'");
						$Xalumnocursos2->tipo = "I";
						$Xalumnocursos2->save();
					}
					if($xalumnocursos1->tipo == "I" || $xalumnocursos1->tipo == "C"){
						$Xalumnocursos2 = new Xtalumnocursos();
						$Xalumnocursos2->find_first("id = '".$xalumnocursos->id."'");
						$Xalumnocursos2->tipo = "C";
						$Xalumnocursos2->save();
					}
				}
			}
		} // function poner_tipo_en_materias_tonala()
		
		function pasar_xalumnocursosbt_a_xtalumnocursosbt(){
			$this -> validar_usuario_calculo();
			
			$Xalumnocursosbt = new XAlumnocursosbt();
			
			$Xalumnocursosbt = new Xalumnocursosbt();
			
			foreach( $Xalumnocursosbt->find_all_by_sql("
					select * from xalumnocursosbt
					where curso like 'TCT%'
					or curso < '999'") as $xalbt ){
				$Xtalumnocursosbt = new Xtalumnocursosbt();
				
				$Xtalumnocursosbt->id_xc = $xalbt->id;
				$Xtalumnocursosbt->periodo = $xalbt->periodo;
				
				if(eregi("TCT", $xalbt->curso)){
					$xalbt->curso = substr($xalbt->curso, 3);
					echo $xalbt->curso;
				}
				
				$Xtalumnocursosbt->id_xc = $xalbt->id_xc;
				$Xtalumnocursosbt->curso_id = $xalbt->curso;
				$Xtalumnocursosbt->registro = $xalbt->registro;
				$Xtalumnocursosbt->faltas1 = $xalbt->faltas1;
				$Xtalumnocursosbt->calificacion1 = $xalbt->calificacion1;
				$Xtalumnocursosbt->faltas2 = $xalbt->faltas;
				$Xtalumnocursosbt->calificacion2 = $xalbt->calificacion2;
				$Xtalumnocursosbt->faltas3 = $xalbt->faltas3;
				$Xtalumnocursosbt->calificacion3 = $xalbt->calificacion3;
				$Xtalumnocursosbt->faltas = $xalbt->faltas;
				$Xtalumnocursosbt->calificacion = $xalbt->calificacion;
				$Xtalumnocursosbt->situacion = $xalbt->situacion;
				$Xtalumnocursosbt->tipo = $xalbt->tipo;
				
				if( $Xtalumnocursosbt->tipo == null || $Xtalumnocursosbt->tipo == ""){
					$Xtalumnocursosbt->tipo = "-";
				}
				if( ((int)$Xtalumnocursosbt->curso_id) > 999 )
					continue;
				
				$xalbt->delete();
				
				$Xtalumnocursosbt->create();
			}
			
		} // function pasar_xalumnocursosbt_a_xtalumnocursosbt()
		
		function borrar_division_xalumnocursosbt(){
			$this -> validar_usuario_calculo();
			
			$Xalumnocursosbt = new XAlumnocursosbt();
			
			$Xalumnocursosbt = new Xalumnocursosbt();
			
			foreach( $Xalumnocursosbt->find_all_by_sql("
					select * from xalumnocursosbt") as $xalbt ){
				
				if(eregi("MCT", $xalbt->curso) || eregi("IIM", $xalbt->curso) || eregi("IEC", $xalbt->curso) ||
						eregi("TCB", $xalbt->curso)){
					$xalbt->curso = substr($xalbt->curso, 3);
				}
				if( $xalbt->tipo == null || $xalbt->tipo == ""){
					$xalbt->tipo = "-";
				}
				$xalbt->update();
			}
		} // function borrar_division_xalumnocursosbt()
		
		function promocion_alumnos(){
			
		}
		
		function validar_usuario_calculo(){
			if(Session::get_data('tipousuario')!="CALCULO"){
				$this->redirect('/');
			}
		} // function validar_usuario_calculo()
		
		function modificarBaseDeDatos(){
			$this -> validar_usuario_calculo();
		}
		function modificarBaseDeDatos2(){
			$this -> validar_usuario_calculo();
			
			$query = $this -> post("query");
			
			$alumnos = new Alumnos();
			$query = str_replace("\\", "", $query);
			foreach( $alumnos -> find_all_by_sql($query) as $al ){
				break;
			}
			echo $query."<br />";
			mysql_query($query);
			
			echo "Bien";
		}
		function hacerConsultas(){
			$this -> validar_usuario_calculo();
		}
		function hacerConsultas2(){
			
			$this -> validar_usuario_calculo();
			
			$conn = mysql_connect('10.168.2.58', 'kumbiaing', 'bailador_ing_i');
			if (!$conn){
				die('Could not connect: ' . mysql_error());
			}
			mysql_select_db('ingenieria');
			
			$query = $this -> post("query");
			$tablas = $this -> post("tablas");
			$query = str_replace("\\", "", $query);
			$cuantasTablas = 0;
			$largo = strlen($tablas);
			for( $i = 0; $i < $largo; $i++ ){
				if( eregi( ",", substr($tablas, $i, 1) ) ){
					$cuantasTablas++;
				}
			}
			
			switch($cuantasTablas){
				case 1:
					list($tabla1) = split(',', $tablas);
					$result = mysql_query("Select * From ".$tabla1);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla1Nombre[$i] = $meta->name;
						$tabla1Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
						break;
				
				case 2:
					list($tabla1, $tabla2) = split(',', $tablas);
					// Info de la tabla1
					$result = mysql_query("Select * From ".$tabla1);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla1Nombre[$i] = $meta->name;
						$tabla1Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla2
					$result = mysql_query("Select * From ".$tabla2);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla2Nombre[$i] = $meta->name;
						$tabla2Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
						break;
				
				case 3:
					list($tabla1, $tabla2, $tabla3) = split(',', $tablas);
					// Info de la tabla1
					$result = mysql_query("Select * From ".$tabla1);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla1Nombre[$i] = $meta->name;
						$tabla1Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla2
					$result = mysql_query("Select * From ".$tabla2);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla2Nombre[$i] = $meta->name;
						$tabla2Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla3
					$result = mysql_query("Select * From ".$tabla3);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla3Nombre[$i] = $meta->name;
						$tabla3Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
						break;
				case 4:
					list($tabla1, $tabla2, $tabla3, $tabla4) = split(',', $tablas);
					// Info de la tabla1
					$result = mysql_query("Select * From ".$tabla1);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla1Nombre[$i] = $meta->name;
						$tabla1Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla2
					$result = mysql_query("Select * From ".$tabla2);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla2Nombre[$i] = $meta->name;
						$tabla2Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla3
					$result = mysql_query("Select * From ".$tabla3);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla3Nombre[$i] = $meta->name;
						$tabla3Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla4
					$result = mysql_query("Select * From ".$tabla4);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla4Nombre[$i] = $meta->name;
						$tabla4Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
						break;
				case 5:
					list($tabla1, $tabla2, $tabla3, $tabla4, $tabla5) = split(',', $tablas);
					// Info de la tabla1
					$result = mysql_query("Select * From ".$tabla1);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla1Nombre[$i] = $meta->name;
						$tabla1Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla2
					$result = mysql_query("Select * From ".$tabla2);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla2Nombre[$i] = $meta->name;
						$tabla2Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla3
					$result = mysql_query("Select * From ".$tabla3);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla3Nombre[$i] = $meta->name;
						$tabla3Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla4
					$result = mysql_query("Select * From ".$tabla4);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla4Nombre[$i] = $meta->name;
						$tabla4Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
					
					// Info de la tabla5
					$result = mysql_query("Select * From ".$tabla5);
					// get column metadata
					$i = 0;
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$tabla5Nombre[$i] = $meta->name;
						$tabla5Tipo[$i] = $meta->type;
						$i++;
					}
					mysql_free_result($result);
						break;
			} // switch($cuantasTablas)
			
			// tabla1
			// tabla1Nombre[]
			// tabla5Tipo[]
			
			
			// Creamos un objeto para acceder a la base de datos
			//desde "kumbia"
			$alumnos = new Alumnos();
			$i = 0;
			foreach( $alumnos -> find_all_by_sql($query) as $al ){
				
				if( isset($tabla1) ){
					echo "<br />Tabla $tabla1<br />";
					for( $j = 0; $j < count($tabla1Nombre); $j++ ){
						echo "$tabla1Nombre[$j]: ".$al -> $tabla1Nombre[$j]."<br />";
					}
				}
				if( isset($tabla2) ){
					echo "<br />Tabla $tabla2<br />";
					for( $j = 0; $j < count($tabla2Nombre); $j++ ){
						echo "$tabla2Nombre[$j]: ".$al -> $tabla2Nombre[$j]."<br />";
					}
				}
				if( isset($tabla3) ){
					echo "<br />Tabla $tabla3<br />";
					for( $j = 0; $j < count($tabla3Nombre); $j++ ){
						echo "$tabla3Nombre[$j]: ".$al -> $tabla3Nombre[$j]."<br />";
					}
				}
				if( isset($tabla4) ){
					echo "<br />Tabla $tabla4<br />";
					for( $j = 0; $j < count($tabla4Nombre); $j++ ){
						echo "$tabla4Nombre[$j]: ".$al -> $tabla4Nombre[$j]."<br />";
					}
				}
				if( isset($tabla5) ){
					echo "<br />Tabla $tabla5<br />";
					for( $j = 0; $j < count($tabla5Nombre); $j++ ){
						echo "$tabla51Nombre[$j]: ".$al -> $tabla5Nombre[$j]."<br />";
					}
				}
				$i++;
			}
			echo "<br />rows: $i";
		}
    }
?>
<?
			/*
			// $this -> post("query");
			$conn = mysql_connect('localhost', 'root', '');
			if (!$conn) {
				die('Could not connect: ' . mysql_error());
			}
			mysql_select_db('ingenieria');
			$result = mysql_query('select * from xpermisoscaptura');
			if (!$result) {
				die('Query failed: ' . mysql_error());
			}
			// get column metadata
			$i = 0;
			while ($i < mysql_num_fields($result)) {
				echo "Information for column $i:<br />\n";
				$meta = mysql_fetch_field($result, $i);
				if (!$meta) {
					echo "No information available<br />\n";
				}
				echo "<pre>
			name:         $meta->name
			table:        $meta->table
			type:         $meta->type
			</pre>";
				$i++;
			}
			mysql_free_result($result);
			*/

			/*
			$conn = mysql_connect('localhost', 'root', '');
			if (!$conn) {
				die('Could not connect: ' . mysql_error());
			}
			mysql_select_db('ingeineria');
			$result = mysql_query('select * from xalumnocursos');
			if (!$result) {
				die('Query failed: ' . mysql_error());
			}
			// get column metadata
			$i = 0;
			while ($i < mysql_num_fields($result)) {
				echo "Information for column $i:<br />\n";
				$meta = mysql_fetch_field($result, $i);
				if (!$meta) {
					echo "No information available<br />\n";
				}
				echo "<pre>
			blob:         $meta->blob
			max_length:   $meta->max_length
			multiple_key: $meta->multiple_key
			name:         $meta->name
			not_null:     $meta->not_null
			numeric:      $meta->numeric
			primary_key:  $meta->primary_key
			table:        $meta->table
			type:         $meta->type
			default:      $meta->def
			unique_key:   $meta->unique_key
			unsigned:     $meta->unsigned
			zerofill:     $meta->zerofill
			</pre>";
				$i++;
			}
			mysql_free_result($result);
			*/
?>


<?
/*
Query para sacar reporte de profesores que no han capturado
en Titulo y en extraordinarios.
Select xcc.clavecurso, xcc.materia, m.nombre, xcc.nomina, ma.nombre
from xtextraordinarios xext, xtpermisoscaptura xp, xtcursos xcc, materia m, maestros ma
where xext.periodo= 12010
and xext.calificacion = 0
and xext.clavecurso = xp.curso
and ( xp.ncapturas4 = 0
and xp.ncapturas5 = 0 )
and xp.curso = xcc.clavecurso
and xcc.materia = m.clave
and xcc.nomina = ma.nomina
group by xp.id;
*/
?>