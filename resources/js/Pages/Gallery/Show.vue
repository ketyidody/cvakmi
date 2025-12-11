<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';
import Lightbox from '@/Components/Gallery/Lightbox.vue';

const props = defineProps({
    album: Object,
    photos: Array,
});

const showLightbox = ref(false);
const currentPhotoIndex = ref(0);

const openLightbox = (index) => {
    currentPhotoIndex.value = index;
    showLightbox.value = true;
};

const closeLightbox = () => {
    showLightbox.value = false;
};

const navigateToPhoto = (index) => {
    currentPhotoIndex.value = index;
};
</script>

<template>
    <Head :title="album.title" />

    <PublicLayout>
        <!-- Album Header -->
        <div class="py-16 md:py-24 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <Link
                    :href="route('gallery.index')"
                    class="text-sm text-gray-500 hover:text-gray-900 mb-8 inline-flex items-center tracking-wider font-light transition-colors uppercase"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    Späť
                </Link>

                <div class="text-center mt-8 mb-16">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider text-gray-900 mb-4 uppercase">{{ album.title }}</h1>
                    <p v-if="album.description" class="text-gray-600 text-lg font-light max-w-3xl mx-auto mb-4">{{ album.description }}</p>
                    <p class="text-gray-500 text-sm tracking-wider font-light uppercase">{{ photos.length }} {{ photos.length === 1 ? 'fotografia' : 'fotografie' }}</p>
                </div>
            </div>
        </div>

        <!-- Photos Grid -->
        <div class="px-4 sm:px-6 lg:px-8 pb-24">
            <div class="max-w-7xl mx-auto">
                <div v-if="photos.length === 0" class="text-center py-32">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-light text-gray-700 uppercase">V tomto albume zatiaľ nie sú žiadne fotografie</h3>
                </div>

                <!-- Masonry Layout -->
                <div v-else class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                    <div
                        v-for="(photo, index) in photos"
                        :key="photo.id"
                        @click="openLightbox(index)"
                        class="group cursor-pointer break-inside-avoid"
                    >
                        <div class="relative overflow-hidden bg-gray-100">
                            <LazyImage
                                :src="photo.medium_path ? `/storage/${photo.medium_path}` : `/storage/${photo.image_path}`"
                                :alt="photo.title"
                                class-name="w-full h-auto transition-all duration-700 group-hover:scale-105 group-hover:opacity-90"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <Lightbox
            :photos="photos"
            :current-index="currentPhotoIndex"
            :show="showLightbox"
            @close="closeLightbox"
            @navigate="navigateToPhoto"
        />
    </PublicLayout>
</template>
