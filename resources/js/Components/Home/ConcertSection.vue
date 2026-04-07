<script setup>
import ConcertCard from './ConcertCard.vue';

defineProps({
    concerts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <section id="concerten" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-slate-900 mb-2">Aankomende Concerten</h2>
            <p class="text-slate-600 mb-12">Beleef de magie van live muziek</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <ConcertCard v-for="concert in concerts" :key="concert.id" :concert="concert" />
            </div>

            <div v-if="concerts.length === 0" class="text-center py-12">
                <p class="text-slate-600">Momenteel geen concerten gepland. Kom binnenkort terug!</p>
            </div>
        </div>
    </section>
</template>
