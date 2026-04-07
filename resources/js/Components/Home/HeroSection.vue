<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="relative h-screen bg-gradient-to-b from-slate-900 to-slate-800 text-white flex items-center justify-center overflow-hidden">
        <!-- Background image overlay -->
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=1200&h=600&fit=crop" alt="Orchestra" class="w-full h-full object-cover" />
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center max-w-3xl mx-auto px-4">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Koninklijke Almelosche Harmonie
            </h1>
            <p class="text-xl md:text-2xl text-slate-300 mb-8 font-light">
                Sinds 1904 het vlaggenschip van de Almelose muziekcultuur
            </p>
            <div class="flex gap-4 justify-center flex-wrap">
                <Link
                    href="#concerten"
                    class="bg-yellow-500 text-slate-900 px-8 py-3 rounded-lg font-semibold hover:bg-yellow-400 transition-colors"
                >
                    Bekijk Concerten
                </Link>
                <a
                    href="#over-ons"
                    class="border-2 border-yellow-500 text-yellow-400 px-8 py-3 rounded-lg font-semibold hover:bg-yellow-400/10 transition-colors"
                >
                    Over Ons
                </a>
            </div>
        </div>
    </section>
</template>
