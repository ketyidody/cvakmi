<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    albums: Array,
});

const deleteAlbum = (album) => {
    if (confirm(`Are you sure you want to delete "${album.title}"?`)) {
        router.delete(route('admin.albums.destroy', album.id));
    }
};
</script>

<template>
    <Head title="Albums" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Albums</h2>
                <Link :href="route('admin.albums.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Album
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="albums.length === 0" class="text-center py-8 text-gray-500">
                            No albums found. Create your first album!
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="album in albums" :key="album.id" class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                <div v-if="album.cover_image" class="h-48 bg-gray-200">
                                    <img :src="`/storage/${album.cover_image}`" :alt="album.title" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold">{{ album.title }}</h3>
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded',
                                            album.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ album.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>

                                    <p v-if="album.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ album.description }}
                                    </p>

                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span>{{ album.photos_count }} photos</span>
                                        <span>Order: {{ album.display_order }}</span>
                                    </div>

                                    <div class="flex gap-2">
                                        <Link :href="route('admin.albums.show', album.id)" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm">
                                            View
                                        </Link>
                                        <Link :href="route('admin.albums.edit', album.id)" class="flex-1 text-center bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded text-sm">
                                            Edit
                                        </Link>
                                        <button @click="deleteAlbum(album)" class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded text-sm">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
