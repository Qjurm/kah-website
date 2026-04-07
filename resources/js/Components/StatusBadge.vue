<template>
  <span
    :class="[
      'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
      statusClass,
    ]"
  >
    {{ statusLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true, // 'pending', 'approved', 'rejected'
  },
});

const statusClass = computed(() => {
  const classes = {
    pending: 'bg-red-100 text-red-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-gray-100 text-gray-800',
  };
  return classes[props.status] || classes.pending;
});

const statusLabel = computed(() => {
  const labels = {
    pending: 'Afwachtend',
    approved: 'Goedgekeurd',
    rejected: 'Geweigerd',
  };
  return labels[props.status] || 'Onbekend';
});
</script>
