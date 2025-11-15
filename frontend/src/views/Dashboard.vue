<template>
  <div>
    <h2>Dashboard</h2>
    
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Today's Attendance</h3>
        <div class="stat-value">{{ statistics.present || 0 }}</div>
        <div class="stat-label">Present</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ statistics.absent || 0 }}</div>
        <div class="stat-label">Absent</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ statistics.late || 0 }}</div>
        <div class="stat-label">Late</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ statistics.attendance_percentage || 0 }}%</div>
        <div class="stat-label">Attendance Rate</div>
      </div>
    </div>

    <div class="card">
      <h3>Monthly Attendance Chart</h3>
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Chart, registerables } from 'chart.js'
import api from '../services/api'

Chart.register(...registerables)

const statistics = ref({})
const chartCanvas = ref(null)
let chartInstance = null

const loadStatistics = async () => {
  try {
    const response = await api.get('/attendances/statistics/today')
    statistics.value = response.data.data
  } catch (error) {
    console.error('Error loading statistics:', error)
  }
}

const loadMonthlyData = async () => {
  try {
    const currentMonth = new Date().toISOString().slice(0, 7)
    const response = await api.get('/attendances/reports/monthly', {
      params: { month: currentMonth }
    })
    
    if (chartCanvas.value && response.data.data.length > 0) {
      const data = response.data.data
      const labels = data.map(d => d.name)
      const presentData = data.map(d => d.present_days)
      const absentData = data.map(d => d.absent_days)
      
      if (chartInstance) {
        chartInstance.destroy()
      }
      
      chartInstance = new Chart(chartCanvas.value, {
        type: 'bar',
        data: {
          labels: labels.slice(0, 10), // Show first 10 students
          datasets: [
            {
              label: 'Present',
              data: presentData.slice(0, 10),
              backgroundColor: '#2ecc71',
            },
            {
              label: 'Absent',
              data: absentData.slice(0, 10),
              backgroundColor: '#e74c3c',
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      })
    }
  } catch (error) {
    console.error('Error loading monthly data:', error)
  }
}

onMounted(() => {
  loadStatistics()
  loadMonthlyData()
})

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  text-align: center;
}

.stat-card h3 {
  margin-bottom: 1rem;
  color: #2c3e50;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: bold;
  color: #3498db;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}
</style>

