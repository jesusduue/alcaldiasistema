<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos_update_factura.css">
    <title>FACTURA</title>
    <style>
        .btn {
            padding: 10px 20px;
            background-color: #2c8091;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #246a78;
            color: white;
        }

        .alert {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div id="formulario" class="formulario">
        <div class="container">
            <img class="logo" src="../logo.png" alt="logo alcaldia">
            <div class="menbrete">
                <h6>REPUBLICA BOLIVARIANA DE VENEZUELA</h6><br>
                <h6>ALCALDIA DEL MUNICIPIO GARCIA DE HEVIA</h6>
                <h6> &nbsp; La Fria - Edo. Tachira  </h6><br>
                <h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RIF:G-20001125-4 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </h6>
                <h6>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;DIRECCION DE HACIENDA &nbsp;&nbsp;&nbsp;&nbsp;</h6>
            </div>
        </div>

        <form id="form-modificar">
            <div class="fila">
                <div class="fecha-recibo">FECHA:
                    <input type="date" class="FECHA" id="fecha" readonly>
                </div>
                <div class="campo numero-factura">NUMERO FACTURA:
                    <input type="text" class="factura" id="num_factura" readonly>
                </div>
            </div>

            <div class="fila-cont">
                <div class="codigo"> CODIGO:
                    <input class="campo cod-inp" type="text" id="cod_contribuyente" readonly>
                </div>

                <div class="cedula-rif">CEDULA/RIF:
                    <input class="campo cedula" type="text" id="cedula_rif" readonly>
                </div>

                <div>
                    <input type="text" class="nombre" id="razon_social" readonly>
                </div>
            </div>

            <div class="descripcion">
                <input type="text" class="concepto" id="concepto" readonly>
            </div>

            <div>
                <span>DETALLE</span>
                <span> ----</span>
                <span> ----</span>
                <span> ----</span>
                <span> ----</span>
                <span> ----</span>
                <span> ----</span>
                <span>MONTO</span>
            </div>
            <div class="">
                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_A" readonly>
                    <input type="text" class="monto" id="monto_impuesto_A" readonly>
                </div>

                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_B" readonly>
                    <input type="text" class="monto" id="monto_impuesto_B" readonly>
                </div>

                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_C" readonly>
                    <input type="text" class="monto" id="monto_impuesto_C" readonly>
                </div>

                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_D" readonly>
                    <input type="text" class="monto" id="monto_impuesto_D" readonly>
                </div>

                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_E" readonly>
                    <input type="text" class="monto" id="monto_impuesto_E" readonly>
                </div>

                <div class="clasificador-monto">
                    <input class="nom_impuesto" id="impuesto_F" readonly>
                    <input type="text" class="monto" id="monto_impuesto_F" readonly>
                </div>
            </div>

            <label>TOTAL CANCELADO:</label>
            <input class="total" type="text" id="total_factura" readonly><br>

            <div>
                <input class="nulo" type="text" id="estado_factura" readonly>
            </div>

            <div id="mensaje" class="alert d-none" role="alert"></div>

            <div>
                <button type="submit" class="btn oculto-impresion">ELIMINAR</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const numFactura = params.get('num_factura');
        const formulario = document.getElementById('form-modificar');
        const mensaje = document.getElementById('mensaje');

        function mostrarMensaje(tipo, texto) {
            mensaje.className = `alert alert-${tipo}`;
            mensaje.textContent = texto;
            mensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            mensaje.classList.add('d-none');
            mensaje.textContent = '';
        }

        async function cargarFactura() {
            if (!numFactura) {
                mostrarMensaje('warning', 'Debe indicar un numero de factura valido.');
                formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
                return;
            }

            try {
                const respuesta = await apiRequest('facturas', 'show', {
                    params: { num_factura: numFactura },
                });
                const factura = respuesta?.data;
                if (!factura) {
                    throw new Error('Factura no encontrada.');
                }

                document.getElementById('fecha').value = factura.fecha;
                document.getElementById('num_factura').value = factura.num_factura;
                document.getElementById('cod_contribuyente').value = factura.cod_contribuyente;
                document.getElementById('cedula_rif').value = factura.cedula_rif;
                document.getElementById('razon_social').value = factura.razon_social;
                document.getElementById('concepto').value = factura.concepto ?? '';
                document.getElementById('monto_impuesto_A').value = factura.monto_impuesto_A ?? '';
                document.getElementById('impuesto_A').value = factura.nombre_impuesto_A ?? '';
                document.getElementById('monto_impuesto_B').value = factura.monto_impuesto_B ?? '';
                document.getElementById('impuesto_B').value = factura.nombre_impuesto_B ?? '';
                document.getElementById('monto_impuesto_C').value = factura.monto_impuesto_C ?? '';
                document.getElementById('impuesto_C').value = factura.nombre_impuesto_C ?? '';
                document.getElementById('monto_impuesto_D').value = factura.monto_impuesto_D ?? '';
                document.getElementById('impuesto_D').value = factura.nombre_impuesto_D ?? '';
                document.getElementById('monto_impuesto_E').value = factura.monto_impuesto_E ?? '';
                document.getElementById('impuesto_E').value = factura.nombre_impuesto_E ?? '';
                document.getElementById('monto_impuesto_F').value = factura.monto_impuesto_F ?? '';
                document.getElementById('impuesto_F').value = factura.nombre_impuesto_F ?? '';
                document.getElementById('total_factura').value = factura.total_factura ?? '';
                document.getElementById('estado_factura').value = factura.ESTADO_FACT ?? '';
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la factura.');
                formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
            }
        }

        formulario.addEventListener('submit', async (evento) => {
            evento.preventDefault();
            limpiarMensaje();

            const licenciaActiva = await ensureLicenseActive();
            if (!licenciaActiva) {
                formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
                return;
            }

            if (!confirm('Estas seguro de que deseas eliminar esta factura?')) {
                return;
            }

            try {
                const respuesta = await apiRequest('facturas', 'delete', {
                    method: 'POST',
                    params: { num_factura: numFactura },
                });

                mostrarMensaje('success', respuesta?.message || 'Factura eliminada correctamente.');
                formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
            } catch (error) {
                if (error?.payload?.details?.code === 'LICENSE_EXPIRED') {
                    await ensureLicenseActive({ force: true });
                    formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
                    return;
                }
                mostrarMensaje('danger', error.message || 'No fue posible eliminar la factura.');
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {
            const licenciaActiva = await ensureLicenseActive({ silent: false });
            if (!licenciaActiva) {
                formulario.querySelectorAll('button').forEach((boton) => boton.disabled = true);
                return;
            }
            cargarFactura();
        });
    </script>
</body>
</html>
