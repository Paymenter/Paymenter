<div class="space-y-4">
    @foreach ($tickets as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate>
        <div class="bg-[#1e293b] hover:bg-background-secondary/80 p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-[#2d3b4f] p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.00488 9.49979V3.99979C2.00488 3.4475 2.4526 2.99979 3.00488 2.99979H21.0049C21.5572 2.99979 22.0049 3.4475 22.0049 3.99979V9.49979C20.6242 9.49979 19.5049 10.6191 19.5049 11.9998C19.5049 13.3805 20.6242 14.4998 22.0049 14.4998V19.9998C22.0049 20.5521 21.5572 20.9998 21.0049 20.9998H3.00488C2.4526 20.9998 2.00488 20.5521 2.00488 19.9998V14.4998C3.38559 14.4998 4.50488 13.3805 4.50488 11.9998C4.50488 10.6191 3.38559 9.49979 2.00488 9.49979ZM14.0049 4.99979H4.00488V7.96755C5.4866 8.7039 6.50488 10.2329 6.50488 11.9998C6.50488 13.7666 5.4866 15.2957 4.00488 16.032V18.9998H14.0049V4.99979ZM16.0049 4.99979V18.9998H20.0049V16.032C18.5232 15.2957 17.5049 13.7666 17.5049 11.9998C17.5049 10.2329 18.5232 8.7039 20.0049 7.96755V4.99979H16.0049Z"></path>
                </svg>
            </div>
            <span class="text-white font-medium">{{ $ticket->subject }}</span>
            </div>
            <div class="w-5 h-5 rounded-md p-0.5
                @if ($ticket->status == 'open') text-success bg-success/20 
                @elseif($ticket->status == 'closed') text-inactive bg-inactive/20
                @else text-info bg-info/20 
                @endif"
                @if ($ticket->status == 'open')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 11H7V13H11V17H13V13H17V11H13V7H11V11Z"></path>
                    </svg>
                @elseif($ticket->status == 'closed')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM8.52313 7.10891C8.25459 7.30029 7.99828 7.51644 7.75736 7.75736C7.51644 7.99828 7.30029 8.25459 7.10891 8.52313L15.4769 16.8911C15.7454 16.6997 16.0017 16.4836 16.2426 16.2426C16.4836 16.0017 16.6997 15.7454 16.8911 15.4769L8.52313 7.10891Z"></path>
                    </svg>
                @elseif($ticket->status == 'replied')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.29117 20.8242L2 22L3.17581 16.7088C2.42544 15.3056 2 13.7025 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C10.2975 22 8.6944 21.5746 7.29117 20.8242ZM7 12C7 14.7614 9.23858 17 12 17C14.7614 17 17 14.7614 17 12H15C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12H7Z"></path>
                    </svg>
                @endif
            </div>
        </div>
        <p class="text-gray-400 text-sm">
            {{ __('ticket.last_activity') }}
            {{ $ticket->messages()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() }}
            {{ $ticket->department ? ' - ' . $ticket->department : '' }}
        </p>
        </div>
    </a>
    @endforeach
</div>


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
