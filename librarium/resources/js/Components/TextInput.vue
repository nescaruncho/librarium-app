<script setup>
import { ref, onMounted, defineExpose } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  id: { type: String, default: undefined },
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) input.value.focus()
})

defineExpose({
  focus() {
    input.value?.focus()
  },
  select() {
    input.value?.select()
  },
})
</script>

<template>
  <input
    ref="input"
    :id="id"
    :type="type"
    :value="modelValue"
    @input="emit('update:modelValue', $event.target.value)"
    class="mt-2 mb-4 border rounded-full text-brandblue focus:ring-2 focus:ring-brandgold dark:border-white dark:bg-gray-800 dark:text-slate-100"
    v-bind="$attrs"
  />
</template>
