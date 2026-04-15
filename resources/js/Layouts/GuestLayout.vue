<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-6 selection:bg-yellow-400 selection:text-blue-950">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="flex justify-center mb-10">
                <Link href="/" class="group">
                    <div class="w-20 h-20 bg-blue-950 rounded-[1.5rem] flex items-center justify-center shadow-2xl shadow-blue-900/20 group-hover:bg-black transition-all group-hover:scale-105 active:scale-95">
                        <ApplicationLogo class="w-10 h-10 text-white fill-current" />
                    </div>
                </Link>
            </div>

            <!-- Card Content -->
            <div class="bg-white rounded-[3rem] p-12 shadow-sm border border-gray-100 relative overflow-hidden">
                <!-- Subtle accent -->
                <div class="absolute top-0 left-0 w-full h-2 bg-yellow-400"></div>
                
                <slot />
            </div>
            
            <!-- Footer subtle link -->
            <div class="mt-10 text-center">
                <Link href="/" class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-300 hover:text-blue-950 transition-colors">
                    &larr; Terug naar homepage
                </Link>
            </div>
        </div>
    </div>
</template>
