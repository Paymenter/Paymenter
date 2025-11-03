<?php

namespace App\Admin\Resources\TicketResource\Pages;

use App\Admin\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    public function mount(): void
    {
        parent::mount();

        $data = [];
        if ($userId = request()->get('ownerRecord')) {
            $data['user_id'] = $userId;
        }
        if ($serviceId = request()->get('service_id')) {
            $data['service_id'] = $serviceId;
        }

        if (!empty($data)) {
            $this->form->fill($data);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        $record->messages()->create([
            'user_id' => Auth::id(),
            'message' => $data['message'],
        ]);

        return $record;
    }
}
