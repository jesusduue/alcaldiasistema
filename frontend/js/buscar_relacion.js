document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('datos');
    const cajaBusqueda = document.getElementById('caja_busqueda');

    async function cargarFacturas(termino = '') {
        contenedor.innerHTML = '<p class="text-center py-4 text-muted">Cargando...</p>';
        try {
            const respuesta = await apiRequest('facturas', 'list', {
                params: { term: termino },
            });
            const items = respuesta?.data ?? [];
            if (!items.length) {
                contenedor.innerHTML = "<p class='text-center py-4 text-muted'>No hay datos</p>";
                return;
            }

            let totalSum = 0;
            let anuladosSum = 0;

            const tabla = document.createElement('table');
            tabla.className = 'table table-hover align-middle mb-0';
            tabla.innerHTML = `
                <thead>
                    <tr>
                        <th scope="col">N° FACTURA</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">CEDULA/RIF</th>
                        <th scope="col">RAZÓN SOCIAL</th>
                        <th scope="col">CONCEPTO</th>
                        <th scope="col">MONTO CANCELADO</th>
                        <th scope="col">ESTADO</th>
                    </tr>
                </thead>
            `;
            const tbody = document.createElement('tbody');

            items.forEach((factura) => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${factura.num_factura}</td>
                    <td>${factura.fecha}</td>
                    <td>${factura.cedula_rif}</td>
                    <td>${factura.razon_social}</td>
                    <td>${factura.concepto ?? ''}</td>
                    <td>${factura.total_factura ?? ''}</td>
                    <td>${factura.ESTADO_FACT ?? ''}</td>
                `;
                tbody.appendChild(fila);

                const monto = parseFloat(factura.total_factura ?? 0);
                if (!Number.isNaN(monto)) {
                    totalSum += monto;
                    if ((factura.ESTADO_FACT ?? '').toLowerCase() === 'nulo') {
                        anuladosSum += monto;
                    }
                }
            });

            const totalFinal = totalSum - anuladosSum;

            const resumen = document.createElement('tr');
            resumen.innerHTML = `
                <td colspan="5" class="text-end fw-semibold text-uppercase">Sub-total:</td>
                <td>${totalSum.toFixed(2)}</td>
                <td></td>
            `;

            const anulados = document.createElement('tr');
            anulados.innerHTML = `
                <td colspan="5" class="text-end fw-semibold text-uppercase">Total recibos anulados:</td>
                <td>${anuladosSum.toFixed(2)}</td>
                <td></td>
            `;

            const total = document.createElement('tr');
            total.innerHTML = `
                <td colspan="5" class="text-end fw-semibold text-uppercase">Total:</td>
                <td>${totalFinal.toFixed(2)}</td>
                <td></td>
            `;

            tbody.appendChild(resumen);
            tbody.appendChild(anulados);
            tbody.appendChild(total);

            tabla.appendChild(tbody);
            contenedor.innerHTML = '';
            contenedor.appendChild(tabla);
        } catch (error) {
            contenedor.innerHTML = `<p class='text-center text-danger py-4'>${error.message || 'Error al cargar las facturas.'}</p>`;
        }
    }

    cajaBusqueda.addEventListener('input', (evento) => {
        const valor = evento.target.value.trim();
        cargarFacturas(valor);
    });

    cargarFacturas();
});
