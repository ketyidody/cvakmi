<script setup>
import WizardLayout from '@/Layouts/WizardLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    groups: Array,
    emptySelectedClassrooms: Array,
    totals: Object,
    hasItems: Boolean,
    steps: Array,
});

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);

const updateQuantity = (item) => {
    router.patch(route('order.cart.update', item.id), { quantity: item.quantity }, { preserveScroll: true });
};
const removeItem = (item) => router.delete(route('order.cart.remove', item.id), { preserveScroll: true });

const form = useForm({ note: '' });
const submit = () => form.post(route('order.submit'));
</script>

<template>
    <Head title="Sumár objednávky" />

    <WizardLayout :steps="steps">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Sumár objednávky</h1>
                <p class="text-sm text-gray-600 mt-1">Skontrolujte výber a odošlite objednávku.</p>
            </div>

            <div v-if="!hasItems" class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                Váš výber je prázdny. Vráťte sa späť a pridajte fotografie.
            </div>

            <template v-else>
                <!-- Optional notice: classrooms with a package but no items. -->
                <div v-if="emptySelectedClassrooms?.length" class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    <p class="font-medium mb-1">Niektoré triedy nemajú vybrané fotografie:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li v-for="(c, idx) in emptySelectedClassrooms" :key="idx">
                            {{ c.classroom_name }} — {{ c.package_name ?? 'bez balíka' }}
                            <Link v-if="c.classroom_slug" :href="route('order.photos', c.classroom_slug)" class="ml-2 underline">vybrať fotografie</Link>
                        </li>
                    </ul>
                    <p class="text-xs mt-2">
                        Triedy bez fotografií sa nezarátajú do ceny. Ak nechcete objednať balík pre niektorú z nich, vráťte sa späť cez krok „Balíky“.
                    </p>
                </div>

                <!-- One block per classroom: package on top, then items. -->
                <div v-for="group in groups" :key="group.classroom_id" class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex justify-between items-baseline mb-3 border-b pb-2">
                        <div>
                            <h3 class="font-semibold">{{ group.classroom_name }}</h3>
                            <p class="text-sm text-gray-500">
                                Balík: <span class="font-medium text-gray-800">{{ group.package_name ?? '—' }}</span>
                                ({{ formatPrice(group.package_price) }})
                            </p>
                        </div>
                        <Link :href="route('order.package')" class="text-xs text-blue-700 hover:underline">Zmeniť balík</Link>
                    </div>

                    <div class="space-y-3">
                        <div v-for="item in group.items" :key="item.id"
                            class="flex items-center gap-4 border-b last:border-0 pb-3 last:pb-0">
                            <img v-if="item.thumbnail_url" :src="item.thumbnail_url"
                                class="w-16 h-16 object-cover rounded bg-gray-100" />
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ item.photo_title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ item.print_option_name }} ·
                                    <span v-if="item.included_count">{{ item.included_count }} v balíku</span>
                                    <span v-if="item.included_count && item.extra_count"> · </span>
                                    <span v-if="item.extra_count" class="text-amber-700">{{ item.extra_count }} navyše ({{ formatPrice(item.unit_price) }}/ks)</span>
                                </p>
                            </div>
                            <input type="number" min="1" max="99" v-model.number="item.quantity"
                                @change="updateQuantity(item)"
                                class="w-20 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div class="text-right w-24">
                                <p class="font-medium">
                                    <span v-if="item.extra_count">{{ formatPrice(item.line_total) }}</span>
                                    <span v-else class="text-gray-400 text-sm">V balíku</span>
                                </p>
                                <button @click="removeItem(item)" class="text-xs text-red-700 hover:underline">Odstrániť</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white shadow-sm rounded-lg p-5 text-sm space-y-1">
                    <div class="flex justify-between"><span>Balíky</span><span>{{ formatPrice(totals.packages) }}</span></div>
                    <div class="flex justify-between"><span>Fotografie navyše</span><span>{{ formatPrice(totals.extras) }}</span></div>
                    <div class="flex justify-between text-lg font-semibold border-t pt-2 mt-2">
                        <span>Spolu</span>
                        <span>{{ formatPrice(totals.total) }}</span>
                    </div>
                </div>

                <!-- Note + submit -->
                <form @submit.prevent="submit" class="bg-white shadow-sm rounded-lg p-5 space-y-3">
                    <label for="note" class="block text-sm font-medium text-gray-700">Poznámka (nepovinné)</label>
                    <textarea id="note" v-model="form.note" rows="3"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <InputError :message="form.errors.note" />
                    <InputError :message="form.errors.cart" />
                    <InputError :message="form.errors.selections" />

                    <p class="text-sm text-gray-500">
                        Odoslaním objednávky súhlasíte s výrobou vami zvolených fotografií v požadovaných rozmeroch. Fotografie budú vyhotovené bez vodoznaku. V nasledujúcom kroku môžete uskutočniť platbu pomocou QR kódu, alebo bankovým prevodom na účet (je potrebné uviesť číslo objednávky) alebo priniesť hotovosť do škôlky.
                    </p>

                    <div class="flex justify-end">
                        <PrimaryButton :disabled="form.processing">Odoslať objednávku</PrimaryButton>
                    </div>
                </form>
            </template>
        </div>
    </WizardLayout>
</template>
