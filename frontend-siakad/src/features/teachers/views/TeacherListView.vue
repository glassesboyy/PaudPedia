<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { teacherService } from '@/features/teachers/services/teacher.service'
import type { Teacher } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const isHeadmaster = computed(() => schoolStore.isHeadmaster)
const copy = computed(() => getCopy('teacher'))

const isLoading = ref(false)
const teachers = ref<Teacher[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const searchQuery = ref('')
const generalError = ref('')

// Delete modal state
const showDeleteModal = ref(false)
const deleteTarget = ref<Teacher | null>(null)
const isDeleting = ref(false)

onMounted(() => {
  fetchTeachers()
})

async function fetchTeachers(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  generalError.value = ''
  try {
    const params: Record<string, any> = { page }
    if (searchQuery.value) params.search = searchQuery.value

    const response = await teacherService.getTeachers(schoolStore.currentSchoolId, params)
    teachers.value = response.data.teachers
    meta.value = response.data.meta
  } catch (error) {
    console.error('Gagal mengambil data guru:', error)
    generalError.value = 'Gagal memuat daftar guru.'
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchTeachers(1)
}

function handleReset() {
  searchQuery.value = ''
  fetchTeachers(1)
}

function confirmDelete(teacher: Teacher) {
  deleteTarget.value = teacher
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await teacherService.deleteTeacher(schoolStore.currentSchoolId!, deleteTarget.value.id)
    showDeleteModal.value = false
    deleteTarget.value = null
    await fetchTeachers()
  } catch (error) {
    alert('Gagal menghapus guru.')
  } finally {
    isDeleting.value = false
  }
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-muted">{{ copy.subtitle }}</p>
      </div>
      <BaseButton v-if="isHeadmaster" @click="router.push({ name: 'TeacherCreate' })">
        <template #prepend>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Tambah Guru Baru
      </BaseButton>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="flex-1 max-w-md">
        <BaseInput
          v-model="searchQuery"
          placeholder="Cari nama guru atau NIP..."
          @keyup.enter="handleSearch"
        >
          <template #prepend><Icon name="lucide:search" class="w-4 h-4" /></template>
        </BaseInput>
      </div>
      <BaseButton 
        v-if="searchQuery" 
        variant="outline" 
        size="md" 
        @click="handleReset"
        class="text-muted hover:text-primary-600"
      >
        <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
        Reset
      </BaseButton>
    </div>

    <!-- Error state -->
    <BaseAlert
      v-if="generalError"
      variant="danger"
      dismissible
      @dismiss="generalError = ''"
    >
      {{ generalError }}
    </BaseAlert>

    <!-- Table Card -->
    <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-muted/50 border-b border-border/50">
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Informasi Guru</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">NIP</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Spesialisasi</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Terdaftar Pada</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 bg-white">
            <!-- Loading State -->
            <tr v-if="isLoading" v-for="i in 5" :key="i">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <Skeleton width="3rem" height="3rem" class="rounded-2xl" />
                  <div class="space-y-2">
                    <Skeleton width="120px" height="1rem" />
                    <Skeleton width="80px" height="0.6rem" />
                  </div>
                </div>
              </td>
              <td class="px-8 py-5"><Skeleton width="100px" height="1.5rem" class="rounded-lg" /></td>
              <td class="px-8 py-5"><Skeleton width="80px" height="1rem" class="rounded-full" /></td>
              <td class="px-8 py-5"><Skeleton width="100px" height="1rem" /></td>
              <td class="px-8 py-5 text-right"><Skeleton width="40px" height="2rem" class="ml-auto" /></td>
            </tr>
            <tr v-else-if="teachers.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <!-- Case: Data truly empty -->
                <div v-if="!searchQuery" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                      <Icon name="lucide:graduation-cap" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Belum ada Guru</p>
                    <p class="text-sm text-muted">Mulai tambahkan tenaga pengajar untuk mengelola sekolah Anda.</p>
                  </div>
                  <BaseButton 
                    variant="primary" 
                    size="md" 
                    class="mt-2 w-full"
                    @click="router.push({ name: 'TeacherCreate' })"
                  >
                    Tambah Guru Pertama
                  </BaseButton>
                </div>
                <!-- Case: Search result empty -->
                <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                    <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Guru Tidak Ditemukan</p>
                    <p class="text-sm text-muted">Tidak ditemukan guru dengan kata kunci <span class="font-bold text-primary-600">"{{ searchQuery }}"</span></p>
                  </div>
                  <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                    Bersihkan Pencarian
                  </BaseButton>
                </div>
              </td>
            </tr>
            <tr 
              v-for="teacher in teachers" 
              :key="teacher.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group cursor-pointer"
              @click="router.push({ name: 'TeacherDetail', params: { id: teacher.id } })"
            >
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-primary-600 flex items-center justify-center text-white font-bold overflow-hidden shadow-lg shadow-primary-600/20 group-hover:scale-110 transition-transform">
                    <img v-if="teacher.avatar_url" :src="teacher.avatar_url" class="w-full h-full object-cover" />
                    <span v-else class="text-lg">{{ teacher.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-heading group-hover:text-primary-700 transition-colors">{{ teacher.name }}</p>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-tight">{{ teacher.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-muted rounded-lg border border-border/50">
                  <span class="text-xs font-bold text-heading">{{ teacher.nip || 'TIDAK ADA NIP' }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span v-if="teacher.specialization" class="badge bg-primary-50 text-primary-700 ring-primary-600/10">
                  {{ teacher.specialization }}
                </span>
                <span v-else class="text-xs text-muted font-medium italic">General</span>
              </td>
              <td class="px-8 py-5 text-sm font-bold text-muted">
                {{ formatDate(teacher.joined_at) }}
              </td>
              <td class="px-6 py-4 text-right" @click.stop>
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50 flex items-center gap-1.5 font-bold text-xs"
                    title="Lihat Detail"
                    @click="router.push({ name: 'TeacherDetail', params: { id: teacher.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                    <span v-if="!isHeadmaster">Detail</span>
                  </button>
                  <button 
                    v-if="isHeadmaster"
                    class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50"
                    @click="confirmDelete(teacher)"
                    title="Hapus Guru"
                  >
                      <Icon name="lucide:trash-2" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-6 py-4 bg-muted/30 border-t border-border-muted flex items-center justify-between">
        <p class="text-xs text-muted">
          Menampilkan {{ teachers.length }} dari {{ meta.total }} guru
        </p>
        <div class="flex gap-2">
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === 1"
            @click="fetchTeachers(meta.current_page - 1)"
          >
            Sebelumnya
          </BaseButton>
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === meta.last_page"
            @click="fetchTeachers(meta.current_page + 1)"
          >
            Selanjutnya
          </BaseButton>
        </div>
      </div>
    </BaseCard>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Guru?"
      :message="`Anda akan menghapus ${deleteTarget?.name || ''} dari sekolah ini. Tindakan ini tidak dapat dibatalkan.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>
