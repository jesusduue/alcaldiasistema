<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relacion diaria por contribuyente</title>
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
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-semibold" href="index.html">
        
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="relacion_diaria.php">Relaciones diarias</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar contribuyente</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <header class="app-card mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h1 class="app-section-title h3 mb-2">Relacion diaria por contribuyente</h1>
                <p class="app-subtitle mb-0 oculto-impresion">Detalle de facturacion correspondiente a la fecha seleccionada. Puedes imprimir el reporte o volver para consultar otra fecha.</p>
            </div>
            <button class="btn btn-app-outline print-hide" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir relacion
            </button>
        </header>

        <section class="app-card">
            <div id="mensaje" class="alert d-none" role="alert"></div>
            <div class="table-responsive app-table">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">N° FACTURA</th>
                            <th scope="col">FECHA</th>
                            <th scope="col">CEDULA/RIF</th>
                            <th scope="col">RAZON SOCIAL</th>
                            <th scope="col">CONCEPTO</th>
                            <th scope="col">MONTO CANCELADO</th>
                            <th scope="col">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-facturas">
                        <tr>
                            <td colspan="7" class="text-center py-4">Cargando informacion...</td>
                        </tr>
                    </tbody>
                    <tfoot id="tabla-resumen"></tfoot>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const fecha = params.get('fecha');
        const mensaje = document.getElementById('mensaje');
        const cuerpoTabla = document.getElementById('tabla-facturas');
        const resumenTabla = document.getElementById('tabla-resumen');

        function mostrarMensaje(tipo, texto) {
            mensaje.className = `alert alert-${tipo}`;
            mensaje.textContent = texto;
            mensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            mensaje.classList.add('d-none');
            mensaje.textContent = '';
        }

        function renderizarTabla(facturas) {
            cuerpoTabla.innerHTML = '';

            if (!facturas.length) {
                const fila = document.createElement('tr');
                fila.innerHTML = `<td colspan="7" class="text-center py-4">No hay registros.</td>`;
                cuerpoTabla.appendChild(fila);
                resumenTabla.innerHTML = '';
                return;
            }

            let totalSum = 0;
            let anuladosSum = 0;

            facturas.forEach((factura) => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${factura.num_factura}</td>
                    <td>${factura.fecha}</td>
                    <td>${factura.cedula_rif}</td>
                    <td>${factura.razon_social}</td>
                    <td>${factura.concepto ?? ''}</td>
                    <td>${factura.total_factura ?? ''}</td>
                    <td>${factura.ESTADO_FACT ?? ''}</td>
                `;
                cuerpoTabla.appendChild(fila);

                const monto = parseFloat(factura.total_factura ?? 0);
                if (!Number.isNaN(monto)) {
                    totalSum += monto;
                    if ((factura.ESTADO_FACT ?? '').toLowerCase() === 'nulo') {
                        anuladosSum += monto;
                    }
                }
            });

            const totalFinal = totalSum - anuladosSum;

            resumenTabla.innerHTML = `
                <tr>
                    <td colspan="5" class="text-end fw-semibold text-uppercase">Sub-total:</td>
                    <td>${totalSum.toFixed(2)}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end fw-semibold text-uppercase">Total recibos anulados:</td>
                    <td>${anuladosSum.toFixed(2)}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end fw-semibold text-uppercase">Total general:</td>
                    <td>${totalFinal.toFixed(2)}</td>
                    <td></td>
                </tr>
            `;
        }

        async function cargarFacturas() {
            limpiarMensaje();

            if (!fecha) {
                renderizarTabla([]);
                mostrarMensaje('warning', 'Debe proporcionar una fecha para realizar la consulta.');
                return;
            }

            try {
                const respuesta = await apiRequest('facturas', 'by_fecha', {
                    params: { fecha },
                });
                renderizarTabla(respuesta?.data ?? []);
            } catch (error) {
                renderizarTabla([]);
                mostrarMensaje('danger', error.message || 'No fue posible obtener la informacion.');
            }
        }

        document.addEventListener('DOMContentLoaded', cargarFacturas);
    </script>
</body>
</html>
