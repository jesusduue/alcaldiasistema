<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>INGRESO FECHA</title>
  <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">
</head>  
<body>
  


 <?php
  $mysqli=new mysqli("localhost","root","","sist_alcaldia");

  $salida="";
  $query="SELECT *FROM contribuyente ORDER By id_contribuyente";

  if(isset($_POST['consulta'])){
  $q= $mysqli->real_escape_string($_POST['consulta']);
  $query="SELECT id_contribuyente,  
  cedula_rif,
  razon_social,
  estado_cont
  FROM contribuyente WHERE 
  cedula_rif LIKE '%".$q."%'
  OR razon_social LIKE '%".$q."%'
  OR estado_cont LIKE '%".$q."%'
   "; 
  }

$resultado= $mysqli->query($query);
if($resultado->num_rows > 0){
  $salida.="<table class='container'>
  <thead class='head'>
      <tr>
        <td>CODIGO</td>
        <td>CEDULA/RIF</td>
        <td>RAZON SOCIAL</td>
        <td>ESTADO</td>
        <td colspan='2'>Procesos</td>
        <td></td>
      </tr>
  </thead>
  <tbody>
  ";

  while($fila=$resultado->fetch_assoc()){

    $salida.="<tr class='sal'>
    
    <td>".$fila['id_contribuyente']."</td>
    <td>".$fila['cedula_rif']."</td>
    <td>".$fila['razon_social']."</td>
    <td>".$fila['estado_cont']."</td>
    <td><a href='guardar_factura.php?accion=actualizar&id_contribuyente=$fila[id_contribuyente]' class='btn btn-primary'>GENERAR RECIBO</a></td>
   
    <td><a href='ver_pagos.php?id_contribuyente=$fila[id_contribuyente]' class='btn btn-info'>VER PAGOS</a></td>

    </tr>";

  }//cambiar los enlaces de editar y eliminar
  $salida.="</tbody></table>";


}else{
  $salida.="<p class='msj'>No hay datos</p>";


}
echo $salida;
$mysqli->close();
?>

</body>
</html>