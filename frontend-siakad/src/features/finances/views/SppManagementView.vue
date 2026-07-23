<script setup lang="ts">
/**
 * SppManagementView — Pencatatan pembayaran SPP siswa.
 * Role: Headmaster, Teacher (Pro Plan only)
 */
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { financeService } from '@/features/finances/services/finance.service'
import { http as api } from '@/services/http/client'
import type { FinanceRecord, SppPaymentPayload } from '@/features/finances/types/finance.types'
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
const copy = usePageCopy().getCopy('spp')

const isLoading = ref(true)
const isSubmitting = ref(false)
const error = ref('')
const success = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])
const sppRecords = ref<FinanceRecord[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 50 }) // TODO: Revert per_page to 50 after testing

// Filters
const selectedClassId = ref<number | ''>('')
const selectedMonth = ref('')
const searchQuery = ref('')
const filterPaymentMethod = ref('')
const showAdvancedFilters = ref(false)

const isFiltering = computed(() => !!searchQuery.value || !!selectedClassId.value || !!selectedMonth.value || !!filterPaymentMethod.value)

// Form
const showForm = ref(false)
const form = ref({
  class_id: '' as number | '',
  amount: 0,
  month: new Date().toISOString().slice(0, 7),
  description: '',
})

const formClassMinMonth = computed(() => {
  if (!form.value.class_id) return ''
  const cls = classes.value.find(c => c.id === form.value.class_id)
  if (!cls || !cls.academic_year) return ''
  // e.g. "2024/2025" -> starts July 2024
  const startYear = cls.academic_year.split('/')[0]
  return `${startYear}-07`
})

const formClassMaxMonth = computed(() => {
  if (!form.value.class_id) return ''
  const cls = classes.value.find(c => c.id === form.value.class_id)
  if (!cls || !cls.academic_year) return ''
  // e.g. "2024/2025" -> ends June 2025
  const endYear = cls.academic_year.split('/')[1]
  return `${endYear}-06`
})

watch(() => form.value.class_id, () => {
  if (formClassMinMonth.value && form.value.month < formClassMinMonth.value) {
    form.value.month = formClassMinMonth.value
  }
  if (formClassMaxMonth.value && form.value.month > formClassMaxMonth.value) {
    form.value.month = formClassMaxMonth.value
  }
})

onMounted(async () => {
  if (schoolStore.isPro) {
    isLoading.value = true
    await Promise.all([fetchClasses(), fetchStudents(), fetchSppRecords()])
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

const previewImageUrl = ref<string | null>(null)

function showPreview(url: string) {
  previewImageUrl.value = url
}

async function fetchSppRecords(page = 1) {
  isLoading.value = true
  try {
    const params: Record<string, unknown> = { page, per_page: 50 } // TODO: Revert to 50 after testing
    if (selectedMonth.value) params.month = selectedMonth.value
    if (selectedClassId.value) params.class_id = selectedClassId.value
    if (searchQuery.value) params.search = searchQuery.value
    if (filterPaymentMethod.value) params.payment_method = filterPaymentMethod.value
    const res = await financeService.getSppList(schoolStore.currentSchoolId!, params as any)
    sppRecords.value = (res as any).data
    meta.value = {
      current_page: (res as any).current_page,
      last_page: (res as any).last_page,
      total: (res as any).total,
      per_page: (res as any).per_page
    }
  } catch {
    error.value = 'Gagal memuat data SPP.'
  } finally {
    isLoading.value = false
  }
}

async function submitSppBatch() {
  if (isSubmitting.value || !form.value.class_id || !form.value.amount) return
  isSubmitting.value = true
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.storeSppBatch(schoolStore.currentSchoolId!, {
      class_id: Number(form.value.class_id),
      amount: form.value.amount,
      month: form.value.month,
      description: form.value.description
    })
    success.value = (res as any).message
    showForm.value = false
    form.value = { class_id: '', amount: 0, month: selectedMonth.value, description: '' }
    await fetchSppRecords()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal membuat tagihan massal.'
  } finally {
    isSubmitting.value = false
  }
}

const isUpdating = ref<number | null>(null)

const showVerificationModal = ref(false)
const verificationRecord = ref<FinanceRecord | null>(null)
const verificationPaymentMethod = ref('cash')
const verificationProofFile = ref<File | null>(null)

function openVerificationModal(record: FinanceRecord) {
  verificationRecord.value = record
  verificationPaymentMethod.value = 'cash'
  verificationProofFile.value = null
  showVerificationModal.value = true
}

function handleVerificationFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    verificationProofFile.value = target.files[0] || null
  } else {
    verificationProofFile.value = null
  }
}

