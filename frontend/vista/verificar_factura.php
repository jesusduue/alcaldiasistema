<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sist_alcaldia");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el número de factura enviado por POST
$num_factura = $_POST['num_factura'];

// Consultar la base de datos para verificar si la factura existe
$consulta = $conexion->prepare("SELECT COUNT(*) as existe FROM detalle_recibo WHERE id_detalle_recibo = ?");
$consulta->bind_param("s", $num_factura);
$consulta->execute();
$resultado = $consulta->get_result();
$fila = $resultado->fetch_assoc();

// Devolver una respuesta JSON
echo json_encode(['existe' => $fila['existe'] > 0]);

// Cerrar la conexión
$consulta->close();
$conexion->close();
?>