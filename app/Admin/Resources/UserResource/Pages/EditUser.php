<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn ($record) => Auth::user()->id == $record->id),
            Actions\Action::make('impersonate')
                ->label('Impersonate')
                ->action(function ($record) {
                    session()->put('impersonating', $record->id);
                    $this->redirect('/dashboard');
                })
                ->hidden(fn ($record) => Auth::user()->hasPermission('impersonate', $record) == false || Auth::user()->id == $record->id),
        ];
    }
}
