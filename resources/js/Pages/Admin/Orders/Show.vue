<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    statuses: Array,
});

const form = useForm({ status: props.order.status });

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleString('sk-SK') : '';

const thumb = (item) => item.classroom_photo_id
    ? route('order.photo', { classroomPhoto: item.classroom_photo_id, size: 'thumbnail' })
    : null;

const updateStatus = () => form.patch(route('admin.orders.update', props.order.id), { preserveScroll: true });
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ order.order_number }}</h2>
                <Link :href="route('admin.orders.index')" class="text-gray-600 hover:text-gray-900">Back to Orders</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 grid sm:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold mb-2">Customer</h3>
                        <p>{{ order.user?.name }}</p>
                        <p class="text-gray-500">{{ order.user?.email }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            Classes: {{ order.user?.classrooms?.map(c => c.name).join(', ') || '—' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-2">Submitted: {{ formatDate(order.submitted_at) }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2">Status</h3>
                        <form @submit.prevent="updateStatus" class="flex items-center gap-3">
                            <select v-model="form.status" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                            <PrimaryButton :disabled="form.processing">Update</PrimaryButton>
                        </form>
                        <p class="mt-4 text-lg font-semibold">Estimated total: {{ formatPrice(order.total_estimate) }}</p>
                    </div>
                </div>

                <div v-if="order.note" class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-2">Customer note</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ order.note }}</p>
                </div>

                <!-- One block per classroom with its package + items -->
                <div v-for="group in order.groups" :key="group.classroom_name" class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-baseline mb-3 border-b pb-2">
                        <h3 class="font-semibold">{{ group.classroom_name }}</h3>
                        <p class="text-sm text-gray-500">
                            Package: <span class="font-medium text-gray-800">{{ group.package_name ?? '—' }}</span>
                            ({{ formatPrice(group.package_price) }})
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div v-for="item in group.items" :key="item.id" class="flex items-center gap-4 border-b last:border-0 pb-3 last:pb-0">
                            <img v-if="thumb(item)" :src="thumb(item)" class="w-16 h-16 object-cover rounded bg-gray-100" />
                            <div v-else class="w-16 h-16 rounded bg-gray-100 flex items-center justify-center text-xs text-gray-400">deleted</div>
                            <div class="flex-1">
                                <p class="font-medium">{{ item.photo_title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ item.print_option_name }} · {{ item.quantity }}× ·
                                    <span v-if="item.included_count">{{ item.included_count }} included</span>
                                    <span v-if="item.included_count && item.extra_count"> · </span>
                                    <span v-if="item.extra_count" class="text-amber-700">{{ item.extra_count }} extra ({{ formatPrice(item.unit_price) }}/ea)</span>
                                </p>
                            </div>
                            <div class="font-medium text-right">
                                <span v-if="item.extra_count">{{ formatPrice(item.line_total) }}</span>
                                <span v-else class="text-gray-400 text-sm">In package</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
