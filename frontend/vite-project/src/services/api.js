import axios from 'axios';
import Cookies from 'js-cookie';

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000';

// Main API instance - for routes under /api/*
const api = axios.create({
    baseURL: `${API_BASE}/api`,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    withXSRFToken: true,
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

// Auth instance - for web routes (/login, /register, /logout, /sanctum/csrf-cookie)
const authAxios = axios.create({
    baseURL: API_BASE,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

let csrfPromise = null;

async function refreshCsrfToken() {
    if (csrfPromise) {
        return csrfPromise;
    }

    csrfPromise = authAxios.get('/sanctum/csrf-cookie', {
        withCredentials: true,
    }).then(() => {
        return Cookies.get('XSRF-TOKEN');
    }).finally(() => {
        csrfPromise = null;
    });

    return csrfPromise;
}

api.interceptors.request.use(async (config) => {
    if (['post', 'put', 'delete', 'patch'].includes(config.method)) {
        await refreshCsrfToken();
    }

    const token = Cookies.get('XSRF-TOKEN');
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }

    return config;
});

authAxios.interceptors.request.use(async (config) => {
    if (['post', 'put', 'delete', 'patch'].includes(config.method)) {
        await refreshCsrfToken();
    }

    const token = Cookies.get('XSRF-TOKEN');
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }

    return config;
});

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if ((error.response?.status === 419 || error.response?.status === 401) && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                await refreshCsrfToken();
                originalRequest.headers['X-XSRF-TOKEN'] = Cookies.get('XSRF-TOKEN');
                return api(originalRequest);
            } catch (csrfError) {
                console.error('CSRF refresh failed:', csrfError);
            }
        }

        if (error.response?.status === 401) {
            console.error('Unauthorized - user may not be authenticated');
        }

        return Promise.reject(error);
    }
);

authAxios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if ((error.response?.status === 419) && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                await refreshCsrfToken();
                originalRequest.headers['X-XSRF-TOKEN'] = Cookies.get('XSRF-TOKEN');
                return authAxios(originalRequest);
            } catch (csrfError) {
                console.error('CSRF refresh failed:', csrfError);
            }
        }

        return Promise.reject(error);
    }
);

export { api, authAxios, refreshCsrfToken };
export default api;
