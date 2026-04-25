<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PdfPreviewModal from '@/Components/PdfPreviewModal.vue';
import NextConcertWidget from '@/Components/Dashboard/NextConcertWidget.vue';
import PrimaryInstrumentWidget from '@/Components/Dashboard/PrimaryInstrumentWidget.vue';
import MyPartsWidget from '@/Components/Dashboard/MyPartsWidget.vue';

const props = defineProps({
    primaryInstrument: Object,
    nextConcert: Object,
    myParts: Array,
    recentStats: Object,
    allInstruments: Array,
});

const previewUrl = ref('');
const previewTitle = ref('');
const showPreview = ref(false);

function openPreview(part) {
    previewUrl.value = route('muziek.view', { score: part.score_id, part: part.part_id });
    previewTitle.value = `${part.score_title} - ${part.instrument}`;
    showPreview.value = true;
}

function closePreview() {
    showPreview.value = false;
    previewUrl.value = '';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mijn Dashboard" />

        <template #header>
            <div class="flex flex-col gap-1 text-left">
                <h2 class="text-xl font-black leading-tight text-blue-950 italic">{{ __('Mijn Muziekdashboard') }}</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ __('Jouw instrumenten en komende optredens') }}</p>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">
                    
                    <!-- Left Column -->
                    <div class="lg:col-span-3 space-y-12">
                        <!-- Next Concert -->
                        <NextConcertWidget :next-concert="nextConcert" />

                        <!-- My Instrument -->
                        <PrimaryInstrumentWidget :primary-instrument="primaryInstrument" />
                    </div>

                    <!-- Right Column: My Parts -->
                    <div class="lg:col-span-2 h-full">
                        <MyPartsWidget 
                            :my-parts="myParts" 
                            :next-concert="nextConcert" 
                            @preview="openPreview" 
                        />
                    </div>

                </div>

            </div>
        </div>

        <PdfPreviewModal 
            :show="showPreview" 
            :url="previewUrl" 
            :title="previewTitle" 
            @close="closePreview" 
        />
    </AuthenticatedLayout>
</template>
