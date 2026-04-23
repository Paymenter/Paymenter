<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\ServiceConfig;
use Illuminate\Console\Command;

class BackfillSliderConfigValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymenter:backfill-slider-config-values
                            {--force : Apply changes (default is dry-run)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For every active service with dynamic_slider properties but no corresponding '
        . 'service_configs row, write the missing row. Dry-run by default; use --force to mutate.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = ! $this->option('force');

        if ($isDryRun) {
            $this->warn('DRY RUN — pass --force to apply changes.');
        }

        $written = 0;
        $skipped = 0;

        $services = Service::with([
            'product.configOptions',
            'configs',
            'properties',
        ])->whereIn('status', [Service::STATUS_ACTIVE, Service::STATUS_SUSPENDED])->get();

        foreach ($services as $service) {
            $sliders = $service->product->configOptions->where('type', 'dynamic_slider');

            foreach ($sliders as $slider) {
                // Check if a ServiceConfig row already exists for this slider
                $existing = $service->configs->firstWhere('config_option_id', $slider->id);

                if ($existing && $existing->slider_value !== null) {
                    $skipped++;
                    continue;
                }

                // Look up the property value
                $propertyKey = $slider->env_variable ?: $slider->name;
                $property = $service->properties->firstWhere('key', $propertyKey);

                if (! $property || $property->value === null) {
                    $this->line("service={$service->id} slider={$slider->id}: no property value found, skipping");
                    $skipped++;
                    continue;
                }

                $sliderValue = (float) $property->value;

                $this->line("service={$service->id} slider={$slider->id} ({$slider->name}): writing slider_value={$sliderValue}");

                if (! $isDryRun) {
                    ServiceConfig::updateOrCreate(
                        [
                            'configurable_id' => $service->id,
                            'configurable_type' => Service::class,
                            'config_option_id' => $slider->id,
                        ],
                        [
                            'config_value_id' => null,
                            'slider_value' => $sliderValue,
                        ]
                    );
                }

                $written++;
            }
        }

        if ($isDryRun) {
            $this->warn("Dry run complete. {$written} row(s) would be written, {$skipped} skipped. Re-run with --force to apply.");
        } else {
            $this->info("Backfill complete. {$written} row(s) written, {$skipped} skipped.");
        }

        return Command::SUCCESS;
    }
}
