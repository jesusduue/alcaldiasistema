document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('datos');
    const cajaBusqueda = document.getElementById('caja_busqueda');

    async function cargarContribuyentes(termino = '') {
        contenedor.innerHTML = '<p class="text-center">Cargando...</p>';
        try {
            const respuesta = await apiRequest('contribuyentes', 'list', {
                params: { term: termino },
            });
            const items = respuesta?.data ?? [];
            if (!items.length) {
                contenedor.innerHTML = "<p class='msj'>No hay datos</p>";
                return;
            }
            const tabla = document.createElement('table');
            tabla.className = 'table table-hover align-middle mb-0';
            tabla.innerHTML = `
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Cédula/RIF</th>
                        <th scope="col">Razón social</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-center" colspan="2">Procesos</th>
                    </tr>
                </thead>
            `;
            const tbody = document.createElement('tbody');

            items.forEach((item) => {
                const fila = document.createElement('tr');
                fila.className = 'sal';
                fila.innerHTML = `
                    <td>${item.id_contribuyente}</td>
                    <td>${item.cedula_rif}</td>
                    <td>${item.razon_social}</td>
                    <td>${item.estado_cont ?? ''}</td>
                    <td class="text-center">
                        <a href="guardar_factura.php?id_contribuyente=${item.id_contribuyente}" class="btn btn-sm btn-app-primary accion-licencia" data-action="generar" data-url="guardar_factura.php?id_contribuyente=${item.id_contribuyente}">
                            <i class="bi bi-receipt-cutoff me-1"></i>Generar recibo
                        </a>
                    </td>
                    <td class="text-center">
                        <a href="ver_pagos.php?id_contribuyente=${item.id_contribuyente}" class="btn btn-sm btn-app-outline">
                            <i class="bi bi-journal-text me-1"></i>Ver pagos
                        </a>
                    </td>
                `;
                tbody.appendChild(fila);
            });

            tabla.appendChild(tbody);
            contenedor.innerHTML = '';
            contenedor.appendChild(tabla);
        } catch (error) {
            contenedor.innerHTML = `<p class='msj'>${error.message || 'Error al cargar los datos.'}</p>`;
        }
    }

    cajaBusqueda.addEventListener('input', (evento) => {
        const valor = evento.target.value.trim();
        cargarContribuyentes(valor);
    });

    contenedor.addEventListener('click', async (evento) => {
        const enlace = evento.target.closest('.accion-licencia');
        if (!enlace) {
            return;
        }

        evento.preventDefault();
        const activo = await ensureLicenseActive();
        if (!activo) {
            enlace.classList.add('disabled');
            return;
        }
        const url = enlace.dataset.url || enlace.getAttribute('href');
        if (url) {
            window.location.href = url;
        }
    });

    cargarContribuyentes();
});
