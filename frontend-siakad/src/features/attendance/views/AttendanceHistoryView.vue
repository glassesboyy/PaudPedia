<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { studentService } from '@/features/students/services/student.service'
import { attendanceService } from '@/features/attendance/services/attendance.service'
import type { ClassRoom, Student } from '@/types'
import type { StudentAttendanceSummaryResponse } from '@/types/attendance.types'
import BaseCard from '@/components/ui/Card/Card.vue'

const schoolStore = useSchoolStore()

const classes = ref<ClassRoom[]>([])
const selectedClassId = ref<number | null>(null)

const students = ref<Student[]>([])
const selectedStudentId = ref<number | null>(null)

const selectedMonth = ref<number>(new Date().getMonth() + 1)
const selectedYear = ref<number>(new Date().getFullYear())

const attendanceData = ref<StudentAttendanceSummaryResponse | null>(null)
const isLoading = ref(false)

onMounted(async () => {
  await fetchClasses()
})

watch(selectedClassId, async () => {
  if (selectedClassId.value) {
    selectedStudentId.value = null
    await fetchStudents()
  } else {
    students.value = []
  }
})

watch([selectedStudentId, selectedMonth, selectedYear], () => {
  if (selectedStudentId.value) {
    fetchHistory()
  }
})

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  const res = await classService.getClasses(schoolStore.currentSchoolId as number, { per_page: 100 })
  classes.value = res.data
  if (classes.value.length > 0) {
    selectedClassId.value = classes.value[0]?.id || null
  }
}

async function fetchStudents() {
  if (!schoolStore.currentSchoolId || !selectedClassId.value) return
  // studentService expects (schoolId, params)
  const res = await studentService.getStudents(schoolStore.currentSchoolId as number, { class_id: selectedClassId.value as number, per_page: 100 })
  students.value = res.data
}

async function fetchHistory() {
  if (!schoolStore.currentSchoolId || !selectedStudentId.value) return
  isLoading.value = true
  try {
    const res = await attendanceService.getStudentHistory(
      schoolStore.currentSchoolId as number,
      selectedStudentId.value as number,
      { month: selectedMonth.value, year: selectedYear.value }
    )
    attendanceData.value = res as any
  } catch (err) {
    console.error(err)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-heading">Riwayat Absensi Siswa</h1>
        <p class="text-sm text-muted">Pantau rekapitulasi kehadiran anak didik.</p>
      </div>
    </div>

    <!-- Filter -->
    <BaseCard class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Kelas</label>
          <select v-model="selectedClassId" class="w-full h-11 px-4 text-sm rounded-xl border border-slate-200 bg-white">
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            <option v-if="classes.length === 0" value="">Belum ada kelas</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Siswa</label>
          <select v-model="selectedStudentId" class="w-full h-11 px-4 text-sm rounded-xl border border-slate-200 bg-white">
            <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }}</option>
            <option value="" disabled>Pilih Siswa</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Bulan</label>
          <select v-model="selectedMonth" class="w-full h-11 px-4 text-sm rounded-xl border border-slate-200 bg-white">
            <option v-for="m in 12" :key="m" :value="m">{{ new Date(2000, m - 1).toLocaleString('id-ID', { month: 'long' }) }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700">Tahun</label>
          <input type="number" v-model="selectedYear" class="w-full h-11 px-4 text-sm rounded-xl border border-slate-200" />
        </div>
      </div>
    </BaseCard>

    <div v-if="isLoading" class="p-12 text-center text-slate-400">
      <div class="animate-spin w-8 h-8 rounded-full border-4 border-slate-200 border-t-primary-500 mx-auto mb-4"></div>
      <p>Memuat rekapitulasi...</p>
    </div>

    <template v-else-if="attendanceData">
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        <BaseCard class="p-4 sm:p-6 text-center shadow-sm">
          <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase">Hadir</p>
          <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1 sm:mt-2">{{ attendanceData.summary.present }}</p>
        </BaseCard>
        <BaseCard class="p-4 sm:p-6 text-center shadow-sm">
          <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase">Sakit</p>
          <p class="text-2xl sm:text-3xl font-black text-amber-500 mt-1 sm:mt-2">{{ attendanceData.summary.sick }}</p>
        </BaseCard>
        <BaseCard class="p-4 sm:p-6 text-center shadow-sm">
          <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase">Izin</p>
          <p class="text-2xl sm:text-3xl font-black text-blue-500 mt-1 sm:mt-2">{{ attendanceData.summary.permission }}</p>
        </BaseCard>
        <BaseCard class="p-4 sm:p-6 text-center shadow-sm">
          <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase">Alfa</p>
          <p class="text-2xl sm:text-3xl font-black text-rose-500 mt-1 sm:mt-2">{{ attendanceData.summary.absent }}</p>
        </BaseCard>
        <BaseCard class="p-4 sm:p-6 text-center shadow-sm border-2 border-primary-100 bg-primary-50 col-span-2 md:col-span-1">
          <p class="text-[10px] sm:text-xs font-bold text-primary-700 uppercase">Rasio Hadir</p>
          <p class="text-2xl sm:text-3xl font-black text-primary-700 mt-1 sm:mt-2">{{ attendanceData.summary.percentage }}%</p>
        </BaseCard>
      </div>

      <BaseCard class="p-0 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500 font-bold">
              <tr>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 w-full">Keterangan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="item in attendanceData.history" :key="item.id">
                <td class="px-6 py-4 font-medium">{{ item.date }}</td>
                <td class="px-6 py-4 font-bold" :class="{
                  'text-emerald-600': item.status === 'present',
                  'text-amber-500': item.status === 'sick',
                  'text-blue-500': item.status === 'permission',
                  'text-rose-500': item.status === 'absent'
                }">{{ item.status_label }}</td>
                <td class="px-6 py-4 text-slate-600 whitespace-normal">{{ item.notes || '-' }}</td>
              </tr>
              <tr v-if="attendanceData.history.length === 0">
                <td colspan="3" class="px-6 py-12 text-center text-slate-400">Belum ada rekapan pada bulan ini.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>
    </template>
  </div>
</template>
