<template>
  <div>
    <TransactionForm
      :editItem="editItem"
      @saved="onSaved"
      @cancel="onCancelEdit"
    />
    <v-data-table style="height: 700px;" :items="items" :headers="headers" :loading="loading">
      <template #item.occurred_at="{ item }">
        {{ formatDate(item.occurred_at) }}
      </template>
      <template #item.category="{ item }">
        {{ item.category?.name ?? '' }}
      </template>
      <template #item.amount="{ item }">
        {{ formatMoney(item.amount ?? 0) }}
      </template>
      <template #item.running_balance="{ item }">
        {{ formatMoney(item.running_balance ?? 0) }}
      </template>
      <template #item.actions="{ item }">
        <v-btn icon @click="edit(item)"><v-icon>mdi-pencil</v-icon></v-btn>
        <v-btn icon color="red" @click="remove(item)"><v-icon>mdi-delete</v-icon></v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'
import { formatMoney } from '../utils/money'
import TransactionForm from './TransactionForm.vue'

// define a local transaction type to help TS in templates
type Transaction = {
  id?: number
  type?: string
  category?: { id?: number, name?: string }
  occurred_at?: string
  amount?: number
  running_balance?: number
  comment?: string
  [key: string]: any
}

const tx = useTransactionsStore()
// ensure items has a known type for the template compiler
const items = computed<Transaction[]>(() => (tx.items as unknown) as Transaction[])
const loading = computed(() => tx.loading)
const headers = computed(() => [
  { title: 'Тип', key: 'type' },
  { title: 'Категория', key: 'category' },
  { title: 'Дата', key: 'occurred_at' },
  { title: 'Сумма', key: 'amount' },
  { title: 'Итого', key: 'running_balance' },
  { title: 'Комментарий', key: 'comment' },
  { title: 'Действия', key: 'actions', sortable: false }
])

function formatDate(val?: string) {
  if (!val) return ''
  const d = new Date(val)
  const dd = String(d.getDate()).padStart(2, '0')
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${dd}.${mm}.${yyyy}`
}

const editItem = ref<Transaction | null>(null)

function edit(item: Transaction) {
  editItem.value = { ...item }
}

function onSaved() {
  editItem.value = null
}

function onCancelEdit() {
  editItem.value = null
}

async function remove(item: Transaction) {
  if (!confirm('Удалить транзакцию?')) return
  try {
    if (!item.id) throw new Error('Нет id')
    await tx.deleteTransaction(item.id)
    await tx.fetchTransactions()
  } catch (err: any) {
    alert('Ошибка при удалении транзакции')
  }
}
</script>
