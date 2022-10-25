<x-app-layout>
    <x-slot name="title">
        {{ __('Ticket') }}
    </x-slot>

    <!-- show last messages and form to reply -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:border-black">
                    <h1 class="dark:text-darkmodetext text-xl text-gray-500">Ticket #{{ $ticket->id }}</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <div class="mt-4">
                                <label for="subject">{{ __('tickets.subject') }}</label>
                                <input id="subject"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                    name="subject" required type="text" value="{{ $ticket->title }}" disabled>
                            </div>
                            <div class="mt-4">
                                <label for="priority">{{ __('tickets.priority') }}</label>
                                <input id="priority"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                    name="priority" required type="text" value="{{ $ticket->priority }}" disabled>
                            </div>
                            <div class="mt-4">
                                <label for="status">{{ __('tickets.status') }}</label>
                                <input id="status"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                    name="status" required type="text" value="{{ $ticket->status }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:border-black">
                    <h1 class="dark:text-darkmodetext text-xl text-gray-500">Messages</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            @foreach ($messages as $message)
                                <div class="mt-4 dark:bg-darkmode p-4 rounded-md">
                                    <label for="message">{{ $message->message }}</label>
                                    <p class="text-xs text-gray-500 dark:text-darkmodetext z-10 -top-2 relative float-right">{{ $message->created_at }}</p>
                                    <p class="text-xs text-gray-500 dark:text-darkmodetext text-end">
                                        @if($message->user_id == Auth::user()->id)
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
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:border-black">
                        <h1 class="dark:text-darkmodetext text-xl text-gray-500">Reply</h1>
                        <div class="grid grid-cols-1 gap-4">
                            <x-success class="mt-4" />
                            <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                                <div class="mt-4">
                                    <label for="message">{{ __('tickets.message') }}</label>
                                    <textarea id="message"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="message" required></textarea>
                                </div>
                                <div class="mt-4">
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('normal.reply') }}
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