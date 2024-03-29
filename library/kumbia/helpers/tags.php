<?php

/**
 * Kumbia PHP Framework
 *
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbia.org/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package Helpers
 * @copyright Copyright (c) 2005-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (C) 2007-2007 Roger Jose Padilla Camacho(rogerjose81 at gmail.com)
 * @copyright Copyright (C) 2007-2008 Deivinson Jose Tejeda Brito(deivinsontejeda at gmail.com)
 * @copyright Copyright (C) 2008-2008 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Construye el inicio del tag xhtml
 *
 * @param string $tag nombre de etiqueta
 * @param array $attrs atributos para la etiqueta
 * @return string
 */
function xhtml_start_tag($tag, $attrs=null) {
	$params = is_array($tag) ? $tag : get_params(func_get_args());
	
	if(isset($params[1]) && is_array($params[1])) {
		$attrs = $params[1];
		unset($params[1]);
		$attrs = array_merge($attrs, $params);
	} else {
		$attrs = $params;
	}
	
	$code = "<$tag";
	if(is_array($attrs)) {
		$filter = Filter::get_instance();
		$filter->apply_filter($attrs, 'htmlspecialchars');
		foreach($attrs as $k=>$v) {
			if(!is_numeric($k)) {
				$code.=" $k=\"$v\"";
			}
		}
	}
	$code.='>';
	
	return $code;
}

/**
 * Construye el cierre de un tag xhtml
 *
 * @param string $tag nombre de etiqueta
 * @return string
 **/
function xhtml_end_tag($tag) {
	$params = is_array($tag) ? $tag : get_params(func_get_args());
	$tag = $params[0];
	return "</$tag>";
}

/**
 * Construye un tag xhtml
 *
 * @param string $tag nombre de etiqueta
 * @param array $attrs atributos para la etiqueta
 *
 * content: contenido, este parametro con nombre es incluido debido a que
 *  el argumento $content puede ser confundido con un parametro con nombre,
 *  si este llegase a poseer la sintaxis de los parametros con nombre.
 *
 * Nota: el parametro con nombre content nunca es utilizado como un atributo
 *  para el tag, a menos de que este se pase en el array de atributos.
 *
 * @return string
 **/
function xhtml_tag($tag, $attrs=null) {
	$params = is_array($tag) ? $tag : get_params(func_get_args());

	/**
	 * Pueden tener cierre corto
	 **/
	$short_close = array('input', 'link', 'img');
	/**
	 * Necesitan estar entre CDATA
	 **/
	$need_cdata = array('script', 'style');

	$tag = $params[0];
	unset($params[0]);
	
	/**
	 * Cargo el contenido interno para el tag
	 **/
	if(isset($params['content'])) {
		$content = $params['content'];
		unset($params['content']);
	} else {
		$content = '';
	}
	
	if(isset($params[1]) && is_array($params[1])) {
		$attrs = $params[1];
		unset($params[1]);
		$attrs = array_merge($attrs, $params);
	} else {
		$attrs = $params;
	}
	
	$code = "<$tag";
	if(is_array($attrs)) {
		$filter = Filter::get_instance();
		$filter->apply_filter($attrs, 'htmlspecialchars');
		foreach($attrs as $k=>$v) {
			if(!is_numeric($k)) {
				$code.=" $k=\"$v\"";
			}
		}
	}
	
	if($content || !in_array($tag, $short_close)) {
		if($content && in_array($tag, $need_cdata)) {
			$content = "\r\n<!-- <![CDATA[\r\n{$content}\r\n// ]]> -->\r\n";
		}
		$code.=">$content</$tag>";
	} else {
		$code.="/>";
	}

	return $code;
}

/**
 * Crea un enlace en una Aplicacion respetando
 * las convenciones de Kumbia
 *
 * @param string $action
 * @param string $text
 *
 * confirm: confirmacion antes de ejecutar
 *
 * @return string
 */
function link_to($action, $text=''){
	$params = is_array($action) ? $action : get_params(func_get_args());
	
	if(isset($params['confirm'])&&$params['confirm']){
		if(isset($params['onclick'])) {
			$params['onclick'] = "if(!confirm(\"{$params['confirm']}\")) { return false; }; ".$params['onclick'];
		} else {
			$params['onclick'] = "if(!confirm(\"{$params['confirm']}\")) { return false; };";
		}
		unset($params['confirm']);
	}
	
	if(isset($params['text'])) {
		$params[1] = $params['text'];
		unset($params['text']);
	}
	
	if(!isset($params[1])) {
		$text = strtr($params[0], '_/', '  ');
		$params[1] = ucwords($text);
	}
	
	$params['href'] = get_kumbia_url($params[0]);
	
	return xhtml_tag('a', $params, "content: {$params[1]}");
}

/**
 * Crea un enlace a una accion dentro del controlador Actual
 *
 * @param string $action
 * @param string $text
 *
 * confirm: confirmacion antes de ejecutar
 *
 * @return string
 */
function link_to_action($action, $text=''){
	$params = is_array($action) ? $action : get_params(func_get_args());
	
	if(isset($params['confirm'])&&$params['confirm']){
		if(isset($params['onclick'])) {
			$params['onclick'] = "if(!confirm(\"{$params['confirm']}\")) { return false; }; ".$params['onclick'];
		} else {
			$params['onclick'] = "if(!confirm(\"{$params['confirm']}\")) { return false; };";
		}
		unset($params['confirm']);
	}
	
	if(isset($params['text'])) {
		$params[1] = $params['text'];
		unset($params['text']);
	}
	
	if(!isset($params[1])) {
		$text = strtr($params[0], '_/', '  ');
		$params[1] = ucwords($text);
	}
	
	$module_name = Router::get_module();
	$controller_name = Router::get_controller();
	if($module_name) {
		$path = "$module_name/$controller_name";
	} else {
		$path = $controller_name;
	}
	$params['href'] = get_kumbia_url("$path/{$params[0]}");
	
	return xhtml_tag('a', $params, "content: {$params[1]}");
}

/**
 * Permite ejecutar una acci�n en la vista actual dentro de un contenedor
 * HTML usando AJAX
 *
 * confirm: Texto de Confirmaci�n
 * success: Codigo JavaScript a ejecutar cuando termine la petici�n AJAX
 * before: Codigo JavaScript a ejecutar antes de la petici�n AJAX
 * oncomplete: Codigo JavaScript que se ejecuta al terminar la petici�n AJAX
 * update: Que contenedor HTML ser� actualizado
 * action: Accion que ejecutar� la petici�n AJAX
 * text: Texto del Enlace
 *
 * @return string
 */
