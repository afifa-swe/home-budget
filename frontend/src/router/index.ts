import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import StatsView from '../views/StatsView.vue'

const routes = [
  { path: '/', component: HomeView },
  { path: '/stats', component: StatsView },
]

const router = createRouter({ history: createWebHistory(), routes })
export default router
