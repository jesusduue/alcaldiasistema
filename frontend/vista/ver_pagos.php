<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGOS</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-center">PAGOS DEL CONTRIBUYENTE</h2>
        <div id="estado-mensaje" class="alert d-none" role="alert"></div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <td>N° FACTURA</td>
                        <td>FECHA</td>
                        <td>CEDULA/RIF</td>
                        <td>RAZON SOCIAL</td>
                        <td>DESCRIPCION</td>
                        <td>PAGO</td>
                        <td>ESTADO</td>
                        <td colspan="3">PROCESOS</td>
                    </tr>
                </thead>
                <tbody id="tabla-pagos">
                    <tr>
                        <td colspan="10" class="text-center">Cargando información...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
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
                <td><a class="btn btn-primary" href="reimprimir_factura.php?num_factura=${factura.num_factura}">IMPRIMIR</a></td>
                <td><button type="button" class="btn btn-primary btn-modificar" data-num="${factura.num_factura}">Modificar Factura</button></td>
                <td><button type="button" class="btn btn-primary btn-anular" data-url="anular_factura.php?num_factura=${factura.num_factura}">ANULAR</button></td>
            `;

            return fila;
        }

        function renderTabla(facturas) {
            tablaPagos.innerHTML = '';

            if (!facturas.length) {
                const fila = document.createElement('tr');
                fila.innerHTML = `<td colspan="10" class="text-center">No hay pagos registrados para este contribuyente.</td>`;
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
                mostrarMensaje('warning', 'Parámetro id_contribuyente no proporcionado.');
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
