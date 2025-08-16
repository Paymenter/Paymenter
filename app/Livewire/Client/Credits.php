<?php

namespace App\Livewire\Client;

use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Credit;
use App\Models\Gateway;
use App\Models\Invoice;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class Credits extends Component
{
    #[Validate('required|exists:currencies,code')]
    public $currency;

    public $amount;

    #[Locked]
    public $gateways = [];

    public $gateway;

    public function mount()
    {
        if (!config('settings.credits_enabled')) {
            return redirect()->route('account');
        }

        $this->amount = config('settings.credits_minimum_deposit');
        $this->currency = session('currency', config('settings.default_currency'));
        $this->gateways = ExtensionHelper::getCheckoutGateways($this->amount, $this->currency, 'credits');
        if (count($this->gateways) > 0 && !array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
            $this->gateway = $this->gateways[0]->id;
        }
    }

    public function updated($variable)
    {
        if ($variable === 'amount' || $variable === 'currency') {
            $this->gateways = ExtensionHelper::getCheckoutGateways($this->amount, $this->currency, 'credits');
            if (count($this->gateways) > 0 && !array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
                $this->gateway = $this->gateways[0]->id;
            }
        }
    }

    public function addCredit()
    {
        $this->validate([
            'currency' => 'required|exists:currencies,code',
            'amount' => 'required|numeric|min:' . config('settings.credits_minimum_deposit') . '|max:' . config('settings.credits_maximum_deposit'),
            'gateway' => 'required|in:' . implode(',', array_column($this->gateways, 'id')),
        ]);

        if (Auth::user()->credits()->where('currency_code', $this->currency)->exists()) {
            // Check if the current credits + the new credits exceed the maximum credits allowed
            if (Auth::user()->credits()->where('currency_code', $this->currency)->sum('amount') + $this->amount > config('settings.credits_maximum_credit')) {
                $this->notify('You cannot exceed the maximum credits allowed.', 'error');

                return;
            }
        }

        // Create invoice
        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'user_id' => Auth::id(),
                'currency_code' => $this->currency,
                'due_at' => now(),
            ]);

            $invoice->items()->create([
                'description' => __('account.credit_deposit', ['currency' => $this->currency]),
                'quantity' => 1,
                'price' => $this->amount,
                'reference_type' => Credit::class,
            ]);

            DB::commit();

            Session::put(['gateway' => $this->gateway]);

            // Redirect to the invoices page and pay the invoice
            if ($this->gateway) {
                $pay = ExtensionHelper::pay(Gateway::where('id', $this->gateway)->first(), $invoice->fresh());
                if (is_string($pay)) {
                    return $this->redirect($pay);
                }
            }

            return $this->redirect(route('invoices.show', $invoice) . '?gateway=' . $this->gateway . '&pay', true);
        } catch (Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Return error message
            // Is it a real error or a validation error?
            // If it's a validation error, you can use the $this->addError() method to display the error message to the user.
            if ($e instanceof DisplayException) {
                $this->notify($e->getMessage(), 'error');
            } else {
                Log::error($e);
                $this->notify('An error occurred while processing your order. Please try again later.');
            }
            throw $e;
        }
    }

    public function render()
    {
        return view('client.account.credits')->layoutData([
            'sidebar' => true,
        ]);
    }
}
