<script setup>
import { ref, computed } from 'vue';
import NavTab             from '@/Components/Layout/NavTab.vue';
import AdminPill          from '@/Components/Layout/AdminPill.vue';
import AdminBadge         from '@/Components/Layout/AdminBadge.vue';
import UserDropdown       from '@/Components/Layout/UserDropdown.vue';
import MobileNavMenu      from '@/Components/Layout/MobileNavMenu.vue';
import { Link, usePage }  from '@inertiajs/vue3';

const showMobileMenu = ref(false);
const page = usePage();

const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

const homeRoute = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === 'admin')    return route('beheer.dashboard');
    if (role === 'musician') return route('mijn-instrument');
    return route('home');
});

const musicianLinks = computed(() => {
    const translations = page.props.translations || {};
    const t = (key) => translations[key] || key;

    return [
        { label: t('Dashboard'),    routeName: 'mijn-instrument',       active: route().current('mijn-instrument') },
        { label: t('Bibliotheek'),  routeName: 'muziek.index',           active: route().current('muziek.index') },
        { label: t('Instrumenten'), routeName: 'mijn-instrumenten.edit', active: route().current('mijn-instrumenten.edit') },
    ];
});

const adminLinks = computed(() => {
    const translations = page.props.translations || {};
    const t = (key) => translations[key] || key;

    return [
        { label: t('Dashboard'),    routeName: 'beheer.dashboard',        active: route().current('beheer.dashboard') },
        { label: t('Concerten'),    routeName: 'beheer.concerten.index',  active: route().current('beheer.concerten.*') },
        { label: t('Bladmuziek'),   routeName: 'beheer.bladmuziek.index', active: route().current('beheer.bladmuziek.*') },
        { label: t('Gebruikers'),   routeName: 'beheer.gebruikers.index',  active: route().current('beheer.gebruikers.*') },
        { label: t('Instrumenten'), routeName: 'beheer.instrumenten.index', active: route().current('beheer.instrumenten.*') },
    ];
});

const activeAdminIndex = computed(() => adminLinks.value.findIndex(l => l.active));
</script>

<template>
    <div class="min-h-screen bg-[#F8F9FB]">

        <!-- ════════════════════════ NAV ════════════════════════ -->
        <nav class="bg-blue-950 shadow-2xl sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between gap-8">

                    <!-- ── Left: logo + tabs ──────────────────── -->
                    <div class="flex items-center h-full gap-0 min-w-0">

                        <!-- Music Note Logo -->
                        <Link
                            :href="homeRoute"
                            class="logo-link flex shrink-0 items-center justify-center w-12 h-12 bg-yellow-400 rounded-2xl mr-6 hover:rotate-6 transition-all shadow-lg shadow-yellow-400/20"
                            aria-label="Home"
                        >
                            <svg class="w-7 h-7 text-blue-950" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                            </svg>
                        </Link>

                        <!-- Musician tabs (desktop) -->
                        <div class="hidden md:flex items-center h-full gap-1">
                            <NavTab
                                v-for="link in musicianLinks"
                                :key="link.routeName"
                                :href="route(link.routeName)"
                                :active="link.active"
                            >
                                {{ link.label }}
                            </NavTab>
                        </div>

                        <!-- Divider + Admin pill (desktop, admin only) -->
                        <template v-if="isAdmin">
                            <div class="hidden lg:block mx-6 h-8 w-px bg-blue-800/60 self-center shrink-0" aria-hidden="true" />
                            <div class="hidden lg:block">
                                <AdminPill
                                    :links="adminLinks"
                                    :active-index="activeAdminIndex"
                                />
                            </div>
                        </template>
                    </div>

                    <div class="hidden sm:flex items-center gap-4 shrink-0">
                        <AdminBadge v-if="isAdmin" />
                        <UserDropdown :user="$page.props.auth.user" />
                    </div>

                    <!-- ── Hamburger (mobile) ─────────────────── -->
                    <button
                        class="hamburger -me-2 inline-flex items-center justify-center rounded-2xl p-3
                               text-blue-200 md:hidden hover:bg-blue-800 hover:text-white transition-all focus:outline-none"
                        :class="{ 'is-open': showMobileMenu }"
                        :aria-expanded="showMobileMenu"
                        aria-label="Menu"
                        @click="showMobileMenu = !showMobileMenu"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path :class="{ hidden: showMobileMenu, 'inline-flex': !showMobileMenu }"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{ hidden: !showMobileMenu, 'inline-flex': showMobileMenu }"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                </div>
            </div>

            <!-- Mobile menu (extracted component) -->
            <MobileNavMenu
                :show="showMobileMenu"
                :is-admin="isAdmin"
                :musician-links="musicianLinks"
                :admin-links="adminLinks"
                :user-name="$page.props.auth.user.name"
                :user-email="$page.props.auth.user.email"
            />
        </nav>

        <!-- Page heading slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-100 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)]">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page content -->
        <main class="transition-all duration-300">
            <slot />
        </main>

    </div>
</template>

<style scoped>
.logo-link {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
}
.logo-link:hover {
    transform: scale(1.1) rotate(6deg);
}

.hamburger {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.hamburger.is-open {
    background-color: rgba(30, 58, 138, 0.5); /* blue-900/50 */
}
</style>
