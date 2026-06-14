<script setup>
import WizardLayout from '@/Layouts/WizardLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

const ordersEnabled = computed(() => usePage().props.order?.ordersEnabled ?? true);

const props = defineProps({
    classroom: Object,
    package: Object,
    // [{ print_option_id, name, allowance, used }] — what the package covers
    // for this class plus how much has already been consumed.
    allowanceRows: Array,
    photos: Array,
    printOptions: Array,
    // { [photoId]: [{ item_id, print_option_id, print_option_name, quantity,
    //                 included_count, extra_count, unit_price }] }
    selections: Object,
    cartItemCount: Number,
    prevClassroom: Object,
    nextClassroom: Object,
    steps: Array,
});

// Per-photo working state for the "add" form: which print option + quantity.
const working = reactive({});
const adding = ref(null);
const defaultOption = props.printOptions[0]?.id ?? '';
props.photos.forEach((p) => {
    working[p.id] = { print_option_id: defaultOption, quantity: 1 };
});

const formatPrice = (p) => new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR' }).format(p ?? 0);

const pickedFor = (photoId) => (props.selections?.[photoId] ?? []);
const pickedTotal = (photoId) => pickedFor(photoId).reduce((sum, s) => sum + s.quantity, 0);

const allowanceFull = (row) => row.allowance > 0 && row.used >= row.allowance;

const addToCart = (photo) => {
    if (!ordersEnabled.value) return;
    const sel = working[photo.id];
    if (!sel.print_option_id) return;
    adding.value = photo.id;
    router.post(route('order.cart.add'), {
        classroom_photo_id: photo.id,
        print_option_id: sel.print_option_id,
        quantity: sel.quantity,
    }, {
        preserveScroll: true,
        onFinish: () => { adding.value = null; },
    });
};

const removeLine = (line) => {
    router.delete(route('order.cart.remove', line.item_id), { preserveScroll: true });
};

// Lightbox (same UX as before: prev/next + keyboard).
const lightboxIndex = ref(null);
const lightboxPhoto = computed(() =>
    lightboxIndex.value !== null ? props.photos[lightboxIndex.value] : null,
);
const openLightbox = (idx) => { lightboxIndex.value = idx; };
const closeLightbox = () => { lightboxIndex.value = null; };
const nextPhoto = () => {
    if (lightboxIndex.value === null) return;
    lightboxIndex.value = (lightboxIndex.value + 1) % props.photos.length;
};
const prevPhoto = () => {
    if (lightboxIndex.value === null) return;
    lightboxIndex.value = (lightboxIndex.value - 1 + props.photos.length) % props.photos.length;
};
const onKey = (e) => {
    if (lightboxIndex.value === null) return;
    if (e.key === 'Escape') closeLightbox();
    else if (e.key === 'ArrowRight') nextPhoto();
    else if (e.key === 'ArrowLeft') prevPhoto();
};
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

const continueUrl = computed(() =>
    props.nextClassroom
        ? route('order.photos', props.nextClassroom.slug)
        : route('order.summary'),
);
const continueLabel = computed(() =>
    props.nextClassroom ? `Pokračovať: ${props.nextClassroom.name}` : 'Pokračovať na sumár',
);
const backUrl = computed(() =>
    props.prevClassroom
        ? route('order.photos', props.prevClassroom.slug)
        : route('order.package'),
);
const backLabel = computed(() =>
    props.prevClassroom ? `Späť: ${props.prevClassroom.name}` : 'Späť na balík',
);
</script>

