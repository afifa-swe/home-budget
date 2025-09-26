import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useCategoriesStore = defineStore('categories', () => {
  const items = ref<any[]>([])

  async function fetchCategories() {
    try {
      const res = await api.get('/categories')
      const data = res.data
      // Support both flattened array response and grouped { income:[], expense:[] }
      if (Array.isArray(data)) {
        items.value = data
      } else if (data && (Array.isArray(data.income) || Array.isArray(data.expense))) {
        const income = Array.isArray(data.income) ? data.income.map((c: any) => ({ ...c, type: 'income' })) : []
        const expense = Array.isArray(data.expense) ? data.expense.map((c: any) => ({ ...c, type: 'expense' })) : []
        items.value = [...income, ...expense]
      } else {
        // Fallback: empty
        items.value = []
      }
      return items.value
    } catch (e: any) {
      console.error('fetchCategories error', e?.response?.data ?? e.message)
      items.value = []
      throw e
    }
  }

  async function createCategory(payload: any) {
    const res = await api.post('/categories', payload)
    // refresh list from server to keep consistent ordering/types
    await fetchCategories()
    return res.data
  }

  async function updateCategory(id: number, payload: any) {
    const res = await api.put(`/categories/${id}`, payload)
    await fetchCategories()
    return res.data
  }

  async function deleteCategory(id: number) {
    await api.delete(`/categories/${id}`)
    await fetchCategories()
  }

  return {
    items,
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
  }
})
