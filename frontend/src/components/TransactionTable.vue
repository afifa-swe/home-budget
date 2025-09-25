<template>
  <v-data-table style="height: 700px;" :items="items" :headers="headers" :loading="loading">
    <template #item.occurred_at="{ item }">
      {{ formatDate(item.occurred_at) }}
    </template>
    <template #item.amount="{ item }">
      {{ formatMoney(item.amount) }}
    </template>
    <template #item.running_balance="{ item }">
      {{ formatMoney(item.running_balance) }}
    </template>
    <template #item.actions="{ item }">
      <v-btn icon @click="edit(item)"><v-icon>mdi-pencil</v-icon></v-btn>
      <v-btn icon color="red" @click="remove(item)"><v-icon>mdi-delete</v-icon></v-btn>
    </template>
  </v-data-table>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'
import { formatMoney } from '../utils/money'

import { VDataTable } from 'vuetify/components/VDataTable'

const tx = useTransactionsStore()

const items = computed(() => tx.items)
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

function formatDate(val: string) {
  if (!val) return ''
  const d = new Date(val)
  const dd = String(d.getDate()).padStart(2, '0')
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${dd}.${mm}.${yyyy}`
}

function edit(item: any) {
  // TODO implement edit modal
  console.log('edit', item)
}

async function remove(item: any) {
  if (!confirm('Удалить транзакцию?')) return
  try {
    await tx.deleteTransaction(item.id)
  } catch (err: any) {
    console.error(err)
    alert('Ошибка при удалении транзакции')
  }
}
</script>
