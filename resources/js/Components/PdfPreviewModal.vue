<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    url: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: 'Voorbeeld',
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

const onKeyDown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onMounted(() => {
    window.addEventListener('keydown', onKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeyDown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex flex-col bg-slate-900/95 backdrop-blur-sm p-4 md:p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 px-2">
                    <div class="flex flex-col">
                        <h3 class="text-white font-black italic text-xl leading-tight">{{ title }}</h3>
                        <p class="text-blue-400 text-[10px] uppercase font-black tracking-widest leading-none mt-1">PDF Preview</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <a 
                            v-if="url"
                            :href="url"
                            target="_blank"
                            class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all active:scale-90"
                            :title="__('Open in nieuw tabblad')"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <button 
                            @click="close"
                            class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center hover:bg-red-500 transition-all active:scale-90"
                            :title="__('Sluiten')"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Viewer Container -->
                <div class="flex-1 bg-white rounded-[2.5rem] overflow-hidden shadow-2xl relative">
                    <iframe 
                        v-if="url"
                        :src="url" 
                        class="w-full h-full border-none"
                    ></iframe>
                    
                    <div v-else class="flex flex-col items-center justify-center h-full text-slate-400">
                        <svg class="w-16 h-16 animate-pulse mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-black italic">Bestand laden...</p>
                    </div>
                </div>

                <!-- Footer / Info -->
                <div class="mt-6 flex justify-center">
                    <p class="text-white/20 text-[9px] font-black uppercase tracking-[0.5em]">Klik buiten het document of druk op ESC om te sluiten</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Ensure the iframe doesn't show default scrollbars if not needed, 
   but allow internal scrolling of the PDF */
iframe {
    background-color: #f8fafc;
}
</style>
