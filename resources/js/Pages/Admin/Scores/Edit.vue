<script setup>
import { ref, onMounted } from 'vue';
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
    arranger: props.score.arranger || '',
    removed_part_ids: [],
    existing_parts: props.score.parts.map(p => ({
        id: p.id,
        score_id: p.score_id,
        instrument: p.instrument?.name || p.instrument,
        original_filename: p.original_filename,
        pdf_path: p.pdf_path,
        pdf: null, // For replacement
        status: 'pending',
        statusMessage: '',
    })),
    new_parts: [], 
});

const fileInputSingle = ref(null);
const fileInputFolder = ref(null);
const fileInputReplace = ref(null);
const replacingPartIndex = ref(null);
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

function triggerReplace(index) {
    replacingPartIndex.value = index;
    fileInputReplace.value.click();
}

function handleReplaceUpload(event) {
    const file = event.target.files[0];
    if (file && file.type === 'application/pdf' && replacingPartIndex.value !== null) {
        form.existing_parts[replacingPartIndex.value].pdf = file;
        form.existing_parts[replacingPartIndex.value].original_filename = file.name;
        form.existing_parts[replacingPartIndex.value].status = 'pending';
    }
    replacingPartIndex.value = null;
    fileInputReplace.value.value = '';
}

function handleFileUpload(event) {
    const files = Array.from(event.target.files);
    processFiles(files);
    if (fileInputSingle.value) fileInputSingle.value.value = '';
    if (fileInputFolder.value) fileInputFolder.value.value = '';
}

async function handleDrop(event) {
    isDragging.value = false;
    const items = event.dataTransfer?.items;
    if (items) {
        const filesToProcess = [];
        const promises = [];
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            if (item.kind === 'file') {
                const entry = item.webkitGetAsEntry ? item.webkitGetAsEntry() : null;
                if (entry) promises.push(traverseFileTree(entry, filesToProcess));
                else { const f = item.getAsFile(); if (f) filesToProcess.push(f); }
            }
        }
        await Promise.all(promises);
        processFiles(filesToProcess);
    } else {
        const files = Array.from(event.dataTransfer?.files || []);
        processFiles(files);
    }
}

function traverseFileTree(entry, fileList) {
    return new Promise((resolve) => {
        if (entry.isFile) {
            entry.file((file) => { fileList.push(file); resolve(); });
        } else if (entry.isDirectory) {
            const dirReader = entry.createReader();
            dirReader.readEntries(async (entries) => {
                const promises = entries.map(e => traverseFileTree(e, fileList));
                await Promise.all(promises);
                resolve();
            });
        } else resolve();
    });
}