function link_to_remote($action){
	$params = is_array($action) ? $action : get_params(func_get_args());
	
	if(!isset($params['update'])||!$params['update']){
		$update = isset($params[2]) ? $params[2] : "";
	} else {
		$update = $params['update'];
	}
	if(!isset($params['text'])||!$params['text']){
		$text = isset($params[1]) ? $params[1] : "";
	} else {
		$text = $params['text'];
	}
	if(!$text){
		$text = $params[0];
	}
	if(!isset($params['action'])||!$params['action']){
		$action = $params[0];
	} else {
		$action = $params['action'];
	}
	
	$code = '';
	if(isset($params['confirm'])){
		$code.= "if(confirm('{$params['confirm']}')) {";
	}
	$code.= "new AJAX.viewRequest({action: '$action', container: '$update'";

	$call = array();
	if(isset($params['before'])){
		$call["before"] = "before: function(){ {$params['before']} }";
	}
	if(isset($params['oncomplete'])){
		$call["oncomplete"] = "oncomplete: function(){ {$params['oncomplete']} }";
	}
	if(isset($params['success'])){
		$call["success"] = "success: function(){ {$params['success']} }";
	}
	if(count($call)){
		$code.=", callbacks: { ";
		$code.=join(",", $call);
		$code.="}";
	}
	$code.="})";
	if(isset($params['confirm'])){
		$code.=" }";
	}
	$code.="; return false;";
	
	$params['onclick'] = $code;
	$params['href'] = '#';
	
	unset($params['before']);
	unset($params['oncomplete']);
	unset($params['success']);
	unset($params['loading']);
	unset($params['update']);
	unset($params['confirm']);
	
	return xhtml_tag('a',$params, "content: $text");
}

/**
 * Genera una etiqueta script que apunta a un archivo JavaScript
 * respetando las rutas y convenciones de Kumbia
 *
 * @param string $src
 *
 * cache: indica si usa cache (true, false), por defecto se utiliza la cache
 *
 * @return string
 */
function javascript_include_tag($src=''){
	$params = is_array($src) ? $src : get_params(func_get_args());
	
	if(isset($params['cache']) && $params['cache']=='false') {
		$cache = false;
		unset($params['cache']);
	} else {
		$cache = true;
	}
	
	if(!isset($params[0])) {
		$params[0] = Router::get_controller();
	}
	
	$code = '';
	foreach($params as $src) {
		$src.=".js";
		if(!$cache) {
			$src.="?nocache=".md5(uniqid());
		}
		$src = KUMBIA_PATH."javascript/$src";
		$code.=xhtml_tag('script', $params, 'type: text/javascript', "src: $src");
	}
	
	return $code;
}

/**
 * Agrega una etiqueta script que apunta a un archivo en public/javascript/kumbia
 *
 * @param string $src
 * @return string
 */
function javascript_library_tag($src){
	$params = is_array($src) ? $src : get_params(func_get_args());
	$code = '';
	foreach($params as $src) {
		$src = KUMBIA_PATH."javascript/kumbia/$src.js";
		$code.=xhtml_tag('script', 'type: text/javascript', "src: $src");
	}
	return $code;
}

/**
 * Agrega una etiqueta link para incluir un archivo CSS respetando
 * las rutas y convenciones de Kumbia
 *
 * @param string $name nombre de hoja de estilo
 *
 * use_variables: utilizar variables de Kumbia en el css
 *
 * @return string
 */
function stylesheet_link_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$use_variables = isset($params['use_variables']);
	unset($params['use_variables']);
	
	$params['rel'] = 'stylesheet';
	$params['type'] = 'text/css';
	
	$kb = substr(KUMBIA_PATH, 0, strlen(KUMBIA_PATH)-1);
	$code = '';
	for($i=0; isset($params[$i]); $i++){
		$src = $params[$i];
		if($use_variables){
			$params['href'] = KUMBIA_PATH."css.php?c=$src&p=$kb";
		} else {
			$params['href'] = KUMBIA_PATH."css/$src.css";
		}
		$code.=xhtml_tag('link',$params);
	}

	if(!$i){ //$i=0 si no se especificaron hojas de estilo
		$src = $_REQUEST['action'];
		if($use_variables){
			$params['href'] = KUMBIA_PATH."css.php?c=$src&p=$kb";
		} else {
			$params['href'] = KUMBIA_PATH."css/$src.css";
		}
		$code.=xhtml_tag('link',$params);
	}
    return $code;
}

/**
 * Permite incluir una imagen dentro de una vista respetando
 * las convenciones de directorios y rutas en Kumbia
 *
 * @param string $img
 *
 * drag: capacidad de arrastrar la imagen
 * reflect: adicionar reflejo
 *
 * @return string
 */
function img_tag($img){
	$params = is_array($img) ? $img : get_params(func_get_args());

	if(!isset($params['src']) && isset($params[0])){
		$params['src'] = KUMBIA_PATH."img/{$params[0]}";
	}
	if(!isset($params['alt'])) {
		$params['alt'] = '';
	}
	
	if(isset($params['drag'])&&$params['drag']) {
		$drag = true;
		unset($params['drag']);
	} else {
		$drag = false;
	}
	if(isset($params['reflect'])&&$params['reflect']) {
		$reflect = true;
		unset($params['reflect']);
	} else{
		$reflect = false;
	}

	$code = xhtml_tag('img', $params);
	if($drag){
		$code.=xhtml_tag('script', 'type: text/javascript', "content: new Draggable('{$atts['id']}', {revert:true})");
	}
	if($reflect){
		$code.=xhtml_tag('script', 'type: text/javascript', "content: new Reflector.reflect('{$atts['id']}')");
	}
	
	return $code;
}

/**
 * Permite generar un formulario remoto
 *
 * @param string $data
 *
 * update: contenedor html a actualizar
 * success: Codigo JavaScript a ejecutar cuando termine la peticion AJAX
 * before: Codigo JavaScript a ejecutar antes de la peticion AJAX
 * complete: Codigo JavaScript que se ejecuta al terminar la peticion AJAX
 *
 * @return string
 */
