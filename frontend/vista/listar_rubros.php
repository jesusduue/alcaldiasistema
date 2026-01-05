<?php

require_once __DIR__ . '/partials/session_guard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relacion por rubros</title>
    <link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/theme.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/nav.php'; ?>

    <main class="container py-5">
        <header class="app-card mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h1 class="app-section-title h3 mb-2">Relacion por rubros</h1>
                <p class="app-subtitle mb-0 oculto-impresion">Montos agrupados por rubros tributarios para la fecha seleccionada. Imprime el reporte o regresa para elegir otra fecha.</p>
            </div>
            <button class="btn btn-app-outline print-hide" type="button" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir relacion
            </button>
        </header>

        <section class="app-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 print-hide">
                <div class="d-flex align-items-center gap-2">
                    <span class="app-pill">
                        <i class="bi bi-calendar3 me-1"></i>
                        Fecha seleccionada: <span id="fecha-consulta">--</span>
                    </span>
                </div>
                <a class="btn btn-app-outline" href="registar_clasificador.php#fecha_det_recibo">
                    <i class="bi bi-arrow-left-circle me-2"></i>Elegir otra fecha
                </a>
            </div>

            <div id="mensaje" class="alert d-none" role="alert"></div>

            <div class="table-responsive app-table">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Rubros</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-rubros"></tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-semibold text-uppercase">Total general</td>
                            <td class="fw-semibold" id="total-general">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </main>

    <script src="../js/apiClient.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const fecha = params.get('fecha_det_recibo');
        const tablaRubros = document.getElementById('tabla-rubros');
        const totalGeneralEl = document.getElementById('total-general');
        const mensaje = document.getElementById('mensaje');
        const fechaConsulta = document.getElementById('fecha-consulta');

        const TODOS_IMPUESTOS = [
            'DEUDA MOROSA DE CATASTRO',
            'DEUDA ACTUAL DE CATASTRO',
            'SOLVENCIA TIPO A',
            'CEDULA CATASTRAL',
            'SOLVENCIA MUNICIPAL',
            'PATENTE DE INDUSTRIA Y COMERCIO',
            'SOLVENCIA PATENTE',
            'RENOVACION PATENTE',
            'TRAMITACION PATENTE',
            'SOLVENCIA LICORES',
            'RENOVACION LICORES',
            'RENOVACION LICENCIA DE LICORES',
            'PUBLICIDAD Y PROPAGANDA',
            'DECLARACION ESTIMADA',
            'DEFINITIVA INGRESOS BRUTOS',
            'ESPECTACULOS PUBLICOS',
            'PATENTE DE VEHICULO FISCAL',
            'PERMISO EVENTUAL POR ENFERMEDAD LICORES',
            'PERMISO EVENTUAL ESPECTACULOS PUBLICOS LICORES',
            'MULTAS LICORES',
            'TRAMITACION LICENCIA DE LICORES',
            'APUESTAS LICITAS',
            'TRASPASO LICENCIA LICORES',
            'PERMISO MUNICIPALES',
            'PERMISO DE CONSTRUCCION',
            'USO CONFORME',
            'ZONIFICACION',
            'PERMISO DE ROTURA',
            'TERMINAL DE PASAJEROS LA FRIA',
            'MULTAS INGENIERIA',
            'PERMISO TEMPORAL DE LICORES',
            'MANTENIMIENTO ANUAL',
            'LEY DE CONTRATACIONES PUBLICAS',
            'OTROS INGRESOS EXTRAORDINARIOS'
        ];
        const RUBROS_BASE = new Set(TODOS_IMPUESTOS.map((nombre) => nombre.toUpperCase()));

        function mostrarMensaje(tipo, texto) {
            mensaje.className = `alert alert-${tipo}`;
            mensaje.textContent = texto;
            mensaje.classList.remove('d-none');
        }

        function limpiarMensaje() {
            mensaje.classList.add('d-none');
            mensaje.textContent = '';
        }

        function agregarMonto(coleccion, nombre, monto) {
            if (!nombre) {
                return;
            }
            const clave = String(nombre).toUpperCase();
            const valor = parseFloat(monto);
            if (Number.isNaN(valor)) {
                return;
            }
            if (!coleccion[clave]) {
                coleccion[clave] = 0;
            }
            coleccion[clave] += valor;
        }

        function renderizarTabla(agrupados) {
            tablaRubros.innerHTML = '';
            let totalGeneral = 0;

            TODOS_IMPUESTOS.forEach((nombre) => {
                const clave = nombre.toUpperCase();
                const monto = agrupados[clave];
                const esNumero = typeof monto === 'number' && !Number.isNaN(monto);
                if (esNumero) {
                    totalGeneral += monto;
                }
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${nombre}</td>
                    <td>${esNumero ? monto.toFixed(2) : '-'}</td>
                `;
                tablaRubros.appendChild(fila);
            });

            Object.entries(agrupados)
                .filter(([nombre]) => !RUBROS_BASE.has(nombre))
                .sort(([a], [b]) => a.localeCompare(b))
                .forEach(([nombre, monto]) => {
                    const esNumero = typeof monto === 'number' && !Number.isNaN(monto);
                    if (esNumero) {
                        totalGeneral += monto;
                    }
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${nombre}</td>
                        <td>${esNumero ? monto.toFixed(2) : '-'}</td>
                    `;
                    tablaRubros.appendChild(fila);
                });

            totalGeneralEl.textContent = totalGeneral.toFixed(2);
        }

        async function cargarRubros() {
            limpiarMensaje();
            tablaRubros.innerHTML = `<tr><td colspan="2" class="text-center py-4">Cargando informacion...</td></tr>`;
            totalGeneralEl.textContent = '0.00';

            if (!fecha) {
                mostrarMensaje('warning', 'Debe proporcionar una fecha para consultar los rubros.');
                tablaRubros.innerHTML = '';
                return;
            }

            fechaConsulta.textContent = fecha;

            try {
                const respuesta = await apiRequest('detalles', 'by_fecha', {
                    params: { fecha_det_recibo: fecha },
                });

                const detalles = respuesta?.data ?? [];
                const agrupados = {};

                detalles.forEach((item) => {
                    agregarMonto(agrupados, item.nombre_impuesto, item.total_monto);
                });

                if (!detalles.length) {
                    mostrarMensaje('info', 'No hay registros de rubros para la fecha indicada.');
                }

                renderizarTabla(agrupados);
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la informacion por rubros.');
                tablaRubros.innerHTML = '';
            }
        }

        document.addEventListener('DOMContentLoaded', cargarRubros);
    </script>
</body>
</html>
