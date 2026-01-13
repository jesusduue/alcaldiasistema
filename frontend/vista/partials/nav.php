<?php

$nombreUsuario = htmlspecialchars((string) ($currentUser['nombre'] ?? 'Usuario'), ENT_QUOTES, 'UTF-8');
$rolUsuario = htmlspecialchars((string) ($currentUser['rol_nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$rolNombre = strtoupper(trim((string) ($currentUser['rol_nombre'] ?? $currentUser['rol'] ?? '')));
$isTreasurer = in_array($rolNombre, ['TESORERO', 'TESORERA', 'TESORERIA'], true);
?>

<nav class="navbar navbar-expand-lg navbar-dark app-navbar py-3">
    <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-semibold" href="index.php">
        <img src="../logo.png" alt="Logo">
        Alcaldia Sistema
    </a>
    <div class="container">
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link active" href="./index.php">Inicio</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navReportes" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Reportes</a>
                    <ul class="dropdown-menu dropdown-menu-lg-start" aria-labelledby="navReportes">
                        <li><a class="dropdown-item" href="ingresos_diarios.php">Ingresos diarios</a></li>
                        <li><a class="dropdown-item" href="ingresos_rango.php">Ingresos por rango</a></li>
                        <li><a class="dropdown-item" href="graficos.php">Graficos</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar
                        contribuyente</a></li>
                <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a>
                </li>
                <?php if ($isAdmin) : ?>
                    <li class="nav-item"><a class="nav-link" href="usuarios.php">Gestion de usuarios</a></li>
                <?php endif; ?>
                <?php if ($isAdmin || $isTreasurer) : ?>
                    <li class="nav-item"><a class="nav-link" href="sincronizacion.php">Sincronizacion</a></li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navUser" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i><?php echo $nombreUsuario; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navUser">
                        <li><span class="dropdown-item-text small text-muted">Rol: <?php echo $rolUsuario; ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Cerrar sesion</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
