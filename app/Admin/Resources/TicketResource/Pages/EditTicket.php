<?php

namespace App\Admin\Resources\TicketResource\Pages;

use App\Admin\Components\UserComponent;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TicketResource;
use App\Admin\Resources\UserResource;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected static string $view = 'admin.resources.ticket-resource.pages.edit-ticket';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\MarkdownEditor::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Send Message'),
            $this->getCancelFormAction(),
        ];
    }

    // Save action
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->messages()->create([
            'user_id' => Auth::id(),
            'message' => $data['message'],
        ]);

        return $record;
    }

    // Clear form after save
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        parent::save($shouldRedirect, $shouldSendSavedNotification);

        $this->form->fill();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->columns(['default' => 3, 'md' => 1])
            ->schema([
                Infolists\Components\TextEntry::make('user_id')
                    ->size(TextEntrySize::Large)
                    ->formatStateUsing(fn ($record) => $record->user->name)
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user]))
                    ->label('User ID'),
                Infolists\Components\TextEntry::make('subject')
                    ->size(TextEntrySize::Large)
                    ->label('Subject'),
                Infolists\Components\TextEntry::make('status')
                    ->size(TextEntrySize::Large)
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        'replied' => 'gray',
                    })
                    ->label('Status'),
                Infolists\Components\TextEntry::make('priority')
                    ->size(TextEntrySize::Large)
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'low' => 'success',
                        'medium' => 'gray',
                        'high' => 'danger',
                    })
                    ->label('Priority'),
                Infolists\Components\TextEntry::make('department')
                    ->size(TextEntrySize::Large)
                    ->formatStateUsing(fn ($state) => array_combine(config('settings.ticket_departments'), config('settings.ticket_departments'))[$state])
                    ->placeholder('No department')
                    ->label('Department'),

                Infolists\Components\TextEntry::make('assigned_to')
                    ->size(TextEntrySize::Large)
                    ->label('Assigned To')
                    ->placeholder('No assigned user')
                    ->formatStateUsing(fn ($record) => $record->assignedTo->name),

                Infolists\Components\TextEntry::make('service_id')
                    ->size(TextEntrySize::Large)
                    ->label('Service')
                    ->url(fn ($record) => $record->service ? ServiceResource::getUrl('edit', ['record' => $record->service]) : null)
                    ->placeholder('No service')
                    ->formatStateUsing(fn ($record) => "{$record->service->product->name} - " . ucfirst($record->service->status)),

                InfolistActions::make([
                    Action::make('Edit')
                        ->form(function (Form $form) {
                            return $form
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'open' => 'Open',
                                            'closed' => 'Closed',
                                            'replied' => 'Replied',
                                        ])
                                        ->default('open')
                                        ->required(),
                                    Forms\Components\Select::make('priority')
                                        ->label('Priority')
                                        ->options([
                                            'low' => 'Low',
                                            'medium' => 'Medium',
                                            'high' => 'High',
                                        ])
                                        ->default('medium')
                                        ->required(),
                                    Forms\Components\Select::make('department')
                                        ->label('Department')
                                        ->options(array_combine(config('settings.ticket_departments'), config('settings.ticket_departments'))),
                                    UserComponent::make('user_id'),
                                    Forms\Components\Select::make('assigned_to')
                                        ->label('Assigned To')
                                        ->relationship('assignedTo', 'id', fn (Builder $query) => $query->where('role_id', '!=', null))
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                                    Forms\Components\Select::make('service_id')
                                        ->label('Service')
                                        ->relationship('service', 'id', function (Builder $query, Get $get) {
                                            $query->where('user_id', $get('user_id'));
                                        })
                                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - " . ucfirst($record->status))
                                        ->disabled(fn (Get $get) => !$get('user_id')),
                                ]);
                        })
                        ->fillForm(fn ($record) => [
                            'status' => $record->status,
                            'priority' => $record->priority,
                            'department' => $record->department,
                            'user_id' => $record->user_id,
                            'assigned_to' => $record->assigned_to,
                            'service_id' => $record->service_id,
                        ])
                        ->action(function (array $data, Ticket $record): void {
                            $record->update($data);
                        })
                        ->hidden(!auth()->user()->can('update', $this->record))
                        ->icon('heroicon-o-pencil'),
                    Action::make('Delete')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn (Ticket $record) => $record->delete())
                        ->hidden(!auth()->user()->can('delete', $this->record))
                        ->after(function (StaticAction $action) {
                            Notification::make()
                                ->title(__('filament-actions::delete.single.notifications.deleted.title'))
                                ->success()
                                ->send();

                            $action->redirect(TicketResource::getUrl('index'));
                        }),
                ])->columnSpan(['default' => 'full', 'md' => 1]),
            ]);
    }

    public function deleteMessage(TicketMessage $message): void
    {
        if (auth()->user()->can('delete', $message)) {
            $message->delete();
        }
    }
}
