<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { AttendanceStatus, PAUDScale } from '@/types'
import type { ClassRoom, AssessmentRecord, Semester } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Icon from '@/components/ui/Icon/Icon.vue'

const schoolStore = useSchoolStore()

const classes = ref<ClassRoom[]>([])
const selectedClassId = ref<number | null>(null)
const selectedSemester = ref<Semester>('1')

const predefinedAspects = [
  'Nilai Agama dan Moral',
  'Fisik Motorik',
  'Kognitif',
  'Bahasa',
  'Sosial Emosional',
  'Seni'
]
const aspectSelection = ref<string>(predefinedAspects[0] || '')
const customAspect = ref('')

const finalAspect = computed(() => {
  return aspectSelection.value === 'CUSTOM' ? customAspect.value.trim() : aspectSelection.value
})

const selectedClass = computed(() => classes.value.find(c => c.id === selectedClassId.value))
const currentAcademicYear = computed(() => selectedClass.value?.academic_year || '2025/2026')

const students = ref<AssessmentRecord[]>([])
const isLoadingClasses = ref(true)
const isLoadingStudents = ref(false)
const isSaving = ref(false)
const showValidationErrors = ref(false)
const error = ref('')
const successMessage = ref('')

onMounted(async () => {
  await fetchClasses()
})

