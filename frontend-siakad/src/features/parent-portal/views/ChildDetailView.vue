<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import { attendanceService } from '@/features/attendance/services/attendance.service'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { financeService } from '@/features/finances/services/finance.service'
import { reportService } from '@/features/reports/services/report.service'
import type { Student } from '@/types'
import type { StudentFinanceSummary } from '@/features/finances/types/finance.types'
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
const selectedAcademicYear = ref<string>('')
const attendanceFilter = ref('all') // 'semester1', 'semester2', 'all'
const assessmentSemesterFilter = ref('all') // '1', '2', 'all'
const isAttendanceLoading = ref(false)
const isAssessmentLoading = ref(false)
const financeData = ref<StudentFinanceSummary | null>(null)
const isFinanceLoading = ref(false)
const isDownloadingSemester1 = ref(false)
const isDownloadingSemester2 = ref(false)

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
  // We compute locally, no need to re-fetch
})

watch(assessmentSemesterFilter, () => {
  // Simulate loading for better UX consistency
  isAssessmentLoading.value = true
  setTimeout(() => {
    isAssessmentLoading.value = false
  }, 400)
})

const availableAcademicYears = computed(() => {
  const years = new Set<string>()
  
  if (assessmentData.value?.history) {
    assessmentData.value.history.forEach(h => {
      if (h.academic_year) years.add(h.academic_year)
    })
  }
  
  if (attendanceData.value?.history) {
    attendanceData.value.history.forEach(h => {
      const date = new Date(h.date)
      const month = date.getMonth() + 1 // 1-12
      const year = date.getFullYear()
      if (month >= 7) {
        years.add(`${year}/${year + 1}`)
      } else {
        years.add(`${year - 1}/${year}`)
      }
    })
  }

  // Fallback to current year if empty
  if (years.size === 0) {
    const currentYear = new Date().getFullYear()
    const currentMonth = new Date().getMonth() + 1
    if (currentMonth >= 7) {
      years.add(`${currentYear}/${currentYear + 1}`)
    } else {
      years.add(`${currentYear - 1}/${currentYear}`)
    }
  }
  
  return Array.from(years).sort().reverse()
})

const isAcademicYearInitialized = ref(false)

watch([availableAcademicYears, student, isLoading], () => {
  if (isAcademicYearInitialized.value) return
  if (isLoading.value) return // Wait until all data is fetched

  if (student.value?.class?.academic_year && availableAcademicYears.value.includes(student.value.class.academic_year)) {
    selectedAcademicYear.value = student.value.class.academic_year
    isAcademicYearInitialized.value = true
  } else if (availableAcademicYears.value.length > 0 && availableAcademicYears.value[0]) {
    selectedAcademicYear.value = availableAcademicYears.value[0]
    isAcademicYearInitialized.value = true
  }
}, { immediate: true, deep: true })

async function fetchAttendance() {
  // Kept for backward compatibility if needed, but data is fetched in fetchStudent.
}

