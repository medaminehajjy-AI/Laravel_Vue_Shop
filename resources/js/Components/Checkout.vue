<template>
  <div class="checkout-page">
    <h1>Checkout</h1>
    
    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="cartItems.length === 0" class="empty-cart">
      <p>Your cart is empty. Add items before checking out.</p>
      <button @click="$router.push('/')">Continue Shopping</button>
    </div>
    
    <div v-else class="checkout-content">
      <div class="checkout-items">
        <h2>Order Summary</h2>
        <div class="items-list">
          <div v-for="item in cartItems" :key="item.id" class="checkout-item">
            <img 
              :src="item.product?.image || '/placeholder.png'" 
              :alt="item.product?.name"
              class="item-image"
              @error="handleImageError"
            />
            <div class="item-info">
              <h4>{{ item.product?.name }}</h4>
              <p>Qty: {{ item.quantity }} × ${{ item.product?.price }}</p>
            </div>
            <div class="item-price">
              ${{ ((item.product?.price || 0) * item.quantity).toFixed(2) }}
            </div>
          </div>
        </div>
      </div>
      
      <div class="checkout-summary">
        <h2>Total</h2>
        <div class="summary-details">
          <div class="summary-row">
            <span>Subtotal</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>$0.00</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>

        <div class="toggle-header" @click="showForm = !showForm">
          <span class="arrow" :class="{ open: showForm }">▶</span>
          <span class="title">Fill This Form - اضغط هنا </span>
        </div>

        <div v-if="showForm" class="checkout-form">
          <input type="tel" v-model="phone" placeholder="Phone Number" />

          <textarea v-model="shippingAddress" placeholder="Shipping Address"></textarea>

          <textarea v-model="notes" placeholder="Order Notes (Optional)"></textarea>

          <div class="payment-method">
            <label>
              <input type="radio" name="paymentmethod" value="cod" v-model="paymentMethod" />
              Cash on Delivery
            </label>

            <label>
              <input type="radio" name="paymentmethod" value="online" v-model="paymentMethod" disabled/>
              Online Payment <span style="color: gray;">(Coming Soon)</span>
            </label>
          </div>
        </div>

        <button @click="placeOrder" class="btn-place-order" :disabled="processing">
          {{ processing ? 'Processing...' : 'Place Order' }}
        </button>
        
        <button @click="$router.push('/cart')" class="btn-back-cart">
          Back to Cart
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watchEffect } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { useAuth } from '../composables/useAuth';

export default {
  name: 'Checkout',
  setup() {
    const router = useRouter();
    const { user } = useAuth();
    
    const phone = ref('');
    const shippingAddress = ref('');
    const notes = ref('');
    const paymentMethod = ref('cod');

    const cartItems = ref([]);
    const loading = ref(false);
    const processing = ref(false);

    const total = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        return sum + ((item.product?.price || 0) * item.quantity);
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

    watchEffect(() => {
      if (user.value) {
        fetchCart();
      } else {
        cartItems.value = [];
        loading.value = false;
      }
    });

    const placeOrder = async () => {
      if (!phone.value || !shippingAddress.value) {
        alert('Please fill all required fields.');
        return;
      }
      
      processing.value = true;
      try {
        const response = await api.post('/api/checkout', {
          phone: phone.value,
          shipping_address: shippingAddress.value,
          notes: notes.value,
          cart_items: cartItems.value,
          total_amount: total.value,
          payment_method: paymentMethod.value
        });

        cartItems.value = [];
        window.dispatchEvent(new CustomEvent('cart-count-updated', {
          detail: 0
        }));
        window.dispatchEvent(new Event('orders-updated'));
        alert(response.data.message);

        router.push('/');
      } catch (error) {
        console.error('Checkout failed:', error);
        alert(error.response?.data?.message || 'Checkout failed. Please try again.');
      } finally {
        processing.value = false;
      }
    };

    const handleImageError = (event) => {
      event.target.src = '/placeholder.png';
    };

    return {
      showForm: ref(false),
      cartItems,
      phone,
      shippingAddress,
      notes,
      loading,
      processing,
      total,
      placeOrder,
      handleImageError
    };
  }
}
</script>

<style scoped>
.toggle-header {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 15px;
}

.arrow {
    transition: transform 0.3s;
}

.arrow.open {
    transform: rotate(90deg);
}

.checkout-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.checkout-page h1 {
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

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 30px;
}

@media (max-width: 768px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }
}

.checkout-items {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.checkout-items h2 {
  margin: 0 0 20px 0;
  color: #1a1a2e;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.checkout-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px 0;
  border-bottom: 1px solid #eee;
}

.checkout-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}

.item-info {
  flex: 1;
}

.item-info h4 {
  margin: 0 0 5px 0;
  color: #1a1a2e;
}

.item-info p {
  margin: 0;
  color: #666;
}

.item-price {
  font-weight: bold;
  color: #667eea;
}

.checkout-summary {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  height: fit-content;
}

.checkout-summary h2 {
  margin: 0 0 20px 0;
  color: #1a1a2e;
}

.summary-details {
  margin-bottom: 20px;
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
  padding-top: 15px;
  color: #667eea;
}

.checkout-form {
  margin: 20px 0;
}

.checkout-form input,
.checkout-form textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 1rem;
  box-sizing: border-box;
}

.checkout-form textarea {
  resize: vertical;
  min-height: 80px;
}

.payment-method {
  margin: 15px 0;
}

.payment-method label {
  display: block;
  padding: 10px;
  margin-bottom: 5px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
}

.payment-method input {
  margin-right: 10px;
}

.btn-place-order {
  width: 100%;
  padding: 15px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  margin-top: 10px;
  transition: background 0.3s;
}

.btn-place-order:hover:not(:disabled) {
  background: #5568d3;
}

.btn-place-order:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-back-cart {
  width: 100%;
  padding: 12px;
  background: #f8f9fa;
  color: #1a1a2e;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}

.btn-back-cart:hover {
  background: #e9ecef;
}
</style>
