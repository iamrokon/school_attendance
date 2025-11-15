<template>
  <div class="card">
    <h2>Edit Student</h2>

    <div v-if="loading">Loading...</div>
    <div v-else>
      <form @submit.prevent="save">
        <!-- Keep student ID as a hidden field so it's submitted but not editable -->
        <input type="hidden" id="student_id" v-model="student.student_id" />

        <div class="form-group">
          <label for="name">Name</label>
          <input id="name" v-model="student.name" class="input" />
        </div>

        <div class="form-group">
          <label for="class">Class</label>
          <input id="class" v-model="student.class" class="input" />
        </div>

        <div class="form-group">
          <label for="section">Section</label>
          <input id="section" v-model="student.section" class="input" />
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn" @click="cancel">Cancel</button>
        </div>
      </form>
    </div>

    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const id = route.params.id

const student = ref({ student_id: '', name: '', class: '', section: '' })
const loading = ref(false)
const error = ref('')

const loadStudent = async () => {
  if (!id) return
  loading.value = true
  try {
    const response = await api.get(`/students/${id}`)
    student.value = response.data.data
    console.log('Loaded student', student.value)
  } catch (err) {
    console.error('Failed to load student', err)
    error.value = 'Failed to load student.'
  } finally {
    loading.value = false
  }
}

const save = async () => {
  error.value = ''
  try {
    await api.put(`/students/${id}`, student.value)
    router.push('/students')
  } catch (err) {
    console.error('Failed to save student', err)
    error.value = 'Failed to save student.'
  }
}

const cancel = () => {
  router.back()
}

onMounted(loadStudent)
</script>

<style scoped>
.form-group { margin-bottom: 0.75rem }
.input { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px }
.form-actions { display: flex; gap: 0.5rem; margin-top: 1rem }
.error { color: red; margin-top: 1rem }
</style>
