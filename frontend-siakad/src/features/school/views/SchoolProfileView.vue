<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import { schoolService } from '@/features/school/services/school.service'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const schoolStore = useSchoolStore()

const isLoading = ref(false)
const isSaving = ref(false)
const message = ref({ type: '', text: '' })
const logoPreview = ref<string | null>(null)
const logoFile = ref<File | null>(null)

const form = reactive({
  name: '',
  npsn: '',
  address: '',
  phone: '',
  email: '',
})

const fieldErrors = reactive<Record<string, string>>({
  name: '',
  npsn: '',
  address: '',
  phone: '',
  email: '',
  logo: '',
})

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchSchoolProfile()
  }
})

async function fetchSchoolProfile() {
  isLoading.value = true
  try {
    const response = await schoolService.getProfile(schoolStore.currentSchoolId!)
    const school = response.data
    form.name = school.name
    form.npsn = school.npsn
    form.address = school.address
    form.phone = school.phone || ''
    form.email = school.email || ''
    logoPreview.value = school.logo_url || null
  } catch (error: any) {
    message.value = { type: 'error', text: 'Gagal mengambil data profil sekolah.' }
  } finally {
    isLoading.value = false
  }
}

function handleLogoChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
    fieldErrors.logo = ''
  }
}

async function handleSubmit() {
  isSaving.value = true
  message.value = { type: '', text: '' }
  // Reset field errors
  Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('npsn', form.npsn)
    formData.append('address', form.address)
    formData.append('phone', form.phone)
    formData.append('email', form.email)
    formData.append('_method', 'PUT') // For Laravel spoofing if needed
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    await schoolService.updateProfile(schoolStore.currentSchoolId!, formData)
    
    // Refresh school info in store
    await schoolStore.fetchMemberships()
    
    message.value = { type: 'success', text: 'Profil sekolah berhasil diperbarui.' }
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
        text: 'Gagal memperbarui profil sekolah. Periksa kembali data yang diinput.' 
      }
    } else {
      message.value = { 
        type: 'error', 
        text: backendMessage || 'Terjadi kesalahan. Silakan coba lagi.' 
      }
    }
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in">
    <div class="mb-8 flex justify-between items-end">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Profil Sekolah</h1>
        <p class="text-slate-500 font-medium">Kelola identitas dan informasi kontak lembaga Anda</p>
      </div>
    </div>

    <div v-if="isLoading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600"></div>
    </div>

    <BaseCard v-else class="p-0 border-none shadow-sm overflow-hidden">
      <form @submit.prevent="handleSubmit">
        <!-- Profile Header Area -->
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-8">
          <div class="relative group">
            <div class="w-32 h-32 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-primary-400 group-hover:shadow-xl group-hover:shadow-primary-900/10">
              <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
              <div v-else class="text-center p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Belum Ada Logo</p>
              </div>
              <input 
                type="file" 
                class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                accept="image/*"
                @change="handleLogoChange" 
              />
            </div>
            <div class="absolute -right-2 -bottom-2 w-9 h-9 bg-primary-600 text-white rounded-xl flex items-center justify-center shadow-lg border-4 border-white pointer-events-none group-hover:scale-110 transition-transform">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              </svg>
            </div>
          </div>

          <div class="text-center sm:text-left">
            <h2 class="text-xl font-black text-slate-900 leading-none mb-2">{{ form.name || 'Nama Sekolah' }}</h2>
            <p class="text-sm font-bold text-slate-400 mb-4">NPSN: {{ form.npsn || '-' }}</p>
            <div 
              v-if="fieldErrors.logo" 
              class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100"
            >
              {{ fieldErrors.logo }}
            </div>
            <p v-else class="text-xs text-slate-400 font-medium italic">Klik foto untuk mengganti logo sekolah (Format: JPG, PNG, Max 2MB)</p>
          </div>
        </div>

        <div class="p-8 space-y-8">
          <!-- Success/Error Message -->
          <transition name="fade">
            <div 
              v-if="message.text" 
              :class="[
                'p-4 rounded-xl text-sm font-bold flex items-center gap-3 border',
                message.type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-100' : 'bg-red-50 text-red-800 border-red-100'
              ]"
            >
              <div :class="['w-6 h-6 rounded-lg flex items-center justify-center', message.type === 'success' ? 'bg-emerald-200/50' : 'bg-red-200/50']">
                <svg v-if="message.type === 'success'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </div>
              {{ message.text }}
            </div>
          </transition>

          <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <BaseInput
                v-model="form.name"
                label="Nama Sekolah"
                placeholder="Masukkan nama sekolah"
                required
                :error="fieldErrors.name"
              />
              <BaseInput
                v-model="form.npsn"
                label="NPSN"
                placeholder="8 Digit NPSN (Angka)"
                required
                :maxlength="8"
                :error="fieldErrors.npsn"
                @input="form.npsn = form.npsn.replace(/[^0-9]/g, '')"
              />
            </div>

            <BaseInput
              v-model="form.address"
              label="Alamat Lengkap"
              placeholder="Masukkan alamat sekolah"
              required
              :error="fieldErrors.address"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <BaseInput
                v-model="form.phone"
                label="Nomor Telepon Sekolah"
                placeholder="Contoh: 021XXXXXXXX"
                type="tel"
                :error="fieldErrors.phone"
                @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
              />
              <BaseInput
                v-model="form.email"
                label="Email Sekolah"
                placeholder="Contoh: info@sekolah.sch.id"
                type="email"
                :error="fieldErrors.email"
              />
            </div>

            <div class="pt-6 flex justify-end border-t border-slate-100">
              <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="isSaving"
                class="min-w-[160px]"
              >
                Simpan Perubahan
              </BaseButton>
            </div>
          </div>
        </div>
      </form>
    </BaseCard>
  </div>
</template>
