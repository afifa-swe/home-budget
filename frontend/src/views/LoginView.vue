<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Login</v-card-title>
          <v-card-text>
            <v-form ref="form" @submit.prevent="onSubmit">
              <v-text-field v-model="email" label="Email" required></v-text-field>
              <v-text-field v-model="password" label="Password" type="password" required></v-text-field>
              <v-btn type="submit" color="primary">Login</v-btn>
              <v-btn text color="primary" class="ml-2" :to="{ path: '/register' }">Нет аккаунта? Зарегистрироваться</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/useAuthStore'

const email = ref('')
const password = ref('')
const router = useRouter()
const auth = useAuthStore()

async function onSubmit() {
  try {
    const res = await auth.login(email.value, password.value)
    router.push('/')
  } catch (err: any) {
    if (err.response && err.response.status === 401) {
      alert('Неверный email или пароль')
    } else {
      alert('Ошибка при входе')
    }
  }
}
</script>
