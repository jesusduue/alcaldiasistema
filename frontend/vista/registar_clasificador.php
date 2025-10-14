<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registrar clasificador</title>
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
					<li class="nav-item"><a class="nav-link" href="relacion_diaria.php">Relaciones diarias</a></li>
					<li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar contribuyente</a></li>
					<li class="nav-item"><a class="nav-link active" href="registar_clasificador.php">Registrar clasificador</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<main class="container py-5">
		<section class="row g-4">
			<div class="col-lg-6">
				<div class="app-card h-100">
					<header class="mb-4">
						<h1 class="app-section-title h4 mb-2">Nuevo clasificador</h1>
						<p class="app-subtitle mb-0">Identifica rubros tributarios para utilizarlos durante la generación de recibos.</p>
					</header>
					<form id="form-clasificador" class="needs-validation" novalidate>
						<div class="mb-3">
							<label for="nombre" class="form-label fw-semibold">Nombre del rubro</label>
							<input type="text" name="nombre" id="nombre" class="form-control app-input" placeholder="Ej. Solvencia Municipal" required>
						</div>
						<div id="mensaje-clasificador" class="alert d-none mt-3" role="alert"></div>
						<div class="d-grid mt-4">
							<button type="submit" class="btn btn-app-primary" value="insertar" name="accion">
								<i class="bi bi-plus-circle me-2"></i>Agregar clasificador
							</button>
						</div>
					</form>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="app-card h-100">
					<header class="mb-4">
						<h2 class="app-section-title h5 mb-2">Relacion por rubros</h2>
						<p class="app-subtitle mb-0">Consulta la recaudacion agrupada por rubros en una fecha especifica.</p>
					</header>
					<form action="listar_rubros.php" method="get" class="needs-validation" novalidate>
						<div class="mb-3">
							<label for="fecha_det_recibo" class="form-label fw-semibold">Selecciona una fecha</label>
							<input class="form-control app-input" name="fecha_det_recibo" id="fecha_det_recibo" type="date" required>
						</div>
						<div class="d-grid mt-4">
							<button class="btn btn-app-outline" type="submit">
								<i class="bi bi-funnel me-2"></i>Ver relacion por rubros
							</button>
						</div>
					</form>
					<p class="text-muted small mt-4 mb-0">
						<i class="bi bi-info-circle me-1"></i>Tambien puedes generar relaciones diarias completas desde el menu superior en la seccion “Relaciones diarias”.
					</p>
				</div>
			</div>
		</section>
	</main>

	<script src="../js/apiClient.js"></script>
	<script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const formulario = document.getElementById('form-clasificador');
			const mensaje = document.getElementById('mensaje-clasificador');

			function mostrarMensaje(tipo, texto) {
				mensaje.className = `alert alert-${tipo} mt-3`;
				mensaje.textContent = texto;
				mensaje.classList.remove('d-none');
			}

			function limpiarMensaje() {
				mensaje.classList.add('d-none');
				mensaje.textContent = '';
			}

			formulario.addEventListener('submit', async (evento) => {
				evento.preventDefault();
				limpiarMensaje();
				const formData = new FormData(formulario);
				const payload = Object.fromEntries(formData.entries());
				try {
					const respuesta = await apiRequest('clasificadores', 'store', {
						method: 'POST',
						body: payload,
					});
					formulario.reset();
					mostrarMensaje('success', respuesta?.message || 'Clasificador registrado correctamente.');
				} catch (error) {
					mostrarMensaje('danger', error.message || 'No fue posible registrar el clasificador.');
				}
			});
		});
	</script>
</body>
</html>
