<script setup>
import PartActionButtons from '@/Components/PartActionButtons.vue';

defineProps({
    part: {
        type: Object,
        required: true,
    },
    layout: {
        type: String,
        default: 'horizontal', // 'horizontal' or 'vertical'
    },
    downloadUrl: {
        type: String,
        required: true,
    },
    viewBtnClass: {
        type: String,
        default: 'bg-yellow-400 text-blue-950 hover:bg-yellow-300 shadow-lg shadow-yellow-400/10'
    },
    downloadBtnClass: {
        type: String,
        default: 'bg-white text-blue-950 hover:bg-blue-50 shadow-sm'
    },
});

defineEmits(['preview']);
</script>

<template>
    <div 
        class="group bg-white shadow-sm hover:shadow-xl transition-all"
        :class="[
            layout === 'vertical' ? 'p-6 rounded-[2rem] border border-blue-950/5 hover:shadow-blue-900/5 hover:-translate-y-1' : 'p-6 rounded-2xl border border-blue-100 flex items-center justify-between'
        ]"
    >
        <div :class="layout === 'vertical' ? 'flex flex-col gap-4' : 'flex items-center justify-between w-full'">
            
            <div class="min-w-0" :class="layout === 'vertical' ? '' : 'mr-4'">
                <div v-if="layout === 'horizontal'" class="flex items-center justify-between mb-2">
                    <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-blue-50 text-blue-600">{{ part.instrument }}</span>
                </div>
                <span v-else class="text-[9px] font-black uppercase tracking-widest text-blue-600 mb-1 block leading-none">{{ part.instrument }}</span>
                
                <h5 
                    class="font-black text-blue-950 italic truncate"
                    :class="layout === 'vertical' ? 'text-lg uppercase tracking-tighter leading-tight mb-1' : 'text-sm leading-tight'"
                >
                    {{ part.score_title }}
                </h5>
                
                <div :class="layout === 'vertical' ? 'flex items-center justify-between gap-4 mt-2' : 'flex flex-col mt-1'">
                    <p 
                        class="font-bold text-gray-500 uppercase tracking-widest truncate"
                        :class="layout === 'vertical' ? 'text-xs' : 'text-[10px]'"
                    >
                        {{ part.score_composer }}
                    </p>
                    <p v-if="part.original_filename" 
                        class="font-bold truncate max-w-full"
                        :class="[
                            layout === 'vertical' ? 'text-[10px] text-blue-600/70 text-right' : 'text-[9px] text-blue-600/70 mt-1',
                        ]"
                    >
                        {{ part.original_filename }}
                    </p>
                </div>
            </div>

            <div :class="layout === 'vertical' ? 'flex items-center gap-2 pt-2' : 'flex items-center gap-2 flex-shrink-0'">
                <PartActionButtons 
                    :download-url="downloadUrl"
                    :view-class="viewBtnClass"
                    :download-class="downloadBtnClass"
                    @preview="$emit('preview', part)"
                />
            </div>

        </div>
    </div>
</template>
