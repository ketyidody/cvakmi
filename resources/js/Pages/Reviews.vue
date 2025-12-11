<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    reviews: Array,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long'
    });
};
</script>

<template>
    <Head title="Recenzie" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-6xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-20">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider mb-4 text-gray-900 uppercase">Recenzie</h1>
                    <p class="text-gray-600 text-lg font-light uppercase">Čo hovoria moji klienti</p>
                </div>

                <!-- No Reviews Message -->
                <div v-if="!reviews || reviews.length === 0" class="text-center py-16">
                    <p class="text-gray-500 font-light text-lg">Zatiaľ žiadne recenzie.</p>
                </div>

                <!-- Reviews Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        v-for="review in reviews"
                        :key="review.id"
                        class="border border-gray-200 p-8 hover:border-gray-400 transition-colors duration-300"
                    >
                        <!-- Reviewer Profile -->
                        <div class="flex items-center gap-4 mb-6">
                            <div v-if="review.thumbnail_image" class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0">
                                <img :src="`/storage/${review.thumbnail_image}`" :alt="review.full_name" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-light uppercase tracking-wider">{{ review.full_name }}</p>
                                <p class="text-gray-500 text-sm font-light uppercase">{{ formatDate(review.review_date) }}</p>
                                <p v-if="review.source" class="text-gray-400 text-xs font-light uppercase">via {{ review.source }}</p>
                            </div>
                        </div>

                        <!-- Rating Stars -->
                        <div class="flex mb-4">
                            <svg
                                v-for="star in 5"
                                :key="star"
                                class="w-5 h-5"
                                :class="star <= review.rating ? 'text-gray-900' : 'text-gray-300'"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>

                        <!-- Review Text -->
                        <p class="text-gray-700 font-light leading-relaxed">
                            "{{ review.content }}"
                        </p>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="mt-20 text-center border-t border-gray-200 pt-16">
                    <h2 class="text-2xl md:text-3xl font-light tracking-wider mb-6 text-gray-900 uppercase">
                        Chcete byť ďalší spokojný klient?
                    </h2>
                    <p class="text-gray-600 font-light mb-8 max-w-2xl mx-auto">
                        Kontaktujte ma a dohodneme si termín konzultácie, kde prediskutujeme váš projekt
                        a nájdeme najlepšie riešenie pre vaše potreby.
                    </p>
                    <a
                        href="/kontakt"
                        class="inline-block border border-gray-900 text-gray-900 px-8 py-3 text-sm font-light tracking-wider hover:bg-gray-900 hover:text-white transition-all duration-300 uppercase"
                    >
                        Kontaktujte ma
                    </a>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