function form_remote_tag($data){
	$params = is_array($data) ? $data : get_params(func_get_args());
	
	if(!isset($params['action'])||!$params['action']) {
		$params['action'] = $params[0];
	}
	if(!isset($params['method'])||!$params['method']) {
		$params['method'] = 'post';
	}

	if(isset($params['update'])) {
		$update = $params['update'];
		unset($params['update']);
	} else {
		$update = '';
	}

	$callbacks = array();
	$id = Router::get_id();
	if(isset($params['complete'])&&$params['complete']){
		$callbacks[] = " complete: function(){ ".$params['complete']." }";
		unset($params['complete']);
	}
	if(isset($params['before'])&&$params['before']){
		$callbacks[] = " before: function(){ ".$params['before']." }";
		unset($params['before']);
	}
	if(isset($params['success'])&&$params['success']){
		$callbacks[] = " success: function(){ ".$params['success']." }";
		unset($params['sucess']);
	}
	if(isset($params['required'])&&$params['required']){
		$requiredFields = encomillar_lista($params['required']);
		$params['onsubmit'] = "if(validaForm(this,new Array({$requiredFields}))){ return ajaxRemoteForm(this,\"{$update}\",{".join(",",$callbacks)."}); } else{ return false; }";
		unset($params['required']);
	} else{
		$params['onsubmit'] = "return ajaxRemoteForm(this, \"{$update}\", { ".join(",", $callbacks)." });";
	}
	$params['action'] = get_kumbia_url("{$params['action']}/$id");
	
	return xhtml_start_tag('form', $params);
}


/**
 * Crea una etiqueta de formulario
 *
 * @param string $action
 *
 * confirm: confirmacion antes de enviar datos
 *
 * @return string
 */
function form_tag($action){
	$params = is_array($action) ? $action : get_params(func_get_args());
	
	$id = Router::get_id();
	if(!isset($params['action']) && isset($params[0])) {
		$params['action'] = $params[0];
	}
	if(isset($params['action'])) {
		$params['action'] = get_kumbia_url("{$params['action']}/$id");
	}
	if(!isset($params['method'])||!$params['method']) {
		$params['method'] = "post";
	}
	if(isset($params['confirm'])&&$params['confirm']){
		if(isset($params['onsubmit'])) {
			$params['onsubmit'].=";if(!confirm(\"{$params['confirm']}\")) { return false; }";
		} else {
			$params['onsubmit'] = "if(!confirm(\"{$params['confirm']}\")) { return false; }";
		}
		unset($params['confirm']);
	}
	
	return xhtml_start_tag('form', $params);
}



/**
 * Etiqueta para cerrar un formulario
 *
 * @return $string_code
 */
function end_form_tag(){
	$str = "</form>\r\n";
	return $str;
}

/**
 * Crea un boton de submit para el formulario actual
 *
 * @param string $caption
 * @return html code
 */
function submit_tag($caption){
	$params = is_array($caption) ? $caption : get_params(func_get_args());
	if(isset($params['caption'])) {
		$params['value'] = $params['caption'];
		unset($params['caption']);
	} elseif(isset($params[0])) {
		$params['value'] = $params[0];
	}
	return xhtml_tag('input', $params, 'type: submit');
}

/**
 * Crea un boton de submit para el formulario remoto actual
 *
 * @param string $caption
 *
 * caption: texto del boton
 * update: contenedor html a actualizar
 * success: Codigo JavaScript a ejecutar cuando termine la peticion AJAX
 * before: Codigo JavaScript a ejecutar antes de la peticion AJAX
 * complete: Codigo JavaScript que se ejecuta al terminar la peticion AJAX
 *
 * @return html code
 */
function submit_remote_tag($caption){
	$params = is_array($caption) ? $caption : get_params(func_get_args());
	
	if(isset($params['caption'])) {
		$params['value'] = $params['caption'];
		unset($params['caption']);
	} elseif(isset($params[0])) {
		$params['value'] = $params[0];
	}
	
	if(isset($params['update'])) {
		$update = $params['update'];
		unset($params['update']);
	} else {
		$update = '';
	}
	
	$callbacks = array();
	if(isset($params['complete']) && $params['complete']){
		$callbacks[] = " complete: function(){ ".$params['complete']." }";
		unset($params['complete']);
	}
	if(isset($params['before']) && $params['before']){
		$callbacks[] = " before: function(){ ".$params['before']." }";
		unset($params['before']);
	}
	if(isset($params['success']) && $params['success']){
		$callbacks[] = " success: function(){ ".$params['success']." }";
		unset($params['success']);
	}
	
	if(isset($params['onclick'])) {
		$params['onclick'].= "; return ajaxRemoteForm(this.form, \"$update\", { ".join(",", $callbacks)." });";
	} else {
		$params['onclick'] = "return ajaxRemoteForm(this.form, \"$update\", { ".join(",", $callbacks)." });";
	}
	
	return xhtml_tag('input', $params, 'type: submit');
}

/**
 * Crea un boton de submit tipo imagen para el formulario actual
 *
 * @param string $caption
 *
 * caption: texto del boton
 *
 * @return html code
 */
function submit_image_tag($caption, $src=''){
	$params = is_array($caption) ? $caption : get_params(func_get_args());
	if(isset($params['caption'])) {
		$params['value'] = $params['caption'];
		unset($params['caption']);
	} elseif(isset($params[0])) {
		$params['value'] = $params[0];
	}
	if(!isset($params['src']) && isset($params[1])) {
		$params['src'] = $params[1];
	}
	return xhtml_tag('input', $params, 'type: image');
}

/**
 * Crea un boton HTML
 * @param string $caption
 *
 * caption: texto del boton
 *
 * @return string
 */
function button_tag($caption=''){
	$params = is_array($caption) ? $caption : get_params(func_get_args());
	if(isset($params['caption'])) {
		$params['value'] = $params['caption'];
		unset($params['caption']);
	} elseif(isset($params[0])) {
		$params['value'] = $params[0];
	}
	return xhtml_tag('input', $params, 'type: button');
}

/**
 * Obtiene el valor de un componente tomado
 * del mismo valor del nombre del campo en el modelo
 * del mismo nombre del controlador o el indice en
 * $_REQUEST
 *
 * @param string $name
 * @return mixed
 */
function get_value_from_action($name){
	$p = explode('.', $name);
	if(count($p)>1) {
		$value = get_value_from_action($p[0]);
		if(is_object($value) && isset($value->$p[1])) {
			return $value->$p[1];
		} else {
			return '';
		}
	} else {
		$controller = Dispatcher::get_controller();
		
		if(isset($controller->$name)){
			return $controller->$name;
		} else {
			return "";
		}
	}
}

/**
 * Obtiene el id and name apartir del argumento, con la sintaxis "model.field" o solo "field"
 *
 * @param string $value
 * @return array
 */
function get_id_and_name($value){
	$p = explode('.', $value);
	if(count($p)>1) {
		$id = "{$p[0]}_{$p[1]}";
		$name = "{$p[0]}[{$p[1]}]";
	} else {
		$id = $name = $value;
	}
	return array('id'=>$id, 'name'=>$name);
}

