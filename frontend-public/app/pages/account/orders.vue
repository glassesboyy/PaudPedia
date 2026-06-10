<script setup lang="ts">
/**
 * Transaction History Page (FR-UA-12)
 * Route: /account/orders
 *
 * Displays list of user orders with status, items, amounts and detail modal.
 */
import { dashboardService } from '~~/services'
import type { Transaction } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth'],
})

useSeo({ title: 'Riwayat Transaksi' })

const transactions = ref<Transaction[]>([])
const isLoading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

// Filter
const activeFilter = ref<'all' | 'paid' | 'pending' | 'failed'>('all')

const statusFilters = [
  { key: 'all' as const, label: 'Semua', icon: 'lucide:layers' },
  { key: 'paid' as const, label: 'Berhasil', icon: 'lucide:check-circle' },
  { key: 'pending' as const, label: 'Menunggu', icon: 'lucide:clock' },
  { key: 'failed' as const, label: 'Gagal', icon: 'lucide:x-circle' },
]

const filteredTransactions = computed(() => {
  if (activeFilter.value === 'all') return transactions.value
  if (activeFilter.value === 'failed')
    return transactions.value.filter((t) => ['failed', 'cancelled', 'expired'].includes(t.status))
  return transactions.value.filter((t) => t.status === activeFilter.value)
})

// Detail modal
const selectedTx = ref<Transaction | null>(null)
const showDetail = ref(false)

async function fetchTransactions(page = 1) {
  isLoading.value = true
  error.value = ''
  try {
    const res = await dashboardService.getTransactions({ page, per_page: 10 })
    transactions.value = res.data
    currentPage.value = res.meta.current_page
    totalPages.value = res.meta.last_page
    totalItems.value = res.meta.total
  } catch {
    error.value = 'Gagal memuat riwayat transaksi.'
  } finally {
    isLoading.value = false
  }
}

function formatCurrency(val: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val)
}

function statusClasses(color: string): string {
  const map: Record<string, string> = {
    green: 'bg-success-50 text-success-700 ring-success-200',
    yellow: 'bg-warning-50 text-warning-700 ring-warning-200',
    red: 'bg-danger-50 text-danger-700 ring-danger-200',
    gray: 'bg-surface-muted text-muted ring-border',
  }
  return map[color] ?? map.gray ?? ''
}

function itemTypeIcon(type: string): string {
  const map: Record<string, string> = {
    course: 'lucide:book-open',
    webinar: 'lucide:video',
    product: 'lucide:package',
  }
  return map[type] || 'lucide:box'
}

function itemTypeColor(type: string): string {
  const map: Record<string, string> = {
    course: 'bg-primary-50 text-primary-500',
    webinar: 'bg-secondary-50 text-secondary-500',
    product: 'bg-success-50 text-success-500',
  }
  return map[type] || 'bg-surface-muted text-muted'
}

function openDetail(tx: Transaction) {
  selectedTx.value = tx
  showDetail.value = true
}

function openPaymentUrl(url: string | null | undefined) {
  if (url) window.open(url, '_blank')
}

