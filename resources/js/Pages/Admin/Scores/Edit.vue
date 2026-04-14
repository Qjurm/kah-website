<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchableSelectWithCreate from '@/Components/Form/SearchableSelectWithCreate.vue';
import FileUpload from '@/Components/Form/FileUpload.vue';

const props = defineProps({
    score: Object,
    instruments: Array,
});

// DEBUG: Log what we receive
console.log('Score Edit - props:', JSON.stringify({ score: props.score, instruments: props.instruments?.length }, null, 2));

// Existing parts (kept from DB)
const keptParts = ref([]);
const removedPartIds = ref([]);
// New parts to add
const newParts = ref([]);

// Initialize form EMPTY
const form = useForm({
    title: '',
    composer: '',
    arranger: '',
    removed_part_ids: [],
    new_parts: [],
    _method: 'PUT',
});

// Watch props.score, populate form when data arrives
watch(
    () => props.score,
    (newScore) => {
        if (!newScore) return;

        // Set each form field individually
        Object.assign(form, {
            title: newScore.title || '',
            composer: newScore.composer || '',
            arranger: newScore.arranger || '',
        });

        // Reset part tracking - handle both wrapped and unwrapped data
        const parts = newScore.parts?.data ? newScore.parts.data : (newScore.parts || []);
        keptParts.value = [...parts];
        removedPartIds.value = [];
        newParts.value = [];
    },
    { immediate: true, deep: true }
);

const instrumentOptions = computed(() => {
    if (!props.instruments || !Array.isArray(props.instruments)) {
        return [];
    }
    return props.instruments.map((instr) => {
        // Handle both string names and object resources
        const name = typeof instr === 'string' ? instr : (instr.name || '');
        const value = typeof instr === 'string' ? instr : (instr.name || '');
        return { value, label: name };
    });
});
const selectedInstrument = ref(null);

async function createNewInstrument(name) {
    try {
        const response = await fetch(route('beheer.api.instruments.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name }),
        });

        if (response.ok) {
            const data = await response.json();
            return { value: data.name, label: data.name };
        } else {
            const error = await response.json();
            alert('Fout bij aanmaken instrument: ' + (error.message || JSON.stringify(error)));
            return null;
        }
    } catch (error) {
        console.error('Error creating instrument:', error);
        alert('Fout bij aanmaken instrument: ' + error.message);
        return null;
    }
}

function removeExistingPart(part) {
    removedPartIds.value.push(part.id);
    keptParts.value = keptParts.value.filter((p) => p.id !== part.id);
}

function addNewInstrument() {
    if (!selectedInstrument.value) return;
    // Get instrument name from options
    const instrumentName = instrumentOptions.value.find(opt => opt.value === selectedInstrument.value)?.label || selectedInstrument.value;
    // Allow duplicates! Users can add "Trompet 1" and "Trompet 2"
    newParts.value.push({ instrument: instrumentName, pdf: null });
    selectedInstrument.value = null;
}

function removeNewPart(idx) {
    newParts.value.splice(idx, 1);
}

function submit() {
    form.removed_part_ids = removedPartIds.value;
    
    // Flatten new_parts for FormData compatibility
    newParts.value.forEach((part, idx) => {
        form[`new_parts.${idx}.instrument`] = part.instrument;
        if (part.pdf) {
            form[`new_parts.${idx}.pdf`] = part.pdf;
        }
    });
    
    form.put(route('beheer.bladmuziek.update', props.score.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="`Bewerk: ${form.title || 'Stuk'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Stuk bewerken</h2>
                <Link :href="route('beheer.bladmuziek.index')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar overzicht</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                    <!-- Score details -->
                    <h3 class="text-lg font-bold text-blue-900 mb-6">Stukgegevens</h3>
                    <div class="space-y-5 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Componist *</label>
                            <input
                                v-model="form.composer"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.composer }"
                            />
                            <p v-if="form.errors.composer" class="text-red-500 text-sm mt-1">{{ form.errors.composer }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Arrangeur</label>
                            <input
                                v-model="form.arranger"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Existing parts -->
                    <div class="border-t border-gray-100 pt-8 mb-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-4">Bestaande partijen</h3>
                        <div v-if="keptParts.length" class="space-y-2">
                            <div
                                v-for="part in keptParts"
                                :key="part.id"
                                class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-800">{{ part.instrument }}</span>
                                    </div>
                                    <a :href="route('beheer.bladmuziek.partijen.download', { score: score.id, part: part.id })" 
                                       target="_blank"
                                       class="text-xs text-blue-600 hover:text-blue-800 underline flex items-center gap-1">
                                       <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                       Inzien / Downloaden
                                    </a>
                                </div>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium"
                                    @click="removeExistingPart(part)"
                                >Verwijderen</button>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 py-4 text-center border border-dashed border-gray-200 rounded-lg">
                            Geen bestaande partijen.
                        </div>
                    </div>

                    <!-- New parts -->
                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Nieuwe partijen toevoegen</h3>
                        <p class="text-sm text-gray-500 mb-4">Selecteer een instrument en upload de bijbehorende PDF.</p>

                        <div class="flex gap-2 mb-4">
                            <div class="flex-1">
                                <SearchableSelectWithCreate
                                    v-model="selectedInstrument"
                                    :options="instrumentOptions"
                                    placeholder="Instrument toevoegen..."
                                    :clearable="true"
                                    creatable
                                    :onCreateNew="createNewInstrument"
                                />
                            </div>
                            <button
                                type="button"
                                :disabled="!selectedInstrument"
                                class="bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40 flex-shrink-0"
                                @click="addNewInstrument"
                            >
                                Toevoegen
                            </button>
                        </div>

                        <div v-if="newParts.length" class="space-y-4 mb-6">
                            <div
                                v-for="(part, idx) in newParts"
                                :key="idx"
                                class="border border-blue-100 bg-blue-50 rounded-xl p-4"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                        <span class="font-semibold text-blue-900">{{ part.instrument }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                        @click="removeNewPart(idx)"
                                    >Annuleren</button>
                                </div>
                                <FileUpload
                                    v-model="part.pdf"
                                    accept=".pdf,application/pdf"
                                    :label="`PDF voor ${part.instrument}`"
                                    :max-size-mb="20"
                                />
                                <p v-if="form.errors[`new_parts.${idx}.pdf`]" class="text-red-500 text-sm mt-1">
                                    {{ form.errors[`new_parts.${idx}.pdf`] }}
                                </p>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 py-4 text-center border border-dashed border-gray-200 rounded-lg mb-6">
                            Nog geen nieuwe partijen toegevoegd.
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
                        <button
                            type="button"
                            :disabled="form.processing"
                            class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                            @click="submit"
                        >
                            <span v-if="form.processing">Opslaan...</span>
                            <span v-else>Wijzigingen opslaan</span>
                        </button>
                        <Link
                            :href="route('beheer.bladmuziek.index')"
                            class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Annuleren
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
