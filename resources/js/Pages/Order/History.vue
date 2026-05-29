<script setup>
import WizardLayout from '@/Layouts/WizardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    orders: Array,
});

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleString('sk-SK') : '';

const statusLabels = {
    pending: 'Prijatá',
    processing: 'Spracováva sa',
    completed: 'Dokončená',
    cancelled: 'Zrušená',
};
</script>

<template>
    <Head title="Moje objednávky" />

    <WizardLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Moje objednávky</h1>
                <Link :href="route('order.start')" class="text-sm text-blue-700 hover:underline">Nová objednávka</Link>
            </div>

            <div v-if="orders.length === 0" class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                Zatiaľ nemáte žiadne objednávky.
            </div>

            <div v-else class="bg-white shadow-sm rounded-lg divide-y">
                <Link v-for="order in orders" :key="order.id" :href="route('order.show', order.id)"
                    class="flex items-center justify-between p-4 hover:bg-gray-50">
                    <div>
                        <p class="font-medium">{{ order.order_number }}</p>
                        <p class="text-sm text-gray-500">
                            {{ formatDate(order.submitted_at) }} ·
                            {{ order.package_names?.length ? order.package_names.join(' + ') : '—' }} ·
                            {{ order.items_count }} položiek
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">{{ formatPrice(order.total_estimate) }}</p>
                        <p class="text-sm text-gray-500">{{ statusLabels[order.status] ?? order.status }}</p>
                    </div>
                </Link>
            </div>
        </div>
    </WizardLayout>
</template>
