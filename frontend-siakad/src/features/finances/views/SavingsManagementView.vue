<script setup lang="ts">
/**
 * SavingsManagementView — Pencatatan tabungan siswa (setor & tarik).
 * Role: Headmaster, Teacher (Pro Plan only)
 */
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { financeService } from '@/features/finances/services/finance.service'
import { http as api } from '@/services/http/client'
import type { FinanceRecord, SavingsPayload } from '@/features/finances/types/finance.types'
import type { ClassRoom, Student } from '@/types'
import ProPlanGate from '@/features/finances/components/ProPlanGate.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseModal from '@/components/ui/Modal/Modal.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { Pagination } from '@/components/ui'
import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const copy = usePageCopy().getCopy('savings')

const isLoading = ref(true)
const isSubmitting = ref(false)
const error = ref('')
const formError = ref('')
const success = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])
const savingsRecords = ref<FinanceRecord[]>([])
const balanceInfo = ref<{ balance: number; total_deposits: number; total_withdrawals: number } | null>(null)
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 50 }) // TODO: Revert per_page to 50 after testing

const searchQuery = ref('')
const selectedClassId = ref<number | ''>('')
const filterTransactionType = ref('')
const showAdvancedFilters = ref(false)

const isFiltering = computed(() => !!searchQuery.value || !!selectedClassId.value || !!filterTransactionType.value)

const showForm = ref(false)
const form = ref<SavingsPayload>({
  student_id: 0,
  amount: 0,
  transaction_type: 'deposit',
  description: '',
})

const filteredStudents = computed(() => {
  const activeStudents = students.value.filter(s => !s.status || s.status === 'active')
  if (!selectedClassId.value) return activeStudents
  return activeStudents.filter(s => s.class_id === selectedClassId.value)
})

onMounted(async () => {
  if (schoolStore.isPro) {
    isLoading.value = true
    await Promise.all([fetchClasses(), fetchStudents(), fetchSavings()])
  }
  isLoading.value = false
})

async function fetchClasses() {
  try {
    const res = await api.get<{ data: ClassRoom[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/classes?all_classes=1&per_page=100`)
    classes.value = (res as any).data
  } catch { /* silent */ }
}

async function fetchStudents() {
  try {
    const res = await api.get<{ data: Student[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/students?only_my_class=true`)
    students.value = (res as any).data
  } catch { /* silent */ }
}

async function fetchSavings(page = 1) {
  isLoading.value = true
  try {
    const params: Record<string, unknown> = { page, per_page: 50 } // TODO: Revert to 50 after testing
    if (selectedClassId.value) params.class_id = selectedClassId.value
    if (searchQuery.value) params.search = searchQuery.value
    if (filterTransactionType.value) params.transaction_type = filterTransactionType.value
    const res = await financeService.getSavingsList(schoolStore.currentSchoolId!, params as any)
    savingsRecords.value = (res as any).data
    balanceInfo.value = (res as any).balance_info || null
    meta.value = {
      current_page: (res as any).current_page,
      last_page: (res as any).last_page,
      total: (res as any).total,
      per_page: (res as any).per_page
    }
  } catch {
    error.value = 'Gagal memuat data tabungan.'
  } finally {
    isLoading.value = false
  }
}

async function submitSavings() {
  if (isSubmitting.value || !form.value.student_id || !form.value.amount) return
  isSubmitting.value = true
  formError.value = ''
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.storeSavings(schoolStore.currentSchoolId!, form.value)
    success.value = (res as any).message
    showForm.value = false
    form.value = { student_id: 0, amount: 0, transaction_type: 'deposit', description: '' }
    await fetchSavings()
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Gagal mencatat transaksi.'
  } finally {
    isSubmitting.value = false
  }
}

function closeForm() {
  showForm.value = false
  formError.value = ''
}

