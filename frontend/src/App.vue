<template>
  <v-app>
    <v-app-bar app>
      <v-toolbar-title>HomeBudget</v-toolbar-title>
      <v-spacer />
      <div v-if="auth.user">
        {{ auth.user.email }}
        <v-btn text @click="onLogout">Выйти</v-btn>
      </div>
      <div v-else>
        <v-btn text to="/login">Войти</v-btn>
      </div>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useCategoriesStore } from './stores/useCategoriesStore'
import { useAuthStore } from './stores/useAuthStore'
import { useRouter } from 'vue-router'

const cat = useCategoriesStore()
const auth = useAuthStore()
const router = useRouter()

onMounted(async () => {
  cat.fetchCategories()
  if (auth.accessToken && !auth.user) {
    await auth.fetchUser()
  }
})

function onLogout() {
  auth.logout()
  router.push('/login')
}
</script>

<style>
@import 'vuetify/styles';
</style>
