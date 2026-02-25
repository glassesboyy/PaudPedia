<script setup lang="ts">
const props = defineProps<{
  error: {
    statusCode: number
    message: string
  }
}>()

const handleError = () => clearError({ redirect: '/' })

const title = computed(() => {
  switch (props.error.statusCode) {
    case 404:
      return 'Halaman Tidak Ditemukan'
    case 403:
      return 'Akses Ditolak'
    case 500:
      return 'Terjadi Kesalahan'
    default:
      return 'Error'
  }
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-surface-muted">
    <div class="text-center px-4">
      <h1 class="text-7xl font-bold text-primary-600">
        {{ error.statusCode }}
      </h1>
      <h2 class="mt-4 text-2xl font-semibold text-heading">
        {{ title }}
      </h2>
      <p class="mt-2 text-body">
        {{ error.message || 'Terjadi kesalahan yang tidak terduga.' }}
      </p>
      <button
        class="mt-8 inline-flex items-center px-6 py-3 bg-primary-600 text-on-primary font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:ring-offset-2 transition-colors duration-200"
        @click="handleError"
      >
        Kembali ke Beranda
      </button>
    </div>
  </div>
</template>
