<?php

namespace App\Admin\Resources\OrderResource\Pages;

use App\Admin\Resources\OrderResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        $invoice = new Invoice([
            'user_id' => $this->record->user_id,
            'currency_code' => $this->record->currency_code,
            'due_at' => now()->addDays(7),
        ]);
        $invoice->save();

        foreach ($this->record->services as $service) {
            $invoice->items()->create([
                'description' => $service->description,
                'price' => $service->price,
                'quantity' => $service->quantity,
                'reference_id' => $service->id,
                'reference_type' => get_class($service),
            ]);
        }

    }
}
