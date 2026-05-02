<template>
  <div class="orders-page">
    <h1>Order Management</h1>
    
    <div v-if="loading" class="loading">Loading...</div>
    
    <div v-else-if="orders.length === 0" class="no-orders">
      <p>No orders found.</p>
    </div>
    
    <table v-else class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>#{{ order.id }}</td>
          <td>{{ order.user?.name || 'Unknown' }}</td>
          <td>${{ order.total_price }}</td>
          <td>
            <select 
              :value="order.status" 
              @change="updateStatus(order.id, $event.target.value)"
              class="status-select"
              :class="order.status"
            >
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
            </select>
          </td>
          <td>{{ formatDate(order.created_at) }}</td>
          <td>
            <button @click="viewDetails(order)" class="btn-view">View</button>
          </td>
        </tr>
      </tbody>
    </table>
    
    <div class="pagination-buttons" style="margin-top: 15px; text-align: center;">
      <button @click="fetchOrders(currentPage - 1)" :disabled="currentPage === 1">Prev</button>
      <span style="margin: 0 10px;">Page {{ currentPage }} of {{ lastPage }}</span>
      <button @click="fetchOrders(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
    </div>

    <div v-if="showDetails" class="modal">
      <div class="modal-content">
        <h2>Order #{{ selectedOrder?.id }}</h2> <hr>
        <div class="order-info">
          <p><strong>Customer:</strong> {{ selectedOrder?.user?.name }}</p>
          <p><strong>Phone:</strong> {{ selectedOrder?.phone }}</p>
          <p><strong>Email:</strong> {{ selectedOrder?.user?.email }}</p>
          <hr><p><strong>Shipping Address:</strong> {{ selectedOrder?.shipping_address }}</p><hr>
          <p><strong>Status:</strong> {{ selectedOrder?.status }}</p>
          <p class="total-price">[<strong>Total:</strong> ${{ selectedOrder?.total_price }}]</p>
          <p><strong>Date:</strong> {{ formatDate(selectedOrder?.created_at) }}</p>
        </div><hr>
        <button @click="printFacture" class="btn-print">Print</button>
        <button @click="showDetails = false" class="btn-close">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api';

export default {
  name: 'Orders',
  data() {
    return {
      orders: [],
      loading: false,
      showDetails: false,
      selectedOrder: null,
      currentPage: 1,
      lastPage: 1,
    }
  },
  created() {
    this.fetchOrders();
    this.markAsRead();
  },
  methods: {
    
    async fetchOrders(page = 1) {
      this.loading = true;
      try {
        const response = await api.get(`/admin/orders?page=${page}`);
        this.orders = response.data.data;          // actual orders
        this.currentPage = response.data.current_page;
        this.lastPage = response.data.last_page;
      } catch (error) {
        console.error('Failed to fetch orders:', error);
      } finally {
        this.loading = false;
      }
    },
    async updateStatus(orderId, newStatus) {
      try {
        await api.put(`/admin/orders/${orderId}`, { status: newStatus });
        const order = this.orders.find(o => o.id === orderId);
        if (order) {
          order.status = newStatus;
        }
      } catch (error) {
        console.error('Failed to update status:', error);
        alert('Failed to update status');
      }
    },
    viewDetails(order) {
      this.selectedOrder = order;
      this.showDetails = true;
    },
    printFacture() {

      const printContent = document.querySelector('.modal-content').innerHTML;
      const originalContent = document.body.innerHTML;

      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = originalContent;
      window.location.reload(); 
    },
    async markAsRead() {
      try {
        await api.post('/admin/orders/mark-as-read');
      } catch (error) {
        console.error('Failed to mark orders as read:', error);
      }
    },
    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  }
}
</script>

<style scoped>
.btn-print {
    width: 100%;
    margin-bottom: 10px;
    padding: 12px;
    background-color: #3498db; /* nice blue */
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-print:hover {
    background-color: #2980b9; /* darker blue on hover */
}
.orders-page {
  max-width: 1200px;
}

h1 {
  margin-bottom: 30px;
  color: #2c3e50;
}

.loading, .no-orders {
  text-align: center;
  padding: 60px;
  background: white;
  border-radius: 8px;
  color: #666;
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.orders-table th,
.orders-table td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.orders-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.orders-table tr:last-child td {
  border-bottom: none;
}

.status-select {
  padding: 6px 12px;
  border-radius: 4px;
  border: 1px solid #ddd;
  cursor: pointer;
  font-size: 0.85rem;
}

.status-select.pending {
  background: #fff3cd;
  color: #856404;
}

.status-select.paid {
  background: #d4edda;
  color: #155724;
}

.status-select.shipped {
  background: #cce5ff;
  color: #004085;
}

.status-select.delivered {
  background: #d1e7dd;
  color: #0f5132;
}

.btn-view {
  padding: 6px 15px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
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
  align-items: center;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 400px;
  max-width: 90%;
}

.modal-content h2 {
  margin: 0 0 20px 0;
  color: #2c3e50;
}

.order-info {
  margin-bottom: 20px;
}

.order-info p {
  margin: 10px 0;
  color: #333;
}

.btn-close {
  width: 100%;
  padding: 12px;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.btn-close:hover {
    background-color: #5b6167; 
}
.total-price {
    text-align: center;   
    font-weight: bold;    
    font-size: 1.2em;     
    margin: 10px 0;      
}



.pagination-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 15px;
    gap: 10px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.pagination-buttons button {
    background-color: #1c155ba3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s, transform 0.1s;
}

.pagination-buttons button:hover:not(:disabled) {
    background-color: #1c155b;
    transform: scale(1.05);
}

.pagination-buttons button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.pagination-buttons span {
    font-weight: 500;
    color: #333;
}
</style>
