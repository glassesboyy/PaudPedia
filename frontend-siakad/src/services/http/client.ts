import axios from 'axios'
import { setupInterceptors } from './interceptors'

const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: 30000,
  headers: {
    Accept: 'application/json',
  },
})

setupInterceptors(http)

export { http }
