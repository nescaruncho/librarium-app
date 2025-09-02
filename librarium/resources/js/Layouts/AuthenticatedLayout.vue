<template>
  <div class="flex min-h-screen bg-white dark:bg-gray-900" :class="{'overflow-hidden': sidebarOpen}">
    <!-- Sidebar (drawer en móvil) -->
    <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Overlay para móvil -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/40 z-40 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Contenido -->
    <div class="flex-1 ml-0 lg:ml-64 dark:bg-black">
      <Header :sidebar-open="sidebarOpen" @toggleSidebar="sidebarOpen = !sidebarOpen" />
      <div class="h-20"></div>
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import Header from '@/Components/Header.vue'

const sidebarOpen = ref(false)
</script>
