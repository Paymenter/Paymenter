import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import tailwindcssVite from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        tailwindcssVite(),
        laravel({
            input: [
                path.resolve(__dirname, 'js/app.js'),
                path.resolve(__dirname, 'css/easymde.css')
            ],
            buildDirectory: 'default/',
            refresh: true
        }),
    ],
})
