import { ref, computed } from 'vue';
import { api, authAxios } from '../services/api';

const user = ref(null);
const loading = ref(false);
let initialized = false;

export function useAuth() {
    const isAuthenticated = computed(() => !!user.value);
    const isAdmin = computed(() => user.value?.is_admin === true);

    async function fetchUser() {
        try {
            const response = await api.get('/user');
            user.value = response.data;
            console.log('fetchUser success:', user.value);
            return user.value;
        } catch (error) {
            user.value = null;
            console.log('fetchUser failed:', error.response?.status);
            return null;
        }
    }

    async function login(email, password) {
        loading.value = true;
        try {
            await authAxios.get('/sanctum/csrf-cookie');
            const response = await authAxios.post('/login', { email, password });
            const newUser = await fetchUser();
            return { success: true, user: newUser };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Login failed'
            };
        } finally {
            loading.value = false;
        }
    }

    async function register(name, email, password, password_confirmation) {
        loading.value = true;
        try {
            await authAxios.get('/sanctum/csrf-cookie');
            const response = await authAxios.post('/register', { name, email, password, password_confirmation });
            const newUser = await fetchUser();
            return { success: true, user: newUser };
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors;
                return { success: false, message: Object.values(errors).flat().join(', ') };
            }
            return { success: false, message: 'Registration failed' };
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        loading.value = true;
        try {
            await authAxios.get('/sanctum/csrf-cookie');
            await authAxios.post('/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            user.value = null;
            loading.value = false;
        }
    }

    async function initAuth() {
        if (initialized) return user.value;
        
        await fetchUser();
        initialized = true;
        return user.value;
    }

    async function syncAuth() {
        await fetchUser();
    }

    return {
        user,
        loading,
        isAuthenticated,
        isAdmin,
        fetchUser,
        login,
        register,
        logout,
        initAuth,
        syncAuth
    };
}
