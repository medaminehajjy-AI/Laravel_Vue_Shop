<template>
  <div class="contact-page">
    <div class="contact-hero">
      <h1>Contact Us</h1>
      <p>Have questions? We'd love to hear from you!</p>
    </div>
    
    <div class="contact-content">
      <div class="contact-form-wrapper">
        <h2>Send us a message</h2>
        
        <div v-if="success" class="success-message">
          <CheckCircle :size="24" />
          <span>Thank you! Your message has been sent.</span>
        </div>

       <!-- <div v-else-if="!isAuthenticated" class="form-contact">
          <p>Please login to send messages.</p>
          <button @click="$router.push('/login')">Login</button>
        </div> -->

        <form v-else @submit.prevent="submitForm" class="contact-form">
          <div class="form-group">
            <label>Your Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Your Fullname"
              required
            />
          </div>
          
          <div class="form-group">
            <label>Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              placeholder="YourEmail@example.com"
              required
            />
          </div>
          
          <div class="form-group">
            <label>Subject</label>
            <input 
              v-model="form.subject" 
              type="text" 
              placeholder="How can we help?"
              required
            />
          </div>
          
          <div class="form-group">
            <label>Message</label>
            <textarea 
              v-model="form.message" 
              placeholder="Tell us more about your inquiry..."
              rows="5"
              required
            ></textarea>
          </div>
          
          <button type="submit" class="btn-submit" :disabled="loading">
            <Loader2 v-if="loading" class="animate-spin" :size="20" />
            <span v-else>Send Message</span>
          </button>
        </form>
      </div>
      
      <div class="contact-info">
        <div class="info-card">
          <Mail :size="24" />
          <div>
            <h3>Email</h3>
            <p>support@minishop.com</p>
          </div>
        </div>
        
        <div class="info-card">
          <Phone :size="24" />
          <div>
            <h3>Phone</h3>
            <p>+212-774947907</p>
          </div>
        </div>
        
        <div class="info-card">
          <MapPin :size="24" />
          <div>
            <h3>Address</h3>
            <p>123 Fes Street<br/>Morocco, MAR XXXX</p>
          </div>
        </div>
        
        <div class="info-card">
          <Clock :size="24" />
          <div>
            <h3>Business Hours</h3>
            <p>Mon - Fri: 9AM - 6PM<br/>Sat - Sun: Closed</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Mail, Phone, MapPin, Clock, CheckCircle, Loader2 } from 'lucide-vue-next';
import api from '../services/api';

export default {
  name: 'Contact',
  components: { Mail, Phone, MapPin, Clock, CheckCircle, Loader2 },
  data() {
    return {
      form: {
        name: '',
        email: '',
        subject: '',
        message: ''
      },
      errors: {},
      loading: false,
      success: false
    }
  },
  methods: {
    async submitForm() {
      this.loading = true;
      try {
        const res = await axios.post('/api/contact', this.form);
        alert(res.data.message);
        this.form = { name:'', email:'', subject:'', message:'' };
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          alert('Something went wrong!');
        }
      }finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>/*
 .form-contact {
  text-align: center;
  padding: 60px;
  background: white;
  border-radius: 12px;
}

 .form-contact p {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 20px;
}

.form-contact button {
  padding: 12px 30px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

*/


.contact-page {
  max-width: 1200px;
  margin: 0 auto;
}

.contact-hero {
  text-align: center;
  padding: 60px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 16px;
  margin-bottom: 40px;
}

.contact-hero h1 {
  font-size: 2.5rem;
  margin: 0 0 10px 0;
}

.contact-hero p {
  font-size: 1.2rem;
  margin: 0;
  opacity: 0.9;
}

.contact-content {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 40px;
}

@media (max-width: 900px) {
  .contact-content {
    grid-template-columns: 1fr;
  }
}

.contact-form-wrapper {
  background: white;
  padding: 40px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
}

.contact-form-wrapper h2 {
  margin: 0 0 30px 0;
  color: #1a1a2e;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  transition: border-color 0.2s;
  background: white;
  color: #1a1a2e;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group textarea {
  resize: vertical;
}

.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: transform 0.2s;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.success-message {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px;
  background: #d1fae5;
  color: #065f46;
  border-radius: 10px;
  font-weight: 500;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #f0f0f0;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.info-card svg {
  color: #667eea;
  flex-shrink: 0;
  margin-top: 4px;
}

.info-card h3 {
  margin: 0 0 4px 0;
  color: #1a1a2e;
  font-size: 1rem;
}

.info-card p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.5;
}
</style>
