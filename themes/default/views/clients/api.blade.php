<x-app-layout>
    <x-slot name="title">
        API Tokens
    </x-slot>
    
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 flex flex-col">
            <x-success/>

            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 mt-4">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Create API Token</h1>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <form method="POST" action="{{ route('clients.api.create') }}">
                                @csrf
                                <div class="mt-4">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input id="name"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode dark:border-black"
                                        name="name" required type="text" placeholder="Token Name">
                                </div>

                                <h2 class='mb-2 mt-4'>Permissions</h2>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($permissions as $permission)
                                    <div class="flex">
                                        <input name='permissions[{{ $permission }}]' type='checkbox' class="text-indigo-600 my-auto dark:border-black border-gray-300 rounded shadow-sm dark:bg-darkmode focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                                        <label class='ml-2 my-auto block' for='{{ $permission }}'>{{ $permission }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit"
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 mt-8">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Manage API Tokens</h1>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <div class="grid grid-cols-1 gap-2">
                                @if ($tokens->isEmpty())
                                <label class='text-center'>You have not tokens created yet!</label>
                                @endif

                                @foreach($tokens as $token)
                                <form method="POST" action="{{ route('clients.api.delete', $token->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="flex items-center justify-between p-4 dark:bg-darkmode border bg-gray-50 rounded dark:border-black">
                                        <div class="break-all dark:text-white">
                                            {{ $token->name }}
                                        </div>

                                        <div class="flex items-center ml-2">
                                            @if ($token->last_used_at)
                                                <div class="text-sm text-gray-400">
                                                    {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                                </div>
                                            @endif

                                            <button class="cursor-pointer ml-6 text-sm text-red-500" type="submit">
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
