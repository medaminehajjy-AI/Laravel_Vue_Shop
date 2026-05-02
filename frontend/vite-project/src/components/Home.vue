<template>
  <div class="home">
    <div class="hero">
      <h1>Welcome to Our Shop</h1>
      <p>Discover amazing products at great prices</p>
      <div v-if="categories.length > 0" class="categories-section">
      <h2>Shop by Category</h2>
      <div class="categories-grid">
        <router-link 
          v-for="category in categories" 
          :key="category.id" 
          :to="`/category/${category.id}`"
          class="category-card"
        >
          <span class="category-name">{{ category.name }}</span>
        </router-link>
      </div>
    </div>
    </div>

    
    
    <div class="products-section">
      <h2>Featured Products</h2>
      <div v-if="loading" class="loading">Loading products...</div>
    
    <div v-else-if="products.length === 0" class="no-products">
      <p>No products available at the moment.</p>
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

    <div v-if="pagination.last_page > 1" class="pagination">
      <div class="pagination-info">
        Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to 
        {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
        of {{ pagination.total }} products
      </div>
      <div class="pagination-controls">
        <button 
          @click="changePage(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1"
          class="pagination-btn"
        >
          Previous
        </button>
        <button 
          v-for="page in displayedPages" 
          :key="page"
          @click="changePage(page)"
          :class="['pagination-btn', { active: page === pagination.current_page }]"
          :disabled="page === '...'"
        >
          {{ page }}
        </button>
        <button 
          @click="changePage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page"
          class="pagination-btn"
        >
          Next
        </button>
      </div>
    </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { useAuth } from '../composables/useAuth';

export default {
  name: 'Home',
  setup() {
    const router = useRouter();
    const { isAuthenticated } = useAuth();
    const { initAuth, user } = useAuth();
    const products = ref([]);
    const categories = ref([]);
    const loading = ref(false);
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      total: 0,
      per_page: 9
    });

    const fetchProducts = async (page = 1) => {
      loading.value = true;
      try {
        const response = await api.get('/home-products', {
          params: { page: page }
        });
        console.log("API RESPONSE:", response.data);
        products.value = response.data.data;
        pagination.value = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          per_page: response.data.per_page
        };
      } catch (error) {
        console.error('Failed to fetch products:', error);
      } finally {
        loading.value = false;
      }
    };

    const displayedPages = computed(() => {
      const pages = [];
      const total = pagination.value.last_page;
      const current = pagination.value.current_page;
      
      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) {
            pages.push(i);
          }
          pages.push('...');
          pages.push(total);
        } else if (current >= total - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = total - 4; i <= total; i++) {
            pages.push(i);
          }
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) {
            pages.push(i);
          }
          pages.push('...');
          pages.push(total);
        }
      }
      
      return pages;
    });

    const changePage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page && page !== '...') {
        fetchProducts(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    };

    const fetchCategories = async () => {
      try {
        const response = await api.get('/categories');
        categories.value = response.data;
      } catch (error) {
        console.error('Failed to fetch categories:', error);
      }
    };


      const addToCart = async (productId) => {
        await initAuth();

        if (!user.value) {
          alert('Please login first');
          router.push('/login');
          return;
        }

        try {
          await api.get('/sanctum/csrf-cookie');
          await api.post('/cart', { product_id: productId, quantity: 1 });

          const countResponse = await api.get('/cart/count');
          window.dispatchEvent(new CustomEvent('cart-count-updated', {
            detail: countResponse.data.count || 0
          }));

          alert('Product added to cart!');
        } catch (error) {
          console.error(error);
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

    onMounted(() => {
      fetchProducts();
      fetchCategories();
    });

    return {
      products,
      categories,
      loading,
      pagination,
      displayedPages,
      changePage,
      addToCart,
      goToProduct,
      truncate,
      handleImageError
    };
  }
}
</script>

<style scoped>
.home {
  padding: 0px;
}

.hero {
  text-align: center;
  background-image: url('/images/logo_backgr.jpg');
  color: white;
  border-radius: 12px;
  margin-bottom: 40px;

  width: 100%;              
  height: 330px;             
  background-size: cover;    
  background-position: center;
  display: flex;
  flex-direction: column;
  justify-content: center;   
  align-items: center;        

}

.categories-section h2{
    color: white;
    padding-top: 39px;
}


.products-section h2 {
  font-size: 1.8rem;
  color: #1a1a2e;
  margin-bottom: 24px;
}

.categories-grid {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.category-card {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px 32px;
  text-decoration: none;
  color: #4b5563;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.category-card:hover {
  border-color: #667eea;
  color: #667eea;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.hero h1 {
  margin: 0 0 10px 0;
  font-size: 2.5rem;
}

.hero p {
  margin: 0;
  font-size: 1.2rem;
  opacity: 0.9;
}

.loading, .no-products {
  text-align: center;
  padding: 60px;
  color: #666;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
  padding: 20px 0;
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
  color: #666;
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

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 30px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.pagination-info {
  color: #6b7280;
  font-size: 0.95rem;
}

.pagination-controls {
  display: flex;
  gap: 8px;
}

.pagination-btn {
  padding: 8px 14px;
  background: white;
  color: #667eea;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
}

.pagination-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.pagination-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>