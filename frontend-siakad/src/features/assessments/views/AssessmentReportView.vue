<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { studentService } from '@/features/students/services/student.service'
import type { ClassRoom, Semester, DevelopmentProgram } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Icon from '@/components/ui/Icon/Icon.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import ProPlanGate from '@/features/finances/components/ProPlanGate.vue'
import { format } from 'date-fns'

const schoolStore = useSchoolStore()
const route = useRoute()

const classes = ref<ClassRoom[]>([])
const selectedClassId = ref<number | null>(null)
const selectedSemester = ref<Semester>('1')

const students = ref<any[]>([])
const selectedStudentId = ref<number | null>(null)

const programs = ref<DevelopmentProgram[]>([])
// matrix state: Record<indicator_id, Record<month_string, {scale_label, scale_color}>>
const matrix = ref<any>({})

const isLoading = ref(true)
const isSaving = ref(false)
const error = ref('')
const successMessage = ref('')

const selectedClass = computed(() => classes.value.find(c => c.id === selectedClassId.value))
const currentAcademicYear = computed<string | null>(() => selectedClass.value?.academic_year || null)

// Calculate the 6 months available based on the academic year and semester
const availableMonths = computed(() => {
  if (!currentAcademicYear.value) return []
  
  const years = currentAcademicYear.value.split('/') // e.g. ["2024", "2025"]
  if (years.length !== 2) return []

  const startYear = parseInt(years[0]!)
  const endYear = parseInt(years[1]!)
  
  const months = []
  if (selectedSemester.value === '1') {
    for (let m = 7; m <= 12; m++) {
      const date = new Date(startYear, m - 1, 1)
      months.push({ value: format(date, 'yyyy-MM'), label: format(date, 'MMM yyyy') })
    }
  } else {
    for (let m = 1; m <= 6; m++) {
      const date = new Date(endYear, m - 1, 1)
      months.push({ value: format(date, 'yyyy-MM'), label: format(date, 'MMM yyyy') })
    }
  }
  return months
})

const isMatrixComplete = computed(() => {
  if (programs.value.length === 0) return false
  if (!availableMonths.value.length) return false
  
  for (const prog of programs.value) {
    if (!prog.indicators || prog.indicators.length === 0) return false
    for (const ind of prog.indicators) {
      if (!matrix.value[ind.id]) return false
      for (const month of availableMonths.value) {
        if (!matrix.value[ind.id][month.value]) {
          return false
        }
      }
    }
  }
  return true
})

// Narrative Report Draft State
const reportDraft = ref({
  introduction_notes: '',
  closing_notes: '',
  recommendation: '',
  details: {} as Record<number, string> // program_id -> narrative
})

onMounted(async () => {
  if (route.query.semester) {
    selectedSemester.value = route.query.semester as Semester
  }
  await fetchClasses()
})

watch([selectedClassId], () => {
  if (selectedClassId.value) {
    fetchStudents()
  }
})

watch([selectedStudentId, selectedSemester], () => {
  if (selectedStudentId.value && selectedClassId.value) {
    fetchMatrixAndReport()
  } else {
    matrix.value = {}
    programs.value = []
  }
})

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  isLoading.value = true
  try {
    const res = await classService.getClasses(schoolStore.currentSchoolId as number, { per_page: 100 })
    classes.value = res.data
    if (classes.value.length > 0) {
      if (route.query.class_id && classes.value.some(c => c.id === Number(route.query.class_id))) {
        selectedClassId.value = Number(route.query.class_id)
      } else {
        selectedClassId.value = classes.value[0]?.id || null
      }
    }
  } catch (err: any) {
    error.value = 'Gagal memuat data kelas.'
  } finally {
    isLoading.value = false
  }
}

async function fetchStudents() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value) return
  isLoading.value = true
  selectedStudentId.value = null
  try {
    const res = await studentService.getStudents(schoolStore.currentSchoolId as number, {
      class_id: selectedClassId.value,
      per_page: 100
    })
    students.value = res.data
    if (students.value.length > 0) {
      if (route.query.student_id && students.value.some(s => s.id === Number(route.query.student_id))) {
        selectedStudentId.value = Number(route.query.student_id)
      } else {
        selectedStudentId.value = students.value[0].id
      }
    }
  } catch (err: any) {
    error.value = 'Gagal memuat data siswa.'
  } finally {
    isLoading.value = false
  }
}

