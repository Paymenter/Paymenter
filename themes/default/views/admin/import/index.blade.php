<x-admin-layout>
    <x-slot name="title">
        {{ __('Import') }}
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        {{ __('Import') }}
                    </div>
                    <div class="mt-6 text-gray-500">
                        <form method="POST" action="{{ route('admin.import.import') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- define url and api secret and api key -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Url') }}
                                </label>
                                <input id="url" type="text"
                                    class="form-input w-full @error('url') border-red-500 @enderror" name="url"
                                    value="{{ old('url') }}" required autocomplete="url" autofocus>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Identifier') }}
                                </label>
                                <input id="identifier" type="text"
                                    class="form-input w-full @error('identifier') border-red-500 @enderror"
                                    name="identifier" value="{{ old('identifier') }}" required autocomplete="identifier"
                                    autofocus>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Secret') }}
                                </label>
                                <input id="secret" type="text"
                                    class="form-input w-full @error('secret') border-red-500 @enderror" name="secret"
                                    value="{{ old('secret') }}" required autocomplete="secret" autofocus>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Import') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
