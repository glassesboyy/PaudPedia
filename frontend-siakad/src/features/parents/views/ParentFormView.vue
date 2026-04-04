<script setup lang="ts">
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { parentService } from '@/features/parents/services/parent.service'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseSelect from '@/components/ui/Input/Select.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const isEditMode = computed(() => route.name === 'ParentEdit')
const parentId = computed(() => Number(route.params.id))

const isLoading = ref(isEditMode.value)
const isSaving = ref(false)
const message = ref({ type: '', text: '' })

const form = reactive({
  email: '',
  father_name: '',
  mother_name: '',
  phone: '',
  father_occupation: '',
  mother_occupation: '',
  address: '',
})

// Hybrid Occupation State
const fatherOccType = ref('')
const motherOccType = ref('')
const fatherOccManual = ref('')
const motherOccManual = ref('')

const occupationOptions = [
  { value: 'PNS', label: 'PNS' },
  { value: 'Swasta', label: 'Karyawan Swasta' },
  { value: 'Wirausaha', label: 'Wirausaha' },
  { value: 'TNI/Polri', label: 'TNI/Polri' },
  { value: 'Buruh', label: 'Buruh' },
  { value: 'Petani', label: 'Petani' },
  { value: 'Pedagang', label: 'Pedagang' },
  { value: 'IRT', label: 'Ibu Rumah Tangga' },
  { value: 'OTHER', label: 'Lainnya (Input Manual...)' },
]

// Sync hybrid states to form
watch([fatherOccType, fatherOccManual], ([type, manual]) => {
  form.father_occupation = type === 'OTHER' ? manual : type
})
watch([motherOccType, motherOccManual], ([type, manual]) => {
  form.mother_occupation = type === 'OTHER' ? manual : type
})

const fieldErrors = reactive<Record<string, string>>({
  email: '',
  father_name: '',
  mother_name: '',
  phone: '',
  father_occupation: '',
  mother_occupation: '',
  address: '',
})

onMounted(async () => {
  if (isEditMode.value && schoolStore.currentSchoolId) {
    await fetchParent()
  }
})

async function fetchParent() {
  isLoading.value = true
  try {
    const response = await parentService.getParent(schoolStore.currentSchoolId!, parentId.value)
    const data = response.data
    form.email = data.email || ''
    form.father_name = data.father_name || ''
    form.mother_name = data.mother_name || ''
    form.phone = data.phone || ''
    
    // Smart Edit Mode for occupations
    const fOcc = data.father_occupation || ''
    const mOcc = data.mother_occupation || ''
    
    if (occupationOptions.some(o => o.value === fOcc)) {
      fatherOccType.value = fOcc
    } else if (fOcc) {
      fatherOccType.value = 'OTHER'
      fatherOccManual.value = fOcc
    }
    
    if (occupationOptions.some(o => o.value === mOcc)) {
      motherOccType.value = mOcc
    } else if (mOcc) {
      motherOccType.value = 'OTHER'
      motherOccManual.value = mOcc
    }

    form.address = data.address || ''
  } catch {
    message.value = { type: 'error', text: 'Gagal mengambil data orang tua.' }
  } finally {
    isLoading.value = false
  }
}

