import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

type TransactionItem = any

export const useTransactionsStore = defineStore('transactions', () => {
  const items = ref<TransactionItem[]>([])
  const month = ref('')
  const loading = ref(false)

  async function fetchTransactions(m?: string) {
    loading.value = true
    try {
      const params: any = {}
      if (m) params.month = m
      else if (month.value) params.month = month.value

      const res = await api.get('/transactions', { params })

      // Expecting an array from API; ensure category is object if present
      items.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])

      // normalize: if category is present as string, convert to { id: null, name: string }
      items.value = items.value.map((t: any) => ({
        ...t,
        category: typeof t.category === 'object' ? t.category : (t.category ? { id: t.category_id ?? null, name: t.category } : null)
      }))

      if (!items.value || items.value.length === 0) {
        console.log('fetchTransactions: empty result from API', res)
      }

      return res
    } catch (e: any) {
      console.error('fetchTransactions error', e?.response?.data ?? e.message)
      items.value = []
      throw e
    } finally {
      loading.value = false
    }
  }

  async function createTransaction(payload: any) {
    // ensure we send category_id only
    const body = { ...payload }
    if (body.category && typeof body.category === 'object') body.category_id = body.category.id
    delete body.category

    const res = await api.post('/transactions', body)
    // API returns transaction with category name; normalize
    const t = { ...res.data, category: res.data.category ? { id: res.data.category_id ?? null, name: res.data.category } : null }
    items.value.push(t)
    return t
  }

  async function updateTransaction(id: number, payload: any) {
    const body = { ...payload }
    if (body.category && typeof body.category === 'object') body.category_id = body.category.id
    delete body.category

    const res = await api.put(`/transactions/${id}`, body)
    const idx = items.value.findIndex((i: any) => i.id === id)
    const t = { ...res.data, category: res.data.category ? { id: res.data.category_id ?? null, name: res.data.category } : null }
    if (idx !== -1) items.value.splice(idx, 1, t)
    return t
  }

  async function deleteTransaction(id: number) {
    await api.delete(`/transactions/${id}`)
    const idx = items.value.findIndex((i: any) => i.id === id)
    if (idx !== -1) items.value.splice(idx, 1)
  }

  return {
    items,
    month,
    loading,
    fetchTransactions,
    createTransaction,
    updateTransaction,
    deleteTransaction,
  }
})
