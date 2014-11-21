<?php

class AspirantesController extends ApplicationController {

    function index() {
        $this->checarusuario();
    }

    function aspirantes() {
        $this->checarusuario();
    }

    function reporte($nivel = "I", $carrera = 400, $periodo = 0) {
        $this->checarusuario();
        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $maspirantes = new Aspirantes();

        if ($nivel == "tgo") {
            $this->aspirantes = $maspirantes->find
                    ("nivel='T' AND carrera=" . $carrera . " ORDER BY nivel DESC,carrera LIMIT $x,25");
            $this->total = $maspirantes->count("nivel='T' AND carrera=" . $carrera . "") / 25;
        } else {
            $this->aspirantes = $maspirantes->find
                    ("nivel='I' AND carrera=" . $carrera . " ORDER BY nivel DESC,carrera LIMIT $x,25");
            $this->total = $maspirantes->count("nivel='I' AND carrera=" . $carrera . "") / 25;
        }

        if ($nivel == "" || $nivel == "-") {
            $this->aspirantes = $maspirantes->find("1 ORDER BY nivel DESC,carrera LIMIT $x,25");
            $this->total = $maspirantes->count() / 25;
        }

        $this->carrerita = $carrera;

        if ($nivel == "tgo") {
            switch ($carrera) {
                case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN";
                    break;
                case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
                    break;
                case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN";
                    break;
                case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES";
                    break;
                case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA";
                    break;
                case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA";
                    break;
                case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ";
                    break;
                case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS";
                    break;
            }
        } else {
            switch ($carrera) {
                case 400: $carrera = "INGENIERÍA INDUSTRIAL";
                    break;
                case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA";
                    break;
                case 801: $carrera = "INGENIERÍA EN MECATRÓNICA";
                    break;
            }
        }

        if ($nivel == "" || $nivel == "-") {
            $carrera = "TODOS LAS CARRERAS Y NIVELES";
            $nivel = "-";
        }

        $this->carrera = $carrera;
        $this->nivel = $nivel;

        $this->carreras["T"][200] = "TGO. EN INFORMÁTICA Y COMPUTACIÓN";
        $this->carreras["T"][400] = "TGO. EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
        $this->carreras["T"][500] = "TGO. EN CONTRUCCIÓN";
        $this->carreras["T"][600] = "TGO. EN ELECTRÓNICA Y COMUNICACIONES";
        $this->carreras["T"][700] = "TGO. EN ELECTROTECNIA";
        $this->carreras["T"][800] = "TGO. EN MÁQUINAS HERRAMIENTA";
        $this->carreras["T"][801] = "TGO. EN MECÁNICA AUTOMOTRIZ";
        $this->carreras["T"][804] = "TGO. EN MANUFACTURA DE PLÁSTICOS";
        $this->carreras["I"][400] = "ING. INDUSTRIAL";
        $this->carreras["I"][600] = "ING. EN ELECTRÓNICA Y COMPUTACIÓN";
        $this->carreras["I"][801] = "ING. EN MECATRÓNICA";
    }

    function admitidos($nivel = "I", $carrera = 400, $x = 0) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $maspirantes = new Aspirantes();

        unset($this->periodoo);
        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";

        $this->periodoo .= substr($periodo, 1, 4);

