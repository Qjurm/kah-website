<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Logo + Description -->
                <div>
                    <h3 class="font-semibold text-white mb-2">Koninklijke Almelosche Harmonie</h3>
                    <p class="text-sm">Sinds 1904 brengen we muziek en cultuur naar Almelo en omgeving.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-semibold text-white mb-3">Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="hover:text-yellow-400 transition-colors">Home</a></li>
                        <li><a href="/muziek" class="hover:text-yellow-400 transition-colors">Muziek</a></li>
                        <li><a href="/agenda" class="hover:text-yellow-400 transition-colors">Agenda</a></li>
                        <li><a href="#contact" class="hover:text-yellow-400 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-semibold text-white mb-3">Contact</h4>
                    <p class="text-sm mb-1">Email: <a href="mailto:info@kah.nl" class="text-yellow-400 hover:underline">info@kah.nl</a></p>
                    <p class="text-sm">Telefoon: <a href="tel:+31546123456" class="text-yellow-400 hover:underline">+31 (0)546 123 456</a></p>
                </div>
            </div>

            <div class="border-t border-slate-700 pt-6 text-center text-sm">
                <p>&copy; {{ new Date().getFullYear() }} Koninklijke Almelosche Harmonie. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>
</template>
