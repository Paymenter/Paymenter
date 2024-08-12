<?php

namespace App\Admin\Resources;

use App\Admin\Resources\InvoiceResource\Pages;
use App\Admin\Resources\InvoiceResource\RelationManagers;
use App\Models\Currency;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'ri-bill-line';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'id')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),
                Forms\Components\Select::make('currency_code')
                    ->label('Currency')
                    ->required()
                    ->relationship('currency', 'code')
                    ->placeholder('Select the currency'),
                Forms\Components\DatePicker::make('issued_at')
                    ->label('Issued At')
                    ->required()
                    ->placeholder('Select the date and time the invoice was issued'),
                Forms\Components\DatePicker::make('due_at')
                    ->label('Due At')
                    ->required()
                    ->placeholder('Select the date and time the invoice is due'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ])
                    ->placeholder('Select the status of the invoice'),
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->label('Items')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            // Grab invoice currency
                            ->prefix(fn(Get $get): ?string => Currency::where('code', $get('../../currency_code'))->first()?->prefix)
                            ->suffix(fn(Get $get): ?string => Currency::where('code', $get('../../currency_code'))->first()?->suffix)
                            ->required()
                            ->numeric()
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->placeholder('Enter the price of the product'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter the quantity of the product'),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->hintAction(
                                Forms\Components\Actions\Action::make('View Order Product')
                                    ->url(fn(Get $get): string => OrderProductResource::getUrl('edit', ['record' => $get('order_product_id')]))
                                    ->hidden(fn(Get $get): bool => !$get('order_product_id'))
                            )
                            ->placeholder('Enter the description of the product'),
                        Forms\Components\Hidden::make('order_product_id'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    // Make first letter uppercase
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Issued At')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formattedTotal')
                    ->label('Total')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formattedRemaining')
                    ->label('Remaining')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('id', 'desc');
            })
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
