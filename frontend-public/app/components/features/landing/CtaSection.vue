<script setup lang="ts">
/**
 * CtaSection — Call-to-action section for the landing page.
 *
 * Encourages visitors to register or explore courses.
 * Fully static content. Uses design system tokens exclusively.
 */
const { isAuthenticated } = useAuth()
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-r from-primary-600 to-primary-700">
    <div class="container py-14 sm:py-20 text-center relative z-10">
      <h2 class="text-2xl sm:text-3xl font-bold text-white max-w-2xl mx-auto">
        Siap Memulai Perjalanan Pendidikan Anda?
      </h2>
      <p class="mt-4 text-base text-primary-100 max-w-lg mx-auto">
        Bergabung bersama ribuan pendidik dan orang tua yang telah meningkatkan kualitas pendidikan anak usia dini bersama PaudPedia.
      </p>
      <div class="mt-8 flex flex-wrap gap-3 justify-center">
        <template v-if="!isAuthenticated">
          <NuxtLink to="/auth/register">
            <UButton variant="secondary" size="lg">
              Daftar Gratis Sekarang
            </UButton>
          </NuxtLink>
          <NuxtLink to="/courses">
            <UButton variant="ghost" size="lg" class="!text-white hover:!bg-white/10">
              Jelajahi Kursus
            </UButton>
          </NuxtLink>
        </template>
        <template v-else>
          <NuxtLink to="/courses">
            <UButton variant="secondary" size="lg">
              Jelajahi Kursus
            </UButton>
          </NuxtLink>
          <NuxtLink to="/account">
            <UButton variant="ghost" size="lg" class="!text-white hover:!bg-white/10">
              Ke Dashboard
            </UButton>
          </NuxtLink>
        </template>
      </div>
    </div>

    <!-- Decorative circles -->
    <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-white/5" />
    <div class="absolute -bottom-16 -right-16 w-60 h-60 rounded-full bg-white/5" />
    <div class="absolute top-1/2 left-1/4 w-20 h-20 rounded-full bg-white/5" />
  </section>
</template>
