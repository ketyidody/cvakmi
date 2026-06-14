<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import StepIndicator from '@/Components/StepIndicator.vue';
import logoBlack from '../../images/logo-black.png';

defineProps({
    // Optional: when present, the step indicator is shown. History/show pages
    // re-use this layout without steps for visual consistency.
    steps: {
        type: Array,
        default: null,
    },
});

const ordersEnabled = computed(() => usePage().props.order?.ordersEnabled ?? true);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="border-b bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link :href="route('order.start')" class="flex items-center">
                    <img :src="logoBlack" alt="Cvakmi" class="block h-6 w-auto" />
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

        <div v-if="!ordersEnabled" class="bg-amber-50 border-b border-amber-200">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-sm font-medium text-amber-900">
                Objednávanie je uzavreté.
            </div>
        </div>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
