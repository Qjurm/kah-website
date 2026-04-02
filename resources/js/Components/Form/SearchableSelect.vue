<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: null,
    },
    options: {
        type: Array,
        default: () => [],
        // Each option: { value, label } or string
    },
    placeholder: {
        type: String,
        default: 'Selecteer...',
    },
    searchPlaceholder: {
        type: String,
        default: 'Zoeken...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const search = ref('');
const highlighted = ref(0);
const container = ref(null);
const searchInput = ref(null);

const normalizedOptions = computed(() =>
    props.options.map((o) =>
        typeof o === 'object' ? o : { value: o, label: String(o) }
    )
);

const filtered = computed(() => {
    if (!search.value) return normalizedOptions.value;
    const q = search.value.toLowerCase();
    return normalizedOptions.value.filter((o) =>
        o.label.toLowerCase().includes(q)
    );
});

const selectedLabel = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
        return null;
    }
    const opt = normalizedOptions.value.find((o) => o.value == props.modelValue);
    return opt ? opt.label : null;
});

function toggle() {
    if (props.disabled) return;
    open.value = !open.value;
    if (open.value) {
        search.value = '';
        highlighted.value = 0;
        setTimeout(() => searchInput.value?.focus(), 50);
    }
}

function select(option) {
    emit('update:modelValue', option.value);
    open.value = false;
    search.value = '';
}

function clear(e) {
    e.stopPropagation();
    emit('update:modelValue', null);
}

function onKeydown(e) {
    if (!open.value) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
            e.preventDefault();
            toggle();
        }
        return;
    }
    if (e.key === 'Escape') {
        open.value = false;
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlighted.value = Math.min(highlighted.value + 1, filtered.value.length - 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlighted.value = Math.max(highlighted.value - 1, 0);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (filtered.value[highlighted.value]) {
            select(filtered.value[highlighted.value]);
        }
    }
}

watch(search, () => {
    highlighted.value = 0;
});

function onClickOutside(e) {
    if (container.value && !container.value.contains(e.target)) {
        open.value = false;
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));
</script>

<template>
    <div ref="container" class="relative" @keydown="onKeydown">
        <!-- Trigger -->
        <button
            type="button"
            :disabled="disabled"
            :class="[
                'w-full flex items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm transition-colors',
                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
                disabled
                    ? 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white border-gray-300 text-gray-900 hover:border-gray-400 cursor-pointer',
                open ? 'border-indigo-500 ring-2 ring-indigo-500' : '',
            ]"
            @click="toggle"
        >
            <span :class="selectedLabel ? 'text-gray-900' : 'text-gray-400'">
                {{ selectedLabel ?? placeholder }}
            </span>
            <span class="flex items-center gap-1 ml-2 flex-shrink-0">
                <button
                    v-if="clearable && selectedLabel"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 rounded"
                    @click="clear"
                    tabindex="-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <svg
                    :class="['w-4 h-4 text-gray-400 transition-transform', open ? 'rotate-180' : '']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </span>
        </button>

        <!-- Dropdown -->
        <div
            v-if="open"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden"
        >
            <!-- Search -->
            <div class="p-2 border-b border-gray-100">
                <input
                    ref="searchInput"
                    v-model="search"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-indigo-400"
                />
            </div>

            <!-- Options list -->
            <ul class="max-h-56 overflow-y-auto py-1" role="listbox">
                <li
                    v-if="filtered.length === 0"
                    class="px-3 py-2 text-sm text-gray-400 text-center"
                >
                    Geen resultaten
                </li>
                <li
                    v-for="(option, idx) in filtered"
                    :key="option.value"
                    role="option"
                    :aria-selected="option.value == modelValue"
                    :class="[
                        'px-3 py-2 text-sm cursor-pointer select-none flex items-center gap-2',
                        idx === highlighted ? 'bg-indigo-50 text-indigo-700' : 'text-gray-900 hover:bg-gray-50',
                        option.value == modelValue ? 'font-medium' : '',
                    ]"
                    @mouseenter="highlighted = idx"
                    @click="select(option)"
                >
                    <svg
                        v-if="option.value == modelValue"
                        class="w-4 h-4 text-indigo-600 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-else class="w-4 flex-shrink-0" />
                    {{ option.label }}
                </li>
            </ul>
        </div>
    </div>
</template>
