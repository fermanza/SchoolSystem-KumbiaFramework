<?php
			
	class xperiodoscaptura extends ActiveRecord {
		
		public $antesAnterior = 32010;
		public $anterior = 12011;
		public $actual = 32011;
		public $proximo = 12012;
		
		// Regresar el objetivo, meta, evidencia de una actividad sustantiva, proporcionando su clave
		function get_unixtimestamp_from_partial($parcial){
			$xperiodoscaptura = new Xperiodoscaptura();
			foreach( $xperiodoscaptura -> find_all_by_sql(
					"select inicio
					from xperiodoscaptura
					where periodo = ".$this -> actual."
					and parcial = ".$parcial."
					limit 1" ) as $unixtime ){
				$unixtimestamp = $unixtime -> inicio;
			}
			return $unixtimestamp;
		} // get_unixtimestamp_from_partial($parcial)
	}
	
?>
