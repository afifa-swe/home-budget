<template>
  <v-form @submit.prevent="onSubmit">
    <v-row>
      <v-col cols="12" md="3">
        <v-select v-model="form.type" :items="['income','expense']" label="Тип" />
      </v-col>
      <v-col cols="12" md="3">
        <v-select v-model="form.category_id" :items="categoriesList" item-text="name" item-value="id" label="Категория" />
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
import { ref, computed, onMounted, watch } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'
import { useCategoriesStore } from '../stores/useCategoriesStore'

const props = defineProps<{ editItem?: any }>()
const emit = defineEmits(['saved', 'cancel'])

const tx = useTransactionsStore()
const categoriesStore = useCategoriesStore()

const defaultForm = () => ({
  type: 'expense',
  category_id: null,
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
      // map incoming editItem which may have category object to form
      form.value = {
        type: val.type || 'expense',
        category_id: val.category ? val.category.id : (val.category_id ?? null),
        occurred_at: val.occurred_at ? val.occurred_at.slice(0,16) : '',
        amount: val.amount ?? 0,
        comment: val.comment ?? ''
      }
    } else {
      form.value = defaultForm()
    }
  },
  { immediate: true }
)

const categoriesList = computed(() => {
  const items = categoriesStore.items || []
  return items.filter((c: any) => c.type === form.value.type)
})

onMounted(async () => {
  try {
    await categoriesStore.fetchCategories()
    console.log('categories loaded', categoriesStore.items)
  } catch (e) {
    console.error('fetchCategories failed', e)
  }
})

watch(() => form.value.type, () => {
  form.value.category_id = null
})

async function onSubmit() {
  try {
    if (!form.value.category_id) {
      alert('Выберите категорию')
      return
    }

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
