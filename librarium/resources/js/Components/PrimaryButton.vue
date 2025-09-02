<script setup>
defineProps(['type'])
</script>

<template>
  <button
    :type="type ?? 'submit'"
    class="bg-brandgold text-white px-6 py-2 rounded-full font-bold hover:bg-yellow-500 transition"
  >
    <slot />
  </button>
</template>
