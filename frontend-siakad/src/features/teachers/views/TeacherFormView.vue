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
    <!-- Back button -->
    <button 
      @click="router.back()" 
      class="flex items-center gap-2 text-sm text-muted hover:text-primary-600 transition-colors mb-4 focus:outline-none"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar Guru
    </button>

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-heading">Tambah Guru Baru</h1>
      <p class="text-muted">Masukkan data guru untuk didaftarkan ke sekolah Anda</p>
    </div>

    <!-- Success View -->
    <BaseCard v-if="isSuccess" class="p-8 text-center animate-fade-in space-y-6">
      <div class="w-20 h-20 bg-success-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-success-100">
        <svg class="w-10 h-10 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
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
          <BaseInput
            v-model="form.name"
            label="Nama Lengkap"
            placeholder="Contoh: Budi Santoso, S.Pd"
            required
            :disabled="isSaving"
            :error="fieldErrors.name"
          >
            <template #prepend>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
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
          >
            <template #prepend>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10a2 2 0 002 2z" />
              </svg>
            </template>
          </BaseInput>

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
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
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
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
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
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
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
