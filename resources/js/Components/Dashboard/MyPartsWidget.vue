<script setup>
import { Link } from '@inertiajs/vue3';
import MusicPartCard from '@/Components/MusicPartCard.vue';

const props = defineProps({
    myParts: Array,
    nextConcert: Object,
});

defineEmits(['preview']);
</script>

<template>
    <div class="space-y-6 text-left h-full">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-950 rounded-2xl flex items-center justify-center text-yellow-400 shadow-xl shadow-blue-950/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" /></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-blue-950 uppercase italic tracking-tight">{{ __('Mijn Partijen') }}</h3>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-900/40">{{ nextConcert ? __('Voor het eerstvolgende concert') : __('Nog geen repertoire beschikbaar') }}</p>
            </div>
        </div>

        <div class="bg-blue-50/50 rounded-[3rem] p-2 border border-blue-950/5 shadow-inner">
            <div class="max-h-[600px] overflow-y-auto pr-2 custom-scrollbar space-y-4 p-4 lg:p-6">
                <template v-if="myParts.length">
                    <MusicPartCard 
                        v-for="part in myParts" 
                        :key="part.part_id" 
                        :part="part" 
                        layout="vertical"
                        :download-url="route('muziek.download', { score: part.score_id, part: part.part_id })"
                        view-btn-class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white"
                        download-btn-class="flex-1 bg-blue-950 text-white hover:bg-black shadow-lg shadow-blue-950/10"
                        @preview="$emit('preview', part)"
                    />
                </template>
                <div v-else class="py-32 text-center flex flex-col items-center gap-8">
                    <div class="w-20 h-20 bg-blue-100 rounded-3xl flex items-center justify-center text-blue-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div class="space-y-3">
                        <p class="font-black text-xl text-blue-950 italic uppercase tracking-tighter leading-none">{{ __('Geen Partijen Gevonden') }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] leading-relaxed max-w-[200px] mx-auto">{{ __('Zorg dat je instrumenten correct zijn ingesteld.') }}</p>
                    </div>
                    <Link :href="route('mijn-instrumenten.edit')" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-950 transition-colors bg-blue-50 px-6 py-3 rounded-xl">
                        {{ __('Instellen') }} &rarr;
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(30, 58, 138, 0.1);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(30, 58, 138, 0.2);
}
</style>
