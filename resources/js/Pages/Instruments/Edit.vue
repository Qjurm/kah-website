<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PdfPreviewModal from '@/Components/PdfPreviewModal.vue';
import MusicPartCard from '@/Components/MusicPartCard.vue';

const props = defineProps({
    userInstruments: Array,
    primaryInstrumentId: [Number, String],
    allInstruments: Array,
    instrumentSections: Array,
    nextConcert: Object,
    myParts: Array,
});

const form = useForm({
    instrument_ids: props.userInstruments,
    primary_instrument_id: props.primaryInstrumentId,
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

function toggleInstrument(id) {
    const idx = form.instrument_ids.indexOf(id);
    if (idx > -1) {
        form.instrument_ids.splice(idx, 1);
        if (form.primary_instrument_id === id) {
            form.primary_instrument_id = null;
        }
    } else {
        form.instrument_ids.push(id);
        if (!form.primary_instrument_id) {
            form.primary_instrument_id = id;
        }
    }
    submit();
}

function setPrimary(id) {
    form.primary_instrument_id = id;
    submit();
}

function submit() {
    form.post(route('mijn-instrumenten.update'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="__('Mijn Instrumenten')" />

        <template #header>
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">{{ __('Welk instrument speel je?') }}</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ __('Kies je instrumenten voor de juiste bladmuziek') }}</p>
                </div>
                <div v-if="form.processing" class="flex items-center gap-2 text-blue-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-900/40 italic">{{ __('Opslaan...') }}</span>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
                    
                    <!-- Left: Instrument Selection -->
                    <div :class="[myParts.length > 0 ? 'lg:col-span-3' : 'lg:col-span-5', 'space-y-10 text-left transition-all duration-500']">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">{{ __('Kies je Instrumenten') }}</h3>
                            
                            <div class="space-y-12">
                                <div v-for="section in instrumentSections" :key="section.id">
                                    <div v-if="section.instruments && section.instruments.length > 0">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="h-px bg-gray-100 flex-1"></div>
                                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-300">{{ section.name }}</span>
                                            <div class="h-px bg-gray-100 flex-1"></div>
                                        </div>
                                        
                                        <div :class="['grid gap-6', myParts.length > 0 ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4']">
                                            <div 
                                                v-for="inst in section.instruments" 
                                                :key="inst.id"
                                                @click="toggleInstrument(inst.id)"
                                                class="relative p-6 rounded-2xl border-2 transition-all cursor-pointer group active:scale-95"
                                                :class="form.instrument_ids.includes(inst.id) 
                                                    ? 'bg-blue-950 border-blue-950 text-white shadow-xl shadow-blue-900/20' 
                                                    : 'bg-white border-gray-50 hover:border-blue-100'"
                                            >
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all" :class="form.instrument_ids.includes(inst.id) ? 'bg-yellow-400 text-blue-950' : 'bg-gray-100 text-gray-300'">
                                                        <svg v-if="form.instrument_ids.includes(inst.id)" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12M6 12h12"/></svg>
                                                    </div>
                                                    
                                                    <button 
                                                        v-if="form.instrument_ids.includes(inst.id)"
                                                        @click.stop="setPrimary(inst.id)"
                                                        class="px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest transition-all"
                                                        :class="form.primary_instrument_id === inst.id 
                                                            ? 'bg-yellow-400 text-blue-950' 
                                                            : 'bg-white/10 text-white/40 hover:text-white hover:bg-white/20'"
                                                    >
                                                        {{ form.primary_instrument_id === inst.id ? __('Primair') : __('Maak Primair') }}
                                                    </button>
                                                </div>
                                                <div class="font-black text-xs uppercase tracking-[0.15em] leading-tight">{{ inst.name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Repertoire Preview -->
                    <div v-if="myParts.length > 0" class="lg:col-span-2 space-y-10 text-left">
                        <div v-if="nextConcert" class="bg-blue-50 rounded-[2.5rem] p-10 border border-blue-100 h-full">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-10 h-10 bg-blue-950 rounded-2xl text-white flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-blue-950 italic leading-tight">{{ __('Jouw Repertoire') }}</h3>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-900/40">{{ __('Gevonden voor') }} {{ nextConcert.title }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <MusicPartCard
                                    v-for="part in myParts" 
                                    :key="part.part_id"
                                    :part="part"
                                    layout="horizontal"
                                    :download-url="route('muziek.download', { score: part.score_id, part: part.part_id })"
                                    view-btn-class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white"
                                    download-btn-class="bg-blue-950 text-white hover:bg-black shadow-lg shadow-blue-950/10"
                                    @preview="openPreview"
                                />
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
