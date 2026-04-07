<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

                <!-- Intro Card -->
                <div class="mb-8 bg-blue-50 border-l-4 border-blue-900 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Welkom in het admin panel</h3>
                    <p class="text-gray-700 text-sm">Beheer hier alle muziekstukken, concerten en gebruikers van je orkest. Goedkeur nieuwe leden en upload partijen voor concerten.</p>
                </div>

                <!-- Top Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8">
                    <!-- Stukken - Document icon -->
                    <div class="bg-blue-900 text-white rounded-2xl p-6 min-h-[140px] flex flex-col justify-between">
                        <div>
                            <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.scores }}</div>
                            <div class="text-blue-200 text-sm">Stukken</div>
                        </div>
                        <svg class="w-8 h-8 text-yellow-500 opacity-50 self-end" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <!-- Concerten - Calendar icon -->
                    <div class="bg-blue-900 text-white rounded-2xl p-6 min-h-[140px] flex flex-col justify-between">
                        <div>
                            <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.concerts }}</div>
                            <div class="text-blue-200 text-sm">Concerten</div>
                        </div>
                        <svg class="w-8 h-8 text-yellow-500 opacity-50 self-end" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <!-- Gebruikers - Users icon -->
                    <div class="bg-blue-900 text-white rounded-2xl p-6 min-h-[140px] flex flex-col justify-between">
                        <div>
                            <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.users }}</div>
                            <div class="text-blue-200 text-sm">Gebruikers</div>
                        </div>
                        <svg class="w-8 h-8 text-yellow-500 opacity-50 self-end" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <!-- Pending - Hourglass/Clock icon -->
                    <div class="bg-red-600 text-white rounded-2xl p-6 min-h-[140px] flex flex-col justify-between" v-if="stats.pendingUsers > 0">
                        <div>
                            <div class="text-4xl font-bold text-white mb-1">{{ stats.pendingUsers }}</div>
                            <div class="text-red-200 text-sm">In afwachting</div>
                        </div>
                        <svg class="w-8 h-8 text-white opacity-50 self-end" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div v-if="stats.currentConcert" class="bg-yellow-50 border-2 border-yellow-300 rounded-2xl p-4 mb-8">
                    <span class="text-yellow-700 font-bold text-sm">Huidig concert: </span>
                    <span class="text-yellow-800 font-semibold">{{ stats.currentConcert }}</span>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Pending Approvals -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="font-bold text-gray-900">In afwachting van goedkeuring</h3>
                        </div>
                        <div v-if="pendingUsers.length === 0" class="text-gray-400 text-sm text-center py-4">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Geen gebruikers in afwachting
                        </div>
                        <div v-else class="space-y-2">
                            <div v-for="user in pendingUsers" :key="user.id" class="p-3 bg-red-50 rounded-lg border border-red-200">
                                <div class="font-semibold text-gray-900 text-sm">{{ user.name }}</div>
                                <div class="text-gray-500 text-xs">{{ user.email }}</div>
                                <Link :href="route('beheer.gebruikers.index')" class="text-blue-600 text-xs font-semibold hover:text-blue-800 mt-2 inline-block">
                                    Goedkeuren →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Concerts -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="font-bold text-gray-900">Volgende concerten</h3>
                        </div>
                        <div v-if="upcomingConcerts.length === 0" class="text-gray-400 text-sm text-center py-4">
                            Geen toekomstige concerten
                        </div>
                        <div v-else class="space-y-2">
                            <Link v-for="concert in upcomingConcerts" :key="concert.id" :href="route('beheer.concerten.index')" class="block p-3 bg-blue-50 rounded-lg border border-blue-200 hover:border-blue-500 transition-colors">
                                <div class="font-semibold text-gray-900 text-sm">{{ concert.title }}</div>
                                <div class="text-gray-500 text-xs">{{ formatDate(concert.date) }}</div>
                            </Link>
                        </div>
                    </div>

                    <!-- Instrument Breakdown -->
                    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            <h3 class="font-bold text-gray-900">Instrumenten</h3>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-2">
                                <span class="text-gray-700 text-sm">Houtblazers</span>
                                <span class="bg-blue-100 text-blue-900 font-semibold text-sm px-3 py-1 rounded-full">
                                    {{ instrumentBreakdown?.woodwinds || 0 }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-2">
                                <span class="text-gray-700 text-sm">Koperblazers</span>
                                <span class="bg-yellow-100 text-yellow-900 font-semibold text-sm px-3 py-1 rounded-full">
                                    {{ instrumentBreakdown?.brass || 0 }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-2">
                                <span class="text-gray-700 text-sm">Slagwerk</span>
                                <span class="bg-red-100 text-red-900 font-semibold text-sm px-3 py-1 rounded-full">
                                    {{ instrumentBreakdown?.percussion || 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Scores -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="font-bold text-gray-900">Recente uploads</h3>
                        </div>
                        <Link :href="route('beheer.bladmuziek.index')" class="text-blue-600 text-sm font-semibold hover:text-blue-800">
                            Alles zien →
                        </Link>
                    </div>
                    <div v-if="recentScores.length === 0" class="text-gray-400 text-sm text-center py-4">
                        Geen stukken toegevoegd
                    </div>
                    <div v-else class="space-y-2">
                        <div v-for="score in recentScores" :key="score.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">{{ score.title }}</div>
                                <div class="text-gray-500 text-xs">{{ score.composer }}</div>
                            </div>
                            <span class="text-gray-400 text-xs">{{ formatDate(score.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Add Score -->
                    <Link :href="route('beheer.bladmuziek.create')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all min-h-[180px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-3">
                                <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors">
                                    <!-- Plus icon for adding -->
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900">Nieuw stuk</h3>
                            </div>
                            <p class="text-gray-500 text-sm">Voeg een nieuw muziekstuk toe aan je bibliotheek. Upload componist en arrangeur info.</p>
                        </div>
                    </Link>

                    <!-- Add Concert -->
                    <Link :href="route('beheer.concerten.create')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all min-h-[180px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-3">
                                <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors">
                                    <!-- Plus icon for adding -->
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900">Nieuw concert</h3>
                            </div>
                            <p class="text-gray-500 text-sm">Plan een nieuw concert in. Voeg datum, locatie en stukken toe.</p>
                        </div>
                    </Link>

                    <!-- Approve Users -->
                    <Link :href="route('beheer.gebruikers.index')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all min-h-[180px] flex flex-col justify-between" :class="{'border-red-300 bg-red-50': stats.pendingUsers > 0}">
                        <div>
                            <div class="flex items-center gap-4 mb-3">
                                <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors" :class="{'bg-red-100 text-red-900 group-hover:bg-red-900': stats.pendingUsers > 0}">
                                    <!-- Check-badge icon for approvals -->
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 7a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900" :class="{'text-red-900': stats.pendingUsers > 0}">Goedkeuren</h3>
                            </div>
                            <p class="text-gray-500 text-sm" :class="{'text-red-700 font-semibold': stats.pendingUsers > 0}" v-if="stats.pendingUsers > 0"><strong>{{ stats.pendingUsers }} muzikant(en)</strong> wachten op goedkeuring!</p>
                            <p class="text-gray-500 text-sm" v-else>Beheer gebruikers en goedkeuringen.</p>
                        </div>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
