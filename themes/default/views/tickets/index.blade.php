<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <div class="flex flex-col md:flex-row justify-between">
        <h1 class="text-2xl font-semibold text-white">Tickets</h1>
        <a href="{{ route('tickets.create') }}" wire:navigate>
            <x-button.primary class="h-fit w-fit">
                Create Ticket
            </x-button.primary>
        </a>
    </div>

    <div class="flex flex-col gap-2 my-2">
        @foreach ($tickets as $ticket)
            <div class="flex flex-row justify-between w-full bg-primary-700 p-2 px-4 rounded-md">
                <div class="flex flex-col gap-1 w-fit">
                    <h2 class="text-2xl font-semibold text-white">
                        {{ $ticket->subject }}
                    </h2>
                    <div class="text-sm text-white line-clamp-3">
                        {!! Str::markdown($ticket->messages()->orderBy('created_at', 'desc')->first()->message, [
                            'html_input' => 'strip',
                            'allow_unsafe_links' => false,
                        ]) !!}
                    </div>
                </div>
                <div class="flex flex-col justify-between items-end gap-4 w-fit">
                    <h3 class="text-xl font-semibold p-1 text-white">
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
