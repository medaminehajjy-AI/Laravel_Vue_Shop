
<template>
  <div class="product-management">
    <h1>Product Management</h1>
    
    <div class="filters-section">
      <div class="filter-row">
        <div class="filter-group search-filter">
          <input 
            v-model="filters.search" 
            @input="applyFilters"
            type="text" 
            placeholder="Search products..."
            class="search-input"
          />
        </div>
        <div class="filter-group">
          <select v-model="filters.category_id" @change="applyFilters" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div class="filter-group price-filter">
          <input 
            v-model.number="filters.min_price" 
            @input="applyFilters"
            type="number" 
            placeholder="Min Price"
            min="0"
            step="0.01"
            class="price-input"
          />
          <span class="price-separator">-</span>
          <input 
            v-model.number="filters.max_price" 
            @input="applyFilters"
            type="number" 
            placeholder="Max Price"
            min="0"
            step="0.01"
            class="price-input"
          />
        </div>
        <button @click="resetFilters" class="btn-reset">Reset</button>
        <button @click="openForm(null)" class="btn-add">+ Add Product</button>
      </div>
    </div>
    
    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="products.length === 0" class="no-products">
      No products found. Try adjusting your filters.
    </div>
    
    <table v-else class="products-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>
            <img v-if="product.image" :src="product.image" class="product-image" alt="Product" @error="handleImageError" />
            <span v-else class="no-image">-</span>
          </td>
          <td>{{ product.name }}</td>
          <td>{{ truncate(product.description, 50) }}</td>
          <td>${{ product.price }}</td>
          <td>{{ product.stock }}</td>
          <td>
            <div class="button-container">
            <button @click="openForm(product)" class="btn-edit">Edit</button>
            <button @click="deleteProduct(product.id)" class="btn-delete">Delete</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

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

    <div v-if="showForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingProduct ? 'Edit Product' : 'Add Product' }}</h2>
        <form @submit.prevent="saveProduct">
          <div class="form-group">
            <label>Name:</label>
            <input v-model="form.name" type="text" required />
            <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="form.description"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Price:</label>
              <input v-model.number="form.price" type="number" step="0.01" min="0" required />
              <span v-if="errors.price" class="error">{{ errors.price[0] }}</span>
            </div>
            <div class="form-group">
              <label>Stock:</label>
              <input v-model.number="form.stock" type="number" min="0" required />
              <span v-if="errors.stock" class="error">{{ errors.stock[0] }}</span>
            </div>
          </div>
          <div class="form-group">
            <label>Image:</label>
            <input @change="handleImageChange" type="file" accept="image/*" />
            <div v-if="imagePreview" class="image-preview">
              <img :src="imagePreview" alt="Preview" />
            </div>
          </div>
          <div class="form-group">
            <label>Category:</label>
            <select v-model="form.category_id">
              <option :value="null">No Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-save">{{ editingProduct ? 'Update' : 'Save' }}</button>
            <button type="button" @click="closeForm" class="btn-cancel">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api';

