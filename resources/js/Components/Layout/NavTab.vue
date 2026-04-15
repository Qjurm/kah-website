<script setup>
/**
 * NavTab — desktop navigation tab with an underline active indicator.
 * Used for the musician section of the nav bar.
 */
import { Link } from '@inertiajs/vue3';

defineProps({
    href:   { type: String,  required: true },
    active: { type: Boolean, default: false },
});
</script>

<template>
    <Link
        :href="href"
        :class="[
            'nav-tab inline-flex items-center px-4 h-full text-sm font-medium border-b-2 transition-colors duration-150',
            active
                ? 'border-yellow-400 text-white'
                : 'border-transparent text-blue-300 hover:text-white hover:border-blue-500/60'
        ]"
    >
        <slot />
    </Link>
</template>

<style scoped>
.nav-tab {
    transition: color 0.15s ease, border-color 0.15s ease;
}
</style>
