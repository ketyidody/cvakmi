<script setup>
import { ref, watchEffect } from 'vue';
import { encode } from 'bysquare/pay';
import { Version } from 'bysquare';
import QRCode from 'qrcode';

const props = defineProps({
    payment: Object,
});

const qrDataUrl = ref(null);
const qrError = ref(null);

// IBAN is displayed in space-separated groups of four; the QR payload uses
// the raw, unspaced form.
const formatIban = (iban) => (iban ?? '').replace(/(.{4})/g, '$1 ').trim();

const formatPrice = (p) =>
    new Intl.NumberFormat('sk-SK', { style: 'currency', currency: props.payment?.currency ?? 'EUR' })
        .format(p ?? 0);

watchEffect(async () => {
    qrError.value = null;
    qrDataUrl.value = null;

    if (!props.payment?.iban) return;

    try {
        const beneficiaryName = (props.payment.beneficiary_name ?? '').trim();
        const payload = {
            payments: [{
                type: 1, // PaymentOrder
                bankAccounts: [{ iban: props.payment.iban }],
                amount: Number(props.payment.amount) || 0,
                currencyCode: props.payment.currency ?? 'EUR',
                variableSymbol: String(props.payment.variable_symbol ?? ''),
                ...(beneficiaryName ? { beneficiary: { name: beneficiaryName } } : {}),
            }],
        };
        // v1.2.0 requires a beneficiary name. Without one, drop to v1.1.0
        // where the beneficiary block is optional.
        const qrString = encode(payload, {
            version: beneficiaryName ? Version['1.2.0'] : Version['1.1.0'],
        });
        qrDataUrl.value = await QRCode.toDataURL(qrString, { margin: 1, width: 240 });
    } catch (e) {
        qrError.value = e?.message ?? 'Nepodarilo sa vygenerovať QR kód.';
    }
});
</script>

<template>
    <section v-if="payment" class="bg-white shadow-sm rounded-lg p-5">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Platobné údaje</h2>

        <div class="grid gap-5 md:grid-cols-[1fr_auto]">
            <dl class="text-sm space-y-2">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">IBAN</dt>
                    <dd class="font-mono text-gray-800 text-right break-all">{{ formatIban(payment.iban) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Variabilný symbol</dt>
                    <dd class="font-medium text-gray-800">{{ payment.variable_symbol }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Suma</dt>
                    <dd class="font-semibold text-gray-900">{{ formatPrice(payment.amount) }}</dd>
                </div>
                <div v-if="payment.beneficiary_name" class="flex justify-between gap-4">
                    <dt class="text-gray-500">Príjemca</dt>
                    <dd class="text-gray-800 text-right">{{ payment.beneficiary_name }}</dd>
                </div>
                <div v-if="payment.beneficiary_address" class="flex justify-between gap-4">
                    <dt class="text-gray-500">Adresa</dt>
                    <dd class="text-gray-800 text-right whitespace-pre-line">{{ payment.beneficiary_address }}</dd>
                </div>
                <div v-if="payment.beneficiary_email" class="flex justify-between gap-4">
                    <dt class="text-gray-500">E-mail</dt>
                    <dd class="text-gray-800 text-right break-all">{{ payment.beneficiary_email }}</dd>
                </div>
            </dl>

            <div class="flex flex-col items-center justify-start text-center">
                <img v-if="qrDataUrl" :src="qrDataUrl" alt="PAY by square QR kód"
                    class="w-44 h-44 sm:w-56 sm:h-56" />
                <div v-else-if="qrError" class="w-44 h-44 sm:w-56 sm:h-56 flex items-center justify-center text-xs text-red-600 border border-red-200 rounded p-2">
                    {{ qrError }}
                </div>
                <div v-else class="w-44 h-44 sm:w-56 sm:h-56 flex items-center justify-center text-xs text-gray-400 border border-gray-200 rounded">
                    Generujem QR…
                </div>
                <p class="text-xs text-gray-500 mt-2">Naskenujte v bankovej aplikácii</p>
            </div>
        </div>
    </section>
</template>
