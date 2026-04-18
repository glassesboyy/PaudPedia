<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import { attendanceService } from '@/features/attendance/services/attendance.service'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import type { Student } from '@/types'
import type { StudentAttendanceSummaryResponse } from '@/types/attendance.types'
import type { StudentAssessmentHistoryResponse } from '@/types/assessment.types'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('student'))
const studentId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const student = ref<Student | null>(null)
const attendanceData = ref<StudentAttendanceSummaryResponse | null>(null)
const assessmentData = ref<StudentAssessmentHistoryResponse | null>(null)
const error = ref('')

const activeTab = ref('profile')
const attendanceFilter = ref('month') // 'week', 'month', 'all'
const assessmentSemesterFilter = ref('all') // '1', '2', 'all'
const isAttendanceLoading = ref(false)
const isAssessmentLoading = ref(false)

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

watch(attendanceFilter, () => {
  fetchAttendance()
})

watch(assessmentSemesterFilter, () => {
  // Simulate loading for better UX consistency
  isAssessmentLoading.value = true
  setTimeout(() => {
    isAssessmentLoading.value = false
  }, 400)
})

async function fetchAttendance() {
  if (!schoolStore.currentSchoolId || !studentId.value) return
  
  isAttendanceLoading.value = true
  const params: any = {}
  if (attendanceFilter.value === 'month' || attendanceFilter.value === 'week') {
    params.month = new Date().getMonth() + 1
    params.year = new Date().getFullYear()
  }
  // For 'all', we leave params empty
  
  try {
    const res = await attendanceService.getStudentHistory(schoolStore.currentSchoolId!, studentId.value, params)
    attendanceData.value = (res as any)
  } catch (e) {
    console.error('Failed fetching attendance', e)
  } finally {
    isAttendanceLoading.value = false
  }
}

async function fetchStudent() {
  isLoading.value = true
  try {
    const response = await studentService.getStudent(schoolStore.currentSchoolId!, studentId.value)
    student.value = response.data

    try {
      isAttendanceLoading.value = true
      const [attRes, assRes] = await Promise.all([
        attendanceService.getStudentHistory(schoolStore.currentSchoolId!, studentId.value, { 
          month: new Date().getMonth() + 1, 
          year: new Date().getFullYear() 
        }),
        assessmentService.getStudentHistory(schoolStore.currentSchoolId!, studentId.value)
      ])
      attendanceData.value = (attRes as any)
      assessmentData.value = (assRes as any)
    } catch (e) {
      console.error('Failed fetching extra data', e)
    } finally {
      isAttendanceLoading.value = false
    }
  } catch {
    error.value = 'Gagal mengambil data anak.'
  } finally {
    isLoading.value = false
  }
}

