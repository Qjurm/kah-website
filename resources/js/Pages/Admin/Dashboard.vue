<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: Object,
    pendingUsers: Array,
    recentScores: Array,
    upcomingConcerts: Array,
    instrumentBreakdown: Object,
});

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

const safeStats = computed(() => props.stats || {});
const hasPendingUsers = computed(() => (props.stats?.pendingUsers || 0) > 0);
const hasUpcomingConcerts = computed(() => (props.upcomingConcerts?.length || 0) > 0);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin Dashboard" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">Beheercentrum</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Centraal overzicht van de vereniging</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-10 text-left">
                    <h1 class="text-5xl font-black text-blue-950 mb-3 italic">Dashboard</h1>
                    <p class="text-gray-500 font-bold">Beheersysteem voor de Koninklijke Almelosche Harmonie.</p>
                </div>

                <template v-if="safeStats.currentConcert">
                    <div class="mb-10 text-left">
                        <div class="relative overflow-hidden bg-blue-950 rounded-[2.5rem] p-10 text-white shadow-2xl">
                            <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                                <svg class="w-48 h-48" fill="white" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0 -2-2H5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2z" /></svg>
                            </div>
                            
                            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400 mb-3">Focus Project</div>
                                    <h2 class="text-4xl font-black mb-2 italic">{{ safeStats.currentConcert.title }}</h2>
                                    <p class="text-blue-200 font-black uppercase tracking-widest text-xs">{{ formatDate(safeStats.currentConcert.date) }}</p>
                                </div>
                                <Link :href="route('beheer.concerten.edit', safeStats.currentConcert.id)"
                                       class="bg-yellow-400 text-blue-950 px-8 py-4 rounded-2xl hover:bg-yellow-300 transition-all font-black text-[10px] uppercase tracking-widest active:scale-95 shadow-xl shadow-yellow-400/20 whitespace-nowrap">
                                    Repertoire bewerken
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16 text-left">
                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 bg-blue-50 rounded-2xl text-blue-600 flex items-center justify-center shadow-inner group-hover:bg-blue-950 group-hover:text-white transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">Bibliotheek</div>
                                <div class="text-4xl font-black text-blue-950 leading-tight">{{ safeStats.scores || 0 }}</div>
                            </div>
                        </div>
                        <Link :href="route('beheer.bladmuziek.create')"
                              class="flex items-center justify-center w-full bg-blue-950 text-white py-4 rounded-2xl hover:bg-blue-900 transition-all font-black text-[10px] uppercase tracking-widest active:scale-95 shadow-lg shadow-blue-950/20">
                            Toevoegen
                        </Link>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 bg-yellow-400/10 rounded-2xl text-yellow-600 flex items-center justify-center shadow-inner group-hover:bg-yellow-400 group-hover:text-blue-950 transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0 -2-2H5a2 0 0 0 -2 2v12a2 0 0 0 2 2z" /></svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">Projecten</div>
                                <div class="text-4xl font-black text-blue-950 leading-tight">{{ safeStats.concerts || 0 }}</div>
                            </div>
                        </div>
                        <Link :href="route('beheer.concerten.create')"
                              class="flex items-center justify-center w-full bg-blue-950 text-white py-4 rounded-2xl hover:bg-blue-900 transition-all font-black text-[10px] uppercase tracking-widest active:scale-95 shadow-lg shadow-blue-950/20">
                            Inplannen
                        </Link>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group"
                         :class="{'ring-2 ring-orange-500/50 bg-orange-50/20': hasPendingUsers}">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 bg-orange-50 rounded-2xl text-orange-600 flex items-center justify-center shadow-inner group-hover:bg-orange-600 group-hover:text-white transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">Wachtlijst</div>
                                <div class="text-4xl font-black text-blue-950 leading-tight">{{ safeStats.pendingUsers || 0 }}</div>
                            </div>
                        </div>
                        <Link :href="route('beheer.gebruikers.index')"
                              class="flex items-center justify-center w-full py-4 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest active:scale-95 shadow-lg shadow-orange-600/10"
                              :class="hasPendingUsers ? 'bg-orange-600 text-white hover:bg-orange-700' : 'bg-gray-100 text-gray-400 hover:text-blue-950 hover:bg-gray-200 shadow-none'">
                            {{ hasPendingUsers ? 'Nu goedkeuren' : 'Alle leden' }}
                        </Link>
                    </div>
                </div>

                <div class="text-left">
                    <h3 class="text-2xl font-black text-blue-950 mb-10 italic">Agenda Overzicht</h3>
                    <template v-if="!hasUpcomingConcerts">
                        <div class="py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                            <p class="text-gray-300 font-black italic opacity-50">Geen toekomstige optredens.</p>
                        </div>
                    </template>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <Link v-for="concert in upcomingConcerts" :key="'agenda-'+concert.id" :href="route('beheer.concerten.index')" 
                              class="group block p-10 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm transition-all hover:shadow-2xl hover:border-blue-100 hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-black text-blue-950 text-2xl group-hover:text-blue-600 transition-colors leading-tight mb-3 italic">{{ concert.title }}</div>
                                    <div class="text-[11px] font-black uppercase tracking-[0.3em] text-gray-300 group-hover:text-blue-300 transition-colors">{{ formatDate(concert.date) }}</div>
                                </div>
                                <div class="w-14 h-14 rounded-[1.25rem] bg-gray-50 flex items-center justify-center group-hover:bg-blue-950 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="mt-20 text-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-300">Koninklijke Almelosche Harmonie &copy; {{ new Date().getFullYear() }}</p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
