<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    primaryInstrument: Object,
    nextConcert: Object,
    myParts: Array,
    recentStats: Object,
    allInstruments: Array,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatTime(date) {
    return new Date(date).toLocaleTimeString('nl-NL', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getDaysUntil(date) {
    const now = new Date();
    const target = new Date(date);
    const diff = Math.ceil((target - now) / (1000 * 60 * 60 * 24));
    return diff;
}
</script>

<template>
    <Head title="Mijn Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Mijn Muziekdashboard</h2>
                <p class="text-sm text-gray-600">Jouw voornaamste instrument en de komende concerten</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Primary Instrument Card -->
                <div v-if="primaryInstrument" class="mb-8 bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl p-8 text-white shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-semibold text-blue-200 mb-1">Mijn primaire instrument</div>
                            <h1 class="text-4xl font-bold mb-2">{{ primaryInstrument.name }}</h1>
                            <p class="text-blue-200">
                                Je speelt {{ allInstruments.length }} instrument{{ allInstruments.length !== 1 ? 'en' : '' }}
                            </p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>

                    <!-- Other instruments -->
                    <div v-if="allInstruments.length > 1" class="mt-6 pt-6 border-t border-blue-700">
                        <div class="text-sm font-semibold text-blue-200 mb-3">Andere instrumenten</div>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="instrument in allInstruments.filter(i => i.id !== primaryInstrument.id)"
                                :key="instrument.id"
                                class="bg-blue-700 text-white px-3 py-1 rounded-full text-sm"
                            >
                                {{ instrument.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Change Instruments Button -->
                    <div class="mt-6 pt-6 border-t border-blue-700">
                        <Link
                            :href="route('profile.edit')"
                            class="inline-flex items-center gap-2 bg-yellow-500 text-blue-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-400 transition-colors text-sm"
                        >
                            Wijzig mijn instrumenten
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Nieuw deze maand</div>
                                <div class="text-3xl font-bold text-blue-900">{{ recentStats.newScores }}</div>
                                <p class="text-gray-500 text-sm mt-1">nieuwe stukken toegevoegd</p>
                            </div>
                            <svg class="w-10 h-10 text-yellow-500 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Volgende concerten</div>
                                <div class="text-3xl font-bold text-blue-900">{{ recentStats.upcomingConcerts }}</div>
                                <p class="text-gray-500 text-sm mt-1">komende concerten</p>
                            </div>
                            <svg class="w-10 h-10 text-yellow-500 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Next Concert & My Parts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Next Concert Card -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Volgende concert</h2>
                        </div>

                        <div v-if="nextConcert" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 mb-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-blue-900 mb-2">{{ nextConcert.title }}</h3>
                                    <div class="space-y-1 text-blue-800">
                                        <p class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ formatDate(nextConcert.date) }}
                                        </p>
                                        <p v-if="nextConcert.location" class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ nextConcert.location }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-4xl font-bold text-blue-900">
                                        {{ getDaysUntil(nextConcert.date) }}
                                    </div>
                                    <div class="text-blue-600 text-sm font-medium">dag{{ getDaysUntil(nextConcert.date) !== 1 ? 'en' : '' }}</div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="bg-gray-50 rounded-xl p-6 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Geen toekomstige concerten gepland
                        </div>
                    </div>

                    <!-- My Parts Card -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            <h3 class="font-bold text-gray-900">Mijn partijen</h3>
                        </div>

                        <div v-if="myParts.length === 0" class="text-gray-400 text-sm text-center py-4">
                            <p v-if="nextConcert">Geen partijen voor jouw instrument</p>
                            <p v-else>Geen concerten gepland</p>
                        </div>

                        <div v-else class="space-y-2">
                            <div v-for="part in myParts" :key="part.part_id" class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="font-semibold text-gray-900 text-sm">{{ part.score_title }}</div>
                                <div class="text-gray-500 text-xs mb-2">{{ part.score_composer }}</div>
                                <Link
                                    :href="route('muziek.download', { score: part.score_id, part: part.part_id })"
                                    class="inline-flex items-center gap-1 bg-yellow-500 text-blue-900 text-xs font-bold px-3 py-1 rounded hover:bg-yellow-400 transition-colors"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download
                                </Link>
                            </div>
                        </div>

                        <Link
                            :href="route('muziek.index')"
                            class="mt-4 w-full block text-center bg-blue-900 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors"
                        >
                            Alle stukken →
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
