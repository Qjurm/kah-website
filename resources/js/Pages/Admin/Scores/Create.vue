<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
        let inferredInstrumentStr = '';

        const underscoreIndex = nameWithoutExt.lastIndexOf('_');
        const dashIndex = nameWithoutExt.lastIndexOf(' - ');

        if (underscoreIndex !== -1) {
            inferredTitle = nameWithoutExt.substring(0, underscoreIndex).trim();
            inferredInstrumentStr = nameWithoutExt.substring(underscoreIndex + 1).trim();
        } else if (dashIndex !== -1) {
            inferredTitle = nameWithoutExt.substring(0, dashIndex).trim();
            inferredInstrumentStr = nameWithoutExt.substring(dashIndex + 3).trim();
        } else {
            // No separator found -> Entire filename serves as the 'possible' alias match String
            inferredTitle = nameWithoutExt.trim();
            inferredInstrumentStr = nameWithoutExt.trim();
        }

        let matchedInstrument = '';
        if (inferredInstrumentStr) {
            const lowerInferred = inferredInstrumentStr.toLowerCase();
            const strippedInferred = lowerInferred.replace(/\s*\d+$/, ''); // strip numbers for alias check
            
            if (strippedInferred.trim() !== '') {
                // Exact Match
                let match = instrumentsList.value.find(i => i.name.toLowerCase() === lowerInferred);
                
                // "percussie 1" maps to "percussie" (Inferred str includes the DB instrument name)
                if (!match) {
                    match = instrumentsList.value.find(i => lowerInferred.includes(i.name.toLowerCase()));
                }
                
                // Reverse Include matching -> "alt" maps to "saxofoon alt"
                if (!match) {
                    match = instrumentsList.value.find(i => i.name.toLowerCase().includes(lowerInferred));
                }

                // Alias mapping. Also checks for inclusions if the alias is 4+ chars, else uses word boundaries to prevent bugs like "cl" matching "clef". 
                if (!match) {
                    match = instrumentsList.value.find(i => 
                        i.aliases && i.aliases.some(alias => {
                            const lowerAlias = alias.toLowerCase();
                            if (strippedInferred === lowerAlias) return true;
                            
                            // Prevent "cl" matching inside "clef"
                            if (lowerAlias.length > 3 && strippedInferred.includes(lowerAlias)) return true;
                            if (lowerAlias.length > 3 && lowerAlias.includes(strippedInferred)) return true;
                            
                            // Word boundary check for short aliases
                            const regex = new RegExp(`\\b${lowerAlias}\\b`, 'i');
                            return regex.test(strippedInferred);
                        })
                    );
                }
                
                // STRICT DB assignment
                if (match) {
                    matchedInstrument = match.name;
                } else {
                    matchedInstrument = ''; // Force user to use dropdown or add custom
                }
            }
        }

        form.parts.push({
            pdf: file,
            filename: file.name,
            inferredTitle: inferredTitle,
            original_parsed_instrument: inferredInstrumentStr,
            instrument: matchedInstrument,
        });
        
        if (!form.title && inferredTitle) {
            form.title = inferredTitle;
        }
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

function submit() {
    form.post(route('beheer.bladmuziek.store'), { 
        forceFormData: true,
        onError: (errors) => {
            console.error("Validation or server errors during upload:", errors);
            // Even if we have errors, don't crash
        },
        onFinish: () => {
            console.log("Upload attempt finished.");
        }
    });

    // Provide a helpful fallback UI message if the browser throws immediately
    window.addEventListener('unhandledrejection', (event) => {
        if (event.reason && event.reason.isAxiosError) {
            alert("UPLOAD CRASHT (Axios Error): " + event.reason.message + "\n\nAls er een 413 foutmelding bij staat, staat er ergens een server/upload limiet in de weg.");
        } else if (event.reason) {
            alert("UPLOAD CRASHT (Onbekende JS Error): " + event.reason.toString());
        }
    });
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
                        <button type="button" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50" @click="submit" :disabled="form.processing">
                            <span>Muziekstuk Opslaan</span>
                        </button>
                    </div>
                </SectionCard>

            </div>
        </div>
    </AuthenticatedLayout>
</template>