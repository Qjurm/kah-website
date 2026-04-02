<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    score: Object,
});

const form = useForm({
    title: props.score.title,
    composer: props.score.composer,
    arranger: props.score.arranger || '',
    number: props.score.number,
});

const partForm = useForm({
    instrument: '',
    pdf: null,
});

function submit() {
    form.put(route('admin.scores.update', props.score.id));
}

function addPart() {
    partForm.post(route('admin.scores.parts.store', props.score.id), {
        forceFormData: true,
        onSuccess: () => {
            partForm.reset();
        },
    });
}

function deletePart(part) {
    if (confirm(`Part "${part.instrument}" verwijderen?`)) {
        router.delete(route('admin.parts.destroy', part.id));
    }
}

function onFileChange(e) {
    partForm.pdf = e.target.files[0];
}
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
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Score form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-bold text-blue-900 mb-6">Stukgegevens</h3>
                    <form @submit.prevent="submit" class="space-y-6">
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

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                        >
                            Bijwerken
                        </button>
                    </form>
                </div>

                <!-- Parts section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-bold text-blue-900 mb-6">Parts (PDF's)</h3>

                    <!-- Existing parts -->
                    <div v-if="score.parts && score.parts.length" class="mb-6 space-y-2">
                        <div
                            v-for="part in score.parts"
                            :key="part.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200"
                        >
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ part.instrument }}</span>
                            </div>
                            <button
                                @click="deletePart(part)"
                                class="text-red-600 hover:text-red-900 text-sm font-medium"
                            >Verwijderen</button>
                        </div>
                    </div>
                    <div v-else class="mb-6 text-gray-400 text-sm">Nog geen parts toegevoegd.</div>

                    <!-- Add part form -->
                    <form @submit.prevent="addPart" class="space-y-4 border-t border-gray-200 pt-6">
                        <h4 class="font-semibold text-gray-700">Part toevoegen</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instrument *</label>
                            <input
                                v-model="partForm.instrument"
                                type="text"
                                placeholder="bijv. Trompet 1, Klarinet 2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': partForm.errors.instrument }"
                            />
                            <p v-if="partForm.errors.instrument" class="text-red-500 text-sm mt-1">{{ partForm.errors.instrument }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PDF-bestand *</label>
                            <input
                                type="file"
                                accept="application/pdf"
                                @change="onFileChange"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': partForm.errors.pdf }"
                            />
                            <p v-if="partForm.errors.pdf" class="text-red-500 text-sm mt-1">{{ partForm.errors.pdf }}</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="partForm.processing"
                            class="bg-yellow-500 text-blue-900 px-6 py-2 rounded-lg font-semibold hover:bg-yellow-400 transition-colors disabled:opacity-50"
                        >
                            Part uploaden
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
