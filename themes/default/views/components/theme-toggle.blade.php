<button
    x-data="{ 
        theme: localStorage.getItem('theme') || 'system',
        init() {
            // Apply initial theme
            this.applyTheme(this.theme);
            
            // Watch for theme changes
            this.$watch('theme', (value) => {
                localStorage.setItem('theme', value);
                this.applyTheme(value);
            });
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (this.theme === 'system') {
                    this.applyTheme('system');
                }
            });
        },
        applyTheme(theme) {
            if (theme === 'system') {
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } else if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        nextTheme() {
            const themes = ['light', 'dark', 'system'];
            const currentIndex = themes.indexOf(this.theme);
            return themes[(currentIndex + 1) % themes.length];
        }
    }"
    x-init="init()"
    @click="theme = nextTheme()"
    type="button"
    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 text-gray-700 dark:text-gray-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500"
    :title="theme === 'system' ? 'System theme' : (theme === 'dark' ? 'Dark mode' : 'Light mode')"
>
    <!-- Light Mode Icon -->
    <template x-if="theme === 'light'">
        <x-ri-sun-fill class="size-4 text-amber-500" />
    </template>

    <!-- Dark Mode Icon -->
    <template x-if="theme === 'dark'">
        <x-ri-moon-fill class="size-4 text-indigo-400" />
    </template>

    <!-- System Mode Icon -->
    <template x-if="theme === 'system'">
        <x-ri-computer-line class="size-4 text-gray-600 dark:text-gray-400" />
    </template>
</button>