# Helpers
/**
 * Crea un campo input
 * @param string $name
 * @return string
 **/
function input_field_tag($name) {
	$params = is_array($name) ? $name : get_params(func_get_args());
	/**
	 * Obtengo id, name y value
	 **/
	if(isset($params[0])) {
		$params = array_merge(get_id_and_name($params[0]), $params);
		$value = get_value_from_action($params[0]);
		if(!isset($params['value'])) {
			if($value) {
				$params['value'] = $value;
			}
		} elseif($params['type']=='radio' || $params['type']=='checkbox') {
			if($params['value']==$value) {
				$params['checked'] = 'checked';
			}
		}
	}
	return xhtml_tag('input', $params);
}

/**
 * Crea una caja de Texto
 *
 * @param string $name
 * @return string
 */
function text_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'text';
	return input_field_tag($params);
}

/**
 * Crea un CheckBox
 *
 * @param string $name
 * @return string
 */
function checkbox_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'checkbox';
	return input_field_tag($params);
}

/**
 * Caja de texto que admite solo numeros
 *
 * @param string $name
 * @return string
 */
function numeric_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	if(!isset($params['onkeydown'])) {
		$params['onkeydown'] = "valNumeric(event)";
	} else {
		$params['onkeydown'].=";valNumeric(event)";
	}
	return text_field_tag($params);
}

/**
 * Crea una caja de texto que acepta solo texto en Mayuscula
 *
 * @param string $name
 * @return string
 */
function textupper_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	if(!isset($params['onblur'])) {
		$params['onblur'] = "keyUpper2(this)";
	} else {
		$params['onblur'].=";keyUpper2(this)";
	}
	return text_field_tag($params);
}

/**
 * Crea un componente para seleccionar la fechas
 *
 * @param string $name
 * @param string $format
 * @param string $theme
 * @param string $languaje
 * @return string
 */

function date_field_tag($name){
    static $i = false;
	$params = is_array($name) ? $name : get_params(func_get_args());
	
	if(isset($params['format'])){
		$format = $params['format'];
		unset($params['format']);
	} else {
		$format = "%d-%m-%Y";
	}
	if(isset($params['theme'])){
		$theme = $params['theme'];
		unset($params['theme']);
	} else {
		$theme = "theme-1";
	}
	if(isset($params['language'])){
		$language = $params['language'];
		unset($params['language']);
	} else {
		$language = "calendar-es";
	}

	if(isset($params[0])) {
		$params = array_merge(get_id_and_name($params[0]), $params);
	}
	
	$code = '';
	if($i == false){
	    $i = true;
	    $code .= javascript_library_tag('jscalendar/calendar');
	    $code .= javascript_library_tag('jscalendar/calendar-setup'); 
	    $code .= javascript_library_tag("jscalendar/$language");   
	}
	
	$code .= text_field_tag($params);
    $code .= img_tag("calendar.gif","id: ".$params['id']."tigger","style: cursor: pointer;")."\n";
		
	
	$code .= stylesheet_link_tag("style-calendar/$theme", "true");	
	$script= "	Calendar.setup({ \n".
      		"		inputField  : '".$params['id']."',         // ID of the input field \n".
			"		ifFormat    : '".$format."',    // the date format \n".
			"		daFormat	: '".$format."', \n".
			"		button      : '".$params['id']."tigger"."'       // ID of the button \n".
			"		} \n".
			"		); \n";						 
	$code .= xhtml_tag('script', 'type: text/javascript', "content: $script");
	
	return $code;
}

/**
 * Crea un Input tipo Text
 *
 * @param string $name
 * @return string
 */
function file_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'file';
	return input_field_tag($params);
}

/**
 * Crea un input tipo Radio
 *
 * @param string $name
 * @return string
 */
function radio_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'radio';
	return input_field_tag($params);
}

/**
 * Crea un TextArea
 *
 * @param string $name id del textarea
 * @return string
 */
function textarea_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	/**
	 * Obtengo id, name y value
	 **/
	if(isset($params[0])) {
		$params = array_merge(get_id_and_name($params[0]), $params);
		if(!isset($params['value'])) {
			$value = get_value_from_action($params[0]);
			if($value) {
				$params['value'] = $value;
			}
		}
	}

	$filter = Filter::get_instance();
	$filter->apply_filter($params, 'htmlspecialchars');

	if(isset($params['value'])) {
		$value = $params['value'];
		unset($params['value']);
	} else {
		$value = '';
	}
	
	if(!isset($params['rows'])) {
		$params['rows'] = '25';
	}
	if(!isset($params['cols'])) {
		$params['cols'] = '50';
	}

	return xhtml_tag('textarea', $params, "content: $value");
}


/**
 * Crea un componente para capturar Passwords
 *
 * @param string $name
 * @return string
 */
function password_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'password';
	return input_field_tag($params);
}

/**
 * Crea un Componente Oculto
 *
 * @param string $name
 * @return string
 */
function hidden_field_tag($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	$params['type'] = 'hidden';
	return input_field_tag($params);
}

/**
 * Crea una lista SELECT
 *
 * @param string $name
 * @param array, string $data
 *
 * selected: opcion seleccionada
 * include_blank: incluir opcion con valor nulo, se muestra en la opcion el texto aqui indicado
 *
 * Para el select basado en activerecord:
 * option: lista de campos separados por coma para colocar en la opcion (por defecto es el id)
 * separator: separador de valores para los campos de la opcion
 * value: indica el campo que servira de valor para la opcion (por defecto es el id)
 * conditions: condiciones de busqueda
 *
 * @return string
 *
 * Ejemplos:
 * select_tag('marca_id', 'Marca', 'conditions: tipo="2"', 'option: nombre')
 * select_tag('marca_id', 'Marca', 'SELECT * FROM marca WHERE tipo="2"', 'option: nombre')
 * select_tag('sexo', array('M' => 'Masculino', 'F' => 'Femenino'), 'include_blank: Seleccione uno...')
 */
