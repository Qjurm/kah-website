<template>
  <div class="flex flex-col gap-1">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ title }}</h2>
    <p v-if="subtitle" class="text-sm text-gray-600">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: null,
  },
});
</script>
