<?php

namespace App\Admin\Resources\CurrencyResource\Pages;

use App\Admin\Resources\CurrencyResource;
use App\Models\Currency;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        if (config('settings.default_currency') == $this->record->code) {
            return [];
        }

        return [
            DeleteAction::make()->before(function (DeleteAction $action, Currency $record) {
                // Prevent deletion if its being used by services/orders/credits
                $serviceCount = $record->services()->count();
                $orderCount = $record->orders()->count();
                $creditCount = $record->credits()->where('amount', '>', 0)->count();
                if ($serviceCount > 0 || $orderCount > 0 || $creditCount > 0) {
                    $message = 'Cannot delete currency because it is being used by ';
                    $parts = [];
                    if ($serviceCount > 0) {
                        $parts[] = "{$serviceCount} service(s)";
                    }
                    if ($orderCount > 0) {
                        $parts[] = "{$orderCount} order(s)";
                    }
                    if ($creditCount > 0) {
                        $parts[] = "{$creditCount} credit(s)";
                    }
                    $message .= implode(', ', $parts) . '.';
                    Notification::make()
                        ->title('Whoops!')
                        ->body($message)
                        ->danger()
                        ->send();
                    $action->cancel();
                }
            })
                ->after(function (DeleteAction $action, Currency $record) {
                    // Remove carts with this currency
                    \App\Models\Cart::where('currency_code', $record->code)->delete();
                    \App\Models\Price::where('currency_code', $record->code)->delete();
                }),
        ];
    }
}
