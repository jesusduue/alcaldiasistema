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
            tabla.className = 'container table table-bordered table-hover mt-3';
            tabla.innerHTML = `
                <thead class="head table-light">
                    <tr>
                        <td>CODIGO</td>
                        <td>CEDULA/RIF</td>
                        <td>RAZON SOCIAL</td>
                        <td>ESTADO</td>
                        <td colspan="2" class="text-center">PROCESOS</td>
                        
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
                    <td><a href="guardar_factura.php?id_contribuyente=${item.id_contribuyente}" class="btn btn-primary accion-licencia" data-action="generar" data-url="guardar_factura.php?id_contribuyente=${item.id_contribuyente}">GENERAR RECIBO</a></td>
                    <td><a href="ver_pagos.php?id_contribuyente=${item.id_contribuyente}" class="btn btn-info">VER PAGOS</a></td>
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
