<div class="container mx-auto px-4 sm:px-6 mt-20 md:mt-24 mb-16 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    {{-- Header Section --}}
    <div class="mb-8">
        <x-navigation.breadcrumb />
        <div class="flex items-center gap-2 mt-4">
            <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
            <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">My Services</p>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mt-2">
            Active Modules
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Manage and monitor all your active services and subscriptions</p>
    </div>

    {{-- Stats Summary --}}
    @if($services->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-950/30 dark:to-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Active</span>
                <x-ri-checkbox-circle-line class="size-4 text-emerald-500" />
            </div>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $services->where('status', 'active')->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-950/30 dark:to-yellow-900/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-yellow-600 dark:text-yellow-400">Pending</span>
                <x-ri-time-line class="size-4 text-yellow-500" />
            </div>
            <p class="text-2xl font-black text-yellow-600 dark:text-yellow-400 mt-2">{{ $services->where('status', 'pending')->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-950/30 dark:to-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-red-600 dark:text-red-400">Suspended</span>
                <x-ri-forbid-2-line class="size-4 text-red-500" />
            </div>
            <p class="text-2xl font-black text-red-600 dark:text-red-400 mt-2">{{ $services->where('status', 'suspended')->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-600 dark:text-gray-400">Total</span>
                <x-ri-apps-line class="size-4 text-gray-500" />
            </div>
            <p class="text-2xl font-black text-gray-600 dark:text-gray-400 mt-2">{{ $services->total() }}</p>
        </div>
    </div>
    @endif

    {{-- Services Grid --}}
    <div class="grid gap-4">
        @forelse ($services as $service)
            <a href="{{ route('services.show', $service) }}" wire:navigate class="group block outline-none">
                <div class="relative overflow-hidden bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-5 rounded-2xl transition-all duration-300 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-gray-900/50 hover:-translate-y-0.5">
                    
                    {{-- Background Glow Effect --}}
                    <div class="absolute -right-4 -top-4 size-24 rounded-full blur-3xl opacity-10 transition-colors duration-300 group-hover:opacity-20
                        @if ($service->status == 'active') bg-emerald-500 
                        @elseif($service->status == 'suspended' || $service->status == 'cancelled') bg-red-500 
                        @else bg-amber-500 @endif">
                    </div>

                    {{-- Status Bar --}}
                    <div class="absolute left-0 top-0 h-full w-1 transition-all duration-300 group-hover:w-1.5
                        @if ($service->status == 'active') bg-gradient-to-b from-emerald-500 to-emerald-600
                        @elseif($service->status == 'suspended' || $service->status == 'cancelled') bg-gradient-to-b from-red-500 to-red-600
                        @else bg-gradient-to-b from-amber-500 to-amber-600 @endif">
                    </div>

                    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {{-- Left Side - Icon & Info --}}
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center size-12 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-primary-500 shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:bg-primary-50 dark:group-hover:bg-primary-950/30 group-hover:border-primary-200 dark:group-hover:border-primary-800">
                                <x-ri-instance-line class="size-6" />
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $service->product->name }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ $service->product->category->name }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-600">•</span>
                                    <span class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider">
                                        {{ in_array($service->plan->type, ['recurring']) ? __('services.every_period', [
                                            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                                            'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                                        ]) : 'One-time' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Right Side - Expiry & Status --}}
                        <div class="flex items-center gap-6">
                            @if ($service->expires_at)
                                <div class="hidden md:flex flex-col items-end">
                                    <span class="text-[8px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">{{ __('services.expires_at') }}</span>
                                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">
                                        {{ $service->expires_at->format('M d, Y') }}
                                        @if($service->expires_at->isPast())
                                            <span class="text-red-500 ml-1">(Expired)</span>
                                        @elseif($service->expires_at->diffInDays(now()) <= 7)
                                            <span class="text-amber-500 ml-1">(Expiring soon)</span>
                                        @endif
                                    </span>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="flex items-center gap-2 px-3 py-2 rounded-full border text-[10px] font-black uppercase tracking-wider transition-all
                                @if ($service->status == 'active') border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400
                                @elseif($service->status == 'suspended' || $service->status == 'cancelled') border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400
                                @else border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 @endif">
                                
                                <span class="relative flex size-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-current"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-current"></span>
                                </span>
                                
                                <span>{{ strtoupper($service->status) }}</span>
                            </div>
                            
                            {{-- Arrow Indicator --}}
                            <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                                <x-ri-arrow-right-s-line class="size-5 text-primary-500" />
                            </div>
                        </div>
                    </div>
                    
                    {{-- Progress Bar for Expiring Services --}}
                    @if($service->expires_at && !$service->expires_at->isPast() && $service->status == 'active')
                        @php
                            $totalDays = $service->created_at->diffInDays($service->expires_at);
                            $daysLeft = now()->diffInDays($service->expires_at);
                            $percentage = ($daysLeft / $totalDays) * 100;
                        @endphp
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                <span>Usage</span>
                                <span>{{ $daysLeft }} days remaining</span>
                            </div>
                            <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 
                                    @if($percentage > 60) bg-emerald-500
                                    @elseif($percentage > 30) bg-amber-500
                                    @else bg-red-500 @endif"
                                    style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            {{-- Empty State --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 border-2 border-dashed border-gray-200 dark:border-gray-800 p-12 rounded-3xl flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    <x-ri-ghost-line class="size-10 text-gray-400 dark:text-gray-600" />
                </div>
                <p class="text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ __('services.no_services') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">You don't have any active services yet</p>
                <a href="{{ route('category.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all hover:scale-105">
                    Browse Marketplace
                    <x-ri-arrow-right-line class="size-3" />
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($services->hasPages())
    <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-800">
        {{ $services->links() }}
    </div>
    @endif
    
    {{-- Quick Actions --}}
    @if($services->count() > 0)
    <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                    <x-ri-customer-service-line class="size-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Need assistance?</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Our support team is available 24/7 to help you</p>
                </div>
            </div>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-gray-800 text-white text-xs font-black uppercase tracking-wider rounded-xl hover:bg-primary-600 transition-all">
                <x-ri-headphone-line class="size-4" />
                Contact Support
            </a>
        </div>
    </div>
    @endif
    
</div>