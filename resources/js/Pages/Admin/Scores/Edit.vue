<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ScorePartItem from '@/Components/ScorePartItem.vue';

const props = defineProps({
    score: Object,
    instruments: Array,
});

const instrumentsList = ref([...props.instruments]);

const form = useForm({
    title: props.score.title,
    composer: props.score.composer,
    arranger: props.score.arranger,
    removed_part_ids: [],
    new_parts: [], // { file, fileName, instrument }
});

const fileInput = ref(null);
const isUploading = ref(false);
const currentScoreId = ref(props.score.id);

function handleFileUpload(e) {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        let suggestedInstrument = '';
        const fileName = file.name.toLowerCase();
        
        props.instruments.forEach(inst => {
            if (fileName.includes(inst.name.toLowerCase())) suggestedInstrument = inst.name;
            inst.aliases.forEach(alias => {
                if (fileName.includes(alias.toLowerCase())) suggestedInstrument = inst.name;
            });
        });

        form.new_parts.push({
            pdf: file,
            filename: file.name,
            inferredTitle: suggestedInstrument,
            original_parsed_instrument: suggestedInstrument,
            instrument: suggestedInstrument,
            status: 'pending',
            statusMessage: '',
        });
    });
}

async function onAddInstrument(newName) {
    if (instrumentsList.value.find(i => i.name.toLowerCase() === newName.toLowerCase())) return;

    try {
        const response = await axios.post(route('beheer.api.instruments.store'), { name: newName });
        instrumentsList.value.push(response.data);
    } catch (error) {
        console.error("Fout bij toevoegen instrument:", error);
        instrumentsList.value.push({ id: 'new_' + Date.now(), name: newName });
    }
}

async function submit() {
    if (isUploading.value) return;
    
    isUploading.value = true;
    form.clearErrors();
    
    try {
        // Step 1: Update metadata & handle removals
        await axios.put(route('beheer.bladmuziek.update', props.score.id), {
            title: form.title,
            composer: form.composer,
            arranger: form.arranger,
            removed_part_ids: form.removed_part_ids,
        });
        
        // Step 2: Sequential Uploads for new parts
        for (let i = 0; i < form.new_parts.length; i++) {
            const part = form.new_parts[i];
            if (part.status === 'success') continue;
            
            part.status = 'uploading';
            part.statusMessage = 'Bezig met uploaden...';
            
            try {
                const formData = new FormData();
                formData.set('instrument', String(part.instrument));
                formData.set('pdf', part.pdf, part.filename);
                
                await axios.post(route('beheer.bladmuziek.partijen.store', props.score.id), formData);
                
                part.status = 'success';
                part.statusMessage = 'Opgeslagen';
            } catch (err) {
                part.status = 'error';
                let msg = 'Upload mislukt';
                if (err.response?.data?.errors) {
                    msg = Object.values(err.response.data.errors).flat().join(', ');
                } else if (err.response?.data?.message) {
                    msg = err.response.data.message;
                }
                part.statusMessage = msg;
            }
        }
        
        const anyFailed = form.new_parts.some(p => p.status === 'error');
        if (!anyFailed) {
            router.visit(route('beheer.bladmuziek.index'));
        }
    } catch (err) {
        console.error("Fout bij bijwerken muziekstuk:", err);
        if (err.response?.status === 422 && err.response.data.errors) {
            const errors = err.response.data.errors;
            Object.keys(errors).forEach(key => {
                form.setError(key, errors[key][0]);
            });
        } else {
            alert("Er is een onverwachte fout opgetreden bij het opslaan van de basisgegevens.");
        }
    } finally {
        isUploading.value = false;
    }
}

