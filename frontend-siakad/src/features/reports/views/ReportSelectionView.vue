<script setup lang="ts">
/**
 * ReportSelectionView — Pilih siswa dan semester untuk generate rapor.
 * Role: Headmaster, Teacher (Pro Plan only)
 */
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { reportService } from '@/features/reports/services/report.service'
import { http as api } from '@/services/http/client'
import type { ClassRoom, Student } from '@/types'
import ProPlanGate from '@/features/finances/components/ProPlanGate.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { Pagination } from '@/components/ui'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const downloadingId = ref<number | null>(null)
const error = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])
const generatedReportStudentIds = ref<number[]>([])

const selectedClassId = ref<number | ''>('')
const selectedSemester = ref('1')
const selectedStatus = ref('')

const filteredStudents = computed(() => {
  let list = students.value
  if (selectedClassId.value) {
    list = list.filter(s => s.class_id === selectedClassId.value)
  }
  if (selectedStatus.value === 'generated') {
    list = list.filter(s => generatedReportStudentIds.value.includes(s.id))
  } else if (selectedStatus.value === 'not_generated') {
    list = list.filter(s => !generatedReportStudentIds.value.includes(s.id))
  }
  return list
})

const currentPage = ref(1)
const itemsPerPage = 20 // TODO: Revert to 20 after testing

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredStudents.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage))

watch([selectedClassId, selectedSemester, selectedStatus], () => {
  currentPage.value = 1
})

const selectedClassName = computed(() => {
  const cls = classes.value.find(c => c.id === selectedClassId.value)
  return cls?.name || ''
})

const selectedAcademicYear = computed(() => {
  const cls = classes.value.find(c => c.id === selectedClassId.value)
  return cls?.academic_year || ''
})

onMounted(async () => {
  if (schoolStore.isPro) {
    await Promise.all([fetchClasses(), fetchStudents()])
  }
  isLoading.value = false
})

async function fetchClasses() {
  try {
    const res = await api.get<{ data: ClassRoom[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/classes`)
    classes.value = (res as any).data
    if (classes.value.length > 0) selectedClassId.value = classes.value[0]?.id ?? ''
  } catch { /* silent */ }
}

async function fetchStudents() {
  try {
    const res = await api.get<{ data: Student[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/students?only_my_class=true&per_page=100`)
    students.value = (res as any).data
    await fetchReportsStatus()
  } catch { /* silent */ }
}

async function fetchReportsStatus() {
  if (!selectedClassId.value) {
    generatedReportStudentIds.value = []
    return
  }
  isLoading.value = true
  try {
    const res = await reportService.getReportsStatusList(schoolStore.currentSchoolId!, selectedClassId.value as number, {
      semester: selectedSemester.value,
      academic_year: selectedAcademicYear.value
    })
    generatedReportStudentIds.value = (res as any).generated_student_ids || []
  } catch { /* silent */ } finally {
    isLoading.value = false
  }
}

function handleFilterChange() {
  fetchReportsStatus()
}

function navigateToAssessment(studentId: number) {
  router.push({
    name: 'AssessmentReport',
    query: {
      class_id: selectedClassId.value,
      student_id: studentId,
      semester: selectedSemester.value
    }
  })
}

async function downloadReport(studentId: number) {
  if (downloadingId.value) return
  downloadingId.value = studentId
  error.value = ''
  try {
    const res = await reportService.downloadPdf(schoolStore.currentSchoolId!, studentId, {
      semester: selectedSemester.value,
      academic_year: selectedAcademicYear.value,
    })
    // Trigger download
    const blob = new Blob([res as any], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `Rapor_${studentId}_Semester${selectedSemester.value}.pdf`
    a.click()
    window.URL.revokeObjectURL(url)
  } catch (err: any) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Gagal mengunduh rapor. Pastikan data rapor siswa telah disusun. Anda bisa menyusunnya pada menu "Rapor Naratif".'
    }
  } finally {
    downloadingId.value = null
  }
}

</script>

<template>
  <div class="animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">Laporan / Rapor Siswa</h1>
        <p class="text-sm text-muted">Unduh rapor perkembangan peserta didik</p>
      </div>
    </div>

    <ProPlanGate v-if="!schoolStore.isPro" featureName="Laporan / Rapor" />

    <template v-else>
      <!-- Error -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <Icon name="lucide:alert-circle" class="w-5 h-5 text-red-600 shrink-0" />
        <p class="text-sm font-medium text-red-800">{{ error }}</p>
        <button @click="error = ''" class="ml-auto text-red-400 hover:text-red-600"><Icon name="lucide:x" class="w-4 h-4" /></button>
      </div>

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="flex-1 sm:w-64">
          <select v-model="selectedClassId" @change="handleFilterChange" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
            <option value="">Semua Kelas</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="flex-1 sm:w-64">
          <select v-model="selectedSemester" @change="handleFilterChange" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
          </select>
        </div>
        <div class="flex-1 sm:w-64">
          <select v-model="selectedStatus" class="w-full h-[42px] px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
            <option value="">Semua Status</option>
            <option value="generated">Sudah Disusun</option>
            <option value="not_generated">Belum Disusun</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <BaseCard v-if="isLoading" class="p-6"><Skeleton height="16rem" /></BaseCard>

      <!-- Student List -->
      <BaseCard v-else class="p-0 overflow-hidden border-none shadow-xl">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-lg font-black text-heading flex items-center gap-2">
            <Icon name="lucide:file-text" class="w-5 h-5 text-primary-500" /> Daftar Siswa
          </h3>
          <p class="text-xs text-slate-400 font-medium">{{ filteredStudents.length }} siswa</p>
        </div>

        <div v-if="filteredStudents.length === 0" class="p-12 text-center">
          <Icon name="lucide:users" class="w-12 h-12 text-slate-200 mx-auto mb-3" />
          <p class="text-sm text-slate-400 font-medium">Tidak ada siswa pada kelas yang dipilih</p>
        </div>

        <div v-else class="divide-y divide-slate-100">
          <div v-for="student in paginatedStudents" :key="student.id" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-50 flex items-center justify-center shrink-0">
              <img v-if="student.photo_url" :src="student.photo_url" class="w-full h-full object-cover" />
              <span v-else class="text-sm font-bold text-primary-600">{{ student.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-slate-800 truncate">{{ student.name }}</p>
              <p class="text-xs text-slate-400">NISN: {{ student.nisn || '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
              <template v-if="generatedReportStudentIds.includes(student.id)">
                <BaseButton variant="primary" size="sm" :loading="downloadingId === student.id" @click="downloadReport(student.id)">
                  <template #prepend><Icon name="lucide:download" class="w-3 h-3" /></template>
                  Unduh PDF
                </BaseButton>
              </template>
              <template v-else>
                <BaseButton v-if="schoolStore.isTeacher" variant="outline" size="sm" @click="navigateToAssessment(student.id)" class="text-primary-600 border-primary-200 hover:bg-primary-50">
                  <template #prepend><Icon name="lucide:file-edit" class="w-3 h-3" /></template>
                  Susun Rapor
                </BaseButton>
                <BaseButton v-else variant="outline" size="sm" disabled class="opacity-50">
                  <template #prepend><Icon name="lucide:clock" class="w-3 h-3" /></template>
                  Belum Disusun
                </BaseButton>
              </template>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <Pagination
          v-if="filteredStudents.length > 0"
          :current-page="currentPage"
          :last-page="totalPages"
          :total-items="filteredStudents.length"
          :items-per-page="itemsPerPage"
          @page-change="page => currentPage = page"
        />
      </BaseCard>
    </template>
  </div>
</template>
