<script setup>
/**
 * AdminPill — the admin navigation section.
 *
 * Renders a pill that contains:
 *   • A non-interactive "Beheer" label on the left (visually distinct — smaller, dimmer,
 *     not clickable so it cannot be confused with a button)
 *   • The admin tab links on the right, with a smooth sliding yellow indicator
 *
 * Props:
 *   links         — Array<{ label, routeName, active }>
 *   activeIndex   — index of the currently active link (-1 if none)
 */
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links:       { type: Array,  required: true },
    activeIndex: { type: Number, default: -1 },
});
</script>

<template>
    <div class="flex items-stretch self-center h-9 rounded-xl overflow-hidden
                bg-blue-900 ring-1 ring-blue-700/50 shadow-md">

        <!-- ── Label segment ──────────────────────────────────────────── -->
        <!-- Not a button/link — purely informational, visually de-emphasised -->
        <div class="flex items-center gap-1.5 px-3 border-r border-blue-700/50 bg-blue-800/60 shrink-0 select-none">
            <svg class="w-3 h-3 text-yellow-400/70 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M2 4l3 6 5-8 5 8 3-6 2 12H0L2 4z"/>
            </svg>
            <span class="text-[10px] font-medium tracking-widest text-blue-400/80 uppercase leading-none">
                Beheer
            </span>
        </div>

        <!-- ── Tab segment ────────────────────────────────────────────── -->
        <div
            class="relative p-1"
            :style="{
                display: 'grid',
                gridTemplateColumns: `repeat(${links.length}, 1fr)`,
            }"
        >
            <!-- Sliding yellow indicator — moves behind the active tab -->
            <div
                v-if="activeIndex >= 0"
                class="pointer-events-none absolute top-1 bottom-1 rounded-lg bg-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.3)]"
                :style="{
                    width: `calc(1fr - 2px)`,
                    width: `calc(${100 / links.length}% - 2px)`,
                    transform: `translateX(calc(${activeIndex} * (100% + 2px)))`,
                    transition: 'transform 0.28s cubic-bezier(0.4, 0, 0.2, 1)',
                }"
                aria-hidden="true"
            />

            <!-- Tab links -->
            <Link
                v-for="link in links"
                :key="link.routeName"
                :href="route(link.routeName)"
                :class="[
                    'relative z-10 flex items-center justify-center px-4 text-[11px] font-bold tracking-tight rounded-lg whitespace-nowrap transition-colors duration-200',
                    link.active
                        ? 'text-blue-950'
                        : 'text-blue-300/80 hover:text-white',
                ]"
            >
                {{ link.label }}
            </Link>
        </div>
    </div>
</template>
