<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { PAUDScale } from '@/types'
import type { ClassRoom, Semester, DevelopmentProgram } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Icon from '@/components/ui/Icon/Icon.vue'
import { Pagination } from '@/components/ui'
import { format } from 'date-fns'

const schoolStore = useSchoolStore()

const classes = ref<ClassRoom[]>([])
const selectedClassId = ref<number | null>(null)
const selectedSemester = ref<Semester>('1')
const selectedMonth = ref<string>('')

const programs = ref<DevelopmentProgram[]>([])
const activeStudentId = ref<number | null>(null)

const students = ref<any[]>([])
const isLoadingClasses = ref(true)
const isLoadingStudents = ref(false)
const isSaving = ref(false)
const searchQuery = ref('')
const showValidationErrors = ref(false)
const error = ref('')
const successMessage = ref('')

// Form state: { [student_id]: { [indicator_id]: { scale, notes } } }
const formState = ref<Record<number, Record<number, { scale: PAUDScale | null, notes: string }>>>({})

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
    // July to December of startYear
    for (let m = 7; m <= 12; m++) {
      const date = new Date(startYear, m - 1, 1)
      months.push({
        value: format(date, 'yyyy-MM'),
        label: format(date, 'MMMM yyyy')
      })
    }
  } else {
    // January to June of endYear
    for (let m = 1; m <= 6; m++) {
      const date = new Date(endYear, m - 1, 1)
      months.push({
        value: format(date, 'yyyy-MM'),
        label: format(date, 'MMMM yyyy')
      })
    }
  }
  return months
})

const filteredStudents = computed(() => {
  if (!searchQuery.value) return students.value
  const q = searchQuery.value.toLowerCase()
  return students.value.filter(s => s.name.toLowerCase().includes(q) || (s.nisn && s.nisn.toLowerCase().includes(q)))
})

const currentPage = ref(1)
const itemsPerPage = 20 // TODO: Revert to 20 after testing

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredStudents.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage))

watch(searchQuery, () => {
  currentPage.value = 1
})

// Automatically set selectedMonth to the first available month when semester changes
watch(availableMonths, (newMonths) => {
  if (newMonths.length > 0 && !newMonths.find(m => m.value === selectedMonth.value)) {
    selectedMonth.value = newMonths[0]!.value
  }
}, { immediate: true })

onMounted(async () => {
  await fetchClasses()
})

watch([selectedClassId, selectedMonth, selectedSemester], () => {
  if (selectedClassId.value && selectedMonth.value) {
    // Prevent double fetching when class changes but month hasn't synced yet
    if (!availableMonths.value.find(m => m.value === selectedMonth.value)) {
      return // Wait for availableMonths watcher to update selectedMonth
    }
    fetchStudents()
  } else {
    students.value = []
    programs.value = []
  }
})

watch(students, (newStudents) => {
  if (newStudents.length > 0 && !activeStudentId.value) {
    activeStudentId.value = newStudents[0].student_id
  }
})

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  isLoadingClasses.value = true
  try {
    const res = await classService.getClasses(schoolStore.currentSchoolId as number, { per_page: 100 })
    classes.value = res.data
    if (classes.value.length > 0) {
      selectedClassId.value = classes.value[0]?.id || null
    }
  } catch (err: any) {
    error.value = 'Gagal memuat data kelas.'
  } finally {
    isLoadingClasses.value = false
  }
}

async function fetchStudents() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !selectedMonth.value) return
  isLoadingStudents.value = true
  error.value = ''
  successMessage.value = ''
  try {
    const res = await assessmentService.getAssessmentList(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      selectedMonth.value,
      selectedSemester.value,
      currentAcademicYear.value as string
    )
    
    const responseData = res as any

    students.value = responseData.data || []
    programs.value = responseData.programs || []

    // Set first student as active
    if (students.value.length > 0) {
      if (!activeStudentId.value || !students.value.find(s => s.student_id === activeStudentId.value)) {
        activeStudentId.value = students.value[0].student_id
      }
    } else {
      activeStudentId.value = null
    }

    // Initialize form state
    const newFormState: any = {}
    students.value.forEach(student => {
      newFormState[student.student_id] = {}
      programs.value.forEach(program => {
        program.indicators.forEach((indicator: any) => {
          const existingScale = student.assessments && student.assessments[indicator.id] ? student.assessments[indicator.id] : null
          newFormState[student.student_id][indicator.id] = {
            scale: existingScale || (schoolStore.isTeacher ? 'MB' : null),
            notes: '' // We could load existing notes if API returned them
          }
        })
      })
    })
    formState.value = newFormState

  } catch (err: any) {
    error.value = 'Gagal memuat data siswa.'
    console.error(err)
  } finally {
    isLoadingStudents.value = false
  }
}

const activeStudent = computed(() => students.value.find(s => s.student_id === activeStudentId.value))

