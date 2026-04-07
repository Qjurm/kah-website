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
            <div class="mt-4">
                <InputLabel for="instruments" value="Jouw instrumenten" />
                <div class="relative mt-1">
                    <button
                        @click.prevent="isOpen = !isOpen"
                        type="button"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <span v-if="!form.instruments || form.instruments.length === 0" class="text-gray-500">
                            Selecteer instrumenten...
                        </span>
                        <span v-else class="text-gray-900">
                            {{ form.instruments.length }} instrument(en) geselecteerd
                        </span>
                    </button>

                    <div v-if="isOpen" class="absolute z-10 w-full mt-1 border border-gray-300 rounded-lg bg-white shadow-lg">
                        <div class="max-h-60 overflow-y-auto p-2">
                            <label
                                v-for="instrument in instruments"
                                :key="instrument.id"
                                class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.instruments?.includes(instrument.id)"
                                    @change="(e) => {
                                        if (e.target.checked) {
                                            form.instruments.push(instrument.id);
                                        } else {
                                            form.instruments = form.instruments.filter(id => id !== instrument.id);
                                        }
                                    }"
                                    class="rounded"
                                />
                                <span class="text-gray-900">{{ instrument.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.instruments" />
            </div>

            <!-- Primary Instrument Selection -->
            <div v-if="form.instruments && form.instruments.length > 0" class="mt-4">
                <InputLabel for="primary_instrument" value="Primair instrument" />
                <select
                    v-model="form.primary_instrument"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option :value="null">Selecteer een primair instrument</option>
                    <option
                        v-for="id in form.instruments"
                        :key="id"
                        :value="id"
                    >
                        {{ instruments.find(i => i.id === id)?.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.primary_instrument" />
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
