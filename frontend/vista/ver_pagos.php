<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos del contribuyente</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/1.11.3/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <header class="app-card mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h1 class="app-section-title h3 mb-2">Pagos del contribuyente</h1>
                <p class="app-subtitle mb-0 oculto-impresion">Consulta el historico de facturas emitidas, reimprime recibos, modifica o anula segun el estado actual.</p>
            </div>
            <button class="btn btn-app-outline print-hide" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir listado
            </button>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Total neto</p>
                    <h3 class="mb-0 text-primary" id="resumen-neto">-</h3>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Monto bruto</p>
                    <h3 class="mb-0" id="resumen-bruto">-</h3>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Anulados</p>
                    <h3 class="mb-0 text-danger" id="resumen-anulados">-</h3>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="bg-white border rounded-3 p-3 h-100">
                    <p class="text-uppercase text-muted small mb-1">Recibos</p>
                    <h3 class="mb-0" id="resumen-recibos">-</h3>
                </div>
            </div>
        </section>

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

    <script src="../vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script src="../bootstrap-5.3.1-dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>    
        const tablaPagos = document.getElementById('tabla-pagos');
        const estadoMensaje = document.getElementById('estado-mensaje');
        const resumenBruto = document.getElementById('resumen-bruto');
        const resumenAnulados = document.getElementById('resumen-anulados');
        const resumenNeto = document.getElementById('resumen-neto');
        const resumenRecibos = document.getElementById('resumen-recibos');

        const numberFormat = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function mostrarMensaje(tipo, mensaje) {
            estadoMensaje.className = `alert alert-${tipo}`;
            estadoMensaje.textContent = mensaje;
            estadoMensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            estadoMensaje.classList.add('d-none');
            estadoMensaje.textContent = '';
        }

        function esAnulada(factura) {
            const estadoPago = String(factura?.estado_pago ?? '').toUpperCase();
            const estadoTexto = String(factura?.ESTADO_FACT ?? '').toUpperCase();
            return estadoPago === 'N' || estadoTexto === 'NULO';
        }

        function setResumen(facturas) {
            if (!facturas.length) {
                resumenBruto.textContent = '-';
                resumenAnulados.textContent = '-';
                resumenNeto.textContent = '-';
                resumenRecibos.textContent = '-';
                return;
            }

            let bruto = 0;
            let anulados = 0;
            facturas.forEach((factura) => {
                const monto = Number(factura.total_factura) || 0;
                bruto += monto;
                if (esAnulada(factura)) {
                    anulados += monto;
                }
            });

            resumenBruto.textContent = numberFormat.format(bruto);
            resumenAnulados.textContent = numberFormat.format(anulados);
            resumenNeto.textContent = numberFormat.format(bruto - anulados);
            resumenRecibos.textContent = facturas.length.toString();
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
                setResumen([]);
                return;
            }

            facturas.forEach((factura) => {
                tablaPagos.appendChild(crearFila(factura));
            });

            setResumen(facturas);
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
