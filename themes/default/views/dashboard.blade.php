<div>
    <!-- Dashboard content -->
    <x-navigation.breadcrumb />
    <span class="text-md text-base/80 text-nowrap font-semibold mt-4">{{ __('dashboard.welcome_back', ['name' =>
        auth()->user()->name]) }} 👋</span>
    <p class="text-base text-base/80 text-nowrap font mt-4">{{ __('dashboard.dashboard_description') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-0 mt-8">
        <!-- Active Services Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <x-ri-archive-stack-fill class="size-5" />
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.active_services') }}</h2>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md size-5 text-sm text-white">{{
                    Auth::user()->services()->where('status', 'active')->count() }}</span>
            </div>

            <div class="space-y-4">
                <livewire:services.widget status="active" />
            </div>

            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('services')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="size-5" />
            </x-navigation.link>
        </div>
        <!-- Unpaid Invoices Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <x-ri-receipt-fill class="size-5" />
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.unpaid_invoices') }}</h2>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md size-5 text-sm text-white">{{
                    Auth::user()->invoices()->where('status', 'pending')->count() }}</span>
            </div>
            <div class="space-y-4">
                <livewire:invoices.widget :limit="3" />
            </div>
            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('invoices')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="size-5" />
            </x-navigation.link>
        </div>
        <!-- Open Tickets Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <x-ri-customer-service-fill class="size-5" />
                    </div>
                    <h2 class="text-xl font-semibold">{{ __('dashboard.open_tickets') }}</h2>
                    <a href="{{ route('tickets.create') }}" wire:navigate>
                        <x-ri-add-fill class="size-5" />
                    </a>
                </div>
                <span
                    class="bg-primary flex items-center justify-center font-semibold rounded-md size-5 text-sm text-white">{{
                    Auth::user()->tickets()->where('status', '!=', 'closed')->count() }}</span>
            </div>

            <div class="space-y-4">
                <livewire:tickets.widget />
            </div>

            <x-navigation.link
                class="bg-background-secondary hover:bg-background-secondary/80 flex items-center justify-center rounded-lg"
                :href="route('services')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-fill class="size-5" />
            </x-navigation.link>
        </div>
        {{--
        <!-- Announcements Widget -->
        <div class="">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                        <x-ri-megaphone-fill class="size-5" />
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
                <x-ri-arrow-right-fill class="size-5" />
            </x-navigation.link>
        </div> --}}
    </div>
</div>