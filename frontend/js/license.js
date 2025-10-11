const LICENSE_CACHE = {
    data: null,
    fetchedAt: 0,
    ttl: 60 * 1000, // 1 minuto
};

async function fetchLicenseStatus(force = false) {
    const now = Date.now();
    if (!force && LICENSE_CACHE.data && now - LICENSE_CACHE.fetchedAt < LICENSE_CACHE.ttl) {
        return LICENSE_CACHE.data;
    }

    const respuesta = await apiRequest('system', 'license_status');
    LICENSE_CACHE.data = respuesta?.data ?? { active: true };
    LICENSE_CACHE.fetchedAt = now;
    return LICENSE_CACHE.data;
}

async function ensureLicenseActive(options = {}) {
    const { silent = false, force = false } = options;
    try {
        const status = await fetchLicenseStatus(force);
        if (status.active) {
            return true;
        }

        if (!silent) {
            const mensajes = [];
            if (status.message) {
                mensajes.push(status.message);
            }
            if (status.support_contact) {
                mensajes.push(`Contacto: ${status.support_contact}`);
            }
            const texto = mensajes.join(' ');
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Licencia expirada',
                    text: texto || 'La licencia ha expirado. Comuníquese con el desarrollador.',
                    confirmButtonText: 'Cerrar',
                });
            } else {
                alert(texto || 'La licencia ha expirado. Comuníquese con el desarrollador.');
            }
        }
        return false;
    } catch (error) {
        if (!silent) {
            const mensaje = error?.message || 'No se pudo verificar la licencia.';
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje,
                    confirmButtonText: 'Cerrar',
                });
            } else {
                alert(mensaje);
            }
        }
        return false;
    }
}
