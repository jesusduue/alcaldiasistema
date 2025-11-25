<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../css/theme.css">
    <title>Reimprimir Factura | Alcaldia</title>
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

        /* Compacto para encabezado de datos */
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
            /* border-bottom: 2px double #7f1d1d; */
            white-space: nowrap;
            min-width: 0;
        }

        .info-chip .label {
            font-size: 1rem;

            color: var(--color-text-head);
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

        .codigo {
            width: 35px;
        }

        .cedula {
            width: 100px;
        }

        .nombre {
            width: 350px;
        }

        #concepto {
            width: 85%;
            border: none;
            background: transparent;
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
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
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

    <!-- Botones flotantes (ocultos en impresion) -->
    <div class="floating-action floating-left oculto-impresion">
        <button class="btn btn-app-primary" id="btn-imprimir">
            <i class="bi bi-printer-fill"></i> Imprimir Factura
        </button>
    </div>
    <div class="floating-action floating-right oculto-impresion">
        <a href="index.html" class="btn btn-app-outline">Volver</a>
    </div>

    <div class="container py-5">

        <!-- Mensajes -->
        <div id="mensaje" class="alert d-none mb-4" role="alert"></div>

        <!-- Contenedor de Factura -->
        <div class="factura-container" id="factura-content">
            <!-- Encabezado Factura -->
            <div class="factura-header">
                <div class="d-flex align-items-center gap-3">
                    <img class="factura-logo" src="../logo.png" alt="logo alcaldía">
                    <div class="text-uppercase" style="font-size: 0.8rem; line-height: 1.2; color: var(--color-text-body);">
                        <strong>República Bolivariana de Venezuela</strong><br>
                        Alcaldia del Municipio García de Hevia<br>
                        La Fría - Edo. Táchira<br>
                        RIF: G-20001125-4<br>
                        Dirección de Hacienda
                    </div>
                </div>
                <div class="factura-titulo">
                    <h2>Factura</h2>
                    <div style="font-size: 0.8rem; line-height: 1.2; color: var(--color-text-body);">Comprobante de Pago
                        <input type="text" class="form-control-plaintext p-0 fw-semibold text-end text-muted" id="num_factura" aria-label="Número de factura" readonly>
                        <input type="text" class="form-control-plaintext p-0 fw-semibold text-end text-muted" id="fecha" aria-label="Fecha de emisión" readonly>
                    </div>
                </div>
            </div>

            <form id="form-reimprimir">
                <!-- Información Principal compacta -->
                <div class="info-compact info-compact-row mb-3 form">
                    <div class="info-chip">
                        <span class="label">Cod:</span>
                        <input type="text" class="value codigo" id="cod_contribuyente" readonly aria-label="Código del contribuyente">
                    </div>
                    <div class="info-chip">
                        <span class="label">Cédula/RIF:</span>
                        <input type="text" class="value cedula" id="cedula_rif" readonly aria-label="Cédula o RIF">
                    </div>
                    <div class="info-chip">
                        <span class="label">Razon social:</span>
                        <input type="text" class="value nombre" id="razon_social" readonly aria-label="Razón social">
                    </div>
                </div>
                <div class="info-compact mb-3">
                    <div class="w-100">
                        <span class="label">Descripción</span>
                        <!-- <div class="info-line"></div> -->
                        <input type="text" class="value" id="concepto" readonly aria-label="Descripción o concepto">
                        <div class="info-line"></div>
                    </div>
                </div>

                <!-- Detalle de Impuestos -->
                <div class="factura-detalle">
                    <div class="factura-detalle-header">
                        <div>Concepto / Clasificador</div>
                        <div class="text-end">Monto (Bs)</div>
                    </div>

                    <!-- Items A-F -->
                    <div class="factura-item" id="row-A">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorA" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_A" readonly>
                    </div>
                    <div class="factura-item" id="row-B">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorB" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_B" readonly>
                    </div>
                    <div class="factura-item" id="row-C">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorC" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_C" readonly>
                    </div>
                    <div class="factura-item" id="row-D">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorD" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_D" readonly>
                    </div>
                    <div class="factura-item" id="row-E">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorE" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_E" readonly>
                    </div>
                    <div class="factura-item" id="row-F">
                        <input type="text" class="form-control-plaintext p-0" id="clasificadorF" readonly>
                        <input type="text" class="form-control-plaintext p-0 text-end" id="monto_impuesto_F" readonly>
                    </div>
                </div>

                <!-- Total -->
                <div class="factura-total">
                    <span class="factura-total-label">TOTAL A PAGAR:</span>
                    <input type="text" class="form-control-plaintext factura-total-value w-auto text-end p-0" id="total_factura" readonly>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/apiClient.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const numFactura = params.get('num_factura');
        const mensaje = document.getElementById('mensaje');
        const btnImprimir = document.getElementById('btn-imprimir');
        let clasificadores = [];

        function mostrarMensaje(tipo, texto) {
            mensaje.className = `alert alert-${tipo}`;
            mensaje.textContent = texto;
            mensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            mensaje.classList.add('d-none');
            mensaje.textContent = '';
        }

        async function cargarClasificadores() {
            try {
                const respuesta = await apiRequest('clasificadores', 'list');
                clasificadores = respuesta?.data ?? [];
            } catch (error) {
                console.error('Error al cargar clasificadores:', error);
            }
        }

        function obtenerNombreClasificador(id) {
            if (!id) return '';
            const encontrado = clasificadores.find(c => c.id_clasificador == id);
            return encontrado ? encontrado.nombre : id;
        }

        async function cargarFactura() {
            if (!numFactura) {
                mostrarMensaje('warning', 'Debe indicar un número de factura válido.');
                btnImprimir.disabled = true;
                return;
            }

            try {
                const respuesta = await apiRequest('facturas', 'show', {
                    params: {
                        num_factura: numFactura
                    },
                });
                const factura = respuesta?.data;

                if (!factura) {
                    throw new Error('Factura no encontrada.');
                }

                document.getElementById('fecha').value = factura.fecha;
                const numeroControl = factura.numero_control ?? factura.num_factura;
                document.getElementById('num_factura').value = numeroControl ?? '';
                document.getElementById('cod_contribuyente').value = factura.cod_contribuyente;
                document.getElementById('cedula_rif').value = factura.cedula_rif;
                document.getElementById('razon_social').value = factura.razon_social;
                document.getElementById('concepto').value = factura.concepto ?? '';
                document.getElementById('total_factura').value = factura.total_factura ?? '';

                // Cargar impuestos
                const letras = ['A', 'B', 'C', 'D', 'E', 'F'];
                letras.forEach(letra => {
                    const monto = factura[`monto_impuesto_${letra}`];
                    const clasificadorInput = document.getElementById(`clasificador${letra}`);
                    const montoInput = document.getElementById(`monto_impuesto_${letra}`);
                    const row = document.getElementById(`row-${letra}`);

                    if (monto && parseFloat(monto) > 0) {
                        montoInput.value = monto;
                        const idClasificador = factura[`impuesto_${letra}`] || factura[`id_clasificador${letra}`];
                        const nombreClasificador = factura[`nombre_impuesto_${letra}`] || obtenerNombreClasificador(idClasificador);
                        clasificadorInput.value = nombreClasificador;
                        row.style.display = 'grid'; // Mostrar fila si tiene datos
                    } else {
                        montoInput.value = '';
                        clasificadorInput.value = '';
                        row.style.display = 'none'; // Ocultar fila vacía para limpiar el diseño
                    }
                });

            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la factura.');
                btnImprimir.disabled = true;
            }
        }

        btnImprimir.addEventListener('click', () => {
            window.print();
        });

        document.addEventListener('DOMContentLoaded', async () => {
            limpiarMensaje();
            await cargarClasificadores();
            await cargarFactura();
        });
    </script>
</body>

</html>