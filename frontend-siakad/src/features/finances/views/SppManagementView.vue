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
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const isSubmitting = ref(false)
const error = ref('')
const success = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])
const sppRecords = ref<FinanceRecord[]>([])

// Filters
const selectedClassId = ref<number | ''>('')
const selectedMonth = ref(new Date().toISOString().slice(0, 7))
const searchQuery = ref('')

const isFiltering = computed(() => !!searchQuery.value || !!selectedClassId.value || !!selectedMonth.value)

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
    const res = await api.get<{ data: ClassRoom[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/classes`)
    classes.value = (res as any).data
  } catch { /* silent */ }
}

async function fetchStudents() {
  try {
    const res = await api.get<{ data: Student[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/students`)
    students.value = (res as any).data
  } catch { /* silent */ }
}

async function fetchSppRecords() {
  isLoading.value = true
  try {
    const params: Record<string, unknown> = { per_page: 50 }
    if (selectedMonth.value) params.month = selectedMonth.value
    if (selectedClassId.value) params.class_id = selectedClassId.value
    if (searchQuery.value) params.search = searchQuery.value
    const res = await financeService.getSppList(schoolStore.currentSchoolId!, params as any)
    sppRecords.value = (res as any).data
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

function openVerificationModal(record: FinanceRecord) {
  verificationRecord.value = record
  verificationPaymentMethod.value = 'cash'
  showVerificationModal.value = true
}

async function confirmMarkAsPaid() {
  if (!verificationRecord.value) return
  
  isUpdating.value = verificationRecord.value.id
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.updateSpp(schoolStore.currentSchoolId!, verificationRecord.value.id, {
      is_paid: true,
      payment_method: verificationPaymentMethod.value as 'cash' | 'transfer'
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
  fetchSppRecords()
}

function handleReset() {
  searchQuery.value = ''
  selectedClassId.value = ''
  selectedMonth.value = ''
  fetchSppRecords()
}
</script>

<template>
  <div class="max-w-5xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.push({ name: 'FinanceOverview' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Pembayaran SPP</h1>
          <p class="text-sm text-muted">Catat dan kelola pembayaran SPP bulanan siswa</p>
        </div>
      </div>
      <BaseButton v-if="schoolStore.isPro" variant="primary" @click="openForm" class="shadow-lg shadow-primary-500/20">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Buat Tagihan Kelas
      </BaseButton>
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
      <BaseCard v-if="showForm" class="p-6 border-2 border-primary-200 shadow-xl space-y-5">
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
        <div class="flex justify-end gap-3 pt-2">
          <BaseButton variant="ghost" @click="showForm = false">Batal</BaseButton>
          <BaseButton variant="primary" :loading="isSubmitting" :disabled="!form.class_id || !form.amount" @click="submitSppBatch">
            Buat Tagihan
          </BaseButton>
        </div>
      </BaseCard>

      <!-- Filters -->
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
        <div class="w-44">
          <select v-model="selectedClassId" @change="fetchSppRecords" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
            <option value="">Semua Kelas</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="w-44">
          <input v-model="selectedMonth" @change="fetchSppRecords" type="month" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" />
        </div>
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
                    <BaseButton variant="primary" size="md" class="mt-2 w-full" @click="openForm">
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
                  <p class="font-bold text-heading">{{ r.student_name }}</p>
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
                  <BaseButton 
                    v-if="!r.is_paid" 
                    variant="outline" 
                    size="sm" 
                    class="text-xs h-8 px-3 whitespace-nowrap" 
                    :loading="isUpdating === r.id"
                    @click="openVerificationModal(r)"
                  >
                    Tandai Lunas
                  </BaseButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
  </div>
</template>
