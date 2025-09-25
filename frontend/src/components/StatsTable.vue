<template>
  <v-data-table :items="items">
    <v-data-table-column label="Месяц" field="month" />
    <v-data-table-column label="Доходы" field="total_income" />
    <v-data-table-column label="Расходы" field="total_expense" />
    <v-data-table-column label="Баланс" field="balance" />
  </v-data-table>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useTransactionsStore } from '../stores/useTransactionsStore'

const items = computed(() => [])
</script>
