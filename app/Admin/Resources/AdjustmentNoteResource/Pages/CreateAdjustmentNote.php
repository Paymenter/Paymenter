<?php

namespace App\Admin\Resources\AdjustmentNoteResource\Pages;

use App\Admin\Resources\AdjustmentNoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdjustmentNote extends CreateRecord
{
    protected static string $resource = AdjustmentNoteResource::class;

    public function mount(): void
    {
        parent::mount();

        if ($invoiceId = request()->get('invoice_id')) {
            $this->form->fill([
                'invoice_id' => $invoiceId,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
