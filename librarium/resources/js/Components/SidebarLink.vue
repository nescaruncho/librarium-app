<template>
  <Link
    :href="href"
    class="flex items-center space-x-3 px-4 py-2 rounded-lg font-bold transition"
    :class="{
      'bg-white text-brandblue': isActive,
      'text-white hover:text-brandgold': !isActive,
      'dark:bg-white dark:text-black' : isActive
    }"
  >
    <component :is="iconComponent" class="w-6 h-6" :class="{ 'text-brandgold': true }" />
    <span>{{ label }}</span>
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Home, BookOpen, Library } from 'lucide-vue-next'

const props = defineProps({
  icon: String,
  label: String,
  href: String,
})

const iconMap = {
  home: Home,
  library: Library,
  book: BookOpen,
}

const iconComponent = computed(() => iconMap[props.icon] || Home)

const page = usePage()
const isActive = computed(() => page.url.startsWith(props.href))
</script>
