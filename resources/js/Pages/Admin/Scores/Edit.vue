<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SectionCard from '@/Components/SectionCard.vue';
import ScorePartItem from '@/Components/ScorePartItem.vue';
import PdfPreviewModal from '@/Components/PdfPreviewModal.vue';

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
    new_parts: [], 
});

const fileInputSingle = ref(null);
const fileInputFolder = ref(null);
const isDragging = ref(false);
const isUploading = ref(false);

const previewUrl = ref('');
const previewTitle = ref('');
const showPreview = ref(false);

function triggerFileInputSingle() {
    fileInputSingle.value.click();
}

function triggerFileInputFolder() {
    fileInputFolder.value.click();
}

function handleFileUpload(event) {
    const files = Array.from(event.target.files);
    processFiles(files);
    if (fileInputSingle.value) fileInputSingle.value.value = '';
    if (fileInputFolder.value) fileInputFolder.value.value = '';
}

async function handleDrop(event) {
    isDragging.value = false;
    const files = Array.from(event.dataTransfer?.files || []);
    processFiles(files);
}

function processFiles(files) {
    files.forEach(file => {
        if (file.type !== 'application/pdf') return;

        let suggestedInstrument = '';
        const fileName = file.name.toLowerCase();
        
        instrumentsList.value.forEach(inst => {
            if (fileName.includes(inst.name.toLowerCase())) suggestedInstrument = inst.name;
            if (inst.aliases) {
                inst.aliases.forEach(alias => {
                    if (fileName.includes(alias.toLowerCase())) suggestedInstrument = inst.name;
                });
            }
        });

        form.new_parts.push({
            pdf: file,
            filename: file.name,
            inferredTitle: suggestedInstrument,
            instrument: suggestedInstrument,
            status: 'pending',
            statusMessage: '',
        });
    });
}

function toggleRemovePart(partId) {
    const idx = form.removed_part_ids.indexOf(partId);
    if (idx > -1) form.removed_part_ids.splice(idx, 1);
    else form.removed_part_ids.push(partId);
}

function openPreview(url, title) {
    previewUrl.value = url;
    previewTitle.value = title;
    showPreview.value = true;
}

function openExistingPreview(part) {
    const url = route('beheer.bladmuziek.partijen.view', { score: props.score.id, part: part.id });
    openPreview(url, part.instrument || 'Partij');
}

function closePreview() {
    showPreview.value = false;
    if (previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value);
    }
    previewUrl.value = '';
}

async function onAddInstrument(newName) {
    if (instrumentsList.value.find(i => i.name.toLowerCase() === newName.toLowerCase())) return;
    try {
        const response = await axios.post(route('beheer.api.instruments.store'), { name: newName });
        instrumentsList.value.push(response.data);
    } catch (error) {
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
                part.statusMessage = err.response?.data?.message || 'Upload mislukt';
            }
        }
        
        if (!form.new_parts.some(p => p.status === 'error')) {
            router.visit(route('beheer.bladmuziek.index'));
        }
    } catch (err) {
        if (err.response?.status === 422 && err.response.data.errors) {
            const errors = err.response.data.errors;
            Object.keys(errors).forEach(key => form.setError(key, errors[key][0]));
        } else {
            alert("Er is een fout opgetreden bij het bijwerken.");
        }
    } finally {
        isUploading.value = false;
    }
}
</script>

