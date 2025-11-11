<?php

namespace App\Admin\Resources\Audits\Tables;

use App\Admin\Resources\CategoryResource;
use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\OrderResource;
use App\Admin\Resources\ProductResource;
use App\Admin\Resources\ServiceResource;
use App\Admin\Resources\TaxRateResource;
use App\Admin\Resources\TicketResource;
use App\Admin\Resources\UserResource;
use App\Models\Audit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        'Ticket' => TicketResource::class,
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->url(fn (Audit $record): string => $record->user_id ? UserResource::getUrl('edit', [$record->user_id]) : '')
                    ->formatStateUsing(fn (Audit $record): string => $record->user ? $record->user->name : 'User #' . $record->user_id)
                    ->placeholder('System')
                    ->sortable(),
                TextColumn::make('event')
                    ->formatStateUsing(fn (Audit $record): string => $record->event . ' - ' . class_basename($record->auditable_type) . ' (' . $record->auditable_id . ')')
                    ->url(function (Audit $record) {
                        if ($record->event != 'deleted' && isset(self::TYPE_TO_RESOURCE[class_basename($record->auditable_type)])) {
                            return self::TYPE_TO_RESOURCE[class_basename($record->auditable_type)]::getUrl('edit', [$record->auditable_id]);
                        }
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search) {
                            $query->where('event', 'like', "%{$search}%")
                                ->orWhere('auditable_type', 'like', "%{$search}%")
                                ->orWhere('auditable_id', 'like', "%{$search}%");
                        });
                    }),
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
            ->defaultSort(function (Builder $query) {
                return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
