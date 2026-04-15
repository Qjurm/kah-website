<script setup>
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    user: { type: Object, required: true }
});
</script>

<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-700/60
                       bg-blue-900 px-3 py-1.5 text-sm font-medium text-blue-100
                       hover:bg-blue-800 hover:text-white transition-all duration-200
                       active:scale-95 focus:outline-none"
            >
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                             bg-yellow-400 text-[10px] font-bold text-blue-950 shrink-0">
                    {{ user.name.charAt(0).toUpperCase() }}
                </span>
                <span class="max-w-[100px] truncate">{{ user.name }}</span>
                <svg class="h-3.5 w-3.5 text-blue-400 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </template>
        <template #content>
            <div class="px-4 py-2 border-b border-gray-100 mb-1">
                <p class="text-xs text-gray-400 truncate">{{ user.email }}</p>
            </div>
            <DropdownLink :href="route('profile.edit')">Mijn Profiel</DropdownLink>
            <DropdownLink :href="route('logout')" method="post" as="button">
                Uitloggen
            </DropdownLink>
        </template>
    </Dropdown>
</template>
