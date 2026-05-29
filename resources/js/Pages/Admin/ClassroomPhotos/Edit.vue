<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    photo: Object,
    classrooms: Array,
});

const form = useForm({
    classroom_id: props.photo.classroom_id,
    title: props.photo.title,
    display_order: props.photo.display_order,
});

const thumb = route('order.photo', { classroomPhoto: props.photo.id, size: 'medium' });

const submit = () => form.put(route('admin.classroom-photos.update', props.photo.id));
</script>

<template>
    <Head title="Edit Photo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Photo</h2>
                <Link :href="route('admin.classroom-photos.index', { classroom_id: photo.classroom_id })" class="text-gray-600 hover:text-gray-900">Back to Photos</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <img :src="thumb" :alt="photo.title" class="mb-4 max-h-64 rounded object-contain bg-gray-100" />

                        <div class="mb-4">
                            <InputLabel for="classroom_id" value="Classroom" />
                            <select id="classroom_id" v-model="form.classroom_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.classroom_id" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="display_order" value="Display Order" />
                            <TextInput id="display_order" type="number" class="mt-1 block w-full" v-model="form.display_order" />
                            <InputError class="mt-2" :message="form.errors.display_order" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>
                            <Link :href="route('admin.classroom-photos.index', { classroom_id: photo.classroom_id })" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
