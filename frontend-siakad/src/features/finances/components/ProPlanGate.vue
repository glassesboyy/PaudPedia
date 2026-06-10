<script setup lang="ts">
/**
 * ProPlanGate — Reusable component that blocks access to Pro-only features.
 * Shows upgrade prompt when school is on Free Plan.
 */
import { useRouter } from 'vue-router'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

defineProps<{
  featureName?: string
}>()

const router = useRouter()
</script>

<template>
  <BaseCard class="p-0 border-none shadow-xl overflow-hidden">
    <div class="p-12 text-center space-y-6 bg-gradient-to-b from-violet-50/50 to-white">
      <div class="w-20 h-20 rounded-2xl bg-violet-100 flex items-center justify-center mx-auto">
        <Icon name="lucide:lock" class="w-10 h-10 text-violet-400" />
      </div>
      <div class="space-y-2">
        <h3 class="text-xl font-black text-slate-800">Fitur Pro Plan</h3>
        <p class="text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
          {{ featureName || 'Fitur ini' }} hanya tersedia untuk sekolah dengan paket <strong class="text-violet-600">Pro</strong>. 
          Upgrade sekarang untuk membuka semua fitur premium.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <BaseButton variant="primary" @click="router.push({ name: 'Subscription' })" class="bg-violet-600 hover:bg-violet-700 shadow-lg shadow-violet-500/25">
          <template #prepend><Icon name="lucide:crown" class="w-4 h-4" /></template>
          Lihat Paket Pro
        </BaseButton>
        <BaseButton variant="ghost" @click="router.back()">
          <template #prepend><Icon name="lucide:arrow-left" class="w-4 h-4" /></template>
          Kembali
        </BaseButton>
      </div>
      <div class="flex items-center justify-center gap-6 text-xs text-slate-400 pt-4">
        <span class="flex items-center gap-1.5"><Icon name="lucide:check-circle" class="w-3.5 h-3.5 text-violet-400" /> Keuangan</span>
        <span class="flex items-center gap-1.5"><Icon name="lucide:check-circle" class="w-3.5 h-3.5 text-violet-400" /> Rapor PDF</span>
        <span class="flex items-center gap-1.5"><Icon name="lucide:check-circle" class="w-3.5 h-3.5 text-violet-400" /> Unlimited</span>
      </div>
    </div>
  </BaseCard>
</template>