        if ($nivel == "tgo") {
            $this->aspirantes = $maspirantes->find("admitido=1 
						AND nivel='T' AND carrera=" . $carrera . " ORDER BY fecha_at LIMIT $x,25");
            $this->total = $maspirantes->count("admitido=1 AND nivel='T' AND carrera=" . $carrera . "") / 25;
        } else {
            $this->aspirantes = $maspirantes->find("admitido=1 AND nivel='I' 
						AND carrera=" . $carrera . " ORDER BY fecha_at LIMIT $x,25");
            $this->total = $maspirantes->count("admitido=1 AND nivel='I' AND carrera=" . $carrera . "") / 25;
        }

        if ($nivel == "" || $nivel == "-") {
            $this->aspirantes = $maspirantes->find("admitido=1 ORDER BY fecha_at LIMIT $x,25");
            $this->total = $maspirantes->count("admitido=1") / 25;
        }

        $this->carrerita = $carrera;

        if ($nivel == "tgo") {
            switch ($carrera) {
                case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN";
                    break;
                case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
                    break;
                case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN";
                    break;
                case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES";
                    break;
                case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA";
                    break;
                case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA";
                    break;
                case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ";
                    break;
                case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS";
                    break;
            }
        } else {
            switch ($carrera) {
                case 400: $carrera = "INGENIERÍA INDUSTRIAL";
                    break;
                case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA";
                    break;
                case 801: $carrera = "INGENIERÍA EN MECATRÓNICA";
                    break;
            }
        }

        if ($nivel == "" || $nivel == "-") {
            $carrera = "TODOS LAS CARRERAS Y NIVELES";
            $nivel = "-";
        }

        $this->carrera = $carrera;
        $this->nivel = $nivel;
    }

    function estadisticas() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";
        $this->periodoo .= substr($periodo, 1, 4);

        $maspirantes = new Aspirantes();
        // TODOS
        unset($this->todos);
        unset($this->ingenieria);
        unset($this->industrial);
        //unset($this->electronica2);
        unset($this->mecatronica);
		unset($this->software);
		unset($this->electronico);

        unset($this->industrialHombres);
        //unset($this->electronica2Hombres);
		unset($this->softwareHombres);
		unset($this->electronicoHombres);
        unset($this->mecatronicaHombres);
        unset($this->industrialMujeres);
        //unset($this->electronica2Mujeres);
		unset($this->softwareMujeres);
		unset($this->electronicoMujeres);
        unset($this->mecatronicaMujeres);
        // PD
        unset($this->todosPD);
        unset($this->hombresTodosPD);
        unset($this->mujeresTodosPD);
        unset($this->ingenieriaPD);
        unset($this->industrialPD);
        //unset($this->electronica2PD);
		unset($this->softwarePD);
		unset($this->electronicoPD);
        unset($this->mecatronicaPD);

        unset($this->industrialHombresPD);
       //unset($this->electronica2HombresPD);
	   unset($this->softwareHombresPD);
		unset($this->electronicoHombresPD);
        unset($this->mecatronicaHombresPD);
        unset($this->industrialMujeresPD);
        //unset($this->electronica2MujeresPD);
		unset($this->softwareMujeresPD);
		unset($this->electronicoMujeresPD);
        unset($this->mecatronicaMujeresPD);
        // CE
        unset($this->todosCE);
        unset($this->hombresTodosCE);
        unset($this->mujeresTodosCE);
        unset($this->ingenieriaCE);
        unset($this->industrialCE);
        //unset($this->electronica2CE);
		unset($this->softwareCE);
		unset($this->electronicoCE);
        unset($this->mecatronicaCE);

        unset($this->industrialHombresCE);
       // unset($this->electronica2HombresCE);
	   unset($this->softwareHombresCE);
		unset($this->electronicoHombresCE);
        unset($this->mecatronicaHombresCE);
        unset($this->industrialMujeresCE);
        //unset($this->electronica2MujeresCE);
        unset($this->mecatronicaMujeresCE);
		unset($this->softwareMujeresCE);
		unset($this->electronicoMujeresCE);
		
        unset($this->industrialVespertino);
        unset($this->industrialMatutino);
        unset($this->mecatronicaTonala);
        unset($this->mecatronicaColomos);

        $this->todos = $maspirantes->count("periodo=" . $configuracion->periodo);
        // $this -> tecnologo = $maspirantes -> count("nivel='T' AND periodo=".$configuracion -> periodo);
        $this->ingenieria = $maspirantes->count("nivel='I' AND periodo=" . $configuracion->periodo);
        $this->hombresIng = $maspirantes->count("nivel='I' 
					AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
        $this->mujeresIng = $maspirantes->count("nivel='I' 
					AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");

        /*
          $this -> informatica = $maspirantes -> count("carrera=200 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> control = $maspirantes -> count("carrera=400 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> construccion = $maspirantes -> count("carrera=500 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> electronica = $maspirantes -> count("carrera=600 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> electrotecnia = $maspirantes -> count("carrera=700 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> maquinas = $maspirantes -> count("carrera=800 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> mecanica = $maspirantes -> count("carrera=801 AND nivel='T' AND periodo=".$configuracion -> periodo);
          $this -> manufactura = $maspirantes -> count("carrera=804 AND nivel='T' AND periodo=".$configuracion -> periodo);
         */

        $this->industrial = $maspirantes->count("carrera=400 AND nivel='I' AND periodo=" . $configuracion->periodo);
       // $this->electronica2 = $maspirantes->count("carrera=600 AND nivel='I' AND periodo=" . $configuracion->periodo);
	   $this->software = $maspirantes->count("carrera=900 AND nivel='I' AND periodo=" . $configuracion->periodo);
	   $this->electronico = $maspirantes->count("carrera=901 AND nivel='I' AND periodo=" . $configuracion->periodo);
        $this->mecatronica = $maspirantes->count("carrera=801 AND nivel='I' AND periodo=" . $configuracion->periodo);

        $this->industrialVespertino = $maspirantes->count("carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 1");
        $this->industrialMatutino = $maspirantes->count("carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 2");
        $this->mecatronicaColomos = $maspirantes->count("carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 4");
        $this->mecatronicaTonala = $maspirantes->count("carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 5");


        $this->industrialHombresVesp = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=1");
        $this->industrialHombresMat = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=2");
        $this->industrialMujeresVesp = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=1");
        $this->industrialMujeresMat = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=2");
        $this->mecatronicaHombresColomos = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 4");
        $this->mecatronicaMujeresColomos = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 4");
        $this->mecatronicaHombresTonala = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 5");
        $this->mecatronicaMujeresTonala = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 5");

        $this->industrialHombres = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
        //$this->electronica2Hombres = $maspirantes->count("carrera=600 
				//	AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
		$this->softwareHombres = $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
		
		$this->electronicoHombres = $maspirantes->count("carrera=901 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
		
				
        $this->mecatronicaHombres = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'");
        $this->industrialMujeres = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");
       // $this->electronica2Mujeres = $maspirantes->count("carrera=600 
				//	AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");
		$this->softwareMujeres= $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");
		$this->electronicoMujeres= $maspirantes->count("carrera=901 
		             AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");
        $this->mecatronicaMujeres = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'");

        // Aspirantes Pago de Derecho
        $this->todosPD = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");
        $this->hombresTodosPD = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " 
							AND tipoAspirante = 1 AND sexo = 'H'");
        $this->mujeresTodosPD = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " 
							AND tipoAspirante = 1 AND sexo = 'M'");
        $this->ingenieriaPD = $maspirantes->count
                ("nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");
        $this->industrialPD = $maspirantes->count
                ("carrera=400 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");
       // $this->electronica2PD = $maspirantes->count
                //("carrera=600 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");
		 $this->softwarePD = $maspirantes->count
                ("carrera=900 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");
		$this->electronicoPD = $maspirantes->count
                ("carrera=901 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");				
        $this->mecatronicaPD = $maspirantes->count
                ("carrera=801 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 1");

        $this->industrialHombresPD = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 1");
        /*$this->electronica2HombresPD = $maspirantes->count("carrera=600 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 1");*/
		$this->softwareHombresPD = $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 1");
	  $this->electronicoHombresPD = $maspirantes->count("carrera=901 
                    AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
	                   AND tipoAspirante = 1");				
        $this->mecatronicaHombresPD = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 1");
        $this->industrialMujeresPD = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 1");
        /*$this->electronica2MujeresPD = $maspirantes->count("carrera=600 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 1");*/
		$this->softwareMujeresPD = $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 1");
		$this->electronicoMujeresPD = $maspirantes->count("carrera=901 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 1");
        $this->mecatronicaMujeresPD = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 1");

        $this->industrialVespertinoPD = $maspirantes->count("tipoAspirante = 1 AND carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 1");
        $this->industrialMatutinoPD = $maspirantes->count("tipoAspirante = 1 AND carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 2");
        $this->mecatronicaColomosPD = $maspirantes->count("tipoAspirante = 1 AND carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 4");
        $this->mecatronicaTonalaPD = $maspirantes->count("tipoAspirante = 1 AND carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 5");

        $this->industrialHombresVespPD = $maspirantes->count("tipoAspirante = 1 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=1");
        $this->industrialHombresMatPD = $maspirantes->count("tipoAspirante = 1 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=2");
        $this->industrialMujeresVespPD = $maspirantes->count("tipoAspirante = 1 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=1");
        $this->industrialMujeresMatPD = $maspirantes->count("tipoAspirante = 1 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=2");
        $this->mecatronicaHombresColomosPD = $maspirantes->count("tipoAspirante = 1 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 4");
        $this->mecatronicaMujeresColomosPD = $maspirantes->count("tipoAspirante = 1 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 4");
        $this->mecatronicaHombresTonalaPD = $maspirantes->count("tipoAspirante = 1 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 5");
        $this->mecatronicaMujeresTonalaPD = $maspirantes->count("tipoAspirante = 1 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 5");

        // Aspirantes Continuación de Estudios
        $this->todosCE = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");
        $this->hombresTodosCE = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " 
							AND tipoAspirante = 2 AND sexo = 'H'");
        $this->mujeresTodosCE = $maspirantes->count
                ("periodo=" . $configuracion->periodo . " 
							AND tipoAspirante = 2 AND sexo = 'M'");
        $this->ingenieriaCE = $maspirantes->count
                ("nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");
        $this->industrialCE = $maspirantes->count
                ("carrera=400 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");
        /*$this->electronica2CE = $maspirantes->count
                ("carrera=600 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");*/
		$this->softwareCE = $maspirantes->count
                ("carrera=900 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");
		$this->electronicoCE = $maspirantes->count
                ("carrera=901 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");
        $this->mecatronicaCE = $maspirantes->count
                ("carrera=801 AND nivel='I' AND periodo=" . $configuracion->periodo . " AND tipoAspirante = 2");

        $this->industrialHombresCE = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 2");
       /* $this->electronica2HombresCE = $maspirantes->count("carrera=600 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 2");*/
		 $this->softwareHombresCE = $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 2");
		$this->electronicoHombresCE = $maspirantes->count("carrera=901 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 2");
        $this->mecatronicaHombresCE = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H'
						AND tipoAspirante = 2");
        $this->industrialMujeresCE = $maspirantes->count("carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 2");
        /*$this->electronica2MujeresCE = $maspirantes->count("carrera=600 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 2");*/
		$this->softwareMujeresCE = $maspirantes->count("carrera=900 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 2");
		$this->electronicoMujeresCE = $maspirantes->count("carrera=901 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 2");
        $this->mecatronicaMujeresCE = $maspirantes->count("carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M'
						AND tipoAspirante = 2");

        $this->industrialVespertinoCE = $maspirantes->count("tipoAspirante = 2 AND carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 1");
        $this->industrialMatutinoCE = $maspirantes->count("tipoAspirante = 2 AND carrera = 400 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 2");
        $this->mecatronicaColomosCE = $maspirantes->count("tipoAspirante = 2 AND carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 4");
        $this->mecatronicaTonalaCE = $maspirantes->count("tipoAspirante = 2 AND carrera = 801 AND nivel='I' AND periodo= " . $configuracion->periodo . " AND opc1 = 5");

        $this->industrialHombresVespCE = $maspirantes->count("tipoAspirante = 2 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=1");
        $this->industrialHombresMatCE = $maspirantes->count("tipoAspirante = 2 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1=2");
        $this->industrialMujeresVespCE = $maspirantes->count("tipoAspirante = 2 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=1");
        $this->industrialMujeresMatCE = $maspirantes->count("tipoAspirante = 2 AND carrera=400 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1=2");
        $this->mecatronicaHombresColomosCE = $maspirantes->count("tipoAspirante = 2 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 4");
        $this->mecatronicaMujeresColomosCE = $maspirantes->count("tipoAspirante = 2 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 4");
        $this->mecatronicaHombresTonalaCE = $maspirantes->count("tipoAspirante = 2 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'H' and opc1 = 5");
        $this->mecatronicaMujeresTonalaCE = $maspirantes->count("tipoAspirante = 2 AND carrera=801 
					AND nivel='I' AND periodo=" . $configuracion->periodo . " AND sexo = 'M' and opc1 = 5");
    }

    function registrar_ceneval() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $mexanis = new AspirantesExani();

        $mexanis->nivel = $this->post("nivel");
        $mexanis->plantel = $this->post("plantel");
        $mexanis->fecha = $this->post("fecha");
        $mexanis->lugar = $this->post("lugar");
        $mexanis->alumnos = $this->post("alumnos");
        $mexanis->periodo = $configuracion->periodo;
        $mexanis->tipo = 'CENEVAL';

        $mexanis->save();

        $this->redirect("aspirantes/config_ceneval");
    }

    function config_ceneval() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $this->admision = $configuracion->admision;

        $mexanis = new AspirantesExani();

        unset($this->periodoo);

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";

        $this->periodoo .= substr($periodo, 1, 4);

        $h = date("H", time() + 3600 * 1);
        $d = date("d", time() + 3600 * 1);
        $m = date("m", time() + 3600 * 1);
        $y = date("Y", time() + 3600 * 1);

        $this->rojas = $mexanis->find("plantel='C' AND nivel='T' AND tipo='CENEVAL'
					AND (alumnos=0 OR fecha < '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00') ORDER BY fecha");

        $this->verde = $mexanis->find_first("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='CENEVAL' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='T' AND plantel='C' ORDER BY fecha");

        if (!$this->verde->id)
            $this->verde->id = '0';
        $this->azules = $mexanis->find("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='CENEVAL' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='T' AND plantel='C' AND id!=" . $this->verde->id . " ORDER BY fecha");

        $this->rojas2 = $mexanis->find("plantel='C' AND nivel='I' AND tipo='CENEVAL'
					AND (alumnos=0 OR fecha < '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00') ORDER BY fecha");

        $this->verde2 = $mexanis->find_first("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='CENEVAL' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='I' AND plantel='C' ORDER BY fecha");

        if (!$this->verde2->id)
            $this->verde2->id = '0';
        $this->azules2 = $mexanis->find("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='CENEVAL' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='I' AND plantel='C' AND id!=" . $this->verde2->id . " ORDER BY fecha");
    }

    function registrar_exani() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $mexanis = new AspirantesExani();

        $mexanis->nivel = $this->post("nivel");
        $mexanis->plantel = $this->post("plantel");
        $mexanis->fecha = $this->post("fecha");
        $mexanis->lugar = $this->post("lugar");
        $mexanis->alumnos = $this->post("alumnos");
        $mexanis->periodo = $configuracion->periodo;
        $mexanis->tipo = 'EXANI';

        $mexanis->save();

        $this->redirect("aspirantes/config_exani");
    }

    function config_exani() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);
        $this->verde2->id = '0';
        $mexanis = new AspirantesExani();

        $h = date("H", time() + 3600 * 1);
        $d = date("d", time() + 3600 * 1);
        $m = date("m", time() + 3600 * 1);
        $y = date("Y", time() + 3600 * 1);

        unset($this->periodoo);
        unset($this->rojas);
        unset($this->verde);
        unset($this->azules);

        unset($this->rojas2);
        unset($this->verde2);
        unset($this->azules2);

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";

        $this->periodoo .= substr($periodo, 1, 4);

        $this->rojas = $mexanis->find("plantel='C' AND nivel='T' AND tipo='EXANI'
					AND (alumnos=0 OR fecha < '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00') ORDER BY fecha");

        $this->verde = $mexanis->find_first("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='EXANI' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='T' AND plantel='C' ORDER BY fecha");

        if (!$this->verde->id)
            $this->verde->id = '0';

        $this->azules = $mexanis->find("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='EXANI' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='T' AND plantel='C' AND id!=" . $this->verde->id . " ORDER BY fecha");


        $this->rojas2 = $mexanis->find("plantel='C' AND nivel='I' AND tipo='EXANI'
					AND (alumnos=0 OR fecha < '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00') 
						AND periodo = " . $periodo . " ORDER BY fecha");

        $this->verde2 = $mexanis->find_first("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='EXANI' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='I' AND plantel='C' AND periodo = " . $periodo . " ORDER BY fecha");

        if (!$this->verde2->id)
            $this->verde2->id = '0';

        $this->azules2 = $mexanis->find("fecha > '" . $y . "-" . $m . "-" . $d . " " . $h . ":00:00'
					AND tipo='EXANI' AND periodo=" . $configuracion->periodo . " AND alumnos>0
							AND nivel='I' AND plantel='C' AND id!=" . $this->verde2->id . " 
								AND periodo = " . $periodo . " ORDER BY fecha");
    }

    function registro($folio, $tipoAspirante) {
        $this->checarusuario();
        if (!is_numeric($folio)) {
            $this->redirect("ventanilla/aspirantes");
        }

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);
        $carrerasDisp = new Carrerasdisp();

        $maspirantes = new Aspirantes();
        $n = $maspirantes->count
                ("folio=" . $folio . " 
						AND periodo=" . $configuracion->periodo . "
							AND tipoAspirante = " . $tipoAspirante);

        if ($n > 0) {
            $this->redirect("aspirantes/modificacion/" . $folio . "/" . $tipoAspirante);
            return;
        }

        unset($this->carrerasDisp);
        $this->carrerasDisp = Array();
        foreach ($carrerasDisp->find_all_by_sql("select * from carrerasdisp where activo = 1") as $carrDisp) {
            array_push($this->carrerasDisp, $carrDisp);
        }

        $this->periodo = $configuracion->periodo;
        $this->ficha = "-";
        $this->folio = $folio;
        $this->tipoAspirante = $tipoAspirante;
    }

    function modificacion($folio, $tipoAspirante) {
        $this->checarusuario();
        if (!is_numeric($folio)) {
            $this->redirect("ventanilla/aspirantes");
        }
        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        // Eliminar variables de la clase.
        unset($this->fotos);
        unset($this->certificadoo);
        unset($this->certificadoc);
        unset($this->constanciae);
        unset($this->acta);
        unset($this->constanciab);
        unset($this->certificadom);
        unset($this->fichaPago);
        unset($this->cartaCompromiso);
        unset($this->comprobanteAntidoping);
        unset($this->noAntecedentesPenales);
        unset($this->fotos);
        unset($this->certificadoo);
        unset($this->certificadoc);
        unset($this->constanciae);
        unset($this->acta);
        unset($this->actac);
        unset($this->constanciab);
        unset($this->certificadom);
        unset($this->fichaPago);
        unset($this->cartaCompromiso);
        unset($this->comprobanteAntidoping);
        unset($this->noAntecedentesPenales);
        unset($this->periodo);
        unset($this->ficha);
        unset($this->folio);
        unset($this->nivel);
        unset($this->carrera);
        unset($this->paterno);
        unset($this->materno);
        unset($this->nombre);
        unset($this->calle);
        unset($this->exterior);
        unset($this->interior);
        unset($this->colonia);
        unset($this->lugarNacimiento);
        unset($this->cp);
        unset($this->municipio);
        unset($this->estado);
        unset($this->telefono);
        unset($this->celular);
        unset($this->correo);
        unset($this->sexo);
        unset($this->estadocivil);
        unset($this->plantel);
        unset($this->plantelorigen);
        unset($this->registro_tecnologo);
        unset($this->procedencia);
        unset($this->sexo);
        unset($this->sangre);
        unset($this->diaa);
        unset($this->mess);
        unset($this->anioo);
        unset($this->lugar_nacimiento);
        unset($this->curp);
        unset($this->tipoAspirante);
        unset($this->promedioprepa);
        unset($this->curpcopia);
        unset($this->paisnacimiento);
        unset($this->folioceneval);
        unset($this->estadoprepa);

        $hora = date("H", time());
        if ($hora <= 15) {
            $nivel = "T";
        } else {
            $nivel = "I";
        }

        if (Session::get_data('usuario') == "ventanilla_tgo") {
            $nivel = "T";
        }

        if (Session::get_data('usuario') == "ventanilla") {
            $nivel = "I";
        }

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        unset($this->carrerasDisp);
        $CarrerasDisp = new CarrerasDisp();
        $this->carrerasDisp = Array();
        foreach ($CarrerasDisp->find_all_by_sql("select * from carrerasdisp where activo = 1") as $carrDisp) {
            array_push($this->carrerasDisp, $carrDisp);
        }

        $maspirantes = new Aspirantes();
        $AspDoc = new AspirantesDocumentacion();
        if ($n = $maspirantes->count("folio=" . $folio . " 
					AND tipoAspirante = " . $tipoAspirante . "
						AND nivel='" . $nivel . "' AND periodo=" . $configuracion->periodo)) {
            
        }

        //SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
        if ($n == 0) {
            $this->redirect("aspirantes/registro/" . $folio . "/" . $tipoAspirante);
        }

        $maspirantes = new Aspirantes();
        $aspirante = $maspirantes->find_first("folio=" . $folio . " 
					AND tipoAspirante = " . $tipoAspirante . "
						AND nivel='" . $nivel . "' AND periodo=" . $configuracion->periodo);
        /*
          echo "folio: ".$folio." nivel ".$nivel." periodo: ".$configuracion -> periodo."<br />";
          echo $aspirante -> id;
          exit(1);
         */
        if ($AspDoc->find_first("aspirante_id = " . $aspirante->id)) {
            $this->fotos = $AspDoc->fotos;
            $this->certificadoo = $AspDoc->certificadoo;
            $this->certificadoc = $AspDoc->certificadoc;
            $this->constanciae = $AspDoc->constanciae;
            $this->acta = $AspDoc->acta;
            $this->actac = $AspDoc->actac;
            $this->constanciab = $AspDoc->constanciab;
            $this->certificadom = $AspDoc->certificadom;
            $this->fichaPago = $AspDoc->ficha_pago;
            $this->cartaCompromiso = $AspDoc->carta_compromiso;
            $this->comprobanteAntidoping = $AspDoc->deteccion_drogas;
            $this->noAntecedentesPenales = $AspDoc->antecedentes_penales;
            $this->curpcopia = $AspDoc->curpcopia;
        } else {
            $this->fotos = "";
            $this->certificadoo = "";
            $this->certificadoc = "";
            $this->constanciae = "";
            $this->acta = "";
            $this->actac = "";
            $this->constanciab = "";
            $this->certificadom = "";
            $this->fichaPago = "";
            $this->cartaCompromiso = "";
            $this->comprobanteAntidoping = "";
            $this->noAntecedentesPenales = "";
            $this->curpcopia = "";
        }

        $this->periodo = $aspirante->periodo;
        $this->ficha = $aspirante->ficha;
        $this->folio = $aspirante->folio;
        $this->folioceneval = $aspirante->folioceneval;

        $this->nivel = $aspirante->nivel;
        $this->carrera = $aspirante->carrera;

        $this->paterno = $aspirante->paterno;
        $this->materno = $aspirante->materno;
        $this->nombre = $aspirante->nombre;
        $this->calle = $aspirante->calle;
        $this->exterior = $aspirante->exterior;
        $this->interior = $aspirante->interior;
        $this->colonia = $aspirante->colonia;
        $this->lugarNacimiento = $aspirante->lugarNacimiento;
        $this->cp = $aspirante->cp;
        $this->municipio = $aspirante->municipio;
        $this->estado = $aspirante->estado;
        $this->paisnacimiento = $aspirante->paisnacimiento;
        $this->telefono = $aspirante->telefono;
        $this->celular = $aspirante->celular;
        $this->correo = $aspirante->correo;
        $this->sexo = $aspirante->sexo;
        $this->estadocivil = $aspirante->estadocivil;
        $this->plantel = $aspirante->plantel;
        $this->plantelorigen = $aspirante->plantelorigen;
        $this->registro_tecnologo = $aspirante->registro_tecnologo;
        $this->procedencia = $aspirante->procedencia;
        $this->estadoprepa = $aspirante->estadoprepa;
        $this->sexo = $aspirante->sexo;
        $this->sangre = $aspirante->sangre;

        $this->diaa = substr($aspirante->fecha_nacimiento, 8, 2);
        $this->mess = substr($aspirante->fecha_nacimiento, 5, 2);
        $this->anioo = substr($aspirante->fecha_nacimiento, 0, 4);

        $this->lugar_nacimiento = $aspirante->lugar_nacimiento;
        $this->curp = $aspirante->curp;
        $this->promedioprepa = $aspirante->promedioprepa;

        $this->tipoAspirante = $tipoAspirante;
    }

    function consulta($folio, $tipoAspirante, $nivel = "") {
        $this->checarusuario();
        if (!is_numeric($folio)) {
            $this->redirect("ventanilla/aspirantes");
        }

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $maspirantes = new Aspirantes();
        $hora = date("H", time());

        unset($this->tipoAspirante);

        if (Session::get_data('usuario') == "ventanilla_tgo") {
            $nivel = "T";
        }

        if (Session::get_data('usuario') == "ventanilla") {
            $nivel = "I";
        }

        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        if (isset($_POST["periodoNumero"])) {
            $periodoNumero = $this->post("periodoNumero");
            if ($n = $maspirantes->count("folio=" . $folio . " AND nivel='" . $nivel . "' 
						AND tipoAspirante = " . $tipoAspirante . "
							AND periodo= " . $periodoNumero)) {
                
            }
        } else {
            if ($n = $maspirantes->count("folio=" . $folio . " AND nivel='" . $nivel . "' 
						AND tipoAspirante = " . $tipoAspirante . "
							AND periodo= " . $configuracion->periodo)) {
                
            }
        }

        $this->tipoAspirante = $tipoAspirante;

        //SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
        if ($n == 0) {
            $this->redirect("aspirantes/registro/" . $folio . "/" . $tipoAspirante);
        }

        $maspirantes = new Aspirantes();
        if (isset($_POST["periodoNumero"])) {
            $periodoNumero = $this->post("periodoNumero");
            $aspirante = $maspirantes->find_first("folio=" . $folio . "
					AND tipoAspirante = " . $tipoAspirante . "
						AND nivel='" . $nivel . "' AND periodo=" . $periodoNumero);
        } else {
            $aspirante = $maspirantes->find_first("folio=" . $folio . "
					AND tipoAspirante = " . $tipoAspirante . "
						AND nivel='" . $nivel . "' AND periodo=" . $configuracion->periodo);
        }

        unset($this->opc1);
        unset($this->opc2);
        unset($this->opc3);

        $this->periodo = $aspirante->periodo;
        $this->ficha = $aspirante->ficha;
        $this->folio = $aspirante->folio;
        $this->carrera = $aspirante->carrera;
        $this->nivel = $aspirante->nivel;

        $this->paterno = $aspirante->paterno;
        $this->materno = $aspirante->materno;
        $this->nombre = $aspirante->nombre;
        $this->calle = $aspirante->calle;
        $this->exterior = $aspirante->exterior;
        $this->interior = $aspirante->interior;
        $this->colonia = $aspirante->colonia;
        $this->lugarNacimiento = $aspirante->lugarNacimiento;
        $this->cp = $aspirante->cp;
        $this->municipio = $aspirante->municipio;
        $this->estado = $aspirante->estado;
        $this->telefono = $aspirante->telefono;
        $this->celular = $aspirante->celular;
        $this->correo = $aspirante->correo;
        $this->sexo = $aspirante->sexo;
        $this->estadocivil = $aspirante->estadocivil;

        $this->promedioprepa = $aspirante->promedioprepa;

        $this->d = substr($aspirante->fecha_nacimiento, 8, 2);
        $this->m = substr($aspirante->fecha_nacimiento, 5, 2);
        $this->a = substr($aspirante->fecha_nacimiento, 0, 4);

        $this->lugar_nacimiento = $aspirante->lugar_nacimiento;
        $this->curp = $aspirante->curp;
        $this->opc1 = $aspirante->opc1;
        $this->opc2 = $aspirante->opc2;
        $this->opc3 = $aspirante->opc3;
    }

    function registrar($folio, $tipoAspirante) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();
        $validador1 = 0;
        //$validador2=0;
        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);

        $maspirantes = new Aspirantes();
        $maspirantes->fecha_nacimiento = $this->post("anioo") . "-" . $this->post("mess") . "-" . $this->post("diaa");

        $maspirantes = new Aspirantes();

        $maspirantes->nivel = "I";

        //CHECAMOS SI EL FOLIO, Y ESE TIPO DE ASPIRANTE HABIA SIDO REGISTRADO ANTERIORMENTE
        $n = $maspirantes->count("folio=" . $folio . " 
					AND nivel='I' 
						AND tipoAspirante = " . $tipoAspirante . "
							AND periodo=" . $configuracion->periodo);

        //SI YA SE HABIA REGISTRADO DEBERA MODIFICARSE, SINO REGISTRAR NUEVO ASPIRANTE
        if ($n > 0) {
            // AND nivel='".substr($this -> post("carrera"),0,1)."'
            $maspirantes = $maspirantes->find_first("folio=" . $folio . " 
						AND nivel='I'
							AND tipoAspirante = " . $tipoAspirante . "
								AND periodo=" . $configuracion->periodo);

            $maspirantes->tipoAspirante = $tipoAspirante;
            $maspirantes->nivel = "I";
            //$maspirantes -> nivel = substr($this -> post("carrera"),0,1);
            //$maspirantes -> carrera = substr($this -> post("carrera"),2);

            $maspirantes->folioceneval = strtoupper($this->post("folioceneval"));
            if ((!isset($maspirantes->folioceneval)) ||
                    $maspirantes->folioceneval == 0 ||
                    $maspirantes->folioceneval == "")
                $maspirantes->folioceneval = '0';
            $maspirantes->paterno = strtoupper($this->post("paterno"));
            $maspirantes->materno = strtoupper($this->post("materno"));
            $maspirantes->nombre = strtoupper($this->post("nombre"));
            $maspirantes->sangre = $this->post("sangre");

            $maspirantes->calle = strtoupper($this->post("calle"));
            $maspirantes->exterior = strtoupper($this->post("exterior"));

            if (!is_numeric($this->post("interior"))) {
                $maspirantes->interior = '0';
            } else {
                if ($this->post("interior") == 0 ||
                        $this->post("interior") == "") {
                    $maspirantes->interior = '0';
                } else if (!isset($_POST["interior"])) {
                    $maspirantes->interior = '0';
                } else {
                    $maspirantes->interior = $this->post("interior");
                    if ($maspirantes->interior == "" || $maspirantes->interior == null) {
                        $maspirantes->interior = '0';
                    }
                }
            }

            $maspirantes->cp = $this->post("cp");
            $maspirantes->municipio = strtoupper($this->post("municipio"));
            $maspirantes->estado = strtoupper($this->post("estado"));
            $maspirantes->paisnacimiento = strtoupper($this->post("paisnacimiento"));
            $maspirantes->telefono = $this->post("telefono");

            $maspirantes->colonia = strtoupper($this->post("colonia"));

            $maspirantes->lugarNacimiento = strtoupper($this->post("lugarNacimiento"));

            $maspirantes->celular = $this->post("celular");

            $maspirantes->correo = strtolower($this->post("correo"));

            $maspirantes->estadocivil = strtoupper($this->post("estadocivil"));
            $maspirantes->sexo = $this->post("sexo");
            $maspirantes->fecha_nacimiento = $this->post("anioo") . "-" . $this->post("mess") . "-" . $this->post("diaa");
            $maspirantes->lugar_nacimiento = strtoupper($this->post("nacimiento"));
            $maspirantes->curp = strtoupper($this->post("curp"));
            $maspirantes->procedencia = strtolower($this->post("procedencia"));
            $maspirantes->estadoprepa = strtolower($this->post("estadoprepa"));
            $maspirantes->sexo = $this->post("sexo");

            // Preferencias de carreras
            if (!$_POST["carreraa1"]) {
                $maspirantes->opc1 = '0';
            }
            if (!$_POST["carreraa2"]) {
                $maspirantes->opc2 = '0';
            }
            if (!$_POST["carreraa3"]) {
                $maspirantes->opc3 = '0';
            }

            $carrerasDisp = new Carrerasdisp();
            foreach ($carrerasDisp->find("id = " . $this->post("carreraa1")) as $carrDisp) {
                $maspirantes->carrera = $carrDisp->idcarrera;
            }
            $maspirantes->opc1 = $_POST["carreraa1"];
            $maspirantes->opc2 = $_POST["carreraa2"];
            $maspirantes->opc3 = $_POST["carreraa3"];
            if ($_POST["carreraa1"] == $_POST["carreraa2"] ||
                    $_POST["carreraa1"] == $_POST["carreraa3"] ||
                    $_POST["carreraa2"] == $_POST["carreraa3"]) { // Si hay una igual lo redirijo al principio
                if ($_POST["carreraa2"] != 1 && $_POST["carreraa3"] != 1) {
                    //$this -> redirect("ventanilla/aspirantes");
                    //return;
                }
            }
            // Fin preferencia de carreras...

            switch ($this->post("plantel")) {
                case 1: $maspirantes->plantel = "colomos";
                    break;
                case 2: $maspirantes->plantel = "tonala";
                    break;
            }

            switch ($this->post("plantelorigen")) {
                case 1: $maspirantes->plantelorigen = "colomos";
                    break;
                case 2: $maspirantes->plantelorigen = "tonala";
                    break;
                case 3: $maspirantes->plantelorigen = "noaplica";
                    break;
            }

            $maspirantes->promedioprepa = $this->post("promedioprepa");
            $maspirantes->registro_tecnologo = $this->post("registro_tecnologo");


            if (!$this->post("promedioprepa")) {
                $maspirantes->promedioprepa = '0';
            }
            if (!$this->post("calle")) {
                $maspirantes->calle = "-";
            }
            if (!$this->post("exterior")) {
                $maspirantes->exterior = "-";
            }
            if (!$this->post("colonia")) {
                $maspirantes->colonia = "-";
            }
            if (!$this->post("cp")) {
                $maspirantes->cp = "-";
            }
            if (!$this->post("municipio")) {
                $maspirantes->municipio = "-";
            }
            if (!$this->post("estado")) {
                $maspirantes->estado = "-";
            }
            if (!$this->post("paisnacimiento")) {
                $maspirantes->paisnacimiento = "-";
            }
            if (!$this->post("telefono")) {
                $maspirantes->telefono = "-";
            }
            if (!$this->post("celular")) {
                $maspirantes->celular = "-";
            }
            if (!$this->post("estadocivil")) {
                $maspirantes->estadocivil = "-";
            }
            if (!$this->post("sexo")) {
                $maspirantes->sexo = "-";
            }
            if (!$this->post("nacimiento")) {
                $maspirantes->lugar_nacimiento = "-";
            }
            if (!$this->post("curp")) {
                $maspirantes->curp = "-";
            }
            if (!$this->post("correo")) {
                $maspirantes->correo = "-";
            }
            if (!$this->post("plantel")) {
                $maspirantes->plantel = "-";
            }
            if (!$this->post("plantelorigen")) {
                $maspirantes->plantelorigen = "-";
            }
            if (!$this->post("sangre")) {
                $maspirantes->sangre = "-1";
            }
            if (!$this->post("procedencia")) {
                $maspirantes->procedencia = "-";
            }
            if (!$this->post("estadoprepa")) {
                $maspirantes->estadoprepa = "-";
            }
            if (!$this->post("registro_tecnologo")) {
                $maspirantes->registro_tecnologo = '0';
            }
            $maspirantes->admitido = -1;

            if (!isSet($_POST["certificadom"]) || ( $_POST["certificadom"] ) != "si") {
                $_POST["certificadom"] = "no";
            }

            $maspirantes->save();
            $AspDoc = new AspirantesDocumentacion();

            if ($AspDoc->find_first("aspirante_id = " . $maspirantes->id)) {
                $AspDoc->fotos = "no";
                $AspDoc->certificadoo = "no";
                $AspDoc->certificadoc = "no";
                $AspDoc->constanciae = "no";
                $AspDoc->acta = "no";
                $AspDoc->actac = "no";
                $AspDoc->constanciab = "no";
                $AspDoc->certificadom = "no";
                $AspDoc->carta_compromiso = "no";
                $AspDoc->deteccion_drogas = "no";
                $AspDoc->antecedentes_penales = "no";
                $AspDoc->ficha_pago = "no";
                $AspDoc->curpcopia = "no";
            } else {
                echo 'no se encontro';
            }
            if ($AspDoc->save_from_request()) {
                echo $validador1 = 1;
            } else {
                Flash::error('no se pudo guardar');
            }
        } else {

            $maspirantes = new Aspirantes();
            $maspirantes->fecha_nacimiento = $this->post("anioo") . "-" . $this->post("mess") . "-" . $this->post("diaa");

            $maspirantes->tipoAspirante = $tipoAspirante;
            $maspirantes->nivel = "I";
            //$maspirantes -> nivel = substr($this -> post("carrera"),0,1);
            //$maspirantes -> carrera = substr($this -> post("carrera"),2);

            if ($maspirantes->nivel == 'T')
                $maspirantes->ficha = $configuracion->tgo_ficha; //SACAR FICHA DE LA CONFIGURACION (CONSECUTIVO)
            else
                $maspirantes->ficha = $configuracion->ing_ficha; //SACAR FICHA DE LA CONFIGURACION (CONSECUTIVO)
            $maspirantes->folio = $folio;

            //SELECT *  FROM `aspirantes_exani` WHERE `fecha` > '2008-09-10 08:00:00'

            $exanis = new AspirantesExani();

            $d = date("d", time() + 60 * 60 * 24);
            $m = date("m", time() + 60 * 60 * 24);
            $y = date("Y", time() + 60 * 60 * 24);

            /*
              $exani = $exanis -> find_first("fecha > '".$y."-".$m."-".$d." 08:00:00'
              AND tipo='EXANI' AND periodo=".$configuracion -> periodo."
              AND alumnos>0 AND nivel='".$maspirantes -> nivel."' AND plantel='C'");
              $maspirantes -> exani_id = $exani -> id;
              $exani -> alumnos --;
              $exani -> save();

              $ceneval = $exanis -> find_first("fecha > '".$y."-".$m."-".$d." 08:00:00'
              AND tipo='CENEVAL' AND periodo=".$configuracion -> periodo."
              AND alumnos>0 AND nivel='".$maspirantes -> nivel."' AND plantel='C'");
              $maspirantes -> ceneval_id = $ceneval -> id;
              $ceneval -> alumnos --;
              $ceneval -> save();
             */

            if ($this->post('ayuda') == 'si') {
                if ($exani = $exanis->find_first("fecha > '" . $y . "-" . $m . "-" . $d . " 08:00:00'
							AND tipo='EXANI' AND periodo=" . $configuracion->periodo . "
									AND alumnos > 1 AND nivel='" . $maspirantes->nivel . "' AND plantel='C'")) {
                    $maspirantes->exani_id = $exani->id;
                    echo $exani->alumnos = $exani->alumnos - 1;
                    if ($exani->save()) {
                        echo "SE GUARDO - SI hay examenes Exani";
                        $validador1 = 1;
                    } else {
                        echo "No se guardo el Exani.<br>";
                    }
                } else {
                    echo "No hay Exani.<br>";
                }
            } else {
                $validador1 = 1;
            }
            $maspirantes->periodo = $configuracion->periodo; //SACAR PERIODO DE LA CONFIGURACION 

            $maspirantes->paterno = strtoupper($this->post("paterno"));
            $maspirantes->materno = strtoupper($this->post("materno"));
            $maspirantes->nombre = strtoupper($this->post("nombre"));
            $maspirantes->sangre = $this->post("sangre");

            $maspirantes->calle = strtoupper($this->post("calle"));
            $maspirantes->exterior = $this->post("exterior");


            // Preferencias de carreras
            if (!$_POST["carreraa1"]) {
                $maspirantes->opc1 = '0';
            }
            if (!$_POST["carreraa2"]) {
                $maspirantes->opc2 = '0';
            }
            if (!$_POST["carreraa3"]) {
                $maspirantes->opc3 = '0';
            }

            $carrerasDisp = new Carrerasdisp();
            foreach ($carrerasDisp->find("id = " . $this->post("carreraa1")) as $carrDisp) {
                $maspirantes->carrera = $carrDisp->idcarrera;
            }
            $maspirantes->opc1 = $_POST["carreraa1"];
            $maspirantes->opc2 = $_POST["carreraa2"];
            $maspirantes->opc3 = $_POST["carreraa3"];
            if ($_POST["carreraa1"] == $_POST["carreraa2"] ||
                    $_POST["carreraa1"] == $_POST["carreraa3"] ||
                    $_POST["carreraa2"] == $_POST["carreraa3"]) { // Si hay una igual lo redirijo al principio
                if ($_POST["carreraa2"] != 1 && $_POST["carreraa3"] != 1) {
                    //$this -> redirect("ventanilla/aspirantes");
                    //return;
                }
            }
            // Fin preferencia de carreras...


            switch ($this->post("plantel")) {
                case 1: $maspirantes->plantel = "colomos";
                    break;
                case 2: $maspirantes->plantel = "tonala";
                    break;
            }

            switch ($this->post("plantelorigen")) {
                case 1: $maspirantes->plantelorigen = "colomos";
                    break;
                case 2: $maspirantes->plantelorigen = "tonala";
                    break;
                case 3: $maspirantes->plantelorigen = "noaplica";
                    break;
            }

            if (!is_numeric($this->post("interior"))) {
                $maspirantes->interior = '0';
            } else {
                if ($this->post("interior") == 0 ||
                        $this->post("interior") == "") {
                    $maspirantes->interior = '0';
                } else if (!isset($_POST["interior"])) {
                    $maspirantes->interior = '0';
                } else {
                    $maspirantes->interior = $this->post("interior");
                    if ($maspirantes->interior == "" || $maspirantes->interior == null) {
                        $maspirantes->interior = '0';
                    }
                }
            }

            $maspirantes->colonia = strtoupper($this->post("colonia"));

            if (!$this->post("colonia")) {
                $maspirantes->colonia = "-";
            }

            $maspirantes->lugarNacimiento = strtoupper($this->post("lugarNacimiento"));
            if (!$this->post("lugarNacimiento")) {
                $maspirantes->lugarNacimiento = "-";
            }

            if (!$this->post("sangre")) {
                $maspirantes->sangre = "-1";
            }

            $maspirantes->folioceneval = strtoupper($this->post("folioceneval"));
            if ((!isset($maspirantes->folioceneval)) ||
                    $maspirantes->folioceneval == 0 ||
                    $maspirantes->folioceneval == "")
                $maspirantes->folioceneval = '0';
            $maspirantes->cp = $this->post("cp");
            $maspirantes->municipio = strtoupper($this->post("municipio"));
            $maspirantes->estado = strtoupper($this->post("estado"));
            $maspirantes->paisnacimiento = strtoupper($this->post("paisnacimiento"));
            $maspirantes->telefono = $this->post("telefono");

            $maspirantes->celular = $this->post("celular");

            if (!$this->post("celular")) {
                $maspirantes->celular = "-";
            }

            $maspirantes->correo = strtolower($this->post("correo"));

            $maspirantes->estadocivil = strtoupper($this->post("estadocivil"));
            $maspirantes->sexo = $this->post("sexo");
            $maspirantes->fecha_nacimiento = $this->post("anioo") . "-" . $this->post("mess") . "-" . $this->post("diaa");
            $maspirantes->lugar_nacimiento = strtoupper($this->post("nacimiento"));
            $maspirantes->curp = strtoupper($this->post("curp"));
            $maspirantes->procedencia = strtolower($this->post("procedencia"));
            $maspirantes->estadoprepa = strtolower($this->post("estadoprepa"));
            $maspirantes->sexo = $this->post("sexo");

            $maspirantes->promedioprepa = $this->post("promedioprepa");
            $maspirantes->registro_tecnologo = $this->post("registro_tecnologo");

            if (!$this->post("promedioprepa")) {
                $maspirantes->promedioprepa = '0';
            }
            if (!$this->post("calle")) {
                $maspirantes->calle = "-";
            }
            if (!$this->post("exterior")) {
                $maspirantes->exterior = "-";
            }
            if (!$this->post("colonia")) {
                $maspirantes->colonia = "-";
            }
            if (!$this->post("cp")) {
                $maspirantes->cp = "-";
            }
            if (!$this->post("municipio")) {
                $maspirantes->municipio = "-";
            }
            if (!$this->post("estado")) {
                $maspirantes->estado = "-";
            }
            if (!$this->post("paisnacimiento")) {
                $maspirantes->paisnacimiento = "-";
            }
            if (!$this->post("telefono")) {
                $maspirantes->telefono = "-";
            }
            if (!$this->post("celular")) {
                $maspirantes->celular = "-";
            }
            if (!$this->post("estadocivil")) {
                $maspirantes->estadocivil = "-";
            }
            if (!$this->post("sexo")) {
                $maspirantes->sexo = "-";
            }
            if (!$this->post("nacimiento")) {
                $maspirantes->lugar_nacimiento = "-";
            }
            if (!$this->post("correo")) {
                $maspirantes->correo = "-";
            }
            if (!$this->post("curp")) {
                $maspirantes->curp = "-";
            }
            if (!$this->post("plantel")) {
                $maspirantes->plantel = "-";
            }
            if (!$this->post("plantelorigen")) {
                $maspirantes->plantelorigen = "-";
            }
            if (!$this->post("procedencia")) {
                $maspirantes->procedencia = "-";
            }
            if (!$this->post("estadoprepa")) {
                $maspirantes->estadoprepa = "-";
            }
            if (!$this->post("registro_tecnologo")) {
                $maspirantes->registro_tecnologo = '0';
            }

            $maspirantes->admitido = -1;
            $maspirantes->save();
            $AspDoc = new AspirantesDocumentacion();

            $maspirantes = new Aspirantes();
            foreach ($maspirantes->find_all_by_sql("
						Select max(id) id from aspirantes") as $maspirant) {
                $AspDoc->aspirante_id = $maspirant->id;
            }

            if ($AspDoc->save_from_request()) {
                
            } else {
                Flash::error('no se pudo guardar');
            }

            if ($maspirantes->nivel == 'T')
                $configuracion->tgo_ficha++;
            else
                $configuracion->ing_ficha++;

            $configuracion->save();
        }
        //if($validador1 == 1 && $validador2 == 1){
        if ($validador1 == 1) {
            $this->redirect("aspirantes/consulta/" . $folio . "/" . $tipoAspirante);
        } else {
            echo "<br> algun validador fallo";
        }
        $this->redirect("aspirantes/consulta/" . $folio . "/" . $tipoAspirante);
    }

// function registrar($folio)

    function ficha() {
        $this->checarusuario();
        //define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
        //require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');

        $folio = $this->post("folio");
        $tipoAspirante = $this->post("tipoAspirante");
        $periodo_aspirante = $this->post("periodo_aspirante");
        
        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $maspirantes = new Aspirantes();
        $mexanis = new AspirantesExani();
        
        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);
        
		$nivel = "I";
        if( $periodo_aspirante != 0 ){
            $periodo_aux = $periodo_aspirante;
        }
        else{
            $periodo_aux = $periodo;
        }
        $aspirante = $maspirantes->find_first("folio=" . $folio . " 
					AND tipoAspirante = " . $tipoAspirante . " AND nivel='" . $nivel . "' 
						AND periodo=" . $periodo_aux);
        if( $aspirante->registro != 0 ){
            $this->redirect("aspirantes/ficha_alumno/".$folio.
                    "/".$tipoAspirante."/".$periodo_aspirante);
            return;
        }

        if (Session::get_data('usuario') == "ventanilla_tgo") {
            $nivel = "T";
        }

        if (Session::get_data('usuario') == "ventanilla") {
            $nivel = "I";
        }
        
        $ficha = $aspirante->ficha;

        $exani = $mexanis->find_first("id=" . $aspirante->exani_id);
        $ceneval = $mexanis->find_first("id = " . $mexanis->get_last_id());

        $this->set_response("view");


        $reporte = new FPDF();

        $reporte->Open();
        $reporte->AddPage();

        $reporte->AddFont('verdana', '', 'verdana.php');

        $reporte->Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 8);

        $reporte->Image('http://ase.ceti.mx/ingenieria/img/fotografia.jpg', 180, 15);

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 14);
        $reporte->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);

        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 12);
        $reporte->MultiCell(0, 3, "Organismo Público Descentralizado Federal", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "Departamento de Servicios de Apoyo Académico", 0, 'C', 0);
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 8);
        if ($aspirante->plantel == "colomos")
            $reporte->MultiCell(0, 2, "PLANTEL COLOMOS", 0, 'C', 0);
        else
            $reporte->MultiCell(0, 2, "PLANTEL TONALA", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "FICHA DE ASPIRANTE PARA ADMISIÓN", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 6);

        $reporte->Ln();
        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(18);
        $reporte->Cell(18, 4, "FICHA", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "NOMBRE DEL ASPIRANTE", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "NIVEL", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "PERIODO", 1, 0, 'C', 1);
        $reporte->Cell(55, 4, "CARRERA", 1, 0, 'C', 1);



        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

        $reporte->SetX(18);
        if ($aspirante->plantel == "colomos")
            $reporte->Cell(18, 4, "IC" . $ficha, 1, 0, 'C', 1);
        else
            $reporte->Cell(18, 4, "IT" . $ficha, 1, 0, 'C', 1);

        $reporte->Cell(60, 4, $aspirante->paterno . " " . $aspirante->materno . " " . $aspirante->nombre, 1, 0, 'C', 1);

        if ($aspirante->nivel == "T") {
            $nivel = "TECNÓLOGO";
            switch ($aspirante->carrera) {
                case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN";
                    break;
                case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
                    break;
                case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN";
                    break;
                case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES";
                    break;
                case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA";
                    break;
                case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA";
                    break;
                case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ";
                    break;
                case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS";
                    break;
            }
        } else {
            $nivel = "INGENIERÍA";
            switch ($aspirante->carrera) {
                case 400: $carrera = "INGENIERÍA INDUSTRIAL";
                    break;
                case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA";
                    break;
                case 801: $carrera = "INGENIERÍA EN MECATRÓNICA";
                    break;
            }
        }

        if ($aspirante->periodo[0] == '1')
            $periodo = "FEB - JUN, ";
        else
            $periodo = "AGO - DIC, ";

        $periodo .= substr($aspirante->periodo, 1, 4);

        $reporte->Cell(25, 4, $nivel, 1, 0, 'C', 1);
        $reporte->Cell(25, 4, $periodo, 1, 0, 'C', 1);

        $carrerasdisp = new Carrerasdisp();
        for ($mm = 1; $mm < 4; $mm++) {
            if ($aspirante->opc . $mm != "" || $aspirante->opc . $mm != null) {
                $tmp = "opc$mm";
                foreach ($carrerasdisp->find_all_by_sql(
                        "select nombre, plantel, turno
						from carrerasdisp
						where id = " . $aspirante->$tmp) as $carrDisp) {
                    $carreraa[$mm] = $carrDisp;
                }
            }
        }
        for ($m = 1; $m < 4; $m++) {
            if ($carreraa[$m]->plantel == "C") {
                $plantelll[$m] = "COLOMOS";
            } else if ($carreraa[$m]->plantel == "T") {
                $plantelll[$m] = "TONALA";
            } else {
                $plantelll[$m] = "";
            }

            if ($carreraa[$m]->turno == "V") {
                $turnooo[$m] = "VESPERTINO";
            } else if ($carreraa[$m]->turno == "M") {
                $turnooo[$m] = "MATUTINO";
            } else {
                $turnooo[$m] = "";
            }
        }

        //$reporte -> Ln();
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);

          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc1",1,0,'C',1);
         */
        $reporte->Cell(3, 4, "1.", 1, 0, 'C', 1);
        $reporte->Cell(52, 4, $carreraa[1]->nombre . " " . $plantelll[1] . " " . $turnooo[1], 1, 0, 'C', 1);

        // Agregar folio de solicitud
        $reporte->Ln();
        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 6);
        $reporte->SetX(18);
        $reporte->Cell(39, 4, "FOLIO SOLICITUD ASPIRANTE", 1, 0, 'C', 1);
        $reporte->Cell(39, 4, "REGISTRO TECNOLOGO", 1, 0, 'C', 1);
        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->SetX(18);
        $reporte->Cell(39, 4, $folio, 1, 0, 'C', 1);
        if ($aspirante->registro_tecnologo != 0)
            $reporte->Cell(39, 4, $aspirante->registro_tecnologo, 1, 0, 'C', 1);
        else
            $reporte->Cell(39, 4, "N/A", 1, 0, 'C', 1);
        // Fin folio solicitud

        $reporte->Ln();
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);

          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc2",1,0,'C',1);
         */
        if (isset($carreraa[2]->nombre)) {
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->SetX(146);
            $reporte->Cell(3, 4, "2.", 1, 0, 'C', 1);
            $reporte->Cell(52, 4, $carreraa[2]->nombre . " " . $plantelll[2] . " " . $turnooo[2], 1, 0, 'C', 1);

            $reporte->Ln();
        }
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);
          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc3",1,0,'C',1);
         */
        if (isset($carreraa[3]->nombre)) {
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->SetX(146);
            $reporte->Cell(3, 4, "3.", 1, 0, 'C', 1);
            $reporte->Cell(52, 4, $carreraa[3]->nombre . " " . $plantelll[3] . " " . $turnooo[3], 1, 0, 'C', 1);
            $reporte->Ln();
        }
        // $aspirante -> sangre
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 8);

        if ($aspirante->exani_id != 0) {

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->Cell(55, 6, "ACTIVIDAD", 1, 0, 'C', 1);
            $reporte->Cell(60, 6, "FECHA", 1, 0, 'C', 1);
            $reporte->Cell(20, 6, "HORA", 1, 0, 'C', 1);
            $reporte->Cell(55, 6, "AULA", 1, 0, 'C', 1);

            $reporte->Ln();
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            if ($exani->nivel == 'T')
                $reporte->Cell(20, 6, "Llenado de Hoja de Registro Exani I", 1, 0, 'L', 1);
            else
                $reporte->Cell(20, 6, "Llenado de Hoja de Registro Exani II", 1, 0, 'L', 1);

            $fecha = substr($exani->fecha, 0, 10);

            $d = substr($fecha, 8, 2);
            $m = substr($fecha, 5, 2);
            $y = substr($fecha, 0, 4);

            $tiempo = mktime(0, 0, 0, $m, $d, $y);

            $fecha = date("w", $tiempo);

            switch ($fecha) {
                case 0: $fecha = "Domingo";
                    break;
                case 1: $fecha = "Lunes";
                    break;
                case 2: $fecha = "Martes";
                    break;
                case 3: $fecha = "Miercoles";
                    break;
                case 4: $fecha = "Jueves";
                    break;
                case 5: $fecha = "Viernes";
                    break;
                case 6: $fecha = "Sábado";
                    break;
            }

            $fecha = $fecha . ", " . $d . " de ";

            switch ($m) {
                case 1: $fecha .= "Enero";
                    break;
                case 2: $fecha .= "Fabrero";
                    break;
                case 3: $fecha .= "Marzo";
                    break;
                case 4: $fecha .= "Abril";
                    break;
                case 5: $fecha .= "Mayo";
                    break;
                case 6: $fecha .= "Junio";
                    break;
                case 7: $fecha .= "Julio";
                    break;
                case 8: $fecha .= "Agosto";
                    break;
                case 9: $fecha .= "Septiembre";
                    break;
                case 10: $fecha .= "Octubre";
                    break;
                case 11: $fecha .= "Noviembre";
                    break;
                case 12: $fecha .= "Diciembre";
                    break;
            }

            $fecha .= " de " . $y;

            $reporte->Cell(60, 6, $fecha, 1, 0, 'C', 1);
            $reporte->Cell(20, 6, substr($exani->fecha, 11), 1, 0, 'C', 1);
            $reporte->Cell(55, 6, substr($exani->lugar, 0, 25), 1, 0, 'C', 1);
        } else {
            $reporte->Ln(4);
        }

        $reporte->SetY(80);
        $reporte->SetX(10);
        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->Cell(7, 27, "", 0, 0, 'C', 0);
        $reporte->Cell(60, 27, "", 1, 0, 'C', 1);
        $reporte->Cell(64, 27, "", 1, 0, 'C', 1);
        $reporte->Cell(60, 27, "", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->SetY(100);
        $reporte->Text(84, 78, "DOCUMENTACIÓN ASPIRANTE: ");
        //$reporte -> Text(148, 78, "DOCUMENTACIÓN ALUMNO: ");
        $reporte->SetY(100);

        $reporte->SetFont('verdana', '', 8);
        //if($exani -> nivel == 'T')
//			$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani I");
        //else
        $reporte->Text(20, 98, "Llenado de Hoja de Registro en Línea");
        $reporte->Text(20, 102, "Exani II");
//			echo "aspirante_id: ".$aspirante -> id;
//			exit(1);
        $AspDoc = new AspirantesDocumentacion();
        if ($AspDoc->find_first("aspirante_id = " . $aspirante->id)) {

            $reporte->Text(82, 84, "4 Fotografias: " . $AspDoc->fotos);
            $reporte->Text(82, 88, "Constancia de Estudios: " . $AspDoc->constanciae);
            $reporte->Text(82, 92, "Certificado Original: " . $AspDoc->certificadoo);
            //$reporte -> Text(141, 92, "Certificado Copia: ".$AspDoc -> certificadoc);
            $reporte->Text(82, 96, "Acta Nacimiento Original y Copia: " . $AspDoc->acta);
            //$reporte -> Text(82, 100, "Lugar Nac: ".$aspirante -> lugarNacimiento);
            $reporte->Text(82, 100, "Curp Copia: " . $AspDoc->curpcopia);
            $reporte->Text(82, 104, "CURP: " . $aspirante->curp);


            if ($aspirante->plantelorigen != "noaplica") {
                $reporte->Text(146, 84, "Plantel De Origen: " . $aspirante->plantelorigen);
            }


            //$reporte -> Text(146, 84, "Certificado Medico: ".$AspDoc -> certificadom);
            //$reporte -> Text(146, 88, "Carta Compromiso: ".$AspDoc -> carta_compromiso);
            //$reporte -> Text(146, 92, "Comprobante Antidoping : ".$AspDoc -> deteccion_drogas);
            //if( $aspirante -> sangre == "-1" ){
            //$sangre = "Ninguno";
            //}else{
            //$sangre = $aspirante -> sangre;
            //}
            //$reporte -> Text(146, 96, "Tipo de Sangre: ".$sangre);
            //$reporte -> Text(146, 100, "Ficha Pago: ".$AspDoc -> ficha_pago);
        }

        $reporte->SetY(130);
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->Cell(55, 6, "Examen de Admisión CENEVAL", 1, 0, 'L', 1);

        $fecha = substr($ceneval->fecha, 0, 10);

        $d = substr($fecha, 8, 2);
        $m = substr($fecha, 5, 2);
        $y = substr($fecha, 0, 4);

        $tiempo = mktime(0, 0, 0, $m, $d, $y);

        $fecha = date("w", $tiempo);

        switch ($fecha) {
            case 0: $fecha = "Domingo";
                break;
            case 1: $fecha = "Lunes";
                break;
            case 2: $fecha = "Martes";
                break;
            case 3: $fecha = "Miercoles";
                break;
            case 4: $fecha = "Jueves";
                break;
            case 5: $fecha = "Viernes";
                break;
            case 6: $fecha = "Sábado";
                break;
        }

        $fecha = $fecha . ", " . $d . " de ";

        switch ($m) {
            case 1: $fecha .= "Enero";
                break;
            case 2: $fecha .= "Febrero";
                break;
            case 3: $fecha .= "Marzo";
                break;
            case 4: $fecha .= "Abril";
                break;
            case 5: $fecha .= "Mayo";
                break;
            case 6: $fecha .= "Junio";
                break;
            case 7: $fecha .= "Julio";
                break;
            case 8: $fecha .= "Agosto";
                break;
            case 9: $fecha .= "Septiembre";
                break;
            case 10: $fecha .= "Octubre";
                break;
            case 11: $fecha .= "Noviembre";
                break;
            case 12: $fecha .= "Diciembre";
                break;
        }

        $fecha .= " de " . $y;

        $reporte->Cell(90, 6, $fecha, 1, 0, 'C', 1);
        $reporte->Cell(50, 6, substr($ceneval->fecha, 11), 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetY(110);

        $reporte->Cell(190, 6, "Conserva bien tu ficha, es tu única identificación para que puedas entrar al plantel y hacer tus examenes", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "Es necesario realizar estas dos actividades para poder participar en el proceso de admisión", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "REVISIóN 3                                    A partir del 16 de Abril de 2007                                    FR-02-DPL-CE-PO-024", 0, 0, 'C', 1);

        $reporte->Output("public/files/pdfs/aspirantes/" . $ficha . ".pdf");

        $this->redirect("public/files/pdfs/aspirantes/" . $ficha . ".pdf");
    } // function ficha()
    
    function ficha_alumno($folio, $tipoAspirante, $periodo_aspirante) {
        $this->checarusuario();
//        define('FPDF_FONTPATH', 'C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/font');
//        require('C:/Program Files (x86)/VertrigoServ/www/ingenieria/library/fpdf/fpdf.php');

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $maspirantes = new Aspirantes();
        $mexanis = new AspirantesExani();
		
		$nivel = "I";
        $configuracion = new Configuracion();
        $configuracion = $configuracion->find_first("periodo=" . $periodo);
        $aspirante = $maspirantes->find_first("folio=" . $folio . " 
					AND tipoAspirante = " . $tipoAspirante . " AND nivel='" . $nivel . "' 
						AND periodo=" . $periodo_aspirante);

        if (Session::get_data('usuario') == "ventanilla_tgo") {
            $nivel = "T";
        }
        if (Session::get_data('usuario') == "ventanilla") {
            $nivel = "I";
        }

        $ficha = $aspirante->ficha;

        $exani = $mexanis->find_first("id=" . $aspirante->exani_id);
        $ceneval = $mexanis->find_first("id = " . $mexanis->get_last_id());

        $this->set_response("view");


        $reporte = new FPDF();

        $reporte->Open();
        $reporte->AddPage();

        $reporte->AddFont('verdana', '', 'verdana.php');

        $reporte->Image('http://ase.ceti.mx/ingenieria/img/logoceti.jpg', 5, 8);

        $reporte->Image('http://ase.ceti.mx/ingenieria/img/fotografia.jpg', 180, 15);

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 14);
        $reporte->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);

        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 12);
        $reporte->MultiCell(0, 3, "Organismo Público Descentralizado Federal", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "Departamento de Servicios de Apoyo Académico", 0, 'C', 0);
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 8);
        if ($aspirante->plantel == "colomos")
            $reporte->MultiCell(0, 2, "PLANTEL COLOMOS", 0, 'C', 0);
        else
            $reporte->MultiCell(0, 2, "PLANTEL TONALA", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "FICHA DE ENTREGA DE DOCUMENTACIÓN NUEVO INGRESO", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 6);

        $reporte->Ln();
        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(18);
        $reporte->Cell(18, 4, "FICHA", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "NOMBRE DEL ASPIRANTE", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "NIVEL", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "PERIODO", 1, 0, 'C', 1);
        $reporte->Cell(55, 4, "CARRERA", 1, 0, 'C', 1);



        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);

        $reporte->SetX(18);
        if ($aspirante->plantel == "colomos")
            $reporte->Cell(18, 4, "IC" . $ficha, 1, 0, 'C', 1);
        else
            $reporte->Cell(18, 4, "IT" . $ficha, 1, 0, 'C', 1);

        $reporte->Cell(60, 4, $aspirante->paterno . " " . $aspirante->materno . " " . $aspirante->nombre, 1, 0, 'C', 1);

        if ($aspirante->nivel == "T") {
            $nivel = "TECNÓLOGO";
            switch ($aspirante->carrera) {
                case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN";
                    break;
                case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
                    break;
                case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN";
                    break;
                case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES";
                    break;
                case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA";
                    break;
                case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA";
                    break;
                case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ";
                    break;
                case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS";
                    break;
            }
        } else {
            $nivel = "INGENIERÍA";
            switch ($aspirante->carrera) {
                case 400: $carrera = "INGENIERÍA INDUSTRIAL";
                    break;
                case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA";
                    break;
                case 801: $carrera = "INGENIERÍA EN MECATRÓNICA";
                    break;
            }
        }

        if ($aspirante->periodo[0] == '1')
            $periodo = "FEB - JUN, ";
        else
            $periodo = "AGO - DIC, ";

        $periodo .= substr($aspirante->periodo, 1, 4);

        $reporte->Cell(25, 4, $nivel, 1, 0, 'C', 1);
        $reporte->Cell(25, 4, $periodo, 1, 0, 'C', 1);

        $carrerasdisp = new Carrerasdisp();
        for ($mm = 1; $mm < 4; $mm++) {
            if ($aspirante->opc . $mm != "" || $aspirante->opc . $mm != null) {
                $tmp = "opc$mm";
                foreach ($carrerasdisp->find_all_by_sql(
                        "select nombre, plantel, turno
						from carrerasdisp
						where id = " . $aspirante->$tmp) as $carrDisp) {
                    $carreraa[$mm] = $carrDisp;
                }
            }
        }
        for ($m = 1; $m < 4; $m++) {
            if ($carreraa[$m]->plantel == "C") {
                $plantelll[$m] = "COLOMOS";
            } else if ($carreraa[$m]->plantel == "T") {
                $plantelll[$m] = "TONALA";
            } else {
                $plantelll[$m] = "";
            }

            if ($carreraa[$m]->turno == "V") {
                $turnooo[$m] = "VESPERTINO";
            } else if ($carreraa[$m]->turno == "M") {
                $turnooo[$m] = "MATUTINO";
            } else {
                $turnooo[$m] = "";
            }
        }

        //$reporte -> Ln();
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);

          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc1",1,0,'C',1);
         */
        $reporte->Cell(3, 4, "1.", 1, 0, 'C', 1);
        $reporte->Cell(52, 4, $carreraa[1]->nombre . " " . $plantelll[1] . " " . $turnooo[1], 1, 0, 'C', 1);

        // Agregar folio de solicitud
        $reporte->Ln();
        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 6);
        $reporte->SetX(18);
        $reporte->Cell(39, 4, "FOLIO SOLICITUD ASPIRANTE", 1, 0, 'C', 1);
        $reporte->Cell(39, 4, "REGISTRO TECNOLOGO", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "REGISTRO", 1, 0, 'C', 1);
        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->SetX(18);
        $reporte->Cell(39, 4, $folio, 1, 0, 'C', 1);
        if ($aspirante->registro_tecnologo != 0)
            $reporte->Cell(39, 4, $aspirante->registro_tecnologo, 1, 0, 'C', 1);
        else
            $reporte->Cell(39, 4, "N/A", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, $aspirante->registro, 1, 0, 'C', 1);
        // Fin folio solicitud

        $reporte->Ln();
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);

          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc2",1,0,'C',1);
         */
        if (isset($carreraa[2]->nombre)) {
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->SetX(146);
            $reporte->Cell(3, 4, "2.", 1, 0, 'C', 1);
            $reporte->Cell(52, 4, $carreraa[2]->nombre . " " . $plantelll[2] . " " . $turnooo[2], 1, 0, 'C', 1);

            $reporte->Ln();
        }
        /*
          $reporte -> SetFillColor(0xDD,0xDD,0xDD);
          $reporte -> SetTextColor(0);
          $reporte -> SetDrawColor(0xFF,0x66,0x33);
          $reporte -> SetFont('verdana','',6);
          $reporte -> SetX(18);
          $reporte -> Cell(20,4, "Carrera Opc3",1,0,'C',1);
         */
        if (isset($carreraa[3]->nombre)) {
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            $reporte->SetX(146);
            $reporte->Cell(3, 4, "3.", 1, 0, 'C', 1);
            $reporte->Cell(52, 4, $carreraa[3]->nombre . " " . $plantelll[3] . " " . $turnooo[3], 1, 0, 'C', 1);
            $reporte->Ln();
        }
        // $aspirante -> sangre
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 8);

        if ($aspirante->exani_id != 0) {

            $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
            $reporte->Cell(55, 6, "ACTIVIDAD", 1, 0, 'C', 1);
            $reporte->Cell(60, 6, "FECHA", 1, 0, 'C', 1);
            $reporte->Cell(20, 6, "HORA", 1, 0, 'C', 1);
            $reporte->Cell(55, 6, "AULA", 1, 0, 'C', 1);

            $reporte->Ln();
            $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
            if ($exani->nivel == 'T')
                $reporte->Cell(20, 6, "Documentación Entregada", 1, 0, 'L', 1);
            else
                $reporte->Cell(20, 6, "Documentación Entregada", 1, 0, 'L', 1);

            $fecha = substr($exani->fecha, 0, 10);

            $d = substr($fecha, 8, 2);
            $m = substr($fecha, 5, 2);
            $y = substr($fecha, 0, 4);

            $tiempo = mktime(0, 0, 0, $m, $d, $y);

            $fecha = date("w", $tiempo);

            switch ($fecha) {
                case 0: $fecha = "Domingo";
                    break;
                case 1: $fecha = "Lunes";
                    break;
                case 2: $fecha = "Martes";
                    break;
                case 3: $fecha = "Miercoles";
                    break;
                case 4: $fecha = "Jueves";
                    break;
                case 5: $fecha = "Viernes";
                    break;
                case 6: $fecha = "Sábado";
                    break;
            }

            $fecha = $fecha . ", " . $d . " de ";

            switch ($m) {
                case 1: $fecha .= "Enero";
                    break;
                case 2: $fecha .= "Fabrero";
                    break;
                case 3: $fecha .= "Marzo";
                    break;
                case 4: $fecha .= "Abril";
                    break;
                case 5: $fecha .= "Mayo";
                    break;
                case 6: $fecha .= "Junio";
                    break;
                case 7: $fecha .= "Julio";
                    break;
                case 8: $fecha .= "Agosto";
                    break;
                case 9: $fecha .= "Septiembre";
                    break;
                case 10: $fecha .= "Octubre";
                    break;
                case 11: $fecha .= "Noviembre";
                    break;
                case 12: $fecha .= "Diciembre";
                    break;
            }

            $fecha .= " de " . $y;

            $reporte->Cell(60, 6, $fecha, 1, 0, 'C', 1);
            $reporte->Cell(20, 6, substr($exani->fecha, 11), 1, 0, 'C', 1);
            $reporte->Cell(55, 6, substr($exani->lugar, 0, 25), 1, 0, 'C', 1);
        } else {
            $reporte->Ln(4);
        }

        $reporte->SetY(80);
        $reporte->SetX(10);
        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->Cell(7, 27, "", 0, 0, 'C', 0);
        $reporte->Cell(60, 27, "", 1, 0, 'C', 1);
        $reporte->Cell(64, 27, "", 1, 0, 'C', 1);
        $reporte->Cell(60, 27, "", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->SetY(100);
        $reporte->Text(84, 78, "DOCUMENTACIÓN ALUMNO: ");
        //$reporte -> Text(148, 78, "DOCUMENTACIÓN ALUMNO: ");
        $reporte->SetY(100);

        $reporte->SetFont('verdana', '', 8);
        //if($exani -> nivel == 'T')
//			$reporte -> Text(84, 98, "Llenado de Hoja de Registro Exani I");
        //else
        $reporte->Text(20, 98, "Documentación Entregada");
//			echo "aspirante_id: ".$aspirante -> id;
//			exit(1);
        $AspDoc = new AspirantesDocumentacion();
        if ($AspDoc->find_first("aspirante_id = " . $aspirante->id)) {

            $reporte->Text(82, 84, "4 Fotografias: " . $AspDoc->fotos);
            $reporte->Text(82, 88, "Constancia de Estudios: " . $AspDoc->constanciae);
            $reporte->Text(82, 92, "Certificado Original: " . $AspDoc->certificadoo);
            //$reporte -> Text(141, 92, "Certificado Copia: ".$AspDoc -> certificadoc);
            $reporte->Text(82, 96, "Acta Nacimiento Original y Copia: " . $AspDoc->acta);
            //$reporte -> Text(82, 100, "Lugar Nac: ".$aspirante -> lugarNacimiento);
            $reporte->Text(82, 100, "Curp Copia: " . $AspDoc->curpcopia);
            $reporte->Text(82, 104, "CURP: " . $aspirante->curp);


            if ($aspirante->plantelorigen != "noaplica") {
                $reporte->Text(146, 84, "Plantel De Origen: " . $aspirante->plantelorigen);
            }
            
            $reporte -> Text(146, 88, "Certificado Medico: ".$AspDoc -> certificadom);
            $reporte -> Text(146, 92, "Examen Antidoping: ".$AspDoc -> deteccion_drogas);
            $reporte -> Text(146, 96, "Ficha Pago: ".$AspDoc -> ficha_pago);
            //$reporte -> Text(146, 88, "Carta Compromiso: ".$AspDoc -> carta_compromiso);
            //$reporte -> Text(146, 92, "Comprobante Antidoping : ".$AspDoc -> deteccion_drogas);
            //if( $aspirante -> sangre == "-1" ){
            //$sangre = "Ninguno";
            //}else{
            //$sangre = $aspirante -> sangre;
            //}
            //$reporte -> Text(146, 96, "Tipo de Sangre: ".$sangre);
            //$reporte -> Text(146, 100, "Ficha Pago: ".$AspDoc -> ficha_pago);
        }

        $reporte->SetY(130);
        $reporte->SetX(15);
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->Cell(55, 6, "Fecha de Entrega", 1, 0, 'L', 1);

        $fecha = substr($AspDoc->modificado_in, 0, 10);

        $d = substr($fecha, 8, 2);
        $m = substr($fecha, 5, 2);
        $y = substr($fecha, 0, 4);

        $tiempo = mktime(0, 0, 0, $m, $d, $y);

        $fecha = date("w", $tiempo);

        switch ($fecha) {
            case 0: $fecha = "Domingo";
                break;
            case 1: $fecha = "Lunes";
                break;
            case 2: $fecha = "Martes";
                break;
            case 3: $fecha = "Miercoles";
                break;
            case 4: $fecha = "Jueves";
                break;
            case 5: $fecha = "Viernes";
                break;
            case 6: $fecha = "Sábado";
                break;
        }

        $fecha = $fecha . ", " . $d . " de ";

        switch ($m) {
            case 1: $fecha .= "Enero";
                break;
            case 2: $fecha .= "Febrero";
                break;
            case 3: $fecha .= "Marzo";
                break;
            case 4: $fecha .= "Abril";
                break;
            case 5: $fecha .= "Mayo";
                break;
            case 6: $fecha .= "Junio";
                break;
            case 7: $fecha .= "Julio";
                break;
            case 8: $fecha .= "Agosto";
                break;
            case 9: $fecha .= "Septiembre";
                break;
            case 10: $fecha .= "Octubre";
                break;
            case 11: $fecha .= "Noviembre";
                break;
            case 12: $fecha .= "Diciembre";
                break;
        }

        $fecha .= " de " . $y;

        $reporte->Cell(130, 6, $fecha, 1, 0, 'C', 1);
//        $reporte->Cell(50, 6, substr($ceneval->fecha, 11), 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetY(110);

        $reporte->Cell(190, 6, "Conserva bien este documento, ya que lo necesitas para futuros trámites como Baja Definitia y Egreso", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "Es necesario realizar estas dos actividades para poder participar en el proceso de admisión", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "REVISIÓN 3                                    A partir del 16 de Abril de 2007                                    FR-02-DPL-CE-PO-024", 0, 0, 'C', 1);

        $reporte->Output("public/files/pdfs/aspirantes/" . $ficha . ".pdf");

        $this->redirect("public/files/pdfs/aspirantes/" . $ficha . ".pdf");
    } // function ficha_alumno($folio, $tipoAspirante, $periodo_aspirante)


    function fichablanco() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $this->set_response("view");

        $reporte = new FPDF();

        $reporte->Open();
        $reporte->AddPage();

        $reporte->AddFont('verdana', '', 'verdana.php');

        $reporte->Image('http://ase.ceti.mx/public/img/logoceti.jpg', 5, 8);

        $reporte->Image('http://ase.ceti.mx/public/img/fotografia.jpg', 180, 15);

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 14);
        $reporte->MultiCell(0, 3, "CENTRO DE ENSEÑANZA TÉCNICA INDUSTRIAL", 0, 'C', 0);

        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 12);
        $reporte->MultiCell(0, 3, "Organismo Público Descentralizado Federal", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "Departamento de Servicios de Apoyo Académico", 0, 'C', 0);
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 8);
        $reporte->MultiCell(0, 2, "PLANTEL COLOMOS", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetX(20);
        $reporte->SetFont('verdana', '', 10);
        $reporte->MultiCell(0, 2, "FICHA DE ASPIRANTE PARA ADMISIÓN", 0, 'C', 0);

        $reporte->Ln();
        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 6);

