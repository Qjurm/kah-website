<template>
  <div class="space-y-4">
    <div v-if="title" class="border-l-4 border-l-blue-600 pl-4">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <p v-if="description" class="text-sm text-gray-600 mt-1">{{ description }}</p>
    </div>
    <div :class="['p-6 bg-gray-50 rounded-xl', { 'border border-gray-200': hasBorder }]">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: null,
  },
  description: {
    type: String,
    default: null,
  },
  hasBorder: {
    type: Boolean,
    default: true,
  },
});
</script>
