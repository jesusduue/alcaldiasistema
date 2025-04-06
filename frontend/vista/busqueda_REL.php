 <html lang="en">
 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">
   <title>Document</title>
 </head>
 <body>
   
 
 <?php
  $mysqli=new mysqli("localhost","root","","sist_alcaldia");

  $salida="";
  $query="SELECT num_factura, fecha, cedula_rif, razon_social, concepto, total_factura, ESTADO_FACT
      from factura 
      INNER JOIN contribuyente
      ON factura.cod_contribuyente = contribuyente.id_contribuyente ORDER BY num_factura";

  if(isset($_POST['consulta'])){
  $q= $mysqli->real_escape_string($_POST['consulta']);
  $query="SELECT num_factura,
  fecha,  
  cedula_rif,
  razon_social,
  concepto,
  total_factura,
  ESTADO_FACT
  FROM factura 
  INNER JOIN contribuyente
  ON factura.cod_contribuyente = contribuyente.id_contribuyente  WHERE 
  fecha LIKE '%".$q."%'
  OR cedula_rif LIKE '%".$q."%'
  OR razon_social LIKE '%".$q."%'
  OR concepto LIKE '%".$q."%'
  OR total_factura LIKE '%".$q."%'
  OR ESTADO_FACT LIKE '%".$q."%' "; 
  }

$resultado= $mysqli->query($query);
if($resultado->num_rows > 0){
  $salida.="<table class='container'>
  <thead class='head'>
      <tr>
        <td>N° FACTURA</td>
        <td>FECHA</td>
        <td>CEDULA/RIF</td>
        <td>RAZON SOCIAL</td>
        <td>CONCEPTO</td>
        <td>MONTO CANCELADO</td>
        <td>ESTADO</td>
      </tr>
  </thead>
  <tbody>
  ";

      $totalsum = 0; // Se inicializa la variable para sumar cada campo de total_factura
      $anuladosSum = 0; // Variable para sumar los montos de recibos anulados

  while($fila=$resultado->fetch_assoc()){

    $salida.="<tr class='sal'>
    
    <td>".$fila['num_factura']."</td>
    <td>".$fila['fecha']."</td>
    <td>".$fila['cedula_rif']."</td>
    <td>".$fila['razon_social']."</td>
    <td>".$fila['concepto']."</td>
    <td>".$fila['total_factura']."</td>
    <td>".$fila['ESTADO_FACT']."</td>

    </tr>";


      $totalsum += $fila['total_factura'];
      // Verificar si el estado es "nulo" y sumar al total de recibos anulados
      if ($fila['ESTADO_FACT'] == 'nulo') {
      $anuladosSum += $fila['total_factura'];
      }
      $totalFinal = $totalsum - $anuladosSum;

  }//fin while
    $salida.="<tr>
          <td colspan='5' class='footer'>SUB-TOTAL:</td>
          <td>$totalsum</td>
          <td colspan='2' ></td>
      </tr>;
       <tr>
          <td colspan='5' class='footer'>TOTAL RECIBOS ANULADOS:</td>
          <td>$anuladosSum</td>
          <td colspan='2'></td>
      </tr>
      
       <tr>
          <td colspan='5' class='footer'>TOTAL:</td>
          <td>$totalFinal</td>
          <td colspan='2'></td> 
        </tr>";

        $salida.="<tr><td><button onclick=location.href='../../backend/clase/formato_imprimir.php'>IMPRIMIR</button></td></tr>";
  $salida.="</tbody></table>";


}else{
  $salida.="<p class='msj'>No hay datos</p>";


}
echo $salida;
$mysqli->close();
?>

</body>
 </html>