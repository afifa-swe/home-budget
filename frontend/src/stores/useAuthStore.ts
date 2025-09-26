import { defineStore } from 'pinia'
import axios from '../api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as null | Record<string, any>,
    accessToken: (localStorage.getItem('access_token') as string) || null,
  }),
  actions: {
    async login(email: string, password: string) {
      const res = await axios.post('/login', { email, password })
      if (res && res.data && res.data.access_token) {
        this.accessToken = res.data.access_token
        localStorage.setItem('access_token', this.accessToken)
        await this.fetchUser()
      }
      return res
    },

    async register(name: string, email: string, password: string, password_confirmation: string) {
      const res = await axios.post('/register', { name, email, password, password_confirmation })
      if (res && res.data && res.data.access_token) {
        this.accessToken = res.data.access_token
        localStorage.setItem('access_token', this.accessToken)
        await this.fetchUser()
      }
      return res
    },

    async fetchUser() {
      if (!this.accessToken) return null
      try {
        const res = await axios.get('/user')
        this.user = res.data
        return this.user
      } catch (err) {
        this.user = null
        return null
      }
    },

    logout() {
      this.user = null
      this.accessToken = null
      localStorage.removeItem('access_token')
    }
  }
})

export default useAuthStore
