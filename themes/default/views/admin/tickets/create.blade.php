<x-admin-layout>
    <x-slot name="title">
        {{ __('Create Ticket') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext text-center">{{ __('Create Ticket') }}</h1>

    <div class="dark:bg-secondary-100 bg-gray-200 bg-opacity-25 grid grid-cols-1">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.tickets.store') }}">
                @csrf
                <x-input :label="__('Title')" type="text" name="title" value="{{ old('title') }}" required autofocus
                    class="mt-4" icon="ri-pencil-line" />

                <x-input :label="__('Description')" type="textarea" name="description" class="mt-4" required>
                    {{ old('description') }}
                </x-input>

                <x-input type="searchselect" id="user" name="user" :label="__('User')" icon="ri-user-line"
                    class="mt-4">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if ($user->id == old('user')) selected @endif>
                            {{ $user->name }}</option>
                    @endforeach
                </x-input>

                <x-input type="select" id="priority" name="priority" :label="__('Priority')" icon="ri-bar-chart-line"
                    class="mt-4">
                    <option value="low" @if (old('priority') == 1) selected @endif>
                        {{ __('Low') }}</option>
                    <option value="medium" @if (old('priority') == 2) selected @endif>
                        {{ __('Medium') }}</option>
                    <option value="high" @if (old('priority') == 3) selected @endif>
                        {{ __('High') }}</option>
                </x-input>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
