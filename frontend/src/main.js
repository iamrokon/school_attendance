import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import StudentList from './views/StudentList.vue'
import AttendanceRecording from './views/AttendanceRecording.vue'
import Dashboard from './views/Dashboard.vue'
import Login from './views/Login.vue'
import './style.css'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login },
  { path: '/students', component: StudentList },
  { path: '/attendance', component: AttendanceRecording },
  { path: '/dashboard', component: Dashboard },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.path !== '/login' && !token) {
    next('/login')
  } else {
    next()
  }
})

createApp(App).use(router).mount('#app')

