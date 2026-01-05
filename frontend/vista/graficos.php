<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos | Impuestos y Contribuyentes</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
    <style>
        .chart-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 6px 12px -6px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-placeholder {
            min-height: 320px;
        }

        .filter-chip {
            border: 1px dashed var(--color-border);
            padding: 6px 10px;
            border-radius: 6px;
            background: #f8fafc;
            color: var(--color-text-head);
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 320px;
        }

        .chart-wrapper.lg {
            height: 380px;
        }

        .chart-wrapper.xl {
            height: 440px;
        }

        .chart-wrapper.sm {
            height: 260px;
        }

        @media (max-width: 768px) {
            .chart-wrapper,
            .chart-wrapper.lg {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <header class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h4 mb-1">Panel de Gráficos</h1>
                <p class="app-subtitle mb-0">Visualiza ingresos, anulaciones y rubros. Ajusta el rango de fechas en tiempo real.</p>
            </div>
<!--             <div class="d-flex align-items-center gap-2">
                <span class="filter-chip">Datos dinámicos</span>
                <span class="filter-chip">Actualizable al vuelo</span>
            </div> -->
        </header>

        <section class="app-card mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-uppercase text-muted small">Desde</label>
                    <input type="date" class="form-control app-input" id="fecha_desde">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-uppercase text-muted small">Hasta</label>
                    <input type="date" class="form-control app-input" id="fecha_hasta">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-app-primary w-100" id="btn-aplicar">
                        <i class="bi bi-funnel me-1"></i>Aplicar rango
                    </button>
                    <button class="btn btn-app-outline" id="btn-hoy">Hoy</button>
                </div>
            </div>
        </section>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Total bruto</p>
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
                    <p class="text-uppercase text-muted small mb-1">Total neto</p>
                    <h3 class="mb-0 text-primary" id="resumen-neto">-</h3>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Contribuyentes</p>
                    <h3 class="mb-0" id="resumen-contribuyentes">-</h3>
                </div>
            </div>
        </section>

        <!-- Nuevos Gráficos -->
        <section class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" id="titulo-mensual">Ingresos Mensuales</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="periodo" id="btn-mensual" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary" for="btn-mensual">Mensual</label>
                            <input type="radio" class="btn-check" name="periodo" id="btn-trimestral" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="btn-trimestral">Trimestral</label>
                        </div>
                    </div>
                    <div class="chart-wrapper lg">
                        <canvas id="chart-mensual"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Distribución por Tipo</h5>
                        <button class="btn btn-sm btn-outline-primary" id="btn-exportar-dist">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Exportar
                        </button>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="chart-distribucion"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-lg-7">
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Ingresos diarios</h5>
                        <small class="text-muted">Neto (bruto - anulados)</small>
                    </div>
                    <div class="chart-wrapper lg">
                        <canvas id="chart-line"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="chart-card mb-4 mb-lg-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Estado de facturas</h5>
                        <small class="text-muted">Activas vs anuladas</small>
                    </div>
                    <div class="chart-wrapper sm">
                        <canvas id="chart-bar"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">Rubros del día</h5>
                            <small class="text-muted">Suma por impuesto</small>
                        </div>
                        <select class="form-select form-select-sm w-auto" id="select-fecha-rubro">
                        </select>
                    </div>
                    <div class="chart-wrapper sm">
                        <canvas id="chart-rubros"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4 mt-1">
            <div class="col-12">
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Top contribuyentes</h5>
                        <small class="text-muted">Solo facturas activas en el rango</small>
                    </div>
                    <div class="chart-wrapper xl">
                        <canvas id="chart-top-contribuyentes"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/graficos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
