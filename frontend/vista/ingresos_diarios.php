<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresos diarios</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
    <style>
        @media print {
        /* Reglas basicas de impresion */
        .oculto-impresion {
            display: none !important;
        }

        /* Reglas especificas para el modo detalle */
        body.print-mode-detail {
            background-color: white;
        }

        body.print-mode-detail main > :not(#detalle-container) {
            display: none !important;
        }

        body.print-mode-detail #detalle-container {
            position: static;
            width: 100%;
            margin: 0 !important;
            padding: 20px !important;
            border: none !important;
            box-shadow: none !important;
            background-color: white;
        }

        body.print-mode-detail #detalle-container .table-responsive {
            overflow: visible !important;
        }

        body.print-mode-detail #detalle-container tfoot {
            display: table-row-group;
        }

        body.print-mode-detail #detalle-container tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Ocultar el boton de imprimir dentro del reporte */
        body.print-mode-detail #btn-imprimir-detalle {
            display: none !important;
        }
    }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <section class="app-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start align-items-md-center mb-3">
                <div>
                    <h1 class="h4 mb-1">Ingresos diarios</h1>
                    <p class="app-subtitle mb-0 oculto-impresion">Consulta los recibos, totales netos y el detalle por rubros de cada d&iacute;a.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-app-outline" type="button" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                </div>
            </div>

            <form id="form-filtros" class="row g-3 align-items-end mb-4 oculto-impresion">
                <div class="col-md-4">
                    <label for="fecha_desde" class="form-label text-uppercase text-muted small">Desde</label>
                    <input type="date" class="form-control app-input" id="fecha_desde" name="fecha_desde">
                </div>
                <div class="col-md-4">
                    <label for="fecha_hasta" class="form-label text-uppercase text-muted small">Hasta</label>
                    <input type="date" class="form-control app-input" id="fecha_hasta" name="fecha_hasta">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-app-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Aplicar rango
                    </button>
                    <button type="button" id="btn-hoy" class="btn btn-app-outline">
                        Hoy
                    </button>
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="bg-white border rounded-3 p-3 h-100">
                        <p class="text-uppercase text-muted small mb-1">Total neto</p>
                        <h3 class="mb-0 text-primary" id="resumen-neto">-</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bg-white border rounded-3 p-3 h-100">
                        <p class="text-uppercase text-muted small mb-1">Monto bruto</p>
                        <h3 class="mb-0" id="resumen-bruto">-</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bg-white border rounded-3 p-3 h-100">
                        <p class="text-uppercase text-muted small mb-1">Anulados</p>
                        <h3 class="mb-0 text-danger" id="resumen-anulados">-</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="bg-white border rounded-3 p-3 h-100">
                        <p class="text-uppercase text-muted small mb-1">Recibos</p>
                        <h3 class="mb-0" id="resumen-recibos">-</h3>
                    </div>
                </div>
            </div>

            <div id="tabla-diaria" class="table-responsive app-table"></div>
        </section>

        <section class="app-card" id="detalle-container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1" id="titulo-detalle">Detalle por rubros</h2>
                    <p class="app-subtitle mb-0 oculto-impresion">Suma de ingresos por impuesto para la fecha seleccionada.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-app-outline" type="button" id="btn-imprimir-detalle">
                        <i class="bi bi-printer me-1"></i>Imprimir detalle
                    </button>
                    <span class="text-muted small text-uppercase">Fecha:</span>
                    <span class="badge bg-secondary" id="rubro-fecha-label">-</span>
                </div>
            </div>
            <div id="tabla-rubros" class="table-responsive app-table-detallePagos"></div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/ingresos_diarios.js"></script>
</body>

</html>
