<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { operatorService } from '@/features/operators/services/operator.service'
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
  phone: '',
})

const fieldErrors = reactive<Record<string, string>>({
  name: '',
  email: '',
  phone: '',
})

async function handleSubmit() {
  if (!schoolStore.currentSchoolId) return
  
  isSaving.value = true
  message.value = { type: '', text: '' }
  Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

  try {
    await operatorService.createOperator(schoolStore.currentSchoolId, form)
    isSuccess.value = true
    message.value = { 
      type: 'success', 
      text: 'Operator berhasil didaftarkan! Kredensial login telah dikirimkan ke email tujuan.' 
    }
    
    setTimeout(() => {
      router.push({ name: 'OperatorList' })
    }, 4000)
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
        text: 'Gagal mendaftarkan operator. Periksa kembali input Anda.' 
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
  <div class="max-w-3xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <button 
        @click="router.push({ name: 'OperatorList' })" 
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors focus:outline-none shadow-sm"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">Tambah Operator Sekolah</h1>
        <p class="text-sm text-muted">Daftarkan tim operasional baru untuk mengelola administrasi harian sekolah</p>
      </div>
    </div>

    <!-- Success View -->
    <BaseCard v-if="isSuccess" class="p-8 text-center animate-fade-in space-y-6 border-none shadow-xl shadow-primary-900/5">
      <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-emerald-100 shadow-sm">
        <Icon name="lucide:check" class="w-10 h-10 text-emerald-600" />
      </div>
      <div>
        <h2 class="text-2xl font-black text-heading mb-2">Pendaftaran Berhasil!</h2>
        <p class="text-body max-w-md mx-auto leading-relaxed">
          Akun operator sekolah untuk <strong>{{ form.name }}</strong> telah aktif. Jika ini adalah pengguna baru, sistem telah mengirimkan kredensial login sementara ke <strong>{{ form.email }}</strong>.
        </p>
      </div>
      <div class="pt-4 space-y-3">
        <BaseButton @click="router.push({ name: 'OperatorList' })" variant="primary" class="w-full sm:w-auto min-w-[180px]">
          Lihat Daftar Operator
        </BaseButton>
        <p class="text-xs text-muted font-medium">Akan diarahkan otomatis ke daftar operator...</p>
      </div>
    </BaseCard>

    <!-- Form View -->
    <BaseCard v-else class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="p-6 bg-violet-50/50 border-b border-violet-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-violet-600 flex items-center justify-center text-white shadow-lg shadow-violet-600/20 shrink-0">
          <Icon name="lucide:user-cog" class="w-6 h-6" />
        </div>
        <div>
          <h3 class="text-base font-bold text-heading">Informasi Akun Operator</h3>
          <p class="text-xs text-muted">Operator berhak melakukan CRUD Siswa, Guru, Kelas, dan Keuangan operasional.</p>
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="p-6 sm:p-8 space-y-6">
        <!-- Error Message -->
        <div 
          v-if="message.text && message.type === 'error'" 
          class="p-4 rounded-xl text-sm bg-danger-50 text-danger-700 border border-danger-200 flex items-center gap-3 animate-shake"
        >
          <Icon name="lucide:alert-circle" class="w-5 h-5 shrink-0 text-danger-600" />
          <span>{{ message.text }}</span>
        </div>

        <div class="space-y-6">
          <BaseInput
            v-model="form.name"
            label="Nama Lengkap Operator"
            placeholder="Contoh: Siti Aminah, S.Kom"
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
            placeholder="Contoh: siti.operator@sekolah.id"
            required
            :disabled="isSaving"
            :error="fieldErrors.email"
            helper="Email ini digunakan untuk login dan menerima password sementara dari sistem."
          >
            <template #prepend>
              <Icon name="lucide:mail" class="w-4 h-4" />
            </template>
          </BaseInput>

          <BaseInput
            v-model="form.phone"
            label="Nomor Telepon / WhatsApp"
            type="tel"
            placeholder="Minimal 10 digit angka"
            required
            :disabled="isSaving"
            :error="fieldErrors.phone"
            @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
            helper="Digunakan untuk komunikasi koordinasi operasional sekolah."
          >
            <template #prepend>
              <Icon name="lucide:phone" class="w-4 h-4" />
            </template>
          </BaseInput>
        </div>

        <div class="pt-8 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
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
            variant="primary"
            type="submit" 
            :loading="isSaving"
            class="min-w-[160px] shadow-lg shadow-primary-600/20"
          >
            <template #prepend><Icon name="lucide:user-plus" class="w-4 h-4" /></template>
            Daftarkan Operator
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
