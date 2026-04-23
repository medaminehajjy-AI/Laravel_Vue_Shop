<template>
  <nav class="navbar">
    <div class="nav-container">
      <!-- Brand -->
      <router-link to="/" class="nav-brand">
        <ShoppingBag :size="28" class="brand-icon" />
        <span class="brand-text">Mol-Lmli7Shop</span>
      </router-link>
      
      <!-- Desktop Navigation -->
      <div class="nav-links desktop-only">
        <router-link to="/" class="nav-link">
          <Home :size="18" />
          <span>Home</span>
        </router-link>
        <router-link to="/about" class="nav-link">
          <Info :size="18" />
          <span>About</span>
        </router-link>
        <router-link to="/contact" class="nav-link">
          <Mail :size="18" />
          <span>Contact</span>
        </router-link>
      </div>
      

      <div class="nav-search desktop-only">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search products..."
          class="search-input"
        />
        <div v-if="searchQuery && searchResults.length > 0" class="search-dropdown">
          <div 
            v-for="item in searchResults" 
            :key="item.id" 
            class="search-item"
            @click="goToProduct(item.id)"
          >
            {{ item.name }}
          </div>
        </div>
      </div>

      <!-- Right Side Actions -->
      <div class="nav-actions">
        <!-- Authenticated User Actions -->
        <template v-if="isAuthenticated">
          <!-- Cart Link -->
          <router-link to="/cart" class="action-btn cart-btn">
            <ShoppingCart :size="20" />
            <span v-if="cartCount > 0" class="badge">{{ cartCount }}</span>
          </router-link>

          <!-- Notification Bell (Admin Only) -->
          <button 
            v-if="user?.is_admin" 
            class="action-btn notification-btn" 
            @click="handleNotificationClick"
            title="View Orders"
          >
            <Bell :size="20" />
            <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
          </button>

           <div  v-if="user?.is_admin"  class="notification-mail">
              <router-link to="/admin/messages">
                <Mail :size="22" />
                <span v-if="unreadMessages > 0" class="mail_badge">
                  {{ unreadMessages }}
                </span>
              </router-link>
            </div>

          <!-- User Menu -->
          <div class="dropdown-wrapper" v-click-outside="closeUserMenu">
            <button class="user-button" @click="toggleUserMenu">
              <div class="avatar-wrapper">
                <img 
                  v-if="user?.avatar" 
                  :src="user.avatar" 
                  :alt="user.name" 
                  class="avatar-image"
                />
                <div v-else class="avatar-placeholder">
                  <span>{{ userInitials }}</span>
                </div>
              </div>
              <div class="user-info">
                <span class="user-name">{{ user?.name }}</span>
                <span v-if="user?.is_admin" class="user-role">Admin</span>
              </div>
              <ChevronDown :size="16" :class="{ rotated: showUserMenu }" />
            </button>
            <Transition name="dropdown">
              <div v-if="showUserMenu" class="dropdown user-dropdown">
                <div class="dropdown-header">
                  <div class="dropdown-user-info">
                    <span class="dropdown-user-name">{{ user?.name }}</span>
                    <span class="dropdown-user-email">{{ user?.email }}</span>
                  </div>
                </div>
                <div class="dropdown-body">
                  <router-link v-if="user?.is_admin" to="/admin/dashboard" class="dropdown-item" @click="closeUserMenu">
                    <LayoutDashboard :size="18" />
                    <span>Dashboard</span>
                  </router-link>
                  <router-link to="/cart" class="dropdown-item" @click="closeUserMenu">
                    <ShoppingBag :size="18" />
                    <span>My Orders</span>
                  </router-link>
                </div>
                <div class="dropdown-footer">
                  <button class="dropdown-item logout-item" @click="handleLogout">
                    <LogOut :size="18" />
                    <span>Sign Out</span>
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </template>
        
        <!-- Guest Actions -->
        <template v-else>
          <router-link to="/login" class="action-btn login-btn">
            <LogIn :size="18" />
            <span class="action-label">Login</span>
          </router-link>
          <router-link to="/register" class="action-btn register-btn">
            <UserPlus :size="18" />
            <span class="action-label">Register</span>
          </router-link>
        </template>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-btn mobile-only" @click="toggleMobileMenu">
          <Menu v-if="!showMobileMenu" :size="24" />
          <X v-else :size="24" />
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition name="mobile-menu">
      <div v-if="showMobileMenu" class="mobile-menu">
        <router-link to="/" class="mobile-link" @click="closeMobileMenu">
          <Home :size="20" />
          <span>Home</span>
        </router-link>
        <router-link to="/about" class="mobile-link" @click="closeMobileMenu">
          <Info :size="20" />
          <span>About</span>
        </router-link>
        <router-link to="/contact" class="mobile-link" @click="closeMobileMenu">
          <Mail :size="20" />
          <span>Contact</span>
        </router-link>
        <template v-if="isAuthenticated">
          <router-link to="/cart" class="mobile-link" @click="closeMobileMenu">
            <ShoppingCart :size="20" />
            <span>Cart</span>
            <span v-if="cartCount > 0" class="badge">{{ cartCount }}</span>
          </router-link>
          <router-link v-if="user?.is_admin" to="/admin/dashboard" class="mobile-link" @click="closeMobileMenu">
            <LayoutDashboard :size="20" />
            <span>Dashboard</span>
          </router-link>
          <button class="mobile-link logout-mobile" @click="handleLogout">
            <LogOut :size="20" />
            <span>Logout</span>
          </button>
        </template>
        <template v-else>
          <router-link to="/login" class="mobile-link" @click="closeMobileMenu">
            <LogIn :size="20" />
            <span>Login</span>
          </router-link>
          <router-link to="/register" class="mobile-link" @click="closeMobileMenu">
            <UserPlus :size="20" />
            <span>Register</span>
          </router-link>
        </template>
      </div>
    </Transition>
  </nav>
