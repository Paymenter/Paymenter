<div class="bg-primary-800 rounded-lg mt-2">
    <div class="grid grid-cols-3 gap-4 p-6 pb-0">
        <button class="bg-primary-700 p-4 rounded-lg shadow-xl border-primary-600 border"
            wire:click="$set('activeComponent', 'services')">
            <h4 class="text-lg font-semibold text-white">{{ __('dashboard.active_services') }}:</h4>
            <div class="mt-2 text-3xl">
                {{ Auth::user()->services()->where('status', 'active')->count() }}
            </div>
        </button>
        
        <button class="bg-primary-700 p-4 rounded-lg shadow-xl border-primary-600 border"
            wire:click="$set('activeComponent', 'invoices')">
            <h4 class="text-lg font-semibold text-white">{{ __('dashboard.unpaid_invoices') }}:</h4>
            <div
                class="mt-2 text-3xl @if(Auth::user()->invoices()->where('status', 'unpaid')->count() > 0) text-orange-500 @else text-green-500 @endif">
                {{ Auth::user()->invoices()->where('status', 'unpaid')->count() }}
            </div>
        </button>

        <button class="bg-primary-700 p-4 rounded-lg shadow-xl border-primary-600 border"
            wire:click="$set('activeComponent', 'tickets')">
            <h4 class="text-lg font-semibold text-white">{{ __('dashboard.open_tickets') }}:</h4>
            <div class="mt-2 text-3xl">
                {{ Auth::user()->tickets()->where('status', '!=', 'closed')->count() }}
            </div>
        </button>
    </div>

    <div x-data="{ activeComponent: @entangle('activeComponent') }">

        <div x-show="activeComponent === 'tickets'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent == 'tickets')
                <livewire:tickets.index />
            @endif
        </div>

        <!-- Services Component -->
        <div x-show="activeComponent === 'services'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent == 'services')
                <livewire:services.index />
            @endif
        </div>

        <!-- Invoices Component -->
        <div x-show="activeComponent === 'invoices'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent == 'invoices')
                <livewire:invoices.index />
            @endif
        </div>
    </div>
</div>