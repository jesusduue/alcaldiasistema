<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/estilos.css">
    <link rel="stylesheet" href="../css/theme.css">
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

        /* Estilo compacto alineado con reimprimir_factura */
        .info-compact {
            display: flex;
            gap: 12px;
            align-items: center;
            font-size: 0.95rem;
        }

        .info-compact-row {
            flex-wrap: nowrap;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .info-compact-row {
                flex-wrap: wrap;
            }
        }

        .info-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f7f7f7;
            padding: 6px 10px 4px;
            border-bottom: 2px double #7f1d1d;
            white-space: nowrap;
            min-width: 0;
        }

        .info-chip .label {
            font-weight: 700;
            text-transform: uppercase;
            color: #1c1917;
        }

        .info-chip .value,
        .info-compact .value {
            border: none;
            background: transparent;
            padding: 0;
            margin: 0;
            font-weight: 600;
            color: #1c1917;
            min-width: 0;
        }

        .info-chip .value:focus,
        .info-compact .value:focus {
            outline: none;
        }

        .info-line {
            border-bottom: 2px double #7f1d1d;
            margin-top: 8px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
      <nav class="navbar navbar-expand-lg navbar-dark app-navbar py-3 oculto-impresion">
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

        <form id="form-anular">
            <div class="fila">
                <div class="fecha-recibo">FECHA:
                    <input type="date" class="FECHA" name="fecha" id="fecha" required>
                </div>
                <div class="campo numero-factura">NUMERO FACTURA:
                    <input type="text" class="factura" name="num_factura" id="num_factura" readonly>
                </div>
            </div>

            <div class="info-compact info-compact-row mb-3">
                <div class="info-chip">
                    <span class="label">Código:</span>
                    <input class="value" type="text" id="cod_contribuyente" readonly aria-label="Código del contribuyente">
                </div>
                <div class="info-chip">
                    <span class="label">Cédula/RIF:</span>
                    <input class="value" type="text" id="cedula_rif" readonly aria-label="Cédula o RIF">
                </div>
                <div class="info-chip">
                    <span class="label">Nombre:</span>
                    <input class="value" type="text" id="razon_social" readonly aria-label="Razón social">
                </div>
            </div>

            <div class="info-compact mb-3">
                <div class="w-100">
                    <span class="label d-block mb-1">Descripción</span>
                    <div class="info-line"></div>
                    <input type="text" class="value w-100" name="concepto" id="concepto" aria-label="Descripción o concepto">
                    <div class="info-line"></div>
                </div>
            </div>

            <label>TOTAL CANCELADO:</label>
            <input class="total" type="number" step="any" name="total_factura" id="total_factura" readonly><br>

            <label for="ESTADO_FACT">ESTADO:</label>
            <input class="nulo" type="text" name="ESTADO_FACT" id="ESTADO_FACT" value="nulo">

            <div id="mensaje" class="alert d-none" role="alert"></div>

            <div>
                <button type="submit" class="btn oculto-impresion">GUARDAR RECIBO</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script>
        const form = document.getElementById('form-anular');
        const mensaje = document.getElementById('mensaje');
        const params = new URLSearchParams(window.location.search);
        const numFactura = params.get('num_factura');

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
                form.querySelectorAll('input, button').forEach((elemento) => elemento.disabled = true);
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
                document.getElementById('total_factura').value = factura.total_factura ?? 0;
                document.getElementById('ESTADO_FACT').value = factura.ESTADO_FACT ?? 'nulo';
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la factura.');
                form.querySelectorAll('input, button').forEach((elemento) => elemento.disabled = true);
            }
        }

        form.addEventListener('submit', async (evento) => {
            evento.preventDefault();
            limpiarMensaje();

            const licenciaActiva = await ensureLicenseActive();
            if (!licenciaActiva) {
                form.querySelectorAll('input, button').forEach((elemento) => elemento.disabled = true);
                return;
            }

            const payload = {
                num_factura: document.getElementById('num_factura').value,
                fecha: document.getElementById('fecha').value,
                cod_contribuyente: document.getElementById('cod_contribuyente').value,
                concepto: document.getElementById('concepto').value,
                total_factura: document.getElementById('total_factura').value,
                ESTADO_FACT: document.getElementById('ESTADO_FACT').value,
            };

            try {
                const respuesta = await apiRequest('facturas', 'update', {
                    method: 'POST',
                    body: payload,
                });

                mostrarMensaje('success', respuesta?.message || 'Factura actualizada correctamente.');
            } catch (error) {
                if (error?.payload?.details?.code === 'LICENSE_EXPIRED') {
                    await ensureLicenseActive({ force: true });
                    form.querySelectorAll('input, button').forEach((elemento) => elemento.disabled = true);
                    return;
                }
                mostrarMensaje('danger', error.message || 'No fue posible actualizar la factura.');
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {
            const licenciaActiva = await ensureLicenseActive({ silent: false });
            if (!licenciaActiva) {
                form.querySelectorAll('input, button').forEach((elemento) => elemento.disabled = true);
                return;
            }
            cargarFactura();
        });
    </script>
</body>
</html>
