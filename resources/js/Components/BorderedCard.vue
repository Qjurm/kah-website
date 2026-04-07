<template>
  <div :class="['rounded-xl border-l-4 p-6 bg-white', borderColorClass]">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

defineProps({
  borderColor: {
    type: String,
    default: 'blue', // 'blue', 'yellow', 'green', 'red', 'orange'
  },
});

const borderColorClass = computed(() => {
  const colors = {
    blue: 'border-l-blue-600',
    yellow: 'border-l-yellow-600',
    green: 'border-l-green-600',
    red: 'border-l-red-600',
    orange: 'border-l-orange-600',
  };
  return colors[borderColor] || colors.blue;
});
</script>
