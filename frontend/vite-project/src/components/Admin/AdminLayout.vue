  <template>
    <div v-if="loading" class="loading-screen">
      Loading...
    </div>
    
    <div v-else-if="!isAuthenticated || !isAdmin" class="access-denied">
      <h2>Access Denied</h2>
      <p>You don't have permission to access this page.</p>
      <button @click="goHome">Go to Home</button>
    </div>
    
    <div v-else class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h3>Admin Panel</h3>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/admin/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span>Dashboard</span>
        </router-link>
        <router-link to="/admin/products" class="nav-item">
          <span class="nav-icon">📦</span>
          <span>Products</span>
        </router-link>
        <router-link to="/admin/orders" class="nav-item">
          <span class="nav-icon">🛒</span>
          <span>Orders</span>
        </router-link>
        <router-link to="/admin/categories" class="nav-item">
          <span class="nav-icon">📁</span>
          <span>Categories</span>
        </router-link>
        <router-link to="/admin/messages" class="nav-item">
          <span class="nav-icon">✉️</span>
          <span>Messages</span>
        </router-link>
      </nav>
      <div class="sidebar-footer">
        <div class="user-info">
          <span>{{ user?.name }}</span>
          <span class="badge">Admin</span>
        </div>
        <button @click="handleLogout" class="btn-logout">Logout</button>
      </div>
    </aside>
    
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

  <script>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

export default {
  name: 'AdminLayout',
  setup() {
    const router = useRouter();
    const { user, isAuthenticated, isAdmin, logout } = useAuth();
    
    const loading = ref(false);
    
    watch(() => user.value, (newUser) => {
      if (!newUser) {
        router.push('/login');
      } else if (newUser && !newUser.is_admin) {
        router.push('/');
      }
    });
    
    async function handleLogout() {
      await logout();
      router.push('/login');
    }
    
    function goHome() {
      router.push('/');
    }
    
    return {
      user,
      isAuthenticated,
      isAdmin,
      loading,
      handleLogout,
      goHome
    };
  }
}
</script>

<style scoped>
.loading-screen {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  font-size: 1.5rem;
  color: #666;
}

.access-denied {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100vh;
  text-align: center;
}

.access-denied h2 {
  color: #e74c3c;
  margin-bottom: 10px;
}

.access-denied button {
  margin-top: 20px;
  padding: 10px 30px;
  background: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f3f4f6;
}

.sidebar {
  width: 250px;
  background: #1c155b;
  color: white;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #5568d385;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 1.2rem;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #ecf0f1;
  text-decoration: none;
  transition: background 0.3s;
}

.nav-item:hover,
.nav-item.router-link-active {
  background: #5568d385;
}

.nav-icon {
  margin-right: 10px;
  font-size: 1.2rem;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid #5568d385;
}

.user-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  font-size: 0.9rem;
}

.badge {
  background: #27ae60;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
}

.btn-logout {
  width: 100%;
  padding: 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-logout:hover {
  background: #c0392b;
}

.main-content {
  flex: 1;
  padding: 30px;
  overflow-y: auto;
  background: #f3f4f6;
}


</style>
