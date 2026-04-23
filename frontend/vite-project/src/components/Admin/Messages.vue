<template>
  <div class="messages-page">
    <h1>Messages</h1>
    <p class="clickonmsg">(Click on the message when you finish reading)</p><br>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="messages.length === 0" class="no-messages">
      <p>No messages found.</p>
    </div>

    <table v-else class="messages-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="msg in messages" 
          :key="msg.id"
          :class="{ unread: !msg.is_read }"
          @click="markAsRead(msg)"
        >
          <td>{{ msg.name }}</td>
          <td>{{ msg.email }}</td>
          <td>{{ msg.subject }}</td>
          <td>{{ msg.message }}</td>
          <td>
            <span v-if="msg.is_read">✅ Read</span>
            <span v-else>🔴 New</span>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="pagination-buttons">
      <button @click="fetchMessages(currentPage - 1)" :disabled="currentPage === 1">Prev</button>
      <span>Page {{ currentPage }} of {{ lastPage }}</span>
      <button @click="fetchMessages(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Messages',
  data() {
    return {
      messages: [],
      loading: false,
      currentPage: 1,
      lastPage: 1,
    };
  },
  mounted() {
    this.fetchMessages();
  },
  methods: {
    async fetchMessages(page = 1) {
      this.loading = true;
      try {
        const res = await axios.get(`/api/messages?page=${page}`);
        this.messages = res.data.data;
        this.currentPage = res.data.current_page;
        this.lastPage = res.data.last_page;
      } catch (error) {
        console.error('Failed to fetch messages:', error);
      } finally {
        this.loading = false;
      }
    },

    async markAsRead(msg) {
      if (!msg.is_read) {
        try {
          await axios.post(`/api/messages/${msg.id}/read`);
          msg.is_read = true;
        } catch (error) {
          console.error('Failed to mark message as read:', error);
        }
      }
    }
  }
};
</script>

<style scoped>
.messages-page {
  max-width: 1200px;
}

h1 {
  margin-bottom: 20px;
  color: #2c3e50;
}

.loading, .no-messages {
  text-align: center;
  padding: 60px;
  background: white;
  border-radius: 8px;
  color: #666;
}

.messages-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.messages-table th,
.messages-table td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.messages-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.messages-table tr:last-child td {
  border-bottom: none;
}

.messages-table tr.unread {
  font-weight: 600;
  background-color: #fff9e6; /* highlight unread messages */
  cursor: pointer;
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

.clickonmsg{
  color:#f7752b;
}
</style>