</template>

<script>
import axios from 'axios';
import { watch } from 'vue';
import { ref, computed, onMounted, watchEffect } from 'vue';
import { useAuth } from '../composables/useAuth';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { 
  ShoppingBag, Home, ShoppingCart, User, ChevronDown, 
  LogIn, UserPlus, LogOut, LayoutDashboard, Bell, 
  Info, Mail, Menu, X, ArrowRight, CheckCircle
} from 'lucide-vue-next';

export default {
  name: 'Navbar',
  components: { 
    ShoppingBag, Home, ShoppingCart, User, ChevronDown, 
    LogIn, UserPlus, LogOut, LayoutDashboard, Bell, 
    Info, Mail, Menu, X, ArrowRight, CheckCircle
  },
  directives: {
    clickOutside: {
      mounted(el, binding) {
        el._clickOutside = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value();
          }
        };
        document.addEventListener('click', el._clickOutside);
      },
      unmounted(el) {
        document.removeEventListener('click', el._clickOutside);
      }
    }
  },
  setup() {
    const { user, isAuthenticated, logout, fetchUser } = useAuth();
    const router = useRouter();
    const cartCount = ref(0);
    const unreadMessages = ref(0);
    const showUserMenu = ref(false);
    const showMobileMenu = ref(false);
    const unreadCount = ref(0);
    const searchQuery = ref('');
    const searchResults = ref([]);
    const searching = ref(false);
    let searchTimeout = null;
    
    const fetchCartCount = async () => {
      if (!user.value) {
        cartCount.value = 0;
        return;
      }
      try {
        const response = await api.get('/api/cart/count');
        cartCount.value = response.data.count || 0;
      } catch (e) {
        cartCount.value = 0;
      }
    };
    
  
    
    onMounted(async () => {
      await fetchUser();

      if (user.value) {
        fetchCartCount();
      }

      fetchUnreadOrders();
      fetchUnreadMessages();

      // auto refresh
      setInterval(fetchUnreadOrders, 5000);
      setInterval(fetchUnreadMessages, 10000);

      // listeners
      window.addEventListener('orders-updated', fetchUnreadOrders);
      window.addEventListener('cart-count-updated', (e) => {
        cartCount.value = e.detail;
      });
    });

    // watch for the search bar
    watch(searchQuery, (newVal) => {
      if (searchTimeout) clearTimeout(searchTimeout);

      if (!newVal.trim()) {
        searchResults.value = [];
        return;
      }

  searching.value = true;
  
  // debounce to avoid too many requests
  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get('/api/products/search', {
        params: { q: newVal }
      });
      searchResults.value = res.data; // assuming API returns array of products
        } catch (err) {
          console.error(err);
          searchResults.value = [];
        } finally {
          searching.value = false;
        }
      }, 300); // 300ms delay
    });
    // this follow to the search bar
    const goToProduct = (id) => {
      searchQuery.value = '';       // clear search
      searchResults.value = [];     // close dropdown
      router.push(`/product/${id}`);
    };

    const fetchUnreadOrders = async () => {
      try {
        const res = await api.get('/api/admin/orders/unread-count');
        unreadCount.value = res.data.count || 0;
      } catch (e) {
        unreadCount.value = 0;
      }
    };
    const fetchUnreadMessages = async () => {
        try {
          const res = await axios.get('/api/messages/unread-count');
          unreadMessages.value = res.data;
        } catch (err) {
          console.error(err);
        }
      };

    const userInitials = computed(() => {
      if (!user.value?.name) return '?';
      const names = user.value.name.split(' ');
      if (names.length >= 2) {
        return (names[0][0] + names[1][0]).toUpperCase();
      }
      return names[0][0].toUpperCase();
    });
    
    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value;
    };
    
    const closeUserMenu = () => {
      showUserMenu.value = false;
    };

    const handleNotificationClick = async () => {
      try {
        await api.post('/api/admin/orders/mark-as-read');
        unreadCount.value = 0;
      } catch (error) {
        console.error('Failed to mark orders as read:', error);
      }
      router.push('/admin/orders');
    };

    const toggleMobileMenu = () => {
      showMobileMenu.value = !showMobileMenu.value;
    };

    const closeMobileMenu = () => {
      showMobileMenu.value = false;
    };
    
    const updateCartCount = (event) => {
      cartCount.value = event.detail;
    };
    
    const handleLogout = async () => {
      closeUserMenu();
      closeMobileMenu();
      cartCount.value = 0;
      unreadCount.value = 0;
      await logout();
      router.push('/login');
    };
    
    // Listen for cart updates from other components
    window.addEventListener('cart-count-updated', updateCartCount);
    
    
    return {
      user,
      isAuthenticated,
      cartCount,
      showUserMenu,
      showMobileMenu,
      unreadCount,
      unreadMessages,
      userInitials,
      toggleUserMenu,
      closeUserMenu,
      handleNotificationClick,
      toggleMobileMenu,
      closeMobileMenu,
      handleLogout,
      searchQuery,
      searchResults,
      searching,
      goToProduct
    };
  },
  
}
</script>

