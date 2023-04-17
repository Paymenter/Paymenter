<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets ' . $ticket->title) }}
    </x-slot>

    <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
        <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{ __('View Ticket #') }}{{ $ticket->id }}
        </h1>
    </div>
    <div class="ml-10 flex flex-col md:flex-row items-baseline">
        <h1 class="dark:text-darkmodetext text-gray-600 px-3 rounded-md font-bold text-xl md:m-4">
            <strong>{{ __('Subject:') }}</strong>
            {{ $ticket->title }}
        </h1>
        <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl md:m-4">
            <strong>{{ __('Priority:') }}</strong>
            {{ $ticket->priority }}
        </p>
        <p class="dark:text-darkmodetext text-gray-600 px-3  rounded-md text-xl md:m-4">
            <strong>{{ __('Status:') }}</strong>
            {{ $ticket->status }}
        </p>
    </div>
    <div class="ml-10 flex flex-col md:flex-row items-baseline">
        <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl md:m-4">
            <strong>{{ __('Client:') }}</strong>
            {{ $ticket->client()->get()[0]->name }}
        </p>
        <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl md:m-4">
            <strong>{{ __('Product(s):') }}</strong>
            @forelse ($ticket->orders()->get() as $product)
                {{ $product->name }}
            @empty
                {{ __('No products') }}
            @endforelse
        </p>
        <form action="{{ route('admin.tickets.status', $ticket->id) }}" method="POST">
            @csrf
            <div class="flex items-baseline p-2 md:m-4">
                <select name="status" id="status"
                    class="dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block">
                    <option value="open">{{ __('Open') }}</option>
                    <option value="closed">{{ __('Closed') }}</option>
                </select>
                <button type="submit"
                    class="ml-10 bg-logo hover:bg-logo/75 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                    {{ __('Change status') }}
                </button>
            </div>
        </form>
    </div>

    @if (empty($ticket->messages()->get()[0]))
        <div class="ml-10 flex items-baseline ">
            <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                {{ __('No messages yet') }}
            </p>
        </div>
    @else
        <div class="p-6 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:border-white">
            <div class="grid grid-cols-1 gap-4">
                <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">
                        {{ __('Messages') }}</h1>
                    @foreach ($ticket->messages()->get() as $message)
                        <div class="p-4 mt-4 rounded-md dark:bg-darkmode bg-gray-100 grid grid-cols-1 md:grid-cols-2">
                            <label class="prose dark:prose-invert">
                                {!! Str::Markdown(str_replace("\n", "  \n", $message->message), ['html_input' => 'escape']) !!}
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
    @endif
    <br>
    <div class="overflow-hidden bg-white sm:rounded-lg dark:bg-darkmode2">
        <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="mt-10">
            @csrf
            <div class="p-6 bg-white border-gray-200 sm:px-20 dark:bg-darkmode2 mt-10">
                <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">
                    {{ __('Reply') }}</h1>
                <div class="grid grid-cols-1 gap-4">
                    <x-success class="mt-4" />
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                        <label for="message" class="text-md">{{ __('Message') }}</label>
                        <textarea id="message"
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                            rows="4" name="message" required></textarea>
                        <br>
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


</x-admin-layout>
