<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use Carbon\Carbon;

class Upgrade extends Component
{
    public Service $service;

    public $upgrade;

    public Product $upgradeProduct;

    public function mount()
    {
        $this->authorize('view', $this->service);

        if (!$this->service->upgradable) {
            $this->notify('This service is not upgradable.', 'error');

            return $this->redirect(route('services.show', $this->service), true);
        }
        $upgrade = $this->service->upgrades()->first();
        $this->upgradeProduct = $upgrade;
        $this->upgrade = $upgrade->id;
        $this->totalToday();
    }

    private function totalToday()
    {
        $upgrade = new ServiceUpgrade([
            'service' => $this->service,
            'product' => $this->upgradeProduct,
        ]);

        return $upgrade->calculatePrice();
    }

    // When upgrade changes, update the upgradeProduct
    public function updatedUpgrade($upgrade)
    {
        // Check if the upgrade is valid
        if (!$this->service->upgrades()->contains($upgrade)) {
            $this->notify('Invalid upgrade.', 'error');

            return;
        }
        $this->upgradeProduct = Product::findOrFail($upgrade);
    }

    public function doUpgrade()
    {
        if (!$this->service->upgradable) {
            $this->notify('This service is not upgradable.', 'error');

            return $this->redirect(route('services.show', $this->service), true);
        }

        $upgradePlan = $this->upgradeProduct->availablePlans()->where('billing_period', $this->service->plan->billing_period)->where('billing_unit', $this->service->plan->billing_unit)->first();

        if (!$upgradePlan) {
            $this->notify('Invalid upgrade.', 'error');

            return;
        }

        $upgrade = new ServiceUpgrade([
            'service_id' => $this->service->id,
            'product_id' => $this->upgradeProduct->id,
            'plan_id' => $upgradePlan->id,
        ]);

        $price = $upgrade->calculatePrice();
        if ($price->price <= 0) {
            $upgrade->status = ServiceUpgrade::STATUS_COMPLETED;
            $upgrade->save();

            if ($price->price < 0) {
                $this->notify('The upgrade has been completed. We\'ve added the remaining amount to your account balance.', 'success');
            } else {
                $this->notify('The upgrade has been completed.', 'success');
            }

            return;
        }

        $invoice = new Invoice([
            'order' => $this->service->order,
            'currency_code' => $this->service->order->currency_code,
            'total' => $price->price,
            'status' => Invoice::STATUS_PENDING,
            'due_at' => Carbon::now()->addDays(7),
            'user_id' => $this->service->order->user_id,
        ]);
        $invoice->save();

        $invoice->items()->create([
            'description' => 'Upgrade ' . $this->service->product->name . ' to ' . $this->upgradeProduct->name,
            'price' => $price->price,
            'quantity' => 1,
            'relation_id' => $upgrade->id,
            'relation_type' => ServiceUpgrade::class,
        ]);

        $upgrade->invoice_id = $invoice->id;
        $upgrade->save();

        $this->notify('The upgrade has been added to your cart. Please complete the payment to proceed.', 'success');

        return $this->redirect(route('invoices.show', $invoice));
    }

    public function render()
    {
        return view('services.upgrade')->layoutData([
            'title' => 'Upgrade Service',
        ]);
    }
}
