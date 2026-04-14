<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionHeader from '@/Components/Dashboard/SectionHeader.vue';
import TipsCard from '@/Components/Dashboard/TipsCard.vue';
import InfoBanner from '@/Components/Dashboard/InfoBanner.vue';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import ActionGrid from '@/Components/Dashboard/ActionGrid.vue';

defineProps({
    stats: Object,
    pendingUsers: Array,
    recentScores: Array,
    upcomingConcerts: Array,
    instrumentBreakdown: Object,
});


function formatDate(date) {
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Admin Dashboard</h2>
                <p class="text-sm text-gray-600">Beheer alle stukken, concerten en gebruikers</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section header -->
                <SectionHeader
                    title="Admin Dashboard"
                    subtitle="Beheer alle aspecten van je orkest"
                />

                <!-- Tips card -->
                <TipsCard>
                    Hier vind je alle tools om stukken, concerten en leden te beheren.
                </TipsCard>

                <!-- Current concert banner -->
                <InfoBanner
                    v-if="stats.currentConcert"
                    title="Huidig Concert"
                    :heading="stats.currentConcert.title"
                    subtitle="Dit is het concert waar we momenteel voor repeteren"
                    bgClass="bg-blue-900 text-white"
                >
                    <template #action>
                        <Link :href="route('beheer.concerten.edit', stats.currentConcert.id)"
                              class="bg-yellow-400 p-4 text-gray-900 py-2 rounded-lg hover:bg-yellow-300 text-center transition-colors font-semibold text-sm">
                            + Concert bewerken
                        </Link>
                    </template>
                    <template #icon>
                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </template>
                </InfoBanner>

                <!-- Stat cards grid -->
                <ActionGrid>
                    <!-- Stukken -->
                    <StatCard
                        :stat="stats.scores"
                        label="Stukken in bibliotheek"
                    >
                        <template #icon>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </template>
                        <template #action>
                            <Link :href="route('beheer.bladmuziek.create')"
                                  class="mt-4 p-4 w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-800 text-center transition-colors font-semibold text-sm">
                                + Stuk toevoegen
                            </Link>
                        </template>
                    </StatCard>

                    <!-- Concerten -->
                    <StatCard
                        :stat="stats.concerts"
                        label="Geplande concerten"
                    >
                        <template #icon>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </template>
                        <template #action>
                            <Link :href="route('beheer.concerten.create')"
                                  class="mt-4 p-4 w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-800 text-center transition-colors font-semibold text-sm">
                                + Concert toevoegen
                            </Link>
                        </template>
                    </StatCard>

                    <!-- Leden in afwachting -->
                    <StatCard
                        :stat="stats.pendingUsers"
                        label="Leden in afwachting"
                    >
                        <template #icon>
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template #action>
                            <Link :href="route('beheer.gebruikers.index')"
                                  class="mt-4 p-4 w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 text-center transition-colors font-semibold text-sm">
                                Goedkeuren
                            </Link>
                        </template>
                    </StatCard>
                </ActionGrid>

                <!-- Upcoming Concerts Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Volgende concerten</h3>
                    <div v-if="upcomingConcerts.length === 0" class="text-gray-400 text-sm text-center py-8 bg-white rounded-2xl border border-gray-200">
                        Geen toekomstige concerten
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Link v-for="concert in upcomingConcerts" :key="concert.id" :href="route('beheer.concerten.index')" class="block p-4 bg-blue-50 rounded-lg border border-blue-200 hover:border-blue-500 transition-colors">
                            <div class="font-semibold text-gray-900 text-sm">{{ concert.title }}</div>
                            <div class="text-gray-500 text-xs mt-1">{{ formatDate(concert.date) }}</div>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
