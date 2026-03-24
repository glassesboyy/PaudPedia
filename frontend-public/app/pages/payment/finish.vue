<script setup lang="ts">
/**
 * Payment Finish Page
 * Route: /payment/finish
 *
 * Catch-all endpoint for Midtrans external redirects (e.g. GoPay deeplinks).
 * Reads the URL query params to display a contextual static post-payment UI.
 */
definePageMeta({
  layout: 'minimal',
  middleware: ['auth'],
})

useSeo({ title: 'Status Pembayaran' })

const route = useRoute()
const orderId = computed(() => route.query.order_id as string | undefined)
const txStatus = computed(() => route.query.transaction_status as string | undefined)
const statusCode = computed(() => route.query.status_code as string | undefined)

// Determine state context
const isSuccess = computed(() => ['settlement', 'capture'].includes(txStatus.value ?? ''))
const isPending = computed(() => txStatus.value === 'pending')
const isFailed = computed(() => ['cancel', 'deny', 'expire', 'failure'].includes(txStatus.value ?? ''))

const { clearCart } = useCart()

onMounted(async () => {
  // If user reaches here, it means they engaged with a checkout. We safely clear the cart.
  if (isSuccess.value || isPending.value) {
    try {
      const { cartService } = await import('~~/services')
      await cartService.clearCart()
      clearCart()
    } catch {
      // Background best-effort
    }
  }
})
</script>

<template>
  <div class="bg-gradient-to-b from-surface via-surface to-primary-50/10 min-h-[60vh] flex flex-col items-center justify-center py-16 px-4">
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-border p-8 text-center relative overflow-hidden">
      <!-- Decorative background blur -->
      <div 
        class="absolute -top-24 -right-24 w-48 h-48 rounded-full blur-3xl opacity-20 pointer-events-none"
        :class="{
          'bg-success-500': isSuccess,
          'bg-warning-500': isPending,
          'bg-danger-500': isFailed,
          'bg-primary-500': !txStatus
        }"
      />

      <!-- Icon State -->
      <div 
        class="w-24 h-24 mx-auto rounded-full flex items-center justify-center mb-6 shadow-inner relative z-10"
        :class="{
          'bg-success-100 text-success-600': isSuccess,
          'bg-warning-100 text-warning-600': isPending,
          'bg-danger-100 text-danger-600': isFailed,
          'bg-primary-100 text-primary-600': !txStatus
        }"
      >
        <Icon v-if="isSuccess" name="lucide:check-circle-2" class="w-12 h-12" />
        <Icon v-else-if="isPending" name="lucide:clock" class="w-12 h-12" />
        <Icon v-else-if="isFailed" name="lucide:x-circle" class="w-12 h-12" />
        <Icon v-else name="lucide:info" class="w-12 h-12" />
      </div>

      <!-- Typography State -->
      <h1 class="text-2xl font-bold text-heading mb-2 relative z-10">
        {{ 
          isSuccess ? 'Pembayaran Berhasil!' : 
          isPending ? 'Menunggu Pembayaran' : 
          isFailed ? 'Pembayaran Gagal' : 
          'Informasi Pembayaran' 
        }}
      </h1>
      
      <p class="text-sm text-body mb-8 leading-relaxed relative z-10">
        <template v-if="isSuccess">
          Terima kasih! Transaksi Anda telah lunas dan diverifikasi. Materi pembelajaran kini sudah dapat diakses sepenuhnya.
        </template>
        <template v-else-if="isPending">
          Pesanan sedang diproses. Silakan selesaikan instruksi pembayaran Anda sebelum batas waktu berakhir.
        </template>
        <template v-else-if="isFailed">
          Mohon maaf, transaksi Anda gagal atau kedaluwarsa. Silakan lakukan pemesanan ulang untuk mendapatkan materi.
        </template>
        <template v-else>
          Kami telah merekam status transaksi Anda. Silakan cek menu riwayat pembelian.
        </template>
      </p>

      <!-- Reference Block -->
      <div v-if="orderId" class="bg-surface-muted/50 rounded-xl p-4 mb-8 text-left border border-border-muted relative z-10">
        <p class="text-xs text-muted font-medium mb-1 uppercase tracking-wider">Nomor Referensi (Order ID)</p>
        <p class="text-sm font-mono font-bold text-heading">{{ orderId }}</p>
      </div>

      <!-- Actions -->
      <div class="flex flex-col gap-3 relative z-10">
        <UButton
          @click="navigateTo('/account/orders')"
          size="lg"
          class="justify-center font-bold"
          :color="isFailed ? 'gray' : 'primary'"
        >
          Lihat Riwayat Transaksi
        </UButton>
        <UButton
          v-if="isSuccess"
          @click="navigateTo('/account/courses')"
          size="lg"
          variant="outline"
          color="gray"
          class="justify-center font-semibold bg-white text-gray-700 hover:bg-gray-50 border border-gray-200"
        >
          Mulai Belajar
        </UButton>
        <UButton
          v-if="isFailed"
          @click="navigateTo('/cart')"
          size="lg"
          variant="outline"
          color="primary"
          class="justify-center font-semibold bg-white"
        >
          Kembali ke Keranjang
        </UButton>
      </div>

    </div>
  </div>
</template>
