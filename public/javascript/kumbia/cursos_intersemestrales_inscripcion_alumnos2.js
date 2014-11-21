	function delete_alumno(inter_al_id, row){
		tmp = {
			inter_al_id: inter_al_id
		};
		$.post("/ingenieria/capturarcursos/cursos_intersemestrales_desinscribir_alumno", tmp, function(){
			document.getElementById('row'+row).innerHTML="";
			var mensaje = "<p class='letraAzul'>Alumno Desinscrito Correctamente</p>";
			document.getElementById('mensajes').innerHTML=mensaje;
		});
	} // function delete_alumno(inter_al_id, row)
	
	function cursos_intersemestrales_validar_eliminar_curso(){
		if( window.confirm( "¿Estás seguro de que deseas eliminar dicho curso?" ) ){
			return true;
		}
		return false;
	} // function cursos_intersemestrales_validar_eliminar_curso()
	
	/*
	function edit_alumno(inter_al_id, row){
		var path = $('#path').val();
		
		var registro = $('#registro'+row).val();
		var nombre = $('#nombre'+row).val();
		var tipo_ex = $('#tipo_ex'+row).val();
		
		alert("edit");
		editando 
		
		tmp = {
			inter_al_id: inter_al_id,
			registro: registro,
			nombre: nombre,
			tipo_ex: tipo_ex
		};
		$.post("/ingenieria/capturarcursos/cursos_intersemestrales_editando_alumno", tmp, function(result){
			resultSplited = result.split('#');
			
			if( resultSplited[0] == 1 ){
			var renglon =
					"<div class='centrar' style='float: left;' id='row"+row+"' id='row"+row+"'>"+
					"<div class='centrar' style='width: 75px; float: left; margin-right: 5px; margin-left: 3px;'>"+registro+"</div>"+
					"<div class='centrar' style='width: 150px; float: left; margin-right: 5px'>"+nombre+"</div>"+
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+tipo_ex+"</div>"+
					"<div style='clear:both;'></div>";
				document.getElementById('row'+row).innerHTML=renglon;
				var mensaje = "<p class='letraAzul'>Alumno Editado Correctamente.</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
			}
			else{
				var mensaje = "<p class='letraRoja11px'>Ocurri&oacute; un error editando el alumno.</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
			}
			
		});
	} // function edit_alumno(inter_al_id, row)
	
	function edit_alumno(inter_al_id, row){
		var path = $('#path').val();
		alert("edit");
		tmp = {
			inter_al_id: inter_al_id
		};
		$.post("/ingenieria/capturarcursos/cursos_intersemestrales_editando_alumno", tmp, function(result){
			resultSplited = result.split('#');
			
			if( resultSplited[0] == 1 ){
			var renglon =
					"<div class='centrar' style='float: left;' id='row"+row+"' id='row"+row+"'>"+
					"<div class='centrar' style='width: 75px; float: left; margin-right: 5px; margin-left: 3px;'>"+resultSplited[1]+"</div>"+
					"<div class='centrar' style='width: 150px; float: left; margin-right: 5px'>"+resultSplited[2]+"</div>"+
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[3]+"</div>"+
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[4]+"</div>"+
					"<div class='centrar' style='width: 100px; float: left; margin-right: 5px'>"+resultSplited[5]+"</div>"+
					"<div class='centrar' style='width: 125px; float: left; margin-right: 5px'>"+resultSplited[6]+"</div>"+
					"<div class='centrar' style='width: 150px; float: left; margin-right: 2px'>"+resultSplited[7]+"</div>"+
					"<div class='centrar' style='width: 45px; float: left; margin-right: 2px'>"+
							"<input type='image' src='"+path+"img/edit.gif' id='inter_al_id_edit' "+
							"name='inter_al_id_edit' value='"+inter_al_id+"' onclick='edit_alumno(this.value, "+row+")' />"+"</div>"+
					"<div class='centrar' style='width: 45px; float: left; margin-right: 2px'>"+
							"<input type='image' src='"+path+"img/delete.gif' id='inter_al_id_delete' "+
							"name='inter_al_id_delete' value='"+inter_al_id+"' onclick='delete_alumno(this.value, "+row+")' />"."</div>"+
					"</div>";
				document.getElementById('row'+row).innerHTML=renglon;
				var mensaje = "<p class='letraAzul'>Alumno Editado Correctamente.</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
			}
			else{
				var mensaje = "<p class='letraRoja11px'>Ocurri&oacute; un error editando el alumno.</p>";
				document.getElementById('mensajes').innerHTML=mensaje;
			}
			
		});
	} // function edit_alumno(inter_al_id, row)
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