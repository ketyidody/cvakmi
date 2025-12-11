<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    photo: Object,
    albums: Array,
});

const form = useForm({
    album_id: props.photo.album_id,
    title: props.photo.title,
    description: props.photo.description,
    image: null,
    display_order: props.photo.display_order,
    is_featured: props.photo.is_featured,
});

const imagePreview = ref(null);

const submit = () => {
    form.post(route('admin.photos.update', props.photo.id), {
        forceFormData: true,
        _method: 'put',
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    // Generate preview
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <Head title="Edit Photo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Photo: {{ photo.title }}</h2>
                <Link :href="route('admin.photos.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Photos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="album_id" value="Album" />
                            <select
                                id="album_id"
                                v-model="form.album_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select an album</option>
                                <option v-for="album in albums" :key="album.id" :value="album.id">
                                    {{ album.title }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.album_id" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="title" value="Title" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="image" value="Photo" />

                            <div v-if="imagePreview" class="mb-4">
                                <img :src="imagePreview" alt="Preview" class="max-h-64 rounded">
                                <p class="text-sm text-gray-500 mt-1">New preview</p>
                            </div>

                            <div v-else-if="photo.medium_path" class="mb-4">
                                <img :src="`/storage/${photo.medium_path}`" :alt="photo.title" class="max-h-64 rounded">
                                <p class="text-sm text-gray-500 mt-1">Current photo</p>
                            </div>

                            <input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                            <InputError class="mt-2" :message="form.errors.image" />
                            <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image. New thumbnails will be generated if you upload a new image.</p>
                        </div>

                        <div class="mb-4">
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

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Featured Photo</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_featured" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Update Photo
                            </PrimaryButton>

                            <Link :href="route('admin.photos.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Updating...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
