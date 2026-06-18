<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { parentService } from '@/features/parents/services/parent.service'
import type { ParentProfile } from '@/types'
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

const copy = computed(() => getCopy('parent'))

const isLoading = ref(false)
const parents = ref<ParentProfile[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const generalError = ref('')
const searchQuery = ref('')

// Delete modal state
const showDeleteModal = ref(false)
const deleteTarget = ref<ParentProfile | null>(null)
const isDeleting = ref(false)

const isHeadmaster = computed(() => schoolStore.isHeadmaster)

onMounted(() => {
  fetchParents()
})

async function fetchParents(page = 1) {
  if (!schoolStore.currentSchoolId) return
  isLoading.value = true
  generalError.value = ''
  try {
    const response = await parentService.getParents(schoolStore.currentSchoolId, {
      page,
      search: searchQuery.value || undefined,
    })
    parents.value = response.data
    meta.value = response.meta
  } catch (error) {
    console.error('Gagal mengambil data orang tua:', error)
    generalError.value = 'Gagal memuat daftar orang tua. Silakan periksa koneksi Anda.'
  } finally {
    isLoading.value = false
  }
}

function handleSearch() {
  fetchParents(1)
}

function handleReset() {
  searchQuery.value = ''
  fetchParents(1)
}

function confirmDelete(parent: ParentProfile) {
  deleteTarget.value = parent
  showDeleteModal.value = true
}

async function executeDelete() {
  if (!deleteTarget.value) return
  isDeleting.value = true
  try {
    await parentService.deleteParent(schoolStore.currentSchoolId!, deleteTarget.value.id)
    await fetchParents()
  } catch (error: any) {
    generalError.value = error.response?.data?.message || 'Gagal menghapus data orang tua.'
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
    deleteTarget.value = null
  }
}

function getParentDisplayName(p: ParentProfile): string {
  const names = [p.father_name, p.mother_name].filter(Boolean)
  return names.join(' & ') || p.email
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
      <BaseButton v-if="isHeadmaster" @click="router.push({ name: 'ParentCreate' })">
        <template #prepend>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Tambah Orang Tua
      </BaseButton>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="flex-1 max-w-md">
        <BaseInput
          v-model="searchQuery"
          placeholder="Cari nama, email, atau telepon..."
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

    <!-- Error -->
    <BaseAlert v-if="generalError" variant="danger" dismissible @dismiss="generalError = ''">
      {{ generalError }}
    </BaseAlert>

    <!-- Table -->
    <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-muted/50 border-b border-border/50">
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Nama Orang Tua</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Email</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap">Telepon</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-center">Jumlah Anak</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 bg-white">
            <!-- Loading State -->
            <tr v-if="isLoading" v-for="i in 5" :key="i">
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <Skeleton width="2.5rem" height="2.5rem" class="rounded-xl" />
                  <Skeleton width="160px" height="0.875rem" />
                </div>
              </td>
              <td class="px-8 py-5"><Skeleton width="180px" height="0.875rem" /></td>
              <td class="px-8 py-5"><Skeleton width="120px" height="0.875rem" /></td>
              <td class="px-8 py-5 text-center"><Skeleton width="70px" height="1.25rem" class="rounded-full mx-auto" /></td>
              <td class="px-8 py-5 text-right"><Skeleton width="40px" height="2rem" class="ml-auto" /></td>
            </tr>
            <!-- Empty -->
            <tr v-else-if="parents.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <!-- Case: Data truly empty -->
                <div v-if="!searchQuery" class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                    <Icon name="lucide:users" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading text-center">Belum ada Orang Tua</p>
                    <p class="text-sm text-muted" v-if="isHeadmaster">Mulai tambahkan data orang tua siswa ke sistem.</p>
                    <p class="text-sm text-muted" v-else>Belum ada data orang tua yang terdaftar di sekolah.</p>
                  </div>
                  <BaseButton v-if="isHeadmaster" variant="primary" size="md" class="mt-2 w-full" @click="router.push({ name: 'ParentCreate' })">
                    Tambah Orang Tua Pertama
                  </BaseButton>
                </div>
                <!-- Case: Search result empty -->
                <div v-else class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-300">
                    <Icon name="lucide:search-x" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Pencarian Tidak Ditemukan</p>
                    <p class="text-sm text-muted">Tidak ditemukan data orang tua dengan kata kunci <span class="font-bold text-primary-600">"{{ searchQuery }}"</span></p>
                  </div>
                  <BaseButton variant="outline" size="md" class="mt-2 w-full" @click="handleReset">
                    Bersihkan Pencarian
                  </BaseButton>
                </div>
              </td>
            </tr>
            <!-- Data -->
            <tr
              v-else
              v-for="p in parents"
              :key="p.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group cursor-pointer"
              @click="router.push({ name: 'ParentDetail', params: { id: p.id } })"
            >
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-black text-sm shrink-0">
                    {{ (p.father_name || p.mother_name || 'O').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-heading group-hover:text-primary-700 transition-colors">{{ getParentDisplayName(p) }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <p class="text-sm text-body">{{ p.email }}</p>
              </td>
              <td class="px-8 py-5">
                <p class="text-sm text-body">{{ p.phone }}</p>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="px-3 py-1 rounded-full bg-secondary-50 text-secondary-700 text-xs font-bold border border-secondary-100">
                  {{ p.children_count ?? 0 }} anak
                </span>
              </td>
              <td class="px-8 py-5 text-right" @click.stop>
                <div class="flex items-center justify-end gap-2">
                  <button 
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50 flex items-center gap-1.5 font-bold text-xs"
                    title="Lihat Detail"
                    @click="router.push({ name: 'ParentDetail', params: { id: p.id } })"
                  >
                    <Icon name="lucide:eye" class="w-4 h-4" />
                    <span v-if="!isHeadmaster">Detail</span>
                  </button>
                  <template v-if="isHeadmaster">
                    <button
                      class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50"
                      title="Edit"
                      @click="router.push({ name: 'ParentEdit', params: { id: p.id } })"
                    >
                      <Icon name="lucide:pencil" class="w-4 h-4" />
                    </button>
                    <button
                      class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50"
                      title="Hapus"
                      @click="confirmDelete(p)"
                    >
                      <Icon name="lucide:trash-2" class="w-4 h-4" />
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-6 py-4 bg-muted/30 border-t border-border-muted flex items-center justify-between">
        <p class="text-xs text-muted">
          Menampilkan {{ parents.length }} dari {{ meta.total }} orang tua
        </p>
        <div class="flex gap-2">
          <BaseButton variant="outline" size="sm" :disabled="meta.current_page === 1" @click="fetchParents(meta.current_page - 1)">
            Sebelumnya
          </BaseButton>
          <BaseButton variant="outline" size="sm" :disabled="meta.current_page === meta.last_page" @click="fetchParents(meta.current_page + 1)">
            Selanjutnya
          </BaseButton>
        </div>
      </div>
    </BaseCard>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Data Orang Tua?"
      :message="`Anda akan menghapus data ${deleteTarget ? getParentDisplayName(deleteTarget) : ''}. Semua data siswa yang terkait juga akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>
