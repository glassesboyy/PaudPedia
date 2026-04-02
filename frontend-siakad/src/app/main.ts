import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import App from './App.vue'
import router from './router'
import BaseIcon from '@/components/ui/Icon/Icon.vue'
import BaseSkeleton from '@/components/ui/Skeleton/Skeleton.vue'
import BaseLoader from '@/components/ui/Loader/Loader.vue'

import '@/assets/styles/main.css'

const app = createApp(App)

// Global component registration
app.component('Icon', BaseIcon)
app.component('Skeleton', BaseSkeleton)
app.component('Loader', BaseLoader)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(router)

app.mount('#app')
