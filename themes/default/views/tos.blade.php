<x-app-layout title="Terms of Service">
        <div class="content ">
            <div class="content-box">
            <h1 class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">
                Terms of Service
            </h1>
            <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400 italic">
                Last Updated: {{ config('settings::tos_last_updated') }}
            </p>
            <div class="prose dark:prose-invert min-w-full">
                @markdownify(config('settings::tos_text'))
            </div>
        </div>
    </div>
</x-app-layout>
