<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    packages: Array,
});

const deletePackage = (pkg) => {
    if (confirm(`Are you sure you want to delete "${pkg.name}"?`)) {
        router.delete(route('admin.pricing-packages.destroy', pkg.id));
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('sk-SK', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

<template>
    <Head title="Pricing Packages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pricing Packages</h2>
                <Link :href="route('admin.pricing-packages.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Pricing Package
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="packages.length === 0" class="text-center py-8 text-gray-500">
                            No pricing packages found. Create your first package!
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="pkg in packages" :key="pkg.id" class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-semibold">{{ pkg.name }}</h3>
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded',
                                            pkg.is_featured ? 'bg-yellow-100 text-yellow-800' : '',
                                            pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ pkg.is_featured ? 'Featured' : pkg.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ formatPrice(pkg.price) }}</span>
                                </div>

                                <div class="mb-2">
                                    <span class="text-sm text-gray-600">Type: </span>
                                    <span class="text-sm font-medium text-gray-900">{{ pkg.pricing_type?.name || 'N/A' }}</span>
                                    <span v-if="pkg.duration" class="ml-4 text-sm text-gray-600">Duration: {{ pkg.duration }}</span>
                                </div>

                                <p v-if="pkg.description" class="text-sm text-gray-600 mb-3">
                                    {{ pkg.description }}
                                </p>

                                <div v-if="pkg.services && pkg.services.length > 0" class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Services:</p>
                                    <ul class="list-disc list-inside text-sm text-gray-600">
                                        <li v-for="service in pkg.services" :key="service.id">
                                            {{ service.name }} <span v-if="service.pivot.quantity > 1">({{ service.pivot.quantity }}x)</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                    <span>Display Order: {{ pkg.display_order }}</span>
                                </div>

                                <div class="flex gap-2">
                                    <Link :href="route('admin.pricing-packages.edit', pkg.id)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                                        Edit
                                    </Link>
                                    <button @click="deletePackage(pkg)" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
