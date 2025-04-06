<?php 
  
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sist_alcaldia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

    $num_factura = $_POST['num_factura'];
    $fecha = $_POST['fecha'];
    $cod_contribuyente = $_POST['cod_contribuyente'];
    $concepto = $_POST['concepto'];
    $total_factura = $_POST['total_factura'];
    $ESTADO_FACT= $_POST['ESTADO_FACT'];

   echo $update="UPDATE 
            factura 
        SET 
            fecha = '$fecha',
            id_usuario = 1,
            cod_contribuyente = '$cod_contribuyente',
            concepto = '$concepto',
            total_factura = '$total_factura',
            ESTADO_FACT = '$ESTADO_FACT'
        WHERE 
            num_factura = '$num_factura';";

    $conn->query($update);
// mensaje luego de actualizar datos
    if ($conn->query($update) === TRUE) {
        echo "<script languaje='javascript'>
        	alert('Recibo Actualizado exitosamente');
        	document.location='../../frontend/vista/'
        	</script>";
    } else {
       echo "Error: " . $conn->error;
    }

$conn->close();

/*
*/

?>