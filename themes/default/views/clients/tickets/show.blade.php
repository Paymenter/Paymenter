<x-app-layout clients title="{{ __('Ticket') }}">
    @php
        function showTicketDate(int $createdAt):string{
            if(date('Y-m-d', $createdAt) === date('Y-m-d', strtotime('now'))) {
                return date('H:i', $createdAt);
            } else {
                return date('d', $createdAt) . " " . date('M', $createdAt) . " " . date('Y, H:i', $createdAt);
            }
        }
    @endphp

    <!-- show last messages and form to reply -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-t-lg dark:bg-secondary-100 border-b border-gray-200 dark:bg-secondary-100 dark:border-secondary-300">
                <div class="p-6 bg-white sm:px-20 dark:bg-secondary-100">
                    <x-success class="mt-4" />
                    <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{__('Ticket')}} #{{ $ticket->id }}
                    </h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-3 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
                            <div class="flex flex-row gap-x-4 items-baseline">
                                <x-input disabled type="text" id="title" :label="__('Subject')" name="title" value="{{ $ticket->title }}"
                                         required class="mt-2 w-full" icon="ri-pencil-line" />

                                <x-input disabled type="text" name="status" :label="__('Status')" icon="ri-bar-chart-line" class="mt-2 w-full" value="{{ ucfirst($ticket->status) }}"/>
                            </div>
                            @if($ticket->status !== 'closed')
                                <form action="{{ route('clients.tickets.close', $ticket->id) }} " method="POST" class="mt-3">
                                    @csrf
                                    <button class="button button-danger float-right"><i class="ri-close-circle-line"></i> {{__('Close Ticket')}}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white sm:px-20 dark:bg-secondary-100 dark:border-black">
                    <div class="mt-6 dark:bg-secondary-100">
                        <div class="grid grid-cols-3 gap-4">

                            @foreach ($messages as $message)
                                @if ($message->user_id == Auth::user()->id)
                                    <div class="col-span-3 text-center w-full">
                                        {{$message->messageDate()}}
                                    </div>
                                    <div class="col-span-1"></div>
                                    <div class="w-full col-span-2" id="message">
                                        <div class="grid grid-cols-12 max-w-full place-items-end">
                                            <div class="col-span-11 my-auto">
                                                <span class="text-sm flex max-w-full justify-end font-normal text-opacity-50 mr-5">{{__('You')}}</span>
                                                <div class="justify-end text-gray-100 break-all dark:text-white-400 w-fit rounded-2xl bg-indigo-600 p-2 px-4 mr-2">
                                                    <p class="max-w-full text-end" style="color: white !important;">
                                                        {!! Str::Markdown(str_replace("\n", "  \n", $message->message), ['html_input' => 'escape']) !!}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="justify-start w-full h-full flex col-span-1">
                                                <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                                                     class="h-10 w-10 mt-7 shadow-md rounded-full bg-secondary-200 inline-block"
                                                     onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($message->user_id !== Auth::user()->id)
                                    <div class="col-span-3 text-center w-full">
                                        {{$message->messageDate()}}
                                    </div>
                                    <div class="w-full col-span-2" id="message">
                                        <div class="grid grid-cols-12 max-w-full">
                                            <div class="justify-end w-full flex col-span-1">
                                                <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                                                     class="h-10 w-10 mt-7 shadow-md rounded-full bg-secondary-200 inline-block"
                                                     onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'>
                                            </div>
                                            <div class="col-span-11">
                                                <span class="text-sm ml-5 font-normal text-opacity-50">{{$message->user->name}} ({{ ucfirst($message->user->role->name) }})</span>
                                                <div class="my-auto text-gray-500 break-all dark:text-darkmodetext ml-2 w-fit rounded-2xl bg-gray-200 dark:bg-darkmode p-2 px-4">
                                                    <p class="prose dark:prose-invert max-w-full break-word">
                                                        {!! Str::Markdown(str_replace("\n", "  \n", $message->message), ['html_input' => 'escape']) !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-1"></div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-b-lg dark:bg-secondary-100">
                @if($ticket->status == "closed")
                    <div class="p-6 text-center text-red-500 bg-white sm:px-20 dark:bg-secondary-100 mx-auto my-auto">
                        {{__('The ticket has been closed, you cannot respond.')}}
                    </div>
                @else
                    <form method="POST" action="{{ route('clients.tickets.reply', $ticket->id) }}" class="mt-10"
                          id="reply">
                        @csrf
                        <div
                            class="bg-white mb-5 border-gray-200 sm:px-20 dark:bg-secondary-100 dark:border-black mt-5">
                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">{{ __('Reply') }}</h1>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="mt-3 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
                                    <div class="flex flex-row">
                                        <textarea
                                            id="message"
                                            class="block my-auto w-full rounded-2xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-indigo-300 dark:border-0 sm:text-sm dark:bg-secondary-200"
                                            rows="2"
                                            name="message"
                                            placeholder="Aa"
                                            required
                                        ></textarea>
                                        <x-recaptcha form="reply" />
                                        <button class="button-primary ml-1 rounded-full w-10 h-10 float-right transition-all ease-in-out">
                                            <i class="ri-send-plane-fill"></i>
                                        </button>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- Script for auto-extend message textarea -->
    <script>
        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
            tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
            tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
            this.style.height = 0;
            this.style.height = (this.scrollHeight) + "px";
        }
    </script>
</x-app-layout>