        $reporte->Cell(20, 4, "FICHA", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "NOMBRE DEL ASPIRANTE", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "NIVEL", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "CARRERA", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "PERIODO", 1, 0, 'C', 1);


        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->Cell(20, 4, "", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "", 1, 0, 'C', 1);

        if ($aspirante->nivel == "T") {
            $nivel = "TECNÓLOGO";
            switch ($aspirante->carrera) {
                case 200: $carrera = "TECNÓLOGO EN INFORMÁTICA Y COMPUTACIÓN";
                    break;
                case 400: $carrera = "TECNÓLOGO EN CONTROL AUTOMÁTICO E INSTRUMENTACIÓN";
                    break;
                case 500: $carrera = "TECNÓLOGO EN CONTRUCCIÓN";
                    break;
                case 600: $carrera = "TECNÓLOGO EN ELECTRÓNICA Y COMUNICACIONES";
                    break;
                case 700: $carrera = "TECNÓLOGO EN ELECTROTECNIA";
                    break;
                case 800: $carrera = "TECNÓLOGO EN MÁQUINAS HERRAMIENTA";
                    break;
                case 801: $carrera = "TECNÓLOGO EN MECÁNICA AUTOMOTRIZ";
                    break;
                case 804: $carrera = "TECNÓLOGO EN MANUFACTURA DE PLÁSTICOS";
                    break;
            }
        } else {
            $nivel = "INGENIERÍA";
            switch ($aspirante->carrera) {
                case 400: $carrera = "INGENIERÍA INDUSTRIAL";
                    break;
                case 600: $carrera = "INGENIERÍA EN ELECTRÓNICA";
                    break;
                case 801: $carrera = "INGENIERÍA EN MECATRÓNICA";
                    break;
            }
        }

