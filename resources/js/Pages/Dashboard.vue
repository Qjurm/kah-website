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
                <h2 class="text-xl font-black leading-tight text-blue-950">{{ __('Mijn Muziekdashboard') }}</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ __('Jouw instrumenten en komende optredens') }}</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div v-if="myParts.length > 0 || nextConcert" class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12 items-start">
                    <!-- Next Concert Card (Large) -->
                    <div class="lg:col-span-2 bg-white rounded-[3rem] border border-gray-100 p-8 sm:p-12 shadow-sm text-left">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-blue-950 rounded-2xl text-white flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 0 00-2 2v12a2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-black text-blue-950 italic">{{ __('Volgend Optreden') }}</h2>
                        </div>

                        <div v-if="nextConcert" class="bg-gray-50/50 rounded-[2.5rem] p-10 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-10">
                            <div class="min-w-0">
                                <h3 class="text-4xl sm:text-5xl font-black text-blue-950 mb-6 italic leading-tight uppercase tracking-tight">{{ nextConcert.title }}</h3>
                                <div class="flex flex-wrap gap-8 text-blue-900/40 text-[11px] font-black uppercase tracking-widest">
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
                            <div class="px-10 py-8 bg-white rounded-3xl shadow-xl shadow-blue-900/5 text-center flex-shrink-0">
                                <div class="text-6xl font-black text-blue-950 leading-none mb-1">{{ getDaysUntil(nextConcert.date) }}</div>
                                <div class="text-[10px] uppercase font-black tracking-[0.2em] text-blue-300">{{ __('dagen') }}</div>
                            </div>
                        </div>

                        <div v-else class="py-20 text-center bg-gray-50/50 rounded-[2rem] border-2 border-dashed border-gray-100">
                            <p class="text-gray-300 font-black italic">{{ __('Geen concerten in de agenda.') }}</p>
                        </div>
                    </div>

                    <!-- My Parts Card (Next to Concert) -->
                    <div class="bg-yellow-400 rounded-[3rem] p-10 shadow-2xl shadow-yellow-400/20 text-blue-950 text-left h-full flex flex-col">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-blue-950 rounded-2xl text-white flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black italic">{{ __('Mijn Partijen') }}</h3>
                        </div>

                        <div v-if="myParts.length === 0" class="flex-1 flex flex-col items-center justify-center py-10 opacity-30 italic">
                            <p class="font-black text-blue-900/60">{{ __('Geen partijen voor jou.') }}</p>
                        </div>

                        <div v-else class="space-y-4 mb-8">
                            <div v-for="part in myParts" :key="part.part_id" class="p-5 bg-white/20 rounded-2xl border border-white/20 hover:bg-white/40 transition-all flex items-center justify-between group">
                                <div class="min-w-0 mr-4">
                                    <div class="font-black text-blue-950 text-sm sm:text-base leading-tight mb-1 truncate italic">{{ part.score_title }}</div>
                                    <div class="flex flex-col gap-0.5">
                                        <div class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-blue-950/60 leading-none">
                                            {{ part.instrument }}
                                        </div>
                                        <div v-if="part.original_filename" class="text-[8px] font-bold text-blue-950/30 truncate max-w-[150px] sm:max-w-xs">
                                            {{ part.original_filename }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button @click="openPreview(part)" class="p-3 sm:p-4 bg-blue-950 text-white rounded-xl shadow-lg hover:scale-110 transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('muziek.index')" class="mt-auto w-full flex items-center justify-center gap-3 bg-blue-950 text-white text-[10px] font-black uppercase tracking-widest py-4 rounded-2xl hover:bg-black transition-all active:scale-95 shadow-xl shadow-blue-950/20">
                            {{ __('Alle muziek') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </Link>
                    </div>
                </div>

                <!-- Secondary Dashboard Items -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <!-- Stats Section (Moved left) -->
                    <div class="lg:col-span-1 space-y-6 text-left">
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm flex items-center gap-6">
                            <div class="w-14 h-14 bg-blue-50 rounded-2xl text-blue-600 flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12M6 12h12" /></svg>
                            </div>
                            <div>
                                <div class="text-[9px] font-black uppercase tracking-widest text-gray-300">{{ __('Nieuw Repertoire') }}</div>
                                <div class="text-3xl font-black text-blue-950 leading-tight">{{ recentStats.newScores }}</div>
                            </div>
                        </div>
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm flex items-center gap-6">
                            <div class="w-14 h-14 bg-yellow-400/10 rounded-2xl text-yellow-600 flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 0 00-2-2H5a2 0 00-2 2v12a2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <div class="text-[9px] font-black uppercase tracking-widest text-gray-300">{{ __('Projecten') }}</div>
                                <div class="text-3xl font-black text-blue-950 leading-tight">{{ recentStats.upcomingConcerts }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Instrument Management (Tiny side card now) -->
                    <div v-if="primaryInstrument" class="lg:col-span-1 bg-blue-950 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden text-left flex flex-col justify-center group">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                        </div>
                        <div>
                            <div class="text-[8px] font-black uppercase tracking-[0.3em] text-yellow-400 mb-2 opacity-60">{{ __('Mijn Instrument') }}</div>
                            <h1 class="text-xl font-black italic tracking-tight mb-4 truncate">{{ primaryInstrument.name }}</h1>
                            <Link :href="route('mijn-instrumenten.edit')" class="inline-block bg-white/10 text-white px-4 py-2 rounded-xl hover:bg-yellow-400 hover:text-blue-950 transition-all font-black text-[8px] uppercase tracking-widest border border-white/10">
                                {{ __('Bewerken') }}
                            </Link>
                        </div>
                    </div>

                    <!-- Empty space or third card if needed -->
                    <div class="lg:col-span-1 hidden lg:block"></div>

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
