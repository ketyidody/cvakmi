<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    price: '',
    display_order: 0,
    is_active: true,
});

const submit = () => form.post(route('admin.print-options.store'));
</script>

<template>
    <Head title="Create Print Option" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Print Option</h2>
                <Link :href="route('admin.print-options.index')" class="text-gray-600 hover:text-gray-900">Back</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="name" value="Name" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <textarea id="description" v-model="form.description" rows="2"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="price" value="Price (EUR)" />
                            <TextInput id="price" type="number" step="0.01" min="0" class="mt-1 block w-full" v-model="form.price" required />
                            <InputError class="mt-2" :message="form.errors.price" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="display_order" value="Display Order" />
                            <TextInput id="display_order" type="number" class="mt-1 block w-full" v-model="form.display_order" />
                            <InputError class="mt-2" :message="form.errors.display_order" />
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">Create</PrimaryButton>
                            <Link :href="route('admin.print-options.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
