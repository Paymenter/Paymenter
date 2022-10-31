<x-app-layout>
    <!-- show open tickets -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:text-darkmodetext">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        {{ __('tickets.open_tickets') }}
                    </div>
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
                        {{ __('tickets.open_tickets_description') }}
                    </div>
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext grid sm:grid-cols-1 md:grid-cols-2">
                        @if (count($tickets) > 0)
                            <!-- show card per open ticket -->
                            @foreach ($tickets as $ticket)
                                <div class="dark:hover:shadow-lg dark:hover:bg-darkmodehover dark:transition dark:hover:transition bg-gray-200 bg-opacity-25 grid grid-cols-1 dark:bg-darkmode rounded-lg m-2 cursor-pointer" onclick="location.href='{{ route('tickets.show', $ticket->id) }}'">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="dark:text-darkmodetext ml-4 text-lg text-gray-600 leading-7 font-semibold">
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
                                                {{ __('tickets.priority') }}:
                                                @if ($ticket->priority == 'low')
                                                    {{ __('tickets.priority_low') }}
                                                @elseif ($ticket->priority == 'medium')
                                                    {{ __('tickets.priority_medium') }}
                                                @elseif ($ticket->priority == 'high')
                                                    {{ __('tickets.priority_high') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-12">
                                            <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                {{ __('tickets.status') }}:
                                                @if ($ticket->status == 'open')
                                                    {{ __('tickets.status_open') }}
                                                @elseif ($ticket->status == 'replied')
                                                    {{ __('tickets.status_in_progress') }}
                                                @elseif ($ticket->status == 'closed')
                                                    {{ __('tickets.status_closed') }}
                                                @endif
                                            </div>
                                        </div>
                                        <!-- last message -->
                                        @if (count($ticket->messages) > 0)
                                            <div class="ml-12">
                                                <div class="mt-2 text-sm text-gray-500 dark:text-darkmodetext">
                                                    {{ __('tickets.last_message') }}:
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