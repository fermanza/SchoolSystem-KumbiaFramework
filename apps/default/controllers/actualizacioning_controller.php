<?php
	class ActualizacioningController extends ApplicationController {
		
		//funcion para modificar datos
		function modificainformacion(){
			$this -> valida();
		}
	
		/**
		* funcion que busca los datos del alumno
		*/
		function buscar(){
			$this -> set_response("view");
			if($this -> post('registro') ){
				$this -> registro = $this -> post('registro');
				
				$XalumnosPersonal = new XAlumnosPersonal();
				//Checar por que salen valores en la pantalla de servicio medico.
				$datos  = $XalumnosPersonal -> find_all_by_sql("SELECT al.nombre,al.paterno,al.materno,ap.sexo,ap.estadocivil,ap.sangre,ap.domicilio,ap.colonia,ap.cp,
					 ap.ciudad AS ciudadPersonal,ap.estado AS estadoAlumno,ap.telefono,ap.curp,ap.telefono_otro,ap.celular AS celPersonal,ap.correo AS correoPersonal,
					 ap.fnacimiento,ap.municipioNac,ap.paisNacimiento AS nac,ap.estadoNac,ap.numero_exterior,ap.numero_interior,sm.seguridad_social,sm.dependencia_seguro,
					 sm.especificar_enfermedad,sm.alergias,sm.especifica_alergias,sm.capacidad_diferente,sm.especifica_capacidad,sm.protesis,sm.especifica_protesis,
					 sm.parte_de_quien_seguro,sm.numero_seguro_social,sm.numClinica,sm.vigencia,sm.tipoSangre,sm.otroSeguro,ip.nombre_pariente,apellido_paterno,apellido_materno,ip.parentesco,ip.correo,
					 ip.nacionalidad,ip.paisNacimiento,ip.grado_estudios,ip.calle_numero,ip.colonia AS colonia_pariente,ip.ciudad AS ciudadP,ip.estado AS estadoP,ip.telefono_casa,
					 ip.celular,ip.ocupacion,ip.telefono_trabajo,ip.nombre_pariente2,apellido_paterno2,apellido_materno2,ip.parentesco_pariente2,ip.correo_pariente2,
					 ip.nacionalidad_pariente2,ip.paisNacimiento_pariente2,ip.grado_estudios_pariente2,ip.calle_numero_pariente2,ip.colonia_pariente2,ip.estado_pariente2,ip.ciudad_pariente2,
					 ip.telefono_casa_pariente2,ip.celular_pariente2,ip.ocupacion_pariente2,ip.telefono_trabajo_pariente2,it.nombre_empresa,it.domicilio_empresa,
					 it.colonia_empresa,it.cp_empresa,it.ciudad_empresa,it.estado_empresa,it.telenofo_empresa,it.horario_entrada,it.horario_salida,it.puesto,
					 ca.nombre AS nombreCarrera,ca.clave,al.enPlantel,al.nacionalidad AS nacionalidadAlumno,asp.folio,asp.ficha,asp.periodo,asp.folioceneval,
					 asp.carrera,asp.procedencia,asp.promedioprepa,asp.estadoprepa,asp.registro_tecnologo,asp.plantel,asp.tipoIngreso,asp.carreraTramite,
					 asp.anio1,asp.anio2,asp.municipioEscuela,asp.periodoEgreso
					 FROM xalumnos_personal AS ap LEFT JOIN servicio_medico AS sm ON ap.registro = sm.registro
					 LEFT JOIN alumnos AS al ON ap.registro = al.miReg
					 LEFT JOIN carrera AS ca ON al.carrera_id = ca.id
					 LEFT JOIN informacion_padres AS ip ON ap.registro = ip.registro
					 LEFT JOIN informacion_trabajo AS it ON ap.registro = it.registro
					 LEFT JOIN aspirantes AS asp ON ap.registro = asp.registro
					 WHERE ap.registro = ".$this -> registro);
				
			    
				if( $datos != false ){
					foreach($datos AS $info){
						
						//Informacion personal del alumno
						$this -> nombre = utf8_encode($info -> nombre);
						$this -> paterno = utf8_encode($info -> paterno);
						$this -> materno = utf8_encode($info -> materno);
						$this -> sexo = $info -> sexo;
						$this -> estadocivil = $info -> estadocivil;
						$this -> sangre = $info -> sangre;
						$this -> domicilio = utf8_encode($info -> domicilio);
						$this -> colonia = utf8_encode($info -> colonia);
						$this -> cp = $info -> cp;
						$this -> ciudadAlumno = utf8_encode($info -> ciudadPersonal);
						$this -> estadoAlumno = utf8_encode($info -> estadoAlumno);
						$this -> telefono = $info -> telefono;
						$this -> curp = $info -> curp;
						$this -> telefono_otro = $info -> telefono_otro;
						$this -> celPersonal = $info -> celPersonal;
						$this -> correoPersonal = $info -> correoPersonal;
						$this -> fnacimiento = $info -> fnacimiento;
						$this -> ciudadNacimiento = utf8_encode($info -> municipioNac);
						$this -> estadoNac = utf8_encode($info -> estadoNac);
						$this -> paisNacimientoAl = utf8_encode($info -> nac);
						$this -> nacionalidadAl = utf8_encode($info -> nacionalidadAlumno);
						$this -> nombreCarrera = utf8_encode($info -> nombreCarrera);
						$this -> numero_exterior = $info -> numero_exterior;
						$this -> numero_interior = $info -> numero_interior;
						
						if( $info -> enPlantel == "C" ){
							$this -> plantelE = "Colomos";
						}
						else{
							$this -> plantelE = "Tonala";
						}
						
						if( $info -> seguridad_social == "SI" ){
							//Informacion de segurosocial
							$this -> seguroSi = "block";
							$this -> seguro = $info -> seguridad_social;
							$this -> dependencia = $info -> dependencia_seguro;
							$this -> parteDeQuien = $info -> parte_de_quien_seguro;
							$this -> noAfiliacion = $info -> numero_seguro_social;
							$this -> numClinica = $info -> numClinica;
							$this -> vigencia = $info -> vigencia;
							$this -> tipoSangre = $info -> tipoSangre;
							$this -> otroSeguro = $info -> otroSeguro;
							if( $info -> enfermedad_cronica == "" ){
								$this -> enfermedad_cronica = "NO";
							}else{
								$this -> enfermedad_cronica = $info -> enfermedad_cronica;
							}
							$this -> especificar_enfermedad = $info -> especificar_enfermedad;
							
							if( $info -> alergias == "" ){
								$this -> alergias = "NO";
							}else{
								$this -> alergias = $info -> alergias;
							}
							$this -> especifica_alergias = $info -> especifica_alergias;
							
							if( $info -> capacidad_diferente == "" ){
								$this -> capacidad_diferente = "NO";
							}else{
								$this -> capacidad_diferente = $info -> capacidad_diferente;
							}
							$this -> especifica_capacidad = $info -> especifica_capacidad;
							if( $info -> protesis == "" ){
								$this -> protesis = "NO";
							}else{
								$this -> protesis = $info -> protesis;
							}
							$this -> especifica_protesis = $info -> especifica_protesis;
						}
						else{
							$this -> seguro = $info -> seguridad_social;
							$this -> seguroSi = "block";
						}
						//Datos de parentesco
						$this -> nombre_pariente = utf8_encode($info -> nombre_pariente);
						$this -> apellido_paterno = utf8_encode($info -> apellido_paterno);
						$this -> apellido_materno = utf8_encode($info -> apellido_materno);
						$this -> parentesco = utf8_encode($info -> parentesco);
						$this -> correo = $info -> correo;
						$this -> nacionalidad = $info -> nacionalidad;
						$this -> paisNacimiento = $info -> paisNacimiento;
						$this -> grado_estudios = $info -> grado_estudios;
						$this -> calle_numero = utf8_encode($info -> calle_numero);
						$this -> colonia_pariente = utf8_encode($info -> colonia_pariente);
						$this -> ciudadP = utf8_encode($info -> ciudadP);
						$this -> estadoP = utf8_encode($info -> estadoP);
						$this -> telefono_casa = $info -> telefono_casa;
						$this -> celular = $info -> celular;
						$this -> ocupacion = utf8_encode($info -> ocupacion);
						$this -> telefono_trabajo = $info -> telefono_trabajo;
						$this -> nombre_pariente2 = utf8_encode($info -> nombre_pariente2);
						$this -> apellido_paterno2 = utf8_encode($info -> apellido_paterno2);
						$this -> apellido_materno2 = utf8_encode($info -> apellido_materno2);
						$this -> parentesco_pariente2 = utf8_encode($info -> parentesco_pariente2);
						$this -> correo_pariente2 = $info -> correo_pariente2;
						$this -> nacionalidad_pariente2 = utf8_encode($info -> nacionalidad_pariente2);
						$this -> paisNacimiento_pariente2 = utf8_encode($info -> paisNacimiento_pariente2);
						$this -> grado_estudios_pariente2 = $info -> grado_estudios_pariente2;
						$this -> calle_numero_pariente2 = utf8_encode($info -> calle_numero_pariente2);
						$this -> colonia_pariente2 = utf8_encode($info -> colonia_pariente2);
						$this -> ciudad_pariente2 = utf8_encode($info -> ciudad_pariente2);
						$this -> estado_pariente2 = utf8_encode($info -> estado_pariente2);
						$this -> telefono_casa_pariente2 = $info -> telefono_casa_pariente2;
						$this -> celular_pariente2 = $info -> celular_pariente2;
						$this -> ocupacion_pariente2 = utf8_encode($info -> ocupacion_pariente2);
						$this -> telefono_trabajo_pariente2 = $info -> telefono_trabajo_pariente2;
						//Datos de trabajo
						$this -> nombre_empresa = utf8_encode($info -> nombre_empresa);
						$this -> domicilio_empresa = utf8_encode($info -> domicilio_empresa);
						$this -> colonia_empresa = utf8_encode($info -> colonia_empresa);
						$this -> cp_empresa = $info -> cp_empresa;
						$this -> ciudad_empresa = utf8_encode($info -> ciudad_empresa);
						$this -> estado_empresa = utf8_encode($info -> estado_empresa);
						$this -> telenofo_empresa = $info -> telenofo_empresa;
						$this -> horario_entrada = $info -> horario_entrada;
						$this -> horario_salida = $info -> horario_salida;
						$this -> puesto = utf8_encode($info -> puesto);
						
						$this -> carreraNom = $info -> carrera;
						$this -> folio = $info -> folio;
						$this -> ficha = $info -> ficha;
						$this -> periodo = $info -> periodo;
						$this -> folioceneval = $info -> folioceneval;
						$this -> procedencia = $info -> procedencia;
						$this -> promedioprepa = $info -> promedioprepa;
						$this -> estadoprepa = $info -> estadoprepa;
						$this -> registro_tecnologo = $info -> registro_tecnologo;
						$this -> plantel = $info -> plantel;
						$this -> tipoIngreso = $info -> tipoIngreso;
						$this -> anio1 = $info -> anio1;
						$this -> anio2 = $info -> anio2;
						$this -> municipioEscuela = $info -> municipioEscuela;
						$this -> periodoEgreso = $info -> periodoEgreso;
						$this -> clave = $info -> clave;
					
					}
					$db = DbBase::raw_connect();
					if($this -> carreraNom){
						$carreraN = $XalumnosPersonal -> find_all_by_sql('SELECT nombre FROM carrera WHERE clave ='.$this -> carreraNom);
						foreach( $carreraN as $carreraNombre ){
							$this -> carreraTramite = utf8_encode($carreraNombre -> nombre);
						}
					}
					
					$log =  $db->query("INSERT INTO log_datosalumno (usuario,accion,alumno,fecha_accion) VALUES
					('".Session::get_data('registro')."','Consulta',".$this ->registro.",'".date('Y-m-d H:i:s')."')");
				}
				else{
					$this -> seguro = "NO";
				}
			}
			
			if( $datos == false ){
				Flash::error('El registro no se ha encontrado');
			}
			else{
				$this -> render_partial('informacionAlumnos');
			}
		}
		
		//Funcion que actualiza los datos del alumno seleccionado.
		function actualizaDatos(){
		
			if( $this ->post('Gpersonal') == "1" ){
				$XalumnosPersonal = new XAlumnosPersonal();
				$db = DbBase::raw_connect();
				$this -> set_response("view");
				//Actualizacion de la informacion personal del alumno
				$sql = "UPDATE xalumnos_personal SET curp = '".$this -> post('curp')."', domicilio = '".$this -> post('domicilio')."',
					numero_exterior = '".$this -> post('numExterior')."', numero_interior = '".$this -> post('numInterior')."',
					colonia = '".$this -> post('colonia')."', ciudad = '".$this -> post('ciudad')."', estado = '".$this -> post('estado')."',
					cp = '".$this -> post('cp')."', telefono = '".$this -> post('telCasa')."', celular = '".$this -> post('celular')."',
					telefono_otro = '".$this -> post('telFamiliar')."', estadocivil = '".$this -> post('estadoCivil')."',
					correo = '".$this -> post('correoIntitucional')."', nacionalidad = '".$this -> post('nacionalidad')."',
					fnacimiento = '".$this -> post('fechaNacimiento')."', estadoNac ='".$this -> post('ciudadNacimiento')."',
					paisNacimiento ='".$this -> post('paisNacimientoAl')."', sangre='".$this -> post('tipoSangre')."' WHERE registro = ".$this ->registro;
					
				$result = $db->query($sql);
				//$nombreAlumno = $this -> post('paterno')." ".$this -> post('materno')." ".$this -> post('nombre');
			
				$alumnos = $db->query("UPDATE alumnos SET nacionalidad = '".$this -> post('nacionalidad')."' WHERE miReg = ".$this ->registro);
			
				if( $result == false){
					Flash::error("Los datos personales del registro ".$this ->registro.", no ha sido actualizada.");
				}
			}
			
			if( $this ->post('GTutores') == "1" ){
				//Acualizacion de la informacion de los padres del alumno
				$this -> idInfo = "";
				$infopadres = $XalumnosPersonal -> find_all_by_sql("SELECT id FROM informacion_padres WHERE registro = ".$this -> registro);
				foreach( $infopadres as $info ){
					$this -> idInfo = $info -> id;
				}
				if( $this -> idInfo ){
					$sql2 ="UPDATE informacion_padres SET nombre_pariente='".$this -> post('nombreFamiliar')."',apellido_paterno ='".$this -> post('apellido_paterno')."',
						apellido_materno ='".$this -> post('apellido_materno')."',parentesco='".$this -> post('parentesco')."',
						correo='".$this -> post('correoFamiliar')."',nacionalidad='".$this -> post('nacionalidadFamiliar')."',
						paisNacimiento ='".$this -> post('paisFamiliar')."',grado_estudios='".$this -> post('gradoEstudios')."',
						calle_numero='".$this -> post('calleNumeroFamiliar')."',colonia='".$this -> post('coloniaFamiliar')."',ciudad='".$this -> post('ciudadFamiliar')."',
						estado='".$this -> post('estadoFamiliar')."',telefono_casa='".$this -> post('telefonoFamiliar')."',celular='".$this -> post('celularFamiliar')."',
						ocupacion='".$this -> post('ocupacionFamiliar')."',telefono_trabajo='".$this -> post('telTrabajoFamiliar')."',
						nombre_pariente2='".$this -> post('nombreFamiliar2')."',apellido_paterno2 ='".$this -> post('apellido_paterno2')."',
						apellido_materno2 ='".$this -> post('apellido_materno2')."', parentesco_pariente2='".$this -> post('parentesco2')."',
						correo_pariente2='".$this -> post('correoFamiliar2')."',nacionalidad_pariente2='".$this -> post('nacionalidadFamiliar2')."',
						paisNacimiento_pariente2 ='".$this -> post('paisFamiliar2')."',grado_estudios_pariente2='".$this -> post('gradoEstudiosFamiliar')."',
						calle_numero_pariente2='".$this -> post('calleNumeroFamiliar2')."',
						colonia_pariente2='".$this -> post('coloniaFamiliar2')."',ciudad_pariente2='".$this -> post('ciudadFamiliar2')."',
						estado_pariente2='".$this -> post('estadoFamiliar2')."',telefono_casa_pariente2='".$this -> post('telFamiliar2')."',
						celular_pariente2='".$this -> post('celFamiliar')."',ocupacion_pariente2='".$this -> post('ocupacionFamiliar2')."',
						telefono_trabajo_pariente2='".$this -> post('telTrabajoFamiliar2')."' WHERE registro = ".$this ->registro;
				}else{
					$sql2 = "INSERT INTO informacion_padres(registro,nombre_pariente,apellido_paterno,apellido_materno,parentesco,correo,nacionalidad,grado_estudios,
						 calle_numero,colonia,ciudad,estado,telefono_casa,celular,ocupacion,telefono_trabajo,nombre_pariente2,apellido_paterno2,apellido_materno2,
						 parentesco_pariente2,correo_pariente2,nacionalidad_pariente2,grado_estudios_pariente2,calle_numero_pariente2,colonia_pariente2,
						 ciudad_pariente2,estado_pariente2,telefono_casa_pariente2,celular_pariente2,ocupacion_pariente2,telefono_trabajo_pariente2,
						 paisNacimiento,paisNacimiento_pariente2 ) VALUE 
						 ('".$this ->registro."','".$this -> post('nombreFamiliar')."','".$this -> post('apellido_paterno')."','".$this -> post('apellido_materno')."',
						 '".$this -> post('parentesco')."','".$this -> post('correoFamiliar')."','".$this -> post('nacionalidadFamiliar')."',
						 '".$this -> post('gradoEstudios')."','".$this -> post('calleNumeroFamiliar')."','".$this -> post('coloniaFamiliar')."',
						 '".$this -> post('ciudadFamiliar')."','".$this -> post('estadoFamiliar')."','".$this -> post('telefonoFamiliar')."',
						 '".$this -> post('celularFamiliar')."','".$this -> post('ocupacionFamiliar')."','".$this -> post('telTrabajoFamiliar')."',
						 '".$this -> post('nombreFamiliar2')."','".$this -> post('apellido_paterno2')."','".$this -> post('apellido_materno2')."',
						 '".$this -> post('parentesco2')."','".$this -> post('correoFamiliar2')."','".$this -> post('nacionalidadFamiliar2')."',
						 '".$this -> post('gradoEstudiosFamiliar')."','".$this -> post('calleNumeroFamiliar2')."','".$this -> post('coloniaFamiliar2')."',
						 '".$this -> post('ciudadFamiliar2')."','".$this -> post('estadoFamiliar2')."','".$this -> post('telFamiliar2')."',
						 '".$this -> post('celFamiliar')."','".$this -> post('ocupacionFamiliar2')."','".$this -> post('telTrabajoFamiliar2')."',
						 '".$this -> post('paisFamiliar')."','".$this -> post('paisFamiliar2')."')";
				}
			
				$result2 = $db->query($sql2);
			
				if( $result2 == false ){
					Flash::error("La informaci&oacute;n de los padres del registro ".$this ->registro.", no ha sido actualizada");
				}
			}
			
			if( $this ->post('GTrabajo') == "1" ){
				//Actualizacion de la informacion del trabajo del alumno
				$this -> infoTrabajo = "";
				$infoT = $XalumnosPersonal -> find_all_by_sql('SELECT idInfoEmpresa FROM informacion_trabajo WHERE registro = '.$this ->registro);
				foreach( $infoT as $infotr ){
					$this -> infoTrabajo = $infotr -> idInfoEmpresa;
				}
				if( $this -> infoTrabajo ){
					$sql3 ="UPDATE informacion_trabajo SET nombre_empresa='".$this -> post('empresa')."',domicilio_empresa='".$this -> post('DomicilioEmpresa')."',
						colonia_empresa='".$this -> post('coloniaEmpresa')."',cp_empresa='".$this -> post('cpEmpresa')."',ciudad_empresa='".$this -> post('ciudadEmpresa')."',
						estado_empresa='".$this -> post('estadoEmpresa')."',telenofo_empresa='".$this -> post('telefonoEmpresa')."',
						horario_entrada='".$this -> post('horarioEntrada')."',horario_salida='".$this -> post('horarioSalida')."',puesto='".$this -> post('puesto')."'
						WHERE registro = ".$this ->registro;
				}else{
					$sql3 ="INSERT INTO informacion_trabajo (registro,nombre_empresa,domicilio_empresa,colonia_empresa,cp_empresa,ciudad_empresa,estado_empresa,
						telenofo_empresa,horario_entrada,horario_salida,puesto) VALUE ('".$this -> registro."','".$this -> post('empresa')."',
						'".$this -> post('DomicilioEmpresa')."','".$this -> post('coloniaEmpresa')."','".$this -> post('cpEmpresa')."',
						'".$this -> post('ciudadEmpresa')."','".$this -> post('estadoEmpresa')."','".$this -> post('telefonoEmpresa')."',
						'".$this -> post('horarioEntrada')."','".$this -> post('horarioSalida')."','".$this -> post('puesto')."')";
				}
				$result3 = $db->query($sql3);
			
				if( $result3 == false ){
					Flash::error("La informaci&oacute;n referente al empleo del registro ".$this ->registro.", no ha sido actualizada");
				}
			}
			
			
				//validacion de los campos de especificacion de enfermedades.
				if( $this -> post('enfermedad') == "SI" && $this -> post('especificar_enfermedad') == "" ){
					$this -> post('enfermedad') == "NO";
				}
				if( $this -> post('alergias') == "SI" && $this -> post('especifica_alergias') == "" ){
					$this -> post('alergias') == "NO";
				}
				if( $this -> post('capacidad_diferente') == "SI" && $this -> post('especifica_capacidad') == "" ){
					$this -> post('capacidad_diferente') == "NO";
				}
				if( $this -> post('protesis') == "SI" && $this -> post('especifica_protesis') ){
					$this -> post('protesis') == "NO";
				}
				
			if( $this ->post('GMedico') == "1" ){	
				//Actualizacion de la informacion medica del alumno
				$this -> serv = "";
			
				$servicio = $XalumnosPersonal -> find_all_by_sql("SELECT id FROM servicio_medico WHERE registro =".$this ->registro);
				foreach( $servicio as $servicioM ){
					$this -> serv = $servicioM -> id;
				}
				if( $this -> serv ){
					$sql4 ="UPDATE servicio_medico SET seguridad_social ='".$this -> post('seguro')."',dependencia_seguro ='".$this -> post('tipoSeguro')."',
						parte_de_quien_seguro ='".$this -> post('porParte')."',numero_seguro_social ='".$this -> post('numAfiliacion')."',
						numClinica ='".$this -> post('numeroClinica')."', enfermedad_cronica ='".$this -> post('enfermedad')."',
						especificar_enfermedad = '".$this -> post('especificar_enfermedad')."', alergias ='".$this -> post('alergias')."',
						especifica_alergias ='".$this -> post('especifica_alergias')."', capacidad_diferente ='".$this -> post('capacidad_diferente')."',
						especifica_capacidad ='".$this -> post('especifica_capacidad')."', protesis ='".$this -> post('protesis')."', 
						especifica_protesis ='".$this -> post('especifica_protesis')."',tipoSangre ='".$this -> post('tipoSangre')."', 
						otroSeguro ='".$this -> post('otroSeguro')."' WHERE registro =".$this ->registro;
				}else{
					$sql4 ="INSERT INTO servicio_medico(registro,seguridad_social,dependencia_seguro,parte_de_quien_seguro,numero_seguro_social,numClinica,
						enfermedad_cronica,especificar_enfermedad,alergias,especifica_alergias,capacidad_diferente,especifica_capacidad,protesis,
						especifica_protesis,tipoSangre,otroSeguro) VALUES	('".$this -> registro."','".$this -> post('seguro')."','".$this -> post('tipoSeguro')."',
						'".$this -> post('porParte')."','".$this -> post('numAfiliacion')."','".$this -> post('numeroClinica')."',
						'".$this -> post('enfermedad')."','".$this -> post('especificar_enfermedad')."','".$this -> post('alergias')."',
						'".$this -> post('especifica_alergias')."','".$this -> post('capacidad_diferente')."','".$this -> post('especifica_capacidad')."',
						'".$this -> post('protesis')."','".$this -> post('especifica_protesis')."','".$this -> post('tipoSangre')."','".$this -> post('otroSeguro')."')";
				}
			
				$result4 = $db->query($sql4);
			
				if( $result4 == false ){
					Flash::error("La informaci&oacute;n de serviciomedico del registro ".$this ->registro.", no ha sido actualizada");
				}
			}
			
			if( $this ->post('Gaspirante') == "1" ){
				//Actualizacion de la informacion de aspirantes
				$aspirantes = $XalumnosPersonal -> find_all_by_sql('SELECT id FROM aspirantes WHERE registro = '.$this ->registro);
				$this -> folioAsp = "";
				foreach( $aspirantes as $asp ){
					$this -> folioAsp = $asp -> id;
				}
			
				if( $this -> folioAsp ){
				
					$sql5 ="UPDATE aspirantes SET folio ='".$this -> post('folio')."', ficha ='".$this -> post('ficha')."', periodo ='".$this -> post('periodo')."', 
						folioceneval ='".$this -> post('folioceneval')."', carrera ='".$this -> post('carrera')."', procedencia ='".$this -> post('procedencia')."',
						promedioprepa ='".$this -> post('promedioprepa')."', estadoprepa ='".$this -> post('estadoprepa')."', 
						registro_tecnologo ='".$this -> post('registro_tecnologo')."', plantel ='".$this -> post('plantel')."', tipoIngreso ='".$this -> post('tipoIngreso')."',
						carreraTramite ='".$this -> post('carreraTramite')."', anio1 ='".$this -> post('anio1')."', anio2 ='".$this -> post('anio2')."',
						municipioEscuela ='".$this -> post('municipioEscuela')."', periodoEgreso ='".$this -> post('periodoEgreso')."'
						WHERE registro =".$this ->registro;
				}else{
				
					$sql5 ="INSERT INTO aspirantes (registro,folio,ficha,periodo,folioceneval,carrera,procedencia,promedioprepa,estadoprepa,registro_tecnologo,plantel,
						tipoIngreso,carreraTramite,anio1,anio2,municipioEscuela,periodoEgreso) VALUE ('".$this ->registro."','".$this -> post('folio')."',
						'".$this -> post('ficha')."','".$this -> post('periodo')."','".$this -> post('folioceneval')."','".$this -> post('carrera')."',
						'".$this -> post('procedencia')."','".$this -> post('promedioprepa')."','".$this -> post('estadoprepa')."','".$this -> post('registro_tecnologo')."',
						'".$this -> post('plantel')."','".$this -> post('tipoIngreso')."','".$this -> post('carreraTramite')."','".$this -> post('anio1')."',
						'".$this -> post('anio2')."','".$this -> post('municipioEscuela')."','".$this -> post('periodoEgreso')."')";
				}
				$result5 = $db->query($sql5);
			
				if( $result4 == false ){
					Flash::error("La informaci&oacute;n de aspirantes del registro ".$this ->registro.", no ha sido actualizada");
				}
			}
			
			
			Flash::success("La informaci&oacute;n del registro ".$this ->registro.", se ha actualizado correctamente.");
			
			
			$log =  $db->query("INSERT INTO log_datosalumno (usuario,accion,alumno,fecha_accion) VALUES
					('".Session::get_data('registro')."','Modificacion',".$this ->registro.",'".date('Y-m-d H:i:s')."')");
		}
		
		/*
		* Funcion que valida el permiso para los usuarios
		*/
		function valida(){
			if( Session::get_data('registro') == 8108 || Session::get_data('registro') == 1861 ){
				return true;
			}
			$this -> redirect("/administrador/index");
			return true;
		}
		
	}
?>