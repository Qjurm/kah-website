<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Inloggen" />

        <div class="text-left mb-10">
            <h1 class="text-4xl font-black text-blue-950 italic leading-tight">Welkom Terug</h1>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Log in bij de vereniging</p>
        </div>

        <div v-if="status" class="mb-6 bg-green-50 text-green-700 p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-green-100">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="text-left">
                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">E-mailadres</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                    placeholder="jouw@email.nl"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="text-left">
                <div class="flex items-center justify-between mb-3 ml-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-blue-950/40">Wachtwoord</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[9px] font-black uppercase tracking-widest text-blue-400 hover:text-blue-950 transition-colors"
                    >
                        Wachtwoord vergeten?
                    </Link>
                </div>
                <input
                    v-model="form.password"
                    type="password"
                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" v-model="form.remember" class="sr-only peer" />
                        <div class="w-10 h-6 bg-gray-100 rounded-full peer peer-checked:bg-blue-950 transition-all after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4 shadow-inner"></div>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-blue-950 transition-colors">Onthoud mij</span>
                </label>
            </div>

            <div class="pt-6">
                <button
                    type="submit"
                    class="w-full bg-blue-950 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Bezig...' : 'Inloggen &rarr;' }}
                </button>
            </div>
        </form>

        <!-- New Registration Entry -->
        <div class="mt-12 pt-10 border-t border-gray-50 text-center">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Nieuw bij de vereniging?</p>
            <Link
                :href="route('register')"
                class="inline-block w-full border-2 border-gray-100 text-blue-950 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-blue-950 transition-all active:scale-95"
            >
                Account Aanvragen
            </Link>
        </div>
    </GuestLayout>
</template>
