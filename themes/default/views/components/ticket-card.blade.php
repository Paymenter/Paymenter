@props(['ticket'])

@php
    $statusClass = match($ticket->status) {
        'open'   => 'text-success bg-success/20',
        'closed' => 'text-inactive bg-inactive/20',
        default  => 'text-info bg-info/20',
    };
@endphp

<x-entity-card :href="route('tickets.show', $ticket)" :statusClass="$statusClass">
    <x-slot:icon><x-ri-ticket-line class="size-5 text-secondary" /></x-slot:icon>

    <x-slot:heading>
        <span class="font-medium">#{{ $ticket->id }} - {{ $ticket->subject }}</span>
    </x-slot:heading>

    <x-slot:status>
        @if ($ticket->status === 'open')
            <x-ri-add-circle-fill />
        @elseif($ticket->status === 'closed')
            <x-ri-forbid-fill />
        @else
            <x-ri-chat-smile-2-fill />
        @endif
    </x-slot:status>

    <x-slot:detail>
        <p class="text-base/50 text-sm">
            {{ __('ticket.last_activity') }}
            {{ $ticket->latestMessage?->created_at->diffForHumans() }}
            {{ $ticket->department ? ' - ' . $ticket->department : '' }}
        </p>
    </x-slot:detail>
</x-entity-card>
