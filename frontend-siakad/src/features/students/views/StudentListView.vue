<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import { classService } from '@/features/classes/services/class.service'
import type { Student, ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import { Pagination } from '@/components/ui'

import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('student'))

const isLoading = ref(false)
const students = ref<Student[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 20 }) // TODO: Revert per_page to 20 after testing
const generalError = ref('')
const searchQuery = ref('')
const filterClass = ref('')
const filterStatus = ref('')
const filterGender = ref('')
const showAdvancedFilters = ref(false)

// Classes for filter
const classes = ref<ClassRoom[]>([])

// Delete modal
const showDeleteModal = ref(false)
const deleteTarget = ref<Student | null>(null)
const isDeleting = ref(false)

const canManageStudents = computed(() => schoolStore.canManageStudents)

const statusLabels: Record<string, string> = {
  active: 'Aktif',
  graduated: 'Lulus',
  transferred: 'Pindah',
}

const statusColors: Record<string, string> = {
  active: 'bg-emerald-50 text-emerald-700 border-emerald-100',
  graduated: 'bg-primary-50 text-primary-700 border-primary-100',
  transferred: 'bg-amber-50 text-amber-700 border-amber-100',
}

const genderLabels: Record<string, string> = {
  male: 'Laki-laki',
  female: 'Perempuan',
}

onMounted(async () => {
  await Promise.all([fetchStudents(), fetchClasses()])
})

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  try {
    const response = await classService.getClasses(schoolStore.currentSchoolId, { per_page: 100, all_classes: 1 })
    classes.value = response.data
  } catch { /* ignore */ }
}

async function fetchStudents(page = 1) {
  if (!schoolStore.currentSchoolId) return
  isLoading.value = true
  generalError.value = ''
  try {
    const params: Record<string, any> = { page, per_page: 20 } // TODO: Revert to 20 after testing
    if (searchQuery.value) params.search = searchQuery.value
    if (filterClass.value) params.class_id = filterClass.value
    if (filterStatus.value) params.status = filterStatus.value
    if (filterGender.value) params.gender = filterGender.value

    const response = await studentService.getStudents(schoolStore.currentSchoolId, params)
    students.value = response.data
    meta.value = response.meta
  } catch {
    generalError.value = 'Gagal memuat daftar siswa.'
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchStudents(1)
}

function handleFilterChange() {
  fetchStudents(1)
}

function handleReset() {
  searchQuery.value = ''
  filterClass.value = ''
  filterStatus.value = ''
  filterGender.value = ''
  fetchStudents(1)
}

const isFiltering = computed(() => {
  return searchQuery.value || filterClass.value || filterStatus.value || filterGender.value
})

function confirmDelete(student: Student) {
  deleteTarget.value = student
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await studentService.deleteStudent(schoolStore.currentSchoolId!, deleteTarget.value.id)
    await fetchStudents()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal menghapus data siswa.'
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
    deleteTarget.value = null
  }
}

const classOptions = computed(() => [
  { value: '', label: 'Semua Kelas' },
  ...classes.value.map(c => ({ value: String(c.id), label: c.name })),
])

const statusOptions = [
  { label: 'Semua Status', value: '' },
  { label: 'Aktif', value: 'active' },
  { label: 'Lulus', value: 'graduated' },
  { label: 'Pindah', value: 'transferred' },
]

const genderOptions = [
  { label: 'Semua Kelamin', value: '' },
  { label: 'Laki-laki', value: 'male' },
  { label: 'Perempuan', value: 'female' },
]


