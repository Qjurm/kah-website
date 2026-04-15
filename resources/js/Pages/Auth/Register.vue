<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    instruments: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    instruments: [],
    primary_instrument: null,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registreren" />

        <div class="text-left mb-10">
            <h1 class="text-4xl font-black text-blue-950 italic leading-tight">Word Lid</h1>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Meld je aan bij de vereniging</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="text-left">
                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Volledige Naam</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                    placeholder="Bijv. Jan de Boer"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="text-left">
                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">E-mailadres</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                    placeholder="jouw@email.nl"
                    required
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Instrument Selection -->
            <div class="text-left pt-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-4 ml-1">Kies je instrument(en)</label>
                <div class="grid grid-cols-2 gap-3 max-h-[300px] overflow-y-auto p-1 pr-2 scrollbar-thin">
                    <div 
                        v-for="instrument in instruments" 
                        :key="instrument.id"
                        @click="() => {
                            if (form.instruments.includes(instrument.id)) {
                                form.instruments = form.instruments.filter(id => id !== instrument.id);
                                if (form.primary_instrument === instrument.id) form.primary_instrument = null;
                            } else {
                                form.instruments.push(instrument.id);
                                if (!form.primary_instrument) form.primary_instrument = instrument.id;
                            }
                        }"
                        class="p-4 rounded-xl border-2 transition-all cursor-pointer text-left active:scale-95"
                        :class="form.instruments.includes(instrument.id)
                            ? 'bg-blue-950 border-blue-950 text-white shadow-lg shadow-blue-900/10'
                            : 'bg-white border-gray-100 hover:border-blue-100 text-blue-950'"
                    >
                        <div class="flex items-center justify-between mb-2">
                             <div class="w-5 h-5 rounded-full flex items-center justify-center" :class="form.instruments.includes(instrument.id) ? 'bg-yellow-400 text-blue-950' : 'bg-gray-100 text-gray-300'">
                                <svg v-if="form.instruments.includes(instrument.id)" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12M6 12h12"/></svg>
                            </div>
                            <span v-if="form.primary_instrument === instrument.id" class="text-[8px] font-black uppercase bg-yellow-400 text-blue-950 px-2 py-0.5 rounded-full">Primair</span>
                            <button 
                                v-else-if="form.instruments.includes(instrument.id)"
                                @click.stop="form.primary_instrument = instrument.id"
                                class="text-[8px] font-black uppercase text-blue-400 hover:text-white"
                            >
                                Maak primair
                            </button>
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-widest truncate leading-tight">{{ instrument.name }}</div>
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.instruments" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Wachtwoord</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"
                        required
                    />
                </div>
                <div class="text-left">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Bevestig</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"
                        required
                    />
                </div>
            </div>
            <InputError class="mt-2" :message="form.errors.password" />

            <div class="pt-6">
                <button
                    type="submit"
                    class="w-full bg-blue-950 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Bezig...' : 'Lid Worden &rarr;' }}
                </button>
            </div>

            <div class="pt-4 text-center">
                <Link
                    :href="route('login')"
                    class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 transition-colors"
                >
                    Heb je al een account? Log in
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
