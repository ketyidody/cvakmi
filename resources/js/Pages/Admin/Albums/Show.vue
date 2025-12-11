<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    album: Object,
});

const deletePhoto = (photo) => {
    if (confirm(`Are you sure you want to delete "${photo.title}"?`)) {
        router.delete(route('admin.photos.destroy', photo.id));
    }
};
</script>

<template>
    <Head :title="`Album: ${album.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ album.title }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ album.photos.length }} photos</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.photos.create', { album_id: album.id })" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Add Photos
                    </Link>
                    <Link :href="route('admin.albums.edit', album.id)" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                        Edit Album
                    </Link>
                    <Link :href="route('admin.albums.index')" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                        Back to Albums
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="album.photos.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No photos</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a new photo.</p>
                            <div class="mt-6">
                                <Link :href="route('admin.photos.create', { album_id: album.id })" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add Photo
                                </Link>
                            </div>
                        </div>

                        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="photo in album.photos" :key="photo.id" class="group relative border rounded-lg overflow-hidden hover:shadow-lg transition">
                                <div class="aspect-square bg-gray-200">
                                    <img
                                        v-if="photo.thumbnail_path"
                                        :src="`/storage/${photo.thumbnail_path}`"
                                        :alt="photo.title"
                                        class="w-full h-full object-cover"
                                    >
                                    <img
                                        v-else
                                        :src="`/storage/${photo.image_path}`"
                                        :alt="photo.title"
                                        class="w-full h-full object-cover"
                                    >
                                </div>

                                <div class="p-3">
                                    <h3 class="font-medium text-sm truncate">{{ photo.title }}</h3>
                                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                        <span>Order: {{ photo.display_order }}</span>
                                        <span v-if="photo.is_featured" class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                            Featured
                                        </span>
                                    </div>
                                </div>

                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                                    <Link :href="route('admin.photos.edit', photo.id)" class="bg-white text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-100">
                                        Edit
                                    </Link>
                                    <button @click="deletePhoto(photo)" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
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
