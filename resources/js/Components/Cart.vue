<template>
  <div class="cart-page">
    <h1>Shopping Cart</h1>
    
    <div v-if="loading" class="loading">Loading cart...</div>
    
    <div v-else-if="cartItems.length === 0" class="empty-cart">
      <p>Your cart is empty.</p>
      <button @click="$router.push('/')">Continue Shopping</button>
    </div>
    
    <div v-else class="cart-content">
      <div class="cart-items">
        <div v-for="item in cartItems" :key="item.id" class="cart-item">
          <img 
            :src="item.product?.image || '/placeholder.png'" 
            :alt="item.product?.name"
            class="item-image"
            @error="handleImageError"
            @click="$router.push(`/product/${item.product_id}`)"
          />
          
          <div class="item-details">
            <h3 @click="$router.push(`/product/${item.product_id}`)">
              {{ item.product?.name || 'Unknown Product' }}
            </h3>
            <p class="price">${{ item.product?.price || 0 }}</p>
          </div>
          
          <div class="quantity-controls">
            <button @click="decreaseQuantity(item)" :disabled="item.quantity <= 1">-</button>
            <span>{{ item.quantity }}</span>
            <button @click="increaseQuantity(item)" :disabled="item.quantity >= item.product?.stock">+</button>
          </div>
          
          <div class="item-total">
            <p>${{ calculateItemTotal(item) }}</p>
          </div>
          
          <button @click="removeItem(item.id)" class="btn-remove">Remove</button>
        </div>
      </div>
      
      <div class="cart-summary">
        <h2>Summary</h2>
        <div class="summary-row">
          <span>Subtotal ({{ cartItems.length }} items)</span>
          <span>${{ subtotal.toFixed(2) }}</span>
        </div>
        <div class="summary-row total">
          <span>Total</span>
          <span>${{ subtotal.toFixed(2) }}</span>
        </div>
        <button @click="proceedToCheckout" class="btn-checkout">
          Proceed to Checkout
        </button>
        <button @click="$router.push('/')" class="btn-continue">
          Continue Shopping
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted } from 'vue';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { useAuth } from '../composables/useAuth';

export default {
  name: 'Cart',
  setup() {
    const router = useRouter();
    const { user } = useAuth();
    
    const { initAuth } = useAuth();

    const cartItems = ref([]);
    const loading = ref(false);

    const subtotal = computed(() => {
      return cartItems.value.reduce((total, item) => {
        return total + (item.product?.price * item.quantity || 0);
      }, 0);
    });

    const fetchCart = async () => {
      loading.value = true;
      try {
        const response = await api.get('/api/cart');
        cartItems.value = response.data;
      } catch (error) {
        console.error('Failed to fetch cart:', error);
        cartItems.value = [];
      } finally {
        loading.value = false;
      }
    };

    
      onMounted(async () => {
        await initAuth();

        if (user.value) {
          fetchCart();
        }
      });

    const increaseQuantity = async (item) => {
      if (item.quantity >= item.product?.stock) return;
      
      try {
        await api.put(`/api/cart/${item.id}`, { quantity: item.quantity + 1 });
        item.quantity++;
      } catch (error) {
        console.error('Failed to update quantity:', error);
      }
    };

    const decreaseQuantity = async (item) => {
      if (item.quantity <= 1) return;
      
      try {
        await api.put(`/api/cart/${item.id}`, { quantity: item.quantity - 1 });
        item.quantity--;
      } catch (error) {
        console.error('Failed to update quantity:', error);
      }
    };

    const removeItem = async (id) => {
      if (!confirm('Are you sure you want to remove this item?')) return;
      
      try {
        await api.delete(`/api/cart/${id}`);
        cartItems.value = cartItems.value.filter(item => item.id !== id);
        window.dispatchEvent(new CustomEvent('cart-count-updated', {
          detail: cartItems.value.length
        }));
      } catch (error) {
        console.error('Failed to remove item:', error);
      }
    };

    const calculateItemTotal = (item) => {
      return (item.product?.price * item.quantity || 0).toFixed(2);
    };

    const proceedToCheckout = () => {
      router.push('/checkout');
    };

    const handleImageError = (event) => {
      event.target.src = '/placeholder.png';
    };

    return {
      cartItems,
      loading,
      subtotal,
      increaseQuantity,
      decreaseQuantity,
      removeItem,
      calculateItemTotal,
      proceedToCheckout,
      handleImageError
    };
  }
}
</script>

<style scoped>
.cart-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.cart-page h1 {
  margin-bottom: 30px;
  color: #1a1a2e;
}

.loading, .empty-cart {
  text-align: center;
  padding: 60px;
  background: white;
  border-radius: 12px;
}

.empty-cart p {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 20px;
}

.empty-cart button {
  padding: 12px 30px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 30px;
}

@media (max-width: 768px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
}

.cart-items {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto auto auto;
  gap: 20px;
  align-items: center;
  padding: 20px 0;
  border-bottom: 1px solid #e5e7eb;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
}

.item-details h3 {
  margin: 0 0 5px 0;
  font-size: 1rem;
  cursor: pointer;
  color: #1a1a2e;
}

.item-details h3:hover {
  color: #667eea;
}

.item-details .price {
  margin: 0;
  color: #667eea;
  font-weight: bold;
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
}

.quantity-controls button {
  padding: 8px 12px;
  background: #f8f9fa;
  border: none;
  cursor: pointer;
  color: #1a1a2e;
}

.quantity-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-controls span {
  padding: 8px 15px;
  min-width: 40px;
  text-align: center;
}

.item-total p {
  font-weight: bold;
  font-size: 1.1rem;
  margin: 0;
}

.btn-remove {
  padding: 8px 15px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.cart-summary {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  height: fit-content;
}

.cart-summary h2 {
  margin: 0 0 20px 0;
  font-size: 1.3rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.summary-row.total {
  font-size: 1.3rem;
  font-weight: bold;
  border-bottom: none;
  padding-top: 20px;
  color: #667eea;
}

.btn-checkout {
  width: 100%;
  padding: 15px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  margin-top: 20px;
  transition: background 0.3s;
}

.btn-checkout:hover {
  background: #5568d3;
}

.btn-continue {
  width: 100%;
  padding: 12px;
  background: #f8f9fa;
  color: #1a1a2e;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}

.btn-continue:hover {
  background: #e9ecef;
}
</style>
