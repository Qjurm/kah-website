<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    userInstruments: Array,
    availableInstruments: Array,
});

const form = useForm({
    instruments: props.userInstruments.map(i => i.id),
    primary_instrument: props.userInstruments.find(i => i.pivot.is_primary)?.id || null,
});

const isOpen = ref(false);

const submit = () => {
    form.post(route('profile.instruments.update'), {
        onSuccess: () => {
            isOpen.value = false;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Mijn instrumenten</h2>
            <p class="mt-1 text-sm text-gray-600">
                Selecteer je instrumenten en kies je primaire instrument.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="instruments" value="Instrumenten" />
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
                                v-for="instrument in availableInstruments"
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
                                            if (form.primary_instrument === instrument.id) {
                                                form.primary_instrument = null;
                                            }
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
            <div v-if="form.instruments && form.instruments.length > 0">
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
                        {{ availableInstruments.find(i => i.id === id)?.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.primary_instrument" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Opslaan</PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-show="form.recentlySuccessful" class="text-sm text-gray-600">Opgeslagen.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
