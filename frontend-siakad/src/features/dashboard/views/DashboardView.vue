<script setup lang="ts">
import { computed } from 'vue'
import { useSchoolStore } from '@/stores/school.store'
import HeadmasterDashboard from './HeadmasterDashboard.vue'
import TeacherDashboard from './TeacherDashboard.vue'
import ParentDashboard from './ParentDashboard.vue'

const schoolStore = useSchoolStore()

const currentRole = computed(() => schoolStore.currentRole)
</script>

<template>
  <div>
    <HeadmasterDashboard v-if="currentRole === 'headmaster'" />
    <TeacherDashboard v-else-if="currentRole === 'teacher'" />
    <ParentDashboard v-else-if="currentRole === 'parent'" />
    <div v-else class="text-center py-20">
      <h2 class="text-xl font-semibold text-heading">Akses Tidak Dikenali</h2>
      <p class="text-muted">Role Anda tidak memiliki akses dashboard di sekolah ini.</p>
    </div>
  </div>
</template>
