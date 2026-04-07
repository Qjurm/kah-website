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

const selectedInstruments = ref(props.userInstruments.map(i => ({ id: i.id, name: i.name, checked: true })));

const submit = () => {
    form.post(route('profile.instruments.update'), {
        onSuccess: () => {
            // Component resets automatically via Inertia
        },
    });
};

function toggleInstrument(instrumentId) {
    const instrument = selectedInstruments.value.find(i => i.id === instrumentId);
    if (instrument) {
        instrument.checked = !instrument.checked;
        form.instruments = selectedInstruments.value.filter(i => i.checked).map(i => i.id);
        
        // If unchecked and it's the primary, clear primary
        if (!instrument.checked && form.primary_instrument === instrumentId) {
            form.primary_instrument = null;
        }
    }
}

function selectAllInstruments() {
    selectedInstruments.value.forEach(i => i.checked = true);
    form.instruments = props.availableInstruments.map(i => i.id);
}

function deselectAllInstruments() {
    selectedInstruments.value.forEach(i => i.checked = false);
    form.instruments = [];
    form.primary_instrument = null;
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-gray-900">Mijn Instrumenten</h2>
            <p class="mt-1 text-sm text-gray-600">
                Welke instrumenten bespeel je? Selecteer alle instrumenten en kies je voornaamste.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">

            <!-- Main Instruments Selection -->
            <div>
                <InputLabel for="instruments" value="Welke instrumenten bespeel je?" />
                
                <div class="mt-3 flex gap-2 text-xs">
                    <button
                        type="button"
                        @click="selectAllInstruments"
                        class="px-3 py-1 bg-blue-100 text-blue-900 rounded hover:bg-blue-200 font-medium"
                    >
                        Alles selecteren
                    </button>
                    <button
                        type="button"
                        @click="deselectAllInstruments"
                        class="px-3 py-1 bg-gray-200 text-gray-900 rounded hover:bg-gray-300 font-medium"
                    >
                        Alles deselecteren
                    </button>
                </div>

                <!-- Instruments Checklist -->
                <div class="mt-4 space-y-3 bg-gray-50 rounded-lg p-4 border border-gray-200 max-h-96 overflow-y-auto">
                    <div v-if="availableInstruments.length === 0" class="text-center text-gray-400 py-8">
                        Geen instrumenten beschikbaar
                    </div>

                    <div
                        v-for="instrument in availableInstruments"
                        :key="instrument.id"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white transition-colors cursor-pointer"
                        @click="toggleInstrument(instrument.id)"
                    >
                        <input
                            type="checkbox"
                            :checked="selectedInstruments.find(i => i.id === instrument.id)?.checked"
                            @change="toggleInstrument(instrument.id)"
                            class="w-5 h-5 rounded border-gray-300 text-blue-900 cursor-pointer"
                        />
                        <label class="flex-1 text-gray-900 font-medium cursor-pointer text-base">
                            {{ instrument.name }}
                        </label>
                        
                        <!-- Primary Radio Button -->
                        <div class="flex items-center gap-1">
                            <input
                                type="radio"
                                :name="'primary_' + instrument.id"
                                :value="instrument.id"
                                v-model="form.primary_instrument"
                                :disabled="!selectedInstruments.find(i => i.id === instrument.id)?.checked"
                                class="w-4 h-4 text-yellow-500 cursor-pointer"
                            />
                            <span class="text-xs text-gray-600">Primair</span>
                        </div>
                    </div>
                </div>

                <InputError class="mt-2" :message="form.errors.instruments" />
            </div>

            <!-- Summary Card -->
            <div v-if="form.instruments && form.instruments.length > 0" class="bg-blue-50 border-l-4 border-blue-900 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Je selectie:</h3>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-700">
                        <strong>Aantal instrumenten:</strong> {{ form.instruments.length }}
                    </p>
                    <p v-if="form.primary_instrument" class="text-gray-700">
                        <strong>Voornaamste instrument:</strong> 
                        {{ availableInstruments.find(i => i.id === form.primary_instrument)?.name }}
                    </p>
                    <p v-else class="text-red-700 font-semibold">
                        Kies je voornaamste instrument hierboven
                    </p>
                </div>
            </div>

            <!-- Instructions for older users -->
            <div class="bg-yellow-50 border-l-4 border-yellow-600 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Hoe het werkt:</h3>
                <ul class="text-sm text-gray-700 space-y-1 list-disc list-inside">
                    <li>Vink alle instrumenten aan die je speelt</li>
                    <li>Kies welk instrument je voornaamste is (dit is je standaard filterinstrument)</li>
                    <li>Als je klaar bent, klik 'Opslaan' onderaan</li>
                    <li>Dit helpt je om de juiste partijen in de muziekbibliotheek te vinden</li>
                </ul>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                <PrimaryButton :disabled="form.processing || !form.instruments || form.instruments.length === 0 || !form.primary_instrument">
                    Opslaan
                </PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-show="form.recentlySuccessful" class="text-sm text-green-600 font-semibold">
                        ✓ Opgeslagen!
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
