<?php
	class AspirantesExani extends ActiveRecord {
		
		function get_last_id(){
			foreach( $this->find_all_by_sql(
				"select max(id) maxid
				from aspirantes_exani" ) as $resulset){
				return $resulset->maxid;
			}
		} // function get_last_id()
		
	}
?>