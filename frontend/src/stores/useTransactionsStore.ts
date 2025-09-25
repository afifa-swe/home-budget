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

      // Expecting an array from API
      items.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])

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
    const res = await api.post('/transactions', payload)
    items.value.push(res.data)
    return res.data
  }

  async function updateTransaction(id: number, payload: any) {
    const res = await api.put(`/transactions/${id}`, payload)
    const idx = items.value.findIndex((i: any) => i.id === id)
    if (idx !== -1) items.value.splice(idx, 1, res.data)
    return res.data
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