function select_tag($name, $data=array()){
	$params = is_array($name) ? $name : get_params(func_get_args());
	
	/**
	 * Obtengo id, name y value
	 **/
    $params = array_merge(get_id_and_name($params[0]), $params);
    if(!isset($params['selected'])) {
        $value = get_value_from_action($params[0]);
        if($value) {
            $params['selected'] = $value;
        }
    }
	
    if(!isset($params[1])) {
        return xhtml_start_tag('select', $params);
    }
    
	if(isset($params['selected'])) {
		$selected = $params['selected'];
		unset($params['selected']);
	}
	
	$filter = Filter::get_instance();
	
	$options = "\r\n";
	if(isset($params['include_blank'])) {
		$filter->apply_filter($params['include_blank'], 'htmlspecialchars');
		$options.="\t".xhtml_tag('option', array('value'=>''), "content: {$params['include_blank']}");
		unset($params['include_blank']);
	}

	if(is_array($params[1])){
		foreach($params[1] as $k=>$v){
			if(isset($selected) && $selected==$k) {
				$options.="\t".option_tag($k, $v, 'selected: selected');
			} else {
				$options.="\t".option_tag($k, $v);
			}
		}
	} elseif(is_string($params[1]) && $params[1]) {
		if(isset($params['option'])) {
			$fields = array_map('trim', explode(',', $params['option']));
			unset($params['option']);
		} else {
			$fields = array('id');
		}
			
		if(isset($params['separator'])) {
			$separator = $params['separator'];
			unset($params['separator']);
		} else {
			$separator = '';
		}
			
		/**
		 * combo creado a partir de un modelo
		 **/
		$model = ucfirst(camelize($params[1]));
		$m = kumbia::$models[$model];
		
		if(isset($params['value'])) {
			$value = $params['value'];
			unset($params['value']);
		} else {
			$m2 = clone $m;
			$m2->dump_model();
			$value = $m2->primary_key[0];
		}
		
		if(isset($params[2]) && $params[2]) {
			$items = $m->find_all_by_sql($params[2]);
		} else {
			/**
			 * Arreglo que contiene los argumentos para el find
			 **/
			$find_args = array();
			
			/**
			 * Asignando parametros de busqueda
			 **/
			if(isset($params['conditions'])) {
				array_push($find_args, "conditions: {$params['conditions']}");
				unset($params['conditions']);
			}
			if(isset($params['columns'])) {
				array_push($find_args, "columns: {$params['columns']}");
				unset($params['columns']);
			}
			if(isset($params['join'])) {
				array_push($find_args, "join: {$params['join']}");
				unset($params['join']);
			}
			if(isset($params['group'])) {
				array_push($find_args, "group: {$params['group']}");
				unset($params['group']);
			}
			if(isset($params['having'])) {
				array_push($find_args, "having: {$params['having']}");
				unset($params['having']);
			}
			if(isset($params['order'])) {
				array_push($find_args, "order: {$params['order']}");
				unset($params['order']);
			}
			if(isset($params['distinct'])) {
				array_push($find_args, "distinct: {$params['distinct']}");
				unset($params['distinct']);
			}
			if(isset($params['limit'])) {
				array_push($find_args, "limit: {$params['limit']}");
				unset($params['limit']);
			}
			if(isset($params['offset'])) {
				array_push($find_args, "limit: {$params['offset']}");
				unset($params['offset']);
			}

			$items = call_user_func_array(array($m, 'find'), $find_args);
		}
		
		foreach($items as $item) {
			$vals = array();
			foreach($fields as $option) {
				array_push($vals, $item->$option);
			}
			
			$k = $item->$value;
			$v = implode($vals, $separator);
			
			if(isset($selected) && $selected==$k) {
				$options.="\t".option_tag($k, $v, 'selected: selected');
			} else {
				$options.="\t".option_tag($k, $v);
			}
		}
	}

	return xhtml_tag('select', $params, "content: $options");
}

/**
 * Crea una opcion de un SELECT
 *
 * @param string $value
 * @param string $text
 * @return string
 */
function option_tag($value, $text=''){
	$params = is_array($value) ? $value : get_params(func_get_args());
	
	$params['value'] = $params[0];
	if(isset($params[1])) {
		$text = $params[1];
	} else {
		$text = '';
	}
	$filter = Filter::get_instance();
	$filter->apply_filter($text, 'htmlspecialchars');
	
	return xhtml_tag('option', $params, "content: $text");
}


/**
 * Crea un componente para Subir Imagenes
 *
 * @param string $name id del tag
 * @return string
 */
function upload_image_tag($name){
	$opts = is_array($name) ? $name : get_params(func_get_args());
	$code = '';
	
	if(isset($opts[0])){
		$opts['name'] = $opts[0];
	} else {
	    $opts['name'] = '';
	}
	if(isset($opts['value'])){
		$opts['value'] = $opts[1];
	} else {
	    $opts['value'] = '';
	}
	
	$code.="<span id='{$opts['name']}_span_pre'>
	<select name='{$opts[0]}' id='{$opts[0]}' onchange='show_upload_image(this)'>";
	$code.="<option value='@'>Seleccione...\n";
	foreach(scandir("public/img/upload") as $file){
		if($file!='index.html'&&$file!='.'&&$file!='..'&&$file!='Thumbs.db'&&$file!='desktop.ini'){
			$nfile = str_replace('.gif', '', $file);
			$nfile = str_replace('.jpg', '', $nfile);
			$nfile = str_replace('.png', '', $nfile);
			$nfile = str_replace('.bmp', '', $nfile);
			$nfile = strtr($nfile, '_', ' ');
			$nfile = ucfirst($nfile);
			if(urlencode("upload/$file")==$opts['value']){
				$code.="<option selected='selected' value='upload/$file' style='background: #EAEAEA'>$nfile</option>\n";
			} else {
				$code.="<option value='upload/$file'>$nfile</option>\n";
			}
		}
	}
	$code.="</select> <a href='#{$opts['name']}_up' name='{$opts['name']}_up' id='{$opts['name']}_up' onclick='enable_upload_file(\"{$opts['name']}\")'>Subir Imagen</a></span>
	<span style='display:none' id='{$opts['name']}_span'>
	<input type='file' id='{$opts['name']}_file' onchange='upload_file(\"{$opts['name']}\")' />
	<a href='#{$opts['name']}_can' name='{$opts['name']}_can' id='{$opts['name']}_can' style='color:red' onclick='cancel_upload_file(\"{$opts['name']}\")'>Cancelar</a></span>
	";
	if(!isset($opts['width'])) {
		$opts['width'] = 128;
	}
	if(!isset($opts['value'])){
		$opts['style']="border: 1px solid black;margin: 5px;".$opts['value'];
	} else {
		$opts['style']="border: 1px solid black;display:none;margin: 5px;".$opts['value'];
	}
	$code.="<div>".img_tag(urldecode($opts['value']), 'width: '.$opts['width'], 'style: '.$opts['style'], 'id: '.$opts['name']."_im")."</div>";
	return $code;
}

/**
 * Hace que un objeto se pueda arrastrar en la pantalla
 * @param string $obj id del objeto
 * @param string $action accion a ejecutar al soltar
 * @return string
 *
 * name: id del objeto
 * action: accion a ejecutar al soltar
 **/
