<template>
  <v-data-table :items="items" :headers="headers">
    <template #item.total_income="{ item }">
      {{ formatNumber(item.total_income) }}
    </template>
    <template #item.total_expense="{ item }">
      {{ formatNumber(item.total_expense) }}
    </template>
    <template #item.balance="{ item }">
      {{ formatNumber(item.balance) }}
    </template>
    <template #item.month="{ item }">
      {{ formatMonth(item.month) }}
    </template>
    <template #no-data>
      <div class="pa-4">Нет данных</div>
    </template>
  </v-data-table>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  items: { type: Array as () => any[], default: () => [] }
})

const headers = computed(() => [
  { title: 'Месяц', key: 'month', text: 'Месяц', value: 'month' },
  { title: 'Доход', key: 'total_income', text: 'Доход', value: 'total_income' },
  { title: 'Расход', key: 'total_expense', text: 'Расход', value: 'total_expense' },
  { title: 'Баланс', key: 'balance', text: 'Баланс', value: 'balance' }
])

function formatNumber(val: number | string | null | undefined) {
  if (val == null) return '0'
  const num = typeof val === 'string' ? Number(val) : val
  if (Number.isNaN(num as number)) return '0'
  return new Intl.NumberFormat('ru-RU').format(num as number)
}

const monthNames = [
  'Январь',
  'Февраль',
  'Март',
  'Апрель',
  'Май',
  'Июнь',
  'Июль',
  'Август',
  'Сентябрь',
  'Октябрь',
  'Ноябрь',
  'Декабрь'
]

function formatMonth(val: number | string | null | undefined) {
  if (val == null) return ''
  const num = typeof val === 'string' ? Number(val) : val
  if (Number.isNaN(num as number)) return ''
  // months in API are 1-based or possibly 8 for August? The example used 8->Август
  const idx = Number(num) - 1
  return monthNames[idx] ?? String(val)
}
</script>
