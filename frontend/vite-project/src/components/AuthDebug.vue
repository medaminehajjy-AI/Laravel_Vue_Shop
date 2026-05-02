<template>
  <div class="debug-page">
    <h1>Authentication Debug Tool</h1>
    
    <div class="debug-section">
      <h2>Step-by-Step Debug</h2>
      
      <div class="test-section">
        <h3>Test 1: Clear Cookies</h3>
        <button @click="testClearCookies" class="btn-test">Clear All Cookies</button>
        <div v-if="testResults.clear" class="result">
          <pre>{{ testResults.clear }}</pre>
        </div>
      </div>
      
      <div class="test-section">
        <h3>Test 2: Get CSRF Cookie</h3>
        <button @click="testCSRFCookie" class="btn-test" :disabled="loading">
          {{ loading ? 'Loading...' : 'Get CSRF Cookie' }}
        </button>
        <div v-if="testResults.csrf" class="result">
          <pre>{{ JSON.stringify(testResults.csrf, null, 2) }}</pre>
        </div>
      </div>
      
      <div class="test-section">
        <h3>Test 3: Login</h3>
        <div class="form-row">
          <input v-model="testEmail" placeholder="Email" type="email" />
          <input v-model="testPassword" placeholder="Password" type="password" />
          <button @click="testLogin" class="btn-test" :disabled="loading">
            {{ loading ? 'Loading...' : 'Test Login' }}
          </button>
        </div>
        <div v-if="testResults.login" class="result">
          <pre>{{ JSON.stringify(testResults.login, null, 2) }}</pre>
        </div>
      </div>
      
      <div class="test-section">
        <h3>Test 4: Check Authentication</h3>
        <button @click="testAuth" class="btn-test" :disabled="loading">
          {{ loading ? 'Loading...' : 'Check Auth Status' }}
        </button>
        <div v-if="testResults.auth" class="result">
          <pre>{{ JSON.stringify(testResults.auth, null, 2) }}</pre>
        </div>
      </div>
    </div>
    
    <div class="debug-section">
      <h2>Current Browser State</h2>
      <div class="cookies-info">
        <p><strong>All Cookies:</strong> {{ currentCookies || 'No cookies' }}</p>
        <p><strong>XSRF Token:</strong> {{ xsrfToken || 'Not found' }}</p>
        <p><strong>Laravel Session:</strong> {{ sessionCookie ? 'Present' : 'Not found' }}</p>
      </div>
      <button @click="refreshCookieInfo" class="btn-refresh">Refresh Cookie Info</button>
    </div>
    
    <div v-if="error" class="error-message">
      <h3>Error Occurred!</h3>
      <pre>{{ JSON.stringify(error, null, 2) }}</pre>
    </div>
  </div>
</template>

<script>
import { api, authAxios } from '../services/api';

export default {
  name: 'AuthDebug',
  data() {
    return {
      loading: false,
      error: null,
      testEmail: 'admin@example.com',
      testPassword: 'password',
      testResults: {
        clear: null,
        csrf: null,
        login: null,
        auth: null
      },
      currentCookies: '',
      xsrfToken: '',
      sessionCookie: false
    }
  },
  mounted() {
    this.refreshCookieInfo();
  },
  methods: {
    getAllCookies() {
      return document.cookie;
    },
    
    refreshCookieInfo() {
      this.currentCookies = this.getAllCookies();
      const match = this.currentCookies.match(/XSRF-TOKEN=([^;]+)/);
      this.xsrfToken = match ? decodeURIComponent(match[1]) : null;
      this.sessionCookie = this.currentCookies.includes('laravel_session');
    },
    
    async testClearCookies() {
      console.log('=== Clearing cookies ===');
      document.cookie.split(";").forEach((c) => { 
        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
      });
      this.testResults.clear = 'Cookies cleared successfully';
      this.refreshCookieInfo();
    },
    
    async testCSRFCookie() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('=== Getting CSRF cookie ===');
        const response = await authAxios.get('/sanctum/csrf-cookie');
        
        this.testResults.csrf = {
          success: true,
          status: response.status,
          cookies: this.getAllCookies(),
          xsrfToken: this.xsrfToken
        };
        
        this.refreshCookieInfo();
      } catch (error) {
        console.error('CSRF Error:', error);
        this.error = error;
        this.testResults.csrf = {
          success: false,
          error: error.message,
          status: error.response?.status,
          data: error.response?.data
        };
      } finally {
        this.loading = false;
      }
    },
    
    async testLogin() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('=== Testing login ===');
        console.log('Cookies before login:', this.getAllCookies());
        
        const response = await authAxios.post('/login', {
          email: this.testEmail,
          password: this.testPassword
        });
        
        this.testResults.login = {
          success: true,
          status: response.status,
          data: response.data,
          cookies: this.getAllCookies()
        };
        
        this.refreshCookieInfo();
      } catch (error) {
        console.error('Login Error:', error);
        this.error = error;
        this.testResults.login = {
          success: false,
          error: error.message,
          status: error.response?.status,
          data: error.response?.data,
          headers: error.response?.headers
        };
      } finally {
        this.loading = false;
      }
    },
    
    async testAuth() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('=== Testing auth check ===');
        console.log('Cookies before auth check:', this.getAllCookies());
        
        const response = await api.get('/user');
        
        this.testResults.auth = {
          success: true,
          status: response.status,
          user: response.data,
          cookies: this.getAllCookies()
        };
        
        this.refreshCookieInfo();
      } catch (error) {
        console.error('Auth Error:', error);
        this.error = error;
        this.testResults.auth = {
          success: false,
          error: error.message,
          status: error.response?.status,
          data: error.response?.data
        };
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.debug-page {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  color: #1a1a2e;
  margin-bottom: 30px;
}

.debug-section {
  background: white;
  padding: 24px;
  border-radius: 12px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

h2 {
  color: #2c3e50;
  margin-bottom: 20px;
  border-bottom: 2px solid #667eea;
  padding-bottom: 10px;
}

.test-section {
  margin-bottom: 24px;
}

h3 {
  color: #34495e;
  margin-bottom: 12px;
}

.btn-test {
  padding: 12px 24px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-test:hover:not(:disabled) {
  background: #5568d3;
  transform: translateY(-1px);
}

.btn-test:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-row {
  display: flex;
  gap: 12px;
  margin-bottom: 12px;
}

.form-row input {
  flex: 1;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

.result {
  margin-top: 16px;
  background: #f8f9fa;
  padding: 16px;
  border-radius: 6px;
  border-left: 4px solid #667eea;
}

.result pre {
  margin: 0;
  white-space: pre-wrap;
  word-wrap: break-word;
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
}

.cookies-info {
  background: #f8f9fa;
  padding: 16px;
  border-radius: 6px;
  margin-bottom: 16px;
}

.cookies-info p {
  margin: 8px 0;
}

.btn-refresh {
  padding: 8px 16px;
  background: #95a5a6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.error-message {
  background: #fee;
  border: 1px solid #fcc;
  padding: 20px;
  border-radius: 8px;
  margin-top: 20px;
}

.error-message h3 {
  color: #c00;
  margin-bottom: 12px;
}

.error-message pre {
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style>
