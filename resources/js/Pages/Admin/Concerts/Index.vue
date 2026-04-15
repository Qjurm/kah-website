<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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
    <AuthenticatedLayout>
        <Head title="Concerten beheren" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1 text-left">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Optredens</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Planning en programma beheer</p>
                </div>
                <Link :href="route('beheer.concerten.create')" class="bg-blue-950 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 transition-all active:scale-95 shadow-xl shadow-blue-900/20">
                    + Nieuw concert
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div v-if="concerts.data.some(c => c.is_current)" class="mb-10 bg-yellow-400 rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden text-left">
                    <div class="absolute top-0 right-0 p-12 opacity-10 pointer-events-none text-blue-950">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-950/40 mb-3">Huidig Focus Project</div>
                        <div v-for="concert in concerts.data.filter(c => c.is_current)" :key="'focus-'+concert.id" class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-4xl font-black text-blue-950 italic mb-2 leading-tight">{{ concert.title }}</h3>
                                <p class="text-blue-950/60 font-black uppercase tracking-widest text-[11px]">{{ formatDate(concert.date) }} · {{ concert.location }}</p>
                            </div>
                            <Link :href="route('beheer.concerten.edit', concert.id)" class="bg-blue-950 text-white px-8 py-4 rounded-2xl font-black text-[10px] tracking-widest uppercase hover:bg-black transition-all shadow-lg active:scale-95 whitespace-nowrap">
                                Instellen & Volgorde
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Naam Optreden</th>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Locatie</th>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Status</th>
                                <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="concert in concerts.data" :key="'row-'+concert.id" class="hover:bg-blue-50/30 transition-colors" :class="{'bg-yellow-50/30': concert.is_current}">
                                <td class="px-10 py-8">
                                    <div class="text-lg font-black text-blue-950 leading-tight italic">{{ concert.title }}</div>
                                    <div class="text-[10px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ formatDate(concert.date) }}</div>
                                </td>
                                <td class="px-10 py-8 text-sm font-bold text-gray-500 italic">
                                    {{ concert.location || 'Nog geen locatie' }}
                                </td>
                                <td class="px-10 py-8">
                                    <button
                                        @click="toggleCurrent(concert)"
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all active:scale-90"
                                        :class="concert.is_current
                                            ? 'bg-yellow-400 text-blue-950 shadow-md shadow-yellow-400/20'
                                            : 'bg-gray-100 text-gray-300 hover:bg-gray-200 hover:text-blue-950'"
                                    >
                                        {{ concert.is_current ? '🎯 Actief' : 'Activeren' }}
                                    </button>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-6">
                                        <Link :href="route('beheer.concerten.edit', concert.id)" class="text-blue-600 hover:text-black font-black text-[10px] uppercase tracking-widest transition-colors">
                                            Aanpassen
                                        </Link>
                                        <button @click="destroy(concert)" class="text-red-300 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition-colors">
                                            Verwijder
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!concerts.data.length">
                                <td colspan="4" class="px-10 py-24 text-center">
                                    <p class="font-black text-gray-300 italic">Geen concerten in de database.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-20 text-center">
                    <Link :href="route('beheer.dashboard')" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Terug naar Dashboard
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
