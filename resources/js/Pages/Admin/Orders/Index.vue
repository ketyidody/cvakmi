<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    orders: Object,
    statuses: Array,
    filters: Object,
});

const status = ref(props.filters?.status ?? '');

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleString('sk-SK') : '';

const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const filterByStatus = () => {
    router.get(route('admin.orders.index'), status.value ? { status: status.value } : {},
        { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Orders</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by status</label>
                        <select v-model="status" @change="filterByStatus"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>

                    <div v-if="orders.data.length === 0" class="text-center py-8 text-gray-500">No orders found.</div>

                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2">Order</th>
                                <th class="py-2">Customer</th>
                                <th class="py-2">Classrooms</th>
                                <th class="py-2">Package</th>
                                <th class="py-2">Items</th>
                                <th class="py-2">Total</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id" class="border-b hover:bg-gray-50">
                                <td class="py-2 font-medium">{{ order.order_number }}</td>
                                <td class="py-2">{{ order.user?.name }}<br><span class="text-gray-400">{{ order.user?.email }}</span></td>
                                <td class="py-2">{{ order.classroom_names?.length ? order.classroom_names.join(', ') : '—' }}</td>
                                <td class="py-2">{{ order.package_names?.length ? order.package_names.join(' + ') : '—' }}</td>
                                <td class="py-2">{{ order.items_count }}</td>
                                <td class="py-2">{{ formatPrice(order.total_estimate) }}</td>
                                <td class="py-2">
                                    <span :class="['px-2 py-1 text-xs rounded', statusClasses[order.status]]">{{ order.status }}</span>
                                </td>
                                <td class="py-2 text-gray-500">{{ formatDate(order.submitted_at) }}</td>
                                <td class="py-2 text-right">
                                    <Link :href="route('admin.orders.show', order.id)" class="text-blue-700 hover:underline">View</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="orders.links" class="mt-6 flex flex-wrap gap-1">
                        <template v-for="link in orders.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                :class="['px-3 py-1 text-sm rounded', link.active ? 'bg-blue-600 text-white' : 'text-gray-600']" />
                            <span v-else v-html="link.label" class="px-3 py-1 text-sm rounded text-gray-300" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
