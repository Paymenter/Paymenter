<?php

namespace App\Admin\Resources\TicketResource\Pages;

use App\Admin\Components\UserComponent;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TicketResource;
use App\Admin\Resources\UserResource;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'admin.resources.ticket-resource.pages.edit-ticket';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                MarkdownEditor::make('message')
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

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->columns(['default' => 3, 'md' => 1])
            ->components([
                TextEntry::make('user_id')
                    ->size(TextSize::Large)
                    ->formatStateUsing(fn ($record) => $record->user->name)
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user]))
                    ->label('User ID'),
                TextEntry::make('subject')
                    ->size(TextSize::Large)
                    ->label('Subject'),
                TextEntry::make('status')
                    ->size(TextSize::Large)
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        'replied' => 'gray',
                    })
                    ->label('Status'),
                TextEntry::make('priority')
                    ->size(TextSize::Large)
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'low' => 'success',
                        'medium' => 'gray',
                        'high' => 'danger',
                    })
                    ->label('Priority'),
                TextEntry::make('department')
                    ->size(TextSize::Large)
                    ->formatStateUsing(fn ($state) => array_combine(config('settings.ticket_departments'), config('settings.ticket_departments'))[$state])
                    ->placeholder('No department')
                    ->label('Department'),

                TextEntry::make('assigned_to')
                    ->size(TextSize::Large)
                    ->label('Assigned To')
                    ->placeholder('No assigned user')
                    ->formatStateUsing(fn ($record) => $record->assignedTo->name),

                TextEntry::make('service_id')
                    ->size(TextSize::Large)
                    ->label('Service')
                    ->url(fn ($record) => $record->service ? ServiceResource::getUrl('edit', ['record' => $record->service]) : null)
                    ->placeholder('No service')
                    ->formatStateUsing(fn ($record) => "{$record->service->product->name} - " . ucfirst($record->service->status)),

                Actions::make([
                    Action::make('Edit')
                        ->schema(function (Schema $schema) {
                            return $schema
                                ->columns(2)
                                ->components([
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'open' => 'Open',
                                            'closed' => 'Closed',
                                            'replied' => 'Replied',
                                        ])
                                        ->default('open')
                                        ->required(),
                                    Select::make('priority')
                                        ->label('Priority')
                                        ->options([
                                            'low' => 'Low',
                                            'medium' => 'Medium',
                                            'high' => 'High',
                                        ])
                                        ->default('medium')
                                        ->required(),
                                    Select::make('department')
                                        ->label('Department')
                                        ->options(array_combine(config('settings.ticket_departments'), config('settings.ticket_departments'))),
                                    UserComponent::make('user_id'),
                                    Select::make('assigned_to')
                                        ->label('Assigned To')
                                        ->relationship('assignedTo', 'id', fn (Builder $query) => $query->where('role_id', '!=', null))
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                                    Select::make('service_id')
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
                        ->after(function (Action $action) {
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
