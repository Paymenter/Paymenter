<x-app-layout clients title="{{ __('Affiliate') }}">
    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
                    <h2 class="text-xl font-semibold">{{ __('Affiliate') }}</h2>
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
                        @if (config('settings::credits'))
                            <a href="{{ route('clients.credits') }}"
                                class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Credits') }}
                            </a>
                        @endif
                        <a href="{{ route('clients.api.index') }}"
                            class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('Account API') }}
                        </a>
                        @if (config('settings::affiliate'))
                            <a href="{{ route('clients.affiliate') }}"
                                class="text-secondary-900 pl-3 border-primary-400 border-l-2 duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Affiliate') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div class="content-box">
                    <h1 class="text-2xl font-semibold">{{ __('Affiliate') }}</h1>
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <span class="text-xl text-gray-500">
                                {{ __('Earn money with our affiliate program') }}
                            </span>
                        </div>
                    </div>
                </div>

                <x-success />
                @isset($affiliate)
                    <div class="content-box mt-4 md:grid-cols-3 grid">
                        <!-- Visitors -->
                        <div class="flex flex-col gap-2">
                            <span class="text-xl font-semibold">{{ __('Visitors') }}</span>
                            <span class="text-gray-500">{{ __('Total visitors') }}</span>
                            <span class="text-2xl font-semibold">{{ $affiliate->visitors }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-xl font-semibold">{{ __('Signups') }}</span>
                            <span class="text-gray-500">{{ __('Total signups') }}</span>
                            <span class="text-2xl font-semibold">{{ $affiliate->affiliateUsers->count() }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-xl font-semibold">{{ __('Earnings') }}</span>
                            <span class="text-gray-500">{{ __('Total earnings') }}</span>
                            <span class="text-2xl font-semibold">
                                <x-money :amount="$affiliate->earnings()" />
                            </span>
                        </div>
                        <div class="col-span-3 flex flex-col mt-4">
                            <span class="text-xl font-semibold">{{ __('Affiliate') }}</span>
                            <span class="text-gray-500">{{ __('Your affiliate link') }}</span>
                            <div class="flex flex-row gap-2 mt-2">
                                <x-input type="text" value="{{ url('/') }}?ref={{ $affiliate->code }}" name="ref" class="w-full"/>
                                <button class="button button-primary w-fit rounded-lg" id="copy"
                                    onclick="copyToClipboard('{{ url('/?ref=' . $affiliate->code) }}')">{{ __('Copy') }}</button>
                            </div>
                        </div>
                    </div>
                    <script>
                        function copyToClipboard(text) {
                            var $temp = $("<input>");
                            $("body").append($temp);
                            $temp.val(text).select();
                            document.execCommand("copy");
                            $temp.remove();

                            $('#copy').html('<i class="ri-check-line"></i>');
                            setTimeout(function() {
                                $('#copy').html('{{ __('Copy') }}');
                            }, 1500);
                        }
                    </script>
                @else
                    <div class="content-box mt-4 flex flex-col">
                        <span class="text-xl font-semibold">{{ __('Affiliate') }}</span>
                        <span class="text-gray-500">{{ __('Signup for affiliate') }}</span>
                        <form action="{{ route('clients.affiliate.store') }}" method="POST">
                            @csrf
                            @if (config('settings::affiliate_type') == 'custom')
                                <x-input type="text" name="code" :label="__('Affiliate Code')" required class="mt-2 w-full" />
                            @endif
                            <button type="submit" class="button button-primary w-fit mt-4">{{ __('Signup') }}</button>
                        </form>
                    </div>
                @endisset

            </div>
        </div>
    </div>
</x-app-layout>
