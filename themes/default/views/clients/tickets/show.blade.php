<x-app-layout>
    <x-slot name="title">
        {{ __('Ticket') }}
    </x-slot>

    <!-- show last messages and form to reply -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">

                <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
                    <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">View Ticket #{{ $ticket->id }}
                    </h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <div class="mt-4">
                                <p><strong>Subject:</strong> {{ $ticket->title }}</p>
                                <form action="{{ route('clients.tickets.close', $ticket->id) }} " method="POST">
                                    @csrf
                                    <button class="form-submit bg-red-600 float-right">Close Ticket</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <h1 class="text-xl text-gray-500 dark:text-darkmodetext">{{ __('Messages') }}</h1>

                            @foreach ($messages as $message)
                                <div
                                    class="p-4 mt-4 rounded-md dark:bg-darkmode bg-gray-100 grid grid-cols-1 md:grid-cols-2">
                                    <label class="prose dark:prose-invert max-w-full">
                                        {!! Str::Markdown(str_replace("\n", "\n", $message->message), ['html_input' => 'escape']) !!}
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-darkmodetext text-end"
                                        style="align-items:flex-end">
                                        @if ($message->user_id == Auth::user()->id)
                                            {{ __('You') }}
                                            <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                                                class="h-8 w-8 shadow-lg rounded-full ml-1 inline-block"
                                                onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'><br>{{ $message->created_at }}
                                        @else
                                            {{ $message->user->name }}
                                            <img src="https://www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=200&d=mp"
                                                class="h-8 w-8 shadow-lg rounded-full ml-1 inline-block"
                                                onerror='this.error=null;this.src="https://d33wubrfki0l68.cloudfront.net/c0e8a3c6172bd5bebfe787d49974adcff1ec4d3a/ca6a2/img/people/joseph-jolton.png";'><br>{{ $message->created_at }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <form method="POST" action="{{ route('clients.tickets.reply', $ticket->id) }}" class="mt-10"
                    id="reply">
                    @csrf
                    <div
                        class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black mt-10">
                        <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">{{ __('Reply') }}</h1>
                        <div class="grid grid-cols-1 gap-4">
                            <x-success class="mt-4" />
                            <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                                <label for="message" class="text-md">{{ __('Message') }}</label>
                                <textarea id="message"
                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                    rows="4" name="message" required></textarea>
                                <br>
                                <x-recaptcha form="reply" />
                                <div class="mt-4">
                                    <button class="form-submit float-right">
                                        {{ __('Reply') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
