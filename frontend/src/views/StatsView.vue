<template>
  <v-container>
    <v-text-field v-model="year" label="Год" @change="fetch" />
    <StatsTable />
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import StatsTable from '../components/StatsTable.vue'
import api from '../api'

const year = ref(new Date().getFullYear())

async function fetch() {
  const res = await api.get('/stats/monthly', { params: { year: year.value } })
  console.log(res.data)
}
</script>
