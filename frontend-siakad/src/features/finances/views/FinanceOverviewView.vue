<script setup lang="ts">
/**
 * FinanceOverviewView — Dashboard keuangan sekolah.
 * Menampilkan ringkasan SPP, tabungan, dan transaksi terbaru.
 * Role: Headmaster (Pro Plan only)
 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { financeService } from '@/features/finances/services/finance.service'
import type { FinanceSummary } from '@/features/finances/types/finance.types'
import ProPlanGate from '@/features/finances/components/ProPlanGate.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const error = ref('')
const summary = ref<FinanceSummary | null>(null)

onMounted(async () => {
  if (schoolStore.isPro) await fetchSummary()
  else isLoading.value = false
})

async function fetchSummary() {
  isLoading.value = true
  try {
    const res = await financeService.getSummary(schoolStore.currentSchoolId!)
    summary.value = res.data
  } catch {
    error.value = 'Gagal memuat data keuangan.'
  } finally {
    isLoading.value = false
  }
}

function formatCurrency(val: number): string {
  return 'Rp ' + val.toLocaleString('id-ID')
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
  <div class="max-w-5xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.push({ name: 'Dashboard' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">Ringkasan Keuangan</h1>
        <p class="text-sm text-muted">Pantau pembayaran SPP dan tabungan siswa</p>
      </div>
    </div>

    <!-- Pro Plan Gate -->
    <ProPlanGate v-if="!schoolStore.isPro" featureName="Manajemen Keuangan" />

    <!-- Loading -->
    <div v-else-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <BaseCard v-for="i in 3" :key="i" class="p-6"><Skeleton height="5rem" /></BaseCard>
      </div>
      <BaseCard class="p-6"><Skeleton height="16rem" /></BaseCard>
    </div>

    <!-- Error -->
    <BaseCard v-else-if="error" class="p-12 text-center">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-500 mx-auto mb-4" />
      <p class="text-lg font-bold text-slate-900">{{ error }}</p>
    </BaseCard>

    <template v-else-if="summary">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <BaseCard class="p-6 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer" @click="router.push({ name: 'SppManagement' })">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center">
              <Icon name="lucide:credit-card" class="w-7 h-7 text-primary-500" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SPP Terkumpul</p>
              <p class="text-2xl font-black text-slate-900">{{ formatCurrency(summary.spp_collected) }}</p>
              <p class="text-xs text-amber-600 font-medium">{{ formatCurrency(summary.spp_pending) }} belum lunas</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer" @click="router.push({ name: 'SavingsManagement' })">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
              <Icon name="lucide:piggy-bank" class="w-7 h-7 text-emerald-500" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Tabungan</p>
              <p class="text-2xl font-black text-slate-900">{{ formatCurrency(summary.savings_balance) }}</p>
              <p class="text-xs text-slate-400 font-medium">Total keseluruhan siswa</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6 border-none shadow-lg">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center">
              <Icon name="lucide:bar-chart-3" class="w-7 h-7 text-violet-500" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pemasukan</p>
              <p class="text-2xl font-black text-slate-900">{{ formatCurrency(summary.spp_collected + summary.total_deposits) }}</p>
              <p class="text-xs text-slate-400 font-medium">SPP + Tabungan</p>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button @click="router.push({ name: 'SppManagement' })" class="flex items-center gap-4 p-5 rounded-xl bg-white border border-slate-200 hover:border-primary-400 hover:shadow-md transition-all group">
          <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center group-hover:bg-primary-100 transition-colors">
            <Icon name="lucide:credit-card" class="w-5 h-5 text-primary-500" />
          </div>
          <div class="text-left flex-1">
            <p class="text-sm font-bold text-slate-800">Pembayaran SPP</p>
            <p class="text-xs text-slate-400">Catat & kelola pembayaran bulanan</p>
          </div>
          <Icon name="lucide:chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-primary-500 transition-colors" />
        </button>

        <button @click="router.push({ name: 'SavingsManagement' })" class="flex items-center gap-4 p-5 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:shadow-md transition-all group">
          <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
            <Icon name="lucide:piggy-bank" class="w-5 h-5 text-emerald-500" />
          </div>
          <div class="text-left flex-1">
            <p class="text-sm font-bold text-slate-800">Tabungan Siswa</p>
            <p class="text-xs text-slate-400">Setor & tarik tabungan siswa</p>
          </div>
          <Icon name="lucide:chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors" />
        </button>
      </div>

      <!-- Recent Transactions -->
      <BaseCard class="p-0 border-none shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-100">
          <h3 class="text-lg font-black text-heading flex items-center gap-2">
            <Icon name="lucide:history" class="w-5 h-5 text-primary-500" /> Transaksi Terbaru
          </h3>
        </div>
        <div v-if="summary.recent_transactions.length === 0" class="p-12 text-center">
          <Icon name="lucide:inbox" class="w-12 h-12 text-slate-200 mx-auto mb-3" />
          <p class="text-sm text-slate-400 font-medium">Belum ada transaksi tercatat</p>
        </div>
        <div v-else class="divide-y divide-slate-100">
          <div v-for="tx in summary.recent_transactions" :key="tx.id" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="tx.type === 'spp' ? 'bg-primary-50' : (tx.transaction_type === 'withdrawal' ? 'bg-red-50' : 'bg-emerald-50')">
              <Icon :name="tx.type === 'spp' ? 'lucide:credit-card' : (tx.transaction_type === 'withdrawal' ? 'lucide:arrow-up-circle' : 'lucide:arrow-down-circle')" class="w-5 h-5" :class="tx.type === 'spp' ? 'text-primary-500' : (tx.transaction_type === 'withdrawal' ? 'text-red-500' : 'text-emerald-500')" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-slate-800 truncate">{{ tx.student_name }}</p>
              <p class="text-xs text-slate-400">{{ tx.type_label }} {{ tx.transaction_type_label ? '— ' + tx.transaction_type_label : '' }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm font-bold" :class="tx.transaction_type === 'withdrawal' ? 'text-red-600' : 'text-slate-900'">
                {{ tx.transaction_type === 'withdrawal' ? '-' : '+' }}{{ tx.amount_formatted }}
              </p>
              <p class="text-[10px] text-slate-400">{{ formatDate(tx.created_at) }}</p>
            </div>
          </div>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
