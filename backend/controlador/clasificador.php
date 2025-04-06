<?php 
	require_once("../clase/clasificador.class.php");
	
	$obj_clas= new clasificador;
	$obj_clas->asignar_valor();

		switch ($_REQUEST["accion"]) {
			case 'insertar': $obj_clas->resultado=$obj_clas->insertar();
				             $obj_clas->mensajeCLAS();
				
				break;
			
			default:
				// code...
				break;
		}


 ?>