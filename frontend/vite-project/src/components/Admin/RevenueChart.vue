<template>
  <div class="chart-container">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import api from '../../services/api'

const chartCanvas = ref(null)

onMounted(async () => {
  try {
    const response = await api.get('/admin/revenue-chart')

    const data = response.data
    const labels = data.map(item => item.date)
    const totals = data.map(item => item.total)

    new Chart(chartCanvas.value, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Monthly Revenue',
            data: totals,
            borderWidth: 2,
            tension: 0.3
          }
        ]
      }
    })
  } catch (error) {
    console.error('Chart API error:', error.response?.data || error.message)
  }
})
</script>

<style>
.chart-container {
  background: white;
  padding: 20px;
  border-radius: 10px;
}
</style>