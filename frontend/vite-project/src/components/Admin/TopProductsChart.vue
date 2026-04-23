<template>
  <div class="chart-container">
    <p>Top Selling Products</p>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import api from '../../services/api'
import { useAuth } from '../../composables/useAuth'
import { watch } from 'vue'

const { user } = useAuth()

const chartCanvas = ref(null)



watch(user, async (newUser) => {
  if (!newUser || !newUser.is_admin) return

  
  if (!chartCanvas.value) return

  try {
    const response = await api.get('/api/admin/top-products')

    const labels = response.data.map(item => item.name)
    const values = response.data.map(item => Number(item.total))
    
    let chartInstance = null
    if (chartInstance) {
        chartInstance.destroy()
        }
    chartInstance = new Chart(chartCanvas.value, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [
          {
            label: 'Top Products',
            data: values,
            borderWidth: 1
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    })

  } catch (e) {
    console.log('Top products error', e)
  }
})
</script>