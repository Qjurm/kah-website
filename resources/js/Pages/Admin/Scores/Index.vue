<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionHeader from '@/Components/Dashboard/SectionHeader.vue';
import TipsCard from '@/Components/Dashboard/TipsCard.vue';

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
    <Head title="Stukken beheren" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Stukken beheren</h2>
                    <p class="text-sm text-gray-600">Voeg stukken toe, upload partijen per instrument</p>
                </div>
                <Link :href="route('beheer.bladmuziek.create')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors whitespace-nowrap">
                    + Nieuw stuk
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section header -->
                <SectionHeader 
                    title="Stukken beheren"
                    subtitle="Voeg stukken toe, upload partijen per instrument"
                />

                <!-- Search -->
                <div class="mb-6">
                    <div class="relative max-w-sm">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Zoek op titel, componist of arrangeur..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                        />
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Scores Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nr.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Componist</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrangeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partijen</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="score in filteredScores" :key="score.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-yellow-600 font-bold">{{ score.number }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ score.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ score.composer }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ score.arranger || '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="bg-blue-100 text-blue-900 font-semibold text-xs px-2 py-1 rounded">
                                        {{ score.parts_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    <Link :href="route('beheer.bladmuziek.edit', score.id)" class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-medium text-xs">
                                        Bewerken
                                    </Link>
                                    <button @click="destroy(score)" class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium text-xs">
                                        Verwijderen
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!filteredScores.length">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ search ? 'Geen stukken gevonden voor deze zoekopdracht.' : 'Nog geen stukken toegevoegd.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="props.scores.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                        <span class="text-sm text-gray-500">{{ props.scores.from }}-{{ props.scores.to }} van {{ props.scores.total }}</span>
                        <div class="flex gap-2">
                            <Link
                                v-if="props.scores.prev_page_url"
                                :href="props.scores.prev_page_url"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 transition-colors"
                            >⬅️ Vorige</Link>
                            <Link
                                v-if="props.scores.next_page_url"
                                :href="props.scores.next_page_url"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 transition-colors"
                            >Volgende ➡️</Link>
                        </div>
                    </div>
                </div>

                <!-- Helper Card -->
                <div class="mt-8">
                    <TipsCard>
                        <div class="space-y-1">
                            <div>Klik '📤 Partijen' om PDF-bestanden voor elk instrument te uploaden</div>
                            <div>Gebruik het zoekveld om snel stukken te vinden</div>
                            <div>De nummering helpt bij het organiseren van concertvolgordes</div>
                            <div>Muzikanten kunnen hun partijen downloaden in het 'Muziek' menu</div>
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