async function handleSubmit() {
  isSaving.value = true
  message.value = { type: '', text: '' }
  Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

  try {
    const payload: Record<string, any> = { ...form }

    if (isEditMode.value) {
      await parentService.updateParent(schoolStore.currentSchoolId!, parentId.value, payload)
      message.value = { type: 'success', text: 'Data orang tua berhasil diperbarui.' }
    } else {
      await parentService.createParent(schoolStore.currentSchoolId!, payload)
      message.value = { type: 'success', text: 'Orang tua berhasil didaftarkan. Email kredensial telah dikirim.' }
    }

    setTimeout(() => {
      router.push({ name: 'ParentList' })
    }, 2000)
  } catch (error: any) {
    const apiErrors = error.response?.data?.errors
    const backendMessage = error.response?.data?.message

    if (apiErrors) {
      Object.keys(apiErrors).forEach(key => {
        if (fieldErrors.hasOwnProperty(key)) {
          fieldErrors[key] = apiErrors[key][0]
        }
      })
      message.value = { type: 'error', text: 'Gagal menyimpan data. Periksa kembali isian Anda.' }
    } else {
      message.value = { type: 'error', text: backendMessage || 'Terjadi kesalahan. Silakan coba lagi.' }
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
      <button
        @click="router.push({ name: 'ParentList' })"
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ isEditMode ? 'Edit Data Orang Tua' : 'Tambah Orang Tua Baru' }}</h1>
        <p class="text-sm text-muted">{{ isEditMode ? 'Perbarui informasi wali murid' : 'Daftarkan wali murid baru ke dalam sistem' }}</p>
      </div>
    </div>

    <!-- Loading: Skeletons -->
    <div v-if="isLoading" class="space-y-6 animate-fade-in">
      <BaseCard class="p-10 border-none shadow-xl shadow-primary-900/5 space-y-10">
        <!-- Email Section -->
        <div class="space-y-4">
          <Skeleton width="160px" height="1.25rem" />
          <Skeleton height="3.5rem" class="rounded-2xl" />
          <Skeleton width="45%" height="0.75rem" class="mt-2" />
        </div>

        <!-- Names Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-4">
            <Skeleton width="110px" height="1rem" />
            <Skeleton height="3.5rem" class="rounded-2xl" />
          </div>
          <div class="space-y-4">
            <Skeleton width="110px" height="1rem" />
            <Skeleton height="3.5rem" class="rounded-2xl" />
          </div>
        </div>

        <!-- Phone Section -->
        <div class="space-y-4">
          <Skeleton width="130px" height="1rem" />
          <Skeleton height="3.5rem" class="rounded-2xl" />
        </div>

        <!-- Occupations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-4">
            <Skeleton width="120px" height="1rem" />
            <Skeleton height="3.5rem" class="rounded-2xl" />
          </div>
          <div class="space-y-4">
            <Skeleton width="120px" height="1rem" />
            <Skeleton height="3.5rem" class="rounded-2xl" />
          </div>
        </div>

        <!-- Address Section -->
        <div class="space-y-4">
          <Skeleton width="90px" height="1rem" />
          <Skeleton height="4rem" class="rounded-2xl" />
        </div>

        <!-- Action Buttons -->
        <div class="pt-10 border-t border-slate-100 flex justify-end gap-4">
          <Skeleton width="110px" height="3rem" class="rounded-2xl" />
          <Skeleton width="180px" height="3rem" class="rounded-2xl" />
        </div>
      </BaseCard>
    </div>

    <!-- Form -->
    <BaseCard v-else class="p-8 border-none shadow-xl shadow-primary-900/5">
      <form @submit.prevent="handleSubmit" class="space-y-8">
        <!-- Alert -->
        <BaseAlert v-if="message.text" :variant="message.type === 'success' ? 'success' : 'danger'" dismissible @dismiss="message.text = ''">
          {{ message.text }}
        </BaseAlert>

        <!-- Email -->
        <BaseInput
          v-model="form.email"
          label="Email Orang Tua"
          placeholder="contoh@email.com"
          type="email"
          required
          :error="fieldErrors.email"
          :disabled="isEditMode"
          :helper="!isEditMode ? 'Email ini akan menjadi akun login orang tua untuk mengakses SIAKAD.' : ''"
        >
          <template #prepend><Icon name="lucide:mail" class="w-5 h-5" /></template>
        </BaseInput>

        <!-- Parent Names -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <BaseInput
            v-model="form.father_name"
            label="Nama Ayah"
            placeholder="Nama lengkap ayah"
            required
            :error="fieldErrors.father_name"
          >
            <template #prepend><Icon name="lucide:user" class="w-5 h-5" /></template>
          </BaseInput>
          <BaseInput
            v-model="form.mother_name"
            label="Nama Ibu"
            placeholder="Nama lengkap ibu"
            required
            :error="fieldErrors.mother_name"
          >
            <template #prepend><Icon name="lucide:user" class="w-5 h-5" /></template>
          </BaseInput>
        </div>

        <!-- Phone -->
        <BaseInput
          v-model="form.phone"
          label="Nomor Telepon"
          placeholder="08XXXXXXXXXX"
          required
          :error="fieldErrors.phone"
          @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
        >
          <template #prepend><Icon name="lucide:phone" class="w-5 h-5" /></template>
        </BaseInput>

        <!-- Occupations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <BaseSelect
              v-model="fatherOccType"
              :options="occupationOptions"
              label="Pekerjaan Ayah"
              required
              :error="fatherOccType === 'OTHER' ? '' : fieldErrors.father_occupation"
            >
              <template #prepend><Icon name="lucide:briefcase" class="w-5 h-5" /></template>
            </BaseSelect>
            <transition name="slide-down">
              <BaseInput
                v-if="fatherOccType === 'OTHER'"
                v-model="fatherOccManual"
                placeholder="Masukkan pekerjaan ayah..."
                :error="fieldErrors.father_occupation"
                required
              />
            </transition>
          </div>

          <div class="space-y-3">
            <BaseSelect
              v-model="motherOccType"
              :options="occupationOptions"
              label="Pekerjaan Ibu"
              required
              :error="motherOccType === 'OTHER' ? '' : fieldErrors.mother_occupation"
            >
              <template #prepend><Icon name="lucide:briefcase" class="w-5 h-5" /></template>
            </BaseSelect>
            <transition name="slide-down">
              <BaseInput
                v-if="motherOccType === 'OTHER'"
                v-model="motherOccManual"
                placeholder="Masukkan pekerjaan ibu..."
                :error="fieldErrors.mother_occupation"
                required
              />
            </transition>
          </div>
        </div>

        <!-- Address -->
        <BaseInput
          v-model="form.address"
          label="Alamat"
          placeholder="Alamat lengkap orang tua"
          required
          :error="fieldErrors.address"
        >
          <template #prepend><Icon name="lucide:map-pin" class="w-5 h-5" /></template>
        </BaseInput>

        <!-- Actions -->
        <div class="pt-6 border-t border-border/50 flex justify-end gap-3">
          <BaseButton type="button" variant="outline" @click="router.push({ name: 'ParentList' })" :disabled="isSaving">
            Batal
          </BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSaving" class="px-8 shadow-lg shadow-primary-500/20">
            {{ isEditMode ? 'Simpan Perubahan' : 'Daftarkan Orang Tua' }}
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
