function validar_agregar_materia_periodo(){
	var periodo = document.getElementById("periodo");
	var periodoo = "";
	periodoo = periodo.value;
	
	var firstdigit = parseInt(periodoo.substr(0, 1));
	var rest = parseInt(periodoo.substr(1));
	var aux = 0;
	
	if( firstdigit >= 1 && firstdigit <= 4 && rest > 1000 )
		aux = 1;
	
	if( aux == 0 ){
		alert(
			"Periodo Incorrecto, el periodo debe empezar con 1, 2, 3 ó 4 y los siguientes 4 números indican el año\n"+
			"1: Febrero - Junio\n"+
			"2: Julio\n"+
			"3: Agosto Diciembre\n"+
			"4: Enero" );
		periodo.value='32011';
	}
} //function validar_agregar_materia_periodo()


function validar_agregar_materia_promedio(){
	var promedio = document.getElementById("promedio");
	
	if( promedio.value < 70 || promedio.value > 100 ){
		promedio.value='70';
		alert("Solo se aceptan promedios entre 70 y 100");
	}
	
} // function validar_agregar_materia_promedio()


function preguntar_envio(){
	
	var err = "";
	
	var promedio = document.getElementById("promedio");
	if( promedio.value < 70 || promedio.value > 100 ){
		//promedio.value='70';
		err += "Solo se aceptan promedios entre 70 y 100\n";
	}
	
	var periodo = document.getElementById("periodo");
	var periodoo = "";
	periodoo = periodo.value;
	
	var firstdigit = parseInt(periodoo.substr(0, 1));
	var rest = parseInt(periodoo.substr(1));
	var aux = 0;
	
	if( firstdigit >= 1 && firstdigit <= 4 && rest > 1000 )
		aux = 1;
	
	if( aux == 0 ){
		err+=
			"Periodo Incorrecto, el periodo debe empezar con 1, 2, 3 ó 4 y los siguientes 4 números indican el año\n"+
			"1: Febrero - Junio\n"+
			"2: Julio\n"+
			"3: Agosto Diciembre\n"+
			"4: Enero";
		// periodo.value='32011';
	}
	if( err != "" ){
		alert(err);
		return false;
	}
	return true;
} // function preguntar_envio()

function preguntar_eliminar_kardex(){
	if( window.confirm( "¿Estás seguro de que deseas eliminar dicha materia?" ) ){
		return true;
	}
	return false;
} // function preguntar_eliminar_kardex()