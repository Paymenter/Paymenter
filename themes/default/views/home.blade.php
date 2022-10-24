<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>

    <div class="dark:bg-darkmode py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 p-4 dark:bg-darkmode2 overflow-hidden bg-white rounded-lg">

                <!-- show the user services and products -->
                <div class="dark:bg-darkmode2 p-6 bg-white col-span-2">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="dark:bg-white overflow-hidden rounded-lg">
                                    @if (!empty($services))
                                        <div class="dark:bg-darkmode px-4 py-5 sm:px-6">
                                            <h3 class="dark:text-darkmodetext text-lg leading-6 font-medium text-gray-900">
                                                {{ __('Services') }}
                                            </h3>
                                            <p class="dark:text-darkmodetext mt-1 max-w-2xl text-sm text-gray-500">
                                                {{ __('You have no services yet.') }} <a href=""
                                                    class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Create one') }}</a>
                                            </p>
                                        </div>
                                    @else
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Name
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Description
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Price
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Edit</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($services as $service)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="float-left flex-shrink-0 h-10 w-10">
                                                                    <img class=" h-10 w-10 rounded-full"
                                                                        src="{{ $service->image }}" alt="">
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="text-sm font-medium text-gray-900">
                                                                        {{ $service->name }}
                                                                    </div>
                                                                    <div class="text-sm text-gray-500">
                                                                        {{ $service->description }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">
                                                                {{ $service->description }}
                                                            </div>

                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $service->price }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $service->status }}
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a href="{{ route('services.edit', $service->id) }}"
                                                                class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- More items... -->
                                            </tbody>
                                        </table>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- show the user tickets and more -->
                <div class="dark:bg-darkmode dark:border-darkmode p-10 bg-white border-2 rounded-xl border-grey-600 ml-4">
                    <h1 class="dark:text-darkmodetext text-xl text-gray-500">Created tickets</h1>
                    <div class="grid grid-cols-1 gap-4">
                    @foreach(App\Models\Tickets::all()->take(3) as $ticket)
                        @if ($ticket->client == Auth::user()->id)
                        @if ($ticket->status == 'open')
                        <a href="tickets/{{$ticket->id}}">   
                        <div class="dark:hover:bg-darkbutton dark:bg-darkmode2 bg-normal rounded-md p-2">
                                <h1 class="dark:text-darkmodetext text-xl text-gray-500">Ticket #{{$ticket->id}}</h1>
                                <p class="dark:text-darkmodetext text-black font-bold text-2xl">{{ $ticket->title }} 
                                    @if($ticket->priority == 'high')
                                        <span class="bg-red-500 text-white rounded-full p-1 text-base">High</span>
                                    @elseif($ticket->priority == 'medium')
                                        <span class="bg-yellow-500 text-white rounded-full p-1 text-base" >Medium</span>
                                    @elseif($ticket->priority == 'low')
                                        <span class="bg-green-500 text-white rounded-full p-1 text-base">Low</span>
                                    @endif
                                </p>
                            </div>
                        </a> 
                        @endif
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
