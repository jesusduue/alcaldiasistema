<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
</head>

<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <!--         <section class="app-hero mb-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold mb-3">Gestion de contribuyentes</h1>
                    <p class="app-subtitle fs-5 mb-4">
                        Consulta, genera recibos y navega rapidamente entre los procesos tributarios. Utiliza el buscador para localizar contribuyentes por RIF, nombre o estado.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="app-pill">Gestion diaria optimizada</span>
                        <span class="app-pill">Historial de pagos accesible</span>
                        <span class="app-pill">Generacion de recibos en segundos</span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <img src="../logo.png" alt="Escudo" class="img-fluid opacity-75" style="max-height: 120px;">
                </div>
            </div>
        </section> -->

        <section class="app-card mb-5">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="app-section-title h4 mb-1">Directorio de contribuyentes</h2>
                    <p class="app-subtitle mb-0">Escribe al menos tres caracteres para iniciar la busqueda.</p>
                </div>
                <div class="w-100 w-md-auto">
                    <label for="caja_busqueda" class="form-label text-uppercase text-muted small mb-1">Buscar
                        contribuyente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                        <input class="form-control app-input" type="text" name="caja_busqueda" id="caja_busqueda"
                            placeholder="Cedula/RIF, razon social o estado">
                    </div>
                </div>
            </div>
            <div id="datos" class="table-responsive app-table"></div>
        </section>
        <footer class="app-footer text-center">
            &copy; <span id="year"></span> Direccion de Hacienda y Municipio Garcia de Hevia
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../js/apiClient.js"></script>
        <script src="../js/license.js"></script>
        <script src="../js/buscar_datos.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script>
            document.getElementById('year').textContent = new Date().getFullYear();
        </script>
</body>

</html>