        if ($aspirante->periodo[0] == '1')
            $periodo = "FEB - JUN, ";
        else
            $periodo = "AGO - DIC, ";

        $periodo .= substr($aspirante->periodo, 1, 4);

        $reporte->Cell(25, 4, "", 1, 0, 'C', 1);
        $reporte->Cell(60, 4, "", 1, 0, 'C', 1);
        $reporte->Cell(25, 4, "", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->SetTextColor(0);
        $reporte->SetDrawColor(0xFF, 0x66, 0x33);
        $reporte->SetFont('verdana', '', 8);

        $reporte->SetFillColor(0xDD, 0xDD, 0xDD);
        $reporte->Cell(55, 6, "ACTIVIDAD", 1, 0, 'C', 1);
        $reporte->Cell(60, 6, "FECHA", 1, 0, 'C', 1);
        $reporte->Cell(20, 6, "HORA", 1, 0, 'C', 1);
        $reporte->Cell(55, 6, "AULA", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        if ($exani->nivel == 'T')
            $reporte->Cell(55, 6, "", 1, 0, 'L', 1);
        else
            $reporte->Cell(55, 6, "", 1, 0, 'L', 1);

        $fecha = substr($exani->fecha, 0, 10);

        $d = substr($fecha, 8, 2);
        $m = substr($fecha, 5, 2);
        $y = substr($fecha, 0, 4);

        $tiempo = mktime(0, 0, 0, $m, $d, $y);

        $fecha = date("w", $tiempo);

        switch ($fecha) {
            case 0: $fecha = "Domingo";
                break;
            case 1: $fecha = "Lunes";
                break;
            case 2: $fecha = "Martes";
                break;
            case 3: $fecha = "Miercoles";
                break;
            case 4: $fecha = "Jueves";
                break;
            case 5: $fecha = "Viernes";
                break;
            case 6: $fecha = "Sábado";
                break;
        }

        $fecha = $fecha . ", " . $d . " de ";

        switch ($m) {
            case 1: $fecha .= "Enero";
                break;
            case 2: $fecha .= "Fabrero";
                break;
            case 3: $fecha .= "Marzo";
                break;
            case 4: $fecha .= "Abril";
                break;
            case 5: $fecha .= "Mayo";
                break;
            case 6: $fecha .= "Junio";
                break;
            case 7: $fecha .= "Julio";
                break;
            case 8: $fecha .= "Agosto";
                break;
            case 9: $fecha .= "Septiembre";
                break;
            case 10: $fecha .= "Octubre";
                break;
            case 11: $fecha .= "Noviembre";
                break;
            case 12: $fecha .= "Diciembre";
                break;
        }

        $fecha .= " de " . $y;

        $reporte->Cell(60, 6, "", 1, 0, 'C', 1);
        $reporte->Cell(20, 6, "", 1, 0, 'C', 1);
        $reporte->Cell(55, 6, "", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->SetFillColor(0xFF, 0xFF, 0xFF);
        $reporte->Cell(55, 6, "", 1, 0, 'L', 1);

        $fecha = substr($ceneval->fecha, 0, 10);

        $d = substr($fecha, 8, 2);
        $m = substr($fecha, 5, 2);
        $y = substr($fecha, 0, 4);

        $tiempo = mktime(0, 0, 0, $m, $d, $y);

        $fecha = date("w", $tiempo);

        switch ($fecha) {
            case 0: $fecha = "Domingo";
                break;
            case 1: $fecha = "Lunes";
                break;
            case 2: $fecha = "Martes";
                break;
            case 3: $fecha = "Miercoles";
                break;
            case 4: $fecha = "Jueves";
                break;
            case 5: $fecha = "Viernes";
                break;
            case 6: $fecha = "Sábado";
                break;
        }

        $fecha = $fecha . ", " . $d . " de ";

        switch ($m) {
            case 1: $fecha .= "Enero";
                break;
            case 2: $fecha .= "Fabrero";
                break;
            case 3: $fecha .= "Marzo";
                break;
            case 4: $fecha .= "Abril";
                break;
            case 5: $fecha .= "Mayo";
                break;
            case 6: $fecha .= "Junio";
                break;
            case 7: $fecha .= "Julio";
                break;
            case 8: $fecha .= "Agosto";
                break;
            case 9: $fecha .= "Septiembre";
                break;
            case 10: $fecha .= "Octubre";
                break;
            case 11: $fecha .= "Noviembre";
                break;
            case 12: $fecha .= "Diciembre";
                break;
        }

        $fecha .= " de " . $y;

        $reporte->Cell(60, 6, "", 1, 0, 'C', 1);
        $reporte->Cell(20, 6, substr("", 11), 1, 0, 'C', 1);
        $reporte->Cell(55, 6, "", 1, 0, 'C', 1);

        $reporte->Ln();
        $reporte->Ln();

        $reporte->Cell(70, 25, "", 0, 0, 'C', 0);
        $reporte->Cell(60, 25, "", 1, 0, 'C', 1);
        $reporte->Cell(60, 25, "", 1, 0, 'C', 1);

        $reporte->SetY(110);

        $reporte->Cell(190, 6, "Conserva bien tu ficha, es tu única identificación para que puedas entrar al plantel y hacer tus examenes", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "Es necesario realizar estas dos actividades para poder participar en el proceso de admisión", 0, 0, 'C', 1);
        $reporte->Ln();
        $reporte->Cell(190, 6, "REVISIÓN 3                                    A partir del 16 de Abril de 2007                                    FR-02-DPL-CE-PO-024", 0, 0, 'C', 1);

        $reporte->SetY(100);

        $reporte->SetFont('verdana', '', 8);
        if ($exani->nivel == 'T')
            $reporte->Text(84, 98, "Llenado de Hoja de Registro Exani I");
        else
            $reporte->Text(84, 98, "Llenado de Hoja de Registro Exani II");
        $reporte->Text(100, 102, "Firma y fecha");

        $reporte->Text(148, 98, "Examen de Admisión CENEVAL");
        $reporte->Text(160, 102, "Firma y fecha");

        // /datos/calculo/ingenieria/apps/default/views
        $reporte->Output("/datos/calculo/ingenieria/public/files/pdfs/aspirantes/fichablanco.pdf");

        $this->redirect("public/files/pdfs/aspirantes/fichablanco.pdf");
    }

    function listaConsulta() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $aspirantes = new Aspirantes();

        unset($this->iiii);
        unset($this->ficha);
        unset($this->folio);
        unset($this->carrera);
        unset($this->plantel);
        unset($this->nombre);
        unset($this->paterno);
        unset($this->materno);

        unset($this->periodoo);
        unset($this->periodoNumero);

        $this->periodoNumero = $periodo;
        if (!isset($periodo))
            $this->$periodoo = $periodo;

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";
        $this->periodoo .= substr($periodo, 1, 4);

        $this->iiii = 0;
        foreach ($aspirantes->find("periodo = " . $periodo . " order by ficha") as $asp) {
            $this->tipoAspirante[$this->iiii] = $asp->tipoAspirante;
            $this->ficha[$this->iiii] = $asp->ficha;
            $this->folio[$this->iiii] = $asp->folio;
            $this->carrera[$this->iiii] = $asp->carrera;
            $this->plantel[$this->iiii] = $asp->plantel;
            $this->nombre[$this->iiii] = $asp->nombre;
            $this->paterno[$this->iiii] = $asp->paterno;
            $this->materno[$this->iiii] = $asp->materno;
            $this->iiii++;
        }
    }

// function listaConsulta($periodo)

    function listaConsulta2($periodo) {
        $this->checarusuario();

        $this->set_response("view");

        $aspirantes = new Aspirantes();

        unset($this->iiii);
        unset($this->ficha);
        unset($this->folio);
        unset($this->carrera);
        unset($this->plantel);
        unset($this->nombre);
        unset($this->paterno);
        unset($this->materno);

        unset($this->periodoo);
        unset($this->periodoNumero);

        $this->periodoNumero = $periodo;

        if (!isset($periodo))
            $this->$periodoo = $periodo;

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";
        $this->periodoo .= substr($periodo, 1, 4);

        $this->iiii = 0;
        foreach ($aspirantes->find("periodo = " . $periodo . " order by ficha") as $asp) {
            $this->tipoAspirante[$this->iiii] = $asp->tipoAspirante;
            $this->ficha[$this->iiii] = $asp->ficha;
            $this->folio[$this->iiii] = $asp->folio;
            $this->carrera[$this->iiii] = $asp->carrera;
            $this->plantel[$this->iiii] = $asp->plantel;
            $this->nombre[$this->iiii] = $asp->nombre;
            $this->paterno[$this->iiii] = $asp->paterno;
            $this->materno[$this->iiii] = $asp->materno;
            $this->iiii++;
        }

        $this->render_partial("listaConsulta2");
    } // function listaConsulta2($periodo)

    function agregarRegistro() {
        $this->checarusuario();
		
        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();
        $aspirantes = new Aspirantes();

        unset($this->iiii);
        unset($this->ficha);
        unset($this->folio);
        unset($this->carrera);
        unset($this->plantel);
        unset($this->nombre);
        unset($this->paterno);
        unset($this->materno);
        unset($this->registro);

        unset($this->periodoo);

        if (!isset($periodo))
            $this->$periodoo = $periodo;

        if ($periodo[0] == '1')
            $this->periodoo = "FEBRERO - JUNIO, ";
        else
            $this->periodoo = "AGOSTO - DICIEMBRE, ";
        $this->periodoo .= substr($periodo, 1, 4);

        $this->iiii = 0;
        foreach ($aspirantes->find("periodo = " . $periodo . " order by ficha") as $asp) {
            $this->idAspirante[$this->iiii] = $asp->id;
            $this->tipoAspirante[$this->iiii] = $asp->tipoAspirante;
            $this->ficha[$this->iiii] = $asp->ficha;
            $this->folio[$this->iiii] = $asp->folio;
            $this->carrera[$this->iiii] = $asp->carrera;
            $this->plantel[$this->iiii] = $asp->plantel;
            $this->nombre[$this->iiii] = $asp->nombre;
            $this->paterno[$this->iiii] = $asp->paterno;
            $this->materno[$this->iiii] = $asp->materno;
            $this->registro[$this->iiii] = $asp->registro;
            $this->iiii++;
        }
		
		$this->render("agregar_registro_view");
    } // function agregarRegistro($periodo)
	
    function buscandoRegistro($periodo) {
		$this->checarusuario();
		
		$this -> set_response("view");
		
		$ficha = $this -> post("ficha");
		$folio = $this -> post("folio");
		$folioceneval = $this -> post("folioceneval");
		$nombre = $this -> post("nombreAlumno");
		
		unset($this->reconsideracionBaja);
		
		$Periodos = new Periodos();
		$periodo = $Periodos->get_periodo_actual();
		
		$Aspirantes = new Aspirantes();
		$Carrera = new Carrera();
		$Areadeformacion = new Areadeformacion();
		
		if( ($nombre == "" && $ficha == "" && $folio == "" && $folioceneval == "") || 
				$nombre == "Nombre Alumno" && $ficha == "Ficha" && $folio == "Folio" && $folioceneval == "Ficha" ){
			$this -> render_partial("kardex/errorAlumnoNoEncontrado");
			return;
		}
		// Si ingreso un registro, usar solo el registro y verificar que sea integer.
		if( ($ficha != "" && $ficha != "Ficha") && ($folio != "" && $folio != "Folio") )
			$query =  "ficha = '".$ficha."' and folio = '".$folio."'";
		else if( $ficha != "" && $ficha != "Ficha" )
			$query =  "ficha = '".$ficha."'";
		else if( $folio != "" && $folio != "Folio" )
			$query =  "folio = '".$folio."'";
		else if( $folioceneval != "" && $folioceneval != "Folio Ceneval" )
			$query =  "folioceneval = '".$folioceneval."'";
		else
			$query =  "(nombre like '%".$nombre."%' or paterno like '%".$nombre."%' or materno like '%".$nombre."%')";
		
		$query .= " and periodo = '".$periodo."'";
		
		$this -> aspirante = $Aspirantes -> find_first($query);
		if( !isset($this -> aspirante -> id) ){
			//$this -> render_partial("correcionKardex");
			$this -> render_partial("kardex/errorAlumnoNoEncontrado");
			return;
			//return $this -> route_to("controller: ingcalculo", "action: correcionKardex", "id:1");
		}
		// El alumno ya tiene registro
		if( $this -> aspirante -> registro != 0 ){
			$this -> render_partial("aspirantes/agregar-registro_aspirantes_con_registro");
			return;
		}
		
        $periodo_letra = $Periodos->convertirPeriodo_($periodo);
		
		$this->render_partial("aspirantes/agregar-registro_buscando_registro");
		
    } // function buscandoRegistro($periodo)
	
    function creandoAlumno() {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_actual();
        $registro = $this->post("registro");
        $aspirante_id = $this->post("aspirante_id");

        $xccursos = new Xccursos();
        $alumnosss = new Alumnos();
        $Aspirantes = new Aspirantes();

        // admitidos
        //id, nombre, registro, periodo
        // admitodos2
        //id, nombre, plantel, carrera_id, registro, periodo
        // alumnos
        //id, enNivEdu, enPlantel, enPlan, enTurno, idtiEsp, tNivel, chGpo, miReg,
        //enTipo, stSit, vcNomAlu, enSexo, daFechNac, miPerIng, correo, situacion,
        //condonado, pago
        // xalumnocursos
        //id, periodo, curso, registro, faltas1, calificacion1, faltas2, calificacion2
        //faltas3, calificacion3, faltas, calificacion, situacion
        // usuarios
        //id, login, passwd, categoria, registro, clave, tipousuario
        //hacerupdate a aspirantes para agregarle el registro.
		
		$aspirante = $Aspirantes->find_first("periodo = ".$periodo." and id = ".$aspirante_id);
		$periodo = $aspirante->periodo;
		if ($registro == 0){
			$this -> render_partial("kardex/errorAlumnoNoEncontrado");
			return;
		}
		if ($alumnosss->find_first("miReg = " . $registro)) {
			echo "<br /><br />";
			$this -> render_partial("aspirantes/agregar-registro_aspirantes_con_registro");
			$this -> render_partial("aspirantes/agregar-registro_regresar");
			return;
		}
		$aspirante->registro = $registro;
		$aspirante->update();
		
		if($aspirante->opc1==1 || $aspirante->opc1==2){
			$carrera_id = 7;
		}
		if($aspirante->opc1==4 || $aspirante->opc1==5){
			$carrera_id = 8;
		}
		if($aspirante->opc1==6){
			$carrera_id = 9;
		}
		if($aspirante->opc1==7){
			$carrera_id = 10;
		}

		// alumnos
		$alumnos = new Alumnos();
		$alumnos->id = "default";
		$alumnos->enNivEdu = "I";
		if ($aspirante->plantel == "colomos")
			$plantell = "C";
		else
			$plantell = "N";

		$alumnos->enPlantel = $plantell;
		$alumnos->enPlan = "PE07"; // Para Alumnos en el plan 2007
		if ($aspirante->opc1 == 2)
			$turnoo = "M";
		else
			$turnoo = "V";
		$alumnos->enTurno = $turnoo;
		if ($aspirante->carrera == 801)
			$esp = 16;
		else if ($aspirante->carrera == 400)
			$esp = 19;
		else if ($aspirante->carrera == 900)
			$esp = 23;
		else if ($aspirante->carrera == 901)
			$esp = 24;
		else
			$esp = 13;
		$alumnos->idtiEsp = $esp;
		$alumnos->carrera_id = $carrera_id;
		$alumnos->tNivel = '0';
		$alumnos->chGpo = "**";
		$alumnos->miReg = $registro;
		$alumnos->enTipo = "R";
		$alumnos->stSit = "OK";
		$alumnos->vcNomAlu = $aspirante->paterno . " " . $aspirante->materno . " " . $aspirante->nombre;
		$alumnos->paterno = $aspirante->paterno;
		$alumnos->materno = $aspirante->materno;
		$alumnos->nombre = $aspirante->nombre;
		$alumnos->enSexo = $aspirante->sexo;
		$alumnos->daFechNac = $aspirante->fecha_nacimiento;
		$alumnos->miPerIng = $periodo;
		$alumnos->correo = $aspirante->correo;
		$alumnos->situacion = "NUEVO INGRESO";
		$alumnos->condonado = '0';
		$alumnos->correoOficial = '-';
		$alumnos->pago = '0';

		$alumnos->create();

		// admitidos
		$admitidos = new Admitidos();

		$admitidos->id = "default";
		$admitidos->nombre = $aspirante->paterno . " " . $aspirante->materno . " " . $aspirante->nombre;
		$admitidos->registro = $registro;
		$admitidos->periodo = $periodo;

		$admitidos->create();

		//admitidos2
		$admitidos2 = new Admitidos2();

		$admitidos2->id = "default";
		$admitidos2->plantel = $plantell;
		$admitidos2->carrera_id = $carrera_id;
		$admitidos2->registro = $registro;
		$admitidos2->periodo = $periodo;
		$admitidos2->nombre_completo = $aspirante->paterno . " " . $aspirante->materno . " " . $aspirante->nombre;
		
		$admitidos2->create();

		// usuarios
		//id, login, passwd, categoria0, registro, clave, tipousuario
		$Usuarios = new Usuarios();
		$Usuarios->id = "default";
		$Usuarios -> passwd_old = '';
		$Usuarios -> passwd_last_change = '0';
		$Usuarios->categoria = 1;
		$Usuarios->registro = $registro;
		$Usuarios->clave = $registro;
		$Usuarios->tipousuario = '0';

		$Usuarios->create();
		
		$Usuarios->update_clave_by_registro($registro);
		
        $this -> render_partial("aspirantes/agregar-registro_agregando_registro_exito");
		$this -> render_partial("aspirantes/agregar-registro_regresar");
    }

    function agregarPlantelSegunCarrera($carrera, $opcion) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();

        $this->set_response("view");

        unset($this->carreras);
        unset($this->opcion);
        unset($this->i);

        $carreraDisp = new carrerasdisp();

        $this->opcion = $opcion;

        $i = 0;
        foreach ($carreraDisp->find("idcarrera = " . $carrera) as $carreras) {
            $this->carreras[$i] = $carreras;
            $i++;
        }
        $this->i = $i;
        $this->render_partial("agregarPlantelSegunCarrera");
    } // function agregarPlantelSegunCarrera( $carrera, $opcion )

