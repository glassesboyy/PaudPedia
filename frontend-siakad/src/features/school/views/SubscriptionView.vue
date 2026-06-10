<script setup lang="ts">
/**
 * SubscriptionView — Halaman manajemen paket berlangganan sekolah.
 * Menampilkan status plan, usage quota, plan comparison, dan upgrade button.
 * Role: Headmaster only.
 */
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { subscriptionService } from '@/features/school/services/subscription.service'
import type { SubscriptionInfo, SubscriptionOrder } from '@/types/subscription.types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const isUpgrading = ref(false)
const error = ref('')
const successMessage = ref('')
const info = ref<SubscriptionInfo | null>(null)
const paymentHistory = ref<SubscriptionOrder[]>([])

const usagePercentStudent = computed(() => {
  if (!info.value || !info.value.student_limit) return 0
  return Math.min(100, Math.round((info.value.student_usage / info.value.student_limit) * 100))
})

const usagePercentTeacher = computed(() => {
  if (!info.value || !info.value.teacher_limit) return 0
  return Math.min(100, Math.round((info.value.teacher_usage / info.value.teacher_limit) * 100))
})

const freeFeatures = [
  { name: 'Manajemen Siswa', included: true },
  { name: 'Manajemen Guru', included: true },
  { name: 'Manajemen Kelas', included: true },
  { name: 'Absensi', included: true },
  { name: 'Penilaian', included: true },
  { name: 'Maks. 20 Siswa', included: true },
  { name: 'Maks. 5 Guru', included: true },
  { name: 'Manajemen Keuangan', included: false },
  { name: 'Laporan / Rapor PDF', included: false },
  { name: 'Siswa Unlimited', included: false },
  { name: 'Guru Unlimited', included: false },
  { name: 'Prioritas Support', included: false },
]

const proFeatures = [
  { name: 'Manajemen Siswa', included: true },
  { name: 'Manajemen Guru', included: true },
  { name: 'Manajemen Kelas', included: true },
  { name: 'Absensi', included: true },
  { name: 'Penilaian', included: true },
  { name: 'Siswa Unlimited', included: true },
  { name: 'Guru Unlimited', included: true },
  { name: 'Manajemen Keuangan', included: true },
  { name: 'Laporan / Rapor PDF', included: true },
  { name: 'Prioritas Support', included: true },
]

onMounted(async () => {
  await fetchData()
})

async function fetchData() {
  isLoading.value = true
  error.value = ''
  try {
    const [infoRes, historyRes] = await Promise.all([
      subscriptionService.getInfo(schoolStore.currentSchoolId!),
      subscriptionService.getPaymentHistory(schoolStore.currentSchoolId!),
    ])
    info.value = (infoRes as any)
    paymentHistory.value = (historyRes as any).data
  } catch {
    error.value = 'Gagal memuat data langganan.'
  } finally {
    isLoading.value = false
  }
}

async function handleUpgrade() {
  if (isUpgrading.value) return
  isUpgrading.value = true
  error.value = ''
  try {
    const res = await subscriptionService.initiateUpgrade(schoolStore.currentSchoolId!)
    const snapToken = (res as any).snap_token

    // Open Midtrans Snap popup
    if ((window as any).snap) {
      ;(window as any).snap.pay(snapToken, {
        onSuccess: async () => {
          successMessage.value = 'Pembayaran berhasil! Paket Pro Anda aktif.'
          await fetchData()
          await schoolStore.fetchMemberships()
        },
        onPending: () => {
          successMessage.value = 'Pembayaran sedang diproses. Status akan diperbarui otomatis.'
          fetchData()
        },
        onError: () => {
          error.value = 'Pembayaran gagal. Silakan coba lagi.'
        },
        onClose: () => {
          isUpgrading.value = false
        },
      })
    } else {
      error.value = 'Midtrans belum terkonfigurasi. Hubungi administrator.'
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal memproses upgrade.'
  } finally {
    isUpgrading.value = false
  }
}

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    paid: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    pending: 'bg-amber-50 text-amber-700 border-amber-200',
    failed: 'bg-red-50 text-red-700 border-red-200',
    expired: 'bg-slate-100 text-slate-500 border-slate-200',
  }
  return colors[status] || 'bg-slate-100 text-slate-500'
}
</script>

