<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionCard from '@/Components/SectionCard.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    instruments: Array,
});

const form = useForm({
    name: '',
});

const searchQuery = ref('');
const expandedInstrumentId = ref(null);

const filteredInstruments = computed(() => {
    if (!searchQuery.value) return props.instruments;
    const lower = searchQuery.value.toLowerCase();
    return props.instruments.filter(i => i.name.toLowerCase().includes(lower));
});

function toggleAccordion(id) {
    expandedInstrumentId.value = expandedInstrumentId.value === id ? null : id;
}

function submit() {
    form.post(route('beheer.instrumenten.store'), {
        onSuccess: () => form.reset(),
    });
}

function deleteInstrument(id) {
    if (confirm('Weet je zeker dat je dit instrument wilt verwijderen?')) {
        useForm({}).delete(route('beheer.instrumenten.destroy', id));
    }
}
</script>

<template>
    <Head title="Instrumenten Beheer" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black uppercase italic tracking-tight text-blue-950">Instrumenten Beheer</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Quick Add Section -->
                <SectionCard title="Nieuw Instrument Toevoegen">
                    <form @submit.prevent="submit" class="flex flex-col md:flex-row items-end gap-4">
                        <div class="flex-1">
                            <InputLabel for="name" value="Instrument Naam" class="uppercase text-xs font-bold tracking-widest text-blue-900/60 mb-2" />
                            <TextInput 
                                id="name" 
                                type="text" 
                                class="w-full border-2 border-blue-950/10 focus:border-yellow-400 focus:ring-0 rounded-2xl px-5 py-3 transition-all font-medium" 
                                v-model="form.name" 
                                placeholder="bijv. Saxofoon Alt"
                                required 
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-blue-950 text-white px-8 py-3.5 rounded-2xl font-black uppercase italic tracking-wider hover:bg-blue-900 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-950/20 disabled:opacity-50"
                        >
                            Voeg Toe +
                        </button>
                    </form>
                </SectionCard>

                <!-- Instruments List -->
                <div class="bg-white rounded-[2rem] shadow-2xl shadow-blue-950/5 border border-blue-950/5 overflow-hidden">
                    <div class="p-8 border-b border-blue-950/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h3 class="text-xl font-black uppercase italic tracking-tight text-blue-950">Alle Instrumenten</h3>
                        <div class="relative w-full md:w-64">
                            <input 
                                type="text" 
                                v-model="searchQuery"
                                placeholder="Zoek instrument..."
                                class="w-full pl-10 pr-4 py-2 bg-blue-50/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-yellow-400 transition-all font-medium"
                            />
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-blue-950/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="divide-y divide-blue-950/5">
                        <div v-for="instrument in filteredInstruments" :key="instrument.id" class="transition-all hover:bg-blue-50/30">
                            <!-- Header / Summary -->
                            <div 
                                @click="toggleAccordion(instrument.id)"
                                class="p-6 flex items-center justify-between cursor-pointer"
                            >
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 bg-blue-950 text-yellow-400 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-950/10 shrink-0">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-black uppercase italic tracking-tight text-blue-950 leading-none mb-1">{{ instrument.name }}</h4>
                                        <div class="flex gap-4">
                                            <span class="text-xs font-bold uppercase tracking-widest text-blue-900/40">
                                                {{ instrument.users_count }} {{ instrument.users_count === 1 ? 'Lid' : 'Leden' }}
                                            </span>
                                            <Link :href="route('beheer.bladmuziek.index', { instrument: instrument.id })" class="text-xs font-bold uppercase tracking-widest text-blue-900/40 hover:text-blue-600 transition-colors underline decoration-dotted">
                                                {{ instrument.parts_count }} {{ instrument.parts_count === 1 ? 'Partij' : 'Partijen' }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button 
                                        @click.stop="deleteInstrument(instrument.id)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors shrink-0"
                                        title="Verwijderen"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <svg 
                                        class="w-6 h-6 text-blue-950/20 transition-transform duration-300"
                                        :class="{ 'rotate-180': expandedInstrumentId === instrument.id }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>

                            <!-- Details Accordion -->
                            <div 
                                v-show="expandedInstrumentId === instrument.id"
                                class="px-6 pb-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-blue-950/5 bg-blue-50/20"
                            >
                                <!-- Leden Column -->
                                <div class="space-y-4 pt-6">
                                    <h5 class="text-xs font-black uppercase tracking-[0.2em] text-blue-900/40 border-b border-blue-950/5 pb-2">Bespeeld Door</h5>
                                    <div class="space-y-2" v-if="instrument.members.length">
                                        <Link 
                                            v-for="member in instrument.members" 
                                            :key="member.id"
                                            :href="route('beheer.gebruikers.edit', member.id)"
                                            class="flex items-center gap-3 p-3 bg-white rounded-xl border border-blue-950/5 hover:border-yellow-400 hover:scale-[1.02] transition-all group"
                                        >
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs uppercase tracking-tighter">
                                                {{ member.name.charAt(0) }}
                                            </div>
                                            <span class="font-bold text-sm text-blue-950 group-hover:text-blue-700">{{ member.name }}</span>
                                            <svg class="w-4 h-4 ml-auto text-blue-950/20 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </Link>
                                    </div>
                                    <p v-else class="text-sm italic text-blue-950/40 py-2">Nog geen leden toegewezen.</p>
                                </div>

                                <!-- Repertoire Column -->
                                <div class="space-y-4 pt-6">
                                    <h5 class="text-xs font-black uppercase tracking-[0.2em] text-blue-900/40 border-b border-blue-950/5 pb-2">Repertoire</h5>
                                    <div class="space-y-2" v-if="instrument.repertoire.length">
                                        <Link 
                                            v-for="piece in instrument.repertoire" 
                                            :key="piece.id"
                                            :href="route('beheer.bladmuziek.edit', piece.score_id)"
                                            class="flex items-center gap-3 p-3 bg-white rounded-xl border border-blue-950/5 hover:border-yellow-400 hover:scale-[1.02] transition-all group"
                                        >
                                            <div class="w-8 h-8 rounded-lg bg-yellow-400/10 flex items-center justify-center text-yellow-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <span class="font-bold text-sm text-blue-950 group-hover:text-blue-700 transition-colors">{{ piece.title }}</span>
                                            <svg class="w-4 h-4 ml-auto text-blue-950/20 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </Link>
                                    </div>
                                    <p v-else class="text-sm italic text-blue-950/40 py-2">Geen bladmuziek gevonden.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