async function fetchMatrixAndReport() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !selectedStudentId.value) return
  isLoading.value = true
  error.value = ''
  successMessage.value = ''
  try {
    // 1. Fetch Matrix
    const matrixRes = await assessmentService.getStudentMatrix(
      schoolStore.currentSchoolId as number,
      selectedStudentId.value,
      selectedSemester.value,
      currentAcademicYear.value as string
    )
    
    const matrixData = (matrixRes as any).data || matrixRes
    programs.value = matrixData.programs || []
    matrix.value = matrixData.matrix || {}

    // Initialize narrative details for each program
    const detailsObj: Record<number, string> = {}
    programs.value.forEach(p => {
      detailsObj[p.id] = ''
    })

    // 2. Fetch Report Draft
    const reportRes = await assessmentService.getStudentReport(
      schoolStore.currentSchoolId as number,
      selectedClassId.value,
      selectedStudentId.value,
      selectedSemester.value,
      currentAcademicYear.value as string
    )
    
    const reportData = (reportRes as any).data?.data || (reportRes as any).data || null
    
    if (reportData) {
      reportDraft.value.introduction_notes = reportData.introduction_notes || ''
      reportDraft.value.closing_notes = reportData.closing_notes || ''
      reportDraft.value.recommendation = reportData.recommendation || ''
      if (reportData.details) {
        reportData.details.forEach((d: any) => {
          detailsObj[d.program_id] = d.narrative
        })
      }
    } else {
      reportDraft.value.introduction_notes = ''
      reportDraft.value.closing_notes = ''
      reportDraft.value.recommendation = ''
    }

    reportDraft.value.details = detailsObj

  } catch (err: any) {
    error.value = 'Gagal memuat matriks dan rapor siswa.'
    console.error(err)
  } finally {
    isLoading.value = false
  }
}

