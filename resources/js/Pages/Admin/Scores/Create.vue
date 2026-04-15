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

const props = defineProps({
    instruments: Array,
});

const instrumentsList = ref([...props.instruments]);

const form = useForm({
    title: '',
    composer: '',
    arranger: '',
    parts: [],
});

const fileInputSingle = ref(null);
const fileInputFolder = ref(null);
const isDragging = ref(false);
const isUploading = ref(false);
const currentScoreId = ref(null);

function triggerFileInputSingle() {
    fileInputSingle.value.click();
}

function triggerFileInputFolder() {
    fileInputFolder.value.click();
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
                if (entry) {
                    promises.push(traverseFileTree(entry, filesToProcess));
                } else {
                    const f = item.getAsFile();
                    if (f) filesToProcess.push(f);
                }
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
            entry.file((file) => {
                fileList.push(file);
                resolve();
            });
        } else if (entry.isDirectory) {
            const dirReader = entry.createReader();
            dirReader.readEntries(async (entries) => {
                const promises = [];
                for (let i = 0; i < entries.length; i++) {
                    promises.push(traverseFileTree(entries[i], fileList));
                }
                await Promise.all(promises);
                resolve();
            });
        } else {
            resolve();
        }
    });
}

function handleFileUpload(event) {
    const files = Array.from(event.target.files);
    processFiles(files);

    if (fileInputSingle.value) fileInputSingle.value.value = '';
    if (fileInputFolder.value) fileInputFolder.value.value = '';
}

function processFiles(files) {
    if (!files.length) return;

    files.forEach(file => {
        if (file.type !== 'application/pdf') return;

        const nameWithoutExt = file.name.replace(/\.pdf$/i, '');
        let inferredTitle = nameWithoutExt;
        let inferredComposer = '';
        let inferredInstrumentStr = '';

        // Improved Regex for "Composer - Title_Instrument" or "Composer - Title - Instrument"
        // Also supports "Composer_Title_Instrument"
        const dashParts = nameWithoutExt.split(/ - | _ /);
        const underscoreParts = nameWithoutExt.split('_');
        
        if (dashParts.length >= 2) {
            inferredComposer = dashParts[0].trim();
            inferredTitle = dashParts[1].trim();
            if (dashParts.length >= 3) {
                inferredInstrumentStr = dashParts[dashParts.length - 1].trim();
            } else {
                inferredInstrumentStr = inferredTitle;
            }
        } else if (underscoreParts.length >= 2) {
            inferredComposer = underscoreParts[0].trim();
            inferredTitle = underscoreParts[1].trim();
            if (underscoreParts.length >= 3) {
                inferredInstrumentStr = underscoreParts[underscoreParts.length - 1].trim();
            } else {
                inferredInstrumentStr = inferredTitle;
            }
        } else {
            inferredTitle = nameWithoutExt.trim();
            inferredInstrumentStr = nameWithoutExt.trim();
        }

        let matchedInstrument = '';
        if (inferredInstrumentStr) {
            const lowerInferred = inferredInstrumentStr.toLowerCase();
            const strippedInferred = lowerInferred.replace(/\s*\d+$/, '');
            
            if (strippedInferred.trim() !== '') {
                let match = instrumentsList.value.find(i => i.name.toLowerCase() === lowerInferred);
                if (!match) match = instrumentsList.value.find(i => lowerInferred.includes(i.name.toLowerCase()));
                if (!match) match = instrumentsList.value.find(i => i.name.toLowerCase().includes(lowerInferred));
                if (!match) {
                    match = instrumentsList.value.find(i => 
                        i.aliases && i.aliases.some(alias => {
                            const lowerAlias = alias.toLowerCase();
                            if (strippedInferred === lowerAlias) return true;
                            if (lowerAlias.length > 3 && (strippedInferred.includes(lowerAlias) || lowerAlias.includes(strippedInferred))) return true;
                            const regex = new RegExp(`\\b${lowerAlias}\\b`, 'i');
                            return regex.test(strippedInferred);
                        })
                    );
                }
                if (match) matchedInstrument = match.name;
            }
        }

        form.parts.push({
            pdf: file,
            filename: file.name,
            inferredTitle: inferredTitle,
            original_parsed_instrument: inferredInstrumentStr,
            instrument: matchedInstrument,
            status: 'pending',
            statusMessage: '',
        });
        
        // Auto-fill metadata if empty
        if (!form.title && inferredTitle) form.title = inferredTitle;
        if (!form.composer && inferredComposer) form.composer = inferredComposer;
    });
}

function removePart(index) {
    form.parts.splice(index, 1);
}

function onInstrumentChanged(changedIndex, newInstrumentName) {
    const changedPart = form.parts[changedIndex];
    if (!newInstrumentName || !changedPart.original_parsed_instrument) return;

    const targetAlias = changedPart.original_parsed_instrument.toLowerCase().replace(/\s*\d+$/, '');

    form.parts.forEach((p, idx) => {
        if (idx !== changedIndex && p.original_parsed_instrument) {
            const parsed = p.original_parsed_instrument.toLowerCase().replace(/\s*\d+$/, '');
            if (parsed === targetAlias) {
                 p.instrument = newInstrumentName;
            }
        }
    });
}

