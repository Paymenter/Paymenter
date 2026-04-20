<div class="container mx-auto px-6 py-8 max-w-7xl">
        <x-navigation.breadcrumb />

        {{-- Welcome header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family: 'Space Grotesk', sans-serif;">
                    Welcome back, <span class="gradient-text">{{ Auth::user()->name }}</span>
                </h1>
                <p class="text-sm text-muted mt-1">{{ __('dashboard.dashboard_description') }}</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full
                        bg-success/10 border border-success/20 text-success text-xs font-medium">
                <span class="h-1.5 w-1.5 rounded-full bg-success animate-pulse"></span>
                All systems operational
            </div>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl bg-background-secondary border border-neutral/50 p-5 hover:border-primary/30 transition-colors">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-2">Active Services</div>
                <div class="text-2xl font-bold gradient-text">
                    {{ Auth::user()->services()->where('status', 'active')->count() }}
                </div>
            </div>
            <div class="rounded-2xl bg-background-secondary border border-neutral/50 p-5 hover:border-primary/30 transition-colors">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-2">Open Tickets</div>
                <div class="text-2xl font-bold gradient-text">
                    {{ Auth::user()->tickets()->where('status', '!=', 'closed')->count() }}
                </div>
            </div>
            <div class="rounded-2xl bg-background-secondary border border-neutral/50 p-5 hover:border-primary/30 transition-colors">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-2">Unpaid Invoices</div>
                <div class="text-2xl font-bold gradient-text">
                    {{ Auth::user()->invoices()->where('status', 'pending')->count() }}
                </div>
            </div>
            <div class="rounded-2xl bg-background-secondary border border-neutral/50 p-5 hover:border-primary/30 transition-colors">
                <div class="text-xs text-muted font-medium uppercase tracking-wider mb-2">Account Status</div>
                <div class="text-sm font-semibold text-success flex items-center gap-1.5 mt-1">
                    <span class="h-2 w-2 rounded-full bg-success"></span> Active
                </div>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Active Services --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center">
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold">{{ __('dashboard.active_services') }}</h2>
                    </div>
                    <a href="{{ route('services') }}" wire:navigate
                       class="text-xs text-primary hover:underline underline-offset-2">
                        View all &rarr;
                    </a>
                </div>
                <div class="space-y-3">
                    <livewire:services.widget status="active" />
                </div>
            </div>

            {{-- Unpaid Invoices --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-warning/10 border border-warning/20 flex items-center justify-center">
                            <svg class="h-4 w-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold">{{ __('dashboard.unpaid_invoices') }}</h2>
                    </div>
                    <a href="{{ route('invoices') }}" wire:navigate
                       class="text-xs text-primary hover:underline underline-offset-2">
                        View all &rarr;
                    </a>
                </div>
                <div class="space-y-3">
                    <livewire:invoices.widget :limit="3" />
                </div>
            </div>

            {{-- Open Tickets --}}
            @if(!config('settings.tickets_disabled', false))
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-secondary/10 border border-secondary/20 flex items-center justify-center">
                            <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold">{{ __('dashboard.open_tickets') }}</h2>
                    </div>
                    <a href="{{ route('tickets.create') }}" wire:navigate
                       class="text-xs text-primary hover:underline underline-offset-2">
                        + New ticket
                    </a>
                </div>
                <div class="space-y-3">
                    <livewire:tickets.widget />
                </div>
            </div>
            @endif

            {!! hook('pages.dashboard') !!}
        </div>
</div>
