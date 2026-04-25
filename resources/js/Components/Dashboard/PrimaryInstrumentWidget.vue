<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    primaryInstrument: Object,
});
</script>

<template>
    <div v-if="primaryInstrument" class="bg-blue-950 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden text-left group">
        <div class="absolute top-0 right-0 p-10 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-8">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400 mb-3 opacity-60">{{ __('Mijn Hoofdinstrument') }}</div>
                <h5 class="text-3xl font-black italic uppercase tracking-tighter leading-none">{{ primaryInstrument.name }}</h5>
            </div>
            <Link :href="route('mijn-instrumenten.edit')" class="w-full sm:w-auto text-center bg-white/10 text-white px-8 py-3 rounded-xl hover:bg-yellow-400 hover:text-blue-950 transition-all font-black text-[10px] uppercase tracking-widest border border-white/10">
                {{ __('Instrumenten Bewerken') }} &rarr;
            </Link>
        </div>
    </div>
</template>
