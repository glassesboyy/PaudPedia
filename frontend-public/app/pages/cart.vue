<script setup lang="ts">
/**
 * Cart Page
 * Route: /cart
 *
 * Displays cart items with summary sidebar. Two-column layout on desktop,
 * stacked on mobile. Handles promo codes and navigates to checkout.
 */
useSeo({ title: 'Keranjang' })

const {
  items,
  subtotal,
  total,
  discount,
  isEmpty,
  promoCode,
  isValidatingPromo,
  promoError,
  removeFromCart,
  updateQuantity,
  applyPromo,
  clearPromo,
} = useCart()

function handleCheckout() {
  navigateTo('/checkout')
}
</script>

<template>
  <div class="bg-gradient-to-b from-surface via-surface to-primary-50/10 min-h-[60vh]">
    <div class="container py-8 sm:py-12">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-muted mb-8">
        <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <span class="text-primary-600 font-medium">Keranjang</span>
      </nav>

      <!-- Page header -->
      <div class="flex items-center justify-between mb-8 pb-6 border-b border-border/50">
        <div class="flex items-center gap-4">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center flex-shrink-0 shadow-sm">
            <Icon name="lucide:shopping-cart" class="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-heading tracking-tight">Keranjang Belanja</h1>
            <p v-if="!isEmpty" class="text-sm text-muted mt-0.5">{{ items.length }} item di keranjang Anda</p>
          </div>
        </div>

        <!-- Step indicator (desktop) -->
        <div v-if="!isEmpty" class="hidden sm:flex items-center gap-2 text-xs">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-500 text-white font-semibold">
            <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-[10px]">1</span>
            Keranjang
          </span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5 text-border" />
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-muted text-muted font-medium">
            <span class="w-4 h-4 rounded-full bg-border/50 flex items-center justify-center text-[10px]">2</span>
            Checkout
          </span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5 text-border" />
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-muted text-muted font-medium">
            <span class="w-4 h-4 rounded-full bg-border/50 flex items-center justify-center text-[10px]">3</span>
            Pembayaran
          </span>
        </div>
      </div>

      <!-- Empty state -->
      <CartEmpty v-if="isEmpty" />

      <!-- Cart content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Items list (2/3 width) -->
        <div class="lg:col-span-2">
          <div class="space-y-3">
            <TransitionGroup
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-x-4"
            >
              <CartItemCard
                v-for="item in items"
                :key="`${item.type}-${item.id}`"
                :item="item"
                @update-quantity="updateQuantity"
                @remove="removeFromCart"
              />
            </TransitionGroup>
          </div>

          <!-- Continue shopping link -->
          <div class="pt-6 flex items-center justify-between">
            <NuxtLink
              to="/courses"
              class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors group"
            >
              <Icon name="lucide:arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
              Lanjut Belanja
            </NuxtLink>
            <p class="text-xs text-muted">
              {{ items.length }} {{ items.length === 1 ? 'item' : 'items' }}
            </p>
          </div>
        </div>

        <!-- Summary sidebar (1/3 width) -->
        <div>
          <CartSummary
            :subtotal="subtotal"
            :discount="discount"
            :total="total"
            :promo-code="promoCode"
            :is-validating-promo="isValidatingPromo"
            :promo-error="promoError"
            :is-empty="isEmpty"
            @apply-promo="applyPromo"
            @clear-promo="clearPromo"
            @checkout="handleCheckout"
          />
        </div>
      </div>
    </div>
  </div>
</template>
