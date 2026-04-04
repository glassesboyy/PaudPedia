<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import type { ClassRoom, Student } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'

const route = useRoute()
const router = useRouter()
const schoolStore = useSchoolStore()

const classId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const classData = ref<ClassRoom | null>(null)
const students = ref<Student[]>([])
const error = ref('')

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchClassDetail()
  }
})

async function fetchClassDetail() {
  isLoading.value = true
  error.value = ''
  try {
    const response = await classService.getClass(schoolStore.currentSchoolId!, classId.value)
    classData.value = response.data
    students.value = (response.data as any).students || []
  } catch (err) {
    console.error('Gagal mengambil detail kelas:', err)
    error.value = 'Data kelas tidak ditemukan atau terjadi kesalahan server.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <button
          @click="router.push({ name: 'ClassList' })"
          class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
        >
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Detail Kelas</h1>
          <p class="text-sm text-muted">Informasi lengkap dan daftar siswa dalam kelas</p>
        </div>
      </div>
      <div v-if="classData && schoolStore.currentRole === 'headmaster'" class="flex gap-2">
        <BaseButton variant="outline" @click="router.push({ name: 'ClassEdit', params: { id: classData.id } })">
          <template #prepend><Icon name="lucide:pencil" class="w-4 h-4" /></template>
          Edit Kelas
        </BaseButton>
      </div>
    </div>

    <BaseAlert v-if="error" variant="danger">{{ error }}</BaseAlert>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-8 animate-fade-in">
      <BaseCard class="p-8 space-y-8 border-none shadow-xl shadow-primary-900/5">
        <div class="flex flex-col md:flex-row md:items-center gap-8">
          <Skeleton width="6rem" height="6rem" class="rounded-[2rem] shrink-0" />
          <div class="space-y-4 flex-1">
            <Skeleton width="40%" height="2rem" />
            <div class="flex gap-2">
              <Skeleton width="100px" height="1.5rem" />
              <Skeleton width="80px" height="1.5rem" />
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-slate-100">
          <div class="space-y-2"><Skeleton width="100px" height="0.75rem" /><Skeleton width="140px" height="1.25rem" /></div>
          <div class="space-y-2"><Skeleton width="100px" height="0.75rem" /><Skeleton width="140px" height="1.25rem" /></div>
          <div class="space-y-2"><Skeleton width="100px" height="0.75rem" /><Skeleton width="200px" height="1.25rem" class="rounded-xl" /></div>
        </div>
      </BaseCard>

      <BaseCard class="border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
          <Skeleton width="180px" height="1.75rem" />
          <Skeleton width="120px" height="1.25rem" />
        </div>
        <div class="p-8 space-y-6">
          <div v-for="i in 5" :key="i" class="flex items-center justify-between py-2">
            <div class="flex items-center gap-4">
               <Skeleton width="3rem" height="3rem" class="rounded-xl" />
               <Skeleton width="220px" height="1.25rem" />
            </div>
            <Skeleton width="100px" height="1.25rem" />
          </div>
        </div>
      </BaseCard>
    </div>

    <!-- Content -->
    <div v-else-if="classData" class="space-y-8">
      <!-- Top Panel: Class Info -->
      <BaseCard class="p-8 space-y-8 border-none shadow-xl shadow-primary-900/5 bg-gradient-to-br from-white to-slate-50/50 flex flex-col">
        <!-- Header Part -->
        <div class="flex flex-col md:flex-row md:items-center gap-8">
          <div class="w-24 h-24 rounded-[2rem] bg-primary-600 flex items-center justify-center text-white shadow-2xl shadow-primary-600/30 shrink-0 transform hover:scale-105 transition-transform">
            <Icon name="lucide:school" class="w-12 h-12" />
          </div>
          <div class="space-y-4 flex-1">
            <h2 class="text-3xl font-black text-heading tracking-tight leading-none">{{ classData.name }}</h2>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 rounded-full bg-secondary-100 text-secondary-800 text-[10px] font-black uppercase tracking-widest border border-secondary-200">
                {{ classData.level || 'Umum' }}
              </span>
              <span class="px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-[10px] font-black uppercase tracking-widest border border-primary-200">
                {{ classData.academic_year }}
              </span>
            </div>
          </div>
        </div>

        <!-- Info Grid (Aligned with Skeleton) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-slate-100">
          <div class="space-y-1">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahun Ajaran</p>
            <p class="text-lg font-bold text-heading leading-none">{{ classData.academic_year || '-' }}</p>
          </div>
          <div class="space-y-1">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kapasitas</p>
            <div class="flex items-center gap-3">
              <p class="text-lg font-bold text-heading leading-none">{{ students.length }} / {{ classData.capacity || '∞' }}</p>
              <div class="w-16 h-2 rounded-full bg-slate-200 overflow-hidden">
                <div 
                  class="h-full bg-primary-500 rounded-full" 
                  :style="{ width: Math.min((students.length / (classData.capacity || 100)) * 100, 100) + '%' }"
                ></div>
              </div>
            </div>
          </div>
          <div class="space-y-2">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Wali Kelas Utama</p>
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-100 shadow-sm shadow-primary-900/5 max-w-sm">
              <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center border border-primary-100">
                <Icon name="lucide:user" class="w-5 h-5" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-black text-heading truncate">{{ classData.homeroom_teacher_name || 'Belum Ditentukan' }}</p>
                <p class="text-[9px] text-primary-600 font-bold uppercase tracking-widest">Penanggung Jawab</p>
              </div>
            </div>
          </div>
        </div>
      </BaseCard>

      <!-- Bottom Panel: Student List -->
      <BaseCard class="border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-white">
          <h3 class="text-xl font-black text-heading tracking-tight">Daftar Siswa Enrol</h3>
          <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">{{ students.length }} Siswa Terdaftar</span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50">
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Siswa</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">NISN</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Gender</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="students.length === 0">
                <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic font-medium">
                  Belum ada siswa yang terdaftar di kelas ini.
                </td>
              </tr>
              <tr 
                v-for="s in students" 
                :key="s.id"
                class="hover:bg-primary-50/30 transition-all group cursor-pointer"
                @click="router.push({ name: 'StudentDetail', params: { id: s.id } })"
              >
                <td class="px-8 py-5">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform duration-300">
                      <img v-if="s.photo_url" :src="s.photo_url" class="w-full h-full object-cover" />
                      <Icon v-else name="lucide:user" class="w-6 h-6 text-slate-300" />
                    </div>
                    <p class="text-base font-bold text-heading group-hover:text-primary-700 transition-colors">{{ s.name }}</p>
                  </div>
                </td>
                <td class="px-8 py-5 text-center">
                  <span class="text-sm text-body font-mono font-bold tracking-tight bg-slate-50 px-3 py-1 rounded-lg">{{ s.nisn || '-' }}</span>
                </td>
                <td class="px-8 py-5 text-center">
                  <span :class="['text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest', s.gender === 'male' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700']">
                    {{ s.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                  </span>
                </td>
                <td class="px-8 py-5 text-right" @click.stop>
                  <button 
                    @click="router.push({ name: 'StudentDetail', params: { id: s.id } })"
                    class="w-9 h-9 rounded-xl bg-surface hover:bg-primary-600 hover:text-white border border-border text-muted transition-all duration-300 flex items-center justify-center mx-auto lg:ml-auto lg:mr-0 group/btn"
                    title="Lihat Detail"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4 group-hover/btn:scale-110 trasition-transform" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
    </div>
  </div>
</template>