<template>
    <Head :title="`Fotografie: ${classroom.name}`" />

    <WizardLayout :steps="steps">
        <div class="space-y-6">
            <!-- Sticky top bar: classroom heading + package info + allowance
                 chips, all in one block that pins to the top of the viewport
                 once the parent scrolls past the wizard chrome. Bleeds to the
                 layout's content edges via negative margins to look like a
                 toolbar. -->
            <div class="sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white border-b shadow-sm">
                <div class="flex justify-between items-start gap-4 flex-wrap">
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-gray-800">{{ classroom.name }}</h1>
                        <p v-if="package" class="text-sm text-gray-600 mt-0.5">
                            <span class="font-medium text-gray-800">{{ package.name }}</span>
                            <span class="text-gray-500"> · {{ formatPrice(package.price) }}</span>
                        </p>
                        <p v-if="package?.description" class="text-xs text-gray-500 mt-0.5">{{ package.description }}</p>
                    </div>
                    <div class="text-xs text-gray-500 whitespace-nowrap">
                        Vo výbere: <span class="font-medium text-gray-800 tabular-nums">{{ cartItemCount }}</span>
                    </div>
                </div>

                <ul v-if="allowanceRows?.length" class="flex flex-wrap gap-2 mt-3 text-sm">
                    <li v-for="row in allowanceRows" :key="row.print_option_id"
                        :class="['flex items-center gap-2 px-3 py-1 rounded-full border tabular-nums',
                            allowanceFull(row)
                                ? 'border-green-300 bg-green-50 text-green-900'
                                : 'border-gray-200 bg-gray-50 text-gray-700']">
                        <span class="font-medium">{{ row.allowance }}× {{ row.name }}</span>
                        <span class="text-gray-500">·</span>
                        <span>{{ row.used }} / {{ row.allowance }}</span>
                    </li>
                </ul>
                <p v-if="allowanceRows?.length" class="text-xs text-gray-500 mt-2">
                    Pri prekročení balíka sa fotografie navyše účtujú podľa cenníka formátu.
                </p>
            </div>

            <div v-if="photos.length === 0" class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                V tejto triede zatiaľ nie sú žiadne fotografie.
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="(photo, idx) in photos" :key="photo.id"
                    class="bg-white shadow-sm rounded-lg overflow-hidden flex flex-col">
                    <button type="button" @click="openLightbox(idx)" @contextmenu.prevent
                        class="relative block w-full bg-white cursor-zoom-in group focus:outline-none focus:ring-2 focus:ring-blue-500 select-none">
                        <div class="aspect-[4/3] flex items-center justify-center overflow-hidden py-3">
                            <img :src="photo.thumbnail_url" :alt="photo.title" loading="lazy"
                                draggable="false" @dragstart.prevent
                                class="max-w-full max-h-full object-contain pointer-events-none select-none" />
                        </div>
                        <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30 text-white pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                    </button>

                    <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                        <!-- Per-line breakdown of what's already selected on this photo.
                             Each line shows the format/qty and labels whether those
                             prints come out of the package allowance or are extras. -->
                        <ul v-if="pickedFor(photo.id).length" class="space-y-1 text-xs">
                            <li v-for="line in pickedFor(photo.id)" :key="line.item_id"
                                class="flex flex-wrap items-baseline gap-x-2">
                                <span class="font-medium text-gray-800">{{ line.quantity }}× {{ line.print_option_name }}</span>
                                <span v-if="line.included_count" class="px-1.5 py-0.5 rounded bg-green-100 text-green-800">
                                    {{ line.included_count }} v balíku
                                </span>
                                <span v-if="line.extra_count" class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">
                                    {{ line.extra_count }} navyše
                                </span>
                                <button type="button" @click="removeLine(line)"
                                    :aria-label="`Odstrániť ${line.quantity}× ${line.print_option_name}`"
                                    class="ml-auto text-gray-400 hover:text-red-600 leading-none rounded p-0.5 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </li>
                        </ul>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Formát</label>
                            <select v-model="working[photo.id].print_option_id"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option v-for="opt in printOptions" :key="opt.id" :value="opt.id">
                                    {{ opt.name }} — {{ formatPrice(opt.price) }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="w-20">
                                <label class="block text-xs text-gray-500 mb-1">Počet</label>
                                <input type="number" min="1" max="99" v-model.number="working[photo.id].quantity"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <button @click="addToCart(photo)" :disabled="adding === photo.id || !ordersEnabled"
                                :class="['flex-1 text-white text-sm py-2 rounded-md',
                                    ordersEnabled
                                        ? 'bg-blue-600 hover:bg-blue-700 disabled:opacity-50'
                                        : 'bg-gray-400 cursor-not-allowed']">
                                {{ adding === photo.id ? 'Pridávam…' : 'Pridať' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky bottom nav: prev/next class (or back-to-package / to-summary). -->
            <div class="sticky bottom-0 z-20 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white border-t flex items-center justify-between gap-4"
                style="box-shadow: 0 -2px 4px rgba(0,0,0,0.04);">
                <Link :href="backUrl" class="text-sm text-gray-600 hover:text-gray-900">← {{ backLabel }}</Link>
                <Link :href="continueUrl"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    {{ continueLabel }} →
                </Link>
            </div>
        </div>

        <!-- Lightbox -->
        <div v-if="lightboxPhoto"
            class="fixed inset-0 bg-black/90 z-50 flex flex-col items-center justify-center p-4 select-none"
            @click.self="closeLightbox"
            @contextmenu.prevent>
            <button type="button" @click="closeLightbox" aria-label="Zavrieť"
                class="absolute top-4 right-4 text-white text-2xl leading-none bg-white/10 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center">×</button>
            <button v-if="photos.length > 1" type="button" @click="prevPhoto" aria-label="Predošlá"
                class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 text-white text-3xl leading-none bg-white/10 hover:bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">‹</button>
            <button v-if="photos.length > 1" type="button" @click="nextPhoto" aria-label="Ďalšia"
                class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 text-white text-3xl leading-none bg-white/10 hover:bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">›</button>
            <img :src="lightboxPhoto.medium_url" :alt="lightboxPhoto.title"
                draggable="false" @dragstart.prevent @contextmenu.prevent
                class="max-h-[85vh] max-w-full rounded shadow-2xl object-contain pointer-events-none select-none" />
            <p class="text-white text-sm mt-3">
                {{ lightboxPhoto.title }}
                <span class="text-white/60 ml-2">{{ lightboxIndex + 1 }} / {{ photos.length }}</span>
            </p>
        </div>
    </WizardLayout>
</template>