<template>
  <div class="max-w-5xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.push({ name: 'Dashboard' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Paket Berlangganan</h1>
          <p class="text-sm text-muted">Kelola paket dan fitur sekolah Anda</p>
        </div>
      </div>
    </div>

    <!-- Success -->
    <div v-if="successMessage" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
      <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0" />
      <p class="text-sm font-medium text-emerald-800">{{ successMessage }}</p>
      <button @click="successMessage = ''" class="ml-auto text-emerald-400 hover:text-emerald-600">
        <Icon name="lucide:x" class="w-4 h-4" />
      </button>
    </div>

    <!-- Error -->
    <BaseCard v-if="error && !isLoading" class="p-12 text-center flex flex-col items-center gap-4">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-500" />
      <p class="text-lg font-bold text-slate-900">{{ error }}</p>
      <BaseButton variant="outline" @click="fetchData">Coba Lagi</BaseButton>
    </BaseCard>

    <!-- Loading -->
    <div v-else-if="isLoading" class="space-y-6">
      <BaseCard class="p-8"><Skeleton height="12rem" /></BaseCard>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <BaseCard class="p-8"><Skeleton height="20rem" /></BaseCard>
        <BaseCard class="p-8"><Skeleton height="20rem" /></BaseCard>
      </div>
    </div>

    <template v-else-if="info">
      <!-- Current Plan Status -->
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8" :class="info.is_pro ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white' : 'bg-gradient-to-r from-slate-50 to-slate-100'">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
              <div class="w-16 h-16 rounded-2xl flex items-center justify-center" :class="info.is_pro ? 'bg-white/20' : 'bg-white border border-slate-200'">
                <Icon :name="info.is_pro ? 'lucide:crown' : 'lucide:package'" class="w-8 h-8" :class="info.is_pro ? 'text-amber-300' : 'text-slate-400'" />
              </div>
              <div>
                <p class="text-xs font-black uppercase tracking-widest mb-1" :class="info.is_pro ? 'text-white/60' : 'text-slate-400'">Paket Aktif</p>
                <h2 class="text-3xl font-black" :class="info.is_pro ? 'text-white' : 'text-slate-900'">
                  {{ info.plan_label }}
                  <span v-if="info.is_pro" class="text-sm font-bold ml-2 text-amber-300">✨</span>
                </h2>
                <p v-if="info.is_pro && info.subscription_ended_at" class="text-sm mt-1" :class="info.is_pro ? 'text-white/70' : 'text-slate-500'">
                  Berlaku hingga {{ formatDate(info.subscription_ended_at) }}
                </p>
              </div>
            </div>
            <BaseButton v-if="!info.is_pro" variant="primary" size="lg" :loading="isUpgrading" @click="handleUpgrade" class="shadow-lg shadow-primary-500/30 whitespace-nowrap">
              <template #prepend><Icon name="lucide:zap" class="w-5 h-5" /></template>
              Upgrade ke Pro
            </BaseButton>
          </div>
        </div>

        <!-- Usage Quota -->
        <div v-if="!info.is_pro" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Student Quota -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <p class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <Icon name="lucide:backpack" class="w-3.5 h-3.5" /> Kuota Siswa
              </p>
              <p class="text-sm font-bold text-slate-700">{{ info.student_usage }} / {{ info.student_limit }}</p>
            </div>
            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-500" :class="usagePercentStudent >= 90 ? 'bg-red-500' : usagePercentStudent >= 70 ? 'bg-amber-500' : 'bg-emerald-500'" :style="{ width: usagePercentStudent + '%' }" />
            </div>
            <p v-if="usagePercentStudent >= 90" class="text-xs text-red-600 font-medium flex items-center gap-1">
              <Icon name="lucide:alert-triangle" class="w-3 h-3" /> Kuota hampir penuh!
            </p>
          </div>
          <!-- Teacher Quota -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <p class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <Icon name="lucide:graduation-cap" class="w-3.5 h-3.5" /> Kuota Guru
              </p>
              <p class="text-sm font-bold text-slate-700">{{ info.teacher_usage }} / {{ info.teacher_limit }}</p>
            </div>
            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-500" :class="usagePercentTeacher >= 90 ? 'bg-red-500' : usagePercentTeacher >= 70 ? 'bg-amber-500' : 'bg-emerald-500'" :style="{ width: usagePercentTeacher + '%' }" />
            </div>
            <p v-if="usagePercentTeacher >= 90" class="text-xs text-red-600 font-medium flex items-center gap-1">
              <Icon name="lucide:alert-triangle" class="w-3 h-3" /> Kuota hampir penuh!
            </p>
          </div>
        </div>
        <div v-else class="p-8 flex items-center gap-3 border-t border-white/10">
          <Icon name="lucide:infinity" class="w-5 h-5 text-primary-500" />
          <p class="text-sm font-bold text-slate-600">Siswa & Guru <span class="text-primary-600">Unlimited</span> — {{ info.student_usage }} siswa, {{ info.teacher_usage }} guru terdaftar</p>
        </div>
      </BaseCard>

      <!-- Plan Comparison -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Free Plan -->
        <BaseCard class="p-0 overflow-hidden" :class="!info.is_pro ? 'ring-2 ring-primary-500 ring-offset-2' : ''">
          <div class="p-6 bg-slate-50 border-b border-slate-100">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-black text-slate-800">Gratis</h3>
              <span v-if="!info.is_pro" class="text-[10px] font-black text-primary-600 bg-primary-50 border border-primary-100 px-3 py-1 rounded-full uppercase tracking-widest">Aktif</span>
            </div>
            <p class="text-3xl font-black text-slate-900 mt-2">Rp 0 <span class="text-sm font-medium text-slate-400">/bulan</span></p>
          </div>
          <div class="p-6 space-y-3">
            <div v-for="f in freeFeatures" :key="f.name" class="flex items-center gap-3 text-sm">
              <Icon :name="f.included ? 'lucide:check' : 'lucide:x'" class="w-4 h-4 shrink-0" :class="f.included ? 'text-emerald-500' : 'text-slate-300'" />
              <span :class="f.included ? 'text-slate-700 font-medium' : 'text-slate-400 line-through'">{{ f.name }}</span>
            </div>
          </div>
        </BaseCard>

        <!-- Pro Plan -->
        <BaseCard class="p-0 overflow-hidden" :class="info.is_pro ? 'ring-2 ring-violet-500 ring-offset-2' : ''">
          <div class="p-6 bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-black flex items-center gap-2">
                <Icon name="lucide:crown" class="w-5 h-5 text-amber-300" /> Pro
              </h3>
              <span v-if="info.is_pro" class="text-[10px] font-black text-amber-300 bg-white/20 px-3 py-1 rounded-full uppercase tracking-widest">Aktif</span>
            </div>
            <p class="text-3xl font-black mt-2">{{ info.pro_monthly_price_formatted }} <span class="text-sm font-medium text-white/60">/bulan</span></p>
          </div>
          <div class="p-6 space-y-3">
            <div v-for="f in proFeatures" :key="f.name" class="flex items-center gap-3 text-sm">
              <Icon name="lucide:check" class="w-4 h-4 shrink-0 text-violet-500" />
              <span class="text-slate-700 font-medium">{{ f.name }}</span>
            </div>
          </div>
          <div v-if="!info.is_pro" class="px-6 pb-6">
            <BaseButton variant="primary" block :loading="isUpgrading" @click="handleUpgrade" class="bg-violet-600 hover:bg-violet-700 shadow-lg shadow-violet-500/30">
              <template #prepend><Icon name="lucide:zap" class="w-4 h-4" /></template>
              Upgrade Sekarang
            </BaseButton>
          </div>
        </BaseCard>
      </div>

      <!-- Payment History -->
      <BaseCard v-if="paymentHistory.length > 0" class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
          <h3 class="text-lg font-black text-heading flex items-center gap-2">
            <Icon name="lucide:receipt" class="w-5 h-5 text-primary-500" /> Riwayat Pembayaran
          </h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-50">
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Durasi</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in paymentHistory" :key="order.id" class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-700">{{ formatDate(order.created_at) }}</td>
                <td class="px-6 py-4 font-bold text-slate-900">{{ order.amount_formatted }}</td>
                <td class="px-6 py-4 text-slate-600">{{ order.duration_months }} Bulan</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border" :class="getStatusColor(order.status)">
                    {{ order.status_label }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
