<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { assessmentService } from '@/features/assessments/services/assessment.service'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseModal from '@/components/ui/Modal/Modal.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import Icon from '@/components/ui/Icon/Icon.vue'
import type { DevelopmentProgram, DevelopmentIndicator } from '@/types'

const schoolStore = useSchoolStore()
const programs = ref<DevelopmentProgram[]>([])
const isLoading = ref(true)
const error = ref('')
const successMessage = ref('')

// Add Program state
const isAddingProgram = ref(false)
const newProgramName = ref('')
const isSavingProgram = ref(false)

// Edit Program state
const isEditingProgram = ref(false)
const editProgramTarget = ref<DevelopmentProgram | null>(null)
const editProgramName = ref('')
const isUpdatingProgram = ref(false)

// Add Indicator state
const addingIndicatorFor = ref<number | null>(null)
const newIndicatorName = ref('')
const isSavingIndicator = ref(false)

// Edit Indicator state
const isEditingIndicator = ref(false)
const editIndicatorTarget = ref<DevelopmentIndicator | null>(null)
const editIndicatorName = ref('')
const isUpdatingIndicator = ref(false)

const expandedPrograms = ref<Set<number>>(new Set())

// Delete Program state
const showDeleteProgramModal = ref(false)
const deleteProgramTarget = ref<number | null>(null)
const isDeletingProgram = ref(false)

// Delete Indicator state
const showDeleteIndicatorModal = ref(false)
const deleteIndicatorTarget = ref<number | null>(null)
const isDeletingIndicator = ref(false)

function toggleProgramAccordion(id: number) {
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

// --- Program Actions ---

async function addProgram() {
  if (!newProgramName.value.trim() || !schoolStore.currentSchoolId) return
  isSavingProgram.value = true
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
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menambah program.'
  } finally {
    isSavingProgram.value = false
  }
}

function openEditProgram(program: DevelopmentProgram) {
  editProgramTarget.value = program
  editProgramName.value = program.name
  isEditingProgram.value = true
}

async function updateProgram() {
  if (!editProgramName.value.trim() || !editProgramTarget.value || !schoolStore.currentSchoolId) return
  isUpdatingProgram.value = true
  try {
    await assessmentService.updateProgram(schoolStore.currentSchoolId as number, editProgramTarget.value.id, {
      name: editProgramName.value
    })
    isEditingProgram.value = false
    successMessage.value = 'Program berhasil diperbarui.'
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal memperbarui program.'
  } finally {
    isUpdatingProgram.value = false
  }
}

async function toggleProgramStatus(program: DevelopmentProgram) {
  if (!schoolStore.currentSchoolId) return
  try {
    const newStatus = program.is_active === false ? true : false
    await assessmentService.updateProgram(schoolStore.currentSchoolId as number, program.id, {
      is_active: newStatus
    })
    successMessage.value = `Program '${program.name}' berhasil ${newStatus ? 'diaktifkan' : 'dinonaktifkan'}.`
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal mengubah status program.'
  }
}

function confirmDeleteProgram(id: number) {
  deleteProgramTarget.value = id
  showDeleteProgramModal.value = true
}

async function executeDeleteProgram() {
  if (deleteProgramTarget.value === null) return
  isDeletingProgram.value = true
  try {
    await assessmentService.destroyProgram(schoolStore.currentSchoolId as number, deleteProgramTarget.value)
    successMessage.value = 'Program berhasil dihapus.'
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menghapus program.'
  } finally {
    isDeletingProgram.value = false
    showDeleteProgramModal.value = false
    deleteProgramTarget.value = null
  }
}

// --- Indicator Actions ---

async function addIndicator(programId: number) {
  if (!newIndicatorName.value.trim() || !schoolStore.currentSchoolId) return
  isSavingIndicator.value = true
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
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menambah indikator.'
  } finally {
    isSavingIndicator.value = false
  }
}

function openEditIndicator(indicator: DevelopmentIndicator) {
  editIndicatorTarget.value = indicator
  editIndicatorName.value = indicator.name
  isEditingIndicator.value = true
}

async function updateIndicator() {
  if (!editIndicatorName.value.trim() || !editIndicatorTarget.value || !schoolStore.currentSchoolId) return
  isUpdatingIndicator.value = true
  try {
    await assessmentService.updateIndicator(schoolStore.currentSchoolId as number, editIndicatorTarget.value.id, {
      name: editIndicatorName.value
    })
    isEditingIndicator.value = false
    successMessage.value = 'Indikator berhasil diperbarui.'
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal memperbarui indikator.'
  } finally {
    isUpdatingIndicator.value = false
  }
}

async function toggleIndicatorStatus(indicator: DevelopmentIndicator) {
  if (!schoolStore.currentSchoolId) return
  try {
    const newStatus = indicator.is_active === false ? true : false
    await assessmentService.updateIndicator(schoolStore.currentSchoolId as number, indicator.id, {
      is_active: newStatus
    })
    successMessage.value = `Indikator berhasil ${newStatus ? 'diaktifkan' : 'dinonaktifkan'}.`
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal mengubah status indikator.'
  }
}

