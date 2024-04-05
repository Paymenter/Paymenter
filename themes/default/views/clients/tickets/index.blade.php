<script>
    let sortBy;
    if (localStorage.getItem('blockView') == 'false') {
        var showBoxView = false;
    }
    if (localStorage.getItem('blockView') == 'true') {
        var showBoxView = true;
    }
    const style = document.createElement('style');

    if (showBoxView == true) {
        style.textContent = `
            .ticketBlockMaster {
                display: flex;
                flex-flow: wrap;
            }
            .ticketBlock {
                width: 45% !important;
            }
            .ticketBlock:nth-child(even) {
                margin-left: 2rem;
            }
            .ticketBoxRow {
                background-color: transparent !important;
            }
            .ticketBoxBox {
                background-color: var(--primary-400) !important;
            }
        `;
    } else {
        style.textContent = `
            .ticketBlock {
                width: 92.6666% !important;
            }
            .ticketBoxRow {
                background-color: var(--primary-400) !important;
            }
            .ticketBoxBox {
                background-color: transparent !important;
            }
        `;
    }
    document.head.append(style);
</script>
<x-app-layout clients title="{{ __('Tickets') }}">
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-secondary-100">
                <div class="p-6 bg-white border-gray-200 sm:pr-20 sm:pl-12 dark:bg-secondary-100 dark:text-darkmodetext pb-2">
                    <div class="flex flex-row justify-between">
                        <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                            <div class="flex-none">
                                <button class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-logo hover:border-logo hover:text-logo">
                                    {{ __('All Tickets') }}
                                    <span class="w-6 h-6 bg-red-500 rounded-full ml-1 mb-1 text-white justify-center items-center flex" style="margin-top: -3px;">
                                        {{ $tickets->count() }}
                                    </span>
                                </button>
                            </div>
                            <div class="flex-none">
                                <button class="dark:text-darkmodetext dark:hover:bg-darkbutton inline-flex w-full justify-center px-2 py-2 font-bold uppercase text-xs p-4 border-b-2 hover:border-primary-400 border-y-transparent text-gray-900 hover:text-violet-300">
                                    {{ __('On Hold') }}
                                    <div style="margin-left:2px">
                                        ({{ $tickets->where('status', 'on-hold')->count() }})
                                    </div>
                                </button>
                            </div>
                            <div class="flex-none">
                                <button class="dark:text-darkmodetext dark:hover:bg-darkbutton inline-flex w-full justify-center px-2 py-2 font-bold uppercase text-xs p-4 border-b-2 hover:border-primary-400 border-y-transparent text-gray-900 hover:text-violet-300">
                                    {{ __('Awaiting Reply') }}
                                    <div style="margin-left:2px">
                                        ({{ $tickets->where('status', 'awaiting-reply')->count() }})
                                    </div>
                                </button>
                            </div>
                            <div class="flex-none">
                                <button class="dark:text-darkmodetext dark:hover:bg-darkbutton inline-flex w-full justify-center px-2 py-2 font-bold uppercase text-xs p-4 border-b-2 hover:border-primary-400 border-y-transparent text-gray-900 hover:text-violet-300">
                                    {{ __('Deleted') }}
                                    <div style="margin-left:2px">
                                        ({{ $tickets->where('status', 'deleted')->count() }})
                                    </div>
                                </button>
                            </div>
                            <div class="flex-none">
                                <button class="dark:text-darkmodetext dark:hover:bg-darkbutton inline-flex w-full justify-center px-2 py-2 font-bold uppercase text-xs p-4 border-b-2 hover:border-primary-400 border-y-transparent text-gray-900 hover:text-violet-300">
                                    {{ __('Closed') }}
                                    <div style="margin-left:2px">
                                        ({{ $tickets->where('status', 'closed')->count() }})
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full mb-3 pt-3 pb-2">
                    <div class="flex flex-row items-center justify-between ml-4 ">
                        <div class="flex-none" style="margin-left: 32px;">
                            <button onclick="window.location.href='{{ route('clients.tickets.create') }}'" class="dark:text-darkmodetext dark:bg-darkbutton dark:hover:bg-gray-600 bg-gray-100 hover:bg-gray-600
                                        inline-flex w-full justify-center px-4 py-2 text-base font-medium rounded-md text-gray-700">
                                <div class="mr-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                        <path fill="none" d="M0 0h24v24H0z" />
                                        <path d="M15.728 9.686l-1.414-1.414L5 17.586V19h1.414l9.314-9.314zm1.414-1.414l1.414-1.414-1.414-1.414-1.414 1.414 1.414 1.414zM7.242 21H3v-4.243L16.435 3.322a1 1 0 0 1 1.414 0l2.829 2.829a1 1 0 0 1 0 1.414L7.243 21z" fill="var(--primary-400)" />
                                    </svg>
                                </div>
                                {{ __('New Ticket') }}
                            </button>
                        </div>
                        <div class="flex-none mr-12">
                            <div class="flex flex-row items-center justify-end space-x-2">
                                <label class="hidden text-sm font-medium text-gray-700 lg:block whitespace-nowrap dark:text-darkmodetext">
                                    {{ __('Sort by:') }}
                                </label>
                                <select name="select" class="bg-gray-100 dark:bg-darkbutton dark:hover:bg-gray-600 dark:text-darkmodetext text-gray-900 w-28 block form-select rounded-md" style="outline:none; border:none; box-shadow:none;">
                                    <option value="a-z" {{ $sort == 'a-z' ? 'selected' : '' }}>{{ __('A-Z') }}
                                    </option>
                                    <option value="z-a" {{ $sort == 'z-a' ? 'selected' : '' }}>{{ __('Z-A') }}
                                    </option>
                                    <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>
                                        {{ __('Newest') }}
                                    </option>
                                    <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>
                                        {{ __('Oldest') }}
                                    </option>
                                </select>
                                <script>
                                    function updateSort(event) {
                                        window.location.href = "{{ route('clients.tickets.index') }}?sort=" + event.target.value;
                                    }
                                    document.querySelector('select').addEventListener('change', updateSort);
                                </script>
                                <button class="flex text-base text-gray-700 dark:bg-darkbutton dark:text-darkmodetext dark:hover:bg-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md p-1" onclick="showBoxViewSwitch()">
                                    <div class="ticketBoxBox rounded-md ml-2 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                            <path fill="none" d="M0 0h24v24H0z" />
                                            <path d="M3 3h8v8H3V3zm0 10h8v8H3v-8zM13 3h8v8h-8V3zm0 10h8v8h-8v-8zm2-8v4h4V5h-4zm0 10v4h4v-4h-4zM5 5v4h4V5H5zm0 10v4h4v-4H5z" fill="var(--primary-100)" />
                                        </svg>
                                    </div>
                                    <div class="ticketBoxRow rounded-md ml-2 p-1">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                <path fill="none" d="M0 0h24v24H0z" />
                                                <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z" fill="var(--primary-100)" />
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                                <script>
                                    function showBoxViewSwitch() {
                                        if (localStorage.getItem('blockView') == 'false') {
                                            localStorage.setItem('blockView', 'true');
                                            style.textContent = `
                                                .ticketBlockMaster {
                                                    display: flex;
                                                    flex-flow: wrap;
                                                }
                                                .ticketBlock {
                                                    width: 45% !important;
                                                }
                                                .ticketBlock:nth-child(even) {
                                                    margin-left: 2rem;
                                                }
                                                .ticketBoxRow {
                                                    background-color: transparent !important;
                                                }
                                                .ticketBoxBox {
                                                    background-color: var(--primary-400) !important;
                                                }
                                            `;
                                        } else {
                                            localStorage.setItem('blockView', 'false');
                                            style.textContent = `
                                                .ticketBlock {
                                                    width: 92.6666% !important;
                                                }
                                                .ticketBoxRow {
                                                    background-color: var(--primary-400) !important;
                                                }
                                                .ticketBoxBox {
                                                    background-color: transparent !important;
                                                }
                                            `;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-secondary-100 mt-6">
                <div class="ticketBlockMaster overflow-hidden bg-white-200 shadow-xl sm:rounded-lg dark:bg-secondary-100 mt-6 pb-2.5">
                    @if (count($tickets) > 0)

                        @php
                            switch ($sort) {
                                case 'z-a':
                                    $tickets = $tickets->sortByDesc('title');
                                    break;
                                case 'newest':
                                    $tickets = $tickets->sortBy('created_at');
                                    break;
                                case 'oldest':
                                    $tickets = $tickets->sortByDesc('created_at');
                                    break;
                                default:
                                    $tickets = $tickets->sortBy('title');
                                    break;
                            }
                        @endphp


                        @foreach ($tickets as $ticket)
                        <div id="ticket-main" class="ticketBlock hover:dark:bg-secondary-300 hover:bg-gray-200 mb-3 pt-3 bg-gray-100 dark:bg-secondary-200 sm:rounded-lg ml-12 pb-3" style="cursor: pointer" onclick="window.location.href = '{{ route('clients.tickets.show', $ticket->id) }}'">
                            <div class="flex flex-row items-center justify-between ml-4">
                                <div class="flex flex-row items-start justify-start">
                                    <div class="flex flex-col flex-grow w-full space-y-0 self-center">
                                        <div class="text-sm font-semibold text-black dark:text-darkmodetext">
                                            {{ __('Ticket #') }}{{ $ticket->id }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row items-center justify-end mr-6">
                                    <div class="flex flex-row items-center justify-end space-x-2">
                                        <div class="text-sm font-semibold text-black dark:text-darkmodetext">
                                            {{ __('Last Updated:') }} {{ $ticket->updated_at->diffForHumans() }}
                                        </div>
                                        <button class="dropbtn" type="button" aria-label="More options" aria-haspopup="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                <path fill="none" d="M0 0h24v24H0z" />
                                                <path d="M4.5 10.5c-.825 0-1.5.675-1.5 1.5s.675 1.5 1.5 1.5S6 12.825 6 12s-.675-1.5-1.5-1.5zm15 0c-.825 0-1.5.675-1.5 1.5s.675 1.5 1.5 1.5S21 12.825 21 12s-.675-1.5-1.5-1.5zm-7.5 0c-.825 0-1.5.675-1.5 1.5s.675 1.5 1.5 1.5 1.5-.675 1.5-1.5-.675-1.5-1.5-1.5z" fill="rgba(149,164,166,1)" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row items-center justify-between ml-4 pt-1">
                                <div class="flex flex-row items-start justify-start">
                                    <div class="flex flex-col flex-grow w-full space-y-0 self-center text-left">
                                        <div class="text-2xl font-semibold text-black dark:text-darkmodetext">
                                            {{__('Subject')}}: {{ $ticket->title }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-700 dark:text-darkmodetext pt-2 max-w-screen-lg w-full">
                                            @if (count($ticket->messages) > 0)
                                            <div style='{!! count(explode("<br />", nl2br($ticket->messages->last()->message ?? null))) > 3 ? "-webkit-mask-image: linear-gradient(black 35%, transparent 100%);" : "" !!}'>
                                                <h2 class="leading-7">{{__('Last Message')}}:</h2>
                                                <div class="flex">
                                                    <span class="font-bold">{{ $ticket->messages->last()->user->name }}:&nbsp;</span> {!! Str::markdown(implode(array_slice(explode("<br />", Stevebauman\Purify\Facades\Purify::clean(nl2br($ticket->messages->last()->message))), 0, 4))) !!}
                                                </div>
                                            </div>
                                            @else
                                            {{ __('No messages...') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row items-center justify-between ml-4 pt-4">
                                <div class="flex flex-row items-start justify-start">
                                    <div class="shrink-0 w-8 mt-1 mr-4">
                                        <img src="https://www.gravatar.com/avatar/{{ md5($user->email) }}?s=200&d=mp" class="h-8 w-full shadow-lg rounded-full ring" onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'>
                                    </div>
                                    <div class="flex flex-col flex-grow w-full space-y-0 self-center">
                                        <div class="text-sm font-semibold pt-1 text-black dark:text-darkmodetext">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row items-center justify-end mr-6">
                                    <div class="flex flex-row items-center justify-end space-x-2">
                                        <div class="text-sm font-semibold flex">
                                            <div class="ml-1.5 text-black dark:text-darkmodetext">
                                                <div class="p-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if ($ticket->status === 'open')
                                                        bg-green-400 text-green-900
                                                    @elseif ($ticket->status === 'closed')
                                                        bg-red-400 text-red-900
                                                    @else
                                                        bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucwords(str_replace('-', ' ', $ticket->status)) }}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="text-sm font-semibold flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                <path fill="none" d="M0 0h24v24H0z" />
                                                <path d="M14.828 7.757l-5.656 5.657a1 1 0 1 0 1.414 1.414l5.657-5.656A3 3 0 1 0 12 4.929l-5.657 5.657a5 5 0 1 0 7.071 7.07L19.071 12l1.414 1.414-5.657 5.657a7 7 0 1 1-9.9-9.9l5.658-5.656a5 5 0 0 1 7.07 7.07L12 16.244A3 3 0 1 1 7.757 12l5.657-5.657 1.414 1.414z" fill="rgba(149,164,166,1)" />
                                            </svg>
                                            <div class="ml-1.5 text-black dark:text-darkmodetext">
                                                {{ $ticket->messages->sum(function ($message) { return $message->files->count(); }) }}
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                <path fill="none" d="M0 0h24v24H0z" />
                                                <path d="M10 3h4a8 8 0 1 1 0 16v3.5c-5-2-12-5-12-11.5a8 8 0 0 1 8-8zm2 14h2a6 6 0 1 0 0-12h-4a6 6 0 0 0-6 6c0 3.61 2.462 5.966 8 8.48V17z" fill="rgba(149,164,166,1)" />
                                            </svg>
                                            <div class="ml-1.5 text-black dark:text-darkmodetext">
                                                {{ $ticket->messages->where('ticket_id', $ticket->id)->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                            <div class="flex flex-row items-center justify-center">
                                <div class="text-sm font-semibold pt-1">
                                    {{ __('No tickets found.') }}
                                </div>
                            </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
