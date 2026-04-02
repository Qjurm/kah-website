<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    upcomingConcerts: Array,
});

function formatDate(d) {
    return new Date(d).toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>

<template>
    <Head title="Koninklijke Almelosche Harmonie" />

    <div class="min-h-screen bg-white flex flex-col">
        <!-- Navigation -->
        <nav class="bg-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <img src="/images/kah-logo.png" alt="KAH Logo" class="h-10 w-auto object-contain" />
                        <span class="hidden sm:block text-sm text-slate-300">Koninklijke Almelosche Harmonie</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link :href="route('login')" class="text-slate-300 hover:text-yellow-500 transition-colors text-sm font-medium">
                            Inloggen
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="bg-slate-900 text-white py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <!-- Logo banner -->
                <div class="flex justify-center mb-8">
                    <img src="/images/kah-logo.png" alt="Koninklijke Almelosche Harmonie" class="max-w-xl w-full rounded-xl shadow-lg opacity-90" />
                </div>
                <div class="mb-6">
                    <span class="inline-block bg-yellow-500 text-slate-900 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                        Koninklijk &bull; Sinds 1904 &mdash; 122 jaar muziek
                    </span>
                </div>
                <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight mb-6">
                    Koninklijke<br />
                    <span class="text-yellow-500">Almelosche Harmonie</span>
                </h1>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto mb-8">
                    De KAH is een van de oudste harmonieorkesten van Nederland. In samenwerking met
                    <strong class="text-white">De Eendracht</strong> brengen wij prachtige muziek naar Almelo en omgeving.
                    Samen staan wij voor een rijke muzikale traditie die generaties verbindt.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="bg-yellow-500 text-slate-900 px-8 py-3 rounded-lg font-bold text-lg hover:bg-yellow-400 transition-colors">
                        Contact opnemen
                    </a>
                    <a href="#concerten" class="border-2 border-yellow-500 text-yellow-500 px-8 py-3 rounded-lg font-bold text-lg hover:bg-yellow-500 hover:text-slate-900 transition-colors">
                        Aankomende concerten
                    </a>
                </div>
            </div>
        </section>

        <!-- Over KAH & De Eendracht -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-blue-900 mb-6">
                            KAH &times; De Eendracht
                        </h2>
                        <p class="text-gray-600 mb-4">
                            De Koninklijke Almelosche Harmonie en De Eendracht werken nauw samen om de harmoniecultuur
                            in Almelo levend te houden en te laten groeien. Onze gezamenlijke concerten trekken publiek
                            uit de hele regio.
                        </p>
                        <p class="text-gray-600 mb-4">
                            Met een divers repertoire van klassieke harmoniewerken tot moderne bewerkingen bieden wij
                            voor ieder wat wils. Onze muzikanten zijn toegewijde amateurs en semi-professionals die
                            wekelijks repeteren voor de hoogste kwaliteit.
                        </p>
                        <div class="grid grid-cols-3 gap-6 mt-8">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-yellow-500">170+</div>
                                <div class="text-sm text-gray-500 mt-1">Jaar ervaring</div>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-yellow-500">60+</div>
                                <div class="text-sm text-gray-500 mt-1">Muzikanten</div>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-yellow-500">4</div>
                                <div class="text-sm text-gray-500 mt-1">Concerten/jaar</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-2xl p-8 border border-blue-100">
                        <!-- Upcoming Concerts -->
                        <div v-if="upcomingConcerts && upcomingConcerts.length" id="concerten" class="mb-8">
                            <h3 class="text-xl font-bold text-blue-900 mb-4">Aankomende concerten</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="concert in upcomingConcerts"
                                    :key="concert.id"
                                    class="bg-white rounded-xl border border-blue-100 overflow-hidden flex gap-4 items-start p-4"
                                >
                                    <img
                                        v-if="concert.photo_path"
                                        :src="`/storage/${concert.photo_path}`"
                                        :alt="concert.title"
                                        class="w-20 h-20 object-cover rounded-lg flex-shrink-0"
                                    />
                                    <div v-else class="w-20 h-20 bg-blue-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span v-if="concert.is_current" class="inline-block bg-yellow-500 text-slate-900 text-xs font-bold px-2 py-0.5 rounded-full">Huidig</span>
                                            <span class="text-xs text-blue-600 font-medium">{{ formatDate(concert.date) }}</span>
                                        </div>
                                        <p class="font-semibold text-blue-900 text-sm leading-tight">{{ concert.title }}</p>
                                        <p v-if="concert.location" class="text-xs text-gray-500 mt-0.5">{{ concert.location }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-blue-900 mb-4">Volg ons op Facebook</h3>
                        <p class="text-gray-600 text-sm mb-6">Blijf op de hoogte van onze concerten, nieuws en activiteiten.</p>
                        <div class="flex justify-center">
                            <iframe
                                src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FKAHarmonie&tabs=timeline&width=700&height=800&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true"
                                width="700"
                                height="800"
                                style="border:none;overflow:hidden;max-width:100%"
                                scrolling="no"
                                frameborder="0"
                                allowfullscreen="true"
                                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact footer -->
        <footer id="contact" class="bg-slate-900 text-white py-12 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-yellow-500 font-bold text-lg mb-4">Koninklijke Almelosche Harmonie</h3>
                        <p class="text-slate-300 text-sm">
                            Een van de oudste harmonieorkesten van Nederland, trots gevestigd in Almelo.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-yellow-500 font-bold text-lg mb-4">Contact</h3>
                        <ul class="text-slate-300 text-sm space-y-2">
                            <li>info@kah-almelo.nl</li>
                            <li>Almelo, Overijssel</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-yellow-500 font-bold text-lg mb-4">Links</h3>
                        <ul class="text-slate-300 text-sm space-y-2">
                            <li>
                                <Link :href="route('muziek.index')" class="hover:text-yellow-500 transition-colors">Voor Muzikanten</Link>
                            </li>
                            <li>
                                <Link :href="route('login')" class="hover:text-yellow-500 transition-colors">Inloggen</Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-700 mt-8 pt-8 text-center text-slate-400 text-sm">
                    &copy; {{ new Date().getFullYear() }} Koninklijke Almelosche Harmonie. Alle rechten voorbehouden.
                </div>
            </div>
        </footer>
    </div>
</template>