export default {
  name: 'ProductManagement',
  data() {
    return {
      products: [],
      categories: [],
      loading: false,
      showForm: false,
      editingProduct: null,
      form: {
        name: '',
        description: '',
        price: 0,
        stock: 0,
        category_id: null,
      },
      imageFile: null,
      imagePreview: null,
      errors: {},
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 10
      },
      filters: {
        search: '',
        category_id: '',
        min_price: '',
        max_price: ''
      }
    }
  },
  computed: {
    displayedPages() {
      const pages = [];
      const total = this.pagination.last_page;
      const current = this.pagination.current_page;
      
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
    }
  },
  created() {
    this.fetchProducts();
    this.fetchCategories();
  },
  methods: {
    async fetchProducts(page = 1) {
      this.loading = true;
      try {
        const params = { page: page };
        
        if (this.filters.search) params.search = this.filters.search;
        if (this.filters.category_id) params.category_id = this.filters.category_id;
        if (this.filters.min_price) params.min_price = this.filters.min_price;
        if (this.filters.max_price) params.max_price = this.filters.max_price;
        
        const response = await api.get('/products', { params });
        this.products = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          per_page: response.data.per_page
        };
      } catch (error) {
        console.error('Failed to fetch products:', error);
      } finally {
        this.loading = false;
      }
    },

    applyFilters() {
      this.fetchProducts(1);
    },

    resetFilters() {
      this.filters = {
        search: '',
        category_id: '',
        min_price: '',
        max_price: ''
      };
      this.fetchProducts(1);
    },
    async fetchCategories() {
      try {
        const response = await api.get('/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Failed to fetch categories:', error);
      }
    },
    openForm(product) {
      this.editingProduct = product;
      if (product) {
        this.form = {
          name: product.name,
          description: product.description || '',
          price: product.price,
          stock: product.stock,
          category_id: product.category_id || null,
        };
        this.imagePreview = product.image || null;
      } else {
        this.form = { name: '', description: '', price: 0, stock: 0, category_id: null };
        this.imagePreview = null;
      }
      this.imageFile = null;
      this.errors = {};
      this.showForm = true;
    },
    handleImageChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.imageFile = file;
        this.imagePreview = URL.createObjectURL(file);
      }
    },
    async saveProduct() {
      this.errors = {};
      
      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('description', this.form.description || '');
      formData.append('price', this.form.price);
      formData.append('stock', this.form.stock);
      formData.append('category_id', this.form.category_id || '');
      
      if (this.imageFile) {
        formData.append('image', this.imageFile);
      }
      
      try {
        if (this.editingProduct) {
          await api.post(`/products/${this.editingProduct.id}?_method=PUT`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        } else {
          await api.post('/products', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
        this.closeForm();
        this.fetchProducts();
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          console.error('Failed to save product:', error);
        }
      }
    },
    async deleteProduct(id) {
      if (!confirm('Are you sure you want to delete this product?')) return;
      
      try {
        await api.delete(`/products/${id}`);
        this.fetchProducts();
      } catch (error) {
        console.error('Failed to delete product:', error);
      }
    },
    closeForm() {
      this.showForm = false;
      this.editingProduct = null;
      this.imageFile = null;
    },
    truncate(text, length) {
      if (!text) return '-';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },
    handleImageError(event) {
      event.target.src = '/placeholder.png';
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.fetchProducts(page);
      }
    },
  }
}
</script>

<style scoped>
.button-container {
    display: flex;
}
.product-management {
  max-width: 1400px;
}

h1 {
  margin-bottom: 20px;
  color: #2c3e50;
}

.filters-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-row {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.filter-group {
  flex: 1;
  min-width: 150px;
}

.search-filter {
  flex: 2;
  min-width: 300px;
}

.price-filter {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 280px;
}

.search-input,
.filter-select,
.price-input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.95rem;
}

.price-input {
  min-width: 100px;
}

.price-separator {
  color: #666;
  font-weight: 500;
}

.btn-reset {
  padding: 10px 20px;
  background: #95a5a6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  white-space: nowrap;
}

.btn-reset:hover {
  background: #7f8c8d;
}

.btn-add {
  padding: 10px 20px;
  background: #27ae60;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  white-space: nowrap;
}

.loading, .no-products {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 8px;
  color: #666;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.products-table th, .products-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.products-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.products-table tr:last-child td {
  border-bottom: none;
}

.product-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.no-image {
  color: #999;
}

.btn-edit, .btn-delete {
  padding: 6px 12px;
  margin-right: 5px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
}

.btn-edit {
  background: #3498db;
  color: white;
}

.btn-delete {
  background: #e74c3c;
  color: white;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 50px;
  overflow-y: auto;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content h2 {
  margin-top: 0;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #34495e;
}

.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group select {
  background: white;
}

.form-group textarea {
  min-height: 80px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.image-preview {
  margin-top: 10px;
}

.image-preview img {
  max-width: 150px;
  max-height: 150px;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.btn-save {
  flex: 1;
  padding: 12px;
  background: #27ae60;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-cancel {
  flex: 1;
  padding: 12px;
  background: #95a5a6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.error {
  color: #e74c3c;
  font-size: 12px;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-info {
  color: #666;
  font-size: 14px;
}

.pagination-controls {
  display: flex;
  gap: 5px;
}

.pagination-btn {
  padding: 8px 12px;
  background: white;
  color: #3498db;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s;
}

.pagination-btn:hover:not(:disabled) {
  background: #3498db;
  color: white;
  border-color: #3498db;
}

.pagination-btn.active {
  background: #3498db;
  color: white;
  border-color: #3498db;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>