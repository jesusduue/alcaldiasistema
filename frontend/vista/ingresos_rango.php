<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresos por rango</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
    <style>
        @media print {
            /* Ajustes para que las tablas se paginen completas al imprimir */
            .app-table {
                overflow: visible !important;
            }

            .app-table tfoot {
                display: table-row-group;
            }

            .app-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
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
                    <h1 class="h4 mb-1">Ingresos por rango</h1>
                    <p class="app-subtitle mb-0 oculto-impresion">Totales netos, anulados y rubros sumados en el rango seleccionado. Incluye número de contribuyentes.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 oculto-impresion">
                    <button class="btn btn-sm btn-app-outline" type="button" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                </div>
            </div>

            <form id="form-filtros" class="row g-3 align-items-end mb-4">
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
                        <p class="text-uppercase text-muted small mb-1">Contribuyentes</p>
                        <h3 class="mb-0" id="resumen-contribuyentes">-</h3>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-sm btn-app-primary" type="button" id="btn-ver-rubros">Detalle por rubros</button>
                <button class="btn btn-sm btn-app-outline" type="button" id="btn-ver-pagos">Detalle de pagos</button>
            </div>

            <div class="table-responsive app-table app-table-detallePagos" id="contenedor-rubros">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Rubro / Impuesto</th>
                            <th scope="col" class="text-end">Monto (Bs)</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-rubros">
                        <tr>
                            <td colspan="2" class="text-center py-4">Selecciona un rango para ver el detalle.</td>
                        </tr>
                    </tbody>
                    <tfoot id="tabla-rubros-total"></tfoot>
                </table>
            </div>

            <div class="table-responsive app-table app-table-detallePagos" id="contenedor-pagos">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">N° factura</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Contribuyente</th>
                            <th scope="col" class="text-end">Monto (Bs)</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-pagos">
                        <tr>
                            <td colspan="5" class="text-center py-4">Selecciona un rango para ver el detalle.</td>
                        </tr>
                    </tbody>
                    <tfoot id="tabla-pagos-total"></tfoot>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/ingresos_rango.js"></script>
</body>

</html>
