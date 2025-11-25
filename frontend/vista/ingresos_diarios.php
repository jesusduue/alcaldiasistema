<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresos diarios</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
</head>

<body>
      <nav class="navbar navbar-expand-lg navbar-dark app-navbar py-3 oculto-impresion">
             <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-semibold" href="#">
                <img src="../logo.png" alt="Logo">
                Alcaldia Sistema
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="relacion_diaria.php">Relaciones diarias</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar contribuyente</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
                </ul>
            </div>
        </div>
    </nav>

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
                        <p class="text-uppercase text-muted small mb-1">Recibos</p>
                        <h3 class="mb-0" id="resumen-recibos">-</h3>
                    </div>
                </div>
            </div>

            <div id="tabla-diaria" class="table-responsive app-table"></div>
        </section>

        <section class="app-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Detalle por rubros</h2>
                    <p class="app-subtitle mb-0 oculto-impresion">Suma de ingresos por impuesto para la fecha seleccionada.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small text-uppercase">Fecha:</span>
                    <span class="badge bg-secondary" id="rubro-fecha-label">-</span>
                </div>
            </div>
            <div id="tabla-rubros" class="table-responsive app-table"></div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.js"></script>
    <script src="../js/ingresos_diarios.js"></script>
</body>

</html>