async function saveReport() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !selectedStudentId.value) return
  
  isSaving.value = true
  error.value = ''
  successMessage.value = ''
  
  const payloadDetails = Object.keys(reportDraft.value.details).map(progId => {
    const pId = parseInt(progId)
    // Find final scale for this program. Let's just take the lowest average of its indicators or something?
    // Wait! The user wants the final_scale per INDICATOR or per PROGRAM?
    // User: "saya ingin pada bagian tabelnya, terdapat 1 kolom tambahan yakni "Capaian Akhir Semester" atau apapun kata katanya itu, dan ini isinya bukan diinput manual, melainkan hasil perhitungan dari skala penilaian pada bulan bulan sebelumnya (dan ini dibuat per indikator)"
    // The final_scale is calculated per INDICATOR.
    // BUT our `StudentReportDetail` is per PROGRAM.
    // Wait, the table in the UI is `StudentReportDetail`! We store narrative per Program. Do we store final_scale per Program? Or does the user want the Capaian Akhir Semester calculated per Program or per Indicator?
    // "dan ini dibuat per indikator" -> Oh, per indikator!
    // But `StudentReportDetail` stores `program_id`. Do we need to change it to `indicator_id`?
    // Wait, the "Penyusunan Rapor Naratif" table has rows for Programs, and under it, rows for Indicators.
    // I can just calculate it in the UI and not store it?
    // If I don't store it, how does the PDF know the Capaian Akhir Semester?
    // Let me re-read the PDF. The PDF shows "Aspek Perkembangan" (Indicator) and "Capaian".
    // Ah, wait! The PDF shows "Pencapaian Perkembangan: Aspek Perkembangan, Capaian, Catatan Guru".
    // If we use Rapor Naratif, what is the format? Program -> Narrative. There is NO "Capaian" per Indicator in the standard Kurikulum Merdeka PAUD Narrative report?
    // Oh, the user explicitly said:
    // "tampilan rapor pdf yang tergenerate, jika anda lihat pada screenshot yang saya berikan, tampilan dari pdf rapornya masih belum disesuaikan dengan perubahan mekanisme penilaian yang saat ini, dan saya ingin format dari rapor yang tergenerate ini diambil dari: 1. data anak (nama, foto, dll) 2. nilai (yang diambil dari fitur rapor naratif)"
    // Okay, if the PDF ONLY shows the narrative per Program, then it doesn't show the Capaian per Indicator.
    // BUT the user also said: "saya ingin pada bagian tabelnya, terdapat 1 kolom tambahan yakni "Capaian Akhir Semester" atau apapun kata katanya itu, dan ini isinya bukan diinput manual, melainkan hasil perhitungan dari skala penilaian pada bulan bulan sebelumnya (dan ini dibuat per indikator)".
    // So in the UI Table (which shows the 6-month matrix), there should be an extra column "Capaian Akhir Semester" per INDICATOR.
    // Should we save it? If the PDF doesn't need it, we don't need to save it. We just compute it on the fly!
    // But wait, the parent view tab "Perkembangan" needs "ringkasan pencapaian per indikator". If we don't save it, how does the parent view get it?
    // The parent view can also compute it on the fly from the matrix! But computing it on the fly requires the parent view to fetch the 6-month matrix.
    // Let's look at `AssessmentReportView.vue` matrix structure.
    return {
      program_id: pId,
      narrative: reportDraft.value.details[pId] || ''
    }
  })

  const payload = {
    semester: selectedSemester.value,
    academic_year: currentAcademicYear.value as string,
    introduction_notes: reportDraft.value.introduction_notes,
    closing_notes: reportDraft.value.closing_notes,
    recommendation: reportDraft.value.recommendation,
    details: payloadDetails
  }

  try {
    const res = await assessmentService.saveStudentReport(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      selectedStudentId.value,
      payload
    )
    successMessage.value = (res as any).message || 'Draft Rapor berhasil disimpan.'
  } catch (err: any) {
    if (err.response?.status === 422) {
      error.value = 'Semua input pada form Narasi Rapor wajib diisi.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Gagal menyimpan draft rapor.'
    }
  } finally {
    isSaving.value = false
  }
}

function getFinalScale(indicatorId: number) {
  if (!matrix.value[indicatorId]) return null
  const months = availableMonths.value.map(m => m.value)
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

  months.forEach(month => {
    if (matrix.value[indicatorId][month]) {
      const scale = matrix.value[indicatorId][month].scale
      if (scalePoints[scale]) {
        totalPoints += scalePoints[scale]
        count++
      }
    }
  })

  if (count === 0) return null
  const average = totalPoints / count
  const rounded = Math.round(average)
  return { scale: pointScales[rounded], color: pointColors[rounded] }
}

</script>

