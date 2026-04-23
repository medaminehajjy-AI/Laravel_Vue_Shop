<template>
  <div class="category-products">
    <div class="page-header">
      <h1>{{ category?.name || 'Category' }} Products</h1>
      <p>Browse products in this category</p>
    </div>

    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="products.length === 0" class="no-products">
      <p>No products found in this category.</p>
      <router-link to="/" class="back-link">Back to Home</router-link>
    </div>
    
    <div v-else class="products-grid">
      <div v-for="product in products" :key="product.id" class="product-card">
        <img 
          :src="product.image || '/placeholder.png'" 
          :alt="product.name" 
          class="product-image"
          @error="handleImageError"
          @click="goToProduct(product.id)"
        />
        <div class="product-info">
          <h3 @click="goToProduct(product.id)">{{ product.name }}</h3>
          <p class="description">{{ truncate(product.description, 60) }}</p>
          <div class="product-footer">
            <span class="price">${{ product.price }}</span>
            <button @click="addToCart(product.id)" class="btn-add-cart">
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
  name: 'CategoryProducts',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { isAuthenticated } = useAuth();
    const products = ref([]);
    const category = ref(null);
    const loading = ref(false);

    const fetchCategoryProducts = async () => {
      const categoryId = route.params.id;
      loading.value = true;
      try {
        const [catResponse, prodResponse] = await Promise.all([
          api.get(`/api/categories/${categoryId}`),
          api.get('/api/home-products')
        ]);
        category.value = catResponse.data;
        const allProducts = prodResponse.data.data;
        products.value = allProducts.filter(p => p.category_id == categoryId);
      } catch (error) {
        console.error('Failed to fetch products:', error);
      } finally {
        loading.value = false;
      }
    };

    const addToCart = async (productId) => {
      try {
        await api.post('/api/cart', { product_id: productId, quantity: 1 });
        
        const countResponse = await api.get('/api/cart/count');
        window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: countResponse.data.count || 0 }));
        
        alert('Product added to cart!');
      } catch (error) {
        if (error.response?.status === 401) {
          alert('Please login to add items to cart');
          router.push('/login');
        } else {
          console.error('Failed to add to cart:', error);
          alert('Failed to add to cart');
        }
      }
    };

    const goToProduct = (id) => {
      router.push(`/product/${id}`);
    };

    const truncate = (text, length) => {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    };

    const handleImageError = (event) => {
      event.target.src = '/placeholder.png';
    };

    watch(() => route.params.id, fetchCategoryProducts);

    onMounted(() => {
      fetchCategoryProducts();
    });

    return {
      products,
      category,
      loading,
      addToCart,
      goToProduct,
      truncate,
      handleImageError
    };
  }
}
</script>

<style scoped>
.category-products {
  padding: 20px;
}

.page-header {
  margin-bottom: 32px;
}

.page-header h1 {
  font-size: 2rem;
  color: #1a1a2e;
  margin-bottom: 8px;
}

.page-header p {
  color: #6b7280;
}

.loading, .no-products {
  text-align: center;
  padding: 60px;
  color: #666;
}

.back-link {
  display: inline-block;
  margin-top: 16px;
  color: #667eea;
  text-decoration: none;
}

.back-link:hover {
  text-decoration: underline;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  cursor: pointer;
}

.product-info {
  padding: 20px;
}

.product-info h3 {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  cursor: pointer;
  color: #1a1a2e;
}

.product-info h3:hover {
  color: #667eea;
}

.description {
  color: #6b7280;
  font-size: 0.9rem;
  margin: 0 0 15px 0;
  line-height: 1.4;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price {
  font-size: 1.3rem;
  font-weight: bold;
  color: #667eea;
}

.btn-add-cart {
  padding: 10px 20px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.3s;
}

.btn-add-cart:hover {
  background: #5568d3;
}
</style>
