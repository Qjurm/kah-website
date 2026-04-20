<script setup>
import { ref, computed } from 'vue';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/Components/ui/popover';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/Components/ui/command';

const props = defineProps({
    part: Object,
    index: Number,
    instruments: Array,
    modelValue: String,
    errorInstrument: String,
    errorPdf: String,
    status: String, // 'pending', 'uploading', 'success', 'error'
    statusMessage: String,
});

const emit = defineEmits(['remove', 'update:modelValue', 'add-instrument', 'openPreview']);

const open = ref(false);
const searchQuery = ref('');

const selectedInstrumentDisplay = computed(() => {
    if (!props.modelValue) return 'Kies instrument...';
    return props.modelValue;
});

function handleSearchUpdate(val) {
    searchQuery.value = val;
}

function selectInstrument(name) {
    let currentArr = props.modelValue ? props.modelValue.split(', ') : [];
    if (currentArr.includes(name)) {
        currentArr = currentArr.filter(i => i !== name);
    } else {
        currentArr.push(name);
    }
    emit('update:modelValue', currentArr.join(', '));
}

function addNewInstrument() {
    if (!searchQuery.value) return;
    emit('add-instrument', searchQuery.value);
    selectInstrument(searchQuery.value);
    searchQuery.value = '';
}

function previewLocalFile() {
    if (props.part.pdf instanceof File) {
        const url = URL.createObjectURL(props.part.pdf);
        emit('openPreview', url, props.part.filename || 'Voorbeeld');
    }
}
</script>

<template>
    <div class="flex flex-col gap-1 relative">
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 relative group transition-all hover:border-gray-300 shadow-sm"
             :class="{ 'opacity-60': status === 'uploading', 'border-green-300 bg-green-50/30': status === 'success', 'border-red-300 bg-red-50/30': status === 'error' }">
            
            <!-- File Icon / Status Indicator -->
            <div class="flex-shrink-0 p-3 rounded-lg relative"
                 :class="{ 'bg-blue-100 text-blue-600': status === 'uploading', 'bg-green-100 text-green-600': status === 'success', 'bg-red-100 text-red-600': status === 'error', 'bg-gray-100 text-gray-400': !status || status === 'pending' }">
                
                <template v-if="status === 'uploading'">
                    <svg class="animate-spin h-6 w-6" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <template v-else-if="status === 'success'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template v-else-if="status === 'error'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
                <template v-else>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </template>
            </div>
            
            <div class="flex-1 min-w-0 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col justify-center">
                    <div class="text-sm font-bold text-blue-950 truncate" :title="part.filename">{{ part.filename }}</div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 truncate" v-if="part.inferredTitle">{{ part.inferredTitle }}</div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Instrument Toewijzing</label>
                    <Popover v-model:open="open" :disabled="status === 'uploading' || status === 'success'">
                        <PopoverTrigger asChild>
                            <button
                                type="button"
                                role="combobox"
                                :aria-expanded="open"
                                class="flex h-10 w-full items-center justify-between whitespace-nowrap rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold shadow-sm transition-all focus:ring-2 focus:ring-yellow-400 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'text-gray-300': !modelValue, 'border-green-200': status === 'success', 'border-red-200': status === 'error' }"
                            >
                                <span class="truncate">{{ selectedInstrumentDisplay }}</span>
                                <svg class="ml-2 h-4 w-4 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                            </button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[300px] p-0 rounded-2xl shadow-2xl border border-gray-100 overflow-hidden bg-white" align="start">
                            <Command :searchTerm="searchQuery" class="border-none flex flex-col h-full overflow-hidden">
                                <CommandInput placeholder="Zoek instrument..." @update:modelValue="handleSearchUpdate" class="border-b border-gray-100 focus:ring-0 font-bold h-12" />
                                <CommandList class="max-h-[260px] overflow-y-auto">
                                    <CommandEmpty>
                                        <div class="p-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                            Geen instrument gevonden in lijst.
                                        </div>
                                    </CommandEmpty>
                                    
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="inst in instruments"
                                            :key="inst.id"
                                            :value="inst.name"
                                            @select="() => selectInstrument(inst.name)"
                                            class="flex items-center px-4 py-3 cursor-pointer hover:bg-blue-50 transition-colors font-bold text-blue-950"
                                        >
                                            <div class="w-5 h-5 rounded-md border border-gray-200 mr-3 flex items-center justify-center transition-all"
                                                :class="{ 'bg-yellow-400 border-yellow-400': modelValue && modelValue.split(', ').includes(inst.name) }">
                                                <svg v-if="modelValue && modelValue.split(', ').includes(inst.name)" class="w-3 h-3 text-blue-950" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                            </div>
                                            {{ inst.name }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                                
                                <!-- Persistent Add Option (Outside list to avoid cmkd filtering) -->
                                <div v-if="searchQuery" class="border-t border-gray-100 bg-white p-1">
                                    <button
                                        type="button"
                                        @click="addNewInstrument"
                                        class="w-full flex items-center px-4 py-4 rounded-xl text-blue-600 hover:bg-blue-50 transition-colors font-black uppercase tracking-widest text-[11px]"
                                    >
                                        <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center mr-3 shadow-sm shadow-blue-200 text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                        "{{ searchQuery }}" Toevoegen
                                    </button>
                                </div>
                                <div v-else class="p-4 border-t border-gray-50 bg-gray-50/30">
                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-300 text-center">Type voor een nieuw instrument...</p>
                                </div>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>
            </div>
            
            <div class="ml-4 flex-shrink-0 flex items-center gap-2" v-if="status !== 'success'">
                <button 
                    v-if="part.pdf || part.pdf_path"
                    type="button" 
                    @click="part.pdf ? previewLocalFile() : $emit('openPreview', route('beheer.bladmuziek.partijen.view', { score: part.score_id, part: part.id }), part.instrument)"
                    class="text-blue-400 hover:text-blue-600 p-3 rounded-xl hover:bg-blue-50 transition-all active:scale-90"
                    title="Bekijk PDF"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                
                <!-- Replace Button for existing parts -->
                <button 
                    v-if="part.id"
                    type="button" 
                    @click="$emit('replace')"
                    class="text-yellow-500 hover:text-yellow-600 p-3 rounded-xl hover:bg-yellow-50 transition-all active:scale-90"
                    title="PDF Vervangen"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>

                <button type="button" @click="$emit('remove')" class="text-gray-300 hover:text-red-500 p-3 rounded-xl hover:bg-red-50 transition-all active:scale-90" title="Verwijder PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Inline Error/Status Text -->
        <div v-if="statusMessage || errorInstrument || errorPdf" class="px-2 flex flex-col gap-0.5">
            <div v-if="statusMessage" class="text-[10px] font-black uppercase tracking-widest transition-all"
                 :class="{ 'text-blue-500': status === 'uploading', 'text-green-500': status === 'success', 'text-red-500': status === 'error' }">
                {{ statusMessage }}
            </div>
            <div v-if="errorInstrument" class="text-[10px] font-black uppercase tracking-widest text-red-500">{{ errorInstrument }}</div>
            <div v-if="errorPdf" class="text-[10px] font-black uppercase tracking-widest text-red-500">{{ errorPdf }}</div>
        </div>
    </div>
</template>
