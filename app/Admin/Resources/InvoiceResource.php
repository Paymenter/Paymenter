<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\InvoiceCluster;
use App\Admin\Components\UserComponent;
use App\Admin\Resources\InvoiceResource\Pages\CreateInvoice;
use App\Admin\Resources\InvoiceResource\Pages\EditInvoice;
use App\Admin\Resources\InvoiceResource\Pages\ListInvoices;
use App\Admin\Resources\InvoiceResource\RelationManagers\TransactionsRelationManager;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $cluster = InvoiceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-receipt-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-receipt-fill';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                UserComponent::make('user_id'),
                TextInput::make('number')
                    ->label('Invoice Number')
                    ->helperText('The invoice number will be generated automatically')
                    ->disabled(),
                DatePicker::make('created_at')
                    ->label('Issued At')
                    ->required()
                    ->default(now())
                    ->placeholder('Select the date and time the invoice was issued'),
                DatePicker::make('due_at')
                    ->label('Due At')
                    ->required()
                    ->default(now()->addDays(7))
                    ->placeholder('Select the date and time the invoice is due'),
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->placeholder('Select the status of the invoice'),
                Select::make('currency_code')
                    ->label('Currency')
                    ->required()
                    ->relationship('currency', 'code')
                    ->placeholder('Select the currency'),
                Toggle::make('send_email')
                    ->label('Send Email')
                    ->hiddenOn('edit')
                    ->default(true),
                Repeater::make('items')
                    ->relationship('items')
                    ->label('Items')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
                            // Grab invoice currency
                            ->prefix(fn (Get $get): ?string => Currency::where('code', $get('../../currency_code'))->first()?->prefix)
                            ->suffix(fn (Get $get): ?string => Currency::where('code', $get('../../currency_code'))->first()?->suffix)
                            ->required()
                            ->numeric()
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->placeholder('Enter the price of the product'),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter the quantity of the product'),
                        TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->hintAction(
                                Action::make('View Service')
                                    ->url(function (Get $get) {
                                        return ServiceResource::getUrl('edit', ['record' => $get('reference_id')]);
                                    })
                                    ->label('View Service')
                                    ->hidden(fn (Get $get): bool => !in_array($get('reference_type'), [Service::class, ServiceUpgrade::class]))
                            )
                            ->placeholder('Enter the description of the product'),
                        Hidden::make('reference_type'),
                        Hidden::make('reference_id'),
                    ]),

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
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(true, fn (Builder $query, string $search) => $query->whereHas('user', fn (Builder $query) => $query->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"))),
                TextColumn::make('status')
                    ->label('Status')
                    // Make first letter uppercase
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Issued At')
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formattedTotal')
                    ->label('Total'),
                TextColumn::make('formattedRemaining')
                    ->label('Remaining'),
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
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            // Always use id for invoice route binding in admin
            'edit' => EditInvoice::route('/{record:id}/edit'),
        ];
    }
}
