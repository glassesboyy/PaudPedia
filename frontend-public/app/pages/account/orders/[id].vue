<script setup lang="ts">
/**
 * Order Detail Page
 * Route: /account/orders/:id
 *
 * Standalone order detail view showing status, items, amounts, and payment info.
 * This page is navigated to after successful checkout/payment.
 */
import { dashboardService } from '~~/services'
import type { Transaction } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth'],
})

const route = useRoute()
const orderId = Number(route.params.id)

const order = ref<Transaction | null>(null)
const isLoading = ref(true)
const error = ref(false)

async function fetchOrder() {
  isLoading.value = true
  error.value = false
  try {
    const res = await dashboardService.getTransactionDetail(orderId)
    if (res.success && res.data) {
      order.value = res.data
      useSeo({ title: `Pesanan ${res.data.order_number}` })
    } else {
      error.value = true
    }
  } catch {
    error.value = true
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchOrder)

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

function statusIcon(status: string): string {
  const map: Record<string, string> = {
    paid: 'lucide:check-circle',
    pending: 'lucide:clock',
    failed: 'lucide:x-circle',
    cancelled: 'lucide:ban',
    expired: 'lucide:timer-off',
  }
  return map[status] || 'lucide:circle'
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

function accessRoute(type: string): string {
  const map: Record<string, string> = {
    course: '/account/courses',
    webinar: '/account/webinars',
    product: '/account/products',
  }
  return map[type] || '/account'
}
</script>

<template>
  <div>
    <!-- Back link -->
    <div class="mb-5">
      <NuxtLink
        to="/account/orders"
        class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-primary-600 transition-colors"
      >
        <Icon name="lucide:arrow-left" class="w-4 h-4" />
        Kembali ke Riwayat Transaksi
      </NuxtLink>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="space-y-4">
      <div class="rounded-2xl border border-border bg-surface p-6">
        <USkeleton variant="text" class="w-48 h-6 mb-2" />
        <USkeleton variant="text" class="w-32 h-4" />
      </div>
      <div class="rounded-2xl border border-border bg-surface p-6 space-y-3">
        <USkeleton variant="rounded" class="w-full h-16" />
        <USkeleton variant="rounded" class="w-full h-16" />
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error || !order" class="rounded-2xl border border-danger-200 bg-danger-50/50 p-10 text-center">
      <div class="w-14 h-14 rounded-2xl bg-danger-100 flex items-center justify-center mx-auto mb-4">
        <Icon name="lucide:file-x" class="w-7 h-7 text-danger-500" />
      </div>
      <h3 class="text-base font-semibold text-heading mb-1">Pesanan Tidak Ditemukan</h3>
      <p class="text-sm text-muted mb-5">Pesanan yang Anda cari tidak ditemukan atau tidak tersedia.</p>
      <div class="flex items-center justify-center gap-3">
        <NuxtLink to="/account/orders">
          <UButton variant="outline" size="sm">
            <Icon name="lucide:arrow-left" class="w-3.5 h-3.5 mr-1.5" />
            Kembali
          </UButton>
        </NuxtLink>
        <UButton variant="primary" size="sm" @click="fetchOrder">
          <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
          Coba Lagi
        </UButton>
      </div>
    </div>

    <!-- Order detail -->
    <template v-else>
      <!-- Header card -->
      <div class="rounded-2xl border border-border bg-surface p-6 mb-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:receipt" class="w-5 h-5 text-primary-500" />
              </div>
              <div>
                <h1 class="text-lg font-bold text-heading">{{ order.order_number }}</h1>
                <p class="text-xs text-muted">{{ order.created_date }}</p>
              </div>
            </div>
          </div>
          <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ring-1 ring-inset"
            :class="statusClasses(order.status_color)"
          >
            <Icon :name="statusIcon(order.status)" class="w-3.5 h-3.5" />
            {{ order.status_label }}
          </span>
        </div>

        <!-- Success message for paid orders -->
        <div
          v-if="order.status === 'paid'"
          class="mt-4 p-3 rounded-lg bg-success-50 border border-success-200 flex items-start gap-2.5"
        >
          <Icon name="lucide:check-circle" class="w-5 h-5 text-success-600 flex-shrink-0 mt-0.5" />
          <div>
            <p class="text-sm font-medium text-success-700">Pembayaran berhasil!</p>
            <p class="text-xs text-success-600 mt-0.5">Akses konten Anda sudah tersedia di dashboard.</p>
          </div>
        </div>

        <!-- Pending message -->
        <div
          v-else-if="order.status === 'pending'"
          class="mt-4 p-3 rounded-lg bg-warning-50 border border-warning-200 flex items-start gap-2.5"
        >
          <Icon name="lucide:clock" class="w-5 h-5 text-warning-600 flex-shrink-0 mt-0.5" />
          <div>
            <p class="text-sm font-medium text-warning-700">Menunggu pembayaran</p>
            <p class="text-xs text-warning-600 mt-0.5">Silakan selesaikan pembayaran Anda untuk mendapatkan akses.</p>
          </div>
        </div>
      </div>

      <!-- Items card -->
      <div class="rounded-2xl border border-border bg-surface p-6 mb-4">
        <h2 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
          <Icon name="lucide:package" class="w-4 h-4 text-primary-500" />
          Item Pesanan
        </h2>
        <div class="space-y-2.5">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="flex items-center gap-3 p-3 rounded-xl bg-surface-muted/40"
          >
            <div
              class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
              :class="itemTypeColor(item.type)"
            >
              <Icon :name="itemTypeIcon(item.type)" class="w-5 h-5" />
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

      <!-- Summary card -->
      <div class="rounded-2xl border border-border bg-surface p-6 mb-4">
        <h2 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
          <Icon name="lucide:calculator" class="w-4 h-4 text-primary-500" />
          Ringkasan Pembayaran
        </h2>
        <div class="space-y-2.5">
          <div class="flex justify-between text-sm text-body">
            <span>Subtotal</span>
            <span>{{ formatCurrency(order.total_amount) }}</span>
          </div>
          <div v-if="order.discount_amount > 0" class="flex justify-between text-sm text-success-600">
            <span>Diskon</span>
            <span>-{{ formatCurrency(order.discount_amount) }}</span>
          </div>
          <div class="flex justify-between text-base font-bold text-heading pt-3 border-t border-border-muted">
            <span>Total</span>
            <span class="text-primary-600">{{ formatCurrency(order.final_amount) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment info card -->
      <div v-if="order.payment_method || order.paid_at" class="rounded-2xl border border-border bg-surface p-6 mb-4">
        <h2 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
          <Icon name="lucide:credit-card" class="w-4 h-4 text-primary-500" />
          Informasi Pembayaran
        </h2>
        <div class="space-y-2.5 text-sm">
          <div v-if="order.payment_method" class="flex items-center gap-2.5 text-body">
            <Icon name="lucide:credit-card" class="w-4 h-4 text-muted" />
            <span>Metode: <strong class="text-heading">{{ order.payment_method }}</strong></span>
          </div>
          <div v-if="order.paid_at" class="flex items-center gap-2.5 text-body">
            <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500" />
            <span>Dibayar pada: <strong class="text-heading">{{ order.paid_at }}</strong></span>
          </div>
          <div class="flex items-center gap-2.5 text-body">
            <Icon name="lucide:calendar" class="w-4 h-4 text-muted" />
            <span>Dibuat pada: {{ order.created_date }}</span>
          </div>
        </div>
      </div>

      <!-- Access links (only for paid orders) -->
      <div v-if="order.status === 'paid'" class="rounded-2xl border border-primary-200 bg-primary-50/30 p-6">
        <h2 class="text-sm font-semibold text-heading mb-3 flex items-center gap-2">
          <Icon name="lucide:key" class="w-4 h-4 text-primary-500" />
          Akses Konten
        </h2>
        <p class="text-xs text-muted mb-4">Akses pembelian Anda melalui link di bawah ini:</p>
        <div class="flex flex-wrap gap-2">
          <NuxtLink
            v-for="item in order.items"
            :key="item.id"
            :to="accessRoute(item.type)"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-medium bg-surface border border-border text-body hover:border-primary-300 hover:text-primary-600 transition-all"
          >
            <Icon :name="itemTypeIcon(item.type)" class="w-3.5 h-3.5" />
            {{ item.type_label }}: {{ item.title }}
          </NuxtLink>
        </div>
      </div>
    </template>
  </div>
</template>
