function cursos_intersemestrales_validar_creacion_curso(){
	var profesor = document.getElementById("profesor").value;
	var materias = document.getElementById("materias").value;
	
	if(profesor == '-1'){
		alert("Favor de seleccionar el profesor que impartirá dicho cursos");
		return false;
	}
	return true;
} // function cursos_intersemestrales_validar_creacion_curso()

function cursos_intersemestrales_validar_inscripcion_alumnos(){
	var alumnos = document.getElementById("alumnos").value;
	var tipo_ex = document.getElementById("tipo_ex").value;
	
	var err = "";
	
	if(alumnos == ""){
		err += "Favor de ingresar el registro del alumno al que desea inscribir en dicho curso\n";
	}
	if(tipo_ex == '-1'){
		err += "Favor de seleccionar el tipo de curso al que desea inscribir al alumno\n";
	}
	
	if( err != "" ){
		alert(err);
		return false;
	}
	return true;
} // function cursos_intersemestrales_validar_inscripcion_alumnos()