import api from '../services/api';
import axios from 'axios';

export function useAuthDebug() {
    async function debugStep1_ClearCookies() {
        console.log('=== STEP 1: Clear All Cookies ===');
        document.cookie.split(";").forEach(function(c) { 
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
        });
        console.log('Cookies cleared:', document.cookie);
        return { success: true };
    }

    async function debugStep2_GetCSRFCookie() {
        console.log('=== STEP 2: Get CSRF Cookie ===');
        try {
            const response = await api.get('/sanctum/csrf-cookie');
            console.log('CSRF Cookie Response:', response);
            console.log('All Cookies After CSRF:', document.cookie);
            
            const xsrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];
            console.log('XSRF Token Found:', xsrfToken ? 'YES' : 'NO');
            
            return { 
                success: true, 
                cookies: document.cookie,
                xsrfToken: xsrfToken
            };
        } catch (error) {
            console.error('CSRF Cookie Error:', error);
            return { success: false, error };
        }
    }

    async function debugStep3_Login(email, password) {
        console.log('=== STEP 3: Attempt Login ===');
        console.log('Email:', email);
        console.log('Current Cookies Before Login:', document.cookie);
        
        try {
            const response = await api.post('/api/login', { 
                email, 
                password 
            });
            console.log('Login Success:', response.data);
            console.log('Cookies After Login:', document.cookie);
            
            return { 
                success: true, 
                data: response.data,
                cookies: document.cookie
            };
        } catch (error) {
            console.error('Login Error:', error);
            console.error('Error Status:', error.response?.status);
            console.error('Error Data:', error.response?.data);
            console.error('Error Headers:', error.response?.headers);
            console.log('Cookies After Failed Login:', document.cookie);
            
            return { 
                success: false, 
                status: error.response?.status,
                error: error.response?.data || error.message
            };
        }
    }

    async function debugStep4_CheckAuth() {
        console.log('=== STEP 4: Check Authentication ===');
        console.log('Cookies Before Auth Check:', document.cookie);
        
        try {
            const response = await api.get('/api/user');
            console.log('Auth Check Success:', response.data);
            return { 
                success: true, 
                user: response.data,
                cookies: document.cookie
            };
        } catch (error) {
            console.error('Auth Check Error:', error);
            console.error('Error Status:', error.response?.status);
            console.log('Cookies After Failed Auth Check:', document.cookie);
            
            return { 
                success: false, 
                status: error.response?.status,
                error: error.response?.data || error.message
            };
        }
    }

    async function debugStep5_TestCSRFToken() {
        console.log('=== STEP 5: Test CSRF Token in Request ===');
        
        const xsrfCookie = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];
        console.log('XSRF Cookie Value:', xsrfCookie);
        console.log('Decoded XSRF Token:', xsrfCookie ? decodeURIComponent(xsrfCookie) : 'NOT FOUND');
        
        if (xsrfCookie) {
            try {
                const response = await axios.post(
                    'http://127.0.0.1:8000/api/login',
                    { email: 'test@test.com', password: 'test' },
                    {
                        headers: {
                            'X-XSRF-TOKEN': decodeURIComponent(xsrfCookie),
                            'Content-Type': 'application/json',
                        },
                        withCredentials: true
                    }
                );
                console.log('Direct Axios Success:', response);
                return { success: true, response };
            } catch (error) {
                console.error('Direct Axios Error:', error);
                return { success: false, error };
            }
        }
        
        return { success: false, error: 'No XSRF token found' };
    }

    async function fullAuthTest(email, password) {
        console.log('\n');
        console.log('╔════════════════════════════════════════╗');
        console.log('║   COMPREHENSIVE AUTH DEBUG TEST       ║');
        console.log('╚════════════════════════════════════════╝');
        console.log('');
        
        await debugStep1_ClearCookies();
        console.log('');
        
        const csrfResult = await debugStep2_GetCSRFCookie();
        if (!csrfResult.success) {
            console.log('❌ FAILED at Step 2: Cannot get CSRF cookie');
            return { failed: 'CSRF cookie acquisition failed' };
        }
        console.log('✅ Step 2: CSRF cookie acquired');
        console.log('');
        
        const loginResult = await debugStep3_Login(email, password);
        if (!loginResult.success) {
            console.log('❌ FAILED at Step 3: Login failed');
            console.log('Status:', loginResult.status);
            console.log('Error:', loginResult.error);
            return loginResult;
        }
        console.log('✅ Step 3: Login successful');
        console.log('');
        
        const authResult = await debugStep4_CheckAuth();
        if (!authResult.success) {
            console.log('❌ FAILED at Step 4: Auth check failed');
            return authResult;
        }
        console.log('✅ Step 4: Authentication verified');
        console.log('');
        
        console.log('╔════════════════════════════════════════╗');
        console.log('║   ✅ ALL TESTS PASSED!               ║');
        console.log('╚════════════════════════════════════════╝');
        
        return { 
            success: true, 
            user: authResult.user,
            cookies: authResult.cookies 
        };
    }

    return {
        debugStep1_ClearCookies,
        debugStep2_GetCSRFCookie,
        debugStep3_Login,
        debugStep4_CheckAuth,
        debugStep5_TestCSRFToken,
        fullAuthTest
    };
}