function set_droppable($obj, $action=''){
	$params = is_array($obj) ? $obj : get_params(func_get_args());
	if(!isset($params['name']) || !$params['name']){
		$params['name'] = $params[0];
	}
	if(!isset($params['action']) || !$params['action']){
		$params['action'] = $params[1];
	}
	return xhtml_tag('script', 'type: text/javascript', "content: Droppables.add('{$params['name']}', {hoverclass: '{$params['hover_class']}',onDrop:{$params['action']}})");
}

/**
 * Redirecciona una a accion luego de transcurrir un periodo de tiempo
 * @param string $action accion a ejecutar
 * @param float $seconds segundos a esperar
 * @return string
 **/
function redirect_to($action, $seconds = 0.01){
	$seconds*=1000;
	return xhtml_tag('script', 'type: text/javascript', "content: setTimeout('window.location=\"?/$action\"', $seconds)");
}

/**
 * Renderiza una vista parcial
 * 
 * @param string $partial vista a renderizar
 *
 * partial_view: vista a renderizar (ruta cruda de ubicacion en views)
 * controller: controlador al que pertenece (por defecto se toma el actual)
 * module: controlador al que pertenece (por defecto se toma el actual si existe)
 *
 * @return string
 **/
function render_partial($partial){
	$params = get_params(func_get_args());

	if(isset($params['controller'])) {
		$controller_name = $params['controller'];
	} else {
		$controller_name = Router::get_controller();
	}
	if(isset($params['module'])) {
		$module_name = $params['module'];
	} else {
		$module_name = Router::get_module();
	}
	
	$views_dir = Kumbia::$active_views_dir;

	if(defined('USE_SMARTY')) {
		$ext = 'tpl';
	} else {
		$ext = 'phtml';
	}
	
	if(isset($params['partial_view'])) {
		$data = explode('/', $params['partial_view']);
	} else {
		$data = explode('/', $params[0]);
	}
	if($n = count($data)) {
		$partial = $data[$n-1];
		$data[$n-1] = '_'.$data[$n-1];
		$partial_view = implode('/', $data);
	}
	
	/**
	 * Si es una ruta en crudo o la ruta tiene mas de 
	 * dos elementos para formar la direccion
	 **/
	if($n>2 || isset($params['partial_view'])) {
		$partial_file = join_path($views_dir, "$partial_view.$ext");
	} elseif($n==1) {
		$partial_file = join_path($views_dir, $module_name, $controller_name, "$partial_view.$ext");
	} elseif($n==2) {
		$partial_file = join_path($views_dir, $module_name, "$partial_view.$ext");
	}

	if(file_exists($partial_file)){
		extract(Kumbia::$models, EXTR_OVERWRITE);
		
		if(is_subclass_of(Dispatcher::get_controller(), "ApplicationController")){
			foreach(Dispatcher::get_controller() as $var => $value) {
				$$var = $value;
			}
		}

		if(isset($params[1])) {
			$$partial = $params[1];
		} elseif(isset($params['partial_view']) && isset($params[0])) {
			$$partial = $params[0];
		}
		
		include $partial_file;
	} else {
		throw new KumbiaException('Kumbia no puede encontrar la vista parcial: "'.$partial_view.'"', 0);
	}
}

function tr_break($x=''){
	static $l;
	if($x=='') {
		$l = 0;
		return;
	}
	if(!$l) {
		$l = 1;
	} else {
		$l++;
	}
	if(($l%$x)==0) {
		print "</tr><tr>";
	}
}

function br_break($x=''){
	static $l;
	if($x=='') {
		$l = 0;
		return;
	}
	if(!$l) {
		$l = 1;
	} else {
		$l++;
	}
	if(($l%$x)==0) {
		print "<br/>\n";
	}
}

function tr_color(){
	static $i;
	if(func_num_args()>1){
		$params = get_params(func_get_args());
	}
	if(!$i) {
		$i = 1;
	}
	print "<tr bgcolor=\"{$params[$i-1]}\"";
	if(count($params)==$i) {
		$i = 1;
	} else {
		$i++;
	}
	if(isset($params)){
		if(is_array($params)){
			foreach($params as $key => $value){
				if(!is_numeric($key)){
					print " $key = '$value'";
				}
			}
		}
	}
	print ">";
}

/**
 * Crea un Button que al hacer click carga
 * un controlador y una accion determinada
 *
 * @param string $caption
 * @param string $action
 * @param string $classCSS
 *
 * caption: texto del boton
 * action: accion a ejecutar
 *
 * @return HTML del Boton
 */
function button_to_action($caption, $action='', $classCSS=''){
	$params= is_array($caption) ? $caption : get_params(func_get_args());
	
	if(isset($params['caption'])) {
		$caption = $params['caption'];
		unset($params['caption']);
	} elseif(isset($params[0])) {
		$caption = $params[0];
	} else {
		$caption = '';
	}
	if(isset($params['action'])) {
		$action = $params['action'];
		unset($params['action']);
	} elseif(isset($params[1])) {
		$action = $params[1];
	} else {
		$action = '';
	}
	if(isset($params[2])) {
		$params['class'] = $params[2];
	}
	
	if(isset($params['onclick'])) {
		$params['onclick'].=';window.location="'.get_kumbia_url($action).'";';
	} else {
		$params['onclick'] = 'window.location="'.get_kumbia_url($action).'";';
	}
	
	return xhtml_tag('button', $params, "content: $caption");
}

/**
 * Crea un Button que al hacer click carga
 * con AJAX un controlador y una acci�n determinada
 *
 * @param string $caption
 * @param string $action
 * @param string $classCSS
 * @return HTML del Bot�n
 */
function button_to_remote_action($caption, $action='', $classCSS=''){
	$opts = is_array($caption) ? $caption : get_params(func_get_args());
	if(func_num_args()==2){
		$opts['action'] = $opts[1];
		$opts['caption'] = $opts[0];
	} else {
		if(!isset($opts['action'])||!$opts['action']) {
			$opts['action'] = $opts[1];
		}
		if(!isset($opts['caption'])||!$opts['caption']) {
			$opts['caption'] = $opts[0];
		}
	}
	if(!isset($opts['update'])){
		$opts['update'] = "";
	}
    
    if(!isset($opts['success'])){
        $opts['success'] = '';
    }
    if(!isset($opts['before'])){
        $opts['before'] = '';
    }
    if(!isset($opts['complete'])){
        $opts['complete'] = '';
    }

	$code = "<button onclick='AJAX.execute({action:\"{$opts['action']}\", container:\"{$opts['update']}\", callbacks: { success: function(){{$opts['success']}}, before: function(){{$opts['before']}} } })'";
	unset($opts['action']);
	unset($opts['success']);
	unset($opts['before']);
	unset($opts['complete']);
	foreach($opts as $k => $v){
		if(!is_numeric($k)&&$k!='caption'){
			$code.=" $k='$v' ";
		}
	}
	$code.=">{$opts['caption']}</button>";
	return $code;
}

