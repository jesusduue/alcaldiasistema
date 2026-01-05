document.addEventListener('DOMContentLoaded', () => {
    const tablaUsuarios = document.getElementById('tabla-usuarios');
    const tablaLogs = document.getElementById('tabla-logs');
    const tablaRoles = document.getElementById('tabla-roles');
    const btnNuevoUsuario = document.getElementById('btn-nuevo-usuario');
    const btnRoles = document.getElementById('btn-roles');
    const btnRecargarLogs = document.getElementById('btn-recargar-logs');
    const formUsuario = document.getElementById('form-usuario');
    const formRol = document.getElementById('form-rol');
    const modalUsuarioEl = document.getElementById('modal-usuario');
    const modalRolesEl = document.getElementById('modal-roles');
    const tituloUsuario = document.getElementById('titulo-usuario');
    const inputUsuarioId = document.getElementById('usuario-id');
    const inputUsuarioNombre = document.getElementById('usuario-nombre');
    const inputUsuarioRol = document.getElementById('usuario-rol');
    const inputUsuarioClave = document.getElementById('usuario-clave');
    const inputUsuarioEstado = document.getElementById('usuario-estado');

    const modalUsuario = new bootstrap.Modal(modalUsuarioEl);
    const modalRoles = new bootstrap.Modal(modalRolesEl);

    let usuarios = [];
    let roles = [];

    function estadoTexto(estado) {
        return estado === 'A' ? 'Activo' : 'Inactivo';
    }

    function estadoClase(estado) {
        return estado === 'A' ? 'active' : 'inactive';
    }

    function badgeAccion(accion) {
        const normal = String(accion || '').toUpperCase();
        if (normal.includes('LOGIN')) return 'bg-primary';
        if (normal.includes('LOGOUT')) return 'bg-secondary';
        if (normal.includes('CREAR') || normal.includes('REGISTRAR')) return 'bg-success';
        if (normal.includes('ACTUALIZAR') || normal.includes('DETALLE')) return 'bg-warning text-dark';
        if (normal.includes('ANULAR') || normal.includes('DESHABILITAR')) return 'bg-danger';
        return 'bg-info text-dark';
    }

    function limpiarFormularioUsuario() {
        formUsuario.reset();
        inputUsuarioId.value = '';
        inputUsuarioClave.value = '';
        inputUsuarioEstado.value = 'A';
    }

    function llenarSelectRoles() {
        inputUsuarioRol.innerHTML = '';
        if (!roles.length) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Sin roles disponibles';
            inputUsuarioRol.appendChild(option);
            return;
        }

        roles.forEach((rol) => {
            const option = document.createElement('option');
            option.value = rol.id_rol;
            option.textContent = rol.nombre;
            inputUsuarioRol.appendChild(option);
        });
    }

    function renderUsuarios() {
        tablaUsuarios.innerHTML = '';

        if (!usuarios.length) {
            tablaUsuarios.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay usuarios registrados.</td></tr>';
            return;
        }

        usuarios.forEach((usuario) => {
            const estado = usuario.estado || 'A';
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${usuario.id_usuario}</td>
                <td>${usuario.nombre}</td>
                <td>${usuario.rol_nombre || usuario.rol}</td>
                <td><span class="status-pill ${estadoClase(estado)}">${estadoTexto(estado)}</span></td>
                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-app-outline action-btn" data-action="edit" data-id="${usuario.id_usuario}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-app-outline action-btn" data-action="toggle" data-id="${usuario.id_usuario}">
                            <i class="bi ${estado === 'A' ? 'bi-person-x' : 'bi-person-check'}"></i>
                        </button>
                    </div>
                </td>
            `;
            tablaUsuarios.appendChild(fila);
        });
    }

    function renderRoles() {
        tablaRoles.innerHTML = '';

        if (!roles.length) {
            tablaRoles.innerHTML = '<tr><td colspan="4" class="text-center py-4">No hay roles disponibles.</td></tr>';
            return;
        }

        roles.forEach((rol) => {
            const estado = rol.estado || 'A';
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${rol.id_rol}</td>
                <td>${rol.nombre}</td>
                <td><span class="status-pill ${estadoClase(estado)}">${estadoTexto(estado)}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-app-outline action-btn" data-role-id="${rol.id_rol}">
                        <i class="bi ${estado === 'A' ? 'bi-eye-slash' : 'bi-eye'}"></i>
                    </button>
                </td>
            `;
            tablaRoles.appendChild(fila);
        });
    }

    function renderLogs(logs) {
        tablaLogs.innerHTML = '';

        if (!logs.length) {
            tablaLogs.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay actividad registrada.</td></tr>';
            return;
        }

        logs.forEach((log) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${log.fecha || '-'}</td>
                <td>${log.usuario || '-'}</td>
                <td><span class="badge ${badgeAccion(log.accion)}">${log.accion || '-'}</span></td>
                <td>${log.modulo || '-'}</td>
                <td>${log.detalle || '-'}</td>
            `;
            tablaLogs.appendChild(fila);
        });
    }

    async function cargarRoles() {
        const respuesta = await apiRequest('roles', 'list', {
            params: { include_inactive: true },
        });
        roles = respuesta?.data ?? [];
        llenarSelectRoles();
        renderRoles();
    }

    async function cargarUsuarios() {
        const respuesta = await apiRequest('usuarios', 'list', {
            params: { include_inactive: true },
        });
        usuarios = respuesta?.data ?? [];
        renderUsuarios();
    }

    async function cargarLogs() {
        const respuesta = await apiRequest('actividades', 'list', {
            params: { limit: 50 },
        });
        renderLogs(respuesta?.data ?? []);
    }

    function obtenerUsuarioPorId(id) {
        return usuarios.find((item) => Number(item.id_usuario) === Number(id));
    }

    async function guardarUsuario(payload) {
        if (payload.id_usuario) {
            await apiRequest('usuarios', 'update', {
                method: 'POST',
                body: payload,
            });
        } else {
            await apiRequest('usuarios', 'store', {
                method: 'POST',
                body: payload,
            });
        }
    }

    formUsuario.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            id_usuario: inputUsuarioId.value || undefined,
            nombre: inputUsuarioNombre.value.trim(),
            id_rol: inputUsuarioRol.value,
            estado: inputUsuarioEstado.value,
        };

        if (inputUsuarioClave.value) {
            payload.clave = inputUsuarioClave.value;
        }

        try {
            await guardarUsuario(payload);
            modalUsuario.hide();
            limpiarFormularioUsuario();
            await Promise.all([cargarUsuarios(), cargarLogs()]);
        } catch (error) {
            alert(error?.message || 'No fue posible guardar el usuario.');
        }
    });

    tablaUsuarios.addEventListener('click', (event) => {
        const boton = event.target.closest('button[data-action]');
        if (!boton) {
            return;
        }

        const id = boton.getAttribute('data-id');
        const usuario = obtenerUsuarioPorId(id);
        if (!usuario) {
            return;
        }

        if (boton.dataset.action === 'edit') {
            limpiarFormularioUsuario();
            tituloUsuario.textContent = 'Editar usuario';
            inputUsuarioId.value = usuario.id_usuario;
            inputUsuarioNombre.value = usuario.nombre;
            inputUsuarioRol.value = usuario.rol;
            inputUsuarioEstado.value = usuario.estado || 'A';
            modalUsuario.show();
        }

        if (boton.dataset.action === 'toggle') {
            const nuevoEstado = usuario.estado === 'A' ? 'I' : 'A';
            const confirma = confirm(`Deseas ${nuevoEstado === 'A' ? 'habilitar' : 'deshabilitar'} este usuario?`);
            if (!confirma) {
                return;
            }
            guardarUsuario({
                id_usuario: usuario.id_usuario,
                nombre: usuario.nombre,
                id_rol: usuario.rol,
                estado: nuevoEstado,
            })
                .then(() => Promise.all([cargarUsuarios(), cargarLogs()]))
                .catch((error) => {
                    alert(error?.message || 'No fue posible actualizar el usuario.');
                });
        }
    });

    formRol.addEventListener('submit', async (event) => {
        event.preventDefault();
        const nombre = document.getElementById('rol-nombre').value.trim();
        if (!nombre) {
            return;
        }

        try {
            await apiRequest('roles', 'store', {
                method: 'POST',
                body: { nombre },
            });
            formRol.reset();
            await Promise.all([cargarRoles(), cargarLogs()]);
        } catch (error) {
            alert(error?.message || 'No fue posible registrar el rol.');
        }
    });

    tablaRoles.addEventListener('click', (event) => {
        const boton = event.target.closest('button[data-role-id]');
        if (!boton) {
            return;
        }

        const idRol = boton.getAttribute('data-role-id');
        const rol = roles.find((item) => Number(item.id_rol) === Number(idRol));
        if (!rol) {
            return;
        }

        const nuevoEstado = rol.estado === 'A' ? 'I' : 'A';
        const confirma = confirm(`Deseas ${nuevoEstado === 'A' ? 'habilitar' : 'deshabilitar'} este rol?`);
        if (!confirma) {
            return;
        }

        apiRequest('roles', 'update', {
            method: 'POST',
            body: {
                id_rol: rol.id_rol,
                nombre: rol.nombre,
                estado: nuevoEstado,
            },
        })
            .then(() => Promise.all([cargarRoles(), cargarLogs()]))
            .catch((error) => {
                alert(error?.message || 'No fue posible actualizar el rol.');
            });
    });

    btnNuevoUsuario.addEventListener('click', () => {
        limpiarFormularioUsuario();
        tituloUsuario.textContent = 'Registrar usuario';
        modalUsuario.show();
    });

    btnRoles.addEventListener('click', () => {
        modalRoles.show();
    });

    btnRecargarLogs.addEventListener('click', () => {
        cargarLogs().catch(() => {});
    });

    Promise.all([cargarRoles(), cargarUsuarios(), cargarLogs()]).catch(() => {});
});
