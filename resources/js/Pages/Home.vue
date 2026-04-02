<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    upcomingConcerts: Array,
});

function formatDate(d) {
    return new Date(d).toLocaleDateString('nl-NL', {
        weekday: 'short',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Koninklijke Almelosche Harmonie" />

    <div class="min-h-screen bg-white flex flex-col">

        <!-- ── Navigation ────────────────────────────────────────────── -->
        <nav class="bg-slate-900 text-white sticky top-0 z-40 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo + name -->
                    <div class="flex items-center gap-3">
                        <img src="/images/kah-logo.png" alt="KAH Logo" class="h-9 w-auto object-contain" />
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

        <!-- ── Hero ──────────────────────────────────────────────────── -->
        <section class="bg-slate-900 text-white pt-20 pb-28 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950 pointer-events-none" />
            <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-500/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none" />

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12">
                    <!-- Text -->
                    <div class="flex-1 text-center lg:text-left">
                        <span class="inline-block bg-yellow-500 text-slate-900 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-6">
                            Koninklijk &bull; Sinds 1904 &mdash; 122 jaar muziek
                        </span>
                        <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                            Koninklijke<br />
                            <span class="text-yellow-500">Almelosche</span><br />
                            Harmonie
                        </h1>
                        <p class="text-lg text-slate-300 max-w-xl mb-8">
                            Een van de oudste harmonieorkesten van Nederland. Samen met
                            <strong class="text-white">De Eendracht</strong> brengen wij prachtige muziek naar Almelo en omgeving.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                            <a
                                href="#concerten"
                                class="bg-yellow-500 text-slate-900 px-7 py-3 rounded-lg font-bold hover:bg-yellow-400 transition-colors text-center"
                            >
                                Aankomende concerten
                            </a>
                            <a
                                href="#contact"
                                class="border border-slate-600 text-slate-300 px-7 py-3 rounded-lg font-medium hover:border-yellow-500 hover:text-yellow-400 transition-colors text-center"
                            >
                                Contact opnemen
                            </a>
                        </div>
                    </div>

                    <!-- Logo image -->
                    <div class="flex-shrink-0">
                        <img
                            src="/images/kah-logo.png"
                            alt="Koninklijke Almelosche Harmonie"
                            class="w-64 sm:w-80 opacity-90 drop-shadow-2xl"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Stats strip ────────────────────────────────────────────── -->
        <div class="bg-yellow-500 text-slate-900 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-extrabold">122</div>
                        <div class="text-xs font-semibold uppercase tracking-wide opacity-70 mt-0.5">Jaar ervaring</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold">60+</div>
                        <div class="text-xs font-semibold uppercase tracking-wide opacity-70 mt-0.5">Muzikanten</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold">4+</div>
                        <div class="text-xs font-semibold uppercase tracking-wide opacity-70 mt-0.5">Concerten/jaar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Concerts grid ──────────────────────────────────────────── -->
        <section id="concerten" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Aankomende concerten</h2>
                    <p class="text-gray-500 max-w-xl mx-auto">
                        Mis geen enkel optreden &mdash; zet onze concerten alvast in uw agenda.
                    </p>
                </div>

                <!-- Has concerts -->
                <div
                    v-if="upcomingConcerts && upcomingConcerts.length"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="concert in upcomingConcerts"
                        :key="concert.id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col"
                    >
                        <!-- Photo or placeholder -->
                        <div class="relative h-44 bg-slate-100 flex-shrink-0">
                            <img
                                v-if="concert.photo_path"
                                :src="`/storage/${concert.photo_path}`"
                                :alt="concert.title"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                            <span
                                v-if="concert.is_current"
                                class="absolute top-3 left-3 bg-yellow-500 text-slate-900 text-xs font-bold px-2.5 py-0.5 rounded-full shadow"
                            >
                                Huidig
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="p-5 flex-1 flex flex-col">
                            <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">
                                {{ formatDate(concert.date) }}
                            </p>
                            <h3 class="font-bold text-slate-900 text-lg leading-snug mb-1">{{ concert.title }}</h3>
                            <p v-if="concert.location" class="text-sm text-gray-500 flex items-center gap-1 mt-auto pt-3">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ concert.location }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- No concerts -->
                <div v-else class="text-center py-16 bg-gray-50 rounded-2xl">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">Momenteel zijn er geen aankomende concerten gepland.</p>
                    <p class="text-sm text-gray-400 mt-1">Volg ons op Facebook voor de laatste updates.</p>
                </div>
            </div>
        </section>

        <!-- ── About section ──────────────────────────────────────────── -->
        <section class="py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">
                            KAH &times; De Eendracht
                        </h2>
                        <p class="text-gray-600 mb-4">
                            De Koninklijke Almelosche Harmonie en De Eendracht werken nauw samen om de harmoniecultuur
                            in Almelo levend te houden en te laten groeien. Onze gezamenlijke concerten trekken publiek
                            uit de hele regio.
                        </p>
                        <p class="text-gray-600">
                            Met een divers repertoire van klassieke harmoniewerken tot moderne bewerkingen bieden wij
                            voor ieder wat wils. Onze muzikanten zijn toegewijde amateurs en semi-professionals die
                            wekelijks repeteren voor de hoogste kwaliteit.
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Wekelijkse repetities</p>
                                <p class="text-sm text-gray-500">Professionele begeleiding</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Koninklijk erkend</p>
                                <p class="text-sm text-gray-500">Koninklijk predicaat sinds 1904</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Open voor nieuwe leden</p>
                                <p class="text-sm text-gray-500">Alle niveaus welkom</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Facebook feed ──────────────────────────────────────────── -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Volg ons op Facebook</h2>
                    <p class="text-gray-500">Blijf op de hoogte van concerten, nieuws en activiteiten.</p>
                </div>
                <div class="flex justify-center">
                    <!-- Responsive Facebook iframe wrapper -->
                    <div class="w-full max-w-3xl rounded-2xl overflow-hidden shadow-sm border border-gray-100" style="aspect-ratio: 1.2;">
                        <iframe
                            src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FKAHarmonie&tabs=timeline&width=800&height=600&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true"
                            style="border:none;overflow:hidden;width:100%;height:100%"
                            scrolling="no"
                            frameborder="0"
                            allowfullscreen="true"
                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Footer ────────────────────────────────────────────────── -->
        <footer id="contact" class="bg-slate-900 text-white py-14 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img src="/images/kah-logo.png" alt="KAH" class="h-10 w-auto opacity-80" />
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Een van de oudste harmonieorkesten van Nederland, trots gevestigd in Almelo.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-yellow-500 font-bold text-sm uppercase tracking-wide mb-4">Contact</h3>
                        <ul class="text-slate-400 text-sm space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                info@kah-almelo.nl
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Almelo, Overijssel
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-yellow-500 font-bold text-sm uppercase tracking-wide mb-4">Links</h3>
                        <ul class="text-slate-400 text-sm space-y-2">
                            <li>
                                <Link :href="route('muziek.index')" class="hover:text-yellow-400 transition-colors">
                                    Voor Muzikanten
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('login')" class="hover:text-yellow-400 transition-colors">
                                    Inloggen
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-800 pt-6 text-center text-slate-500 text-sm">
                    &copy; {{ new Date().getFullYear() }} Koninklijke Almelosche Harmonie. Alle rechten voorbehouden.
                </div>
            </div>
        </footer>

    </div>
</template>
