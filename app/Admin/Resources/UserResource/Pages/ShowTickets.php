<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use App\Models\Ticket;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShowTickets extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'tickets';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-customer-service-line';

    public static function getNavigationLabel(): string
    {
        return __('users.tickets');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('subject')
                    ->label(__('users.subject'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('users.status'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Ticket $record) => match ($record->status) {
                        'open' => 'success',
                        'closed' => 'danger',
                        'replied' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => __('users.' . $state)),
                TextColumn::make('priority')
                    ->label(__('users.priority'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Ticket $record) => match ($record->priority) {
                        'low' => 'success',
                        'medium' => 'gray',
                        'high' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => __('users.' . $state)),
                TextColumn::make('department')
                    ->label(__('users.department'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('users.user'))
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
            ]);
    }
}
