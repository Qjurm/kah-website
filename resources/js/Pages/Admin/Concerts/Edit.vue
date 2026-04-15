<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    concert: Object,
    allScores: Array,
});

const form = useForm({
    title: props.concert.title,
    date: props.concert.date,
    location: props.concert.location,
    photo: null,
    is_current: !!props.concert.is_current,
    score_ids: props.concert.scores.map(s => s.id),
});

const photoPreview = ref(props.concert.photo_path ? '/storage/' + props.concert.photo_path : null);
const photoInput = ref(null);

function handlePhotoUpload(e) {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

const search = ref('');
const filteredScores = computed(() => {
    if (!search.value) return props.allScores;
    const q = search.value.toLowerCase();
    return props.allScores.filter(s => 
        s.title.toLowerCase().includes(q) || 
        s.composer.toLowerCase().includes(q)
    );
});

function toggleScore(scoreId) {
    const idx = form.score_ids.indexOf(scoreId);
    if (idx > -1) form.score_ids.splice(idx, 1);
    else form.score_ids.push(scoreId);
}

function submit() {
    router.post(route('beheer.concerten.update', props.concert.id), {
        _method: 'put',
        ...form.data(),
    }, {
        forceFormData: true,
    });
}

function move(scoreId, direction) {
    const idx = form.score_ids.indexOf(scoreId);
    if (idx < 0) return;
    const newIdx = idx + direction;
    if (newIdx < 0 || newIdx >= form.score_ids.length) return;
    
    const temp = form.score_ids[idx];
    form.score_ids[idx] = form.score_ids[newIdx];
    form.score_ids[newIdx] = temp;
}

const selectedScoresFull = computed(() => {
    return form.score_ids.map(id => props.allScores.find(s => s.id === id)).filter(Boolean);
});

function formatDate(d) {
    return new Date(d).toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="'Bewerken: ' + concert.title" />

        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1 text-left">
                    <h2 class="text-xl font-black leading-tight text-blue-950 italic">Project Aanpassen</h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ concert.title }}</p>
                </div>
                <Link :href="route('beheer.concerten.index')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-blue-950 px-6 py-3 transition-colors">
                    Annuleren
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <!-- Form Side -->
                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-lg font-black text-blue-950 italic mb-8">Basis Gegevens</h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Titel</label>
                                    <input v-model="form.title" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Datum</label>
                                    <input v-model="form.date" type="date" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Locatie</label>
                                    <input v-model="form.location" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all"/>
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-blue-950/40 mb-3 ml-1">Project Afbeelding</label>
                                    <div 
                                        @click="$refs.photoInput.click()"
                                        class="relative w-full aspect-video rounded-2xl bg-gray-50 border-2 border-dashed border-gray-100 flex flex-col items-center justify-center cursor-pointer overflow-hidden group hover:border-blue-200 transition-all"
                                    >
                                        <input 
                                            type="file" 
                                            ref="photoInput" 
                                            class="hidden" 
                                            accept="image/*"
                                            @change="handlePhotoUpload"
                                        />
                                        
                                        <template v-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover"/>
                                            <div class="absolute inset-0 bg-blue-950/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all px-4 text-center">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-white">Verander Afbeelding</span>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <svg class="w-8 h-8 text-gray-200 group-hover:text-blue-200 transition-all mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-300">Klik om foto toe te voegen</span>
                                        </template>
                                    </div>
                                    <div v-if="form.errors.photo" class="mt-2 text-[10px] font-black text-red-500 uppercase tracking-widest ml-1">{{ form.errors.photo }}</div>
                                </div>
                                <div class="pt-4">
                                    <label class="flex items-center gap-4 cursor-pointer group">
                                        <div class="relative">
                                            <input type="checkbox" v-model="form.is_current" class="sr-only"/>
                                            <div class="w-14 h-8 bg-gray-200 rounded-full transition-colors group-hover:bg-gray-300" :class="{'bg-yellow-400': form.is_current}"></div>
                                            <div class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full transition-transform" :class="{'translate-x-6': form.is_current}"></div>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-950">Huidig Focus Project</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-blue-950 text-white py-6 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-blue-900/20 active:scale-95 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Opslaan...' : 'Project Bijwerken' }}
                        </button>
                    </div>

                    <!-- Program Side -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left">
                            <div class="flex items-center justify-between mb-10">
                                <h3 class="text-lg font-black text-blue-950 italic">Programma & Volgorde</h3>
                                <div class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ form.score_ids.length }} Stukken
                                </div>
                            </div>

                            <div v-if="selectedScoresFull.length === 0" class="py-20 text-center bg-gray-50/50 rounded-[2rem] border-2 border-dashed border-gray-100 mb-10">
                                <p class="text-gray-300 font-black italic">Selecteer bladmuziek in de lijst hieronder.</p>
                            </div>
                            
                            <div v-else class="space-y-4 mb-10">
                                <div v-for="(sc, i) in selectedScoresFull" :key="'sel-'+sc.id" class="flex items-center gap-6 p-6 bg-gray-50/50 rounded-2xl border border-gray-100 group">
                                    <div class="w-8 h-8 rounded-full bg-blue-950 text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-lg">
                                        {{ i + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-black text-blue-950 italic text-base leading-tight">{{ sc.title }}</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ sc.composer }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="move(sc.id, -1)" :disabled="i === 0" class="p-3 rounded-xl bg-white text-gray-400 hover:text-blue-950 transition-all disabled:opacity-20 shadow-sm border border-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button type="button" @click="move(sc.id, 1)" :disabled="i === selectedScoresFull.length - 1" class="p-3 rounded-xl bg-white text-gray-400 hover:text-blue-950 transition-all disabled:opacity-20 shadow-sm border border-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button type="button" @click="toggleScore(sc.id)" class="p-3 rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all shadow-sm border border-red-100/50 ml-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-50 mb-10"/>

                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 relative">
                                        <input v-model="search" type="text" placeholder="Zoek bladmuziek..." class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-xs font-black text-blue-950 focus:ring-2 focus:ring-yellow-400 transition-all pl-12"/>
                                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-200">
                                    <button 
                                        type="button"
                                        v-for="sc in filteredScores" 
                                        :key="'lib-'+sc.id" 
                                        @click="toggleScore(sc.id)"
                                        class="flex items-center justify-between p-5 rounded-2xl border transition-all text-left active:scale-95 group"
                                        :class="form.score_ids.includes(sc.id) 
                                            ? 'bg-blue-950 border-blue-950' 
                                            : 'bg-white border-gray-100 hover:border-blue-200'"
                                    >
                                        <div class="flex-1 min-w-0 pr-4">
                                            <div class="font-black text-[13px] leading-tight break-words italic transition-colors" :class="form.score_ids.includes(sc.id) ? 'text-white' : 'text-blue-950'">{{ sc.title }}</div>
                                            <div class="text-[9px] font-black uppercase tracking-widest mt-0.5 transition-colors" :class="form.score_ids.includes(sc.id) ? 'text-blue-400' : 'text-gray-400'">{{ sc.composer }}</div>
                                        </div>
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all" :class="form.score_ids.includes(sc.id) ? 'bg-yellow-400 text-blue-950' : 'bg-gray-100 text-gray-300 group-hover:bg-blue-50 group-hover:text-blue-600'">
                                            <svg v-if="form.score_ids.includes(sc.id)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v12M6 12h12"/></svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar { width: 6px; }
.scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
.scrollbar-thin::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>
