
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

</script>

</head>
<body>

	<?php 
	    require('../../backend/clase/contribuyente.class.php');
	    require('../../backend/clase/clasificador.class.php');
	    require('../../backend/clase/factura_clase_listar.php');
		require('ENCRYPTAR.php');
		
	    $obj_clas=new clasificador;
		$obj_cont=new contribuyente;
		$obj_fact=new factura;
		$obj_cont->asignar_valor();
		$obj_cont->puntero=$obj_cont->filtrar();
		$contribuyente=$obj_cont->extraer_dato();

		$obj_fact->asignar_valor();
		$obj_fact->puntero=$obj_fact->filtrar();
		$factura=$obj_fact->extraer_dato();

		//$obj_clas->obj_clas="";
		/* METODO PARA LISTAR CADA UNA DE LAS OPCIONES*/
		$objA_clas=new clasificador;
		$objA_clas->puntero=$objA_clas->listar();
    /////////////////////////////////////////
		$objB_clas=new clasificador;
		$objB_clas->puntero=$objB_clas->listar();
		/////////////////////////////////////////
		$objC_clas=new clasificador;
		$objC_clas->puntero=$objC_clas->listar();
		/////////////////////////////////////////
		$objD_clas=new clasificador;
		$objD_clas->puntero=$objD_clas->listar();
		/////////////////////////////////////////
		$objE_clas=new clasificador;
		$objE_clas->puntero=$objE_clas->listar();
		/////////////////////////////////////////
		$objF_clas=new clasificador;
		$objF_clas->puntero=$objF_clas->listar();
		/////////////////////////////////////////
		function fecha_actual(){
			  date_default_timezone_set('america/caracas');
			  $fecha_hora = date('Y-m-d');
			  return $fecha_hora;
			}
			$fecha_hora =fecha_actual();
			verificar_licencia($fecha_expiracion, $password);

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

				<form id="myForm"   action="../../backend/clase/factura.class.php" method="post">
						
						<div class="fila">
						<div class="fecha-recibo">FECHA:
						<input type="text" class="FECHA" name="fecha" value="<?php echo $fecha_hora; ?>" ></div>
						<div class="campo numero-factura">N° FACTURA:
						<input type="text" class="factura" value="<?php echo $factura['num_factura']+1; ?>"  ></div>
						</div>

						<div class="fila-cont">
						<div class="codigo"> CODIGO:
						<input class="campo cod-inp" type="text" name="cod_contribuyente" required value="<?php echo '00' . $contribuyente['id_contribuyente']; ?>" readonly> </div>

						<div class="cedula-rif">CEDULA/RIF:
						<input class="campo cedula" type="text" name="cedula_rif" required value="<?php echo $contribuyente['cedula_rif']; ?>" readonly> </div>

						<div >
						<input type="text" class="nombre" name="razon_social" required value="<?php echo $contribuyente['razon_social']; ?>" readonly> </div>
					</div>

					<div class="descripcion">
						<input type="text" class="concepto" name="concepto" value="DESCRIPCION">
					</div>

					<div class="">
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

						<div class="clasificador-monto">
					<select class="campo clasificador" name="id_clasificadorA" required >
						<?php  
						$i=0;
						while (($clasificadorA=$objA_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorA[id_clasificador]'>$clasificadorA[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" REQUIRED step="any" id="monto_impuesto_A" name="monto_impuesto_A" class="monto" onchange="sumar()"> </div>

						</div>
					<!--''''''''''''''''''''''''''''''''' -->
						<div class="clasificador-monto">
					
					<select class="campo clasificador" name="id_clasificadorB" >
						<?php  
						$i=0;
						while (($clasificadorB=$objB_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorB[id_clasificador]'>$clasificadorB[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" step="any" id="monto_impuesto_B" name="monto_impuesto_B" class="monto" onchange="sumar()"> </div>

						</div>
					<!--''''''''''''''''''''''''''''''''' -->	
						<div class="clasificador-monto">
					
					<select class="campo clasificador" name="id_clasificadorC" >
						<?php  
						$i=0;
						while (($clasificadorC=$objC_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorC[id_clasificador]'>$clasificadorC[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" step="any" id="monto_impuesto_C" name="monto_impuesto_C" class="monto" onchange="sumar()"> </div>
						</div>						
					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
						<div class="clasificador-monto">
					
					<select class="campo clasificador" name="id_clasificadorD" >
						<?php  
						$i=0;
						while (($clasificadorD=$objD_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorD[id_clasificador]'>$clasificadorD[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" step="any" id="monto_impuesto_D" name="monto_impuesto_D" class="monto" onchange="sumar()"> </div>
						</div>						
					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
						<div class="clasificador-monto">
					
					<select class="campo clasificador" name="id_clasificadorE" >
						<?php  
						$i=0;
						while (($clasificadorE=$objE_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorE[id_clasificador]'>$clasificadorE[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" step="any"	id="monto_impuesto_E" name="monto_impuesto_E" class="monto" onchange="sumar()"> </div>
						</div>						
					<!--''''''''''''''''''''''''''''''''' -->
					<!--''''''''''''''''''''''''''''''''' -->	
						<div class="clasificador-monto">
					
					<select class="campo clasificador" name="id_clasificadorF" >
						<?php  
						$i=0;
						while (($clasificadorF=$objF_clas->extraer_dato())>0){
						    echo "<option value='$clasificadorF[id_clasificador]'>$clasificadorF[nombre]</option>";
							}
							$i++;
						?>
					</select>
						<div class="campo">
						<input type="text" step="any" id="monto_impuesto_F" name="monto_impuesto_F" class="monto" onchange="sumar()"> </div>
						</div>						
					<!--''''''''''''''''''''''''''''''''' -->
				    </div>

					<label >TOTAL A CANCELAR:</label>
        <input  class="total" type="text" step="any" name="total_factura"  id="total" value="0" ><br>
        
        	<div>
        <!--	<input class="btn oculto-impresion" type="submit" value="GUARDAR RECIBO"> -->
        	
        	<input class="btn oculto-impresion" type="reset" value="LIMPIAR">
			<input type="submit" class="btn oculto-impresion" onclick="imprimirPantalla()" value="imprimir">
        	</div>
			</form>

				<!--	<button class="btn oculto-impresion" onclick="imprimirPantalla()"> imprimir </button> -->
		<!--	<button  class="oculto-impresion" onclick="descargarPDF()">Descargar PDF</button> -->
		</div>

<script>
function imprimirPantalla() {
    window.print();
}
</script>

</body>
</html>