<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    reviews: Array,
});

const deleteReview = (review) => {
    if (confirm(`Are you sure you want to delete the review from "${review.full_name}"?`)) {
        router.delete(route('admin.reviews.destroy', review.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const renderStars = (rating) => {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
};
</script>

<template>
    <Head title="Reviews" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reviews</h2>
                <Link :href="route('admin.reviews.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Review
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="reviews.length === 0" class="text-center py-8 text-gray-500">
                            No reviews found. Create your first review!
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="review in reviews" :key="review.id" class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex gap-4">
                                    <!-- Thumbnail -->
                                    <div class="flex-shrink-0">
                                        <div v-if="review.thumbnail_image" class="w-20 h-20 rounded-full overflow-hidden bg-gray-200">
                                            <img :src="`/storage/${review.thumbnail_image}`" :alt="review.full_name" class="w-full h-full object-cover">
                                        </div>
                                        <div v-else class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <h3 class="text-lg font-semibold">{{ review.full_name }}</h3>
                                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                                    <span class="text-yellow-500">{{ renderStars(review.rating) }}</span>
                                                    <span>{{ formatDate(review.review_date) }}</span>
                                                    <span v-if="review.source" class="text-gray-500">via {{ review.source }}</span>
                                                </div>
                                            </div>
                                            <span :class="[
                                                'px-2 py-1 text-xs rounded',
                                                review.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ review.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>

                                        <p class="text-gray-700 mb-3 line-clamp-2">{{ review.content }}</p>

                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-500">Display Order: {{ review.display_order }}</span>
                                            <div class="flex gap-2">
                                                <Link :href="route('admin.reviews.edit', review.id)" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded text-sm">
                                                    Edit
                                                </Link>
                                                <button @click="deleteReview(review)" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
