<?php

namespace App\Livewire\Invoices;

use App\Classes\PDF;
use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Credit;
use App\Models\Gateway;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class Show extends Component
{
    #[Locked]
    public Invoice $invoice;

    #[Url]
    public $gateway = null;

    #[Locked]
    public $gateways;

    public $checkPayment = false;

    private $pay = null;

    public $use_credits = false;

    public function mount()
    {
        $sessionGateway = session('gateway');
        if ($sessionGateway) {
            $this->gateway = $sessionGateway;
        }

        if ($this->invoice->status === 'pending') {
            $this->gateways = ExtensionHelper::getCheckoutGateways($this->invoice->total, $this->invoice->currency_code, 'invoice', $this->invoice->items);
            if (count($this->gateways) > 0 && !array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
                $this->gateway = $this->gateways[0]->id;
            }
            if ($sessionGateway && Request::has('pay')) {
                $this->pay();
            }
        }
        if (Request::has('checkPayment') && $this->invoice->status === 'pending') {
            $this->checkPayment = true;
        }

        // We don't want to toggle use_credits before $this->pay() is called, otherwise it will always paid with credits
        $this->use_credits = true;
        $hasCredits = $this->invoice->items()->where('reference_type', Credit::class)->exists();
        if ($hasCredits) {
            $this->use_credits = false;
        }
    }

    public function pay()
    {
        if ($this->use_credits) {
            $credit = Auth::user()->credits()->where('currency_code', $this->invoice->currency_code)->first();
            if ($credit && $credit->amount > 0) {
                // Is it more credits or less credits than the total price?
                if ($credit->amount >= $this->invoice->remaining) {
                    $credit->amount -= $this->invoice->remaining;
                    $credit->save();
                    ExtensionHelper::addPayment($this->invoice->id, null, amount: $this->invoice->remaining);

                    return $this->redirect(route('invoices.show', $this->invoice), true);
                } else {
                    ExtensionHelper::addPayment($this->invoice->id, null, amount: $credit->amount);
                    $credit->amount = 0;
                    $credit->save();

                    $this->invoice = $this->invoice->fresh();
                }
            }
        }

        if ($this->invoice->status !== 'pending') {
            return $this->notify(__('This invoice cannot be paid.'), 'error');
        }
        if ($this->checkPayment) {
            $this->checkPayment = false;
        }
        $this->validate([
            'gateway' => 'required',
        ]);

        $this->pay = ExtensionHelper::pay(Gateway::where('id', $this->gateway)->first(), $this->invoice);

        if (is_string($this->pay)) {
            $this->redirect($this->pay);
        }
    }

    public function exitPay()
    {
        $this->pay = null;
        // Dispatch event so extensions can do their thing
        $this->dispatch('invoice.payment.cancelled', $this->invoice);
        // Refresh invoice status
        $this->redirect(route('invoices.show', $this->invoice), true);
    }

    public function checkPaymentStatus()
    {
        $this->invoice->refresh();
        if ($this->invoice->status === 'paid') {
            $this->notify(__('The invoice has been paid.'), 'success');
            $this->checkPayment = false;
        }
    }

    public function render()
    {
        return view('invoices.show')->layoutData([
            'title' => __('invoices.invoice', ['id' => $this->invoice->number]),
            'sidebar' => true,
        ]);
    }

    public function downloadPDF()
    {
        return response()->streamDownload(function () {
            echo PDF::generateInvoice($this->invoice)->stream();
        }, 'invoice-' . $this->invoice->number . '.pdf');
    }
}
