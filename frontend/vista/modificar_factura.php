
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_update_factura.css">
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

	<script type="text/javascript">
    
    function sumar()
{
  const $total = document.getElementById('total');
  let subtotal = 0;
  [ ...document.getElementsByClassName( "monto" ) ].forEach( function ( element ) {
    if(element.value !== '') {
      subtotal += parseFloat(element.value);
    }
  });
  $total.value = subtotal;
}
////////////////////////////////////////////////

function imprimirPantalla() {
    window.print();
}



</script>

</head>
<body>

	<?php 

	    require('../../backend/clase/factura_clase_listar.php');


		$obj_fact=new factura;
		$obj_fact->asignar_valor();
		$obj_fact->puntero=$obj_fact->mostrar_factura();
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

				<form id="myForm" action="actualizar_factura.php"  method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta factura?');" >

						<div class="fila">
						<div class="fecha-recibo">FECHA:
						<input type="text" class="FECHA" name="fecha" value="<?php echo $factura['fecha']; ?>"></div>
						<div class="campo numero-factura">N° FACTURA:
						<input type="text" class="factura" name="num_factura" value="<?php echo $factura['num_factura']; ?>" ></div>
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


					
					<!--''''''''''''''''''''''''''''''''' -->
						<div>
							<span>DETALLE</span>
							<span> ----</span>
							<span> ----</span>
							<span> ----</span>
							<span> ----</span>
							<span> ----</span>
							<span> ----</span>
					
							<span>MONTO</span>
						</div>
				<div class="">
					<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_A" readonly value="<?php echo $factura['nombre_impuesto_A']; ?>"  > 
			
					<input type="text"  class="monto" name="monto_impuesto_A" value="<?php echo $factura['monto_impuesto_A']; ?>" > 

					</div>

					<!--''''''''''''''''''''''''''''''''' -->
					
						<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_B" readonly   value="<?php echo $factura['nombre_impuesto_B']; ?>" required >
						
						<input type="text" class="monto" name="monto_impuesto_B" value="<?php echo $factura['monto_impuesto_B']; ?>" >
					
						</div>
					<!--''''''''''''''''''''''''''''''''' -->	
					
					<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_C" readonly  value="<?php echo $factura['nombre_impuesto_C']; ?>" required >
					
						<input type="text"  class="monto" name="monto_impuesto_C" value="<?php echo $factura['monto_impuesto_C']; ?>" > 
							
				</div>


					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
					
						<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_D" readonly  value="<?php echo $factura['nombre_impuesto_D']; ?>" required >
						
						<input type="text" class="monto" name="monto_impuesto_D" value="<?php echo $factura['monto_impuesto_D']; ?>" > 				
					</div>

					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
					
						<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_E" readonly  value="<?php echo $factura['nombre_impuesto_E']; ?>" required >
						
						<input type="text" class="monto" name="monto_impuesto_E" value="<?php echo $factura['monto_impuesto_E']; ?>" > 
						</div>
	
					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
					
						<div class="clasificador-monto">
					<input class="nom_impuesto" name="impuesto_F" readonly  value="<?php echo $factura['nombre_impuesto_F']; ?>" required >

						<input type="text" class="monto" name="monto_impuesto_F" value="<?php echo $factura['monto_impuesto_F']; ?>" > 
					</div>
	
					<!--''''''''''''''''''''''''''''''''' -->
				    </div>

					<label >TOTAL CANCELADO:</label>
        <input  class="total" type="text" step="any" name="total_factura" readonly id="total" value="<?php echo $factura['total_factura']; ?>" ><br>

        
        	<div>

        	<input class="btn oculto-impresion" type="submit" value="ELIMINAR"> 
        <!--	<button class="btn oculto-impresion" onclick="imprimirPantalla()"> imprimir </button> -->
        
        	</div>
			</form>

		</div>


</body>
</html>