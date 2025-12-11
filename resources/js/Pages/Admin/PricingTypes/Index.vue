<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    types: Array,
});

const deleteType = (type) => {
    if (confirm(`Are you sure you want to delete "${type.name}"? This will also delete all associated packages.`)) {
        router.delete(route('admin.pricing-types.destroy', type.id));
    }
};
</script>

<template>
    <Head title="Pricing Types" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pricing Types</h2>
                <Link :href="route('admin.pricing-types.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Pricing Type
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="types.length === 0" class="text-center py-8 text-gray-500">
                            No pricing types found. Create your first pricing type!
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="type in types" :key="type.id" class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-semibold">{{ type.name }}</h3>
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded',
                                            type.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ type.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ type.packages_count }} packages</span>
                                </div>

                                <p v-if="type.description" class="text-sm text-gray-600 mb-3">
                                    {{ type.description }}
                                </p>

                                <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                    <span>Slug: {{ type.slug }}</span>
                                    <span>Display Order: {{ type.display_order }}</span>
                                </div>

                                <div class="flex gap-2">
                                    <Link :href="route('admin.pricing-types.edit', type.id)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                                        Edit
                                    </Link>
                                    <button @click="deleteType(type)" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
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