<style scoped>
/*Search bar*/ 
/* Search Container */
.nav-search {
  position: relative;
  width: 320px;
}

/* Input */
.search-input {
  width: 100%;
  padding: 10px 14px 10px 40px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  outline: none;
  font-size: 0.9rem;
  background: #f9fafb;
  transition: all 0.25s ease;
}

/* Focus effect */
.search-input:focus {
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

/* Search Icon */
.nav-search::before {
  content: "🔍";
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #9ca3af;
}

/* Dropdown */
.search-dropdown {
  position: absolute;
  top: 110%;
  left: 0;
  width: 100%;
  background: white;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  max-height: 320px;
  overflow-y: auto;
  z-index: 999;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  animation: fadeIn 0.2s ease;
}

/* Each item */
.search-item {
  padding: 10px 14px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #374151;
  transition: all 0.2s;
}

/* Hover */
.search-item:hover {
  background: #f3f4f6;
  color: #111827;
}

/* Optional: highlight first result */
.search-item:first-child {
  border-top-left-radius: 14px;
  border-top-right-radius: 14px;
}

/* Optional: last item */
.search-item:last-child {
  border-bottom-left-radius: 14px;
  border-bottom-right-radius: 14px;
}

/* Smooth animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
/* Base Navbar */
.navbar {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.nav-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 72px;
}

/* Brand */
.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: #1a1a2e;
  font-weight: 700;
  font-size: 1.4rem;
  transition: color 0.2s;
}

.nav-brand:hover {
  color: #667eea;
}

.brand-icon {
  color: #667eea;
}

/* Desktop Navigation Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 4px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  color: #4b5563;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.nav-link:hover {
  background: #f3f4f6;
  color: #1a1a2e;
}

.nav-link.router-link-active {
  color: #667eea;
  background: #f0f4ff;
}

/* Right Side Actions */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Action Buttons */
.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border: none;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  position: relative;
  color: #4b5563;
  background: transparent;
}

.action-btn:hover {
  background: #f3f4f6;
}

.cart-btn {
  color: #4b5563;
}

.badge {
  position: absolute;
  top: -2px;
  right: 11px;
  background: #ef4444;
  color: white;
  font-size: 0.65rem;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 6px;
  text-align: center;
}

/* Notification Button */
.notification-btn {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: 2px;
  right: 8px;
  background: #ef4444;
  color: white;
  font-size: 0.6rem;
  font-weight: 700;
  padding: 2px 5px;
  border-radius: 10px;
  min-width: 7px;
  text-align: center;
  border: 2px solid white;
}

/* Dropdown Wrapper */
.dropdown-wrapper {
  position: relative;
}

