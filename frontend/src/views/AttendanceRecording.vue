<template>
  <div>
    <div class="card">
      <h2>Record Attendance</h2>
      <div class="form-group">
        <label>Select Class</label>
        <select v-model="selectedClass" @change="loadStudentsByClass" class="form-group select">
          <option value="">Select Class</option>
          <option value="9">Class 9</option>
          <option value="10">Class 10</option>
          <option value="11">Class 11</option>
          <option value="12">Class 12</option>
        </select>
      </div>
      <div class="form-group">
        <label>Select Section</label>
        <select v-model="selectedSection" @change="loadStudentsByClass" class="form-group select">
          <option value="">All Sections</option>
          <option value="A">Section A</option>
          <option value="B">Section B</option>
          <option value="C">Section C</option>
          <option value="D">Section D</option>
        </select>
      </div>
      <div class="form-group">
        <label>Date</label>
        <input v-model="attendanceDate" type="date" class="form-group input" />
      </div>
    </div>

    <div v-if="students.length > 0" class="card">
      <div class="bulk-actions">
        <button @click="markAll('present')" class="btn btn-success">Mark All Present</button>
        <button @click="markAll('absent')" class="btn btn-danger">Mark All Absent</button>
        <button @click="markAll('late')" class="btn" style="background: #f39c12; color: white;">Mark All Late</button>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Note</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.id">
            <td>{{ student.student_id }}</td>
            <td>{{ student.name }}</td>
            <td>
              <select v-model="attendance[student.id].status" class="form-group select">
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
              </select>
            </td>
            <td>
              <input
                v-model="attendance[student.id].note"
                type="text"
                placeholder="Optional note"
                class="form-group input"
              />
            </td>
          </tr>
        </tbody>
      </table>

      <div class="attendance-stats">
        <p>Present: {{ stats.present }} | Absent: {{ stats.absent }} | Late: {{ stats.late }}</p>
        <p>Attendance: {{ attendancePercentage }}%</p>
      </div>

      <button @click="submitAttendance" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Saving...' : 'Save Attendance' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import api from '../services/api'

const selectedClass = ref('')
const selectedSection = ref('')
const attendanceDate = ref(new Date().toISOString().split('T')[0])
const students = ref([])
const attendance = ref({})
const loading = ref(false)

const stats = computed(() => {
  const present = Object.values(attendance.value).filter(a => a.status === 'present').length
  const absent = Object.values(attendance.value).filter(a => a.status === 'absent').length
  const late = Object.values(attendance.value).filter(a => a.status === 'late').length
  return { present, absent, late }
})

const attendancePercentage = computed(() => {
  const total = students.value.length
  if (total === 0) return 0
  return Math.round((stats.value.present / total) * 100)
})

const loadStudentsByClass = async () => {
  if (!selectedClass.value) {
    students.value = []
    return
  }

  try {
    const params = { class: selectedClass.value }
    if (selectedSection.value) {
      params.section = selectedSection.value
    }
    const response = await api.get('/students', { params })
    students.value = response.data.data
    
    // Initialize attendance object
    students.value.forEach(student => {
      attendance.value[student.id] = {
        student_id: student.id,
        status: 'present',
        note: '',
      }
    })
  } catch (error) {
    console.error('Error loading students:', error)
  }
}

const markAll = (status) => {
  students.value.forEach(student => {
    attendance.value[student.id].status = status
  })
}

const submitAttendance = async () => {
  loading.value = true
  try {
    const attendances = Object.values(attendance.value)
    await api.post('/attendances', {
      date: attendanceDate.value,
      attendances,
    })
    alert('Attendance recorded successfully!')
    // Reset form
    students.value = []
    attendance.value = {}
  } catch (error) {
    alert('Error recording attendance: ' + (error.response?.data?.message || 'Unknown error'))
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bulk-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.attendance-stats {
  margin: 1rem 0;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 4px;
}

.attendance-stats p {
  margin: 0.5rem 0;
  font-weight: 500;
}
</style>

