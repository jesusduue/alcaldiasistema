<?php

require_once __DIR__ . '/partials/session_guard.php';

$rolNombre = strtoupper(trim((string) ($currentUser['rol_nombre'] ?? $currentUser['rol'] ?? '')));
$isTreasurer = in_array($rolNombre, ['TESORERO', 'TESORERA', 'TESORERIA'], true);
$canImport = $isAdmin;
$canStatus = $isAdmin;

if (!$isAdmin && !$isTreasurer) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronizacion</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
</head>

<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5" data-can-status="<?php echo $canStatus ? '1' : '0'; ?>" data-can-import="<?php echo $canImport ? '1' : '0'; ?>">
        <?php if ($canStatus) : ?>
            <section class="app-card mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h1 class="h4 mb-1">Sincronizacion de respaldo</h1>
                        <p class="app-subtitle mb-0">Genera paquetes JSON o importa un respaldo completo del sistema.</p>
                    </div>
                    <button class="btn btn-sm btn-app-outline" type="button" id="btn-recargar-status">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-surface h-100">
                            <div class="text-uppercase text-muted small">Total registros</div>
                            <div class="h4 mb-0" id="status-total">--</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-surface h-100">
                            <div class="text-uppercase text-muted small">Ultima accion</div>
                            <div class="fw-semibold" id="status-accion">Sin registros</div>
                            <div class="small text-muted" id="status-fecha">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-surface h-100">
                            <div class="text-uppercase text-muted small">Usuario</div>
                            <div class="fw-semibold" id="status-usuario">-</div>
                            <div class="small text-muted" id="status-detalle">-</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive app-table mt-4">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tabla</th>
                                <th scope="col" class="text-end">Registros</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-sync-resumen">
                            <tr>
                                <td colspan="2" class="text-center py-4">Cargando resumen...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else : ?>
            <section class="app-card mb-4">
                <h1 class="h4 mb-1">Sincronizacion de respaldo</h1>
                <p class="app-subtitle mb-0">Genera el paquete para que el administrador pueda importarlo.</p>
            </section>
        <?php endif; ?>

        <div id="sync-message" class="alert d-none mb-4" role="alert"></div>

        <div class="row g-4">
            <div class="<?php echo $canImport ? 'col-lg-6' : 'col-12'; ?>">
                <section class="app-card h-100">
                    <h2 class="h5 mb-2">Generar paquete</h2>
                    <p class="app-subtitle mb-4">Descarga un respaldo completo en formato JSON.</p>
                    <button class="btn btn-app-primary" type="button" id="btn-exportar">
                        <i class="bi bi-download me-2"></i>Generar paquete
                    </button>
                </section>
            </div>
            <?php if ($canImport) : ?>
                <div class="col-lg-6">
                    <section class="app-card h-100">
                        <h2 class="h5 mb-2">Importar paquete</h2>
                        <p class="app-subtitle mb-3">Sube un archivo JSON para restaurar o combinar los datos.</p>
                        <div class="mb-3">
                            <label class="form-label text-uppercase text-muted small" for="archivo-paquete">Archivo JSON</label>
                            <input class="form-control app-input" type="file" id="archivo-paquete" accept=".json,application/json">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="replace-all">
                            <label class="form-check-label" for="replace-all">Reemplazar todo</label>
                        </div>
                        <button class="btn btn-app-primary" type="button" id="btn-importar">
                            <i class="bi bi-upload me-2"></i>Importar paquete
                        </button>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/sincronizacion.js"></script>
</body>

</html>
