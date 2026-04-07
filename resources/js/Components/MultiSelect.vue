<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
        </label>
        <div class="relative">
            <button
                @click="isOpen = !isOpen"
                type="button"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <span v-if="!modelValue || modelValue.length === 0" class="text-gray-500">
                    Selecteer instrumenten...
                </span>
                <span v-else class="text-gray-900">
                    {{ modelValue.length }} instrument(en) geselecteerd
                </span>
            </button>

            <div v-if="isOpen" class="absolute z-10 w-full mt-1 border border-gray-300 rounded-lg bg-white shadow-lg">
                <div class="max-h-60 overflow-y-auto p-2">
                    <label
                        v-for="option in options"
                        :key="option.id"
                        class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            :checked="modelValue?.includes(option.id)"
                            @change="handleSelect(option.id)"
                            class="rounded"
                        />
                        <span class="text-gray-900">{{ option.name }}</span>
                    </label>
                </div>
            </div>
        </div>
        <span v-if="error" class="text-red-500 text-sm mt-2 block">{{ error }}</span>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    modelValue: Array,
    label: String,
    options: Array,
    error: String,
});

defineEmits(['update:modelValue']);

const isOpen = ref(false);

const handleSelect = (optionId) => {
    const newValue = modelValue || [];
    if (newValue.includes(optionId)) {
        // Remove
        emit('update:modelValue', newValue.filter(id => id !== optionId));
    } else {
        // Add
        emit('update:modelValue', [...newValue, optionId]);
    }
};
</script>
