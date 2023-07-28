<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets ' . $ticket->title) }}
    </x-slot>

    <div class="bg-white dark:bg-secondary-100">
        <h1 class="text-2xl font-bold text-gray-500 dark:text-darkmodetext">{{ __('View Ticket #') }}{{ $ticket->id }}
        </h1>
    </div>
    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="pb-10">
        @csrf

        <div class="grid md:grid-cols-2 mt-4 gap-4">
            <div class="flex flex-col items-baseline">
                <x-input type="text" id="title" :label="__('Subject')" name="title" value="{{ $ticket->title }}"
                    required class="mt-2 w-full" icon="ri-pencil-line" />

                <x-input type="select" name="priority" :label="__('Priority')" icon="ri-bar-chart-line" class="mt-2 w-full">
                    <option value="low" @if ($ticket->priority == 1) selected @endif>
                        {{ __('Low') }}</option>
                    <option value="medium" @if ($ticket->priority == 2) selected @endif>
                        {{ __('Medium') }}</option>
                    <option value="high" @if ($ticket->priority == 3) selected @endif>
                        {{ __('High') }}</option>
                </x-input>

                <x-input type="select" name="status" :label="__('Status')" icon="ri-bar-chart-line" class="mt-2 w-full">
                    <option value="open" @if ($ticket->status == 'open') selected @endif>
                        {{ __('Open') }}</option>
                    <option value="closed" @if ($ticket->status == 'closed') selected @endif>
                        {{ __('Closed') }}</option>
                </x-input>
            </div>
            <div class="flex flex-col items-baseline">
                <x-input type="text" id="user" :label="__('User')" name="user"
                    value="{{ $ticket->user->name }}" required class="mt-2 w-full" icon="ri-user-line" readonly />
                <x-input type="select" id="product" name="product_id" :label="__('Product')" icon="ri-checkbox-circle-line"
                    class="mt-2 w-full">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($ticket->user->orderProducts()->get() as $product)
                        <option value="{{ $product->id }}" @if ($product->id == $ticket->order_id) selected @endif>
                            {{ $product->id }} - {{ $product->product->name }}
                    @endforeach
                </x-input>

                <x-input type="select" id="assigned_to" name="assigned_to" :label="__('Assigned To')" icon="ri-user-line"
                    class="mt-2 w-full">
                    <option value="">{{ __('None') }}</option>
                    @foreach (App\Models\User::where('role_id', '!=', 2)->get() as $user)
                        <option value="{{ $user->id }}" @if ($user->id == $ticket->assigned_to) selected @endif>
                            {{ $user->name }} - {{ $user->role->name }}
                    @endforeach
                </x-input>
            </div>
        </div>
        <button type="submit" class="button button-success float-right mt-4">
            {{ __('Update') }}
        </button>
    </form>
    @if (empty(
            $ticket->messages()->get()->first()
        ))
        <div class="ml-10 flex items-baseline ">
            <p class="dark:text-darkmodetext text-gray-600 px-3 rounded-md text-xl m-4">
                {{ __('No messages yet') }}
            </p>
        </div>
    @else
        <div class="p-6 bg-white border-b border-gray-200 dark:bg-secondary-100 dark:border-secondary-300">
            <div class="grid grid-cols-1 gap-4">
                <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
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
    <div class="overflow-hidden bg-white sm:rounded-lg dark:bg-secondary-100">
        <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="mt-10">
            @csrf
            <div class="border-gray-200 sm:px-20 dark:bg-secondary-100 mt-10">
                <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">
                    {{ __('Reply') }}</h1>
                <div class="grid grid-cols-1 gap-4">
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
                        <x-input type="textarea" id="message" :label="__('Message')" rows="4" name="message"
                            required></x-input>
                        <br>
                        <button class="button button-success float-right">
                            {{ __('Reply') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>


</x-admin-layout>
