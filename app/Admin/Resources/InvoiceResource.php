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

    public static function getNavigationLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function getModelLabel(): string
    {
        return __('invoices.invoice_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('invoices.invoices_plural_label');
    }

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
                    ->label(__('invoices.invoice_number'))
                    ->helperText(__('invoices.invoice_number_helper'))
                    ->disabled(),
                DatePicker::make('created_at')
                    ->label(__('invoices.issued_at'))
                    ->required()
                    ->default(now())
                    ->placeholder(__('invoices.issued_at_placeholder')),
                DatePicker::make('due_at')
                    ->label(__('invoices.due_at'))
                    ->required()
                    ->default(now()->addDays(7))
                    ->placeholder(__('invoices.due_at_placeholder')),
                Select::make('status')
                    ->label(__('invoices.status'))
                    ->required()
                    ->options([
                        'paid' => __('invoices.paid'),
                        'pending' => __('invoices.pending'),
                        'cancelled' => __('invoices.cancelled'),
                    ])
                    ->default('pending')
                    ->placeholder(__('invoices.status_placeholder')),
                Select::make('currency_code')
                    ->label(__('invoices.currency'))
                    ->required()
                    ->relationship('currency', 'code')
                    ->placeholder(__('invoices.currency_placeholder')),
                Toggle::make('send_email')
                    ->label(__('invoices.send_email'))
                    ->hiddenOn('edit')
                    ->default(true),
                Repeater::make('items')
                    ->relationship('items')
                    ->label(__('invoices.items'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label(__('invoices.price'))
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
                            ->placeholder(__('invoices.price_placeholder')),
                        TextInput::make('quantity')
                            ->label(__('invoices.quantity'))
                            ->required()
                            ->numeric()
                            ->placeholder(__('invoices.quantity_placeholder')),
                        TextInput::make('description')
                            ->label(__('invoices.description'))
                            ->required()
                            ->hintAction(
                                Action::make('View Service')
                                    ->url(function (Get $get) {
                                        return ServiceResource::getUrl('edit', ['record' => $get('reference_id')]);
                                    })
                                    ->label(__('invoices.view_service'))
                                    ->hidden(fn (Get $get): bool => !in_array($get('reference_type'), [Service::class, ServiceUpgrade::class]))
                            )
                            ->placeholder(__('invoices.description_placeholder')),
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
                    ->label(__('invoices.id'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number')
                    ->label(__('invoices.invoice_no'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('invoices.user'))
                    ->searchable(true, fn (Builder $query, string $search) => $query->whereHas('user', fn (Builder $query) => $query->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"))),
                TextColumn::make('status')
                    ->label(__('invoices.status'))
                    // Localize status
                    ->formatStateUsing(fn (string $state): string => __('invoices.' . $state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('invoices.issued_at'))
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formattedTotal')
                    ->label(__('invoices.total')),
                TextColumn::make('formattedRemaining')
                    ->label(__('invoices.remaining')),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('id', 'desc');
            })
            ->filters([
                SelectFilter::make('status')
                    ->label(__('invoices.status'))
                    ->options([
                        'paid' => __('invoices.paid'),
                        'pending' => __('invoices.pending'),
                        'cancelled' => __('invoices.cancelled'),
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