function processFiles(files) {
    files.forEach(file => {
        if (file.type !== 'application/pdf') return;

        // Duplicate prevention
        const isDuplicate = [...form.existing_parts, ...form.new_parts].some(p => p.original_filename === file.name || p.filename === file.name);
        if (isDuplicate) {
            console.warn(`Bestand ${file.name} is al toegevoegd.`);
            return;
        }

        const nameWithoutExt = file.name.replace(/\.pdf$/i, '');
        let inferredInstrumentStr = '';
        const dashParts = nameWithoutExt.split(/ - | _ /);
        const underscoreParts = nameWithoutExt.split('_');
        
        if (dashParts.length >= 2) inferredInstrumentStr = dashParts[dashParts.length - 1].trim();
        else if (underscoreParts.length >= 2) inferredInstrumentStr = underscoreParts[underscoreParts.length - 1].trim();
        else inferredInstrumentStr = nameWithoutExt.trim();

        let matchedInstrument = '';
        if (inferredInstrumentStr) {
            const lowerInferred = inferredInstrumentStr.toLowerCase();
            const strippedInferred = lowerInferred.replace(/\s*\d+$/, '');
            let match = instrumentsList.value.find(i => i.name.toLowerCase() === lowerInferred);
            if (!match) match = instrumentsList.value.find(i => lowerInferred.includes(i.name.toLowerCase()));
            if (!match) match = instrumentsList.value.find(i => i.name.toLowerCase().includes(lowerInferred));
            if (match) matchedInstrument = match.name;
        }

        form.new_parts.push({
            pdf: file,
            filename: file.name,
            original_filename: file.name,
            inferredTitle: matchedInstrument,
            instrument: matchedInstrument,
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

function closePreview() {
    showPreview.value = false;
    if (previewUrl.value.startsWith('blob:')) URL.revokeObjectURL(previewUrl.value);
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
        // Step 1: Update metadata & handle existing parts
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('title', form.title);
        formData.append('composer', form.composer);
        formData.append('arranger', form.arranger);
        
        form.removed_part_ids.forEach((id, i) => formData.append(`removed_part_ids[${i}]`, id));
        
        form.existing_parts.forEach((part, i) => {
            if (form.removed_part_ids.includes(part.id)) return;
            formData.append(`existing_parts[${i}][id]`, part.id);
            formData.append(`existing_parts[${i}][instrument]`, part.instrument);
            if (part.pdf) formData.append(`existing_parts[${i}][pdf]`, part.pdf);
        });

        await axios.post(route('beheer.bladmuziek.update', props.score.id), formData);
        
        // Step 2: Sequential Uploads for NEW parts
        for (let i = 0; i < form.new_parts.length; i++) {
            const part = form.new_parts[i];
            if (part.status === 'success') continue;
            part.status = 'uploading';
            part.statusMessage = 'Uploaden...';
            
            try {
                const newPartData = new FormData();
                newPartData.set('instrument', String(part.instrument));
                newPartData.set('pdf', part.pdf, part.filename);
                await axios.post(route('beheer.bladmuziek.partijen.store', props.score.id), newPartData);
                part.status = 'success';
                part.statusMessage = 'Opgeslagen';
            } catch (err) {
                part.status = 'error';
                part.statusMessage = err.response?.data?.message || 'Fout';
            }
        }
        
        if (!form.new_parts.some(p => p.status === 'error')) {
            router.visit(route('beheer.bladmuziek.index'));
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const errors = err.response.data.errors;
            Object.keys(errors).forEach(key => form.setError(key, errors[key][0]));
        } else {
            alert("Er is een fout opgetreden.");
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
                
                <!-- Main Form -->
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

                <!-- Parts Management -->
                <SectionCard title="Partijen Beheren">
                    <template #header>
                        <div class="flex gap-2">
                            <input type="file" ref="fileInputSingle" class="hidden" multiple accept=".pdf" @change="handleFileUpload" />
                            <input type="file" ref="fileInputFolder" class="hidden" webkitdirectory directory multiple @change="handleFileUpload" />
                            <input type="file" ref="fileInputReplace" class="hidden" accept=".pdf" @change="handleReplaceUpload" />
                            
                            <button type="button" @click="triggerFileInputFolder" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">Map toevoegen</button>
                            <button type="button" @click="triggerFileInputSingle" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">PDF(s) toevoegen</button>
                        </div>
                    </template>

                    <div class="space-y-8 mt-6">
                        <!-- Existing Parts -->
                        <div v-if="form.existing_parts.length > 0">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Huidige Partijen</h4>
                            <div class="space-y-3">
                                <template v-for="(part, index) in form.existing_parts" :key="part.id">
                                    <div v-if="!form.removed_part_ids.includes(part.id)" class="relative">
                                        <ScorePartItem 
                                            :part="{...part, filename: part.original_filename}"
                                            :index="index"
                                            :instruments="instrumentsList"
                                            :modelValue="part.instrument"
                                            :status="part.status"
                                            :statusMessage="part.statusMessage"
                                            @update:modelValue="val => { part.instrument = val; }"
                                            @remove="toggleRemovePart(part.id)"
                                            @replace="triggerReplace(index)"
                                            @add-instrument="onAddInstrument"
                                            @openPreview="openPreview"
                                        />
                                        <div v-if="part.pdf" class="absolute -top-2 -right-2 bg-yellow-400 text-blue-950 text-[8px] font-black px-2 py-1 rounded-full shadow-sm z-10 border border-white">
                                            GEWIJZIGD
                                        </div>
                                    </div>
                                    <div v-else class="flex items-center justify-between p-4 bg-red-50/50 border border-red-100 rounded-xl opacity-60">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </div>
                                            <div class="text-xs font-bold text-red-900 italic">Verwijderd: {{ part.instrument }}</div>
                                        </div>
                                        <button @click="toggleRemovePart(part.id)" class="text-[10px] font-black uppercase text-blue-600 hover:underline">Herstellen</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- New Parts -->
                        <div v-if="form.new_parts.length > 0">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-blue-600 mb-4 px-2">Nieuwe Toevoegingen</h4>
                            <div class="space-y-3">
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
                                    @remove="form.new_parts.splice(index, 1)"
                                    @add-instrument="onAddInstrument"
                                    @openPreview="openPreview"
                                />
                            </div>
                        </div>

                        <!-- Empty State / Dropzone -->
                        <div v-if="form.existing_parts.filter(p => !form.removed_part_ids.includes(p.id)).length === 0 && form.new_parts.length === 0"
                            class="text-center py-16 rounded-2xl border-2 border-dashed transition-colors"
                            :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop"
                        >
                             <svg class="mx-auto h-12 w-12 text-gray-400 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                             <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Sleep bestanden hierin om toe te voegen</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-10 pt-8 border-t border-gray-100">
                        <Link :href="route('beheer.bladmuziek.index')" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-colors tracking-widest">Annuleren</Link>
                        <button type="button" 
                            class="bg-blue-950 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/10 flex items-center gap-2 disabled:opacity-50" 
                            @click="submit" 
                            :disabled="isUploading"
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
