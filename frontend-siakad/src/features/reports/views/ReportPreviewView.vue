<script setup lang="ts">
/**
 * ReportPreviewView — Preview data rapor siswa sebelum download.
 * Role: Headmaster, Teacher, Parent (Pro Plan only)
 */
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { reportService, type ReportData } from '@/features/reports/services/report.service'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const isDownloading = ref(false)
const error = ref('')
const report = ref<ReportData | null>(null)

const studentId = Number(route.params.studentId)
const semester = (route.query.semester as string) || '1'
const academicYear = (route.query.academic_year as string) || ''

onMounted(async () => {
  await fetchReport()
})

async function fetchReport() {
  isLoading.value = true
  try {
    const res = await reportService.getReportData(schoolStore.currentSchoolId!, studentId, {
      semester,
      academic_year: academicYear || undefined,
    })
    report.value = res.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal memuat data rapor.'
  } finally {
    isLoading.value = false
  }
}

async function downloadPdf() {
  if (isDownloading.value) return
  isDownloading.value = true
  try {
    const res = await reportService.downloadPdf(schoolStore.currentSchoolId!, studentId, {
      semester,
      academic_year: academicYear || undefined,
    })
    const blob = new Blob([res.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `Rapor_${report.value?.student?.name || studentId}_Semester${semester}.pdf`
    a.click()
    window.URL.revokeObjectURL(url)
  } catch {
    error.value = 'Gagal mengunduh PDF.'
  } finally {
    isDownloading.value = false
  }
}

function getScaleColor(scale: string): string {
  const colors: Record<string, string> = {
    BB: 'bg-red-50 text-red-700 border-red-200',
    MB: 'bg-amber-50 text-amber-700 border-amber-200',
    BSH: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    BSB: 'bg-blue-50 text-blue-700 border-blue-200',
  }
  return colors[scale] || 'bg-slate-50 text-slate-600 border-slate-200'
}

function getAttendancePercent(): string {
  if (!report.value || report.value.attendance.total === 0) return '0'
  return ((report.value.attendance.present / report.value.attendance.total) * 100).toFixed(1)
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Preview Rapor</h1>
          <p class="text-sm text-muted">Semester {{ semester }} — {{ academicYear || 'Tahun Ajaran Aktif' }}</p>
        </div>
      </div>
      <BaseButton v-if="report" variant="primary" :loading="isDownloading" @click="downloadPdf" class="shadow-lg shadow-primary-500/20">
        <template #prepend><Icon name="lucide:download" class="w-4 h-4" /></template>
        Download PDF
      </BaseButton>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="space-y-6">
      <BaseCard class="p-8"><Skeleton height="6rem" /></BaseCard>
      <BaseCard class="p-8"><Skeleton height="10rem" /></BaseCard>
      <BaseCard class="p-8"><Skeleton height="14rem" /></BaseCard>
    </div>

    <!-- Error -->
    <BaseCard v-else-if="error" class="p-12 text-center">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-500 mx-auto mb-4" />
      <p class="text-lg font-bold text-slate-900 mb-2">{{ error }}</p>
      <BaseButton variant="outline" @click="fetchReport">Coba Lagi</BaseButton>
    </BaseCard>

    <template v-else-if="report">
      <!-- School & Student Info -->
      <BaseCard class="p-0 overflow-hidden border-none shadow-xl">
        <!-- School Header -->
        <div class="p-6 bg-gradient-to-r from-primary-600 to-primary-800 text-white text-center">
          <h2 class="text-xl font-black uppercase tracking-wide">{{ report.school.name }}</h2>
          <p class="text-sm text-white/70 mt-1">NPSN: {{ report.school.npsn }}</p>
          <p class="text-xs text-white/50">{{ report.school.address }}</p>
        </div>
        <div class="p-6 border-b border-slate-100">
          <div class="flex items-start gap-6">
            <div v-if="report.student.photo_url" class="w-20 h-24 rounded-lg overflow-hidden border-2 border-slate-200 shrink-0">
              <img :src="report.student.photo_url" class="w-full h-full object-cover" alt="Foto Siswa" />
            </div>
            <div class="flex-1 grid grid-cols-2 gap-4">
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama</p>
                <p class="text-sm font-bold text-slate-800">{{ report.student.name }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NISN</p>
                <p class="text-sm font-bold text-slate-800">{{ report.student.nisn || '-' }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelas</p>
                <p class="text-sm font-bold text-slate-800">{{ report.student.class_name }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Semester</p>
                <p class="text-sm font-bold text-slate-800">{{ report.semester_label }}</p>
              </div>
            </div>
          </div>
        </div>
      </BaseCard>

      <!-- Attendance Summary -->
      <BaseCard class="p-0 overflow-hidden border-none shadow-xl">
        <div class="p-6 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
            <Icon name="lucide:calendar-check" class="w-4 h-4 text-primary-500" /> Rekap Kehadiran
          </h3>
        </div>
        <div class="p-6 grid grid-cols-5 gap-4 text-center">
          <div class="p-4 rounded-xl bg-emerald-50">
            <p class="text-2xl font-black text-emerald-700">{{ report.attendance.present }}</p>
            <p class="text-[10px] font-bold text-emerald-500 uppercase">Hadir</p>
          </div>
          <div class="p-4 rounded-xl bg-amber-50">
            <p class="text-2xl font-black text-amber-700">{{ report.attendance.sick }}</p>
            <p class="text-[10px] font-bold text-amber-500 uppercase">Sakit</p>
          </div>
          <div class="p-4 rounded-xl bg-blue-50">
            <p class="text-2xl font-black text-blue-700">{{ report.attendance.permission }}</p>
            <p class="text-[10px] font-bold text-blue-500 uppercase">Izin</p>
          </div>
          <div class="p-4 rounded-xl bg-red-50">
            <p class="text-2xl font-black text-red-700">{{ report.attendance.absent }}</p>
            <p class="text-[10px] font-bold text-red-500 uppercase">Alfa</p>
          </div>
          <div class="p-4 rounded-xl bg-slate-50">
            <p class="text-2xl font-black text-slate-700">{{ getAttendancePercent() }}%</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase">Kehadiran</p>
          </div>
        </div>
      </BaseCard>

      <!-- Assessments -->
      <BaseCard class="p-0 overflow-hidden border-none shadow-xl">
        <div class="p-6 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
            <Icon name="lucide:star" class="w-4 h-4 text-primary-500" /> Pencapaian Perkembangan
          </h3>
        </div>
        <div v-if="report.assessments.length === 0" class="p-12 text-center">
          <Icon name="lucide:clipboard" class="w-12 h-12 text-slate-200 mx-auto mb-3" />
          <p class="text-sm text-slate-400 font-medium">Belum ada data penilaian</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-50">
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-8">No.</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Aspek Perkembangan</th>
                <th class="px-6 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Capaian</th>
                <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(a, i) in report.assessments" :key="a.id" class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 text-slate-400 font-medium text-center">{{ i + 1 }}</td>
                <td class="px-6 py-4 font-medium text-slate-700">{{ a.aspect }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border" :class="getScaleColor(a.scale)">
                    {{ a.scale }}
                  </span>
                </td>
                <td class="px-6 py-4 text-slate-500 text-xs">{{ a.notes || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 text-xs text-slate-400 flex gap-4 flex-wrap">
          <span><strong class="text-red-600">BB</strong> = Belum Berkembang</span>
          <span><strong class="text-amber-600">MB</strong> = Mulai Berkembang</span>
          <span><strong class="text-emerald-600">BSH</strong> = Berkembang Sesuai Harapan</span>
          <span><strong class="text-blue-600">BSB</strong> = Berkembang Sangat Baik</span>
        </div>
      </BaseCard>

      <!-- Generated Info -->
      <p class="text-xs text-center text-slate-400">Digenerate pada {{ report.generated_at }} oleh sistem PaudPedia SIAKAD</p>
    </template>
  </div>
</template>
