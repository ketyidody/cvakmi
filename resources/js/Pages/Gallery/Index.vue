<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';

const props = defineProps({
    albums: Array,
});
</script>

<template>
    <Head title="Portfólio" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-7xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-20">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider mb-4 text-gray-900 uppercase">Portfólio</h1>
                    <p class="text-gray-600 text-lg font-light uppercase">Kolekcia fotografií</p>
                </div>

                <!-- Empty State -->
                <div v-if="albums.length === 0" class="text-center py-32">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-2xl font-light text-gray-700 mb-2 uppercase">Zatiaľ žiadne albumy</h3>
                    <p class="text-gray-500 font-light uppercase">Nové kolekcie budú čoskoro pridané</p>
                </div>

                <!-- Albums Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <Link
                        v-for="album in albums"
                        :key="album.id"
                        :href="route('gallery.show', album.slug)"
                        class="group"
                    >
                        <div class="relative overflow-hidden bg-gray-100 aspect-[4/3] mb-4">
                            <LazyImage
                                v-if="album.cover_image"
                                :src="`/storage/${album.cover_image}`"
                                :alt="album.title"
                                class-name="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 group-hover:opacity-90"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Album Info -->
                        <div>
                            <h3 class="text-xl font-light tracking-wider text-gray-900 group-hover:text-gray-600 transition-colors mb-1 uppercase">
                                {{ album.title }}
                            </h3>
                            <p class="text-sm text-gray-500 font-light uppercase">
                                {{ album.photos_count }} {{ album.photos_count === 1 ? 'fotografia' : 'fotografie' }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
