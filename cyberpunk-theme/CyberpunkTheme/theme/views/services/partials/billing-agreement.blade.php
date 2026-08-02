@if($service->plan->type == 'recurring' && $service->status == 'active' && Auth::user()->billingAgreements->count() > 0)
<div class="flex items-center text-base">
    <!-- Auto paying using -->
    <span class="mr-2">{{ __(key: 'services.auto_pay') }}:</span>
    <span class="text-base/50">{{ $service->billingAgreement ? $service->billingAgreement->name :
        __('services.auto_pay_not_configured') }}
        <!-- Edit icon -->
        <button wire:click="$set('showBillingAgreement', true)" class="cursor-pointer">
            <x-ri-edit-line class="inline size-4 ml-1 text-base/50" />
        </button>
        @if($service->billingAgreement)
        <button  x-on:click="$store.confirmation.confirm({
                                title: '{{ __('services.remove_payment_method') }}',
                                message: '{{ __('services.remove_payment_method_confirm', ['name' => $service->billingAgreement->name]) }}',
                                confirmText: '{{ __('common.confirm') }}',
                                cancelText: '{{ __('common.cancel') }}',
                                callback: () => $wire.clearBillingAgreement()
                            })" class="cursor-pointer">
            <x-ri-close-line class="inline size-4 ml-1 text-base/50" />
        </button>
        @endif
    </span>
</div>
@if($showBillingAgreement)
<x-modal :title="__('services.select_billing_agreement')" open="{{ $showBillingAgreement }}">
    <x-slot name="closeTrigger">
        <div class="flex gap-4">
            <button wire:click="$set('showBillingAgreement', false)" @click="open = false" class="text-primary-100">
                <x-ri-close-fill class="size-6" />
            </button>
        </div>
    </x-slot>
    <div class="space-y-4">
        @foreach(Auth::user()->billingAgreements as $agreement)
        <div wire:click="$set('selectedMethod', '{{ $agreement->ulid }}')"
            class="flex items-center justify-between p-4 bg-background-secondary border rounded-lg cursor-pointer transition-all
                    {{ $selectedMethod === $agreement->ulid ? 'border-primary ring-2 ring-primary' : 'border-neutral hover:border-neutral-focus' }}"
            wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedMethod,processPayment">
            <div class="flex items-center gap-4">
                <div>
                    @switch(strtolower($agreement->type))
                    @case('visa')
                    <x-icons.visa class="size-9" /> @break
                    @case('mastercard')
                    <x-icons.mastercard class="size-9" /> @break
                    @case('amex')
                    <x-icons.amex class="size-9" /> @break
                    @case('american express')
                    <x-icons.american-express class="size-9" /> @break
                    @case('discover')
                    <x-icons.discover class="size-9" /> @break
                    @case('paypal')
                    <x-icons.paypal class="size-9" /> @break
                    @case('sepa_debit')
                    <x-icons.sepa class="size-9" /> @break
                    @case('ideal')
                    <x-icons.ideal class="size-9" /> @break
                    @case('bancontact')
                    <x-icons.bancontact class="size-9" /> @break
                    @case('sofort')
                    <x-icons.sofort class="size-9" /> @break
                    @case('us_bank_account')
                    @case('bacs_debit')
                    @case('au_becs_debit')
                    <x-icons.bank-debit class="size-9" /> @break
                    @default
                    <x-ri-bank-card-line class="size-6 text-primary" />
                    @endswitch
                </div>
                <div>
                    <div class="font-semibold text-base"> {{ $agreement->name }} </div>
                    @if($agreement->expiry)
                    <div class="text-sm text-neutral-500">
                        Expires: {{ \Carbon\Carbon::parse($agreement->expiry)->format('m/Y') }}
                    </div>
                    @endif
                </div>
            </div>
            <div
                class="size-5 rounded-full border border-neutral flex items-center justify-center
                                    {{ $selectedMethod === $agreement->ulid ? 'border-primary bg-primary' : 'border-neutral-focus' }}">
                @if($selectedMethod === $agreement->ulid)
                <x-ri-check-line class="size-4 text-white" /> @endif
            </div>
        </div>
        @endforeach

        <div class="flex justify-end">
            <x-button.primary wire:click="updateBillingAgreement" wire:loading.attr="disabled">
                {{ __('services.update_billing_agreement') }}
            </x-button.primary>
        </div>
    </div>

</x-modal>
@endif
@endif