<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    classrooms: Array,
});

const deleteClassroom = (classroom) => {
    if (confirm(`Delete "${classroom.name}"? This also deletes all its photos.`)) {
        router.delete(route('admin.classrooms.destroy', classroom.slug));
    }
};
</script>

<template>
    <Head title="Classrooms" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Classrooms</h2>
                <Link :href="route('admin.classrooms.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Classroom
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="classrooms.length === 0" class="text-center py-8 text-gray-500">
                        No classrooms yet. Create your first one!
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="classroom in classrooms" :key="classroom.id" class="border rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold">{{ classroom.name }}</h3>
                                    <span :class="['px-2 py-1 text-xs rounded', classroom.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                        {{ classroom.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ classroom.photos_count }} photos · {{ classroom.parents_count }} parents
                                </div>
                            </div>

                            <p v-if="classroom.description" class="text-sm text-gray-600 mb-3">{{ classroom.description }}</p>

                            <div class="flex gap-2">
                                <Link :href="route('admin.classroom-photos.index', { classroom_id: classroom.id })" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-4 py-2 rounded text-sm">
                                    Manage Photos
                                </Link>
                                <Link :href="route('admin.classrooms.edit', classroom.slug)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                                    Edit
                                </Link>
                                <button @click="deleteClassroom(classroom)" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
