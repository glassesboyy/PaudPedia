<script setup lang="ts">
/**
 * ReportSelectionView — Pilih siswa dan semester untuk generate rapor.
 * Role: Headmaster, Teacher (Pro Plan only)
 */
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { reportService } from '@/features/reports/services/report.service'
import { http as api } from '@/services/http/client'
import type { ClassRoom, Student } from '@/types'
import ProPlanGate from '@/features/finances/components/ProPlanGate.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const downloadingId = ref<number | null>(null)
const error = ref('')

const classes = ref<ClassRoom[]>([])
const students = ref<Student[]>([])

const selectedClassId = ref<number | ''>('')
const selectedSemester = ref('1')

const filteredStudents = computed(() => {
  if (!selectedClassId.value) return students.value
  return students.value.filter(s => s.class_id === selectedClassId.value)
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
    classes.value = res.data.data
    if (classes.value.length > 0) selectedClassId.value = classes.value[0]?.id ?? ''
  } catch { /* silent */ }
}

async function fetchStudents() {
  try {
    const res = await api.get<{ data: Student[] }>(`/api/v1/schools/${schoolStore.currentSchoolId}/students`)
    students.value = res.data.data
  } catch { /* silent */ }
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
    const blob = new Blob([res.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `Rapor_${studentId}_Semester${selectedSemester.value}.pdf`
    a.click()
    window.URL.revokeObjectURL(url)
  } catch {
    error.value = 'Gagal mengunduh rapor. Pastikan data absensi & penilaian sudah terisi.'
  } finally {
    downloadingId.value = null
  }
}

function previewReport(studentId: number) {
  router.push({
    name: 'ReportPreview',
    params: { studentId: String(studentId) },
    query: { semester: selectedSemester.value, academic_year: selectedAcademicYear.value },
  })
}
</script>

<template>
  <div class="max-w-5xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.push({ name: 'Dashboard' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">Laporan / Rapor Siswa</h1>
        <p class="text-sm text-muted">Generate dan unduh rapor perkembangan peserta didik</p>
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
      <BaseCard class="p-6 space-y-4">
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">Pilih Kelas & Semester</h3>
        <div class="flex flex-wrap items-center gap-4">
          <select v-model="selectedClassId" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500">
            <option value="">Semua Kelas</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} ({{ c.academic_year }})</option>
          </select>
          <select v-model="selectedSemester" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-primary-500">
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
          </select>
        </div>
      </BaseCard>

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
          <div v-for="student in filteredStudents" :key="student.id" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors">
            <div class="w-10 h-10 rounded-full bg-primary-50 flex items-center justify-center">
              <span class="text-sm font-bold text-primary-600">{{ student.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-slate-800 truncate">{{ student.name }}</p>
              <p class="text-xs text-slate-400">NISN: {{ student.nisn || '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
              <BaseButton variant="ghost" size="sm" @click="previewReport(student.id)">
                <template #prepend><Icon name="lucide:eye" class="w-4 h-4" /></template>
                Preview
              </BaseButton>
              <BaseButton variant="primary" size="sm" :loading="downloadingId === student.id" @click="downloadReport(student.id)">
                <template #prepend><Icon name="lucide:download" class="w-4 h-4" /></template>
                PDF
              </BaseButton>
            </div>
          </div>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
