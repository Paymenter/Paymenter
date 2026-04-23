<?php

namespace App\Admin\Resources\ConfigOptionResource\Concerns;

use App\Rules\DynamicSliderPricingRule;
use Filament\Notifications\Notification;

/**
 * Runs DynamicSliderPricingRule against submitted config-option form data
 * before Filament persists it. On failure, sends a danger notification
 * and halts the page action so no invalid pricing ever reaches the DB.
 */
trait ValidatesDynamicSliderPricing
{
    protected function validateDynamicSliderPricing(array $data): void
    {
        if (($data['type'] ?? null) !== 'dynamic_slider') {
            return;
        }

        $pricing = $data['metadata']['pricing'] ?? null;

        if ($pricing === null) {
            Notification::make()
                ->title('Invalid pricing configuration')
                ->body('Dynamic slider options require a pricing configuration.')
                ->danger()
                ->send();

            $this->halt();

            return;
        }

        $errors = [];
        (new DynamicSliderPricingRule())->validate(
            'metadata.pricing',
            $pricing,
            function (string $message) use (&$errors) {
                $errors[] = $message;
            }
        );

        if ($errors === []) {
            return;
        }

        Notification::make()
            ->title('Invalid pricing configuration')
            ->body(implode(' ', $errors))
            ->danger()
            ->send();

        $this->halt();
    }
}
