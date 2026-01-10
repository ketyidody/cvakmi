<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';
import logoBlack from '../../images/logo-black.png';

defineProps({
    featuredPhotos: Array,
    albums: Array,
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Home" />

    <PublicLayout>
        <!-- Hero Section -->
        <div class="px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="max-w-7xl mx-auto text-center">
                <div class="mb-6 flex justify-center">
                    <img :src="logoBlack" alt="Cvakmi" class="h-24 md:h-32 lg:h-40 w-auto" />
                </div>
                <p class="text-lg md:text-xl text-gray-600 font-light tracking-wide max-w-2xl mx-auto">
                    Fotografia • Portfolio
                </p>
            </div>
        </div>

        <!-- Featured Photos Grid -->
        <div v-if="featuredPhotos.length > 0" class="px-4 sm:px-6 lg:px-8 pb-20">
            <div class="max-w-7xl mx-auto">
                <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                    <Link
                        v-for="photo in featuredPhotos"
                        :key="photo.id"
                        :href="photo.album ? route('gallery.show', photo.album.slug) : '#'"
                        class="block break-inside-avoid group"
                    >
                        <div class="relative overflow-hidden bg-gray-100">
                            <LazyImage
                                v-if="photo.medium_path"
                                :src="`/storage/${photo.medium_path}`"
                                :alt="photo.title"
                                class-name="w-full h-auto transition-all duration-700 group-hover:scale-105 group-hover:opacity-90"
                            />
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Albums Section -->
        <div v-if="albums.length > 0" class="px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-light tracking-wider mb-16 text-center text-gray-900 uppercase">Kolekcie</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
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
                        <h3 class="text-xl font-light tracking-wider text-gray-900 group-hover:text-gray-600 transition-colors mb-1 uppercase">
                            {{ album.title }}
                        </h3>
                        <p class="text-sm text-gray-500 font-light uppercase">
                            {{ album.photos_count }} {{ album.photos_count === 1 ? 'fotografia' : 'fotografie' }}
                        </p>
                    </Link>
                </div>

                <div class="text-center mt-16">
                    <Link
                        :href="route('gallery.index')"
                        class="inline-block border border-gray-900 text-gray-900 px-8 py-3 text-sm font-light tracking-wider hover:bg-gray-900 hover:text-white transition-all duration-300 uppercase"
                    >
                        Všetky albumy
                    </Link>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="featuredPhotos.length === 0 && albums.length === 0" class="px-4 sm:px-6 lg:px-8 py-32">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-light tracking-wider mb-6 text-gray-900 uppercase">Čoskoro</h2>
                <p class="text-gray-600 text-lg font-light mb-12">Nové fotografie budú čoskoro k dispozícii.</p>
                <Link
                    :href="route('gallery.index')"
                    class="inline-block border border-gray-900 text-gray-900 px-8 py-3 text-sm font-light tracking-wider hover:bg-gray-900 hover:text-white transition-all duration-300 uppercase"
                >
                    Portfólio
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