    function agregarTurnoSegunCarrera($carrera, $opcion) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();
        $this->set_response("view");

        unset($this->carreras);
        unset($this->opcion);
        unset($this->i);

        $carreraDisp = new carrerasdisp();

        $this->opcion = $opcion;

        $i = 0;
        foreach ($carreraDisp->find("idcarrera = " . $carrera) as $carreras) {
            $this->carreras[$i] = $carreras;
            $i++;
        }
        $this->i = $i;
        $this->render_partial("agregarTurnoSegunCarrera");
    } // function agregarTurnoSegunCarrera( $carrera, $opcion )

    function agregarPlantelSegunCarrera1($carrera, $plantel, $turno, $opcion) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();
        $this->set_response("view");

        unset($this->carreras);
        unset($this->opcion);
        unset($this->i);

        $carreraDisp = new carrerasdisp();

        $this->opcion = $opcion;

        $i = 0;
        foreach ($carreraDisp->find_all_by_sql(
                "select * from carrerasdisp
					where (idcarrera <> $carrera
					or plantel <> '" . $plantel . "'
					or turno <> '" . $turno . "')") as $carreras) {
            $this->carreras[$i] = $carreras;
            $i++;
        }
        $this->i = $i;
        $this->render_partial("agregarCarreraSegunCarrera");
    } // function agregarPlantelSegunCarrera1( $carrera, $plantel, $turno, $opcion )

    function agregarCarreraSegunCarrera($idcarrera, $opcion) {
        $this->checarusuario();

        $Periodos = new Periodos();
        $periodo = $Periodos->get_periodo_proximo();
        $this->set_response("view");

        unset($this->carreras);
        unset($this->opcion);
        unset($this->i);

        $carrerasDisp = new carrerasdisp();

        $this->opcion = $opcion;

        unset($this->carrerasDisp);
        $i = 0;
        foreach ($carrerasDisp->find_all_by_sql("
					select * from carrerasdisp
					where id <> $idcarrera") as $carrDisp) {
            $this->carrerasDisp[$i] = $carrDisp;
            $i++;
        }
        $this->i = $i;
        $this->render_partial("agregarCarreraSegunCarrera");
    } // function agregarCarreraSegunCarrera( $idcarrera, $opcion )

    function limpiar() {
        echo "";
        exit(1);
    } // function limpiar()

    function checarusuario() {
        if (Session::get_data('tipousuario') != "VENTANILLA") {
            $this->redirect('/');
        }
    }

}

?>