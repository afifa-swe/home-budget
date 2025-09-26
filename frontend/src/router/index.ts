import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import StatsView from '../views/StatsView.vue'
import CategoriesView from '../views/CategoriesView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

const routes = [
  { path: '/', component: HomeView, meta: { requiresAuth: true } },
  { path: '/stats', component: StatsView, meta: { requiresAuth: true } },
  { path: '/categories', name: 'categories', component: CategoriesView, meta: { requiresAuth: true } },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to, from, next) => {
  if (to.meta && (to.meta as any).requiresAuth) {
    const token = localStorage.getItem('access_token')
    if (!token) {
      return next({ path: '/login' })
    }
  }
  return next()
})
export default router
