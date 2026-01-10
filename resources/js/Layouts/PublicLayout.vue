<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import logoBlack from '../../images/logo-black.png';

const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <!-- Navigation -->
        <nav class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <!-- Logo / Brand -->
                    <div class="flex-shrink-0">
                        <Link :href="route('homepage')" class="hover:opacity-75 transition-opacity">
                            <img :src="logoBlack" alt="Cvakmi" class="h-8 md:h-5 w-auto" />
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex space-x-8">
                        <Link
                            :href="route('gallery.index')"
                            class="text-sm font-light tracking-wider hover:text-gray-600 transition-colors uppercase"
                            :class="{ 'text-gray-900': $page.url.startsWith('/gallery'), 'text-gray-500': !$page.url.startsWith('/gallery') }"
                        >
                            Portfólio
                        </Link>
                        <Link
                            :href="route('about')"
                            class="text-sm font-light tracking-wider hover:text-gray-600 transition-colors uppercase"
                            :class="{ 'text-gray-900': route().current('about'), 'text-gray-500': !route().current('about') }"
                        >
                            O mne
                        </Link>
<!--                        <Link-->
<!--                            :href="route('reviews')"-->
<!--                            class="text-sm font-light tracking-wider hover:text-gray-600 transition-colors uppercase"-->
<!--                            :class="{ 'text-gray-900': route().current('reviews'), 'text-gray-500': !route().current('reviews') }"-->
<!--                        >-->
<!--                            Recenzie-->
<!--                        </Link>-->
<!--                        <Link-->
<!--                            :href="route('pricing')"-->
<!--                            class="text-sm font-light tracking-wider hover:text-gray-600 transition-colors uppercase"-->
<!--                            :class="{ 'text-gray-900': route().current('pricing'), 'text-gray-500': !route().current('pricing') }"-->
<!--                        >-->
<!--                            Cenník-->
<!--                        </Link>-->
<!--                        <Link-->
<!--                            :href="route('contact')"-->
<!--                            class="text-sm font-light tracking-wider hover:text-gray-600 transition-colors uppercase"-->
<!--                            :class="{ 'text-gray-900': route().current('contact'), 'text-gray-500': !route().current('contact') }"-->
<!--                        >-->
<!--                            Kontakt-->
<!--                        </Link>-->
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button
                            type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-gray-600 hover:text-black focus:outline-none"
                            aria-label="Toggle menu"
                        >
                            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-if="mobileMenuOpen" class="md:hidden mt-4 pt-4 border-t border-gray-200">
                <div class="space-y-3">
                    <Link
                        :href="route('gallery.index')"
                        class="block text-sm font-light tracking-wider hover:text-gray-900 transition-colors uppercase"
                        :class="{ 'text-gray-900': $page.url.startsWith('/gallery'), 'text-gray-500': !$page.url.startsWith('/gallery') }"
                        @click="mobileMenuOpen = false"
                    >
                        Portfólio
                    </Link>
                    <Link
                        :href="route('about')"
                        class="block text-sm font-light tracking-wider hover:text-gray-900 transition-colors uppercase"
                        :class="{ 'text-gray-900': route().current('about'), 'text-gray-500': !route().current('about') }"
                        @click="mobileMenuOpen = false"
                    >
                        O mne
                    </Link>
<!--                    <Link-->
<!--                        :href="route('reviews')"-->
<!--                        class="block text-sm font-light tracking-wider hover:text-gray-900 transition-colors uppercase"-->
<!--                        :class="{ 'text-gray-900': route().current('reviews'), 'text-gray-500': !route().current('reviews') }"-->
<!--                        @click="mobileMenuOpen = false"-->
<!--                    >-->
<!--                        Recenzie-->
<!--                    </Link>-->
<!--                    <Link-->
<!--                        :href="route('pricing')"-->
<!--                        class="block text-sm font-light tracking-wider hover:text-gray-900 transition-colors uppercase"-->
<!--                        :class="{ 'text-gray-900': route().current('pricing'), 'text-gray-500': !route().current('pricing') }"-->
<!--                        @click="mobileMenuOpen = false"-->
<!--                    >-->
<!--                        Cenník-->
<!--                    </Link>-->
<!--                    <Link-->
<!--                        :href="route('contact')"-->
<!--                        class="block text-sm font-light tracking-wider hover:text-gray-900 transition-colors uppercase"-->
<!--                        :class="{ 'text-gray-900': route().current('contact'), 'text-gray-500': !route().current('contact') }"-->
<!--                        @click="mobileMenuOpen = false"-->
<!--                    >-->
<!--                        Kontakt-->
<!--                    </Link>-->
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-32 mb-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="border-t border-gray-200 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-sm text-gray-500 font-light">
                            &copy; {{ new Date().getFullYear() }} CVAKMI.SK • Všetky práva vyhradené
                        </p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-gray-900 transition-colors" aria-label="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-900 transition-colors" aria-label="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
