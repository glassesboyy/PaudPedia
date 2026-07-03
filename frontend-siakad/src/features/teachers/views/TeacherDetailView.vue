<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { teacherService } from '@/features/teachers/services/teacher.service'
import type { Teacher, ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'

const route = useRoute()
const router = useRouter()
const schoolStore = useSchoolStore()

const teacherId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const teacherData = ref<Teacher | null>(null)
const homeroomClasses = ref<ClassRoom[]>([])
const error = ref('')

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchTeacherDetail()
  }
})

async function fetchTeacherDetail() {
  isLoading.value = true
  error.value = ''
  try {
    const response = await teacherService.getTeacher(schoolStore.currentSchoolId!, teacherId.value)
    teacherData.value = response.data
    homeroomClasses.value = (response.data as any).homeroom_classes || []
  } catch (err) {
    console.error('Gagal mengambil detail guru:', err)
    error.value = 'Data guru tidak ditemukan atau terjadi kesalahan server.'
  } finally {
    isLoading.value = false
  }
}

function formatDate(dateString: string) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <button
          @click="router.push({ name: 'TeacherList' })"
          class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
        >
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Detail Guru</h1>
          <p class="text-sm text-muted">Profil lengkap dan penugasan guru</p>
        </div>
      </div>
      <div v-if="teacherData" class="flex gap-2">
         <!-- Note: Edit Teacher logic might need separate view or overlay -->
         <!-- For now just back to list if no specific edit view -->
      </div>
    </div>

    <BaseAlert v-if="error" variant="danger">{{ error }}</BaseAlert>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
      <BaseCard class="lg:col-span-1 p-6 space-y-8 border-none shadow-xl shadow-primary-900/5">
        <div class="flex flex-col items-center py-4">
           <Skeleton width="6rem" height="6rem" class="rounded-[2rem] mb-6 shadow-sm" />
           <Skeleton width="70%" height="1.5rem" />
           <Skeleton width="40%" height="0.75rem" class="mt-2" />
        </div>
        <div class="space-y-6 pt-6 border-t border-slate-100">
          <div v-for="i in 3" :key="i" class="flex items-center gap-3">
             <Skeleton width="2rem" height="2rem" class="rounded-lg shrink-0" />
             <div class="space-y-2 w-full">
                <Skeleton width="25%" height="0.5rem" />
                <Skeleton width="60%" height="0.875rem" />
             </div>
          </div>
        </div>
      </BaseCard>
      <div class="lg:col-span-2 space-y-6">
         <BaseCard class="p-6 space-y-6 border-none shadow-xl shadow-primary-900/5">
            <div class="flex justify-between items-center">
              <Skeleton width="140px" height="1.5rem" />
              <Skeleton width="80px" height="1.25rem" class="rounded-full" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <Skeleton height="6rem" v-for="i in 2" :key="i" class="rounded-2xl" />
            </div>
         </BaseCard>
         <BaseCard class="p-6 space-y-6 border-none shadow-xl shadow-primary-900/5">
            <Skeleton width="180px" height="1.5rem" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <Skeleton height="5rem" v-for="i in 2" :key="i" class="rounded-2xl" />
            </div>
         </BaseCard>
      </div>
    </div>

    <!-- Content -->
    <div v-else-if="teacherData" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Panel: Profile Info -->
      <div class="lg:col-span-1 space-y-6">
        <BaseCard class="p-6 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
          <div class="flex flex-col items-center text-center py-4">
            <div class="w-24 h-24 rounded-[2rem] bg-primary-600 flex items-center justify-center text-white text-3xl font-black shadow-2xl shadow-primary-600/30 overflow-hidden mb-5">
              <img v-if="teacherData.avatar_url" :src="teacherData.avatar_url" class="w-full h-full object-cover" />
              <span v-else>{{ teacherData.name.charAt(0).toUpperCase() }}</span>
            </div>
            <h2 class="text-xl font-bold text-heading">{{ teacherData.name }}</h2>
            <p class="text-primary-600 text-xs font-black uppercase tracking-widest mt-1">
              {{ teacherData.specialization || 'Pendidik' }}
            </p>
          </div>

          <div class="space-y-4 pt-6 border-t border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                <Icon name="lucide:id-card" class="w-4 h-4" />
              </div>
              <div>
                <p class="text-[10px] font-black text-muted uppercase tracking-wider">NIP</p>
                <p class="text-sm font-bold text-heading">{{ teacherData.nip || '-' }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                <Icon name="lucide:mail" class="w-4 h-4" />
              </div>
              <div>
                <p class="text-[10px] font-black text-muted uppercase tracking-wider">Email</p>
                <p class="text-sm font-bold text-heading truncate">{{ teacherData.email }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                <Icon name="lucide:phone" class="w-4 h-4" />
              </div>
              <div>
                <p class="text-[10px] font-black text-muted uppercase tracking-wider">WhatsApp</p>
                <p class="text-sm font-bold text-heading">{{ teacherData.phone || '-' }}</p>
              </div>
            </div>
          </div>


        </BaseCard>
      </div>

      <!-- Right Panel: Stats & Assignments -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Assigned Classes -->
        <BaseCard class="p-6 border-none shadow-xl shadow-primary-900/5">
           <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-heading">Kelas Perwalian</h3>
              <span class="px-2.5 py-1 rounded-full bg-primary-100 text-primary-700 text-[10px] font-black uppercase tracking-wider">
                {{ homeroomClasses.length }} KELAS
              </span>
           </div>

           <div v-if="homeroomClasses.length === 0" class="py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center">
              <Icon name="lucide:school" class="w-12 h-12 text-slate-300 mx-auto mb-3" />
              <p class="text-sm text-muted font-medium">Belum ditugaskan sebagai Wali Kelas.</p>
           </div>
           
           <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="c in homeroomClasses" 
                :key="c.id"
                class="p-4 rounded-2xl border border-slate-100 hover:border-primary-200 hover:shadow-lg hover:shadow-primary-900/5 transition-all bg-white group cursor-pointer"
                @click="router.push({ name: 'ClassDetail', params: { id: c.id } })"
              >
                 <div class="flex items-center justify-between mb-3">
                   <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-colors">
                     <Icon name="lucide:users" class="w-5 h-5" />
                   </div>
                   <span class="px-2 py-0.5 rounded-md bg-secondary-50 text-secondary-700 text-[10px] font-bold uppercase tracking-wider">
                     {{ c.level || 'TA' }}
                   </span>
                 </div>
                 <h4 class="font-bold text-heading group-hover:text-primary-700 transition-colors">{{ c.name }}</h4>
                 <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center gap-1.5">
                      <Icon name="lucide:user" class="w-3 h-3 text-muted" />
                      <span class="text-[10px] font-bold text-muted uppercase">{{ c.current_students }} SISWA</span>
                    </div>
                    <span class="text-[10px] font-black text-slate-300">{{ c.academic_year }}</span>
                 </div>
              </div>
           </div>
        </BaseCard>

        <!-- Activities or Other Info -->
        <BaseCard class="p-6 border-none shadow-xl shadow-primary-900/5">
           <h3 class="text-lg font-bold text-heading mb-6">Informasi Kepegawaian</h3>
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-black text-muted uppercase tracking-wider mb-1">Status Keaktifan</p>
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                  <span class="text-sm font-bold text-heading">Aktif Mengajar</span>
                </div>
              </div>
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-black text-muted uppercase tracking-wider mb-1">Terdaftar Sejak</p>
                <span class="text-sm font-bold text-heading">{{ formatDate(teacherData.joined_at) }}</span>
              </div>
           </div>
        </BaseCard>
      </div>
    </div>
  </div>
</template>
