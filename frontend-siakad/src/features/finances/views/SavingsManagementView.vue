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
import BaseButton from '@/components/ui/Button/Button.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const isSubmitting = ref(false)
const error = ref('')
const success = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])
const savingsRecords = ref<FinanceRecord[]>([])
const balanceInfo = ref<{ balance: number; total_deposits: number; total_withdrawals: number } | null>(null)

const selectedClassId = ref<number | ''>('')
const selectedStudentId = ref<number | ''>('')

const showForm = ref(false)
const form = ref<SavingsPayload>({
  student_id: 0,
  amount: 0,
  transaction_type: 'deposit',
  description: '',
})

const filteredStudents = computed(() => {
  if (!selectedClassId.value) return students.value
  return students.value.filter(s => s.class_id === selectedClassId.value)
})

onMounted(async () => {
  if (schoolStore.isPro) {
    await Promise.all([fetchClasses(), fetchStudents(), fetchSavings()])
  }
  isLoading.value = false
})

async function fetchClasses() {
  try {
    const res = await api.get<{ data: ClassRoom[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/classes`)
    classes.value = res.data.data
  } catch { /* silent */ }
}

async function fetchStudents() {
  try {
    const res = await api.get<{ data: Student[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/students`)
    students.value = res.data.data
  } catch { /* silent */ }
}

async function fetchSavings() {
  try {
    const params: Record<string, unknown> = { per_page: 50 }
    if (selectedClassId.value) params.class_id = selectedClassId.value
    if (selectedStudentId.value) params.student_id = selectedStudentId.value
    const res = await financeService.getSavingsList(schoolStore.currentSchoolId!, params as any)
    savingsRecords.value = res.data.data
    balanceInfo.value = res.data.balance_info || null
  } catch {
    error.value = 'Gagal memuat data tabungan.'
  }
}

async function submitSavings() {
  if (isSubmitting.value || !form.value.student_id || !form.value.amount) return
  isSubmitting.value = true
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.storeSavings(schoolStore.currentSchoolId!, form.value)
    success.value = res.data.message
    showForm.value = false
    form.value = { student_id: 0, amount: 0, transaction_type: 'deposit', description: '' }
    await fetchSavings()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal mencatat transaksi.'
  } finally {
    isSubmitting.value = false
  }
}

function formatCurrency(val: number): string {
  return 'Rp ' + val.toLocaleString('id-ID')
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function handleFilterChange() {
  fetchSavings()
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
          <h1 class="text-2xl font-bold text-heading">Tabungan Siswa</h1>
          <p class="text-sm text-muted">Kelola setoran dan penarikan tabungan siswa</p>
        </div>
      </div>
      <BaseButton v-if="schoolStore.isPro" variant="primary" @click="showForm = true" class="shadow-lg shadow-emerald-500/20 bg-emerald-600 hover:bg-emerald-700">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Transaksi Baru
      </BaseButton>
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

      <!-- Balance Info -->
      <BaseCard v-if="balanceInfo" class="p-6 border-2 border-emerald-100 bg-emerald-50/30">
        <div class="flex items-center gap-6 flex-wrap">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
              <Icon name="lucide:wallet" class="w-6 h-6 text-emerald-600" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo</p>
              <p class="text-xl font-black text-emerald-700">{{ formatCurrency(balanceInfo.balance) }}</p>
            </div>
          </div>
          <div class="h-10 w-px bg-emerald-200 hidden md:block" />
          <div>
            <p class="text-xs text-slate-400">Total Setor</p>
            <p class="text-sm font-bold text-emerald-600">{{ formatCurrency(balanceInfo.total_deposits) }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-400">Total Tarik</p>
            <p class="text-sm font-bold text-red-500">{{ formatCurrency(balanceInfo.total_withdrawals) }}</p>
          </div>
        </div>
      </BaseCard>

      <!-- Form -->
      <BaseCard v-if="showForm" class="p-6 border-2 border-emerald-200 shadow-xl space-y-5">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-black text-heading">Transaksi Tabungan</h3>
          <button @click="showForm = false" class="text-slate-400 hover:text-slate-600"><Icon name="lucide:x" class="w-5 h-5" /></button>
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
        <div class="flex justify-end gap-3 pt-2">
          <BaseButton variant="ghost" @click="showForm = false">Batal</BaseButton>
          <BaseButton variant="primary" :loading="isSubmitting" :disabled="!form.student_id || !form.amount" @click="submitSavings" class="bg-emerald-600 hover:bg-emerald-700">
            <template #prepend><Icon name="lucide:check" class="w-4 h-4" /></template>
            Simpan
          </BaseButton>
        </div>
      </BaseCard>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3">
        <select v-model="selectedClassId" @change="handleFilterChange" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium">
          <option value="">Semua Kelas</option>
          <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <select v-model="selectedStudentId" @change="handleFilterChange" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium">
          <option value="">Semua Siswa</option>
          <option v-for="s in filteredStudents" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>

      <!-- Loading -->
      <BaseCard v-if="isLoading" class="p-6"><Skeleton height="16rem" /></BaseCard>

      <!-- Records -->
      <BaseCard v-else class="p-0 overflow-hidden border-none shadow-xl">
        <div v-if="savingsRecords.length === 0" class="p-12 text-center">
          <Icon name="lucide:piggy-bank" class="w-12 h-12 text-slate-200 mx-auto mb-3" />
          <p class="text-sm text-slate-400 font-medium">Belum ada transaksi tabungan</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-50">
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in savingsRecords" :key="r.id" class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <p class="font-bold text-slate-800">{{ r.student_name }}</p>
                  <p class="text-xs text-slate-400">{{ r.class_name || '-' }}</p>
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
                <td class="px-6 py-4 text-slate-600 text-xs">{{ r.description || '-' }}</td>
                <td class="px-6 py-4 text-xs text-slate-400">{{ formatDate(r.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
