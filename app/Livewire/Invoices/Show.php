<?php

namespace App\Livewire\Invoices;

use App\Classes\PDF;
use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Gateway;
use App\Models\Invoice;
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

    public function mount()
    {
        $sessionGateway = session('gateway');
        if ($sessionGateway) {
            $this->gateway = $sessionGateway;
        }

        if ($this->invoice->status === 'pending') {
            $this->gateways = ExtensionHelper::getCheckoutGateways($this->invoice->products, 'invoice');
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
    }

    public function pay()
    {
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
        $this->redirect(route('invoices.show', $this->invoice->id), true);
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
            'title' => __('invoices.invoice', ['id' => $this->invoice->id]),
            'sidebar' => true,
        ]);
    }

    public function downloadPDF()
    {
        return response()->streamDownload(function () {
            echo PDF::generateInvoice($this->invoice)->stream();
        }, 'invoice-' . $this->invoice->id . '.pdf');
    }
}
