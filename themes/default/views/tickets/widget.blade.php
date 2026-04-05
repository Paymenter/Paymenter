<div class="space-y-4 animate-in fade-in slide-in-from-bottom-2 duration-500">
    @foreach ($tickets as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="group block">
        <div class="bg-white hover:bg-gray-50 border border-gray-200 group-hover:border-primary/50 p-5 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
            
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/5 p-2.5 rounded-xl border border-primary/10 group-hover:bg-primary group-hover:text-white transition-colors">
                        <x-ri-ticket-line class="size-5 text-primary group-hover:text-white" />
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 leading-none mb-1">
                            <span class="text-gray-400 font-medium">#{{ $ticket->id }}</span> 
                            {{ $ticket->subject }}
                        </h3>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-[10px] font-black uppercase tracking-widest transition-all
                    @if ($ticket->status == 'open') border-blue-100 bg-blue-50 text-blue-600
                    @elseif($ticket->status == 'closed') border-gray-200 bg-gray-50 text-gray-400
                    @else border-green-100 bg-green-50 text-green-600 @endif">
                    
                    @if ($ticket->status == 'open')
                        <x-ri-add-circle-fill class="size-3.5 animate-pulse" />
                    @elseif($ticket->status == 'closed')
                        <x-ri-forbid-fill class="size-3.5" />
                    @elseif($ticket->status == 'replied')
                        <x-ri-chat-smile-2-fill class="size-3.5" />
                    @endif
                    
                    <span>{{ $ticket->status }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                <div class="flex items-center gap-2 text-gray-400">
                    <x-ri-time-line class="size-3.5" />
                    <p class="text-[11px] font-semibold uppercase tracking-tighter">
                        {{ __('ticket.last_activity') }}: 
                        <span class="text-gray-600 font-bold lowercase">
                            {{ $ticket->messages()->latest()->first()?->created_at->diffForHumans() }}
                        </span>
                        
                        @if($ticket->department)
                            <span class="mx-2 text-gray-200">|</span>
                            <span class="text-primary/70">{{ $ticket->department }}</span>
                        @endif
                    </p>
                </div>

                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-1">
                        View Thread <x-ri-arrow-right-s-line class="size-4" />
                    </span>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>