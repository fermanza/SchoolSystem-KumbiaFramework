function view_cursos_alumno(registro){
	var path = $('#path').val();
	$('#basic-modal-content2').modal();
	
	var path = $('#path').val();
	
	tmp ={
		registro: registro,
	};
	
	$.post(path+"view_alumnos_by_registro", tmp, function(result){
		first = result.split('@');
		resultSplited = first[1].split('&&&');
		// var excel = '<form id="form_excel" name="form_excel" method="post"'+
				// ' action="'+path+'cupo_en_xccursos_plan_rigido_exportar_excel/">';
		// excel += '<input type="image" width="25px" class="exportarExcel" title="Exportar a Excel" src="/ingenieria/img/exportarexcel.jpg">';
		// excel += '<input type="hidden" value="'+first[0]+'" id="salon_id" name="salon_id" >';
		// excel += '</form>';
		var txt = htmlEntities(first[0])+'\n\n\n';
		
		// if(resultSplited.length>1){
			// txt += '<div id="basic-modal-content-">';
			// txt += '<table align="left">';
				// txt += '<tr class="azul">';
					// txt += '<th class="bordeAzul2">No.</th>'
					// txt += '<th class="bordeAzul2">Clavecurso</th>';
					// txt += '<th class="bordeAzul2">Clave Mat</th>';
					// txt += '<th class="bordeAzul2">Materia</th>';
					// txt += '<th class="bordeAzul2">Nomina</th>';
					// txt += '<th class="bordeAzul2">Profesor</th>';
				// txt += '</tr>';
				// for (var i=1;i<(resultSplited.length-1);i++)
				// {
					// resultSplited2 = resultSplited[i].split('/');
					// txt += '<tr>';
					// txt += '<td align="center">'+i+'</td>';
					// for (var j=0;j<(resultSplited2.length);j++)
					// {
							// txt += '<td align="center">'+htmlEntities(resultSplited2[j])+'</td>';
					// }
					// txt += '<tr>';
				// }
			// txt += '</table>';
			// txt += '</div>';
		// }
		if(resultSplited.length>1){
				for (var i=0;i<(resultSplited.length-1);i++)
				{
					resultSplited2 = resultSplited[i].split('/');
					for (var j=0;j<(resultSplited2.length);j++)
					{
							txt += (i+1)+". - "+htmlEntities(resultSplited2[j]);
					}
					txt += '\n';
				}
		}
		else{
			txt += 'El alumno no tiene cursos.';
		}
		alert(txt);
	});
	
	
} // function view_cursos_alumno()

function view_alumnos_by_salon_id(salon_id){
// Validate that comment_title and comment_text they are not blank
	var path = $('#path').val();
	$('#basic-modal-content').modal();
	
	var path = $('#path').val();
	
	tmp ={
		salon_id: salon_id,
	};
	
	$.post(path+"view_alumnos_by_salon_id", tmp, function(result){
		first = result.split('@');
		resultSplited = first[2].split('&&&');
		
		var excel = '<form id="form_excel" name="form_excel" method="post"'+
				' action="'+path+'cupo_en_xccursos_plan_rigido_exportar_excel/">';
		excel += '<input type="image" width="25px" class="exportarExcel" title="Exportar a Excel" src="/ingenieria/img/exportarexcel.jpg">';
		excel += '<input type="hidden" value="'+first[0]+'" id="salon_id" name="salon_id" >';
		excel += '</form>';
		var txt = '<h3>'+htmlEntities(resultSplited[0]) +'  '+excel+'</h3>';
		
		var txt = '<h4>Alumnos que tienen TODOS los cursos que conforman el salon '+first[1]+'</h4>';
		
		if(resultSplited.length>1){
			txt += '<div id="basic-modal-content-">';
			txt += '<table align="left">';
				txt += '<tr class="azul">';
					txt += '<th class="bordeAzul2">No.</th>'
					txt += '<th class="bordeAzul2">No. de Cursos</th>';
					txt += '<th class="bordeAzul2">Ver Cursos</th>';
					txt += '<th class="bordeAzul2">Registro</th>';
					txt += '<th class="bordeAzul2">Nombre</th>';
					txt += '<th class="bordeAzul2">Plan</th>';
					txt += '<th class="bordeAzul2">Turno</th>';
					txt += '<th class="bordeAzul2">Plantel</th>';
					txt += '<th class="bordeAzul2">Nivel</th>';
					txt += '<th class="bordeAzul2">Creditos o Rigido</th>';
					txt += '<th class="bordeAzul2">Tipo</th>';
					txt += '<th class="bordeAzul2">Carrera</th>';
				txt += '</tr>';
				for (var i=1;i<(resultSplited.length-1);i++)
				{
					resultSplited2 = resultSplited[i].split('/');
					txt += '<tr>';
					txt += '<td align="center">'+i+'</td>';
					for (var j=0;j<(resultSplited2.length);j++)
					{
						if(j==1){
							txt += '<td align="center"><img src="/ingenieria/img/search2.gif"'+
								'class="pointer basic floatright" width="15px"'+
								'style="margin-right: 20px;" onclick="view_cursos_alumno('+htmlEntities(resultSplited2[j])+')" /></td>';
						}
						if(resultSplited2[j]=="**" || resultSplited2[j]=="CR"){
							if(resultSplited2[j]=="**")
								txt += '<td align="center">R&iacute;gido</td>';
							else
								txt += '<td align="center">Cr&eacute;ditos</td>';
						}
						else{
							txt += '<td align="center">'+htmlEntities(resultSplited2[j])+'</td>';
						}
					}
					txt += '<tr>';
				}
			txt += '</table>';
			txt += '</div>';
		}
		else{
			txt += '<p>No hay alumnos inscritos en este curso</p>';
		}
		
		document.getElementById('basic-modal-content').innerHTML=txt;
	});
	return false;
} // function view_alumnos_by_salon_id(salon_id)


