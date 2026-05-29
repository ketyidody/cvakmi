<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    classrooms: Array,
    selectedClassroomId: [Number, String],
});

const form = useForm({
    classroom_id: props.selectedClassroomId ?? '',
    title: '',
    images: [],
});

const onFiles = (e) => {
    form.images = Array.from(e.target.files);
};

const submit = () => {
    form.post(route('admin.classroom-photos.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Upload Class Photos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload Class Photos</h2>
                <Link :href="route('admin.classroom-photos.index')" class="text-gray-600 hover:text-gray-900">Back to Photos</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="classroom_id" value="Classroom" />
                            <select id="classroom_id" v-model="form.classroom_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="" disabled>Select a classroom</option>
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.classroom_id" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="title" value="Base title (optional)" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" />
                            <p class="mt-1 text-sm text-gray-500">Each photo is titled after its filename; this prefix is added if set.</p>
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="images" value="Photos" />
                            <input id="images" type="file" multiple accept="image/*" @change="onFiles"
                                class="mt-1 block w-full text-sm text-gray-600" />
                            <p v-if="form.images.length" class="mt-1 text-sm text-gray-500">{{ form.images.length }} file(s) selected</p>
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2"></progress>
                            <InputError class="mt-2" :message="form.errors.images" />
                            <InputError class="mt-2" :message="form.errors['images.0']" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing || form.images.length === 0">Upload</PrimaryButton>
                            <Link :href="route('admin.classroom-photos.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                            <span v-if="form.processing" class="text-sm text-gray-600">Uploading…</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
