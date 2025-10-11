<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACTURA</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos.css">
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

        .campo[readonly] {
            background-color: #f8f9fa;
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

        <form id="form-factura">
            <input type="hidden" name="cod_contribuyente" id="cod_contribuyente">
            <input type="hidden" name="id_usuario" value="1">
            <div class="fila">
                <div class="fecha-recibo">FECHA:
                    <input type="date" class="FECHA" name="fecha" id="fecha" required>
                </div>
                <div class="campo numero-factura">N° FACTURA:
                    <input type="text" class="factura" id="numero_factura" readonly>
                </div>
            </div>

            <div class="fila-cont">
                <div class="codigo"> CODIGO:
                    <input class="campo cod-inp" type="text" id="codigo_mostrado" readonly>
                </div>

                <div class="cedula-rif">CEDULA/RIF:
                    <input class="campo cedula" type="text" id="cedula_rif" readonly>
                </div>

                <div>
                    <input type="text" class="nombre" id="razon_social" readonly>
                </div>
            </div>

            <div class="descripcion">
                <input type="text" class="concepto" name="concepto" id="concepto" value="DESCRIPCION">
            </div>

            <div class="">
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

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorA" id="clasificadorA" required></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_A" name="monto_impuesto_A" class="monto" onchange="sumar()" required>
                    </div>
                </div>

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorB" id="clasificadorB"></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_B" name="monto_impuesto_B" class="monto" onchange="sumar()">
                    </div>
                </div>

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorC" id="clasificadorC"></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_C" name="monto_impuesto_C" class="monto" onchange="sumar()">
                    </div>
                </div>

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorD" id="clasificadorD"></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_D" name="monto_impuesto_D" class="monto" onchange="sumar()">
                    </div>
                </div>

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorE" id="clasificadorE"></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_E" name="monto_impuesto_E" class="monto" onchange="sumar()">
                    </div>
                </div>

                <div class="clasificador-monto">
                    <select class="campo clasificador" name="id_clasificadorF" id="clasificadorF"></select>
                    <div class="campo">
                        <input type="number" step="any" id="monto_impuesto_F" name="monto_impuesto_F" class="monto" onchange="sumar()">
                    </div>
                </div>
            </div>

            <label>TOTAL A CANCELAR:</label>
            <input class="total" type="number" step="any" name="total_factura" id="total" value="0" readonly><br>

            <div id="alerta" class="alert d-none" role="alert"></div> 

            <div>
                <input class="btn oculto-impresion" type="reset" value="LIMPIAR">
                <button type="submit" class="btn oculto-impresion">IMPRIMIR</button>
            </div>
        </form>
    </div>

    <script>
        function sumar() {
            const totalInput = document.getElementById('total');
            let subtotal = 0;
            document.querySelectorAll('.monto').forEach((element) => {
                if (element.value !== '') {
                    subtotal += parseFloat(element.value);
                }
            });
            totalInput.value = subtotal.toFixed(2);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script>
        const form = document.getElementById('form-factura');
        const alerta = document.getElementById('alerta');
        const params = new URLSearchParams(window.location.search);
        const idContribuyente = params.get('id_contribuyente');

        function mostrarAlerta(tipo, mensaje) {
            alerta.className = `alert alert-${tipo} oculto-impresion`;
            alerta.textContent = mensaje;
            alerta.classList.remove('d-none');
        }

        function limpiarAlerta() {
            alerta.classList.add('d-none');
            alerta.textContent = '';
        }

        async function cargarClasificadores() {
            try {
                const respuesta = await apiRequest('clasificadores', 'list');
                const opciones = respuesta?.data ?? [];
                const selects = [
                    document.getElementById('clasificadorA'),
                    document.getElementById('clasificadorB'),
                    document.getElementById('clasificadorC'),
                    document.getElementById('clasificadorD'),
                    document.getElementById('clasificadorE'),
                    document.getElementById('clasificadorF'),
                ];

                selects.forEach((select, index) => {
                    if (!select) {
                        return;
                    }
                    select.innerHTML = '<option value="">---</option>';
                    opciones.forEach((item) => {
                        const opcion = document.createElement('option');
                        opcion.value = item.id_clasificador;
                        opcion.textContent = item.nombre;
                        select.appendChild(opcion);
                    });
                    if (index === 0 && select.options.length > 1) {
                        select.selectedIndex = 1;
                    }
                });
            } catch (error) {
                mostrarAlerta('danger', error.message || 'No fue posible cargar los clasificadores.');
            }
        }

        async function cargarContribuyente() {
            if (!idContribuyente) {
                mostrarAlerta('warning', 'Debe seleccionar un contribuyente válido.');
                form.querySelectorAll('input, select, button').forEach((elemento) => elemento.disabled = true);
                return;
            }

            try {
                const respuesta = await apiRequest('contribuyentes', 'show', {
                    params: { id_contribuyente: idContribuyente },
                });
                const contribuyente = respuesta?.data;

                if (!contribuyente) {
                    throw new Error('Contribuyente no encontrado.');
                }

                document.getElementById('cod_contribuyente').value = contribuyente.id_contribuyente;
                document.getElementById('codigo_mostrado').value = `00${contribuyente.id_contribuyente}`;
                document.getElementById('cedula_rif').value = contribuyente.cedula_rif;
                document.getElementById('razon_social').value = contribuyente.razon_social;
            } catch (error) {
                mostrarAlerta('danger', error.message || 'No fue posible obtener los datos del contribuyente.');
            }
        }

        async function cargarNumeroFactura() {
            try {
                const respuesta = await apiRequest('facturas', 'next_number');
                const numero = respuesta?.data?.next ?? '';
                document.getElementById('numero_factura').value = numero;
            } catch (error) {
                document.getElementById('numero_factura').value = '';
            }
        }

        function fechaActual() {
            const hoy = new Date();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0');
            const dia = String(hoy.getDate()).padStart(2, '0');
            return `${hoy.getFullYear()}-${mes}-${dia}`;
        }

        async function inicializar() {
            const licenciaActiva = await ensureLicenseActive({ silent: false });
            if (!licenciaActiva) {
                form.querySelectorAll('input, select, button').forEach((elemento) => elemento.disabled = true);
                return;
            }

            document.getElementById('fecha').value = fechaActual();
            await Promise.all([
                cargarClasificadores(),
                cargarContribuyente(),
                cargarNumeroFactura(),
            ]);
        }

        form.addEventListener('submit', async (evento) => {
            evento.preventDefault();
            limpiarAlerta();

            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            try {
                const respuesta = await apiRequest('facturas', 'store', {
                    method: 'POST',
                    body: payload,
                });

                mostrarAlerta('success', respuesta?.message || 'Factura generada correctamente.');
                await cargarNumeroFactura();
                window.print();
            } catch (error) {
                // Mostrar bloqueo visual cuando la licencia expiró usando SweetAlert2.
                if (window.Swal && error?.payload?.details?.code === 'LICENSE_EXPIRED') {
                    const contact = error.payload?.details?.support_contact;
                    const texto = contact ? `${error.message} (${contact})` : error.message;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Licencia expirada',
                        text: texto,
                        confirmButtonText: 'Cerrar',
                    });
                    // Deshabilitar el formulario para impedir nuevos intentos sin reactivar la licencia.
                    form.querySelectorAll('input, select, button').forEach((elemento) => elemento.disabled = true);
                    return;
                }

                mostrarAlerta('danger', error.message || 'No fue posible guardar la factura.');
            }
        });

        document.addEventListener('DOMContentLoaded', inicializar);
    </script>
</body>
</html>
