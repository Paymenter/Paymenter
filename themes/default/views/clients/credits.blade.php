<x-app-layout clients title="{{ __('Credits') }}">
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
                        @if (config('settings::credits'))
                            <a href="{{ route('clients.credits') }}"
                                class="text-secondary-900 pl-3 border-primary-400 border-l-2 duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Credits') }}
                            </a>
                        @endif
                        <a href="{{ route('clients.api.index') }}"
                            class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('Account API') }}
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
                <div class="content-box">
                    <h1 class="text-2xl font-semibold">{{ __('Current Balance') }}</h1>
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <span class="text-2xl font-semibold">
                                <x-money :amount="Auth::user()->credits" />
                            </span>
                        </div>
                    </div>
                </div>

                <div class="content-box mt-4">
                    <h1 class="text-2xl font-semibold">{{ __('Add Credits') }}</h1>
                    <p class="text-gray-500">{{ __('It can take some time to process your payment.') }}</p>
                    <x-success />
                    @if (count($gateways) == 0)
                        <div class="alert alert-warning">
                            {{ __('No payment gateway found.') }}
                        </div>
                    @else
                        <form action="{{ route('clients.credits.add') }}" method="POST">
                            @csrf
                            <div class="flex flex-row items-center justify-between">
                                <x-input type="text" class="flex-1" placeholder="{{ __('Amount') }}" name="amount"
                                    id="amount" label="{{ __('Amount') }}" />
                                <x-input type="select" class="flex-1 ml-2" placeholder="{{ __('Payment Method') }}"
                                    id="gateway_id" name="gateway" label="{{ __('Payment Method') }}">
                                    <option value="" selected disabled>{{ __('Select Gateway') }}</option>
                                    @foreach ($gateways as $gateway)
                                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                                    @endforeach
                                </x-input>
                                <button class="button button-primary mt-6 flex w-fit h-fit ml-3">
                                    {{ __('Add') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="content-box mt-4">
                    <h1 class="text-2xl font-semibold">{{ __('History of the payments') }}</h1>

                    <table class="w-full pt-2">
                        <thead class="border-b-2 border-secondary-200 dark:border-secondary-200 text-secondary-600">
                        <tr>
                            <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">{{ __('Date') }}</th>
                            <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">{{ __('Amount') }}</th>
                            <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">{{ __('Type') }}</th>
                            <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($userInvoices as $invoice)
                            <tr>
                                <td class="pl-6 py-3 items-center break-all max-w-fit">{{ $invoice->created_at }}</td>
                                <td class="pl-6 py-3 items-center break-all max-w-fit">@if($invoice->credits > 0) + @else @if($invoice->paid_with == 'manual') - @endif @endif <x-money :amount="$invoice->total() + $invoice->credits" /> </td>
                                <td class="pl-6 py-3 items-center break-all max-w-fit">
                                    @if($invoice->credits > 0)
                                        {{ __('Charge') }}
                                    @else
                                        {{ __('Invoice') }} #{{ $invoice->id }}
                                        @if($invoice->paid_with !== 'manual' && $invoice->paid_with !== "unknown") ({{ $invoice->paid_with }}) @endif
                                    @endif

                                </td>
                                <td class="pl-6 py-3 items-center break-all max-w-fit">
                                    @if ($invoice->status == 'pending')
                                        <span class="text-yellow-400 dark:text-yellow-200">{{ __('Pending') }}</span>
                                    @elseif($invoice->status == 'paid')
                                        <span class="text-success-400 dark:text-success-200">{{ __('Completed') }}</span>
                                    @elseif($invoice->status == 'failed')
                                        <span class="text-danger-400 dark:text-danger-200">{{ __('Failed') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">{{ __('No records found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
