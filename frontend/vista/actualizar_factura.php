<?php 


// Incluir la clase detalle_recibo
require_once("../../backend/clase/detalle_recibo_class.php");

// Verificar si se ha enviado el ID
if (isset($_POST['num_factura'])) {
    $id_detalle_recibo = $_POST['num_factura'];
    
    // Crear una instancia de la clase detalle_recibo
    $obj_detalle_recibo = new detalle_recibo();
    
    // Llamar al método eliminar
    if ($obj_detalle_recibo->eliminar($id_detalle_recibo)) {
        echo "<script languaje='javascript'>
        alert('Detalle eliminado exitosamente');
        document.location='index.html'
        </script>";
    } else {
        echo "Error al eliminar el registro.";
    }
} else {
    echo "ID de detalle de recibo no proporcionado.";
}

// Redirigir a la lista de recibos o a otra página después de la eliminación
/*header("Location: index.html");**/
exit();


?>































/* Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sist_alcaldia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $num_factura = $_POST['num_factura'];
    $fecha = $_POST['fecha'];
    $cod_contribuyente = $_POST['cod_contribuyente'];
    $concepto = $_POST['concepto'];
    $total_factura = $_POST['total_factura'];
    $ESTADO_FACT = $_POST['ESTADO_FACT'];

    $impuesto_A = $_POST['impuesto_A'];
    $monto_impuesto_A = $_POST['monto_impuesto_A'];

    $impuesto_B = $_POST['impuesto_B'];
    $monto_impuesto_B = $_POST['monto_impuesto_B'];

    $impuesto_C = $_POST['impuesto_C'];
    $monto_impuesto_C = $_POST['monto_impuesto_C'];

    $impuesto_D = $_POST['impuesto_D'];
    $monto_impuesto_D = $_POST['monto_impuesto_D'];

    $impuesto_E = $_POST['impuesto_E'];
    $monto_impuesto_E = $_POST['monto_impuesto_E'];

    $impuesto_F = $_POST['impuesto_F'];
    $monto_impuesto_F = $_POST['monto_impuesto_F'];


    /*$resultado = modificar_factura();*/

 /*function modificar_factura()
{
    echo  $actualizar = "UPDATE 
            factura 
        SET 
            fecha = '$fecha',
			id_usuario = 1,
            cod_contribuyente = '$cod_contribuyente',
            concepto = '$concepto',
            total_factura = '$total_factura',
            ESTADO_FACT = '$ESTADO_FACT'
        WHERE 
            num_factura = '$num_factura';
        
        UPDATE 
            detalle_recibo 
        SET 
            impuesto_A = '$impuesto_A', monto_impuesto_A = '$monto_impuesto_A',
            impuesto_B = '$impuesto_B', monto_impuesto_B = '$monto_impuesto_B',
            impuesto_C = '$impuesto_C', monto_impuesto_C = '$monto_impuesto_C',
            impuesto_D = '$impuesto_D', monto_impuesto_D = '$monto_impuesto_D',
            impuesto_E = '$impuesto_E', monto_impuesto_E = '$monto_impuesto_E',
            impuesto_F = '$impuesto_F', monto_impuesto_F = '$monto_impuesto_F'
        WHERE 
            cod_factura = '$num_factura';";

    // Ejecutar la consulta y devolver los resultados
    $conn->query($actualizar);


    /*if ($resultado) {
        echo "<script language='javascript'>
              alert('modificado correctamente');
              document.location='#';
              </script>";
    } else {
        echo "Error al modificar";
    }*/


