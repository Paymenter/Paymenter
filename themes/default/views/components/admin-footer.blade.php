<div class="ring-1 ring-gray-950/5 dark:ring-white/10">
</div>

<span class="text-xs text-gray-400 mt-auto">
    Powered by <a href="https://paymenter.org" target="_blank" class="text-gray-400">Paymenter &copy;
        {{ date('Y') }}</a>
</span>

<ul class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-7">
    <li class="fi-sidebar-group flex flex-col gap-y-1">
        <ul class="fi-sidebar-group-items flex flex-col gap-y-1">
            <li class="fi-sidebar-item">
                <a href="https://github.com/sponsors/Paymenter" target="_blank" class="fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5">
                    <x-ri-service-fill class="fi-sidebar-item-icon h-6 w-6 sponsor" />
                    <span class="fi-sidebar-item-label flex-1 truncate text-sm font-medium text-gray-700 dark:text-gray-200">
                        Sponsor
                    </span>
                </a>
            </li>
            <li class="fi-sidebar-item">
                <a href="https://github.com/Paymenter/Paymenter" target="_blank" class="fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5">
                    <x-ri-star-fill class="fi-sidebar-item-icon h-6 w-6 star-git" />
                    <span class="fi-sidebar-item-label flex-1 truncate text-sm font-medium text-gray-700 dark:text-gray-200">
                        Star us on GitHub
                    </span>
                </a>
            </li>
            <li class="fi-sidebar-item">
                <a href="https://paymenter.org/docs/getting-started/introduction" target="_blank" class="fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5">
                    <x-ri-book-2-line class="fi-sidebar-item-icon h-6 w-6 text-gray-400 dark:text-gray-500" />
                    <span class="fi-sidebar-item-label flex-1 truncate text-sm font-medium text-gray-700 dark:text-gray-200">
                        Documentation
                    </span>
                </a>
            </li>
        </ul>
    </li>
</ul>

<style>
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
    }

    .btn svg {
        width: 20px;
        height: 20px;
        margin-right: 8px;
    }

    .sponsor {
        fill: #DB61A2;
    }

    .star-git {
        fill: #E3B341;
    }
</style>
