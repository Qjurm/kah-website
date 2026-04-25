<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PdfPreviewModal from '@/Components/PdfPreviewModal.vue';
import PartActionButtons from '@/Components/PartActionButtons.vue';

const props = defineProps({
    scores: Array,
    currentConcert: Object,
    myRelevantParts: Object,
    userInstruments: Array,
    primaryInstrument: Object,
});

const search = ref('');
const selectedInstrumentId = ref(props.primaryInstrument?.id || null);
const expandedScores = ref(new Set());

const previewUrl = ref('');
const previewTitle = ref('');
const showPreview = ref(false);

const filteredScores = computed(() => {
    let filtered = props.scores || [];
    if (selectedInstrumentId.value) {
        filtered = filtered.filter(score =>
            score.parts.some(p => p.instrument_id === selectedInstrumentId.value)
        );
    }
    if (!search.value) return filtered;
    const q = search.value.toLowerCase();
    return filtered.filter(score =>
        score.title.toLowerCase().includes(q) ||
        score.composer.toLowerCase().includes(q) ||
        score.parts.some(p => p.instrument?.name?.toLowerCase().includes(q))
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

function getPartDisplayName(part) {
    const baseName = part.instrument?.name || part.instrument;
    return (part.part_number && part.part_number > 1) 
        ? `${baseName} ${part.part_number}`
        : baseName;
}

const getDownloadUrl = (scoreId, partId) => route('muziek.download', { score: scoreId, part: partId });
const getViewUrl = (scoreId, partId) => route('muziek.view', { score: scoreId, part: partId });

function openPreview(score, part) {
    previewUrl.value = getViewUrl(score.id, part.id);
    previewTitle.value = `${score.title} - ${getPartDisplayName(part)}`;
    showPreview.value = true;
}

function closePreview() {
    showPreview.value = false;
    previewUrl.value = '';
}

const isUserPart = (part) => selectedInstrumentId.value && part.instrument_id === selectedInstrumentId.value;

const getSortedParts = (parts) => {
    if (!selectedInstrumentId.value) return parts;
    const items = [...parts];
    return items.sort((a, b) => {
        if (isUserPart(a)) return -1;
        if (isUserPart(b)) return 1;
        return 0;
    });
};

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="__('Muziekotheek')" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">{{ __('Bibliotheek') }}</h2>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] leading-none">{{ __('Bladmuziek en partijen') }}</p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-12 text-left">
                    <h1 class="text-5xl font-black text-blue-950 mb-3 italic">{{ __('Repertoire') }}</h1>
                    <p class="text-gray-500 font-bold">{{ __('Vind je partijen voor de harmonie.') }}</p>
                </div>

                <div v-if="currentConcert" class="mb-20 bg-blue-950 rounded-[3rem] p-10 sm:p-12 text-white shadow-2xl relative overflow-hidden text-left">
                    <div class="absolute top-0 right-0 p-16 opacity-5 pointer-events-none">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                <div>
                                    <span class="inline-block bg-yellow-400 text-blue-950 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full mb-4 leading-none">{{ __('Volgend Optreden') }}</span>
                                    <h1 class="text-4xl sm:text-5xl font-black italic leading-tight uppercase tracking-tight">{{ currentConcert.title }}</h1>
                                    <p class="text-blue-300 text-xs font-black uppercase tracking-[0.2em] mt-2 opacity-60 leading-none">{{ formatDate(currentConcert.date) }}</p>
                                </div>
                            </div>
                            
                            <!-- Bulk Download Top Right -->
                            <div class="lg:absolute lg:top-12 lg:right-12 z-20">
                                <a 
                                    :href="route('muziek.download-bulk-concert', currentConcert.id)"
                                    class="bg-white text-blue-950 px-8 py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-yellow-400 transition-all flex items-center gap-3 shadow-2xl shadow-black/40 hover:-translate-y-1 active:scale-95"
                                >
                                    <div class="p-2 bg-blue-950 text-white rounded-lg group-hover:bg-blue-900 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    </div>
                                    {{ __('Download jouw partijen voor dit concert') }}
                                </a>
                            </div>
                        </div>

                        <!-- Adaptive View: Grid for few items, Compact List for many -->
                        <div v-if="currentConcert.scores.length <= 6" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="score in currentConcert.scores" :key="'cc-'+score.id" 
                                class="bg-white/5 border border-white/10 rounded-[2.5rem] p-8 hover:bg-white/10 transition-all group flex flex-col"
                            >
                                <div class="mb-4">
                                    <h3 class="font-black text-white text-xl leading-tight mb-2 italic">{{ score.title }}</h3>
                                    <p class="text-blue-400/60 text-[10px] font-black uppercase tracking-widest">{{ score.composer }}</p>
                                </div>
                                
                                <a 
                                    :href="route('muziek.download-bulk-score', { score: score.id, instrument_id: selectedInstrumentId })"
                                    class="mb-6 flex items-center justify-center gap-2 py-2.5 px-4 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black uppercase tracking-widest text-white/70 hover:bg-white/10 hover:text-white hover:border-white/20 transition-all shadow-sm group"
                                >
                                    <svg class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ __('Download alle partijen') }}
                                </a>
                                
                                <div v-if="myRelevantParts[score.id]" class="space-y-3">
                                    <div v-for="part in myRelevantParts[score.id]" :key="'rel-'+part.id" 
                                        class="flex items-center justify-between bg-white/5 rounded-2xl px-5 py-4 border border-white/5"
                                    >
                                        <div class="min-w-0 mr-3">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-100 truncate block leading-tight">
                                                {{ getPartDisplayName(part) }}
                                            </span>
                                            <span v-if="part.original_filename" class="text-[9px] font-bold text-blue-200/80 truncate block mt-1">
                                                {{ part.original_filename }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <PartActionButtons 
                                                :download-url="getDownloadUrl(score.id, part.id)"
                                                view-class="bg-yellow-400 text-blue-950 hover:bg-yellow-300 shadow-lg shadow-yellow-400/10"
                                                download-class="bg-white text-blue-950 hover:bg-blue-50 shadow-sm"
                                                @preview="openPreview(score, part)"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="pt-6 border-t border-white/5 flex items-center justify-center opacity-30 italic">
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ __('Geen partij voor jou') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Compact List View for large repertoires -->
                        <div v-else class="space-y-3">
                            <div v-for="score in currentConcert.scores" :key="'cc-'+score.id" 
                                class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-all flex items-center justify-between gap-6"
                            >
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-black text-white text-lg leading-tight italic truncate">{{ score.title }}</h3>
                                    <p class="text-blue-400/60 text-[9px] font-black uppercase tracking-widest mt-0.5 truncate">{{ score.composer }}</p>
                                </div>
                                
                                <div v-if="myRelevantParts[score.id]" class="flex items-center gap-6">
                                    <a 
                                        :href="route('muziek.download-bulk-score', { score: score.id, instrument_id: selectedInstrumentId })"
                                        class="hidden lg:flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 hover:border-white/20 hover:-translate-y-0.5 shadow-sm transition-all text-white/70 hover:text-white group flex-shrink-0"
                                        :title="__('Download alle partijen als ZIP')"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        <span class="text-[8px] font-black uppercase tracking-[0.2em] mt-0.5">{{ __('Alles downloaden') }}</span>
                                    </a>
                                    <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1">
                                        <div v-for="part in myRelevantParts[score.id]" :key="'rel-'+part.id" class="flex items-center justify-between gap-4 px-3 py-2 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-colors flex-shrink-0">
                                            <div class="hidden sm:flex flex-col text-right">
                                                <div class="text-[9px] font-black uppercase tracking-widest text-blue-200 opacity-80">{{ getPartDisplayName(part) }}</div>
                                                <div v-if="part.original_filename" class="text-[9px] font-bold text-blue-200/80 truncate max-w-[150px] mt-0.5">{{ part.original_filename }}</div>
                                            </div>
                                            <PartActionButtons 
                                                :download-url="getDownloadUrl(score.id, part.id)"
                                                view-class="!p-2 bg-yellow-400 text-blue-950 hover:bg-yellow-300 shadow-lg shadow-yellow-400/10"
                                                download-class="!p-2 bg-white text-blue-950 hover:bg-blue-50"
                                                @preview="openPreview(score, part)"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="opacity-30 italic text-[9px] font-black uppercase tracking-widest hidden md:block">
                                    {{ __('Geen partij') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 p-8 text-left">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-4">{{ __('Sorteer op instrument') }}</span>
                        <div class="flex flex-wrap gap-2">
                             <button
                                @click="selectedInstrumentId = null"
                                :class="[
                                    'px-6 py-3 rounded-2xl text-xs font-black transition-all active:scale-95 border-2',
                                    selectedInstrumentId === null
                                        ? 'bg-blue-950 border-blue-950 text-white shadow-xl shadow-blue-900/20'
                                        : 'bg-white border-gray-50 text-gray-400 hover:border-blue-200 hover:text-blue-950'
                                ]"
                            >
                                {{ __('Alle instrumenten') }}
                            </button>
                            <button
                                v-for="inst in userInstruments"
                                :key="inst.id"
                                @click="selectedInstrumentId = inst.id"
                                :class="[
                                    'px-6 py-3 rounded-2xl text-xs font-black transition-all active:scale-95 border-2',
                                    selectedInstrumentId === inst.id
                                        ? 'bg-blue-950 border-blue-950 text-white shadow-xl shadow-blue-900/20'
                                        : 'bg-white border-gray-50 text-gray-400 hover:border-blue-200 hover:text-blue-950'
                                ]"
                            >
                                {{ inst.name }}
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 text-left text-blue-950">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-4">{{ __('Zoeken in bibliotheek') }}</span>
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="__('Titel, componist...')"
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 placeholder:text-gray-300 focus:ring-2 focus:ring-yellow-400"
                        />
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-if="filteredScores.length === 0" class="py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                        <p class="text-gray-300 font-black italic">{{ __('Geen resultaten.') }}</p>
                    </div>

                    <div v-for="score in filteredScores" :key="score.id" class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden group hover:border-blue-200 transition-all">
                        <button @click="toggleScore(score.id)" class="w-full flex items-center justify-between p-10 text-left">
                            <div class="flex items-center gap-6 text-left">
                                <div class="hidden sm:flex w-14 h-14 bg-gray-50 rounded-2xl items-center justify-center text-gray-300 group-hover:bg-blue-950 group-hover:text-white transition-all">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div class="text-left">
                                    <div class="text-2xl font-black text-blue-950 italic">{{ score.title }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ score.composer }}</div>
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center transition-all" :class="{'bg-blue-950 text-white rotate-180': expandedScores.has(score.id)}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </button>

                        <div v-if="expandedScores.has(score.id)" class="px-10 pb-10 border-t border-gray-50 bg-gray-50/30">
                            <div class="flex items-center justify-between pt-10 pb-2">
                                <h4 class="text-sm font-black text-blue-950 italic uppercase tracking-widest">{{ __('Partijen') }}</h4>
                                <a 
                                    :href="route('muziek.download-bulk-score', { score: score.id, instrument_id: selectedInstrumentId })"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all text-blue-600 font-black uppercase tracking-widest text-[9px] active:scale-95 shadow-sm group"
                                    :title="__('Download geselecteerde partijen als ZIP')"
                                >
                                    <svg class="w-3.5 h-3.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ __('Alles downloaden') }}
                                </a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pt-4 text-left">
                                <div 
                                    v-for="part in getSortedParts(score.parts)"
                                    :key="'part-'+part.id"
                                    class="flex items-center justify-between p-5 rounded-2xl transition-all border"
                                    :class="isUserPart(part) ? 'bg-yellow-400 border-yellow-400 shadow-lg shadow-yellow-400/20' : 'bg-white border-gray-100 hover:border-blue-200 shadow-sm'"
                                >
                                    <div class="flex flex-col min-w-0 mr-2">
                                        <span class="text-[11px] font-black uppercase tracking-widest truncate" :class="isUserPart(part) ? 'text-blue-950' : 'text-gray-500'">
                                            {{ getPartDisplayName(part) }} {{ isUserPart(part) ? '★' : '' }}
                                        </span>
                                        <span v-if="part.original_filename" class="text-[9px] font-bold truncate mt-0.5" :class="isUserPart(part) ? 'text-blue-900/60' : 'text-gray-400'">
                                            {{ part.original_filename }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <PartActionButtons 
                                            :download-url="getDownloadUrl(score.id, part.id)"
                                            :view-class="['!p-1 hover:scale-110 transition-transform !shadow-none bg-transparent', isUserPart(part) ? 'text-blue-950/60 hover:text-blue-950' : 'text-gray-300 hover:text-blue-600']"
                                            :download-class="['!p-1 hover:scale-110 transition-transform !shadow-none bg-transparent', isUserPart(part) ? 'text-blue-950/60 hover:text-blue-950' : 'text-gray-300 hover:text-blue-600']"
                                            @preview="openPreview(score, part)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <PdfPreviewModal 
            :show="showPreview" 
            :url="previewUrl" 
            :title="previewTitle" 
            @close="closePreview" 
        />
    </AuthenticatedLayout>
</template>
