import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '../api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<Record<string, any> | null>(null)
  const accessToken = ref<string | null>(localStorage.getItem('access_token') || null)

  async function login(email: string, password: string) {
    const res = await axios.post('/login', { email, password })
    if (res && res.data && res.data.access_token) {
      accessToken.value = res.data.access_token
      localStorage.setItem('access_token', accessToken.value!)
      await fetchUser()
    }
    return res
  }

  async function register(name: string, email: string, password: string, password_confirmation: string) {
    const res = await axios.post('/register', { name, email, password, password_confirmation })
    if (res && res.data && res.data.access_token) {
      accessToken.value = res.data.access_token
      localStorage.setItem('access_token', accessToken.value!)
      await fetchUser()
    }
    return res
  }

  async function fetchUser() {
    if (!accessToken.value) return null
    try {
      const res = await axios.get('/user')
      user.value = res.data
      return user.value
    } catch (err) {
      user.value = null
      return null
    }
  }

  function logout() {
    user.value = null
    accessToken.value = null
    localStorage.removeItem('access_token')
  }

  return {
    user,
    accessToken,
    login,
    register,
    fetchUser,
    logout,
  }
})

export default useAuthStore
