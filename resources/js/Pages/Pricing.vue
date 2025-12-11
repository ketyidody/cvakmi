<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    pricingTypes: Array,
    additionalServices: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('sk-SK', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head title="Cenník" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-7xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-20">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider mb-4 text-gray-900 uppercase">Cenník</h1>
                    <p class="text-gray-600 text-lg font-light uppercase">Balíky a služby</p>
                </div>

                <!-- Pricing Types -->
                <div v-for="pricingType in pricingTypes" :key="pricingType.id" class="mb-24">
                    <!-- Pricing Type Header -->
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-light tracking-wider mb-4 text-gray-900 uppercase">
                            {{ pricingType.name }}
                        </h2>
                        <p v-if="pricingType.description" class="text-gray-600 font-light max-w-2xl mx-auto">
                            {{ pricingType.description }}
                        </p>
                    </div>

                    <!-- Packages Grid -->
                    <div v-if="pricingType.packages && pricingType.packages.length > 0"
                         class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div
                            v-for="pkg in pricingType.packages"
                            :key="pkg.id"
                            class="border transition-all duration-300"
                            :class="pkg.is_featured ? 'border-gray-900 shadow-lg scale-105' : 'border-gray-200 hover:border-gray-400'"
                        >
                            <div class="p-8">
                                <!-- Package Name -->
                                <h3 class="text-2xl font-light tracking-wider mb-2 text-gray-900 uppercase">
                                    {{ pkg.name }}
                                </h3>

                                <!-- Featured Badge -->
                                <div v-if="pkg.is_featured" class="mb-4">
                                    <span class="text-xs font-light tracking-wider text-gray-600 uppercase">Najpopulárnejší</span>
                                </div>

                                <!-- Price -->
                                <div class="mb-6">
                                    <span class="text-4xl font-light text-gray-900">{{ formatPrice(pkg.price) }}</span>
                                    <span v-if="pkg.duration" class="text-gray-600 font-light ml-2">/ {{ pkg.duration }}</span>
                                </div>

                                <!-- Description -->
                                <p v-if="pkg.description" class="text-gray-700 font-light mb-6">
                                    {{ pkg.description }}
                                </p>

                                <!-- Services -->
                                <ul v-if="pkg.services && pkg.services.length > 0" class="space-y-4 mb-8">
                                    <li
                                        v-for="service in pkg.services"
                                        :key="service.id"
                                        class="flex items-start"
                                    >
                                        <svg class="w-5 h-5 text-gray-900 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-700 font-light">
                                            {{ service.name }}
                                            <span v-if="service.pivot.quantity > 1" class="text-gray-500 text-sm">({{ service.pivot.quantity }}x)</span>
                                        </span>
                                    </li>
                                </ul>

                                <!-- CTA Button -->
                                <a
                                    href="/kontakt"
                                    class="block text-center border border-gray-900 text-gray-900 px-6 py-3 text-sm font-light tracking-wider hover:bg-gray-900 hover:text-white transition-all duration-300 uppercase"
                                >
                                    Objednať
                                </a>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center text-gray-500 font-light">
                        Žiadne balíky nie sú momentálne dostupné pre túto kategóriu.
                    </div>
                </div>

                <!-- Additional Services -->
                <div v-if="additionalServices && additionalServices.length > 0" class="border-t border-gray-200 pt-16">
                    <h2 class="text-3xl font-light tracking-wider mb-12 text-center text-gray-900 uppercase">
                        Doplnkové služby
                    </h2>
                    <div class="max-w-3xl mx-auto">
                        <div class="space-y-4">
                            <div
                                v-for="service in additionalServices"
                                :key="service.id"
                                class="flex justify-between items-center py-4 border-b border-gray-200"
                            >
                                <div>
                                    <span class="text-gray-700 font-light block">{{ service.name }}</span>
                                    <span v-if="service.description" class="text-gray-500 font-light text-sm">{{ service.description }}</span>
                                </div>
                                <span v-if="service.price" class="text-gray-900 font-light">{{ formatPrice(service.price) }}</span>
                                <span v-else class="text-gray-500 font-light text-sm italic">Cena na požiadanie</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Note -->
                <div class="mt-16 text-center max-w-3xl mx-auto">
                    <p class="text-gray-600 font-light text-sm">
                        * Všetky ceny sú orientačné a môžu sa líšiť v závislosti od konkrétnych požiadaviek projektu.
                        Pre presný cenový návrh ma prosím kontaktujte.
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
