<script setup lang="ts">
/**
 * ProPlanGate — Reusable component that blocks access to Pro-only features.
 * Shows upgrade prompt when school is on Free Plan.
 */
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

defineProps<{
  featureName?: string
}>()

const router = useRouter()
const schoolStore = useSchoolStore()
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
        <BaseButton v-if="schoolStore.isHeadmaster" variant="primary" @click="router.push({ name: 'Subscription' })" class="bg-violet-600 hover:bg-violet-700 shadow-lg shadow-violet-500/25">
          <template #prepend><Icon name="lucide:crown" class="w-4 h-4" /></template>
          Lihat Paket Pro
        </BaseButton>
      </div>
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-center gap-4 sm:gap-6 text-sm text-slate-500 pt-6 max-w-max mx-auto">
        <span class="flex items-center gap-2"><Icon name="lucide:check-circle" class="w-4 h-4 text-violet-500 shrink-0" /> Manajemen Keuangan</span>
        <span class="flex items-center gap-2"><Icon name="lucide:check-circle" class="w-4 h-4 text-violet-500 shrink-0" /> Pembuatan Raport & E-Raport</span>
        <span class="flex items-center gap-2"><Icon name="lucide:check-circle" class="w-4 h-4 text-violet-500 shrink-0" /> Unlimited Kuota Guru & Siswa</span>
      </div>
    </div>
  </BaseCard>
</template>
