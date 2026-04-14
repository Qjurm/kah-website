<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();

const homeRoute = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === 'admin') return route('beheer.dashboard');
    if (role === 'musician') return route('muziek.index');
    return route('home');
});

const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

// Admin-Musician Toggle Detection
const isAdminAndMusician = computed(() => {
    const user = page.props.auth?.user;
    return user?.role === 'admin'; // Admins can also access musician mode
});

const currentMode = computed(() => {
    const currentRoute = route().current();
    if (currentRoute?.startsWith('beheer.')) {
        return 'admin';
    }
    if (currentRoute?.startsWith('muziek.') || currentRoute === 'mijn-instrument') {
        return 'musician';
    }
    return null;
});

const toggleLink = computed(() => {
    if (currentMode.value === 'admin') {
        return route('muziek.index');
    }
    if (currentMode.value === 'musician') {
        return route('beheer.dashboard');
    }
    return null;
});

const headerClass = computed(() => {
    if (currentMode.value === 'admin') {
        return 'bg-blue-900 border-b-4 border-yellow-400';
    }
    return 'border-b border-gray-100 bg-white';
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                :class="headerClass"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="homeRoute">
                                    <ApplicationLogo
                                        :class="{
                                            'text-white': currentMode === 'admin',
                                            'text-gray-800': currentMode !== 'admin'
                                        }"
                                        class="block h-9 w-auto fill-current"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                                <div  v-if="isAdmin" class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink
                                        :href="route('beheer.dashboard')"
                                        :active="route().current('beheer.dashboard')"
                                        :class="{
                                            'text-white hover:text-gray-100': currentMode === 'admin',
                                            'border-yellow-400': currentMode === 'admin' && route().current('beheer.dashboard')
                                        }"
                                    >
                                        Dashboard
                                    </NavLink>
                                    <NavLink
                                        :href="route('beheer.concerten.index')"
                                        :active="route().current('beheer.concerten.*')"
                                        :class="{
                                            'text-white hover:text-gray-100': currentMode === 'admin',
                                            'border-yellow-400': currentMode === 'admin' && route().current('beheer.concerten.*')
                                        }"
                                    >
                                        Concerten
                                    </NavLink>
                                    <NavLink
                                        :href="route('beheer.bladmuziek.index')"
                                        :active="route().current('beheer.bladmuziek.*')"
                                        :class="{
                                            'text-white hover:text-gray-100': currentMode === 'admin',
                                            'border-yellow-400': currentMode === 'admin' && route().current('beheer.bladmuziek.*')
                                        }"
                                    >
                                        Muziek
                                    </NavLink>
                                    <NavLink
                                        :href="route('beheer.gebruikers.index')"
                                        :active="route().current('beheer.gebruikers.*')"
                                        :class="{
                                            'text-white hover:text-gray-100': currentMode === 'admin',
                                            'border-yellow-400': currentMode === 'admin' && route().current('beheer.gebruikers.*')
                                        }"
                                    >
                                        Gebruikers
                                    </NavLink>

                                    <div class=" bg-white w-1" />

                                </div>
                                <div class="space-x-8 w-full pl-4 pr-4 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink
                                        :href="route('muziek.index')"
                                        :active="route().current('muziek.index')"
                                        class="text-white"
                                    >
                                        Muziek
                                    </NavLink>
                                </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center sm:gap-4">
                            <!-- Mode Indicator Badge (Admin Mode) -->
                            <span
                                v-if="isAdmin"
                                class="inline-block rounded-full bg-yellow-400 px-3 py-2 text-xs font-bold uppercase tracking-wide text-gray-900"
                            >
                                ADMIN
                            </span>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                :class="{
                                                    'bg-blue-800 text-gray-100 border-gray-700 hover:text-white': currentMode === 'admin',
                                                    'bg-white text-gray-500 border-transparent hover:text-gray-700': currentMode !== 'admin'
                                                }"
                                                class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                :class="{
                                    'text-gray-200 hover:bg-blue-800 hover:text-white focus:bg-blue-800 focus:text-white': currentMode === 'admin',
                                    'text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500': currentMode !== 'admin'
                                }"
                                class="inline-flex items-center justify-center rounded-md p-2 transition duration-150 ease-in-out focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                        'bg-blue-800': currentMode === 'admin',
                        'bg-white': currentMode !== 'admin',
                    }"
                    class="sm:hidden"
                >
                    <div
                        :class="{
                            'bg-blue-800': currentMode === 'admin',
                        }"
                        class="space-y-1 pb-3 pt-2"
                    >
                        <ResponsiveNavLink
                            v-if="isAdmin"
                            :href="route('beheer.dashboard')"
                            :active="route().current('beheer.dashboard')"
                            :class="{
                                'text-gray-100 hover:bg-blue-700': currentMode === 'admin',
                            }"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="isAdmin"
                            :href="route('beheer.concerten.index')"
                            :active="route().current('beheer.concerten.*')"
                            :class="{
                                'text-gray-100 hover:bg-blue-700': currentMode === 'admin',
                            }"
                        >
                            Concerten
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="isAdmin"
                            :href="route('beheer.bladmuziek.index')"
                            :active="route().current('beheer.bladmuziek.*')"
                            :class="{
                                'text-gray-100 hover:bg-blue-700': currentMode === 'admin',
                            }"
                        >
                            Stukken
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="!isAdmin"
                            :href="route('muziek.index')"
                            :active="route().current('muziek.index')"
                        >
                            Muziek
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        :class="{
                            'border-t border-blue-700 bg-blue-800': currentMode === 'admin',
                            'border-t border-gray-200 bg-white': currentMode !== 'admin',
                        }"
                        class="pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                :class="{
                                    'text-gray-100': currentMode === 'admin',
                                    'text-gray-800': currentMode !== 'admin',
                                }"
                                class="text-base font-medium"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div
                                :class="{
                                    'text-gray-300': currentMode === 'admin',
                                    'text-gray-500': currentMode !== 'admin',
                                }"
                                class="text-sm font-medium"
                            >
                                {{ $page.props.auth.user.email }}
                            </div>
                            <!-- Mode Indicator Badge (Mobile) -->
                            <div
                                v-if="currentMode === 'admin'"
                                class="mt-2 inline-block rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gray-900"
                            >
                                ADMIN
                            </div>
                        </div>

                        <!-- Mode Toggle (Mobile) -->
                        <div
                            v-if="isAdminAndMusician && toggleLink"
                            class="mt-3 space-y-1"
                        >
                            <ResponsiveNavLink
                                :href="toggleLink"
                                as="button"
                            >
                                {{ toggleButtonText }}
                            </ResponsiveNavLink>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                :class="{
                                    'text-gray-100 hover:bg-blue-700': currentMode === 'admin',
                                }"
                            >
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                :class="{
                                    'text-gray-100 hover:bg-blue-700': currentMode === 'admin',
                                }"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
