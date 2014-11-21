$(document).ready(function()
{
	function cursos_intersemestrales_validar_creacion_curso(){
		var profesor = $('#profesor').val();
		
		if(profesor == -1){
			alert("Favor de seleccionar el profesor que impartirá dicho cursos");
			return false;
		}
		return true;
	} // function cursos_intersemestrales_validar_creacion_curso()
	
	/*
	$('#submitir_inscripcion_alumnos').click(function(){
		var path = $('#path').val();
		
		document.getElementById('mensajes').innerHTML="";
		var cargando = "<img src="+path+"img/spin.gif border='0' /> Cargando...";
		document.getElementById('div_cargando').innerHTML=cargando;
		
		document.getElementById('submitir').disabled=true;
		
		var registro = $('#alumnos').val();
		var tipo_ex = $('#tipo_ex').val();
		var clavecurso = $('#clavecurso').val();
		tmp = {
			registro: registro,
			clavecurso: clavecurso,
			tipo_ex: tipo_ex,
		};
		$.post(path+"capturarcursos/cursos_intersemestrales_inscribir_a_un_alumno", tmp, function(result){
			alert("resultSinSplit: "+result);
			resultSplited = result.split('#');
			//resultSplited[0] -> Indica si fue exitoso o no. Si no fue exitoso será: "NO", Si sí, será un registro
			//resultSplited[1] -> En caso de no ser exitoso esta pocisión será la que diga el mensaje del porque
			//no se inscribió dicho alumno...
			alert("result: "+resultSplited[0]+" "+resultSplited[1]);
			
			// En caso de ser exítoso se llenara un arreglo con la siguiente información
			//resultSplited[1] -> miReg
			//resultSplited[2] -> vcNomAlu
			//resultSplited[3] -> carrera_nombre
			//resultSplited[4] -> enPlan
			//resultSplited[5] -> tipo_ex
			//resultSplited[6] -> creado_at
			//resultSplited[7] -> info_maestro
			if( resultSplited[0] == "SI" ){
				var mensaje = "<p class='letraAzul'>Alumno Inscrito Correctamente</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
				
				var agregarRow = "";
				agregarRow += 
					"<div class='centrar' style='width: 75px; float: left; margin-right: 5px; margin-left: 3px;'>"+resultSplited[1]+"</div>" +
					"<div class='centrar' style='width: 150px; float: left; margin-right: 5px'>"+resultSplited[2].substring(0, 20)+"</div>" +
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[3].substring(0, 12)+"</div>" +
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[4]+"</div>" +
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[5]+"</div>" +
					"<div class='centrar' style='width: 150px; float: left; margin-right: 5px'>"+resultSplited[6]+"</div>" +
					"<div class='centrar' style='width: 225px; float: left; margin-right: 2px'>"+resultSplited[7].substring(0, 25)+"</div>";
				document.getElementById('listado_inscritos').innerHTML+=agregarRow;
			}
			else{
				//letraRoja11px
				var mensaje = "<p class='letraRoja11px'>"+resultSplited[1]+"</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
			}
			document.getElementById('div_cargando').innerHTML="";
			document.getElementById('submitir').disabled=false;
		});
	});
	*/
});