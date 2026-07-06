<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\InvoiceCluster;
use App\Admin\Resources\AdjustmentNoteResource\Pages\CreateAdjustmentNote;
use App\Admin\Resources\AdjustmentNoteResource\Pages\EditAdjustmentNote;
use App\Admin\Resources\AdjustmentNoteResource\Pages\ListAdjustmentNotes;
use App\Enums\AdjustmentNoteStatus;
use App\Models\AdjustmentNote;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdjustmentNoteResource extends Resource
{
    protected static ?string $model = AdjustmentNote::class;

    protected static ?string $cluster = InvoiceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-scales-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-scales-fill';

    protected static ?string $navigationLabel = 'Adjustment Notes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->label('Invoice')
                    ->relationship('invoice', modifyQueryUsing: fn (Builder $query) => $query->with('user')->orderByDesc('id'))
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => ($record->number ? "#$record->number" : $record->id) . " ({$record->user->email})")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select an invoice'),
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
                Select::make('status')
                    ->label('Status')
                    ->options(AdjustmentNoteStatus::class)
                    ->default(AdjustmentNoteStatus::Active->value)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice.number')
                    ->label('Invoice')
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof AdjustmentNoteStatus ? $state->value : $state) {
                        AdjustmentNoteStatus::Active->value => 'success',
                        AdjustmentNoteStatus::Voided->value => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state instanceof AdjustmentNoteStatus ? $state->value : ucfirst($state))
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query->orderBy('id', 'desc');
            })
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdjustmentNotes::route('/'),
            'create' => CreateAdjustmentNote::route('/create'),
            'edit' => EditAdjustmentNote::route('/{record:id}/edit'),
        ];
    }
}
