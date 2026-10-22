import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vite';

/**
 * Builds the standalone bundle used by ResumePdfRenderer: one IIFE script and
 * one stylesheet with the document fonts inlined, so headless Chrome can
 * render the CV from a single local HTML file without network access.
 */
export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    define: {
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
    publicDir: false,
    build: {
        outDir: 'bootstrap/cv-print',
        emptyOutDir: true,
        assetsInlineLimit: () => true,
        lib: {
            entry: 'resources/js/cv/print.ts',
            name: 'CvPrint',
            formats: ['iife'],
            fileName: () => 'cv-print.js',
            cssFileName: 'cv-print',
        },
    },
});
