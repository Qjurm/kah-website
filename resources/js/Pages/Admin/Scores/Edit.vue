<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchableSelect from '@/Components/Form/SearchableSelect.vue';
import FileUpload from '@/Components/Form/FileUpload.vue';

const props = defineProps({
    score: Object,
    instruments: Array,
});

const step = ref(1);

// Existing parts (kept from DB)
const keptParts = ref([...(props.score.parts ?? [])]);
const removedPartIds = ref([]);
// New parts to add
const newParts = ref([]);

const form = useForm({
    title: props.score.title,
    composer: props.score.composer,
    arranger: props.score.arranger || '',
    number: props.score.number,
    removed_part_ids: [],
    new_parts: [],
});

// Instrument options
const instrumentOptions = computed(() =>
    (props.instruments ?? []).map((name) => ({ value: name, label: name }))
);
const selectedInstrument = ref(null);

function removeExistingPart(part) {
    removedPartIds.value.push(part.id);
    keptParts.value = keptParts.value.filter((p) => p.id !== part.id);
}

function addNewInstrument() {
    if (!selectedInstrument.value) return;
    newParts.value.push({ instrument: selectedInstrument.value, pdf: null });
    selectedInstrument.value = null;
}

function removeNewPart(idx) {
    newParts.value.splice(idx, 1);
}

function goToStep(n) {
    step.value = n;
}

function submit() {
    form.removed_part_ids = removedPartIds.value;
    form.new_parts = newParts.value;
    form.post(route('admin.scores.update', props.score.id), {
        forceFormData: true,
        method: 'put',
    });
}

const stepLabels = ['Gegevens', 'Instrumenten', 'PDF\'s uploaden'];
</script>

<template>
    <Head :title="`Bewerk: ${score.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Stuk bewerken</h2>
                <Link :href="route('admin.scores.index')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar overzicht</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Step indicator -->
                <div class="flex items-center mb-8">
                    <template v-for="(label, idx) in stepLabels" :key="idx">
                        <button
                            type="button"
                            :class="[
                                'flex items-center gap-2 text-sm font-medium transition-colors cursor-pointer',
                                step === idx + 1 ? 'text-blue-900' : 'text-gray-400 hover:text-gray-600',
                            ]"
                            @click="goToStep(idx + 1)"
                        >
                            <span :class="[
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 flex-shrink-0',
                                step === idx + 1 ? 'bg-blue-900 border-blue-900 text-white' : 'bg-white border-gray-300 text-gray-400',
                            ]">{{ idx + 1 }}</span>
                            <span class="hidden sm:inline">{{ label }}</span>
                        </button>
                        <div v-if="idx < stepLabels.length - 1" class="flex-1 h-px bg-gray-200 mx-3" />
                    </template>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                    <!-- STEP 1: Score details -->
                    <div v-if="step === 1">
                        <h3 class="text-lg font-bold text-blue-900 mb-6">Stukgegevens</h3>
                        <div class="space-y-5">
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

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Volgnummer *</label>
                                <input
                                    v-model="form.number"
                                    type="number"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.number }"
                                />
                                <p v-if="form.errors.number" class="text-red-500 text-sm mt-1">{{ form.errors.number }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-8">
                            <button
                                type="button"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors"
                                @click="goToStep(2)"
                            >
                                Volgende &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Manage instruments -->
                    <div v-else-if="step === 2">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Instrumenten beheren</h3>
                        <p class="text-sm text-gray-500 mb-6">Verwijder bestaande partijen of voeg nieuwe toe.</p>

                        <!-- Existing parts -->
                        <div v-if="keptParts.length" class="mb-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Bestaande partijen</p>
                            <div
                                v-for="part in keptParts"
                                :key="part.id"
                                class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-800">{{ part.instrument }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium"
                                    @click="removeExistingPart(part)"
                                >Verwijderen</button>
                            </div>
                        </div>
                        <div v-else-if="newParts.length === 0" class="mb-4 text-sm text-gray-400 py-4 text-center border border-dashed border-gray-200 rounded-lg">
                            Geen bestaande partijen.
                        </div>

                        <!-- New parts to add -->
                        <div v-if="newParts.length" class="mb-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Nieuw toe te voegen</p>
                            <div
                                v-for="(part, idx) in newParts"
                                :key="idx"
                                class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg"
                            >
                                <span class="text-sm font-medium text-blue-900">{{ part.instrument }}</span>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 text-sm"
                                    @click="removeNewPart(idx)"
                                >Annuleren</button>
                            </div>
                        </div>

                        <!-- Add new instrument -->
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                            <div class="flex-1">
                                <SearchableSelect
                                    v-model="selectedInstrument"
                                    :options="instrumentOptions"
                                    placeholder="Instrument toevoegen..."
                                    :clearable="true"
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

                        <div class="flex gap-3 mt-6">
                            <button
                                type="button"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors"
                                @click="goToStep(3)"
                            >
                                Volgende &rarr;
                            </button>
                            <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors" @click="goToStep(1)">&larr; Terug</button>
                        </div>
                    </div>

                    <!-- STEP 3: Upload PDFs for new parts -->
                    <div v-else-if="step === 3">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">PDF's uploaden</h3>
                        <p class="text-sm text-gray-500 mb-6">Upload de PDF voor elk nieuw toegevoegd instrument.</p>

                        <div v-if="newParts.length" class="space-y-6">
                            <div v-for="(part, idx) in newParts" :key="idx">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    <svg class="inline w-4 h-4 text-blue-700 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    {{ part.instrument }}
                                </label>
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
                        <div v-else class="text-sm text-gray-400 py-6 text-center border border-dashed border-gray-200 rounded-lg mb-6">
                            Geen nieuwe instrumenten toegevoegd. Klik op "Opslaan" om de wijzigingen te bewaren.
                        </div>

                        <div class="flex gap-3 mt-8">
                            <button
                                type="button"
                                :disabled="form.processing"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                                @click="submit"
                            >
                                <span v-if="form.processing">Opslaan...</span>
                                <span v-else>Wijzigingen opslaan</span>
                            </button>
                            <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors" @click="goToStep(2)">&larr; Terug</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
