
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos.css">
	<title>FACTURA</title>
	<style> /* estilos boton imprimir*/
    .btn {
        padding: 10px 20px;
        background-color: #2c8091;
        color: #fff;
        border: none;
        cursor: pointer;
         font-weight: bold;
         text-transform: uppercase;
        border-radius: 5px;
    }
    .btn:hover{
    	background-color: #246a78;
    	color: white;
    }
</style>
</head>
<body>

	<?php 

	    require('../../backend/clase/factura_clase_listar.php');

		$obj_fact=new factura;
		$obj_fact->asignar_valor();
		$obj_fact->puntero=$obj_fact->listar_reimprimir_factura();
		$factura=$obj_fact->extraer_dato();

?>
			<div id="formulario" class="formulario">
				<div class="container"> 
					<img  class="logo" src="../logo.png" alt="">
				<div class="menbrete ">
				<h6>REPÚBLICA BOLIVARIANA DE VENEZUELA</h6><br>
				<h6>ALCALDIA DEL MUNICIPIO GARCIA DE HEVIA</h6>
				<h6> &nbsp; La Fría - Edo. Táchira  </h5><br>
				<h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RIF:G-20001125-4 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </h6>
				<h6>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;DIRECCION DE HACIENDA &nbsp;&nbsp;&nbsp;&nbsp;</h6>
				</div>
					</div>

				<form id="myForm"  action="../../backend/clase/factura.update.php"  method="post">
						
						<div class="fila">
						<div class="fecha-recibo">FECHA:
						<input type="text" class="FECHA" name="fecha" value="<?php echo $factura['fecha']; ?>"></div>
						<div class="campo numero-factura">N° FACTURA:
						<input type="text" class="factura" name="num_factura" value="<?php echo  $factura['num_factura']; ?>" ></div>
						</div>

						<div class="fila-cont">
						<div class="codigo"> CODIGO:
						<input class="campo cod-inp" type="text" name="cod_contribuyente" required value="<?php echo $factura['id_contribuyente']; ?>" readonly> </div>

						<div class="cedula-rif">CEDULA/RIF:
						<input class="campo cedula" type="text" name="cedula_rif" required value="<?php echo $factura['cedula_rif']; ?>" readonly> </div>

						<div >
						<input type="text" class="nombre" name="razon_social" required value="<?php echo $factura['razon_social']; ?>" readonly> </div>
					</div>

					<div class="descripcion">
						<input type="text" class="concepto" name="concepto" value="<?php echo $factura['concepto']; ?>">
					</div>

					<label >TOTAL CANCELADO:</label>
        			<input  class="total" type="text" step="any" name="total_factura" readonly id="total" value="<?php echo $factura['total_factura']; ?>" ><br>

        			<label for="">ESTADO:</label>
        			<input class="nulo" type="text"  name="ESTADO_FACT" value="<?php echo $factura['ESTADO_FACT']; ?>">
        
        	<div>
        	<input class="btn oculto-impresion" type="submit" value="GUARDAR RECIBO"> 

        	</div>
			</form>

	
		</div>


</body>
</html>