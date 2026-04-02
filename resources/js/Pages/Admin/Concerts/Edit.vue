<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    concert: Object,
    scores: Array,
});

const form = useForm({
    _method: 'PUT',
    title: props.concert.title,
    date: props.concert.date,
    location: props.concert.location || '',
    photo: null,
    is_current: props.concert.is_current,
    score_ids: props.concert.scores?.map(s => s.id) || [],
});

function submit() {
    form.post(route('admin.concerts.update', props.concert.id));
}

function handlePhoto(e) {
    form.photo = e.target.files[0] ?? null;
}

function toggleScore(id) {
    const idx = form.score_ids.indexOf(id);
    if (idx === -1) {
        form.score_ids.push(id);
    } else {
        form.score_ids.splice(idx, 1);
    }
}
</script>

<template>
    <Head :title="`Bewerk: ${concert.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Concert bewerken</h2>
                <Link :href="route('admin.concerts.index')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar overzicht</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
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
                            <div v-if="concert.photo_path" class="mb-2">
                                <img :src="`/storage/${concert.photo_path}`" alt="Huidig foto" class="h-24 w-auto rounded-lg object-cover" />
                                <p class="text-xs text-gray-400 mt-1">Huidig foto — upload een nieuw om te vervangen</p>
                            </div>
                            <input
                                type="file"
                                accept="image/*"
                                @change="handlePhoto"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.photo" class="text-red-500 text-sm mt-1">{{ form.errors.photo }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                v-model="form.is_current"
                                type="checkbox"
                                id="is_current"
                                class="h-4 w-4 text-blue-900 rounded"
                            />
                            <label for="is_current" class="text-sm font-medium text-gray-700">
                                Dit is het huidige/komende concert
                            </label>
                        </div>

                        <!-- Score selection -->
                        <div v-if="scores && scores.length">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stukken koppelen</label>
                            <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg divide-y divide-gray-100">
                                <label
                                    v-for="score in scores"
                                    :key="score.id"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="score.id"
                                        :checked="form.score_ids.includes(score.id)"
                                        @change="toggleScore(score.id)"
                                        class="h-4 w-4 text-blue-900 rounded"
                                    />
                                    <span class="text-sm text-gray-800">{{ score.title }} <span class="text-gray-400">— {{ score.composer }}</span></span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-800 transition-colors disabled:opacity-50"
                            >
                                Bijwerken
                            </button>
                            <Link :href="route('admin.concerts.index')" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                Annuleren
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
