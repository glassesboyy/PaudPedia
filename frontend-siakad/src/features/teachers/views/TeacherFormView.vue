<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { teacherService } from '@/features/teachers/services/teacher.service'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isSaving = ref(false)
const message = ref({ type: '', text: '' })
const isSuccess = ref(false)

const form = reactive({
  name: '',
  email: '',
  nip: '',
  phone: '',
  specialization: '',
})

const fieldErrors = reactive<Record<string, string>>({
  name: '',
  email: '',
  nip: '',
  phone: '',
  specialization: '',
})

async function handleSubmit() {
  if (!schoolStore.currentSchoolId) return
  
  isSaving.value = true
  message.value = { type: '', text: '' }
  // Reset field errors
  Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

  try {
    await teacherService.createTeacher(schoolStore.currentSchoolId, form)
    isSuccess.value = true
    message.value = { 
      type: 'success', 
      text: 'Guru berhasil didaftarkan! Kredensial login telah dikirimkan ke email tujuan.' 
    }
    
    // Auto redirect after 5 seconds
    setTimeout(() => {
      router.push({ name: 'TeacherList' })
    }, 5000)
  } catch (error: any) {
    const apiErrors = error.response?.data?.errors
    const backendMessage = error.response?.data?.message

    if (apiErrors) {
      Object.keys(apiErrors).forEach(key => {
        if (fieldErrors.hasOwnProperty(key)) {
          fieldErrors[key] = apiErrors[key][0]
        }
      })
      message.value = { 
        type: 'error', 
        text: 'Gagal mendaftarkan guru. Periksa kembali data yang diinput.' 
      }
    } else {
      message.value = { 
        type: 'error', 
        text: backendMessage || 'Terjadi kesalahan. Silakan coba lagi.' 
      }
    }
    isSaving.value = false
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <button 
        @click="router.push({ name: 'TeacherList' })" 
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors focus:outline-none"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">Tambah Guru Baru</h1>
        <p class="text-sm text-muted">Daftarkan guru baru ke dalam sistem</p>
      </div>
    </div>

    <!-- Success View -->
    <BaseCard v-if="isSuccess" class="p-8 text-center animate-fade-in space-y-6">
      <div class="w-20 h-20 bg-success-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-success-100">
        <Icon name="lucide:check" class="w-10 h-10 text-success-600" />
      </div>
      <div>
        <h2 class="text-xl font-bold text-heading mb-2">Pendaftaran Berhasil!</h2>
        <p class="text-body max-w-md mx-auto">
          Akun guru untuk <strong>{{ form.name }}</strong> telah dibuat. Kami telah mengirimkan email berisi password sementara ke <strong>{{ form.email }}</strong>.
        </p>
      </div>
      <div class="pt-4 space-y-3">
        <BaseButton @click="router.push({ name: 'TeacherList' })" class="w-full sm:w-auto min-w-[160px]">
          Lihat Daftar Guru
        </BaseButton>
        <p class="text-xs text-muted">Akan diarahkan otomatis dalam beberapa detik...</p>
      </div>
    </BaseCard>

    <!-- Form View -->
    <BaseCard v-else class="overflow-hidden">
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Error Message -->
        <div 
          v-if="message.text && message.type === 'error'" 
          class="p-4 rounded-lg text-sm bg-danger-50 text-danger-700 border border-danger-200"
        >
          {{ message.text }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2 space-y-6">
            <BaseInput
              v-model="form.name"
              label="Nama Lengkap"
              placeholder="Contoh: Budi Santoso, S.Pd"
              required
              :disabled="isSaving"
              :error="fieldErrors.name"
            >
              <template #prepend>
                <Icon name="lucide:user" class="w-4 h-4" />
              </template>
            </BaseInput>

            <BaseInput
              v-model="form.email"
              label="Alamat Email"
              type="email"
              placeholder="Contoh: budi@email.com"
              required
              :disabled="isSaving"
              :error="fieldErrors.email"
              helper="Email ini akan digunakan sebagai akun login guru untuk mengakses SIAKAD."
            >
              <template #prepend>
                <Icon name="lucide:mail" class="w-4 h-4" />
              </template>
            </BaseInput>
          </div>

          <BaseInput
            v-model="form.nip"
            label="NIP"
            placeholder="Wajib 18 digit angka"
            required
            :disabled="isSaving"
            :maxlength="18"
            :error="fieldErrors.nip"
            @input="form.nip = form.nip.replace(/[^0-9]/g, '')"
          >
            <template #prepend>
              <Icon name="lucide:id-card" class="w-4 h-4" />
            </template>
          </BaseInput>

          <BaseInput
            v-model="form.phone"
            label="Nomor Telepon"
            type="tel"
            placeholder="Minimal 10 digit"
            required
            :disabled="isSaving"
            :error="fieldErrors.phone"
            @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
          >
            <template #prepend>
              <Icon name="lucide:phone" class="w-4 h-4" />
            </template>
          </BaseInput>

          <div class="md:col-span-2">
            <BaseInput
              v-model="form.specialization"
              label="Spesialisasi / Mata Pelajaran"
              placeholder="Contoh: Konseling, Bahasa Inggris, dll"
              :disabled="isSaving"
              :error="fieldErrors.specialization"
            >
              <template #prepend>
                <Icon name="lucide:book-open" class="w-4 h-4" />
              </template>
            </BaseInput>
          </div>
        </div>

        <div class="pt-6 border-t border-border-muted flex flex-col-reverse sm:flex-row justify-end gap-3">
          <BaseButton 
            variant="ghost" 
            type="button" 
            @click="router.back()"
            :disabled="isSaving"
          >
              <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
              Batal
            </BaseButton>
          <BaseButton 
            type="submit" 
            :loading="isSaving"
            class="min-w-[140px]"
          >
            Daftarkan Guru
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
