<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import type { ClassRoom } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(false)
const classes = ref<ClassRoom[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const generalError = ref('')

onMounted(() => {
  fetchClasses()
})

async function fetchClasses(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  generalError.value = ''
  try {
    const response = await classService.getClasses(schoolStore.currentSchoolId, { page })
    // response is PaginatedResponse, response.data is ClassRoom[]
    classes.value = response.data
    meta.value = response.meta
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error)
    generalError.value = 'Gagal memuat daftar kelas. Silakan periksa koneksi Anda.'
  } finally {
    isLoading.value = false
  }
}

async function handleDelete(classRoom: ClassRoom) {
  if (!confirm(`Apakah Anda yakin ingin menghapus kelas ${classRoom.name}?`)) return

  try {
    await classService.deleteClass(schoolStore.currentSchoolId!, classRoom.id)
    await fetchClasses()
  } catch (error: any) {
    const backendMessage = error.response?.data?.message
    alert(backendMessage || 'Gagal menghapus kelas.')
  }
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">Daftar Kelas</h1>
        <p class="text-muted">Kelola data ruangan kelas dan wali kelas di sekolah Anda</p>
      </div>
      <BaseButton @click="router.push({ name: 'ClassCreate' })">
        <template #prepend>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Tambah Kelas Baru
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
            <tr v-if="isLoading">
              <td colspan="5" class="px-8 py-20 text-center">
                <Loader text="Memuat data kelas..." minHeight="300px" />
              </td>
            </tr>
            <tr v-else-if="classes.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <div class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                    <Icon name="lucide:school" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Belum ada Kelas</p>
                    <p class="text-sm text-muted">Mulai tambahkan ruangan kelas untuk operasional sekolah.</p>
                  </div>
                  <BaseButton 
                    variant="primary" 
                    size="md" 
                    class="mt-2 w-full"
                    @click="router.push({ name: 'ClassCreate' })"
                  >
                    Tambah Kelas Pertama
                  </BaseButton>
                </div>
              </td>
            </tr>
            <tr 
              v-else
              v-for="c in classes" 
              :key="c.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group"
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
                    class="p-1.5 text-muted hover:text-primary-600 transition-colors rounded-md hover:bg-primary-50"
                    title="Edit Kelas"
                    @click="router.push({ name: 'ClassEdit', params: { id: c.id } })"
                  >
                    <Icon name="lucide:pencil" class="w-4 h-4" />
                  </button>
                  <button 
                    class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50"
                    @click="handleDelete(c)"
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
  </div>
</template>
