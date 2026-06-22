<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseModal from '@/components/ui/Modal/Modal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import Icon from '@/components/ui/Icon/Icon.vue'
import type { DevelopmentProgram } from '@/types'

const schoolStore = useSchoolStore()
const programs = ref<DevelopmentProgram[]>([])
const isLoading = ref(true)
const error = ref('')
const successMessage = ref('')

// Form state
const isAddingProgram = ref(false)
const newProgramName = ref('')
const addingIndicatorFor = ref<number | null>(null)
const newIndicatorName = ref('')
const expandedPrograms = ref<Set<number>>(new Set())

function toggleProgram(id: number) {
  if (expandedPrograms.value.has(id)) {
    expandedPrograms.value.delete(id)
  } else {
    expandedPrograms.value.add(id)
  }
}

onMounted(async () => {
  await fetchPrograms()
})

async function fetchPrograms() {
  if (!schoolStore.currentSchoolId) return
  isLoading.value = true
  try {
    const res = await assessmentService.getPrograms(schoolStore.currentSchoolId as number)
    programs.value = res.data || res
  } catch (err) {
    error.value = 'Gagal memuat program perkembangan.'
  } finally {
    isLoading.value = false
  }
}

async function addProgram() {
  if (!newProgramName.value.trim() || !schoolStore.currentSchoolId) return
  try {
    const order = programs.value.length + 1
    await assessmentService.storeProgram(schoolStore.currentSchoolId as number, {
      name: newProgramName.value,
      order
    })
    newProgramName.value = ''
    isAddingProgram.value = false
    successMessage.value = 'Program berhasil ditambahkan.'
    await fetchPrograms()
  } catch (err) {
    error.value = 'Gagal menambah program.'
  }
}

async function deleteProgram(id: number) {
  if (!confirm('Hapus program ini beserta seluruh indikatornya?')) return
  try {
    await assessmentService.destroyProgram(schoolStore.currentSchoolId as number, id)
    successMessage.value = 'Program berhasil dihapus.'
    await fetchPrograms()
  } catch (err) {
    error.value = 'Gagal menghapus program.'
  }
}

async function addIndicator(programId: number) {
  if (!newIndicatorName.value.trim() || !schoolStore.currentSchoolId) return
  try {
    const program = programs.value.find(p => p.id === programId)
    const order = program?.indicators?.length ? program.indicators.length + 1 : 1
    await assessmentService.storeIndicator(schoolStore.currentSchoolId as number, programId, {
      name: newIndicatorName.value,
      order
    })
    newIndicatorName.value = ''
    addingIndicatorFor.value = null
    successMessage.value = 'Indikator berhasil ditambahkan.'
    await fetchPrograms()
  } catch (err) {
    error.value = 'Gagal menambah indikator.'
  }
}

