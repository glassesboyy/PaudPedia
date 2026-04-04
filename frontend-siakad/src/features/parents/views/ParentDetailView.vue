<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { parentService } from '@/features/parents/services/parent.service'
import type { ParentProfile } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import Loader from '@/components/ui/Loader/Loader.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const parentId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const parent = ref<ParentProfile | null>(null)
const error = ref('')

const isHeadmaster = computed(() => schoolStore.isHeadmaster)

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchParent()
  }
})

async function fetchParent() {
  isLoading.value = true
  try {
    const response = await parentService.getParent(schoolStore.currentSchoolId!, parentId.value)
    parent.value = response.data
  } catch {
    error.value = 'Gagal mengambil data orang tua.'
  } finally {
    isLoading.value = false
  }
}

function getParentDisplayName(p: ParentProfile): string {
  const names = [p.father_name, p.mother_name].filter(Boolean)
  return names.join(' & ') || p.email
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button
          @click="router.push({ name: 'ParentList' })"
          class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
        >
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Detail Orang Tua</h1>
          <p class="text-sm text-muted">Informasi lengkap wali murid</p>
        </div>
      </div>
      <BaseButton v-if="isHeadmaster && parent" variant="primary" @click="router.push({ name: 'ParentEdit', params: { id: parent.id } })">
        <template #prepend><Icon name="lucide:edit-3" class="w-4 h-4" /></template>
        Edit Data
      </BaseButton>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="animate-fade-in space-y-6">
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
          <Skeleton width="5rem" height="5rem" class="rounded-[2rem] shrink-0" />
          <div class="space-y-4 w-full">
            <Skeleton width="40%" height="2rem" />
            <Skeleton width="180px" height="1.25rem" />
          </div>
        </div>
        <div class="p-8 space-y-10">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div v-for="i in 6" :key="i" class="space-y-3">
              <Skeleton width="100px" height="0.75rem" />
              <Skeleton width="180px" height="1.25rem" />
            </div>
          </div>
        </div>
      </BaseCard>
    </div>

    <!-- Error -->
    <BaseCard v-else-if="error" class="p-12 text-center flex flex-col items-center gap-4">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-500" />
      <p class="text-lg font-bold text-slate-900">{{ error }}</p>
      <BaseButton variant="outline" @click="fetchParent">Coba Lagi</BaseButton>
    </BaseCard>

    <!-- Detail Content -->
    <BaseCard v-else-if="parent" class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <!-- Header Card -->
      <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
        <div class="w-20 h-20 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-700 font-black text-3xl shrink-0">
          {{ (parent.father_name || parent.mother_name || 'O').charAt(0).toUpperCase() }}
        </div>
        <div class="space-y-1">
          <h2 class="text-2xl font-black text-heading">{{ getParentDisplayName(parent) }}</h2>
          <p class="text-sm text-primary-600 font-bold">{{ parent.email }}</p>
        </div>
      </div>

      <!-- Info Grid -->
      <div class="p-8 space-y-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:user" class="w-3 h-3" /> Nama Ayah
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.father_name || 'Belum terdata' }}</p>
          </div>
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:user" class="w-3 h-3" /> Nama Ibu
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.mother_name || 'Belum terdata' }}</p>
          </div>
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:phone" class="w-3 h-3" /> Telepon
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.phone }}</p>
          </div>
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:mail" class="w-3 h-3" /> Email
            </p>
            <p class="text-base font-bold text-primary-600">{{ parent.email }}</p>
          </div>
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:briefcase" class="w-3 h-3" /> Pekerjaan Ayah
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.father_occupation || 'Belum terdata' }}</p>
          </div>
          <div class="space-y-1.5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:briefcase" class="w-3 h-3" /> Pekerjaan Ibu
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.mother_occupation || 'Belum terdata' }}</p>
          </div>
          <div class="space-y-1.5 md:col-span-2">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
              <Icon name="lucide:map-pin" class="w-3 h-3" /> Alamat
            </p>
            <p class="text-base font-bold text-slate-800">{{ parent.address || 'Belum terdata' }}</p>
          </div>
        </div>

        <!-- Children Section -->
        <div v-if="parent.children && parent.children.length > 0" class="pt-6 border-t border-slate-100">
          <h3 class="text-lg font-black text-heading mb-4 flex items-center gap-2">
            <Icon name="lucide:baby" class="w-5 h-5 text-primary-600" />
            Anak Terdaftar ({{ parent.children.length }})
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
              v-for="child in parent.children"
              :key="child.id"
              class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-4 cursor-pointer hover:bg-primary-50/50 hover:border-primary-200 transition-all"
              @click="router.push({ name: 'StudentDetail', params: { id: child.id } })"
            >
              <div class="w-12 h-12 rounded-xl overflow-hidden bg-white border border-slate-200 shrink-0">
                <img v-if="child.photo_url" :src="child.photo_url" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                  <Icon name="lucide:user" class="w-6 h-6" />
                </div>
              </div>
              <div>
                <p class="text-sm font-bold text-heading">{{ child.name }}</p>
                <p class="text-xs text-muted">{{ child.class?.name || 'Belum ada kelas' }}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="pt-6 border-t border-slate-100">
          <p class="text-sm text-muted italic">Orang tua ini belum memiliki anak yang terdaftar.</p>
        </div>
      </div>
    </BaseCard>
  </div>
</template>
