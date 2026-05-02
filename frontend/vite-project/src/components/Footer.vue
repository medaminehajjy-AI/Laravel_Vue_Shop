<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-brand">
        <router-link to="/" class="footer-logo">
          <ShoppingBag :size="24" />
          <span>Mol-Lmli7Shop</span>
        </router-link>
        <p class="footer-tagline">Your one-stop shop for quality products at great prices.</p>
      </div>
      
      <div class="footer-links">
        <h4>Quick Links</h4>
        <nav>
          <router-link to="/">Home</router-link>
          <router-link to="/about">About Us</router-link>
          <router-link to="/contact">Contact</router-link>
        </nav>
      </div>
      
      <div class="footer-links">
        <h4>Categories</h4>
        <nav>
          <router-link v-for="cat in categories" :key="cat.id" :to="`/category/${cat.id}`">
            {{ cat.name }}
          </router-link>
        </nav>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; {{ currentYear }} Mol-Lmli7Shop. All rights reserved.</p>
    </div>
  </footer>
</template>

<script>
import { ref, onMounted } from 'vue';
import { ShoppingBag } from 'lucide-vue-next';
import api from '../services/api';

export default {
  name: 'Footer',
  components: { ShoppingBag },
  setup() {
    const categories = ref([]);
    const currentYear = new Date().getFullYear();

    const fetchCategories = async () => {
      try {
        const response = await api.get('/categories');
        categories.value = response.data;
      } catch (error) {
        categories.value = [];
      }
    };

    onMounted(() => {
      fetchCategories();
    });

    return {
      categories,
      currentYear
    };
  }
}
</script>

<style scoped>
.footer {
  background: #1a1a2e;
  color: #d1d5db;
  padding: 48px 24px 0;
  margin-top: auto;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 48px;
  padding-bottom: 32px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: white;
  font-weight: 700;
  font-size: 1.25rem;
  margin-bottom: 16px;
}

.footer-logo:hover {
  color: #667eea;
}

.footer-tagline {
  color: #9ca3af;
  line-height: 1.6;
  max-width: 300px;
}

.footer-links h4 {
  color: white;
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 20px;
}

.footer-links nav {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-links a {
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.2s ease;
  font-size: 0.9rem;
}

.footer-links a:hover {
  color: #667eea;
}

.footer-bottom {
  border-top: 1px solid #374151;
  padding: 24px;
  text-align: center;
  font-size: 0.85rem;
  color: #6b7280;
}

@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr;
    gap: 32px;
  }
}
</style>
