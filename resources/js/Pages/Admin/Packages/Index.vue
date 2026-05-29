<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    packages: Array,
});

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);

const deletePackage = (pkg) => {
    if (confirm(`Delete "${pkg.name}"?`)) {
        router.delete(route('admin.packages.destroy', pkg.id));
    }
};
</script>

<template>
    <Head title="Packages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Packages</h2>
                <Link :href="route('admin.packages.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Package
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div v-if="packages.length === 0" class="text-center py-8 text-gray-500">
                        No packages yet.
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="pkg in packages" :key="pkg.id" class="border rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-semibold">{{ pkg.name }}</h3>
                                        <span :class="['px-2 py-1 text-xs rounded', pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                            {{ pkg.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p v-if="pkg.description" class="text-sm text-gray-600 mt-1">{{ pkg.description }}</p>
                                    <ul class="text-sm text-gray-700 mt-2 space-y-0.5">
                                        <li v-for="opt in pkg.print_options" :key="opt.id">
                                            {{ opt.pivot.included_quantity }}× {{ opt.name }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-medium text-gray-700">{{ formatPrice(pkg.price) }}</span>
                                    <Link :href="route('admin.packages.edit', pkg.id)" class="text-sm text-blue-700 hover:underline">Edit</Link>
                                    <button @click="deletePackage(pkg)" class="text-sm text-red-700 hover:underline">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