onMounted(() => fetchTransactions())
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold text-heading">Riwayat Transaksi</h1>
        <span
          v-if="!isLoading && totalItems > 0"
          class="text-xs font-medium text-muted bg-surface-muted px-2.5 py-1 rounded-full"
        >
          {{ totalItems }} transaksi
        </span>
      </div>
      <p class="text-sm text-muted">Riwayat pembelian kursus, produk, dan webinar Anda.</p>
    </div>

    <!-- Filters -->
    <div v-if="!isLoading && transactions.length > 0" class="flex flex-wrap gap-2 mb-5">
      <button
        v-for="f in statusFilters"
        :key="f.key"
        type="button"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200"
        :class="activeFilter === f.key
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-surface-muted text-muted hover:bg-surface-sunken hover:text-body'"
        @click="activeFilter = f.key"
      >
        <Icon :name="f.icon" class="w-3.5 h-3.5" />
        {{ f.label }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="space-y-4">
      <div v-for="n in 5" :key="n" class="rounded-2xl border border-border bg-surface p-5">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <USkeleton variant="text" class="w-28 h-4" />
            <USkeleton variant="rounded" class="w-16 h-5" />
          </div>
          <USkeleton variant="text" class="w-20 h-3" />
        </div>
        <div class="flex gap-2 mb-3">
          <USkeleton variant="rounded" class="w-32 h-7" />
          <USkeleton variant="rounded" class="w-24 h-7" />
        </div>
        <div class="flex items-center justify-between pt-3 border-t border-border-muted">
          <USkeleton variant="text" class="w-20 h-3" />
          <USkeleton variant="text" class="w-24 h-4" />
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="rounded-2xl border border-danger-200 bg-danger-50/50 p-8 text-center">
      <div class="w-14 h-14 rounded-2xl bg-danger-100 flex items-center justify-center mx-auto mb-4">
        <Icon name="lucide:wifi-off" class="w-7 h-7 text-danger-500" />
      </div>
      <h3 class="text-base font-semibold text-heading mb-1">Gagal Memuat Data</h3>
      <p class="text-sm text-muted mb-5">{{ error }}</p>
      <UButton variant="primary" size="sm" @click="fetchTransactions(currentPage)">
        <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
        Coba Lagi
      </UButton>
    </div>

    <!-- Empty -->
    <div v-else-if="!transactions.length" class="relative overflow-hidden rounded-2xl border border-border bg-surface p-10 text-center">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-50/40 via-transparent to-warning-50/30 pointer-events-none" />
      <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-primary-50 flex items-center justify-center mx-auto mb-5 shadow-sm">
          <Icon name="lucide:receipt" class="w-10 h-10 text-primary-400" />
        </div>
        <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Transaksi</h3>
        <p class="text-sm text-muted mb-6 max-w-sm mx-auto">
          Anda belum melakukan pembelian apapun. Jelajahi kursus dan produk kami!
        </p>
        <NuxtLink to="/courses">
          <UButton variant="primary" size="md">
            <Icon name="lucide:shopping-bag" class="w-4 h-4 mr-1.5" />
            Mulai Belanja
          </UButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Filter Empty -->
    <div
      v-else-if="filteredTransactions.length === 0"
      class="rounded-2xl border border-border bg-surface-muted/30 p-10 text-center"
    >
      <Icon name="lucide:search-x" class="w-10 h-10 text-muted mx-auto mb-3" />
      <p class="text-sm text-muted">Tidak ada transaksi dengan filter ini.</p>
    </div>

    <!-- Transaction List -->
    <template v-else>
      <div class="space-y-3">
        <button
          v-for="tx in filteredTransactions"
          :key="tx.id"
          type="button"
          class="w-full text-left rounded-2xl border border-border bg-surface p-5 hover:shadow-medium hover:border-primary-200 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
          @click="openDetail(tx)"
        >
          <!-- Header row -->
          <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:receipt" class="w-4 h-4 text-primary-500" />
              </div>
              <div>
                <span class="text-sm font-semibold text-heading block">{{ tx.order_number }}</span>
                <span class="text-[11px] text-muted">{{ tx.created_date }}</span>
              </div>
            </div>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset"
              :class="statusClasses(tx.status_color)"
            >
              {{ tx.status_label }}
            </span>
          </div>

          <!-- Items summary -->
          <div class="flex flex-wrap gap-1.5 mb-3">
            <span
              v-for="item in tx.items.slice(0, 3)"
              :key="item.id"
              class="inline-flex items-center gap-1.5 bg-surface-sunken px-2.5 py-1 rounded-lg text-xs text-body"
            >
              <Icon :name="itemTypeIcon(item.type)" class="w-3 h-3 text-muted flex-shrink-0" />
              <span class="truncate max-w-[140px]">{{ item.title }}</span>
            </span>
            <span
              v-if="tx.items.length > 3"
              class="inline-flex items-center bg-surface-sunken px-2.5 py-1 rounded-lg text-xs text-muted font-medium"
            >
              +{{ tx.items.length - 3 }} lainnya
            </span>
          </div>

          <!-- Footer row -->
          <div class="flex items-center justify-between pt-3 border-t border-border-muted">
            <span v-if="tx.payment_method" class="text-xs text-muted flex items-center gap-1.5">
              <Icon name="lucide:credit-card" class="w-3.5 h-3.5" />
              {{ tx.payment_method }}
            </span>
            <span v-else class="text-xs text-muted">-</span>
            <span class="text-sm font-bold text-heading">{{ formatCurrency(tx.final_amount) }}</span>
          </div>
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 mt-8">
        <UButton
          variant="outline"
          size="sm"
          :disabled="currentPage <= 1"
          @click="fetchTransactions(currentPage - 1)"
        >
          <Icon name="lucide:chevron-left" class="w-4 h-4" />
        </UButton>
        <template v-for="p in totalPages" :key="p">
          <button
            v-if="p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)"
            type="button"
            class="w-9 h-9 rounded-xl text-xs font-semibold transition-all duration-200"
            :class="p === currentPage
              ? 'bg-primary-500 text-white shadow-sm'
              : 'text-muted hover:bg-surface-muted'"
            @click="fetchTransactions(p)"
          >
            {{ p }}
          </button>
          <span
            v-else-if="p === currentPage - 2 || p === currentPage + 2"
            class="px-1 text-muted text-xs"
          >
            ...
          </span>
        </template>
        <UButton
          variant="outline"
          size="sm"
          :disabled="currentPage >= totalPages"
          @click="fetchTransactions(currentPage + 1)"
        >
          <Icon name="lucide:chevron-right" class="w-4 h-4" />
        </UButton>
      </div>
    </template>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showDetail && selectedTx"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDetail = false" />

          <!-- Panel -->
          <Transition name="scale">
            <div
              v-if="showDetail"
              class="relative w-full max-w-lg bg-surface rounded-2xl shadow-xl overflow-hidden"
            >
              <!-- Header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-border bg-surface-muted/30">
                <div>
                  <h2 class="text-base font-bold text-heading">Detail Transaksi</h2>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-muted font-mono">{{ selectedTx.order_number }}</span>
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ring-1 ring-inset"
                      :class="statusClasses(selectedTx.status_color)"
                    >
                      {{ selectedTx.status_label }}
                    </span>
                  </div>
                </div>
                <button
                  type="button"
                  class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors"
                  @click="showDetail = false"
                >
                  <Icon name="lucide:x" class="w-5 h-5 text-muted" />
                </button>
              </div>

              <!-- Body -->
              <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-5">
                <!-- Items -->
                <div>
                  <h3 class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Item Pesanan</h3>
                  <div class="space-y-2">
                    <div
                      v-for="item in selectedTx.items"
                      :key="item.id"
                      class="flex items-center gap-3 p-3 rounded-xl bg-surface-muted/40"
                    >
                      <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                        :class="itemTypeColor(item.type)"
                      >
                        <Icon :name="itemTypeIcon(item.type)" class="w-4 h-4" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm text-heading font-medium truncate">{{ item.title }}</p>
                        <p class="text-[11px] text-muted">
                          {{ item.type_label }}
                          <template v-if="item.quantity > 1"> &times; {{ item.quantity }}</template>
                        </p>
                      </div>
                      <span class="text-sm font-semibold text-heading whitespace-nowrap">
                        {{ formatCurrency(item.subtotal) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Summary -->
                <div class="rounded-xl border border-border p-4 space-y-2.5">
                  <div class="flex justify-between text-sm text-body">
                    <span>Subtotal</span>
                    <span>{{ formatCurrency(selectedTx.total_amount) }}</span>
                  </div>
                  <div v-if="selectedTx.discount_amount > 0" class="flex justify-between text-sm text-success-600">
                    <span>Diskon</span>
                    <span>-{{ formatCurrency(selectedTx.discount_amount) }}</span>
                  </div>
                  <div class="flex justify-between text-base font-bold text-heading pt-2.5 border-t border-border-muted">
                    <span>Total</span>
                    <span class="text-primary-600">{{ formatCurrency(selectedTx.final_amount) }}</span>
                  </div>
                </div>

                <!-- Payment Action (Pending) -->
                <div v-if="selectedTx.status === 'pending'" class="rounded-xl border border-warning-200 bg-warning-50/50 p-6 text-center shadow-sm">
                  <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <Icon name="lucide:clock" class="w-6 h-6" />
                  </div>
                  <h3 class="text-lg font-bold text-heading mb-1.5 text-center">Menunggu Pembayaran</h3>
                  <p class="text-xs text-body mb-5 leading-relaxed max-w-sm mx-auto">
                    Selesaikan tagihan Anda melalui portal pembayaran resmi Midtrans untuk mendapatkan akses materi secara instan.
                  </p>
                  <UButton
                    v-if="selectedTx.payment_url"
                    @click="openPaymentUrl(selectedTx.payment_url)"
                    size="lg"
                    class="justify-center bg-primary-600 hover:bg-primary-700 text-white font-bold w-full transition-all shadow-sm"
                  >
                    Selesaikan Pembayaran
                    <Icon name="lucide:external-link" class="w-4 h-4 ml-1.5 opacity-90" />
                  </UButton>
                </div>

                <!-- Payment info (Paid / Cancelled / Expired) -->
                <div v-else-if="selectedTx.payment_method || selectedTx.paid_at">
                  <h3 class="text-xs font-semibold text-muted uppercase tracking-wider mb-2.5">Pembayaran</h3>
                  <div class="space-y-2 text-sm">
                    <div v-if="selectedTx.payment_method" class="flex items-center gap-2 text-body">
                      <Icon name="lucide:credit-card" class="w-4 h-4 text-muted" />
                      <span class="uppercase font-medium">{{ selectedTx.payment_method }}</span>
                    </div>
                    <div v-if="selectedTx.paid_at" class="flex items-center gap-2 text-body">
                      <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500" />
                      <span>Dibayar {{ new Date(selectedTx.paid_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) + ' WIB' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-body">
                      <Icon name="lucide:calendar" class="w-4 h-4 text-muted" />
                      <span>Dibuat {{ selectedTx.created_date }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="px-6 py-4 border-t border-border">
                <UButton variant="outline" size="sm" block @click="showDetail = false">
                  Tutup
                </UButton>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.scale-enter-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.scale-leave-active {
  transition: all 0.15s ease-in;
}
.scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(8px);
}
.scale-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
</style>

