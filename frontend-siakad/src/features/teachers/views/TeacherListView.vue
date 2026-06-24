<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { teacherService } from '@/features/teachers/services/teacher.service'
import type { Teacher } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import { Pagination } from '@/components/ui'

import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const isHeadmaster = computed(() => schoolStore.isHeadmaster)
const copy = computed(() => getCopy('teacher'))

const isLoading = ref(false)
const teachers = ref<Teacher[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 20 }) // TODO: Revert per_page to 20 after testing
const searchQuery = ref('')
const filterStatus = ref('')
const filterSpecialization = ref('')
const showAdvancedFilters = ref(false)
const generalError = ref('')

// Delete modal state
const showDeleteModal = ref(false)
const deleteTarget = ref<Teacher | null>(null)
const isDeleting = ref(false)

// Toggle Active modal state
const showToggleModal = ref(false)
const toggleTarget = ref<Teacher | null>(null)
const isToggling = ref(false)

onMounted(() => {
  fetchTeachers()
})

async function fetchTeachers(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  generalError.value = ''
  try {
    const params: Record<string, any> = { page, per_page: 20 }
    if (searchQuery.value) params.search = searchQuery.value
    if (filterStatus.value) params.status = filterStatus.value
    if (filterSpecialization.value) params.specialization = filterSpecialization.value

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

function handleFilterChange() {
  fetchTeachers(1)
}

function handleReset() {
  searchQuery.value = ''
  filterStatus.value = ''
  filterSpecialization.value = ''
  fetchTeachers(1)
}

const isFiltering = computed(() => {
  return searchQuery.value || filterStatus.value || filterSpecialization.value
})

const statusOptions = [
  { label: 'Semua Status', value: '' },
  { label: 'Aktif', value: 'active' },
  { label: 'Nonaktif', value: 'inactive' },
]

function confirmDelete(teacher: Teacher) {
  deleteTarget.value = teacher
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await teacherService.deleteTeacher(schoolStore.currentSchoolId!, deleteTarget.value.id)
    await fetchTeachers()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal menghapus guru.'
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
    deleteTarget.value = null
  }
}

function confirmToggle(teacher: Teacher) {
  toggleTarget.value = teacher
  showToggleModal.value = true
}

async function executeToggle() {
  if (!toggleTarget.value) return
  isToggling.value = true
  try {
    await teacherService.toggleTeacherActive(schoolStore.currentSchoolId!, toggleTarget.value.id)
    await fetchTeachers()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal merubah status guru.'
  } finally {
    isToggling.value = false
    showToggleModal.value = false
    toggleTarget.value = null
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
    <div class="flex flex-col space-y-4">
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
          variant="outline" 
          size="md" 
          @click="showAdvancedFilters = !showAdvancedFilters"
          :class="showAdvancedFilters ? 'bg-primary-50 text-primary-700 border-primary-200' : ''"
        >
          <template #prepend><Icon name="lucide:sliders-horizontal" class="w-4 h-4" /></template>
          Filter Lanjutan
        </BaseButton>
        <BaseButton 
          v-if="isFiltering" 
          variant="outline" 
          size="md" 
          @click="handleReset"
          class="text-muted hover:text-primary-600"
        >
          <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
          Reset
        </BaseButton>
      </div>

      <!-- Advanced Filters Panel -->
      <div v-show="showAdvancedFilters" class="p-4 bg-surface rounded-xl border border-border grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 animate-in slide-in-from-top-2 duration-200">
        <div>
          <label class="block text-xs font-semibold text-muted mb-1.5">Status Keaktifan</label>
          <BaseSelect v-model="filterStatus" :options="statusOptions" @change="handleFilterChange" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted mb-1.5">Spesialisasi</label>
          <BaseInput v-model="filterSpecialization" placeholder="Ketik spesialisasi..." @keyup.enter="handleFilterChange">
          </BaseInput>
        </div>
      </div>
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
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest text-center">Status</th>
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
              <td class="px-8 py-5"><Skeleton width="100px" height="1.5rem" /></td>
              <td class="px-8 py-5"><Skeleton width="80px" height="1rem" class="rounded-full" /></td>
              <td class="px-8 py-5 text-center"><Skeleton width="60px" height="1.5rem" class="rounded-full mx-auto" /></td>
              <td class="px-8 py-5 text-right"><Skeleton width="80px" height="2rem" class="ml-auto" /></td>
            </tr>
            <tr v-else-if="teachers.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <!-- Case: Data truly empty -->
                <div v-if="!isFiltering" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
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
                    <p class="text-sm text-muted">Tidak ditemukan guru dengan filter yang dipilih.</p>
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
                <p class="text-sm text-body font-mono">{{ teacher.nip || '-' }}</p>
              </td>
              <td class="px-8 py-5">
                <span v-if="teacher.specialization" class="badge bg-primary-50 text-primary-700 ring-primary-600/10">
                  {{ teacher.specialization }}
                </span>
                <span v-else class="text-xs text-muted font-medium italic">General</span>
              </td>
              <td class="px-8 py-5 text-center">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold border', teacher.is_active ? 'bg-success-50 text-success-700 border-success-100' : 'bg-slate-50 text-slate-700 border-slate-100']">
                  {{ teacher.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right" @click.stop>
                <div class="flex items-center justify-end gap-1">
                  <button 
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50 flex items-center justify-center"
                    title="Lihat Detail"
                    @click="router.push({ name: 'TeacherDetail', params: { id: teacher.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                  </button>
                  <button 
                    v-if="isHeadmaster"
                    class="p-1.5 text-muted transition-colors rounded-md flex items-center justify-center"
                    :class="teacher.is_active ? 'hover:text-warning-600 hover:bg-warning-50' : 'hover:text-success-600 hover:bg-success-50'"
                    @click="confirmToggle(teacher)"
                    :title="teacher.is_active ? 'Nonaktifkan Guru' : 'Aktifkan Guru'"
                  >
                    <Icon :name="teacher.is_active ? 'lucide:user-x' : 'lucide:user-check'" class="w-4 h-4" />
                  </button>
                  <button 
                    v-if="isHeadmaster"
                    class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50 flex items-center justify-center"
                    @click="confirmDelete(teacher)"
                    title="Hapus Permanen"
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
      <Pagination
        :current-page="meta.current_page"
        :last-page="meta.last_page"
        :total-items="meta.total"
        :items-per-page="meta.per_page"
        @page-change="fetchTeachers"
      />
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

    <!-- Toggle Active Confirmation Modal -->
    <ConfirmModal
      :show="showToggleModal"
      :title="toggleTarget?.is_active ? 'Nonaktifkan Guru?' : 'Aktifkan Guru?'"
      :message="toggleTarget?.is_active 
        ? `Anda akan menonaktifkan ${toggleTarget?.name}. Guru ini tidak akan bisa login, tetapi data riwayatnya tetap aman.` 
        : `Anda akan mengaktifkan kembali ${toggleTarget?.name}. Guru ini akan bisa mengakses SIAKAD lagi.`"
      :confirm-text="toggleTarget?.is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'"
      :variant="toggleTarget?.is_active ? 'warning' : 'primary'"
      :loading="isToggling"
      @confirm="executeToggle"
      @cancel="showToggleModal = false"
    />
  </div>
</template>
