<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { attendanceService } from '@/features/attendance/services/attendance.service'
import { AttendanceStatus } from '@/types'
import type { ClassRoom, AttendanceRecord } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'

const schoolStore = useSchoolStore()

const classes = ref<ClassRoom[]>([])
const selectedClassId = ref<number | null>(null)
const selectedDate = ref<string>(new Date().toISOString().split('T')[0] || '')
const todayDate = new Date().toISOString().split('T')[0]

const students = ref<AttendanceRecord[]>([])
const isLoadingClasses = ref(true)
const isLoadingStudents = ref(false)
const isSaving = ref(false)
const error = ref('')
const successMessage = ref('')

onMounted(async () => {
  await fetchClasses()
})

watch([selectedClassId, selectedDate], () => {
  if (selectedClassId.value) {
    fetchStudents()
  }
})

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  isLoadingClasses.value = true
  try {
    const res = await classService.getClasses(schoolStore.currentSchoolId, { per_page: 100 })
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
  if (!schoolStore.currentSchoolId || !selectedClassId.value) return
  isLoadingStudents.value = true
  error.value = ''
  successMessage.value = ''
  try {
    const res = await attendanceService.getAttendanceList(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      selectedDate.value
    )
    
    // Ensure we have an array, supporting both raw array and wrapped { data: [] }
    const rawList = Array.isArray(res) ? res : (res as any).data
    
    if (!Array.isArray(rawList)) {
      throw new Error('Invalid data format')
    }

    students.value = rawList.map(item => ({
      ...item,
      // Only set default 'present' for Teachers when creating new records.
      // For Headmaster or existing records, keep the original status (could be null).
      status: item.status || (schoolStore.isTeacher ? 'present' : null)
    })) as AttendanceRecord[]
  } catch (err: any) {
    error.value = 'Gagal memuat data siswa.'
  } finally {
    isLoadingStudents.value = false
  }
}

async function saveAttendance() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value) return
  isSaving.value = true
  error.value = ''
  successMessage.value = ''
  try {
    const payload = {
      date: selectedDate.value,
      attendances: students.value.map(s => ({
        student_id: s.student_id,
        status: s.status as AttendanceStatus,
        notes: s.notes
      }))
    }
    const res = await attendanceService.storeBulkAttendance(
      schoolStore.currentSchoolId as number,
      selectedClassId.value as number,
      payload
    )
    successMessage.value = (res as any).message
  } catch (err: any) {
    error.value = 'Gagal menyimpan absensi.'
  } finally {
    isSaving.value = false
  }
}


function getStatusColor(status: string | null) {
  if (!status) return 'bg-slate-50 text-slate-400 border-slate-200 border-dashed'
  switch (status) {
    case 'present': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'sick': return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'permission': return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'absent': return 'bg-rose-50 text-rose-700 border-rose-200'
    default: return 'bg-slate-50 text-slate-700 border-slate-200'
  }
}

const hasAnyAttendance = computed(() => {
  return students.value.some(s => s.status !== null)
})

