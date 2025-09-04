<script setup>
import { ref, onMounted, watch } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { Moon, Sun } from "lucide-vue-next";
import NotifBell from "./NotifBell.vue";

const props = defineProps({
  sidebarOpen: { type: Boolean, default: false }
})
const emit = defineEmits(['toggleSidebar'])

const isDark = ref(false);
function toggleTheme() {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle("dark", isDark.value);
}
onMounted(() => {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    isDark.value = true;
    document.documentElement.classList.add("dark");
  } else {
    isDark.value = false;
    document.documentElement.classList.remove("dark");
  }
});
watch(isDark, (val) => {
  localStorage.setItem("theme", val ? "dark" : "light");
});

const search = ref("");
const handleSearch = () => {
  if (search.value.trim()) {
    router.get(route("bibliotecas.search"), { query: search.value.trim() });
  }
};
const handleKeyPress = (e) => { if (e.key === "Enter") handleSearch(); };
</script>

<template>
  <header
    class="fixed top-0 left-0 lg:left-64 right-0 z-30 flex items-center justify-between px-4 sm:px-6 py-4 bg-white dark:bg-[#272727] shadow-md"
  >
    <div class="flex items-center gap-3">
      <button
        class="lg:hidden p-2 rounded-md ring-1 ring-[#142A47]/20 hover:bg-gray-100 dark:hover:bg-gray-700"
        @click="emit('toggleSidebar')"
        aria-label="Abrir menú"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <button
        @click="toggleTheme"
        class="relative w-12 h-6 rounded-full px-1 flex items-center transition bg-gray-200 dark:bg-gray-600 ring-1 ring-[#142A47]/30 focus:outline-none focus-visible:ring-2"
        :aria-pressed="isDark"
        :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
      >
        <div
          class="w-5 h-5 flex items-center justify-center rounded-full shadow bg-[#142A47] dark:bg-white dark:ring dark:ring-[#142A47]/40 transform transition-transform duration-300"
          :class="{ 'translate-x-6': isDark }"
        >
          <Sun v-if="!isDark" class="w-3 h-3 text-yellow-400" />
          <Moon v-else class="w-3 h-3 text-[#142A47]" />
        </div>
      </button>
    </div>

    <div class="flex-1 mx-3 sm:mx-6 relative">
      <input
        type="text"
        placeholder="Buscar..."
        v-model="search"
        @keyup="handleKeyPress"
        class="w-full h-10 pl-4 pr-10 rounded-full border border-gray-300 shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
      />
      <button @click="handleSearch" class="absolute right-3 top-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-bluebrand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.5 10a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
        </svg>
      </button>
    </div>

    <div class="flex items-center space-x-6 text-[#142A47] dark:text-white">
      <NotifBell />
      <Link href="/profile" class="hover:text-yellow-500 transition" aria-label="Perfil">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2a5 5 0 015 5v1a5 5 0 01-10 0V7a5 5 0 015-5zm0 12c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/>
        </svg>
      </Link>
      <Link href="/logout" method="post" as="button" class="hover:text-red-500 transition" aria-label="Cerrar sesión">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M16 13v-2H7V8l-5 4 5 4v-3h9zM20 3h-8v2h8v14h-8v2h8a2 2 0 002-2V5a2 2 0 00-2-2z"/>
        </svg>
      </Link>
    </div>
  </header>
</template>
