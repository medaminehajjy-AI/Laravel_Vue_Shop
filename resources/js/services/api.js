import axios from 'axios';
import Cookies from 'js-cookie';

const baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';

const api = axios.create({
    baseURL,
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

let csrfPromise = null;

async function refreshCsrfToken() {
    if (csrfPromise) {
        return csrfPromise;
    }

    csrfPromise = axios.get(`${baseURL}/sanctum/csrf-cookie`, {
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

export default api;