function view_alumnos_by_xccurso_id(curso_id){
// Validate that comment_title and comment_text they are not blank
	var path = $('#path').val();
	$('#basic-modal-content').modal();
	
	var path = $('#path').val();
	
	tmp ={
		curso_id: curso_id,
	};
	
	$.post(path+"cupo_en_xccursos_plan_rigido_ver_alumnos_by_curso_id", tmp, function(result){
		first = result.split('@');
		resultSplited = first[1].split('&&&');
		
		var excel = "";
		// var excel = '<form id="form_excel" name="form_excel" method="post"'+
				// ' action="'+path+'cupo_en_xccursos_plan_rigido_exportar_excel/">';
		// excel += '<input type="image" width="25px" class="exportarExcel" title="Exportar a Excel" src="/ingenieria/img/exportarexcel.jpg">';
		// excel += '<input type="hidden" value="'+first[0]+'" id="curso_id" name="curso_id" >';
		// excel += '</form>';
		var txt = '<h3>'+htmlEntities(resultSplited[0]) +'  '+excel+'</h3>';
		
		if(resultSplited.length>2){
			txt += '<div id="basic-modal-content-">';
			txt += '<table align="left">';
				txt += '<tr class="azul">';
					txt += '<th class="bordeAzul2">No.</th>'
					txt += '<th class="bordeAzul2">Registro</th>';
					txt += '<th class="bordeAzul2">Nombre</th>';
					txt += '<th class="bordeAzul2">Plan</th>';
					txt += '<th class="bordeAzul2">Turno</th>';
					txt += '<th class="bordeAzul2">Plantel</th>';
					txt += '<th class="bordeAzul2">Nivel</th>';
					txt += '<th class="bordeAzul2">Creditos o Rigido</th>';
					txt += '<th class="bordeAzul2">Tipo</th>';
					txt += '<th class="bordeAzul2">Carrera</th>';
				txt += '</tr>';
				for (var i=1;i<(resultSplited.length-1);i++)
				{
					resultSplited2 = resultSplited[i].split('/');
					txt += '<tr>';
					txt += '<td align="center">'+i+'</td>';
					for (var j=0;j<(resultSplited2.length);j++)
					{
						if(resultSplited2[j]=="**" || resultSplited2[j]=="CR"){
							if(resultSplited2[j]=="**")
								txt += '<td align="center">R&iacute;gido</td>';
							else
								txt += '<td align="center">Cr&eacute;ditos</td>';
						}
						else{
							txt += '<td align="center">'+htmlEntities(resultSplited2[j])+'</td>';
						}
					}
					txt += '<tr>';
				}
			txt += '</table>';
			txt += '</div>';
		}
		else{
			txt += '<p>No hay alumnos inscritos en este curso</p>';
		}
		
		document.getElementById('basic-modal-content').innerHTML=txt;
	});
	return false;
} // function view_alumnos_by_xccurso_id(curso_id)