<template>
    <Head :title="'Bewerken: ' + score.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1 text-left">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Stuk Bewerken</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ score.title }}</p>
                </div>
                <Link :href="route('beheer.bladmuziek.index')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 px-6 py-3 transition-colors">
                    &larr; Terug naar overzicht
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Metadata Section -->
                <SectionCard title="Algemene Informatie" class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="title" value="Titel *" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="composer" value="Componist *" />
                            <TextInput id="composer" type="text" class="mt-1 block w-full" v-model="form.composer" required />
                            <InputError class="mt-2" :message="form.errors.composer" />
                        </div>
                        <div>
                            <InputLabel for="arranger" value="Arrangeur" />
                            <TextInput id="arranger" type="text" class="mt-1 block w-full" v-model="form.arranger" />
                            <InputError class="mt-2" :message="form.errors.arranger" />
                        </div>
                    </div>
                </SectionCard>

                <!-- Existing Parts -->
                <SectionCard title="Huidige Partijen" class="mb-8">
                    <div v-if="score.parts.length === 0" class="py-12 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-100">
                        <p class="text-gray-300 font-black italic">Geen partijen gevonden.</p>
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="part in score.parts" :key="part.id" 
                            class="flex items-center justify-between p-4 rounded-2xl border transition-all"
                            :class="form.removed_part_ids.includes(part.id) ? 'bg-red-50 border-red-200' : 'bg-white border-gray-100 hover:border-blue-200 shadow-sm'"
                        >
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center font-black text-[10px] shadow-inner"
                                     :class="form.removed_part_ids.includes(part.id) ? 'bg-red-100 text-red-600' : 'bg-blue-50 text-blue-600'">
                                    PDF
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-blue-950 truncate italic" :class="{'line-through opacity-40': form.removed_part_ids.includes(part.id)}">{{ part.instrument?.name || part.instrument }}</div>
                                    <div v-if="form.removed_part_ids.includes(part.id)" class="text-[9px] font-black uppercase text-red-500">Gemarkeerd om te wissen</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button v-if="!form.removed_part_ids.includes(part.id)" type="button" @click="openExistingPreview(part)" class="p-3 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                                <button type="button" @click="toggleRemovePart(part.id)" class="p-3 transition-all active:scale-90 rounded-xl" :class="form.removed_part_ids.includes(part.id) ? 'text-blue-600 hover:bg-blue-100' : 'text-gray-300 hover:text-red-500 hover:bg-red-50'">
                                    <svg v-if="form.removed_part_ids.includes(part.id)" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </SectionCard>

                <!-- New Parts Section -->
                <SectionCard title="Nieuwe Partijen">
                    <template #header>
                        <div class="flex gap-2">
                            <input type="file" ref="fileInputSingle" class="hidden" multiple accept=".pdf" @change="handleFileUpload" />
                            <input type="file" ref="fileInputFolder" class="hidden" webkitdirectory directory multiple @change="handleFileUpload" />
                            <button type="button" @click="triggerFileInputFolder" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Map toevoegen</button>
                            <button type="button" @click="triggerFileInputSingle" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">PDF(s) toevoegen</button>
                        </div>
                    </template>

                    <div v-if="form.new_parts.length === 0" 
                        class="text-center py-16 rounded-2xl border-2 border-dashed transition-colors"
                        :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                    >
                        <svg class="mx-auto h-12 w-12 text-gray-400 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <p class="mt-4 text-xs font-black uppercase tracking-widest text-gray-400">Sleep nieuwe PDF's hierin om toe te voegen</p>
                    </div>

                    <div v-else class="space-y-4">
                        <ScorePartItem 
                            v-for="(part, index) in form.new_parts" 
                            :key="'new-'+index"
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
                            @openPreview="openPreview"
                        />
                    </div>

                    <!-- Bottom Actions -->
                    <div class="flex justify-end gap-3 mt-10 pt-8 border-t border-gray-100">
                        <Link :href="route('beheer.bladmuziek.index')" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-colors">Annuleren</Link>
                        <button type="button" 
                            class="bg-blue-950 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/10 flex items-center gap-2 disabled:opacity-50" 
                            @click="submit" 
                            :disabled="isUploading || (form.new_parts.length === 0 && form.removed_part_ids.length === 0 && !form.isDirty)"
                        >
                            <svg v-if="isUploading" class="animate-spin h-3 w-3 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>{{ isUploading ? 'Laden...' : 'Wijzigingen Opslaan' }}</span>
                        </button>
                    </div>
                </SectionCard>

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
