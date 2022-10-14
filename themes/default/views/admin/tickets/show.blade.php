<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets ' . $ticket->title) }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <x-success class="mb-4" />
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <div class="ml-10 flex items-baseline ">
                                        <h1 class="text-gray-600 px-3 rounded-md font-bold text-xl m-4">Title:
                                            {{ $ticket->title }}</h1>
                                        <p class="text-gray-600 px-3 rounded-md text-xl m-4">Priority:
                                            {{ $ticket->priority }}</p>
                                        <p class="text-gray-600 px-3  rounded-md text-xl m-4">Status:
                                            {{ $ticket->status }}</p>
                                    </div>
                                    <div class="ml-10 flex items-baseline ">
                                        <p class="text-gray-600 px-3 rounded-md text-xl m-4">Client:
                                            {{ $ticket->client()->get()[0]->name }}
                                        </p>
                                        <p class="text-gray-600 px-3 rounded-md text-xl m-4">Product(s):
                                            @foreach ($ticket->orders()->get() as $product)
                                                {{ $product->name }}
                                            @endforeach
                                        </p>
                                        <form action="{{ route('admin.tickets.status', $ticket->id) }}" method="POST">
                                            @csrf
                                            <div class="ml-10 flex items-baseline p-2">
                                                <select name="status" id="status"
                                                    class="form-input rounded-md shadow-sm mt-1 block">
                                                    <option value="open">Open</option>
                                                    <option value="closed">Closed</option>
                                                </select>
                                                <button type="submit"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                    Change status
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @if (empty($ticket->messages()->get()[0]))
                                        <div class="ml-10 flex items-baseline ">
                                            <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                                                No messages yet
                                            </p>
                                        </div>
                                    @endif
                                    @foreach ($ticket->messages()->get() as $message)
                                        <div class="ml-10 border-black border-2 rounded">
                                            <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                                                {{ $message->message }}
                                            </p>
                                            <p
                                                class="text-gray-600 rounded-md text-sm z-10 -top-10 relative float-right">
                                                {{ $message->user()->get()[0]->name }},
                                                {{ $message->created_at }}
                                            </p>
                                        </div>
                                    @endforeach
                                    <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                                        @csrf
                                        <div class="ml-10 flex items-baseline ">
                                            <textarea name="message" id="message" cols="30" rows="10"
                                                class="form-input rounded-md shadow-sm mt-1 block w-full"></textarea>
                                        </div>
                                        <div class="ml-10 flex items-baseline p-2">
                                            <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-admin-layout>
