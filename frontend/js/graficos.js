document.addEventListener('DOMContentLoaded', () => {
    const inputDesde = document.getElementById('fecha_desde');
    const inputHasta = document.getElementById('fecha_hasta');
    const btnAplicar = document.getElementById('btn-aplicar');
    const btnHoy = document.getElementById('btn-hoy');
    const selectFechaRubro = document.getElementById('select-fecha-rubro');

    const resumenBruto = document.getElementById('resumen-bruto');
    const resumenAnulados = document.getElementById('resumen-anulados');
    const resumenNeto = document.getElementById('resumen-neto');
    const resumenContribuyentes = document.getElementById('resumen-contribuyentes');

    let facturas = [];
    let chartLine;
    let chartBar;

    let chartRubros;
    let chartMensual;
    let chartDistribucion;
    let chartTopContrib;

    const btnMensual = document.getElementById('btn-mensual');
    const btnTrimestral = document.getElementById('btn-trimestral');
    const btnExportarDist = document.getElementById('btn-exportar-dist');
    const tituloMensual = document.getElementById('titulo-mensual');

    const numberFormat = new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const chartPalette = ['#7f1d1d', '#9b1c1c', '#b91c1c', '#d9480f', '#eab308', '#0ea5e9', '#2563eb', '#10b981'];
    const formatCurrency = (valor) => numberFormat.format(Number(valor) || 0);

    function hoyISO() {
        const hoy = new Date();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        const hoyStr = `${hoy.getFullYear()}-${mes}-${dia}`;
        return hoyStr;
    }

    function parseFecha(valor) {
        if (!valor) return null;
        const [y, m, d] = valor.split('-').map((v) => parseInt(v, 10));
        if (!y || !m || !d) return null;
        return new Date(Date.UTC(y, m - 1, d));
    }

    function enRango(fechaTexto, desde, hasta) {
        const fecha = parseFecha(fechaTexto?.slice(0, 10));
        if (!fecha) return false;
        if (desde && fecha < desde) return false;
        if (hasta && fecha > hasta) return false;
        return true;
    }

    function esAnulada(factura) {
        const estadoPago = String(factura?.estado_pago ?? '').toUpperCase();
        const estadoTexto = String(factura?.ESTADO_FACT ?? '').toUpperCase();
        return estadoPago === 'N' || estadoTexto === 'NULO';
    }

    function agruparTopCategorias(labels, data, limite = 6, etiquetaOtros = 'Otros') {
        const pares = labels.map((label, index) => ({
            label: label || 'Sin nombre',
            valor: Number(data[index]) || 0,
        })).filter((item) => item.valor > 0);

        pares.sort((a, b) => b.valor - a.valor);

        const top = pares.slice(0, limite);
        const resto = pares.slice(limite);
        const totalOtros = resto.reduce((acc, item) => acc + item.valor, 0);

        if (totalOtros > 0) {
            top.push({ label: etiquetaOtros, valor: totalOtros });
        }

        if (!top.length) {
            return { labels: ['Sin datos'], data: [0] };
        }

        return {
            labels: top.map((item) => item.label),
            data: top.map((item) => item.valor),
        };
    }

    function agruparPorFecha(filtradas) {
        const mapa = new Map();
        filtradas.forEach((factura) => {
            const fecha = (factura.fecha || '').slice(0, 10);
            if (!fecha) return;
            if (!mapa.has(fecha)) {
                mapa.set(fecha, { fecha, bruto: 0, anulados: 0, neto: 0 });
            }
            const grupo = mapa.get(fecha);
            const monto = Number(factura.total_factura) || 0;
            grupo.bruto += monto;
            if (esAnulada(factura)) {
                grupo.anulados += monto;
            }
        });
        const lista = Array.from(mapa.values());
        lista.forEach((g) => {
            g.neto = g.bruto - g.anulados;
        });
        return lista.sort((a, b) => a.fecha.localeCompare(b.fecha));
    }

    function setResumen(filtradas) {
        if (!filtradas.length) {
            resumenBruto.textContent = '-';
            resumenAnulados.textContent = '-';
            resumenNeto.textContent = '-';
            resumenContribuyentes.textContent = '-';
            return;
        }

        let bruto = 0;
        let anulados = 0;
        const contribuyentes = new Set();

        filtradas.forEach((factura) => {
            const monto = Number(factura.total_factura) || 0;
            bruto += monto;
            if (esAnulada(factura)) {
                anulados += monto;
            }
            if (factura.cod_contribuyente) {
                contribuyentes.add(factura.cod_contribuyente);
            }
        });

        resumenBruto.textContent = numberFormat.format(bruto);
        resumenAnulados.textContent = numberFormat.format(anulados);
        resumenNeto.textContent = numberFormat.format(bruto - anulados);
        resumenContribuyentes.textContent = contribuyentes.size.toString();
    }

    function renderLine(agrupados) {
        const ctx = document.getElementById('chart-line');
        const labels = agrupados.map((g) => g.fecha);
        const data = agrupados.map((g) => g.neto);
        const safeLabels = labels.length ? labels : ['Sin datos'];
        const safeData = labels.length ? data : [0];

        if (chartLine) {
            chartLine.data.labels = safeLabels;
            chartLine.data.datasets[0].data = safeData;
            chartLine.update();
            return;
        }

        chartLine = new Chart(ctx, {
            type: 'line',
            data: {
                labels: safeLabels,
                datasets: [
                    {
                        label: 'Neto',
                        data: safeData,
                        borderColor: '#7f1d1d',
                        backgroundColor: 'rgba(127,29,29,0.1)',
                        tension: 0.25,
                        fill: true,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `Bs. ${formatCurrency(ctx.raw)}`,
                        },
                    },
                },
            },
        });
    }

    function renderBar(filtradas) {
        const ctx = document.getElementById('chart-bar');
        const activos = filtradas.filter((f) => !esAnulada(f)).length;
        const anuladas = filtradas.length - activos;

        if (chartBar) {
            chartBar.data.datasets[0].data = [activos, anuladas];
            chartBar.update();
            return;
        }

        chartBar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Activas', 'Anuladas'],
                datasets: [
                    {
                        data: [activos, anuladas],
                        backgroundColor: ['#7f1d1d', '#d14343'],
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });
    }

    function renderRubros(labels, data) {
        const ctx = document.getElementById('chart-rubros');
        const top = agruparTopCategorias(labels, data, 6, 'Otros rubros');
        const colors = top.data.map((_, i) => chartPalette[i % chartPalette.length]);

        if (chartRubros) {
            chartRubros.data.labels = top.labels;
            chartRubros.data.datasets[0].data = top.data;
            chartRubros.data.datasets[0].backgroundColor = colors;
            chartRubros.update();
            return;
        }

        chartRubros = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: top.labels,
                datasets: [
                    {
                        data: top.data,
                        backgroundColor: colors,
                        borderWidth: 1,
                        hoverOffset: 6,
                        cutout: '55%',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: Bs. ${formatCurrency(ctx.raw)}`,
                        },
                    },
                },
            },
        });
    }

    async function cargarRubros(fecha) {
        if (!fecha) {
            renderRubros([], []);
            return;
        }
        try {
            const respuesta = await apiRequest('detalles', 'by_fecha', { params: { fecha_det_recibo: fecha } });
            const items = respuesta?.data ?? [];
            const labels = items.map((i) => i.nombre_impuesto ?? 'Rubro');
            const data = items.map((i) => Number(i.total_monto) || 0);
            renderRubros(labels, data);
        } catch (error) {
            renderRubros([], []);
        }
    }

    function renderTopContribuyentes(filtradas) {
        const ctx = document.getElementById('chart-top-contribuyentes');
        const activos = filtradas.filter((f) => !esAnulada(f));
        const mapa = new Map();

        activos.forEach((factura) => {
            const nombre = (factura.razon_social || factura.cedula_rif || `Contribuyente ${factura.cod_contribuyente || ''}`).trim() || 'Sin nombre';
            const monto = Number(factura.total_factura) || 0;
            mapa.set(nombre, (mapa.get(nombre) || 0) + monto);
        });

        const pares = Array.from(mapa.entries()).map(([label, valor]) => ({ label, valor }));
        pares.sort((a, b) => b.valor - a.valor);

        const limite = 7;
        const top = pares.slice(0, limite);
        const resto = pares.slice(limite);
        const otros = resto.reduce((acc, item) => acc + item.valor, 0);
        if (otros > 0) {
            top.push({ label: 'Otros', valor: otros });
        }

        const labelsFull = top.length ? top.map((i) => i.label) : ['Sin datos'];
        const labels = labelsFull.map((name) => (name.length > 32 ? `${name.slice(0, 32)}...` : name));
        const data = top.length ? top.map((i) => i.valor) : [0];
        const colors = labels.map((_, i) => chartPalette[i % chartPalette.length]);

        if (chartTopContrib) {
            chartTopContrib.data.labels = labels;
            chartTopContrib.data.datasets[0].data = data;
            chartTopContrib.data.datasets[0].backgroundColor = colors;
            chartTopContrib.update();
            return;
        }

        chartTopContrib = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        data,
                        backgroundColor: colors,
                        borderRadius: 8,
                        maxBarThickness: 42,
                        originalLabels: labelsFull,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const originals = ctx.chart.data.datasets[0].originalLabels || [];
                                const name = originals[ctx.dataIndex] || ctx.label;
                                return `${name}: Bs. ${formatCurrency(ctx.raw)}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Bs. ' + formatCurrency(value),
                        },
                        grid: { display: false },
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                        },
                    },
                },
            },
        });
    }

    function actualizarGraficos() {
        const desde = parseFecha(inputDesde.value);
        const hasta = parseFecha(inputHasta.value);
        if (desde && hasta && desde > hasta) {
            setResumen([]);
            renderLine([]);
            renderBar([]);
            renderTopContribuyentes([]);
            selectFechaRubro.innerHTML = '<option value=\"\">Rango invalido</option>';
            renderRubros([], []);
            renderDistribucion([], []);
            renderMensualChart([], getPeriodo());
            return;
        }

        const filtradas = facturas.filter((f) => enRango(f.fecha, desde, hasta));
        setResumen(filtradas);

        const agrupados = agruparPorFecha(filtradas);
        renderLine(agrupados);
        renderBar(filtradas);
        renderTopContribuyentes(filtradas);

        const fechas = agrupados.map((g) => g.fecha);
        selectFechaRubro.innerHTML = '';
        if (!fechas.length) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Sin fechas';
            selectFechaRubro.appendChild(opt);
            cargarRubros('');
            actualizarMensual();
            renderDistribucion([], []);
            return;
        }
        fechas.forEach((f) => {
            const opt = document.createElement('option');
            opt.value = f;
            opt.textContent = f;
            selectFechaRubro.appendChild(opt);
        });
        const ultima = fechas[fechas.length - 1];
        selectFechaRubro.value = ultima;
        cargarRubros(ultima);


        actualizarMensual();
        cargarDistribucion(desde ? inputDesde.value : null, hasta ? inputHasta.value : null);
    }

    function getPeriodo() {
        return btnTrimestral.checked ? 'trimestre' : 'mes';
    }

    function agruparPorPeriodo(filtradas, periodo) {
        const mapa = new Map();
        let fechaMin = null;
        let fechaMax = null;

        filtradas.forEach((factura) => {
            if (esAnulada(factura)) return;
            const fecha = parseFecha(factura.fecha?.slice(0, 10));
            if (!fecha) return;

            if (!fechaMin || fecha < fechaMin) fechaMin = fecha;
            if (!fechaMax || fecha > fechaMax) fechaMax = fecha;

            let key;
            if (periodo === 'trimestre') {
                const q = Math.floor(fecha.getUTCMonth() / 3) + 1;
                key = `${fecha.getUTCFullYear()}-T${q}`;
            } else {
                const m = String(fecha.getUTCMonth() + 1).padStart(2, '0');
                key = `${fecha.getUTCFullYear()}-${m}`;
            }

            if (!mapa.has(key)) mapa.set(key, 0);
            mapa.set(key, mapa.get(key) + (Number(factura.total_factura) || 0));
        });

        if (!mapa.size || !fechaMin || !fechaMax) {
            return [];
        }

        const cursor = new Date(Date.UTC(fechaMin.getUTCFullYear(), fechaMin.getUTCMonth(), 1));
        const limite = new Date(Date.UTC(fechaMax.getUTCFullYear(), fechaMax.getUTCMonth(), 1));

        while (cursor <= limite) {
            let key;
            if (periodo === 'trimestre') {
                const q = Math.floor(cursor.getUTCMonth() / 3) + 1;
                key = `${cursor.getUTCFullYear()}-T${q}`;
                cursor.setUTCMonth(cursor.getUTCMonth() + 3);
            } else {
                const m = String(cursor.getUTCMonth() + 1).padStart(2, '0');
                key = `${cursor.getUTCFullYear()}-${m}`;
                cursor.setUTCMonth(cursor.getUTCMonth() + 1);
            }

            if (!mapa.has(key)) mapa.set(key, 0);
        }

        return Array.from(mapa.entries())
            .sort((a, b) => a[0].localeCompare(b[0]))
            .map(([label, valor]) => ({ label, valor }));
    }

    function renderMensualChart(data, periodo) {
        const ctx = document.getElementById('chart-mensual');
        const labels = data.map(d => d.label);
        const values = data.map(d => d.valor);
        const labelStr = periodo === 'trimestre' ? 'Ingresos Trimestrales' : 'Ingresos Mensuales';
        const safeLabels = labels.length ? labels : ['Sin datos'];
        const safeValues = values.length ? values : [0];

        if (chartMensual) {
            chartMensual.data.labels = safeLabels;
            chartMensual.data.datasets[0].data = safeValues;
            chartMensual.data.datasets[0].label = labelStr;
            chartMensual.update();
            return;
        }

        chartMensual = new Chart(ctx, {
            type: 'line',
            data: {
                labels: safeLabels,
                datasets: [{
                    label: labelStr,
                    data: safeValues,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `Bs. ${formatCurrency(ctx.raw)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Bs. ' + formatCurrency(value)
                        }
                    }
                }
            }
        });
    }

    function actualizarMensual() {
        const periodo = getPeriodo();
        tituloMensual.textContent = periodo === 'trimestre' ? 'Ingresos Trimestrales' : 'Ingresos Mensuales';

        // Usamos las facturas del rango seleccionado para mantener coherencia con el resto de graficos.
        const desde = parseFecha(inputDesde.value);
        const hasta = parseFecha(inputHasta.value);
        const filtradas = facturas.filter((f) => enRango(f.fecha, desde, hasta));

        const datos = agruparPorPeriodo(filtradas, periodo);
        renderMensualChart(datos, periodo);
    }

    async function cargarDistribucion(desde, hasta) {
        if (!desde || !hasta) {
            renderDistribucion([], []);
            return;
        }
        try {
            const respuesta = await apiRequest('detalles', 'by_range', { params: { desde, hasta } });
            const items = respuesta?.data ?? [];
            const labels = items.map((i) => i.nombre_impuesto ?? 'Rubro');
            const data = items.map((i) => Number(i.total_monto) || 0);
            renderDistribucion(labels, data);
        } catch (error) {
            console.error(error);
            renderDistribucion([], []);
        }
    }

    function renderDistribucion(labels, data) {
        const ctx = document.getElementById('chart-distribucion');
        const top = agruparTopCategorias(labels, data, 7, 'Otros impuestos');
        const colors = top.data.map((_, i) => chartPalette[i % chartPalette.length]);

        if (chartDistribucion) {
            chartDistribucion.data.labels = top.labels;
            chartDistribucion.data.datasets[0].data = top.data;
            chartDistribucion.data.datasets[0].backgroundColor = colors;
            chartDistribucion.update();
            return;
        }

        chartDistribucion = new Chart(ctx, {
            type: 'doughnut', // O 'bar'
            data: {
                labels: top.labels,
                datasets: [{
                    data: top.data,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: Bs. ${formatCurrency(ctx.raw)}`,
                        },
                    },
                }
            }
        });
    }

    btnMensual.addEventListener('change', actualizarMensual);
    btnTrimestral.addEventListener('change', actualizarMensual);

    btnExportarDist.addEventListener('click', () => {
        const link = document.createElement('a');
        link.download = 'distribucion_impuestos.png';
        link.href = document.getElementById('chart-distribucion').toDataURL();
        link.click();
    });

    async function cargarFacturas() {
        try {
            const respuesta = await apiRequest('facturas', 'list');
            facturas = respuesta?.data ?? [];
            actualizarGraficos();
        } catch (error) {
            facturas = [];
            actualizarGraficos();
        }
    }

    btnAplicar.addEventListener('click', () => actualizarGraficos());
    btnHoy.addEventListener('click', () => {
        const hoy = hoyISO();
        inputDesde.value = hoy;
        inputHasta.value = hoy;
        actualizarGraficos();
    });
    selectFechaRubro.addEventListener('change', (e) => {
        cargarRubros(e.target.value);
    });

    inputDesde.value = hoyISO();
    inputHasta.value = hoyISO();
    cargarFacturas();
});