function confirmDeleteIndicator(id: number) {
  deleteIndicatorTarget.value = id
  showDeleteIndicatorModal.value = true
}

async function executeDeleteIndicator() {
  if (deleteIndicatorTarget.value === null) return
  isDeletingIndicator.value = true
  try {
    await assessmentService.destroyIndicator(schoolStore.currentSchoolId as number, deleteIndicatorTarget.value)
    successMessage.value = 'Indikator berhasil dihapus.'
    await fetchPrograms()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menghapus indikator.'
  } finally {
    isDeletingIndicator.value = false
    showDeleteIndicatorModal.value = false
    deleteIndicatorTarget.value = null
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
      <BaseButton variant="primary" @click="isAddingProgram = true" class="w-full sm:w-auto">
        <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
        Tambah Program Baru
      </BaseButton>
    </div>

    <!-- Messages -->
    <div v-if="error" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3">
      <Icon name="lucide:alert-circle" class="w-5 h-5 shrink-0" />
      <span>{{ error }}</span>
      <button @click="error = ''" class="ml-auto text-rose-400 hover:text-rose-600"><Icon name="lucide:x" class="w-4 h-4" /></button>
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
        <BaseButton variant="outline" class="mt-4" @click="isAddingProgram = true">
          <template #prepend><Icon name="lucide:plus" class="w-4 h-4" /></template>
          Buat Program Pertama
        </BaseButton>
      </div>

      <!-- Accordion List -->
      <div v-for="program in programs" :key="program.id" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300" :class="{ 'opacity-75 bg-slate-50': program.is_active === false }">
        
        <!-- Accordion Header -->
        <div class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
          <button @click="toggleProgramAccordion(program.id)" class="flex items-center gap-4 flex-1 text-left focus:outline-none">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0" :class="{ 'bg-slate-200 text-slate-500': program.is_active === false }">
              <Icon name="lucide:folder-tree" class="w-5 h-5" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h2 class="font-bold text-lg text-slate-800" :class="{ 'line-through text-slate-500': program.is_active === false }">{{ program.name }}</h2>
                <span v-if="program.is_active === false" class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded uppercase">Nonaktif</span>
              </div>
              <p class="text-xs font-medium text-slate-500 mt-0.5">{{ program.indicators?.length || 0 }} Indikator</p>
            </div>
          </button>

          <div class="flex items-center gap-1 sm:gap-2">
            <!-- Toggle Active Status Button -->
            <button @click="toggleProgramStatus(program)" class="p-2 rounded-lg transition-colors" :class="program.is_active !== false ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100'" :title="program.is_active !== false ? 'Nonaktifkan Program' : 'Aktifkan Program'">
              <Icon :name="program.is_active !== false ? 'lucide:power' : 'lucide:power-off'" class="w-4 h-4" />
            </button>
            <!-- Edit Button -->
            <button @click="openEditProgram(program)" class="text-slate-400 hover:text-amber-600 hover:bg-amber-50 p-2 rounded-lg transition-colors" title="Edit Program">
              <Icon name="lucide:edit-3" class="w-4 h-4" />
            </button>
            <!-- Delete Button -->
            <button @click="confirmDeleteProgram(program.id)" class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition-colors" title="Hapus Program">
              <Icon name="lucide:trash-2" class="w-4 h-4" />
            </button>
            
            <!-- Accordion Toggle Button -->
            <button @click="toggleProgramAccordion(program.id)" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300 ml-1" :class="expandedPrograms.has(program.id) ? 'rotate-180' : ''">
              <Icon name="lucide:chevron-down" class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Accordion Body -->
        <div v-show="expandedPrograms.has(program.id)" class="border-t border-slate-100 bg-slate-50/50 p-4 sm:p-6 animate-fade-in">
          
          <div v-if="program.indicators && program.indicators.length > 0" class="space-y-3 mb-6">
            <div v-for="(indicator, idx) in program.indicators" :key="indicator.id" class="flex justify-between items-start sm:items-center p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:border-slate-200 transition-all gap-4" :class="{ 'opacity-60 bg-slate-100/60': indicator.is_active === false }">
              <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 sm:mt-0" :class="{ 'bg-slate-200': indicator.is_active === false }">
                  {{ idx + 1 }}
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                  <span class="text-slate-700 text-sm font-medium leading-relaxed" :class="{ 'line-through text-slate-500': indicator.is_active === false }">{{ indicator.name }}</span>
                  <span v-if="indicator.is_active === false" class="w-fit px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded uppercase">Nonaktif</span>
                </div>
              </div>

              <div class="flex items-center gap-1 shrink-0">
                <!-- Toggle Active Indicator -->
                <button @click="toggleIndicatorStatus(indicator)" class="p-2 rounded-lg transition-colors" :class="indicator.is_active !== false ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-200'" :title="indicator.is_active !== false ? 'Nonaktifkan Indikator' : 'Aktifkan Indikator'">
                  <Icon :name="indicator.is_active !== false ? 'lucide:power' : 'lucide:power-off'" class="w-4 h-4" />
                </button>
                <!-- Edit Indicator -->
                <button @click="openEditIndicator(indicator)" class="text-slate-400 hover:text-amber-600 p-2 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Indikator">
                  <Icon name="lucide:edit-3" class="w-4 h-4" />
                </button>
                <!-- Delete Indicator -->
                <button @click="confirmDeleteIndicator(indicator.id)" class="text-slate-400 hover:text-rose-600 p-2 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Indikator">
                  <Icon name="lucide:trash-2" class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-slate-400 italic mb-6 py-4 px-4 bg-white border border-dashed border-slate-200 rounded-xl text-center">
            Belum ada indikator untuk program ini.
          </div>

          <!-- Add Indicator Inline -->
          <div v-if="addingIndicatorFor === program.id" class="flex flex-col sm:flex-row gap-2">
            <BaseInput v-model="newIndicatorName" placeholder="Ketik nama indikator..." class="flex-1" @keyup.enter="addIndicator(program.id)" />
            <div class="flex gap-2">
              <BaseButton variant="primary" :loading="isSavingIndicator" @click="addIndicator(program.id)">
                <template #prepend><Icon name="lucide:save" class="w-4 h-4" /></template>
                Simpan Data
              </BaseButton>
              <BaseButton variant="outline" :disabled="isSavingIndicator" @click="addingIndicatorFor = null">
                <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
                Batal
              </BaseButton>
            </div>
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
          <BaseButton variant="ghost" :disabled="isSavingProgram" @click="isAddingProgram = false">
            <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
            Batal
          </BaseButton>
          <BaseButton variant="primary" :disabled="!newProgramName" :loading="isSavingProgram" @click="addProgram">
            <template #prepend><Icon name="lucide:save" class="w-4 h-4" /></template>
            Simpan Data
          </BaseButton>
        </div>
      </template>
    </BaseModal>

    <!-- Modal Edit Program -->
    <BaseModal :show="isEditingProgram" title="Edit Program Perkembangan" @close="isEditingProgram = false">
      <div class="space-y-4 py-2">
        <div class="space-y-2">
          <label class="text-sm font-bold text-slate-700">Nama Program</label>
          <BaseInput 
            v-model="editProgramName" 
            placeholder="Contoh: Fisik Motorik" 
            @keyup.enter="updateProgram"
            autofocus
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-3 w-full">
          <BaseButton variant="ghost" :disabled="isUpdatingProgram" @click="isEditingProgram = false">
            <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
            Batal
          </BaseButton>
          <BaseButton variant="primary" :disabled="!editProgramName" :loading="isUpdatingProgram" @click="updateProgram">
            <template #prepend><Icon name="lucide:save" class="w-4 h-4" /></template>
            Simpan Data
          </BaseButton>
        </div>
      </template>
    </BaseModal>

    <!-- Modal Edit Indicator -->
    <BaseModal :show="isEditingIndicator" title="Edit Indikator Penilaian" @close="isEditingIndicator = false">
      <div class="space-y-4 py-2">
        <div class="space-y-2">
          <label class="text-sm font-bold text-slate-700">Nama Indikator</label>
          <BaseInput 
            v-model="editIndicatorName" 
            placeholder="Contoh: Terbiasa mengucapkan salam..." 
            @keyup.enter="updateIndicator"
            autofocus
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-3 w-full">
          <BaseButton variant="ghost" :disabled="isUpdatingIndicator" @click="isEditingIndicator = false">
            <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
            Batal
          </BaseButton>
          <BaseButton variant="primary" :disabled="!editIndicatorName" :loading="isUpdatingIndicator" @click="updateIndicator">
            <template #prepend><Icon name="lucide:save" class="w-4 h-4" /></template>
            Simpan Data
          </BaseButton>
        </div>
      </template>
    </BaseModal>

    <!-- Delete Program Modal -->
    <ConfirmModal
      :show="showDeleteProgramModal"
      title="Hapus Program Perkembangan?"
      message="Anda yakin ingin menghapus program ini beserta seluruh indikatornya? Data yang dihapus tidak dapat dikembalikan."
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeletingProgram"
      @confirm="executeDeleteProgram"
      @cancel="showDeleteProgramModal = false"
    />

    <!-- Delete Indicator Modal -->
    <ConfirmModal
      :show="showDeleteIndicatorModal"
      title="Hapus Indikator?"
      message="Anda yakin ingin menghapus indikator ini dari program perkembangan?"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeletingIndicator"
      @confirm="executeDeleteIndicator"
      @cancel="showDeleteIndicatorModal = false"
    />
  </div>
</template>
