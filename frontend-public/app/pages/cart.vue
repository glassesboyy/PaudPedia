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
  <div class="bg-gradient-to-b from-surface to-primary-50/10 min-h-[60vh]">
    <div class="container py-8 sm:py-10">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-muted mb-6">
        <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <span class="text-primary-600 font-medium">Keranjang</span>
      </nav>

      <!-- Page header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
          <Icon name="lucide:shopping-cart" class="w-5 h-5 text-primary-600" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-heading">Keranjang Belanja</h1>
          <p v-if="!isEmpty" class="text-sm text-muted">{{ items.length }} item di keranjang</p>
        </div>
      </div>

      <!-- Empty state -->
      <CartEmpty v-if="isEmpty" />

      <!-- Cart content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Items list (2/3 width) -->
        <div class="lg:col-span-2 space-y-3">
          <CartItemCard
            v-for="item in items"
            :key="`${item.type}-${item.id}`"
            :item="item"
            @update-quantity="updateQuantity"
            @remove="removeFromCart"
          />

          <!-- Continue shopping link -->
          <div class="pt-4">
            <NuxtLink
              to="/courses"
              class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
            >
              <Icon name="lucide:arrow-left" class="w-4 h-4" />
              Lanjut Belanja
            </NuxtLink>
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
