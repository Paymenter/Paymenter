<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\UserResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShowServices extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'services';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationLabel(): string
    {
        return __('users.products_services');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('id')
                    ->label(__('users.id'))
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('users.product')),
                TextColumn::make('quantity')
                    ->label(__('users.quantity')),
                TextColumn::make('formattedPrice')->label(__('products.price')),
                TextColumn::make('status')
                    ->label(__('users.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __('services.statuses.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => ServiceResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
