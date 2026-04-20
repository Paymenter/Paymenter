<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\InvoiceCluster;
use App\Admin\Components\UserComponent;
use App\Admin\Resources\InvoiceResource\Pages\CreateInvoice;
use App\Admin\Resources\InvoiceResource\Pages\EditInvoice;
use App\Admin\Resources\InvoiceResource\Pages\ListInvoices;
use App\Admin\Resources\InvoiceResource\RelationManagers\TransactionsRelationManager;
use App\Models\Coupon;
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
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
                Select::make('coupon_id')
                    ->label(__('admin.invoice.coupon'))
                    ->options(fn () => Coupon::query()->pluck('code', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('admin.invoice.coupon_placeholder'))
                    ->helperText(__('admin.invoice.coupon_helper'))
                    ->dehydrated(false)
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        if (! $state) {
                            return;
                        }

                        $coupon = Coupon::find($state);

                        if (! $coupon) {
                            return;
                        }

                        if ($coupon->expires_at?->isPast()) {
                            Notification::make()
                                ->title(__('admin.invoice.coupon_expired'))
                                ->danger()
                                ->send();
                            $set('coupon_id', null);

                            return;
                        }

                        if ($coupon->starts_at?->isFuture()) {
                            Notification::make()
                                ->title(__('admin.invoice.coupon_not_active'))
                                ->danger()
                                ->send();
                            $set('coupon_id', null);

                            return;
                        }

                        if ($coupon->max_uses && $coupon->services()->count() >= $coupon->max_uses) {
                            Notification::make()
                                ->title(__('admin.invoice.coupon_max_uses'))
                                ->danger()
                                ->send();
                            $set('coupon_id', null);

                            return;
                        }

                        $items = $get('items') ?? [];
                        $subtotal = collect($items)
                            ->filter(fn ($item) => floatval($item['price'] ?? 0) > 0 && ! ($item['apply_after_tax'] ?? false))
                            ->sum(fn ($item) => floatval($item['price']) * max(1, intval($item['quantity'] ?? 1)));

                        if ($subtotal <= 0) {
                            Notification::make()
                                ->title(__('admin.invoice.coupon_no_items'))
                                ->warning()
                                ->send();
                            $set('coupon_id', null);

                            return;
                        }

                        $discount = $coupon->calculateDiscount($subtotal);

                        if ($discount <= 0) {
                            Notification::make()
                                ->title(__('admin.invoice.coupon_no_discount'))
                                ->danger()
                                ->send();
                            $set('coupon_id', null);

                            return;
                        }

                        $label = $coupon->type === 'percentage'
                            ? $coupon->code . ' (-' . number_format($coupon->value, 2) . '%)'
                            : $coupon->code;

                        $items[(string) Str::uuid()] = [
                            'price' => -round($discount, 2),
                            'quantity' => 1,
                            'description' => $label,
                            'reference_type' => null,
                            'reference_id' => null,
                            'apply_after_tax' => $coupon->apply_after_tax,
                        ];

                        $set('items', $items);
                        $set('coupon_id', null);

                        Notification::make()
                            ->title(__('admin.invoice.coupon_applied'))
                            ->success()
                            ->send();
                    }),
                Repeater::make('items')
                    ->relationship('items')
                    ->label('Items')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
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
                        TextInput::make('unit')
                            ->label(__('admin.invoice_item.unit'))
                            ->nullable()
                            ->placeholder(__('admin.invoice_item.unit_placeholder'))
                            ->helperText(__('admin.invoice_item.unit_helper')),
                        MarkdownEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'strike',
                            ])
                            ->columnSpanFull()
                            ->hintAction(
                                Action::make('viewService')
                                    ->url(function (Get $get) {
                                        return ServiceResource::getUrl('edit', ['record' => $get('reference_id')]);
                                    })
                                    ->label('View Service')
                                    ->hidden(fn (Get $get): bool => ! in_array($get('reference_type'), [Service::class, ServiceUpgrade::class]))
                            )
                            ->placeholder('Enter the description of the product'),
                        Hidden::make('reference_type'),
                        Hidden::make('reference_id'),
                        Hidden::make('apply_after_tax'),
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
                TextColumn::make('formattedGrandTotal')
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['items', 'currency', 'transactions', 'snapshot', 'user']);
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
