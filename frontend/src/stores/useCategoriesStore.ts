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
    try {
      console.debug('[categories] create payload:', payload)
      const res = await api.post('/categories', payload)
      console.debug('[categories] create response:', res && res.data)
      // refresh list from server to keep consistent ordering/types
      await fetchCategories()
      return res.data
    } catch (err: any) {
      // log useful debug info to console for easier diagnosis in browser
      console.error('[categories] create error', err?.response?.status, err?.response?.data ?? err?.message)
      throw err
    }
  }

  async function updateCategory(id: number, payload: any) {
    try {
      console.debug('[categories] update payload:', id, payload)
      const res = await api.put(`/categories/${id}`, payload)
      await fetchCategories()
      return res.data
    } catch (err: any) {
      console.error('[categories] update error', err?.response?.status, err?.response?.data ?? err?.message)
      throw err
    }
  }

  async function deleteCategory(id: number) {
    try {
      console.debug('[categories] delete id:', id)
      await api.delete(`/categories/${id}`)
      await fetchCategories()
    } catch (err: any) {
      console.error('[categories] delete error', err?.response?.status, err?.response?.data ?? err?.message)
      throw err
    }
  }

  return {
    items,
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
  }
})
