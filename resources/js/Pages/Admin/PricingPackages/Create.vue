<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    pricingTypes: Array,
    services: Array,
});

const form = useForm({
    pricing_type_id: '',
    name: '',
    description: '',
    price: '',
    duration: '',
    display_order: 0,
    is_featured: false,
    is_active: true,
    services: [],
});

const addService = () => {
    form.services.push({
        id: '',
        quantity: 1
    });
};

const removeService = (index) => {
    form.services.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.pricing-packages.store'));
};
</script>

<template>
    <Head title="Create Pricing Package" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Pricing Package</h2>
                <Link :href="route('admin.pricing-packages.index')" class="text-gray-600 hover:text-gray-900">
                    Back to Pricing Packages
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <InputLabel for="pricing_type_id" value="Pricing Type" />
                            <select
                                id="pricing_type_id"
                                v-model="form.pricing_type_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select a pricing type</option>
                                <option v-for="type in pricingTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.pricing_type_id" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <InputLabel for="price" value="Price (EUR)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="duration" value="Duration" />
                                <TextInput
                                    id="duration"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.duration"
                                    placeholder="e.g., 2 hours, Full day"
                                />
                                <InputError class="mt-2" :message="form.errors.duration" />
                            </div>
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

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <InputLabel value="Services" />
                                <button type="button" @click="addService" class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded">
                                    Add Service
                                </button>
                            </div>

                            <div v-if="form.services.length === 0" class="text-sm text-gray-500 italic">
                                No services added yet
                            </div>

                            <div v-else class="space-y-2">
                                <div v-for="(service, index) in form.services" :key="index" class="flex gap-2">
                                    <select
                                        v-model="service.id"
                                        class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Select a service</option>
                                        <option v-for="s in services" :key="s.id" :value="s.id">
                                            {{ s.name }}
                                        </option>
                                    </select>

                                    <input
                                        type="number"
                                        v-model="service.quantity"
                                        min="1"
                                        class="w-20 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Qty"
                                        required
                                    />

                                    <button type="button" @click="removeService(index)" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.services" />
                        </div>

                        <div class="mb-4 flex gap-4">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Featured</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Create Pricing Package
                            </PrimaryButton>

                            <Link :href="route('admin.pricing-packages.index')" class="text-sm text-gray-600 hover:text-gray-900">
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
