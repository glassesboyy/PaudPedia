<script setup lang="ts">
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import { parentService } from '@/features/parents/services/parent.service'
import { classService } from '@/features/classes/services/class.service'
import type { ParentProfile, ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const isEditMode = computed(() => route.name === 'StudentEdit')
const studentId = computed(() => Number(route.params.id))

const isLoading = ref(isEditMode.value)
const isSaving = ref(false)
const message = ref({ type: '', text: '' })

// Photo state
const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)

// Parents & Classes for dropdown
const parentsList = ref<ParentProfile[]>([])
const classesList = ref<ClassRoom[]>([])
const parentSearchQuery = ref('')

// Form state
const form = reactive({
  name: '',
  parent_profile_id: '',
  class_id: '',
  nisn: '',
  birth_date: '',
  gender: '',
  address: '',
  enrollment_date: '',
  status: 'active',
})

const fieldErrors = reactive<Record<string, string>>({
  name: '', parent_profile_id: '', class_id: '', nisn: '',
  birth_date: '', gender: '', address: '', photo: '',
  enrollment_date: '', status: '',
})

const genderOptions = [
  { value: '', label: 'Pilih jenis kelamin' },
  { value: 'male', label: 'Laki-laki' },
  { value: 'female', label: 'Perempuan' },
]

const statusOptions = [
  { value: 'active', label: 'Aktif' },
  { value: 'graduated', label: 'Lulus' },
  { value: 'transferred', label: 'Pindah' },
]

const parentOptions = computed(() => {
  return [
    { value: '', label: 'Pilih orang tua...' },
    ...parentsList.value.map(p => {
      const names = [p.father_name, p.mother_name].filter(Boolean).join(' & ')
      return { value: String(p.id), label: `${names || p.email} (${p.email})` }
    }),
  ]
})

const classOptions = computed(() => [
  { value: '', label: 'Belum ditentukan' },
  ...classesList.value.map(c => ({ value: String(c.id), label: c.name })),
])

onMounted(async () => {
  await Promise.all([fetchParents(), fetchClasses()])
  if (isEditMode.value && schoolStore.currentSchoolId) {
    await fetchStudent()
  }
})

async function fetchParents() {
  if (!schoolStore.currentSchoolId) return
  try {
    const response = await parentService.getParents(schoolStore.currentSchoolId, { per_page: 200 })
    parentsList.value = response.data
  } catch { /* ignore */ }
}

async function fetchClasses() {
  if (!schoolStore.currentSchoolId) return
  try {
    const response = await classService.getClasses(schoolStore.currentSchoolId, { per_page: 100 })
    classesList.value = response.data
  } catch { /* ignore */ }
}

async function fetchStudent() {
  isLoading.value = true
  try {
    const response = await studentService.getStudent(schoolStore.currentSchoolId!, studentId.value)
    const data = response.data
    form.name = data.name || ''
    form.parent_profile_id = data.parent_profile_id ? String(data.parent_profile_id) : ''
    form.class_id = data.class_id ? String(data.class_id) : ''
    form.nisn = data.nisn || ''
    form.birth_date = data.birth_date || ''
    form.gender = data.gender || ''
    form.address = data.address || ''
    form.enrollment_date = data.enrollment_date || ''
    form.status = data.status || 'active'
    photoPreview.value = data.photo_url || null
  } catch {
    message.value = { type: 'error', text: 'Gagal mengambil data siswa.' }
  } finally {
    isLoading.value = false
  }
}

function handlePhotoChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    photoFile.value = target.files[0]
    photoPreview.value = URL.createObjectURL(target.files[0])
    fieldErrors.photo = ''
  }
}

