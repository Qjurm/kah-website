<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    userInstruments: Array,
    primaryInstrumentId: [Number, String],
    allInstruments: Array,
    nextConcert: Object,
    myParts: Array,
});

const form = useForm({
    instrument_ids: props.userInstruments,
    primary_instrument_id: props.primaryInstrumentId,
});

const instrumentFamilies = [
    { name: 'Hout', keywords: ['fluit', 'piccolo', 'hobo', 'fagot', 'klarinet', 'saxofoon'] },
    { name: 'Koper', keywords: ['trompet', 'hoorn', 'trombone', 'euphonium', 'tuba', 'cornet', 'bugel'] },
    { name: 'Slagwerk', keywords: ['pauken', 'percussie', 'slagwerk', 'mallets', 'drums'] },
];

const groupedInstruments = computed(() => {
    const groups = {
        'Hout': [],
        'Koper': [],
        'Slagwerk': [],
        'Overig': []
    };

    props.allInstruments.forEach(inst => {
        const name = inst.name.toLowerCase();
        let found = false;
        for (const family of instrumentFamilies) {
            if (family.keywords.some(k => name.includes(k))) {
                groups[family.name].push(inst);
                found = true;
                break;
            }
        }
        if (!found) groups['Overig'].push(inst);
    });

    return groups;
});

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
                    
                    <!-- Left: Instrument Selection (Larger) -->
                    <div class="lg:col-span-3 space-y-10 text-left">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">{{ __('Kies je Instrumenten') }}</h3>
                            
                            <div class="space-y-12">
                                <div v-for="(instruments, family) in groupedInstruments" :key="family">
                                    <div v-if="instruments.length > 0">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="h-px bg-gray-100 flex-1"></div>
                                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-300">{{ family }}</span>
                                            <div class="h-px bg-gray-100 flex-1"></div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div 
                                                v-for="inst in instruments" 
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

                    <!-- Right: Repertoire Preview (Smaller/Sidebar) -->
                    <div class="lg:col-span-2 space-y-10 text-left">
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

                        <div v-if="myParts.length > 0" class="space-y-4">
                                <div v-for="part in myParts" :key="part.part_id" class="p-6 bg-white rounded-2xl border border-blue-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-blue-50 text-blue-600">{{ part.instrument }}</span>
                                    </div>
                                    <div class="font-black text-blue-950 italic leading-tight text-sm">{{ part.score_title }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ part.score_composer }}</div>
                                </div>
                            </div>
                            <div v-else class="py-20 text-center opacity-30 italic">
                                <p class="font-black text-blue-900">{{ __('Kies een instrument om de partijen voor het volgende concert te zien.') }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
