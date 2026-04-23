<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <h2>Create Account</h2>
        <p>Join us and start shopping</p>
      </div>
      
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <form @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input 
            id="name"
            v-model="name" 
            type="text" 
            placeholder="Enter your full name"
            required 
          />
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            id="email"
            v-model="email" 
            type="email" 
            placeholder="Enter your email"
            required 
          />
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            id="password"
            v-model="password" 
            type="password" 
            placeholder="Create a password (min 8 characters)"
            required 
            minlength="8"
          />
        </div>
        
        <div class="form-group">
          <label for="confirm-password">Confirm Password</label>
          <input 
            id="confirm-password"
            v-model="password_confirmation" 
            type="password" 
            placeholder="Confirm your password"
            required 
          />
        </div>
        
        <button type="submit" class="btn-primary" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          {{ loading ? 'Creating account...' : 'Create Account' }}
        </button>
      </form>
      
      <div class="auth-footer">
        <p>Already have an account? 
          <router-link to="/login" class="link">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuth } from '../../composables/useAuth';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      error: '',
      loading: false
    }
  },
  setup() {
    const { register, syncAuth } = useAuth();
    return { register, syncAuth };
  },
  methods: {
    async handleRegister() {
      this.error = '';
      
      if (this.password !== this.password_confirmation) {
        this.error = 'Passwords do not match';
        return;
      }
      
      if (this.password.length < 8) {
        this.error = 'Password must be at least 8 characters';
        return;
      }
      
      this.loading = true;
      
      const result = await this.register(this.name, this.email, this.password, this.password_confirmation);
      
      if (result.success) {
        await this.syncAuth();
        this.$router.push('/');
      } else {
        this.error = result.message;
      }
      
      this.loading = false;
    }
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 72px);
  padding: 20px;
}

.auth-card {
  width: 100%;
  max-width: 440px;
  background: white;
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.auth-header {
  text-align: center;
  margin-bottom: 32px;
}

.auth-header h2 {
  color: #1a1a2e;
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.auth-header p {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #374151;
  font-size: 0.9rem;
  font-weight: 500;
}

.form-group input {
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: #f9fafb;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  background: white;
}

.form-group input::placeholder {
  color: #9ca3af;
}

.btn-primary {
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-message {
  color: #dc2626;
  background: #fef2f2;
  border: 1px solid #fecaca;
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 0.9rem;
  margin-bottom: 20px;
  text-align: center;
}

.auth-footer {
  margin-top: 24px;
  text-align: center;
}

.auth-footer p {
  color: #6b7280;
  font-size: 0.9rem;
  margin: 0;
}

.auth-footer .link {
  color: #667eea;
  font-weight: 500;
  text-decoration: none;
}

.auth-footer .link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .auth-card {
    padding: 24px;
    margin: 16px;
  }
  
  .auth-header h2 {
    font-size: 1.5rem;
  }
}
</style>