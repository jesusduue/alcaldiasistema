<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Relaciones diarias</title>
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="../css/theme.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark app-navbar py-3">
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
					<li class="nav-item"><a class="nav-link" href="ingresos_diarios.php">Ingresos diarios</a></li>
					<li class="nav-item"><a class="nav-link active" href="relacion_diaria.php">Relaciones diarias</a></li>
					<li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar contribuyente</a></li>
					<li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<main class="container py-5">
		<section class="app-card mb-4">
			<header class="mb-4 text-center text-md-start">
				<h1 class="app-section-title h3 mb-2">Relación Diaria</h1>
				<p class="app-subtitle mb-0 oculto-impresion">Filtra las facturas por fecha, contribuyente o estado. Los resultados se actualizan mientras escribes para agilizar el control diario.</p>
			</header>

			<div class="row g-4 align-items-end mb-4 print-hide oculto-impresion">
				<div class="col-lg-6">
					<label for="caja_busqueda" class="form-label text-uppercase text-muted small">Buscar por fecha, RIF, razón social o estado</label>
					<div class="input-group">
						<span class="input-group-text bg-white text-muted">
							<i class="bi bi-search"></i>
						</span>
						<input class="form-control app-input" type="text" name="caja_busqueda" id="caja_busqueda" placeholder="Ej. 2025-01-15, V-12345678, Activo">
					</div>
				</div>
				<div class="col-lg-6 text-lg-end">
					<p class="app-subtitle small mb-2 oculto-impresion">Accesos rápidos</p>
					<div class="d-flex flex-wrap gap-2">
						<a href="registar_clasificador.php" class="btn btn-sm btn-app-outline">
							<i class="bi bi-clipboard-data me-1"></i>Relación por rubros
						</a>
						<a href="registar_clasificador.php#fecha_det_recibo" class="btn btn-sm btn-app-outline">
							<i class="bi bi-calendar2-week me-1"></i>Seleccionar otra fecha
						</a>
						<button class="btn btn-sm btn-app-outline" type="button" onclick="window.print()">
							<i class="bi bi-printer me-1"></i>Imprimir vista
						</button>
					</div>
				</div>
			</div>

			<div id="datos" class="table-responsive app-table"></div>
		</section>
	</main>

	<script src="../js/apiClient.js"></script>
	<script src="../js/buscar_relacion.js"></script>
	<script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
