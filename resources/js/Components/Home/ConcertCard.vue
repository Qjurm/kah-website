<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    concert: {
        type: Object,
        required: true,
    },
});

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('nl-NL', {
        weekday: 'short',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
</script>

<template>
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
        <!-- Image -->
        <div v-if="concert.photo_path" class="h-48 bg-slate-200 overflow-hidden">
            <img :src="`/storage/${concert.photo_path}`" :alt="concert.title" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
        </div>
        <div v-else class="h-48 bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center">
            <span class="text-slate-600 text-sm font-medium">Geen foto beschikbaar</span>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="mb-3">
                <span class="inline-block bg-yellow-500 text-slate-900 px-3 py-1 rounded-full text-xs font-bold">
                    {{ formatDate(concert.date) }}
                </span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ concert.title }}</h3>
            <p class="text-slate-600 text-sm mb-4">📍 {{ concert.location }}</p>
            <p v-if="concert.description" class="text-slate-600 text-sm mb-4 line-clamp-2">{{ concert.description }}</p>
            <Link
                :href="`/muziek?concert=${concert.id}`"
                class="inline-block bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800 transition-colors"
            >
                Meer informatie →
            </Link>
        </div>
    </div>
</template>
