<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    scores: Array,
    currentConcert: Object,
});

const search = ref('');
const expandedScores = ref(new Set());

const filteredScores = computed(() => {
    if (!search.value) return props.scores;
    const q = search.value.toLowerCase();
    return props.scores.filter(score =>
        score.title.toLowerCase().includes(q) ||
        score.composer.toLowerCase().includes(q) ||
        score.parts.some(p => p.instrument.toLowerCase().includes(q))
    );
});

function toggleScore(id) {
    if (expandedScores.value.has(id)) {
        expandedScores.value.delete(id);
    } else {
        expandedScores.value.add(id);
    }
    expandedScores.value = new Set(expandedScores.value);
}
</script>

<template>
    <Head title="Muziekaanbod" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Muziekaanbod</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Current Concert -->
                <div v-if="currentConcert" class="mb-10 bg-blue-900 rounded-2xl p-8 text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-yellow-500 text-blue-900 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                            Huidig concert
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold mb-1">{{ currentConcert.title }}</h2>
                    <p class="text-blue-200 text-sm mb-6">
                        {{ new Date(currentConcert.date).toLocaleDateString('nl-NL', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                        <span v-if="currentConcert.location"> &bull; {{ currentConcert.location }}</span>
                    </p>

                    <h3 class="text-yellow-500 font-semibold mb-4">Stukken voor dit concert</h3>
                    <div v-if="currentConcert.scores && currentConcert.scores.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="score in currentConcert.scores"
                            :key="score.id"
                            class="bg-blue-800 rounded-xl p-4"
                        >
                            <div class="font-semibold text-white">{{ score.title }}</div>
                            <div class="text-blue-300 text-sm">{{ score.composer }}</div>
                            <div v-if="score.parts && score.parts.length" class="mt-3 flex flex-wrap gap-2">
                                <a
                                    v-for="part in score.parts"
                                    :key="part.id"
                                    :href="route('muziek.download', { score: score.id, part: part.id })"
                                    class="inline-flex items-center gap-1 bg-yellow-500 text-blue-900 text-xs font-semibold px-2 py-1 rounded hover:bg-yellow-400 transition-colors"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ part.instrument }}
                                </a>
                            </div>
                            <div v-else class="text-blue-400 text-xs mt-2">Geen parts beschikbaar</div>
                        </div>
                    </div>
                    <div v-else class="text-blue-300">Nog geen stukken gekoppeld aan dit concert.</div>
                </div>

                <!-- All Scores -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <h2 class="text-xl font-bold text-blue-900">Alle stukken</h2>
                        <div class="relative">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Zoek op titel, componist of instrument..."
                                class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <div v-if="filteredScores.length === 0" class="text-center py-12 text-gray-400">
                        Geen stukken gevonden.
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="score in filteredScores"
                            :key="score.id"
                            class="border border-gray-200 rounded-xl overflow-hidden"
                        >
                            <button
                                @click="toggleScore(score.id)"
                                class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors text-left"
                            >
                                <div class="flex items-center gap-4">
                                    <span class="text-yellow-500 font-bold text-sm w-8 text-right">{{ score.number }}</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ score.title }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ score.composer }}
                                            <span v-if="score.arranger" class="text-gray-400"> &bull; arr. {{ score.arranger }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ score.parts.length }} part(s)</span>
                                    <svg
                                        class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': expandedScores.has(score.id) }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>

                            <div v-if="expandedScores.has(score.id)" class="px-5 pb-4 bg-gray-50">
                                <div v-if="score.parts.length" class="flex flex-wrap gap-2 pt-3">
                                    <a
                                        v-for="part in score.parts"
                                        :key="part.id"
                                        :href="route('muziek.download', { score: score.id, part: part.id })"
                                        class="inline-flex items-center gap-2 bg-blue-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ part.instrument }}
                                    </a>
                                </div>
                                <div v-else class="pt-3 text-sm text-gray-400">
                                    Nog geen parts beschikbaar voor dit stuk.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
