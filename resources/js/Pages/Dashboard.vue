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
    if (!date) return '-';
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
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
    <AuthenticatedLayout>
        <Head title="Mijn Dashboard" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950">{{ __('Mijn Muziekdashboard') }}</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ __('Jouw instrumenten en komende optredens') }}</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Primary Instrument Card -->
                <div v-if="primaryInstrument" class="mb-10 bg-blue-950 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden text-left">
                    <div class="absolute top-0 right-0 p-12 opacity-10 pointer-events-none">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400 mb-3">{{ __('Mijn Hoofdinstrument') }}</div>
                            <h1 class="text-5xl font-black mb-3 italic leading-tight">{{ primaryInstrument.name }}</h1>
                            <p class="text-blue-200 font-bold uppercase tracking-widest text-xs">
                                {{ __('Je speelt') }} {{ allInstruments.length }} {{ allInstruments.length !== 1 ? __('instrumenten') : __('instrument') }} {{ __('in totaal') }}
                            </p>
                        </div>
                        <Link
                            :href="route('mijn-instrumenten.edit')"
                            class="bg-yellow-400 text-blue-950 px-8 py-4 rounded-2xl hover:bg-yellow-300 transition-all font-black text-[10px] uppercase tracking-widest active:scale-95 shadow-xl shadow-yellow-400/20 whitespace-nowrap"
                        >
                            {{ __('Instrumenten bewerken') }}
                        </Link>
                    </div>

                    <!-- Other instruments -->
                    <div v-if="allInstruments.length > 1" class="mt-10 pt-8 border-t border-white/10 flex flex-wrap gap-3">
                        <span
                            v-for="instrument in allInstruments.filter(i => i.id !== primaryInstrument.id)"
                            :key="instrument.id"
                            class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest border border-white/5"
                        >
                            {{ instrument.name }}
                        </span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-12 text-left">
                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-blue-50 rounded-2xl text-blue-600 flex items-center justify-center shadow-inner group-hover:bg-blue-950 group-hover:text-white transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12M6 12h12" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">{{ __('Nieuw Repertoire') }}</div>
                                <div class="text-4xl font-black text-blue-950 leading-tight">{{ recentStats.newScores }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-yellow-400/10 rounded-2xl text-yellow-600 flex items-center justify-center shadow-inner group-hover:bg-yellow-400 group-hover:text-blue-950 transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 0 00-2 2v12a2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">{{ __('Aankomende Projecten') }}</div>
                                <div class="text-4xl font-black text-blue-950 leading-tight">{{ recentStats.upcomingConcerts }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Concert & My Parts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-left">
                    <!-- Next Concert Card -->
                    <div class="lg:col-span-2 bg-white rounded-[3rem] border border-gray-100 p-10 shadow-sm">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-blue-950 rounded-2xl text-white flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 0 00-2-2H5a2 0 00-2 2v12a2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-black text-blue-950 italic">{{ __('Volgend Optreden') }}</h2>
                        </div>

                        <div v-if="nextConcert" class="bg-gray-50/50 rounded-[2rem] p-8 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-8">
                            <div>
                                <h3 class="text-4xl font-black text-blue-950 mb-4 italic leading-tight">{{ nextConcert.title }}</h3>
                                <div class="flex flex-wrap gap-6 text-blue-900/40 text-[10px] font-black uppercase tracking-widest">
                                    <p class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 0 00-2 2v12a2 0 002 2z"/></svg>
                                        {{ formatDate(nextConcert.date) }}
                                    </p>
                                    <p v-if="nextConcert.location" class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ nextConcert.location }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-8 py-6 bg-white rounded-2xl shadow-xl shadow-blue-900/5 text-center">
                                <div class="text-5xl font-black text-blue-950 leading-none mb-1">{{ getDaysUntil(nextConcert.date) }}</div>
                                <div class="text-[9px] uppercase font-black tracking-widest text-blue-300">{{ __('dagen') }}</div>
                            </div>
                        </div>

                        <div v-else class="py-20 text-center bg-gray-50/50 rounded-[2rem] border-2 border-dashed border-gray-100">
                            <p class="text-gray-300 font-black italic">{{ __('Geen concerten in de agenda.') }}</p>
                        </div>
                    </div>

                    <!-- My Parts Card -->
                    <div class="lg:col-span-1 bg-white rounded-[3rem] border border-gray-100 p-10 shadow-sm flex flex-col">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-yellow-400 rounded-2xl text-blue-950 flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-blue-950 italic">{{ __('Mijn Partijen') }}</h3>
                        </div>

                        <div v-if="myParts.length === 0" class="flex-1 flex flex-col items-center justify-center py-10 opacity-30 italic">
                            <p class="font-black text-gray-400">{{ __('Nog geen partijen.') }}</p>
                        </div>

                        <div v-else class="space-y-4 mb-8">
                            <div v-for="part in myParts" :key="part.part_id" class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all active:scale-95 group">
                                <Link :href="route('muziek.download', { score: part.score_id, part: part.part_id })" class="block">
                                    <div class="font-black text-blue-950 text-base leading-tight mb-1 group-hover:text-blue-600 transition-colors">{{ part.score_title }}</div>
                                    <div class="text-gray-400 text-[9px] font-black uppercase tracking-widest">{{ part.score_composer }}</div>
                                </Link>
                            </div>
                        </div>

                        <Link
                            :href="route('muziek.index')"
                            class="mt-auto w-full flex items-center justify-center gap-3 bg-blue-950 text-white text-[10px] font-black uppercase tracking-widest py-5 rounded-[1.5rem] hover:bg-blue-900 transition-all active:scale-95 shadow-xl shadow-blue-950/20"
                        >
                            {{ __('Alle muziek bekijken') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
