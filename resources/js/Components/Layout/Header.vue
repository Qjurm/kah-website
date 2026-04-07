<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <nav class="bg-slate-900 text-white sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo + name -->
                <div class="flex items-center gap-3">
                    <img src="/images/kah.png" alt="KAH Logo" class="h-9 w-auto object-contain" />
                    <span class="hidden sm:block text-sm font-semibold text-slate-300 tracking-wide">
                        Koninklijke Almelosche Harmonie
                    </span>
                </div>

                <!-- Nav links -->
                <div class="flex items-center gap-6">
                    <a href="#concerten" class="text-sm text-slate-300 hover:text-yellow-400 transition-colors hidden sm:block">
                        Concerten
                    </a>
                    <a href="#contact" class="text-sm text-slate-300 hover:text-yellow-400 transition-colors hidden sm:block">
                        Contact
                    </a>
                    <Link
                        :href="route('login')"
                        class="text-sm font-semibold bg-yellow-500 text-slate-900 px-4 py-1.5 rounded-lg hover:bg-yellow-400 transition-colors"
                    >
                        Inloggen
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>