async function confirmMarkAsPaid() {
  if (!verificationRecord.value) return
  
  isUpdating.value = verificationRecord.value.id
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.updateSpp(schoolStore.currentSchoolId!, verificationRecord.value.id, {
      is_paid: true,
      payment_method: verificationPaymentMethod.value as 'cash' | 'transfer',
      proof_file: verificationProofFile.value
    })
    success.value = (res as any).message
    showVerificationModal.value = false
    await fetchSppRecords()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal memverifikasi pembayaran.'
  } finally {
    isUpdating.value = null
    verificationRecord.value = null
  }
}
  

function openForm() {
  form.value.class_id = selectedClassId.value || ''
  
  // Try to use selectedMonth, but clamp to boundaries if class is selected
  let targetMonth = selectedMonth.value || new Date().toISOString().slice(0, 7)
  if (formClassMinMonth.value && targetMonth < formClassMinMonth.value) targetMonth = formClassMinMonth.value
  if (formClassMaxMonth.value && targetMonth > formClassMaxMonth.value) targetMonth = formClassMaxMonth.value
  
  form.value.month = targetMonth
  showForm.value = true
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatMonth(m: string): string {
  const [y, mo] = m.split('-')
  const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  return `${months[parseInt(mo || '0')]} ${y}`
}

function handleSearchEnter() {
  fetchSppRecords(1)
}

function handleReset() {
  searchQuery.value = ''
  selectedClassId.value = ''
  selectedMonth.value = ''
  filterPaymentMethod.value = ''
  fetchSppRecords(1)
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
      <BaseButton v-if="schoolStore.isPro && (schoolStore.canManageFinances || schoolStore.currentRole === 'teacher')" variant="primary" @click="openForm" class="shadow-lg shadow-primary-500/20 w-full sm:w-auto">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Buat Tagihan Kelas
      </BaseButton>
    </div>

    <div v-if="schoolStore.currentRole === 'headmaster'" class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
      <Icon name="lucide:info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" />
      <div>
        <p class="text-sm font-bold text-blue-800">Mode Peninjau</p>
        <p class="text-xs mt-1 text-blue-700">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya Operator dan Guru yang dapat membuat tagihan dan memverifikasi pembayaran SPP.</p>
      </div>
    </div>

    <ProPlanGate v-if="!schoolStore.isPro" featureName="Pembayaran SPP" />

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

      <!-- Form Modal -->
      <BaseModal :show="showForm" title="Buat Tagihan Kelas" @close="showForm = false">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</label>
            <select v-model="form.class_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              <option value="" disabled>Pilih kelas...</option>
              <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal Tagihan per Siswa (Rp)</label>
            <input v-model.number="form.amount" type="number" min="1" placeholder="100000" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bulan Tagihan</label>
            <input 
              v-model="form.month" 
              type="month" 
              :min="formClassMinMonth"
              :max="formClassMaxMonth"
              class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
            />
            <p v-if="form.class_id" class="text-[10px] text-slate-400 font-medium">Bulan dibatasi sesuai tahun ajaran kelas.</p>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
            <input v-model="form.description" type="text" placeholder="Catatan tambahan..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>
        </div>
        <template #footer>
          <div class="flex flex-col sm:flex-row sm:justify-end gap-3 w-full">
            <BaseButton variant="ghost" @click="showForm = false" class="w-full sm:w-auto order-last sm:order-first">Batal</BaseButton>
            <BaseButton variant="primary" :loading="isSubmitting" :disabled="!form.class_id || !form.amount" @click="submitSppBatch" class="w-full sm:w-auto">
              Buat Tagihan
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
            <select v-model="selectedClassId" @change="() => fetchSppRecords(1)" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Kelas</option>
              <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">Bulan Tagihan</label>
            <input v-model="selectedMonth" @change="() => fetchSppRecords(1)" type="month" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">Metode Pembayaran</label>
            <select v-model="filterPaymentMethod" @change="() => fetchSppRecords(1)" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Metode</option>
              <option value="cash">Tunai (Cash)</option>
              <option value="transfer">Transfer Bank</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Records Table -->
      <BaseCard class="p-0 overflow-hidden border-none shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-surface-muted/50 border-b border-border/50">
                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-400 w-1/4">Siswa</th>
                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-400">Bulan</th>
                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-400">Nominal</th>
                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-400">Metode</th>
                <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-400">Status</th>
                <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-wider text-slate-400">Aksi</th>
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
                <td class="px-6 py-4"><Skeleton width="60px" height="1rem" /></td>
                <td class="px-6 py-4"><Skeleton width="100px" height="1rem" /></td>
                <td class="px-6 py-4"><Skeleton width="80px" height="1.5rem" class="rounded-full" /></td>
                <td class="px-6 py-4"><Skeleton width="60px" height="1.5rem" class="rounded-full" /></td>
                <td class="px-6 py-4 text-right"></td>
              </tr>
              <!-- Empty States -->
              <tr v-else-if="sppRecords.length === 0">
                <td colspan="6" class="px-8 py-20 text-center">
                  <!-- Case: Data truly empty -->
                  <div v-if="!isFiltering" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                      <Icon name="lucide:inbox" class="w-10 h-10" stroke-width="1.5" />
                    </div>
                    <div>
                      <p class="text-lg font-bold text-heading">Belum ada Tagihan/Pembayaran</p>
                      <p class="text-sm text-muted">Belum ada data tagihan SPP yang diterbitkan.</p>
                    </div>
                    <BaseButton v-if="schoolStore.canManageFinances || schoolStore.currentRole === 'teacher'" variant="primary" size="md" class="mt-2 w-full" @click="openForm">
                      Buat Tagihan Kelas
                    </BaseButton>
                  </div>
                  <!-- Case: Search/Filter empty -->
                  <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                      <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                    </div>
                    <div>
                      <p class="text-lg font-bold text-heading">SPP Tidak Ditemukan</p>
                      <p class="text-sm text-muted">Tidak ditemukan pembayaran dengan kriteria filter yang aktif saat ini.</p>
                    </div>
                    <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                      Bersihkan Filter & Cari Lagi
                    </BaseButton>
                  </div>
                </td>
              </tr>
              <!-- Data Rows -->
              <tr v-else v-for="r in sppRecords" :key="r.id" class="hover:bg-primary-50/30 transition-all duration-300">
                <td class="px-6 py-4">
                  <p class="font-bold text-heading flex items-center gap-1.5">
                    <span>{{ r.student_name }}</span>
                    <span v-if="r.student_status && r.student_status !== 'active'" class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                      Arsip
                    </span>
                  </p>
                  <p class="text-xs text-muted">{{ r.class_name || '-' }}</p>
                </td>
                <td class="px-6 py-4 font-medium text-muted whitespace-nowrap">{{ formatMonth(r.month) }}</td>
                <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">{{ r.amount_formatted }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    {{ r.payment_method_label || '-' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border" :class="r.is_paid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                    {{ r.is_paid ? 'Lunas' : 'Belum Lunas' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap">
                  <div class="flex items-center justify-center gap-2">
                    <button 
                      v-if="r.proof_file_url" 
                      @click="showPreview(r.proof_file_url)" 
                      type="button" 
                      class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-2.5 py-1.5 rounded-md flex items-center gap-1.5 transition-colors border border-primary-100"
                      title="Lihat Bukti"
                    >
                      <Icon name="lucide:file-image" class="w-3.5 h-3.5" /> Bukti
                    </button>
                    <BaseButton 
                      v-if="!r.is_paid && (schoolStore.canManageFinances || schoolStore.currentRole === 'teacher')" 
                      variant="outline" 
                      size="sm" 
                      class="text-xs h-8 px-3 whitespace-nowrap" 
                      :loading="isUpdating === r.id"
                      @click="openVerificationModal(r)"
                    >
                      Tandai Lunas
                    </BaseButton>
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
          @page-change="fetchSppRecords"
        />
      </BaseCard>

      <!-- Verification Modal -->
      <BaseCard v-if="showVerificationModal && verificationRecord" class="p-6 border-2 border-primary-200 shadow-xl space-y-5 w-full max-w-sm fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50">
        <h3 class="text-lg font-bold text-heading text-center">Verifikasi Pembayaran</h3>
        <p class="text-sm text-center text-slate-500">
          Tandai tagihan SPP <span class="font-bold text-slate-700">{{ verificationRecord.student_name }}</span> bulan <span class="font-bold text-slate-700">{{ formatMonth(verificationRecord.month) }}</span> sebagai lunas?
        </p>
        
        <div class="space-y-1.5 pt-2">
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Metode Bayar</label>
          <select v-model="verificationPaymentMethod" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="cash">Tunai (Cash)</option>
            <option value="transfer">Transfer Bank</option>
          </select>
        </div>

        <div class="space-y-1.5 pt-2">
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Upload Bukti (Opsional)</label>
          <input type="file" accept="image/*,application/pdf" @change="handleVerificationFileChange" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <BaseButton variant="ghost" class="w-full" @click="showVerificationModal = false">Batal</BaseButton>
          <BaseButton variant="primary" class="w-full" :loading="isUpdating === verificationRecord.id" @click="confirmMarkAsPaid">
            Simpan
          </BaseButton>
        </div>
      </BaseCard>
      
      <!-- Overlay for Verification Modal -->
      <div v-if="showVerificationModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 transition-opacity" @click="showVerificationModal = false"></div>

    </template>

    <!-- Image Preview Modal -->
    <template v-if="previewImageUrl">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] transition-opacity" @click="previewImageUrl = null"></div>

      <!-- Modal Container -->
      <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[60] bg-white rounded-xl shadow-2xl w-[90vw] max-w-3xl flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
          <h3 class="font-bold text-heading text-base flex items-center gap-2.5">
            <Icon name="lucide:image" class="w-5 h-5 text-primary-500" /> Preview Bukti
          </h3>
          <div class="flex items-center gap-1.5">
            <a :href="previewImageUrl" target="_blank" class="text-slate-500 hover:text-primary-600 hover:bg-primary-50 p-2 rounded-full transition-colors" title="Buka Ukuran Penuh">
              <Icon name="lucide:maximize" class="w-4 h-4" />
            </a>
            <div class="w-px h-4 bg-slate-200 mx-1"></div>
            <button @click="previewImageUrl = null" class="text-slate-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-full transition-colors" title="Tutup Preview">
              <Icon name="lucide:x" class="w-5 h-5" />
            </button>
          </div>
        </div>
        <!-- Body -->
        <div class="bg-slate-100/80 p-4 md:p-6 rounded-b-xl flex items-center justify-center">
          <img :src="previewImageUrl" style="max-width: 100%; max-height: 70vh;" class="object-contain drop-shadow-sm rounded-md" alt="Preview Bukti" />
        </div>
      </div>
    </template>
  </div>
</template>