async function handleSubmit() {
  isSaving.value = true
  message.value = { type: '', text: '' }
  Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('parent_profile_id', form.parent_profile_id)
    if (form.class_id) formData.append('class_id', form.class_id)
    if (form.nisn) formData.append('nisn', form.nisn)
    formData.append('birth_date', form.birth_date)
    formData.append('gender', form.gender)
    if (form.address) formData.append('address', form.address)
    if (form.enrollment_date) formData.append('enrollment_date', form.enrollment_date)
    formData.append('status', form.status)
    if (photoFile.value) formData.append('photo', photoFile.value)

    if (isEditMode.value) {
      formData.append('_method', 'PUT')
      await studentService.updateStudent(schoolStore.currentSchoolId!, studentId.value, formData)
      message.value = { type: 'success', text: 'Data siswa berhasil diperbarui.' }
    } else {
      await studentService.createStudent(schoolStore.currentSchoolId!, formData)
      message.value = { type: 'success', text: 'Siswa berhasil didaftarkan.' }
    }

    setTimeout(() => {
      router.push({ name: 'StudentList' })
    }, 2000)
  } catch (error: any) {
    const apiErrors = error.response?.data?.errors
    const backendMessage = error.response?.data?.message
    if (apiErrors) {
      Object.keys(apiErrors).forEach(key => {
        if (fieldErrors.hasOwnProperty(key)) fieldErrors[key] = apiErrors[key][0]
      })
      message.value = { type: 'error', text: 'Gagal menyimpan data. Periksa kembali isian Anda.' }
    } else {
      message.value = { type: 'error', text: backendMessage || 'Terjadi kesalahan.' }
    }
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.push({ name: 'StudentList' })" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors">
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ isEditMode ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h1>
        <p class="text-sm text-muted">{{ isEditMode ? 'Perbarui informasi peserta didik' : 'Daftarkan peserta didik baru ke dalam sistem' }}</p>
      </div>
    </div>

    <!-- Loading: Skeletons -->
    <div v-if="isLoading" class="space-y-6 animate-fade-in">
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-8">
          <Skeleton width="8rem" height="8rem" class="rounded-2xl shrink-0" />
          <div class="space-y-3 w-full"><Skeleton width="30%" height="1rem" /><Skeleton width="50%" height="1rem" /></div>
        </div>
        <div class="p-8 space-y-8">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
            <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
          </div>
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
            <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
          </div>
          <div class="space-y-2"><Skeleton width="20%" height="1rem" /><Skeleton height="3rem" /></div>
          <div class="pt-6 border-t flex justify-end gap-3"><Skeleton width="6rem" height="2.5rem" /><Skeleton width="10rem" height="2.5rem" /></div>
        </div>
      </BaseCard>
    </div>

    <!-- Form -->
    <BaseCard v-else class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <form @submit.prevent="handleSubmit">
        <!-- Photo Upload Section -->
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-8">
          <div class="relative group">
            <div class="w-32 h-32 rounded-2xl bg-white border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-primary-400">
              <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
              <div v-else class="text-center p-4">
                <Icon name="lucide:user" class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Foto Siswa</p>
              </div>
              <input type="file" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" @change="handlePhotoChange" />
            </div>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-primary-600 text-white rounded-xl flex items-center justify-center shadow-lg border-4 border-white pointer-events-none group-hover:scale-110 transition-transform">
              <Icon name="lucide:camera" class="w-5 h-5" />
            </div>
          </div>
          <div class="text-center sm:text-left space-y-2">
            <h3 class="text-lg font-bold text-heading">Foto Profil</h3>
            <p class="text-xs text-muted max-w-sm">Format: JPG, PNG, atau WebP. Maksimal 2MB.</p>
            <p v-if="fieldErrors.photo" class="text-xs font-bold text-danger-600">{{ fieldErrors.photo }}</p>
          </div>
        </div>

        <div class="p-8 space-y-8">
          <BaseAlert v-if="message.text" :variant="message.type === 'success' ? 'success' : 'danger'" dismissible @dismiss="message.text = ''">
            {{ message.text }}
          </BaseAlert>

          <!-- Section: Data Siswa -->
          <div class="space-y-6">
            <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
              <Icon name="lucide:backpack" class="w-5 h-5 text-primary-600" /> Data Siswa
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <BaseInput v-model="form.name" label="Nama Lengkap" placeholder="Nama lengkap siswa" required :error="fieldErrors.name">
                <template #prepend><Icon name="lucide:user" class="w-5 h-5" /></template>
              </BaseInput>
              <BaseInput 
                v-model="form.nisn" 
                label="NISN" 
                placeholder="10 digit angka" 
                required 
                :maxlength="10"
                :error="fieldErrors.nisn"
                @input="form.nisn = form.nisn.replace(/[^0-9]/g, '').slice(0, 10)"
              >
                <template #prepend><Icon name="lucide:hash" class="w-5 h-5" /></template>
              </BaseInput>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <BaseInput v-model="form.birth_date" label="Tanggal Lahir" type="date" required :error="fieldErrors.birth_date">
                <template #prepend><Icon name="lucide:calendar" class="w-5 h-5" /></template>
              </BaseInput>
              <BaseSelect v-model="form.gender" :options="genderOptions" label="Jenis Kelamin" required :error="fieldErrors.gender" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <BaseSelect v-model="form.class_id" :options="classOptions" label="Kelas" required :error="fieldErrors.class_id" />
              <BaseSelect v-model="form.status" :options="statusOptions" label="Status" required :error="fieldErrors.status" />
            </div>

            <BaseInput v-model="form.enrollment_date" label="Tanggal Masuk" type="date" required :error="fieldErrors.enrollment_date">
              <template #prepend><Icon name="lucide:log-in" class="w-5 h-5" /></template>
            </BaseInput>

            <BaseInput v-model="form.address" label="Alamat" placeholder="Alamat tinggal siswa" required :error="fieldErrors.address">
              <template #prepend><Icon name="lucide:map-pin" class="w-5 h-5" /></template>
            </BaseInput>
          </div>

          <!-- Section: Data Orang Tua -->
          <div class="space-y-6">
            <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-2 border-b border-slate-100">
              <Icon name="lucide:users" class="w-5 h-5 text-primary-600" /> Data Orang Tua
            </h3>

            <BaseSelect
              v-model="form.parent_profile_id"
              :options="parentOptions"
              label="Pilih Orang Tua"
              required
              :error="fieldErrors.parent_profile_id"
            />

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
              <Icon name="lucide:info" class="w-5 h-5 text-primary-500 shrink-0" />
              <p class="text-xs text-muted">
                Orang tua belum terdaftar?
                <button
                  type="button"
                  class="text-primary-600 font-bold hover:underline ml-1"
                  @click="router.push({ name: 'ParentCreate' })"
                >
                  Tambahkan orang tua baru terlebih dahulu →
                </button>
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-6 border-t border-border/50 flex justify-end gap-3">
            <BaseButton type="button" variant="outline" @click="router.push({ name: 'StudentList' })" :disabled="isSaving">
              Batal
            </BaseButton>
            <BaseButton type="submit" variant="primary" :loading="isSaving" class="px-8 shadow-lg shadow-primary-500/20">
              {{ isEditMode ? 'Simpan Perubahan' : 'Daftarkan Siswa' }}
            </BaseButton>
          </div>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
