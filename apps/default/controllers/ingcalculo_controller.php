<?php			
	class ingcalculoController extends ApplicationController {
		
		public $antesAnterior=12010;
		public $anterior	= 32010;
		public $actual		= 12011;
		public $proximo		= 32011;
		
		function index(){
			$this -> valida();
			echo "<br /><h2>Bienvenido</h2><br />";
		}
		
		function seleccionar_materias($aux=0){
			$this -> valida_fuera_de_tiempo_coordinador();
			if($aux==1)
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
			$this -> render_partial("seleccionar_materias");
		} // function seleccionar_materias()
		
		function problemas_alumnos(){
			$this -> valida();
			
			unset($this -> periodo);
			
			$Periodos = new Periodos();
			$this -> periodo = $Periodos -> get_periodo_actual_();
			
			unset($this -> alumnosProblemas);
			
			$AlumnosProblemas = new AlumnosProblemas();
			$this -> alumnosProblemas = $AlumnosProblemas -> get_todos_alumnos();
			
			$this -> render_partial("alumnos_con_problemas");
			$this -> render_partial("alumnos_con_problemas_creando");
		} // function problemas_alumnos()
		
		function problemas_alumnos_crearlos(){
			$this -> valida();
			unset($this -> alumnosProblemas);
			
			$AlumnosProblemas = new AlumnosProblemas();
			$Periodos = new Periodos();
			
			$registro = $this -> post("registro");
			$descripcion = $this -> post("descripcion");
			
			$Alumnos = new Alumnos();
			if(!$Alumnos -> find_first("miReg = ".$registro) ){
				$mensaje = "Ese registro no es un alumno";
				Session::set_data('mensaje', $mensaje);
				$this -> redirect("ingcalculo/error");
			}
			$AlumnosProblemas -> registro = $registro;
			$AlumnosProblemas -> periodo = $Periodos -> get_periodo_actual_();
			$AlumnosProblemas -> descripcion = $descripcion;
			$AlumnosProblemas -> aun_con_problemas = 1;
			$AlumnosProblemas -> creado_at = $Periodos -> get_datetime();
			$AlumnosProblemas -> modificado_at = "0000-00-00 00:00:00";
			
			$AlumnosProblemas -> create();
			
			$this -> redirect("ingcalculo/problemas_alumnos");
		} // function problemas_alumnos_crearlos()
		
		function problema_alumno_eliminar(){
			$this -> valida();
			
			$this -> set_response("view");
			
			$id_problema	= $this -> post("id_problema");
			
			if(!isset($id_problema)){
				$mensaje = "Ocurrió un error intenta de nuevo.";
				Session::set_data('mensaje', $mensaje);
				$this -> redirect("ingcalculo/error");
			}
			
			unset($this -> alumno);
			
			$AlumnosProblemas	= new AlumnosProblemas();
			
			$alumnosProblemas = $AlumnosProblemas -> find_first("id = ".$id_problema);
			
			echo "";
			$alumnosProblemas -> delete();
		} // function problema_alumno_eliminar()
		
		function problema_alumno_solucion(){
			$this -> valida();
			
			$this -> set_response("view");
			
			unset($this -> alumno);
			unset($this -> j);
			
			$id_problema	= $this -> post("id_problema");
			$this -> j	= $this -> post("row");
			
			if(!isset($id_problema)){
				$mensaje = "Ocurrió un error intenta de nuevo.";
				Session::set_data('mensaje', $mensaje);
				$this -> redirect("ingcalculo/error");
			}
			
			$AlumnosProblemas	= new AlumnosProblemas();
			$Periodos = new Periodos();
			
			$this -> alumno = $alumnosProblemas = $AlumnosProblemas -> get_info_problema($id_problema);
			
			if( $alumnosProblemas -> aun_con_problemas == 1 ){
				$alumnosProblemas -> activo = '0';
				
				$this -> alumno -> aun_con_problemas = '0';
				$this -> alumno -> modificado_at = $Periodos -> get_datetime();
			}
			else{
				$alumnosProblemas -> activo = 1;
				$this -> alumno -> aun_con_problemas = 1;
				$this -> alumno -> modificado_at = $Periodos -> get_datetime();
			}
			$this -> render_partial("problema_alumno_solucion");
			$alumnosProblemas -> save();
		} // function problema_alumno_solucion()
		
		function correcionKardex(){
			$this -> valida();
			$this -> render_partial("correcionKardex");
		} // function correcionKardex()
		
		function corrigiendoKardex(){
			$this -> valida();
			
			//$this -> set_response("view");
			
			$nombre = $this -> post("nombreAlumno");
			$registro = $this -> post("registroAlumno");
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			
			//$corregirKardexExito = $this -> post("corregirKardexExito");
			//echo $registro." ".$corregirKardexExito;
			
			if( ($nombre == "" && $registro == "") || 
					$nombre == "Nombre Alumno" && $registro == "Registro Alumno" ){ // Ademas checar que el registro se integer...
				$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
			}
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> materias);
			unset($this -> plantel);
			unset($this -> promedio);
			unset($this -> areadeformacion);
			unset($this -> editable);
			
			$KardexIng = new Kardexing();
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			// Si ingreso un registro, usar solo el registro y verificar que sea integer.
			if( $registro != "" && $registro != "Registro Alumno" )
				$query =  "miReg = '".$registro."'";
			else
				$query =  "vcNomAlu like '%".$nombre."%'";
			
			$this -> alumno = $Alumnos -> find_first($query);
			if( !isset($this -> alumno -> miReg) ){
				$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
				//return $this -> route_to("controller: ingcalculo", "action: correcionKardex", "id:1");
			}
			$registro = $this -> alumno -> miReg;
			//$this -> kardex = $KardexIng -> find_first("registro = ".$this -> alumno -> miReg);
			
			$this -> materias = $KardexIng -> get_materias_para_agregar($this -> alumno);
			
			if( $this -> alumno -> enPlantel == "C" || $this -> alumno -> enPlantel == "c" )
				$this -> plantel = "Colomos";
			else
				$this -> plantel = "Tonal&aacute;";
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$this -> render_partial("correcionKardex");
			$this -> render_partial("corrigiendoKardexInfoAlumno");
			$this -> render_partial("corrigiendoKardex");
			
			
			////////////////////////////////////////////
			$Alumnos = new Alumnos();
			$Areadeformacion = new $Areadeformacion();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			// El registro viene de un post de registroAlumno
			//$registro = $this -> post("registroAlumno");
			$registro = $this -> alumno -> miReg;
			
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			
			$this -> kardex = $KardexIng -> get_completeKardex($registro, 1);
			$this -> cuantasMaterias = $KardexIng -> get_count_kardex_from_student($registro);
			
			$this -> ncreditos = $this -> kardex[0][0] -> sumaCreditos;
			$this -> promedio = $this -> kardex[0][0] -> promedioTotal;
			$i = 0;
			while( isset($this -> kardex[$i][0] -> periodosBuenos) ){
				$this -> avancePeriodos[$i] = $this -> kardex[$i][0] -> periodosBuenos;
				$i++;
			}
			
			// Variable que indica si se puede editar el kardex o no
			$this -> editable = 1;
			$this -> render_partial("/kardex/iniciodivmostrandoinfo");
			$this -> render_partial("/kardex/kardexbienvenida");
			$this -> render_partial("/kardex/imprimirKardex");
			$this -> render_partial("/ingcalculo/promedioycreditos");
			$this -> render_partial("/kardex/kardex");
			$this -> render_partial("/kardex/cerrardiv");
		} // function corrigiendoKardex()
		
		function corrigiendoKardexContainer(){
			$this -> valida();
			
			$this -> set_response("view");
			$this -> render_partial("corrigiendoKardex");
		} // function corrigiendoKardexContainer()
		
		function agregarMateriaKardex(){
			$this -> valida();
		
			$this -> set_response("view");
			
			$clave	= $this -> post("clave");
			$periodo	= $this -> post("periodo");
			
			$registro	= $this -> post("registro");
			
			$tipo_de_ex	= $this -> post("tipo_de_ex");
			$promedio	= $this -> post("promedio");
			$fecha_reg = $year = date ("Y")."-".$month = date ("m")."-".$day = date ("d");
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> materias);
			unset($this -> plantel);
			unset($this -> promedio);
			unset($this -> areadeformacion);
			unset($this -> editable);
			unset($this -> exito);
			
			$KardexIng = new KardexIng();
			
			$KardexIng -> id = 'default';
			$KardexIng -> registro = $registro;
			$KardexIng -> clavemat = $clave;
			$KardexIng -> periodo = $periodo;
			$KardexIng -> nivel = '0';
			$KardexIng -> periodo = $periodo;
			$KardexIng -> tipo_de_ex = $tipo_de_ex;
			$KardexIng -> promedio = $promedio;
			$KardexIng -> fecha_reg = $fecha_reg;
			$KardexIng -> activo = 1;
			
			// Aquí se agrega la nueva materia
			if( $KardexIng -> create() )
				$this -> exito = 1;
			
			//$LogKardexIng = new LogKardexIng();
			//$LogKardexIng -> materiaAgregada();
			
			unset($KardexIng);
			
			$KardexIng = new Kardexing();
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			$query =  "miReg = '".$registro."'";
			$this -> alumno = $Alumnos -> find_first($query);
			if( !isset($this -> alumno -> miReg) ){
				$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
			}
			
			//$this -> kardex = $KardexIng -> find_first("registro = ".$this -> alumno -> miReg);
			
			$this -> materias = $KardexIng -> get_materias_para_agregar($this -> alumno);
			
			if( $this -> alumno -> enPlantel == "C" || $this -> alumno -> enPlantel == "c" )
				$this -> plantel = "Colomos";
			else
				$this -> plantel = "Tonal&aacute;";
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$this -> render_partial("corrigiendoKardex");
			
			$this -> render_partial("/ingcalculo/infomateriaagregada");
			
			
			////////////////////////////////////////////
			$KardexIng = new KardexIng();
			// El registro viene de un post de registroAlumno
			//$registro = $this -> post("registroAlumno");
			$registro = $this -> alumno -> miReg;
			
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			
			$this -> kardex = $KardexIng -> get_completeKardex($registro, 1);
			$this -> cuantasMaterias = $KardexIng -> get_count_kardex_from_student($registro);
			
			$this -> promedio = $this -> kardex[0][0] -> promedioTotal;
			$i = 0;
			while( isset($this -> kardex[$i][0] -> periodosBuenos) ){
				$this -> avancePeriodos[$i] = $this -> kardex[$i][0] -> periodosBuenos;
				$i++;
			}
			
			// Variable que indica si se puede editar el kardex o no
			$this -> editable = 1;
			$this -> render_partial("kardex/borrandodivmostrandoinfo");
			$this -> render_partial("/kardex/iniciodivmostrandoinfo");
			$this -> render_partial("/kardex/kardexbienvenida");
			$this -> render_partial("/kardex/imprimirKardex");
			$this -> render_partial("/ingcalculo/promedioycreditos");
			$this -> render_partial("/kardex/kardex");
			$this -> render_partial("/kardex/cerrardiv");
		}
		
		function corregirKardex(){
			$this -> valida();
			
			$this -> set_response("view");
			
			$kardexID = $this -> post("kardexID");
			if(!isset($kardexID))
				$this -> redirect("kardex/errorAlumnoNoEncontrado");
			//$periodo = $this -> post("periodo");
			//$clave = $this -> post("clave");
			//$tipo_de_ex = $this -> post("tipo_de_ex");
			//$promedio = $this -> post("promedio");
			
			$KardexIng	= new KardexIng();
			$Alumnos	= new Alumnos();
			$Materia	= new Materia();
			
			$materiaInfo = $KardexIng -> get_materia_info_kardex($kardexID);
			$alumno = $Alumnos -> get_relevant_info_from_student($materiaInfo -> registro);
			$materia = $Materia -> get_nombre_materia($materiaInfo -> clavemat, $alumno);
			
			unset($this -> materiaCorrecion);
			
			$this -> materiaCorreccion = $materiaInfo;
			$this -> materiaCorreccion -> nombre = $materia -> nombre;
			$this -> materiaCorreccion -> clave = $materia -> clave;
			
			$this -> render_partial("/kardex/corregirKardex");
			
		} // function corregirKardex()
		
		function corregirKardex2(){
			$this -> valida();
			
			$this -> set_response("view");
			
			$kardexID	= $this -> post("kardexID");
			$registro	= $this -> post("registro");
			
			$periodo	= $this -> post("periodo");
			$tipo_de_ex	= $this -> post("tipo_de_ex");
			$promedio	= $this -> post("promedio");
			
			if(!isset($kardexID) || !isset($registro) || !isset($periodo) || !isset($tipo_de_ex) || !isset($promedio))
				$this -> redirect("kardex/errorAlumnoNoEncontrado");
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> materias);
			unset($this -> plantel);
			unset($this -> promedio);
			unset($this -> areadeformacion);
			unset($this -> editable);
			unset($this -> exito);
			
			$Alumnos = new Alumnos();
			$Materia = new Materia();
			$KardexIng	= new KardexIng();
			$Areadeformacion = new Areadeformacion();
			$Carrera = new Carrera();
			
			// Regresa 1, si la materia se corrigió correctamente
			//Aquí es donde se hace el update
			$this -> exito = $KardexIng -> corregir_kardex($kardexID, $periodo, $tipo_de_ex, $promedio);
			unset($KardexIng);
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			if( !isset($this -> alumno -> miReg) ){
				$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
			}
			
			$KardexIng	= new KardexIng();
			$this -> materias = $KardexIng -> get_materias_para_agregar($this -> alumno);
			
			if( $this -> alumno -> enPlantel == "C" || $this -> alumno -> enPlantel == "c" )
				$this -> plantel = "Colomos";
			else
				$this -> plantel = "Tonal&aacute;";
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$this -> render_partial("corrigiendoKardex");
			
			$this -> render_partial("/ingcalculo/infomateriacorreccion");
			
			////////////////////////////////////////////
			$Alumnos = new Alumnos();
			$Areadeformacion = new $Areadeformacion();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			// El registro viene de un post de registroAlumno
			//$registro = $this -> post("registroAlumno");
			$registro = $this -> alumno -> miReg;
			
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			
			$this -> kardex = $KardexIng -> get_completeKardex($registro, 1);
			$this -> cuantasMaterias = $KardexIng -> get_count_kardex_from_student($registro);
			
			$this -> promedio = $this -> kardex[0][0] -> promedioTotal;
			$i = 0;
			while( isset($this -> kardex[$i][0] -> periodosBuenos) ){
				$this -> avancePeriodos[$i] = $this -> kardex[$i][0] -> periodosBuenos;
				$i++;
			}
			// Variable que indica si se puede editar el kardex o no
			$this -> editable = 1;
			$this -> render_partial("kardex/borrandodivmostrandoinfo");
			$this -> render_partial("/kardex/iniciodivmostrandoinfo");
			$this -> render_partial("/kardex/kardexbienvenida");
			$this -> render_partial("/kardex/imprimirKardex");
			$this -> render_partial("/ingcalculo/promedioycreditos");
			$this -> render_partial("/kardex/kardex");
			$this -> render_partial("/kardex/cerrardiv");
			//Router::route_to("controller: ingcalculo", "action: consultar", "registro:".$registro);
		} // function corregirKardex2()
		
		function eliminarKardex(){
			$this -> valida();
			
			$this -> set_response("view");
			
			$kardexID	= $this -> post("delete_kardexID");
			$registro	= $this -> post("registro_delete");
			
			if(!isset($kardexID) || !isset($registro))
				$this -> redirect("kardex/errorAlumnoNoEncontrado");
			
			unset($this -> alumno);
			unset($this -> carrera);
			unset($this -> kardex);
			unset($this -> materias);
			unset($this -> plantel);
			unset($this -> promedio);
			unset($this -> areadeformacion);
			unset($this -> editable);
			unset($this -> exito);
			
			$Alumnos	= new Alumnos();
			$Carrera	= new Carrera();
			$KardexIng	= new KardexIng();
			$Areadeformacion = new AreadeFormacion();
			
			// Aquí es donde se borrar la materia
			$this -> exito = $KardexIng -> borradoFisico($kardexID);
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			if( $this -> exito != 1 || !isset($this -> alumno -> miReg) ){
				$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
			}
			
			$KardexIng	= new KardexIng();
			$this -> materias = $KardexIng -> get_materias_para_agregar($this -> alumno);
			
			if( $this -> alumno -> enPlantel == "C" || $this -> alumno -> enPlantel == "c" )
				$this -> plantel = "Colomos";
			else
				$this -> plantel = "Tonal&aacute;";
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$this -> render_partial("corrigiendoKardex");
			
			$this -> render_partial("/ingcalculo/infomateriaeliminada");
			
			////////////////////////////////////////////
			$Alumnos = new Alumnos();
			$Areadeformacion = new $Areadeformacion();
			$Carrera = new Carrera();
			$KardexIng = new KardexIng();
			// El registro viene de un post de registroAlumno
			//$registro = $this -> post("registroAlumno");
			$registro = $this -> alumno -> miReg;
			
			unset($this -> kardex);
			unset($this -> cuantasMaterias);
			unset($this -> promedio);
			unset($this -> avancePeriodos);
			
			$this -> kardex = $KardexIng -> get_completeKardex($registro, 1);
			$this -> cuantasMaterias = $KardexIng -> get_count_kardex_from_student($registro);
			
			$this -> promedio = $this -> kardex[0][0] -> promedioTotal;
			$i = 0;
			while( isset($this -> kardex[$i][0] -> periodosBuenos) ){
				$this -> avancePeriodos[$i] = $this -> kardex[$i][0] -> periodosBuenos;
				$i++;
			}
			// Variable que indica si se puede editar el kardex o no
			$this -> editable = 1;
			$this -> render_partial("kardex/borrandodivmostrandoinfo");
			$this -> render_partial("/kardex/iniciodivmostrandoinfo");
			$this -> render_partial("/kardex/kardexbienvenida");
			$this -> render_partial("/kardex/imprimirKardex");
			$this -> render_partial("/ingcalculo/promedioycreditos");
			$this -> render_partial("/kardex/kardex");
			$this -> render_partial("/kardex/cerrardiv");
		} // function eliminarKardex()
		
		function activar_desactivarKardex(){
			$this -> valida();
			
			$this -> set_response("view");
			
			$kardexID	= $this -> post("activar_desactivar_kardexID");
			
			if(!isset($kardexID))
				$this -> redirect("kardex/errorAlumnoNoEncontrado");
			
			unset($this -> alumno);
			
			$KardexIng	= new KardexIng();
			
			$kardexIng = $KardexIng -> find_first("id = ".$kardexID);
			
			if( $kardexIng -> activo == 1 ){
				$kardexIng -> activo = '0';
				echo "<image src='".KUMBIA_PATH."/img/inactive_icon.gif' border='0'/>";
			}
			else{
				$kardexIng -> activo = 1;
				echo "<image src='".KUMBIA_PATH."/img/active_icon.gif' border='0'/>";
			}
			
			$kardexIng -> save();
		} // function activar_desactivarKardex()
		
		function incrementaPeriodoKardex($periodo){
			$this -> valida();
			
			if(date("m",time())<7){
				$actual = "1".date("Y",time());
			}
			else{
				$actual = "3".date("Y",time());
			}
			
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
		} // function incrementaPeriodoKardex($periodo)
		
        function condicionado($alumno, $curso){
			$this -> valida();
			//$alumno contiene el registro del alumno
			//$curso contiene la clave del curso del alumno
			
			// Para saber si un alumno está condicionado
			$xccursos	= new Xccursos();
			$xtcursos	= new Xtcursos();
			$xalcursos	= new Xalumnocursos();
			$xtalcursos	= new Xtalumnocursos();
			
			$mmateria	= new Materia();
			$alumnos	= new Alumnos();
			
			$alumno = $alumnos -> find_first( "miReg = ".$alumno );
			
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
				return true;
			else
				return false;
        } // function condicionado($alumno, $curso)
		
        function cambiarContrasena( $vacia ){
			$this -> valida();
			
            $usuarios	= new Usuarios();

            $id = Session::get_data('registro');
			
            // Eliminar las variables que van a pertenecer a este método.
            unset($this -> usuario);
            unset($this -> maestro);
			unset($this -> vacia);
			unset($this -> last);
			unset($this -> date);
			
			$semilla = new Semilla();
			foreach( $usuarios -> find_all_by_sql( "
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave
					from usuarios
					where registro = '".$id."'") as $usuario){
				$this -> usuario = $usuario;
			}
			$this -> vacia = $vacia;
			
			if( $this -> usuario -> passwd_last_change == 0 ){
				$this -> date = "No ha modificado su contrase&ntilde;a recientemente, por motivos de seguridad
				es necesario cambiarla";
				$this -> b = 0;
			}
			else if( Session::get_data('cambiarcontrasena') == 1 ){
				$this -> date = date("F j, Y, g:i a", $usuarios -> passwd_last_change);
				$this -> last = 1;
			}
        } // function cambiarContrasena()

        function cambiandoContrasena(){
			$this -> valida();
			
            $usuarios	= new Usuarios();

            $id = Session::get_data('registro');

            $contrasena = $this -> post( "contrasena" );

            if( !isset($contrasena) || $contrasena == "" )
                $this->redirect('ingcalculo/cambiarContrasena/1');
            // Eliminar las variables que van a pertenecer a este método.
            unset( $this -> usuario );
            unset( $this -> exito );

            $this -> exito = 0;
			$contrasenaAnterior = "";
			$semilla = new Semilla();
            foreach( $usuarios -> find_all_by_sql("
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave,
					AES_DECRYPT(passwd_old,'".$semilla -> getSemilla()."') passwd_old
					from usuarios
					where registro = '".$id."'") as $usuario){
				if( $contrasena == $usuario -> passwd_old || $contrasena == $usuario -> clave ){
					$this->redirect('ingcalculo/cambiarContrasena/2'); return;
				}
				if( strlen($contrasena) < 6 || strlen($contrasena) > 15	){
					$this->redirect('ingcalculo/cambiarContrasena/3'); return;
				}
				$contrasenaAnterior = $usuario -> clave;
			}
			$day = date ("d"); $month = date ("m"); $year = date ("Y");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$semilla = new Semilla();
            foreach( $usuarios -> find_all_by_sql( "
					update usuarios
					set clave = AES_ENCRYPT('".$contrasena."','".$semilla -> getSemilla()."'),
					passwd_old = '".$contrasenaAnterior."',
					passwd_last_change = ".mktime( $hour, $minute, $second, $month, $day, $year )."
					where registro = '".$id."'" ) as $usuario ){
				Session::unset_data('cambiarcontrasena');
				$this -> exito = 1;
            }
			foreach( $usuarios -> find_all_by_sql( "
					Select registro, AES_DECRYPT(clave,'".$semilla -> getSemilla()."') clave,
					AES_DECRYPT(passwd_old,'".$semilla -> getSemilla()."') passwd_old, registro
					from usuarios
					where registro = '".$id."'") as $usuario){
				$this -> usuario = $usuario;
			}

        } // function cambiandoContrasena()
		
		function correcionCalificaciones(){
			$this -> valida();
		} // function correcionCalificaciones()
		
		function realizandoBusqueda($tipoBusqueda, $plantel){
			$this -> valida();
			
			$this -> set_response("view");
			
			unset($this -> tipoBusquedaa);
			unset($this -> plantell);
			unset($this -> tipoBusqueda);
			
			switch($tipoBusqueda){
				case 1:
					$this -> tipoBusquedaa = "Clave de Curso";
						break;
				case 2:
					$this -> tipoBusquedaa = "N&oacute;mina de Profesor";
						break;
				case 3:
					$this -> tipoBusquedaa = "Nombre de Profesor";
						break;
				case 4:
					$this -> tipoBusquedaa = "Clave de Materia";
						break;
				case 5:
					$this -> tipoBusquedaa = "Nombre de Materia";
						break;
			}
			$this -> tipoBusqueda = $tipoBusqueda;
			$this -> plantell = $plantel;
			$this -> render_partial("realizandoBusqueda");
		} // function realizandoBusqueda()
		
		function mostrandoInfoCursos(){
			$this -> valida();
			
			$valorParaBuscar = $this -> post("valorParaBuscar");
			$tipoBusquedaa = $this -> post("tipoBusquedaa");
			$plantel = $this -> post("plantell");
			if( !isset($valorParaBuscar) || !isset($tipoBusquedaa) ){
				$this -> redirect("general/inicio");
				return;
			}
			$this -> set_response("view");
			
			unset($this -> mainInfo);
			unset($this -> activarParcial);
			unset($this -> plantel);
			// Def valores para tipoBusqueda
			// 1. Clave de Curso.
			// 2. Nómina de Profesor.
			// 3. Nombre de Profesor.
			// 4. Clave de Materia.
			// 5. Nombre de Materia.
			switch($tipoBusquedaa){
				case 1:
					$mainQuery = "select xcc.clavecurso, mat.clave, mat.nombre matnombre, maes.nomina, maes.nombre
								from x".$plantel."cursos xcc
								inner join materia mat on xcc.materia = mat.clave
								inner join maestros maes on xcc.nomina = maes.nomina
								where xcc.clavecurso = '".$valorParaBuscar."'
								and periodo = ".$this -> actual."
								group by xcc.clavecurso";
						break;
				case 2:
					$mainQuery = "select xcc.clavecurso, mat.clave, mat.nombre matnombre, maes.nomina, maes.nombre
								from maestros maes
								inner join x".$plantel."cursos xcc on maes.nomina = xcc.nomina
								inner join materia mat on mat.clave = xcc.materia
								where maes.nomina = ".$valorParaBuscar."
								and periodo = ".$this -> actual."
								group by xcc.clavecurso";
						break;
				case 3:
					$mainQuery = "select xcc.clavecurso, mat.clave, mat.nombre matnombre, maes.nomina, maes.nombre
								from maestros maes
								inner join x".$plantel."cursos xcc on maes.nomina = xcc.nomina
								inner join materia mat on mat.clave = xcc.materia
								where maes.nombre like '%".$valorParaBuscar."%'
								and periodo = ".$this -> actual."
								group by xcc.clavecurso";
						break;
				case 4:
					$mainQuery = "select xcc.clavecurso, mat.clave, mat.nombre matnombre, maes.nomina, maes.nombre
								from materia mat
								inner join x".$plantel."cursos xcc on mat.clave = xcc.materia
								inner join maestros maes on maes.nomina = xcc.nomina
								where mat.clave = '".$valorParaBuscar."'
								and periodo = ".$this -> actual."
								group by xcc.clavecurso";
						break;
				case 5:
					$mainQuery = "select xcc.clavecurso, mat.clave, mat.nombre matnombre, maes.nomina, maes.nombre
								from materia mat
								inner join xccursos x".$plantel."c on mat.clave = xcc.materia
								inner join maestros maes on maes.nomina = xcc.nomina
								where mat.nombre like '%".$valorParaBuscar."%'
								and periodo = ".$this -> actual."
								group by xcc.clavecurso";
						break;
			} // switch($tipoBusqueda)
			
			$year = date ("Y"); $month = date ("m"); $day = date ("d");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$fechaActual = mktime( $hour, $minute, $second, $month, $day, $year );
			$i = 0;
			$xccursos			= new Xccursos();
			$xperiodoscaptura	= new Xperiodoscaptura();
			$xextraordinarios	= new Xextraordinarios();
			
			foreach( $xccursos -> find_all_by_sql($mainQuery) as $xcc ){
				$this -> mainInfo[$i] = $xcc;
				$i++;
			}
			$periodosDisp = $xperiodoscaptura -> find("
					plantel = '".$plantel."'
					and periodo = ".$this -> actual." 
					order by id");
			$j = 0;
			foreach($periodosDisp as $perDisp){
				$j++;
				if( $fechaActual >= $perDisp -> fin )
					$this -> activarParcial[$j] = true;
				else
					$this -> activarParcial[$j] = false;
			}
			$this -> plantel= $plantel;
			$this -> render_partial("mostrandoInfoCursos");
		} // mostrandoInfoCursos
		
        //function capturando($curso, $parcial){
		function corrigiendoCalificaciones($parcial, $clavecurso, $plantel){
			$this -> valida();
			
			// Validar que si sea tiempo de modificar dicho parcial
			$xperiodoscaptura	= new Xperiodoscaptura();
			$year = date ("Y"); $month = date ("m"); $day = date ("d");
			$hour = date ("H"); $minute = date ("i"); $second = date ("s");
			$fechaActual = mktime( $hour, $minute, $second, $month, $day, $year );
			if( !$periodosDisp = $xperiodoscaptura -> find("
					plantel = '".$plantel."'
					and periodo = ".$this -> actual."
					and parcial = ".$parcial."
					and fin < ".$fechaActual."
					order by id") ){
				$mensaje = "Está intentando modificar calificaciones de un parcial
				que aún no le corresponde. Favor de contactar al administrador del sistema
				para futuras aclaraciones.";
				Session::set_data('mensaje', $mensaje);
				$this -> redirect("ingcalculo/error");
				return;
			}
			// FIN DE Validar que si sea tiempo de modificar dicho parcial -----
			
			//ELIMINAR CONTENIDO DE LAS VARIABLES QUE PERTENECERÁN A LA CLASE
			unset($this -> objetoCurso);
			unset($this -> objetoProfesor);
			unset($this -> nombreProfesor);
			unset($this -> objetoMateria);
			unset($this -> curso);
			unset($this -> materia);
			unset($this -> clave);
			unset($this -> registro);
			unset($this -> alumnado);
			unset($this -> parcialito);
			unset($this -> nombre);
			
			$id = Session::get_data('registro');
			
			if( strtoupper($plantel) == "C" )
				$xccursos = new Xccursos();
			else
				$xccursos = new Xtcursos();
			$this -> objetoCurso = $xccursos -> find_first("clavecurso = '".$clavecurso."'");
			
			$maestros = new Maestros();
			$this -> objetoProfesor = $maestros -> find_first("nomina=".$this -> objetoCurso -> nomina."");

			$periodo = $this -> actual;
			
			$pertenece = $xccursos -> count("clavecurso='".$clavecurso."' AND nomina=".$this -> objetoProfesor -> nomina." AND periodo='".$periodo."'");
			if($pertenece<1){
				$log = new Xlogcalificacion();
				$log -> clavecurso = $clavecurso;
				$log -> nomina = $id;
				$log -> accion = "INTENTANDO MODIFICAR UNA CALIFICACION QUE NO LE CORRESPONDE";
				$log -> ip = $this -> getIP();
				$log -> fecha = time();
				$log -> save();
				
				$this->redirect('ingcalculo/correcionCalificaciones');
				return;
			}
			
			$materias = new Materia();
			$this -> objetoMateria = $materias -> find_first("clave = '".$this -> objetoCurso -> materia."'");
			if( strtoupper($plantel) == "C" )
				$xalumnocursos = new Xalumnocursos();
			else
				$xalumnocursos = new Xtalumnocursos();
			
			$alumnos = new Alumnos();
			$especialidades = new Especialidades();

			$avance = $xccursos -> find_first("clavecurso='".$clavecurso."'");

			switch($parcial){
				case 1: $this -> horas = $avance -> horas1; break;
				case 2: $this -> horas = $avance -> horas2; break;
				case 3: $this -> horas = $avance -> horas3; break;
			}

			$this -> horas1 = $avance -> horas1;
			$this -> horas2 = $avance -> horas2;
			$this -> horas3 = $avance -> horas3;

			if($this -> horas==0){
				$this -> horas = "";
			}

			if($this -> horas1==0){
				$this -> horas1 = "-";
			}

			if($this -> horas2==0){
				$this -> horas2 = "-";
			}

			if($this -> horas3==0){
				$this -> horas3 = "-";
			}

			switch($parcial){
				case 1: $this -> avance = $avance -> avance1; break;
				case 2: $this -> avance = $avance -> avance2; break;
				case 3: $this -> avance = $avance -> avance3; break;
			}

			$this -> avance1 = $avance -> avance1;
			$this -> avance2 = $avance -> avance2;
			$this -> avance3 = $avance -> avance3;

			if($this -> avance==0){
				$this -> avance = "";
			}

			if($this -> avance1==0){
				$this -> avance1 = "-";
			}
			else{
				$this -> avance1 .= "%";
			}

			if($this -> avance2==0){
				$this -> avance2 = "-";
			}
			else{
				$this -> avance2 .= "%";
			}

			if($this -> avance3==0){
				$this -> avance3 = "-";
			}
			else{
				$this -> avance3 .= "%";
			}
			$total = 0;
			
			$this -> curso = $clavecurso;
			$this -> materia = $objetoMateria -> nombre;
			$this -> clave = $objetoCurso -> materia;
			foreach($xalumnocursos -> find("curso='".$clavecurso."' ORDER BY registro") as $alumno){
				$this -> registro = $alumno -> registro;
				$this -> alumnado[$total]["id"] = $alumno -> id;

				//$parcial = $this -> post("parcial");
				$this -> parcialito = $parcial;

				switch($parcial){
					case 1: $this -> parcial = "PRIMER PARCIAL"; break;
					case 2: $this -> parcial = "SEGUNDO PARCIAL"; break;
					case 3: $this -> parcial = "TERCER PARCIAL"; break;
				}

				foreach($alumnos -> find("miReg=".$alumno->registro) as $a){
					$this -> nombre = iconv("latin1", "ISO-8859-1", $a -> vcNomAlu);
					$situacion = $a -> enTipo;
					$carrera_id = $a -> carrera_id;
					$areadeformacion_id = $a -> areadeformacion_id;
					break;
				}

				switch($situacion){
					case 'R': $this -> situacion = "REGULAR"; break;
					case 'I': $this -> situacion = "IRREGULAR"; break;
					case 'P': $this -> situacion = "PROCESO DE REGULARIZACION"; break;
					case 'C': $this -> situacion = "CONDICIONADO"; break;
				}
				
				$carrera = new Carrera();
				foreach($carrera -> find("id =".$carrera_id) as $c){
					$this -> carrera = $c -> nombre;
				}
				$areadeformacion = new Areadeformacion();
				foreach($areadeformacion -> find("idareadeformacion = ".$areadeformacion_id." and carrera_id = ".$carrera_id) as $area){
					$this -> nombreareaformacion = $area -> nombreareaformacion;
				}

				$this -> alumnado[$total]["registro"] = $this -> registro;
				$this -> alumnado[$total]["nombre"] = $this -> nombre;
				$this -> alumnado[$total]["carrerayarea"] = $this -> carrera." - ".$this -> nombreareaformacion;
				$this -> alumnado[$total]["situacion"] = $this -> situacion;

				switch($parcial){
					case 1: $this -> alumnado[$total]["faltas"] = $alumno -> faltas1;break;
					case 2: $this -> alumnado[$total]["faltas"] = $alumno -> faltas2;break;
					case 3: $this -> alumnado[$total]["faltas"] = $alumno -> faltas3;break;
				}

				$this -> alumnado[$total]["faltas1"] = $alumno -> faltas1;
				$this -> alumnado[$total]["faltas2"] = $alumno -> faltas2;
				$this -> alumnado[$total]["faltas3"] = $alumno -> faltas3;

				switch($parcial){
					case 1: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion1;break;
					case 2: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion2;break;
					case 3: $this -> alumnado[$total]["calificacion"] = $alumno -> calificacion3;break;
				}

				$this -> alumnado[$total]["calificacion1"] = $alumno -> calificacion1;
				$this -> alumnado[$total]["calificacion2"] = $alumno -> calificacion2;
				$this -> alumnado[$total]["calificacion3"] = $alumno -> calificacion3;

				if($this -> alumnado[$total]["calificacion"]==300){
						$this -> alumnado[$total]["calificacion"]="";
						$this -> alumnado[$total]["faltas"]="";
				}

				if($this -> alumnado[$total]["calificacion1"]==300){
						$this -> alumnado[$total]["calificacion1"]="-";
						$this -> alumnado[$total]["faltas1"]="-";
				}

				if($this -> alumnado[$total]["calificacion2"]==300){
						$this -> alumnado[$total]["calificacion2"]="-";
						$this -> alumnado[$total]["faltas2"]="-";
				}

				if($this -> alumnado[$total]["calificacion3"]==300){
						$this -> alumnado[$total]["calificacion3"]="-";
						$this -> alumnado[$total]["faltas3"]="-";
				}

				if($this -> alumnado[$total]["calificacion"]==999){
						$this -> alumnado[$total]["calificacion"]="NP";
				}

				if($this -> alumnado[$total]["calificacion1"]==999){
						$this -> alumnado[$total]["calificacion1"]="NP";
				}

				if($this -> alumnado[$total]["calificacion2"]==999){
						$this -> alumnado[$total]["calificacion2"]="NP";
				}

				if($this -> alumnado[$total]["calificacion3"]==999){
						$this -> alumnado[$total]["calificacion3"]="NP";
				}

				if($this -> alumnado[$total]["calificacion"]==500){
						$this -> alumnado[$total]["calificacion"]="PD";
				}

				if($this -> alumnado[$total]["calificacion1"]==500){
						$this -> alumnado[$total]["calificacion1"]="PD";
				}

				if($this -> alumnado[$total]["calificacion2"]==500){
						$this -> alumnado[$total]["calificacion2"]="PD";
				}

				if($this -> alumnado[$total]["calificacion3"]==500){
						$this -> alumnado[$total]["calificacion3"]="PD";
				}
				
				$total++;
			} // foreach($xalumnocursos -> find("curso='".$clavecurso."' ORDER BY registro") as $alumno)
		} // function corrigiendoCalificaciones($parcial, $clavecurso)
		
        function corregirCalificaciones(){
			$this -> valida();
			
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
			
			$curso = $this -> post("curso");
			$xcursos = new Xccursos();
			$xcurso = $xcursos -> find_first("clavecurso='".$curso."'");
			
			$calificaciones = new Xalumnocursos();
			$flag = 3;
			foreach($calificaciones -> find("curso='".$curso."' ORDER BY id") as $alumno){
				if( (strtoupper($this -> post("calificacion".$alumno -> id)) == "NP") || 
					(is_numeric($this -> post("calificacion".$alumno -> id))) ){
					// Asignamos 1 a flag para informar que puede seguir capturando.
					$flag = 1;
				}
				if( !is_numeric($this -> post("calificacion".$alumno -> id)) &&
					(strtoupper($this -> post("calificacion".$alumno -> id)) != "NP") ){
					// Asignamos 3 a flag para saber que NO puede seguir capturando.
					$flag = 3;
				}if(!is_numeric($this -> post("faltas".$alumno -> id)) ){
					// Asignamos 4 a flag para saber que NO puede seguir capturando.
					$flag = 4;
				}if( $this -> post("faltas".$alumno -> id) > $this -> post("horas") ){
					// Asignamos 2 a flag para saber que NO puede seguir capturando.
					$flag = 2;
				}if( $flag == 3 || $flag == 2 || $flag == 4 ){
					Session::set_data('flag', $flag);
					$this->redirect('ingcalculo/correcionCalificaciones');
					return;
				}
			}
			
			switch($this -> post("parcial")){
				case 1: $xcurso -> horas1 = $this -> post("horas"); break;
				case 2: $xcurso -> horas2 = $this -> post("horas"); break;
				case 3: $xcurso -> horas3 = $this -> post("horas"); break;
			}
			
			switch($this -> post("parcial")){
				case 1: $xcurso -> avance1 = $this -> post("avance"); break;
				case 2: $xcurso -> avance2 = $this -> post("avance"); break;
				case 3: $xcurso -> avance3 = $this -> post("avance"); break;
			}
			
			$xcurso -> paralelo = -1;
			
			$xcurso -> save();
			$conpermiso = new Xpermisoscaptura();
			$permiso = $conpermiso -> find_first("curso_id='".$xcurso->id."'");
			
			$xpermesp		= 	new Xpermisoscapturaesp();
			$idcapturaesp	= 	$this -> post("idcapturaesp");
			
			switch($this -> post("parcial")){
				case 1: $permiso -> ncapturas1 ++; break;
				case 2: $permiso -> ncapturas2 ++; break;
				case 3: $permiso -> ncapturas3 ++; break;
			}
			
			$permiso -> save();
			
			$log = new Xlogcalificacion();
			$log -> clavecurso = $curso;
			$log -> nomina = 999999; // Seis 9 = ingcalculo
			$log -> accion = "ACTUALIZAR CALIFICACIONES PARCIAL ".$this -> post("parcial");
			$log -> ip = $this -> getIP();
			$log -> fecha = time();
			$log -> save();
			
			$aux1 = 0;
			foreach($calificaciones -> find("curso='".$curso."' ORDER BY id") as $alumno){
				
				$calificacion = $calificaciones -> find_first("id=".$alumno -> id);
				
				$tmp1 = "calificacion".$alumno -> id;
				$tmp2 = "faltas".$alumno -> id;
				
				switch($this -> post("parcial")){
					case 1:
						$calificacion -> calificacion1 = $this -> post($tmp1);
						$calificacion -> faltas1 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion1 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion1 = 500;
						//}
							break;
					case 2:
						$calificacion -> calificacion2 = $this -> post($tmp1);
						$calificacion -> faltas2 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion2 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion2 = 500;
						//}
							break;
					case 3:
						$calificacion -> calificacion3 = $this -> post($tmp1);
						$calificacion -> faltas3 = $this -> post($tmp2);
						if(strtoupper($this -> post($tmp1)) == "NP"){
							$calificacion -> calificacion3 = 999;
						}
						//if(strtoupper($this -> post($tmp1)) == "PD"){
							//$calificacion -> calificacion3 = 500;
						//}
							break;
				}
				
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
				if( $this -> condicionado($calificacion -> registro, $calificacion -> curso)
					&& $calificacion -> calificacion < 70 ){
					$calificacion -> situacion = "BAJA DEFINITIVA";
				}
				
				if( $calificacion -> calificacion == null || $calificacion -> calificacion == '0' )
					$calificacion -> calificacion = '0';
				if( $calificacion -> faltas == null )
					$calificacion -> faltas = '0';
				
				$calificacion -> save();
				
				// Aquí se captura el tiempo en que se hizo la captura especial
				//si es que se había autorizado la captura en destiempo.
				if( $aux1 == 0 && ($idcapturaesp != "" || $idcapturaesp != null) ){
					$day = date ("d");
					$month = date ("m");
					$year = date ("Y");
					$hour = date ("H");
					$minute = date ("i");
					$second = date ("s");
					
					$fechacaptura = mktime( $hour, $minute, $second, $month, $day, $year );
					$xpermesp -> id = $idcapturaesp;
					$xpermesp -> captura = 1;
					$xpermesp -> fecha_captura = $fechacaptura;
					$xpermesp -> update();
					$aux = 1;
				}
			}
			
			$this->redirect('ingcalculo/correcionCalificaciones');
        } // function capturar($curso)
		
        function abrirSeleccionAlumno(){
			$this -> valida();
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$date1 = date ("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

			$nomina = Session::get_data('registro');
			$division = Session::get_data('coordinacion');

			unset( $this -> noCondicionados );
			unset( $this -> condicionados );
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			
        } // function abrirSeleccionAlumno()


        function abrirSeleccionAlumno2( $registroo, $tiempo ){
			$this -> valida();
			// $xccursos		= new Xccursos();
			$seleccionTiempo		= new SeleccionTiempo();

			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			$date1 = mktime($hour, $minute, $second, $month, $day, $year);
			
			$Periodos = new Periodos();
			$periodo = $Periodos -> get_periodo_actual();
			$this -> set_response("view");
			
			if( $tiempo >= 10 ){
				$date1 +=($tiempo * 60);
			}else{
				$date1 += ($tiempo * 3600);
			}
			foreach( $seleccionTiempo -> find
					( "registro = ".$registroo."
							and periodo = ".$periodo) as $selTiempo ){
				//$selTiempo -> inicio = $year."-".$month."-".($day)." ".$hour.":".($minute -2).":33";
				$selTiempo -> inicio = $year."-".$month."-".($day)." ".$hour.":".$minute.":33";
				//$selTiempo -> fin = $year."-".$month."-".$day." ".$hour.":".$minute.":33";
				$selTiempo -> fin = date("Y-m-d H-i-s", $date1);
				// $year."-".$month."-".$day." ".($hour-2).":".($minute + 17).":33"
				$selTiempo -> save();
				// 2008-08-11 09:00:00
			}
			if( !$selTiempo = $seleccionTiempo -> find_first
					( "registro = ".$registroo."
							and periodo = ".$periodo) ){
				$seleccionTiempo -> registro = $registroo;
				$seleccionTiempo -> promedio = '0';
				$seleccionTiempo -> inicio = $year."-".$month."-".($day)." ".$hour.":".$minute.":33";
				/*
				if( ($minute + 30) > 60 ){
					$minute = ( ($minute + 30) ) - 60;
					$hour = $hour + 1;
				}else{
					$hour += $tiempo;
				}
				if( ($hour + $tiempo) > 24 ){
					$hour = ( $hour + $tiempo ) - 24;
					$day = $day + 1;
				}else{
					$hour += $tiempo;
				}
				*/
				//$seleccionTiempo -> fin = $year."-".$month."-".$day." ".$hour.":".$minute.":33";
				$selTiempo -> fin = date("Y-m-d H-i-s", $date1);
				$seleccionTiempo -> periodo = $periodo;
				$seleccionTiempo -> create();
			}

			echo "$registroo Apertura exitosa<br />";

			echo "Hora actual: ".
					$year."-".$month."-".$day." ".$hour.":".$minute.":".$second."<br />";

			echo "El sistema se cerrar&aacute; a las: ".date("Y-m-d H-i-s", $date1)."<br />";
			
			$this -> render_partial("abrirSeleccionAlumno2");
        } // function abrirSeleccionAlumno2()
		
		function condicionados(){
			$this -> render("condicionados_view");
		} // function condicionados()
		
		function buscar_alumno_condicionados(){
			
			$this -> set_response("view");
			
			$registro = $this -> post("registroAlumno");
			$nombre = $this -> post("nombreAlumno");
			
			unset($this->reconsideracionBaja);
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			$Alumnos = new Alumnos();
			$Carrera = new Carrera();
			$Areadeformacion = new Areadeformacion();
			
			if( ($nombre == "" && $registro == "") || 
					$nombre == "Nombre Alumno" && $registro == "Registro Alumno" ){ // Ademas checar que el registro se integer...
				//$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
			}
			// Si ingreso un registro, usar solo el registro y verificar que sea integer.
			if( $registro != "" && $registro != "Registro Alumno" )
				$query =  "miReg = '".$registro."'";
			else
				$query =  "vcNomAlu like '%".$nombre."%'";
			
			$this -> alumno = $Alumnos -> find_first($query);
			if( !isset($this -> alumno -> miReg) ){
				//$this -> render_partial("correcionKardex");
				$this -> render_partial("kardex/errorAlumnoNoEncontrado");
				return;
				//return $this -> route_to("controller: ingcalculo", "action: correcionKardex", "id:1");
			}
			$registro = $this -> alumno -> miReg;
			
			$this -> alumno = $Alumnos -> get_relevant_info_from_student($registro);
			$this -> carrera = $Carrera -> get_nombre_carrera($this -> alumno -> carrera_id);
			$this -> areadeformacion = $Areadeformacion -> get_nombre_areadeformacion
					($this -> alumno -> carrera_id, $this -> alumno -> areadeformacion_id);
			
			$ReconsideracionBaja = new ReconsideracionBaja();
			$this->reconsideracionBaja = $ReconsideracionBaja->find_first("registro = '".$registro."' and periodo = '".$periodo."'");
			
			if( isset($this->reconsideracionBaja->periodo)){ // Si ya tiene dada de alta mostrar menú donde podrá cambiar el status, o meter más materias.
				$this->render_partial("status_carta_modify");
				return;
			}
			else{ // Dar de alta una nueva materia.
				$this->render_partial("status_carta");
				return;
			}
		}
		
		function status_carta(){
			$this -> set_response("view");
		
			$registro = $this->post("registro");
			$procede = $this->post("procede");
			
			$Periodos = new Periodos();
			$periodo = $Periodos->get_periodo_actual();
			
			$ReconsideracionBaja = new ReconsideracionBaja();
			$reconsideracionBaja = $ReconsideracionBaja->find_first("registro = '".$registro."' and periodo = '".$periodo."'");
			
			if(isset($reconsideracionBaja->periodo)){
				$reconsideracionBaja->procede = $procede;
				$reconsideracionBaja->update();
			}
			else{
				$ReconsideracionBaja->registro = $registro;
				$ReconsideracionBaja->procede = $procede;
				$ReconsideracionBaja->periodo = $periodo;
				$ReconsideracionBaja->horarioautorizado = '0';
				$ReconsideracionBaja->quienautorizo = '0';
				
				$ReconsideracionBaja->save();
			}
			
			if($procede==1){
				$this->render_partial("condicionados_materias");
				return;
			}
			
		} // function status_carta()
		
		function render_condicionados_materias(){
			$this -> set_response("view");
			$this->render_partial("condicionados_materias");
		}
		
		function promocion_alumno(){
			$this->render("promocion_alumno_view");
		} // promocion_alumno
		
		function promocion_alumno_plan_creditos(){
			$this -> set_response("view");
			$this->render_partial("promocion_alumno_plan_creditos");
		} // function promocion_alumno_plan_creditos()
		
		function promocion_alumno_plan_rigido(){
			$this -> set_response("view");
			$this->render_partial("promocion_alumno_plan_rigido");
		} // function promocion_alumno_plan_rigido()
		
		function valida(){
			if(Session::get_data('tipousuario')!="INGCALCULO"){
				$this->redirect('/');
			}
		} // function valida()
		
		function valida_fuera_de_tiempo_coordinador(){
			if(Session::get_data('coordinador')=="OK"){
				$this->redirect('profesores');
			}
		} // function valida_fuera_de_tiempo_coordinador()	
		
		function error(){
			$this -> valida();
			$this -> error = Session::get_data('mensaje');;
			Session::unset_data('mensaje');
		} // function error($mensaje)
		
        function getIP(){
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
        } // function getIP()
	}
?>