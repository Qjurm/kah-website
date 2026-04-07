<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionHeader from '@/Components/Dashboard/SectionHeader.vue';
import TipsCard from '@/Components/Dashboard/TipsCard.vue';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    pendingUsers: Array,
});

const expandedSections = ref({
    pending: true,
    active: true,
    addNew: false,
});

const newUser = ref({
    name: '',
    email: '',
    password: '',
    instruments: [],
    role: 'musician',
    skip_approval: false,
});

function toggleSection(section) {
    expandedSections.value[section] = !expandedSections.value[section];
}

function deletePendingUser(userId) {
    if (confirm('Weet je zeker dat je deze aanvraag wilt verwijderen?')) {
        router.delete(route('beheer.gebruikers.destroy', userId));
    }
}

function submitNewUser() {
    router.post(route('beheer.gebruikers.store'), newUser.value, {
        onSuccess: () => {
            newUser.value = {
                name: '',
                email: '',
                password: '',
                instruments: [],
                role: 'musician',
                skip_approval: false,
            };
            expandedSections.value.addNew = false;
        }
    });
}
</script>

<template>
    <Head title="Gebruikers beheren" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Gebruikers beheren</h2>
                <p class="text-sm text-gray-600">Goedkeur registraties, beheer muzikanten en rollen</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section header -->
                <SectionHeader 
                    title="Gebruikers beheren"
                    subtitle="Goedkeur registraties, beheer muzikanten en rollen"
                />

                <!-- Intro Card -->
                <TipsCard>
                    Voeg muzikanten toe, goedkeur registraties, en beheer gebruikerrollen. Onderstaande secties geven je een overzicht van alle gebruikers.
                </TipsCard>

                <!-- SECTION 1: Pending Approvals -->
                <div class="mb-6 bg-white rounded-2xl border border-red-200 overflow-hidden">
                    <button
                        @click="toggleSection('pending')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-red-50 transition-colors border-b border-red-200"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 text-red-900 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-gray-900">In afwachting van goedkeuring</h3>
                                <p class="text-gray-500 text-xs">Nieuwe aanvragen van muzikanten</p>
                            </div>
                            <span class="ml-auto bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                {{ pendingUsers?.length || 0 }}
                            </span>
                        </div>
                        <svg
                            class="w-5 h-5 text-gray-400 transition-transform"
                            :class="{'rotate-180': expandedSections.pending}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div v-if="expandedSections.pending" class="p-6">
                        <div v-if="!pendingUsers || pendingUsers.length === 0" class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Geen aanvragen in afwachting
                        </div>
                        <div v-else class="space-y-3">
                            <div v-for="user in pendingUsers" :key="user.id" class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ user.name }}</h4>
                                        <p class="text-gray-600 text-sm">{{ user.email }}</p>
                                        <p class="text-gray-500 text-xs mt-1">
                                            Aangevraagd: {{ new Date(user.created_at).toLocaleDateString('nl-NL') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('beheer.gebruikers.approve', user.id)"
                                        method="put"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Goedkeuren
                                    </Link>
                                    <button
                                        @click="deletePendingUser(user.id)"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Weigeren
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Active Musicians -->
                <div class="mb-6 bg-white rounded-2xl border border-green-200 overflow-hidden">
                    <button
                        @click="toggleSection('active')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-green-50 transition-colors border-b border-green-200"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 text-green-900 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-gray-900">Actieve Muzikanten</h3>
                                <p class="text-gray-500 text-xs">Goedgekeurde leden</p>
                            </div>
                            <span class="ml-auto bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                {{ props.users?.data?.length || 0 }}
                            </span>
                        </div>
                        <svg
                            class="w-5 h-5 text-gray-400 transition-transform"
                            :class="{'rotate-180': expandedSections.active}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div v-if="expandedSections.active" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Naam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mailadres</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lid sinds</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acties</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="user in props.users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
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
                                    <td class="px-6 py-4 text-right text-sm space-x-2">
                                        <!-- Edit not available for users, only approve/reject -->
                                    </td>
                                </tr>
                                <tr v-if="!props.users.data.length">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">Geen actieve gebruikers gevonden.</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div v-if="props.users.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                            <span class="text-sm text-gray-500">{{ props.users.from }}-{{ props.users.to }} van {{ props.users.total }}</span>
                            <div class="flex gap-2">
                                <Link v-if="props.users.prev_page_url" :href="props.users.prev_page_url" class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Vorige</Link>
                                <Link v-if="props.users.next_page_url" :href="props.users.next_page_url" class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Volgende</Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Add New User -->
                <div class="mb-6 bg-white rounded-2xl border border-blue-200 overflow-hidden">
                    <button
                        @click="toggleSection('addNew')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-blue-50 transition-colors border-b border-blue-200"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 text-blue-900 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-gray-900">Voeg gebruiker toe</h3>
                                <p class="text-gray-500 text-xs">Voeg handmatig een nieuwe muzikant toe</p>
                            </div>
                        </div>
                        <svg
                            class="w-5 h-5 text-gray-400 transition-transform"
                            :class="{'rotate-180': expandedSections.addNew}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div v-if="expandedSections.addNew" class="p-6 bg-blue-50">
                        <form @submit.prevent="submitNewUser" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Naam</label>
                                <input
                                    v-model="newUser.name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Volledige naam"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">E-mailadres</label>
                                <input
                                    v-model="newUser.email"
                                    type="email"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="naam@voorbeeld.nl"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Wachtwoord</label>
                                <input
                                    v-model="newUser.password"
                                    type="password"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Sterk wachtwoord"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Rol</label>
                                <select v-model="newUser.role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="musician">Muzikant</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <input
                                    v-model="newUser.skip_approval"
                                    type="checkbox"
                                    id="skip_approval"
                                    class="rounded"
                                />
                                <label for="skip_approval" class="text-sm text-gray-700">
                                    Sla goedkeuring over (direct actief)
                                </label>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-900 text-white font-semibold rounded-lg hover:bg-blue-800 transition-colors"
                                >
                                    Gebruiker toevoegen
                                </button>
                                <button
                                    type="button"
                                    @click="expandedSections.addNew = false"
                                    class="px-4 py-2 bg-gray-300 text-gray-900 font-semibold rounded-lg hover:bg-gray-400 transition-colors"
                                >
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-4">
                    <Link :href="route('beheer.dashboard')" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">
                        ← Terug naar dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
