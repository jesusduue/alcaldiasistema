<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registrar contribuyente</title>
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
					<li class="nav-item"><a class="nav-link active" href="registar_contribuyente.php">Registrar contribuyente</a></li>
					<li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<main class="container py-5">
		<section class="app-card mx-auto" style="max-width: 600px;">
			<header class="mb-4 text-center">
				<h1 class="app-section-title h3 mb-2">Nuevo contribuyente</h1>
				<p class="app-subtitle mb-0">Completa los datos y guarda para que esté disponible en los procesos de recaudacion.</p>
			</header>

			<form id="form-contribuyente" class="needs-validation" novalidate>
				<div class="mb-3">
					<label for="cedula_rif" class="form-label fw-semibold">Cedula/RIF</label>
					<input type="text" class="form-control app-input" name="cedula_rif" id="cedula_rif" placeholder="Ej. V-12345678" required>
				</div>
				<div class="mb-3">
					<label for="razon_social" class="form-label fw-semibold">Razon social</label>
					<input type="text" class="form-control app-input" name="razon_social" id="razon_social" placeholder="Nombre de la empresa o contribuyente" required>
				</div>
				<div class="mb-3">
					<label for="estado_cont" class="form-label fw-semibold">Estado</label>
					<select name="estado_cont" id="estado_cont" class="form-select app-input">
						<option value="">Seleccionar estado</option>
						<option value="Activo">Activo</option>
						<option value="Inactivo">Inactivo</option>
					</select>
				</div>

				<div id="mensaje" class="alert d-none mt-3" role="alert"></div>

				<div class="d-grid mt-4">
					<button type="submit" class="btn btn-app-primary">
						<i class="bi bi-save me-2"></i>Guardar contribuyente
					</button>
				</div>
			</form>
		</section>
	</main>

	<script src="../js/apiClient.js"></script>
	<script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const formulario = document.getElementById('form-contribuyente');
			const mensaje = document.getElementById('mensaje');

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
					const respuesta = await apiRequest('contribuyentes', 'store', {
						method: 'POST',
						body: payload,
					});
					formulario.reset();
					mostrarMensaje('success', respuesta?.message || 'Contribuyente registrado correctamente.');
				} catch (error) {
					mostrarMensaje('danger', error.message || 'No fue posible registrar el contribuyente.');
				}
			});
		});
	</script>
</body>
</html>
