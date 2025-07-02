<?php

namespace App\Admin\Resources\ServiceResource\RelationManagers;

use App\Admin\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    // Edit query
    protected function getTableQuery(): Builder|Relation|null
    {
        return Invoice::query()->whereIn('id', InvoiceItem::query()
            ->where(function ($query) {
                $query->where('reference_type', 'App\Models\Service')
                    ->where('reference_id', $this->ownerRecord->id);
            })->orWhere(function ($query) {
                $query->where('reference_type', 'App\Models\ServiceUpgrade')
                    ->whereIn('reference_id', $this->ownerRecord->upgrade()->pluck('id')->filter());
            })->pluck('invoice_id'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('formattedTotal')->label('Total'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    // Make first letter uppercase
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => InvoiceResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('invoices.id', 'desc');
    }
}
