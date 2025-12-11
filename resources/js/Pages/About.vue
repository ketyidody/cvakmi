<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="O mne" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-4xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider mb-4 text-gray-900 uppercase">O mne</h1>
                    <p class="text-gray-600 text-lg font-light uppercase">Fotograf</p>
                </div>

                <!-- Content -->
                <div class="prose prose-lg max-w-none">
                    <!-- Profile Image -->
                    <div class="mb-12 flex justify-center">
                        <div class="w-64 h-64 bg-gray-200 rounded-full overflow-hidden">
                            <!-- Placeholder for profile image -->
                            <svg class="w-full h-full text-gray-400 p-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="space-y-6 text-gray-700 font-light">
                        <p class="text-lg leading-relaxed">
                            Vitajte v mojom fotografickom svete. Som profesionálny fotograf so záľubou v zachytávaní
                            jedinečných okamihov a príbehov.
                        </p>

                        <p class="leading-relaxed">
                            Fotografia je pre mňa viac než len práca – je to spôsob, ako vidím a interpretujem svet okolo seba.
                            Každý snímok je jedinečný príbeh, ktorý sa snažím zachytiť s citom pre detail a atmosféru.
                        </p>

                        <p class="leading-relaxed">
                            Špecializujem sa na rôzne žánre fotografie a rád pracujem na projektoch, ktoré ma inšpirujú
                            a umožňujú mi tvoriť niečo výnimočné.
                        </p>
                    </div>

                    <!-- Skills/Specialization -->
                    <div class="mt-16">
                        <h2 class="text-2xl font-light tracking-wider mb-8 text-gray-900 uppercase">Špecializácia</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-l-2 border-gray-900 pl-4">
                                <h3 class="text-lg font-light text-gray-900 mb-2 uppercase">Portrétna fotografia</h3>
                                <p class="text-gray-600 font-light">Zachytávam osobnosť a emócie ľudí</p>
                            </div>
                            <div class="border-l-2 border-gray-900 pl-4">
                                <h3 class="text-lg font-light text-gray-900 mb-2 uppercase">Krajinná fotografia</h3>
                                <p class="text-gray-600 font-light">Krása prírody v jej najlepšom svetle</p>
                            </div>
                            <div class="border-l-2 border-gray-900 pl-4">
                                <h3 class="text-lg font-light text-gray-900 mb-2 uppercase">Eventová fotografia</h3>
                                <p class="text-gray-600 font-light">Zachytávam dôležité momenty a oslavy</p>
                            </div>
                            <div class="border-l-2 border-gray-900 pl-4">
                                <h3 class="text-lg font-light text-gray-900 mb-2 uppercase">Umelecká fotografia</h3>
                                <p class="text-gray-600 font-light">Kreatívne a koncepčné projekty</p>
                            </div>
                        </div>
                    </div>

                    <!-- Experience/Education -->
                    <div class="mt-16">
                        <h2 class="text-2xl font-light tracking-wider mb-8 text-gray-900 uppercase">Vzdelanie & Skúsenosti</h2>
                        <div class="space-y-6">
                            <div>
                                <p class="text-gray-900 font-light">2020 - Súčasnosť</p>
                                <p class="text-gray-600 font-light">Profesionálny fotograf na voľnej nohe</p>
                            </div>
                            <div>
                                <p class="text-gray-900 font-light">2018 - 2020</p>
                                <p class="text-gray-600 font-light">Asistent fotografa v štúdiu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.prose p {
    margin-bottom: 1rem;
}
</style>