const attendanceStats = computed(() => {
  const stats = { total: students.value.length, present: 0, sick: 0, permission: 0, absent: 0, uninputted: 0 }
  students.value.forEach(s => {
    if (s.status === 'present') stats.present++
    else if (s.status === 'sick') stats.sick++
    else if (s.status === 'permission') stats.permission++
    else if (s.status === 'absent') stats.absent++
    else stats.uninputted++
  })
  return stats
})
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-heading">Absensi Kelas</h1>
        <p class="text-sm text-muted">Input pencatatan kehadiran harian siswa.</p>
      </div>
    </div>

    <!-- Filter Card -->
    <BaseCard class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Tanggal Absensi</label>
          <input type="date" :max="todayDate" v-model="selectedDate" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all shadow-sm" />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Pilih Kelas</label>
          <select v-model="selectedClassId" :disabled="isLoadingClasses" class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all shadow-sm bg-white">
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            <option v-if="classes.length === 0" value="">Belum ada kelas</option>
          </select>
        </div>
      </div>
    </BaseCard>

    <!-- Stats Widget -->
    <div v-if="students.length > 0" class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Siswa</p>
        <p class="text-xl font-black text-slate-800">{{ attendanceStats.total }}</p>
      </div>
      <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 shadow-sm flex flex-col items-center">
        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Hadir</p>
        <p class="text-xl font-black text-emerald-700">{{ attendanceStats.present }}</p>
      </div>
      <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 shadow-sm flex flex-col items-center">
        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Sakit</p>
        <p class="text-xl font-black text-amber-700">{{ attendanceStats.sick }}</p>
      </div>
      <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col items-center">
        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Izin</p>
        <p class="text-xl font-black text-blue-700">{{ attendanceStats.permission }}</p>
      </div>
      <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100 shadow-sm flex flex-col items-center">
        <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Alfa</p>
        <p class="text-xl font-black text-rose-700">{{ attendanceStats.absent }}</p>
      </div>
    </div>

    <!-- Error/Success Alerts -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3">
      <span>{{ error }}</span>
    </div>
    <div v-if="!schoolStore.isTeacher" class="p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3">
      <div class="p-1"><Icon name="lucide:info" class="w-4 h-4" /></div>
      <span class="text-sm">Anda mengakses halaman ini sebagai Peninjau (Read-Only). Hanya Guru Kelas yang dapat mengisikan absensi secara langsung.</span>
    </div>
    <div v-if="successMessage" class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3">
      <span>{{ successMessage }}</span>
    </div>

    <!-- Main Content -->
    <BaseCard class="p-0 overflow-hidden shadow-sm">
      <div v-if="isLoadingStudents" class="p-12 text-center text-slate-400">
        <div class="animate-spin w-8 h-8 rounded-full border-4 border-slate-200 border-t-primary-500 mx-auto mb-4"></div>
        <p>Memuat data siswa...</p>
      </div>
      <div v-else-if="students.length === 0" class="p-12 text-center text-slate-400">
        <p>Tidak ada siswa di kelas ini/tanggal belum dipilih.</p>
      </div>
      <div v-else-if="!schoolStore.isTeacher && !hasAnyAttendance" class="p-20 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
          <Icon name="lucide:calendar-x" class="w-8 h-8" />
        </div>
        <h3 class="text-lg font-bold text-slate-800">Belum Ada Catatan Absensi</h3>
        <p class="text-slate-500 max-w-xs mx-auto text-sm mt-1">Guru kelas belum mengisikan data kehadiran siswa untuk tanggal ini.</p>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-bold">
              <th class="px-6 py-4">Nama Siswa</th>
              <th class="px-6 py-4">Status Kehadiran</th>
              <th class="px-6 py-4 w-1/3">Catatan Tambahan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="student in students" :key="student.student_id" class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4">
                <p class="font-bold text-slate-800">{{ student.name }}</p>
                <p class="text-xs text-slate-500 font-mono">{{ student.nisn || '-' }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                  <label v-for="(label, val) in {'present': 'Hadir', 'sick': 'Sakit', 'permission': 'Izin', 'absent': 'Alfa'}" :key="val" class="cursor-pointer">
                    <input type="radio" :disabled="!schoolStore.isTeacher" :name="`status-${student.student_id}`" :value="val" v-model="student.status" class="hidden" />
                    <span :class="['px-3 py-1.5 rounded-lg text-sm font-bold border transition-all block', student.status === val ? getStatusColor(val) : 'bg-white text-slate-500 border-slate-200 border-dashed', schoolStore.isTeacher ? 'hover:bg-slate-50' : 'opacity-70 cursor-not-allowed']">
                      {{ label }}
                    </span>
                  </label>
                  <div v-if="!student.status && !schoolStore.isTeacher" class="py-1 px-3 text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-50 rounded-lg border border-slate-100 italic">
                    Belum diinput
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <input type="text" v-model="student.notes" :disabled="!schoolStore.isTeacher" placeholder="Tuliskan catatan..." class="w-full text-sm px-3 py-2 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-all bg-white disabled:opacity-50 disabled:bg-slate-50" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="schoolStore.isTeacher" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end">
        <BaseButton variant="primary" :disabled="isSaving || students.length === 0" @click="saveAttendance" class="px-8 shadow-md">
          {{ isSaving ? 'Menyimpan...' : 'Simpan Absensi' }}
        </BaseButton>
      </div>
    </BaseCard>
  </div>
</template>
