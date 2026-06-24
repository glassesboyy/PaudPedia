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
const searchQuery = ref('')
const showAdvancedFilters = ref(false)

const activeFiltersCount = computed(() => {
  let count = 0
  if (selectedClassId.value) count++
  if (selectedStatus.value) count++
  if (selectedSemester.value !== '1') count++
  return count
})

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
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(s => 
      s.name.toLowerCase().includes(q) || 
      (s.nisn && s.nisn.toLowerCase().includes(q))
    )
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

watch([selectedClassId, selectedSemester, selectedStatus, searchQuery], () => {
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
      
      <!-- Actions and Filters -->
      <div>
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
          <!-- Search -->
          <div class="relative flex-1 max-w-sm">
            <Icon name="lucide:search" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
            <input 
              v-model="searchQuery" 
              placeholder="Cari berdasarkan nama atau NISN..." 
              class="w-full h-[42px] pl-11 pr-4 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all placeholder:text-slate-400"
            />
          </div>
          
          <!-- Filter Toggle -->
          <BaseButton 
              variant="outline" 
              size="md" 
              @click="showAdvancedFilters = !showAdvancedFilters"
              :class="showAdvancedFilters ? 'bg-primary-50 text-primary-700 border-primary-200' : ''"
            > 
            <Icon name="lucide:sliders-horizontal" class="w-4 h-4" />
            Filter Lanjutan
          </BaseButton>
        </div>

        <!-- Advanced Filters Panel -->
        <div v-show="showAdvancedFilters" class="p-4 bg-surface rounded-xl border border-border grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 animate-in slide-in-from-top-2 duration-200">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kelas</label>
            <select v-model="selectedClassId" @change="handleFilterChange" class="w-full h-[40px] px-3 rounded-lg border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Kelas</option>
              <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Semester</label>
            <select v-model="selectedSemester" @change="handleFilterChange" class="w-full h-[40px] px-3 rounded-lg border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="1">Semester 1 (Ganjil)</option>
              <option value="2">Semester 2 (Genap)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Status Rapor</label>
            <select v-model="selectedStatus" class="w-full h-[40px] px-3 rounded-lg border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all">
              <option value="">Semua Status</option>
              <option value="generated">Sudah Disusun</option>
              <option value="not_generated">Belum Disusun</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Table -->
      <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap">Siswa</th>
                <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap">NISN</th>
                <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap">Kelas</th>
                <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap text-center">Status Rapor</th>
                <th class="px-6 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <tr v-if="isLoading" v-for="i in 5" :key="i">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <Skeleton width="2.5rem" height="2.5rem" class="rounded-xl" />
                    <div class="space-y-2">
                      <Skeleton width="140px" height="0.875rem" />
                      <Skeleton width="80px" height="0.6rem" />
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4"><Skeleton width="90px" height="0.875rem" /></td>
                <td class="px-6 py-4"><Skeleton width="110px" height="0.875rem" /></td>
                <td class="px-6 py-4 text-center"><Skeleton width="80px" height="1.25rem" class="rounded-full mx-auto" /></td>
                <td class="px-6 py-4 text-right"><Skeleton width="80px" height="2rem" class="ml-auto" /></td>
              </tr>
              <tr v-else-if="filteredStudents.length === 0">
                <td colspan="5" class="px-8 py-20 text-center">
                  <div class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 border-2 border-dashed border-slate-200">
                      <Icon name="lucide:users" class="w-10 h-10" stroke-width="1.5" />
                    </div>
                    <div>
                      <p class="text-lg font-bold text-heading">Belum ada Siswa</p>
                      <p class="text-sm text-slate-500">Tidak ada siswa yang sesuai dengan filter yang dipilih.</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-else v-for="student in paginatedStudents" :key="student.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-primary-50 flex items-center justify-center shrink-0 border border-primary-100 shadow-sm">
                      <img v-if="student.photo_url" :src="student.photo_url" class="w-full h-full object-cover" />
                      <span v-else class="text-sm font-bold text-primary-600">{{ student.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm font-bold text-slate-800 truncate">{{ student.name }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="text-sm text-slate-600 font-mono">{{ student.nisn || '-' }}</span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-md border border-primary-100 whitespace-nowrap">{{ student.class?.name || '-' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span v-if="generatedReportStudentIds.includes(student.id)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                    <Icon name="lucide:check-circle-2" class="w-3.5 h-3.5" /> Sudah
                  </span>
                  <span v-else class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100">
                    <Icon name="lucide:clock" class="w-3.5 h-3.5" /> Belum
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <template v-if="generatedReportStudentIds.includes(student.id)">
                      <BaseButton variant="primary" size="sm" :loading="downloadingId === student.id" @click="downloadReport(student.id)" class="shadow-sm">
                        <template #prepend><Icon name="lucide:download" class="w-3.5 h-3.5" /></template>
                        Unduh PDF
                      </BaseButton>
                    </template>
                    <template v-else>
                      <BaseButton v-if="schoolStore.isTeacher" variant="outline" size="sm" @click="navigateToAssessment(student.id)" class="text-primary-600 border-primary-200 hover:bg-primary-50">
                        <template #prepend><Icon name="lucide:file-edit" class="w-3.5 h-3.5" /></template>
                        Susun Rapor
                      </BaseButton>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <Pagination
          v-if="filteredStudents.length > 0"
          :current-page="currentPage"
          :last-page="totalPages"
          :total-items="filteredStudents.length"
          :items-per-page="itemsPerPage"
          @page-change="page => currentPage = page"
          class="p-4 border-t border-slate-100 bg-slate-50/50"
        />
      </BaseCard>
    </template>
  </div>
</template>
