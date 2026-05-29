<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    printOptions: Array,
});

const form = useForm({
    name: '',
    description: '',
    price: '',
    display_order: 0,
    is_active: true,
    allowances: [],
});

const addAllowance = () => {
    form.allowances.push({
        print_option_id: props.printOptions[0]?.id ?? '',
        included_quantity: 1,
    });
};

const removeAllowance = (idx) => form.allowances.splice(idx, 1);

const submit = () => form.post(route('admin.packages.store'));
</script>

<template>
    <Head title="Create Package" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Package</h2>
                <Link :href="route('admin.packages.index')" class="text-gray-600 hover:text-gray-900">Back</Link>
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
                            <InputLabel value="Included photos" />
                            <p class="text-xs text-gray-500 mb-2">
                                How many photos of each format the package covers. Parents pay extra for anything beyond these.
                            </p>

                            <div class="space-y-2">
                                <div v-for="(row, idx) in form.allowances" :key="idx" class="flex items-center gap-2">
                                    <select v-model="row.print_option_id" required
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option v-for="opt in printOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                    </select>
                                    <input type="number" min="1" max="999" v-model.number="row.included_quantity"
                                        class="w-24 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <button type="button" @click="removeAllowance(idx)" class="text-sm text-red-700 hover:underline">Remove</button>
                                </div>
                            </div>

                            <button type="button" @click="addAllowance"
                                class="mt-3 text-sm text-blue-700 hover:underline" :disabled="printOptions.length === 0">
                                + Add format
                            </button>
                            <InputError class="mt-2" :message="form.errors.allowances" />
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
                            <PrimaryButton :disabled="form.processing">Create Package</PrimaryButton>
                            <Link :href="route('admin.packages.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
