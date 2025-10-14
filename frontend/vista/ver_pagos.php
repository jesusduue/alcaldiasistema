<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos del contribuyente</title>
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
                    <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <header class="app-card mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h1 class="app-section-title h3 mb-2">Pagos del contribuyente</h1>
                <p class="app-subtitle mb-0">Consulta el historico de facturas emitidas, reimprime recibos, modifica o anula segun el estado actual.</p>
            </div>
            <button class="btn btn-app-outline print-hide" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir listado
            </button>
        </header>

        <section class="app-card">
            <div id="estado-mensaje" class="alert d-none" role="alert"></div>
            <div class="table-responsive app-table">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">N° FACTURA</th>
                            <th scope="col">FECHA</th>
                            <th scope="col">CEDULA/RIF</th>
                            <th scope="col">RAZON SOCIAL</th>
                            <th scope="col">DESCRIPCION</th>
                            <th scope="col">MONTO</th>
                            <th scope="col">ESTADO</th>
                            <th scope="col" class="text-center oculto-impresion" colspan="3">PROCESOS</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-pagos">
                        <tr>
                            <td colspan="10" class="text-center py-4">Cargando informacion...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tablaPagos = document.getElementById('tabla-pagos');
        const estadoMensaje = document.getElementById('estado-mensaje');

        function mostrarMensaje(tipo, mensaje) {
            estadoMensaje.className = `alert alert-${tipo}`;
            estadoMensaje.textContent = mensaje;
            estadoMensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            estadoMensaje.classList.add('d-none');
            estadoMensaje.textContent = '';
        }

        function crearFila(factura) {
            const fila = document.createElement('tr');

            fila.innerHTML = `
                <td>${factura.num_factura}</td>
                <td>${factura.fecha}</td>
                <td>${factura.cedula_rif}</td>
                <td>${factura.razon_social}</td>
                <td>${factura.concepto ?? ''}</td>
                <td>${factura.total_factura ?? ''}</td>
                <td>${factura.ESTADO_FACT ?? ''}</td>
                <td class="text-center"><a class="btn btn-sm btn-outline-secondary oculto-impresion" href="reimprimir_factura.php?num_factura=${factura.num_factura}"><i class="bi bi-printer"></i></a></td>
                
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-anular oculto-impresion" data-url="anular_factura.php?num_factura=${factura.num_factura}"><i class="bi bi-x-circle me-1"></i>Anular</button></td>
            `;

            return fila;
        }

        function renderTabla(facturas) {
            tablaPagos.innerHTML = '';

            if (!facturas.length) {
                const fila = document.createElement('tr');
                fila.innerHTML = `<td colspan="10" class="text-center py-4">No hay pagos registrados para este contribuyente.</td>`;
                tablaPagos.appendChild(fila);
                return;
            }

            facturas.forEach((factura) => {
                tablaPagos.appendChild(crearFila(factura));
            });
        }

        async function cargarPagos() {
            limpiarMensaje();
            const params = new URLSearchParams(window.location.search);
            const idContribuyente = params.get('id_contribuyente');

            if (!idContribuyente) {
                renderTabla([]);
                mostrarMensaje('warning', 'Parametro id_contribuyente no proporcionado.');
                return;
            }

            try {
                const respuesta = await apiRequest('facturas', 'by_contribuyente', {
                    params: { id_contribuyente: idContribuyente },
                });

                renderTabla(respuesta?.data ?? []);
            } catch (error) {
                renderTabla([]);
                mostrarMensaje('danger', error.message || 'No fue posible obtener los pagos.');
            }
        }

        async function verificarFactura(numFactura) {
            try {
                const respuesta = await apiRequest('facturas', 'verify', {
                    params: { num_factura: numFactura },
                });
                return respuesta?.existe === true;
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No se pudo verificar la factura.');
                return false;
            }
        }

        tablaPagos.addEventListener('click', async (evento) => {
            const botonModificar = evento.target.closest('.btn-modificar');
            if (botonModificar) {
                evento.preventDefault();
                const licenciaActiva = await ensureLicenseActive();
                if (!licenciaActiva) {
                    botonModificar.disabled = true;
                    return;
                }

                const numeroFactura = botonModificar.getAttribute('data-num');
                if (!numeroFactura) {
                    return;
                }

                const existe = await verificarFactura(numeroFactura);
                if (existe) {
                    window.location.href = `modificar_factura.php?num_factura=${numeroFactura}`;
                } else {
                    mostrarMensaje('warning', 'La factura no existe en la base de datos.');
                }
                return;
            }

            const botonAnular = evento.target.closest('.btn-anular');
            if (botonAnular) {
                evento.preventDefault();
                const licenciaActiva = await ensureLicenseActive();
                if (!licenciaActiva) {
                    botonAnular.disabled = true;
                    return;
                }

                const url = botonAnular.dataset.url;
                if (url) {
                    window.location.href = url;
                }
            }
        });

        document.addEventListener('DOMContentLoaded', cargarPagos);
    </script>
</body>
</html>
