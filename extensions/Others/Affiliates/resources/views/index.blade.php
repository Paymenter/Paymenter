@extends('client.account.wrapper')

@section('content')
  <h4 class="text-2xl font-bold pb-3">{{ __('account.affiliate') }}</h4>
  @isset($affiliate)
    <div class="content-box mt-4 md:grid-cols-3 grid">
      <div class="flex flex-col gap-2">
        <span class="text-xl font-semibold">Visitors</span>
        <span class="text-gray-500">Total visitors</span>
        <span class="text-2xl font-semibold">{{ $affiliate->visitors }}</span>
      </div>
      <div class="flex flex-col gap-2">
        <span class="text-xl font-semibold">Signups</span>
        <span class="text-gray-500">Total signups</span>
        <span class="text-2xl font-semibold">
          {{ $affiliate->referrals->count() }}
        </span>
      </div>
      <div class="flex flex-col gap-2">
        <span class="text-xl font-semibold">Earnings</span>
        <span class="text-gray-500">Total earnings</span>
        <span class="text-2xl font-semibold">
          {{ $affiliate->earnings }}
        </span>
      </div>
      <div class="col-span-3 flex flex-col mt-4">
        <span class="text-xl font-semibold">Affiliate</span>
        <span class="text-gray-500">Your affiliate link</span>
        <div class="flex flex-row gap-2 mt-2" data-protonpass-form="">
          <div class="w-full">

            <div class="relative">
              <input type="text" value="http://paymenter.generic.localhost?ref={{ $referral->code }}" name="ref"
                id="ref"
                class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
px-4              border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 ">
            </div>
          </div>
          <button class="button button-primary w-fit rounded-lg" id="copy"
            onclick="copyToClipboard('http://paymenter.generic.localhost?ref={{ $referral->code }}')">Copy</button>
        </div>
      </div>
    </div>
  @else
    <div class="content-box mt-4 flex flex-col">
      <span class="text-xl font-semibold">{{ __('Affiliate') }}</span>
      <span class="text-gray-500">{{ __('Signup for affiliate') }}</span>
      <form wire:submit="signup" method="POST">

        @if ($signup_type === 'custom')
          <x-form.input name="referral_code" type="text" :label="__('affiliate.code-input-label')" wire:model="referral_code" required />
        @endif

        <x-button.primary type="submit" class="text-sm !w-fit">
          {{ __('affiliate.signup') }}
        </x-button.primary>
      </form>
    </div>
  @endisset
@endsection
