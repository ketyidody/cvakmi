<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    thumbnail_image: null,
    full_name: '',
    review_date: new Date().toISOString().split('T')[0],
    source: '',
    content: '',
    rating: 5,
    display_order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.reviews.store'), {
        forceFormData: true,
    });
};

const handleFileChange = (event) => {
    form.thumbnail_image = event.target.files[0];
};
</script>

<template>
    <Head title="Create Review" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Review</h2>
                <Link :href="route('admin.reviews.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Reviews
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="thumbnail_image" value="Thumbnail Image" />
                            <input
                                id="thumbnail_image"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                            <InputError class="mt-2" :message="form.errors.thumbnail_image" />
                            <p class="mt-1 text-sm text-gray-500">Optional profile photo for the reviewer</p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="full_name" value="Full Name" />
                            <TextInput
                                id="full_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.full_name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.full_name" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="review_date" value="Review Date" />
                                <TextInput
                                    id="review_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.review_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.review_date" />
                            </div>

                            <div>
                                <InputLabel for="source" value="Source" />
                                <TextInput
                                    id="source"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.source"
                                    placeholder="e.g., Google, Facebook"
                                />
                                <InputError class="mt-2" :message="form.errors.source" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="content" value="Review Content" />
                            <textarea
                                id="content"
                                v-model="form.content"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="6"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="rating" value="Rating (1-5 stars)" />
                                <select
                                    id="rating"
                                    v-model.number="form.rating"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option :value="5">5 stars (★★★★★)</option>
                                    <option :value="4">4 stars (★★★★☆)</option>
                                    <option :value="3">3 stars (★★★☆☆)</option>
                                    <option :value="2">2 stars (★★☆☆☆)</option>
                                    <option :value="1">1 star (★☆☆☆☆)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.rating" />
                            </div>

                            <div>
                                <InputLabel for="display_order" value="Display Order" />
                                <TextInput
                                    id="display_order"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.display_order"
                                />
                                <InputError class="mt-2" :message="form.errors.display_order" />
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_active" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Create Review
                            </PrimaryButton>

                            <Link :href="route('admin.reviews.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Creating...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
