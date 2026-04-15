<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'musician',
});

function submit() {
    form.post(route('beheer.gebruikers.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nieuwe gebruiker" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">Gebruiker Toevoegen</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Account handmatig registreren</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 text-left">
                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Volledige Naam</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Bijv. Jan de Boer"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                                />
                                <p v-if="form.errors.name" class="text-red-500 text-[10px] font-black uppercase tracking-widest ml-1">{{ form.errors.name }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">E-mailadres</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="jan@voorbeeld.nl"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                                />
                                <p v-if="form.errors.email" class="text-red-500 text-[10px] font-black uppercase tracking-widest ml-1">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Wachtwoord</label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"
                                />
                                <p v-if="form.errors.password" class="text-red-500 text-[10px] font-black uppercase tracking-widest ml-1">{{ form.errors.password }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Bevestig Wachtwoord</label>
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"
                                />
                            </div>

                            <div class="space-y-3 md:col-span-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Systeem Rol</label>
                                <select
                                    v-model="form.role"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all appearance-none cursor-pointer"
                                >
                                    <option value="musician">Muzikant (Standaard)</option>
                                    <option value="admin">Administrator (Volledige Toegang)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 bg-blue-950 text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                            >
                                Gebruiker Opslaan
                            </button>
                            <Link :href="route('beheer.gebruikers.index')" class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-gray-200 hover:text-blue-950 text-center transition-all active:scale-95">
                                Annuleren
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
