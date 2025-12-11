<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    photos: Object,
    albums: Array,
    filters: Object,
});

const selectedAlbum = ref(props.filters.album_id || '');

watch(selectedAlbum, (value) => {
    router.get(route('admin.photos.index'), { album_id: value }, {
        preserveState: true,
        replace: true,
    });
});

const deletePhoto = (photo) => {
    if (confirm(`Are you sure you want to delete "${photo.title}"?`)) {
        router.delete(route('admin.photos.destroy', photo.id));
    }
};
</script>

<template>
    <Head title="Photos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Photos</h2>
                <Link :href="route('admin.photos.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Upload Photo
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <label for="album-filter" class="block text-sm font-medium text-gray-700 mb-2">
                                Filter by Album
                            </label>
                            <select
                                id="album-filter"
                                v-model="selectedAlbum"
                                class="block w-full md:w-64 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">All Albums</option>
                                <option v-for="album in albums" :key="album.id" :value="album.id">
                                    {{ album.title }}
                                </option>
                            </select>
                        </div>

                        <div v-if="photos.data.length === 0" class="text-center py-12 text-gray-500">
                            No photos found. Upload your first photo!
                        </div>

                        <div v-else>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="photo in photos.data" :key="photo.id" class="group relative border rounded-lg overflow-hidden hover:shadow-lg transition">
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
                                        <p class="text-xs text-gray-500 mt-1">{{ photo.album?.title || 'No Album' }}</p>
                                        <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                            <span v-if="photo.width && photo.height">{{ photo.width }} × {{ photo.height }}</span>
                                            <span v-else>No dimensions</span>
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

                            <!-- Pagination -->
                            <div v-if="photos.links && photos.links.length > 3" class="mt-6 flex justify-center gap-1">
                                <component
                                    v-for="(link, index) in photos.links"
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || undefined"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-2 text-sm rounded',
                                        link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
