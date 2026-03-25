import { http } from '../http/client'
import type { PaginatedResponse } from '@/types'

export abstract class BaseService<T, CreateDTO, UpdateDTO> {
  constructor(protected baseUrl: string) {}

  async getList(params?: Record<string, unknown>): Promise<PaginatedResponse<T>> {
    return http.get(this.baseUrl, { params })
  }

  async getById(id: number): Promise<T> {
    return http.get(`${this.baseUrl}/${id}`)
  }

  async create(data: CreateDTO): Promise<T> {
    return http.post(this.baseUrl, data)
  }

  async update(id: number, data: UpdateDTO): Promise<T> {
    return http.put(`${this.baseUrl}/${id}`, data)
  }

  async delete(id: number): Promise<void> {
    return http.delete(`${this.baseUrl}/${id}`)
  }
}
