<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    photos: Array,
    currentIndex: Number,
    show: Boolean,
});

const emit = defineEmits(['close', 'navigate']);

const currentPhoto = computed(() => props.photos[props.currentIndex]);

const hasPrevious = computed(() => props.currentIndex > 0);
const hasNext = computed(() => props.currentIndex < props.photos.length - 1);

const navigatePrevious = () => {
    if (hasPrevious.value) {
        emit('navigate', props.currentIndex - 1);
    }
};

const navigateNext = () => {
    if (hasNext.value) {
        emit('navigate', props.currentIndex + 1);
    }
};

const handleKeydown = (e) => {
    if (!props.show) return;

    if (e.key === 'Escape') {
        emit('close');
    } else if (e.key === 'ArrowLeft') {
        navigatePrevious();
    } else if (e.key === 'ArrowRight') {
        navigateNext();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

// Prevent body scroll when lightbox is open
watch(() => props.show, (newValue) => {
    if (newValue) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <Transition name="lightbox">
        <div
            v-if="show && currentPhoto"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95"
            @click.self="$emit('close')"
        >
            <!-- Close Button -->
            <button
                @click="$emit('close')"
                class="absolute top-4 right-4 z-50 text-white hover:text-gray-300 transition"
                aria-label="Close lightbox"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Previous Button -->
            <button
                v-if="hasPrevious"
                @click="navigatePrevious"
                class="absolute left-4 z-50 text-white hover:text-gray-300 transition p-2"
                aria-label="Previous photo"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Next Button -->
            <button
                v-if="hasNext"
                @click="navigateNext"
                class="absolute right-4 z-50 text-white hover:text-gray-300 transition p-2"
                aria-label="Next photo"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Photo Container -->
            <div class="relative max-w-7xl max-h-screen mx-auto px-4">
                <img
                    :src="`/storage/${currentPhoto.image_path}`"
                    :alt="currentPhoto.title"
                    class="max-w-full max-h-[90vh] w-auto h-auto mx-auto object-contain"
                >

                <!-- Photo Info -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6 text-white">
                    <p class="text-xs text-gray-400 mt-2">
                        {{ currentIndex + 1 }} / {{ photos.length }}
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active {
    transition: opacity 0.3s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
    opacity: 0;
}
</style>
