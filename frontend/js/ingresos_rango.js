document.addEventListener('DOMContentLoaded', () => {
    const tablaRubros = document.getElementById('tabla-rubros');
    const tablaRubrosTotal = document.getElementById('tabla-rubros-total');
    const resumenBruto = document.getElementById('resumen-bruto');
    const resumenAnulados = document.getElementById('resumen-anulados');
    const resumenNeto = document.getElementById('resumen-neto');
    const resumenContribuyentes = document.getElementById('resumen-contribuyentes');
    const formFiltros = document.getElementById('form-filtros');
    const inputDesde = document.getElementById('fecha_desde');
    const inputHasta = document.getElementById('fecha_hasta');
    const btnHoy = document.getElementById('btn-hoy');
    const btnVerRubros = document.getElementById('btn-ver-rubros');
    const btnVerPagos = document.getElementById('btn-ver-pagos');
    const contenedorRubros = document.getElementById('contenedor-rubros');
    const contenedorPagos = document.getElementById('contenedor-pagos');
    const tablaPagos = document.getElementById('tabla-pagos');

    let facturasFiltradas = [];

    const numberFormat = new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    function hoyISO() {
        const hoy = new Date();
        const m = String(hoy.getMonth() + 1).padStart(2, '0');
        const d = String(hoy.getDate()).padStart(2, '0');
        return `${hoy.getFullYear()}-${m}-${d}`;
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

    function setResumen(facturas) {
        if (!facturas.length) {
            resumenBruto.textContent = '-';
            resumenAnulados.textContent = '-';
            resumenNeto.textContent = '-';
            resumenContribuyentes.textContent = '-';
            return;
        }

        let bruto = 0;
        let anulados = 0;
        const contribuyentes = new Set();

        facturas.forEach((factura) => {
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

    function renderRubros(detalles) {
        tablaRubros.innerHTML = '';
        tablaRubrosTotal.innerHTML = '';

        if (!detalles.length) {
            tablaRubros.innerHTML = '<tr><td colspan="2" class="text-center py-4">No hay rubros en el rango seleccionado.</td></tr>';
            return;
        }

        let total = 0;
        detalles.forEach((item) => {
            const monto = Number(item.total_monto) || 0;
            total += monto;
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${item.nombre_impuesto ?? 'Rubro'}</td>
                <td class="text-end">${numberFormat.format(monto)}</td>
            `;
            tablaRubros.appendChild(fila);
        });

        tablaRubrosTotal.innerHTML = `
            <tr>
                <td class="text-end fw-semibold text-uppercase">Total</td>
                <td class="fw-semibold text-end">${numberFormat.format(total)}</td>
            </tr>
        `;
    }

    function renderPagos() {
        tablaPagos.innerHTML = '';

        if (!facturasFiltradas.length) {
            tablaPagos.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay pagos en el rango seleccionado.</td></tr>';
            return;
        }

        facturasFiltradas.forEach((factura) => {
            const fila = document.createElement('tr');
            const monto = numberFormat.format(Number(factura.total_factura) || 0);
            const estado = String(factura.ESTADO_FACT ?? factura.estado_pago ?? '').toLowerCase();
            const estadoTexto = estado === 'nulo' || estado === 'n' ? 'Anulado' : 'Activo';

            fila.innerHTML = `
                <td>${factura.num_factura ?? factura.numero_control ?? ''}</td>
                <td>${(factura.fecha ?? '').slice(0, 10)}</td>
                <td>${factura.razon_social ?? factura.cedula_rif ?? ''}</td>
                <td class="text-end">${monto}</td>
                <td>${estadoTexto}</td>
            `;
            tablaPagos.appendChild(fila);
        });
    }

    function mostrarRubros() {
        contenedorRubros.classList.remove('d-none');
        contenedorPagos.classList.add('d-none');
        btnVerRubros.classList.replace('btn-app-outline', 'btn-app-primary');
        btnVerPagos.classList.replace('btn-app-primary', 'btn-app-outline');
    }

    function mostrarPagos() {
        contenedorPagos.classList.remove('d-none');
        contenedorRubros.classList.add('d-none');
        btnVerPagos.classList.replace('btn-app-outline', 'btn-app-primary');
        btnVerRubros.classList.replace('btn-app-primary', 'btn-app-outline');
        renderPagos();
    }

    function fechasValidas(desde, hasta) {
        if (desde && hasta && desde > hasta) {
            tablaRubros.innerHTML = '<tr><td colspan="2" class="text-center text-danger py-4">La fecha "Desde" no puede ser mayor que "Hasta".</td></tr>';
            tablaRubrosTotal.innerHTML = '';
            setResumen([]);
            return false;
        }
        return true;
    }

    async function cargarDatos() {
        const desde = parseFecha(inputDesde.value);
        const hasta = parseFecha(inputHasta.value);

        if (!fechasValidas(desde, hasta)) {
            return;
        }

        try {
            const respuesta = await apiRequest('facturas', 'list');
            const facturas = respuesta?.data ?? [];
            facturasFiltradas = facturas.filter((f) => enRango(f.fecha, desde, hasta));
            setResumen(facturasFiltradas);
        } catch (error) {
            facturasFiltradas = [];
            setResumen([]);
        }

        if (!inputDesde.value || !inputHasta.value) {
            renderRubros([]);
            return;
        }

        try {
            const detallesResp = await apiRequest('detalles', 'listByRange', {
                params: { desde: inputDesde.value, hasta: inputHasta.value }
            });
            renderRubros(detallesResp?.data ?? []);
        } catch (error) {
            renderRubros([]);
        }
    }

    formFiltros.addEventListener('submit', (evento) => {
        evento.preventDefault();
        cargarDatos();
    });

    btnHoy.addEventListener('click', () => {
        const hoy = hoyISO();
        inputDesde.value = hoy;
        inputHasta.value = hoy;
        cargarDatos();
    });

    btnVerRubros.addEventListener('click', mostrarRubros);
    btnVerPagos.addEventListener('click', mostrarPagos);

    inputDesde.value = hoyISO();
    inputHasta.value = hoyISO();
    cargarDatos();
});
