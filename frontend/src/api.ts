import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8078/api',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
})

// attach Authorization header from localStorage or Pinia auth store
api.interceptors.request.use((config) => {
  try {
    // prefer store if available to support reactive updates
    const token = localStorage.getItem('access_token')
    if (token && config.headers) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
  } catch (e) {
    // ignore
  }
  return config
})

export default api
