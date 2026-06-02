<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    setting: Object,
});

const form = useForm({
    full_name: props.setting.full_name ?? '',
    address: props.setting.address ?? '',
    email: props.setting.email ?? '',
    iban: props.setting.iban ?? '',
    watermark_text: props.setting.watermark_text ?? '',
});

const flash = computed(() => usePage().props.flash ?? {});

const submit = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div v-if="flash.success" class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <p class="mb-6 text-sm text-gray-500">All fields are optional.</p>

                        <div class="mb-4">
                            <InputLabel for="full_name" value="Full Name" />
                            <TextInput
                                id="full_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.full_name"
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.full_name" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="address" value="Address" />
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="iban" value="IBAN" />
                            <TextInput
                                id="iban"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.iban"
                            />
                            <InputError class="mt-2" :message="form.errors.iban" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="watermark_text" value="Watermark Text" />
                            <TextInput
                                id="watermark_text"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.watermark_text"
                            />
                            <InputError class="mt-2" :message="form.errors.watermark_text" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Save Settings
                            </PrimaryButton>

                            <div v-if="form.processing" class="text-sm text-gray-600">
                                Saving...
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
