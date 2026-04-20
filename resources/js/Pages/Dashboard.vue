<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PdfPreviewModal from '@/Components/PdfPreviewModal.vue';

const props = defineProps({
    primaryInstrument: Object,
    nextConcert: Object,
    myParts: Array,
    recentStats: Object,
    allInstruments: Array,
});

const previewUrl = ref('');
const previewTitle = ref('');
const showPreview = ref(false);

function openPreview(part) {
    previewUrl.value = route('muziek.view', { score: part.score_id, part: part.part_id });
    previewTitle.value = `${part.score_title} - ${part.instrument}`;
    showPreview.value = true;
}

function closePreview() {
    showPreview.value = false;
    previewUrl.value = '';
}

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
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">{{ __('Mijn Muziekdashboard') }}</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ __('Jouw instrumenten en komende optredens') }}</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">
                    
                    <!-- Left: Next Concert (Large) -->
                    <div class="lg:col-span-3 space-y-12">
                        <div class="space-y-6 text-left">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-950 rounded-2xl flex items-center justify-center text-yellow-400 shadow-xl shadow-blue-950/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0 -2-2H5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-blue-950 uppercase italic tracking-tight">{{ __('Eerstvolgende Concert') }}</h3>
                                    <p class="text-xs font-bold uppercase tracking-widest text-blue-900/40">{{ __('Bereid je voor op het volgende optreden') }}</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-[3rem] p-8 sm:p-12 shadow-2xl shadow-blue-950/5 border border-blue-950/5 relative overflow-hidden group">
                                <div class="absolute top-0 right-0 w-48 h-48 bg-yellow-400/10 rounded-bl-full -mr-24 -mt-24 transition-all group-hover:scale-110"></div>
                                
                                <div class="relative space-y-10">
                                    <div v-if="nextConcert">
                                        <h4 class="text-4xl sm:text-6xl font-black text-blue-950 uppercase italic tracking-tighter mb-6 leading-none">{{ nextConcert.title }}</h4>
                                        <div class="flex flex-wrap gap-8">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-yellow-400/20 text-yellow-600 rounded-xl">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg>
                                                </div>
                                                <span class="font-black text-blue-950 italic uppercase tracking-widest text-xs">{{ formatDate(nextConcert.date) }}</span>
                                            </div>
                                            <div v-if="nextConcert.location" class="flex items-center gap-3">
                                                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z" /></svg>
                                                </div>
                                                <span class="font-black text-blue-950 italic uppercase tracking-widest text-xs">{{ nextConcert.location }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="py-10 text-center opacity-30 italic">
                                        <p class="font-black text-blue-900">{{ __('Geen geplande concerten gevonden.') }}</p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-center gap-6 pt-4">
                                        <Link 
                                            :href="route('muziek.index')"
                                            class="w-full sm:w-auto bg-blue-950 text-white px-10 py-5 rounded-2xl font-black uppercase italic tracking-widest hover:bg-blue-900 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-950/20"
                                        >
                                            {{ __('Alle muziek') }}
                                        </Link>
                                        <div v-if="nextConcert" class="flex items-center gap-3 px-6 py-4 bg-gray-50 rounded-2xl">
                                            <span class="text-3xl font-black text-blue-950 leading-none">{{ getDaysUntil(nextConcert.date) }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-300 leading-tight">{{ __('dagen tot') }}<br>{{ __('optreden') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Info (My Instrument) -->
                        <div v-if="primaryInstrument" class="bg-blue-950 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden text-left group">
                            <div class="absolute top-0 right-0 p-10 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                            </div>
                            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-8">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400 mb-3 opacity-60">{{ __('Mijn Hoofdinstrument') }}</div>
                                    <h5 class="text-3xl font-black italic uppercase tracking-tighter leading-none">{{ primaryInstrument.name }}</h5>
                                </div>
                                <Link :href="route('mijn-instrumenten.edit')" class="w-full sm:w-auto text-center bg-white/10 text-white px-8 py-3 rounded-xl hover:bg-yellow-400 hover:text-blue-950 transition-all font-black text-[10px] uppercase tracking-widest border border-white/10">
                                    {{ __('Instrumenten Bewerken') }} &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Right: My Parts (Scrollable Sidebar) -->
                    <div class="lg:col-span-2 space-y-6 text-left h-full">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-950 rounded-2xl flex items-center justify-center text-yellow-400 shadow-xl shadow-blue-950/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-blue-950 uppercase italic tracking-tight">{{ __('Mijn Partijen') }}</h3>
                                <p class="text-xs font-bold uppercase tracking-widest text-blue-900/40">{{ nextConcert ? __('Voor het eerstvolgende concert') : __('Nog geen repertoire beschikbaar') }}</p>
                            </div>
                        </div>

                        <div class="bg-blue-50/50 rounded-[3rem] p-2 border border-blue-950/5 shadow-inner">
                            <div class="max-h-[600px] overflow-y-auto pr-2 custom-scrollbar space-y-4 p-4 lg:p-6">
                                <div v-if="myParts.length" v-for="part in myParts" :key="part.part_id" class="group bg-white p-6 rounded-[2rem] border border-blue-950/5 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all hover:-translate-y-1">
                                    <div class="flex flex-col gap-4">
                                        <div class="min-w-0">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 mb-2 block">{{ part.instrument }}</span>
                                            <h5 class="text-lg font-black text-blue-950 italic uppercase tracking-tighter leading-tight mb-1 truncate">{{ part.score_title }}</h5>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ part.score_composer }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 pt-2">
                                            <button @click="openPreview(part)" class="flex-1 bg-blue-50 text-blue-600 px-4 py-3 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-widest shadow-sm">
                                                {{ __('Bekijk') }}
                                            </button>
                                            <a 
                                                :href="route('muziek.download', { score: part.score_id, part: part.part_id })" 
                                                class="flex-1 bg-blue-950 text-white px-4 py-3 rounded-xl flex items-center justify-center hover:bg-black transition-all shadow-lg shadow-blue-950/10 font-black text-[9px] uppercase tracking-widest"
                                            >
                                                {{ __('Download') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="py-32 text-center flex flex-col items-center gap-8">
                                    <div class="w-20 h-20 bg-blue-100 rounded-3xl flex items-center justify-center text-blue-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div class="space-y-3">
                                        <p class="font-black text-xl text-blue-950 italic uppercase tracking-tighter leading-none">{{ __('Geen Partijen Gevonden') }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] leading-relaxed max-w-[200px] mx-auto">{{ __('Zorg dat je instrumenten correct zijn ingesteld.') }}</p>
                                    </div>
                                    <Link :href="route('mijn-instrumenten.edit')" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-950 transition-colors bg-blue-50 px-6 py-3 rounded-xl">
                                        {{ __('Instellen') }} &rarr;
                                    </Link>
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

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(30, 58, 138, 0.1);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(30, 58, 138, 0.2);
}
</style>
