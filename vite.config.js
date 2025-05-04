import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
    base: 'https://pickup-movie.onrender.com/build/',
});
