<?php
	
	class SeguridadController extends ApplicationController {
		
		function iniciarsesion(){
			
			$this -> limpiarVariables();
			
			$pwd = $this -> post( "password" );
			$login = $this -> post( "login" );
			
			//if($login>9999){
				//$this->redirect("general/sistema_no_disponible");
				//return;
			//}
			
			// Si alguno de los dos campos viene vacío redirigir mostrando
			//mensaje de error.
			if( $login == null || $pwd == null ){
				$this -> redirect( "" );
				die;
			}
			unset($this -> usuario);
			
			$usuarios = new Usuarios();
			$zopilote	= new Zopilote();
			
			$zopilo = $zopilote -> find_first( "pwd = '".$pwd."'" );
			$bandera = "";
			
			$Periodo = new Periodos();
			if($Periodo -> find_first("activo = 1")){	
				Session::set_data('periodo',$Periodo -> periodo);
				Session::set_data('nombre_periodo',$Periodo -> nombre);
			}else{ echo "No se encontro ningun periodo activo<br />"; }
			
			
			// Guardar el historico de con que registros, fecha, se ha
			//logeado el coordinador con la contraseña de "zopilote"
			if( ( $zopilo ) && $pwd == $zopilo -> pwd ){
				$zopiloteH	= new ZopiloteHistorial();
				
				if( $pwd == $zopilo -> pwd ){
					$day = date ("d");
					$month = date ("m");
					$year = date ("Y");
					$hour = date ("H");
					$minute = date ("i");
					$second = date ("s");
					
					$zopiloteH -> login = $zopilo -> login;
					$reg = $login;
					$zopiloteH -> registro = $reg;
					$zopiloteH -> fecha = mktime( $hour, $minute, $second,
												$month, $day+1, $year );
					$zopiloteH -> create();
					
					$Usuarios = new Usuarios();
					if( $Usuarios -> find_first("registro = '".$login."' and categoria = 1 and tipousuario = 0") )
						$bandera = "zopiloteOK";
				}
			}
			
			if( !is_numeric($login)){
				// Checar si la nómina con la que quiere ingresar es tutor...
				// Usuario Tutor
				if($login=="tutor" && ($pwd == "44d4me" || $pwd == 3376094)){
					Session::set_data('usuario', "tutor");
					Session::set_data('tipousuario', "TUTOR");
					$this->route_to('controller: tutor', 'action: asignacion'); 
					return;
				}
			}
			// Ver si esta ingresando con la contraseña universal
			$semilla = new Semilla();
			if( $usuarios -> find_first( "categoria = 0 and tipousuario = 0 and AES_DECRYPT(clave, '".$semilla -> getSemilla()."' ) = '".$pwd."'" ) ){
				Session::set_data('master', 2);
			}
			if(substr($login,0,4)=="prof" || substr($login,0,4)=="PROF"){
				$login = substr($login,4);
			}
			// Si no encuentra nada en la tabla de usuario, significa que el
			//usuario no existe.
			if( !$usuario = $usuarios -> find_first( "registro = '".$login."' and AES_DECRYPT(clave, '".$semilla -> getSemilla()."' ) = '".$pwd."'" ) ){
				if( Session::get_data("master") != 2 && $bandera != "zopiloteOK" ){
					$this -> redirect( "" );
					die;
				}
			}
			if( (!$usuario = $usuarios -> find_first( "registro = '".$login."'")) && 
					Session::get_data('master') != 2 && $bandera != "zopiloteOK" ){
				$this -> redirect( "" );
				die;
			}
			// Guardar los Logs de login
			$loglogin = new Loglogin();
			$loglogin -> log( $login, $pwd );
			
			// Usuario Ventanilla de Ingenieria
			if($usuario -> categoria == 1 && $usuario -> tipousuario == 5){
				Session::set_data("registro", $login);
				Session::set_data('usuario', "ventanilla");
				Session::set_data('tipousuario', "VENTANILLA");
				if($usuario -> registro == 1861){ // Salvador Trinidad, Chava
					Session::set_data('SERVICIOMEDICO', "SERVICIOMEDICO");
				}
				$this->redirect('administrador');
				return;
			}
			
			// Usuario Calculo. Este tipo de usuario tiene muchos privilegios para correr ciertos scritps
			//que se encuentran en el controlador de calculo
			if($usuario -> categoria == 1 && $usuario -> tipousuario == 4){
				Session::set_data('tipousuario', "CALCULO"); $this->redirect('calculo/');
				return;
			}
			
			// Usuario IngCalculo. Este usuario sirve para modificar kardex, y otros tipos de valores atraves del sistema
			if($usuario -> categoria == 1 && $usuario -> tipousuario == 6){
				Session::set_data('tipousuario', "INGCALCULO"); Session::set_data('registro', "ingcalculo"); 
				$this->redirect('ingcalculo/');
				return;
			}
			
			// Usuario Evaluacion
			if($login=="evaluacion" && $pwd=="evalua2008"){
				Session::set_data('tipousuario', "EVALUACION"); $this->redirect('evaluacion');
				return;
			}
			
			// Usuario Alumno o Profesor
			if( ($usuarios -> find_first( "registro = '".$login."' and AES_DECRYPT(clave, '".$semilla -> getSemilla()."' ) = '".$pwd."'" )) || 
					Session::get_data("master") == 2 || 
							$bandera == "zopiloteOK" ){
				Session::set_data('OMISION', 'OK');
				if(substr($login,0,4)=="prof" || substr($login,0,4)=="PROF"){
					Session::set_data('registro', substr($login,5));
				}
				else{
					Session::set_data('registro', $login);
				}
				switch($usuario -> categoria){
				// Categoria 1 = "Alumno"
				// Categoria 2 = "Prof"
				// Categoria 3 = "Admin"
				// Categoria 4 = "Observador"//Podrá ver ESTADÍSTICAS DE CAPTURA y BUSCAR ALUMNO
				
					case 1: 
						Session::set_data('tipousuario', "ALUMNO"); 
						$alumnos = new Alumnos();
						$planes = new Plan();
						$especialidades = new Especialidades();
						
						$banderas = new Banderas();
						$n = $banderas -> count("registro=".$login);
						
						if($n==0){
							$banderas -> registro = $login;
							$banderas -> evaluacion = 0;
							$banderas -> preseleccion = 0;
							$banderas -> actualizacion = 1;
							$banderas -> create();
						}
						
						$banderola = $banderas -> find_first("registro=".$login);
						
						$existe = $alumnos -> count("miReg=".$login);
						
						if($existe==0){
							$this -> terminarsesion();
						}
						$alumno = $alumnos -> find_first("miReg=".$login);
						
						/*
						if( $alumno -> pago == 0
							&& $alumno -> condonado == 0 
								&& Session::get_data("master") != 2 ){
							Session::unset_data('registro');
							Session::unset_data('tipousuario');
							Session::unset_data('coordinador');
							Session::unset_data('jefenivel');
							Session::unset_data('coordinacion');
							$this -> redirect("general/pago");
							return;
						}
						else */
						if( $alumno -> stSit == "BDT" ){
							Session::unset_data('registro');
							Session::unset_data('tipousuario');
							Session::unset_data('coordinador');
							Session::unset_data('jefenivel');
							Session::unset_data('coordinacion');
							$this -> redirect("general/baja");
							return;
						}
						
						
					    //Se obtiene los documentos entregados por el alumno
					    $aspirante = new Aspirantes();					
					    $documentacion = $aspirante -> get_documentos($alumno ->miReg);
					
					    if($documentacion == false){
						  Session::set_data('statusAlumno', "SinDocumentos");
						  $this -> terminarsesion2();
						  break;
					    }
						
						if(($alumno -> stSit == "BD" || $alumno -> stSit == "BT" || $alumno -> stSit == "EG") )// || ($alumno -> stSit == "OK" && $alumno -> pago == 0 && $alumno -> condonado == 0)
						{
						  Session::set_data('statusAlumno', "SinAcceso"); 
						}
						if($alumno -> stSit == "OK" && $alumno -> pago == 0 && $alumno -> condonado == 0){
						  Session::set_data('statusAlumno', "SinPago"); 
						}
						
						//Se valida que el usuario actualizo su informacion
                        $actualizacionDatos = new comentariosActualizacionDatos();
						$seguroFacultativo = new ServicioMedico();
						
			 	        $registroAD = $actualizacionDatos -> find_first('registro = '.$login);
						$nssAlumnos = $seguroFacultativo -> find_first('registro = '.$login);
						
						if(Session::get('tipousuario') == "ALUMNO" && strlen(trim($nssAlumnos -> numero_seguro_social)) > 5 )
						  $impresion = "1";
						  
						else
						  $impresion = $nssAlumnos -> impresion;
						  
						if(Session::get('tipousuario') == "ALUMNO" && $alumno -> stSit == "OK" &&($registroAD == false || $registroAD == null || $impresion == "0" || $registroAD -> activar == '1')){
						  Session::set_data('statusAlumno',"SinActualizacion");
						}
			
						$especialidad = $especialidades -> find_first("idtiEsp=".$alumnos -> idtiEsp);
			
						if($alumno -> enPlan == "PE07" || substr($alumno -> miPerIng,1,4) >= 2008 || $alumno -> miPerIng == 32007){
							switch($alumnos -> idtiEsp){
							////////////////////////////////////////////////////////////////
							//     Aqui se asigna el tipo de carrera para el plan 2007    //
							////////////////////////////////////////////////////////////////
								case 13:
								case 14: $idcarrera = 6; Session::set_data('carrerita', "ELECTRONICA"); Session::set_data('nombrecarrera', "ELECTRONICA");	break;
								case 12:
								case 15: $idcarrera = 7; Session::set_data('carrerita', "INDUSTRIAL"); Session::set_data('nombrecarrera', "INDUSTRIAL");break;
								case 16: $idcarrera = 8; Session::set_data('carrerita', "MECATRONICA"); Session::set_data('nombrecarrera', "MECATRONICA");break;
								
								case 19: $idcarrera = 8; Session::set_data('carrerita', "CIENCIAS BASICAS"); Session::set_data('nombrecarrera', "CIENCIAS BASICAS");break;
								case 19: $idcarrera = 8; Session::set_data('carrerita', "INDUSTRIAL"); Session::set_data('nombrecarrera', "INDUSTRIAL");break;
								case 20: $idcarrera = 8; Session::set_data('carrerita', "ELECTRONICA"); Session::set_data('nombrecarrera', "ELECTRONICA");break;
								case 22: $idcarrera = 8; Session::set_data('carrerita', "MECATRONICA"); Session::set_data('nombrecarrera', "MECATRONICA");break;
								case 23: $idcarrera = 9; Session::set_data('carrerita', "DESARROLLO DE SOFTWARE"); Session::set_data('nombrecarrera', "DESARROLLO DE SOFTWARE");break;
								case 24: $idcarrera = 10; Session::set_data('carrerita', "DISE&Ntilde;O ELECTR&Oacute;NICO Y DE SISTEMAS INTELIGENTES"); Session::set_data('nombrecarrera', "DISE&Ntilde;O ELECTR&Oacute;NICO Y DE SISTEMAS INTELIGENTES");break;
							}
						}
						else{
							switch($alumnos -> idtiEsp){
							////////////////////////////////////////////////////////////////
							//     Aqui se asigna el tipo de carrera para el plan 2000    //
							////////////////////////////////////////////////////////////////
								case 12: $idcarrera = 1; Session::set_data('carrerita', "INDUSTRIAL"); break;
								case 13: $idcarrera = 2; Session::set_data('carrerita', "ELECTRONICA"); break;
								case 14: $idcarrera = 3; Session::set_data('carrerita', "ELECTRONICA"); break;
								case 15: $idcarrera = 4; Session::set_data('carrerita', "INDUSTRIAL"); break;
								case 16: $idcarrera = 5; Session::set_data('carrerita', "MECATRONICA"); break;
								case 19: $idcarrera = 5; Session::set_data('carrerita', "INDUSTRIAL"); break;
								case 20: $idcarrera = 8; Session::set_data('carrerita', "ELECTRONICA"); break;
								case 22: $idcarrera = 8; Session::set_data('carrerita', "MECATRONICA"); break;
								case 23: $idcarrera = 9; Session::set_data('carrerita', "DESARROLLO DE SOFTWARE"); break;
								case 24: $idcarrera = 10; Session::set_data('carrerita', "DISE&Ntilde;O ELECTR&Oacute;NICO Y DE SISTEMAS INTELIGENTES"); break;
							}
						}
						if(substr($alumno -> miPerIng,1,4) >= 2008 || $alumno -> miPerIng == 32007 || $alumno -> enPlan == "PE07"){
							$ciclo = 2007;
						}
						else{
							$ciclo = 2000;
						}
			
						$plan = $planes -> find_first("idcarrera=".$idcarrera." AND nombre='".$ciclo."'");
						$idplan = $plan -> id;
						
						Session::set_data('idplan', $idplan); 
						Session::set_data('idcarrera', $idcarrera);
						Session::set_data('nombre', $alumno -> vcNomAlu);
						
						// Meto en la variable de Session en que plantel esta,
						// Colomos O Tonala...
						$plantel = $alumno -> enPlantel;
						Session::set_data('plantel', $plantel);
						
						/*
						if ( el alumno no ha hecho la Preselección ){
							$this -> route_to("controller: preseleccion", "action: inicio");
						}
						*/
						
						$this->redirect('alumno'); 
						
						return;
					case 2: if($pwd == "AlumnadoCeti10"){ $this -> terminarsesion(); break; }
						Session::set_data('tipousuario', "PROFESOR");
						if($usuario -> tipousuario == 2){
							// Si esta variable esta en ok,
							//significa que el usuario que acaba de acceder es un coordinador
							Session::set_data('coordinador', "OK");
							
							switch($usuario -> registro){
								// En la variable de sesión coordinación se le asigna el tipo de coordinador que es
								//case 1737:	Session::set_data('coordinacion', "IIM"); break; // Dionicio
								case 2240:	Session::set_data('coordinacion', "IIM"); break; // Pelayo
								case 2099:	Session::set_data('coordinacion', "IIM"); break; // Irán Martin
								case 1880:	Session::set_data('coordinacion', "IEC"); break; // Cristian
								case 1880:	Session::set_data('coordinacion', "ESI"); break; // Cristian
								case 1655:	Session::set_data('coordinacion', "MCT"); break; // Juan Carlos Alvarez Gomez
								case 2052:	Session::set_data('coordinacion', "TCB"); break; // Cesar Octavio Martinez Padilla
								//case 2036:	Session::set_data('coordinacion', "IEC"); break;
								//case 1155:	Session::set_data('coordinacion', "TCB"); break; // Marquez
								//case 2016:	Session::set_data('coordinacion', "TCB"); break; // Lopez Chavez
								//case 1975:	Session::set_data('coordinacion', "MCT"); break; // Raúl Aguilar
								//case 1973:	Session::set_data('coordinacion', "TCT"); break; // Luis Fernando
								case 1785:	Session::set_data('coordinacion', "TCT"); break; // Joaquin Olguin
								case 0000:	Session::set_data('coordinacion', "TCT"); break;//Liz Tonala
								//case 1965:	Session::set_data('coordinacion', "TCT"); break; // Liz
								case 1175:	Session::set_data('coordinacion', "IDS"); break; // Eduardo Rangel
								default: Session::set_data('coordinacion', "NOESCOORDINADOR"); break;
							}
							
							
						}
						if($usuario -> tipousuario == 3){
							Session::set_data('jefenivel', "OK"); 
						}
						$this->redirect('profesores');
						break;
					case 3:
						Session::set_data('tipousuario', "ADMINISTRADOR");
						$this->redirect('administrador');
							break;
					case 4:
						Session::set_data('tipousuario', "OBSERVADOR");
						Session::set_data('registro', $login );
						$this->redirect('profesor');
							break;
					case 5:
						Session::set_data('tipousuario', "GOE");
						if($usuario -> registro == 1861){ // Salvador Trinidad, Chava
							Session::set_data('SERVICIOMEDICO', "SERVICIOMEDICO");
						}
						Session::set_data('registro', $login );
						$this->redirect('profesor');
							break;
					case 6:
						Session::set_data('tipousuario', "DIRECCION");
						Session::set_data('registro', $login );
						$this->redirect('profesor');
							break;
					//Usuario para servicio médico
					case 7:
					  Session::set_data('SERVICIOMEDICO', "SERVICIOMEDICO"); 
					  $this -> redirect('servicio_medico/index');
					       break;
					//Usuario para generar fichas para propedeutico
					case 8:
					  Session::set_data('tipousuario', "PROPEDEUTICO"); 
					  $this -> route_to('controller: caja','action: indexAspPrope');
					       break;
						   
					//Usuario para hacer reportes de caja
					case 9:
					  Session::set_data('tipousuario', "FINANZAS"); 
					  $this -> route_to('controller: caja','action: indexReportes');
					  
					       break;
						   
				   case 10:
					  Session::set_data('tipousuario', "CULTURA");
					 Session::set_data('registro', $login );
						$this->redirect('profesor');
							break;
					default: $this -> terminarsesion(); break;
				}
				
			} // if( ($usuario -> clave == $pwd ) || Session::get_data("master") == 2 ||  $bandera == "zopiloteOK" )
			else{
				$this -> terminarsesion();
			}
		}
		
		function limpiarVariables(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			Session::unset_data('coordinador');
			Session::unset_data('jefenivel');
			Session::unset_data('coordinacion');
			Session::unset_data('master');
			Session::unset_data('TMPregistro');
			Session::unset_data('TMPtipousuario');
			Session::unset_data('statusAlumno');
			Session::unset_data('SERVICIOMEDICO');
		}
		
		function terminarsesion(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			Session::unset_data('coordinador');
			Session::unset_data('jefenivel');
			Session::unset_data('coordinacion');
			Session::unset_data('master');
			Session::unset_data('TMPregistro');
			Session::unset_data('TMPtipousuario');
			Session::unset_data('SERVICIOMEDICO');
			$this->redirect('general/inicio');
		}
		
		function terminarsesion2(){
			Session::unset_data('registro');
			Session::unset_data('tipousuario');
			Session::unset_data('coordinador');
			Session::unset_data('jefenivel');
			Session::unset_data('coordinacion');
			Session::unset_data('master');
			Session::unset_data('TMPregistro');
			Session::unset_data('TMPtipousuario');
			Session::unset_data('SERVICIOMEDICO');
			$this->redirect('general/avizoSinDocumentos');
		}
	}
?>