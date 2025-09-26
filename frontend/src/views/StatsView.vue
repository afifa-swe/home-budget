<template>
  <v-container>
    <v-text-field v-model="year" label="Год" @change="fetch" />

    <v-card v-if="stats.length" class="pa-4 mb-4">
      <div class="d-flex flex-column">
        <div class="text-h6 mb-2">Статистика за {{ year }} год</div>
        <div class="d-flex align-center">
          <div class="me-6"><strong>Доход:</strong>&nbsp;{{ formattedTotalIncome }}</div>
          <div class="me-6"><strong>Расход:</strong>&nbsp;{{ formattedTotalExpense }}</div>
          <div><strong>Баланс:</strong>&nbsp;{{ formattedBalance }}</div>
        </div>
      </div>
    </v-card>

    <StatsTable :items="stats" />
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import StatsTable from '../components/StatsTable.vue'
import api from '../api'

const year = ref(new Date().getFullYear())
const stats = ref<any[]>([])

const formatter = new Intl.NumberFormat('ru-RU')

const totalIncome = computed(() => stats.value.reduce((s, r) => s + (Number(r.total_income) || 0), 0))
const totalExpense = computed(() => stats.value.reduce((s, r) => s + (Number(r.total_expense) || 0), 0))
const balance = computed(() => totalIncome.value - totalExpense.value)

const formattedTotalIncome = computed(() => formatter.format(totalIncome.value))
const formattedTotalExpense = computed(() => formatter.format(totalExpense.value))
const formattedBalance = computed(() => formatter.format(balance.value))

async function fetch() {
  const res = await api.get('/stats/monthly', { params: { year: year.value } })
  // Ensure we write an array of objects with numeric values for income/expense/balance
  const data = Array.isArray(res.data) ? res.data : []
  stats.value = data.map((row: any) => ({
    month: row.month,
    total_income: row.total_income == null ? 0 : Number(row.total_income),
    total_expense: row.total_expense == null ? 0 : Number(row.total_expense),
    balance: row.balance == null ? 0 : Number(row.balance)
  }))
}

onMounted(() => {
  fetch()
})
</script>
