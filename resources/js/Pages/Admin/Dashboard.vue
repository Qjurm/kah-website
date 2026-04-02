<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Admin Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-900 text-white rounded-2xl p-6">
                        <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.scores }}</div>
                        <div class="text-blue-200 text-sm">Stukken in de bibliotheek</div>
                    </div>
                    <div class="bg-blue-900 text-white rounded-2xl p-6">
                        <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.concerts }}</div>
                        <div class="text-blue-200 text-sm">Concerten</div>
                    </div>
                    <div class="bg-blue-900 text-white rounded-2xl p-6">
                        <div class="text-4xl font-bold text-yellow-500 mb-1">{{ stats.users }}</div>
                        <div class="text-blue-200 text-sm">Gebruikers</div>
                    </div>
                </div>

                <div v-if="stats.currentConcert" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-8">
                    <span class="text-yellow-700 font-semibold text-sm">Huidig concert: </span>
                    <span class="text-yellow-800">{{ stats.currentConcert }}</span>
                </div>

                <!-- Navigation cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <Link :href="route('admin.scores.index')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900">Stukken beheren</h3>
                        </div>
                        <p class="text-gray-500 text-sm">Voeg stukken toe, bewerk ze en upload de parts als PDF.</p>
                    </Link>

                    <Link :href="route('admin.concerts.index')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900">Concerten beheren</h3>
                        </div>
                        <p class="text-gray-500 text-sm">Beheer concerten en koppel stukken aan een concert.</p>
                    </Link>

                    <Link :href="route('admin.users.index')" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="bg-blue-100 text-blue-900 p-3 rounded-xl group-hover:bg-blue-900 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900">Gebruikers beheren</h3>
                        </div>
                        <p class="text-gray-500 text-sm">Voeg muzikanten en admins toe en beheer hun toegang.</p>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
