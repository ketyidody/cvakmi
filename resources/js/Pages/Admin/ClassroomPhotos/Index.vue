<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    photos: Object,
    classrooms: Array,
    filters: Object,
});

const selectedClassroom = ref(props.filters?.classroom_id ?? '');

const list = computed(() => props.photos.data ?? props.photos);

const filterByClassroom = () => {
    router.get(route('admin.classroom-photos.index'),
        selectedClassroom.value ? { classroom_id: selectedClassroom.value } : {},
        { preserveState: true, replace: true });
};

const thumb = (photo) => route('order.photo', { classroomPhoto: photo.id, size: 'thumbnail' });

const deletePhoto = (photo) => {
    if (confirm('Delete this photo?')) {
        router.delete(route('admin.classroom-photos.destroy', photo.id));
    }
};
</script>

<template>
    <Head title="Class Photos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Class Photos</h2>
                <Link :href="route('admin.classroom-photos.create', selectedClassroom ? { classroom_id: selectedClassroom } : {})"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Upload Photos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by classroom</label>
                        <select v-model="selectedClassroom" @change="filterByClassroom"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All classrooms</option>
                            <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div v-if="list.length === 0" class="text-center py-8 text-gray-500">
                        No photos found.
                    </div>

                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <div v-for="photo in list" :key="photo.id" class="border rounded-lg overflow-hidden">
                            <img :src="thumb(photo)" :alt="photo.title" class="w-full h-40 object-cover bg-gray-100" />
                            <div class="p-2">
                                <p class="text-xs text-gray-600 truncate" :title="photo.title">{{ photo.title }}</p>
                                <p class="text-xs text-gray-400">{{ photo.classroom?.name }}</p>
                                <div class="flex gap-2 mt-2">
                                    <Link :href="route('admin.classroom-photos.edit', photo.id)" class="text-xs text-blue-700 hover:underline">Edit</Link>
                                    <button @click="deletePhoto(photo)" class="text-xs text-red-700 hover:underline">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="photos.links" class="mt-6 flex flex-wrap gap-1">
                        <template v-for="link in photos.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                :class="['px-3 py-1 text-sm rounded', link.active ? 'bg-blue-600 text-white' : 'text-gray-600']" />
                            <span v-else v-html="link.label" class="px-3 py-1 text-sm rounded text-gray-300" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
