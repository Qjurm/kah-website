<script setup>
/**
 * MobileNavMenu — the slide-down mobile navigation menu.
 *
 * Props:
 *   show          — whether the menu is visible
 *   isAdmin       — whether to render the admin section
 *   musicianLinks — Array<{ label, routeName, active }>
 *   adminLinks    — Array<{ label, routeName, active }>
 *   userName      — display name of the current user
 *   userEmail     — email address of the current user
 */
import { Link } from '@inertiajs/vue3';

defineProps({
    show:          { type: Boolean, required: true },
    isAdmin:       { type: Boolean, default: false },
    musicianLinks: { type: Array,   required: true },
    adminLinks:    { type: Array,   required: true },
    userName:      { type: String,  required: true },
    userEmail:     { type: String,  required: true },
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-show="show" class="sm:hidden border-t border-blue-800/60">

            <!-- ── Musician links ───────────────────────────────── -->
            <div class="pt-2 pb-1 px-2">
                <p class="px-3 pb-1 text-[9px] font-bold uppercase tracking-[0.18em] text-blue-500 select-none">
                    Muzikant
                </p>
                <Link
                    v-for="link in musicianLinks"
                    :key="link.routeName"
                    :href="route(link.routeName)"
                    :class="[
                        'mob-link block w-full px-3 py-2.5 rounded-lg text-sm font-medium border-l-2 mb-0.5 transition-all duration-150',
                        link.active
                            ? 'border-yellow-400 bg-blue-900/60 text-white'
                            : 'border-transparent text-blue-300 hover:bg-blue-800/40 hover:text-white hover:border-blue-500 hover:pl-4',
                    ]"
                >
                    {{ link.label }}
                </Link>
            </div>

            <!-- ── Admin links ──────────────────────────────────── -->
            <div v-if="isAdmin" class="mx-2 mb-2 mt-1 rounded-xl overflow-hidden ring-1 ring-blue-700/40">
                <!-- Admin section header — clearly a label, not a button -->
                <div class="flex items-center gap-1.5 px-3 py-2 bg-blue-800/60 border-b border-blue-700/40 select-none">
                    <svg class="w-3 h-3 text-yellow-400/80 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M2 4l3 6 5-8 5 8 3-6 2 12H0L2 4z"/>
                    </svg>
                    <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-blue-300/70">Beheer</p>
                </div>
                <div class="bg-blue-900/50 px-2 py-1">
                    <Link
                        v-for="link in adminLinks"
                        :key="link.routeName"
                        :href="route(link.routeName)"
                        :class="[
                            'mob-link block w-full px-3 py-2.5 rounded-lg text-sm font-medium border-l-2 mb-0.5 transition-all duration-150',
                            link.active
                                ? 'border-yellow-400 bg-yellow-400/10 text-yellow-300'
                                : 'border-transparent text-blue-300/80 hover:bg-white/5 hover:text-white hover:border-yellow-400/40 hover:pl-4',
                        ]"
                    >
                        {{ link.label }}
                    </Link>
                </div>
            </div>

            <!-- ── User footer ──────────────────────────────────── -->
            <div class="border-t border-blue-800/60 py-3 px-4">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full
                                 bg-yellow-400 text-sm font-bold text-blue-950 shrink-0">
                        {{ userName.charAt(0).toUpperCase() }}
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ userName }}</div>
                        <div class="text-xs text-blue-400 truncate">{{ userEmail }}</div>
                    </div>
                    <span
                        v-if="isAdmin"
                        class="ml-auto shrink-0 inline-flex items-center rounded-full
                               bg-yellow-400 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-blue-950"
                    >
                        Admin
                    </span>
                </div>

                <Link
                    :href="route('profile.edit')"
                    class="mob-link block px-3 py-2 text-sm text-blue-200 rounded-lg transition-all duration-150
                           hover:text-white hover:bg-blue-800/50 hover:pl-4"
                >
                    Profiel
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="mob-link block w-full text-left px-3 py-2 text-sm text-blue-200 rounded-lg transition-all duration-150
                           hover:text-white hover:bg-blue-800/50 hover:pl-4"
                >
                    Uitloggen
                </Link>
            </div>

        </div>
    </Transition>
</template>

<style scoped>
.mob-link {
    transition: color 0.15s ease, background 0.15s ease,
                border-color 0.15s ease, padding-left 0.15s ease;
}
</style>
