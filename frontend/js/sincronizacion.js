document.addEventListener('DOMContentLoaded', () => {
    const btnExportar = document.getElementById('btn-exportar');
    const btnImportar = document.getElementById('btn-importar');
    const btnRecargar = document.getElementById('btn-recargar-status');
    const inputArchivo = document.getElementById('archivo-paquete');
    const checkReplace = document.getElementById('replace-all');
    const mensaje = document.getElementById('sync-message');
    const statusTotal = document.getElementById('status-total');
    const statusAccion = document.getElementById('status-accion');
    const statusFecha = document.getElementById('status-fecha');
    const statusUsuario = document.getElementById('status-usuario');
    const statusDetalle = document.getElementById('status-detalle');
    const tablaResumen = document.getElementById('tabla-sync-resumen');
    const main = document.querySelector('main[data-can-status]');
    const canStatus = main?.dataset?.canStatus === '1';

    const tableLabels = {
        rol: 'Roles',
        usuario: 'Usuarios',
        contribuyente: 'Contribuyentes',
        tipo_impuesto: 'Tipos de impuesto',
        factura: 'Facturas',
        factura_detalle: 'Detalle de facturas',
        log_actividad: 'Log de actividad',
    };

    function mostrarMensaje(tipo, texto) {
        if (!mensaje) return;
        mensaje.className = `alert alert-${tipo} mb-4`;
        mensaje.textContent = texto;
        mensaje.classList.remove('d-none');
    }

    function limpiarMensaje() {
        if (!mensaje) return;
        mensaje.classList.add('d-none');
        mensaje.textContent = '';
    }

    function toggleBoton(boton, loading, texto) {
        if (!boton) return;
        if (loading) {
            boton.dataset.originalText = boton.innerHTML;
            boton.innerHTML = texto;
            boton.disabled = true;
            return;
        }

        if (boton.dataset.originalText) {
            boton.innerHTML = boton.dataset.originalText;
        }
        boton.disabled = false;
    }

    function renderResumen(counts) {
        if (!tablaResumen) return;
        tablaResumen.innerHTML = '';

        const entries = Object.entries(counts || {});
        if (!entries.length) {
            tablaResumen.innerHTML = '<tr><td colspan="2" class="text-center py-4">Sin datos disponibles.</td></tr>';
            return;
        }

        entries.forEach(([tabla, total]) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${tableLabels[tabla] || tabla}</td>
                <td class="text-end">${total}</td>
            `;
            tablaResumen.appendChild(fila);
        });
    }

    function aplicarEstado(data) {
        const counts = data?.counts || {};
        const total = data?.total ?? 0;
        const last = data?.last_sync;

        if (statusTotal) {
            statusTotal.textContent = total;
        }

        if (last) {
            if (statusAccion) statusAccion.textContent = last.accion || '-';
            if (statusFecha) statusFecha.textContent = last.fecha || '-';
            if (statusUsuario) statusUsuario.textContent = last.usuario || '-';
            if (statusDetalle) statusDetalle.textContent = last.detalle || '-';
        } else {
            if (statusAccion) statusAccion.textContent = 'Sin registros';
            if (statusFecha) statusFecha.textContent = '-';
            if (statusUsuario) statusUsuario.textContent = '-';
            if (statusDetalle) statusDetalle.textContent = '-';
        }

        renderResumen(counts);
    }

    async function cargarEstado() {
        try {
            const respuesta = await apiRequest('sync', 'status');
            aplicarEstado(respuesta?.data || {});
        } catch (error) {
            if (tablaResumen) {
                tablaResumen.innerHTML = '<tr><td colspan="2" class="text-center py-4">No fue posible cargar el resumen.</td></tr>';
            }
        }
    }

    function resolveFilename(response) {
        const disposition = response.headers.get('Content-Disposition') || '';
        const match = disposition.match(/filename=\"?([^\";]+)\"?/i);
        if (match && match[1]) {
            return match[1];
        }

        const stamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '');
        return `sync_${stamp}.json`;
    }

    async function descargarPaquete() {
        limpiarMensaje();
        toggleBoton(btnExportar, true, '<i class="bi bi-hourglass-split me-2"></i>Generando...');
        if (btnImportar) {
            btnImportar.disabled = true;
        }

        try {
            const response = await fetch(`${API_BASE_URL}?entity=sync&action=export`, {
                method: 'GET',
                credentials: 'same-origin',
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => null);
                throw new Error(payload?.message || 'No fue posible generar el paquete.');
            }

            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = resolveFilename(response);
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);

            mostrarMensaje('success', 'Paquete generado correctamente.');
        } catch (error) {
            mostrarMensaje('danger', error.message || 'No fue posible generar el paquete.');
        } finally {
            toggleBoton(btnExportar, false);
            if (btnImportar) {
                btnImportar.disabled = false;
            }
        }
    }

    async function importarPaquete() {
        limpiarMensaje();

        const file = inputArchivo?.files?.[0];
        if (!file) {
            mostrarMensaje('warning', 'Selecciona un archivo JSON para continuar.');
            return;
        }

        toggleBoton(btnImportar, true, '<i class="bi bi-hourglass-split me-2"></i>Importando...');
        if (btnExportar) {
            btnExportar.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('package', file);
            formData.append('replace', checkReplace?.checked ? '1' : '0');

            const response = await fetch(`${API_BASE_URL}?entity=sync&action=import`, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
            });

            const payload = await response.json().catch(() => null);
            if (!response.ok) {
                throw new Error(payload?.message || 'No fue posible importar el paquete.');
            }

            mostrarMensaje('success', payload?.message || 'Paquete importado correctamente.');
            if (inputArchivo) {
                inputArchivo.value = '';
            }
            if (checkReplace) {
                checkReplace.checked = false;
            }
            await cargarEstado();
        } catch (error) {
            mostrarMensaje('danger', error.message || 'No fue posible importar el paquete.');
        } finally {
            toggleBoton(btnImportar, false);
            if (btnExportar) {
                btnExportar.disabled = false;
            }
        }
    }

    if (btnExportar) {
        btnExportar.addEventListener('click', descargarPaquete);
    }

    if (btnImportar) {
        btnImportar.addEventListener('click', importarPaquete);
    }

    if (btnRecargar) {
        btnRecargar.addEventListener('click', () => {
            cargarEstado();
        });
    }

    if (canStatus) {
        cargarEstado();
    }
});
