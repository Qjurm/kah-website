<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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
    <AuthenticatedLayout>
        <Head title="Ledenbeheer" />

        <template #header>
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Ledenbeheer</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Gebruikers en rollen management</p>
                </div>
                <button
                    @click="toggleSection('addNew')"
                    class="bg-blue-950 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 transition-all active:scale-95 shadow-xl shadow-blue-900/20"
                >
                    + Gebruiker toevoegen
                </button>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-10 text-left">
                    <h1 class="text-5xl font-black text-blue-950 mb-3 italic">Community</h1>
                    <p class="text-gray-500 font-bold">Beheer alle muzikanten en administratieve accounts.</p>
                </div>

                <!-- SECTION 1: Pending Approvals -->
                <div v-if="pendingUsers?.length > 0" class="mb-12 bg-orange-50 rounded-[2.5rem] border border-orange-100 overflow-hidden shadow-sm">
                    <div class="px-10 py-10 flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-orange-600 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-orange-600/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="text-3xl font-black text-blue-950 italic">Wachtlijst</h3>
                                <p class="text-orange-600 font-black text-[10px] uppercase tracking-widest mt-1">Nieuwe registraties in afwachting</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-10 pb-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="user in pendingUsers" :key="user.id" class="bg-white rounded-[2rem] p-8 shadow-sm border border-orange-100 flex flex-col justify-between group hover:border-orange-300 hover:shadow-xl transition-all">
                                <div class="text-left">
                                    <h4 class="text-2xl font-black text-blue-950 leading-tight italic">{{ user.name }}</h4>
                                    <p class="text-gray-400 font-black text-[10px] uppercase tracking-widest mt-1 mb-4">{{ user.email }}</p>
                                    
                                    <div v-if="user.instruments?.length > 0" class="flex flex-wrap gap-2">
                                        <span v-for="inst in user.instruments" :key="inst" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                            {{ inst }}
                                        </span>
                                    </div>
                                    <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between text-[10px] font-black text-gray-300">
                                        <span>AANGEVRAAGD:</span>
                                        <span>{{ new Date(user.created_at).toLocaleDateString('nl-NL') }}</span>
                                    </div>
                                </div>
                                <div class="mt-8 flex gap-3">
                                    <button
                                        @click="() => router.put(`/beheer/gebruikers/${user.id}/approve`)"
                                        class="flex-1 bg-green-600 text-white px-4 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 active:scale-95"
                                    >
                                        Toelaten
                                    </button>
                                    <button
                                        @click="deletePendingUser(user.id)"
                                        class="bg-gray-100 text-gray-400 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 hover:text-red-500 transition-all active:scale-95"
                                    >
                                        Wis
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Add New User Slide-down -->
                <div v-if="expandedSections.addNew" class="mb-12 bg-blue-950 rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden text-left">
                    <div class="absolute top-0 right-0 p-16 opacity-5 pointer-events-none">
                        <svg class="w-64 h-64" fill="white" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>

                    <div class="flex items-center justify-between mb-10 relative z-10">
                        <div>
                            <h3 class="text-4xl font-black italic">Gebruiker Toevoegen</h3>
                            <p class="text-blue-400 font-black text-[10px] uppercase tracking-[0.3em] mt-2">Nieuw lid direct in de database</p>
                        </div>
                        <button @click="expandedSections.addNew = false" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-white/10 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitNewUser" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-end relative z-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 ml-1">Volledige Naam</label>
                            <input v-model="newUser.name" type="text" required class="w-full bg-white/5 border-white/10 border-2 rounded-2xl px-6 py-4 text-sm font-black placeholder:text-blue-800 focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all" placeholder="Bijv. Jan de Boer" />
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 ml-1">E-mail Adres</label>
                            <input v-model="newUser.email" type="email" required class="w-full bg-white/5 border-white/10 border-2 rounded-2xl px-6 py-4 text-sm font-black placeholder:text-blue-800 focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all" placeholder="jan@voorbeeld.nl" />
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 ml-1">Wachtwoord</label>
                            <input v-model="newUser.password" type="password" required class="w-full bg-white/5 border-white/10 border-2 rounded-2xl px-6 py-4 text-sm font-black focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all" />
                        </div>
                        <div class="lg:col-span-3 pt-6 flex justify-end">
                            <button type="submit" class="bg-yellow-400 text-blue-950 px-10 py-5 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-yellow-300 transition-all active:scale-95 shadow-xl shadow-yellow-400/20">
                                Gebruiker aanmaken &larr;
                            </button>
                        </div>
                    </form>
                </div>

                <!-- SECTION 2: Active Users Table -->
                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-10 py-10 border-b border-gray-50 flex items-center justify-between">
                        <div class="text-left">
                            <h3 class="text-2xl font-black text-blue-950 italic">Ledenlijst</h3>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">Alle actieve accounts in de vereniging</p>
                        </div>
                        <div class="px-5 py-2 rounded-2xl bg-gray-50 text-blue-950 font-black text-sm border border-gray-100 italic">
                            {{ users.total }} Leden
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Muzikant</th>
                                    <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Rol / Rechten</th>
                                    <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Instrumenten</th>
                                    <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Geregistreerd</th>
                                    <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Acties</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-blue-50/30 transition-all group">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-6">
                                            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-900 group-hover:bg-blue-950 group-hover:text-white transition-all flex items-center justify-center font-black text-xl italic shadow-inner">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="text-left">
                                                <div class="text-lg font-black text-blue-950 leading-tight italic">{{ user.name }}</div>
                                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span
                                            class="inline-flex items-center px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest"
                                            :class="user.role === 'admin'
                                                ? 'bg-blue-950 text-white shadow-lg shadow-blue-900/10 italic'
                                                : 'bg-green-100 text-green-700'"
                                        >
                                            {{ user.role === 'admin' ? '✪ Beheerder' : 'Muzikant' }}
                                        </span>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div v-if="user.instruments?.length > 0" class="flex flex-wrap gap-2 max-w-[200px]">
                                            <span v-for="inst in user.instruments" :key="inst" class="px-3 py-1 bg-blue-50 text-blue-900 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100 italic">
                                                {{ inst }}
                                            </span>
                                        </div>
                                        <span v-else class="text-[9px] font-black text-gray-300 italic uppercase tracking-widest">Geen</span>
                                    </td>
                                    <td class="px-10 py-8 text-sm font-black text-gray-400 italic">
                                        {{ new Date(user.created_at).toLocaleDateString('nl-NL', { month: 'short', year: 'numeric' }) }}
                                    </td>
                                    <td class="px-10 py-8 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('beheer.gebruikers.edit', user.id)"
                                                class="p-4 rounded-xl hover:bg-blue-50 text-gray-200 hover:text-blue-950 transition-all active:scale-90"
                                            >
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button
                                                @click="() => {
                                                    if (confirm(`Weet je zeker dat je ${user.name} wilt verwijderen?`)) {
                                                        router.delete(route('beheer.gebruikers.destroy', user.id));
                                                    }
                                                }"
                                                class="p-4 rounded-xl hover:bg-red-50 text-gray-200 hover:text-red-500 transition-all active:scale-90"
                                            >
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="px-10 py-8 flex justify-between items-center bg-gray-50/50 border-t border-gray-50">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">
                            PAGINA {{ users.current_page }} VAN {{ users.last_page }}
                        </span>
                        <div class="flex gap-4">
                            <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-8 py-3 bg-white border border-gray-100 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 shadow-sm transition-all active:scale-95">Vorige</Link>
                            <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-8 py-3 bg-blue-950 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 shadow-xl shadow-blue-900/20 transition-all active:scale-95">Volgende</Link>
                        </div>
                    </div>
                </div>

                <div class="mt-20 text-center">
                    <Link :href="route('beheer.dashboard')" class="text-gray-400 hover:text-blue-950 text-[10px] font-black uppercase tracking-[0.4em] transition-colors">
                        &larr; Terug naar overview
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
