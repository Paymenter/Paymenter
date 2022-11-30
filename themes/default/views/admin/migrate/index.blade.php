<x-admin-layout>
    <x-slot name="title">
        {{ __('Migrate') }}
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20">
                    <div class="mt-8 text-2xl text-white">
                        {{ __('Migrate') }}
                    </div>
                    <div class="mt-6 mb-6 text-gray-200 my-6">
                        {{ __('Do you want to migrate from WHMCS or Blesta?') }}
                        <div class="float-right">
                            <a href="{{ route('admin.migrate.whmcs') }}" id="form-blesta" class="form-submit">{{ __('WHMCS') }}</a>
                            <a href="{{ route('admin.migrate.blesta') }}" id="form-blesta" class="form-submit">{{ __('Blesta') }}</a>
                        </div>
                    </div>

                    <x-success />
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>