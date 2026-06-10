<script setup lang="ts">
/**
 * SppManagementView — Pencatatan pembayaran SPP siswa.
 * Role: Headmaster, Teacher (Pro Plan only)
 */
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { financeService } from '@/features/finances/services/finance.service'
import { http as api } from '@/services/http/client'
import type { FinanceRecord, SppPaymentPayload } from '@/features/finances/types/finance.types'
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
const sppRecords = ref<FinanceRecord[]>([])

// Filters
const selectedClassId = ref<number | ''>('')
const selectedMonth = ref(new Date().toISOString().slice(0, 7))

// Form
const showForm = ref(false)
const form = ref<SppPaymentPayload>({
  student_id: 0,
  amount: 0,
  month: new Date().toISOString().slice(0, 7),
  payment_method: 'cash',
  description: '',
})

const filteredStudents = computed(() => {
  if (!selectedClassId.value) return students.value
  return students.value.filter(s => s.class_id === selectedClassId.value)
})

onMounted(async () => {
  if (schoolStore.isPro) {
    await Promise.all([fetchClasses(), fetchStudents(), fetchSppRecords()])
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

async function fetchSppRecords() {
  try {
    const params: Record<string, unknown> = { per_page: 50 }
    if (selectedMonth.value) params.month = selectedMonth.value
    if (selectedClassId.value) params.class_id = selectedClassId.value
    const res = await financeService.getSppList(schoolStore.currentSchoolId!, params as any)
    sppRecords.value = res.data.data
  } catch {
    error.value = 'Gagal memuat data SPP.'
  }
}

async function submitSpp() {
  if (isSubmitting.value || !form.value.student_id || !form.value.amount) return
  isSubmitting.value = true
  error.value = ''
  success.value = ''
  try {
    const res = await financeService.storeSpp(schoolStore.currentSchoolId!, form.value)
    success.value = res.data.message
    showForm.value = false
    form.value = { student_id: 0, amount: 0, month: selectedMonth.value, payment_method: 'cash', description: '' }
    await fetchSppRecords()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal mencatat pembayaran.'
  } finally {
    isSubmitting.value = false
  }
}

function openForm() {
  form.value.month = selectedMonth.value
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
        Catat Pembayaran
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
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-black text-heading">Catat Pembayaran SPP</h3>
          <button @click="showForm = false" class="text-slate-400 hover:text-slate-600"><Icon name="lucide:x" class="w-5 h-5" /></button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Siswa</label>
            <select v-model="form.student_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              <option :value="0" disabled>Pilih siswa...</option>
              <option v-for="s in filteredStudents" :key="s.id" :value="s.id">{{ s.name }} ({{ s.nisn || '-' }})</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal (Rp)</label>
            <input v-model.number="form.amount" type="number" min="1" placeholder="100000" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bulan</label>
            <input v-model="form.month" type="month" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Metode Bayar</label>
            <select v-model="form.payment_method" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              <option value="cash">Tunai</option>
              <option value="transfer">Transfer</option>
            </select>
          </div>
          <div class="md:col-span-2 space-y-1.5">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan (Opsional)</label>
            <input v-model="form.description" type="text" placeholder="Catatan tambahan..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <BaseButton variant="ghost" @click="showForm = false">Batal</BaseButton>
          <BaseButton variant="primary" :loading="isSubmitting" :disabled="!form.student_id || !form.amount" @click="submitSpp">
            <template #prepend><Icon name="lucide:check" class="w-4 h-4" /></template>
            Simpan
          </BaseButton>
        </div>
      </BaseCard>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3">
        <select v-model="selectedClassId" @change="fetchSppRecords" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium">
          <option value="">Semua Kelas</option>
          <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <input v-model="selectedMonth" @change="fetchSppRecords" type="month" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium" />
      </div>

      <!-- Loading -->
      <BaseCard v-if="isLoading" class="p-6"><Skeleton height="16rem" /></BaseCard>

      <!-- Records Table -->
      <BaseCard v-else class="p-0 overflow-hidden border-none shadow-xl">
        <div v-if="sppRecords.length === 0" class="p-12 text-center">
          <Icon name="lucide:inbox" class="w-12 h-12 text-slate-200 mx-auto mb-3" />
          <p class="text-sm text-slate-400 font-medium">Belum ada data pembayaran SPP untuk periode ini</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-50">
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Bulan</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in sppRecords" :key="r.id" class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <p class="font-bold text-slate-800">{{ r.student_name }}</p>
                  <p class="text-xs text-slate-400">{{ r.class_name || '-' }}</p>
                </td>
                <td class="px-6 py-4 font-medium text-slate-600">{{ formatMonth(r.month) }}</td>
                <td class="px-6 py-4 font-bold text-slate-900">{{ r.amount_formatted }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <Icon :name="r.payment_method === 'cash' ? 'lucide:banknote' : 'lucide:building'" class="w-3.5 h-3.5" />
                    {{ r.payment_method_label || '-' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border" :class="r.is_paid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                    {{ r.is_paid ? 'Lunas' : 'Belum Lunas' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-400">{{ formatDate(r.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
