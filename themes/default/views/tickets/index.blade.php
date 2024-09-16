<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <div class="flex flex-col md:flex-row justify-between">
        <h1 class="text-2xl font-semibold text-white">Tickets</h1>
        <a href="{{ route('tickets.create') }}" wire:navigate>
            <x-button.primary class="h-fit w-fit">
                Create Ticket
            </x-button.primary>
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="text-white text-center my-4">
            No tickets found.
        </div>
    @endif
    <div class="flex flex-col gap-4 my-4">
        @foreach ($tickets as $ticket)
            <div class="flex flex-row justify-between w-full bg-primary-700 p-4 rounded-lg shadow-lg">
                <div class="flex flex-col gap-2 w-3/4">
                    <h2 class="text-2xl font-semibold text-white">
                        {{ $ticket->subject }}
                    </h2>
                    <div class="text-sm text-white line-clamp-3">
                        {!! Str::markdown($ticket->messages()->orderBy('created_at', 'desc')->first()->message, [
                            'html_input' => 'strip',
                            'allow_unsafe_links' => false,
                        ]) !!}
                    </div>
                    <div class="text-gray-400 text-sm">
                        {{ __('ticket.last_activity') }}
                        {{ $ticket->messages()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() }}
                        {{ $ticket->department ? ' - ' . $ticket->department : '' }}
                    </div>
                </div>
                <div class="flex flex-col justify-between items-end gap-4 w-1/4">
                    <h3
                        class="font-semibold p-1 px-1.5 border rounded-md @if ($ticket->status == 'closed') bg-red-900 border-red-600 text-red-300 @else bg-green-800 border-green-600 text-green-500 @endif">
                        {{ ucfirst($ticket->status) }}
                    </h3>
                    <div class="flex flex-row gap-2">
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate>
                            <x-button.primary class="h-fit w-fit">
                                View
                            </x-button.primary>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $tickets->links() }}

</div>
