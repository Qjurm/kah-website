<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div v-if="$slots.header || title" class="flex items-center justify-between mb-6 border-b pb-4">
            <h3 v-if="title" class="text-lg font-medium text-gray-900">{{ title }}</h3>
            <div v-if="$slots.header">
                <slot name="header"></slot>
            </div>
        </div>
        <slot></slot>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: null
    }
});
</script>
