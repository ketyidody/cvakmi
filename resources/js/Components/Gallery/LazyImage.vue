<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    src: String,
    alt: String,
    placeholderSrc: String,
    className: String,
});

const isLoaded = ref(false);
const isInView = ref(false);
const imgRef = ref(null);
let observer = null;

const loadImage = () => {
    if (isLoaded.value) return;

    const img = new Image();
    img.src = props.src;
    img.onload = () => {
        isLoaded.value = true;
    };
};

onMounted(() => {
    // Intersection Observer for lazy loading
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    isInView.value = true;
                    loadImage();
                    if (observer) {
                        observer.unobserve(entry.target);
                    }
                }
            });
        },
        {
            rootMargin: '100px', // Start loading 100px before image enters viewport
            threshold: 0.01,
        }
    );

    if (imgRef.value) {
        observer.observe(imgRef.value);
    }
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
});
</script>

<template>
    <div ref="imgRef" :class="className" class="relative overflow-hidden bg-gray-100">
        <!-- Placeholder with pulse animation -->
        <div
            v-if="!isLoaded"
            class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
        >
            <div class="animate-pulse">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Actual Image with native lazy loading -->
        <Transition name="fade">
            <img
                v-if="isInView"
                :src="src"
                :alt="alt"
                :class="className"
                loading="lazy"
                decoding="async"
                @load="isLoaded = true"
                class="w-full h-full object-cover"
                :style="{ opacity: isLoaded ? 1 : 0 }"
            >
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active {
    transition: opacity 0.5s ease-in;
}

.fade-leave-active {
    transition: opacity 0.3s ease-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

img {
    transition: opacity 0.5s ease-in;
}
</style>
