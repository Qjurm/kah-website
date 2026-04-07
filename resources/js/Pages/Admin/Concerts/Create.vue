<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchableSelect from '@/Components/Form/SearchableSelect.vue';
import FileUpload from '@/Components/Form/FileUpload.vue';

const props = defineProps({
    scores: Array,
});

const step = ref(1);

const form = useForm({
    title: '',
    date: '',
    location: '',
    photo: null,
    is_current: false,
    is_public: true,
    score_ids: [],
});

// Score selection via SearchableSelect
const scoreOptions = computed(() =>
    (props.scores ?? []).map((s) => ({
        value: s.id,
        label: `#${s.number} ${s.title} — ${s.composer}`,
    }))
);
const scoreToAdd = ref(null);

const selectedScores = computed(() =>
    (props.scores ?? []).filter((s) => form.score_ids.includes(s.id))
);

function addScore() {
    if (scoreToAdd.value !== null && !form.score_ids.includes(scoreToAdd.value)) {
        form.score_ids.push(scoreToAdd.value);
    }
    scoreToAdd.value = null;
}

function removeScore(id) {
    form.score_ids = form.score_ids.filter((sid) => sid !== id);
}

function submit() {
    form.post(route('admin.concerts.store'), { forceFormData: true });
}

const stepLabels = ['Concertgegevens', 'Stukken koppelen'];
</script>

<template>
    <Head title="Nieuw concert" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Nieuw concert toevoegen</h2>
                <Link :href="route('admin.concerts.index')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar overzicht</Link>
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
                            @click="idx + 1 < step ? step = idx + 1 : null"
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

                    <!-- STEP 1: Concert details -->
                    <div v-if="step === 1">
                        <h3 class="text-lg font-bold text-blue-900 mb-6">Concertgegevens</h3>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Datum *</label>
                                <input
                                    v-model="form.date"
                                    type="date"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.date }"
                                />
                                <p v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ form.errors.date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
                                <input
                                    v-model="form.location"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto (optioneel)</label>
                                <FileUpload
                                    v-model="form.photo"
                                    accept="image/*"
                                    label="Klik om een foto te uploaden"
                                    :max-size-mb="5"
                                />
                                <p v-if="form.errors.photo" class="text-red-500 text-sm mt-1">{{ form.errors.photo }}</p>
                            </div>

                            <!-- Toggles -->
                            <div class="space-y-3 pt-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div
                                        :class="[
                                            'relative w-10 h-6 rounded-full transition-colors flex-shrink-0',
                                            form.is_current ? 'bg-blue-900' : 'bg-gray-200',
                                        ]"
                                        @click="form.is_current = !form.is_current"
                                    >
                                        <span :class="[
                                            'absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform',
                                            form.is_current ? 'translate-x-4' : 'translate-x-0',
                                        ]" />
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Huidig concert</span>
                                        <p class="text-xs text-gray-400">Stel dit in als het huidige/komende concert (één tegelijk)</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div
                                        :class="[
                                            'relative w-10 h-6 rounded-full transition-colors flex-shrink-0',
                                            form.is_public ? 'bg-blue-900' : 'bg-gray-200',
                                        ]"
                                        @click="form.is_public = !form.is_public"
                                    >
                                        <span :class="[
                                            'absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform',
                                            form.is_public ? 'translate-x-4' : 'translate-x-0',
                                        ]" />
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Openbaar</span>
                                        <p class="text-xs text-gray-400">Zichtbaar op de publieke website</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-8">
                            <button
                                type="button"
                                :disabled="!form.title || !form.date"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40"
                                @click="step = 2"
                            >
                                Volgende &rarr;
                            </button>
                            <Link :href="route('admin.concerts.index')" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                Annuleren
                            </Link>
                        </div>
                    </div>

                    <!-- STEP 2: Link scores -->
                    <div v-else-if="step === 2">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Stukken koppelen</h3>
                        <p class="text-sm text-gray-500 mb-6">Selecteer de stukken die op dit concert gespeeld worden.</p>

                        <!-- Add score -->
                        <div class="flex gap-2 mb-4">
                            <div class="flex-1">
                                <SearchableSelect
                                    v-model="scoreToAdd"
                                    :options="scoreOptions"
                                    placeholder="Zoek een stuk..."
                                    :clearable="true"
                                />
                            </div>
                            <button
                                type="button"
                                :disabled="scoreToAdd === null"
                                class="bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-40 flex-shrink-0"
                                @click="addScore"
                            >
                                Toevoegen
                            </button>
                        </div>

                        <!-- Selected scores -->
                        <div v-if="selectedScores.length" class="space-y-2 mb-6">
                            <div
                                v-for="score in selectedScores"
                                :key="score.id"
                                class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-blue-900 truncate">{{ score.title }}</p>
                                    <p class="text-xs text-gray-500">{{ score.composer }}</p>
                                </div>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 text-sm ml-3 flex-shrink-0"
                                    @click="removeScore(score.id)"
                                >Verwijderen</button>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 mb-6 py-6 text-center border border-dashed border-gray-200 rounded-lg">
                            Nog geen stukken gekoppeld.
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                :disabled="form.processing"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                                @click="submit"
                            >
                                <span v-if="form.processing">Opslaan...</span>
                                <span v-else>Concert opslaan</span>
                            </button>
                            <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors" @click="step = 1">&larr; Terug</button>
                        </div>
                        <p v-if="form.errors.title || form.errors.date" class="text-red-500 text-sm mt-3">
                            Er zijn fouten in stap 1. <button type="button" class="underline" @click="step = 1">Ga terug</button>.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
