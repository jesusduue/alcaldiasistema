<?php 
	require_once("../clase/contribuyente.class.php");

	$obj_cont= new contribuyente;
	$obj_cont->asignar_valor();

	switch ($_REQUEST["accion"]) {

		case 'insertar': $obj_cont->resultado=$obj_cont->insertar();
						 $obj_cont->mensaje();
				
			break;

		case 'actualizar': $obj_cont->resultado=$obj_cont->actualizar();
    				 
    		break;
			
	}

 ?>