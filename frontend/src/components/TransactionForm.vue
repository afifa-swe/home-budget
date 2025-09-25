<template>
  <v-form  @submit.prevent="onSubmit">
    <v-row>
      <v-col cols="12" md="3">
        <v-select v-model="form.type" :items="['income','expense']" label="Тип" />
      </v-col>

      <v-col cols="12" md="3">
        <v-select v-model="form.category" :items="categoriesList" label="Категория" />
      </v-col>

      <v-col cols="12" md="3">
        <v-text-field v-model="form.occurred_at" type="datetime-local" label="Дата" />
      </v-col>

      <v-col cols="12" md="2">
        <v-text-field v-model.number="form.amount" type="number" label="Сумма" />
      </v-col>

      <v-col cols="12" md="1">
        <v-btn color="primary" type="submit">Добавить</v-btn>
      </v-col>

      <v-col cols="12">
        <v-textarea v-model="form.comment" label="Комментарий" />
      </v-col>
    </v-row>
  </v-form>
</template>

<script setup lang="ts">
import { reactive,ref, computed, onMounted, watch } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'
import { useCategoriesStore } from '../stores/useCategoriesStore'

const tx = useTransactionsStore()
const cat = useCategoriesStore()

const form = ref({
  type: 'expense',
  category: '',
  occurred_at: '',
  amount: 0,
  comment: ''
})

// return plain arrays (unwrapped) for Vuetify; support both refs and plain arrays
const categoriesList = computed(() => {
  const incomeArr: string[] = Array.isArray(cat.income) ? (cat.income as string[]) : (cat.income && (cat.income as any).value) || []
  const expenseArr: string[] = Array.isArray(cat.expense) ? (cat.expense as string[]) : (cat.expense && (cat.expense as any).value) || []
  return form.type === 'income' ? incomeArr : expenseArr
})

onMounted(async () => {
  // ensure categories are loaded
  try {
    await cat.fetchCategories()
  } catch (e) {
    console.error('fetchCategories failed', e)
  }
})

watch(() => form.type, (newType) => {
  // reset category when type changes
  form.category = ''
})

async function onSubmit() {
  await tx.createTransaction({
    ...form,
    occurred_at: form.occurred_at || new Date().toISOString(),
  })
  // refresh list
  tx.fetchTransactions()
}
</script>
