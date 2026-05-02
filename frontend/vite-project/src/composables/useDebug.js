import { api, authAxios } from '../services/api';

export function useDebug() {
    async function checkCSRF() {
        try {
            const response = await api.get('/debug/csrf');
            console.log('CSRF Debug Info:', response.data);
            return response.data;
        } catch (error) {
            console.error('CSRF Debug Error:', error);
            throw error;
        }
    }

    async function checkAuth() {
        try {
            const response = await api.get('/debug/auth');
            console.log('Auth Debug Info:', response.data);
            return response.data;
        } catch (error) {
            console.error('Auth Debug Error:', error);
            throw error;
        }
    }

    async function testLogin(email, password) {
        console.log('Testing login with:', email);
        
        try {
            await authAxios.get('/sanctum/csrf-cookie');
            console.log('CSRF cookie set');
            
            const loginResponse = await authAxios.post('/login', { email, password });
            console.log('Login successful:', loginResponse.data);
            
            return { success: true, data: loginResponse.data };
        } catch (error) {
            console.error('Login error:', error);
            console.error('Error response:', error.response);
            return { success: false, error };
        }
    }

    async function testAuthCheck() {
        console.log('Testing /api/user endpoint...');
        
        try {
            const response = await api.get('/user');
            console.log('User data:', response.data);
            return { authenticated: true, user: response.data };
        } catch (error) {
            console.error('Auth check error:', error);
            console.error('Status:', error.response?.status);
            return { authenticated: false, error };
        }
    }

    return {
        checkCSRF,
        checkAuth,
        testLogin,
        testAuthCheck
    };
}
