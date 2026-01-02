<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura | Alcaldia</title>
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
            width: 45px;
        }

        .cedula {
            width: 110px;
        }

        .nombre {
            width: 350px;
        }

        #concepto {
            width: 85%;
            border: none;
            background: transparent;
        }

        /* Estilos específicos para inputs en la tabla de items */
        .factura-item select.form-control-plaintext {
            padding: 0;
            font-weight: normal;
            cursor: pointer;
        }

        .factura-item input.form-control-plaintext {
            border-bottom: 1px dashed #ccc;
        }

        .factura-item input.form-control-plaintext:focus {
            border-bottom: 1px solid var(--color-primary);
            outline: none;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navReportes" role="button" data-bs-toggle="dropdown" aria-expanded="false">Reportes</a>
                        <ul class="dropdown-menu dropdown-menu-lg-start" aria-labelledby="navReportes">
                            <li><a class="dropdown-item" href="ingresos_diarios.php">Ingresos diarios</a></li>
                            <li><a class="dropdown-item" href="graficos.php">Graficos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="relacion_diaria.php">Relaciones diarias</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_contribuyente.php">Registrar
                            contribuyente</a></li>
                    <li class="nav-item"><a class="nav-link" href="registar_clasificador.php">Registrar clasificador</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Botones flotantes (ocultos en impresion) -->
    <div class="floating-action floating-left oculto-impresion">
        <button class="btn btn-app-primary" type="submit" form="form-factura">
            <i class="bi bi-printer-fill"></i> Guardar e Imprimir
        </button>
    </div>
    <div class="floating-action floating-right oculto-impresion">
        <a href="index.html" class="btn btn-app-outline">Volver</a>
    </div>

    <div class="container py-5">

        <!-- Mensajes -->
        <div id="alerta" class="alert d-none mb-4" role="alert"></div>

        <!-- Contenedor de Factura -->
        <div class="factura-container" id="factura-content">
            <form id="form-factura">
                <!-- Encabezado Factura -->
                <div class="factura-header">
                    <div class="d-flex align-items-center gap-3">
                        <img class="factura-logo" src="../logo.png" alt="logo alcaldía">
                        <div class="text-uppercase"
                            style="font-size: 0.8rem; line-height: 1.2; color: var(--color-text-body);">
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
                            <input type="text" class="form-control-plaintext p-0 fw-semibold text-end text-muted"
                                id="num_factura" aria-label="Número de factura" readonly placeholder="N° Control">
                            <input type="text" class="form-control-plaintext p-0 fw-semibold text-end text-muted" id="fecha"
                                name="fecha" required aria-label="Fecha de emisión" readonly placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="cod_contribuyente" id="cod_contribuyente_hidden">
                <input type="hidden" name="id_usuario" value="1">

                <!-- Información Principal compacta -->
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
                        <span class="label">Razon social:</span>
                        <input type="text" class="value nombre" id="razon_social" readonly aria-label="Razón social">
                    </div>
                </div>
                <div class="info-compact mb-3">
                    <div class="w-100">
                        <span class="label">Descripción</span>
                        <input type="text" class="value" name="concepto" id="concepto" value="DESCRIPCION">
                <!-- Detalle de Impuestos -->
                <div class="factura-detalle">
                    <div class="factura-detalle-header">
                        <div>Concepto / Clasificador</div>
                        <div class="text-end">Monto (Bs)</div>
                    </div>

                    <div id="detalles-container">
                        <!-- Los renglones se agregarán aquí dinámicamente -->
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-app-outline" onclick="agregarRenglon()">
                            <i class="bi bi-plus-circle"></i> Agregar Impuesto
                        </button>
                    </div>
                </div>

                <!-- Total -->
                <div class="factura-total">
                    <span class="factura-total-label">TOTAL A PAGAR:</span>
                    <input type="text" class="form-control-plaintext factura-total-value w-auto text-end p-0"
                        name="total_factura" id="total_factura" readonly value="0.00">
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/apiClient.js"></script>
    <script src="../js/license.js"></script>
    <script>
        const form = document.getElementById('form-factura');
        const alerta = document.getElementById('alerta');
        const params = new URLSearchParams(window.location.search);
        const idContribuyente = params.get('id_contribuyente');
        let clasificadoresCache = [];

        function sumar() {
            const totalInput = document.getElementById('total_factura');
            let subtotal = 0;
            document.querySelectorAll('.monto').forEach((element) => {
                if (element.value !== '') {
                    subtotal += parseFloat(element.value);
                }
            });
            totalInput.value = subtotal.toFixed(2);
        }

        function agregarRenglon() {
            const container = document.getElementById('detalles-container');
            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'factura-item d-flex align-items-center gap-2';
            div.id = `row-${index}`;

            // Select de clasificador
            const select = document.createElement('select');
            select.className = 'form-control-plaintext p-0 flex-grow-1';
            select.name = `detalles[${index}][id_clasificador]`;
            select.required = true;
            select.innerHTML = '<option value="">--- Seleccionar ---</option>';
            
            clasificadoresCache.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id_clasificador;
                option.textContent = item.nombre;
                select.appendChild(option);
            });

            // Input de monto
            const input = document.createElement('input');
            input.type = 'number';
            input.step = 'any';
            input.className = 'form-control-plaintext p-0 text-end monto';
            input.style.width = '120px';
            input.name = `detalles[${index}][monto_impuesto]`;
            input.placeholder = '0.00';
            input.required = true;
            input.onchange = sumar;

            // Botón eliminar
            const btnDelete = document.createElement('button');
            btnDelete.type = 'button';
            btnDelete.className = 'btn btn-link text-danger p-0';
            btnDelete.innerHTML = '<i class="bi bi-trash"></i>';
            btnDelete.onclick = function() {
                div.remove();
                sumar();
            };

            div.appendChild(select);
            div.appendChild(input);
            
            // Solo permitir eliminar si hay más de un renglón o si es un renglón extra
            if (index > 0) {
                 div.appendChild(btnDelete);
            }

            container.appendChild(div);
        }

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
                clasificadoresCache = respuesta?.data ?? [];
                
                // Agregar el primer renglón por defecto si no hay ninguno
                if (document.getElementById('detalles-container').children.length === 0) {
                    agregarRenglon();
                }
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

                // Actualizar campos visibles y ocultos
                document.getElementById('cod_contribuyente_hidden').value = contribuyente.id_contribuyente;
                document.getElementById('cod_contribuyente').value = String(contribuyente.id_contribuyente).padStart(4, '0');
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
                document.getElementById('num_factura').value = numero;
            } catch (error) {
                document.getElementById('num_factura').value = '';
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
            // Convertir FormData a objeto estructurado para manejar arrays
            const payload = {};
            formData.forEach((value, key) => {
                if (key.includes('[')) {
                    // Manejo simple para detalles[index][campo]
                    const match = key.match(/detalles\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!payload.detalles) payload.detalles = [];
                        if (!payload.detalles[index]) payload.detalles[index] = {};
                        payload.detalles[index][field] = value;
                    }
                } else {
                    payload[key] = value;
                }
            });
            
            // Limpiar nulls en detalles si el índice no es consecutivo (aunque aquí lo será)
            if (payload.detalles) {
                payload.detalles = payload.detalles.filter(el => el != null);
            }

            try {
                const respuesta = await apiRequest('facturas', 'store', {
                    method: 'POST',
                    body: payload,
                });

                mostrarAlerta('success', respuesta?.message || 'Factura generada correctamente.');
                await cargarNumeroFactura();
                
                // Reiniciar formulario pero mantener datos básicos
                const cod = document.getElementById('cod_contribuyente').value;
                const ced = document.getElementById('cedula_rif').value;
                const nom = document.getElementById('razon_social').value;
                const idc = document.getElementById('cod_contribuyente_hidden').value;
                
                //Para imprimir
                window.print();

                form.reset();
                document.getElementById('detalles-container').innerHTML = '';
                agregarRenglon(); // Restaurar un renglón vacío
                
                document.getElementById('cod_contribuyente').value = cod;
                document.getElementById('cedula_rif').value = ced;
                document.getElementById('razon_social').value = nom;
                document.getElementById('cod_contribuyente_hidden').value = idc;
                document.getElementById('fecha').value = fechaActual();

                // Opcional: Imprimir
               
            } catch (error) {
                if (window.Swal && error?.payload?.details?.code === 'LICENSE_EXPIRED') {
                    const contact = error.payload?.details?.support_contact;
                    const texto = contact ? `${error.message} (${contact})` : error.message;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Licencia expirada',
                        text: texto,
                        confirmButtonText: 'Cerrar',
                    });
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
