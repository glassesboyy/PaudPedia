<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { operatorService } from '@/features/operators/services/operator.service'
import type { Operator } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { Pagination } from '@/components/ui'
import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('operator'))
const canManageOperators = computed(() => schoolStore.canManageOperators)

const isLoading = ref(false)
const operators = ref<Operator[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 10 })
const generalError = ref('')
const searchQuery = ref('')
const filterStatus = ref('')

// Delete modal state
const showDeleteModal = ref(false)
const deleteTarget = ref<Operator | null>(null)
const isDeleting = ref(false)

// Toggle modal state
const showToggleModal = ref(false)
const toggleTarget = ref<Operator | null>(null)
const isToggling = ref(false)

const statusOptions = [
  { label: 'Semua Status', value: '' },
  { label: 'Aktif', value: 'active' },
  { label: 'Nonaktif', value: 'inactive' },
]

onMounted(() => {
  fetchOperators()
})

async function fetchOperators(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  generalError.value = ''
  try {
    const params: Record<string, any> = { page, per_page: 10 }
    if (searchQuery.value) params.search = searchQuery.value
    if (filterStatus.value) params.status = filterStatus.value

    const response = await operatorService.getOperators(schoolStore.currentSchoolId, params)
    operators.value = response.data.operators
    meta.value = response.data.meta
  } catch (error) {
    console.error('Gagal mengambil data operator:', error)
    generalError.value = 'Gagal memuat daftar operator sekolah. Silakan periksa koneksi Anda.'
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchOperators(1)
}

function handleFilterChange() {
  fetchOperators(1)
}

function handleReset() {
  searchQuery.value = ''
  filterStatus.value = ''
  fetchOperators(1)
}

const isFiltering = computed(() => {
  return !!searchQuery.value || !!filterStatus.value
})

function confirmDelete(operator: Operator) {
  deleteTarget.value = operator
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await operatorService.deleteOperator(schoolStore.currentSchoolId!, deleteTarget.value.id)
    await fetchOperators()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal menghapus data operator.'
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
    deleteTarget.value = null
  }
}

function confirmToggle(operator: Operator) {
  toggleTarget.value = operator
  showToggleModal.value = true
}

async function executeToggle() {
  if (!toggleTarget.value) return
  isToggling.value = true
  try {
    await operatorService.toggleOperatorActive(schoolStore.currentSchoolId!, toggleTarget.value.id)
    await fetchOperators()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal merubah status operator.'
  } finally {
    isToggling.value = false
    showToggleModal.value = false
    toggleTarget.value = null
  }
}

