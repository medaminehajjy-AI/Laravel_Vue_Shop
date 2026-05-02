<template>
  <div class="categories-management">
    <h1>Category Management</h1>
    
    <div class="actions">
      <button @click="openForm(null)" class="btn-add">+ Add Category</button>
    </div>
    
    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="categories.length === 0" class="no-categories">
      No categories found. Add your first category!
    </div>
    
    <table v-else class="categories-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Products</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in categories" :key="category.id">
          <td>{{ category.id }}</td>
          <td>{{ category.name }}</td>
          <td>{{ category.slug }}</td>
          <td>{{ category.products_count || 0 }}</td>
          <td>
            <button @click="openForm(category)" class="btn-edit">Edit</button>
            <button @click="deleteCategory(category.id)" class="btn-delete">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingCategory ? 'Edit Category' : 'Add Category' }}</h2>
        <form @submit.prevent="saveCategory">
          <div class="form-group">
            <label>Name:</label>
            <input v-model="form.name" type="text" required />
            <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-save">{{ editingCategory ? 'Update' : 'Save' }}</button>
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
  name: 'Categories',
  data() {
    return {
      categories: [],
      loading: false,
      showForm: false,
      editingCategory: null,
      form: {
        name: '',
      },
      errors: {}
    }
  },
  created() {
    this.fetchCategories();
  },
  methods: {
    async fetchCategories() {
      this.loading = true;
      try {
        const response = await api.get('/categories');
        this.categories = response.data;
        for (let cat of this.categories) {
          try {
            const prodResponse = await api.get('/products');
            cat.products_count = prodResponse.data.data.filter(p => p.category_id === cat.id).length;
          } catch {
            cat.products_count = 0;
          }
        }
      } catch (error) {
        console.error('Failed to fetch categories:', error);
      } finally {
        this.loading = false;
      }
    },
    openForm(category) {
      this.editingCategory = category;
      if (category) {
        this.form.name = category.name;
      } else {
        this.form.name = '';
      }
      this.errors = {};
      this.showForm = true;
    },
    async saveCategory() {
      this.errors = {};
      
      try {
        if (this.editingCategory) {
          await api.put(`/categories/${this.editingCategory.id}`, this.form);
        } else {
          await api.post('/categories', this.form);
        }
        this.closeForm();
        this.fetchCategories();
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          console.error('Failed to save category:', error);
        }
      }
    },
    async deleteCategory(id) {
      if (!confirm('Are you sure you want to delete this category?')) return;
      
      try {
        await api.delete(`/categories/${id}`);
        this.fetchCategories();
      } catch (error) {
        console.error('Failed to delete category:', error);
        alert('Failed to delete category');
      }
    },
    closeForm() {
      this.showForm = false;
      this.editingCategory = null;
    }
  }
}
</script>

<style scoped>
.categories-management {
  max-width: 1000px;
}

h1 {
  margin-bottom: 20px;
  color: #2c3e50;
}

.actions {
  margin-bottom: 20px;
}

.btn-add {
  padding: 10px 20px;
  background: #27ae60;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.loading, .no-categories {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 8px;
  color: #666;
}

.categories-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.categories-table th, .categories-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.categories-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.categories-table tr:last-child td {
  border-bottom: none;
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
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 400px;
  max-width: 90%;
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

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
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
</style>
