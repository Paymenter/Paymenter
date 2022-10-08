<x-admin-layout>
    <x-slot name="title">
        {{ __('Edit') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Edit {{ $extension->name }}
                    </div>
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <div class="mt-6 text-gray-500">
                        <form method="POST" action="{{ route('admin.extensions.update')}}">
                            @csrf
                            @method('PUT')
                            @foreach ($extension->serverConfig as $setting)
                                <div class="mt-4">
                                    <label for="{{ $setting->Name }}">{{ $setting->FriendlyName }}</label>
                                    <input id="{{ $setting->Name }}" class="block mt-1 w-full" type="text"
                                        name="{{ $setting->Name }}" required />
                                </div>
                            @endforeach
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