function formatCurrency(val: number): string {
  return 'Rp ' + val.toLocaleString('id-ID')
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function handleFilterChange() {
  fetchSavings(1)
}

function handleSearchEnter() {
  fetchSavings(1)
}

function handleReset() {
  searchQuery.value = ''
  selectedClassId.value = ''
  filterTransactionType.value = ''
  fetchSavings(1)
}

</script>

<template>
  <div class="animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
          <p class="text-sm text-muted">{{ copy.subtitle }}</p>
        </div>
      </div>
      <BaseButton v-if="schoolStore.isPro && (schoolStore.canManageFinances || schoolStore.currentRole === 'teacher')" variant="primary" @click="showForm = true" class="shadow-lg shadow-emerald-500/20 bg-emerald-600 hover:bg-emerald-700 w-full sm:w-auto">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Transaksi Baru
      </BaseButton>
    </div>

    <div v-if="schoolStore.currentRole === 'headmaster'" class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
      <Icon name="lucide:info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" />
      <div>
        <p class="text-sm font-bold text-blue-800">Mode Peninjau</p>
        <p class="text-xs mt-1 text-blue-700">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya Operator dan Guru yang dapat mencatat setoran atau penarikan tabungan siswa.</p>
      </div>
    </div>

    <ProPlanGate v-if="!schoolStore.isPro" featureName="Tabungan Siswa" />

    <template v-else>
      <!-- Alerts -->
      <div v-if="success" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
        <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0" />
        <p class="text-sm font-medium text-emerald-800">{{ success }}</p>
        <button @click="success = ''" class="ml-auto text-emerald-400 hover:text-emerald-600"><Icon name="lucide:x" class="w-4 h-4" /></button>
      </div>
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <Icon name="lucide:alert-circle" class="w-5 h-5 text-red-600 shrink-0" />
        <p class="text-sm font-medium text-red-800">{{ error }}</p>
        <button @click="error = ''" class="ml-auto text-red-400 hover:text-red-600"><Icon name="lucide:x" class="w-4 h-4" /></button>
      </div>

      <!-- Form -->
      <BaseModal :show="showForm" title="Transaksi Baru" @close="closeForm">
        <div v-if="formError" class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-3">
          <Icon name="lucide:alert-circle" class="w-4 h-4 text-red-600 shrink-0" />
          <p class="text-xs font-medium text-red-800">{{ formError }}</p>
          <button @click="formError = ''" class="ml-auto text-red-400 hover:text-red-600"><Icon name="lucide:x" class="w-3 h-3" /></button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Siswa</label>
            <select v-model="form.student_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
              <option :value="0" disabled>Pilih siswa...</option>
              <option v-for="s in filteredStudents" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Transaksi</label>
            <select v-model="form.transaction_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
              <option value="deposit">Setoran</option>
              <option value="withdrawal">Penarikan</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal (Rp)</label>
            <input v-model.number="form.amount" type="number" min="1" placeholder="50000" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan (Opsional)</label>
            <input v-model="form.description" type="text" placeholder="Catatan transaksi..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
          </div>
        </div>
        <template #footer>
          <div class="flex justify-end gap-3">
            <BaseButton variant="ghost" @click="closeForm">Batal</BaseButton>
            <BaseButton variant="primary" :loading="isSubmitting" :disabled="!form.student_id || !form.amount" @click="submitSavings" class="bg-emerald-600 hover:bg-emerald-700">
              Simpan
            </BaseButton>
          </div>
        </template>
      </BaseModal>

      <!-- Search & Filters -->
      <div class="flex flex-col space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <div class="flex-1 max-w-sm">
            <BaseInput
              v-model="searchQuery"
              placeholder="Cari nama siswa..."
              @keyup.enter="handleSearchEnter"
            >
              <template #prepend><Icon name="lucide:search" class="w-4 h-4" /></template>
            </BaseInput>
          </div>
          <BaseButton 
            variant="outline" 
            size="md" 
            @click="showAdvancedFilters = !showAdvancedFilters"
            :class="showAdvancedFilters ? 'bg-primary-50 text-primary-700 border-primary-200 h-[42px]' : 'h-[42px]'"
          >
            <template #prepend><Icon name="lucide:sliders-horizontal" class="w-4 h-4" /></template>
            Filter Lanjutan
          </BaseButton>
          <BaseButton 
            v-if="isFiltering" 
            variant="outline" 
            size="md" 
            @click="handleReset"
            class="text-muted hover:text-primary-600 h-[42px]"
          >
            <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
            Reset
          </BaseButton>
        </div>

        <!-- Advanced Filters Panel -->
        <div v-show="showAdvancedFilters" class="p-4 bg-surface rounded-xl border border-border grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 animate-in slide-in-from-top-2 duration-200">
          <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">Kelas</label>
            <select v-model="selectedClassId" @change="handleFilterChange" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Kelas</option>
              <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">Jenis Transaksi</label>
            <select v-model="filterTransactionType" @change="handleFilterChange" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Transaksi</option>
              <option value="deposit">Setoran</option>
              <option value="withdrawal">Penarikan</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Records -->
      <BaseCard class="p-0 overflow-hidden border-none shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-surface-muted/50 border-b border-border/50">
                <th class="px-6 py-5 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Siswa</th>
                <th class="px-6 py-5 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Jenis</th>
                <th class="px-6 py-5 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Nominal</th>
                <th class="px-6 py-5 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Catatan</th>
                <th class="px-6 py-5 text-left text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Tanggal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/50 bg-white">
              <!-- Loading State -->
              <tr v-if="isLoading" v-for="i in 5" :key="i">
                <td class="px-6 py-4">
                  <div class="space-y-2">
                    <Skeleton width="120px" height="0.875rem" />
                    <Skeleton width="80px" height="0.6rem" />
                  </div>
                </td>
                <td class="px-6 py-4"><Skeleton width="80px" height="1.5rem" class="rounded-full" /></td>
                <td class="px-6 py-4"><Skeleton width="100px" height="1rem" /></td>
                <td class="px-6 py-4"><Skeleton width="150px" height="0.875rem" /></td>
                <td class="px-6 py-4"><Skeleton width="80px" height="0.875rem" /></td>
              </tr>
              <!-- Empty States -->
              <tr v-else-if="savingsRecords.length === 0">
                <td colspan="5" class="px-8 py-20 text-center">
                  <!-- Case: Data truly empty -->
                  <div v-if="!isFiltering" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                      <Icon name="lucide:piggy-bank" class="w-10 h-10" stroke-width="1.5" />
                    </div>
                    <div>
                      <p class="text-lg font-bold text-heading">Belum ada Transaksi</p>
                      <p class="text-sm text-muted">Belum ada catatan transaksi tabungan siswa yang dicatat.</p>
                    </div>
                    <BaseButton v-if="schoolStore.canManageFinances || schoolStore.currentRole === 'teacher'" variant="primary" size="md" class="mt-2 w-full" @click="showForm = true">
                      Buat Transaksi Pertama
                    </BaseButton>
                  </div>
                  <!-- Case: Search/Filter empty -->
                  <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                      <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                    </div>
                    <div>
                      <p class="text-lg font-bold text-heading">Transaksi Tidak Ditemukan</p>
                      <p class="text-sm text-muted">Tidak ditemukan transaksi dengan kriteria filter yang aktif saat ini.</p>
                    </div>
                    <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                      Bersihkan Filter & Cari Lagi
                    </BaseButton>
                  </div>
                </td>
              </tr>
              <!-- Data Rows -->
              <tr v-else v-for="r in savingsRecords" :key="r.id" class="hover:bg-primary-50/30 transition-all duration-300">
                <td class="px-6 py-4">
                  <p class="font-bold text-heading flex items-center gap-1.5">
                    <span>{{ r.student_name }}</span>
                    <span v-if="r.student_status && r.student_status !== 'active'" class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                      Arsip
                    </span>
                  </p>
                  <p class="text-xs text-muted">{{ r.class_name || '-' }}</p>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold" :class="r.transaction_type === 'deposit' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                    <Icon :name="r.transaction_type === 'deposit' ? 'lucide:arrow-down-circle' : 'lucide:arrow-up-circle'" class="w-3.5 h-3.5" />
                    {{ r.transaction_type_label }}
                  </span>
                </td>
                <td class="px-6 py-4 font-bold" :class="r.transaction_type === 'withdrawal' ? 'text-red-600' : 'text-emerald-700'">
                  {{ r.transaction_type === 'withdrawal' ? '-' : '+' }}{{ r.amount_formatted }}
                </td>
                <td class="px-6 py-4 text-muted text-xs">{{ r.description || '-' }}</td>
                <td class="px-6 py-4 text-xs font-bold text-muted">{{ formatDate(r.created_at) }}</td>
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
          @page-change="fetchSavings"
        />
      </BaseCard>
    </template>
  </div>
</template>
