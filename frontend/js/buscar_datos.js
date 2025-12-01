document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('datos');
    const cajaBusqueda = document.getElementById('caja_busqueda');
    let paginationStylesLoaded = false;

    function ensurePaginationStyles() {
        if (paginationStylesLoaded) return;
        const style = document.createElement('style');
        style.textContent = `
            /* Paleta vinotinto para paginacion DataTables / Bootstrap */
            .dataTables_wrapper .dataTables_paginate .paginate_button,
            .pagination .page-link {
                color: #7f1d1d !important;
                border-radius: 6px !important;
                padding: 6px 10px !important;
                margin: 0 2px !important;
                border: 1px solid #e5e7eb !important;
                background: #fff !important;
                transition: all 0.15s ease;
                box-shadow: none !important;
                font-weight: 600;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
            .pagination .page-link:hover {
                color: #7f1d1d !important;
                background: #f5f0ef !important;
                border-color: #e7d6d3 !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current,
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
            .pagination .page-item.active .page-link {
                color: #fff !important;
                background: #7f1d1d !important;
                border-color: #7f1d1d !important;
                box-shadow: none !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:active,
            .pagination .page-link:active {
                color: #fff !important;
                background: #6b1616 !important;
                border-color: #6b1616 !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
            .pagination .page-item.disabled .page-link {
                color: #9ca3af !important;
                background: #f9fafb !important;
                border-color: #e5e7eb !important;
                cursor: not-allowed;
                box-shadow: none !important;
            }
        `;
        document.head.appendChild(style);
        paginationStylesLoaded = true;
    }

    function normalizarEstado(estado) {
        if (!estado) {
            return '-';
        }
        const valor = String(estado).toUpperCase();
        if (valor === 'A' || valor === 'ACTIVO') {
            return 'Activo';
        }
        if (valor === 'I' || valor === 'INACTIVO') {
            return 'Inactivo';
        }
        return valor;
    }

    async function cargarContribuyentes(termino = '') {
        contenedor.innerHTML = `<div class="loading-wrapper">
                <div class="spinner-border spinner-vinotinto" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="loading-text">Cargando contribuyentes...</div>
            </div>`;

        try {
            const respuesta = await apiRequest('contribuyentes', 'list', {
                params: { term: termino },
            });
            const items = respuesta?.data ?? [];

            if (!items.length) {
                contenedor.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-search display-6 mb-3 d-block opacity-25"></i>
                        No se encontraron contribuyentes registrados.
                    </div>`;
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
                        <th scope="col">Teléfono</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-center">Recibo</th>
                        <th scope="col" class="text-center">Pagos</th>
                    </tr>
                </thead>
            `;

            const tbody = document.createElement('tbody');

            items.forEach((item) => {
                const fila = document.createElement('tr');
                const telefono = item.telefono && item.telefono !== '' ? item.telefono : '-';
                const correo = item.email && item.email !== '' ? item.email : '-';
                const estado = normalizarEstado(item.estado_cont);
                fila.innerHTML = `
                    <td>${item.id_contribuyente}</td>
                    <td>${item.cedula_rif}</td>
                    <td>${item.razon_social}</td>
                    <td>${telefono}</td>
                    <td>${correo}</td>
                    <td>${estado}</td>
                    <td class="text-center">
                        <a href="guardar_factura.php?id_contribuyente=${item.id_contribuyente}" class="btn btn-sm btn-app-primary accion-licencia" data-url="guardar_factura.php?id_contribuyente=${item.id_contribuyente}">
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

            // Inicializar DataTables
            ensurePaginationStyles();
            $(tabla).DataTable({
                paging: true,
                pageLength: 10,
                lengthChange: false,
                searching: false, // Desactivar búsqueda interna ya que usamos la del servidor
                ordering: true,
                info: true,
                autoWidth: false,
                language: {
                    paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ contribuyentes',
                    infoEmpty: 'Mostrando 0 a 0 de 0 contribuyentes',
                    infoFiltered: '(filtrado de _MAX_ contribuyentes totales)',
                    zeroRecords: 'No se encontraron contribuyentes',
                    search: ''
                },
                dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
            });
        } catch (error) {
            contenedor.innerHTML = `<p class="text-center text-danger py-4">${error.message || 'Error al cargar los datos.'}</p>`;
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
