<template>
  <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6">
    <div class="flex gap-3">
      <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5.36 4.24l-.707-.707M9 12a3 3 0 11 6 0 3 3 0 01-6 0z" />
      </svg>
      <div>
        <h3 class="font-semibold text-yellow-900 text-sm">Tips</h3>
        <p class="text-yellow-800 text-xs mt-1">
          <slot />
        </p>
      </div>
    </div>
  </div>
</template>