async function saveAssessment() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !selectedMonth.value) return
  
  isSaving.value = true
  showValidationErrors.value = true
  
  // Flatten form state into array for API
  const payloadAssessments: any[] = []
  
  students.value.forEach(student => {
    programs.value.forEach(program => {
      program.indicators.forEach((indicator: any) => {
        const data = formState.value[student.student_id]?.[indicator.id as number]
        if (data && data.scale) {
          payloadAssessments.push({
            student_id: student.student_id,
            indicator_id: indicator.id,
            scale: data.scale,
            notes: data.notes || null
          })
        }
      })
    })
  })

  if (payloadAssessments.length === 0) {
    error.value = 'Belum ada penilaian yang diisi.'
    isSaving.value = false
    return
  }

  try {
    const payload = {
      month: selectedMonth.value,
      semester: selectedSemester.value,
      academic_year: currentAcademicYear.value as string,
      assessments: payloadAssessments
    }
    const res = await assessmentService.storeBulkAssessment(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      payload
    )
    successMessage.value = (res as any).message
    // Refetch to sync state
    fetchStudents()
  } catch (err: any) {
    error.value = 'Gagal menyimpan penilaian.'
  } finally {
    isSaving.value = false
  }
}

function getScaleColor(scale: string) {
  switch (scale) {
    case 'BB': return 'bg-rose-50 text-rose-700 border-rose-200'
    case 'MB': return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'BSH': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'BSB': return 'bg-primary-50 text-primary-700 border-primary-200'
    default: return 'bg-slate-50 text-slate-700 border-slate-200'
  }
}

