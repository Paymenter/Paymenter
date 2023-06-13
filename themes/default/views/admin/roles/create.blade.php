<x-admin-layout title="Creating a new role">
    <h1 class="text-center text-2xl font-bold">{{ __('Creating a new role') }}</h1>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <label for="name">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" />
        <label for="permissions">{{ __('Permissions') }}</label>
        <div class="grid grid-cols-5 gap-4">
            @foreach ($permissions as $permission => $value)
            @if($permission == 'ADMINISTRATOR') @continue @endif
                <div>
                    <x-input type="checkbox" name="permissions[]" id="permissions" value="{{ $permission }}"
                        label="{{ $permission }}" :checked="old('permissions') ? in_array($permission, old('permissions')) : null" />
                </div>
            @endforeach
        </div>
        <button type="submit" class="button button-primary">
            {{ __('Save') }}
        </button>
    </form>
</x-admin-layout>