/**
 * Crea un select que actualiza un container
 * usando una accion ajax que cambia dependiendo del id
 * selecionado en el select
 * @param string $id
 * @param array $data
 *
 * update: contenedor html a actualizar
 * container: contenedor html a actualizar
 * action: accion que recibe el parametro
 *
 * Nota: soporta todas las funciones del select_tag
 *
 * @return code
 */
function updater_select($name, $data=array()){
	$params = is_array($name) ? $name : get_params(func_get_args());
	
	/**
	 * Obtengo id, name y value
	 **/
	if(isset($params[0])) {
		$params = array_merge(get_id_and_name($params[0]), $params);
		if(!isset($params['selected'])) {
			$value = get_value_from_action($params[0]);
			if($value) {
				$params['selected'] = $value;
			}
		}
	}

	if(isset($params['container'])) {
		$update = $params['container'];
		unset($params['container']);
	} elseif(isset($params['update'])) {
		$update = $params['update'];
		unset($params['update']);
	} else {
		$update = '';
	}
	
	if(isset($params['action'])) {
		$action = $params['action'];
		unset($params['action']);
	} else {
		$action = '';
	}

	$onchange = "AJAX.viewRequest({action: '$action/'+$(\"{$params['id']}\").value, container: '$update'";
	$call = array();
	if(isset($params['before'])){
		$call["before"] = "before: function(){ {$params['before']} }";
		unset($params['before']);
	}
	if(isset($params['oncomplete'])){
		$call["oncomplete"] = "oncomplete: function(){ {$params['oncomplete']} }";
		unset($params['oncomplete']);
	}
	if(isset($params['success'])){
		$call["success"] = "success: function(){ {$params['success']} }";
		unset($params['success']);
	}
	if(count($call)){
		$onchange.=", callbacks: { ";
		$onchange.=implode(",", $call);
		$onchange.="}";
	}
	$onchange.="})";

	if(isset($params['onchange'])) {
		$params['onchange'].=';'.$onchange;
	} else {
		$params['onchange'] = $onchange;
	}

	return select_tag($params);
}

/**
 * Caja de texto con autocompletacion
 * 
 * @param string $name id de la caja de texto
 *
 * action: accion a ejecutar
 * after_update: despues de actualizar
 * message: mensaje mientras se carga
 *
 * @return string
 **/