async function deleteIndicator(indicatorId: number) {
  if (!confirm('Hapus indikator ini?')) return
  try {
    await assessmentService.destroyIndicator(schoolStore.currentSchoolId as number, indicatorId)
    successMessage.value = 'Indikator berhasil dihapus.'
    await fetchPrograms()
  } catch (err) {
    error.value = 'Gagal menghapus indikator.'
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Master Data Penilaian</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola Program Perkembangan dan Indikator secara dinamis untuk Rapor.</p>
      </div>
      <BaseButton variant="primary" @click="isAddingProgram = true">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Program Baru
      </BaseButton>
    </div>

    <!-- Messages -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3">
      <Icon name="lucide:alert-circle" class="w-5 h-5 shrink-0" />
      <span>{{ error }}</span>
    </div>
    <div v-if="successMessage" class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3">
      <Icon name="lucide:check-circle" class="w-5 h-5 shrink-0" />
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="ml-auto text-emerald-400 hover:text-emerald-600"><Icon name="lucide:x" class="w-4 h-4" /></button>
    </div>

    <!-- Skeletons Loading -->
    <div v-if="isLoading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="w-full bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3 w-1/2">
          <Skeleton width="2rem" height="2rem" class="rounded-lg shrink-0" />
          <Skeleton width="100%" height="1.5rem" />
        </div>
        <Skeleton width="2rem" height="2rem" class="rounded-lg" />
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-4">
      <div v-if="programs.length === 0" class="p-12 text-center text-slate-400 border border-dashed rounded-xl border-slate-300 bg-white">
        <Icon name="lucide:database" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
        <p class="text-slate-500 font-medium">Belum ada program perkembangan yang dikonfigurasi.</p>
        <BaseButton variant="outline" class="mt-4" @click="isAddingProgram = true">Buat Program Pertama</BaseButton>
      </div>

      <!-- Accordion List -->
      <div v-for="program in programs" :key="program.id" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300">
        
        <!-- Accordion Header -->
        <button @click="toggleProgram(program.id)" class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors focus:outline-none">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
              <Icon name="lucide:folder-tree" class="w-5 h-5" />
            </div>
            <div class="text-left">
              <h2 class="font-bold text-lg text-slate-800">{{ program.name }}</h2>
              <p class="text-xs font-medium text-slate-500 mt-0.5">{{ program.indicators?.length || 0 }} Indikator</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button @click.stop="deleteProgram(program.id)" class="text-slate-300 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-lg transition-colors" title="Hapus Program">
              <Icon name="lucide:trash-2" class="w-5 h-5" />
            </button>
            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="expandedPrograms.has(program.id) ? 'rotate-180' : ''">
              <Icon name="lucide:chevron-down" class="w-5 h-5" />
            </div>
          </div>
        </button>

        <!-- Accordion Body -->
        <div v-show="expandedPrograms.has(program.id)" class="border-t border-slate-100 bg-slate-50/50 p-4 sm:p-6 animate-fade-in">
          
          <div v-if="program.indicators && program.indicators.length > 0" class="space-y-3 mb-6">
            <div v-for="(indicator, idx) in program.indicators" :key="indicator.id" class="flex justify-between items-start sm:items-center p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:border-slate-200 transition-all gap-4">
              <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 sm:mt-0">
                  {{ idx + 1 }}
                </div>
                <span class="text-slate-700 text-sm font-medium leading-relaxed">{{ indicator.name }}</span>
              </div>
              <button @click="deleteIndicator(indicator.id)" class="text-slate-300 hover:text-rose-500 p-2 hover:bg-rose-50 rounded-lg shrink-0 transition-colors" title="Hapus Indikator">
                <Icon name="lucide:x" class="w-4 h-4" />
              </button>
            </div>
          </div>
          <div v-else class="text-sm text-slate-400 italic mb-6 py-4 px-4 bg-white border border-dashed border-slate-200 rounded-xl text-center">
            Belum ada indikator untuk program ini.
          </div>

          <!-- Add Indicator Inline -->
          <div v-if="addingIndicatorFor === program.id" class="flex gap-2">
            <BaseInput v-model="newIndicatorName" placeholder="Ketik nama indikator..." class="flex-1" @keyup.enter="addIndicator(program.id)" />
            <BaseButton variant="primary" @click="addIndicator(program.id)">Simpan</BaseButton>
            <BaseButton variant="outline" @click="addingIndicatorFor = null">Batal</BaseButton>
          </div>
          <button v-else @click="addingIndicatorFor = program.id" class="text-sm font-bold text-primary-600 bg-primary-50 hover:bg-primary-100 px-4 py-2.5 rounded-lg flex items-center gap-2 transition-colors">
            <Icon name="lucide:plus" class="w-4 h-4" /> Tambah Indikator Baru
          </button>
          
        </div>
      </div>
    </div>

    <!-- Modal Tambah Program -->
    <BaseModal :show="isAddingProgram" title="Tambah Program Perkembangan" @close="isAddingProgram = false">
      <div class="space-y-4 py-2">
        <div class="space-y-2">
          <label class="text-sm font-bold text-slate-700">Nama Program</label>
          <BaseInput 
            v-model="newProgramName" 
            placeholder="Contoh: Fisik Motorik" 
            @keyup.enter="addProgram"
            autofocus
          />
          <p class="text-xs text-slate-500">Program ini akan muncul sebagai kategori utama di penilaian dan rapor siswa.</p>
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-3 w-full">
          <BaseButton variant="ghost" @click="isAddingProgram = false">Batal</BaseButton>
          <BaseButton variant="primary" :disabled="!newProgramName" @click="addProgram">Simpan Program</BaseButton>
        </div>
      </template>
    </BaseModal>
  </div>
</template>
