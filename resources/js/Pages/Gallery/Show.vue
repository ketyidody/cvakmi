<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import LazyImage from '@/Components/Gallery/LazyImage.vue';
import Lightbox from '@/Components/Gallery/Lightbox.vue';
import ImagesGrid from 'vue-images-grid';
import 'vue-images-grid/dist/style.css';

const props = defineProps({
    album: Object,
    photos: Array,
});

const showLightbox = ref(false);
const currentPhotoIndex = ref(0);
const showBackToTop = ref(false);
const windowWidth = ref(window.innerWidth);

// Prepare images for vue-images-grid
const gridImages = computed(() => {
    return props.photos.map((photo, index) => ({
        id: index,
        src: photo.medium_path ? `/storage/${photo.medium_path}` : `/storage/${photo.image_path}`,
    }));
});

const cols = computed(() => {
    if (windowWidth.value < 768) {
        return 1; // Mobile: 1 column
    } else if (windowWidth.value < 1024) {
        return 2; // Tablet: 2 columns
    } else {
        return 3; // Desktop: 3 columns
    }
});

const handleImageClick = (imageData) => {
    // The event passes the image object with the id (which is our index)
    currentPhotoIndex.value = imageData.id;
    showLightbox.value = true;
};

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

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 300;
};

const handleResize = () => {
    windowWidth.value = window.innerWidth;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
});
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

                <!-- Masonry Grid Layout -->
                <div v-else class="masonry-grid">
                    <ImagesGrid
                        :images="gridImages"
                        :cols="cols"
                        :image-style="{ marginBottom: '24px', cursor: 'pointer' }"
                        @onImageClick="handleImageClick"
                    />
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

        <!-- Back to Top Button (Mobile Only) -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <button
                v-if="showBackToTop"
                @click="scrollToTop"
                class="fixed bottom-6 right-6 z-40 bg-gray-900 text-white p-3 rounded-full shadow-lg hover:bg-gray-800 transition-colors"
                aria-label="Back to top"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </button>
        </transition>
    </PublicLayout>
</template>

<style scoped>
.masonry-grid :deep(div) {
    overflow: hidden;
}

.masonry-grid :deep(img) {
    transition: transform 0.7s ease, opacity 0.7s ease;
}

.masonry-grid :deep(img:hover) {
    transform: scale(1.1);
    opacity: 0.9;
}
</style>
