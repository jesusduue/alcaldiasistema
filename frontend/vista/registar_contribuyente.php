<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
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
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

	<main class="container py-5">
		<section class="app-card mx-auto" style="max-width: 600px;">
			<header class="mb-4 text-center">
				<h1 class="app-section-title h3 mb-2">Nuevo contribuyente</h1>
				<p class="app-subtitle mb-0">Completa los datos y guarda para que esté disponible en los procesos de recaudacion.</p>
			</header>

            <form id="form-contribuyente" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="cedula_numero" class="form-label fw-semibold">Cedula/RIF</label>
                    <div class="input-group">
                        <select class="form-select app-input" id="cedula_tipo" style="max-width: 80px;">
                            <option value="V-">V-</option>
                            <option value="E-">E-</option>
                            <option value="J-">J-</option>
                            <option value="G-">G-</option>
                        </select>
                        <input type="text" class="form-control app-input" id="cedula_numero" placeholder="12345678" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="razon_social" class="form-label fw-semibold">Razon social</label>
                    <input type="text" class="form-control app-input" name="razon_social" id="razon_social" placeholder="Nombre de la empresa o contribuyente" required>
                </div>
                <div class="mb-3">
                    <label for="telefono_numero" class="form-label fw-semibold">Telefono</label>
                    <div class="input-group">
                        <select class="form-select app-input" id="telefono_codigo" style="max-width: 100px;">
                            <option value="0412">0412</option>
                            <option value="0414">0414</option>
                            <option value="0424">0424</option>
                            <option value="0416">0416</option>
                            <option value="0426">0426</option>
                            <option value="0277">0277</option>
                        </select>
                        <input type="tel" class="form-control app-input" id="telefono_numero" placeholder="1234567" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email_usuario" class="form-label fw-semibold">Correo electronico</label>
                    <div class="input-group">
                        <input type="text" class="form-control app-input" id="email_usuario" placeholder="usuario" required>
                        <select class="form-select app-input" id="email_dominio" style="max-width: 150px;">
                            <option value="@gmail.com">@gmail.com</option>
                            <option value="@hotmail.com">@hotmail.com</option>
                            <option value="@outlook.com">@outlook.com</option>
                            <option value="@yahoo.com">@yahoo.com</option>
                            <option value="@gmail.es">@gmail.es</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label fw-semibold">Direccion fiscal</label>
                    <textarea class="form-control app-input" name="direccion" id="direccion" rows="3" placeholder="Direccion completa del contribuyente" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="estado_cont" class="form-label fw-semibold">Estado</label>
                    <select name="estado_cont" id="estado_cont" class="form-select app-input">
                        <option value="A" selected>Activo</option>
                        <option value="I">Inactivo</option>
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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

                // Concatenar valores
                const cedulaTipo = document.getElementById('cedula_tipo').value;
                const cedulaNumero = document.getElementById('cedula_numero').value;
                payload.cedula_rif = `${cedulaTipo}${cedulaNumero}`;

                const telefonoCodigo = document.getElementById('telefono_codigo').value;
                const telefonoNumero = document.getElementById('telefono_numero').value;
                payload.telefono = `${telefonoCodigo}-${telefonoNumero}`;

                const emailUsuario = document.getElementById('email_usuario').value;
                const emailDominio = document.getElementById('email_dominio').value;
                payload.email = `${emailUsuario}${emailDominio}`;

                if (!payload.estado_cont) {
                    payload.estado_cont = 'A';
                }
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
