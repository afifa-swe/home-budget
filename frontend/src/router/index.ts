import { createRouter, createWebHistory, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '../stores/useAuthStore'
import Transactions from '../views/Transactions.vue'
import Categories from '../views/CategoriesView.vue'
import Stats from '../views/StatsView.vue'
import Login from '../views/LoginView.vue'
import Register from '../views/RegisterView.vue'

const routes = [
  {
    path: '/',
    redirect: () => {
      const token = localStorage.getItem('access_token')
      return token ? '/transactions' : '/login'
    },
  },
  { path: '/transactions', name: 'transactions', component: Transactions, meta: { requiresAuth: true } },
  { path: '/categories', name: 'categories', component: Categories, meta: { requiresAuth: true } },
  { path: '/stats', name: 'stats', component: Stats, meta: { requiresAuth: true } },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to: RouteLocationNormalized, from, next) => {
  const auth = useAuthStore()

  let storeAuth = false
  try {
    const maybe = (auth as any).isAuthenticated
    storeAuth = !!(maybe && (maybe.value !== undefined ? maybe.value : maybe))
  } catch (e) {
    storeAuth = false
  }

  const fallbackAuth = !!localStorage.getItem('access_token')
  const isAuth = storeAuth || fallbackAuth

  const requiresAuth = to.meta && (to.meta as any).requiresAuth

  // 1) protected route but not authenticated -> go to /login
  if (requiresAuth && !isAuth) {
    return next({ path: '/login' })
  }

  // 2) authenticated user visiting /login -> redirect to /transactions
  if (to.path === '/login' && isAuth) {
    return next({ path: '/transactions' })
  }

  // otherwise proceed
  return next()
})

export default router
