<?php
			
	class VisitasController extends ApplicationController {
		
		public $antesAnterior=12009;
		public $anterior	= 32009;
		public $actual		= 12010;
		public $proximo		= 32010;
		
		function index(){
			
		}
		
		function incrementar( $idSeccion ){
			
			$secciones	= new VisitasSecciones();
			$contador	= new VisitasContador();
			
			$day = date ("d");
			$month = date ("m");
			$year = date ("Y");
			$hour = date ("H");
			$minute = date ("i");
			$second = date ("s");
			// 2010-04-18 8:33:50
			
			if( $month < 10 )
				$month = substr($month, 1, 1);
			
			$ultimaVisita = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			
			// Buscar si ya existen visitas a esa sección en ese mes y año.
			// Se creó de está manera ya que muchas estadísticas de visitas
			//se piden por mes.
			if( $counter = $contador -> find_first( "visitas_secciones_id = ".$idSeccion." 
					and mes = ".$month." and anio = ".$year ) ){
				
				$counter -> contador_visitas += 1;
				
				$counter -> ultima_visita = $ultimaVisita;
				
				$counter -> save();
				
			}
			else{
				
				$contador -> visitas_secciones_id = $idSeccion;
				$contador -> mes = $month;
				$contador -> anio = $year;
				$contador -> contador_visitas = 1;
				$contador -> ultima_visita = $ultimaVisita;
				
				$contador -> create();
			}
			
		} // function incrementar( $idSeccion )
		
		function verVisitas(){
			
			
			
		} // function verVisitas()
	}
?>