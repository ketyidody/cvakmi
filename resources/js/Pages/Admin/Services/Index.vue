<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    services: Array,
});

const deleteService = (service) => {
    if (confirm(`Are you sure you want to delete "${service.name}"?`)) {
        router.delete(route('admin.services.destroy', service.id));
    }
};

const formatPrice = (price) => {
    if (!price) return 'N/A';
    return new Intl.NumberFormat('sk-SK', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

<template>
    <Head title="Services" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services</h2>
                <Link :href="route('admin.services.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Service
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="services.length === 0" class="text-center py-8 text-gray-500">
                            No services found. Create your first service!
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="service in services" :key="service.id" class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-semibold">{{ service.name }}</h3>
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded',
                                            service.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ service.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm font-medium text-gray-700">{{ formatPrice(service.price) }}</span>
                                        <span class="text-sm text-gray-500">{{ service.packages_count }} packages</span>
                                    </div>
                                </div>

                                <p v-if="service.description" class="text-sm text-gray-600 mb-3">
                                    {{ service.description }}
                                </p>

                                <div class="flex gap-2">
                                    <Link :href="route('admin.services.edit', service.id)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                                        Edit
                                    </Link>
                                    <button @click="deleteService(service)" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
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
