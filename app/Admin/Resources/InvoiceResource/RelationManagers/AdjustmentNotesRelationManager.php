<?php

namespace App\Admin\Resources\InvoiceResource\RelationManagers;

use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdjustmentNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'adjustmentNotes';

    protected static ?string $title = 'Adjustment Notes';

    protected function canModifyAdjustmentNotes(): bool
    {
        return $this->getOwnerRecord()?->status === Invoice::STATUS_PENDING;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Number')
                    ->helperText('The number will be generated automatically')
                    ->disabled(),
                Hidden::make('type')
                    ->default('credit'),
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (is_numeric($state)) {
                            $set('type', $state < 0 ? 'credit' : 'debit');
                        }
                    })
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    ))
                    ->placeholder('Enter the amount (negative = credit, positive = debit)'),
                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter a description'),
                Toggle::make('is_admin_only')
                    ->label('Admin Only')
                    ->helperText('If enabled, this adjustment note will only be visible to admins')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('formattedAmount')
                    ->label('Amount')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                IconColumn::make('is_admin_only')
                    ->label('Admin Only')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => $this->canModifyAdjustmentNotes()),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => $this->canModifyAdjustmentNotes()),
                DeleteAction::make()
                    ->visible(fn (): bool => $this->canModifyAdjustmentNotes()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => $this->canModifyAdjustmentNotes()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
    public function isReadOnly(): bool {
        return false;
    }

}
