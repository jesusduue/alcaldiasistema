<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesion | Alcaldia Sistema</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
    <style>
        body.login-body {
            background: linear-gradient(145deg, rgba(28, 25, 23, 0.95), rgba(127, 29, 29, 0.9)),
                radial-gradient(circle at top left, rgba(245, 245, 244, 0.35), transparent 45%);
            min-height: 100vh;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            align-items: center;
            padding: 2.5rem 1.5rem;
        }

        .login-brand {
            color: var(--color-white);
            max-width: 440px;
        }

        .login-brand img {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .login-brand h1 {
            color: var(--color-white);
            font-weight: 700;
        }

        .login-card {
            background: var(--color-white);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.4);
            padding: 2.5rem;
        }

        .login-card h2 {
            color: var(--color-text-head);
        }

        .login-hint {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.95rem;
        }

        .password-toggle {
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .login-shell {
                padding: 2rem 1.25rem;
            }

            .login-card {
                padding: 2rem;
            }
        }
    </style>
</head>

<body class="login-body">
    <main class="login-shell container">
        <section class="login-brand">
            <div class="d-flex align-items-center gap-3 mb-4">
                <img src="../logo.png" alt="Logo">
                <div>
                    <h1 class="h3 mb-1">Alcaldia Sistema 2026</h1>
                    <p class="login-hint mb-0">Gestion administrativa y control de ingresos municipales.</p>
                </div>
            </div>
            <p class="mb-4">Accede con tu usuario autorizado para registrar contribuyentes, emitir facturas y consultar reportes.</p>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock"></i>
                    <span>Acceso protegido por roles</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Auditoria de actividades en tiempo real</span>
                </div>
            </div>
        </section>

        <section class="login-card">
            <h2 class="h4 mb-2">Iniciar sesion</h2>
            <p class="text-muted mb-4">Ingresa tus credenciales para continuar.</p>
            <form id="form-login" novalidate>
                <div class="mb-3">
                    <label for="usuario" class="form-label text-uppercase text-muted small">Usuario</label>
                    <input type="text" class="form-control app-input" id="usuario" name="usuario" autocomplete="username" required>
                </div>
                <div class="mb-3">
                    <label for="clave" class="form-label text-uppercase text-muted small">Clave</label>
                    <div class="input-group">
                        <input type="password" class="form-control app-input" id="clave" name="clave" autocomplete="current-password" required>
                        <span class="input-group-text bg-white password-toggle" id="toggle-password" title="Mostrar clave">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-app-primary w-100" id="btn-login">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
                </button>
            </form>
            <div class="alert alert-danger d-none mt-3 mb-0" id="login-error" role="alert"></div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../js/login.js"></script>
</body>

</html>
