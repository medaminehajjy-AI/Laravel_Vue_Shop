<template>
  <div class="dashboard">
    <h1>Statistics</h1>
    
    <div v-if="loading" class="loading">
      <Loader2 class="animate-spin" :size="40" />
    </div>
    
    <div v-else class="stats-grid">
      <div class="stat-card clickable" @click="$router.push('/admin/products')">
        <div class="stat-icon-wrapper products">
          <Package :size="32" />
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.products_count }}</span>
          <span class="stat-label">Products</span>
        </div>
      </div>
      
      <div class="stat-card clickable" @click="$router.push('/admin/orders')">
        <div class="stat-icon-wrapper orders">
          <ShoppingCart :size="32" />
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.orders_count }}</span>
          <span class="stat-label">Orders</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon-wrapper users">
          <Users :size="32" />
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.users_count }}</span>
          <span class="stat-label">Users</span>
        </div>
      </div>
      
      <div class="stat-card revenue">
        <div class="stat-icon-wrapper money">
          <DollarSign :size="32" />
        </div>
        <div class="stat-info">
          <span class="stat-number">${{ formatNumber(stats.revenue) }}</span>
          <span class="stat-label">Revenue</span>
        </div>
      </div>
    </div>
         <br>
         <div class="dashboard-bottom">
          <RevenueChart />
          <div class="recent-orders">
          <h2>Recent Orders</h2>

          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="order in recentOrders" :key="order.id">
                <td>{{ order.user }}</td>
                <td>${{ formatNumber(order.total) }}</td>
                <td>
                  <span :class="'status ' + order.status">
                    {{ order.status }}
                  </span>
                </td>
                <td>{{ order.date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        </div>
            <div class="dashboard-row">
              <!--This is Bugged(under maintenance)-->
              <!--<TopProductsChart />-->
            </div>

  </div>
</template>

<script>
import TopProductsChart from './TopProductsChart.vue';
import RevenueChart from './RevenueChart.vue';
import { Package, ShoppingCart, Users, DollarSign, Loader2 } from 'lucide-vue-next';
import api from '../../services/api';
import { useAuth } from '../../composables/useAuth';

export default {
  name: 'Dashboard',
  components: { Package, ShoppingCart, Users, DollarSign, Loader2 ,RevenueChart ,
  TopProductsChart},
  data() {
    return {
      stats: {
        products_count: 0,
        users_count: 0,
        orders_count: 0,
        revenue: 0
      },
      recentOrders: [],
      loading: false
    }
  },


  setup() {
    const { user } = useAuth();
    return { user };
  },
  created() {
    this.fetchStats();
    this.fetchRecentOrders();
  },
  methods: {
    async fetchStats() {
      this.loading = true;
      try {
        const response = await api.get('/admin/stats');
        this.stats = response.data;
      } catch (error) {
        console.error('Failed to fetch stats:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchRecentOrders() {
      try {
        const response = await api.get('/admin/recent-orders');
        this.recentOrders = response.data;
      } catch (error) {
        console.error('Failed to fetch recent orders:', error);
      }
    },
    formatNumber(num) {
      return num ? Number(num).toLocaleString() : '0';
    }
  }
}
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
}

h1 {
  margin-bottom: 30px;
  color: #1a1a2e;
  font-weight: 600;
}

.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px;
  color: #6b7280;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  margin-bottom: 40px;
}

@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}

.stat-card {
  background: white;
  padding: 28px;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.stat-card.clickable {
  cursor: pointer;
}

.stat-card.clickable:hover {
  border-color: #667eea;
}

.stat-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon-wrapper.products {
  background: linear-gradient(135deg, #667eea22 0%, #764ba222 100%);
  color: #667eea;
}

.stat-icon-wrapper.orders {
  background: linear-gradient(135deg, #f093fb22 0%, #f5576c22 100%);
  color: #f5576c;
}

.stat-icon-wrapper.users {
  background: linear-gradient(135deg, #4facfe22 0%, #00f2fe22 100%);
  color: #00f2fe;
}

.stat-icon-wrapper.money {
  background: linear-gradient(135deg, #43e97b22 0%, #38f9d722 100%);
  color: #43e97b;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a2e;
  line-height: 1.2;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 4px;
}

.stat-card.revenue {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.stat-card.revenue .stat-icon-wrapper {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.stat-card.revenue .stat-number {
  color: white;
}

.stat-card.revenue .stat-label {
  color: rgba(255, 255, 255, 0.8);
}

.welcome-section {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
}

.welcome-section h2 {
  margin: 0 0 8px 0;
  color: #1a1a2e;
  font-weight: 600;
}

.welcome-section p {
  margin: 0;
  color: #6b7280;
}


.dashboard-bottom {
  display: flex;
  gap: 24px;
  margin-top: 30px;
  align-items: stretch;
}

.dashboard-bottom > * {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.chart-container {
  background: white;
  padding: 24px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  height: 100%;
  min-height: 320px;
  display: flex;
  flex-direction: column;
}

.chart-container canvas {
  width: 100% !important;
  height: 100% !important;
}

.recent-orders {
  background: white;
  padding: 24px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.recent-orders h2 {
  margin-bottom: 16px;
  font-size: 18px;
  font-weight: 600;
  color: #1a1a2e;
}

.recent-orders table {
  width: 100%;
  border-collapse: collapse;
  flex: 1;
}

.recent-orders th,
.recent-orders td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.recent-orders th {
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

.status {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  text-transform: capitalize;
  display: inline-block;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.paid {
  background: #d4edda;
  color: #155724;
}

.status.shipped {
  background: #d4edda;
  color: #155724;
}
.status.delivered {
  background: #d4edda;
  color: #155724;
}

@media (max-width: 900px) {
  .dashboard-bottom {
    flex-direction: column;
  }
}

.dashboard-row {
  margin-top: 30px;
  display: flex;
  gap: 24px;
}

/* make chart full width */
.dashboard-row > * {
  flex: 1;
}
@media (max-width: 900px) {
  .dashboard-row {
    flex-direction: column;
  }
}
</style>
