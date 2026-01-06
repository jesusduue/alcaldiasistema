<?php

require_once __DIR__ . '/partials/session_admin_guard.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de usuarios</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
    <style>
        .status-pill {
            font-weight: 600;
            border-radius: 999px;
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .status-pill.active {
            background: rgba(22, 163, 74, 0.15);
            color: #166534;
        }

        .status-pill.inactive {
            background: rgba(220, 38, 38, 0.12);
            color: #991b1b;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table-sticky thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <section class="app-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="h4 mb-1">Gestion de usuarios</h1>
                    <p class="app-subtitle mb-0">Administra usuarios, roles y revisa la actividad reciente.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-app-primary" type="button" id="btn-nuevo-usuario">
                        <i class="bi bi-person-plus me-1"></i>Registrar nuevo usuario
                    </button>
                    <button class="btn btn-sm btn-app-outline" type="button" id="btn-roles">
                        <i class="bi bi-shield-lock me-1"></i>Gestionar roles
                    </button>
                </div>
            </div>

            <div class="table-responsive app-table table-sticky">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr>
                            <td colspan="5" class="text-center py-4">Cargando usuarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="app-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Actividad reciente</h2>
                    <p class="app-subtitle mb-0">Eventos mas recientes del sistema.</p>
                </div>
                <button class="btn btn-sm btn-app-outline" type="button" id="btn-recargar-logs">
                    <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                </button>
            </div>

            <div class="table-responsive app-table">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Accion</th>
                            <th scope="col">Modulo</th>
                            <th scope="col">Detalles</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-logs">
                        <tr>
                            <td colspan="5" class="text-center py-4">Cargando actividad...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal fade" id="modal-usuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-usuario">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titulo-usuario">Registrar usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="usuario-id" name="id_usuario">
                        <div class="mb-3">
                            <label class="form-label text-uppercase text-muted small" for="usuario-nombre">Usuario</label>
                            <input type="text" class="form-control app-input" id="usuario-nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase text-muted small" for="usuario-rol">Rol</label>
                            <select class="form-select app-input" id="usuario-rol" name="id_rol" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase text-muted small" for="usuario-clave">Clave</label>
                            <input type="password" class="form-control app-input" id="usuario-clave" name="clave" autocomplete="new-password">
                            <small class="text-muted">Deja en blanco para mantener la clave actual al editar.</small>
                        </div>
                        <div>
                            <label class="form-label text-uppercase text-muted small" for="usuario-estado">Estado</label>
                            <select class="form-select app-input" id="usuario-estado" name="estado">
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-app-outline" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-app-primary" id="btn-guardar-usuario">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-roles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestion de roles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="form-rol" class="row g-3 align-items-end mb-3">
                        <div class="col-md-8">
                            <label class="form-label text-uppercase text-muted small" for="rol-nombre">Nombre del rol</label>
                            <input type="text" class="form-control app-input" id="rol-nombre" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-app-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Registrar rol
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive app-table">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-roles">
                                <tr>
                                    <td colspan="4" class="text-center py-4">Cargando roles...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-app-outline" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/usuarios.js"></script>
</body>

</html>
