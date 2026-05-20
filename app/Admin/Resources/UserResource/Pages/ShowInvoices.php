<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\UserResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShowInvoices extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'invoices';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-receipt-line';

    public static function getNavigationLabel(): string
    {
        return __('users.invoices');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('id')
                    ->label(__('users.id'))
                    ->sortable(),
                TextColumn::make('formattedTotal')->label(__('users.total')),
                TextColumn::make('status')
                    ->label(__('users.status'))
                    ->formatStateUsing(fn (string $state): string => __('users.' . $state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('users.status'))
                    ->options([
                        'paid' => __('users.paid'),
                        'pending' => __('users.pending'),
                        'cancelled' => __('users.cancelled'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => InvoiceResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
