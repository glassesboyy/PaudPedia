<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import { teacherService } from '@/features/teachers/services/teacher.service'
import type { Teacher, ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import { ClassLevel } from '@/types/enums'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const isEditMode = computed(() => !!route.params.id)
const classId = computed(() => Number(route.params.id))

// Initialize loading to true if we are in Edit mode
const isLoading = ref(isEditMode.value)
const isLoadingTeachers = ref(false)
const isSubmitting = ref(false)
const generalError = ref('')
const teachers = ref<Teacher[]>([])

// Form state
const form = ref({
  name: '',
  homeroom_teacher_id: '' as number | string,
  capacity: '' as number | '',
  level: '' as string,
  academic_year: ''
})

// Options for Level Dropdown
const levelOptions = computed(() => {
  const options = (Object.values(ClassLevel) as string[]).map(val => ({
    value: val,
    label: val
  }))
  options.push({ value: 'Lainnya', label: 'Lainnya (Input Manual...)' })
  return options
})

// Manual level input for "Lainnya"
const customLevel = ref('')
const showCustomLevel = computed(() => form.value.level === 'Lainnya')

// Options for Teacher Dropdown
const teacherOptions = computed(() => {
  return teachers.value.map(t => ({
    value: t.id,
    label: `${t.name}${t.nip ? ` (NIP: ${t.nip})` : ''}`
  }))
})

// Current selected start year (proxy for academic_year)
const selectedStartYear = computed({
  get: () => form.value.academic_year ? form.value.academic_year.split('/')[0] : '',
  set: (val) => {
    const input = String(val).trim()
    // Process only if user types exactly 4 digits
    if (/^\d{4}$/.test(input)) {
      const year = parseInt(input)
      form.value.academic_year = `${year}/${year + 1}`
    } else {
      form.value.academic_year = input
    }
  }
})

// Validation errors from API
const fieldErrors = ref<Record<string, string[]>>({})

onMounted(async () => {
  if (!schoolStore.currentSchoolId) return
  
  await fetchTeachers()
  
  if (isEditMode.value) {
    await fetchClassDetails()
  }
})

async function fetchTeachers() {
  isLoadingTeachers.value = true
  try {
    const res = await teacherService.getTeachers(schoolStore.currentSchoolId!, { per_page: 100 })
    // With interceptor, res is ApiResponse<{ teachers: Teacher[], meta: any }>
    teachers.value = res.data.teachers || []
  } catch (error) {
    console.error('Failed to load teachers', error)
  } finally {
    isLoadingTeachers.value = false
  }
}

async function fetchClassDetails() {
  isLoading.value = true
  try {
    const res = await classService.getClass(schoolStore.currentSchoolId!, classId.value)
    // res is ApiResponse<ClassRoom>
    const data = res.data
    
    // Check if data.level is one of the standard levels
    const isStandardLevel = Object.values(ClassLevel).includes(data.level as ClassLevel)
    
    form.value = {
      name: data.name,
      homeroom_teacher_id: data.homeroom_teacher_id || '',
      capacity: data.capacity || '',
      level: isStandardLevel ? (data.level as ClassLevel) : (data.level ? 'Lainnya' : ''),
      academic_year: data.academic_year || ''
    }

    if (!isStandardLevel && data.level) {
      customLevel.value = data.level
    }
  } catch (error) {
    generalError.value = 'Gagal memuat data kelas.'
  } finally {
    isLoading.value = false
  }
}

async function handleSubmit() {
  fieldErrors.value = {}
  generalError.value = ''
  isSubmitting.value = true
  
  const finalLevel = form.value.level === 'Lainnya' ? customLevel.value : form.value.level
  
  const payload = {
    name: form.value.name,
    homeroom_teacher_id: Number(form.value.homeroom_teacher_id),
    capacity: form.value.capacity ? Number(form.value.capacity) : null,
    level: finalLevel,
    academic_year: form.value.academic_year
  }
  
  try {
    if (isEditMode.value) {
      await classService.updateClass(schoolStore.currentSchoolId!, classId.value, payload as any)
    } else {
      await classService.createClass(schoolStore.currentSchoolId!, payload as any)
    }
    router.push({ name: 'ClassList' })
  } catch (error: any) {
    const response = error.response?.data
    if (response?.errors) {
      fieldErrors.value = response.errors
    } else {
      generalError.value = response?.message || 'Terjadi kesalahan saat menyimpan kelas.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button 
        @click="router.back()"
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">
          {{ isEditMode ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
        </h1>
        <p class="text-sm text-muted">
          {{ isEditMode ? 'Ubah informasi ruang kelas' : 'Lengkapi formulir di bawah untuk membuat kelas baru' }}
        </p>
      </div>
    </div>

    <!-- Error State -->
    <BaseAlert
      v-if="generalError"
      variant="danger"
      dismissible
      @dismiss="generalError = ''"
    >
      {{ generalError }}
    </BaseAlert>

    <!-- Main Form -->
    <BaseCard class="border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <form @submit.prevent="handleSubmit" class="p-6 sm:p-8 space-y-8">
        
        <!-- Section: Basic Info -->
        <div class="space-y-6">
          <div class="flex items-center gap-3 pb-2 border-b border-border/50">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center text-primary-600">
              <Icon name="lucide:layout" class="w-4 h-4" />
            </div>
            <h2 class="text-lg font-bold text-heading">Informasi Dasar</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="isLoading" class="space-y-2">
              <Skeleton width="40%" height="1rem" variant="text" />
              <Skeleton height="3rem" />
            </div>
            <BaseInput
              v-else
              v-model="form.name"
              label="Nama Kelas"
              placeholder="Contoh: Kelas TK A Matahari"
              :error="fieldErrors.name?.[0]"
              required
            >
              <template #prefix>
                <Icon name="lucide:book-open" class="w-5 h-5" />
              </template>
            </BaseInput>

            <div v-if="isLoading" class="space-y-2">
              <Skeleton width="40%" height="1rem" variant="text" />
              <Skeleton height="3rem" />
            </div>
            <BaseInput
              v-else
              v-model="form.capacity"
              type="number"
              label="Kapasitas Murid"
              placeholder="Contoh: 20"
              :error="fieldErrors.capacity?.[0]"
              required
              min="1"
            >
              <template #prefix>
                <Icon name="lucide:users" class="w-5 h-5" />
              </template>
            </BaseInput>
          </div>
        </div>

        <!-- Section: Categories -->
        <div class="space-y-6">
          <div class="flex items-center gap-3 pb-2 border-b border-border/50">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center text-primary-600">
              <Icon name="lucide:tag" class="w-4 h-4" />
            </div>
            <h2 class="text-lg font-bold text-heading">Pengelompokan</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="isLoading" class="space-y-2">
              <Skeleton width="40%" height="1rem" variant="text" />
              <Skeleton height="3rem" />
            </div>
            <div v-else class="space-y-4">
              <BaseSelect
                v-model="form.level"
                label="Tingkat"
                :options="levelOptions"
                placeholder="Pilih tingkat..."
                :error="fieldErrors.level?.[0]"
                required
              >
                <template #prepend>
                  <Icon name="lucide:bar-chart" class="w-5 h-5" />
                </template>
              </BaseSelect>

              <!-- Custom Level Input -->
              <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
              >
                <BaseInput
                  v-if="showCustomLevel"
                  v-model="customLevel"
                  label="Nama Tingkat Kustom"
                  placeholder="Misal: Kelas Khusus, dsb"
                  :error="fieldErrors.level?.[0]"
                  required
                >
                  <template #prefix>
                    <Icon name="lucide:edit-3" class="w-5 h-5" />
                  </template>
                </BaseInput>
              </transition>
            </div>

            <div v-if="isLoading" class="space-y-2">
              <Skeleton width="40%" height="1rem" variant="text" />
              <Skeleton height="3rem" />
            </div>
            <BaseInput
              v-else
              v-model="selectedStartYear"
              label="Tahun Ajaran"
              placeholder="Contoh: 2024"
              :error="fieldErrors.academic_year?.[0]"
              required
              :maxlength="4"
              inputmode="numeric"
            >
              <template #prepend>
                <Icon name="lucide:calendar" class="w-5 h-5" />
              </template>
              <template #append v-if="selectedStartYear && /^\d{4}$/.test(String(selectedStartYear))">
                <span class="pr-3 select-none flex items-center whitespace-nowrap">
                  / {{ Number(selectedStartYear) + 1 }}
                </span>
              </template>
            </BaseInput>
          </div>
        </div>

        <!-- Section: Teacher -->
        <div class="space-y-6">
          <div class="flex items-center gap-3 pb-2 border-b border-border/50">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center text-primary-600">
              <Icon name="lucide:user-check" class="w-4 h-4" />
            </div>
            <h2 class="text-lg font-bold text-heading">Wali Kelas</h2>
          </div>

          <div v-if="isLoading" class="space-y-2">
            <Skeleton width="20%" height="1rem" variant="text" />
            <Skeleton height="3rem" />
          </div>
          <BaseSelect
            v-else
            v-model="form.homeroom_teacher_id"
            label="Pilih Wali Kelas"
            :options="teacherOptions"
            placeholder="Pilih seorang wali kelas..."
            :error="fieldErrors.homeroom_teacher_id?.[0]"
            required
            :disabled="isLoadingTeachers"
          >
            <template #prepend>
              <Icon name="lucide:user" class="w-5 h-5" />
            </template>
          </BaseSelect>
          <p class="text-xs text-muted -mt-4 font-medium">Bisa dipilih lebih dari satu kelas, sesuai persetujuan internal sekolah.</p>
        </div>

        <!-- Actions -->
        <div class="pt-6 border-t border-border/50 flex flex-col-reverse sm:flex-row justify-end gap-3">
          <BaseButton 
            type="button" 
            variant="outline" 
            class="w-full sm:w-auto px-8" 
            @click="router.back()"
            :disabled="isSubmitting"
          >
              <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
              Batal
            </BaseButton>
          <BaseButton 
            type="submit" 
            variant="primary" 
            class="w-full sm:w-auto px-8 shadow-lg shadow-primary-500/25"
            :disabled="isSubmitting || !form.name || !form.capacity || !form.homeroom_teacher_id || !form.level || !form.academic_year"
          >
            <template #prepend v-if="isSubmitting">
              <Icon name="lucide:loader-2" class="w-4 h-4 animate-spin" />
            </template>
            <template #prepend v-else>
            </template>
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kelas' }}
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
