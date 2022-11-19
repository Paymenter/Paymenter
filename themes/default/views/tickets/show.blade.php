<x-app-layout>
    <x-slot name="title">
        {{ __('Ticket') }}
    </x-slot>

    <!-- show last messages and form to reply -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Ticket #{{ $ticket->id }}</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <div class="mt-4">
                                <label for="subject">{{ __('tickets.subject') }}</label>
                                <input id="subject"
                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                    name="subject" required type="text" value="{{ $ticket->title }}" disabled>
                            </div>
                            <div class="mt-4">
                                <label for="priority">{{ __('tickets.priority') }}</label>
                                <input id="priority"
                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                    name="priority" required type="text" value="{{ $ticket->priority }}" disabled>
                            </div>
                            <div class="mt-4">
                                <label for="status">{{ __('tickets.status') }}</label>
                                <input id="status"
                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                    name="status" required type="text" value="{{ $ticket->status }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Messages</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            @foreach ($messages as $message)
                                <div class="p-4 mt-4 rounded-md dark:bg-darkmode">
                                    <label for="message">{{ $message->message }}</label>
                                    <p
                                        class="relative z-10 float-right text-xs text-gray-500 dark:text-darkmodetext -top-2">
                                        {{ $message->created_at }}</p>
                                    <p class="text-xs text-gray-500 dark:text-darkmodetext text-end">
                                        @if ($message->user_id == Auth::user()->id)
                                            {{ __('tickets.you') }}
                                        @else
                                            {{ $message->user->name }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}">
                    @csrf
                    <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                        <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Reply</h1>
                        <div class="grid grid-cols-1 gap-4">
                            <x-success class="mt-4" />
                            <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                                <div class="mt-4">
                                    <label for="message">{{ __('tickets.message') }}</label>
                                    <textarea id="message"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="message" required></textarea>
                                </div>
                                <br>
                                @if (config('settings::recaptcha') == 1)
                                    <div class="g-recaptcha"
                                        data-sitekey="{{ config('settings::recaptcha_site_key') }}"></div>
                                @endif
                                <div class="mt-4">
                                    <button
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                        {{ __('tickets.reply') }}
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
