<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    users: Object,
});
</script>

<template>
    <Head title="Gebruikers beheren" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Gebruikers beheren</h2>
                <Link :href="route('admin.users.create')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">
                    + Nieuwe gebruiker
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mailadres</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lid sinds</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                        :class="user.role === 'admin'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-green-100 text-green-800'"
                                    >
                                        {{ user.role === 'admin' ? 'Admin' : 'Muzikant' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ new Date(user.created_at).toLocaleDateString('nl-NL') }}
                                </td>
                            </tr>
                            <tr v-if="!users.data.length">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">Geen gebruikers gevonden.</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ users.from }}-{{ users.to }} van {{ users.total }}</span>
                        <div class="flex gap-2">
                            <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">Vorige</Link>
                            <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">Volgende</Link>
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
