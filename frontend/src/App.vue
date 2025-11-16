<template>
  <div id="app">
    <nav v-if="isAuthenticated" class="navbar">
      <div class="nav-container">
        <h1 class="nav-title">School Attendance System</h1>
        <div class="nav-links">
          <router-link to="/dashboard">Dashboard</router-link>
          <router-link to="/students">Students</router-link>
          <router-link to="/attendance">Record Attendance</router-link>
          <button @click="logout" class="btn-logout">Logout</button>
        </div>
      </div>
    </nav>
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from './services/api'

const router = useRouter()
const route = useRoute()

// Re-compute on every route change so we see updated localStorage token after login/logout.
const isAuthenticated = computed(() => {
  // access route to make this computed depend on it
  route.fullPath
  return !!localStorage.getItem('token')
})

const logout = () => {
  api.post('/logout').then(() => {
    localStorage.removeItem('token')
    router.push('/login')
  })
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background-color: #f5f5f5;
}

.navbar {
  background: #2c3e50;
  color: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-title {
  font-size: 1.5rem;
  font-weight: 600;
}

.nav-links {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.nav-links a {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background 0.3s;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  background: rgba(255,255,255,0.1);
}

.btn-logout {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-logout:hover {
  background: #c0392b;
}

main {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 2rem;
}
</style>

