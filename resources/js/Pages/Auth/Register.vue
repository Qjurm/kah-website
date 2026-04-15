<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import MultiSelect from '@/Components/MultiSelect.vue';
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

const isOpen = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registreren" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Naam" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="E-mailadres" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Instrument Selection -->
            <div class="mt-8">
                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-4 ml-1">Kies je instrument(en)</label>
                <div class="grid grid-cols-2 gap-3 max-h-[300px] overflow-y-auto p-1 scrollbar-thin">
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
                            ? 'bg-blue-950 border-blue-950 text-white'
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
                        <div class="text-[10px] font-black uppercase tracking-widest truncate">{{ instrument.name }}</div>
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.instruments" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Wachtwoord" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Bevestig wachtwoord"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Heb je al een account?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registreren
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