/* User Button */
.user-button {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 12px 6px 6px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.user-button:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

.avatar-wrapper {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  overflow: hidden;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.user-name {
  font-weight: 600;
  color: #1a1a2e;
  font-size: 0.9rem;
}

.user-role {
  font-size: 0.7rem;
  color: #667eea;
  font-weight: 500;
}

.user-button svg {
  color: #6b7280;
  transition: transform 0.2s;
}

.user-button svg.rotated {
  transform: rotate(180deg);
}

/* Dropdown */
.dropdown {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  z-index: 1000;
}

.notification-dropdown {
  width: 360px;
}

.user-dropdown {
  width: 280px;
}

.dropdown-header {
  padding: 16px 20px;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dropdown-title {
  font-weight: 600;
  color: #1a1a2e;
  font-size: 1rem;
}

.dropdown-count {
  font-size: 0.75rem;
  color: #667eea;
  font-weight: 500;
  background: #f0f4ff;
  padding: 4px 10px;
  border-radius: 20px;
}

.dropdown-user-info {
  display: flex;
  flex-direction: column;
}

.dropdown-user-name {
  font-weight: 600;
  color: #1a1a2e;
}

.dropdown-user-email {
  font-size: 0.8rem;
  color: #9ca3af;
}

.dropdown-body {
  padding: 8px;
  max-height: 320px;
  overflow-y: auto;
}

.dropdown-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 20px;
  color: #9ca3af;
}

.empty-icon {
  color: #d1d5db;
  margin-bottom: 12px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 16px;
  color: #4b5563;
  text-decoration: none;
  border: none;
  background: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
}

.dropdown-item:hover {
  background: #f3f4f6;
  color: #1a1a2e;
}

.dropdown-item svg {
  color: #6b7280;
}

.dropdown-item:hover svg {
  color: #667eea;
}

.dropdown-footer {
  padding: 8px;
  border-top: 1px solid #f3f4f6;
}

.view-all {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 12px;
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.85rem;
  border-radius: 10px;
  transition: all 0.2s;
}

.view-all:hover {
  background: #f0f4ff;
}

.logout-item {
  color: #ef4444;
}

.logout-item:hover {
  background: #fef2f2;
  color: #dc2626;
}

.logout-item svg {
  color: #ef4444;
}

/* Notification List */
.notification-list {
  display: flex;
  flex-direction: column;
}

.notification-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-radius: 10px;
  transition: all 0.2s;
  text-decoration: none;
}

.notification-item:hover {
  background: #f3f4f6;
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.order-id {
  font-weight: 600;
  color: #1a1a2e;
  font-size: 0.9rem;
}

.order-customer {
  font-size: 0.8rem;
  color: #9ca3af;
}

.order-details {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.order-amount {
  font-weight: 600;
  color: #1a1a2e;
}

.order-status {
  font-size: 0.65rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  text-transform: uppercase;
}

.order-status.pending {
  background: #fef3c7;
  color: #d97706;
}

.order-status.processing {
  background: #dbeafe;
  color: #2563eb;
}

.order-status.shipped,
.order-status.delivered {
  background: #d1fae5;
  color: #059669;
}

.order-status.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

/* Login/Register Buttons */
.login-btn {
  color: #667eea;
}

.login-btn:hover {
  background: #f0f4ff;
}

.register-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.register-btn:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%);
}

.action-label {
  display: inline;
}

/* Mobile Menu Button */
.mobile-menu-btn {
  display: none;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  background: transparent;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  color: #4b5563;
  transition: all 0.2s;
}

.mobile-menu-btn:hover {
  background: #f3f4f6;
}

/* Mobile Menu */
.mobile-menu {
  display: none;
  flex-direction: column;
  padding: 16px;
  background: white;
  border-top: 1px solid #e5e7eb;
}

.mobile-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  color: #4b5563;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.95rem;
  border: none;
  background: none;
  width: 100%;
  cursor: pointer;
  transition: all 0.2s;
}

.mobile-link:hover {
  background: #f3f4f6;
  color: #1a1a2e;
}

.mobile-link:hover {
  background: #f3f4f6;
  color: #1a1a2e;
}

.mobile-link svg {
  color: #6b7280;
}

.logout-mobile {
  color: #ef4444;
  margin-top: 8px;
  border-top: 1px solid #e5e7eb;
  padding-top: 20px;
}

.logout-mobile svg {
  color: #ef4444;
}

/* Dropdown Animations */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.25s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

/* Mobile Menu Animation */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 1024px) {
  .desktop-only {
    display: none;
  }
  
  .action-label {
    display: none;
  }
  
  .user-info {
    display: none;
  }
}

@media (max-width: 768px) {
  .nav-container {
    padding: 0 16px;
    height: 64px;
  }
  
  .mobile-only {
    display: flex;
  }
  
  .mobile-menu {
    display: flex;
  }
  
  .brand-text {
    display: none;
  }
  
  .notification-dropdown {
    position: fixed;
    top: 72px;
    left: 16px;
    right: 16px;
    width: auto;
  }
  
  .user-dropdown {
    position: fixed;
    top: 72px;
    right: 16px;
    left: auto;
    width: calc(100% - 32px);
    max-width: 300px;
  }
}

.notification-mail {
  position: relative;
  cursor: pointer;
}

.mail_badge {
  position: absolute;
  top: -7px;
  right: -6px;
  background: red;
  color: white;
  font-size: 12px;
  padding: 3px 6px;
  border-radius: 50%;
}

@media (min-width: 769px) {
  .mobile-menu {
    display: none !important;
  }
}
</style>
