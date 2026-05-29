<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    steps: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav class="bg-white border-b">
        <ol class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-1 overflow-x-auto text-sm">
            <template v-for="(step, idx) in steps" :key="step.key">
                <Link :href="step.url"
                    :class="['flex items-center gap-2 px-3 py-1.5 rounded-full transition whitespace-nowrap',
                        step.current ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100']">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-medium"
                          :class="step.current ? 'bg-white/30' : 'bg-gray-200'">
                        {{ idx + 1 }}
                    </span>
                    {{ step.label }}
                </Link>
                <svg v-if="idx < steps.length - 1" class="w-4 h-4 text-gray-300 flex-shrink-0"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </template>
        </ol>
    </nav>
</template>
