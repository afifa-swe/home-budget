<template>
  <v-app>
    <router-view />
  </v-app>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useCategoriesStore } from './stores/useCategoriesStore'

const cat = useCategoriesStore()

onMounted(() => {
  cat.fetchCategories()
})
</script>

<style>
@import 'vuetify/styles';
</style>
