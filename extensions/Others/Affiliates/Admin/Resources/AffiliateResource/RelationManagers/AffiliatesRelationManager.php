<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers;

use App\Admin\Resources\OrderResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateOrder;

class AffiliatesRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $model = AffiliateOrder::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('affiliate_id')
            ->columns([
                TextColumn::make('order.id'),
                TextColumn::make('earnings')->formatStateUsing(function (AffiliateOrder $affiliateOrder) {
                    if (count($affiliateOrder->affiliate->earnings) <= 0) {
                        return null;
                    }

                    return implode(', ', array_map(function ($key, $value) {
                        return "$key: $value";
                    }, array_keys($affiliateOrder->affiliate->earnings), $affiliateOrder->affiliate->earnings));
                })->label('Earning'),
                TextColumn::make('order.created_at')
                    ->label('Created At')
                    ->since()
                    ->sortable()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record->order])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
