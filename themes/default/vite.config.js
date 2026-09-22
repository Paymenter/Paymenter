import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                path.resolve(import.meta.dirname, 'js/app.js'),
                path.resolve(import.meta.dirname, 'js/easymde-entry.js'),
                path.resolve(import.meta.dirname, 'css/app.css'),
                'resources/css/filament/admin/theme.css',
            ],
            buildDirectory: 'default/',
            refresh: true
        }),
        tailwindcss(),
    ],
})