<template>
  <div class="space-y-6 animate-fade-in flex flex-col">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-heading">Penyusunan Rapor Naratif</h1>
        <p class="text-sm text-muted">Lihat rekapitulasi 6 bulan dan susun deskripsi perkembangan.</p>
      </div>
      <BaseButton v-if="schoolStore.isTeacher && schoolStore.isPro" variant="primary" :disabled="isSaving || !selectedStudentId" @click="saveReport" class="shadow-md w-full sm:w-auto">
        <Icon v-if="!isSaving" name="lucide:save" class="w-4 h-4 mr-2" />
        <div v-else class="animate-spin w-4 h-4 rounded-full border-2 border-white/30 border-t-white mr-2"></div>
        {{ isSaving ? 'Menyimpan...' : 'Simpan Draft Rapor' }}
      </BaseButton>
    </div>

    <ProPlanGate v-if="!schoolStore.isPro" featureName="Penyusunan Rapor" />

    <template v-else>
    <!-- Filters -->
    <BaseCard class="p-4 shrink-0">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas</label>
          <select v-model="selectedClassId" :disabled="isLoading" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all text-sm">
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} ({{ c.academic_year }})</option>
            <option v-if="classes.length === 0" value="">Belum ada kelas</option>
          </select>
        </div>
        
        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Semester</label>
          <select v-model="selectedSemester" :disabled="isLoading" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all text-sm">
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
          </select>
        </div>

        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</label>
          <select v-model="selectedStudentId" :disabled="isLoading" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all text-sm">
            <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }}</option>
            <option v-if="students.length === 0" value="">Belum ada siswa</option>
          </select>
        </div>
      </div>
    </BaseCard>

    <!-- Error/Success -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3 shrink-0">
      <Icon name="lucide:alert-circle" class="w-5 h-5 shrink-0" />
      <span>{{ error }}</span>
    </div>
    <div v-if="successMessage" class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3 shrink-0">
      <Icon name="lucide:check-circle" class="w-5 h-5 shrink-0" />
      <span>{{ successMessage }}</span>
    </div>

    <!-- Info Banners -->
    <div v-if="!schoolStore.isTeacher" class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 shrink-0">
      <Icon name="lucide:info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" />
      <div>
        <p class="text-sm font-bold text-blue-800">Mode Peninjau</p>
        <p class="text-xs mt-1 text-blue-700">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya wali kelas yang berhak menyusun Narasi Rapor.</p>
      </div>
    </div>
    <div v-else-if="!isMatrixComplete" class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 flex items-start gap-3 shrink-0">
      <Icon name="lucide:alert-circle" class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" />
      <div>
        <p class="text-sm font-bold">Penilaian Belum Lengkap</p>
        <p class="text-xs mt-1 text-amber-700">Input narasi rapor hanya bisa dibuat ketika seluruh matriks rekapitulasi nilai 6 bulan telah terisi penuh. Silakan lengkapi penilaian bulanan siswa terlebih dahulu.</p>
      </div>
    </div>

    <!-- Main Content Stacked -->
    <div class="flex-1 min-h-0 flex flex-col gap-6">
      
      <!-- TOP: Matrix View (Read-Only) -->
      <BaseCard class="flex-1 flex flex-col p-0 overflow-hidden shadow-sm h-full">
        <div class="px-5 py-4 border-b flex justify-between items-center shrink-0">
          <h2 class="font-bold text-xl flex items-center gap-2">
            Matriks Rekapitulasi (6 Bulan)
          </h2>
        </div>
        
        <div class="flex-1 overflow-auto p-0 relative custom-scrollbar">
          <div v-if="isLoading" class="p-6 space-y-4">
            <Skeleton width="100%" height="3rem" class="rounded-xl" />
            <Skeleton width="100%" height="2.5rem" />
            <Skeleton width="100%" height="2.5rem" />
            <Skeleton width="100%" height="2.5rem" />
            <Skeleton width="100%" height="2.5rem" />
          </div>

          <table class="w-full text-left border-collapse min-w-[600px]" v-else-if="programs.length > 0">
            <thead class="sticky top-0 bg-white z-10 shadow-sm">
              <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-600 font-bold">
                <th class="px-4 py-3 min-w-[200px] text-md">Program & Indikator</th>
                <th v-for="m in availableMonths" :key="m.value" class="px-2 py-3 text-center border-l border-slate-200 w-16">
                  {{ m.label.split(' ')[0] }}
                </th>
                <th class="px-3 py-3 text-center border-l border-slate-200 w-32 bg-primary-50/50 text-primary-800">
                  Capaian Akhir
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template v-for="prog in programs" :key="`prog-${prog.id}`">
                <tr class="bg-primary-50/30">
                  <td :colspan="availableMonths.length + 2" class="px-4 py-2 font-bold text-primary-800 text-sm border-y border-primary-100">
                    {{ prog.name }}
                  </td>
                </tr>
                <tr v-for="ind in prog.indicators" :key="`ind-${ind.id}`" class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 text-sm text-slate-700 pl-8">
                    • {{ ind.name }}
                  </td>
                  <td v-for="m in availableMonths" :key="m.value" class="px-2 py-3 text-center border-l border-slate-100">
                    <div v-if="matrix[ind.id] && matrix[ind.id][m.value]" 
                         :class="['inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold border cursor-help shadow-sm', 
                                  matrix[ind.id][m.value].scale_color || 'bg-slate-100 text-slate-600 border-slate-200']"
                         :title="matrix[ind.id][m.value].scale_label">
                      {{ matrix[ind.id][m.value].scale }}
                    </div>
                    <div v-else class="text-slate-300 text-xs">-</div>
                  </td>
                  <td class="px-3 py-3 text-center border-l border-slate-100 bg-slate-50/50">
                    <div v-if="getFinalScale(ind.id)" 
                         :class="['inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold border shadow-sm w-full', getFinalScale(ind.id)?.color]">
                      {{ getFinalScale(ind.id)?.scale }}
                    </div>
                    <div v-else class="text-slate-300 text-xs">-</div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          <div v-else-if="!isLoading" class="p-12 text-center text-slate-400">
            Pilih siswa untuk melihat matriks.
          </div>
        </div>
      </BaseCard>

      <!-- BOTTOM: Narrative Editor -->
      <BaseCard class="flex-1 flex flex-col p-0 overflow-hidden shadow-sm border-primary-100 ring-1 ring-primary-500/10 mb-8">
        <div class="px-5 py-4 border-b flex justify-between items-center shrink-0">
          <h2 class="font-bold text-xl flex items-center gap-2">
            Input Narasi Rapor
          </h2>
        </div>
        
        <div class="flex-1 overflow-auto p-5 space-y-6 custom-scrollbar relative">
          <div v-if="isLoading" class="space-y-6">
            <div class="space-y-2">
              <Skeleton width="30%" height="1.5rem" />
              <Skeleton width="60%" height="1rem" />
              <Skeleton width="100%" height="6rem" class="rounded-xl" />
            </div>
            <div class="space-y-2">
              <Skeleton width="40%" height="1.5rem" />
              <Skeleton width="70%" height="1rem" />
              <Skeleton width="100%" height="6rem" class="rounded-xl" />
            </div>
          </div>
          
          <template v-else>
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">1. Pendahuluan <span class="text-rose-500">*</span></label>
            <p class="text-xs text-slate-500 mb-2">Tuliskan pengantar rapor atau kalimat sambutan untuk orang tua.</p>
            <textarea v-model="reportDraft.introduction_notes" :disabled="!schoolStore.isTeacher || !isMatrixComplete" class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all resize-y min-h-[100px] disabled:opacity-70 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-600"></textarea>
          </div>

          <div class="space-y-6">
            <div class="flex items-center gap-3">
              <div class="h-px bg-slate-200 flex-1"></div>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Penilaian Per Program</span>
              <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            <div v-for="prog in programs" :key="`draft-${prog.id}`" class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
              <label class="text-sm font-bold text-slate-800">{{ prog.name }} <span class="text-rose-500">*</span></label>
              <textarea v-model="reportDraft.details[prog.id]" :disabled="!schoolStore.isTeacher || !isMatrixComplete" placeholder="Contoh: Ananda berkembang sangat baik dalam hal..." class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all resize-y min-h-[120px] shadow-sm disabled:opacity-70 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-600"></textarea>
            </div>
          </div>

          <div class="space-y-6">
            <div class="flex items-center gap-3 mt-6">
              <div class="h-px bg-slate-200 flex-1"></div>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Penutup & Rekomendasi</span>
              <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700">Penutup <span class="text-rose-500">*</span></label>
              <textarea v-model="reportDraft.closing_notes" :disabled="!schoolStore.isTeacher || !isMatrixComplete" class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all resize-y min-h-[100px] disabled:opacity-70 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-600"></textarea>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700">Rekomendasi / Saran <span class="text-rose-500">*</span></label>
              <textarea v-model="reportDraft.recommendation" :disabled="!schoolStore.isTeacher || !isMatrixComplete" class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white transition-all resize-y min-h-[100px] disabled:opacity-70 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-600"></textarea>
            </div>
          </div>
          </template>
        </div>
      </BaseCard>
    </div>
    </template>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
