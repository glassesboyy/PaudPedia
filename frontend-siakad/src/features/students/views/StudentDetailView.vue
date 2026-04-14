<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import type { Student } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import Loader from '@/components/ui/Loader/Loader.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const studentId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const student = ref<Student | null>(null)
const error = ref('')

const isHeadmaster = computed(() => schoolStore.isHeadmaster)

const statusLabels: Record<string, string> = {
  active: 'Aktif',
  graduated: 'Lulus',
  transferred: 'Pindah',
}
const statusColors: Record<string, string> = {
  active: 'bg-emerald-50 text-emerald-700 border-emerald-100',
  graduated: 'bg-primary-50 text-primary-700 border-primary-100',
  transferred: 'bg-amber-50 text-amber-700 border-amber-100',
}
const genderLabels: Record<string, string> = {
  male: 'Laki-laki',
  female: 'Perempuan',
}

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchStudent()
  }
})

async function fetchStudent() {
  isLoading.value = true
  try {
    const response = await studentService.getStudent(schoolStore.currentSchoolId!, studentId.value)
    student.value = response.data
  } catch {
    error.value = 'Gagal mengambil data siswa.'
  } finally {
    isLoading.value = false
  }
}

function formatDate(date: string | null): string {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function getParentDisplayName(): string {
  if (!student.value?.parent) return '-'
  const names = [student.value.parent.father_name, student.value.parent.mother_name].filter(Boolean)
  return names.join(' & ') || student.value.parent.email
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.push({ name: 'StudentList' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Detail Siswa</h1>
          <p class="text-sm text-muted">Informasi lengkap peserta didik</p>
        </div>
      </div>
      <BaseButton v-if="isHeadmaster && student" variant="primary" @click="router.push({ name: 'StudentEdit', params: { id: student.id } })">
        <template #prepend><Icon name="lucide:edit-3" class="w-4 h-4" /></template>
        Edit Data
      </BaseButton>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="animate-fade-in space-y-6">
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
          <Skeleton width="6rem" height="6rem" class="rounded-2xl shrink-0" />
          <div class="space-y-3 w-full">
            <Skeleton width="40%" height="2rem" />
            <div class="flex gap-2">
              <Skeleton width="80px" height="1.5rem" class="rounded-full" />
              <Skeleton width="120px" height="1.5rem" class="rounded-full" />
            </div>
          </div>
        </div>
        <div class="p-8 space-y-10">
          <div class="space-y-6">
            <Skeleton width="180px" height="1.5rem" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
              <div v-for="i in 6" :key="i" class="space-y-2">
                <Skeleton width="100px" height="0.75rem" />
                <Skeleton width="180px" height="1.25rem" />
              </div>
            </div>
          </div>
        </div>
      </BaseCard>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="animate-fade-in py-12 px-6 text-center bg-white border border-slate-100 rounded-[2.5rem] shadow-xl shadow-primary-900/5 max-w-lg mx-auto">
      <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center text-rose-500 mx-auto mb-6 border border-rose-100 shadow-sm">
        <Icon name="lucide:alert-circle" class="w-10 h-10" />
      </div>
      <h3 class="text-xl font-black text-heading mb-2">Terjadi Kesalahan</h3>
      <p class="text-sm text-slate-500 font-medium mb-8 leading-relaxed">
        {{ error }}<br>Silakan periksa koneksi internet Anda atau hubungi admin jika masalah berlanjut.
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <BaseButton variant="primary" @click="fetchStudent" class="px-8">
          <template #prepend><Icon name="lucide:refresh-cw" class="w-4 h-4" /></template>
          Coba Lagi
        </BaseButton>
        <BaseButton variant="outline" @click="router.push({ name: 'StudentList' })">Kembali</BaseButton>
      </div>
    </div>

    <!-- Detail Content -->
    <BaseCard v-else-if="student" class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <!-- Photo + Name Section -->
      <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-white border-2 border-slate-200 shadow-sm shrink-0">
          <img v-if="student.photo_url" :src="student.photo_url" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
            <Icon name="lucide:user" class="w-12 h-12" />
          </div>
        </div>
        <div class="space-y-2">
          <h2 class="text-2xl font-black text-heading">{{ student.name }}</h2>
          <div class="flex items-center gap-2 flex-wrap">
            <span :class="['px-3 py-1 rounded-full text-xs font-bold border', statusColors[student.status] || 'bg-slate-50 text-slate-700']">
              {{ statusLabels[student.status] || student.status }}
            </span>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
              {{ student.class?.name || 'Belum ada kelas' }}
            </span>
          </div>
        </div>
      </div>

      <div class="p-8 space-y-10">
        <!-- Data Siswa -->
        <div class="space-y-6">
          <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
            <Icon name="lucide:backpack" class="w-5 h-5 text-primary-600" /> Informasi Siswa
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:user" class="w-3 h-3" /> Nama Lengkap</p>
              <p class="text-base font-bold text-slate-800">{{ student.name }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:hash" class="w-3 h-3" /> NISN</p>
              <p class="text-base font-bold text-slate-800 font-mono">{{ student.nisn || '-' }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:calendar" class="w-3 h-3" /> Tanggal Lahir</p>
              <p class="text-base font-bold text-slate-800">{{ formatDate(student.birth_date) }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:user" class="w-3 h-3" /> Jenis Kelamin</p>
              <p class="text-base font-bold text-slate-800">{{ genderLabels[student.gender] || student.gender }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:log-in" class="w-3 h-3" /> Tanggal Masuk</p>
              <p class="text-base font-bold text-slate-800">{{ formatDate(student.enrollment_date) }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:school" class="w-3 h-3" /> Kelas</p>
              <p class="text-base font-bold text-slate-800">{{ student.class?.name || 'Belum ditentukan' }}</p>
            </div>
            <div class="space-y-1.5 md:col-span-2">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:map-pin" class="w-3 h-3" /> Alamat</p>
              <p class="text-base font-bold text-slate-800">{{ student.address || 'Belum terdata' }}</p>
            </div>
          </div>
        </div>

        <!-- Data Orang Tua -->
        <div v-if="student.parent" class="space-y-6">
          <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
            <Icon name="lucide:users" class="w-5 h-5 text-primary-600" /> Data Orang Tua / Wali
          </h3>
          <div
            class="p-5 bg-slate-50 rounded-2xl border border-slate-100 cursor-pointer hover:bg-primary-50/50 hover:border-primary-200 transition-all"
            @click="router.push({ name: 'ParentDetail', params: { id: student.parent.id } })"
          >
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-black text-xl shrink-0">
                {{ (student.parent.father_name || student.parent.mother_name || 'O').charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-base font-bold text-heading">{{ getParentDisplayName() }}</p>
                <p class="text-sm text-muted">{{ student.parent.email }} · {{ student.parent.phone }}</p>
              </div>
              <Icon name="lucide:chevron-right" class="w-5 h-5 text-slate-400" />
            </div>
          </div>
        </div>
      </div>
    </BaseCard>
  </div>
</template>
