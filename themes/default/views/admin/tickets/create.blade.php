<x-admin-layout>
    <x-slot name="title">
        {{ __('Create Ticket') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext text-center">{{ __('Create Ticket') }}</h1>

    <div class="dark:bg-darkmode2 bg-gray-200 bg-opacity-25 grid grid-cols-1">
        <div class="p-6">
            <div class="flex items-center">
                <div class="dark:text-darkmodetext ml-4 text-lg text-gray-600 leading-7 font-semibold">
                    {{ __('Ticket') }}</div>
            </div>
            <div class="ml-6">
                <div class="dark:text-darkmodetext mt-2 text-sm text-gray-500">
                    {{ __('Create a new ticket.') }}
                </div>
            </div>
            <form method="POST" action="{{ route('admin.tickets.store') }}">
                @csrf
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block font-medium text-sm text-gray-700" for="title"
                        id="textareaId">
                        {{ __('Title') }}
                    </label>
                    <input
                        class="dark:bg-darkmode dark:text-darkmodetext form-input rounded-md shadow-sm mt-1 block w-full"
                        id="title" type="text" name="title" value="{{ old('title') }}" required autofocus />
                </div>
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block font-medium text-sm text-gray-700" for="description"
                        id="textareaId">
                        {{ __('Description') }}
                    </label>
                    <textarea class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                        id="description" name="description" required>{{ old('description') }}</textarea>
                </div>
                <!-- user -->
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block font-medium text-sm text-gray-700" for="user">
                        {{ __('User') }}
                    </label>
                    <select id="user" name="user"
                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if ($user->id == old('user')) selected @endif>
                                {{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- priority high/medium/low -->
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block font-medium text-sm text-gray-700" for="priority">
                        {{ __('Priority') }}
                    </label>
                    <select id="priority" name="priority"
                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
                        <option value="low" @if (old('priority') == 1) selected @endif>
                            {{ __('Low') }}</option>
                        <option value="medium" @if (old('priority') == 2) selected @endif>
                            {{ __('Medium') }}</option>
                        <option value="high" @if (old('priority') == 3) selected @endif>
                            {{ __('High') }}</option>
                    </select>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button id="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
