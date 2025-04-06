<?php
// Genera una contraseña única usando random_bytes y la convierte a hexadecimal
function generar_contraseña_unica() {
    // Genera 16 bytes aleatorios y los convierte a una cadena hexadecimal
    return bin2hex(random_bytes(16));
}
#############################################################
        //ENCRIPTAR EL CODIGO
// Encripta un string de datos usando una contraseña
function encriptar_codigo($data, $password) {
    // Método de cifrado
    $method = 'aes-256-cbc';
    // Genera una clave a partir de la contraseña usando SHA-256
    $key = hash('sha256', $password, true);
    // Genera un vector de inicialización (IV) de 16 bytes
    $iv = openssl_random_pseudo_bytes(16);
    // Encripta los datos
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    // Devuelve la concatenación del IV y los datos encriptados, codificados en base64
    return base64_encode($iv . $encrypted);
}
//----------------------------------------------------------// 
        // DESENCRIPTAR EL CODIGO
 // Desencripta un string de datos usando una contraseña
function desencriptar_codigo($data, $password) {
    // Método de cifrado
    $method = 'aes-256-cbc';
    // Genera una clave a partir de la contraseña usando SHA-256
    $key = hash('sha256', $password, true);
    // Decodifica los datos de base64
    $data = base64_decode($data);
    // Extrae el IV (los primeros 16 bytes)
    $iv = substr($data, 0, 16);
    // Extrae los datos encriptados
    $encrypted = substr($data, 16);
    // Desencripta los datos
    return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
}

//------------------------------------------------------------//
    
    // Verifica si la licencia ha expirado y encripta el código en caso afirmativo
function verificar_licencia($fecha_expiracion, $password) {
    // Obtiene la fecha actual
    $fecha_actual = new DateTime();
    // Crea un objeto DateTime con la fecha de expiración
    $fecha_expiracion = new DateTime($fecha_expiracion);
    
    // Compara la fecha actual con la fecha de expiración
    if ($fecha_actual > $fecha_expiracion) {
        // Lee el contenido del archivo de código
        $codigo = file_get_contents('guardar_factura.php');
        // Encripta el contenido del archivo de código
        $codigo_encriptado = encriptar_codigo($codigo, $password);
        // Guarda el contenido encriptado en un nuevo archivo
        file_put_contents('guardar_factura.php', $codigo_encriptado);

        // Muestra un mensaje indicando que la licencia ha expirado y el código ha sido encriptado
        echo "<script languaje='javascript'>
            alert('LA LICENCIA A EXPIRADO. NO SE PUEDEN REALIZAR RECIBOS!');
            document.location='../../frontend/vista/'
            </script>";
        // Finaliza el script
        exit;
    }
}

// Uso de la función para verificar la licencia
$fecha_expiracion = '2025-12-31'; // Fecha de expiración de la licencia
$password = 'alcaldia1234'; // Contraseña única para la encriptación
verificar_licencia($fecha_expiracion, $password);

//-----------------------------------------------------------------------//

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene la contraseña del formulario
    $password = $_POST['password'];
    // Lee el contenido del archivo de código encriptado
    $codigo_encriptado = file_get_contents('guardar_factura.php');
    // Desencripta el contenido del archivo
    $codigo = desencriptar_codigo($codigo_encriptado, $password);
    // Guarda el contenido desencriptado en el archivo original
    file_put_contents('guardar_factura.php', $codigo);
    // Muestra un mensaje indicando que el código ha sido desencriptado
    echo "<script languaje='javascript'>
            alert('NUEVA LICENCIA HASTA 2026');
            document.location='../../frontend/vista/'
            </script>";
}


/*<!-- Formulario HTML para ingresar la contraseña única -->
<form method="post">
    <!-- Campo de entrada para la contraseña -->
    <input type="password" name="password" placeholder="Contraseña única">
    <!-- Botón para enviar el formulario -->
    <button type="submit">Desencriptar Código</button>
</form>*/


?>