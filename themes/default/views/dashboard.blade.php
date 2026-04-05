<div class="relative min-h-screen bg-[#fdfaf5] dark:bg-[#0a0a0a] text-[#2d241e] dark:text-[#e8e2db] antialiased selection:bg-primary-500/30 overflow-x-hidden font-sans">
    
    {{-- High-Tech Mesh Background --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute inset-0 bg-[radial-gradient(at_0%_0%,rgba(59,130,246,0.03)_0px,transparent_50%)] dark:bg-[radial-gradient(at_0%_0%,rgba(59,130,246,0.05)_0px,transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(at_100%_100%,rgba(16,185,129,0.03)_0px,transparent_50%)] dark:bg-[radial-gradient(at_100%_100%,rgba(16,185,129,0.05)_0px,transparent_50%)]"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 relative z-10 pt-12 pb-24 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        {{-- Header Section --}}
        <div class="mb-12">
            <x-navigation.breadcrumb />
            
            <div class="mt-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-px bg-gradient-to-r from-orange-500 to-transparent"></div>
                        <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.3em]">Command Center</p>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-400 bg-clip-text text-transparent">
                        {{ __('dashboard.welcome_back', ['name' => Auth::user()->first_name]) }}<span class="text-orange-500">.</span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 font-medium mt-4 max-w-2xl leading-relaxed">
                        {{ __('Manage your active services, financial records, and support requests.') }}
                    </p>
                </div>
                
                <div class="flex items-center gap-3 px-5 py-3 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-70">Node: Optimal</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Left Column --}}
            <div class="lg:col-span-7 flex flex-col gap-6">
                
                {{-- Active Services Section --}}
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Main Modules</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded-md">{{ Auth::user()->services()->where('status', 'active')->count() }} Active</span>
                </div>

                {{-- We adapt the service widget look here --}}
                <div class="space-y-4">
                    <livewire:services.widget status="active" />
                </div>

                <x-navigation.link 
                    class="group w-full py-5 bg-white dark:bg-gray-900/50 hover:bg-gray-900 dark:hover:bg-orange-500 hover:text-white dark:hover:text-black flex items-center justify-center gap-3 rounded-2xl transition-all font-black text-xs uppercase tracking-widest border border-gray-200 dark:border-gray-800 shadow-sm"
                    :href="route('services')">
                    {{ __('dashboard.view_all_instances') }}
                    <x-ri-arrow-right-line class="size-4 group-hover:translate-x-1 transition-transform" />
                </x-navigation.link>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-5 flex flex-col gap-6">
                
                {{-- Billing Summary --}}
                <div class="relative overflow-hidden bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-6 rounded-3xl transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/5">
                    {{-- Status Bar --}}
                    <div class="absolute left-0 top-0 h-full w-1 bg-red-500"></div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-red-50 dark:bg-red-950/30 rounded-xl text-red-500 border border-red-100 dark:border-red-900/50">
                                <x-ri-receipt-fill class="size-6" />
                            </div>
                            <div>
                                <h2 class="text-lg font-black tracking-tighter">{{ __('dashboard.unpaid_invoices') }}</h2>
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Action Required</p>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-red-500">
                             {{ Auth::user()->invoices()->where('status', 'pending')->count() }}
                        </div>
                    </div>

                    <livewire:invoices.widget :limit="3" />

                    <x-navigation.link 
                        class="group mt-6 w-full py-4 bg-red-500 text-white flex items-center justify-center gap-3 rounded-2xl transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-red-500/20"
                        :href="route('invoices')">
                        {{ __('dashboard.settle_invoices') }}
                        <x-ri-arrow-right-line class="size-4 group-hover:translate-x-1 transition-transform" />
                    </x-navigation.link>
                </div>

                {{-- Support Section --}}
                @if(!config('settings.tickets_disabled', false))
                <div class="relative overflow-hidden bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-6 rounded-3xl">
                     {{-- Status Bar --}}
                     <div class="absolute left-0 top-0 h-full w-1 bg-teal-500"></div>

                     <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <x-ri-customer-service-2-fill class="size-5 text-teal-500" />
                            <h3 class="text-sm font-black uppercase tracking-widest">{{ __('dashboard.open_tickets') }}</h3>
                        </div>
                        <a href="{{ route('tickets.create') }}" class="text-teal-500 hover:scale-110 transition-transform">
                            <x-ri-add-circle-fill class="size-5" />
                        </a>
                     </div>
                     
                     <livewire:tickets.widget :tickets="$tickets ?? null" />
                </div>
                @endif

                {{-- Community Hook --}}
                <div class="p-6 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-transparent">
                    <div class="flex items-center gap-3 mb-4 opacity-50">
                        <x-ri-notification-3-line class="size-4" />
                        <span class="text-[9px] font-black uppercase tracking-widest">Community Addons</span>
                    </div>
                    {!! hook('pages.dashboard') !!}
                </div>
            </div>
        </div>
    </div>
</div>