function view_alumnos_by_xtcurso_id(curso_id){
// Validate that comment_title and comment_text they are not blank
	var path = $('#path').val();
	$('#basic-modal-content').modal();
	
	var path = $('#path').val();
	
	tmp ={
		curso_id: curso_id,
	};
	
	$.post(path+"cupo_en_xtcursos_plan_rigido_ver_alumnos_by_curso_id", tmp, function(result){
		first = result.split('@');
		resultSplited = first[1].split('&&&');
		
		var excel = "";
		// var excel = '<form id="form_excel" name="form_excel" method="post"'+
				// ' action="'+path+'cupo_en_xccursos_plan_rigido_exportar_excel/">';
		// excel += '<input type="image" width="25px" class="exportarExcel" title="Exportar a Excel" src="/ingenieria/img/exportarexcel.jpg">';
		// excel += '<input type="hidden" value="'+first[0]+'" id="curso_id" name="curso_id" >';
		// excel += '</form>';
		var txt = '<h3>'+htmlEntities(resultSplited[0]) +'  '+excel+'</h3>';
		
		if(resultSplited.length>2){
			txt += '<div id="basic-modal-content-">';
			txt += '<table align="left">';
				txt += '<tr class="azul">';
					txt += '<th class="bordeAzul2">No.</th>'
					txt += '<th class="bordeAzul2">Registro</th>';
					txt += '<th class="bordeAzul2">Nombre</th>';
					txt += '<th class="bordeAzul2">Plan</th>';
					txt += '<th class="bordeAzul2">Turno</th>';
					txt += '<th class="bordeAzul2">Plantel</th>';
					txt += '<th class="bordeAzul2">Nivel</th>';
					txt += '<th class="bordeAzul2">Creditos o Rigido</th>';
					txt += '<th class="bordeAzul2">Tipo</th>';
					txt += '<th class="bordeAzul2">Carrera</th>';
				txt += '</tr>';
				for (var i=1;i<(resultSplited.length-1);i++)
				{
					resultSplited2 = resultSplited[i].split('/');
					txt += '<tr>';
					txt += '<td align="center">'+i+'</td>';
					for (var j=0;j<(resultSplited2.length);j++)
					{
						if(resultSplited2[j]=="**" || resultSplited2[j]=="CR"){
							if(resultSplited2[j]=="**")
								txt += '<td align="center">R&iacute;gido</td>';
							else
								txt += '<td align="center">Cr&eacute;ditos</td>';
						}
						else{
							txt += '<td align="center">'+htmlEntities(resultSplited2[j])+'</td>';
						}
					}
					txt += '<tr>';
				}
			txt += '</table>';
			txt += '</div>';
		}
		else{
			txt += '<p>No hay alumnos inscritos en este curso</p>';
		}
		
		document.getElementById('basic-modal-content').innerHTML=txt;
	});
	return false;
} // function view_alumnos_by_xccurso_id(curso_id)


function utf8_encode (argString) {
  // http://kevin.vanzonneveld.net
  // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: sowberry
  // +    tweaked by: Jack
  // +   bugfixed by: Onno Marsman
  // +   improved by: Yves Sucaet
  // +   bugfixed by: Onno Marsman
  // +   bugfixed by: Ulrich
  // +   bugfixed by: Rafal Kukawski
  // +   improved by: kirilloid
  // *     example 1: utf8_encode('Kevin van Zonneveld');
  // *     returns 1: 'Kevin van Zonneveld'

  if (argString === null || typeof argString === "undefined") {
    return "";
  }

  var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var utftext = '',
    start, end, stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode((c1 >> 6) | 192, (c1 & 63) | 128);
    } else {
      enc = String.fromCharCode((c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}



function utf8_decode (str_data) {
  // http://kevin.vanzonneveld.net
  // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // +      input by: Aman Gupta
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Norman "zEh" Fuchs
  // +   bugfixed by: hitwork
  // +   bugfixed by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // *     example 1: utf8_decode('Kevin van Zonneveld');
  // *     returns 1: 'Kevin van Zonneveld'
  var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0;

  str_data += '';

  while (i < str_data.length) {
    c1 = str_data.charCodeAt(i);
    if (c1 < 128) {
      tmp_arr[ac++] = String.fromCharCode(c1);
      i++;
    } else if (c1 > 191 && c1 < 224) {
      c2 = str_data.charCodeAt(i + 1);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
      i += 2;
    } else {
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    }
  }

  return tmp_arr.join('');
}


function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}