async function fetchStudent() {
  isLoading.value = true
  try {
    const response = await studentService.getStudent(schoolStore.currentSchoolId!, studentId.value)
    student.value = response.data

    try {
      isAttendanceLoading.value = true
      const [attRes, assRes] = await Promise.all([
        attendanceService.getStudentHistory(schoolStore.currentSchoolId!, studentId.value, {}),
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

async function fetchFinances() {
  if (!schoolStore.currentSchoolId || !studentId.value || !schoolStore.isPro) return
  isFinanceLoading.value = true
  try {
    const res = await financeService.getStudentFinances(schoolStore.currentSchoolId!, studentId.value)
    financeData.value = res as any
  } catch { /* silent */ }
  finally { isFinanceLoading.value = false }
}

async function downloadRapor(semesterTarget: string) {
  if (!schoolStore.currentSchoolId) return
  
  if (semesterTarget === '1') isDownloadingSemester1.value = true
  else isDownloadingSemester2.value = true

  try {
    const res = await reportService.downloadPdf(schoolStore.currentSchoolId!, studentId.value, {
      semester: semesterTarget,
      academic_year: selectedAcademicYear.value
    })
    const blob = new Blob([res as any], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    const safeYear = (selectedAcademicYear.value || '').replace('/', '-')
    a.download = `Rapor_${student.value?.name || studentId.value}_Semester${semesterTarget}_${safeYear}.pdf`
    a.click()
    window.URL.revokeObjectURL(url)
  } catch (err: any) {
    if (err.response?.data?.message) {
      alert(err.response.data.message)
    } else {
      alert('Gagal mengunduh rapor. Pastikan data rapor telah disusun.')
    }
  }
  finally { 
    if (semesterTarget === '1') isDownloadingSemester1.value = false
    else isDownloadingSemester2.value = false
  }
}

function formatCurrency(val: number): string {
  return 'Rp ' + val.toLocaleString('id-ID')
}

function formatDate(date: string | null): string {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const filteredAssessmentHistory = computed(() => {
  if (!assessmentData.value || !selectedAcademicYear.value) return []
  let history = assessmentData.value.history.filter(group => group.academic_year === selectedAcademicYear.value)
  if (assessmentSemesterFilter.value !== 'all') {
    history = history.filter(group => group.semester === assessmentSemesterFilter.value)
  }
  return history
})

function getAvailableMonths(semester: string) {
  if (semester === '1') {
    return [
      { value: '7', label: 'Juli' },
      { value: '8', label: 'Agustus' },
      { value: '9', label: 'September' },
      { value: '10', label: 'Oktober' },
      { value: '11', label: 'November' },
      { value: '12', label: 'Desember' }
    ]
  } else {
    return [
      { value: '1', label: 'Januari' },
      { value: '2', label: 'Februari' },
      { value: '3', label: 'Maret' },
      { value: '4', label: 'April' },
      { value: '5', label: 'Mei' },
      { value: '6', label: 'Juni' }
    ]
  }
}

function getScaleForMonth(matrix: Record<string, any>, indicatorId: number, monthNum: string) {
  if (!matrix || !matrix[indicatorId]) return null
  const monthPad = monthNum.toString().padStart(2, '0')
  const key = Object.keys(matrix[indicatorId]).find(k => k.endsWith(`-${monthPad}`))
  if (key) return matrix[indicatorId][key]
  return null
}

function getFinalScale(matrix: Record<string, any>, indicatorId: number, semester: string) {
  if (!matrix || !matrix[indicatorId]) return null
  const months = getAvailableMonths(semester).map(m => m.value)
  let totalPoints = 0
  let count = 0
  
  const scalePoints: Record<string, number> = { 'BB': 1, 'MB': 2, 'BSH': 3, 'BSB': 4 }
  const pointScales: Record<number, string> = { 1: 'BB', 2: 'MB', 3: 'BSH', 4: 'BSB' }
  const pointColors: Record<number, string> = { 
    1: 'bg-rose-100 text-rose-700 border-rose-200', 
    2: 'bg-amber-100 text-amber-700 border-amber-200', 
    3: 'bg-emerald-100 text-emerald-700 border-emerald-200', 
    4: 'bg-primary-100 text-primary-700 border-primary-200' 
  }

  for (const month of months) {
    const data = getScaleForMonth(matrix, indicatorId, month)
    if (data) {
      const scaleValue = data.scale
      if (scalePoints[scaleValue]) {
        totalPoints += scalePoints[scaleValue]
        count++
      }
    }
  }

  if (count === 0) return null

  const average = Math.round(totalPoints / count)
  return {
    scale: pointScales[average],
    color: pointColors[average]
  }
}

const attendanceFilterLabel = computed(() => {
  if (attendanceFilter.value === 'semester1') return 'Semester 1'
  if (attendanceFilter.value === 'semester2') return 'Semester 2'
  return 'Keseluruhan'
})

const computedAttendanceSummary = computed(() => {
  if (!attendanceData.value || !selectedAcademicYear.value) return null
  
  const parts = (selectedAcademicYear.value || '').split('/')
  const startYear = parseInt(parts[0] || '0')
  const endYear = parseInt(parts[1] || '0')
  
  let history = attendanceData.value.history.filter(h => {
    const date = new Date(h.date)
    const month = date.getMonth() + 1
    const year = date.getFullYear()
    
    if (year === startYear && month >= 7) return true
    if (year === endYear && month <= 6) return true
    return false
  })
  
  if (attendanceFilter.value === 'semester1') {
    history = history.filter(h => {
      const date = new Date(h.date)
      const month = date.getMonth() + 1
      return month >= 7 && month <= 12
    })
  } else if (attendanceFilter.value === 'semester2') {
    history = history.filter(h => {
      const date = new Date(h.date)
      const month = date.getMonth() + 1
      return month >= 1 && month <= 6
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
  <div class="w-full mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
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
      <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6">
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-white border-2 border-slate-200 shadow-sm shrink-0">
          <img v-if="student.photo_url" :src="student.photo_url" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
            <Icon name="lucide:user" class="w-12 h-12" />
          </div>
        </div>
        <div class="space-y-3 sm:space-y-2 mt-2 sm:mt-0">
          <h2 class="text-2xl font-black text-heading leading-tight">{{ student.name }}</h2>
          <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
            <span :class="['px-3 py-1 rounded-full text-xs font-bold border', statusColors[student.status] || 'bg-slate-50 text-slate-700']">
              {{ statusLabels[student.status] || student.status }}
            </span>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200 flex items-center gap-1.5">
              <Icon name="lucide:school" class="w-3.5 h-3.5" /> {{ student.class?.name || 'Belum ada kelas' }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Tabs Navigation -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 p-2 bg-slate-100 rounded-2xl mx-4 sm:mx-8 mt-4 border border-slate-200">
        <button 
          v-for="tab in [{id: 'profile', label: 'Profil', icon: 'lucide:user'}, {id: 'attendance', label: 'Kehadiran', icon: 'lucide:calendar-check'}, {id: 'assessment', label: 'Perkembangan', icon: 'lucide:trending-up'}, {id: 'finance', label: 'Keuangan', icon: 'lucide:wallet'}]" 
          :key="tab.id"
          @click="activeTab = tab.id; if (tab.id === 'finance' && !financeData) fetchFinances()"
          :class="['flex items-center justify-center gap-2 py-3 px-2 sm:px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-300', activeTab === tab.id ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50']"
        >
          <Icon :name="tab.icon" class="w-4 h-4 shrink-0" />
          <span class="truncate">{{ tab.label }}</span>
        </button>
      </div>



      <div class="p-5 sm:p-8 space-y-10 min-h-[400px]">
        <!-- Data Siswa (Tab: Profile) -->
        <div v-if="activeTab === 'profile'" class="space-y-6 animate-fade-in">
          <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
            <Icon name="lucide:backpack" class="w-5 h-5 text-primary-600" /> Profil Buah Hati
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 sm:gap-y-8">
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
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-100">
            <h3 class="text-lg font-black text-heading flex items-center gap-2">
              <Icon name="lucide:calendar-check" class="w-5 h-5 text-primary-600" /> Rekap Kehadiran
            </h3>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
              <select v-model="selectedAcademicYear" class="w-full sm:w-auto text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-2 sm:py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none cursor-pointer">
                <option v-for="year in availableAcademicYears" :key="year" :value="year">{{ year }}</option>
              </select>
              <select v-model="attendanceFilter" class="w-full sm:w-auto text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-2 sm:py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none cursor-pointer">
                <option value="semester1">Semester 1</option>
                <option value="semester2">Semester 2</option>
                <option value="all">Keseluruhan</option>
              </select>
            </div>
          </div>
          
          <div v-if="attendanceData" class="space-y-6 relative min-h-[200px]">
            <!-- Standardized Loader -->
            <div v-if="isAttendanceLoading" class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px] rounded-2xl transition-all duration-300">
              <div class="text-center">
                <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
                <p class="text-sm text-slate-500 font-medium">Memperbarui data kehadiran...</p>
              </div>
            </div>

            <div v-if="computedAttendanceSummary" class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 transition-all duration-300" :class="{'opacity-20 blur-[1px]': isAttendanceLoading}">
              <div class="p-3 sm:p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase">Hadir</p>
                <p class="text-xl sm:text-2xl font-black text-emerald-600 mt-1">{{ computedAttendanceSummary.present }}</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase">Sakit</p>
                <p class="text-xl sm:text-2xl font-black text-amber-500 mt-1">{{ computedAttendanceSummary.sick }}</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase">Izin</p>
                <p class="text-xl sm:text-2xl font-black text-blue-500 mt-1">{{ computedAttendanceSummary.permission }}</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <p class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase">Alfa</p>
                <p class="text-xl sm:text-2xl font-black text-rose-500 mt-1">{{ computedAttendanceSummary.absent }}</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-primary-50 border border-primary-100 text-center shadow-inner col-span-2 md:col-span-1">
                <p class="text-[11px] sm:text-xs font-bold text-primary-700 uppercase">Persentase</p>
                <p class="text-xl sm:text-2xl font-black text-primary-700 mt-1">{{ computedAttendanceSummary.percentage }}%</p>
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

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-100" :class="{'opacity-20 blur-[1px]': isAssessmentLoading}">
            <h3 class="text-lg font-black text-heading flex items-center gap-2">
              <Icon name="lucide:bar-chart-2" class="w-5 h-5 text-primary-600" /> Pencapaian Perkembangan
            </h3>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
              <select v-model="selectedAcademicYear" class="w-full sm:w-auto text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-2 sm:py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none cursor-pointer">
                <option v-for="year in availableAcademicYears" :key="year" :value="year">{{ year }}</option>
              </select>
              <select v-model="assessmentSemesterFilter" class="w-full sm:w-auto text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-2 sm:py-1.5 focus:ring-2 focus:ring-primary-500/20 outline-none cursor-pointer">
                <option value="all">Semua Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
              </select>
            </div>
          </div>
          
          <div v-if="filteredAssessmentHistory.length > 0" class="space-y-12 transition-all duration-300" :class="{'opacity-20 blur-[1px]': isAssessmentLoading}">
            <div v-for="group in filteredAssessmentHistory" :key="group.academic_year + group.semester" class="space-y-4">
              <h4 class="text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg inline-block">
                {{ group.academic_year }} - {{ group.semester_label }}
              </h4>
              
              <div class="overflow-hidden border border-slate-200 rounded-xl">
                <div class="overflow-x-auto custom-scrollbar">
                  <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead class="bg-slate-50">
                      <tr class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-600 font-bold">
                        <th class="px-4 py-3 min-w-[200px]">Program & Indikator</th>
                        <th v-for="m in getAvailableMonths(group.semester)" :key="m.value" class="px-2 py-3 text-center border-l border-slate-200 w-16">
                          {{ m.label.split(' ')[0] }}
                        </th>
                        <th class="px-3 py-3 text-center border-l border-slate-200 w-32 bg-primary-50/50 text-primary-800">
                          Capaian Akhir
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                      <template v-for="prog in assessmentData?.programs" :key="`prog-${prog.id}`">
                        <tr class="bg-primary-50/30">
                          <td :colspan="getAvailableMonths(group.semester).length + 2" class="px-4 py-2 font-bold text-primary-800 text-sm border-y border-primary-100">
                            {{ prog.name }}
                          </td>
                        </tr>
                        <tr v-for="ind in prog.indicators" :key="`ind-${ind.id}`" class="hover:bg-slate-50 transition-colors">
                          <td class="px-4 py-3 text-sm text-slate-700 pl-8">
                            • {{ ind.name }}
                          </td>
                          <td v-for="m in getAvailableMonths(group.semester)" :key="m.value" class="px-2 py-3 text-center border-l border-slate-100">
                            <div v-if="getScaleForMonth(group.matrix, ind.id, m.value)" 
                                :class="['inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold border shadow-sm cursor-help', 
                                          getScaleForMonth(group.matrix, ind.id, m.value)?.scale_color || 'bg-slate-100 text-slate-600 border-slate-200']"
                                :title="getScaleForMonth(group.matrix, ind.id, m.value)?.scale_label">
                              {{ getScaleForMonth(group.matrix, ind.id, m.value)?.scale }}
                            </div>
                            <div v-else class="text-slate-300 text-xs">-</div>
                          </td>
                          <td class="px-3 py-3 text-center border-l border-slate-100 bg-slate-50/50">
                            <div v-if="getFinalScale(group.matrix, ind.id, group.semester)" 
                                :class="['inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold border shadow-sm w-full', getFinalScale(group.matrix, ind.id, group.semester)?.color]">
                              {{ getFinalScale(group.matrix, ind.id, group.semester)?.scale }}
                            </div>
                            <div v-else class="text-slate-300 text-xs">-</div>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- Download Rapor (Pro Plan) specific to this semester -->
              <div v-if="schoolStore.isPro" class="mt-4 flex flex-col items-start gap-3 border border-slate-100 bg-slate-50/50 rounded-xl p-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full">
                  <BaseButton variant="primary" :disabled="!group.is_report_generated" :loading="group.semester === '1' ? isDownloadingSemester1 : isDownloadingSemester2" @click="downloadRapor(group.semester)" class="w-full sm:w-auto shadow-sm px-6 whitespace-nowrap shrink-0">
                    <template #prepend><Icon name="lucide:download" class="w-4 h-4" /></template>
                    Unduh Rapor {{ group.semester_label }}
                  </BaseButton>
                  <p v-if="!group.is_report_generated" class="text-xs sm:text-sm text-amber-600 font-medium flex items-center gap-1.5">
                    <Icon name="lucide:info" class="w-4 h-4" />
                    Rapor naratif untuk semester ini belum disusun oleh Wali Kelas, sehingga dokumen belum dapat diunduh.
                  </p>
                  <p v-else class="text-xs text-slate-500 font-medium">
                    Laporan mencakup rekap kehadiran dan pencapaian perkembangan khusus untuk semester ini.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="py-20 text-center border border-dashed border-slate-200 rounded-xl">
            <p class="text-slate-500 font-medium">Belum ada catatan penilaian tercatat.</p>
          </div>
        </div>

        <!-- Tab: Finance (Pro Plan only) -->
        <div v-if="activeTab === 'finance'" class="space-y-6 animate-fade-in">
          <!-- Loading -->
          <div v-if="isFinanceLoading" class="flex items-center justify-center py-16">
            <div class="text-center">
              <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
              <p class="text-sm text-muted">Memuat data keuangan...</p>
            </div>
          </div>

          <!-- Not Pro -->
          <div v-else-if="!schoolStore.isPro" class="py-16 text-center">
            <div class="w-16 h-16 bg-violet-50 rounded-full flex items-center justify-center mx-auto mb-4 text-violet-300">
              <Icon name="lucide:lock" class="w-8 h-8" />
            </div>
            <p class="text-slate-500 font-medium">Fitur keuangan hanya tersedia di Paket Pro.</p>
          </div>

          <!-- Data -->
          <template v-else-if="financeData">
            <!-- SPP Summary -->
            <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
              <Icon name="lucide:credit-card" class="w-5 h-5 text-primary-600" /> Riwayat SPP
            </h3>
            <div v-if="financeData.spp.history.length === 0" class="py-8 text-center text-sm text-slate-400">Belum ada data SPP</div>
            <div v-else class="space-y-3">
              <div v-for="rec in financeData.spp.history" :key="rec.id" 
                   class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-all gap-2 sm:gap-4"
                   :class="rec.is_paid ? 'bg-slate-50 border-slate-100' : 'bg-red-50 border-red-200'">
                <div>
                  <p class="text-sm font-bold text-left" :class="rec.is_paid ? 'text-slate-800' : 'text-red-900'">Tagihan SPP - {{ rec.month }}</p>
                  <p class="text-xs mt-1 text-left" :class="rec.is_paid ? 'text-slate-500' : 'text-red-700'">{{ rec.description || '-' }}</p>
                </div>
                <div class="text-left sm:text-right flex items-center justify-between sm:block">
                  <p class="text-sm font-bold mb-1.5" :class="rec.is_paid ? 'text-slate-900' : 'text-red-900'">{{ rec.amount_formatted }}</p>
                  <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full tracking-wider" :class="rec.is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-red-200 text-red-900 animate-pulse'">
                    {{ rec.is_paid ? 'Lunas' : 'Belum Lunas' }}
                  </span>
                </div>
              </div>
              <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl flex gap-3 text-sm text-blue-800">
                <Icon name="lucide:info" class="w-5 h-5 text-blue-500 shrink-0" />
                <p>Silakan lakukan pembayaran via tunai atau transfer bank kepada pihak administrasi sekolah.</p>
              </div>
            </div>

            <!-- Savings Summary -->
            <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100 mt-6">
              <Icon name="lucide:piggy-bank" class="w-5 h-5 text-emerald-600" /> Tabungan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-center">
              <div class="p-3 sm:p-4 rounded-xl bg-emerald-50">
                <p class="text-lg font-black text-emerald-700">{{ formatCurrency(financeData.savings.balance) }}</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase">Saldo</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-blue-50">
                <p class="text-lg font-black text-blue-700">{{ formatCurrency(financeData.savings.total_deposits) }}</p>
                <p class="text-[10px] font-bold text-blue-500 uppercase">Total Setor</p>
              </div>
              <div class="p-3 sm:p-4 rounded-xl bg-red-50">
                <p class="text-lg font-black text-red-700">{{ formatCurrency(financeData.savings.total_withdrawals) }}</p>
                <p class="text-[10px] font-bold text-red-500 uppercase">Total Tarik</p>
              </div>
            </div>
            <div v-if="financeData.savings.history.length > 0" class="space-y-2">
              <div v-for="rec in financeData.savings.history" :key="rec.id" class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div>
                  <p class="text-sm font-bold text-slate-800">{{ rec.transaction_type_label }}</p>
                  <p class="text-xs text-slate-400">{{ rec.description || '-' }}</p>
                </div>
                <p class="text-sm font-bold" :class="rec.transaction_type === 'withdrawal' ? 'text-red-600' : 'text-emerald-700'">
                  {{ rec.transaction_type === 'withdrawal' ? '-' : '+' }}{{ rec.amount_formatted }}
                </p>
              </div>
            </div>
          </template>
        </div>



        <!-- Alert Read Only -->
        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
          <div class="text-amber-600">
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