<div class="container mx-auto px-4 sm:px-6 mt-20 md:mt-24 space-y-8 animate-in fade-in duration-700">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-200 dark:border-gray-800 pb-6">
        <div class="space-y-2">
            <x-navigation.breadcrumb />
            <div class="flex items-center gap-2">
                <div class="w-8 h-px bg-gradient-to-r from-orange-500 to-transparent"></div>
                <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.3em]">Support Overview</p>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                My Support Tickets
            </h1>
        </div>
        
        <x-navigation.link :href="route('tickets.create')" 
            class="group flex items-center gap-2 bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-2.5 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/30 text-white hover:scale-105 active:scale-95">
            <x-ri-add-line class="size-5 group-hover:rotate-90 transition-transform duration-300" />
            <span class="text-xs font-black uppercase tracking-wider">{{ __('ticket.create_ticket') }}</span>
        </x-navigation.link>
    </div>

    {{-- Stats Summary --}}
    @if($tickets->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Total</span>
                <x-ri-ticket-line class="size-4 text-blue-500" />
            </div>
            <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-2">{{ $tickets->total() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-950/30 dark:to-orange-900/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400">Open</span>
                <x-ri-chat-smile-2-line class="size-4 text-orange-500" />
            </div>
            <p class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-2">{{ $tickets->where('status', 'open')->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-950/30 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-green-600 dark:text-green-400">Replied</span>
                <x-ri-chat-check-line class="size-4 text-green-500" />
            </div>
            <p class="text-2xl font-black text-green-600 dark:text-green-400 mt-2">{{ $tickets->where('status', 'replied')->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-600 dark:text-gray-400">Closed</span>
                <x-ri-forbid-2-line class="size-4 text-gray-500" />
            </div>
            <p class="text-2xl font-black text-gray-600 dark:text-gray-400 mt-2">{{ $tickets->where('status', 'closed')->count() }}</p>
        </div>
    </div>
    @endif

    {{-- Tickets List --}}
    <div class="space-y-4">
        @forelse ($tickets as $ticket)
        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="group block outline-none">
            <div class="relative overflow-hidden bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl transition-all duration-300 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-gray-900/50 hover:-translate-y-0.5">
                
                {{-- Status Bar --}}
                <div class="absolute left-0 top-0 h-full w-1.5 transition-all duration-300 group-hover:w-2 
                    @if ($ticket->status == 'open') bg-gradient-to-b from-primary-500 to-primary-600
                    @elseif($ticket->status == 'closed') bg-gradient-to-b from-gray-400 to-gray-500
                    @elseif($ticket->status == 'replied') bg-gradient-to-b from-green-500 to-emerald-500
                    @else bg-gradient-to-b from-yellow-500 to-orange-500 @endif">
                </div>

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700 group-hover:bg-primary-50 dark:group-hover:bg-primary-950/30 group-hover:border-primary-200 dark:group-hover:border-primary-800 transition-all">
                            <x-ri-ticket-line class="size-5 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <span class="text-gray-400 dark:text-gray-500 font-medium mr-1">#{{ $ticket->id }}</span> 
                                {{ $ticket->subject }}
                            </h3>
                            @if($ticket->department)
                                <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $ticket->department }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-wider transition-all self-start sm:self-center
                        @if ($ticket->status == 'open') border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-950/50 text-primary-700 dark:text-primary-400
                        @elseif($ticket->status == 'closed') border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400
                        @elseif($ticket->status == 'replied') border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/50 text-green-700 dark:text-green-400
                        @else border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-950/50 text-yellow-700 dark:text-yellow-400 @endif">
                        
                        @if ($ticket->status == 'open')
                            <div class="size-1.5 rounded-full bg-primary-500 animate-pulse"></div>
                        @elseif($ticket->status == 'closed')
                            <x-ri-forbid-fill class="size-3" />
                        @elseif($ticket->status == 'replied')
                            <x-ri-chat-smile-2-fill class="size-3" />
                        @else
                            <x-ri-chat-history-fill class="size-3" />
                        @endif
                        
                        <span>{{ ucfirst($ticket->status) }}</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <div class="flex flex-wrap items-center gap-3 text-gray-400 dark:text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <x-ri-time-line class="size-3.5" />
                            <p class="text-[11px] font-medium">
                                {{ __('ticket.last_activity') }}: 
                                <span class="text-gray-700 dark:text-gray-300 font-semibold">
                                    {{ $ticket->messages()->latest()->first()?->created_at->diffForHumans() ?? $ticket->created_at->diffForHumans() }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>
                        
                        <div class="flex items-center gap-1.5">
                            <x-ri-mail-line class="size-3.5" />
                            <p class="text-[11px] font-medium">
                                Messages: 
                                <span class="text-gray-700 dark:text-gray-300 font-semibold">{{ $ticket->messages()->count() }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                        <span class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-wider">View Discussion</span>
                        <x-ri-arrow-right-s-line class="size-4 text-primary-600 dark:text-primary-400 group-hover:translate-x-1 transition-transform" />
                    </div>
                </div>
                
                {{-- Priority Indicator (if available) --}}
                @if($ticket->priority && $ticket->priority != 'normal')
                <div class="absolute top-5 right-5">
                    <div class="flex items-center gap-1 text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full
                        @if($ticket->priority == 'high') bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400
                        @elseif($ticket->priority == 'urgent') bg-orange-100 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400
                        @elseif($ticket->priority == 'low') bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 @endif">
                        <x-ri-flag-line class="size-2.5" />
                        {{ ucfirst($ticket->priority) }}
                    </div>
                </div>
                @endif
            </div>
        </a>
        @empty
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-24 bg-gray-50/50 dark:bg-gray-900/30 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-full shadow-sm mb-4">
                <x-ri-inbox-line class="size-12 text-gray-300 dark:text-gray-600" />
            </div>
            <p class="text-sm font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ __('ticket.no_tickets') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">When you open a support request, it will appear here.</p>
            <div class="mt-6">
                <x-navigation.link :href="route('tickets.create')" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-2.5 rounded-xl text-white text-xs font-black uppercase tracking-wider">
                    <x-ri-add-line class="size-4" />
                    Create Your First Ticket
                </x-navigation.link>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-800">
        {{ $tickets->links() }}
    </div>
    @endif
    
    
</div>