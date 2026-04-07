<script setup>
import { Head } from '@inertiajs/vue3';
import Header from '@/Components/Layout/Header.vue';
import Footer from '@/Components/Layout/Footer.vue';
import HeroSection from '@/Components/Home/HeroSection.vue';
import ConcertSection from '@/Components/Home/ConcertSection.vue';

defineProps({
    upcomingConcerts: Array,
});
</script>

<template>
    <Head title="Koninklijke Almelosche Harmonie" />

    <div class="min-h-screen bg-white flex flex-col">
        <Header />
        
        <main class="flex-1">
            <HeroSection />
            <ConcertSection :concerts="upcomingConcerts" />
        </main>

        <Footer />
    </div>
</template>