function formatDate(date: string | null): string {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const filteredAssessmentHistory = computed(() => {
  if (!assessmentData.value) return []
  if (assessmentSemesterFilter.value === 'all') return assessmentData.value.history
  return assessmentData.value.history.filter(group => group.semester === assessmentSemesterFilter.value)
})

const attendanceFilterLabel = computed(() => {
  if (attendanceFilter.value === 'week') return 'Minggu Ini'
  if (attendanceFilter.value === 'month') return 'Bulan Ini'
  return 'Keseluruhan'
})

const computedAttendanceSummary = computed(() => {
  if (!attendanceData.value) return null
  
  let history = attendanceData.value.history
  
  if (attendanceFilter.value === 'week') {
    const now = new Date()
    const startOfWeek = new Date(now)
    // Find the nearest Sunday
    startOfWeek.setDate(now.getDate() - now.getDay())
    startOfWeek.setHours(0, 0, 0, 0)

    history = history.filter(h => {
      const hDate = new Date(h.date)
      hDate.setHours(0,0,0,0)
      return hDate >= startOfWeek
    })
  }
  
  const stats = {
    present: history.filter(h => h.status === 'present').length,
    sick: history.filter(h => h.status === 'sick').length,
    permission: history.filter(h => h.status === 'permission').length,
    absent: history.filter(h => h.status === 'absent').length,
    total: history.length
  }
  
  const percentage = stats.total > 0 ? ((stats.present / stats.total) * 100).toFixed(1) : '0'
  
  return { ...stats, percentage }
})
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.back()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-sm text-muted">{{ copy.subtitle }}</p>
      </div>
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
        <BaseButton variant="outline" @click="router.back()">Kembali</BaseButton>
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
      
      <!-- Tabs Navigation -->
      <div class="flex items-center gap-1 p-2 bg-slate-100 rounded-2xl mx-8 mt-4 border border-slate-200">
        <button 
          v-for="tab in [{id: 'profile', label: 'Profil', icon: 'lucide:user'}, {id: 'attendance', label: 'Kehadiran', icon: 'lucide:calendar-check'}, {id: 'assessment', label: 'Perkembangan', icon: 'lucide:trending-up'}]" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="['flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold transition-all duration-300', activeTab === tab.id ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50']"
        >
          <Icon :name="tab.icon" class="w-4 h-4" />
          {{ tab.label }}
        </button>
      </div>

      <div class="p-8 space-y-10 min-h-[400px]">
        <!-- Data Siswa (Tab: Profile) -->
        <div v-if="activeTab === 'profile'" class="space-y-6 animate-fade-in">
          <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
            <Icon name="lucide:backpack" class="w-5 h-5 text-primary-600" /> Profil Buah Hati
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
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:log-in" class="w-3 h-3" /> Tanggal Masuk Sekolah</p>
              <p class="text-base font-bold text-slate-800">{{ formatDate(student.enrollment_date) }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:school" class="w-3 h-3" /> Kelas Saat Ini</p>
              <p class="text-base font-bold text-slate-800">{{ student.class?.name || 'Belum ditentukan' }}</p>
            </div>
            <div class="space-y-1.5 md:col-span-2">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2"><Icon name="lucide:map-pin" class="w-3 h-3" /> Alamat Domisili</p>
              <p class="text-base font-bold text-slate-800">{{ student.address || 'Belum terdata' }}</p>
            </div>
          </div>
        </div>

        <!-- Attendance Data (Tab: Attendance) -->
        <div v-if="activeTab === 'attendance'" class="space-y-6 animate-fade-in">
          <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-lg font-black text-heading flex items-center gap-2">
              <Icon name="lucide:calendar-check" class="w-5 h-5 text-primary-600" /> Rekap Kehadiran
            </h3>
            <select v-model="attendanceFilter" class="text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none">
              <option value="week">Minggu Ini</option>
              <option value="month">Bulan Ini</option>
              <option value="all">Keseluruhan</option>
            </select>
          </div>
          
          <div v-if="attendanceData" class="space-y-6 relative min-h-[200px]">
            <!-- Standardized Loader -->
            <div v-if="isAttendanceLoading" class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px] rounded-2xl transition-all duration-300">
              <div class="text-center">
                <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
                <p class="text-sm text-slate-500 font-medium">Memperbarui data kehadiran...</p>
              </div>
            </div>

            <div v-if="computedAttendanceSummary" class="grid grid-cols-2 md:grid-cols-5 gap-4 transition-all duration-300" :class="{'opacity-20 blur-[1px]': isAttendanceLoading}">
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase">Hadir</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ computedAttendanceSummary.present }}</p>
              </div>
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase">Sakit</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ computedAttendanceSummary.sick }}</p>
              </div>
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase">Izin</p>
                <p class="text-2xl font-black text-blue-500 mt-1">{{ computedAttendanceSummary.permission }}</p>
              </div>
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-xs font-bold text-slate-500 uppercase">Alfa</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ computedAttendanceSummary.absent }}</p>
              </div>
              <div class="p-4 rounded-xl bg-primary-50 border border-primary-100 text-center shadow-inner">
                <p class="text-xs font-bold text-primary-700 uppercase">Persentase</p>
                <p class="text-2xl font-black text-primary-700 mt-1">{{ computedAttendanceSummary.percentage }}%</p>
              </div>
            </div>
            
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-center space-y-2">
              <p class="text-xs text-slate-500 font-bold">Menampilkan data periode: <span class="text-primary-600 uppercase tracking-wide">{{ attendanceFilterLabel }}</span></p>
              <p v-if="attendanceFilter === 'week'" class="text-[10px] text-slate-400">Dihitung otomatis berdasarkan rentang hari aktif minggu ini.</p>
            </div>
          </div>
          <div v-else class="py-12 text-center text-slate-400">Belum ada data kehadiran.</div>
        </div>

        <!-- Assessment Data (Tab: Assessment) -->
        <div v-if="activeTab === 'assessment'" class="space-y-6 animate-fade-in relative min-h-[300px]">
          <!-- Standardized Loader -->
          <div v-if="isAssessmentLoading" class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px] rounded-2xl transition-all duration-300">
            <div class="text-center">
              <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
              <p class="text-sm text-slate-500 font-medium">Menyaring data perkembangan...</p>
            </div>
          </div>

          <div class="flex items-center justify-between pb-2 border-b border-slate-100" :class="{'opacity-20 blur-[1px]': isAssessmentLoading}">
            <h3 class="text-lg font-black text-heading flex items-center gap-2">
              <Icon name="lucide:bar-chart-2" class="w-5 h-5 text-primary-600" /> Pencapaian Perkembangan
            </h3>
            <select v-model="assessmentSemesterFilter" class="text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none">
              <option value="all">Semua Semester</option>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
            </select>
          </div>
          
          <div v-if="filteredAssessmentHistory.length > 0" class="space-y-8 transition-all duration-300" :class="{'opacity-20 blur-[1px]': isAssessmentLoading}">
            <div v-for="group in filteredAssessmentHistory" :key="group.academic_year + group.semester" class="space-y-4">
              <h4 class="text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg inline-block">
                {{ group.academic_year }} - {{ group.semester_label }}
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in group.items" :key="item.id" class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm space-y-3">
                  <div class="flex justify-between items-start gap-4">
                    <p class="font-bold text-slate-800 leading-tight">{{ item.aspect }}</p>
                    <span class="px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-wider shrink-0" 
                      :class="{
                        'bg-rose-100 text-rose-700': item.scale === 'BB',
                        'bg-amber-100 text-amber-700': item.scale === 'MB',
                        'bg-emerald-100 text-emerald-700': item.scale === 'BSH',
                        'bg-primary-100 text-primary-700': item.scale === 'BSB'
                      }">
                      {{ item.scale }}
                    </span>
                  </div>
                  <p class="text-xs font-semibold text-slate-500">{{ item.description }}</p>
                  <div v-if="item.notes" class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-600 italic">
                    "{{ item.notes }}"
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="py-20 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
              <Icon name="lucide:bar-chart-2" class="w-8 h-8" />
            </div>
            <p class="text-slate-500 font-medium">Belum ada catatan penilaian untuk periode ini.</p>
          </div>
        </div>

        <!-- Alert Read Only -->
        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
          <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
            <Icon name="lucide:info" class="w-5 h-5" />
          </div>
          <div>
            <p class="text-sm font-bold text-amber-900 mb-0.5">Akun Peninjau (Orang Tua)</p>
            <p class="text-xs text-amber-700 leading-relaxed">Anda sedang melihat data resmi yang terdaftar di sekolah. Jika terdapat ketidaksesuaian data, silakan hubungi walikelas atau bagian administrasi sekolah.</p>
          </div>
        </div>
      </div>
    </BaseCard>
  </div>
</template>