</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-muted">{{ copy.subtitle }}</p>
      </div>
      <BaseButton v-if="canManageStudents" @click="router.push({ name: 'StudentCreate' })" class="w-full sm:w-auto">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Siswa
      </BaseButton>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col space-y-4">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="flex-1 max-w-sm">
          <BaseInput v-model="searchQuery" placeholder="Cari nama atau NISN..." @keyup.enter="handleSearch">
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
      <div v-show="showAdvancedFilters" class="p-4 bg-surface rounded-xl border border-border grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 animate-in slide-in-from-top-2 duration-200">
        <div>
          <label class="block text-xs font-semibold text-muted mb-1.5">Kelas</label>
          <BaseSelect v-model="filterClass" :options="classOptions" @change="handleFilterChange" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted mb-1.5">Status Siswa</label>
          <BaseSelect v-model="filterStatus" :options="statusOptions" @change="handleFilterChange" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted mb-1.5">Jenis Kelamin</label>
          <BaseSelect v-model="filterGender" :options="genderOptions" @change="handleFilterChange" />
        </div>
      </div>
    </div>

    <!-- Error -->
    <BaseAlert v-if="generalError" variant="danger" dismissible @dismiss="generalError = ''">{{ generalError }}</BaseAlert>

    <!-- Table -->
    <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-muted/50 border-b border-border/50">
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Siswa</th>
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">NISN</th>
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Kelas</th>
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-center">Jenis Kelamin</th>
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-center">Status</th>
              <th class="px-6 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 bg-white">
            <tr v-if="isLoading" v-for="i in 5" :key="i">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <Skeleton width="2.5rem" height="2.5rem" class="rounded-xl" />
                  <div class="space-y-2">
                    <Skeleton width="140px" height="0.875rem" />
                    <Skeleton width="80px" height="0.6rem" />
                  </div>
                </div>
              </td>
              <td class="px-6 py-4"><Skeleton width="90px" height="0.875rem" /></td>
              <td class="px-6 py-4"><Skeleton width="110px" height="0.875rem" /></td>
              <td class="px-6 py-4 text-center"><Skeleton width="70px" height="0.875rem" class="mx-auto" /></td>
              <td class="px-6 py-4 text-center"><Skeleton width="60px" height="1.25rem" class="rounded-full mx-auto" /></td>
              <td class="px-6 py-4 text-right"><Skeleton width="40px" height="2rem" class="ml-auto" /></td>
            </tr>
            <tr v-else-if="students.length === 0">
              <td colspan="6" class="px-8 py-20 text-center">
                <!-- Case: Data truly empty -->
                <div v-if="!isFiltering" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                    <Icon name="lucide:backpack" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Belum ada Siswa</p>
                    <p class="text-sm text-muted" v-if="canManageStudents">Mulai tambahkan data peserta didik ke sistem.</p>
                    <p class="text-sm text-muted" v-else>Belum ada data siswa yang terdaftar di sekolah.</p>
                  </div>
                  <BaseButton v-if="canManageStudents" variant="primary" size="md" class="mt-2 w-full" @click="router.push({ name: 'StudentCreate' })">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Siswa Pertama
      </BaseButton>
                </div>
                <!-- Case: Filter result empty -->
                <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                    <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Siswa Tidak Ditemukan</p>
                    <p class="text-sm text-muted">Tidak ditemukan data siswa dengan kriteria filter aktif saat ini.</p>
                  </div>
                  <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                    Bersihkan Filter & Cari Lagi
                  </BaseButton>
                </div>
              </td>
            </tr>
            <tr
              v-else
              v-for="s in students"
              :key="s.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group cursor-pointer"
              @click="router.push({ name: 'StudentDetail', params: { id: s.id } })"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                    <img v-if="s.photo_url" :src="s.photo_url" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                      <Icon name="lucide:user" class="w-5 h-5" />
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-heading group-hover:text-primary-700 transition-colors">{{ s.name }}</p>
                    <p class="text-xs text-muted">{{ s.parent?.father_name || s.parent?.mother_name || '-' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-body font-mono">{{ s.nisn || '-' }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-body">{{ s.class?.name || 'Belum ada kelas' }}</p>
              </td>
              <td class="px-6 py-4 text-center">
                <span class="text-sm text-body">{{ genderLabels[s.gender] || s.gender }}</span>
              </td>
              <td class="px-6 py-4 text-center">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold border', statusColors[s.status] || 'bg-slate-50 text-slate-700 border-slate-100']">
                  {{ statusLabels[s.status] || s.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right" @click.stop>
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50 flex items-center gap-1.5 font-bold text-xs"
                    title="Lihat Detail"
                    @click="router.push({ name: 'StudentDetail', params: { id: s.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                  </button>
                  <template v-if="canManageStudents">
                    <button class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50" title="Edit" @click="router.push({ name: 'StudentEdit', params: { id: s.id } })">
                      <Icon name="lucide:edit-3" class="w-4 h-4" />
                    </button>
                    <button class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50" title="Hapus" @click="confirmDelete(s)">
                      <Icon name="lucide:trash-2" class="w-4 h-4" />
                    </button>
                  </template>
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
        @page-change="fetchStudents"
      />
    </BaseCard>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Data Siswa?"
      :message="`Anda akan menghapus data siswa '${deleteTarget?.name || ''}'. Jika siswa memiliki riwayat akademik, presensi, atau keuangan, penghapusan akan diproteksi oleh sistem.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>
