<template>
  <v-form @submit.prevent="onSubmit">
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
        <v-btn color="primary" type="submit">{{ isEdit ? 'Сохранить' : 'Добавить' }}</v-btn>
        <v-btn v-if="isEdit" color="grey" @click="cancelEdit" variant="text">Отмена</v-btn>
      </v-col>
      <v-col cols="12">
        <v-textarea v-model="form.comment" label="Комментарий" />
      </v-col>
    </v-row>
  </v-form>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, watchEffect } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'
import { useCategoriesStore } from '../stores/useCategoriesStore'
import { toRefs } from 'vue'

const props = defineProps<{ editItem?: any }>()
const emit = defineEmits(['saved', 'cancel'])

const tx = useTransactionsStore()
const cat = useCategoriesStore()

const defaultForm = () => ({
  type: 'expense',
  category: '',
  occurred_at: '',
  amount: 0,
  comment: ''
})
const form = ref(defaultForm())

const isEdit = computed(() => !!props.editItem)

watch(
  () => props.editItem,
  (val) => {
    if (val) {
      form.value = { ...val }
      // Преобразуем дату для input type="datetime-local"
      if (form.value.occurred_at) {
        form.value.occurred_at = form.value.occurred_at.slice(0, 16)
      }
    } else {
      form.value = defaultForm()
    }
  },
  { immediate: true }
)

const categoriesList = computed(() => {
  const incomeArr: string[] = Array.isArray(cat.income) ? (cat.income as string[]) : (cat.income && (cat.income as any).value) || []
  const expenseArr: string[] = Array.isArray(cat.expense) ? (cat.expense as string[]) : (cat.expense && (cat.expense as any).value) || []
  return form.value.type === 'income' ? incomeArr : expenseArr
})

onMounted(async () => {
  try {
    await cat.fetchCategories()
  } catch (e) {
    console.error('fetchCategories failed', e)
  }
})

watch(() => form.value.type, () => {
  form.value.category = ''
})

async function onSubmit() {
  try {
    if (isEdit.value && props.editItem?.id) {
      await tx.updateTransaction(props.editItem.id, {
        ...form.value,
        occurred_at: form.value.occurred_at || new Date().toISOString(),
      })
    } else {
      await tx.createTransaction({
        ...form.value,
        occurred_at: form.value.occurred_at || new Date().toISOString(),
      })
    }
    await tx.fetchTransactions()
    emit('saved')
    form.value = defaultForm()
  } catch (e: any) {
    alert(e?.response?.data?.message || e.message || 'Ошибка сохранения')
  }
}

function cancelEdit() {
  emit('cancel')
  form.value = defaultForm()
}
</script>
