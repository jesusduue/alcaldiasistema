
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>LISTA FACTURAS</title>
		<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
		<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">
		<style>
			.titulo{
				text-align: center;
				font-weight: bold;
			}
			.footer{
				text-align: right;
				text-transform: uppercase;
			}
			.TXTO{
			font-size: 11px;
			}
		</style>
	</head>
	<body>


		<?php
			require('../../backend/clase/factura_clase_listar.php');
			$obj_fact= new factura;
			$fecha = $_POST['fecha'];
			$obj_fact->fecha=$fecha;
			//$obj_fact->asignar_valor();
			$obj_fact->puntero=$obj_fact->listar_fecha();
		?>
		<div class="titulo"><span>RELACION DE INGRESOS DIARIOS</span></div>

		<table class="container">
			<thead class="head">
			<tr>
				<td>N° FACTURA</td>
				<td>FECHA</td>
				<td>CEDULA/RIF</td>
				<td>RAZON SOCIAL</td>
				<td>CONCEPTO</td>
				<td>MONTO CANCELADO</td>
				<td colspan="2"></td>
			</tr>
			</thead>
			<?php
			$totalsum = 0; // Se inicializa la variable para sumar cada campo de total_factura
			$anuladosSum = 0; // Variable para sumar los montos de recibos anulados
			$i = 0;
			while (($factura = $obj_fact->extraer_dato()) > 0) {
			echo "<tr class='TXTO'>
					<td>$factura[num_factura]</td>
					<td>$factura[fecha]</td>
					<td>$factura[cedula_rif]</td>
					<td>$factura[razon_social]</td>
					<td>$factura[concepto]</td>
					<td>$factura[total_factura]</td>
					<td>$factura[ESTADO_FACT]</td>
			</tr>";
			$totalsum += $factura['total_factura'];
			// Verificar si el estado es "nulo" y sumar al total de recibos anulados
			if ($factura['ESTADO_FACT'] == 'nulo') {
			$anuladosSum += $factura['total_factura'];
			}
			$i++;
			} #fin while
			if ($i == 0) {
			echo "<tr><td colspan='8'>No hay registros</td></tr>";
			} else {
			echo "<tr>
					<td colspan='5' class='footer'>SUB-TOTAL:</td>
					<td>$totalsum</td>
					<td colspan='2'></td>
			</tr>";
			echo "<tr>
					<td colspan='5' class='footer'>TOTAL RECIBOS ANULADOS:</td>
					<td>$anuladosSum</td>
					<td colspan='2'></td>
			</tr>";
			// Calcular y mostrar el TOTAL FINAL (restando recibos anulados)
			$totalFinal = $totalsum - $anuladosSum;
			echo "<tr>
					<td colspan='5' class='footer'>TOTAL:</td>
					<td>$totalFinal</td>
					<td colspan='2'></td>
			</tr>";
			}
			?>


	</table>
</body>
</html>