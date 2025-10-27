import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                path.resolve(__dirname, 'js/app.js'),
                path.resolve(__dirname, 'js/easymde-entry.js'),
                path.resolve(__dirname, 'css/app.css'),
                'resources/css/filament/admin/theme.css',
            ],
            buildDirectory: 'default/',
            refresh: true
        }),
        tailwindcss(),
    ],
})
