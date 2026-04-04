<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import type { Student } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { usePageCopy } from '@/utils/copy-helper'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const router = useRouter()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('student'))

const myChildren = ref<Student[]>([])
const isLoading = ref(true)

onMounted(async () => {
  if (schoolStore.currentSchoolId && authStore.user?.id) {
    fetchMyChildren()
  }
})

async function fetchMyChildren() {
  isLoading.value = true
  try {
    const response = await studentService.getStudents(schoolStore.currentSchoolId!, {
      parent_user_id: authStore.user?.id,
      per_page: 50
    })
    myChildren.value = response.data
  } catch {
    myChildren.value = []
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button
        @click="router.push('/')"
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-sm text-muted">{{ copy.subtitle }}</p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 gap-6">
      <BaseCard v-for="i in 3" :key="i" class="p-6 space-y-4 shadow-sm border-slate-100">
        <div class="flex items-center gap-4">
          <Skeleton width="4.5rem" height="4.5rem" class="rounded-2xl" />
          <div class="space-y-2 flex-1">
            <Skeleton width="70%" height="1.25rem" />
            <Skeleton width="40%" height="0.75rem" />
          </div>
        </div>
        <div class="pt-4 border-t border-slate-100 space-y-3">
          <Skeleton width="100%" height="1rem" />
          <Skeleton width="80%" height="1rem" />
        </div>
      </BaseCard>
    </div>

    <!-- Empty State -->
    <div v-else-if="myChildren.length === 0" class="py-20 text-center bg-surface border border-dashed border-border rounded-[2rem] shadow-sm">
      <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 mx-auto mb-6">
        <Icon name="lucide:smile text-slate-200" class="w-10 h-10" />
      </div>
      <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Data Anak</h3>
      <p class="text-sm text-body font-medium max-w-sm mx-auto">Data anak Anda belum terdaftar di sistem sekolah. Silakan hubungi pihak sekolah untuk sinkronisasi data wali murid.</p>
    </div>

    <!-- Grid List -->
    <div v-else class="grid grid-cols-1 gap-6">
      <BaseCard 
        v-for="child in myChildren" 
        :key="child.id" 
        class="p-6 border-none shadow-xl shadow-primary-900/5 hover:shadow-2xl hover:shadow-primary-900/10 cursor-pointer group transition-all relative overflow-hidden flex flex-col justify-between"
        @click="router.push({ name: 'StudentDetail', params: { id: child.id } })"
      >
        <!-- Background Decoration -->
        <div class="absolute -right-8 -bottom-8 opacity-[0.03] group-hover:opacity-[0.05] transition-opacity">
          <Icon name="lucide:smile" class="w-32 h-32" />
        </div>

        <div class="flex items-start gap-5 relative z-10">
          <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 shadow-sm group-hover:scale-105 transition-transform duration-300">
            <img v-if="child.photo_url" :src="child.photo_url" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
              <Icon name="lucide:user" class="w-10 h-10" />
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
              <h4 class="font-bold text-heading group-hover:text-primary-700 transition-colors text-lg truncate leading-tight">{{ child.name }}</h4>
              <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider shrink-0 overflow-hidden">
                {{ child.status }}
              </span>
            </div>
            <div class="flex items-center gap-1.5 text-sm text-primary-600 font-bold mb-3 truncate">
              <Icon name="lucide:school" class="w-3.5 h-3.5" />
              <span class="truncate">{{ child.class?.name || 'Belum Ada Kelas' }}</span>
            </div>
            
            <div class="space-y-2 pt-4 border-t border-slate-100">
              <div class="flex items-center justify-between text-[11px] font-bold">
                <span class="text-slate-400 uppercase tracking-widest">NISN</span>
                <span class="text-slate-700">{{ child.nisn || '-' }}</span>
              </div>
              <div class="flex items-center justify-between text-[11px] font-bold">
                <span class="text-slate-400 uppercase tracking-widest">Tgl Lahir</span>
                <span class="text-slate-700">{{ child.birth_date }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-8 flex justify-end">
          <div class="text-xs font-black text-primary-600 uppercase tracking-[0.2em] flex items-center gap-1 group-hover:gap-2 transition-all">
            Lihat Profil
            <Icon name="lucide:arrow-right" class="w-3 h-3" />
          </div>
        </div>
      </BaseCard>
    </div>
  </div>
</template>
