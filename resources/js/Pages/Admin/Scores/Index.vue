<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    scores: Object,
});

function destroy(score) {
    if (confirm(`Weet je zeker dat je "${score.title}" wilt verwijderen?`)) {
        router.delete(route('admin.scores.destroy', score.id));
    }
}
</script>

<template>
    <Head title="Stukken beheren" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Stukken beheren</h2>
                <Link :href="route('admin.scores.create')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">
                    + Nieuw stuk
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Componist</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrangeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parts</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="score in scores.data" :key="score.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-yellow-600 font-bold">{{ score.number }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ score.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ score.composer }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ score.arranger || '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ score.parts_count }}</td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <Link :href="route('admin.scores.edit', score.id)" class="text-blue-600 hover:text-blue-900 font-medium">Bewerken</Link>
                                    <button @click="destroy(score)" class="text-red-600 hover:text-red-900 font-medium">Verwijderen</button>
                                </td>
                            </tr>
                            <tr v-if="!scores.data.length">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">Nog geen stukken toegevoegd.</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="scores.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ scores.from }}-{{ scores.to }} van {{ scores.total }}</span>
                        <div class="flex gap-2">
                            <Link
                                v-if="scores.prev_page_url"
                                :href="scores.prev_page_url"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-50"
                            >Vorige</Link>
                            <Link
                                v-if="scores.next_page_url"
                                :href="scores.next_page_url"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-50"
                            >Volgende</Link>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Terug naar dashboard</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
