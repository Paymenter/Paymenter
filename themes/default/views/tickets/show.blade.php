<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <h1 class="text-2xl font-semibold text-white">Ticket: {{ $ticket->subject }}</h1>

    <div class="md:grid grid-cols-4 flex flex-col gap-4">
        <div class="md:col-span-3">
            <div class="flex flex-col gap-4 max-h-[60vh] overflow-y-auto pr-4" wire:poll.5s>
                @foreach ($ticket->messages()->with('user')->get() as $index => $message)
                    <div
                        class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg w-full max-w-[80%]  {{ $message->user_id === $ticket->user_id ? 'ml-auto' : 'mr-auto' }}"
                        @if ($loop->last) x-data x-init="$el.scrollIntoView()" @endif>
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $message->user->name }}</h2>
                                <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="mt-2 prose dark:prose-invert break-all">{!! Str::markdown(e($message->message), [
                            'allow_unsafe_links' => false,
                            'renderer' => [
                                'soft_break' => "<br>"
                            ]
                        ]) !!}</div>
                    </div>
                @endforeach
            </div>
            <!-- Reply Form -->
            <div class="mt-4">
                <form wire:submit.prevent="save" wire:ignore>
                    <textarea id="editor"></textarea>
                    <x-button.primary class="mt-2 !w-fit float-right">
                        {{ __('ticket.reply') }}
                    </x-button.primary>
                </form>
                <x-easymde-editor />
            </div>

        </div>

        <div class="md:order-last order-first w-full col-span-3 sm:col-auto">
            <!-- Show subject and status -->
            <div class="flex flex-col w-full col-span-1">
                <h2 class="text-2xl font-semibold bg-primary-800 p-2 px-4 rounded-md mb-2">
                    {{ __('ticket.ticket_details') }}
                </h2>
                <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md gap-2">
                    <h4 class="h-fit">{{ __('ticket.subject') }}:</h4> {{ $ticket->subject }}
                </div>
                <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                    <h4>{{ __('ticket.status') }}:</h4> {{ ucfirst($ticket->status) }}
                </div>
                <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                    <h4>{{ __('ticket.priority') }}:</h4> {{ ucfirst($ticket->priority) }}
                </div>
                <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                    <h4>{{ __('ticket.created_at') }}:</h4> {{ $ticket->created_at->diffForHumans() }}
                </div>
                @if ($ticket->department)
                    <div class="font-semibold flex md:flex-col justify-between bg-primary-800 p-2 px-4 rounded-md">
                        <h4>{{ __('ticket.department') }}:</h4>
                        {{ $ticket->department }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
