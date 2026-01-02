document.addEventListener('DOMContentLoaded', () => {
    const tablaDiaria = document.getElementById('tabla-diaria');
    const tablaRubros = document.getElementById('tabla-rubros');
    const tituloDetalle = document.getElementById('titulo-detalle');
    const rubroFechaLabel = document.getElementById('rubro-fecha-label');
    const resumenBruto = document.getElementById('resumen-bruto');
    const resumenAnulados = document.getElementById('resumen-anulados');
    const resumenNeto = document.getElementById('resumen-neto');
    const resumenRecibos = document.getElementById('resumen-recibos');
    const formFiltros = document.getElementById('form-filtros');
    const inputDesde = document.getElementById('fecha_desde');
    const inputHasta = document.getElementById('fecha_hasta');
    const btnHoy = document.getElementById('btn-hoy');

    let facturas = [];
    let fechaSeleccionada = '';
    let modoVisualizacion = 'rubros'; // 'rubros' | 'pagos'

    const numberFormat = new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    function formatoMonto(valor) {
        const numero = Number(valor) || 0;
        return numberFormat.format(numero);
    }

    function setHoy() {
        const hoy = new Date();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        const hoyStr = `${hoy.getFullYear()}-${mes}-${dia}`;
        inputDesde.value = hoyStr;
        inputHasta.value = hoyStr;
    }

    function parseFecha(valor) {
        if (!valor) {
            return null;
        }
        const [y, m, d] = valor.split('-').map((v) => parseInt(v, 10));
        if (!y || !m || !d) {
            return null;
        }
        return new Date(Date.UTC(y, m - 1, d));
    }

    function enRango(fechaTexto, desde, hasta) {
        const fecha = parseFecha(fechaTexto?.slice(0, 10));
        if (!fecha) {
            return false;
        }
        if (desde && fecha < desde) {
            return false;
        }
        if (hasta && fecha > hasta) {
            return false;
        }
        return true;
    }

    function esAnulada(factura) {
        const estadoPago = String(factura?.estado_pago ?? '').toUpperCase();
        const estadoTexto = String(factura?.ESTADO_FACT ?? '').toUpperCase();
        return estadoPago === 'N' || estadoTexto === 'NULO';
    }

    function agruparPorFecha(filtradas) {
        const mapa = new Map();

        filtradas.forEach((factura) => {
            const fecha = (factura.fecha || '').slice(0, 10);
            if (!fecha) {
                return;
            }
            if (!mapa.has(fecha)) {
                mapa.set(fecha, {
                    fecha,
                    recibos: 0,
                    montoBruto: 0,
                    anulados: 0,
                });
            }
            const grupo = mapa.get(fecha);
            const monto = Number(factura.total_factura) || 0;
            grupo.recibos += 1;
            grupo.montoBruto += monto;
            if (esAnulada(factura)) {
                grupo.anulados += monto;
            }
        });

        const lista = Array.from(mapa.values());
        lista.forEach((item) => {
            item.neto = item.montoBruto - item.anulados;
        });

        return lista.sort((a, b) => a.fecha.localeCompare(b.fecha));
    }

    function limpiarResumen() {
        resumenBruto.textContent = '-';
        resumenAnulados.textContent = '-';
        resumenNeto.textContent = '-';
        resumenRecibos.textContent = '-';
    }

    function renderResumen(filtradas) {
        if (!filtradas.length) {
            limpiarResumen();
            return;
        }
        let bruto = 0;
        let anulados = 0;
        filtradas.forEach((factura) => {
            const monto = Number(factura.total_factura) || 0;
            bruto += monto;
            if (esAnulada(factura)) {
                anulados += monto;
            }
        });
        const neto = bruto - anulados;

        resumenBruto.textContent = formatoMonto(bruto);
        resumenAnulados.textContent = formatoMonto(anulados);
        resumenNeto.textContent = formatoMonto(neto);
        resumenRecibos.textContent = filtradas.length.toString();
    }

    function renderTablaDiaria(agrupados) {
        if (!agrupados.length) {
            tablaDiaria.innerHTML = '<p class="text-center py-4 text-muted mb-0">No hay datos en el rango seleccionado.</p>';
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-hover align-middle mb-0';
        tabla.innerHTML = `
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-center">Recibos</th>
                    <th class="text-end">Sub total</th>
                    <th class="text-end">Anulados</th>
                    <th class="text-end">Total</th>
                    <th class="text-center oculto-impresion">Rubros</th>
                </tr>
            </thead>
        `;

        const tbody = document.createElement('tbody');
        agrupados.forEach((item) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${item.fecha}</td>
                <td class="text-center">${item.recibos}</td>
                <td class="text-end">${formatoMonto(item.montoBruto)}</td>
                <td class="text-end text-danger">${formatoMonto(item.anulados)}</td>
                <td class="text-end fw-semibold">${formatoMonto(item.neto)}</td>
                <td class="text-center oculto-impresion">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-app-outline" data-accion="ver-rubros" data-fecha="${item.fecha}">
                            Ver rubros
                        </button>
                        <button class="btn btn-sm btn-app-outline" data-accion="ver-pagos" data-fecha="${item.fecha}">
                            Ver pagos
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(fila);
        });

        tabla.appendChild(tbody);
        tablaDiaria.innerHTML = '';
        tablaDiaria.appendChild(tabla);
    }

    function renderTablaRubros(rubros) {
        if (!fechaSeleccionada) {
            tablaRubros.innerHTML = '<p class="text-center py-4 text-muted mb-0">Selecciona una fecha de la tabla superior para ver los rubros.</p>';
            return;
        }
        if (!rubros.length) {
            tablaRubros.innerHTML = `<p class="text-center py-4 text-muted mb-0">No hay rubros registrados para ${fechaSeleccionada}.</p>`;
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-hover align-middle mb-0';
        tabla.innerHTML = `
            <thead>
                <tr>
                    <th>Rubro / Impuesto</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
        `;

        const tbody = document.createElement('tbody');
        let totalRubros = 0;

        rubros.forEach((rubro) => {
            const monto = Number(rubro.total_monto) || 0;
            totalRubros += monto;
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${rubro.nombre_impuesto ?? 'Sin nombre'}</td>
                <td class="text-end">${formatoMonto(monto)}</td>
            `;
            tbody.appendChild(fila);
        });

        const total = document.createElement('tr');
        total.innerHTML = `
            <td class="text-end text-uppercase fw-semibold">Total</td>
            <td class="text-end fw-semibold">${formatoMonto(totalRubros)}</td>
        `;
        tbody.appendChild(total);

        tabla.appendChild(tbody);
        tablaRubros.innerHTML = '';
        tablaRubros.appendChild(tabla);
    }

    function renderTablaPagos(listaPagos) {
        if (!fechaSeleccionada) {
            tablaRubros.innerHTML = '<p class="text-center py-4 text-muted mb-0">Selecciona una fecha de la tabla superior para ver los pagos.</p>';
            return;
        }
        if (!listaPagos.length) {
            tablaRubros.innerHTML = `<p class="text-center py-4 text-muted mb-0">No hay pagos registrados para ${fechaSeleccionada}.</p>`;
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-hover align-middle mb-0';
        tabla.innerHTML = `
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Fecha</th>
                    <th>Cédula/RIF</th>
                    <th>Razón Social</th>
                    <th>Concepto</th>
                    <th class="text-end">Monto</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
        `;

        const tbody = document.createElement('tbody');
        let totalSum = 0;
        let anuladosSum = 0;

        listaPagos.forEach((pago) => {
            const monto = parseFloat(pago.total_factura ?? 0);
            if (!Number.isNaN(monto)) {
                totalSum += monto;
                if (esAnulada(pago)) {
                    anuladosSum += monto;
                }
            }

            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${pago.num_factura}</td>
                <td>${pago.fecha}</td>
                <td>${pago.cedula_rif}</td>
                <td>${pago.razon_social}</td>
                <td>${pago.concepto ?? ''}</td>
                <td class="text-end">${formatoMonto(monto)}</td>
                <td class="text-center">${pago.ESTADO_FACT ?? ''}</td>
            `;
            tbody.appendChild(fila);
        });

        const totalFinal = totalSum - anuladosSum;

        const tfoot = document.createElement('tfoot');
        tfoot.innerHTML = `
            <tr>
                <td colspan="5" class="text-end fw-semibold text-uppercase">Sub-total:</td>
                <td class="text-end">${formatoMonto(totalSum)}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" class="text-end fw-semibold text-uppercase">Total recibos anulados:</td>
                <td class="text-end text-danger">${formatoMonto(anuladosSum)}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" class="text-end fw-semibold text-uppercase">Total general:</td>
                <td class="text-end fw-semibold">${formatoMonto(totalFinal)}</td>
                <td></td>
            </tr>
        `;
        tabla.appendChild(tbody);
        tabla.appendChild(tfoot);

        tablaRubros.innerHTML = '';
        tablaRubros.appendChild(tabla);
    }

    async function cargarRubros(fecha) {
        fechaSeleccionada = fecha;
        modoVisualizacion = 'rubros';
        rubroFechaLabel.textContent = fecha || '-';
        tituloDetalle.textContent = 'Detalle por rubros';

        if (!fecha) {
            renderTablaRubros([]);
            return;
        }

        tablaRubros.innerHTML = '<p class="text-center py-4 text-muted mb-0">Cargando rubros...</p>';
        try {
            const respuesta = await apiRequest('detalles', 'by_fecha', {
                params: { fecha_det_recibo: fecha },
            });
            renderTablaRubros(respuesta?.data ?? []);
        } catch (error) {
            tablaRubros.innerHTML = `<p class="text-center text-danger py-4 mb-0">${error.message || 'No fue posible cargar los rubros.'}</p>`;
        }
    }

    async function cargarPagos(fecha) {
        fechaSeleccionada = fecha;
        modoVisualizacion = 'pagos';
        rubroFechaLabel.textContent = fecha || '-';
        tituloDetalle.textContent = 'Detalle de pagos';

        if (!fecha) {
            renderTablaPagos([]);
            return;
        }

        tablaRubros.innerHTML = '<p class="text-center py-4 text-muted mb-0">Cargando pagos...</p>';
        try {
            const respuesta = await apiRequest('facturas', 'by_fecha', {
                params: { fecha },
            });
            renderTablaPagos(respuesta?.data ?? []);
        } catch (error) {
            tablaRubros.innerHTML = `<p class="text-center text-danger py-4 mb-0">${error.message || 'No fue posible cargar los pagos.'}</p>`;
        }
    }

    function fechaValida(desde, hasta) {
        if (desde && hasta && desde > hasta) {
            tablaDiaria.innerHTML = '<p class="text-center text-danger py-4 mb-0">La fecha "Desde" no puede ser mayor que "Hasta".</p>';
            limpiarResumen();
            return false;
        }
        return true;
    }

    function aplicarFiltros() {
        const desde = parseFecha(inputDesde.value);
        const hasta = parseFecha(inputHasta.value);

        if (!fechaValida(desde, hasta)) {
            tablaRubros.innerHTML = '';
            rubroFechaLabel.textContent = '-';
            return;
        }

        const filtradas = facturas.filter((factura) => enRango(factura.fecha, desde, hasta));

        renderResumen(filtradas);

        const agrupados = agruparPorFecha(filtradas);
        renderTablaDiaria(agrupados);

        const fechaDestino = agrupados.length
            ? (agrupados.some((item) => item.fecha === fechaSeleccionada)
                ? fechaSeleccionada
                : agrupados[agrupados.length - 1].fecha)
            : '';

        if (modoVisualizacion === 'pagos') {
            cargarPagos(fechaDestino);
        } else {
            cargarRubros(fechaDestino);
        }
    }

    async function cargarFacturas() {
        tablaDiaria.innerHTML = '<p class="text-center py-4 text-muted mb-0">Cargando datos...</p>';
        try {
            const respuesta = await apiRequest('facturas', 'list');
            facturas = respuesta?.data ?? [];
            aplicarFiltros();
        } catch (error) {
            tablaDiaria.innerHTML = `<p class="text-center text-danger py-4 mb-0">${error.message || 'No fue posible cargar los ingresos.'}</p>`;
            limpiarResumen();
        }
    }

    formFiltros.addEventListener('submit', (evento) => {
        evento.preventDefault();
        aplicarFiltros();
    });

    tablaDiaria.addEventListener('click', (evento) => {
        const boton = evento.target.closest('button[data-fecha]');
        if (!boton) {
            return;
        }
        const fecha = boton.getAttribute('data-fecha');
        const accion = boton.getAttribute('data-accion');

        if (accion === 'ver-rubros') {
            cargarRubros(fecha);
        } else if (accion === 'ver-pagos') {
            cargarPagos(fecha);
        }
    });

    btnHoy.addEventListener('click', () => {
        setHoy();
        aplicarFiltros();
    });

    const btnImprimirDetalle = document.getElementById('btn-imprimir-detalle');
    
    if (btnImprimirDetalle) {
        btnImprimirDetalle.addEventListener('click', () => {
            // 1. Agregamos la clase para ocultar lo demás
            document.body.classList.add('print-mode-detail');
            
            // 2. Pequeño hack para dar tiempo al navegador a repintar el CSS antes de imprimir
            setTimeout(() => {
                window.print();
            }, 50);
        });

        // 3. Cuando termine de imprimir (o cancele), quitamos la clase
        window.addEventListener('afterprint', () => {
            document.body.classList.remove('print-mode-detail');
        });
    }

    setHoy();
    cargarFacturas();
});
