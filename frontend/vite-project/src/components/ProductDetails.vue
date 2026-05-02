<template>
  <div class="product-details">
    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="!product" class="not-found">
      <h2>Product not found</h2>
      <button @click="$router.push('/')">Back to Home</button>
    </div>
    
    <div v-else class="product-content">
      <button @click="$router.push('/')" class="btn-back">← Back</button>
      
      <div class="product-container">
        <div class="product-image-section">
          <img 
            :src="product.image || '/placeholder.png'" 
            :alt="product.name" 
            class="main-image"
            @error="handleImageError"
          />
        </div>
        
        <div class="product-info-section">
          <h1>{{ product.name }}</h1>
          <p class="price">${{ product.price }}</p>
          
          <div class="stock-info">
            <span v-if="product.stock > 0" class="in-stock">
              ✓ In Stock ({{ product.stock }} available)
            </span>
            <span v-else class="out-of-stock">
              ✗ Out of Stock
            </span>
          </div>
          
          <div class="description">
            <h3>Description</h3>
            <p>{{ product.description || 'No description available.' }}</p>
          </div>
          
          <div class="actions">
            <div class="quantity-selector">
              <button @click="quantity > 1 ? quantity-- : null">-</button>
              <input type="number" v-model.number="quantity" min="1" :max="product.stock" />
              <button @click="quantity < product.stock ? quantity++ : null">+</button>
            </div>
            
            <button 
              @click="addToCart" 
              class="btn-add-cart"
              :disabled="product.stock === 0"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { useAuth } from '../composables/useAuth';

export default {
  name: 'ProductDetails',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { user, isAuthenticated } = useAuth();
    
    const product = ref(null);
    const loading = ref(false);
    const quantity = ref(1);

    const fetchProduct = async (id) => {
      loading.value = true;
      try {
        const response = await api.get(`/products/${id}`);
        product.value = response.data;
      } catch (error) {
        console.error('Failed to fetch product:', error);
        product.value = null;
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchProduct(route.params.id);
    });

    watch(() => route.params.id, (newId) => {
      fetchProduct(newId);
    });
      
    const addToCart = async () => {
      if (!user.value) {
        alert('Please login to add items to cart');
        router.push('/login');
        return;
      }
      try {
        await api.post('/cart', { 
          product_id: product.value.id, 
          quantity: quantity.value 
        });
        
        const countResponse = await api.get('/cart/count');
        window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: countResponse.data.count || 0 }));
        
        alert('Product added to cart!');
        router.push('/cart');
      } catch (error) {
        console.error('Failed to add to cart:', error);
        alert('Failed to add to cart');
      }
    };

    const handleImageError = (event) => {
      event.target.src = '/placeholder.png';
    };

    return {
      product,
      loading,
      quantity,
      addToCart,
      handleImageError,
      isAuthenticated,
      user
    };
  }
}
</script>

<style scoped>
.product-details {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.loading, .not-found {
  text-align: center;
  padding: 60px;
}

.not-found button {
  padding: 10px 30px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 20px;
}

.btn-back {
  padding: 10px 20px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  margin-bottom: 20px;
}

.product-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
  .product-container {
    grid-template-columns: 1fr;
  }
}

.main-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 8px;
}

.product-info-section h1 {
  margin: 0 0 10px 0;
  color: #1a1a2e;
}

.price {
  font-size: 2rem;
  font-weight: bold;
  color: #667eea;
  margin: 0 0 20px 0;
}

.stock-info {
  margin-bottom: 20px;
}

.in-stock {
  color: #28a745;
  font-weight: 500;
}

.out-of-stock {
  color: #dc3545;
  font-weight: 500;
}

.description {
  margin-bottom: 30px;
}

.description h3 {
  margin: 0 0 10px 0;
  color: #1a1a2e;
}

.description p {
  color: #6b7280;
  line-height: 1.6;
}

.actions {
  display: flex;
  gap: 15px;
  align-items: center;
}

.quantity-selector {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 6px;
  overflow: hidden;
}

.quantity-selector button {
  padding: 10px 15px;
  background: #f8f9fa;
  border: none;
  cursor: pointer;
}

.quantity-selector button:hover {
  background: #e9ecef;
}

.quantity-selector input {
  width: 50px;
  padding: 10px;
  border: none;
  text-align: center;
  font-size: 1rem;
}

.quantity-selector input::-webkit-inner-spin-button,
.quantity-selector input::-webkit-outer-spin-button {
  -webkit-appearance: none;
}

.btn-add-cart {
  flex: 1;
  padding: 15px 30px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background 0.3s;
}

.btn-add-cart:hover:not(:disabled) {
  background: #5568d3;
}

.btn-add-cart:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
