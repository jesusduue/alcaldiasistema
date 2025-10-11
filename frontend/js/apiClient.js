const API_BASE_URL = '../../backend/public/index.php';

async function apiRequest(entity, action, options = {}) {
    const { method = 'GET', params = {}, body = null } = options;
    const searchParams = new URLSearchParams({ entity, action, ...params });
    const config = {
        method,
        headers: {
            'Accept': 'application/json',
        },
    };

    if (body) {
        config.headers['Content-Type'] = 'application/json';
        config.body = JSON.stringify(body);
    }

    const response = await fetch(`${API_BASE_URL}?${searchParams.toString()}`, config);

    if (!response.ok) {
        const errorPayload = await safeJson(response);
        const message = errorPayload?.message || 'Error inesperado';
        const error = new Error(message);
        error.status = response.status;
        error.payload = errorPayload;
        throw error;
    }

    return safeJson(response);
}

async function safeJson(response) {
    try {
        return await response.json();
    } catch (error) {
        return null;
    }
}
