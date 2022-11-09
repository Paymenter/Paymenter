<x-app-layout>
    <!-- show open tickets -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:text-darkmodetext">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        {{ __('Open tickets') }}
                    </div>
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
                        {{ __('Open a ticket so you can talk to us.') }}
                    </div>
                    <!-- display create ticket button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('tickets.create') }}"
                            class="px-4 py-2 font-bold text-white bg-indigo-500 rounded hover:bg-indigo-700">
                            {{ __('Create ticket') }}
                        </a>
                    </div>
                    <div class="grid mt-6 text-gray-500 dark:text-darkmodetext sm:grid-cols-1 md:grid-cols-2">
                        @if (count($tickets) > 0)
                            <!-- show card per open ticket -->
                            @foreach ($tickets as $ticket)
                                <div class="grid grid-cols-1 m-2 bg-gray-200 bg-opacity-25 rounded-lg cursor-pointer dark:hover:shadow-lg dark:hover:bg-darkmodehover dark:transition dark:hover:transition dark:bg-darkmode" onclick="location.href='{{ route('tickets.show', $ticket->id) }}'">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="ml-4 text-lg font-semibold leading-7 text-gray-600 dark:text-darkmodetext">
                                                {{ $ticket->title }}
                                            </div>
                                        </div>
                                        <div class="ml-12">
                                            <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                {{ $ticket->description }}
                                            </div>
                                        </div>
                                        <div class="ml-12">
                                            <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                {{ __('Priority') }}:
                                                @if ($ticket->priority == 'low')
                                                    {{ __('Low Priority') }}
                                                @elseif ($ticket->priority == 'medium')
                                                    {{ __('Normal Priority') }}
                                                @elseif ($ticket->priority == 'high')
                                                    {{ __('High Priority') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-12">
                                            <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                {{ __('Status') }}:
                                                @if ($ticket->status == 'open')
                                                    {{ __('open') }}
                                                @elseif ($ticket->status == 'replied')
                                                    {{ __('replied') }}
                                                @elseif ($ticket->status == 'closed')
                                                    {{ __('closed') }}
                                                @endif
                                            </div>
                                        </div>
                                        <!-- last message -->
                                        @if (count($ticket->messages) > 0)
                                            <div class="ml-12">
                                                <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                    {{ __('Last message') }}:
                                                    {{ $ticket->messages->last()->message }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        @else
                            {{ __('tickets.no_open_tickets') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>