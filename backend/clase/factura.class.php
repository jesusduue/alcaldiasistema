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
// Obtener datos del formulario
//$num_factura = $_POST['num_factura'];
$fecha = $_POST['fecha'];
$id_usuario = 1;
$cod_contribuyente = $_POST['cod_contribuyente'];
$concepto = $_POST['concepto'];
$total_factura = $_POST['total_factura'];

// Insertar en la tabla RECIBO
 $sql_recibo = "INSERT INTO factura 
(fecha,id_usuario, cod_contribuyente, concepto, total_factura)
            VALUES 
               (
               	'$fecha',
               	'$id_usuario',
                '$cod_contribuyente',
                '$concepto',
                 $total_factura);";
$conn->query($sql_recibo);
 // $cod_recibo = $conn->insert_id; // Obtener el ID del recibo insertado

    // Insertar en la tabla DETALLE_RECIBO
    // Puedes repetir esta sección para cada detalle de impuesto

    $impuesto_A = $_POST['id_clasificadorA'];
    $monto_impuesto_A = $_POST['monto_impuesto_A'];

    $impuesto_B = $_POST['id_clasificadorB'];
    $monto_impuesto_B = $_POST['monto_impuesto_B'];

    $impuesto_C = $_POST['id_clasificadorC'];
    $monto_impuesto_C = $_POST['monto_impuesto_C'];

    $impuesto_D = $_POST['id_clasificadorD'];
    $monto_impuesto_D = $_POST['monto_impuesto_D'];

    $impuesto_E = $_POST['id_clasificadorE'];
    $monto_impuesto_E = $_POST['monto_impuesto_E'];

    $impuesto_F = $_POST['id_clasificadorF'];
    $monto_impuesto_F = $_POST['monto_impuesto_F'];
 
    // Verificar y asignar NULL si es necesario
    $impuesto_A = empty($impuesto_A) ? "NULL" : $impuesto_A;
    $monto_impuesto_A = empty($monto_impuesto_A) ? "NULL" : $monto_impuesto_A;

    $impuesto_B = empty($impuesto_B) ? "NULL" : $impuesto_B;
    $monto_impuesto_B = empty($monto_impuesto_B) ? "NULL" : $monto_impuesto_B;

    $impuesto_C = empty($impuesto_C) ? "NULL" : $impuesto_C;
    $monto_impuesto_C = empty($monto_impuesto_C) ? "NULL" : $monto_impuesto_C;

    $impuesto_D = empty($impuesto_D) ? "NULL" : $impuesto_D;
    $monto_impuesto_D = empty($monto_impuesto_D) ? "NULL" : $monto_impuesto_D;

    $impuesto_E = empty($impuesto_E) ? "NULL" : $impuesto_E;
    $monto_impuesto_E = empty($monto_impuesto_E) ? "NULL" : $monto_impuesto_E;

    $impuesto_F = empty($impuesto_F) ? "NULL" : $impuesto_F;
    $monto_impuesto_F = empty($monto_impuesto_F) ? "NULL" : $monto_impuesto_F;
   // $campo5 = empty($campo5) ? NULL : $campo5;
   //$campo6 = empty($campo6) ? NULL : $campo6;
////////////////////////////////////////////////////////////////////////////////////////////
     ECHO $sql_detalle = "INSERT INTO detalle_recibo 
     (fecha_det_recibo, cod_factura, impuesto_A, monto_impuesto_A, impuesto_B, monto_impuesto_B, impuesto_C, monto_impuesto_C,impuesto_D, monto_impuesto_D, impuesto_E, monto_impuesto_E, impuesto_F, monto_impuesto_F)
            VALUES 
            (CURDATE(), LAST_INSERT_ID(),
                '$impuesto_A', $monto_impuesto_A,
                '$impuesto_B', $monto_impuesto_B,
                '$impuesto_C', $monto_impuesto_C,
                '$impuesto_D', $monto_impuesto_D,
                '$impuesto_E', $monto_impuesto_E,
                '$impuesto_F', $monto_impuesto_F );";

    if ($conn->query($sql_detalle) === TRUE) {
        echo "<script languaje='javascript'>
        	alert('Recibo insertado exitosamente');
        	document.location='../../frontend/vista/'
        	</script>";
    } else {
       echo "Error al insertar detalles: " . $conn->error;
    }

$conn->close();

