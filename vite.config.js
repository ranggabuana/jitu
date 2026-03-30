import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command, mode }) => {
    // load env
    const env = loadEnv(mode, process.cwd());

    const isLocal = env.VITE_APP_ENV === 'local';
    const isProduction = env.VITE_APP_ENV === 'production';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: isLocal, // hanya aktif di local
            }),
            tailwindcss(),
        ],

        // khusus LOCAL (development)
        ...(isLocal && {
            server: {
                watch: {
                    ignored: ['**/storage/framework/views/**'],
                },
            },
        }),

        // khusus PRODUCTION
        ...(isProduction && {
            build: {
                outDir: 'public/build',
                emptyOutDir: true,
                manifest: true, // penting untuk Laravel
            },
        }),
    };
});