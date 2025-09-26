import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import StatsView from '../views/StatsView.vue'
import CategoriesView from '../views/CategoriesView.vue'

const routes = [
  { path: '/', component: HomeView },
  { path: '/stats', component: StatsView },
  { path: '/categories', name: 'categories', component: CategoriesView },
]

const router = createRouter({ history: createWebHistory(), routes })
export default router
