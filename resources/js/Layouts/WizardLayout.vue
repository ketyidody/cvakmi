<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import StepIndicator from '@/Components/StepIndicator.vue';

defineProps({
    // Optional: when present, the step indicator is shown. History/show pages
    // re-use this layout without steps for visual consistency.
    steps: {
        type: Array,
        default: null,
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="border-b bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link :href="route('order.start')" class="flex items-center text-gray-800">
                    <ApplicationLogo class="block h-8 w-auto fill-current" />
                </Link>
                <div class="flex items-center gap-5 text-sm">
                    <Link :href="route('order.history')" class="text-gray-600 hover:text-gray-900">
                        Moje objednávky
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-gray-600 hover:text-gray-900">
                        Odhlásiť
                    </Link>
                </div>
            </div>
        </header>

        <StepIndicator v-if="steps" :steps="steps" />

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
