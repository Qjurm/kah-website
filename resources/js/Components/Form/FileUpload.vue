<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null,
    },
    accept: {
        type: String,
        default: 'image/*',
    },
    label: {
        type: String,
        default: 'Klik om een bestand te uploaden',
    },
    maxSizeMb: {
        type: Number,
        default: 10,
    },
    currentUrl: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'error']);

const dragging = ref(false);
const inputRef = ref(null);
const error = ref('');

const isImage = computed(() => {
    if (props.modelValue) {
        return props.modelValue.type.startsWith('image/');
    }
    if (props.currentUrl) {
        return /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(props.currentUrl);
    }
    return false;
});

const previewUrl = computed(() => {
    if (props.modelValue && props.modelValue.type.startsWith('image/')) {
        return URL.createObjectURL(props.modelValue);
    }
    return null;
});

const displayName = computed(() => {
    if (props.modelValue) return props.modelValue.name;
    if (props.currentUrl) {
        const parts = props.currentUrl.split('/');
        return parts[parts.length - 1];
    }
    return null;
});

function validate(file) {
    if (!file) return false;
    if (props.accept && props.accept !== '*') {
        const accepted = props.accept.split(',').map((a) => a.trim());
        const typeOk = accepted.some((a) => {
            if (a.endsWith('/*')) return file.type.startsWith(a.replace('/*', '/'));
            if (a.startsWith('.')) return file.name.toLowerCase().endsWith(a.toLowerCase());
            return file.type === a;
        });
        if (!typeOk) {
            error.value = `Bestandstype niet toegestaan. Toegestaan: ${props.accept}`;
            emit('error', error.value);
            return false;
        }
    }
    if (file.size > props.maxSizeMb * 1024 * 1024) {
        error.value = `Bestand te groot. Maximum: ${props.maxSizeMb} MB`;
        emit('error', error.value);
        return false;
    }
    error.value = '';
    return true;
}

function handleFile(file) {
    if (!file) return;
    if (validate(file)) {
        emit('update:modelValue', file);
    }
}

function onInput(e) {
    handleFile(e.target.files[0]);
}

function onDrop(e) {
    dragging.value = false;
    handleFile(e.dataTransfer.files[0]);
}

function remove() {
    emit('update:modelValue', null);
    error.value = '';
    if (inputRef.value) inputRef.value.value = '';
}

function openPicker() {
    inputRef.value?.click();
}
</script>

<template>
    <div class="space-y-2">
        <!-- Drop zone -->
        <div
            :class="[
                'relative border-2 border-dashed rounded-lg transition-colors cursor-pointer',
                dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 hover:border-gray-400 bg-gray-50 hover:bg-gray-100',
                error ? 'border-red-400 bg-red-50' : '',
            ]"
            @click="openPicker"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="onDrop"
        >
            <input
                ref="inputRef"
                type="file"
                :accept="accept"
                class="sr-only"
                @change="onInput"
            />

            <!-- Preview: image -->
            <div v-if="previewUrl || (currentUrl && isImage && !modelValue)" class="p-3">
                <div class="relative inline-block">
                    <img
                        :src="previewUrl ?? currentUrl"
                        alt="Preview"
                        class="max-h-48 rounded object-contain border border-gray-200"
                    />
                    <button
                        type="button"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 shadow"
                        @click.stop="remove"
                    >
                        &times;
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ displayName }}</p>
            </div>

            <!-- Preview: non-image file -->
            <div
                v-else-if="modelValue && !isImage"
                class="p-4 flex items-center gap-3"
            >
                <svg class="w-8 h-8 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ displayName }}</p>
                    <p class="text-xs text-gray-500">{{ (modelValue.size / 1024).toFixed(1) }} KB</p>
                </div>
                <button
                    type="button"
                    class="text-red-500 hover:text-red-700 flex-shrink-0"
                    @click.stop="remove"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Empty state -->
            <div v-else class="p-6 text-center">
                <svg class="mx-auto w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="text-sm text-gray-600">{{ label }}</p>
                <p class="text-xs text-gray-400 mt-1">of sleep een bestand hierheen</p>
                <p v-if="accept !== '*'" class="text-xs text-gray-400 mt-0.5">{{ accept }} &bull; max {{ maxSizeMb }} MB</p>
            </div>
        </div>

        <!-- Error -->
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </div>
</template>
