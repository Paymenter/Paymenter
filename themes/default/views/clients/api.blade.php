<x-app-layout clients title="{{ __('API') }}">
    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
                    <h2 class="text-xl font-semibold">{{ __("Profile Settings") }}</h2>
                </div>
            </div>
            <div class="lg:col-span-3 col-span-12">
                <div class="content-box">
                    <div class="flex gap-x-2 items-center">
                        <div class="bg-primary-400 w-8 h-8 flex items-center justify-center rounded-md text-gray-50 text-xl">
                            <i class="ri-account-circle-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">{{ __("My Account") }}</h3>
                    </div>
                    <div class="flex flex-col gap-2 mt-2">
                        <a href="{{ route('clients.profile') }}" class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __("My Details") }}
                        </a>
                        @if(config('settings::credits'))
                            <a href="{{ route('clients.credits') }}" class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __("Credits") }}
                            </a>
                        @endif
                        <a href="{{ route('clients.api.index') }}" class="text-secondary-900 pl-3 border-primary-400 border-l-2 duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __("Account API") }}
                        </a>
                        @if (config('settings::affiliate'))
                            <a href="{{ route('clients.affiliate') }}"
                               class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Affiliate') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div>
                    <x-success/>

                    <div class="content-box">
                        <h1 class="text-xl">Create API Token</h1>
                        <form method="POST" action="{{ route('clients.api.create') }}">
                            @csrf
                            <x-input
                                type="text"
                                class="mt-4"
                                placeholder="{{ __('Token') }}"
                                name="name"
                                id="name"
                                label="{{ __('Token Name') }}"
                            />
                            <div class="grid grid-cols-4 gap-2 mt-4">
                                @foreach($permissions as $permission)
                                    <x-input
                                        type="checkbox"
                                        name="permissions[{{ $permission }}]"
                                        id="{{ $permission }}"
                                        label="{{ $permission }}"
                                    />
                                @endforeach
                            </div>
                            <button type="submit" class="button button-primary mt-6">
                                {{ __('Create') }}
                            </button>
                        </form>
                    </div>
                    <div class="content-box mt-4">
                        <h1 class="text-xl">Manage API Token</h1>
                        @if ($tokens->isEmpty())
                            <label class='text-center'>You have not tokens created yet!</label>
                        @endif

                        @foreach($tokens as $token)
                            <form method="POST" action="{{ route('clients.api.delete', $token->id) }}">
                                @csrf
                                @method('DELETE')
                                <div class="w-full flex justify-between items-center px-4 py-3 bg-secondary-200 border border-secondary-300 rounded-md mt-2">
                                    <p class="font-semibold text-lg">{{ $token->name }}</p>
                                    @if ($token->last_used_at)
                                        <div class="text-sm">
                                            {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                        </div>
                                    @endif
                                    <button class="button button-danger" type="submit">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
