<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
    project_type: ''
});

const projectTypes = [
    'Portrétne fotenie',
    'Svadba',
    'Event',
    'Firemné fotenie',
    'Produktová fotografia',
    'Iné'
];

const submit = () => {
    // In production, this would submit to a backend endpoint
    // form.post('/contact', {
    //     onSuccess: () => form.reset(),
    // });
    console.log('Form submitted:', form.data());
    alert('Ďakujem za správu! Čoskoro sa vám ozvem.');
    form.reset();
};
</script>

<template>
    <Head title="Kontakt" />

    <PublicLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-6xl mx-auto">
                <!-- Page Title -->
                <div class="text-center mb-20">
                    <h1 class="text-4xl md:text-6xl font-light tracking-wider mb-4 text-gray-900 uppercase">Kontakt</h1>
                    <p class="text-gray-600 text-lg font-light uppercase">Ozvite sa mi</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-2xl font-light tracking-wider mb-8 text-gray-900 uppercase">
                            Kontaktné informácie
                        </h2>

                        <div class="space-y-6">
                            <!-- Email -->
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-gray-900 mr-4 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-gray-900 font-light uppercase mb-1">Email</p>
                                    <a href="mailto:info@cvakmi.sk" class="text-gray-600 hover:text-gray-900 transition-colors font-light">
                                        info@cvakmi.sk
                                    </a>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-gray-900 mr-4 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <p class="text-gray-900 font-light uppercase mb-1">Telefón</p>
                                    <a href="tel:+421900000000" class="text-gray-600 hover:text-gray-900 transition-colors font-light">
                                        +421 900 000 000
                                    </a>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-gray-900 mr-4 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                <div>
                                    <p class="text-gray-900 font-light uppercase mb-2">Sociálne siete</p>
                                    <div class="flex space-x-4">
                                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="mt-12 pt-12 border-t border-gray-200">
                            <h3 class="text-xl font-light tracking-wider mb-6 text-gray-900 uppercase">Pracovné hodiny</h3>
                            <div class="space-y-2 text-gray-600 font-light">
                                <div class="flex justify-between">
                                    <span>Pondelok - Piatok:</span>
                                    <span>9:00 - 18:00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Sobota:</span>
                                    <span>10:00 - 16:00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Nedeľa:</span>
                                    <span>Zatvorené</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div>
                        <h2 class="text-2xl font-light tracking-wider mb-8 text-gray-900 uppercase">
                            Kontaktný formulár
                        </h2>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-light text-gray-900 mb-2 uppercase">
                                    Meno *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    id="name"
                                    required
                                    class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-900 transition-colors font-light"
                                    placeholder="Vaše meno"
                                />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-light text-gray-900 mb-2 uppercase">
                                    Email *
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    id="email"
                                    required
                                    class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-900 transition-colors font-light"
                                    placeholder="vas@email.com"
                                />
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-light text-gray-900 mb-2 uppercase">
                                    Telefón
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    id="phone"
                                    class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-900 transition-colors font-light"
                                    placeholder="+421 900 000 000"
                                />
                            </div>

                            <!-- Project Type -->
                            <div>
                                <label for="project_type" class="block text-sm font-light text-gray-900 mb-2 uppercase">
                                    Typ projektu
                                </label>
                                <select
                                    v-model="form.project_type"
                                    id="project_type"
                                    class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-900 transition-colors font-light"
                                >
                                    <option value="">Vyberte typ projektu</option>
                                    <option v-for="type in projectTypes" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-sm font-light text-gray-900 mb-2 uppercase">
                                    Správa *
                                </label>
                                <textarea
                                    v-model="form.message"
                                    id="message"
                                    required
                                    rows="6"
                                    class="w-full border border-gray-300 px-4 py-3 text-gray-900 focus:outline-none focus:border-gray-900 transition-colors font-light resize-none"
                                    placeholder="Popíšte svoj projekt alebo požiadavky..."
                                ></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full border border-gray-900 bg-gray-900 text-white px-8 py-4 text-sm font-light tracking-wider hover:bg-white hover:text-gray-900 transition-all duration-300 uppercase"
                            >
                                Odoslať správu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
