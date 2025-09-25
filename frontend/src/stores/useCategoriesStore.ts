import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useCategoriesStore = defineStore('categories', () => {
  const income = ref<string[]>([])
  const expense = ref<string[]>([])

  async function fetchCategories() {
    try {
      const res = await api.get('/categories')
      console.log('fetchCategories response', res && res.data)
      income.value = Array.isArray(res.data?.income) ? res.data.income : []
      expense.value = Array.isArray(res.data?.expense) ? res.data.expense : []
      console.log('income', income.value, 'expense', expense.value)
      return true
    } catch (e: any) {
      console.error('fetchCategories error', e?.response?.data ?? e.message)
      income.value = []
      expense.value = []
      return false
    }
  }

  return {
    income,
    expense,
    fetchCategories,
  }
})