function text_field_with_autocomplete($name){
	$params = is_array($name) ? $name : get_params(func_get_args());
	
	/**
	 * Obtengo id, name y value
	 **/
	if(isset($params[0])) {
		$params = array_merge(get_id_and_name($params[0]), $params);
		if(!isset($params['value'])) {
			$value = get_value_from_action($params[0]);
			if($value) {
				$params['value'] = $value;
			}
		}
	}
	
	$hash = md5(uniqid());

	if(isset($params['after_update'])) {
		$after_update = $params['after_update'];
		unset($params['after_update']);
	}else {
		$after_update = 'function(){}';
	}
	if(isset($params['action'])) {
		$action = $params['action'];
		unset($params['action']);
	}else {
		$action = '';
	}
	if(isset($params['message'])) {
		$message = $params['message'];
		unset($params['message']);
	}else {
		$message = 'Consultando..';
	}

	$code = text_field_tag($params);
	
	$code.= "
	<span id='indicator$hash' style='display: none'><img src='".KUMBIA_PATH."img/spinner.gif' alt='$message'/></span>
	<div id='{$params[0]}_choices' class='autocomplete'></div>
	<script type='text/javascript'>
	<!-- <![CDATA[
	new Ajax.Autocompleter(\"{$params['id']}\", \"{$params['id']}_choices\", \"".get_kumbia_url($action)."\", { minChars: 2, indicator: 'indicator$hash', afterUpdateElement : {$after_update}});
	// ]]> -->
	</script>
	";
	return $code;
}


#Other functions
function truncate($word, $number=0){
	if($number){
		return substr($word, 0, $number);
	} else {
		return rtrim($word);
	}
}

/**
 * Envia la salida en buffer al navegador
 *
 */
function content(){
	echo Kumbia::$content;
}

/**
 * Inserta un documento XHTML antes de una salida en buffer
 *
 * @param string $template
 */
function xhtml_template($template='template'){
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Kumbia PHP Framework</title>
<meta http-equiv="Content-type" content="text/html; charset='.Kumbia::get_active_app_charset().'" />'."\n";
print stylesheet_link_tag("style", 'use_variables: true');
kumbia::stylesheet_link_tags().
print '</head>
<body class="'.$template.'">';
	content();
print '
 </body>
</html>';

}

function tab_tag($tabs, $color='green', $width=800){

	switch($color){
		case 'blue':
		$col1 = '#E8E8E8'; $col2 = '#C0c0c0'; $col3 = '#000000';
		break;

		case 'pink':
		$col1 = '#FFE6F2'; $col2 = '#FFCCE4'; $col3 = '#FE1B59';
		break;

		case 'orange':
		$col1 = '#FCE6BC'; $col2 = '#FDF1DB'; $col3 = '#DE950C';
		break;

		case 'green':
		$col2 = '#EAFFD7'; $col1 = '#DAFFB9'; $col3 = '#008000';
		break;
	}


	print "
			<table cellspacing=0 cellpadding=0 width=$width>
			<tr>";
	$p = 1;
	$w = $width;
	foreach($tabs as $tab){
		if($p==1) $color = $col1;
		else $color = $col2;
		$ww = (int) ($width * 0.22);
		$www = (int) ($width * 0.21);
		print "<td align='center'
				  width=$ww style='padding-top:5px;padding-left:5px;padding-right:5px;padding-bottom:-5px'>
				  <div style='width:$www"."px;border-top:1px solid $col3;border-left:1px solid $col3;border-right:1px solid $col3;background:$color;padding:2px;color:$col3;cursor:pointer' id='spanm_$p'
				  onclick='showTab($p, this)'
				  >".$tab['caption']."</div></td>";
		$p++;
		$w-=$ww;
	}
	print "
			<script>
				function showTab(p, obj){
				  	for(i=1;i<=$p-1;i++){
					    $('tab_'+i).hide();
					    $('spanm_'+i).style.background = '$col2';
					}
					$('tab_'+p).show();
					obj.style.background = '$col1'
				}
			</script>
			";
	$p = $p + 1;
	//$w = $width/2;
	print "<td width=$w></td><tr>";
	print "<td colspan=$p style='border:1px solid $col3;background:$col1;padding:10px'>";
	$p = 1;
	foreach($tabs as $tab){
		if($p!=1){
			print "<div id='tab_$p' style='display:none'>";
		} else {
			print "<div id='tab_$p'>";
		}
		render_partial($tab['partial']);
		print "</div>";
		$p++;
	}
	print "<br></td><td width=30></td>";
	print "</table>";
}

function render_view($view){
	$params = get_params(func_get_args());

	if(isset($params['controller'])) {
		$controller_name = uncamelize($params['controller']);
	}elseif(preg_match("/^(.+)\/(.+)$/", $params[0], $data)) {
		$controller_name = $data[1];
		$view = $data[2];
	}else{
		$controller_name = '';
	}
	
	if(!isset($view)) $view = $params[0];
	
	if(defined('USE_SMARTY')) {
		$ext = 'tpl';
	} else {
		$ext = 'phtml';
	}
	
	$view_file = join_path(Kumbia::$active_views_dir, $controller_name, "$view.$ext");
	if(file_exists($view_file)){
		include $view_file;
	} else{
		throw new KumbiaException("La vista '$view' no existe o no se puede cargar");
	}
}

/**
 * Carga uno o varios helpers (soporta argumento variable)
 * @param string $helper nombre del helper a cargar
 **/
function use_helper($helper) {
	$helpers = func_get_args();
	foreach($helpers as $helper) {
		$file = join_path(Kumbia::$active_helpers_dir, "$helper.php");
		if(!file_exists($file)) {
			throw new KumbiaException("No se encontr&oacute; el Helper \"$helper\"",
				"Es necesario definir el archivo $helper.php para que el helper funcione correctamente");
		}
		include_once($file);
	}
}

/**
 * Ejecuta un script de javascript
 * @param string $s
 **/
function js_execute($s) {
	return xhtml_tag('script', 'type: text/javascript', "content: $s");
}

/**
 * Genera un alert de javascript
 * @param string $s
 **/
function js_alert($s) {
	return js_execute('alert("'.addslashes($s).'");');
}

/**
 * Campo para tipo hora
 * @param string $name id del campo
 * 
 * format: %h:%m:%s  (%h=hora, %m=minutos, %s=segundos)
 *
 * @return string 
 **/
function time_field_tag($name='') {
	$params = is_array($name) ? $name : get_params(func_get_args());

	$hours = array ('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05' , '06' => '06',
		'07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12', '13' => '13',
		'14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20',
		'21' => '21', '22' => '22', '23' => '23');
		
	$mins = array ('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05' , '06' => '06',
		'07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12', '13' => '13',
		'14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20',
		'21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27',
		'28' => '28', '29' => '29', '30' => '30', '31' => '31', '32' => '32', '33' => '33', '34' => '34',
		'35' => '35', '36' => '36', '37' => '37', '38' => '38', '39' => '39', '40' => '40', '41' => '41',
		'42' => '42', '43' => '43', '44' => '44', '45' => '45', '46' => '46', '47' => '47', '48' => '48', '49' => '49',
		'50' => '50', '51' => '51' , '52' => '52', '53' => '53', '54' => '54', '55' => '55', '56' => '56',
		'57' => '57', '58' => '58', '59' => '59' );
	
	if(isset($params['value'])) {
		$value = $params['value'];
		unset($params['value']);
	} else {
		$value = get_value_from_action($name);
	}
	
	if(!$value) {
		$value = '00:00:00';
	}
	
	$format = isset($params['format']) ? $params['format']: '%h:%m';
	$hidden = hidden_field_tag($params[0], "value: $value");
		
	if($value) {
		$value = explode(':', $value);
	}
		
	$data = get_id_and_name($params[0]);
	$code = '';
	$format = explode(':', $format);
	
	$onchange = "
		var hora = document.getElementById('{$data['id']}_h');
		var min = document.getElementById('{$data['id']}_m');
		var seg = document.getElementById('{$data['id']}_s');
		var value = '';
		if(hora) {
			value+=hora.value;
		} else {
			value+='00';
		}
		value+=':';
		if(min) {
			value+=min.value;
		} else {
			value+='00';
		}
		value+=':';
		if(seg) {
			value+=seg.value;
		} else {
			value+='00';
		}
		document.getElementById('{$data['id']}').value = value;
	";
	
	if(isset($params['onchange'])) {
		$params['onchange'].=$onchange;
	} else {
		$params['onchange'] = $onchange;
	}
	
	foreach($format as $f) {
		if($f=='%h') {
			if($code) {
				$code.=':';
			}
			if($value) {
				$params['selected'] = $value[0];
			}
			$params[1] = $hours;
			$code.=select_tag(array_merge($params, array('name'=>'', 'id'=>$data['id'].'_h')));
			unset($params['selected']);			
		} elseif($f=='%m') {
			if($code) {
				$code.=':';
			}
			if($value) {
				$params['selected'] = $value[1];
			}
			$params[1] = $mins;
			$code.=select_tag(array_merge($params, array('name'=>'', 'id'=>$data['id'].'_m')));
			unset($params['selected']);	
		} elseif($f=='%s') {
			if($code) {
				$code.=':';
			}
			if($value) {
				$params['selected'] = $value[2];
			}
			$params[1] = $mins;
			$code.=select_tag(array_merge($params, array('name'=>'', 'id'=>$data['id'].'_s')));
			unset($params['selected']);
		}
	}
	
	return $code.$hidden;
}

/**
 * Select tag para mes
 * @param string $name nombre del campo
 *
 * use_month_numbers: usar meses como numeros (true, si, yes)
 **/
function month_field_tag($name) {
	$params = is_array($name) ? $name : get_params(func_get_args());
	if(isset($params['use_month_numbers'])) {
		$meses = array('01'=>'01', '02'=>'02', '03'=>'03', '04'=>'04', '05'=>'05','06'=>'06', 
			'07'=>'07', '08'=>'08', '09'=>'09', '10'=>'10', '11'=>'11', '12'=>'12');
		unset($params['use_month_numbers']);
	} else {
		$meses = array('01'=>'Enero', '02'=>'Febrero', '03'=>'Marzo', '04'=>'Abril', '05'=>'Mayo','06'=>'Junio', 
			'07'=>'Julio', '08'=>'Agosto', '09'=>'Septiembre', '10'=>'Octubre', '11'=>'Noviembre', '12'=>'Diciembre');
	}
	$params[1] = $meses;
	return select_tag($params);
}
?>
