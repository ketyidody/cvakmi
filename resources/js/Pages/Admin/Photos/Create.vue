<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    albums: Array,
});

const form = useForm({
    album_id: props.albums.length > 0 ? props.albums[0].id : '',
    title: '',
    description: '',
    images: [],
    display_order: 0,
    is_featured: false,
});

const imagePreviews = ref([]);
const fileInput = ref(null);

const submit = () => {
    form.post(route('admin.photos.store'), {
        forceFormData: true,
    });
};

const handleFileChange = (event) => {
    const files = Array.from(event.target.files);
    form.images = files;

    // Generate previews
    imagePreviews.value = [];
    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push({
                url: e.target.result,
                name: file.name,
            });
        };
        reader.readAsDataURL(file);
    });
};

const removePreview = (index) => {
    imagePreviews.value.splice(index, 1);

    // Update form images array
    const dt = new DataTransfer();
    const files = Array.from(form.images);
    files.splice(index, 1);

    files.forEach((file) => {
        dt.items.add(file);
    });

    form.images = Array.from(dt.files);

    // Update file input
    if (fileInput.value) {
        fileInput.value.files = dt.files;
    }
};
</script>

<template>
    <Head title="Upload Photos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload Photos</h2>
                <Link :href="route('admin.photos.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Photos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="album_id" value="Album *" />
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
                            <InputLabel for="images" value="Photos *" />

                            <input
                                id="images"
                                ref="fileInput"
                                type="file"
                                accept="image/*"
                                multiple
                                @change="handleFileChange"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.images" />
                            <p class="mt-1 text-sm text-gray-500">
                                Select one or more images. Thumbnails and medium sizes will be generated automatically.
                            </p>
                        </div>

                        <!-- Image Previews -->
                        <div v-if="imagePreviews.length > 0" class="mb-6">
                            <InputLabel value="Selected Images" class="mb-3" />
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div
                                    v-for="(preview, index) in imagePreviews"
                                    :key="index"
                                    class="relative group"
                                >
                                    <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                                        <img
                                            :src="preview.url"
                                            :alt="preview.name"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="removePreview(index)"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        title="Remove image"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <p class="text-xs text-gray-600 mt-1 truncate" :title="preview.name">
                                        {{ preview.name }}
                                    </p>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                {{ imagePreviews.length }} {{ imagePreviews.length === 1 ? 'image' : 'images' }} selected
                            </p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="title" value="Base Title (optional)" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                            <p class="mt-1 text-sm text-gray-500">
                                If provided, filenames will be appended. Otherwise, only filenames will be used as titles.
                            </p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description (optional)" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                            <p class="mt-1 text-sm text-gray-500">
                                This description will be applied to all uploaded photos.
                            </p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="display_order" value="Starting Display Order" />
                            <TextInput
                                id="display_order"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.display_order"
                            />
                            <InputError class="mt-2" :message="form.errors.display_order" />
                            <p class="mt-1 text-sm text-gray-500">
                                Photos will be ordered sequentially starting from this number. Lower numbers appear first.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Mark first photo as featured</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_featured" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing || imagePreviews.length === 0">
                                Upload {{ imagePreviews.length }} {{ imagePreviews.length === 1 ? 'Photo' : 'Photos' }}
                            </PrimaryButton>

                            <Link :href="route('admin.photos.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Uploading photos, please wait...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
