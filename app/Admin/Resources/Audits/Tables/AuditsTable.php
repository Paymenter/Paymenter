<?php

namespace App\Admin\Resources\Audits\Tables;

use App\Admin\Resources\CategoryResource;
use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\OrderResource;
use App\Admin\Resources\ProductResource;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TaxRateResource;
use App\Admin\Resources\UserResource;
use App\Models\Audit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditsTable
{
    const TYPE_TO_RESOURCE = [
        'User' => UserResource::class,
        'Product' => ProductResource::class,
        'Order' => OrderResource::class,
        'Invoice' => InvoiceResource::class,
        'Category' => CategoryResource::class,
        'Service' => ServiceResource::class,
        'TaxRate' => TaxRateResource::class,
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->url(fn (Audit $record): string => $record->user_id ? UserResource::getUrl('edit', [$record->user_id]) : '')
                    ->formatStateUsing(fn (Audit $record): string => $record->user->name)
                    ->placeholder('System')
                    ->sortable(),
                TextColumn::make('event')
                    ->formatStateUsing(fn (Audit $record): string => $record->event . ' - ' . class_basename($record->auditable_type) . ' (' . $record->auditable_id . ')')
                    ->url(function (Audit $record) {
                        if (isset(self::TYPE_TO_RESOURCE[class_basename($record->auditable_type)])) {
                            return self::TYPE_TO_RESOURCE[class_basename($record->auditable_type)]::getUrl('edit', [$record->auditable_id]);
                        }
                    })
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->searchable(),
                TextColumn::make('user_agent')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
