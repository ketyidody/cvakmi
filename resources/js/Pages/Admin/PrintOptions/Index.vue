<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    printOptions: Array,
});

const formatPrice = (price) =>
    new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(price ?? 0);

const deleteOption = (option) => {
    if (confirm(`Delete "${option.name}"?`)) {
        router.delete(route('admin.print-options.destroy', option.id));
    }
};
</script>

<template>
    <Head title="Print Options" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Print Options</h2>
                <Link :href="route('admin.print-options.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Print Option
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div v-if="printOptions.length === 0" class="text-center py-8 text-gray-500">
                        No print options yet.
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="option in printOptions" :key="option.id" class="border rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="font-semibold">{{ option.name }}</h3>
                                    <span :class="['px-2 py-1 text-xs rounded', option.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                        {{ option.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <p v-if="option.description" class="text-sm text-gray-600">{{ option.description }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-medium text-gray-700">{{ formatPrice(option.price) }}</span>
                                <Link :href="route('admin.print-options.edit', option.id)" class="text-sm text-blue-700 hover:underline">Edit</Link>
                                <button @click="deleteOption(option)" class="text-sm text-red-700 hover:underline">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
