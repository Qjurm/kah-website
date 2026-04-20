<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SectionCard from '@/Components/SectionCard.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    sections: Array,
});

const form = useForm({
    name: '',
    display_order: 0,
});

const editForm = useForm({
    id: null,
    name: '',
    display_order: 0,
});

const editingSectionId = ref(null);

function submit() {
    form.post(route('beheer.instrumenten-secties.store'), {
        onSuccess: () => form.reset(),
    });
}

function startEdit(section) {
    editingSectionId.ref = section.id;
    editForm.name = section.name;
    editForm.display_order = section.display_order;
    editingSectionId.value = section.id;
}

function cancelEdit() {
    editingSectionId.value = null;
    editForm.reset();
}

function updateSection(id) {
    editForm.put(route('beheer.instrumenten-secties.update', id), {
        onSuccess: () => cancelEdit(),
    });
}

function deleteSection(id) {
    if (confirm('Weet je zeker dat je deze sectie wilt verwijderen? Dit kan alleen als er geen instrumenten aan gekoppeld zijn.')) {
        useForm({}).delete(route('beheer.instrumenten-secties.destroy', id));
    }
}
</script>

<template>
    <Head title="Sectie Beheer" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black uppercase italic tracking-tight text-blue-950">Sectie Beheer</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Quick Add Section -->
                <SectionCard title="Nieuwe Sectie Toevoegen">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 items-end gap-6">
                        <div>
                            <InputLabel for="name" value="Sectie Naam" class="uppercase text-xs font-bold tracking-widest text-blue-900/60 mb-2" />
                            <TextInput 
                                id="name" 
                                type="text" 
                                class="w-full border-2 border-blue-950/10 focus:border-yellow-400 focus:ring-0 rounded-2xl px-5 py-3 transition-all font-medium" 
                                v-model="form.name" 
                                placeholder="bijv. Houtblazers"
                                required 
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div>
                            <InputLabel for="display_order" value="Volgorde" class="uppercase text-xs font-bold tracking-widest text-blue-900/60 mb-2" />
                            <TextInput 
                                id="display_order" 
                                type="number" 
                                class="w-full border-2 border-blue-950/10 focus:border-yellow-400 focus:ring-0 rounded-2xl px-5 py-3 transition-all font-medium" 
                                v-model="form.display_order" 
                            />
                            <InputError class="mt-2" :message="form.errors.display_order" />
                        </div>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-blue-950 text-white px-8 py-4 rounded-2xl font-black uppercase italic tracking-wider hover:bg-blue-900 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-950/20 disabled:opacity-50"
                        >
                            Voeg Toe +
                        </button>
                    </form>
                </SectionCard>

                <!-- Sections List -->
                <div class="bg-white rounded-[2rem] shadow-2xl shadow-blue-950/5 border border-blue-950/5 overflow-hidden">
                    <div class="p-8 border-b border-blue-950/5">
                        <h3 class="text-xl font-black uppercase italic tracking-tight text-blue-950">Alle Secties</h3>
                    </div>

                    <div class="divide-y divide-blue-950/5">
                        <div v-for="section in sections" :key="section.id" class="p-6 transition-all hover:bg-blue-50/30">
                            <div v-if="editingSectionId === section.id" class="flex flex-col md:flex-row items-end gap-4">
                                <div class="flex-1">
                                    <TextInput 
                                        type="text" 
                                        class="w-full border-2 border-yellow-400 focus:ring-0 rounded-xl px-4 py-2 font-medium" 
                                        v-model="editForm.name" 
                                        required 
                                    />
                                </div>
                                <div class="w-24">
                                    <TextInput 
                                        type="number" 
                                        class="w-full border-2 border-yellow-400 focus:ring-0 rounded-xl px-4 py-2 font-medium" 
                                        v-model="editForm.display_order" 
                                        required 
                                    />
                                </div>
                                <div class="flex gap-2">
                                    <button @click="updateSection(section.id)" class="p-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button @click="cancelEdit" class="p-3 bg-gray-400 text-white rounded-xl hover:bg-gray-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="flex items-center justify-between">
                                <div class="flex items-center gap-6">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black">
                                        {{ section.display_order }}
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-black uppercase italic tracking-tight text-blue-950 leading-none mb-1">{{ section.name }}</h4>
                                        <span class="text-xs font-bold uppercase tracking-widest text-blue-900/40">
                                            {{ section.instruments_count }} instrumenten
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button @click="startEdit(section)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button 
                                        @click="deleteSection(section.id)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors"
                                        title="Verwijderen"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
