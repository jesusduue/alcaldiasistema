document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('datos');
    const cajaBusqueda = document.getElementById('caja_busqueda');

    async function cargarFacturas(termino = '') {
        contenedor.innerHTML = '<p class="text-center">Cargando...</p>';
        try {
            const respuesta = await apiRequest('facturas', 'list', {
                params: { term: termino },
            });
            const items = respuesta?.data ?? [];
            if (!items.length) {
                contenedor.innerHTML = "<p class='msj'>No hay datos</p>";
                return;
            }

            let totalSum = 0;
            let anuladosSum = 0;

            const tabla = document.createElement('table');
            tabla.className = 'container table table-bordered table-hover mt-3';
            tabla.innerHTML = `
                <thead class="head table-light">
                    <tr class="head">
                        <td>N° FACTURA</td>
                        <td>FECHA</td>
                        <td>CEDULA/RIF</td>
                        <td>RAZON SOCIAL</td>
                        <td>CONCEPTO</td>
                        <td>MONTO CANCELADO</td>
                        <td>ESTADO</td>
                    </tr>
                </thead>
            `;
            const tbody = document.createElement('tbody');

            items.forEach((factura) => {
                const fila = document.createElement('tr');
                fila.className = 'sal';
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
                <td colspan="5" class="footer">SUB-TOTAL:</td>
                <td>${totalSum.toFixed(2)}</td>
                <td></td>
            `;

            const anulados = document.createElement('tr');
            anulados.innerHTML = `
                <td colspan="5" class="footer">TOTAL RECIBOS ANULADOS:</td>
                <td>${anuladosSum.toFixed(2)}</td>
                <td></td>
            `;

            const total = document.createElement('tr');
            total.innerHTML = `
                <td colspan="5" class="footer">TOTAL:</td>
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
            contenedor.innerHTML = `<p class='msj'>${error.message || 'Error al cargar las facturas.'}</p>`;
        }
    }

    cajaBusqueda.addEventListener('input', (evento) => {
        const valor = evento.target.value.trim();
        cargarFacturas(valor);
    });

    cargarFacturas();
});