function formatDate(dateString?: string) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-muted">{{ copy.subtitle }}</p>
      </div>
      <BaseButton v-if="canManageOperators" @click="router.push({ name: 'OperatorCreate' })" class="w-full sm:w-auto">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Operator Baru
      </BaseButton>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="flex-1 max-w-md">
        <BaseInput
          v-model="searchQuery"
          placeholder="Cari nama atau email operator..."
          @keyup.enter="handleSearch"
        >
          <template #prepend><Icon name="lucide:search" class="w-4 h-4" /></template>
        </BaseInput>
      </div>
      <div class="w-full sm:w-48">
        <BaseSelect v-model="filterStatus" :options="statusOptions" @change="handleFilterChange" />
      </div>
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
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Informasi Operator</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Telepon</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest text-center">Tanggal Bergabung</th>
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
                    <Skeleton width="140px" height="1rem" />
                    <Skeleton width="100px" height="0.6rem" />
                  </div>
                </div>
              </td>
              <td class="px-8 py-5"><Skeleton width="110px" height="1rem" /></td>
              <td class="px-8 py-5 text-center"><Skeleton width="100px" height="1rem" class="mx-auto" /></td>
              <td class="px-8 py-5 text-center"><Skeleton width="60px" height="1.5rem" class="rounded-full mx-auto" /></td>
              <td class="px-8 py-5 text-right"><Skeleton width="80px" height="2rem" class="ml-auto" /></td>
            </tr>
            <!-- Empty State -->
            <tr v-else-if="operators.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <div v-if="!isFiltering" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                    <Icon name="lucide:user-cog" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Belum ada Operator</p>
                    <p class="text-sm text-muted">Mulai delegasikan tugas operasional harian dengan menambahkan operator sekolah.</p>
                  </div>
                  <BaseButton v-if="canManageOperators" variant="primary" size="md" class="mt-2 w-full" @click="router.push({ name: 'OperatorCreate' })">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Operator Pertama
      </BaseButton>
                </div>
                <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                    <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Operator Tidak Ditemukan</p>
                    <p class="text-sm text-muted">Tidak ada operator yang sesuai dengan kriteria filter aktif Anda.</p>
                  </div>
                  <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                    Bersihkan Filter
                  </BaseButton>
                </div>
              </td>
            </tr>
            <!-- Data Rows -->
            <tr
              v-else
              v-for="op in operators"
              :key="op.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group cursor-pointer"
              @click="router.push({ name: 'OperatorDetail', params: { id: op.id } })"
            >
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 border border-violet-100 flex items-center justify-center font-black text-lg shrink-0 shadow-sm group-hover:scale-105 transition-transform duration-300">
                    {{ op.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-base font-bold text-heading group-hover:text-primary-700 transition-colors">{{ op.name }}</p>
                    <p class="text-xs text-muted font-medium">{{ op.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <p class="text-sm font-medium text-body">{{ op.phone || '-' }}</p>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="text-xs font-semibold text-muted">{{ formatDate(op.joined_at) }}</span>
              </td>
              <td class="px-8 py-5 text-center">
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-extrabold tracking-wide border',
                  op.is_active
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-slate-100 text-slate-600 border-slate-200'
                ]">
                  {{ op.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-8 py-5 text-right" @click.stop>
                <div class="flex items-center justify-end gap-2">
                  <button
                    class="p-2 text-muted hover:text-primary-600 transition-colors rounded-xl hover:bg-primary-50 flex items-center gap-1.5 font-bold text-xs"
                    title="Lihat Detail"
                    @click="router.push({ name: 'OperatorDetail', params: { id: op.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                  </button>
                  <template v-if="canManageOperators">
                    <button
                      :class="[
                        'p-2 transition-colors rounded-xl font-medium text-xs flex items-center gap-1',
                        op.is_active 
                          ? 'text-amber-600 hover:bg-amber-50' 
                          : 'text-emerald-600 hover:bg-emerald-50'
                      ]"
                      :title="op.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                      @click="confirmToggle(op)"
                    >
                      <Icon :name="op.is_active ? 'lucide:user-x' : 'lucide:user-check'" class="w-4 h-4" />
                    </button>
                    <button
                      class="p-2 text-muted hover:text-danger-600 transition-colors rounded-xl hover:bg-danger-50"
                      title="Hapus Operator"
                      @click="confirmDelete(op)"
                    >
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
        @page-change="fetchOperators"
      />
    </BaseCard>

    <!-- Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Operator Sekolah?"
      :message="`Anda akan menghapus operator '${deleteTarget?.name || ''}' dari lembaga sekolah ini. Hak aksesnya ke sekolah akan dicabut sepenuhnya.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />

    <!-- Toggle Status Modal -->
    <ConfirmModal
      :show="showToggleModal"
      :title="toggleTarget?.is_active ? 'Nonaktifkan Operator?' : 'Aktifkan Operator?'"
      :message="toggleTarget?.is_active 
        ? `Operator '${toggleTarget?.name}' tidak akan bisa mengakses dasbor sekolah selama statusnya nonaktif.` 
        : `Operator '${toggleTarget?.name}' akan diberikan kembali akses penuh ke dasbor operasional sekolah.`"
      :confirm-text="toggleTarget?.is_active ? 'Nonaktifkan' : 'Aktifkan'"
      :variant="toggleTarget?.is_active ? 'danger' : 'primary'"
      :loading="isToggling"
      @confirm="executeToggle"
      @cancel="showToggleModal = false"
    />
  </div>
</template>
