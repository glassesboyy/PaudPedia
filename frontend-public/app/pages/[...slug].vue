<script setup lang="ts">
/**
 * 404 Catch-All Page
 * Route: /*
 */
throw createError({
  statusCode: 404,
  message: 'Halaman yang Anda cari tidak ditemukan.',
})
</script>

<template>
  <div />
</template>
