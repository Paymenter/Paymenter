<div class="space-y-4">
    @foreach ($tickets as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-secondary/10 p-2 rounded-lg">
                <x-ri-ticket-line class="w-5 h-5 text-secondary" />
            </div>
            <span class="font-medium">{{ $ticket->subject }}</span>
            </div>
            <div class="w-5 h-5 rounded-md p-0.5
                @if ($ticket->status == 'open') text-success bg-success/20 
                @elseif($ticket->status == 'closed') text-inactive bg-inactive/20
                @else text-info bg-info/20 
                @endif"
                @if ($ticket->status == 'open')
                    <x-ri-add-circle-fill />
                @elseif($ticket->status == 'closed')
                    <x-ri-forbid-fill />
                @elseif($ticket->status == 'replied')
                    <x-ri-chat-smile-2-fill />
                @endif
            </div>
        </div>
        <p class="text-base text-sm">
            {{ __('ticket.last_activity') }}
            {{ $ticket->messages()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() }}
            {{ $ticket->department ? ' - ' . $ticket->department : '' }}
        </p>
        </div>
    </a>
    @endforeach
</div>
