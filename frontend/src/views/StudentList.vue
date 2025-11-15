<template>
  <div>
    <div class="card">
      <h2>Student List</h2>
      <div class="filters">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Search by name or ID..."
          class="form-group input"
        />
        <select v-model="filters.class" class="form-group select">
          <option value="">All Classes</option>
          <option value="9">Class 9</option>
          <option value="10">Class 10</option>
          <option value="11">Class 11</option>
          <option value="12">Class 12</option>
        </select>
        <button @click="loadStudents" class="btn btn-primary">Filter</button>
      </div>
    </div>

    <div class="card">
      <table class="table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.id">
            <td>{{ student.student_id }}</td>
            <td>{{ student.name }}</td>
            <td>{{ student.class }}</td>
            <td>{{ student.section }}</td>
            <td>
              <button @click="editStudent(student)" class="btn btn-primary">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="btn"
        >
          Previous
        </button>
        <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="btn"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const students = ref([])
const filters = ref({ search: '', class: '' })
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
})

const loadStudents = async (page = 1) => {
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      ...filters.value,
    }
    const response = await api.get('/students', { params })
    students.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
    }
  } catch (error) {
    console.error('Error loading students:', error)
  }
}

const changePage = (page) => {
  loadStudents(page)
}

const router = useRouter()

const editStudent = (student) => {
  // Navigate to the student edit route. Adjust path if your router uses a named route.
  if (!student || !student.id) {
    console.warn('Invalid student for editing:', student)
    return
  }
  router.push(`/students/${student.id}/edit`)
}

onMounted(() => {
  loadStudents()
})
</script>

<style scoped>
.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.filters .input,
.filters .select {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}
</style>