const hasAnyAssessment = computed(() => {
  return students.value.some(s => s.assessments && Object.keys(s.assessments).length > 0)
})
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-heading">Penilaian Skala Perkembangan</h1>
        <p class="text-sm text-muted">Input pencatatan perkembangan anak secara periodik (Bulanan).</p>
      </div>
    </div>

    <!-- Filter Card -->
    <BaseCard class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Pilih Kelas</label>
          <select v-model="selectedClassId" :disabled="isLoadingClasses" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm transition-all">
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} ({{ c.academic_year }})</option>
            <option v-if="classes.length === 0" value="">Belum ada kelas</option>
          </select>
        </div>
        
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Semester</label>
          <select v-model="selectedSemester" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm transition-all">
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Bulan Penilaian</label>
          <select v-model="selectedMonth" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm transition-all">
            <option v-for="month in availableMonths" :key="month.value" :value="month.value">{{ month.label }}</option>
            <option v-if="availableMonths.length === 0" value="">-- Pilih --</option>
          </select>
        </div>
      </div>
    </BaseCard>

    <!-- Error/Success -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3 shadow-sm">
      <Icon name="lucide:alert-circle" class="w-5 h-5 shrink-0" />
      <span>{{ error }}</span>
    </div>
    <div v-if="!schoolStore.isTeacher" class="p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3 shadow-sm">
      <div class="p-1"><Icon name="lucide:info" class="w-4 h-4" /></div>
      <span class="text-sm">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya Guru Kelas yang dapat mengisikan penilaian bulanan.</span>
    </div>
    <div v-if="successMessage" class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
      <Icon name="lucide:check-circle" class="w-5 h-5 shrink-0" />
      <span>{{ successMessage }}</span>
    </div>

    <!-- Main Content: Student-Centric Layout -->
    <div v-if="isLoadingStudents" class="p-12 text-center text-slate-400 bg-white rounded-xl border border-slate-100 shadow-sm">
      <div class="animate-spin w-8 h-8 rounded-full border-4 border-slate-200 border-t-primary-500 mx-auto mb-4"></div>
      <p>Memuat formulir indikator...</p>
    </div>
    
    <div v-else-if="students.length === 0 && selectedClassId" class="p-12 text-center text-slate-400 bg-white rounded-xl border border-slate-100 shadow-sm">
      <p>Tidak ada siswa di kelas ini.</p>
    </div>

    <div v-else-if="students.length > 0" class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start relative">
      
      <!-- Left: Student List -->
      <div class="lg:col-span-1 space-y-3 bg-white rounded-xl shadow-sm border border-slate-100 p-3 max-h-[calc(100vh-10rem)] flex flex-col sticky top-4">
        <div class="space-y-2 shrink-0">
          <h3 class="font-bold text-slate-700 px-1 text-sm uppercase tracking-wider">Daftar Siswa</h3>
          <div class="relative">
            <Icon name="lucide:search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari siswa..." 
              class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
            />
          </div>
        </div>
        
        <div class="space-y-1.5 overflow-y-auto custom-scrollbar flex-1 pr-1">
          <button 
            v-for="(student, idx) in paginatedStudents" 
            :key="student.student_id"
            @click="activeStudentId = student.student_id"
            :class="[
              'w-full text-left px-3 py-2.5 rounded-lg transition-all flex items-center justify-between border',
              activeStudentId === student.student_id 
                ? 'bg-primary-50 border-primary-200 shadow-sm relative overflow-hidden' 
                : 'bg-white border-transparent hover:bg-slate-50 hover:border-slate-200'
            ]"
          >
            <div v-if="activeStudentId === student.student_id" class="absolute left-0 top-0 bottom-0 w-1 bg-primary-500"></div>
            <div>
              <p :class="['font-bold text-sm line-clamp-1', activeStudentId === student.student_id ? 'text-primary-700' : 'text-slate-700']">{{ student.name }}</p>
              <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ student.nisn || '-' }}</p>
            </div>
            <Icon name="lucide:chevron-right" :class="['w-4 h-4 shrink-0 ml-2', activeStudentId === student.student_id ? 'text-primary-500' : 'text-slate-300']" />
          </button>
          
          <div v-if="filteredStudents.length === 0" class="text-center py-4 text-sm text-slate-400 italic">
            Siswa tidak ditemukan.
          </div>
        </div>
        
        <div class="pt-2 border-t border-slate-100">
          <Pagination
            v-if="filteredStudents.length > 0"
            :current-page="currentPage"
            :last-page="totalPages"
            :items-per-page="itemsPerPage"
            @page-change="page => currentPage = page"
            class="scale-90 origin-left"
          />
        </div>
      </div>

      <!-- Right: Assessment Form -->
      <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden flex flex-col relative" v-if="activeStudent">
        <!-- Form Header -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center sticky top-0 z-20 backdrop-blur-md">
          <div>
            <h2 class="text-xl font-bold text-slate-800">{{ activeStudent.name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Isi penilaian indikator untuk siswa ini.</p>
          </div>
          <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center border border-slate-200 shadow-sm text-primary-600 font-bold text-lg overflow-hidden shrink-0">
            <img v-if="activeStudent.photo_url" :src="activeStudent.photo_url" class="w-full h-full object-cover" />
            <span v-else>{{ activeStudent.name.charAt(0).toUpperCase() }}</span>
          </div>
        </div>

        <!-- Form Body (Programs & Indicators) -->
        <div class="p-6 space-y-8">
          <div v-for="program in programs" :key="program.id" class="space-y-4">
            
            <div class="flex items-center gap-3 border-b border-slate-100 pb-2">
              <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                <Icon name="lucide:folder-tree" class="w-4 h-4" />
              </div>
              <h3 class="text-lg font-bold text-slate-800">{{ program.name }}</h3>
            </div>

            <div v-if="program.indicators.length === 0" class="text-sm text-slate-400 italic px-11">
              Tidak ada indikator.
            </div>

            <div class="space-y-3 px-2 md:px-11">
              <div v-for="(indicator, idx) in program.indicators" :key="indicator.id" class="p-4 rounded-xl border border-slate-100 bg-white hover:border-slate-200 hover:shadow-sm transition-all grid grid-cols-1 xl:grid-cols-2 gap-4 items-center">
                
                <div class="flex gap-3 items-start pr-4">
                  <span class="text-slate-300 font-mono text-sm mt-0.5">{{ idx + 1 }}.</span>
                  <p class="text-sm text-slate-700 leading-relaxed">{{ indicator.name }}</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <label v-for="(label, val) in {'BB': 'BB', 'MB': 'MB', 'BSH': 'BSH', 'BSB': 'BSB'}" :key="val" class="cursor-pointer h-10">
                    <input 
                      type="radio" 
                      :disabled="!schoolStore.isTeacher" 
                      :name="`scale-${activeStudent.student_id}-${indicator.id}`" 
                      :value="val" 
                      v-model="formState[activeStudent.student_id]![indicator.id as number]!.scale" 
                      class="hidden" 
                    />
                    <div :class="[
                      'flex items-center text-center justify-center px-2 py-1 rounded-lg text-xs font-bold border transition-all h-full', 
                      formState[activeStudent.student_id]?.[indicator.id as number]?.scale === val ? getScaleColor(val) : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100', 
                      (schoolStore.isTeacher) ? 'hover:shadow-sm' : 'opacity-70 cursor-not-allowed'
                    ]">
                      {{ label }}
                    </div>
                  </label>
                </div>

              </div>
            </div>

          </div>
        </div>

        <!-- Sticky Save Button -->
        <div v-if="schoolStore.isTeacher" class="p-4 border-t border-slate-100 bg-white flex justify-between items-center sticky bottom-0 z-20 shadow-[0_-10px_20px_-15px_rgba(0,0,0,0.1)]">
          <p class="text-xs text-slate-500 hidden sm:block">Perubahan otomatis disimpan saat menekan tombol simpan.</p>
          <BaseButton variant="primary" :disabled="isSaving" @click="saveAssessment" class="px-8 shadow-md hover:shadow-lg transition-all w-full sm:w-auto">
            <Icon v-if="!isSaving" name="lucide:save" class="w-4 h-4 mr-2" />
            <div v-else class="animate-spin w-4 h-4 rounded-full border-2 border-white/30 border-t-white mr-2"></div>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Seluruh Penilaian Kelas' }}
          </BaseButton>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
