<div>
    <!-- Dashboard content -->
    <div class="text-lg font-bold pb-4">{{ __('dashboard.dashboard_title') }}</div>
    <span class="text-md text-base/80 text-nowrap font-semibold mt-4">{{ __('dashboard.welcome_back', ['name' =>
        auth()->user()->name]) }} 👋</span>
    <p class="text-base text-base/80 text-nowrap font mt-4">{{ __('dashboard.dashboard_description') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-0 mt-8">
        <!-- Active Services Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <svg class="text-base w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M4 3H20C20.5523 3 21 3.44772 21 4V11H3V4C3 3.44772 3.44772 3 4 3ZM3 13H21V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V13ZM7 16V18H10V16H7ZM7 6V8H10V6H7Z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.active_services') }}</h2>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md w-5 h-5 text-sm text-white">{{
                    Auth::user()->services()->where('status', 'active')->count() }}</span>
            </div>

            <div class="space-y-4">
                <livewire:services.widget status="active" />
            </div>

            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('services')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="w-5 h-5" />
            </x-navigation.link>
        </div>
        <!-- Unpaid Invoices Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <svg class="text-base w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M9 4L6 2L3 4V16V19C3 20.6569 4.34315 22 6 22H20C21.6569 22 23 20.6569 23 19V17H7V19C7 19.5523 6.55228 20 6 20C5.44772 20 5 19.5523 5 19V15H21V4L18 2L15 4L12 2L9 4Z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.unpaid_invoices') }}</h2>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md w-5 h-5 text-sm text-white">{{
                    Auth::user()->invoices()->where('status', 'pending')->count() }}</span>
            </div>
            <div class="space-y-4">
                <livewire:invoices.widget :limit="3" />
            </div>
            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('invoices')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="w-5 h-5" />
            </x-navigation.link>
        </div>
        <!-- Open Tickets Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M21 8C22.1046 8 23 8.89543 23 10V14C23 15.1046 22.1046 16 21 16H19.9381C19.446 19.9463 16.0796 23 12 23V21C15.3137 21 18 18.3137 18 15V9C18 5.68629 15.3137 3 12 3C8.68629 3 6 5.68629 6 9V16H3C1.89543 16 1 15.1046 1 14V10C1 8.89543 1.89543 8 3 8H4.06189C4.55399 4.05369 7.92038 1 12 1C16.0796 1 19.446 4.05369 19.9381 8H21ZM7.75944 15.7849L8.81958 14.0887C9.74161 14.6662 10.8318 15 12 15C13.1682 15 14.2584 14.6662 15.1804 14.0887L16.2406 15.7849C15.0112 16.5549 13.5576 17 12 17C10.4424 17 8.98882 16.5549 7.75944 15.7849Z">
                            </path>
                        </svg>

                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.open_tickets') }}</h2>
                    <a href="{{ route('tickets.create') }}" wire:navigate>
                        <x-ri-add-fill class="w-5 h-5" />
                    </a>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md w-5 h-5 text-sm text-white">{{
                    Auth::user()->tickets()->where('status', '!=', 'closed')->count() }}</span>
            </div>

            <div class="space-y-4">
                <livewire:tickets.widget />
            </div>

            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('services')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="w-5 h-5" />
            </x-navigation.link>
        </div>
        {{--
        <!-- Announcements Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <svg class="text-base w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M21 10.063V4C21 3.44772 20.5523 3 20 3H19C17.0214 4.97864 13.3027 6.08728 11 6.61281V17.3872C13.3027 17.9127 17.0214 19.0214 19 21H20C20.5523 21 21 20.5523 21 20V13.937C21.8626 13.715 22.5 12.9319 22.5 12 22.5 11.0681 21.8626 10.285 21 10.063ZM5 7C3.89543 7 3 7.89543 3 9V15C3 16.1046 3.89543 17 5 17H6L7 22H9V7H5Z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('Announcements') }}</h2>
                </div>
            </div>

            <div class="space-y-4">
                <livewire:announcements.widget />
            </div>

            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('announcements.index')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="w-5 h-5" />
            </x-navigation.link>
        </div> --}}
    </div>
</div>