async function onAddInstrument(newName) {
    if (instrumentsList.value.find(i => i.name.toLowerCase() === newName.toLowerCase())) return;

    try {
        const response = await axios.post(route('beheer.api.instruments.store'), { name: newName });
        instrumentsList.value.push(response.data);
    } catch (error) {
        console.error("Fout bij toevoegen instrument:", error);
        // Fallback: add locally if API fails
        instrumentsList.value.push({ id: 'new_' + Date.now(), name: newName });
    }
}

async function submit() {
    if (isUploading.value) return;
    
    isUploading.value = true;
    form.clearErrors(); // Clear existing errors
    
    try {
        // Step 1: Create Score Meta first
        const metaResponse = await axios.post(route('beheer.bladmuziek.store'), {
            title: form.title,
            composer: form.composer,
            arranger: form.arranger,
        });
        
        currentScoreId.value = metaResponse.data.score.id;
        
        // Step 2: Sequential Uploads
        for (let i = 0; i < form.parts.length; i++) {
            const part = form.parts[i];
            if (part.status === 'success') continue;
            
            part.status = 'uploading';
            part.statusMessage = 'Bezig met uploaden...';
            
            try {
                const formData = new FormData();
                formData.set('instrument', String(part.instrument));
                formData.set('pdf', part.pdf, part.filename);
                
                await axios.post(route('beheer.bladmuziek.partijen.store', currentScoreId.value), formData);
                
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
        
        const anyFailed = form.parts.some(p => p.status === 'error');
        if (!anyFailed) {
            router.visit(route('beheer.bladmuziek.index'));
        }
    } catch (err) {
        console.error("Fout bij aanmaken muziekstuk:", err);
        // Map Axios validation errors back to the Inertia form
        if (err.response?.status === 422 && err.response.data.errors) {
            const errors = err.response.data.errors;
            Object.keys(errors).forEach(key => {
                form.setError(key, errors[key][0]);
            });
        } else {
            alert("Er is een onverwachte fout opgetreden. Controleer of alle verplichte velden (Titel, Componist) zijn ingevuld.");
        }
    } finally {
        isUploading.value = false;
    }
}
</script>

<template>
    <Head title="Nieuw stuk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Nieuw stuk toevoegen</h2>
                <Link :href="route('beheer.bladmuziek.index')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar overzicht</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Main Form Details -->
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

                <!-- PDF Details -->
                <SectionCard title="PDF Partijen">
                    <template #header>
                        <div class="flex gap-2">
                            <input type="file" ref="fileInputSingle" class="hidden" multiple accept=".pdf" @change="handleFileUpload" />
                            <input type="file" ref="fileInputFolder" class="hidden" webkitdirectory directory multiple @change="handleFileUpload" />
                            
                            <button type="button" @click="triggerFileInputFolder" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">
                                Map toevoegen
                            </button>
                            <button type="button" @click="triggerFileInputSingle" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">
                                PDF(s) toevoegen
                            </button>
                        </div>
                    </template>

                    <div v-if="form.parts.length === 0" 
                        class="text-center py-12 rounded-xl border-2 border-dashed transition-colors"
                        :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                    >
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-4 text-sm text-gray-600">Geen PDF bestanden toegevoegd.</p>
                        <p class="mt-1 text-xs text-gray-500">Sleep bestanden of mappen hierin, of gebruik de knoppen hierboven.</p>
                        <span v-if="isDragging" class="mt-4 inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Drop bestanden om te scannen!</span>
                    </div>

                    <div v-else 
                        class="space-y-4 rounded-xl border-2 border-dashed p-4 transition-colors"
                        :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-transparent'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                    >
                        <ScorePartItem 
                            v-for="(part, index) in form.parts" 
                            :key="index"
                            :part="part"
                            :index="index"
                            :instruments="instrumentsList"
                            :modelValue="part.instrument"
                            :status="part.status"
                            :statusMessage="part.statusMessage"
                            @update:modelValue="val => { part.instrument = val; onInstrumentChanged(index, val); }"
                            :errorInstrument="form.errors[`parts.${index}.instrument`]"
                            :errorPdf="form.errors[`parts.${index}.pdf`]"
                            @remove="removePart(index)"
                            @add-instrument="onAddInstrument"
                        />
                        <div v-if="form.errors.parts" class="mt-2 text-sm text-red-600">
                            {{ form.errors.parts }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                        <Link :href="route('beheer.bladmuziek.index')" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                            Annuleren
                        </Link>
                        <Link v-if="form.parts.some(p => p.status === 'error')" :href="route('beheer.bladmuziek.index')" class="bg-blue-950 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                            Klaar
                        </Link>
                        <button v-else type="button" class="bg-blue-950 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50 flex items-center gap-2" @click="submit" :disabled="isUploading">
                            <svg v-if="isUploading" class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isUploading ? 'Bezig met opslaan...' : 'Muziekstuk Opslaan' }}</span>
                        </button>
                    </div>
                </SectionCard>

            </div>
        </div>
    </AuthenticatedLayout>
</template>