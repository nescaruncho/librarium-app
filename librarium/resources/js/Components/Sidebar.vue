<script setup>
import { Link } from '@inertiajs/vue3'
import SidebarLink from './SidebarLink.vue'
import SidebarLogo from '@/Components/SidebarLogo.vue'
import SidebarLogoDark from './SidebarLogoDark.vue'
import { ref, onMounted } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false }
})
const emit = defineEmits(['close'])

const isDark = ref(false)
onMounted(() => {
  const root = document.documentElement
  isDark.value = root.classList.contains('dark')
  const observer = new MutationObserver(() => {
    isDark.value = root.classList.contains('dark')
  })
  observer.observe(root, { attributes: true, attributeFilter: ['class'] })
})
</script>

<template>
  <aside
    :class="[
      open ? 'translate-x-0' : '-translate-x-full',
      'fixed top-0 left-0 h-screen w-64 bg-brandblue text-white dark:bg-[#272727] z-50',
      'transition-transform duration-300 ease-in-out',
      'flex flex-col justify-between py-8 px-6',
      'lg:translate-x-0 lg:flex'
    ]"
    role="dialog"
    aria-label="Navegación lateral"
  >
    <div class="flex flex-col items-center space-y-8">
      <component :is="isDark ? SidebarLogoDark : SidebarLogo" class="w-32 h-32" />

      <button
        class="self-end lg:hidden rounded-md px-3 py-1 text-sm bg-white/10 hover:bg-white/20"
        @click="emit('close')"
      >
        Cerrar
      </button>

      <nav class="space-y-6 w-full">
        <SidebarLink icon="home" label="Inicio" href="/dashboard" @click="emit('close')" />
        <SidebarLink icon="library" label="Bibliotecas" href="/bibliotecas" @click="emit('close')" />
        <SidebarLink icon="book" label="Lecturas" href="/lecturas" @click="emit('close')" />
      </nav>
    </div>

    <div class="space-y-4 text-sm font-regular text-white/90">
      <ul>
        <li class="mb-2"><Link href="/aviso" class="hover:underline" @click="emit('close')">Aviso legal</Link></li>
        <li class="mb-2"><Link href="/privacidad" class="hover:underline" @click="emit('close')">Política de privacidad</Link></li>
        <li><Link href="/cookies" class="hover:underline" @click="emit('close')">Política de cookies</Link></li>
      </ul>
    </div>
  </aside>
</template>
