<?php
			
	class PromediosyestatusController extends ApplicationController {
		
		$anterior = 12010;
		$actual = 32010;
		$promxio = 12011;
		
		
		function index(){
		}
		
		function calculando(){
			$xalumnocursos = new Xalumnocursos();
			
			$registros = $this -> post("registros");
			
			// 1. Checar si el semestre pasado estudi� en Tonala o Colomos
			// 2. Consultar todos los cursos de Xalumnocursos y Xextraordinario para sacar su promedio
			// 3. Consultar todos los extraordinarios y titulos que tiene (si es que tiene)
			//de Xextraordinarios o Xtextraordinarios, seg�n sea el caso.
			// 4. Checar su Estatus, Seg�n el Sig criterio:
			//* Si tiene todas en Ordinario el alumno es regular.
			//* Si tiene menos o igual a 6 extraordinarios y paso todos los extras, el alumno es regular.
			//* Si paso todas en este semestre y no debe ninguna en xextraordinarios es regular.
			//* Si reprobo menos de 6 extras, se hace irregular.
			//**Puede reprobar por calificaci�n o por faltas. EXTRAORDINARIO, EXTRAORDINARIO POR FALTAS.
			//* Si tiene materias en titulo sin calificaci�n o reprobadas ser� condicionado.
			//* Si tiene materias como BAJA DEFINITIVA su estatus ser� BAJA DEFINITIVA.
			
			foreach( $xalumnocursos -> find_all_by_sql("
					select xal.curso, xal.calificacion, xal.situacion
					from xalumnocursos xal
					where periodo = ".$this -> actual) as $xal ){
				
			}
		}
	}
	
?>