<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    scores: Object,
});

const search = ref('');

const filteredScores = computed(() => {
    if (!search.value) return props.scores.data;
    const q = search.value.toLowerCase();
    return props.scores.data.filter(score =>
        score.title.toLowerCase().includes(q) ||
        score.composer.toLowerCase().includes(q) ||
        (score.arranger && score.arranger.toLowerCase().includes(q))
    );
});

function destroy(score) {
    if (confirm(`Weet je zeker dat je "${score.title}" wilt verwijderen?`)) {
        router.delete(route('beheer.bladmuziek.destroy', score.id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Stukken beheren" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1 text-left">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Bibliotheek</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Bladmuziek en partijen beheer</p>
                </div>
                <Link :href="route('beheer.bladmuziek.create')" class="bg-blue-950 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 transition-all active:scale-95 shadow-xl shadow-blue-900/20">
                    + Nieuw stuk
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Search & Filters -->
                <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Zoek op titel of componist..."
                            class="w-full pl-12 pr-6 py-4 bg-white border-none rounded-2xl shadow-sm text-sm font-black text-blue-950 placeholder:text-gray-300 focus:ring-2 focus:ring-yellow-400 transition-all"
                        />
                    </div>
                    
                    <div class="flex items-center gap-6 px-4">
                        <div class="text-right">
                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-300">Totaal Repertoire</div>
                            <div class="text-3xl font-black text-blue-950 italic leading-none">{{ scores.total }} <span class="text-xs font-bold text-gray-200">stukken</span></div>
                        </div>
                    </div>
                </div>

                <!-- Scores Grid / Table -->
                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Compositie</th>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Arrangeur</th>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Aantal Partijen</th>
                                <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="score in filteredScores" :key="score.id" class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-10 py-8">
                                    <div class="text-lg font-black text-blue-950 leading-tight italic">{{ score.title }}</div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ score.composer }}</div>
                                </td>
                                <td class="px-10 py-8 text-sm font-bold text-gray-500 italic">
                                    {{ score.arranger || '-' }}
                                </td>
                                <td class="px-10 py-8">
                                    <div class="inline-flex items-center justify-center px-4 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest">
                                        {{ score.parts_count }} parts
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex items-center justify-end gap-6">
                                        <Link :href="route('beheer.bladmuziek.edit', score.id)" class="text-blue-600 hover:text-blue-950 font-black text-[10px] uppercase tracking-widest transition-colors">
                                            Aanpassen
                                        </Link>
                                        <button @click="destroy(score)" class="text-red-300 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition-colors">
                                            Wis
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredScores.length">
                                <td colspan="4" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <p class="font-black italic text-gray-300 text-lg mb-2">Geen bladmuziek gevonden.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="scores.last_page > 1" class="px-10 py-8 flex justify-between items-center bg-gray-50/50 border-t border-gray-50">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">
                            PAGINA {{ scores.current_page }} / {{ scores.last_page }}
                        </span>
                        <div class="flex gap-4">
                            <Link v-if="scores.prev_page_url" :href="scores.prev_page_url" class="px-6 py-3 bg-white border border-gray-100 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all active:scale-95 shadow-sm">Vorige</Link>
                            <Link v-if="scores.next_page_url" :href="scores.next_page_url" class="px-6 py-3 bg-blue-950 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 transition-all active:scale-95 shadow-xl shadow-blue-900/10">Volgende</Link>
                        </div>
                    </div>
                </div>

                <div class="mt-20 flex flex-col md:flex-row items-center justify-between gap-10">
                    <Link :href="route('beheer.dashboard')" class="text-gray-400 hover:text-blue-950 text-[10px] font-black uppercase tracking-[0.4em] transition-colors">
                        &larr; Terug naar Dashboard
                    </Link>
                    <div class="p-8 bg-blue-950 text-white rounded-[2rem] shadow-2xl flex items-center gap-6 max-w-lg text-left">
                        <div class="w-12 h-12 rounded-2xl bg-yellow-400 text-blue-950 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="text-xs font-bold leading-relaxed">
                            Muzikanten zien geüploade partijen direct in hun dashboard. Je kunt de volgorde per concert aanpassen bij 'Projecten'.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
