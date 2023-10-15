<x-admin-layout title="Editing {{ $role->name }} role">
    <h1 class="text-center text-2xl font-bold">{{ __('Editing') }} {{ $role->name }} {{ __('role') }}</h1>
    @if($role->name == 'admin' || $role->name == 'user') 
        <h4 class="text-center text-2xl font-bold text-danger-400">{{ __('You cannot edit this role, this is the default role for') }} {{ $role->name }}'s</h4>
    @endif
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        <label for="name">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" class="form-input" value="{{ $role->name }}" @if($role->name == 'admin' || $role->name == 'user') disabled @endif />
        <label for="permissions">{{ __('Permissions') }}</label>
        <div class="grid grid-cols-5 gap-4">
            @foreach ($permissions as $permission => $value)
                        @if($permission == 'ADMINISTRATOR') @continue @endif

                <div>
                    <x-input type="checkbox" name="permissions[]" id="permissions" value="{{ $permission }}"
                        label="{{ $permission }}" :disabled="$role->name == 'admin' || $role->name == 'user'" :checked="$role->has($permission)" />
                </div>
            @endforeach
        </div>
        <button type="submit" class="button button-primary" @if($role->name == 'admin' || $role->name == 'user') disabled @endif>
            {{ __('Save') }}
        </button>
    </form>
</x-admin-layout>
