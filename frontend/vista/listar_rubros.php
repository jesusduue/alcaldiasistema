<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rubros</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0px;

        }
        h1 {
            text-align: center;
            font-size: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;

        }
        table, th, td {
            border-bottom: 1px solid #000;


        }
         .rubro {
            text-align: center;
            font-size: 12px;
        }
        th, td {
            padding: 7.5px;
        

        }
        th {
            background-color: #fff;
        }
        .total {
            font-weight: bold;
        }
        .date {
            font-weight: bold;
            font-size: 11px;
        }
        .nom_impuesto{
        	font-size: 11px;
        }
        .tot_impuesto{
        	font-size: 11px;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Relación por Rubros</h1>
        <div class="date">
            Fecha: <?php echo htmlspecialchars($_POST['fecha_det_recibo']); ?>
        </div>
        <div class="table-container">
            <?php 
                require('../../backend/clase/detalle_recibo_class.php');
                $obj_det = new detalle_recibo;
                $fecha_det_recibo = $_POST['fecha_det_recibo'];

                $obj_det->fecha_det_recibo = $fecha_det_recibo;
                $obj_det->puntero = $obj_det->listar_fecha_rubro();

                // Definir todos los posibles impuestos
                $todos_impuestos = [
                    'DEUDA MOROSA DE CATASTRO',
                    'DEUDA ACTUAL DE CATASTRO',
                    'SOLVENCIA TIPO A',
                    'CEDULA CATASTRAL',
                    'SOLVENCIA MUNICIPAL',
                    'PATENTE DE INDUSTRIA Y COMERCIO',
                    'SOLVENCIA PATENTE',
                    'RENOVACION PATENTE',
		    'TRAMITACION PATENTE',
                    'SOLVENCIA LICORES',
                    'RENOVACION LICORES',
                    'RENOVACION LICENCIA DE LICORES',
                    'PUBLICIDAD Y PROPAGANDA',
                    'DECLARACION ESTIMADA',
                    'DEFINITIVA INGRESOS BRUTOS',
                    'ESPECTACULOS PUBLICOS',
                    'PATENTE DE VEHICULO FISCAL',
                    'PERMISO EVENTUAL POR ENFERMEDAD LICORES',
                    'PERMISO EVENTUAL ESPECTACULOS PUBLICOS LICORES',
                    'MULTAS LICORES',
                    'TRAMITACION LICENCIA DE LICORES',
                    'APUESTAS LICITAS',
                    'TRASPASO LICENCIA LICORES',
                    'PERMISO MUNICIPALES',
                    'PERMISO DE CONSTRUCCION',
                    'USO CONFORME',
                    'ZONIFICACION',
                    'PERMISO DE ROTURA',
                    'TERMINAL DE PASAJEROS LA FRIA',
                    'MULTAS INGENIERIA',
                    'PERMISO TEMPORAL DE LICORES',
                    'MANTENIMIENTO ANUAL',
                    'LEY DE CONTRATACIONES PUBLICAS',
                    'OTROS INGRESOS EXTRAORDINARIOS'
                ];
            ?>

            <table>
                <tr>
                    <th class="rubro">Rubros</th>
                    <th class="rubro">Total</th>
                </tr>
                <?php 
                    $impuestos = array(); // Array para almacenar los montos de impuestos agrupados
                    $total_general = 0; // Variable para almacenar el total general

                    while (($detalle_recibo = $obj_det->extraer_dato()) > 0) {
                        // Agregar el monto del impuesto A al array correspondiente
                        $impuestos[$detalle_recibo['nombre_impuesto_A']][] = $detalle_recibo['monto_impuesto_A'];
                        // Repetir el mismo proceso para los otros impuestos (B, C, D, E, F)
                        $impuestos[$detalle_recibo['nombre_impuesto_B']][] = $detalle_recibo['monto_impuesto_B'];
                        $impuestos[$detalle_recibo['nombre_impuesto_C']][] = $detalle_recibo['monto_impuesto_C'];
                        $impuestos[$detalle_recibo['nombre_impuesto_D']][] = $detalle_recibo['monto_impuesto_D'];
                        $impuestos[$detalle_recibo['nombre_impuesto_E']][] = $detalle_recibo['monto_impuesto_E'];
                        $impuestos[$detalle_recibo['nombre_impuesto_F']][] = $detalle_recibo['monto_impuesto_F'];
                    }

                    // Iterar sobre el array de todos los impuestos para imprimir la tabla
                    foreach ($todos_impuestos as $nombre_impuesto) {
                        // Verificar si el impuesto tiene montos asociados
                        if (isset($impuestos[$nombre_impuesto])) {
                            $total_monto = array_sum($impuestos[$nombre_impuesto]);
                        } else {
                            $total_monto = '-';
                        }
                        // Sumar al total general si el monto es un número
                        if (is_numeric($total_monto)) {
                            $total_general += $total_monto;
                        }
                        // Imprimir la fila de la tabla
                        echo "<tr>
                                <td class='nom_impuesto'>$nombre_impuesto</td>
                                <td class='tot_impuesto'>$total_monto</td>
                              </tr>";
                    }
                ?>
                <tr>
                    <td class="total">Total General</td>
                    <td class="total"><?php echo $total_general; ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
