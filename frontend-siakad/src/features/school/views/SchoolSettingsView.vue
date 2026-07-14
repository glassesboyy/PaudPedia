<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { schoolService } from '@/features/school/services/school.service'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const isSaving = ref(false)
const message = ref({ type: '', text: '' })
const logoPreview = ref<string | null>(null)
const logoFile = ref<File | null>(null)

// Transfer state
const transferEmail = ref('')
const isTransferring = ref(false)
const transferMessage = ref({ type: '', text: '' })
const transferError = ref('')
const showTransferConfirm = ref(false)

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
    formData.append('_method', 'PUT')
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    await schoolService.updateProfile(schoolStore.currentSchoolId!, formData)
    
    // Refresh school info in store
    await schoolStore.fetchMemberships()
    
    message.value = { type: 'success', text: 'Profil sekolah berhasil diperbarui.' }
    
    // Go back to profile after success
    setTimeout(() => {
      router.push({ name: 'SchoolProfile' })
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

async function handleTransfer() {
  if (!transferEmail.value || isTransferring.value) return
  showTransferConfirm.value = true
}

async function submitTransfer() {
  isTransferring.value = true
  transferMessage.value = { type: '', text: '' }
  transferError.value = ''
  showTransferConfirm.value = false

  try {
    const res = await schoolService.transferHeadmaster(schoolStore.currentSchoolId!, transferEmail.value)
    transferMessage.value = { type: 'success', text: res.message || 'Undangan berhasil dikirim!' }
    transferEmail.value = ''
  } catch (error: any) {
    const apiErrors = error.response?.data?.errors
    if (apiErrors && apiErrors.email) {
      transferError.value = apiErrors.email[0]
    } else {
      transferMessage.value = { 
        type: 'error', 
        text: error.response?.data?.message || 'Gagal mengirim undangan transfer.' 
      }
    }
  } finally {
    isTransferring.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button 
        @click="router.back()"
        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors"
      >
        <Icon name="lucide:arrow-left" class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-heading">Pengaturan Sekolah</h1>
        <p class="text-sm text-muted">Perbarui informasi identitas dan kontak lembaga Anda</p>
      </div>
    </div>

    <!-- Loading State: Skeletons -->
    <div v-if="isLoading" class="space-y-6 animate-fade-in">
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-8">
           <Skeleton width="8rem" height="8rem" class="rounded-2xl shrink-0" />
           <div class="space-y-3 w-full">
             <Skeleton width="40%" height="1.5rem" />
             <Skeleton width="60%" height="1rem" />
           </div>
        </div>
        <div class="p-8 space-y-8">
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
             <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
           </div>
           <div class="space-y-2"><Skeleton width="20%" height="1rem" /><Skeleton height="3rem" /></div>
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
             <div class="space-y-2"><Skeleton width="30%" height="1rem" /><Skeleton height="3rem" /></div>
           </div>
           <div class="pt-6 border-t border-border/50 flex justify-end gap-3">
             <Skeleton width="6rem" height="2.5rem" />
             <Skeleton width="10rem" height="2.5rem" />
           </div>
        </div>
      </BaseCard>
    </div>

    <!-- Form Content -->
    <BaseCard v-else class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <form @submit.prevent="handleSubmit">
        <!-- Logo Area -->
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-8">
          <div class="relative group">
            <div class="w-32 h-32 rounded-2xl bg-white border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-primary-400 group-hover:bg-primary-50/10">
              <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
              <div v-else class="text-center p-4">
                <Icon name="lucide:image" class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Logo</p>
              </div>
              <input 
                type="file" 
                class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                accept="image/*"
                @change="handleLogoChange" 
              />
            </div>
            <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-primary-600 text-white rounded-xl flex items-center justify-center shadow-lg border-4 border-white pointer-events-none group-hover:scale-110 transition-transform">
              <Icon name="lucide:camera" class="w-5 h-5" />
            </div>
          </div>

          <div class="text-center sm:text-left space-y-2">
            <h3 class="text-lg font-bold text-heading">Logo Sekolah</h3>
            <p class="text-xs text-muted max-w-sm">
              Gunakan logo resmi sekolah Anda. Format yang didukung: JPG, PNG, atau SVG. Maksimal 2MB.
            </p>
            <p v-if="fieldErrors.logo" class="text-xs font-bold text-danger-600">{{ fieldErrors.logo }}</p>
          </div>
        </div>

        <div class="p-8 space-y-8">
          <!-- Alert Message -->
          <BaseAlert 
            v-if="message.text" 
            :variant="message.type === 'success' ? 'success' : 'danger'"
            dismissible
            @dismiss="message.text = ''"
          >
            {{ message.text }}
          </BaseAlert>

          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <BaseInput
                v-model="form.name"
                label="Nama Sekolah"
                placeholder="Masukkan nama resmi sekolah"
                required
                :error="fieldErrors.name"
              >
                <template #prepend><Icon name="lucide:building" class="w-5 h-5" /></template>
              </BaseInput>
              
              <BaseInput
                v-model="form.npsn"
                label="NPSN"
                placeholder="8 Digit NPSN"
                required
                :maxlength="8"
                :error="fieldErrors.npsn"
                @input="form.npsn = form.npsn.replace(/[^0-9]/g, '')"
              >
                <template #prepend><Icon name="lucide:hash" class="w-5 h-5" /></template>
              </BaseInput>
            </div>

            <BaseInput
              v-model="form.address"
              label="Alamat Lengkap"
              placeholder="Jalan, No. Bangunan, Desa, Kecamatan, Kab/Kota"
              required
              :error="fieldErrors.address"
            >
              <template #prepend><Icon name="lucide:map-pin" class="w-5 h-5" /></template>
            </BaseInput>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <BaseInput
                v-model="form.phone"
                label="Nomor Telepon"
                placeholder="Contoh: 021XXXXXXXX"
                type="tel"
                :error="fieldErrors.phone"
                @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
              >
                <template #prepend><Icon name="lucide:phone" class="w-5 h-5" /></template>
              </BaseInput>
              
              <BaseInput
                v-model="form.email"
                label="Email Sekolah"
                placeholder="info@sekolah.sch.id"
                type="email"
                :error="fieldErrors.email"
              >
                <template #prepend><Icon name="lucide:mail" class="w-5 h-5" /></template>
              </BaseInput>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-6 border-t border-border/50 flex justify-end gap-3">
             <BaseButton 
              type="button" 
              variant="outline" 
              @click="router.push({ name: 'SchoolProfile' })"
              :disabled="isSaving"
            >
              Batal
            </BaseButton>
            <BaseButton
              type="submit"
              variant="primary"
              :loading="isSaving"
              class="px-8 shadow-lg shadow-primary-500/20"
            >
              <template #prepend><Icon name="lucide:save" class="w-4 h-4" /></template>
              Simpan Data
            </BaseButton>
          </div>
        </div>
      </form>
    </BaseCard>

    <!-- Danger Zone: Transfer Ownership -->
    <BaseCard v-if="!isLoading" class="p-0 border-danger-200 shadow-xl shadow-danger-900/5 overflow-hidden border">
      <div class="p-8 bg-danger-50 border-b border-danger-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-danger-100 rounded-xl flex items-center justify-center shrink-0">
          <Icon name="lucide:alert-triangle" class="w-6 h-6 text-danger-600" />
        </div>
        <div>
          <h3 class="text-lg font-bold text-danger-900">Transfer Kepemilikan (Danger Zone)</h3>
          <p class="text-sm text-danger-700">Tindakan ini akan memindahkan jabatan Kepala Sekolah beserta seluruh hak aksesnya ke akun lain.</p>
        </div>
      </div>
      
      <div class="p-8 space-y-6">
        <BaseAlert 
          v-if="transferMessage.text" 
          :variant="transferMessage.type === 'success' ? 'success' : 'danger'"
          dismissible
          @dismiss="transferMessage.text = ''"
        >
          {{ transferMessage.text }}
        </BaseAlert>
        
        <p class="text-sm text-slate-600">
          Masukkan alamat email pengguna yang akan menggantikan posisi Anda sebagai Kepala Sekolah. Sebuah undangan akan dikirimkan ke email tersebut. Jika undangan diterima, <strong>Anda akan langsung kehilangan akses ke panel ini!</strong>
        </p>

        <form @submit.prevent="handleTransfer" class="flex gap-4 items-end">
          <div class="flex-1">
            <BaseInput
              v-model="transferEmail"
              label="Email Kepala Sekolah Baru"
              placeholder="Contoh: guru@sekolah.sch.id"
              type="email"
              required
              :error="transferError"
            >
              <template #prepend><Icon name="lucide:mail" class="w-5 h-5" /></template>
            </BaseInput>
          </div>
          <BaseButton 
            type="submit" 
            variant="danger" 
            :loading="isTransferring"
            class="px-6 mb-1"
          >
            Kirim Undangan Transfer
          </BaseButton>
        </form>
      </div>
    </BaseCard>

    <!-- Confirm Modal -->
    <ConfirmModal
      :show="showTransferConfirm"
      title="Konfirmasi Transfer Kepemilikan"
      :message="`PERINGATAN! Anda akan kehilangan akses Kepala Sekolah jika Bpk/Ibu ${transferEmail} menyetujui undangan ini. Lanjutkan?`"
      confirmText="Kirim Undangan"
      variant="danger"
      @confirm="submitTransfer"
      @cancel="showTransferConfirm = false"
    />
  </div>
</template>
