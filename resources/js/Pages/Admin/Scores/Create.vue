<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchableSelect from '@/Components/Form/SearchableSelect.vue';
import FileUpload from '@/Components/Form/FileUpload.vue';

const props = defineProps({
    instruments: Array,
});

const step = ref(1);

const form = useForm({
    title: '',
    composer: '',
    arranger: '',
    number: '',
    parts: [],
});

// Step 2: instrument selection
const instrumentOptions = computed(() =>
    (props.instruments ?? []).map((name) => ({ value: name, label: name }))
);
const selectedInstrument = ref(null);

function addInstrument() {
    if (!selectedInstrument.value) return;
    const already = form.parts.some((p) => p.instrument === selectedInstrument.value);
    if (!already) {
        form.parts.push({ instrument: selectedInstrument.value, pdf: null });
    }
    selectedInstrument.value = null;
}

function removeInstrument(idx) {
    form.parts.splice(idx, 1);
}

// Step navigation
function goToStep(n) {
    step.value = n;
}

function nextFromStep1() {
    if (!form.title || !form.composer || !form.number) {
        // Touch the fields so errors show
        form.title = form.title;
        return;
    }
    step.value = 2;
}

function submit() {
    form.post(route('admin.scores.store'), { forceFormData: true });
}

const stepLabels = ['Gegevens', 'Instrumenten', 'PDF\'s uploaden'];
</script>

<template>
    <Head title="Nieuw stuk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Nieuw stuk toevoegen</h2>
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
                            :disabled="idx + 1 > step"
                            :class="[
                                'flex items-center gap-2 text-sm font-medium transition-colors',
                                step === idx + 1 ? 'text-blue-900' : idx + 1 < step ? 'text-green-600 cursor-pointer' : 'text-gray-400 cursor-default',
                            ]"
                            @click="idx + 1 < step ? goToStep(idx + 1) : null"
                        >
                            <span :class="[
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 flex-shrink-0',
                                step === idx + 1 ? 'bg-blue-900 border-blue-900 text-white' : idx + 1 < step ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 text-gray-400',
                            ]">
                                <svg v-if="idx + 1 < step" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-else>{{ idx + 1 }}</span>
                            </span>
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
                                :disabled="!form.title || !form.composer || !form.number"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40"
                                @click="nextFromStep1"
                            >
                                Volgende &rarr;
                            </button>
                            <Link :href="route('admin.scores.index')" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                Annuleren
                            </Link>
                        </div>
                    </div>

                    <!-- STEP 2: Select instruments -->
                    <div v-else-if="step === 2">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Instrumenten selecteren</h3>
                        <p class="text-sm text-gray-500 mb-6">Voeg de instrumenten toe waarvoor je een partij wilt uploaden.</p>

                        <!-- Add instrument -->
                        <div class="flex gap-2 mb-6">
                            <div class="flex-1">
                                <SearchableSelect
                                    v-model="selectedInstrument"
                                    :options="instrumentOptions"
                                    placeholder="Kies een instrument..."
                                    :clearable="true"
                                />
                            </div>
                            <button
                                type="button"
                                :disabled="!selectedInstrument"
                                class="bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40 flex-shrink-0"
                                @click="addInstrument"
                            >
                                Toevoegen
                            </button>
                        </div>

                        <!-- Selected instruments list -->
                        <div v-if="form.parts.length" class="space-y-2 mb-6">
                            <div
                                v-for="(part, idx) in form.parts"
                                :key="idx"
                                class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">{{ part.instrument }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 text-sm"
                                    @click="removeInstrument(idx)"
                                >Verwijderen</button>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 mb-6 py-6 text-center border border-dashed border-gray-200 rounded-lg">
                            Nog geen instrumenten geselecteerd. Voeg er minimaal één toe.
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                :disabled="form.parts.length === 0"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40"
                                @click="goToStep(3)"
                            >
                                Volgende &rarr;
                            </button>
                            <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors" @click="goToStep(1)">&larr; Terug</button>
                        </div>
                    </div>

                    <!-- STEP 3: Upload PDFs -->
                    <div v-else-if="step === 3">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">PDF's uploaden</h3>
                        <p class="text-sm text-gray-500 mb-6">Upload voor elk instrument de bijbehorende partij (PDF).</p>

                        <div class="space-y-6">
                            <div v-for="(part, idx) in form.parts" :key="idx">
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
                                <p v-if="form.errors[`parts.${idx}.pdf`]" class="text-red-500 text-sm mt-1">
                                    {{ form.errors[`parts.${idx}.pdf`] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-8">
                            <button
                                type="button"
                                :disabled="form.processing"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                                @click="submit"
                            >
                                <span v-if="form.processing">Opslaan...</span>
                                <span v-else>Stuk opslaan</span>
                            </button>
                            <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors" @click="goToStep(2)">&larr; Terug</button>
                        </div>
                        <p v-if="form.errors.title || form.errors.composer || form.errors.number" class="text-red-500 text-sm mt-3">
                            Er zijn fouten in stap 1. <button type="button" class="underline" @click="goToStep(1)">Ga terug</button> om ze te corrigeren.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
