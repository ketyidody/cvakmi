<script setup>
import WizardLayout from '@/Layouts/WizardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    classrooms: Array,
    // { [classroomId]: packageId | null }
    currentSelections: Object,
    packages: Array,
    steps: Array,
});

const ordersEnabled = computed(() => usePage().props.order?.ordersEnabled ?? true);

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);

// Local working state: { [classroomId]: packageId | null }
// `null` (or unset) means "skip this class".
const choices = reactive({});
props.classrooms.forEach((c) => {
    choices[c.id] = props.currentSelections?.[c.id] ?? null;
});

const setChoice = (classroomId, packageId) => {
    if (!ordersEnabled.value) return;
    choices[classroomId] = packageId;
};

const anySelected = computed(() => Object.values(choices).some((v) => v));

const form = useForm({
    selections: [],
});

const submit = () => {
    form.selections = props.classrooms.map((c) => ({
        classroom_id: c.id,
        package_id: choices[c.id] ?? null,
    }));
    form.post(route('order.package.save'));
};
</script>

<template>
    <Head title="Vyberte balík" />

    <WizardLayout :steps="steps">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Vyberte balík</h1>
                <p class="text-sm text-gray-600 mt-1">
                    <template v-if="classrooms.length > 1">
                        Máte priradených viac tried. Pre každú triedu, z ktorej chcete objednať fotografie, vyberte balík.
                        Triedy bez vybraného balíka budú preskočené.
                    </template>
                    <template v-else>
                        Vyberte balík. Každý balík zahŕňa určený počet fotografií v daných formátoch; čokoľvek navyše je za príplatok.
                    </template>
                </p>
            </div>

            <div v-if="packages.length === 0" class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                Momentálne nie sú k dispozícii žiadne balíky.
            </div>

            <form v-else @submit.prevent="submit" class="space-y-8">
                <section v-for="classroom in classrooms" :key="classroom.id" class="space-y-3">
                    <h2 v-if="classrooms.length > 1" class="text-lg font-semibold text-gray-800">
                        {{ classroom.name }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <button v-for="pkg in packages" :key="pkg.id" type="button"
                            @click="setChoice(classroom.id, pkg.id)"
                            :disabled="!ordersEnabled"
                            :class="['text-left bg-white border rounded-lg p-5 transition focus:outline-none',
                                ordersEnabled ? 'hover:shadow cursor-pointer' : 'cursor-not-allowed opacity-60',
                                choices[classroom.id] === pkg.id ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200']">
                            <div class="flex justify-between items-baseline">
                                <span class="font-semibold">{{ pkg.name }}</span>
                                <span class="font-medium">{{ formatPrice(pkg.price) }}</span>
                            </div>
                            <p v-if="pkg.description" class="text-sm text-gray-600 mt-1">{{ pkg.description }}</p>
                            <ul v-if="pkg.allowances.length" class="mt-3 text-sm text-gray-700 space-y-0.5">
                                <li v-for="a in pkg.allowances" :key="a.print_option_id">
                                    {{ a.included_quantity }}× {{ a.print_option_name }}
                                </li>
                            </ul>
                        </button>

                        <!-- Skip option: only meaningful when the parent has multiple classes. -->
                        <button v-if="classrooms.length > 1" type="button"
                            @click="setChoice(classroom.id, null)"
                            :disabled="!ordersEnabled"
                            :class="['text-left bg-white border rounded-lg p-5 transition focus:outline-none',
                                ordersEnabled ? 'hover:shadow cursor-pointer' : 'cursor-not-allowed opacity-60',
                                choices[classroom.id] === null ? 'border-gray-500 ring-2 ring-gray-300' : 'border-dashed border-gray-300']">
                            <span class="font-semibold text-gray-700">Tentokrát preskočiť</span>
                            <p class="text-sm text-gray-500 mt-1">
                                Z tejto triedy teraz nič neobjednávame.
                            </p>
                        </button>
                    </div>
                </section>

                <InputError :message="form.errors.selections" />

                <div class="flex justify-end">
                    <PrimaryButton :disabled="form.processing || (ordersEnabled && !anySelected)">
                        Pokračovať
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </WizardLayout>
</template>
