<template>
  <v-text-field type="month" v-model="month" label="Месяц" @change="onChange" />
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'

const store = useTransactionsStore()
const month = ref('')

function onChange() {
  store.fetchTransactions(month.value)
}
</script>