function toggleRemovePart(partId) {
    const idx = form.removed_part_ids.indexOf(partId);
    if (idx > -1) form.removed_part_ids.splice(idx, 1);
    else form.removed_part_ids.push(partId);
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="'Bewerken: ' + score.title" />

        <template #header>
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Stuk Bewerken</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ score.title }}</p>
                </div>
                <Link :href="route('beheer.bladmuziek.index')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 px-6 py-3 transition-colors">
                    Terug naar lijst
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <!-- Left: Metadata -->
                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">Compositie Gegevens</h3>
                            <form @submit.prevent="submit" class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Titel</label>
                                    <input v-model="form.title" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Componist</label>
                                    <input v-model="form.composer" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Arrangeur</label>
                                    <input v-model="form.arranger" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="w-full bg-blue-950 text-white py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/10 active:scale-95 disabled:opacity-50 mt-4"
                                >
                                    Basisgegevens Bijwerken
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Parts Management -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Upload New Parts -->
                        <div class="bg-blue-950 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden text-left">
                            <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                                <svg class="w-32 h-32" fill="white" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>
                            </div>
                            
                            <h3 class="text-xl font-black italic mb-8 relative z-10">Partijen Toevoegen</h3>
                            
                            <div class="p-8 border-2 border-dashed border-white/10 rounded-[2rem] bg-white/5 text-center mb-8 relative z-10">
                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf"
                                    @change="handleFileUpload"
                                    class="hidden"
                                    ref="fileInput"
                                />
                                <div @click="$refs.fileInput.click()" class="cursor-pointer group">
                                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-400 group-hover:text-blue-950 transition-all">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <p class="text-sm font-black italic">Klik om PDF's te selecteren</p>
                                </div>
                            </div>

                            <div v-if="form.new_parts.length > 0" class="space-y-4 mb-8 relative z-10 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin">
                                <ScorePartItem 
                                    v-for="(part, index) in form.new_parts" 
                                    :key="index"
                                    :part="part"
                                    :index="index"
                                    :instruments="instrumentsList"
                                    :modelValue="part.instrument"
                                    :status="part.status"
                                    :statusMessage="part.statusMessage"
                                    @update:modelValue="val => { part.instrument = val; }"
                                    :errorInstrument="form.errors[`new_parts.${index}.instrument`]"
                                    :errorPdf="form.errors[`new_parts.${index}.pdf`]"
                                    @remove="form.new_parts.splice(index, 1)"
                                    @add-instrument="onAddInstrument"
                                />
                            </div>

                            <div class="flex justify-end relative z-10">
                                    <button 
                                        @click="submit"
                                        :disabled="isUploading || (form.new_parts.length === 0 && form.removed_part_ids.length === 0 && !form.isDirty)"
                                        class="bg-yellow-400 text-blue-950 px-10 py-5 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-yellow-300 transition-all active:scale-95 shadow-xl shadow-yellow-400/20 disabled:opacity-50 flex items-center gap-2"
                                    >
                                        <svg v-if="isUploading" class="animate-spin h-4 w-4 text-blue-950" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ isUploading ? 'Laden...' : 'Wijzigingen Bevestigen' }}
                                    </button>
                            </div>
                        </div>

                        <!-- List of existing parts -->
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">Huidige Partijen</h3>
                            
                            <div v-if="score.parts.length === 0" class="py-20 text-center bg-gray-50/50 rounded-[2rem] border-2 border-dashed border-gray-100">
                                <p class="text-gray-300 font-black italic">Nog geen partijen geüpload.</p>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="part in score.parts" :key="part.id" class="flex items-center justify-between p-5 bg-gray-50/50 rounded-2xl border border-gray-100 group transition-all" :class="{'opacity-50 grayscale border-red-200 bg-red-50/30': form.removed_part_ids.includes(part.id)}">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shadow-inner">
                                            PDF
                                        </div>
                                        <div>
                                            <div class="font-black text-blue-950 text-sm leading-tight italic">{{ part.instrument?.name || part.instrument }}</div>
                                            <div v-if="form.removed_part_ids.includes(part.id)" class="text-[9px] font-black uppercase text-red-500 mt-1">Gemarkeerd voor verwijdering</div>
                                            <a v-else :href="part.file_path" target="_blank" class="text-[9px] font-black uppercase text-blue-400 hover:text-blue-950 transition-colors">Bestand bekijken</a>
                                        </div>
                                    </div>
                                    <button @click="toggleRemovePart(part.id)" class="p-3 transition-colors" :class="form.removed_part_ids.includes(part.id) ? 'text-blue-600' : 'text-gray-300 hover:text-red-500'">
                                        <svg v-if="form.removed_part_ids.includes(part.id)" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4l16 16m0-16L4 20" /></svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
