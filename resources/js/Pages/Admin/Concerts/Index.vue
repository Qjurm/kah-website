<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionHeader from '@/Components/Dashboard/SectionHeader.vue';
import TipsCard from '@/Components/Dashboard/TipsCard.vue';

defineProps({
    concerts: Object,
});

function destroy(concert) {
    if (confirm(`Concert "${concert.title}" verwijderen?`)) {
        router.delete(route('beheer.concerten.destroy', concert.id));
    }
}

function toggleCurrent(concert) {
    router.put(route('beheer.concerten.update', concert.id), {
        title: concert.title,
        date: concert.date,
        location: concert.location,
        is_current: !concert.is_current,
        score_ids: [],
    }, { preserveScroll: true });
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>

<template>
    <Head title="Concerten beheren" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Concerten beheren</h2>
                    <p class="text-sm text-gray-600">Voeg concerten toe, koppel stukken, en beheer datums</p>
                </div>
                <Link :href="route('beheer.concerten.create')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors whitespace-nowrap">
                    + Nieuw concert
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section header -->
                <SectionHeader 
                    title="Concerten beheren"
                    subtitle="Voeg concerten toe, koppel stukken, en beheer datums"
                />

                <!-- Current Concert Banner -->
                <div v-if="concerts.data.some(c => c.is_current)" class="mb-8 bg-yellow-50 border-l-4 border-yellow-600 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Huidig concert</h3>
                    <div v-for="concert in concerts.data.filter(c => c.is_current)" :key="concert.id" class="text-yellow-900">
                        <p class="font-bold">{{ concert.title }}</p>
                        <p class="text-sm">{{ formatDate(concert.date) }} · {{ concert.location }}</p>
                    </div>
                </div>

                <!-- Concerts Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locatie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="concert in concerts.data" :key="concert.id" class="hover:bg-gray-50 transition-colors" :class="{'bg-yellow-50': concert.is_current}">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ concert.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(concert.date) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ concert.location || '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button
                                        @click="toggleCurrent(concert)"
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold transition-colors"
                                        :class="concert.is_current
                                            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                    >
                                        <span v-if="concert.is_current">🎯 Huidig</span>
                                        <span v-else>Instellen</span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <Link :href="route('beheer.concerten.edit', concert.id)" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                        Bewerken
                                    </Link>
                                    <button @click="destroy(concert)" class="text-red-600 hover:text-red-900 font-medium text-sm">
                                        Verwijderen
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!concerts.data.length">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Nog geen concerten toegevoegd.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Helper Card -->
                <div class="mt-8">
                    <TipsCard>
                        <div class="space-y-1">
                            <div>Klik '🎯 Huidig' om aan te geven welk concert volgende is — muzikanten zien dit prominent</div>
                            <div>Klik 'Bewerken' om stukken aan het concert toe te voegen</div>
                            <div>Voeg altijd een datum in zodat muzikanten weten wanneer het concert is</div>
                            <div>Muzikanten kunnen hun partijen voor het huidige concert downloaden op hun dashboard</div>
                        </div>
                    </TipsCard>
                </div>

                <div class="mt-8">
                    <Link :href="route('beheer.dashboard')" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">
                        ← Terug naar dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
