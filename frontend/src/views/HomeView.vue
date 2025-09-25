<template>
  <v-container>
    <MonthPicker />
    <TransactionTable />
  </v-container>
</template>

<script setup lang="ts">
import MonthPicker from '../components/MonthPicker.vue'
import TransactionTable from '../components/TransactionTable.vue'
import { onMounted } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'

const tx = useTransactionsStore()

onMounted(() => {
  const m = tx.month.value
  const monthToFetch = m && m.length
    ? m
    : (() => {
        const d = new Date()
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
      })()
  tx.fetchTransactions(monthToFetch)
})
</script>
