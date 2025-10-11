<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos.css">
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
            <img class="logo" src="../logo.png" alt="logo alcaldía">
            <div class="menbrete">
                <h6>REPÚBLICA BOLIVARIANA DE VENEZUELA</h6><br>
                <h6>ALCALDIA DEL MUNICIPIO GARCIA DE HEVIA</h6>
                <h6> &nbsp; La Fría - Edo. Táchira  </h5><br>
                <h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RIF:G-20001125-4 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </h6>
                <h6>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;DIRECCIÓN DE HACIENDA &nbsp;&nbsp;&nbsp;&nbsp;</h6>
            </div>
        </div>

        <form id="form-reimprimir">
            <div class="fila">
                <div class="fecha-recibo">FECHA:
                    <input type="text" class="FECHA" id="fecha" readonly>
                </div>
                <div class="campo numero-factura">N° FACTURA:
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

            <label>TOTAL CANCELADO:</label>
            <input class="total" type="text" id="total_factura" readonly><br>

            <div id="mensaje" class="alert d-none" role="alert"></div>
        </form>

        <button class="btn oculto-impresion" id="btn-imprimir">IMPRIMIR</button>
    </div>

    <script src="../js/apiClient.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const numFactura = params.get('num_factura');
        const mensaje = document.getElementById('mensaje');
        const btnImprimir = document.getElementById('btn-imprimir');

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
                mostrarMensaje('warning', 'Debe indicar un número de factura válido.');
                btnImprimir.disabled = true;
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
                document.getElementById('num_factura').value = `000${factura.num_factura}`;
                document.getElementById('cod_contribuyente').value = factura.cod_contribuyente;
                document.getElementById('cedula_rif').value = factura.cedula_rif;
                document.getElementById('razon_social').value = factura.razon_social;
                document.getElementById('concepto').value = factura.concepto ?? '';
                document.getElementById('total_factura').value = factura.total_factura ?? '';
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la factura.');
                btnImprimir.disabled = true;
            }
        }

        btnImprimir.addEventListener('click', () => {
            window.print();
        });

        document.addEventListener('DOMContentLoaded', () => {
            limpiarMensaje();
            cargarFactura();
        });
    </script>
</body>
</html>
