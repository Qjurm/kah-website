<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    instruments: Array,
});

const form = useForm({
    title: '',
    composer: '',
    arranger: '',
    parts: [], // { pdf, fileName, instrument, original_parsed_instrument }
});

const fileInput = ref(null);

function handleFileUpload(e) {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        let suggestedInstrument = '';
        
        // Clean filename for better matching: remove ext, remove leading/trailing noise
        let rawName = file.name.replace(/\.[^/.]+$/, ""); // remove extension
        let cleanName = rawName.toLowerCase()
            .replace(/^[0-9\s._-]+/, "") // remove leading numbers/dots
            .replace(/[0-9\s._-]+$/, "") // remove trailing numbers
            .trim();
        
        // Try exact match or alias match
        props.instruments.forEach(inst => {
            const instName = inst.name.toLowerCase();
            if (cleanName === instName || cleanName.includes(instName)) {
                suggestedInstrument = inst.name;
            }
            if (inst.aliases) {
                inst.aliases.forEach(alias => {
                    if (cleanName === alias.toLowerCase() || cleanName.includes(alias.toLowerCase())) {
                        suggestedInstrument = inst.name;
                    }
                });
            }
        });

        form.parts.push({
            pdf: file,
            fileName: file.name,
            instrument: suggestedInstrument,
            original_parsed_instrument: cleanName // Send the cleaned "raw" name for alias training
        });
    });
}

function submit() {
    form.post(route('beheer.bladmuziek.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nieuw stuk toevoegen" />

        <template #header>
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Nieuw Repertoire</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Toevoegen aan de bibliotheek</p>
                </div>
                <Link :href="route('beheer.bladmuziek.index')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 px-6 py-3 transition-colors">
                    Terug naar lijst
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 text-left">
                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Titel van het Stuk</label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Bijv. The Second Waltz"
                                    class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                                    required
                                />
                                <div v-if="form.errors.title" class="mt-2 text-[10px] font-black text-red-500 uppercase tracking-widest ml-1">{{ form.errors.title }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Componist</label>
                                    <input
                                        v-model="form.composer"
                                        type="text"
                                        placeholder="Bijv. Dmitri Shostakovich"
                                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                                        required
                                    />
                                    <div v-if="form.errors.composer" class="mt-2 text-[10px] font-black text-red-500 uppercase tracking-widest ml-1">{{ form.errors.composer }}</div>
                                </div>

                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 ml-1">Arrangeur (Optioneel)</label>
                                    <input
                                        v-model="form.arranger"
                                        type="text"
                                        placeholder="Naam arrangeur"
                                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all placeholder:text-gray-300"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-black text-blue-950 italic">Partijen Uploaden</h3>
                            
                            <div class="p-8 border-2 border-dashed border-gray-100 rounded-[2rem] bg-gray-50/50 text-center">
                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf"
                                    @change="handleFileUpload"
                                    class="hidden"
                                    ref="fileInput"
                                />
                                <div @click="$refs.fileInput.click()" class="cursor-pointer group">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:bg-blue-950 group-hover:text-white transition-all">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <p class="text-sm font-black text-blue-950">Klik om PDF's te selecteren</p>
                                    <p class="text-[10px] font-black uppercase text-gray-400 mt-2">Meerdere bestanden tegelijk toegestaan</p>
                                </div>
                            </div>

                            <div v-if="form.parts.length > 0" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                                <div v-for="(part, index) in form.parts" :key="index" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-[10px] shrink-0">
                                        PDF
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[11px] font-black text-blue-950 truncate">{{ part.fileName }}</div>
                                        <select v-model="part.instrument" class="mt-1 w-full bg-gray-50 border-none rounded-xl px-3 py-1.5 text-[10px] font-black focus:ring-1 focus:ring-yellow-400">
                                            <option value="" disabled>Kies instrument...</option>
                                            <option v-for="inst in instruments" :key="inst.id" :value="inst.name">{{ inst.name }}</option>
                                        </select>
                                    </div>
                                    <button @click.prevent="form.parts.splice(index, 1)" class="text-red-300 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-blue-950 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                            >
                                {{ form.processing ? __('Opslaan...') : __('Stuk Aanmaken') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
