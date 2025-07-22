<?php

namespace App\Livewire\Services;

use App\Classes\Price;
use App\Events\Invoice\Created as InvoiceCreated;
use App\Jobs\Server\UpgradeJob;
use App\Livewire\Component;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class Upgrade extends Component
{
    public Service $service;

    public $upgrade;

    public Product $upgradeProduct;

    public int $step = 1;

    public $configOptions = [];

    public function mount()
    {
        $this->authorize('view', $this->service);

        if (!$this->service->upgradable) {
            $this->notify('This service is not upgradable.', 'error');

            return $this->redirect(route('services.show', $this->service), true);
        }
        $this->upgradeProduct = $this->service->product;
        $this->upgrade = $this->service->product->id;
        $this->totalToday();

        // We only have upgrabble config options if the product has any
        if ($this->service->productUpgrades()->count() === 0) {
            $this->nextStep();
        }
    }

    #[Computed]
    public function totalToday()
    {
        $upgrade = new ServiceUpgrade([
            'service' => $this->service,
            'product' => $this->upgradeProduct,
        ]);

        $total = $upgrade->calculateProratedAmount(
            $this->service->product,
            $this->upgradeProduct
        )->price;

        // Calculate prices for config options
        foreach ($this->configOptions as $optionId => $value) {
            $option = $this->upgradeProduct->upgradableConfigOptions->where('id', $optionId)->first();
            if (!$option || !$option->children->contains('id', $value)) {
                continue;
            }

            $oldPrice = $this->service->configs->where('config_option_id', $optionId)->first();

            $ctotal = $upgrade->calculateProratedAmount(
                $oldPrice ? $oldPrice->configValue : null,
                $option->children->find($value)
            );
            $total += $ctotal->price;
        }

        return new Price([
            'price' => $total,
            'currency' => $this->service->currency,
        ]);
    }

    // When upgrade changes, update the upgradeProduct
    public function updatedUpgrade($upgrade)
    {
        // Check if the upgrade is valid
        if (!$this->service->productUpgrades()->contains($upgrade) && $upgrade != $this->service->product_id) {
            $this->notify('Invalid upgrade.', 'error');

            return;
        }
        $this->upgradeProduct = Product::findOrFail($upgrade);
    }

    public function nextStep()
    {
        $currentConfigOptions = $this->service->configs->pluck('config_value_id', 'config_option_id')->toArray();
        $this->configOptions = $this->upgradeProduct->upgradableConfigOptions->mapWithKeys(function ($option) use ($currentConfigOptions) {
            return [
                $option->id => $currentConfigOptions[$option->id]
                    ?? $this->configOptions[$option->id]
                    ?? $option->children->first()->id,
            ];
        })->toArray();

        $this->step++;
    }

    public function rules()
    {
        $rules = [
            'upgradeProduct.id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $plan = $this->upgradeProduct->availablePlans()
                        ->where('billing_period', $this->service->plan->billing_period)
                        ->where('billing_unit', $this->service->plan->billing_unit)
                        ->first();
                    if (!$plan) {
                        $fail(__('Invalid upgrade.'));
                    }
                },
            ],

        ];
        foreach ($this->upgradeProduct->upgradableConfigOptions as $option) {
            if (in_array($option->type, ['text', 'number'])) {
                $rules["configOptions.{$option->id}"] = ['required'];
            } elseif ($option->type === 'checkbox') {
            } else {
                $rules["configOptions.{$option->id}"] = ['required', 'exists:config_options,id'];
            }
        }

        return $rules;
    }

    public function doUpgrade()
    {
        if (!$this->service->upgradable) {
            $this->notify('This service is not upgradable.', 'error');

            return $this->redirect(route('services.show', $this->service), true);
        }

        $this->validate();

        $upgradePlan = $this->upgradeProduct->availablePlans()->where('billing_period', $this->service->plan->billing_period)->where('billing_unit', $this->service->plan->billing_unit)->first();

        // The old config options must be in upgradableConfigOptions
        $serviceConfigOptions = $this->service->configs->pluck('config_value_id', 'config_option_id')->toArray();
        $configOptions = collect($serviceConfigOptions)->filter(function ($value, $key) use ($serviceConfigOptions) {
            return isset($serviceConfigOptions[$key]) && $this->upgradeProduct->upgradableConfigOptions->contains('id', $key);
        })->toArray();

        // If the product did not change and the config options are the same, present the user with a message
        if ($this->upgradeProduct->id === $this->service->product_id && $this->configOptions == $configOptions) {
            $this->notify('You have not changed anything. Please select a different product or change the configuration options.', 'error');

            return;
        }

        $upgrade = new ServiceUpgrade([
            'service_id' => $this->service->id,
            'product_id' => $this->upgradeProduct->id,
            'plan_id' => $upgradePlan->id,
        ]);
        $upgrade->save();

        if ($this->configOptions) {
            foreach ($this->configOptions as $optionId => $value) {
                $upgrade->configs()->create([
                    'config_option_id' => $optionId,
                    'config_value_id' => $value,
                ]);
            }
        }
        $price = $upgrade->calculatePrice();

        if ($price->price <= 0) {
            $upgrade->status = ServiceUpgrade::STATUS_COMPLETED;
            $upgrade->save();

            $upgrade->service()->update([
                'plan_id' => $upgrade->plan_id,
                'product_id' => $upgrade->product_id,
            ]);

            // Update the service configs
            foreach ($upgrade->configs as $config) {
                $upgrade->service->configs()->updateOrCreate(
                    ['config_option_id' => $config->config_option_id],
                    ['config_value_id' => $config->config_value_id]
                );
            }

            $this->service->refresh();

            $this->service->recalculatePrice();

            if ($this->service->product->server) {
                // If the service has a server, dispatch the upgrade job
                UpgradeJob::dispatch($this->service);
            }

            // Check if user has credits in this currency
            /** @var \App\Models\User */
            $user = Auth::user();
            $credit = $user->credits()->where('currency_code', $price->currency->code)->first();

            if ($credit) {
                // Increment the credits, `abs()` ensures the amount to add is positive
                $credit->increment('amount', abs($price->price));
            } else {
                $user->credits()->create([
                    'currency_code' => $price->currency->code,
                    'amount' => $price->price,
                ]);
            }

            if ($price->price < 0) {
                $this->notify('The upgrade has been completed. We\'ve added the remaining amount to your account balance.', 'success');
            } else {
                $this->notify('The upgrade has been completed.', 'success');
            }

            return $this->redirect(route('services.show', $this->service), true);
        }

        $invoice = new Invoice([
            'currency_code' => $this->service->currency_code,
            'status' => Invoice::STATUS_PENDING,
            'due_at' => Carbon::now()->addDays(7),
            'user_id' => $this->service->user_id,
        ]);
        $invoice->save();

        $upgrade->invoice_id = $invoice->id;
        $upgrade->save();

        $invoice->items()->create([
            'description' => 'Upgrade ' . $this->service->product->name . ' to ' . $this->upgradeProduct->name,
            'price' => $price->price,
            'quantity' => 1,
            'reference_id' => $upgrade->id,
            'reference_type' => ServiceUpgrade::class,
        ]);

        event(new InvoiceCreated($invoice));

        $this->notify('The upgrade has been added to your cart. Please complete the payment to proceed.', 'success');

        return $this->redirect(route('invoices.show', $invoice));
    }

    public function render()
    {
        return view('services.upgrade')->layoutData([
            'title' => 'Upgrade Service',
            'sidebar' => true,
        ]);
    }
}
