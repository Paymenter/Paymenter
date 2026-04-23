<?php

namespace App\Console\Commands;

use App\Models\ConfigOption;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSliderBasePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymenter:migrate-slider-base-price
                            {--force : Apply changes (default is dry-run)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collapse duplicate per-slider base_price values into a single plan-level dynamic_slider_base_price. '
        . 'Dry-run by default; use --force to mutate. '
        . 'Emits CSV: product_id,plan_id,before_total,after_total.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = ! $this->option('force');

        if ($isDryRun) {
            $this->warn('DRY RUN — pass --force to apply changes.');
        }

        $this->line('product_id,plan_id,before_total,after_total');

        $products = Product::with([
            'configOptions',
            'plans.prices',
        ])->get();

        $changed = 0;

        foreach ($products as $product) {
            $sliders = $product->configOptions->where('type', 'dynamic_slider');

            if ($sliders->isEmpty()) {
                continue;
            }

            // Collect all distinct base_price values across sliders
            $basePrices = $sliders->map(function (ConfigOption $option) {
                return (float) ($option->metadata['pricing']['base_price'] ?? 0);
            })->filter(fn ($bp) => $bp > 0)->unique()->values();

            // Only migrate when all sliders share the same non-zero base_price
            if ($basePrices->count() !== 1) {
                continue;
            }

            $sharedBase = $basePrices->first();
            $sliderCount = $sliders->count();

            foreach ($product->plans as $plan) {
                // Estimate before_total using the first price row (monthly, any currency)
                $planPrice = (float) ($plan->prices->first()?->price ?? 0);
                $beforeTotal = $planPrice + ($sharedBase * $sliderCount);
                $afterTotal = $planPrice + $sharedBase;

                $this->line("{$product->id},{$plan->id},{$beforeTotal},{$afterTotal}");

                if (! $isDryRun) {
                    // Write plan-level base price
                    $plan->dynamic_slider_base_price = $sharedBase;
                    $plan->save();
                }

                $changed++;
            }

            // Zero out per-slider base_price copies once per product (not per plan)
            if (! $isDryRun) {
                foreach ($sliders as $option) {
                    $metadata = $option->metadata ?? [];
                    $metadata['pricing']['base_price'] = 0;
                    $option->metadata = $metadata;
                    $option->save();
                }
            }
        }

        if ($isDryRun) {
            $this->warn("Dry run complete. {$changed} plan(s) would be updated. Re-run with --force to apply.");
        } else {
            $this->info("Migration complete. {$changed} plan(s) updated.");
        }

        return Command::SUCCESS;
    }
}
