<script setup>
import { ref, computed } from 'vue';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';

const props = defineProps({
    part: Object,
    index: Number,
    instruments: Array,
    modelValue: String,
    errorInstrument: String,
    errorPdf: String,
});

const emit = defineEmits(['remove', 'update:modelValue', 'add-instrument']);

const open = ref(false);
const searchQuery = ref('');

const selectedInstrumentDisplay = computed(() => {
    if (!props.modelValue) return 'Kies instrument(en)...';
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
</script>

<template>
    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 relative group transition-all hover:border-gray-300 shadow-sm">
        <div class="flex-shrink-0 bg-red-100 text-red-600 p-3 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        
        <div class="flex-1 min-w-0 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col justify-center">
                <div class="text-sm font-medium text-gray-900 truncate" :title="part.filename">{{ part.filename }}</div>
                <div class="text-xs text-gray-500 truncate" v-if="part.inferredTitle">{{ part.inferredTitle }}</div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Instrument Toewijzing</label>
                <Popover v-model:open="open">
                    <PopoverTrigger asChild>
                        <button
                            type="button"
                            role="combobox"
                            :aria-expanded="open"
                            class="flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
                            :class="{ 'text-gray-500': !modelValue }"
                        >
                            <span class="truncate">{{ selectedInstrumentDisplay }}</span>
                            <!-- Chevron Icon -->
                            <svg class="ml-2 h-4 w-4 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                        </button>
                    </PopoverTrigger>
                    <PopoverContent class="w-[300px] p-0" align="start">
                        <Command v-model:searchTerm="searchQuery">
                            <CommandInput placeholder="Zoek instrument..." @update:modelValue="handleSearchUpdate" />
                            <CommandList>
                                <CommandEmpty>
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        <p class="mb-3">Geen instrument gevonden.</p>
                                        <button 
                                            v-if="searchQuery" 
                                            type="button" 
                                            @click="addNewInstrument" 
                                            class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-md hover:bg-blue-100 transition-colors w-full font-medium text-left"
                                        >
                                            + "{{ searchQuery }}" toevoegen als nieuw instrument
                                        </button>
                                    </div>
                                </CommandEmpty>
                                <CommandGroup>
                                    <CommandItem
                                        v-for="inst in instruments"
                                        :key="inst.id"
                                        :value="inst.name"
                                        @select="() => selectInstrument(inst.name)"
                                    >
                                        <!-- Checkmark Icon -->
                                        <svg 
                                            class="mr-2 h-4 w-4" 
                                            :class="(modelValue && modelValue.split(', ').includes(inst.name)) ? 'opacity-100 text-blue-600' : 'opacity-0'"
                                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                        ><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        {{ inst.name }}
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
            </div>
        </div>
        
        <div class="ml-4 flex-shrink-0">
            <button type="button" @click="$emit('remove')" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 transition-colors" title="Verwijder PDF">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        
        <div v-if="errorInstrument" class="absolute -bottom-5 left-16 text-xs text-red-600">
            {{ errorInstrument }}
        </div>
        <div v-if="errorPdf" class="absolute -bottom-5 left-1/2 text-xs text-red-600">
            {{ errorPdf }}
        </div>
    </div>
</template>