watch([selectedClassId, aspectSelection, customAspect, selectedSemester], () => {
  // Only fetch if required fields are present
  if (selectedClassId.value && finalAspect.value.length > 0) {
    fetchStudents()
  } else {
    students.value = []
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
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !finalAspect.value) return
  isLoadingStudents.value = true
  error.value = ''
  successMessage.value = ''
  try {
    const res = await assessmentService.getAssessmentList(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      finalAspect.value,
      selectedSemester.value,
      currentAcademicYear.value
    )
    
    // Ensure we have an array
    const rawList = Array.isArray(res) ? res : (res as any).data
    
    if (!Array.isArray(rawList)) {
      throw new Error('Invalid data format')
    }

    students.value = rawList.map(item => ({
      ...item,
      // Only set default 'MB' for Teachers when creating new records.
      // For Headmaster or existing records, keep the original scale (could be null).
      scale: item.scale || (schoolStore.isTeacher ? 'MB' : null)
    })) as AssessmentRecord[]
  } catch (err: any) {
    error.value = 'Gagal memuat data siswa.'
  } finally {
    isLoadingStudents.value = false
  }
}

async function saveAssessment() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value || !finalAspect.value) return
  isSaving.value = true
  showValidationErrors.value = true
  if (students.value.some(s => !s.notes || s.notes.trim() === '')) {
    error.value = 'Mohon isi Catatan Perkembangan untuk semua siswa (kolom bergaris merah).'
    isSaving.value = false
    return
  }

  try {
    const payload = {
      aspect: finalAspect.value,
      semester: selectedSemester.value,
      academic_year: currentAcademicYear.value,
      assessments: students.value.map(s => ({
        student_id: s.student_id,
        scale: s.scale as PAUDScale,
        notes: s.notes
      }))
    }
    const res = await assessmentService.storeBulkAssessment(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      payload
    )
    successMessage.value = (res as any).message
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
  return students.value.some(s => s.scale !== null)
})
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-heading">Penilaian Skala Perkembangan</h1>
        <p class="text-sm text-muted">Input pencatatan perkembangan anak secara bulk.</p>
      </div>
    </div>

    <!-- Filter Card -->
    <BaseCard class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Pilih Kelas</label>
          <select v-model="selectedClassId" :disabled="isLoadingClasses" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm">
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} ({{ c.academic_year }})</option>
            <option v-if="classes.length === 0" value="">Belum ada kelas</option>
          </select>
        </div>
        
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Aspek Perkembangan</label>
          <select v-model="aspectSelection" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm">
            <option v-for="aspect in predefinedAspects" :key="aspect" :value="aspect">{{ aspect }}</option>
            <option value="CUSTOM">+ Tulis Manual Lainnya...</option>
          </select>
        </div>

        <div v-if="aspectSelection === 'CUSTOM'" class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Nama Aspek (Manual)</label>
          <input type="text" v-model="customAspect" placeholder="Contoh: Kreativitas Bermain" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none shadow-sm" />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Semester</label>
          <select v-model="selectedSemester" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none bg-white shadow-sm">
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
          </select>
        </div>
      </div>
    </BaseCard>

    <!-- Error/Success -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3">
      <span>{{ error }}</span>
    </div>
    <div v-if="!schoolStore.isTeacher" class="p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3">
      <div class="p-1"><Icon name="lucide:info" class="w-4 h-4" /></div>
      <span class="text-sm">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya Guru Kelas yang dapat mengisikan penilaian.</span>
    </div>
    <div v-if="successMessage" class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3">
      <span>{{ successMessage }}</span>
    </div>

    <!-- Main Content Table -->
    <BaseCard class="p-0 overflow-hidden shadow-sm">
      <div v-if="isLoadingStudents" class="p-12 text-center text-slate-400">
        <div class="animate-spin w-8 h-8 rounded-full border-4 border-slate-200 border-t-primary-500 mx-auto mb-4"></div>
        <p>Memuat form siswa...</p>
      </div>
      <div v-else-if="students.length === 0" class="p-12 text-center text-slate-400">
        <p>Tidak ada siswa di kelas ini.</p>
      </div>
      <div v-else-if="!schoolStore.isTeacher && !hasAnyAssessment" class="p-20 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
          <Icon name="lucide:bar-chart-2" class="w-8 h-8" />
        </div>
        <h3 class="text-lg font-bold text-slate-800">Belum Ada Penilaian</h3>
        <p class="text-slate-500 max-w-xs mx-auto text-sm mt-1">Belum ada input penilaian untuk aspek "{{ finalAspect }}" pada periode ini.</p>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-bold">
              <th class="px-6 py-4 w-1/4">Nama Siswa</th>
              <th class="px-6 py-4 whitespace-nowrap min-w-[320px]">Skala (BB, MB, BSH, BSB)</th>
              <th class="px-6 py-4">Catatan Perkembangan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="student in students" :key="student.student_id" class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4 align-top">
                <p class="font-bold text-slate-800">{{ student.name }}</p>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ student.nisn || '-' }}</p>
              </td>
              <td class="px-6 py-4 align-top">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                  <label v-for="(label, val) in {'BB': 'BB', 'MB': 'MB', 'BSH': 'BSH', 'BSB': 'BSB'}" :key="val" class="cursor-pointer">
                    <input type="radio" :disabled="!schoolStore.isTeacher" :name="`scale-${student.student_id}`" :value="val" v-model="student.scale" class="hidden" />
                    <div :class="['flex items-center text-center justify-center px-2 py-2 rounded-xl text-xs font-bold border transition-all h-full', student.scale === val ? getScaleColor(val) : 'bg-white text-slate-400 border-slate-200 border-dashed', (schoolStore.isTeacher) ? 'hover:bg-slate-50' : 'opacity-70 cursor-not-allowed']">
                      {{ label }}
                    </div>
                  </label>
                </div>
                <div v-if="!student.scale && !schoolStore.isTeacher" class="mt-2 text-center py-2 px-3 text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-50 rounded-lg border border-slate-100 italic">
                  Belum diinput
                </div>
                <p class="text-[10px] text-slate-400 mt-2 ml-1" v-if="student.scale === 'BB'">Belum Berkembang</p>
                <p class="text-[10px] text-slate-400 mt-2 ml-1" v-else-if="student.scale === 'MB'">Mulai Berkembang</p>
                <p class="text-[10px] text-slate-400 mt-2 ml-1" v-else-if="student.scale === 'BSH'">Berkembang Sesuai Harapan</p>
                <p class="text-[10px] text-slate-400 mt-2 ml-1" v-else-if="student.scale === 'BSB'">Berkembang Sangat Baik</p>
              </td>
              <td class="px-6 py-4 align-top">
                <textarea v-model="student.notes" :disabled="!schoolStore.isTeacher" placeholder="Misal: Sudah mampu mengikuti intruksi..." :class="['w-full text-sm px-3 py-2 rounded-xl border outline-none transition-all resize-y min-h-[80px] bg-white disabled:opacity-50 disabled:bg-slate-50 focus:ring-1', showValidationErrors && (!student.notes || student.notes.trim() === '') ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500 bg-danger-50/30' : 'border-slate-200 focus:border-primary-500 focus:ring-primary-500']"></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="schoolStore.isTeacher" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end">
        <BaseButton variant="primary" :disabled="isSaving || students.length === 0" @click="saveAssessment" class="px-8 shadow-md">
          {{ isSaving ? 'Menyimpan...' : 'Simpan Penilaian' }}
        </BaseButton>
      </div>
    </BaseCard>
  </div>
</template>
