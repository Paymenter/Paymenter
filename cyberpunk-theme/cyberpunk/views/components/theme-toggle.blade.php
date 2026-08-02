<button
    @click="theme = theme === 'light' ? 'dark' : (theme === 'dark' ? 'system' : 'light')"
    type="button"
    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-neutral transition text-base cursor-pointer"
    :title="theme === 'system' ? 'System' : (theme === 'dark' ? 'Dark' : 'Light')"
>
    <template x-if="theme === 'light'">
        <x-ri-sun-fill class="size-4" />
    </template>

    <template x-if="theme === 'dark'">
        <x-ri-moon-fill class="size-4" />
    </template>

    <template x-if="theme === 'system'">
        <x-ri-computer-line class="size-4" />
    </template>
</button>
