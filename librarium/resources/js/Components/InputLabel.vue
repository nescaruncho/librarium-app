<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-sans font-medium text-brandblue dark:text-white">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
