<template>
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-blue-900">{{ title }}</h2>
    <p class="text-gray-600 text-sm mt-1">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
});
</script>
