<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import type { ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('class'))

const isHeadmaster = computed(() => schoolStore.isHeadmaster)

const isLoading = ref(false)
const classes = ref<ClassRoom[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const generalError = ref('')
const searchQuery = ref('')

// Delete modal state
const showDeleteModal = ref(false)
const deleteTarget = ref<ClassRoom | null>(null)
const isDeleting = ref(false)

onMounted(() => {
  fetchClasses()
})

async function fetchClasses(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  generalError.value = ''
  try {
    const params: Record<string, any> = { page }
    if (searchQuery.value) params.search = searchQuery.value

    const response = await classService.getClasses(schoolStore.currentSchoolId, params)
    classes.value = response.data
    meta.value = response.meta
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error)
    generalError.value = 'Gagal memuat daftar kelas. Silakan periksa koneksi Anda.'
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchClasses(1)
}

function handleReset() {
  searchQuery.value = ''
  fetchClasses(1)
}

function confirmDelete(classRoom: ClassRoom) {
  deleteTarget.value = classRoom
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await classService.deleteClass(schoolStore.currentSchoolId!, deleteTarget.value.id)
    showDeleteModal.value = false
    deleteTarget.value = null
    await fetchClasses()
  } catch (error: any) {
    const backendMessage = error.response?.data?.message
    alert(backendMessage || 'Gagal menghapus kelas.')
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">{{ copy.title }}</h1>
        <p class="text-muted">{{ copy.subtitle }}</p>
      </div>
      <BaseButton v-if="isHeadmaster" @click="router.push({ name: 'ClassCreate' })">
        <template #prepend>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Tambah Kelas Baru
      </BaseButton>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="flex-1 max-w-md">
        <BaseInput
          v-model="searchQuery"
          placeholder="Cari nama kelas..."
          @keyup.enter="handleSearch"
        >
          <template #prepend><Icon name="lucide:search" class="w-4 h-4" /></template>
        </BaseInput>
      </div>
      <BaseButton 
        v-if="searchQuery" 
        variant="outline" 
        size="md" 
        @click="handleReset"
        class="text-muted hover:text-primary-600"
      >
        <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
        Reset
      </BaseButton>
    </div>

    <!-- Error state -->
    <BaseAlert
      v-if="generalError"
      variant="danger"
      dismissible
      @dismiss="generalError = ''"
    >
      {{ generalError }}
    </BaseAlert>

    <!-- Table Card -->
    <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-muted/50 border-b border-border/50">
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Nama Kelas</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Wali Kelas</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-center">Tahun/Tingkat</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-center">Kapasitas</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 bg-white">
            <!-- Loading State -->
            <tr v-if="isLoading" v-for="i in 5" :key="i">
              <td class="px-8 py-5"><Skeleton width="160px" height="1rem" /></td>
              <td class="px-8 py-5"><Skeleton width="180px" height="1rem" /></td>
              <td class="px-8 py-5 text-center">
                <div class="flex flex-col items-center gap-1">
                  <Skeleton width="60px" height="1rem" class="rounded" />
                  <Skeleton width="40px" height="0.6rem" />
                </div>
              </td>
              <td class="px-8 py-5 text-center"><Skeleton width="50px" height="1rem" class="mx-auto" /></td>
              <td class="px-8 py-5 text-right"><Skeleton width="40px" height="2rem" class="ml-auto" /></td>
            </tr>
            <tr v-else-if="classes.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <!-- Case: Data truly empty -->
                <div v-if="!searchQuery" class="flex flex-col items-center gap-4 max-w-xs mx-auto text-center">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border mx-auto">
                    <Icon name="lucide:school" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading text-center">Belum ada Kelas</p>
                    <p class="text-sm text-muted text-center" v-if="isHeadmaster">Mulai tambahkan ruangan kelas untuk operasional sekolah.</p>
                    <p class="text-sm text-muted text-center" v-else>Anda belum ditugaskan sebagai wali kelas.</p>
                  </div>
                  <BaseButton 
                    v-if="isHeadmaster"
                    variant="primary" 
                    size="md" 
                    class="mt-2 w-full"
                    @click="router.push({ name: 'ClassCreate' })"
                  >
                    Tambah Kelas Pertama
                  </BaseButton>
                </div>
                <!-- Case: Search result empty -->
                <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                    <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Kelas Tidak Ditemukan</p>
                    <p class="text-sm text-muted">Tidak ditemukan kelas dengan kata kunci <span class="font-bold text-primary-600">"{{ searchQuery }}"</span></p>
                  </div>
                  <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                    Bersihkan Pencarian
                  </BaseButton>
                </div>
              </td>
            </tr>
            <tr 
              v-else
              v-for="c in classes" 
              :key="c.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group cursor-pointer"
              @click="router.push({ name: 'ClassDetail', params: { id: c.id } })"
            >
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div>
                    <p class="text-sm font-bold text-heading group-hover:text-primary-700 transition-colors">{{ c.name }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <p class="text-sm font-medium text-body">
                    {{ c.homeroom_teacher_name || 'Belum Ditentukan' }}
                  </p>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <div class="flex flex-col items-center justify-center gap-1">
                  <span class="px-2 py-0.5 rounded-md bg-secondary-50 text-secondary-700 text-xs font-semibold whitespace-nowrap border border-secondary-100">
                    {{ c.academic_year || '-' }}
                  </span>
                  <span class="text-[10px] text-muted font-bold tracking-wider uppercase">{{ c.level || '-' }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <div class="inline-flex items-baseline gap-1">
                  <span class="text-sm font-black text-slate-800">{{ c.current_students }}</span>
                  <span class="text-xs font-medium text-muted">/ {{ c.capacity || '∞' }}</span>
                </div>
                <!-- Capacity Warning -->
                <div v-if="c.capacity && c.current_students >= c.capacity" class="text-[10px] font-bold text-danger-600 mt-1">
                  Penuh
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50 flex items-center gap-1.5 font-bold text-xs"
                    title="Lihat Detail"
                    @click="router.push({ name: 'ClassDetail', params: { id: c.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                    <span v-if="!isHeadmaster">Detail</span>
                  </button>
                  <button 
                    v-if="isHeadmaster"
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50"
                    title="Edit Kelas"
                    @click.stop="router.push({ name: 'ClassEdit', params: { id: c.id } })"
                  >
                    <Icon name="lucide:pencil" class="w-4 h-4" />
                  </button>
                  <button 
                    v-if="isHeadmaster"
                    class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50"
                    @click.stop="confirmDelete(c)"
                    title="Hapus Kelas"
                  >
                    <Icon name="lucide:trash-2" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-6 py-4 bg-muted/30 border-t border-border-muted flex items-center justify-between">
        <p class="text-xs text-muted">
          Menampilkan {{ classes.length }} dari {{ meta.total }} kelas
        </p>
        <div class="flex gap-2">
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === 1"
            @click="fetchClasses(meta.current_page - 1)"
          >
            Sebelumnya
          </BaseButton>
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === meta.last_page"
            @click="fetchClasses(meta.current_page + 1)"
          >
            Selanjutnya
          </BaseButton>
        </div>
      </div>
    </BaseCard>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Kelas?"
      :message="`Anda akan menghapus kelas '${deleteTarget?.name || ''}'. Kelas yang masih memiliki siswa tidak dapat dihapus.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>
