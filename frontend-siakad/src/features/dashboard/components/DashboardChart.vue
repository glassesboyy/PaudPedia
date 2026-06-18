<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps<{
  labels: string[]
  spp: number[]
  tabungan: number[]
  total: number[]
  start: string
  end: string
}>()

const emit = defineEmits<{
  (e: 'update:start', val: string): void
  (e: 'update:end', val: string): void
}>()

const localStart = ref(props.start)
const localEnd = ref(props.end)

const maxEndMonth = computed(() => {
  if (!localStart.value) return ''
  const d = new Date(localStart.value + '-01')
  d.setMonth(d.getMonth() + 11)
  const m = (d.getMonth() + 1).toString().padStart(2, '0')
  return `${d.getFullYear()}-${m}`
})

watch([localStart, localEnd], ([newStart, newEnd]) => {
  if (newStart && newEnd) {
    const dStart = new Date(newStart + '-01')
    const dEnd = new Date(newEnd + '-01')
    
    if (dStart > dEnd) {
      localEnd.value = newStart
    } else {
      const diffMonths = (dEnd.getFullYear() - dStart.getFullYear()) * 12 + (dEnd.getMonth() - dStart.getMonth())
      if (diffMonths > 11) {
        localEnd.value = maxEndMonth.value
      }
    }
  }
})

watch(localStart, (val) => {
  if (val) emit('update:start', val)
})
watch(localEnd, (val) => {
  if (val) emit('update:end', val)
})

const activeFilters = ref({
  spp: true,
  tabungan: true,
  total: false,
})

function toggleFilter(key: 'spp' | 'tabungan' | 'total') {
  activeFilters.value[key] = !activeFilters.value[key]
}

const chartData = computed(() => {
  const datasets = []
  
  if (activeFilters.value.spp) {
    datasets.push({
      label: 'SPP Terkumpul',
      backgroundColor: '#0ea5e9', // sky-500
      data: props.spp,
      borderRadius: 6,
      barPercentage: 0.7,
    })
  }
  
  if (activeFilters.value.tabungan) {
    datasets.push({
      label: 'Tabungan Masuk',
      backgroundColor: '#10b981', // emerald-500
      data: props.tabungan,
      borderRadius: 6,
      barPercentage: 0.7,
    })
  }
  
  if (activeFilters.value.total) {
    datasets.push({
      label: 'Total Pemasukan',
      backgroundColor: '#8b5cf6', // violet-500
      data: props.total,
      borderRadius: 6,
      barPercentage: 0.7,
    })
  }

  return {
    labels: props.labels.map(l => {
      // l is "YYYY-MM"
      const d = new Date(l + '-01')
      return d.toLocaleString('id-ID', { month: 'short', year: 'numeric' })
    }),
    datasets
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false // We use our custom UI
    },
    tooltip: {
      mode: 'index' as const,
      intersect: false,
      backgroundColor: 'rgba(15, 23, 42, 0.9)', // slate-900
      titleFont: { size: 13, family: "'Inter', sans-serif" },
      bodyFont: { size: 12, family: "'Inter', sans-serif" },
      padding: 12,
      cornerRadius: 8,
      callbacks: {
        label: function(context: any) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
          }
          return label;
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
      ticks: {
        font: { family: "'Inter', sans-serif", size: 11 },
        color: '#64748b' // slate-500
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: '#f1f5f9', // slate-100
        drawBorder: false,
      },
      ticks: {
        font: { family: "'Inter', sans-serif", size: 11 },
        color: '#94a3b8', // slate-400
        callback: function(value: any) {
          if (value >= 1000000) {
            return (value / 1000000) + ' Jt';
          }
          if (value >= 1000) {
            return (value / 1000) + ' rb';
          }
          return value;
        }
      }
    }
  }
}
</script>

<template>
  <div class="flex flex-col h-full w-full">
    <!-- Chart Header & Filters -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">
      
      <!-- Custom Legend / Toggles -->
      <div class="flex flex-wrap items-center gap-2">
        <button 
          @click="toggleFilter('spp')"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border flex items-center gap-2',
            activeFilters.spp ? 'bg-sky-50 border-sky-200 text-sky-700' : 'bg-white border-slate-200 text-slate-400 hover:bg-slate-50'
          ]"
        >
          <div :class="['w-2.5 h-2.5 rounded-full', activeFilters.spp ? 'bg-sky-500' : 'bg-slate-300']"></div>
          SPP Terkumpul
        </button>

        <button 
          @click="toggleFilter('tabungan')"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border flex items-center gap-2',
            activeFilters.tabungan ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white border-slate-200 text-slate-400 hover:bg-slate-50'
          ]"
        >
          <div :class="['w-2.5 h-2.5 rounded-full', activeFilters.tabungan ? 'bg-emerald-500' : 'bg-slate-300']"></div>
          Tabungan Masuk
        </button>

        <button 
          @click="toggleFilter('total')"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border flex items-center gap-2',
            activeFilters.total ? 'bg-violet-50 border-violet-200 text-violet-700' : 'bg-white border-slate-200 text-slate-400 hover:bg-slate-50'
          ]"
        >
          <div :class="['w-2.5 h-2.5 rounded-full', activeFilters.total ? 'bg-violet-500' : 'bg-slate-300']"></div>
          Total Pemasukan
        </button>
      </div>

      <!-- Time Range Selector (Month pickers) -->
      <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs font-bold text-slate-400">Dari:</span>
        <input 
          type="month" 
          v-model="localStart" 
          :max="localEnd"
          class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-200 cursor-pointer"
        />
        
        <span class="text-xs font-bold text-slate-400">Hingga:</span>
        <input 
          type="month" 
          v-model="localEnd" 
          :min="localStart"
          :max="maxEndMonth"
          class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-200 cursor-pointer"
        />
      </div>
    </div>

    <!-- The Chart itself -->
    <div class="flex-1 min-h-[300px] w-full relative">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>
