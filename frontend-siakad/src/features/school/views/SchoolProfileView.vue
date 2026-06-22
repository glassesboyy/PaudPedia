<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { schoolService } from '@/features/school/services/school.service'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(true)
const school = ref<any>(null)
const error = ref('')

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchSchoolProfile()
  }
})

async function fetchSchoolProfile() {
  isLoading.value = true
  try {
    const response = await schoolService.getProfile(schoolStore.currentSchoolId!)
    school.value = response.data
  } catch (err: any) {
    error.value = 'Gagal mengambil data profil sekolah.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-heading">Profil Sekolah</h1>
          <p class="text-sm text-muted">Informasi identitas dan rincian kontak lembaga Anda</p>
        </div>
      </div>
      <BaseButton 
        variant="primary" 
        @click="router.push({ name: 'SchoolSettings' })"
        class="shadow-lg shadow-primary-500/20"
      >
        <template #prepend><Icon name="lucide:edit-3" class="w-4 h-4" /></template>
        Edit Data Sekolah
      </BaseButton>
    </div>

    <!-- Error State -->
    <BaseCard v-if="error" class="p-12 text-center flex flex-col items-center gap-4">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-500" />
      <p class="text-lg font-bold text-slate-900">{{ error }}</p>
      <BaseButton variant="outline" @click="fetchSchoolProfile">Coba Lagi</BaseButton>
    </BaseCard>

    <!-- Loading State: Skeletons (Match Settings Layout) -->
    <div v-else-if="isLoading" class="space-y-6 animate-fade-in">
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
        </div>
      </BaseCard>
    </div>

    <!-- Profile View Content (Match Settings Layout) -->
    <BaseCard v-else-if="school" class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <!-- Logo Area -->
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-8">
          <div class="w-32 h-32 rounded-2xl bg-white border-2 border-slate-200 p-2 overflow-hidden shadow-sm">
            <img v-if="school.logo_url" :src="school.logo_url" class="w-full h-full object-contain" />
            <div v-else class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
              <Icon name="lucide:building" class="w-12 h-12 opacity-30" />
            </div>
          </div>

          <div class="text-center sm:text-left space-y-1">
            <h3 class="text-2xl font-black text-heading leading-tight">{{ school.name }}</h3>
            <p class="text-sm font-bold text-primary-600 uppercase tracking-widest">NPSN: {{ school.npsn }}</p>
            <div class="flex items-center justify-center sm:justify-start gap-2 pt-2">
              <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                 <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                 Aktif & Terverifikasi
              </span>
            </div>
          </div>
        </div>

        <div class="p-8 space-y-10">
          <div class="space-y-8">
            <!-- Row 1: Identification -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
              <div class="space-y-1.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                  <Icon name="lucide:building" class="w-3 h-3" />
                  Nama Resmi Lembaga
                </p>
                <p class="text-base font-bold text-slate-800">{{ school.name }}</p>
              </div>
              
              <div class="space-y-1.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                  <Icon name="lucide:hash" class="w-3 h-3" />
                  Nomor Pokok Sekolah Nasional
                </p>
                <p class="text-base font-bold text-slate-800">{{ school.npsn }}</p>
              </div>
            </div>

            <!-- Row 2: Address (Full Row) -->
            <div class="space-y-1.5 pt-4 border-t border-slate-100/50">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <Icon name="lucide:map-pin" class="w-3 h-3" />
                Alamat Lengkap
              </p>
              <p class="text-base font-bold text-slate-800 leading-relaxed">{{ school.address }}</p>
            </div>

            <!-- Row 3: Contact -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 pt-4 border-t border-slate-100/50">
              <div class="space-y-1.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                  <Icon name="lucide:phone" class="w-3 h-3" />
                  Nomor Telepon
                </p>
                <p class="text-base font-bold text-slate-800">{{ school.phone || 'Belum terdaftar' }}</p>
              </div>
              
              <div class="space-y-1.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                  <Icon name="lucide:mail" class="w-3 h-3" />
                  Alamat Email (Resmi)
                </p>
                <p v-if="school.email" class="text-base font-bold text-primary-600">{{ school.email }}</p>
                <p v-else class="text-base font-bold text-slate-400 italic">Belum terdaftar</p>
              </div>
            </div>
          </div>
        </div>
    </BaseCard>
  </div>
</template>
