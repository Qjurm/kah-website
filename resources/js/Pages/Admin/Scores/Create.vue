<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchableSelect from '@/Components/Form/SearchableSelect.vue';
import FileUpload from '@/Components/Form/FileUpload.vue';

const props = defineProps({
    instruments: Array,
});

const form = useForm({
    title: '',
    composer: '',
    arranger: '',
    number: '',
    parts: [],
});

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

function submit() {
    form.post(route('admin.scores.store'), { forceFormData: true });
}
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

                    <!-- Instruments & Parts -->
                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Instrumenten & Partijen</h3>
                        <p class="text-sm text-gray-500 mb-6">Voeg instrumenten toe en upload direct de bijbehorende PDF.</p>

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

                        <!-- Instrument list with inline PDF upload -->
                        <div v-if="form.parts.length" class="space-y-4 mb-6">
                            <div
                                v-for="(part, idx) in form.parts"
                                :key="idx"
                                class="border border-gray-200 rounded-xl p-4"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                        <span class="font-semibold text-gray-800">{{ part.instrument }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                        @click="removeInstrument(idx)"
                                    >Verwijderen</button>
                                </div>
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
                        <div v-else class="text-sm text-gray-400 mb-6 py-6 text-center border border-dashed border-gray-200 rounded-lg">
                            Nog geen instrumenten toegevoegd. Partijen zijn optioneel.
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
                            <span v-else>Stuk opslaan</span>
                        </button>
                        <Link
                            :href="route('admin.scores.index')"
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
