<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anular Factura | Alcaldia</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .floating-action {
            position: fixed;
            bottom: 20px;
            z-index: 1050;
            display: flex;
            align-items: center;
        }

        .floating-left {
            left: 20px;
        }

        .floating-right {
            right: 20px;
        }

        @media (max-width: 576px) {
            .floating-action {
                bottom: 12px;
            }

            .floating-left {
                left: 12px;
            }

            .floating-right {
                right: 12px;
            }
        }

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
            background: transparent;
            padding: 6px 10px 4px;
            white-space: nowrap;
            min-width: 0;
        }

        .info-chip .label {
            font-size: 1rem;
            color: var(--color-text-head);
            text-transform: uppercase;
        }

        .info-chip .value {
            border: none;
            background: transparent;
            padding: 0;
            margin: 0;
            font-weight: 600;
            color: var(--color-text-head);
            min-width: 0;
        }

        .info-chip .value:focus {
            outline: none;
        }

        .info-line {
            border-bottom: 2px solid #7f1d1d;
            margin-top: 2px;
            margin-bottom: 1px;
        }

        #concepto {
            width: 100%;
            border: none;
            background: transparent;
        }

        .factura-total {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
        }

        .factura-total-label {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-text-head);
        }

        .factura-total-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--color-primary);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar py-3 oculto-impresion">
        <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-semibold" href="index.html">
            <img src="../logo.png" alt="Logo">
            Alcaldia Sistema
        </a>
        <div class="container">
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="ingresos_diarios.php">Ingresos diarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="relacion_diaria.php">Relaciones diarias</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar contribuyente</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Botones flotantes -->
    <div class="floating-action floating-left oculto-impresion">
        <button class="btn btn-app-primary" type="submit" form="form-anular">
            <i class="bi bi-x-circle-fill me-1"></i>Anular
        </button>
    </div>
    <div class="floating-action floating-right oculto-impresion">
        <a href="index.html" class="btn btn-app-outline">Volver</a>
    </div>

    <div class="container py-5">
        <div id="mensaje" class="alert d-none mb-4" role="alert"></div>

        <div class="factura-container" id="factura-content">
            <form id="form-anular">
                <div class="factura-header">
                    <div class="d-flex align-items-center gap-3">
                        <img class="factura-logo" src="../logo.png" alt="logo alcaldía">
                        <div class="text-uppercase"
                            style="font-size: 0.8rem; line-height: 1.2; color: var(--color-text-body);">
                            <strong>Republica Bolivariana de Venezuela</strong><br>
                            Alcaldia del Municipio García de Hevia<br>
                            La Fría - Edo. Táchira<br>
                            RIF: G-20001125-4<br>
                            Dirección de Hacienda
                        </div>
                    </div>
                    <div class="factura-titulo">
                        <h2>Factura</h2>
                        <div style="font-size: 0.8rem; line-height: 1.2; color: var(--color-text-body);">
                            Comprobante de Pago
                            <input type="text" class="form-control-plaintext p-0 fw-semibold text-end text-muted"
                                id="num_factura" aria-label="Número de factura" readonly placeholder="N° Control">
                            <input type="date" class="form-control-plaintext p-0 fw-semibold text-end text-muted" id="fecha"
                                name="fecha" required aria-label="Fecha de emisión">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id_usuario" value="1">

                <div class="info-compact info-compact-row mb-3 form">
                    <div class="info-chip">
                        <span class="label">Cod:</span>
                        <input type="text" class="value codigo" id="cod_contribuyente" readonly
                            aria-label="Código del contribuyente">
                    </div>
                    <div class="info-chip">
                        <span class="label">Cédula/RIF:</span>
                        <input type="text" class="value cedula" id="cedula_rif" readonly aria-label="Cédula o RIF">
                    </div>
                    <div class="info-chip">
                        <span class="label">Razón social:</span>
                        <input type="text" class="value nombre" id="razon_social" readonly aria-label="Razón social">
                    </div>
                </div>

                <div class="info-compact mb-3">
                    <div class="w-100">
                        <span class="label">Descripción</span>
                        <input type="text" class="value" name="concepto" id="concepto" aria-label="Descripción o concepto">
                        <div class="info-line"></div>
                    </div>
                </div>

                <div class="info-compact mb-3">
                    <div class="info-chip">
                        <span class="label">Estado del recibo:</span>
                        <input type="text" class="value" name="ESTADO_FACT" id="ESTADO_FACT" value="NULO"
                            aria-label="Estado de la factura (ej: NULO)">
                    </div>
                </div>

                <div class="factura-total">
                    <span class="factura-total-label">Total cancelado:</span>
                    <input class="form-control-plaintext factura-total-value w-auto text-end p-0"
                        type="text" name="total_factura" id="total_factura" readonly value="0.00">
                </div>
            </form>
        </div>
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
            mensaje.className = `alert alert-${tipo} oculto-impresion`;
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
                document.getElementById('cod_contribuyente').value = String(factura.cod_contribuyente || '').padStart(4, '0');
                document.getElementById('cedula_rif').value = factura.cedula_rif;
                document.getElementById('razon_social').value = factura.razon_social;
                document.getElementById('concepto').value = factura.concepto ?? '';
                document.getElementById('total_factura').value = (factura.total_factura ?? 0).toFixed(2);
                document.getElementById('ESTADO_FACT').value = factura.ESTADO_FACT ?? 'NULO';
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

                mostrarMensaje('success', respuesta?.message || 'Factura anulada correctamente.');
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
