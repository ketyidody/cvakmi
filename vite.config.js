import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        // Listen on all interfaces so the dev server is reachable from outside
        // the Docker container; harmless when running natively.
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        // Reduce memory usage during build
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', '@inertiajs/vue3'],
                },
            },
        },
        // Reduce minification complexity
        minify: 'esbuild',
    },
});
