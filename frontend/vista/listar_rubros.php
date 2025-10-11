<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rubros</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0px;

        }
        h1 {
            text-align: center;
            font-size: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;

        }
        table, th, td {
            border-bottom: 1px solid #000;


        }
         .rubro {
            text-align: center;
            font-size: 12px;
        }
        th, td {
            padding: 7.5px;
        

        }
        th {
            background-color: #fff;
        }
        .total {
            font-weight: bold;
        }
        .date {
            font-weight: bold;
            font-size: 11px;
        }
        .nom_impuesto{
        	font-size: 11px;
        }
        .tot_impuesto{
        	font-size: 11px;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Relación por Rubros</h1>
        <div class="date">
            Fecha: <span id="fecha-consulta">--</span>
        </div>
        <div id="mensaje" class="alert" style="display:none;"></div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="rubro">Rubros</th>
                        <th class="rubro">Total</th>
                    </tr>
                </thead>
                <tbody id="tabla-rubros"></tbody>
                <tfoot>
                    <tr>
                        <td class="total">Total General</td>
                        <td class="total" id="total-general">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="../js/apiClient.js"></script>
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

        function mostrarMensaje(tipo, texto) {
            mensaje.style.display = 'block';
            mensaje.className = `alert alert-${tipo}`;
            mensaje.textContent = texto;
        }

        function limpiarMensaje() {
            mensaje.style.display = 'none';
            mensaje.textContent = '';
            mensaje.className = 'alert';
        }

        function agregarMonto(coleccion, nombre, monto) {
            if (!nombre || nombre === 'null' || nombre === '-') {
                return;
            }
            const valor = parseFloat(monto);
            if (Number.isNaN(valor)) {
                return;
            }
            if (!coleccion[nombre]) {
                coleccion[nombre] = 0;
            }
            coleccion[nombre] += valor;
        }

        function renderizarTabla(agrupados) {
            tablaRubros.innerHTML = '';
            let totalGeneral = 0;

            TODOS_IMPUESTOS.forEach((nombre) => {
                const monto = agrupados[nombre];
                const esNumero = typeof monto === 'number' && !Number.isNaN(monto);
                if (esNumero) {
                    totalGeneral += monto;
                }
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td class="nom_impuesto">${nombre}</td>
                    <td class="tot_impuesto">${esNumero ? monto.toFixed(2) : '-'}</td>
                `;
            tablaRubros.appendChild(fila);
            });

            totalGeneralEl.textContent = totalGeneral.toFixed(2);
        }

        async function cargarRubros() {
            limpiarMensaje();
            tablaRubros.innerHTML = `<tr><td colspan="2" class="text-center">Cargando información...</td></tr>`;
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
                    agregarMonto(agrupados, item.nombre_impuesto_A, item.monto_impuesto_A);
                    agregarMonto(agrupados, item.nombre_impuesto_B, item.monto_impuesto_B);
                    agregarMonto(agrupados, item.nombre_impuesto_C, item.monto_impuesto_C);
                    agregarMonto(agrupados, item.nombre_impuesto_D, item.monto_impuesto_D);
                    agregarMonto(agrupados, item.nombre_impuesto_E, item.monto_impuesto_E);
                    agregarMonto(agrupados, item.nombre_impuesto_F, item.monto_impuesto_F);
                });

                renderizarTabla(agrupados);
            } catch (error) {
                mostrarMensaje('danger', error.message || 'No fue posible obtener la información por rubros.');
                tablaRubros.innerHTML = '';
            }
        }

        document.addEventListener('DOMContentLoaded', cargarRubros);
    </script>
</body>
</html>
