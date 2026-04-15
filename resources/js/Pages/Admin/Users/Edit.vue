<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object,
    allInstruments: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    instrument_ids: props.user.instrument_ids,
    primary_instrument_id: props.user.primary_instrument_id,
});

function submit() {
    form.put(route('beheer.gebruikers.update', props.user.id));
}

function toggleInstrument(id) {
    const idx = form.instrument_ids.indexOf(id);
    if (idx > -1) {
        form.instrument_ids.splice(idx, 1);
        if (form.primary_instrument_id === id) {
            form.primary_instrument_id = null;
        }
    } else {
        form.instrument_ids.push(id);
        if (!form.primary_instrument_id) {
            form.primary_instrument_id = id;
        }
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="'Gebruiker Bewerken: ' + user.name" />

        <template #header>
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Gebruiker Aanpassen</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ user.name }}</p>
                </div>
                <Link :href="route('beheer.gebruikers.index')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 px-6 py-3 transition-colors">
                    Terug naar lijst
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <!-- Left: Basic Info -->
                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">Basis Gegevens</h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Naam</label>
                                    <input v-model="form.name" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">E-mailadres</label>
                                    <input v-model="form.email" type="email" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Rol</label>
                                    <select v-model="form.role" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all cursor-pointer">
                                        <option value="musician">Muzikant</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-blue-950 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Opslaan...' : 'Gebruiker Bijwerken' }}
                        </button>
                    </div>

                    <!-- Right: Instrument Selection -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">Instrumenten Beheren</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div 
                                    v-for="inst in allInstruments" 
                                    :key="inst.id"
                                    @click="toggleInstrument(inst.id)"
                                    class="p-5 rounded-2xl border transition-all cursor-pointer group active:scale-95"
                                    :class="form.instrument_ids.includes(inst.id) 
                                        ? 'bg-blue-950 border-blue-950 text-white' 
                                        : 'bg-white border-gray-100 hover:border-blue-200'"
                                >
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all" :class="form.instrument_ids.includes(inst.id) ? 'bg-yellow-400 text-blue-950' : 'bg-gray-100 text-gray-300'">
                                            <svg v-if="form.instrument_ids.includes(inst.id)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12M6 12h12"/></svg>
                                        </div>
                                        <div 
                                            v-if="form.instrument_ids.includes(inst.id)"
                                            @click.stop="form.primary_instrument_id = inst.id"
                                            class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest transition-all"
                                            :class="form.primary_instrument_id === inst.id 
                                                ? 'bg-yellow-400 text-blue-950' 
                                                : 'text-white/40 hover:text-white'"
                                        >
                                            {{ form.primary_instrument_id === inst.id ? 'Primair' : 'Maak Primair' }}
                                        </div>
                                    </div>
                                    <div class="font-black text-xs uppercase tracking-widest leading-tight">{{ inst.name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
