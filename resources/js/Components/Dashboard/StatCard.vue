<template>
  <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-all">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-3xl font-bold text-blue-900">{{ stat }}</div>
        <div class="text-gray-600 text-sm mt-1">{{ label }}</div>
      </div>
      <div class="text-blue-500">
        <slot name="icon" />
      </div>
    </div>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  stat: [String, Number],
  label: String,
});
</script>
