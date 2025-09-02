<script setup>
defineProps(['modelValue', 'type', 'id'])
defineEmits(['update:modelValue'])
</script>

<template>
  <input
    :id="id"
    :type="type"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    class="px-4 py-2 border rounded-xl w-full h-40 text-brandblue focus:ring-2 focus:ring-brandgold dark:bg-gray-800 dark:text-slate-100 dark:border-white"
  />
</template>
