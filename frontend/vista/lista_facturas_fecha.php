<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTA FACTURAS</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_pagos.css">
    <style>
        .titulo {
            text-align: center;
            font-weight: bold;
        }
        .footer {
            text-align: right;
            text-transform: uppercase;
        }
        .TXTO {
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="titulo"><span>RELACIÓN DE INGRESOS DIARIOS</span></div>
    <div id="mensaje" class="alert d-none" role="alert"></div>
    <table class="container">
        <thead class="head">
            <tr>
                <td>N° FACTURA</td>
                <td>FECHA</td>
                <td>CEDULA/RIF</td>
                <td>RAZON SOCIAL</td>
                <td>CONCEPTO</td>
                <td>MONTO CANCELADO</td>
                <td>ESTADO</td>
            </tr>
        </thead>
        <tbody id="tabla-facturas">
            <tr>
                <td colspan="7" class="text-center">Cargando información...</td>
            </tr>
        </tbody>
        <tfoot id="tabla-resumen"></tfoot>
    </table>

    <script src="../js/apiClient.js"></script>
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
                fila.innerHTML = `<td colspan="7" class="text-center">No hay registros.</td>`;
                cuerpoTabla.appendChild(fila);
                resumenTabla.innerHTML = '';
                return;
            }

            let totalSum = 0;
            let anuladosSum = 0;

            facturas.forEach((factura) => {
                const fila = document.createElement('tr');
                fila.classList.add('TXTO');
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
                    <td colspan="5" class="footer">SUB-TOTAL:</td>
                    <td>${totalSum.toFixed(2)}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" class="footer">TOTAL RECIBOS ANULADOS:</td>
                    <td>${anuladosSum.toFixed(2)}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" class="footer">TOTAL:</td>
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
                mostrarMensaje('danger', error.message || 'No fue posible obtener la información.');
            }
        }

        document.addEventListener('DOMContentLoaded', cargarFacturas);
    </script>
</body>
</html>
