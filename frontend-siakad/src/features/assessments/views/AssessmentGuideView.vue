<script setup lang="ts">
import { ref } from 'vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Icon from '@/components/ui/Icon/Icon.vue'

const expandedScales = ref<Set<string>>(new Set(['BB', 'MB', 'BSH', 'BSB'])) // default open all

const guideData = {
  BB: {
    title: 'Belum Berkembang',
    description: `1. Anak masih dalam bimbingan sehingga diberi contoh oleh pendidik.\n2. Anak belum menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya.`
  },
  MB: {
    title: 'Mulai Berkembang',
    description: `1. Anak masih harus diingatkan atau dibantu oleh pendidik.\n2. Anak sudah mulai menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya.`
  },
  BSH: {
    title: 'Berkembang Sesuai dengan Harapan',
    description: `1. Anak sudah dapat melakukannya secara mandiri dan konsisten tanpa harus diingatkan atau dicontohkan pendidik.\n2. Anak sudah menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya.`
  },
  BSB: {
    title: 'Berkembang Sangat Baik',
    description: `1. Anak sudah dapat melakukannya secara mandiri dan sudah dapat membantu temannya yang belum mencapai kemampuan sesuai dengan indikator yang diharapkan.\n2. Anak sudah menunjukkan kemampuan di atas indikator yang ditetapkan dalam kelompok usianya.`
  }
}

function toggleScale(scale: string) {
  const newSet = new Set(expandedScales.value)
  if (newSet.has(scale)) {
    newSet.delete(scale)
  } else {
    newSet.add(scale)
  }
  expandedScales.value = newSet
}
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Panduan Kriteria Penilaian</h1>
        <p class="text-sm text-slate-500 mt-1">Definisi dan deskripsi skala perkembangan anak usia dini.</p>
      </div>
    </div>

    <div class="flex flex-col gap-6">
      <BaseCard v-for="(info, scale) in guideData" :key="scale" class="p-0 overflow-hidden group hover:shadow-lg transition-all duration-300 border-0 ring-1 ring-slate-200 hover:ring-primary-300 bg-white">
        <!-- Accordion Header -->
        <button @click="toggleScale(scale)" class="w-full flex items-center justify-between gap-4 px-6 py-5 border-b focus:outline-none transition-colors duration-300 text-left" :class="[
          scale === 'BB' ? 'bg-rose-50/50 hover:bg-rose-50/80 border-rose-100' :
          scale === 'MB' ? 'bg-amber-50/50 hover:bg-amber-50/80 border-amber-100' :
          scale === 'BSH' ? 'bg-emerald-50/50 hover:bg-emerald-50/80 border-emerald-100' :
          'bg-primary-50/50 hover:bg-primary-50/80 border-primary-100'
        ]">
          <div class="flex items-center gap-4 flex-1">
            <div :class="[
              'inline-flex items-center justify-center px-3 py-1 rounded-lg text-sm font-bold border shrink-0 shadow-sm',
              scale === 'BB' ? 'bg-rose-100 text-rose-700 border-rose-200' :
              scale === 'MB' ? 'bg-amber-100 text-amber-700 border-amber-200' :
              scale === 'BSH' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' :
              'bg-primary-100 text-primary-700 border-primary-200'
            ]">
              {{ scale }}
            </div>
            <div>
              <h3 :class="[
                'font-bold text-lg leading-tight',
                scale === 'BB' ? 'text-rose-900' :
                scale === 'MB' ? 'text-amber-900' :
                scale === 'BSH' ? 'text-emerald-900' :
                'text-primary-900'
              ]">
                {{ info.title }}
              </h3>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <div :class="[
              'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm border bg-white hidden sm:flex',
              scale === 'BB' ? 'text-rose-500 border-rose-100' :
              scale === 'MB' ? 'text-amber-500 border-amber-100' :
              scale === 'BSH' ? 'text-emerald-500 border-emerald-100' :
              'text-primary-500 border-primary-100'
            ]">
              <Icon :name="
                scale === 'BB' ? 'lucide:trending-down' : 
                scale === 'MB' ? 'lucide:trending-up' : 
                scale === 'BSH' ? 'lucide:check-circle' : 
                'lucide:award'
              " class="w-5 h-5" />
            </div>
            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300 shrink-0" :class="expandedScales.has(scale) ? 'rotate-180' : ''">
              <Icon name="lucide:chevron-down" class="w-5 h-5" />
            </div>
          </div>
        </button>
        
        <!-- Card Body -->
        <div v-show="expandedScales.has(scale)" class="p-6 bg-white animate-fade-in border-t border-slate-100">
          <div class="text-slate-600 text-sm leading-relaxed space-y-3">
            <template v-for="(line, idx) in info.description.split('\n')" :key="idx">
              <div v-if="line.trim()" class="flex items-start gap-3">
                <div class="mt-1 w-1.5 h-1.5 rounded-full shrink-0 bg-slate-300"></div>
                <p>{{ line.replace(/^\d+\.\s*/, '') }}</p>
              </div>
            </template>
          </div>
        </div>
      </BaseCard>
    </div>
  </div>
</template>
