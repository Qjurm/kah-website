<!-- resources/js/Components/PartActionButtons.vue -->
<script setup>
defineProps({
    downloadUrl: {
        type: String,
        required: true,
    },
    viewClass: {
        type: String,
        default: 'bg-yellow-400 text-blue-950 hover:bg-yellow-300 shadow-lg shadow-yellow-400/10'
    },
    downloadClass: {
        type: String,
        default: 'bg-white text-blue-950 hover:bg-blue-50 shadow-sm'
    },
    showText: {
        type: Boolean,
        default: false,
    }
});

defineEmits(['preview']);
</script>

<template>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button 
            @click.stop="$emit('preview')"
            class="p-3 rounded-xl transition-all active:scale-90 flex items-center justify-center group"
            :class="viewClass"
            :title="__('Bekijk partij')"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            <span v-if="showText" class="ml-2 font-black text-[9px] uppercase tracking-widest">{{ __('Bekijk') }}</span>
        </button>
        <a 
            :href="downloadUrl"
            class="p-3 rounded-xl transition-all active:scale-90 flex items-center justify-center group"
            :class="downloadClass"
            :title="__('Download partij')"
            @click.stop
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            <span v-if="showText" class="ml-2 font-black text-[9px] uppercase tracking-widest">{{ __('Download') }}</span>
        </a>
    </div>
</template>
