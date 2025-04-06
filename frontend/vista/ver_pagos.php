<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGOS</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">

    <script>
function verificarFactura(num_factura) {
    // Realizar una solicitud AJAX al servidor para verificar la factura
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "verificar_factura.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var respuesta = JSON.parse(xhr.responseText);
            if (respuesta.existe) {
                // Si la factura existe, redirigir a modificar_factura.php
                window.location.href = "modificar_factura.php?num_factura=" + num_factura;
            } else {
                // Si la factura no existe, mostrar una alerta
                alert("La factura no existe en la base de datos.");
            }
        }
    };
    xhr.send("num_factura=" + num_factura);
}
</script>



</head>
<body>

<?php

//require('ENCRY_pagos.php');
// Asegúrate de tener una conexión a la base de datos aquí
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sist_alcaldia";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
 //verificar_licencia($fecha_expiracion, $password);
    
if (isset($_GET['id_contribuyente'])) {
    $id_contribuyente = $_GET['id_contribuyente'];

    // Sanitiza y valida el id_contribuyente (cambia esto según tu lógica)
    $id_contribuyente = filter_var($id_contribuyente, FILTER_VALIDATE_INT);

    if ($id_contribuyente !== false) {
        // Escapa los datos (importante para prevenir la inyección SQL, pero no suficiente por sí solo)
        $id_contribuyente = mysqli_real_escape_string($conn, $id_contribuyente);

        // Ejecuta la consulta SQL directamente
         $consulta = "SELECT f.num_factura, f.fecha, c.razon_social, c.cedula_rif, f.concepto, f.total_factura, f.ESTADO_FACT
                     FROM factura f
                     JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
                     WHERE f.cod_contribuyente = $id_contribuyente";
        /*$consulta = "SELECT * FROM factura WHERE cod_contribuyente = $id_contribuyente";*/

        $result = mysqli_query($conn, $consulta);

        // Verifica si hay resultados
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='container'>
                        <h2 class='mb-4 text-center'>PAGOS DEL CONTRIBUYENTE</h2>
                        <table class='table table-striped'>
                            <thead class='bg-primary text-white'>
                            <tr>
                                <td>N° FACTURA</td>
                                <td>FECHA</td>
                                <td>CEDULA/RIF</td>
                                <td>RAZON SOCIAL</td>
                                <td>DESCRIPCION</td>
                                <td>PAGO</td>
                                <td>ESTADO </td>
                                <td coslpan='2'>PROCESOS </td>
                                </thead>
                            </tr>
                            <tbody>";

                // Itera sobre los resultados y muestra la información
                // <td><a class='btn btn-primary' href='modificar_factura.php?num_factura={$fila['num_factura']}'>MODIFICAR</a></td>
                while ($fila = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                          <td>{$fila['num_factura']}</td>
                          <td>{$fila['fecha']}</td>
                          <td>{$fila['cedula_rif']}</td>
                          <td>{$fila['razon_social']}</td>
                          <td>{$fila['concepto']}</td>
                          <td>{$fila['total_factura']}</td>
                          <td>{$fila['ESTADO_FACT']}</td>
                          <td><a class='btn btn-primary' href='reimprimir_factura.php?num_factura={$fila['num_factura']}'>IMPRIMIR</a></td>
                          <td><button class='btn btn-primary' onclick='verificarFactura(num_factura={$fila['num_factura']})'>Modificar Factura</button></td>
                          <td><a class='btn btn-primary' href='anular_factura.php?num_factura={$fila['num_factura']}'>ANULAR</a></td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='container'>
                        <p class='text-center'>No hay pagos registrados para este contribuyente.</p>
                      </div>";
            }

            // Libera el resultado
            mysqli_free_result($result);
        } else {
            echo "<div class='container'>
                    <p class='text-center'>Error al ejecutar la consulta: " . mysqli_error($conn) . "</p>
                  </div>";
        }
    } else {
        echo "<div class='container'>
                <p class='text-center'>ID de contribuyente no válido.</p>
              </div>";
    }

    // Cierra la conexión a la base de datos
    mysqli_close($conn);
} else {
    echo "<div class='container'>
            <p class='text-center'>Parámetro id_contribuyente no proporcionado.</p>
          </div>";
}
?>

    
</body>
</html>