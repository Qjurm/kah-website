<template>
  <div :class="['rounded-2xl p-6', bgClass]">
    <div class="flex items-center gap-4">
      <div class="flex-1">
        <div class="text-sm font-semibold uppercase tracking-wider opacity-90">
          {{ title }}
        </div>
        <div class="text-2xl font-bold mt-1">{{ heading }}</div>
        <div class="text-sm mt-2 opacity-80">{{ subtitle }}</div>
      </div>
      <slot name="icon" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  heading: String,
  subtitle: String,
  bgClass: {
    type: String,
    default: 'bg-blue-900 text-white',
  },
});
</script>
