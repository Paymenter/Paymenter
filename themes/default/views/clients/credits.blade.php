<x-app-layout clients title="{{ __('Credits') }}">

    <x-success />

    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
                    <h2 class="text-xl font-semibold">{{ __('Credits') }}</h2>
                </div>
            </div>
            <div class="lg:col-span-3 col-span-12">
                <div class="content-box">
                    <div class="flex gap-x-2 items-center">
                        <div
                            class="bg-primary-400 w-8 h-8 flex items-center justify-center rounded-md text-gray-50 text-xl">
                            <i class="ri-account-circle-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">{{ __('My Account') }}</h3>
                    </div>
                    <div class="flex flex-col gap-2 mt-2">
                        <a href="{{ route('clients.profile') }}"
                            class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('My Details') }}
                        </a>
                        <a href="{{ route('clients.credits') }}"
                            class="text-secondary-900 pl-3 border-primary-400 border-l-2 duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('Credits') }}
                        </a>
                        <a href="{{ route('clients.api.index') }}"
                            class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('Account API') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div class="content-box">
                    <h1 class="text-2xl font-semibold">{{ __('Add Credits') }}</h1>
                    <x-success />
                    <div class="flex flex-row items-center">
                    <x-input type="text" class="mt-4 flex-1" placeholder="{{ __('Amount') }}" name="amount" id="amount"
                        label="{{ __('Amount') }}" />
    
                        <button class="button button-primary mt-6 flex w-fit h-fit ml-3">
                            {{ __('Add') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
