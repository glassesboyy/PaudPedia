<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { teacherService } from '@/features/teachers/services/teacher.service'
import type { Teacher } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const router = useRouter()
const schoolStore = useSchoolStore()

const isLoading = ref(false)
const teachers = ref<Teacher[]>([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })

onMounted(() => {
  fetchTeachers()
})

async function fetchTeachers(page = 1) {
  if (!schoolStore.currentSchoolId) return
  
  isLoading.value = true
  try {
    const response = await teacherService.getTeachers(schoolStore.currentSchoolId, { page })
    teachers.value = response.data.teachers
    meta.value = response.data.meta
  } catch (error) {
    console.error('Gagal mengambil data guru:', error)
  } finally {
    isLoading.value = false
  }
}

async function handleDelete(teacher: Teacher) {
  if (!confirm(`Apakah Anda yakin ingin menghapus ${teacher.name} dari sekolah ini?`)) return

  try {
    await teacherService.deleteTeacher(schoolStore.currentSchoolId!, teacher.id)
    await fetchTeachers()
  } catch (error) {
    alert('Gagal menghapus guru.')
  }
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">Daftar Guru</h1>
        <p class="text-muted">Kelola tim pendidik di sekolah Anda</p>
      </div>
      <BaseButton @click="router.push({ name: 'TeacherCreate' })">
        <template #prepend>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Tambah Guru Baru
      </BaseButton>
    </div>

    <!-- Table Card -->
    <BaseCard class="overflow-hidden border-none shadow-xl shadow-primary-900/5">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-muted/50 border-b border-border/50">
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Informasi Guru</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">NIP</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Spesialisasi</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest">Terdaftar Pada</th>
              <th class="px-8 py-5 text-[10px] font-extrabold text-muted uppercase tracking-widest text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 bg-white">
            <!-- Loading State -->
            <tr v-if="isLoading">
              <td colspan="5" class="px-8 py-20 text-center">
                <Loader text="Memuat data guru..." minHeight="300px" />
              </td>
            </tr>
            <tr v-else-if="teachers.length === 0">
              <td colspan="5" class="px-8 py-20 text-center">
                <div class="flex flex-col items-center gap-4 max-w-xs mx-auto">
                  <div class="w-20 h-20 bg-surface-muted rounded-2xl flex items-center justify-center text-muted border-2 border-dashed border-border">
                      <Icon name="lucide:graduation-cap" class="w-10 h-10" stroke-width="1.5" />
                  </div>
                  <div>
                    <p class="text-lg font-bold text-heading">Belum ada Guru</p>
                    <p class="text-sm text-muted">Mulai tambahkan tenaga pengajar untuk mengelola sekolah Anda.</p>
                  </div>
                  <BaseButton 
                    variant="primary" 
                    size="md" 
                    class="mt-2 w-full"
                    @click="router.push({ name: 'TeacherCreate' })"
                  >
                    Tambah Guru Pertama
                  </BaseButton>
                </div>
              </td>
            </tr>
            <tr 
              v-for="teacher in teachers" 
              :key="teacher.id"
              class="hover:bg-primary-50/30 transition-all duration-300 group"
            >
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-primary-600 flex items-center justify-center text-white font-bold overflow-hidden shadow-lg shadow-primary-600/20 group-hover:scale-110 transition-transform">
                    <img v-if="teacher.avatar_url" :src="teacher.avatar_url" class="w-full h-full object-cover" />
                    <span v-else class="text-lg">{{ teacher.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-heading group-hover:text-primary-700 transition-colors">{{ teacher.name }}</p>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-tight">{{ teacher.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-muted rounded-lg border border-border/50">
                  <span class="text-xs font-bold text-heading">{{ teacher.nip || 'TIDAK ADA NIP' }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span v-if="teacher.specialization" class="badge bg-primary-50 text-primary-700 ring-primary-600/10">
                  {{ teacher.specialization }}
                </span>
                <span v-else class="text-xs text-muted font-medium italic">General</span>
              </td>
              <td class="px-8 py-5 text-sm font-bold text-muted">
                {{ formatDate(teacher.joined_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                  <button 
                    class="p-1.5 text-muted hover:text-danger-600 transition-colors rounded-md hover:bg-danger-50"
                    @click="handleDelete(teacher)"
                    title="Hapus Guru"
                  >
                      <Icon name="lucide:trash-2" class="w-4 h-4" />
                  </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="px-6 py-4 bg-muted/30 border-t border-border-muted flex items-center justify-between">
        <p class="text-xs text-muted">
          Menampilkan {{ teachers.length }} dari {{ meta.total }} guru
        </p>
        <div class="flex gap-2">
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === 1"
            @click="fetchTeachers(meta.current_page - 1)"
          >
            Sebelumnya
          </BaseButton>
          <BaseButton 
            variant="outline" 
            size="sm" 
            :disabled="meta.current_page === meta.last_page"
            @click="fetchTeachers(meta.current_page + 1)"
          >
            Selanjutnya
          </BaseButton>
        </div>
      </div>
    </BaseCard>
  </div>
</template>
