<script setup>
import WizardLayout from '@/Layouts/WizardLayout.vue';
import PaymentDetails from '@/Components/PaymentDetails.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: Object,
    payment: Object,
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
    <Head :title="`Objednávka ${order.order_number}`" />

    <WizardLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Objednávka {{ order.order_number }}</h1>
                <Link :href="route('order.history')" class="text-sm text-gray-600 hover:text-gray-900">Späť na objednávky</Link>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-5 space-y-2">
                <p class="text-sm text-gray-500">{{ formatDate(order.submitted_at) }}</p>
                <p>Stav: <span class="font-medium">{{ statusLabels[order.status] ?? order.status }}</span></p>
                <p v-if="order.note" class="mt-3 text-gray-700 whitespace-pre-line">{{ order.note }}</p>
            </div>

            <div v-for="group in order.groups" :key="group.classroom_name" class="bg-white shadow-sm rounded-lg p-5">
                <div class="border-b pb-2 mb-3">
                    <h3 class="font-semibold">{{ group.classroom_name }}</h3>
                    <p class="text-sm text-gray-500">
                        Balík: <span class="font-medium text-gray-800">{{ group.package_name ?? '—' }}</span>
                        ({{ formatPrice(group.package_price) }})
                    </p>
                </div>

                <div class="space-y-3">
                    <div v-for="item in group.items" :key="item.id"
                        class="flex items-center gap-4 border-b last:border-0 pb-3 last:pb-0">
                        <img v-if="item.thumbnail_url" :src="item.thumbnail_url"
                            class="w-16 h-16 object-cover rounded bg-gray-100" />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ item.photo_title }}</p>
                            <p class="text-sm text-gray-500">
                                {{ item.print_option_name }} · {{ item.quantity }}× ·
                                <span v-if="item.included_count">{{ item.included_count }} v balíku</span>
                                <span v-if="item.included_count && item.extra_count"> · </span>
                                <span v-if="item.extra_count" class="text-amber-700">{{ item.extra_count }} navyše ({{ formatPrice(item.unit_price) }}/ks)</span>
                            </p>
                        </div>
                        <div class="font-medium text-right">
                            <span v-if="item.extra_count">{{ formatPrice(item.line_total) }}</span>
                            <span v-else class="text-gray-400 text-sm">V balíku</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-5 text-sm space-y-1">
                <div class="flex justify-between"><span>Balíky</span><span>{{ formatPrice(order.packages_total) }}</span></div>
                <div class="flex justify-between text-lg font-semibold border-t pt-2 mt-2">
                    <span>Spolu</span>
                    <span>{{ formatPrice(order.total_estimate) }}</span>
                </div>
            </div>

            <PaymentDetails :payment="payment" />
        </div>
    </WizardLayout>
</template>
