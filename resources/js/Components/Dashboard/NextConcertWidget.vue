<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    nextConcert: Object,
});

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('nl-NL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getDaysUntil(date) {
    const now = new Date();
    const target = new Date(date);
    const diff = Math.ceil((target - now) / (1000 * 60 * 60 * 24));
    return diff;
}
</script>

<template>
    <div class="space-y-6 text-left">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-950 rounded-2xl flex items-center justify-center text-yellow-400 shadow-xl shadow-blue-950/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0 -2-2H5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2z" /></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-blue-950 uppercase italic tracking-tight">{{ __('Eerstvolgende Concert') }}</h3>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-900/40">{{ __('Bereid je voor op het volgende optreden') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] p-8 sm:p-12 shadow-2xl shadow-blue-950/5 border border-blue-950/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-48 h-48 bg-yellow-400/10 rounded-bl-full -mr-24 -mt-24 transition-all group-hover:scale-110"></div>
            
            <div class="relative space-y-10">
                <div v-if="nextConcert">
                    <h4 class="text-4xl sm:text-6xl font-black text-blue-950 uppercase italic tracking-tighter mb-6 leading-none">{{ nextConcert.title }}</h4>
                    <div class="flex flex-wrap gap-8">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-yellow-400/20 text-yellow-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg>
                            </div>
                            <span class="font-black text-blue-950 italic uppercase tracking-widest text-xs">{{ formatDate(nextConcert.date) }}</span>
                        </div>
                        <div v-if="nextConcert.location" class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z" /></svg>
                            </div>
                            <span class="font-black text-blue-950 italic uppercase tracking-widest text-xs">{{ nextConcert.location }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="py-10 text-center opacity-30 italic">
                    <p class="font-black text-blue-900">{{ __('Geen geplande concerten gevonden.') }}</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 pt-4">
                    <Link 
                        :href="route('muziek.index')"
                        class="w-full sm:w-auto bg-blue-950 text-white px-10 py-5 rounded-2xl font-black uppercase italic tracking-widest hover:bg-blue-900 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-950/20"
                    >
                        {{ __('Alle muziek') }}
                    </Link>
                    <div v-if="nextConcert" class="flex items-center gap-3 px-6 py-4 bg-gray-50 rounded-2xl">
                        <span class="text-3xl font-black text-blue-950 leading-none">{{ getDaysUntil(nextConcert.date) }}</span>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-300 leading-tight">{{ __('dagen tot') }}<br>{{ __('optreden') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
