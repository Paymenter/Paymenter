<x-admin-layout>
    <x-slot name="title">
        {{ __('Create Ticket') }}
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-success class="mb-4" />
                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">{{ __('Ticket') }}</div>
                        </div>
                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">
                                {{ __('Create a new ticket.') }}
                            </div>
                            <form method="POST" action="{{ route('admin.tickets.store') }}">
                                @csrf
                                <div class="mt-4">
                                    <label class="block font-medium text-sm text-gray-700" for="title">
                                        {{ __('Title') }}
                                    </label>
                                    <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="title"
                                        type="text" name="title" value="{{ old('title') }}" required autofocus />
                                </div>
                                <div class="mt-4">
                                    <label class="block font-medium text-sm text-gray-700" for="description">
                                        {{ __('normal.description') }}
                                    </label>
                                    <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="description" name="description" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="flex items-center justify-end mt-4">
                                    <button class="ml-4">
                                        {{ __('normal.create') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
