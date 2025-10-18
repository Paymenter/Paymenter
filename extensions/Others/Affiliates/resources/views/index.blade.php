<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2">

        @isset($affiliate)
            <div class="mt-4 md:grid-cols-3 grid gap-4">
                <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                    <span class="text-xl font-semibold">{{ __('affiliates::affiliate.visitors') }}</span>
                    <span class="text-gray-500">{{ __('affiliates::affiliate.total-visitors') }}</span>
                    <span class="text-2xl font-semibold mt-1">{{ Number::format($affiliate->visitors) }}</span>
                </div>
                <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                    <span class="text-xl font-semibold">{{ __('affiliates::affiliate.signups') }}</span>
                    <span class="text-gray-500">{{ __('affiliates::affiliate.total-signups') }}</span>
                    <span class="text-2xl font-semibold mt-1">
                        {{ Number::format($affiliate->signups) }}
                    </span>
                </div>
                <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                    <span class="text-xl font-semibold">{{ __('affiliates::affiliate.earnings') }}</span>
                    <span class="text-gray-500">{{ __('affiliates::affiliate.total-earnings') }}</span>
                    <span class="text-2xl font-semibold mt-1">
                        <ul>
                            @foreach ($affiliate->earnings as $currency => $amount)
                                <li>
                                    {{ $currency }}: {{ $amount }}
                                </li>
                            @endforeach
                        </ul>
                    </span>
                </div>
                <div class="col-span-3 flex flex-col mt-4">
                    <span class="text-xl font-semibold">{{ __('affiliates::affiliate.affiliate') }}</span>
                    <span class="text-gray-500">{{ __('affiliates::affiliate.your-affiliate-link') }}</span>
                    <div class="flex flex-row gap-2 mt-2">
                        <x-form.input value="{{ url('/') }}?ref={{ $affiliate->code }}" name="ref"
                            divClass="!mt-0" type="text" readonly />

                        <x-button.primary class="!w-fit" type="button"
                            onclick="copyToClipboard('{{ url('/?ref=' . $affiliate->code) }}')">{{ __('affiliates::affiliate.copy') }}</x-button.primary>
                    </div>
                </div>
            </div>
            <script>
                function copyToClipboard(textToCopy) {
                    const temp = document.createElement("input")
                    temp.type = "text"
                    temp.value = textToCopy

                    document.body.appendChild(temp)
                    temp.select()
                    document.execCommand("Copy")
                    document.body.removeChild(temp)
                }
            </script>
        @else
            <p class="mb-4">{{ __('affiliates::affiliate.you-havent-signed-up-yet') }}</p>
            <h3 class="text-lg font-bold mb-4">{{ __('affiliates::affiliate.signup-for-affiliate') }}</h3>
            <form wire:submit.prevent="signup" method="POST">

                @if ($signup_type === 'custom')
                    <x-form.input name="referral_code" type="text" :label="__('affiliates::affiliate.code')" wire:model="referral_code"
                        required />
                @endif

                <x-button.primary type="submit" class="text-sm !w-fit mt-4">
                    {{ __('auth.sign_up') }}
                </x-button.primary>
            </form>
        @endisset
    </div>

</div>
